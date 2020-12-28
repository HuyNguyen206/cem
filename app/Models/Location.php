<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use DB;

class Location extends Model {

    protected $table = 'location';
    protected $primaryKey = 'id';

    protected $oldLocation = [
        ['id'=> 31, 'name' => 'HPG - Hải Phòng (cũ)', 'region' => 'Vùng 3', 'branchcode' => null, 'branch_id' => null]
    ];

    public function getAllLocation()
    {
        $result = DB::table('location')->select('id', 'name')
            ->get();
        return $result;

    }

    public function getAllLocationByPermission($userID)
    {
        $locationPermission =  DB::table('users')->select('user_brand')->where('id', $userID)
            ->get();
//        dump($locationPermission);

        $result = DB::table('location')->select('id', 'name')->whereIn('id',json_decode($locationPermission[0]->user_brand))
            ->get();
//        dump($result);die;
        return $result;

    }

    public function getNameLocationByID($locationID)
    {
        $result = DB::table('location')->select('name')
            ->whereIn('id', $locationID)
            ->get();
        $resultEdit = [];
        foreach($result as $val)
        {
            array_push($resultEdit, $val->name);
        }
        return  $resultEdit;

    }

    public function getAllLocationPlusForPermission(){
        $result = DB::table('location AS l')
            ->leftJoin('location_branches AS lb', 'l.id', '=', 'lb.location_id')
            ->select('lb.id as area_id_plus', 'l.id','lb.name as area_name_plus', 'l.name', 'region', 'branchcode')
            ->orderBy(DB::raw('region, name, branchcode'))
            ->get();
        return $result;
    }

    public function getLocationByBranchID($branchID) {
        $result = '';
        if(!empty($branchID)){
            $result = DB::table('location_branches AS lb')
                ->select('branchcode','location_id', 'id')
                ->where(function($query) use ($branchID) {
                    if(!empty($branchID)){
                        $b = explode(',', $branchID);
                        $query->whereIn('lb.id', $b);
                    }
                })
                ->get();
        }
        return $result;
    }

    public function getBranchCodeSaleMan() {
        $result = DB::table('location_branches_saleman AS lb')
            ->select('branchcode','name')
            ->orderBy('branchcode')
            ->get();
        return $result;
    }
}
