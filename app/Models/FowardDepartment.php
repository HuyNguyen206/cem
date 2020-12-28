<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class FowardDepartment extends Model {

    protected $table = 'foward_department';
//    public $timestamps = false;
//    protected $fillable = [
//        'iObjId', 'iType', 'sCreateBy', 'iInit_Status', 'sDescription', 'i_modem_type',
//        'bit_sub_assign', 'bit_cLElectric', 'bit_upgrade', 'supporter', 'sub_supporter',
//        'DeptID', 'request_from', 'owner_type'];
public function getFD($input)
    {
        $fdArrayResult=[];
            $result = DB::table('foward_department as fd')
                ->select('*')
                ->where('fd.section_contract_num', '=', $input[0])
                ->where('fd.section_survey_id', '=', $input[2])
                ->where('fd.section_code', '=', $input[1])
                ->get();
        foreach ($result as $key => $value) {
            $result[$key]=(array) $value;           
        }

             return $result;
    }
}
