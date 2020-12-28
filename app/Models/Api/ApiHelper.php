<?php

namespace App\Models\Api;

use App\Models\SurveySections;
use App\Models\SurveyResult;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendNotificationEmail;
use App\Jobs\SendNotificationMobile;
use App\Component\HelpProvider;
use App\Models\PushNotification;
use App\Models\RecordChannel;
use App\Models\OutboundAnswers;

class ApiHelper {

    public $domain_confirm = 'https://cem.fpt.vn/';

    public function checkSendMail($param){
        $surRes = new SurveySections();
        $resSurRes = $surRes->getSurveySectionsAndResultHaveCheckList($param);
        $isSendCL = true;
        if(empty($resSurRes)){
            $isSendCL = false;
            $surRes = new SurveyResult();
            $resSurRes = $surRes->getDetailSurvey($param['sectionId']);
            if(empty($resSurRes)){
                $result['status'] = false;
                return $result;
            }
        }

        $arrBadSaleNote     = $arrBadTechNote   = $arrBadTeleNote   = $arrBadNetNote    = $arrBadTvNote     = [];
        $arrBadSaleError    = $arrBadTechError  = $arrBadTeleError  = $arrBadNetError   = $arrBadTvError    = null;
        $arrBadSalePoint    = $arrBadTechPoint  = $arrBadTelePoint  = $arrBadNetPoint   = $arrBadTvPoint    = null;
        $isSendSale         = $isSendTech       = $isSendTele       = $isSendNet        = $isSendTv         = false;

        $arrBadTransactionNote  = $arrBadChargeAtHomeStaffNote  = $arrBadTransactionSaleNote   = [];
        $arrBadTransactionError = $arrBadChargeAtHomeStaffError = $arrBadTransactionSaleError  = null;
        $arrBadTransactionPoint = $arrBadChargeAtHomeStaffPoint = $arrBadTransactionSalePoint  = null;
        $isSendTransaction      = $isSendChargeAtHomeStaff      = $isSendTransactionSale       = false;

        //Kiểm tra toàn bộ kết quả của bảng đánh giá
        foreach ($resSurRes as $resSur) {
            $not = $resSur->survey_result_note;
            $que = $resSur->survey_result_question_id;
            $ans = $resSur->survey_result_answer_id;
            $ansExt = $resSur->survey_result_answer_extra_id;

            // Nhân viên kinh doanh triển khai DirectSale
            if ($que == 1) {
                array_push($arrBadSaleNote, $not);
                $arrBadSaleError = $ansExt;
                $arrBadSalePoint = $ans;
                if(in_array($ans, [1, 2, 5])){
                    $isSendSale = true;
                }
            }

            // Nhân viên kỹ thuật triển khai Direct, bảo trì, Sale tại quầy, sau swap
            if (in_array($que, [2, 4, 38, 43])) {
                array_push($arrBadTechNote, $not);
                $arrBadTechError = $ansExt;
                $arrBadTechPoint = $ans;
                if(in_array($ans, [1, 2])){
                    $isSendTech = true;
                }
            }

            // Nhân viên kinh doanh telesale
            if ($que == 23) {
                array_push($arrBadTeleNote, $not);
                $arrBadTeleError = $ansExt;
                $arrBadTelePoint = $ans;
                if(in_array($ans, [1, 2, 5])){
                    $isSendTele = true;
                }
            }

            // Dịch vụ internet
            if (in_array($que, [10, 12, 20, 41, 46])) {
                array_push($arrBadNetNote, $not);
                $arrBadNetError = $ansExt;
                $arrBadNetPoint = $ans;
            }

            // Dịch vụ truyền hình
            if (in_array($que, [11, 13, 21, 42, 47])) {
                array_push($arrBadTvNote, $not);
                $arrBadTvError = $ansExt;
                $arrBadTvPoint = $ans;
            }

            // Giao dịch tại quầy
            if($que == 26){
                array_push($arrBadTransactionNote, $not);
                $arrBadTransactionError = $ansExt;
                $arrBadTransactionPoint = $ans;
                if (in_array($ans, [1, 2, 5])) {
                    $isSendTransaction = true;
                }
            }

            // Nhân viên kinh doanh giao dịch tại quầy, sale tại quầy
            if(in_array($que, [31, 37])){
                array_push($arrBadTransactionSaleNote, $not);
                $arrBadTransactionSaleError = $ansExt;
                $arrBadTransactionSalePoint = $ans;
                if (in_array($ans, [1, 2, 5])) {
                    $isSendTransactionSale = true;
                }
            }

            if($que == 35){
                array_push($arrBadChargeAtHomeStaffNote, $not);
                $arrBadChargeAtHomeStaffError = $ansExt;
                $arrBadChargeAtHomeStaffPoint = $ans;
                if (in_array($ans, [1, 2])) {
                    $isSendChargeAtHomeStaff = true;
                }
            }
        }

        $desc['badSale'] = implode('.', $arrBadSaleNote);
        $desc['badTech'] = implode('.', $arrBadTechNote);
        $desc['badTele'] = implode('.', $arrBadTeleNote);
        $desc['badNet'] = implode('.', $arrBadNetNote);
        $desc['badTv'] = implode('.', $arrBadTvNote);
        $desc['badTransaction'] = implode('.', $arrBadTransactionNote);
        $desc['badTransactionSale'] = implode('.', $arrBadTransactionSaleNote);
        $desc['badChargeAtHomeStaff'] = implode('.', $arrBadChargeAtHomeStaffNote);

        $send = false;
        if ($isSendTransaction || $isSendTransactionSale || $isSendChargeAtHomeStaff || $isSendSale || $isSendTech || $isSendTele || $isSendCL) {
            $send = true;
        }
        $res['status'] = $send;
        $res['sendCL'] = $isSendCL;
        $res['rule'] = [
            'badNet' => $isSendNet,
            'badTv' => $isSendTv,
            'badTech' => $isSendTech,
            'badTele' => $isSendTele,
            'badSale' => $isSendSale,
            'badTransaction' => $isSendTransaction,
            'badTransactionSale' => $isSendTransactionSale,
            'badChargeAtHomeStaff' => $isSendChargeAtHomeStaff,
        ];
        $res['msg'] = [
            'badNet' => $desc['badNet'],
            'badTv' => $desc['badTv'],
            'badTech' => $desc['badTech'],
            'badTele' => $desc['badTele'],
            'badSale' => $desc['badSale'],
            'badTransaction' => $desc['badTransaction'],
            'badTransactionSale' => $desc['badTransactionSale'],
            'badChargeAtHomeStaff' => $desc['badChargeAtHomeStaff'],
        ];
        $res['error'] = [
            'badNet' => $arrBadNetError,
            'badTv' => $arrBadTvError,
            'badTech' => $arrBadTechError,
            'badTele' => $arrBadTeleError,
            'badSale' => $arrBadSaleError,
            'badTransaction' => $arrBadTransactionError,
            'badTransactionSale' => $arrBadTransactionSaleError,
            'badChargeAtHomeStaff' => $arrBadChargeAtHomeStaffError,
        ];
        $res['point'] = [
            'badNet' => $arrBadNetPoint,
            'badTv' => $arrBadTvPoint,
            'badTech' => $arrBadTechPoint,
            'badTele' => $arrBadTelePoint,
            'badSale' => $arrBadSalePoint,
            'badTransaction' => $arrBadTransactionPoint,
            'badTransactionSale' => $arrBadTransactionSalePoint,
            'badChargeAtHomeStaff' => $arrBadChargeAtHomeStaffPoint,
        ];
        return $res;
    }

