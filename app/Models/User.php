<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
	use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'login_partner',
		'user_zone', 'user_brand','last_login'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	public static function getActiveUser(){
		$res = DB::table('role_user')
			->join('users', 'role_user.user_id', '=', 'users.id')
			->join('roles','role_user.role_id','=','roles.id')
			->where('users.status', '=', 0)
			->select('users.*', 'roles.level','roles.display_name','role_user.*')
			->orderBy('users.created_at','DESC')
            ->get();
    	return $res;
	}

	public static function getRole($userID) {
		$roleID = DB::table('role_user')
				->where('user_id', '=', $userID)
				->select('role_id')
				->first();
		return $roleID->role_id;
	}

	public function getUserByName($name) {
		$res = DB::table('users')
			->where('users.name', '=', $name)
			->select('*')
            ->first();
    	return $res;
	}
	
	public function getUserWithZoneRole(){
		$allUser = DB::table('users')
			->join('role_user', 'role_user.user_id', '=', 'users.id')
			->join('roles','role_user.role_id','=','roles.id')
			->select('users.name', 'users.email', 'users.last_login','users.user_zone','users.user_brand','users.user_brand_plus', 'roles.level','roles.display_name')
			->where('roles.display_name','<>', 'Member')
			->orderBy('roles.level','ASC')
            ->get();
		
		$allBrand = DB::table('location as l')
			->leftjoin('location_branches as lb','lb.location_id','=','l.id')
			->select('l.region','l.id', 'l.name','lb.name as chinhanh', 'lb.id as chinhanhid')
			->get();
		
		$resultUser = [];
		foreach($allUser as $user){
			$brand = null;
			$result = [];
			if(!empty($user->user_brand)){
				$brands = json_decode($user->user_brand);
				foreach($brands as $brand){
					foreach($allBrand as $val){
						if($val->id == $brand && $brand != '4' && $brand != '8'){
							$expZone = explode(' - ', $val->name);
							$expRegion = explode(' ', $val->region);
							if(isset($result[$expRegion[1]])){
								$result[$expRegion[1]] .= ', '.$expZone[1];
							}else{
								$result[$expRegion[1]] = $expZone[1];
							}
						}
					}
				}
			}
			$brand_plus = null;
			if(!empty($user->user_brand_plus)){
				$brand_plus = json_decode($user->user_brand_plus);
				foreach($brand_plus as $plus){
					foreach($allBrand as $val){
						if($val->chinhanhid == $plus){
							$expZone = explode(' - ', $val->name);
							$expRegion = explode(' ', $val->region);
							if(isset($result[$expRegion[1]])){
								$result[$expRegion[1]] .= ', '.$val->chinhanh;
							}else{
								$result[$expRegion[1]] = $expZone[1].': '.$val->chinhanh;
							}
						}
					}
				}
			}
			
			$temp = [
				'name' => $user->name,
				'email' => $user->email,
				'last_login' => $user->last_login,
				'user_zone' => $result,
				'role' => $user->display_name,
			];
			
			array_push($resultUser, $temp);
		}
		
		return $resultUser;
	}
}
