<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CheckList extends Model {

    protected $table = 'checklist';
    public $timestamps = false;
    protected $fillable = [
        'iObjId', 'iType', 'sCreateBy', 'iInit_Status', 'sDescription', 'i_modem_type',
        'bit_sub_assign', 'bit_cLElectric', 'bit_upgrade', 'supporter', 'sub_supporter',
        'DeptID', 'request_from', 'owner_type'];

//    /*
//     * lấy thông tin khách hàng từ database survey
//     */
//    public function getAccountInfoByContractNum( $Contractnum ){
//    	return DB::table('outbound_accounts')->where('contract_num','=',$Contractnum)->first();
//    
//    }
    //Lưu CheckList
    public function saveCL($infoSave) {
//        try {
//            if(!empty($infoSave['ContractNum'])){
//                $temp = explode('/ ',$infoSave['ContractNum']);
//                $contract = $temp[0];
//                $createContract = $temp[1];
//            }
            $result = CheckList::create(
                    [
                        'iObjId' => isset($infoSave['iObjId']) ? $infoSave['iObjId'] : '',
                        'iType' => isset($infoSave['iType']) ? $infoSave['iType'] : '',
                        'sCreateBy' => isset($infoSave['sCreateBy']) ? $infoSave['sCreateBy'] : '',
                        'iInit_Status' => isset($infoSave['iInit_Status']) ? $infoSave['iInit_Status'] : '',
                        'sDescription' => isset($infoSave['sDescription']) ? $infoSave['sDescription'] : '',
                        'i_modem_type' => isset($infoSave['iModemType']) ? $infoSave['iModemType'] : '', //'ten cong ty',
                        'bit_sub_assign' => isset($infoSave['bitSubAssign']) ? $infoSave['bitSubAssign'] : '', // 'số giấy đăng ký kinh doanh của KH đại lý (KH đăng ký gói Public)',
                        'bit_cLElectric' => isset($infoSave['bitCLElectric']) ? $infoSave['bitCLElectric'] : '',
                        'bit_upgrade' => isset($infoSave['bitUpgrade']) ? $infoSave['bitUpgrade'] : '',
                        'supporter' => isset($infoSave['Supporter']) ? $infoSave['Supporter'] : '',
                        'sub_supporter' => isset($infoSave['SubSupporter']) ? $infoSave['SubSupporter'] : '',
                        'DeptID' => isset($infoSave['DeptID']) ? $infoSave['DeptID'] : '',
                        'request_from' => isset($infoSave['RequestFrom']) ? $infoSave['RequestFrom'] : '',
                        'owner_type' => isset($infoSave['OwnerType']) ? $infoSave['OwnerType'] : '',
                        
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

    public function getAccountInfoByContract($contarct) {
        $result = OutboundAccount::select()->where('so_hd', "=", $contarct)->first();
        if (isset($result->id))
            return $result;
        return NULL;
    }

}
