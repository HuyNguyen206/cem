<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ExceptMG extends Eloquent {

    protected $collection = 'cem_excepts';
    protected $connection = 'mongodb';
    protected $fillable = [
        'section_id', 'section_Contract_num', 'section_code',
        'createdAt', 'updatedAt','updatedAtFormat' ,'createdAtFormat',
    ];
    public $timestamps = false;

    public function getExceptMGFirst() {
        $query = ExceptMG::where('createdAt', '<=', time());
        $query->orderBy('section_id', 'DESC');
        $res = $query->first();
        return $res;
    }
    
    public function getAllExceptMG($time) {
        $query = ExceptMG::where('createdAt', '>=', $time);
        $query->orderBy('section_id', 'DESC');
        $query->select('section_id', 'section_Contract_num', 'section_code');
        $res = $query->get();
        return $res;
    }
    
    
    public function insertExceptMG($param) {
        $model = new ExceptMG();
        $model->section_id = $param['section_id'];
        $model->section_Contract_num = $param['section_Contract_num'];
        $model->section_code = $param['section_code'];
        
        $time = time();
        $model->createdAt = $time;
        $model->updatedAt = $time;
        $date = date("Y-m-d H:i:s");
        $model->createdAtFormat = $date;
        $model->updatedAtFormat = $date;
        
        $suc = $model->save();
        return $suc;
    }
}
