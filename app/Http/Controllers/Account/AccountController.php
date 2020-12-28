<?php

namespace App\Http\Controllers\Account;

use DB;
use Session;
use Redis;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Psy\Util\Json;
use Illuminate\Support\Facades\Auth;
use App\SQLServer;
use App\Models\OutboundAccount;
use App\Models\Apiisc;
use App\Models\SurveySections;
use App\Models\ContactProfile;
use App\Models\App\Models;
use App\Models\AccountProfiles;
use App\Models\User;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AccountController extends Controller {

    /**
     * hiển thị danh sách surver cho nhân viên chăm sóc khách hàng lựa chọn cho phù hợp với đối tượng KH
     * @return Response
     */
    public function index(Request $request) {
        return $this->search($request);
    }

    // tìm khách hàng có số hợp đồng
    public function search(Request $request) {
        $surveySec = new SurveySections();
        $resultCodes = $surveySec->checkExistCodes($request->codes, $request->type);
        //Đã khảo sát trước đó
        if (!empty($resultCodes)) {
            //Gặp người sử dụng, cho chỉnh sửa
            if ($resultCodes[0]->section_connected == 4) {
                $roleID = User::getRole(Auth::user()->id);
                $timeLimit = ($roleID == 2) ? 'P30D' : 'PT5M';
                $messageEdit = ($roleID == 2) ? 'Khảo sát này đã vượt quá 30 ngày để sửa' : 'Khảo sát này đã vượt quá 5 phút để sửa';
                $currentDate = new \DateTime();
                $time_complete = new \DateTime($resultCodes[0]->section_time_completed);
                $time_complete->add(new \DateInterval($timeLimit));
                if ($time_complete < $currentDate) {
                    $result = array('code' => 650, 'msg' => $messageEdit, 'idSur' => $resultCodes[0]->section_id);
                    return Json::encode($result);
                } else {
                    $result = array('code' => 600, 'idSur' => $resultCodes[0]->section_id);
//                    $result['code'] = 600;
//                    $result['message'] = 'Bad Request';

                    return Json::encode($result);
                }
            }
            //Không gặp được KH, cho thử lại
            else {
                $result = array('code' => 700, 'idSur' => $resultCodes[0]->section_id);
                return Json::encode($result);
            }
        }
        //Chưa khảo sát
        else {
            if (empty($request->sohd)) {
                $result['code'] = 400; //không có dữ liệu
                $result['message'] = 'Bad Request';
                return $result;
            }
            $infoAcc = array('ObjID' => '',
                'Contract' => $request->sohd,
                'ID' => $request->codes,
                'Type' => $request->type
            );

            /*
             * Lấy thông tin khách hàng
             */
            $apiIsc = new Apiisc();

            $responseAccountInfo = $apiIsc->GetFullAccountInfo($infoAcc);


            // end lấy thông tin khách hàng
            // nếu không lấy được thông tin khách hàng return false
            $responseAccountInfo = json_decode($responseAccountInfo);
            if (!isset($responseAccountInfo[0]->ObjID)) {
                $result['code'] = 400; //không có dữ liệu
                $result['msg'] = 'Bad Request';
                return Json::encode($result);
            }

            // nếu lấy được thông tin khách hàngS       

            $outboundAccount = new OutboundAccount();
            $accountInfoISC = (array) $responseAccountInfo[0];
            // lấy thông tin khách hàng trong database survey
            $accountInfo = $outboundAccount->getAccountInfoByContractNum($request->sohd);
            // update hoặc insert thông tin khách hàng
            $outboundAccount->saveAccount($accountInfoISC);
            //Ghi log dữ liệu thông tin KH trả về từ ISC
            $ip_client = $request->ip();
            $view_log = new Logger('account_info');
            $view_log->pushHandler(new StreamHandler(storage_path("/logs/account_info.log"), Logger::DEBUG));
            $view_log->addInfo('Thong tin khach hang cua hop dong ' . $accountInfoISC['ContractNum'] . ' ung voi duong link khao sat ' . $_SERVER['SERVER_NAME'] . '/#/inputcontract/' . $request->sohd . '/' . $request->type . '/' . $request->codes . '/ tu dia chi IP (' . $ip_client . ') la :' . print_r($accountInfoISC, 1));

            if (empty($accountInfo->contract_num)) { // nếu chưa có thông tin khách hàng
                $this->saveAccountProfiles($accountInfoISC);
            } else {
                // nếu tìm thấy thông tin khách hàng kiểm tra các thông tin
                // Họ và tên, Ngày tháng năm sinh, giới tính, địa chỉ trên CMND, địa chỉ lắp đặt, địa chỉ thanh toán
                // nếu các thông tin trên đã được lưu trong database survey và các thông tin này giống với thông tin đã gọi API của lần trước thì lấy thông tin trong database survey
                // nếu thông tin này là khác với thông tin đã gọi API lần trước thì lưu mới thông tin khách hàng = thông tin API mới
                $accountInfoCurrent = (array) $responseAccountInfo[0];
                $this->saveAccountProfiles($accountInfoISC, $accountInfo);

                // kiểm tra đã lưu thông tin tiếng việt chưa
                // nếu có thì load lên load đè API trả về
                $AccountProfiles = new AccountProfiles;
                $AccountProfilesVN = $AccountProfiles->getAccountProfilesByContract($request->sohd);
                //var_dump( $AccountProfilesVN );
                if (isset($AccountProfilesVN->ap_contract)) {
                    $responseAccountInfo[0]->CustomerName = $AccountProfilesVN->ap_fullname;
                    $responseAccountInfo[0]->Address = $AccountProfilesVN->ap_address_id;
                    $responseAccountInfo[0]->BillTo = $AccountProfilesVN->ap_address_bill;
                    $responseAccountInfo[0]->ObjAddress = $AccountProfilesVN->ap_address_setup;
                    $responseAccountInfo[0]->Sex = $AccountProfilesVN->ap_sex;
                    $responseAccountInfo[0]->Birthday = $AccountProfilesVN->ap_birthday;
                }
                // end tiếng việt
            }

            $responseAccountInfo[0] = $this->processDataFromISC($responseAccountInfo[0]);
            $startDate = $responseAccountInfo[0]->ContractDate;
//       	$startDate = preg_replace( '/[^0-9]/', '', $startDate);
//       	$startDate = $startDate/1000;
            $responseAccountInfo[0]->ContractDate = $startDate; //date("d-m-Y", $startDate );




            $responseInfo['data_cusinfo'] = $responseAccountInfo;
            //Thông tin chủ hợp đồng
            $infoContact = ['phone' => $responseAccountInfo[0]->Phone, 'name' => $responseAccountInfo[0]->CustomerName, 'relationship' => 4];
            $responseInfo['infoContact'] = $infoContact;

            $responseInfo['msg'] = 'Success';
            $responseInfo['code'] = '200';
            // nếu có thông tin khách hàng thì lấy thông tin lịch sử hỗ trợ
            //$ObjID = 1020104442;
            $ObjID = $responseAccountInfo[0]->ObjID;
            //Thong tin bang thong
            $infoBanwidth = array('ObjID' => $ObjID,
            );
            $arrayBandwidth = [0 => 'Không nâng băng thông', 1 => 'Chưa hoàn tất nâng băng thông', 2 => 'Hoàn tất nâng băng thông'];
            $bandwidth = $apiIsc->CheckBandwidthByObjID($infoBanwidth);
            
            $responseInfo['bandWidthInfo'] = $arrayBandwidth[$bandwidth[0]['Result']];
            $paramHistory = array('ObjID' => $ObjID, 'RecordCount' => 3);
            $historyData = json_decode($apiIsc->getCallerHistoryByObjID($paramHistory));
            if (count($historyData) > 0) {
                for ($i = 0; $i < count($historyData); $i++) {
                    if (isset($historyData[$i]->StartDate)) {
                        $startDate = $historyData[$i]->StartDate;
                        $startDate = preg_replace('/[^0-9]/', '', $startDate);
                        $startDate = $startDate / 1000;
                        $historyData[$i]->StartDate = date("d-m-Y", $startDate);
                    }
                    if (isset($historyData[$i]->EndDate)) {

                        $endDate = $historyData[$i]->EndDate;
                        $endDate = preg_replace('/[^0-9]/', '', $endDate);
                        $endDate = $endDate / 1000;
                        $historyData[$i]->EndDate = date("d-m-Y", $endDate);
                    }
                }
            }
            $responseInfo['data_history'] = $historyData;
            /*
             * kiểm tra thông tin khách hàng đã lưu trên Mo hay chưa
             * Nếu lưu rồi lấy thông tin lịch sử survey
             */
            $responseInfo['accountInfoFromSurvey'] = $outboundAccount->getAccountInfoByContract($infoAcc['Contract']);
            $hasNPS = FALSE;
            if (isset($responseInfo['accountInfoFromSurvey']->id)) {
                $SurveySections = new SurveySections();
                $accountInfoFromSurvey = $SurveySections->getAllSurveyInfoOfAccount($responseInfo['accountInfoFromSurvey']->id);

                $historyOutboundSurvey = array();
                $dateSurveyTemp = FALSE;

                foreach ($accountInfoFromSurvey as $i) {
                    // chi tiết từng khảo sát
                    $i->resultDetail = $SurveySections->getAllDetailSurveyInfo($i->section_id);


                    // kiểm tra câu hỏi NPS
                    // nếu câu hỏi có NPS lấy thời gian hoàn so sánh với thời gian hoàn thanh NPS của các câu khảo sát khác.

                    $content = '';
                    $temp = $i->resultDetail;
                    foreach ($i->resultDetail as $d) {
//        			var_dump($i->resultDetail);die;
                        $flag = NULL;

                        if ($d->question_id != $flag) {
                            $flag = $d->question_id;
                            $content .= '<b>' . $d->question_title_short . ': </b>';
                        }
                        $content .= $d->answers_title . ", ";
//        			if(($d->question_is_nps==1) && isset($d->question_is_nps)){
                        if ($d->question_is_nps == 1) {
                            if ($i->section_time_completed > $dateSurveyTemp) {
                                $dateSurveyTemp = $i->section_time_completed;
                            }
                        }
                    }
                    $i->content = $content;
                    //$i->resultDetail = $temp;

                    $historyOutboundSurvey[] = (array) $i;
                }
                $responseInfo['last_nps_time'] = $dateSurveyTemp;



                if ($dateSurveyTemp != FALSE) {

                    $currentDate = new \DateTime();
                    $lastest_survey_nps_time = new \DateTime($dateSurveyTemp);
                    $interval = $lastest_survey_nps_time->diff($currentDate)->format("%a");
//        		if( $currentDate < $dateSurveyTemp + 90 ){
                    if ($interval < 90) {
//         		$dateCompleted = $i->section_time_completed;
//         		$dateCompleted = date_create($dateCompleted);
//         		date_add($dateCompleted, date_interval_create_from_date_string('90 days'));
//         		if ( $dateCompleted > $currentDate ){
                        $hasNPS = TRUE;
                        //}
                    }
                    $responseInfo['interval'] = $interval;
                }

                $responseInfo['outbound_history'] = $historyOutboundSurvey;

                //tìm thông tin người liên hệ trong db
//             $modelContactProfile = new ContactProfile();
//             $info = $modelContactProfile->getContactByID($responseInfo['accountInfoFromSurvey']->id, 1);
//             $arrRelationship = [1 => 'Cha', 2 => 'Mẹ', 3 => "Anh", 4 => 'Chị', 5 => 'Em', 6 => 'Khác'];
//             $responseInfo['data_cusinfo'][0]->contactPerson = NULL;
//             if(!empty($info)){
//                 $responseInfo['data_cusinfo'][0]->contactPerson = !empty($info) ?$info[0]->contact_name.' - '.$info[0]->contact_phone.'('.$arrRelationship[$info[0]->contact_relationship].')' :'';
//             }
            }

            $responseInfo['NPS'] = $hasNPS;
            $responseInfo['section_time_start'] = date('Y-m-d H:i:s');

            return $responseInfo;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request) {
        $account = new Account;
    }

    /*
     * Lấy thông tin khách hàng
     * step1: gọi store ISC lấy thông tin
     * step2: Nếu không có thì truy cấp database mo lấy dự liệu
     */

    //store goi lay thong tin khach hang
    public function getAccountInfo($info) {

        $account = new Account;
        //store gọi lấy thông tin khách hàng, output ít thông tin cơ bản: ObjID: Mã HĐ, Contract: Số HĐ, FullName: Tên đầy đủ, Status: Tình trạng, Passport: CMND, Address: Địa chỉ KH
        $fullInfo = '';
        $response = array('code' => 404);
        $resultAccountFromISC = $this->getAccountInfoFromISC($info); // gọi store lấy từ ISC	
        return $resultAccountFromISC;

        if ($resultAccountFromISC['code'] == 200) {
            return $resultAccountFromISC;
        }
        return $response;
        /*
         * không có dữ liệu từ ISC truy cập database Mo lấy dữ liệu khách hàng
         * tạm thời không truy cập vào database mo nêu không tìm thấy dữ liệu từ ISC
         */
        // return $this->getAccountInfoFromMo($info);
    }

    private function getHistorySupport($iObjid) {
        $account = new Account;
        $result = $account->StoreGetHistorySup($iObjid); //Store lấy lịch sử hỗ trợ

        if (!empty($result)) {
            return $result;
        }
        return false;
        /*
         * không có dữ liệu từ ISC truy cập database Mo lấy dữ liệu khách hàng
         * tạm thời không truy cập vào database mo nêu không tìm thấy dữ liệu từ ISC
         */
        //return $account->getInfoCustomerFromMo($infoAcc['contract']);
    }

//     private function getHistoryOutbound($accountID){
//     	$account = new Account;
//     	$result = $account->getAllSurveyInfoOfAccount($accountID);
//     	if( count($result) > 0 ){
//     		return $result;
//     	}
//     	return NULL;
//     }


    public function save(Request $request) {
//         $model = new OutboundAccount();
//         if(empty($request->datapost['ContractNum'])){
//             return json_encode(['code' => 500]);//không nhập số hợp đồng
//         }
//         $response = $model->saveAccount($request->datapost);
        //Tạo dữ liệu validate trong redis
//        $redis = Redis::connection();
//        $redis->set('sauTrienKhai', json_encode([101 => [1, 2, 6, 10], 102 => [1, 2, 6, 11], 103 => [1, 2, 6, 11, 10], 104 => [1, 2, 10], 105 => [1, 2, 11], 106 => [1, 2, 11, 10]]
//            )
//        );
//        $redis->set('sauBaoTri', json_encode([201 => [4, 12, 8], 202 => [4, 13, 8], 203 => [4, 12, 13, 8], 204 => [4, 12], 205 => [4, 13], 206 => [4, 12, 13]]
//            )
//        );
//        $redis->set('arrayQuesMap', json_encode([10 => '1.a', 11 => '1.b', 2 => '2', 1 => '3', 6 => '4', 7 => '5', 12 => '1.a', 13 => '1.b', 4 => '2', 8 => '3', 5 => '4']
//            )
//        );
        $AccountProfiles = new AccountProfiles;
        $accountInfo = $request->datapost;
        if (!empty($accountInfo['ContractNum'])) { // có truyền thông tin số hợp đồng
            $AccountProfilesVN = $AccountProfiles->getAccountProfilesByContract($accountInfo['ContractNum']);
            $accountProfilesStore = array(
                "ap_fullname" => $accountInfo['CustomerName'],
                "ap_sex" => $accountInfo['Sex'],
                "ap_address_id" => $accountInfo['Address'],
                "ap_address_bill" => $accountInfo['BillTo'],
                "ap_address_setup" => $accountInfo['ObjAddress'],
                "ap_user_update" => Auth::user()->id
            );
            if (!empty($accountInfo['Birthday'])) {
                $accountProfilesStore["ap_birthday"] = date('Y-m-d', strtotime($accountInfo['Birthday']));
            }
            if (isset($AccountProfilesVN->ap_contract)) {
                if ($AccountProfiles->updateAccountProfiles($request->datapost['ContractNum'], $accountProfilesStore)) {
                    return json_encode(['code' => 200]);
                }
            } else {
                $accountProfilesStore["ap_contract"] = $request->datapost['ContractNum'];
                if ($AccountProfiles->insertAccountProfiles($accountProfilesStore)) {
                    return json_encode(['code' => 200]);
                }
            }
        }


        return json_encode(['code' => 404]);
    }

    // xử lý dữ liệu từ ISC trả về
    private function processDataFromISC($data) {
        $dateFormat = 'd-m-Y h:i:s'; //config('app.datetime_format');
        if (!empty($data->ContractDate)) {
            $data->ContractDate = date($dateFormat, strtotime($data->ContractDate));
        }

        // kiểm tra là bảo trì hay triển khai.
        // nếu $data->FinishDateList == null => triển khai
        // Ngày thi công $data->FinishDateINF > ngày bảo trì $data->FinishDateList => triển khai
//    	$data->isCheckList = 1; // mặc định là bảo trì
//    	if ( empty($data->FinishDateList)){
//    		$data->isCheckList = 0; // triển khai
//    	}else if ( $data->FinishDateINF > $data->FinishDateList){
//    		$data->isCheckList = 0;
//    	}
        // end kiểm tra triển khai, bảo trì	
        if (!empty($data->FinishDateINF)) {
            $data->FinishDateINF = date($dateFormat, strtotime($data->FinishDateINF));
        }
        if (!empty($data->FinishDateList)) {
            $data->FinishDateList = date($dateFormat, strtotime($data->FinishDateList));
        }
        return $data;
    }

    /*
     * nếu chưa có account_profiles thì lưu mới 
     * nếu có rồi thì kiểm tra xem thông tin ISC trả về lần gấn nhất và hiện tại có khác nhau không
     * khác thì update nếu giống thì return lại - không làm gì
     */

    private function saveAccountProfiles($accountCurrent, $accountStored = NULL) {
        $AccountProfiles = new AccountProfiles;
        //$accountCurrent = (array)$accountCurrent;
        $accountStored = (array) $accountStored;
        if (empty($accountStored['contract_num'])) {
            $accountProfiles = array(
                "ap_contract" => $accountCurrent['ContractNum'],
                "ap_fullname" => $accountCurrent["CustomerName"],
                "ap_birthday" => $accountCurrent["Birthday"],
                "ap_sex" => $accountCurrent["Sex"],
                "ap_address_id" => $accountCurrent["Address"],
                "ap_address_bill" => $accountCurrent["BillTo"],
                "ap_address_setup" => $accountCurrent["ObjAddress"],
                "ap_user_update" => Auth::user()->id
            );
            $AccountProfiles->insertAccountProfiles($accountProfiles);
        } else {
            $accountProfiles = array();
            //var_dump($accountStored);
            if ($accountCurrent["CustomerName"] != $accountStored['customer_name']) {
                $accountProfiles['ap_fullname'] = $accountCurrent["CustomerName"];
            }
            if ($accountCurrent["Birthday"] != $accountStored['birthday']) {
                $accountProfiles['ap_birthday'] = $accountCurrent["Birthday"];
            }
            if ($accountCurrent["Sex"] != $accountStored['sex']) {
                $accountProfiles['ap_sex'] = $accountCurrent["Sex"];
            }
            if ($accountCurrent["Address"] != $accountStored['address']) {
                $accountProfiles['ap_address_id'] = $accountCurrent["Address"];
            }
            if ($accountCurrent["BillTo"] != $accountStored['address_bill_to']) {
                $accountProfiles['ap_address_bill'] = $accountCurrent["BillTo"];
            }
            if ($accountCurrent["ObjAddress"] != $accountStored['obj_address']) {
                $accountProfiles['ap_address_setup'] = $accountCurrent["ObjAddress"];
            }


            $AccountProfiles->updateAccountProfiles($accountCurrent['ContractNum'], $accountProfiles);
        }
    }

    /*
     * So sánh dữ liệu nếu mới khác cũ thì lấy mới
     */

    private function getfieldAfterCompare($accountCurrent, $accountStored) {
        if ($accountCurrent != $accountStored)
            return TRUE;
        return FALSE;
    }

}
