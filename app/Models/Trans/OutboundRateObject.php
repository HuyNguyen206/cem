<?php

namespace App\Models\Trans;

use Illuminate\Database\Eloquent\Model;
use DB;

class OutboundRateObject extends Model {

    protected $table = 'rate_object';
    public $timestamps = false;
    protected $fillable = [
        'rate_object_name',
        'rate_object_description',
        'rate_object_created_at',
        'rate_object_updated_at',
    ];
    
    protected $primaryKey = 'rate_object_id';
    
    public function getAllRateObject(){
        $result = DB::table($this->table)
                ->select('rate_object_id', 'rate_object_name', 'rate_object_description', 'rate_object_type')
                ->get();
        return $result;
    }
    
    public function getRateObjectCSAT(){
        $result = DB::table($this->table)
                ->select('rate_object_id', 'rate_object_name', 'rate_object_description', 'rate_object_type')
                ->where('rate_object_type', 1)
                ->get();
        return $result;
    }
    
    public function getRateObjectNPS(){
        $result = DB::table($this->table)
                ->select('rate_object_id', 'rate_object_name', 'rate_object_description', 'rate_object_type')
                ->where('rate_object_type', 2)
                ->get();
        return $result;
    }
}
