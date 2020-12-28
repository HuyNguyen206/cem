<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CheckRolesRequest;
use App\Models\Authen\Role;
use App\Models\Authen\GroupRole;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Component\ExtraFunction;

class Roles extends Controller
{
	public function index(){
		$modelRole = new Role();
		$modelGroupRole = new GroupRole();
		$roles = $modelRole->getAllRole();
		$groupRoles = $modelGroupRole->getAllGroupRole();
		return view("roles/index",['role' => $roles,'groupRole' => $groupRoles]);
	}
	
	public function create(){
		$modelRole = new Role();
		$modelGroupRole = new GroupRole();
		$roles = $modelRole->getAllRole();
		$groupRoles = $modelGroupRole->getAllGroupRole();
		var_dump('here');die;
		return view("roles/create",['role' => $roles,'groupRole' => $groupRoles]);
	}
	
	public function store(Request $request){
		$input = $request->all();
		var_dump($input);die;
		foreach($input as $key => $val){
			$input[$key] = trim($val);
		}
		$input['display_name'] = $input['name'];
		
		if(!$input['rate']){
			$input['level'] += 1;
		}

		$resCheck = ExtraFunction::checkCanAction($input['level']);
		if(!$resCheck){
			$request->session()->flash('alert', 'Bạn không có quyền tạo vai trò có cấp độ ngang hoặc cao hơn cấp độ của bạn, trừ khi cấp độ của bạn là cấp độ 1');
			return redirect(main_prefix.'/roles/create');
		}
		
		DB::beginTransaction();
		try{
			$model_role = new Role();
			$param = [
				'name'=>$input['name'],
				'display_name'=>$input['display_name'],
				'description'=>$input['description'],
				'level'=>$input['level'],
				'created_at' => date('Y-m-d H:i:s')
			];
			$create = $model_role->insertRole($param);
			if(!$create){
				$request->session()->flash('status', false);
			}else{
				$request->session()->flash('status', true);
				DB::commit();
			}
		}catch(Exception $e){
			DB::rollback();
		}
		
		return redirect(main_prefix.'/roles/create');
	}
	
	public function edit($id){
		$role = role::findOrFail($id);
		var_dump($role);die;
	    return view('roles/edit',compact('role'));
	}
	
	public function update($id){
		var_dump($id);die;
	}
	
	public function destroy($id, Request $request){
		if($id == '1' || $id == '10'){
			return Response::json(array('state' => 'alert', 'error' => 'Không thể xóa vai trò cơ bản'));
		}
		$role = Role::find($id);
		if(empty($role)){
			return Response::json(array('state' => 'alert', 'error' => 'Không tìm thấy vai trò'));
		}

		$resCheck = ExtraFunction::checkCanAction($role->level);
		if(!$resCheck){
			return Response::json(array('state' => 'alert', 'error' => 'Bạn không có quyền xóa vai trò có cấp độ ngang hoặc cao hơn cấp độ của bạn'));
		}
		
		DB::beginTransaction();
		try{
//			DB::table('role_user')
//            ->where('role_id', $id)
//            ->update(['role_id' => 10]);
			$role->perms()->sync([]);
			DB::table('roles')->where('id', '=', $id)->delete();
			DB::commit();
			$request->session()->flash('del', true);
			return Response::json(array('state' => 'success', 'data' => 'Xóa vai trò thành công'));
		}catch(Exception $e){
			DB::rollback();
		}
		
		return Response::json(array('state' => 'fail', 'error' => 'Xóa vai trò thất bại'));
	}
}