    public function prepareSendMail($param,$resCheckSend){
        $surRes = new SurveySections();
        $surSec = $surRes->getSurveySectionsWithEmailTransaction($param);

        $paramMail = $param;
        switch($surSec->section_survey_id){
            case 1:
                if($surSec->sale_center_id == 1){
                    $paramMail['type'] = 'Triển khai DirectSales';
                    $paramMail['poc'] = 'Sau Triển khai DirectSales';
                    $paramMail['timeComplete'] = $surSec->section_finish_date_inf;
                    $paramMail['user_name_send'] = $surSec->section_user_name;
                }else{
                    return;
                }
                break;
            case 2:
                $paramMail['type'] = 'Bảo trì';
                $paramMail['poc'] = 'Sau Bảo trì';
                $paramMail['timeComplete'] = $surSec->section_finish_date_list;
                $paramMail['user_name_send'] = $surSec->section_user_name;
                break;
            case 3:
                break;
            case 4:
                $paramMail['type'] = 'Giao dịch tại quầy';
                $paramMail['poc'] = 'Sau Giao dịch tại quầy';
                $paramMail['timeComplete'] = $surSec->section_time_start_transaction;
                $paramMail['user_name_send'] = $surSec->section_user_create_transaction;
                break;
            case 5:
                break;
            case 6:
                if($surSec->sale_center_id == 2){
                    $paramMail['type'] = 'Triển khai TeleSales';
                    $paramMail['poc'] = 'Sau Triển khai TeleSales';
                    $paramMail['timeComplete'] = $surSec->section_finish_date_inf;
                    $paramMail['user_name_send'] = $surSec->section_user_name;
                }else{
                    return;
                }
                break;
            case 7:
                $paramMail['type'] = 'Thu cước tại nhà';
                $paramMail['poc'] = 'Sau Thu cước tại nhà';
                $paramMail['timeComplete'] = $surSec->section_time_start_transaction;
                $paramMail['user_name_send'] = $surSec->section_user_create_transaction;
                break;
            case 8:
                break;
            case 9:
                $paramMail['type'] = 'Triển khai sale tại quầy';
                $paramMail['poc'] = 'Sau Triển khai sale tại quầy';
                $paramMail['timeComplete'] = $surSec->section_finish_date_inf;
                $paramMail['user_name_send'] = $surSec->section_user_name;
                break;
            case 10:
                $paramMail['type'] = 'Triển khai Swap';
                $paramMail['poc'] = 'Sau Triển khai Swap';
                $paramMail['timeComplete'] = $surSec->section_finish_date_inf;
                $paramMail['user_name_send'] = $surSec->section_user_name;
                break;
            default:
                return;
        }

        // Các thông tin gửi mail chung của tất cả các loại gửi mail
        $paramMail['saleMan'] = $surSec->section_acc_sale;
        $paramMail['time'] = $surSec->section_time_completed;
        $paramMail['team'] = $surSec->section_supporter . ' - ' . $surSec->section_subsupporter;
        $paramMail['date'] = date('Y-m-d H:i:s');

        $paramMail['num_type'] = $surSec->section_survey_id;
        $paramMail['code'] = $surSec->section_code;
        $paramMail['shd'] = $surSec->section_contract_num;

        $paramMail['name'] = $surSec->section_customer_name;
        $paramMail['address'] = $surSec->section_objAddress;
        $paramMail['phone'] = $surSec->section_phone;
        $location = explode('-', $surSec->section_location);
        $paramMail['location'] = trim($location[0]);
        $paramMail['location_id'] = $surSec->section_location_id;
        $paramMail['branch_code'] = $surSec->section_branch_code;
        $paramMail['point'] = '0';
        $paramMail['note'] = '0';
        $paramMail['csat'] = '0';

        $paramMail['transactionKind'] = $surSec->section_kind_service;
        $paramMail['transactionSale'] = $surSec->section_user_create_transaction;

        // Thông tin kênh ghi nhận
        $modelChannel = new RecordChannel();
        $channels = $modelChannel->getAllRecordChannel();
        foreach($channels as $channel){
            if($channel->record_channel_code == $surSec->section_record_channel){
                $paramMail['channel'] = $channel->record_channel_name;
            }
        }

        $paramMail['results'] = [];
        $object = [
            'badNet' => 'CLDV Internet',
            'badTv' => 'CLDV TH',
            'badTech' => 'Tổ đội kỹ thuật:'.$paramMail['team'],
            'badTele' => 'NVKD:'.$paramMail['saleMan'],
            'badSale' => 'NVKD:'.$paramMail['saleMan'],
            'badTransaction' => 'CL Giao dịch tại quầy',
            'badTransactionSale' => 'NV Giao dịch',
            'badChargeAtHomeStaff' => 'NV Thu cước',
        ];

        $mainTitle =[
            'badNet' => 'Chất lượng dịch vụ',
            'badTv' => 'Chất lượng dịch vụ',
            'badTech' => 'Nhân viên kỹ thuật',
            'badTele' => 'Nhân viên kinh doanh',
            'badSale' => 'Nhân viên kinh doanh',
            'badTransaction' => 'Chất lượng giao dịch',
            'badTransactionSale' => 'Nhân viên giao dịch',
            'badChargeAtHomeStaff' => 'Nhân viên thu cước',
        ];

        $recordBy = [
            '1' => 'NVCS',
            '2' => 'Web khảo sát',
            '3' => 'Hi FPT',
            '4' => 'NVTC',
            '5' => 'NVGD',
            '6' => 'Tablet',
        ];

        // Thông tin điểm
        $modelStatus = new OutboundAnswers();
        $statuses = $modelStatus->getAnswerByGroup([0, 1]);
        // Loại điểm
        $typePoint = [];
        foreach($statuses as $point){
            $typePoint[$point->answer_id] = $point->answers_title;
        }

        // Loại lỗi
        $statuses = $modelStatus->getAnswerByGroup([20, 22, 23, 24]);
        $typeError = [];
        foreach($statuses as $ans){
            $typeError[$ans->answer_id] = $ans->answers_title;
        }

        // Tạo bộ câu trả lời cho email
        $isGoodCL = true;
        $paramMail['results'] = [];
        foreach($resCheckSend['rule'] as $type => $isSend){
            $point = $resCheckSend['point'][$type];
            if(!empty($point) && $point > 0){
                $arrayWarning = [];
                $arrayWarning['object'] = $object[$type];
                $arrayWarning['csat'] = $typePoint[$point];
                $arrayWarning['point'] = $point;
                if(in_array($point,[1,2])){
                    $isGoodCL = false;
                }
                $arrayWarning['typeError'] = (!empty($resCheckSend['error'][$type]))? (isset($typeError[$resCheckSend['error'][$type]])? $typeError[$resCheckSend['error'][$type]]: null) : null;
                $arrayWarning['note'] = $resCheckSend['msg'][$type];
                $paramMail['results']['badCL'][] = $arrayWarning;
                if($isSend){
                    $tempTitle = $mainTitle[$type];
                    if(in_array($type,["badTransaction", "badTransactionSale"])){
                        $type = "badQGD";

                    } elseif(in_array($type,["badChargeAtHomeStaff"])){
                        $type = "badCUS";
                    }

                    $paramMail['results'][$type][] = $arrayWarning;
                    if(in_array($point,[1,2])){
                        $paramMail['results'][$type]['other']['alertGood'] = false;
                    }else{
                        $paramMail['results'][$type]['other']['alertGood'] = true;
                    }
                    $paramMail['results'][$type]['other']['mainTitle'] = $tempTitle;
                    $paramMail['results'][$type]['other']['recordBy'] = $recordBy[$surSec->section_record_channel];
                    $paramMail['results'][$type]['other']['point'] = $arrayWarning['point'];
                    $paramMail['results'][$type]['other']['note'] = $arrayWarning['note'];
                    $paramMail['results'][$type]['other']['csat'] = $arrayWarning['csat'];
                }
                if(in_array($type,['badTv','badNet'])){
                    $paramMail['results']['badCL']['other']['point'] = $arrayWarning['point'];
                    $paramMail['results']['badCL']['other']['note'] = $arrayWarning['note'];
                    $paramMail['results']['badCL']['other']['csat'] = $arrayWarning['csat'];
                }
            }
        }
        $paramMail['results']['badCL']['other']['alertGood'] = $isGoodCL;
        $paramMail['results']['badCL']['other']['mainTitle'] = 'Chất lượng dịch vụ';
        $paramMail['results']['badCL']['other']['recordBy'] = $recordBy[$surSec->section_record_channel];

        // Kiểm tra và send mail theo từng loại tương ứng
        $result = '';
        foreach($paramMail['results'] as $type => $arrayResult){
            switch($type){
                case 'badSale':
                    $paramMail['sale_net_type'] = 'Sale';
                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];

                    //Kiểm tra trường hợp bị trùng lặp dữ liệu
                    $iSagain = $this->isSendAgain($paramMail);
                    if (!$iSagain) {
                        $result .= $this->send($paramMail);
                    } else {
                        $this->updateNoteForResend($paramMail);
                    }
                    break;
                case 'badTech':
                    $paramMail['sale_net_type'] = 'Tech';
                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];

                    //Kiểm tra trường hợp bị trùng lặp dữ liệu
                    $iSagain = $this->isSendAgain($paramMail);
                    if (!$iSagain) {
                        $result .= $this->send($paramMail);
                    } else {
                        $this->updateNoteForResend($paramMail);
                    }
                    break;
                case 'badTele':
                    $paramMail['sale_net_type'] = 'Tele';
                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];

