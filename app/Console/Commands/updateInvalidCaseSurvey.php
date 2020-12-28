<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ListInvalidSurveyCase;
use App\Models\SurveySections;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Exception;
use DB;

class updateInvalidCaseSurvey extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'survey:updateInvalidCase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing info case';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $errorBrachCode = [];
        $errorLocationID = [];
        $errorUserName = [];
        $errorSupporter = [];
        $result = DB::table('list_invalid_survey_case')
                ->select('*')
                ->where('type_error', 'like', '%1%')
                ->orWhere('type_error', 'like', '%2%')
                ->get();
        foreach ($result as $key => $value) {
            $arrayError = explode(',', $value->type_error);
            $caseToDelete = ListInvalidSurveyCase::find($value->id);
            if (in_array(1, $arrayError) || in_array(2, $arrayError)) {
                $infoAcc = array('ObjID' => 0,
                    'Contract' => $value->contract_number,
                    'IDSupportlist' => $value->section_code,
                    'Type' => $value->survey_id

//                 'Contract' => 'BND029485',
//                'IDSupportlist' => '1114583322',
//                'Type' => 9
                );
                DB::beginTransaction();
                /*
                 * Lấy thông tin khách hàng
                 */
                try {
                    $result = json_decode($this->GetFullAccountInfo($infoAcc), true);
//                    $responseAccountInfo=$this->postAPI($infoAcc, $url);
//                    $result='{"data":[{"ObjID":1035405592,"ContractNum":"SGH454210","ContractDate":"10-MAR-18 12.00.00.000000 AM","CustomerName":"HOANG VAN HUY","Passport":"031087004093","CompanyName":null,"CertificateNumber":null,"Address":"Lo A2, T.18, P.1812A CC Sunview Town, Go Dua, P.Tam Binh, TD, HCM","BillTo":"Lo A2, T.18, P.1812A CC Sunview Town, Go Dua, P.Tam Binh, TD, HCM","ContractType":94,"ContractTypeName":"FTTH - F4","ContractStatus":1,"ContractStatusName":"Binh thuong","LoginName":"Sgfdl-180308-210","Email":null,"Location":"HCM - Ho Chi Minh","Region":"Mien Nam","LocationID":8,"BranchCode":5,"Suspend_Date":null,"Suspend_Reason":null,"ObjAddress":"Lo A2, T.18, P.1812A CC Sunview Town, Go Dua, P.Tam Binh, TD, HCM","LegalEntityName":"FPT","PartnerName":"Khong co","EocName":"Eoc","FeeLocalType":"FTTH - F4","Description":null,"Birthday":null,"Sex":0.0,"AccountSale":"HCM.Datct4","PACKAGESAL":null,"AccountINF":"Ly.ld","FinishDateINF":"11-MAR-18 06.18.09.997000 PM","CenterINF":"TIN/PNC","Phone":"0936794950,0941829698","PaymentType":"Auto","AccountPayment":null,"SubParentDesc":"Vung 5","UseService":2.0,"EmailINF":null,"EmailSale":"DatCT4@fpt.com.vn","Supporter":"To FTTH-PhuongNam-DD","SubSupporter":"001","KindDeploy":"Internet","CenterID":4,"BranchCodeSale":5}],"statusCode":200}';
                    $responseAccountInfo = $result['data'];
                    $responseAccountInfo = $responseAccountInfo[0];
                    $validData = [];
                    if ((!isset($responseAccountInfo['Supporter']) || !isset($responseAccountInfo['SubSupporter'])) || (empty($responseAccountInfo['Supporter']) || empty($responseAccountInfo['SubSupporter'])))
                        array_push($validData, 1);
                    if (!isset($responseAccountInfo['LocationID']) || !isset($responseAccountInfo['BranchCode'])) {
                        array_push($validData, 2);
                    } else if (isset($request->surveyid) && $request->surveyid != 6) {
                        if ((!in_array($dataAccount['LocationID'], [4, 8, 31, 65]) || $dataAccount['LocationID'] == null ) && $dataAccount['BranchCode'] != 0)
                            array_push($type, 2);
                        if (in_array($dataAccount['LocationID'], [4, 8]) && ($dataAccount['BranchCode'] == 0 || $dataAccount['BranchCodeSale'] == null )) {
                            array_push($type, 2);
                        }
                        if ($dataAccount['LocationID'] == null)
                            array_push($type, 4);
                    }

                    //Có đủ dữ liệu đúng trả về 
                    if (empty($validData)) {
                        $surveySection = SurveySections::find($value->section_id);
                        $surveySection->section_supporter = isset($responseAccountInfo['Supporter']) ? $responseAccountInfo['Supporter'] : null;
                        $surveySection->section_subsupporter = isset($responseAccountInfo['SubSupporter']) ? $responseAccountInfo['SubSupporter'] : null;
                        $surveySection->section_location_id = isset($responseAccountInfo['LocationID']) ? $responseAccountInfo['LocationID'] : null;
                        $surveySection->section_branch_code = isset($responseAccountInfo['BranchCode']) ? $responseAccountInfo['BranchCode'] : null;
                        $surveySection->section_sale_branch_code = isset($responseAccountInfo['BranchCodeSale']) ? $responseAccountInfo['BranchCodeSale'] : null;
                        if ($surveySection->save()) {
                            $caseToDelete->delete();
                            DB::commit();
                        } else {
                            DB::rollback();
                            $caseToDelete->updated_date_on_survey = date('Y-m-d H:i:s');
                            $caseToDelete->save();
                        }
                    } else {
                        DB::rollback();
                        $caseToDelete->updated_date_on_survey = date('Y-m-d H:i:s');
                        $caseToDelete->save();
                    }
                } catch (Exception $ex) {
                    echo 'loi';
                    echo $ex->getMessage();
                    DB::rollback();
                }
            }
            if (in_array(1, $arrayError)) {
                array_push($errorSupporter, $value->section_id);
            }
            if (in_array(2, $arrayError)) {
                array_push($errorBrachCode, $value->section_id);
            }
            if (in_array(3, $arrayError)) {
                array_push($errorUserName, $value->section_id);
            }
            if (in_array(4, $arrayError)) {
                array_push($errorLocationID, $value->section_id);
            }
            $errorMixBranchLocation = array_unique(array_merge($errorBrachCode, $errorLocationID));
        }
        if (!empty($errorMixBranchLocation)) {
            Mail::send('emails.listInvalidCase', ['info' => $errorBrachCode, 'title' => 'Thông tin các case bị sai thông tin vùng miền, chi nhánh'], function ($message) {
                $message->from('rad.support@fpt.com.vn', 'AlertInvalidCaseSurvey');
                $message->to('huydp2@fpt.com.vn');
//                    $message->cc($cc);
                $message->subject('Thông tin các case bị sai thông tin vùng miền, chi nhánh');
            });
        }
        if (!empty($errorSupporter)) {
            Mail::send('emails.listInvalidCase', ['info' => $errorSupporter, 'title' => 'Thông tin tổ đội tổ con bị thiếu'], function ($message) {
                $message->from('rad.support@fpt.com.vn', 'AlertInvalidCaseSurvey');
                $message->to('huydp2@fpt.com.vn');
//                    $message->cc($cc);
                $message->subject('Thông tin các case bị sai thông tin vùng miền, chi nhánh');
            });
        }
        if (!empty($errorUserName)) {
            Mail::send('emails.listInvalidCase', ['info' => $errorUserName, 'title' => 'Thông tin các case bị thiếu thông tin người đăng nhập'], function ($message) {
                $message->from('rad.support@fpt.com.vn', 'AlertInvalidCaseSurvey');
                $message->to('huynl2@fpt.com.vn');
//                    $message->cc($cc);
                $message->subject('Thông tin các case bị thiếu thông tin người đăng nhập');
            });
        }

        //Tim va day cac ngay bi sai locationId vao Redis de cap nhap lai summary
        if (empty($validData)) {
            $listDayToUpdateLocationId = [];

            $result = DB::table('outbound_survey_sections')->select('section_time_completed')->whereIn('section_id', $errorLocationID)->get();
            foreach ($result as $key => $value) {
                array_push($listDayToUpdateLocationId, date('Y-m-d', strtotime($value->section_time_completed)));
            }
            $listDayToUpdateLocationId = array_unique($listDayToUpdateLocationId);
            foreach ($listDayToUpdateLocationId as $key => $value) {
                Redis::rpush('day_update_summary', $value);
            }
        }
        echo 'Thanh cong';
    }

    private function GetFullAccountInfo($inputArray) {
        $uri = 'http://parapi.fpt.vn/api/RadAPI/spCEM_ObjectGetByObjID?';
        $result = $this->getApiSQL($uri, $inputArray);
//        print_r($result);die;
//        $result='{"data":[{"ObjID":1035376892,"ContractNum":"CMFD03666","ContractDate":"09-MAR-18 12.00.00.000000 AM","CustomerName":"Pham Thanh Tung","Passport":"381004637","CompanyName":null,"CertificateNumber":null,"Address":"0SO Kenh Tinh Doi K1 (Hem Tap Hoa Dieu Hien Nha Giua Hem .), P.8, TP.Ca Mau, Ca Mau","BillTo":"0SO Kenh Tinh Doi K1 (Hem Tap Hoa Dieu Hien Nha Giua Hem .), P.8, TP.Ca Mau, Ca Mau","ContractType":97,"ContractTypeName":"FTTH - F7","ContractStatus":1,"ContractStatusName":"Binh thuong","LoginName":"Cmfdl-180309-666","Email":null,"Location":"CMU - Ca Mau","Region":"Mien Nam","LocationID":780,"BranchCode":0,"Suspend_Date":null,"Suspend_Reason":null,"ObjAddress":"0SO Kenh Tinh Doi K1 (Hem Tap Hoa Dieu Hien Nha Giua Hem .), P.8, TP.Ca Mau, Ca Mau","LegalEntityName":"FPT","PartnerName":"Khong co","EocName":"Eoc","FeeLocalType":"FTTH - F7","Description":null,"Birthday":"1979-04-13 00:00:00","Sex":0.0,"AccountSale":"CMU.Hungnv93","PACKAGESAL":null,"AccountINF":"phuongnam.khuongqv","FinishDateINF":"11-MAR-18 12.06.14.557000 PM","CenterINF":"TIN/PNC","Phone":"01257627497,0917544432","PaymentType":"Tai dia chi cua khach hang","AccountPayment":null,"SubParentDesc":"Vung 7","UseService":2.0,"EmailINF":null,"EmailSale":"HungNv93@fpt.com.vn","Supporter":"","SubSupporter":"","KindDeploy":"Internet","CenterID":1,"BranchCodeSale":0}],"statusCode":200}';
//          $result='{"data":[{"ObjID":1035405592,"ContractNum":"SGH454210","ContractDate":"10-MAR-18 12.00.00.000000 AM","CustomerName":"HOANG VAN HUY","Passport":"031087004093","CompanyName":null,"CertificateNumber":null,"Address":"Lo A2, T.18, P.1812A CC Sunview Town, Go Dua, P.Tam Binh, TD, HCM","BillTo":"Lo A2, T.18, P.1812A CC Sunview Town, Go Dua, P.Tam Binh, TD, HCM","ContractType":94,"ContractTypeName":"FTTH - F4","ContractStatus":1,"ContractStatusName":"Binh thuong","LoginName":"Sgfdl-180308-210","Email":null,"Location":"HCM - Ho Chi Minh","Region":"Mien Nam","LocationID":8,"BranchCode":5,"Suspend_Date":null,"Suspend_Reason":null,"ObjAddress":"Lo A2, T.18, P.1812A CC Sunview Town, Go Dua, P.Tam Binh, TD, HCM","LegalEntityName":"FPT","PartnerName":"Khong co","EocName":"Eoc","FeeLocalType":"FTTH - F4","Description":null,"Birthday":null,"Sex":0.0,"AccountSale":"HCM.Datct4","PACKAGESAL":null,"AccountINF":"Ly.ld","FinishDateINF":"11-MAR-18 06.18.09.997000 PM","CenterINF":"TIN/PNC","Phone":"0936794950,0941829698","PaymentType":"Auto","AccountPayment":null,"SubParentDesc":"Vung 5","UseService":2.0,"EmailINF":null,"EmailSale":"DatCT4@fpt.com.vn","Supporter":"To FTTH-PhuongNam-DD","SubSupporter":"001","KindDeploy":"Internet","CenterID":4,"BranchCodeSale":null}],"statusCode":200}';
        return $result;
    }

    private function postAPIOracle($data, $url) {
        $str_data = json_encode($data);
        $uri = 'http://parapiora.fpt.vn/api/' . $url;
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($ch, CURLOPT_PROXY, "");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
        // close the connection, release resources used
        curl_close($ch);
        return $result;
    }

    private function getApiSQL($uri, $params = '', $method = 'GET') {
//$dataString = json_encode($params);
        $uri = $uri . http_build_query($params);
        $ch = curl_init();
        if (strtoupper($method) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        } else if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        } else if ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        }
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
//        curl_setopt($ch, CURLOPT_PROXY, "");
        if (curl_errno($ch)) {
            var_dump('track loi');
            var_dump(curl_error($ch));
            die;
        }
        $result = curl_exec($ch);
        if (FALSE === $result) {
//            throw new Exception(curl_error($ch), curl_errno($ch));
            var_dump(curl_error($ch));
            var_dump(curl_errno($ch));
            die;
        }
        return $result;
    }

}
