<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authen\Permission;
use App\Http\Requests\CheckPermissionsRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Authen\GroupPermission;
use Illuminate\Support\Facades\DB;
use Exception;

class Permissions extends Controller {

    public function index() {
        $permissions = Permission::all();
        return view("permissions/index")->with("data", $permissions);
    }

    public function create() {
        $modelGroup = new GroupPermission();
        $group = $modelGroup->getAllGroupPermission();
        return view("permissions/create", ['group' => $group]);
    }

    public function store(CheckPermissionsRequest $request) {
        $input = $request->all();

        foreach ($input as $key => $val) {
            $input[$key] = trim($val);
        }

        //Kiểm tra toàn bộ thông tin input theo yêu cầu
        $error = false;
        $validator = $this->checkCustomeInput($input, $error);
        if ($error) {
            $this->throwValidationException($request, $validator);
        }

        DB::beginTransaction();
        try {
            $modelGroup = new GroupPermission();
            $modelPermission = new Permission();
            if ($input['newGroup']) {
                $getRes = $modelGroup->getGroupPermissionByName($input['group0']);
                if (empty($getRes)) {
                    $group['name'] = strtolower($input['group0']);
                    $group['display_name'] = $group['name'];
                    $group['description'] = $input['descriptionGroup'];
                    $idGroup = $modelGroup->insertGroupPermissionGetId($group);
                } else {
                    $idGroup = $getRes->id;
                }
            } else {
                $idGroup = $input['group1'];
            }

            $permission['name'] = strtolower($input['name']);
            $permission['display_name'] = $input['display_name'];
            $permission['description'] = $input['description'];
            $idPermission = $modelPermission->insertPermissionGetId($permission);

            $modelGroup->addPermissionToGroup($idGroup, $idPermission);

            DB::commit();
            $request->session()->flash('status', true);
        } catch (Exception $ex) {
            DB::rollBack();
            $request->session()->flash('status', false);
        }
        return redirect(main_prefix . '/permissions/create');
    }

    public function edit($id) {
        $modelPermission = new Permission();
        $modelGroup = new GroupPermission();
        $permission = $modelPermission->getPermissionGroupPermissionByPermissionId($id);
        $group = $modelGroup->getAllGroupPermission();
        return view('permissions/edit', ['group' => $group, 'permission' => $permission, 'id' => $id]);
    }

    public function update(CheckPermissionsRequest $request) {
        $input = $request->all();
        $input['id'];

        foreach ($input as $key => $val) {
            $input[$key] = trim($val);
        }

        //Kiểm tra toàn bộ thông tin input theo yêu cầu
        $error = false;
        $validator = $this->checkCustomeInput($input, $error);
        if ($error) {
            $this->throwValidationException($request, $validator);
        }

        DB::beginTransaction();
        try {
            $modelGroup = new GroupPermission();
            $modelPermission = new Permission();
            if ($input['newGroup']) {
                $getRes = $modelGroup->getGroupPermissionByName($input['group0']);
                if (empty($getRes)) {
                    $group['name'] = strtolower($input['group0']);
                    $group['display_name'] = $group['name'];
                    $group['description'] = $input['descriptionGroup'];
                    $idGroup = $modelGroup->insertGroupPermissionGetId($group);
                } else {
                    $group['id'] = $getRes->id;
                    $group['description'] = $input['descriptionGroup'];
                    $idGroup = $modelGroup->updateGroupPermissionGetId($group);
                }
            } else {
                $idGroup = $input['group1'];
            }

            $permission['id'] = $input['id'];
            $permission['name'] = strtolower($input['name']);
            $permission['display_name'] = $input['display_name'];
            $permission['description'] = $input['description'];
            $idPermission = $modelPermission->updatePermissionGetId($permission);

            $check = $modelGroup->checkPermissionBelongToGroup($idPermission);
            if(empty($check)){
                $modelGroup->addPermissionToGroup($idGroup, $idPermission);
            }else{
                if($check->grouppermission_id != $idGroup){
                    $modelGroup->changePermissionToGroup($idGroup, $idPermission);
                }
            }
            
            DB::commit();
            $request->session()->flash('statusEdit', true);
        } catch (Exception $ex) {
            DB::rollBack();
            $request->session()->flash('statusEdit', false);
        }
        return redirect(main_prefix . '/permissions/' . $input['id'] . '/edit');
    }

    public function delete(){

    }

    private function checkCustomeInput($input, &$error) {
        $temp = explode('-', trim($input['name']));
        $validator = Validator::make($input, ['name' => 'required']);
        if (count($temp) != 2) {
            $validator->errors()->add('name', trans('permissions.invalid name'));
            $error = true;
        }

        if ($input['newGroup']) {
            if (empty($input['group0'])) {
                $validator->errors()->add('newGroup', trans('permissions.GroupNameCanNotBeBlank'));
                $error = true;
            }
        } else {
            if ($input['group1'] == '0') {
                $validator->errors()->add('newGroup', trans('permissions.PleaseCreateNewGroup'));
                $error = true;
            }
        }
        return $validator;
    }

}