                    //Kiểm tra trường hợp bị trùng lặp dữ liệu
                    $iSagain = $this->isSendAgain($paramMail);
                    if (!$iSagain) {
                        $result .= $this->send($paramMail);
                    } else {
                        $this->updateNoteForResend($paramMail);
                    }
                    break;
                case 'badQGD':
                    $paramMail['sale_net_type'] = 'QGD';
                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];

                    //Kiểm tra trường hợp bị trùng lặp dữ liệu
                    $iSagain = $this->isSendAgain($paramMail);
                    if (!$iSagain) {
                        $result .= $this->send($paramMail);
                    }
                    break;
                case 'badCUS':
                    $paramMail['sale_net_type'] = 'CUS';
                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];

                    //Kiểm tra trường hợp bị trùng lặp dữ liệu
                    $iSagain = $this->isSendAgain($paramMail);
                    if (!$iSagain) {
                        $result .= $this->send($paramMail);
                    }
                    break;
                case 'badCL':
                    if($resCheckSend['sendCL']){
                        // Trường hợp tạo checklist mà không đánh giá
                        if(count($paramMail['results']['badCL']) == 1){
                            continue;
                        }
                        $paramMail['sale_net_type'] = 'CL';
                        $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
                        $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
                        $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];

                        //Kiểm tra trường hợp bị trùng lặp dữ liệu
                        $iSagain = $this->isSendAgain($paramMail);
                        if (!$iSagain) {
                            $result .= $this->send($paramMail);
                        }
                    }
                    break;
                default:
            }
        }

