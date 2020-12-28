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
use Exception;

class SummaryAction extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_action';
    protected $fillable = ['time_id', 'object_id', 'branch_id', 'channel_id', 'poc_id', 'action_id', 'group_id', 'total'];
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

    public function getActionTotalbyParam($params) {
        $sqlRaw = "b.zone_id, b.branch_name, b.branch_code, b.isc_location_id, b.isc_branch_code,
        a.branch_id, a.object_id, a.poc_id, a.action_id, a.group_id, sum(a.total) as total";
        $result = DB::table($this->table . ' as a')
            ->selectRaw($sqlRaw)
            ->join('summary_time as t', 'a.time_id', '=', 't.id')
            ->join('summary_branches as b', 'a.branch_id', '=', 'b.branch_id')
            ->where(function($query) use ($params) {
                if (!empty($params['dayFrom'])) {
                    $query->where('t.time_temp', '>=', strtotime($params['dayFrom']));
                    $query->where('t.time_temp', '<=', strtotime($params['dayTo']));
                }
            })
            ->where(function($query) use ($params) {
                if (!empty($params['arrayPOC'])) {
                    $query->whereIn('a.poc_id', $params['arrayPOC']);
                }
            })
            ->where(function($query) use ($params) {
                if (!empty($params['arrayObject'])) {
                    $query->whereIn('a.object_id', $params['arrayObject']);
                }
            })
            ->groupBy('a.branch_id','a.object_id','a.poc_id', 'a.action_id')
            ->orderBy('a.branch_id')
            ->get();
        return $result;
    }
}