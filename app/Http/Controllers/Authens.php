<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authen\Role;
use App\Models\Authen\User;
use Validator;
use Illuminate\Support\Facades\DB;
use \App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Component\ExtraFunction;
use App\Models\Location;
use App\Models\Authen\GroupPermission;
use App\Models\Authen\Permission;
use App\Models\Authen\Department;

class Authens extends Controller {

    protected function viewSurvey() {
        return view('outbound');
    }

    protected function validator(array $data) {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    public function getUserPermission() {
        $user = Auth::user();
        // Lấy toàn bộ quyền hạn của người đang dùng hệ thống
        $userPermission = User::getAllPermissionByUserId($user->id);

        // Lấy toàn bộ thông tin nhóm của quyền hạn
        $allPermission = GroupPermission::getAllGroupPermissionHavePermission();

        $roles = Role::all()->toArray();
        $zone = Zone::all()->toArray();

        $modelLocation = new Location();
        // Lấy thông tin các chi nhánh 
        $brand = $modelLocation->getAllLocationPlusForPermission();
        $modelUser = new User();
        // Lấy toàn bộ thông tin của người dùng với thông tin chi nhánh
        $userRole = $modelUser->getUserWithFullBrandPlus();
        $allUserRole = [];
        foreach($userRole as $val){
            $val = (array)$val;
            array_push($allUserRole, $val);
        }

        $modelRole = new Role();
        // Lấy toàn bộ quyền hạn theo vai trò
        $perRole = $modelRole->getPermissionByRole();

        $data = [
            'userPermission' => $userPermission,
            'allPermission' => $allPermission,
            'roles' => $roles,
            'zone' => $zone,
            'brand' => $brand,
            'allUserRole' => $allUserRole,
            'perrole' => $perRole
        ];

        return view("authens/indexUP", $data);
    }

    public function saveUserPermission(Request $request) {
        $input = $request->all();
        $userId = $input['baseUser'];
        $roleId = $input['baseRole'];
        
        $newRole = Role::getRoleById($roleId);
        if(empty($newRole)){
            $request->session()->flash('alert', 'Không tìm thấy thông tin vai trò');
            return redirect(main_prefix . '/authens/view-user-permission');
        }
        $oldRole = Role::getAllRoleByUserId($userId);
        if(empty($oldRole)){
            $request->session()->flash('alert', 'Không tìm thấy thông tin vai trò của người quản trị');
            return redirect(main_prefix . '/authens/view-user-permission');
        }
        
        // Kiểm tra xem người dùng được phép thay đổi vai trò của người sử dụng hay không
        $change = false;
        $resOldCheck = ExtraFunction::checkCanAction($oldRole->level);
        if (!$resOldCheck) {
            $request->session()->flash('alert', 'Bạn không có quyền thay đổi vai trò, quyền hạn của thành viên có cấp độ ngang hoặc cao hơn cấp độ của bạn');
            return redirect(main_prefix . '/authens/view-user-permission');
        }
        if ($newRole->id != $oldRole->id) {
            $resNewCheck = ExtraFunction::checkCanAction($newRole->level);
            if (!$resNewCheck) {
                $request->session()->flash('alert', 'Bạn không có quyền thay đổi vai trò của thành viên có cấp độ thấp lên cấp độ ngang hoặc cao hơn cấp độ của bạn');
                return redirect(main_prefix . '/authens/view-user-permission');
            }
            $change = true;
        }
        DB::beginTransaction();
        try {
            $modelRole = new Role();
            $modelUser = new User();
            $modelPermission = new Permission();
            
            //Thay đổi vai trò của người được chọn nếu có thay đổi
            if($change){
                $modelRole->changeRolebyUserId($userId, $roleId);
            }
            
            //Lấy tất cả các quyền của vai trò được chọn
            $allPermissionByRole = Permission::getAllPermissionByRoleId($roleId);
            
            //Lấy các quyền được chọn
            $allPermissionByChose = $this->getUserPermissionFromInput($input);
            
            //Kiểm tra các quyền thuộc về vai trò nhưng người dùng không được cấp
            $missing = [];
            foreach($allPermissionByRole as $val){
                $search = array_search($val->permission_id, $allPermissionByChose);
                if($search === false){
                    array_push($missing, $val->permission_id);
                }else{
                    array_pull($allPermissionByChose, $search);
                }
            }
            
            //Xóa quyền hạn hiện tại của người được chọn
            $modelPermission->removePermissionFromUser($userId);
            //Thêm các quyền hạn ngoài các quyền hạn của vai trò cho người được chọn
            foreach($allPermissionByChose as $per){
                $modelPermission->addPermissionToUser($userId, $per);
            }
            
            //Cập nhật những quyền hạn thuộc về vai trò mà người dùng không được cấp
            $param['id'] = $userId;
            $param['user_except_permission'] = null;
            if(!empty($missing)){
                foreach($missing as $val){
                    if(empty($param['user_except_permission'])){
                        $param['user_except_permission'] = $val;
                    }else{
                        $param['user_except_permission'] .= ','.$val;
                    }
                }
            }
            $modelUser->saveUserExceptPermission($param);
            
            //Lấy thông tin chi nhánh được chọn
            $allBrand = $this->getUserBrandFromInput($input);
            //Cập nhật lại thông tin chi nhánh cho người được chọn
            $allBrand['id'] = $userId;
            $modelUser->saveUserBrand($allBrand);
            
            $request->session()->flash('status', true);
            DB::commit();
        } catch (Exception $e) {
            $request->session()->flash('status', false);
            DB::rollback();
        }
        return redirect(main_prefix . '/authens/view-user-permission');
    }
    
    public function getRolePermission() {
        $user = Auth::user();
        // Lấy toàn bộ quyền hạn của người đang dùng hệ thống
        $userPermission = User::getAllPermissionByUserId($user->id);

        // Lấy toàn bộ thông tin nhóm của quyền hạn
        $allPermission = GroupPermission::getAllGroupPermissionHavePermission();

        $roles = Role::all()->toArray();

        $modelRole = new Role();
        // Lấy toàn bộ quyền hạn theo vai trò
        $permissionRole = $modelRole->getPermissionByRole();

        $departments = Department::all()->toArray();
        // Lấy toàn bộ phòng ban theo vai trò
        $departmentRole = $modelRole->getDepartmentByRole();

        //Lấy vai trò của người dùng
        $userRole = Session::get('userRole');

        $data = [
            'userPermission' => $userPermission,
            'allPermission' => $allPermission,
            'roles' => $roles,
            'departments' => $departments,
            'permissionRole' => $permissionRole,
            'userRole' => $userRole,
            'departmentRole' => $departmentRole,
        ];

        return view("authens/indexRP", $data);
    }

    public function saveRolePermission(Request $request) {
        $input = $request->all();
        $roleId = $input['baseRole'];

        $newRole = Role::getRoleById($roleId);
        if(empty($newRole)){
            $request->session()->flash('alert', 'Không tìm thấy thông tin vai trò');
            return redirect(main_prefix . '/authens/view-role-permission');
        }
        
        // Kiểm tra xem người dùng được phép thay đổi quyền hạn hay không
        $resCheck = ExtraFunction::checkCanAction($newRole->level);
        if (!$resCheck) {
            $request->session()->flash('alert', 'Bạn không có quyền thay đổi quyền hạn của vai trò có cấp độ ngang hoặc cao hơn cấp độ của bạn');
            return redirect(main_prefix . '/authens/view-role-permission');
        }
        DB::beginTransaction();
        try {
            $modelPermission = new Permission();
            //Lấy các quyền được chọn
            $allPermission = $this->getUserPermissionFromInput($input);
            //Xóa quyền hạn hiện tại của vai trò được chọn
            $modelPermission->removePermissionFromRole($roleId);
            //Thêm quyền hạn mới cho vai trò được chọn
            foreach($allPermission as $per){
                $modelPermission->addPermissionToRole($roleId, $per);
            }

            $modelDepartment = new Department();
            //Xóa phòng ban hiện tại của vai trò được chọn
            $modelDepartment->removeDepartmentFromRole($roleId);
            //Thêm phòng ban mới cho vai trò được chọn
            foreach($input['department'] as $dep){
                $modelDepartment->addDepartmentToRole($roleId, $dep);
            }

            $request->session()->flash('status', true);
            DB::commit();
        } catch (Exception $e) {
            $request->session()->flash('status', false);
            DB::rollback();
        }
        return redirect(main_prefix . '/authens/view-role-permission');
    }

    public function checkPersonRole(Request $request){
        $input = $request->all();
        $userPermission = User::getAllPermissionByParam($input);
        if(empty($userPermission)){
            $userPermission = null;
        }
        return response($userPermission);
    }

    private function getUserBrandFromInput($input) {
        $user_zone = [];
        $user_brand = [];
        $user_brand_plus = [];
        foreach ($input as $key => $val) {
            $temp = explode('_', $key);
            if (count($temp) == 2) {
                if ($temp[0] == 'zone') {
                    $zone = $temp[1];
                    array_push($user_zone, $zone);
                    foreach($val as $branchId){
                        $tempVal = explode(':', $branchId);
                        if(count($tempVal) == 2){
                            $search = array_search($tempVal[1], $user_brand);
                            if($search === false){
                                $user_brand[] = $tempVal[1];
                            }
                            $user_brand_plus[] = $tempVal[0];
                        }else{
                            $user_brand[] = $branchId;
                        }
                    }
                }
            }
        }
        
        $res['user_zone'] = $user_zone;
        $res['user_brand'] = $user_brand;
        $res['user_brand_plus'] = $user_brand_plus;
        return $res;
    }

    private function getUserPermissionFromInput($input) {
        $permission = [];
        foreach ($input as $key => $val) {
            $temp = explode('_', $key);
            if (count($temp) == 2) {
                if ($temp[0] == 'per') {
                    foreach($val as $per){
                        array_push($permission, $per);
                    }
                }
            }
        }
        
        $res = $permission;
        return $res;
    }
}
