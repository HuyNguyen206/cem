<?php 
namespace App\Models;

use Zizaco\Entrust\EntrustRole;
use Illuminate\Support\Facades\DB;

class Role extends EntrustRole
{
	protected $table = 'roles';
	
	protected $fillable = [
		'name',
		'display_name',
		'description',
		'level',
		'created_at',
		'updated_at'
	];
	
	public static function getAllRoleById($id){
		$res = DB::table('role_user')
			->join('users', 'role_user.user_id', '=', 'users.id')
			->join('roles','role_user.role_id','=','roles.id')
			->where('role_user.user_id', '=', $id)
            ->select('roles.display_name', 'level')
            ->get();
    	return $res;
	}
	
	public function insertRole($param){
		$resIns = DB::table($this->table)->insert([$param]);
		return $resIns;
	}
	
}