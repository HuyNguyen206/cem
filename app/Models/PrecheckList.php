<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class PrecheckList extends Model {

    protected $table = 'prechecklist';
    public $timestamps = false;
    protected $fillable = [
        'ObjID', 'Location_Name', 'First_Status', 'Location_Phone', 'DivisionID', 'Description',
        'Create_by'];

//    /*
//     * lấy thông tin khách hàng từ database survey
//     */
//    public function getAccountInfoByContractNum( $Contractnum ){
//    	return DB::table('outbound_accounts')->where('contract_num','=',$Contractnum)->first();
//    
//    }
    //Lưu CheckList
    public function savePCL($infoSave) {
//        try {
//            if(!empty($infoSave['ContractNum'])){
//                $temp = explode('/ ',$infoSave['ContractNum']);
//                $contract = $temp[0];
//                $createContract = $temp[1];
//            }
            $result = PrecheckList::create(
                    [
                        'ObjID' => isset($infoSave['ObjID']) ? $infoSave['ObjID'] : '',
                        'Location_Name' => isset($infoSave['Location_Name']) ? $infoSave['Location_Name'] : '',
                        'First_Status' => isset($infoSave['FirstStatus']) ? $infoSave['FirstStatus'] : '',
                        'Location_Phone' => isset($infoSave['Location_Phone']) ? $infoSave['Location_Phone'] : '',
                        'DivisionID' => isset($infoSave['DivisionID']) ? $infoSave['DivisionID'] : '',
                        'Description' => isset($infoSave['Description']) ? $infoSave['Description'] : '',
                        'Create_by' => isset($infoSave['CreateBy']) ? $infoSave['CreateBy'] : '',
            
            ]);
            $res['code'] = 200;
            $res['msg'] = 'Successful';
            $res['data'] = $result;
            return $res;
//        } catch (\Exception $ex) {
//            $res['code'] = 400;
//            $res['msg'] = $ex->getMessage();
//            $res['data'] = '';
//            return $res;
//        }
    }
    
    public function getPreCLWithCL($input)
    {
        $preclWithCl=[];
        $checklistEmpty = ['i_type' => '',
                    's_create_by' => '',
                    'i_lnit_status' => '',
                    's_description' => '',
                    'i_modem_type' => '',
                    'supporter' => '',
                    'sub_supporter' => '',
                    'dept_id' => '',
                    'request_from' => '',
                    'owner_type' => '',
                    'created_at' => '',
                    'updated_at' => '',
                    'final_status' => '',
                    'final_status_id' => '',
                    'total_minute' => '',
                    'input_time' => '',
                    'assign' => '',
                    'store_time'=>'',
             'error_position' => '',
             'error_description' => '',
             'reason_description' => '',
             'way_solving' => '',
             'checklist_type' => '',
             'repeat_checklist' => '',
             'finish_date' => '',
                ];
            $result = DB::table('prechecklist as pc')
                ->select('*')
                ->where('pc.section_contract_num', '=', $input[0])
                ->where('pc.section_survey_id', '=', $input[2])
                ->where('pc.section_code', '=', $input[1])
                ->get();
        foreach ($result as $key => $value) {
            $result[$key]=(array) $value;           
        }
        foreach ($result as $key => $value) {
            if($value['sup_id_partner'] != NULL && $value['sup_id_partner']  != '' && $value['sup_id_partner'] >0)
            {
               $checklistResult = DB::table('checklist as c')
                ->select('*')
                ->where('c.id_checklist_isc', '=', $value['sup_id_partner'])
                ->get(); 
               if(!empty($checklistResult))
               {
                   $checklistResult=(array)$checklistResult[0];
                  $array_merge= array_merge($value,$checklistResult );
                  array_push($preclWithCl, $array_merge);
               }
               else
               {
                   $array_merge= array_merge($value,$checklistEmpty );
                  array_push($preclWithCl, $array_merge); 
               }
                   
            }
            else
            {
                $array_merge= array_merge($value,$checklistEmpty );
                  array_push($preclWithCl, $array_merge); 
            }
        }
//        dump($preclWithCl);die;
             return $preclWithCl;
//        if (empty($result))
//            return false;
//        else {
//            return $result;
//        }
    }



}
