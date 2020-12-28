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

class SummaryObjects extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_objects';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getAllObject(){
        $result = DB::table($this->table)->get();
        return $result;
    }
}