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

class SummaryBranches extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_branches';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getBranchId($IsclocationID, $IscBranchCode){
        $result =  SummaryBranches::where('isc_location_id', '=', $IsclocationID)
            ->where( 'isc_branch_code', '=', $IscBranchCode )
            ->first();
   
        if ( isset($result->branch_id))
        {
//             var_dump($result);die;
            return $result->branch_id;
        }

        return 0;

    }

    public function getAllBranch(){
        $result = DB::table($this->table)->get();
        return $result;
    }
}