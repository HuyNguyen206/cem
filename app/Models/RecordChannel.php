<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RecordChannel extends Model {

    protected $table = 'outbound_record_channel';
    protected $primaryKey = 'record_channel_id';
	
    public function getAllRecordChannel(){
        $result = DB::table($this->table)
                ->get();
        return $result;
    }
}
