<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Component\HelpProvider;
use App\Http\Controllers\Controller;
use App\Models\SurveySections;
use App\Models\OutboundAccount;
use App\Models\Surveys;
use App\Models\SurveyResult;
use Exception;
use App\Models\Api\ApiHelper;
use Illuminate\Support\Facades\DB;
use App\Jobs\ReSendNotificationEmail;
use App\Models\PushNotification;
use Illuminate\Support\Facades\Redis;

use App\Models\OutboundQuestions;
use App\Models\OutboundAnswers;

class Api extends Controller {
    /* lấy lưu thông tin khảo sát */

    public function getResultSurveys(Request $request) {
        $help = new HelpProvider();
        $input = $request->all();
        $result = null;
        $resCheck = $help->checkPost($input, $help->getCondition('getResultSurveys'));
        if ($resCheck['status'] !== true) {
            return $help->responseFail($resCheck['status'], $resCheck['msg']);
        }

        try {

            $survey = new SurveySections;
            $res = $survey->checkSurveyApiUpgrade($input['contract']);
            if ($res === false) {
                $result['status'] = 3;
            } else {
                $result['status'] = 1;
            }
            return $help->responseSuccess($result);
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    /*
      Lưu thông tin khảo sát
     */

    public function saveResultSurveys(Request $request) {
        $help = new HelpProvider();
        $input = $request->all();

        $resCheck = $help->checkPost($input, $help->getCondition('saveResultSurveys'));
        if ($resCheck['status'] !== true) {
            return $help->responseFail($resCheck['status'], $resCheck['msg']);
        }

        try {
            $resValid = $help->validateDateStartEnd($input['time_start'], $input['time_completed']);
            if (!$resValid) {
                return $help->responseFail(406, 'time_start hoặc time_completed không hợp lệ');
            }
            $result = $this->insertSurvey($input);

            return $help->responseSuccess($result);
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    /*
     * lấy lịch sử khảo sát của 1 hợp đồng
     */

    private function getHistorySurvey($contact_num) {
        $outboundAccount = new OutboundAccount();
        $response = $outboundAccount->getAccountInfoByContract($contact_num);
//        $responseInfo['accountInfoFromSurvey'] = $outboundAccount->getAccountInfoByContract($contact_num);
        $hasNPS = FALSE;
        $historyOutboundSurvey = array();
        if (isset($response->id)) {
            $SurveySections = new SurveySections();
            $accountInfoFromSurvey = $SurveySections->getAllSurveyInfoOfAccount($response->id);


            $dateSurveyTemp = FALSE;

            foreach ($accountInfoFromSurvey as $i) {
                // chi tiết từng khảo sát
                $i->resultDetail = $SurveySections->getAllDetailSurveyInfo($i->section_id);


                // kiểm tra câu hỏi NPS
                // nếu câu hỏi có NPS lấy thời gian hoàn so sánh với thời gian hoàn thanh NPS của các câu khảo sát khác.

                $content = '';
                $temp = $i->resultDetail;
                foreach ($i->resultDetail as $d) {
                    $flag = NULL;

                    if ($d->question_id != $flag) {
                        $flag = $d->question_id;
                        $content .= '<b>' . $d->question_title_short . ': </b>';
                    }
                    $content .= $d->answers_title . ", ";
                    if ($d->question_is_nps == 1) {
                        if ($i->section_time_completed > $dateSurveyTemp) {
                            $dateSurveyTemp = $i->section_time_completed;
                        }
                    }
                }
                $i->content = $content;

                $historyOutboundSurvey[] = (array) $i;
            }
            $responseInfo['last_nps_time'] = $dateSurveyTemp;

            if ($dateSurveyTemp != FALSE) {

                $currentDate = new \DateTime();
                $lastest_survey_nps_time = new \DateTime($dateSurveyTemp);
                $interval = $lastest_survey_nps_time->diff($currentDate)->format("%a");
                if ($interval < 90) {
                    $hasNPS = TRUE;
                }
//                $responseInfo['interval'] = $interval;
            }

            $responseInfo['outbound_history'] = $historyOutboundSurvey;
        } else {
            $responseInfo['outbound_history'] = $historyOutboundSurvey;
        }
        $responseInfo['NPS'] = $hasNPS;
        return $responseInfo;
    }

// Lưu khảo sát
    private function insertSurvey($input) {
        $Surveys = new Surveys();
        $dataAccount = $input['dataaccount'];
        if (!isset($dataAccount['ContractNum'])) {
            throw new Exception('Thiếu số hợp đồng', 400, null);
        }

        DB::beginTransaction();
        $OutboundAccount = new OutboundAccount();
        $resSaveAccountInfo = $OutboundAccount->saveAccount($dataAccount);
        if ($resSaveAccountInfo['code'] == 400) {
            DB::rollback();
            throw new Exception('Số hợp đồng không tồn tại', 400, null);
        }
        $accountInfo = $resSaveAccountInfo['data'];

        $datapost = $input['datapost'];
        $surveyID = $input['type'];
        /*
         * tạo survey sections
         */
        // cần tạo try catch

        $user = $input['name'];
        $surveySections['section_account_inf'] = isset($dataAccount['AccountINF']) ? $dataAccount['AccountINF'] : NULL;
        $surveySections['section_account_list'] = isset($dataAccount['AccountList']) ? $dataAccount['AccountList'] : NULL;
        $surveySections['section_account_payment'] = isset($dataAccount['AccountPayment']) ? $dataAccount['AccountPayment'] : NULL;
        $surveySections['section_acc_sale'] = isset($dataAccount['AccountSale']) ? $dataAccount['AccountSale'] : NULL;
        $surveySections['section_objAddress'] = isset($dataAccount['ObjAddress']) ? $dataAccount['ObjAddress'] : NULL;
        $surveySections['section_branch_code'] = isset($dataAccount['BranchCode']) ? $dataAccount['BranchCode'] : "";
        $surveySections['section_center_list'] = isset($dataAccount['CenterList']) ? $dataAccount['CenterList'] : NULL;
        $surveySections['section_company_name'] = isset($dataAccount['CompanyName']) ? $dataAccount['CompanyName'] : NULL;
        $surveySections['section_contract_num'] = isset($dataAccount['ContractNum']) ? $dataAccount['ContractNum'] : NULL;
        $surveySections['section_customer_name'] = isset($dataAccount['CustomerName']) ? $dataAccount['CustomerName'] : NULL;
        $surveySections['section_description'] = isset($dataAccount['Description']) ? $dataAccount['Description'] : NULL;
        $surveySections['section_email_inf'] = isset($dataAccount['EmailINF']) ? $dataAccount['EmailINF'] : NULL;
        $surveySections['section_email_list'] = isset($dataAccount['EmailList']) ? $dataAccount['EmailList'] : NULL;
        $surveySections['section_email_sale'] = isset($dataAccount['EmailSale']) ? $dataAccount['EmailSale'] : NULL;
        $surveySections['section_fee_local_type'] = isset($dataAccount['FeeLocalType']) ? $dataAccount['FeeLocalType'] : NULL;
        $surveySections['section_finish_date_inf'] = isset($dataAccount['FinishDateINF']) ? $dataAccount['FinishDateINF'] : NULL;
        $surveySections['section_finish_date_list'] = isset($dataAccount['FinishDateList']) ? $dataAccount['FinishDateList'] : NULL;
        $surveySections['section_kind_deploy'] = isset($dataAccount['KindDeploy']) ? $dataAccount['KindDeploy'] : NULL;
        $surveySections['section_legal_entity_name'] = isset($dataAccount['LegalEntityName']) ? $dataAccount['LegalEntityName'] : NULL;
        $surveySections['section_location_id'] = isset($dataAccount['LocationID']) ? $dataAccount['LocationID'] : "";
        $surveySections['section_location'] = isset($dataAccount['Location']) ? $dataAccount['Location'] : "";
        $surveySections['section_package_sal'] = isset($dataAccount['PackageSal']) ? $dataAccount['PackageSal'] : NULL;
        $surveySections['section_partner_name'] = isset($dataAccount['PartnerName']) ? $dataAccount['PartnerName'] : NULL;
        $surveySections['section_payment_type'] = isset($dataAccount['PaymentType']) ? $dataAccount['PaymentType'] : NULL;
        $surveySections['section_phone'] = isset($dataAccount['Phone']) ? $dataAccount['Phone'] : NULL;
        $surveySections['section_region'] = isset($dataAccount['Region']) ? $dataAccount['Region'] : "";
        $surveySections['section_sub_parent_desc'] = isset($dataAccount['SubParentDesc']) ? $dataAccount['SubParentDesc'] : "";
        $surveySections['section_subsupporter'] = isset($dataAccount['SubSupporter']) ? $dataAccount['SubSupporter'] : NULL;
        $surveySections['section_supporter'] = isset($dataAccount['Supporter']) ? $dataAccount['Supporter'] : NULL;
        $surveySections['section_use_service'] = isset($dataAccount['UseService']) ? $dataAccount['UseService'] : NULL;

        //trường mới cần thêm
//		$surveySections['section_account_list_indo'] = isset($dataAccount['AccountListINDO']) ? $dataAccount['AccountListINDO'] : NULL;
//		$surveySections['section_CenterINF'] = isset($dataAccount['CenterINF']) ? $dataAccount['CenterINF'] : "";
        //
		
		// trường cũ
        $surveySections['section_connected'] = isset($datapost['connected']) ? $datapost['connected'] : "";
        $surveySections['section_count_connected'] = ($surveySections['section_connected'] == 1 || $surveySections['section_connected'] == 3) ? 1 : 0;
        $surveySections['section_note'] = isset($datapost['note']) ? $datapost['note'] : "";
        $surveySections['section_action'] = isset($datapost['action']) ? $datapost['action'] : "1"; // 1 không làm gì
        $surveySections['section_contact'] = isset($datapost['contact']) ? $datapost['contact'] : "";
        $surveySections['section_account_id'] = $accountInfo->id;
        $surveySections['section_user_id'] = NULL;
        $surveySections['section_user_name'] = isset($user) ? $user : NULL;
        $surveySections['section_contact_person'] = isset($dataAccount['contactPerson']) ? $dataAccount['contactPerson'] : "";
        $surveySections['section_kind_main'] = isset($dataAccount['KindMain']) ? $dataAccount['KindMain'] : NULL;
        $surveySections['section_code'] = isset($input['id']) ? $input['id'] : NULL;



        $surveySections['section_time_completed'] = $input['time_completed'];
        $surveySections['section_time_start'] = $input['time_start'];

        // khách hàng đồng ý khảo sát và chọn 1 nội dung khảo sát
        if (isset($surveyID)) {
            $surveyDetail = $Surveys->getDetailSurvey($surveyID);
            if (!isset($surveyDetail->survey_id)) {
                DB::rollback();
                throw new Exception('Loại khảo sát không tồn tại', 400, null);
            }
            $surveySections['section_survey_id'] = $surveyDetail->survey_id;
        }
        /* lấy dữ liệu của survey
         * nếu không có dự liệu thì trả về kết quả không tìm thấy
         */

        $surveySectionID = $Surveys->saveSurveySections($surveySections);
        if (!$surveySectionID) {
            DB::rollback();
            throw new Exception('Không thể lưu khảo sát', 400, null);
        }
        /*
         * Nếu khách hàng đồng ý trả lời và chọn 1 nội dung khảo sát
         * Lấy danh sách các câu hỏi của surveys
         */

        $surveyRes = new SurveyResult();
        foreach ($datapost as $val) {
            $temp['survey_result_section_id'] = $surveySectionID;
            $temp['survey_result_question_id'] = $val['questionid'];
            $temp['survey_result_answer_id'] = $val['answerid'];
            $temp['survey_result_note'] = $val['note'];
            $temp['survey_result_answer_extra_id'] = $val['extraidquestion'];
            $temp['survey_result_other'] = null;

            $surveyResultID = $surveyRes->saveSurveyResult($temp);
            if (!$surveyResultID) {
                DB::rollback();
                throw new Exception('Không thể lưu nội dung khảo sát', 400, null);
            }
        }


        $apiHelp = new ApiHelper();
        $resCheck = $apiHelp->checkSendSaleNet($surveySectionID);
        if ($resCheck['status']) {
            $apiHelp->sendSaleNet($surveySectionID, $resCheck, $input['name']);
        }

        DB::commit();
        return $surveySectionID;
    }

    private function updateSurvey($input) {
        $SurveyResult = new SurveyResult();
        $modelSurvey = SurveySections::find($input['idS']);
        if (empty($modelSurvey)) {
            throw new Exception('id của khảo sát này không tồn tại', 400, null);
        }
        $modelSurvey->section_connected = $input['datapost']['connected'];
        $modelSurvey->section_note = $input['datapost']['note'];
        $modelSurvey->section_action = $input['datapost']['action'];
        $modelSurvey->section_time_completed = date('Y-m-d H:i:s');
        if ($modelSurvey->section_connected == 1 || $modelSurvey->section_connected == 3) {
            $modelSurvey->section_count_connected = $modelSurvey->section_count_connected + 1;
        }
        if (!$modelSurvey->save()) {
            throw new Exception(null, 500, null);
        }
        $msg = $SurveyResult->updateDetailSurvey($input['idS'], $input['datapost'], null, $input['arrayAnswer']);
        $help = new ApiHelper();
        $resCheck = $help->checkSendSaleNet($input['idS']);
        if ($resCheck['status']) {
            $help->sendSaleNet($input['idS'], $resCheck, $input['name']);
        }
        return $msg;
    }

    /* Bộ api lấy lương */

    public function getInfoSalaryIBB(Request $request) {
        $help = new HelpProvider();
        $input = $request->all();
        $resCheck = $help->checkPost($input, $help->getCondition('getInfoSalaryIBB'));
        if ($resCheck['status'] !== true) {
            return $help->responseFail($resCheck['status'], $resCheck['msg']);
        }

        $validate = HelpProvider::validateDateStartEndForSearchFullDay($input['date_start'], $input['date_end']);
        if (!$validate) {
            return $help->responseFail(406, 'date_start hoặc date_end không hợp lệ');
        }

        try {
            $sr = new SurveyResult();
            $result = $sr->apiGetInfoSurveySalaryIBB('1', '1,2,5', $input['date_start'], $input['date_end']);
            return $help->responseSuccess($result);
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    public function getInfoSalaryTinPNC(Request $request) {
        $help = new HelpProvider();
        $input = $request->all();
        $resCheck = $help->checkPost($input, $help->getCondition('getInfoSalaryIBB'));
        if ($resCheck['status'] !== true) {
            return $help->responseFail($resCheck['status'], $resCheck['msg']);
        }

        $validate = HelpProvider::validateDateStartEndForSearchFullDay($input['date_start'], $input['date_end']);
        if (!$validate) {
            return $help->responseFail(406, 'date_start hoặc date_end không hợp lệ');
        }

        try {
            $sr = new SurveyResult();
            $resGet = $sr->apiGetInfoSurveySalaryTinPNC('2,4', '1,2,5', $input['date_start'], $input['date_end']);
//			$result = $resGet;
            $result = $this->filterResponseSalary($resGet);
            return $help->responseSuccess($result);
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    private function filterResponseSalary($result) {
        foreach ($result as &$val) {
            if ($val->section_survey_id === 1) {
                unset($val->accMaintaince);
            } else {
                unset($val->accDeploy);
            }
            unset($val->section_survey_id);
        }
        return $result;
    }

    // lưu lại xác nhận khảo sát

    public function saveReponseAcceptInfo(Request $request) {
        $help = new HelpProvider();
        //Lấy thông tin POST
        $input = $request->all();

        //Kiểm tra dữ liệu POST
        $resCheck = $help->checkPost($input, $help->getCondition('getReponseAcceptInfo'));
        if ($resCheck['status'] !== true) {
            return $help->responseFail($resCheck['status'], $resCheck['msg']);
        }

        try {
            $model_push = new PushNotification();

            //Lấy thông tin push_notification 
            $resPush = $model_push->getPushNotificationOnConfirmCode($input['code']);
            if (!empty($resPush)) {
                $param['confirm_code'] = $input['code'];
                $param['confirm_note'] = NULL;
                $param['confirmed_at'] = date('Y-m-d H:i:s');

                $user = $input['name'];
                $param['confirm_user'] = $user;
                $param['api_is_reSend'] = 0;

                //Cập nhật thông tin push_notification đã nhận được
                $resUp = $model_push->updatePushNotificationOnConfirmNotification($param);
                if ($resUp) {
                    $result = 'Đã cập nhật';
                    return $help->responseSuccess($result);
                } else {
                    return $help->responseFail(406, 'Không cập nhật được dữ liệu');
                }
            } else {
                return $help->responseFail(406, 'Không tồn tại mã xác nhận trong hệ thống');
            }
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    public function sendNotificationAgain() {
        $help = new HelpProvider();
        try {
            //Lấy ra danh sách api Net cần send lại
            $model_push = new PushNotification();
            $resPush = $model_push->getPushNotificationSendMailAgain();
            foreach ($resPush as $val) {
                $input = (array) $val;
                //Đưa vào hàng đợi gửi lại thông báo
                $job = (new ReSendNotificationEmail($input))->onQueue('callISC')->delay(2);
                $this->dispatch($job);
            }
            $result = 'Đã tiến hành gửi';
            return $help->responseSuccess($result);
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    public function getPushSurveyId() {
        $result = json_decode(Redis::get('push_notification_id'), 1);
        if (!empty($result)) {
            Redis::del('push_notification_id');
            foreach ($result as $surveyId) {
                $help = new ApiHelper();
                $resCheck = $help->checkSendSaleNet($surveyId);
                if ($resCheck['status']) {
                    $help->sendSaleNet($surveyId, $resCheck);
                }
            }
        }
        $help = new HelpProvider();
        return $help->responseSuccess('Đã tiến hành gửi');
    }

    public function getContractInfo(Request $request) {
        $info = SurveySections::select('section_contract_num', 'section_survey_id', 'section_connected', 'section_user_name', 'section_time_completed', 'section_note')->where('section_contract_num', $request->contract)->get();

        $arrResult = [0 => "Không cần liên hệ", 1 => "Không liên lạc được", 2 => "Gặp KH, KH từ chối CS", 3 => "Không gặp người SD", 4 => "Gặp người SD"];
        foreach ($info as &$val) {
            $val->section_connected = !empty($arrResult[$val->section_connected]) ? $arrResult[$val->section_connected] : '';
        }

        $help = new HelpProvider();
        if (!empty($info))
            return $help->responseSuccess($info);
        return $help->responseFail(406, 'Không tìm thấy thông tin');
    }

    public function supportMD5ForISC(Request $request) {
        $help = new HelpProvider();
        $input = $request->all();
        $result = null;
        $resCheck = $help->checkPost($input, $help->getCondition('supportMD5ForISC'));
        if ($resCheck['status'] !== true) {
            return $help->responseFail($resCheck['status'], $resCheck['msg']);
        }

        try {
            $key = 'ISC+R@D';
            $result = md5($input['ContractID'].$input['TransactionID'].$key);
            return $help->responseSuccess($result);
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    public function generateLinkSurvey(Request $request) {
        $help = new HelpProvider();
        $input = $request->all();
        $result = null;
        $resCheck = $help->checkPost($input, $help->getCondition('generateLinkSurvey'));
        if ($resCheck['status'] !== true) {
            return $help->responseFail($resCheck['status'], $resCheck['msg']);
        }

        try {
            $key = 'ISC+R@D';
            $result = md5($input['ContractID'].$input['TransactionID'].$key);
            return $help->responseSuccess($result);
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    public function getInfoSurveyByContractNumber(Request $request){
        $help = new HelpProvider();
        $input = $request->all();
        $condition = null;
        $resCheck = $help->checkPost($input, $help->getCondition('getInfoSurveyByContractNumber'));
        if ($resCheck['status'] !== true) {
            return $help->responseFail($resCheck['status'], $resCheck['msg']);
        }

        try {
            $modelSurveySections = new SurveySections();

            $condition = $this->attachCondition($condition, $request);
            $currentPage = 0;
            $infoSurvey = $modelSurveySections->searchListSurvey($condition, $currentPage);
            if(empty($infoSurvey)){
                return $help->responseSuccess([
                    'Found' => 0,
                    'Content' => 'Không có thông tin khảo sát hợp đồng'
                ]);
            }
            $param['arrayID'] = [];
            $infoSurveyKey = [];
            foreach($infoSurvey as $val){
                $param['arrayID'][] = $val->section_id;
                $infoSurveyKey[$val->section_id] = $val;
            }
            $surveyResultModel = new SurveyResult();
            $surveyResults = $surveyResultModel->getSurveyByParam($param);
            $infoSurvey = $this->convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults);
            $result = $this->repairDataForResultAPI($infoSurvey, $condition);
            return $help->responseSuccess([
                'Found' => count($result),
                'Content' => $result,
            ]);
        } catch (Exception $e) {
            return $help->responseFail($e->getCode(), $e->getMessage());
        }
    }

    private function attachCondition($condition, $request) {
        $outQuestionModel = new OutboundQuestions();
        $allQuestions = $outQuestionModel->getAllQuestion();
        $questionNeed = [];
        foreach($allQuestions as $question){
            if(isset($questionNeed[$question->question_alias])){
                array_push($questionNeed[$question->question_alias], $question->question_id);
            }else{
                $questionNeed[$question->question_alias] = [$question->question_id];
            }
        }

        $condition['contractNum'] = $request->ContractNumber;
        $condition['type'] = '';
        $condition['userSurvey'] = '';
        $condition['departmentType'] = '';
        $condition['allQuestion'] = $questionNeed;

        $condition['arraySurveyID'] = [1,2,6,9,10];
        $condition['section_connected'] = [4];
        $condition['channelConfirm'] = '';

        return $condition;
    }

    private function convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults){
        $modelOAns = new OutboundAnswers();
        $oAns = $modelOAns->getAnswerByGroup([1,2]);
        $oAns = json_decode(json_encode($oAns),1);
        $ansPoints = array_column($oAns, 'answers_point', 'answer_id');
        $ansPoints[-1] = null;

        //set field mặc định
        foreach($infoSurveyKey as &$info){
            $info->csat_salesman_point = null;
            $info->csat_salesman_note = null;

            $info->csat_deployer_point = null;
            $info->csat_deployer_note = null;

            $info->csat_maintenance_staff_point = null;
            $info->csat_maintenance_staff_note = null;

            $info->csat_net_point = null;
            $info->csat_net_note = null;
            $info->csat_net_answer_extra_id = null;

            $info->csat_tv_point = null;
            $info->csat_tv_note = null;
            $info->csat_tv_answer_extra_id = null;

            $info->csat_maintenance_net_point = null;
            $info->csat_maintenance_net_note = null;
            $info->csat_maintenance_net_answer_extra_id = null;

            $info->csat_maintenance_tv_point = null;
            $info->csat_maintenance_tv_note = null;
            $info->csat_maintenance_tv_answer_extra_id = null;

            $info->csat_transaction_point = null;
            $info->csat_transaction_note = null;

            $info->csat_transaction_staff_point = null;
            $info->csat_transaction_staff_note = null;

            $info->csat_charge_at_home_point = null;
            $info->csat_charge_at_home_note = null;

            $info->csat_charge_at_home_staff_point = null;
            $info->csat_charge_at_home_staff_note = null;

            $info->result_action_net = null;
            $info->result_action_tv = null;
        }

        //Gán giá trị vào field
        $maintenance = '';
        if($condition['type'] == 2){
            $maintenance = '_maintenance';
        }

        foreach($surveyResults as $result){
            if(array_search($result->survey_result_question_id, array_merge($condition['allQuestion'][1], $condition['allQuestion'][2], $condition['allQuestion'][29])) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_salesman_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_salesman_note = $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, array_merge($condition['allQuestion'][3], $condition['allQuestion'][30])) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_deployer_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_deployer_note = $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][4]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_maintenance_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_maintenance_staff_note = $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][5]) !== false){
                $keyP = 'csat'.$maintenance.'_net_point';
                $keyN = 'csat'.$maintenance.'_net_note';
                $keyA = 'csat'.$maintenance.'_net_answer_extra_id';
                $infoSurveyKey[$result->survey_result_section_id]->$keyP = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->$keyN = $result->survey_result_note;
                $infoSurveyKey[$result->survey_result_section_id]->$keyA = $result->survey_result_answer_extra_id;
                $infoSurveyKey[$result->survey_result_section_id]->result_action_net = $result->survey_result_action;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][6]) !== false){
                $keyP = 'csat'.$maintenance.'_tv_point';
                $keyN = 'csat'.$maintenance.'_tv_note';
                $keyA = 'csat'.$maintenance.'_tv_answer_extra_id';
                $infoSurveyKey[$result->survey_result_section_id]->$keyP = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->$keyN = $result->survey_result_note;
                $infoSurveyKey[$result->survey_result_section_id]->$keyA = $result->survey_result_answer_extra_id;
                $infoSurveyKey[$result->survey_result_section_id]->result_action_tv = $result->survey_result_action;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][7]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_note = $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][8]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_staff_note = $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][9]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->nps_improvement = $result->survey_result_answer_id;
                $infoSurveyKey[$result->survey_result_section_id]->nps_improvement_note = $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][10]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->nps_point = $ansPoints[$result->survey_result_answer_id];
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][13]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_note = $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][14]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_staff_note = $result->survey_result_note;
            }
        }
        return $infoSurveyKey;
    }

    public function repairDataForResultAPI($infoSurvey) {
        $data = [];

        $columnResult = [
            'SurveyType' => 'section_survey_id',
            'ContractNumber' => 'section_contract_num',
            'SurveyTime' => 'section_time_completed',
            'SalePoint' => 'csat_salesman_point',
            'SaleNote' => 'csat_salesman_note',
            'DeployPoint' => 'csat_deployer_point',
            'DeployNote' => 'csat_deployer_note',
            'MaintenancePoint' => 'csat_maintenance_staff_point',
            'MaintenanceNote' => 'csat_maintenance_staff_note',

            'TVPoint' => 'csat_tv_point',
            'TVNote' => 'csat_tv_note',
            'NetPoint' => 'csat_net_point',
            'NetNote' => 'csat_net_note',
        ];
        $surveyTitle = [
            1 => 'Sau Triển khai DirectSale',
            2 => 'Sau Bảo trì',
            6 => 'Sau Triển khai TeleSale',
            9 => 'Sau Triển khai Sale tại quầy',
            10 => 'Sau Triển khai Swap'
        ];

        foreach ($infoSurvey as $index => $surveySections) {
            $dataRow = [];
            foreach ($columnResult as $key => $val) {
                switch ($val) {
                    case 'section_survey_id':
                        if(isset($surveyTitle[$surveySections->$val])){
                            $tempType = new \stdClass();
                            $tempType->id = $surveySections->$val;
                            $tempType->title = $surveyTitle[$surveySections->$val];
                            $dataRow[$key] = $tempType;
                        }else{
                            $dataRow[$key] = null;
                        }
                        break;
                    case 'section_contract_num':
                    case 'csat_salesman_point':
                    case 'csat_deployer_point':
                    case 'csat_maintenance_staff_point':
                    case 'csat_salesman_note':
                    case 'csat_deployer_note':
                    case 'csat_maintenance_staff_note':
                    case 'section_time_completed':
                        $dataRow[$key] = (empty($surveySections->$val)) ? null : $surveySections->$val;
                        break;
                    case 'csat_tv_point':
                        $dataRow[$key] = (empty($surveySections->$val)) ? $surveySections->csat_maintenance_tv_point : $surveySections->$val;
                        break;
                    case 'csat_net_point':
                        $dataRow[$key] = (empty($surveySections->$val)) ? $surveySections->csat_maintenance_net_point : $surveySections->$val;
                        break;
                    case 'csat_tv_note':
                        $dataRow[$key] = (empty($surveySections->$val)) ? $surveySections->csat_maintenance_tv_note : $surveySections->$val;
                        break;
                    case 'csat_net_note':
                        $dataRow[$key] = (empty($surveySections->$val)) ? $surveySections->csat_maintenance_net_note : $surveySections->$val;
                        break;
                    default:
                }
                if(empty($dataRow[$key]) && trim($dataRow[$key]) == ''){
                    $dataRow[$key] = null;
                }
            }
            array_push($data, $dataRow);
        }
        return $data;
    }
}
