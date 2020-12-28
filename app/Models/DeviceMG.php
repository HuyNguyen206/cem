<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class DeviceMG extends Eloquent {
	protected $collection = 'cem_devices';
	protected $connection = 'mongodb';
	
	protected $fillable = [
		'deviceUser', 'deviceIp','createdAt', 'updatedAt', 'createdAtFormat', 'updatedAtFormat',
    ];
	
	public $timestamps = false;
	
	public function getDeviceByParam($param){
		$query = DeviceMG::where('createdAt','<=', time());
		if(isset($param['deviceUser'])){
			$query->where('deviceUser', $param['deviceUser']);
		}
		if(isset($param['deviceIp'])){
			$query->where('deviceIp', $param['deviceIp']);
		}
		$res = $query->first();
		return $res;
	}
	
	public function insertDevice($param){
		$model = new DeviceMG();
		$model->deviceUser = $param['deviceUser'];
		$model->deviceIp = $param['deviceIp'];
		
		$time = time();
		$model->createdAt = $time;
		$model->updatedAt = $time;
		$date = date("Y-m-d H:i:s");
		$model->createdAtFormat = $date;
		$model->updatedAtFormat = $date;
		
		$suc = $model->save();
		return $suc;
	}
	
	public function updateDevice($param){
		$model = DeviceMG::find($param['id']);
		$model->deviceIp = $param['deviceIp'];
		
		$time = time();
		$model->updatedAt = $time;
		$date = date("Y-m-d H:i:s");
		$model->updatedAtFormat = $date;
		
		$suc = $model->save();
		return $suc;
	}
}
