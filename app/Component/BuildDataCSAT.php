<?php

namespace App\Component;

use App\Models\SummaryAction;
use App\Models\SummaryCsat;
use App\Models\SummaryReason;
use App\Models\SurveySections;
use App\Models\Location;
use App\Models\OutboundAnswers;
use App\Models\SurveyReport;
use App\Models\SurveyViolations;

class BuildDataCSAT {

    protected $modelSurveySections;
    protected $modelSurveyReports;
    protected $modelSurveyViolations;
    protected $modelStatus;
    protected $modelLocation;
    protected $modelSummaryCSAT;
    protected $modelSummaryReason;
    protected $modelSummaryAction;

    public function __construct() {
        $this->modelSurveySections = new SurveySections();
        $this->modelSurveyReports = new SurveyReport();
        $this->modelSurveyViolations = new SurveyViolations();
        $this->modelStatus = new OutboundAnswers();
        $this->modelLocation = new Location();
        $this->modelSummaryCSAT = new SummaryCsat();
        $this->modelSummaryReason = new SummaryReason();
        $this->modelSummaryAction = new SummaryAction();
    }

    public function CSATServiceReport($from_date, $to_date, $region, $branch, $branchcode) {
        //Mảng các trường cần xuất báo cáo
        $arrayTypeSurvey = ['tk_dr', 'tk_tl', 'tin', 'in', 'mobi', 'stks', 'ss', 'th'];
        $arrayKeyData = ['csat1', 'csat2', 'csat_t12', 'csat_d', 'csat_sl'];
        $arrayFields = [];

        $cs = 'cs';
        $cus = 'cus';
        $sta = 'sta';
        $act = 'act';
        $temp = 'temp';

        foreach ($arrayTypeSurvey as $key => $value) {
            foreach ($arrayKeyData as $key2 => $value2) {
                $arrayFields[$value2 . '_' . $value] = 0;
            }
        }
        //Thêm vào các trường hợp lỗi và hướng giải quyết vào mảng trường báo cáo
        $statuses = $this->modelStatus->getAnswerByGroup([20, 21, 22], [88]);
        $statusesNet = $statusesTv = $arrayAction = $orderNet = [];
        $orderNetWant = [85, 97, 120, 89, 91, 92, 94, 98, 87, 90, 86];
        $oderActionNotCSWant = [115,116,117];

        foreach ($statuses as $status) {
            if ($status->answer_group == 20) {
                $arrayFields[$cs][$sta.'_' . $status->answer_id] = 0;
                $arrayFields[$cus][$sta.'_' . $status->answer_id] = 0;
                $statusesNet[$status->answer_id] = $status;
                $orderNet[$status->answer_id] = array_search($status->answer_id, $orderNetWant);
            } elseif ($status->answer_group == 22) {
                $arrayFields[$cs][$sta.'_' . $status->answer_id] = 0;
                $arrayFields[$cus][$sta.'_' . $status->answer_id] = 0;
                $statusesTv[$status->answer_id] = $status;
            } else {
                $arrayFields[$cs][$act.'_' . $status->answer_id] = 0;
                $arrayAction[$cs][$status->answer_id] = $status;
                if(in_array($status->answer_id, $oderActionNotCSWant)){
                    $arrayFields[$cus][$act.'_' . $status->answer_id] = 0;
                    $arrayAction[$cus][$status->answer_id] = $status;
                }
            }
        }
        $arrayFields[$cs][$sta.'_t'] = 0;
        $arrayFields[$cs][$act.'_t'] = 0;
        $arrayFields[$cus][$sta.'_t'] = 0;
        $arrayFields[$cus][$act.'_t'] = 0;

        //Sắp xếp cột dữ liệu "nguyên nhân ghi nhận" theo yêu cầu của mấy bác
        array_multisort($orderNet, SORT_ASC, $statusesNet);

        //Lấy lại key đã bị mất sau khi sắp xếp
        $statusTemp = [];
        foreach ($statusesNet as $status) {
            $statusTemp[$status->answer_id] = $status;
        }
        $statusesNet = $statusTemp;

        //Mảng dữ liệu báo cáo net và tv
        $arrayNet = [];
        $arrayTv = [];

        //get location by region
        $locations = $this->modelLocation->getAllLocation();
        foreach ($locations as $location) {
            $zone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
            $arrayNet[$zone] = $arrayTv[$zone] = $arrayFields;
            $arrayNet[$location->region] = $arrayTv[$location->region] = $arrayFields;
            $arrayNet[$temp] = $arrayTv[$temp] = $arrayFields;
        }

        // Lấy thông tin và xử lý điểm csat
        $arrayObjectPocNet = [
            '1:5' => '_tk_dr',
            '6:5' => '_tk_tl',
            '2:21' => '_in',
            '2:23' => '_tin',
            '3:5' => '_mobi',
            '9:5' => '_stks',
            '10:5' => '_ss'
        ];
        $arrayObjectPocTv = [
            '1:6' => '_tk_dr',
            '6:6' => '_tk_tl',
            '2:22' => '_in',
            '2:24' => '_tin',
            '3:6' => '_mobi',
            '9:6' => '_stks',
            '10:6' => '_ss'
        ];
        $params = [
            'dayFrom' => $from_date,
            'dayTo' => $to_date,
            'arrayPOC' => [1, 2, 3, 6, 9, 10],
            'arrayObject' => [5, 6, 21, 22, 23, 24],
            'arrayLocation' => [],
            'arrayChannel' => [],
        ];

        $totalCSAT = $this->modelSummaryCSAT->getCSATTotalbyParam($params);
        foreach ($totalCSAT as $oneResult) {
            $arrayZone = [
                $oneResult->isc_location_id . '-' . $oneResult->isc_branch_code,
                'Vùng ' . $oneResult->zone_id,
            ];

            foreach ($arrayZone as $zone) {
                if (isset($arrayObjectPocNet[$oneResult->poc_id . ':' . $oneResult->object_id])) {
                    $arrayNet[$zone]['csat1' . $arrayObjectPocNet[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_1;
                    $arrayNet[$zone]['csat2' . $arrayObjectPocNet[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_2;
                    $arrayNet[$zone]['csat_t12' . $arrayObjectPocNet[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2;
                    $arrayNet[$zone]['csat_d' . $arrayObjectPocNet[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2 * 2 + $oneResult->csat_3 * 3 + $oneResult->csat_4 * 4 + $oneResult->csat_5 * 5;
                    $arrayNet[$zone]['csat_sl' . $arrayObjectPocNet[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2 + $oneResult->csat_3 + $oneResult->csat_4 + $oneResult->csat_5;

                    $arrayNet[$zone]['csat1_th'] += $oneResult->csat_1;
                    $arrayNet[$zone]['csat2_th'] += $oneResult->csat_2;
                    $arrayNet[$zone]['csat_t12_th'] += $oneResult->csat_1 + $oneResult->csat_2;
                    $arrayNet[$zone]['csat_d_th'] += $oneResult->csat_1 + $oneResult->csat_2 * 2 + $oneResult->csat_3 * 3 + $oneResult->csat_4 * 4 + $oneResult->csat_5 * 5;
                    $arrayNet[$zone]['csat_sl_th'] += $oneResult->csat_1 + $oneResult->csat_2 + $oneResult->csat_3 + $oneResult->csat_4 + $oneResult->csat_5;
                }
                if (isset($arrayObjectPocTv[$oneResult->poc_id . ':' . $oneResult->object_id])) {
                    $arrayTv[$zone]['csat1' . $arrayObjectPocTv[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_1;
                    $arrayTv[$zone]['csat2' . $arrayObjectPocTv[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_2;
                    $arrayTv[$zone]['csat_t12' . $arrayObjectPocTv[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2;
                    $arrayTv[$zone]['csat_d' . $arrayObjectPocTv[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2 * 2 + $oneResult->csat_3 * 3 + $oneResult->csat_4 * 4 + $oneResult->csat_5 * 5;
                    $arrayTv[$zone]['csat_sl' . $arrayObjectPocTv[$oneResult->poc_id . ':' . $oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2 + $oneResult->csat_3 + $oneResult->csat_4 + $oneResult->csat_5;

                    $arrayTv[$zone]['csat1_th'] += $oneResult->csat_1;
                    $arrayTv[$zone]['csat2_th'] += $oneResult->csat_2;
                    $arrayTv[$zone]['csat_t12_th'] += $oneResult->csat_1 + $oneResult->csat_2;
                    $arrayTv[$zone]['csat_d_th'] += $oneResult->csat_1 + $oneResult->csat_2 * 2 + $oneResult->csat_3 * 3 + $oneResult->csat_4 * 4 + $oneResult->csat_5 * 5;
                    $arrayTv[$zone]['csat_sl_th'] += $oneResult->csat_1 + $oneResult->csat_2 + $oneResult->csat_3 + $oneResult->csat_4 + $oneResult->csat_5;
                }
            }
        }

        // Lấy thông tin và xử lý reason

        $params = [
            'dayFrom' => $from_date,
            'dayTo' => $to_date,
            'arrayPOC' => [1, 2, 3, 6, 9, 10],
            'arrayObject' => [17, 18],
            'arrayLocation' => [],
            'arrayChannel' => [],
        ];

        $totalReason = $this->modelSummaryReason->getReasonTotalbyParam($params);
        foreach ($totalReason as $oneResult) {
            $arrayZone = [
                $oneResult->isc_location_id . '-' . $oneResult->isc_branch_code,
                'Vùng ' . $oneResult->zone_id,
            ];

            $department = $cus;
            if($oneResult->poc_id != 3){
                $department = $cs;
            }

            foreach ($arrayZone as $zone) {
                if (isset($arrayNet[$zone][$department][$sta.'_' . $oneResult->reason_id]) && $oneResult->object_id == 18) {
                    $arrayNet[$zone][$department][$sta.'_' . $oneResult->reason_id] += $oneResult->total;
                    $arrayNet[$zone][$department][$sta.'_t'] += $oneResult->total;
                }
                if (isset($arrayTv[$zone][$department][$sta.'_' . $oneResult->reason_id]) && $oneResult->object_id == 17) {
                    $arrayTv[$zone][$department][$sta.'_' . $oneResult->reason_id] += $oneResult->total;
                    $arrayTv[$zone][$department][$sta.'_t'] += $oneResult->total;
                }
            }
        }

        // Lấy thông tin và xử lý action
        $params = [
            'dayFrom' => $from_date,
            'dayTo' => $to_date,
            'arrayPOC' => [1, 2, 3, 6, 9, 10],
            'arrayObject' => [19, 20],
            'arrayLocation' => [],
            'arrayChannel' => [],
        ];

        $totalAction = $this->modelSummaryAction->getActionTotalbyParam($params);
        foreach ($totalAction as $oneResult) {
            $arrayZone = [
                $oneResult->isc_location_id . '-' . $oneResult->isc_branch_code,
                'Vùng ' . $oneResult->zone_id,
            ];

            $department = $cus;
            if($oneResult->poc_id != 3){
                $department = $cs;
            }

            foreach ($arrayZone as $zone) {
                if (isset($arrayNet[$zone][$department][$act.'_' . $oneResult->action_id]) && $oneResult->object_id == 20) {
                    $arrayNet[$zone][$department][$act.'_' . $oneResult->action_id] += $oneResult->total;
                    $arrayNet[$zone][$department][$act.'_t'] += $oneResult->total;
                }
                if (isset($arrayTv[$zone][$department][$act.'_' . $oneResult->action_id]) && $oneResult->object_id == 19) {
                    $arrayTv[$zone][$department][$act.'_' . $oneResult->action_id] += $oneResult->total;
                    $arrayTv[$zone][$department][$act.'_t'] += $oneResult->total;
                }
            }
        }

        //get location by region
        $locations = $this->modelLocation->getBranchLocationPlus($branch, $branchcode, $region);

        return $result = [
            'statusesNet' => $statusesNet,
            'statusesTv' => $statusesTv,
            'actions' => $arrayAction,
            'csatNet' => $arrayNet,
            'csatTv' => $arrayTv,
            'locations' => $locations,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'region' => $region,
            'arrayTypeSurvey' => $arrayTypeSurvey,
        ];
    }

    public function CSATStaffReport($from_date, $to_date, $region, $branch, $branchcode) {
        //Mảng các trường cần xuất báo cáo
        $arrayFields = [
            'csat_tong_sales' => 0, 'csat1_sales' => 0, 'csat2_sales' => 0, 'csat12_sales' => 0, 'csat12_diem_sales' => 0, 'csat1_cbc_sales' => 0, 'csat2_cbc_sales' => 0, 'csat12_cbc_sales' => 0,
            'csat_tong_deploy' => 0, 'csat1_deploy' => 0, 'csat2_deploy' => 0, 'csat12_deploy' => 0, 'csat12_diem_deploy' => 0, 'csat1_cbc_deploy' => 0, 'csat2_cbc_deploy' => 0, 'csat12_cbc_deploy' => 0,
            'csat_tong_maintain' => 0, 'csat1_maintain' => 0, 'csat2_maintain' => 0, 'csat12_maintain' => 0, 'csat12_diem_maintain' => 0, 'csat1_cbc_maintain' => 0, 'csat2_cbc_maintain' => 0, 'csat12_cbc_maintain' => 0,
            'csat_tong_nvtc' => 0, 'csat1_nvtc' => 0, 'csat2_nvtc' => 0, 'csat12_nvtc' => 0, 'csat12_diem_nvtc' => 0, 'csat1_cbc_nvtc' => 0, 'csat2_cbc_nvtc' => 0, 'csat12_cbc_nvtc' => 0,
            'csat_tong_th' => 0, 'csat1_th' => 0, 'csat2_th' => 0, 'csat12_th' => 0, 'csat12_diem_th' => 0, 'csat1_cbc_th' => 0, 'csat2_cbc_th' => 0, 'csat12_cbc_th' => 0
        ];

        for ($i = 1; $i <= 12; $i++) {
            $arrayFields['violation_sales_' . $i] = 0;
            $arrayFields['violation_deploy_' . $i] = 0;
            $arrayFields['violation_maintaince_' . $i] = 0;
            $arrayFields['violation_deploy_maintaince_' . $i] = 0;
            $arrayFields['violation_nvtc_' . $i] = 0;
            if ($i <= 5) {
                $arrayFields['punish_sales_' . $i] = 0;
                $arrayFields['punish_deploy_' . $i] = 0;
                $arrayFields['punish_maintaince_' . $i] = 0;
                $arrayFields['punish_deploy_maintaince_' . $i] = 0;
                $arrayFields['punish_nvtc_' . $i] = 0;
            }
        }
        $arrayFields['violation_sales_t'] = 0;
        $arrayFields['violation_deploy_t'] = 0;
        $arrayFields['violation_maintaince_t'] = 0;
        $arrayFields['violation_deploy_maintaince_t'] = 0;
        $arrayFields['violation_nvtc_t'] = 0;
        $arrayFields['punish_sales_t'] = 0;
        $arrayFields['punish_deploy_t'] = 0;
        $arrayFields['punish_maintaince_t'] = 0;
        $arrayFields['punish_deploy_maintaince_t'] = 0;
        $arrayFields['punish_nvtc_t'] = 0;
        $arrayFields['ftq_sales'] = 0;
        $arrayFields['ftq_deploy'] = 0;
        $arrayFields['ftq_maintaince'] = 0;
        $arrayFields['ftq_deploy_maintaince'] = 0;
        $arrayFields['ftq_nvtc'] = 0;

        //Mảng dữ liệu báo cáo
        $arrayReport = [];

        //get location by region
        $locations = $this->modelLocation->getAllLocation();
        foreach ($locations as $location) {
            $zone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
            $arrayReport[$zone] = $arrayFields;
            $arrayReport[$location->region] = $arrayFields;
            $arrayReport['temp'] = $arrayFields;
        }

        // Lấy thông tin và xử lý điểm csat
//        $arrayObjectPocNet = [
//            '1:5' => '_tk_dr',
//            '6:5' => '_tk_tl',
//            '2:21' => '_in',
//            '2:23' => '_tin',
//            '3:5' => '_mobi'
//        ];
//        $arrayObjectPocTv = [
//            '1:6' => '_tk_dr',
//            '6:6' => '_tk_tl',
//            '2:22' => '_in',
//            '2:24' => '_tin',
//            '3:6' => '_mobi'
//        ];
//        $params = [
//            'dayFrom' => $from_date,
//            'dayTo' => $to_date,
//            'arrayPOC' => [1,2,3,6],
//            'arrayObject' => [5,6,21,22,23,24],
//            'arrayLocation' => [],
//            'arrayChannel' => [],
//        ];
//
//        $totalCSAT = $this->modelSummaryCSAT->getCSATTotalbyParam($params);
//        foreach($totalCSAT as $oneResult){
//            $arrayZone = [
//                $oneResult->isc_location_id . '-' . $oneResult->isc_branch_code,
//                'Vùng ' . $oneResult->zone_id,
//            ];
//
//            foreach($arrayZone as $zone){
//                if(isset($arrayObjectPocNet[$oneResult->poc_id.':'.$oneResult->object_id])){
//                    $arrayNet[$zone]['csat1'.$arrayObjectPocNet[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_1;
//                    $arrayNet[$zone]['csat2'.$arrayObjectPocNet[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_2;
//                    $arrayNet[$zone]['csat_t12'.$arrayObjectPocNet[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2;
//                    $arrayNet[$zone]['csat_d'.$arrayObjectPocNet[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2*2 + $oneResult->csat_3*3 + $oneResult->csat_4*4 + $oneResult->csat_5*5;
//                    $arrayNet[$zone]['csat_sl'.$arrayObjectPocNet[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2 + $oneResult->csat_3 + $oneResult->csat_4 + $oneResult->csat_5;
//
//                    $arrayNet[$zone]['csat1_th'] += $oneResult->csat_1;
//                    $arrayNet[$zone]['csat2_th'] += $oneResult->csat_2;
//                    $arrayNet[$zone]['csat_t12_th'] += $oneResult->csat_1 + $oneResult->csat_2;
//                    $arrayNet[$zone]['csat_d_th'] += $oneResult->csat_1 + $oneResult->csat_2*2 + $oneResult->csat_3*3 + $oneResult->csat_4*4 + $oneResult->csat_5*5;
//                    $arrayNet[$zone]['csat_sl_th'] += $oneResult->csat_1 + $oneResult->csat_2 + $oneResult->csat_3 + $oneResult->csat_4 + $oneResult->csat_5;
//                }
//                if(isset($arrayObjectPocTv[$oneResult->poc_id.':'.$oneResult->object_id])){
//                    $arrayTv[$zone]['csat1'.$arrayObjectPocTv[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_1;
//                    $arrayTv[$zone]['csat2'.$arrayObjectPocTv[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_2;
//                    $arrayTv[$zone]['csat_t12'.$arrayObjectPocTv[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2;
//                    $arrayTv[$zone]['csat_d'.$arrayObjectPocTv[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2*2 + $oneResult->csat_3*3 + $oneResult->csat_4*4 + $oneResult->csat_5*5;
//                    $arrayTv[$zone]['csat_sl'.$arrayObjectPocTv[$oneResult->poc_id.':'.$oneResult->object_id]] += $oneResult->csat_1 + $oneResult->csat_2 + $oneResult->csat_3 + $oneResult->csat_4 + $oneResult->csat_5;
//
//                    $arrayTv[$zone]['csat1_th'] += $oneResult->csat_1;
//                    $arrayTv[$zone]['csat2_th'] += $oneResult->csat_2;
//                    $arrayTv[$zone]['csat_t12_th'] += $oneResult->csat_1 + $oneResult->csat_2;
//                    $arrayTv[$zone]['csat_d_th'] += $oneResult->csat_1 + $oneResult->csat_2*2 + $oneResult->csat_3*3 + $oneResult->csat_4*4 + $oneResult->csat_5*5;
//                    $arrayTv[$zone]['csat_sl_th'] += $oneResult->csat_1 + $oneResult->csat_2 + $oneResult->csat_3 + $oneResult->csat_4 + $oneResult->csat_5;
//                }
//            }
//        }

        $survey = $this->modelSurveySections->getSurveySectionCSATNV($from_date, $to_date);
        $violation = $this->modelSurveyViolations->getSurveyViolationCSATNV($from_date, $to_date);
        //Gắn dữ liệu
        foreach ($survey as $oneResult) {
            $zone = $oneResult->section_location_id . '-' . $oneResult->section_branch_code;
            $temp = explode(' ', $oneResult->section_sub_parent_desc);
            foreach ($oneResult as $key => $val) {
                if (isset($arrayReport[$zone][$key])) {
                    $arrayReport[$zone][$key] += $val;
                }
                if (isset($arrayReport['Vùng ' . $temp[1]][$key])) {
                    $arrayReport['Vùng ' . $temp[1]][$key] += $val;
                }
            }
        }

        foreach ($violation as $oneResult) {
            $zone = $oneResult->section_location_id . '-' . $oneResult->section_branch_code;
            $temp = explode(' ', $oneResult->section_sub_parent_desc);
            foreach ($oneResult as $key => $val) {
                if (isset($arrayReport[$zone][$key])) {
                    $arrayReport[$zone][$key] += $val;
                }
                if (isset($arrayReport['Vùng ' . $temp[1]][$key])) {
                    $arrayReport['Vùng ' . $temp[1]][$key] += $val;
                }
            }
        }

        //get location by region
        $locations = $this->modelLocation->getBranchLocationPlus($branch, $branchcode, $region);

        return $result = [
            'data' => $arrayReport,
            'locations' => $locations,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'region' => $region
        ];
    }

}
