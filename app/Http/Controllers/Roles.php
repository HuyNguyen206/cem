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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;

class Roles extends Controller {

    public function index() {
        $modelRole = new Role();
        $modelGroupRole = new GroupRole();
        $roles = $modelRole->getAllRole();
        $groupRoles = $modelGroupRole->getAllGroupRole();
        return view("roles/index", ['role' => $roles, 'groupRole' => $groupRoles]);
    }

    public function create() {
        $modelRole = new Role();
        $modelGroupRole = new GroupRole();
        
        $userRole = Session::get('userRole');
        if ($userRole['level'] == 1) {
            $roles = $modelRole->getAllRole();
        } else {
            $roles = $modelRole->getAllRoleByLevel($userRole['level']);
        }

        $group = $modelGroupRole->getAllGroupRole();
        
        $view = view("roles/create", ['role' => $roles, 'group' => $group]);
        return $view;
    }

    public function store(CheckRolesRequest $request) {
        $input = $request->all();
        foreach ($input as $key => $val) {
            if(!is_array($val)){
                $input[$key] = trim($val);
            }
        }
        $input['display_name'] = $input['name'];
        
        if (!$input['rate']) {
            $input['level'] += 1;
        }

        $resCheck = ExtraFunction::checkCanAction($input['level']);
        if (!$resCheck) {
            $request->session()->flash('alert', trans('roles.YouDoNotHavePermissionToCreateNewRoleEqualOrHigherThanYourLevel'));
            return redirect(main_prefix . '/roles/create');
        }

        //Kiểm tra toàn bộ thông tin input theo yêu cầu
        $error = false;
        $validator = $this->checkCustomInput($input, $error);
        if ($error) {
            $this->throwValidationException($request, $validator);
        }

        DB::beginTransaction();
        try {
            $modelGroup = new GroupRole();
            $modelRole = new Role();
            if ($input['newGroup']) {
                $getRes = $modelGroup->getGroupRoleByName($input['group0']);
                if (empty($getRes)) {
                    $group['name'] = $input['group0'];
                    $group['display_name'] = $group['name'];
                    $group['description'] = $input['descriptionGroup'];
                    $tempId = $modelGroup->insertGroupRoleGetId($group);
                } else {
                    $group['id'] = $getRes->id;
                    $group['description'] = $input['descriptionGroup'];
                    $tempId = $modelGroup->updateGroupRoleGetId($group);
                }
                $idGroup = [$tempId];
            } else {
                $idGroup = $input['group1'];
                foreach($idGroup as $id){
                    $check = $modelGroup->getGroupRoleById($id);
                    if(empty($check)){
                        $request->session()->flash('alert', trans('roles.TheGroupNameIsNotExist'));
                        return redirect(main_prefix . '/roles/create');
                    }
                }
            }

            $role['name'] = $input['name'];
            $role['display_name'] = $input['display_name'];
            $role['description'] = $input['description'];
            $role['level'] = $input['level'];
            $idRole = $modelRole->insertRoleGetId($role);
            
            foreach($idGroup as $id){
               $modelGroup->addRoleToGroup($id, $idRole);
            }
  
            DB::commit();
            $request->session()->flash('status', true);
        } catch (Exception $ex) {
            DB::rollBack();
            $request->session()->flash('status', false);
        }

        return redirect(main_prefix . '/roles/create');
    }

    public function edit($id) {
        $modelRole = new Role();
        $modelGroupRole = new GroupRole();
        
        $userRole = Session::get('userRole');
        if ($userRole['level'] == 1) {
            $roles = $modelRole->getAllRole();
        } else {
            $roles = $modelRole->getAllRoleByLevel($userRole['level']);
        }
        $group = $modelGroupRole->getAllGroupRole();
        $editRoles = $modelRole->getRoleGroupRoleByRoleId($id);
        $view = view('roles/edit', ['group' => $group, 'role' => $roles, 'id' => $id, 'editRoles' => $editRoles]);
        return $view;
    }

