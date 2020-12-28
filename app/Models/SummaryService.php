<?php
/**
 * Created by PhpStorm.
 * User: Minh Tuan
 * Date: 2017-06-16
 * Time: 2:24 PM
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SummaryService extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_service';
    protected $fillable = ['time_id', 'object_id', 'branch_id', 'channel_id', 'poc_id', 'service_id', 'group_id', 'total'];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    public function getTableColumns()
    {
        $result = DB::getSchemaBuilder()->getColumnListing($this->table);
        return $result;
    }

    public function getFieldName(){
        return $this->fillable;
    }
}