//        foreach($paramMail['results'] as $type => $arrayResult){
//            switch($type){
//                case 'badSale':
//                    $paramMail['sale_net_type'] = 'Sale';
//                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
//                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
//                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];
//
//                    $result .= $this->sendTest($paramMail);
//                    break;
//                case 'badTech':
//                    $paramMail['sale_net_type'] = 'Tech';
//                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
//                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
//                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];
//
//                    $result .= $this->sendTest($paramMail);
//                    break;
//                case 'badTele':
//                    $paramMail['sale_net_type'] = 'Tele';
//                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
//                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
//                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];
//
//                    $result .= $this->sendTest($paramMail);
//                    break;
//                case 'badQGD':
//                    $paramMail['sale_net_type'] = 'QGD';
//                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
//                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
//                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];
//
//                    $result .= $this->sendTest($paramMail);
//                    break;
//                case 'badCUS':
//                    $paramMail['sale_net_type'] = 'CUS';
//                    $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
//                    $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
//                    $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];
//
//                    $result .= $this->sendTest($paramMail);
//                    break;
//                case 'badCL':
//                    if($resCheckSend['sendCL']){
//                        // Trường hợp tạo checklist mà không đánh giá
//                        if(count($paramMail['results']['badCL']) == 1){
//                            continue;
//                        }
//                        $paramMail['sale_net_type'] = 'CL';
//                        $paramMail['point'] = $paramMail['results'][$type]['other']['point'];
//                        $paramMail['note'] = $paramMail['results'][$type]['other']['note'];
//                        $paramMail['csat'] = $paramMail['results'][$type]['other']['csat'];
//
//                        $result .= $this->sendTest($paramMail);
//                    }
//                    break;
//                default:
//            }
//        }

        return $result;
    }

    private function send($paramMail) {
        $help = new HelpProvider();
        $model_notification = new PushNotification();
        $paramMail['confirm_code'] = md5($paramMail['shd'] . '-' . $paramMail['code'] . '-' . $paramMail['sale_net_type'] . '-' . $paramMail['date']);
        $paramMail['confirm_link'] = $this->domain_confirm . 'confirm-notification?code=' . $paramMail['confirm_code'];

        //Kiểm tra để lấy input theo loại gọi api Sale hay net
        switch($paramMail['sale_net_type']){
            case 'Sale':
                $paramMail['description'] = $help->getDescriptionForSendSale($paramMail);
                $input = $help->getParamApiSale($paramMail);
                break;
            case 'Tech':
                $paramMail['template'] = html_entity_decode(view('emails.sendNotification', ['param' => $paramMail]));
                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – ' . $paramMail['point'] . ' điểm – ' . $paramMail['shd'];
                $input = $help->getParamApiTech($paramMail);
                break;
            case 'Tele':
                $paramMail['template'] = html_entity_decode(view('emails.sendNotification', ['param' => $paramMail]));
                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – ' . $paramMail['point'] . ' điểm – ' . $paramMail['shd'];
                $input = $help->getParamApiTele($paramMail);
                break;
            case 'CL':
                $paramMail['template'] = html_entity_decode(view('emails.sendNotificationCheckList', ['param' => $paramMail]));
                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – Checklist phát sinh – CSAT CLDV Internet/Truyền hình -  '. $paramMail['point'] .' điểm – ' . $paramMail['shd'];
                $input = $help->getParamApiCL($paramMail);
                break;
            case 'QGD':
                $paramMail['template'] = html_entity_decode(view('emails.sendNotificationQGD', ['param' => $paramMail]));
                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – CSAT'.$paramMail['point'] . ' – ' . $paramMail['shd'];
                $input = $help->getParamApiQGD($paramMail);
                break;
            case 'CUS':
                $paramMail['template'] = html_entity_decode(view('emails.sendNotificationCUS', ['param' => $paramMail]));
                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – CSAT'.$paramMail['point'] . ' – ' . $paramMail['shd'];
                $input = $help->getParamApiCUS($paramMail);
                break;
            default:
                return;
        }

        //Lưu lại thông tin trước khi push sang bên ISC
        $model_notification->insertPushNotification($input[strtolower($paramMail['sale_net_type'])], $paramMail);

        $input['paramMail'] = $paramMail;
        //Đưa vào hàng đợi gọi api
        switch($paramMail['sale_net_type']){
            case 'Sale':
                $job = (new SendNotificationMobile($input))->onQueue('mobile');
                break;
            default:
                $job = (new SendNotificationEmail($input))->onQueue('emails');
        }
        Bus::dispatch($job);
    }