    public function update(Request $request) {
        $input = $request->all();
        foreach ($input as $key => $val) {
            if(!is_array($val)){
                $input[$key] = trim($val);
            }
        }
        $input['display_name'] = $input['name'];
        
        if (!$input['rate']) {
            $input['level'] += 1;
        }

        $resCheck = ExtraFunction::checkCanAction($input['level']);
        if (!$resCheck) {
            $request->session()->flash('alert', trans('roles.YouDoNotHavePermissionToCreateNewRoleEqualOrHigherThanYourLevel'));
            return redirect(main_prefix . '/roles/'.$input['id'].'/edit');
        }

        //Kiểm tra toàn bộ thông tin input theo yêu cầu
        $error = false;
        $validator = $this->checkCustomInput($input, $error);
        if ($error) {
            $this->throwValidationException($request, $validator);
        }

        DB::beginTransaction();
        try {
            $modelGroup = new GroupRole();
            $modelRole = new Role();
            if ($input['newGroup']) {
                $getRes = $modelGroup->getGroupRoleByName($input['group0']);
                if (empty($getRes)) {
                    $group['name'] = $input['group0'];
                    $group['display_name'] = $group['name'];
                    $group['description'] = $input['descriptionGroup'];
                    $tempId = $modelGroup->insertGroupRoleGetId($group);
                } else {
                    $group['id'] = $getRes->id;
                    $group['description'] = $input['descriptionGroup'];
                    $tempId = $modelGroup->updateGroupRoleGetId($group);
                }
                $idGroup = [$tempId];
            } else {
                $idGroup = $input['group1'];
                foreach($idGroup as $id){
                    $check = $modelGroup->getGroupRoleById($id);
                    if(empty($check)){
                        $request->session()->flash('alert', trans('roles.TheGroupNameIsNotExist'));
                        return redirect(main_prefix . '/roles/'.$input['id'].'/edit');
                    }
                }
                
                $modelGroup->removeRoleFromAllGroup($input['id']);
            }
            
            $role['id'] = $input['id'];
            $role['name'] = $input['name'];
            $role['display_name'] = $input['display_name'];
            $role['description'] = $input['description'];
            $role['level'] = $input['level'];
            $idRole = $modelRole->updateRoleGetId($role);
            
            foreach($idGroup as $id){
               $modelGroup->addRoleToGroup($id, $idRole);
            }
  
            DB::commit();
            $request->session()->flash('status', true);
        } catch (Exception $ex) {
            DB::rollBack();
            $request->session()->flash('status', false);
        }

        return redirect(main_prefix . '/roles/'.$input['id'].'/edit');
    }

//	public function destroy($id, Request $request){
//		if($id == '1' || $id == '10'){
//			return Response::json(array('state' => 'alert', 'error' => 'Không thể xóa vai trò cơ bản'));
//		}
//		$role = Role::find($id);
//		if(empty($role)){
//			return Response::json(array('state' => 'alert', 'error' => 'Không tìm thấy vai trò'));
//		}
//
//		$resCheck = ExtraFunction::checkCanAction($role->level);
//		if(!$resCheck){
//			return Response::json(array('state' => 'alert', 'error' => 'Bạn không có quyền xóa vai trò có cấp độ ngang hoặc cao hơn cấp độ của bạn'));
//		}
//		
//		DB::beginTransaction();
//		try{
////			DB::table('role_user')
////            ->where('role_id', $id)
////            ->update(['role_id' => 10]);
//			$role->perms()->sync([]);
//			DB::table('roles')->where('id', '=', $id)->delete();
//			DB::commit();
//			$request->session()->flash('del', true);
//			return Response::json(array('state' => 'success', 'data' => 'Xóa vai trò thành công'));
//		}catch(Exception $e){
//			DB::rollback();
//		}
//		
//		return Response::json(array('state' => 'fail', 'error' => 'Xóa vai trò thất bại'));
//	}

    private function checkCustomInput($input, &$error) {
        $validator = Validator::make($input, ['name' => 'required']);
        if ($input['newGroup']) {
            if (empty($input['group0'])) {
                $validator->errors()->add('newGroup', trans('roles.GroupNameCanNotBeBlank'));
                $error = true;
            }
        } else {
            if(isset($input['group1'])){
                
            }else{
                $validator->errors()->add('newGroup', trans('roles.PleaseChooseOneGroupOrCreateNewGroup'));
                $error = true;
            }
        }
        return $validator;
    }

}
