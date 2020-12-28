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

class SummaryReason extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_reason';
    protected $fillable = ['time_id', 'object_id', 'branch_id', 'channel_id', 'poc_id', 'reason_id', 'group_id', 'total'];
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

    public function getReasonTotalbyParam($params) {
        $sqlRaw = "b.zone_id, b.branch_name, b.branch_code, b.isc_location_id, b.isc_branch_code,
        r.branch_id, r.object_id, r.poc_id, r.reason_id, r.group_id,sum(r.total) as total";
        $result = DB::table($this->table . ' as r')
            ->selectRaw($sqlRaw)
            ->join('summary_time as t', 'r.time_id', '=', 't.id')
            ->join('summary_branches as b', 'r.branch_id', '=', 'b.branch_id')
            ->where(function($query) use ($params) {
                if (!empty($params['dayFrom'])) {
                    $query->where('t.time_temp', '>=', strtotime($params['dayFrom']));
                    $query->where('t.time_temp', '<=', strtotime($params['dayTo']));
                }
            })
            ->where(function($query) use ($params) {
                if (!empty($params['arrayPOC'])) {
                    $query->whereIn('r.poc_id', $params['arrayPOC']);
                }
            })
            ->where(function($query) use ($params) {
                if (!empty($params['arrayObject'])) {
                    $query->whereIn('r.object_id', $params['arrayObject']);
                }
            })
            ->groupBy('r.branch_id','r.object_id','r.poc_id')
            ->orderBy('r.branch_id')
            ->get();
        return $result;
    }
}