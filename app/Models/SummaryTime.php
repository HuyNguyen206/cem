<?php
/**
 * Created by PhpStorm.
 * User: Minh Tuan
 * Date: 2017-06-16
 * Time: 2:42 PM
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SummaryTime extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_time';
    protected $fillable = ['date','time_temp','day','month','year'];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getTimeIdByDay($day){
        $result =  SummaryTime::firstOrCreate(['time_temp'=>  strtotime($day),'date' => $day,'day'=>  explode('-', $day)[2],'month'=>  explode('-', $day)[1],'year'=>  explode('-', $day)[0]]);
        return $result->id;
    }

    public function getMaxTimeID(){
        $result = DB::table($this->table)
            ->select('*')
            ->whereRaw('id in (select max(id) from summary_time)')
        ->first();
        return $result;
    }
}