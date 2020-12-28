<?php

namespace App\Models\Trans;

use Illuminate\Database\Eloquent\Model;
use DB;

class OutboundRateSumNPS extends Model {

    protected $table = 'rate_sum_nps';
    protected $fillable = [
        'rate_sum_nps_rate_object_id',
        'rate_sum_nps_date',
        'rate_sum_nps_point_0',
        'rate_sum_nps_point_1',
        'rate_sum_nps_point_2',
        'rate_sum_nps_point_3',
        'rate_sum_nps_point_4',
        'rate_sum_nps_point_5',
        'rate_sum_nps_point_6',
        'rate_sum_nps_point_7',
        'rate_sum_nps_point_8',
        'rate_sum_nps_point_9',
        'rate_sum_nps_region',
        'rate_sum_nps_location',
        'rate_sum_nps_branch'
    ];
    
    protected $primaryKey = 'rate_sum_nps_id';
    public $timestamps = false;
    
    public function insertRateSumNPS($param){
        $model = new OutboundRateSumNPS();
        foreach($this->fillable as $field){
            $model->$field = $param[$field];
        }
        
        dump($model);die;
        
        $model->save();
    }
    
    public function getNewRecord(){
        $result = DB::table($this->table)
                ->select('rate_sum_nps_date')
                ->orderBy('rate_sum_nps_date','DESC')
                ->first();
        return $result;
    }
    
    public function removeRecord($day){
        $result = DB::table($this->table)
                ->where('rate_sum_csat_date', '=', $day)
                ->delete();
        return $result;
    }
}
