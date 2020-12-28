<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Validator;
use App\Http\Requests\CheckUsersRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

use App\Component\ExtraFunction;
use Illuminate\Support\Facades\Hash;

class Users extends Controller
{
	public function index(){
		$users = User::getActiveUser();
		return view("users/index")->with("data", $users);
	}
	
	public function create(){
		$roles = Role::all();
		return view("users/create", ['roles' => $roles]);
	}
	
	public function store(CheckUsersRequest $request){
		$input = $request->all();
		foreach($input as $key => $val){
			$input[$key] = trim($val);
		}
		
		$role = Role::find($input['role']);
		if(empty($role)){
			$request->session()->flash('alert', trans('users.CanNotFindRole'));
			return redirect(main_prefix.'/users/create');
		}

		$resCheck = ExtraFunction::checkCanAction($role->level);
		if(!$resCheck){
			$request->session()->flash('alert', trans('users.YouDoNotHavePermissionToCreateNewMemberWhoHaveLevelEqualOrHigherThanYourLevel'));
			return redirect(main_prefix.'/users/create');
		}
		
		DB::beginTransaction();
		try{
			$create = User::create([
				'name' => $input['name'],
				'email' => $input['email'],
				'password' => bcrypt($input['password']),
			]);
			if(!$create['wasRecentlyCreated']){
				$request->session()->flash('status', false);
				DB::rollback();
			}else{
				$create->attachRole($input['role']);
				$request->session()->flash('status', true);
			}
			DB::commit();
		}catch(Exception $e){
			$request->session()->flash('status', false);
			DB::rollback();
		}
		return redirect(main_prefix.'/users/create');
	}
	
	public function editUser(){
		
	}
	
	public function destroy($id,Request $request){
		$user = User::find($id);
		if(empty($user)){
			return Response::json(array('state' => 'alert', 'error' => trans('users.CannotFindUser')));
		}
		
		$auth = Auth::user();
		if($user->id == $auth->id){
			return Response::json(array('state' => 'alert', 'error' => trans('users.YouCanNotDeleteYourAccount')));
		}
		
		$role = Role::getAllRoleById($id);
		$resCheck = ExtraFunction::checkCanAction($role['0']->level);
		if(!$resCheck){
			return Response::json(array('state' => 'alert', 'error' => 'Bạn không có quyền xóa thành viên có cấp độ ngang hoặc cao hơn cấp độ của bạn'));
		}
		
		DB::beginTransaction();
		try{
			$user->status = 1;
			$user->save();
			DB::commit();
			
			$request->session()->flash('del', true);
			return Response::json(array('state' => 'success', 'data' => trans('users.Deleted successfully')));
		}catch(Exception $e){
			DB::rollback();
		}
		
		return Response::json(array('state' => 'fail', 'error' => trans("users.Deleted fail")));
	}
	
	public function changePassword(Request $request){
		$input = $request->all();
		if(!empty($input['oldpassword'] && !empty($input['newpassword'] && !empty($input['password_confirmation']))) ){
			
		}else{
			return Response::json(array('state' => 'alert', 'error' => trans('passwords.Cannot leave empty')));
		}
		
		$old = $input['oldpassword'];
		$new = $input['newpassword'];
		$confirm = $input['password_confirmation'];
		
		$auth = Auth::user();
		if(!Hash::check($old, $auth->password)){
			return Response::json(array('state' => 'alert', 'error' => trans('passwords.Old password not right')));
		}
		
		if(strlen($new) < 6){
			return Response::json(array('state' => 'alert', 'error' => trans('passwords.At least 6 character')));
		}
		
		if($new !== $confirm){
			return Response::json(array('state' => 'alert', 'error' => trans('passwords.Confirm password not right')));
		}
		
		DB::beginTransaction();
		try{
			$auth->password = bcrypt($new);
			$auth->save();
			DB::commit();
			return Response::json(array('state' => 'success', 'data' => trans('passwords.Changed password successfully')));
		}catch(Exception $e){
			DB::rollback();
		}
		
		return Response::json(array('state' => 'fail', 'error' => trans("passwords.Changed password fail")));
	}
	
	public function newMemberInfo(){
		return view("users/newInfo");
	}
}
