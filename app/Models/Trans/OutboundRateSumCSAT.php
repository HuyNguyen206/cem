<?php

namespace App\Models\Trans;

use Illuminate\Database\Eloquent\Model;
use DB;

class OutboundRateSumCSAT extends Model {

    protected $table = 'rate_sum_csat';
    protected $fillable = [
        'rate_sum_csat_rate_object_id',
        'rate_sum_csat_date',
        'rate_sum_csat_point_1',
        'rate_sum_csat_point_2',
        'rate_sum_csat_point_3',
        'rate_sum_csat_point_4',
        'rate_sum_csat_point_5',
        'rate_sum_csat_region',
        'rate_sum_csat_location',
        'rate_sum_csat_branch'
    ];
    
    protected $primaryKey = 'rate_sum_csat_id';
    public $timestamps = false;
    
    public function insertRateSumCSAT($param){
        $model = new OutboundRateSumCSAT();
        foreach($this->fillable as $field){
            $model->$field = $param[$field];
        }
        
        dump($model);die;
        
        $model->save();
    }
    
    public function getNewRecord(){
        $result = DB::table($this->table)
                ->select('rate_sum_csat_date')
                ->orderBy('rate_sum_csat_date','DESC')
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
