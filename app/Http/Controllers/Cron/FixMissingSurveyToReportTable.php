<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\SurveySections;
use App\Models\SurveySectionsReport;

class FixMissingSurveyToReportTable extends Controller {
    public function fixMissedSurveys(){
        $surveyModel = new SurveySections();
        $result = $surveyModel->checkMissedSurveys();
        $help = new HelpProvider();
        if(!empty($result)){
            foreach ($result as $val){
                $this->saveTableSurveySectionsReport($val->section_id, 'insert');
            }
        }
        return $help->responseSuccess('Đã cập nhật thông tin qua bảng report');
    }

    private function saveTableSurveySectionsReport($sectionId, $type = 'insert') {
        $modelSurSecRep = new SurveySectionsReport();

        //Lấy thông tin khảo sát
        $infoSurSec = (array) $modelSurSecRep->getInfoForReport($sectionId);
        if (empty($infoSurSec)) {
            return ['state' => 'false', 'message' => 'Mã khảo sát không hợp thời'];
        }

        //Các field trong bảng survey_section_report
        $arraySection = [
            'section_id',
            'section_survey_id',
            'section_code',
            'section_contract_num',
            'section_contact_phone',
            'section_note',
            'section_sub_parent_desc',
            'section_supporter',
            'section_subsupporter',
            'section_user_name',
            'section_connected',
            'section_acc_sale',
            'section_action',
            'section_location_id',
            'section_location',
            'section_branch_code',
            'section_center_list',
            'section_user_modified',
            'section_date_modified',
            'section_time_start',
            'section_time_completed',
            'section_account_inf',
            'section_account_list',
            'sale_center_id',
            'section_count_connected',
            'section_sale_branch_code'
        ];

        $array = [
            'question',
            'answer',
            'answer_extra_id',
            'note'
        ];

        // question_id => prefix
        $arrayPrefix = [
            '6' => 'nps_',
            '8' => 'nps_',
            '5' => 'nps_',
            '7' => 'nps_',
            '16' => 'nps_',
            '17' => 'nps_',
            '24' => 'nps_',
            '25' => 'nps_',
            '1' => 'csat_salesman_',
            '23' => 'csat_salesman_',
            '2' => 'csat_deployer_',
            '22' => 'csat_deployer_',
            '10' => 'csat_net_',
            '14' => 'csat_net_',
            '20' => 'csat_net_',
            '11' => 'csat_tv_',
            '15' => 'csat_tv_',
            '21' => 'csat_tv_',
            '4' => 'csat_maintenance_staff_',
            '12' => 'csat_maintenance_net_',
            '13' => 'csat_maintenance_tv_'
        ];

        //Đưa giá trị section vào
        foreach ($arraySection as $val) {
            $param[$val] = $infoSurSec[$val];
        }

        $surveyId = $infoSurSec['section_survey_id'];
        //Lấy thông tin điểm khảo sát
        $infoNPS = (array) $modelSurSecRep->getInfoNPS($sectionId, $surveyId);

        //Kiểm tra và gán các giá trị đã có vào các field mới
        foreach ($infoNPS as $oneQuestion) {
            $oneQuestion = (array) $oneQuestion;

            $check = array_key_exists($oneQuestion['question'], $arrayPrefix);
            if (!$check) {
                continue;
            }

            $prefix = $arrayPrefix[$oneQuestion['question']];
            switch ($oneQuestion['question']) {
                case '1':
                case '23':
                    $param[$prefix . 'point'] = $oneQuestion['kinhdoanh'];
                    break;
                case '2':
                case '4':
                case '22':
                    $param[$prefix . 'point'] = $oneQuestion['kythuat'];
                    break;
                case '5':
                case '7':
                case '17':
                case '25':
                    $param[$prefix . 'improvement'] = $oneQuestion[$prefix . 'improvement'];
                    $param[$prefix . 'improvement_note'] = $oneQuestion[$prefix . 'improvement_note'];
                    break;
                case '6':
                case '8':
                case '24':
                    $param[$prefix . 'point'] = $oneQuestion['point'];
                    break;
                case '10':
                case '12':
                case '20':
                    $param[$prefix . 'point'] = $oneQuestion['internet'];
                    $param['result_action_net'] = $oneQuestion['survey_result_action'];
                    break;
                case '14':
                    $param[$prefix . 'point'] = $oneQuestion['internet'];
                    break;
                case '11':
                case '13':
                case '21':
                    $param[$prefix . 'point'] = $oneQuestion['truyenhinh'];
                    $param['result_action_tv'] = $oneQuestion['survey_result_action'];
                    break;
                case '15':
                    $param[$prefix . 'point'] = $oneQuestion['truyenhinh'];
                    break;
            }

            if ($oneQuestion['question'] == "5" || $oneQuestion['question'] == "7" || $oneQuestion['question'] == "17" || $oneQuestion['question'] == "25") {
                continue;
            }

            foreach ($array as $val) {
                $param[$prefix . $val] = $oneQuestion[$val];
            }
        }

        //Gán các giá trị ngoại lệ
        $param['section_objID'] = $infoSurSec['objid'];
        if ($type == 'insert') {
            $param['insert_at'] = date('Y-m-d H:i:s');
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $modelSurSecRep->insertSurveySectionReport($param);
        } else {
            $param['updated_at'] = date('Y-m-d H:i:s');
            $result = $modelSurSecRep->updateSurveySectionReport($param);
        }

        $message = 'Không thể chuyển đổi dữ liệu';
        if ($result) {
            $message = 'Dữ liệu chuyển đổi thành công';
        }

        return ['state' => $result, 'message' => $message];
    }
}
