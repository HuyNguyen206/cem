<?php

namespace App\Models\Authen;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Department extends Model {

    protected $table = 'departments';
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    public function getAllDepartments() {
        $res = DB::table($this->table)
                ->select('*')
                ->get();
        return $res;
    }

    public function getDepartmentsByRoleID($roleId) {
        $res = DB::table('role_department as rd')
            ->join('departments as d','d.id',"=", "rd.department_id")
            ->where('role_id',$roleId)
            ->select('*')
            ->get();
        return $res;
    }

    public function removeDepartmentFromRole($roleId){
        $res = DB::table('role_department')
            ->where('role_id',$roleId)
            ->delete();
        return $res;
    }

    public function addDepartmentToRole($roleId, $departmentId){
        $res = DB::table('role_department')
            ->insert(['role_id' => $roleId, 'department_id' => $departmentId]);
        return $res;
    }


}