//    private function sendTest($paramMail) {
//        $help = new HelpProvider();
//        $paramMail['confirm_code'] = md5($paramMail['shd'] . '-' . $paramMail['code'] . '-' . $paramMail['sale_net_type'] . '-' . $paramMail['date']);
//        $paramMail['confirm_link'] = $this->domain_confirm . 'confirm-notification?code=' . $paramMail['confirm_code'];
//
//        //Kiểm tra để lấy input theo loại gọi api Sale hay net
//        switch($paramMail['sale_net_type']){
//            case 'Sale':
////                $paramMail['template'] = html_entity_decode(view('emails.sendNotification', ['param' => $paramMail]));
////                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – ' . $paramMail['point'] . ' điểm – ' . $paramMail['shd'];
//                $paramMail['description'] = $help->getDescriptionForSendSale($paramMail);
//                $input = $help->getParamApiSale($paramMail);
//                return json_encode($input);
//                break;
//            case 'Tech':
//                $paramMail['template'] = html_entity_decode(view('emails.sendNotification', ['param' => $paramMail]));
//                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – ' . $paramMail['point'] . ' điểm – ' . $paramMail['shd'];
//                $input = $help->getParamApiTech($paramMail);
//                dump($paramMail);
//                return $paramMail['template'];
//                break;
//            case 'Tele':
//                $paramMail['template'] = html_entity_decode(view('emails.sendNotification', ['param' => $paramMail]));
//                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – ' . $paramMail['point'] . ' điểm – ' . $paramMail['shd'];
//                $input = $help->getParamApiTele($paramMail);
//                dump($paramMail);
//                return $paramMail['template'];
//                break;
//            case 'CL':
//                $paramMail['template'] = html_entity_decode(view('emails.sendNotificationCheckList', ['param' => $paramMail]));
//                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – Checklist phát sinh – CSAT CLDV Internet/Truyền hình -  '. $paramMail['point'] .' điểm – ' . $paramMail['shd'];
//                $input = $help->getParamApiCL($paramMail);
//                dump($paramMail);
//                return $paramMail['template'];
//                break;
//            case 'QGD':
//                $paramMail['template'] = html_entity_decode(view('emails.sendNotificationQGD', ['param' => $paramMail]));
//                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – CSAT'.$paramMail['point'] . ' – ' . $paramMail['shd'];
//                $input = $help->getParamApiQGD($paramMail);
//                dump($paramMail);
//                return $paramMail['template'];
//                break;
//            case 'CUS':
//                $paramMail['template'] = html_entity_decode(view('emails.sendNotificationCUS', ['param' => $paramMail]));
//                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – CSAT'.$paramMail['point'] . ' – ' . $paramMail['shd'];
//                $input = $help->getParamApiCUS($paramMail);
//                dump($paramMail);
//                return $paramMail['template'];
//                break;
//            default:
//                return;
//        }
//
//        $input['paramMail'] = $paramMail;
//        return $input;
//    }

    public function updateNoteForResend($paramMail) {
        $help = new HelpProvider();
        $model_notification = new PushNotification();
        $res = (array) $model_notification->getPushNotificationToCheckDuplicate($paramMail);

        $paramMail['confirm_code'] = $res['confirm_code'];
        $paramMail['confirm_link'] = $this->domain_confirm . 'confirm-notification?code=' . $paramMail['confirm_code'];

        //Kiểm tra để lấy input theo loại gọi api Sale hay net
        switch($paramMail['sale_net_type']){
            case 'Sale':
                $paramMail['description'] = $help->getDescriptionForSendSale($paramMail);
                $input = $help->getParamApiSale($paramMail);
                break;
            case 'Tech':
                $paramMail['template'] = html_entity_decode(view('emails.sendNotification', ['param' => $paramMail]));
                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – ' . $paramMail['point'] . ' điểm – ' . $paramMail['shd'];
                $input = $help->getParamApiNet($paramMail);
                break;
            case 'Tele':
                $paramMail['subject'] = '[CEM – Độ hài lòng KH] – ' . $paramMail['location'] . ' – ' . $paramMail['point'] . ' điểm – ' . $paramMail['shd'];
                $input = $help->getParamApiTele($paramMail);
                break;
            default:
                return;
        }

        $res['api_input'] = json_encode($input);
        $res['push_notification_note'] = $paramMail['note'];
        $res['push_notification_param'] = json_encode($paramMail);
        $model_notification->updateNoteNotification($res);
    }

    private function isSendAgain($param) {
        //Tìm thông tin trong queue nếu có
        $model_notification = new PushNotification();
        $out = $model_notification->getPushNotificationToCheckDuplicate($param);
        $res = false;
        //Nếu tìm thấy dữ liệu trùng
        if (count($out) > 0) {
            $res = true;
        }
        return $res;
    }
}
