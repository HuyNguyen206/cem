<?php

/*
 * Controlers kết nối tới API của ISC
 * 
 */

namespace App\Http\Controllers\Test;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ListEmailCUS;
use App\Models\ListEmailQGD;
use App\Models\SummaryBranches;
use App\Models\SurveySections;
use App\Models\SurveyViolations;
use App\Models\VoiceRecord;
use App\Models\Api\ApiHelper;
use Illuminate\Support\Facades\Redis;
use App\Models\Location;
use App\Component\ExtraFunction;
use App\models\SurveyResult;
use App\Models\OutboundAnswers;
use App\Models\ListInvalidSurveyCase;
use Illuminate\Support\Facades\Mail;
use Exception;
use DB;
use App\Models\Apiisc;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{

    var $link_API = 'http://cemcc.fpt.net/';

    public function test()
    {
        $pdo = DB::connection('sqlsrv_voice_cam_1')->getPdo();
        dump($pdo);
        die;
        $dataVoice = DB::connection('sqlsrv_voice_cam_1')->table('RecordOriginalData')->select('voiceId', 'Channel', 'RecordReference', 'StopRecordTime')
//            ->where('StopRecordTime', '>=' , $fifteenMinuteBefore)
//            ->where('StopRecordTime', '<=' , $fifteenMinuteAfter)
//            ->where('CalledID', $phone)
            ->get();

        dump(date('Y-m-d H:i:s', strtotime('2017-05-25T11:30:40.49')));
            die;
        $infoAcc = array('ObjID' => 0,
            'Contract' => 'BBDF22017',
            'IDSupportlist' => '6633802',
            'Type' => 2
        );

        /*
         * Lấy thông tin khách hàng
         */
        $apiIsc = new Apiisc();
        $responseAccountInfo = $apiIsc->GetFullAccountInfo($infoAcc);
        dump($responseAccountInfo);die;

        $this->getFileConverse();


        dump(explode(',', '1'));
        dump(explode(',', '1,2'));
        die;
//        $day='2018-11-01';
//        if(Redis::exists('testArrayDay'))
//            Redis::rpush('testArrayDay','2017-01-02');
//            $get=Redis::lpop('testArrayDay');
//        else Redis::set('testArrayDay',[]);
//                    var_dump($get);die;
//        die;
//        var_dump(date('l'));die;
        $help = new ApiHelper();
        $param['sectionId'] = 3880648;
//        $param['sectionId'] = 3676902;
//        $param['sectionId'] = 3516787;
//        $param['num_type'] = 2;
//        $param['code'] = 1102781962;
//        $param['shd'] = 'DLD024029';

        $result = $help->checkSendMailCounter($param);
        dump($result);
        if ($result['status']) {
            $need = $help->sendMailCounter($param, $result);
            return $need;
        }

        die;

        $arrayExcel = [];
        $pathLocation = 'D:\test\activeTH.txt';
        $fpD = @fopen($pathLocation, "r");
        // Kiểm tra file mở thành công không
        if (!$fpD) {
            var_dump('Mở file list không thành công');
            die;
        } else {
            while (!feof($fpD)) {
                $tempString = fgets($fpD);
                if ($tempString !== false) {
                    $tempExp = preg_split('/[\t]/', $tempString);
                    $arrayExcel[$tempExp[2]] = null;
                }
            }
        }
        fclose($fpD);

        $condition = [
            "surveyFrom" => "2017-09-01 00:00:00",
            "surveyTo" => "2017-10-10 23:59:59",
            "surveyFromInt" => 1504198800,
            "surveyToInt" => 1507654799,
            "region" => [
                0 => "1"
            ],
            "location" => [
                1 => "240",
                2 => "241",
                3 => "26",
                4 => "230",
                5 => "218",
                6 => "20",
                7 => "25",
                8 => "210",
                9 => "33",
                10 => "22",
                11 => "280",
                12 => "27",
                13 => "211",
                14 => "29",
                15 => "320",
                16 => "351",
                17 => "31",
                18 => "39",
                19 => "321",
                20 => "38",
                21 => "30",
                22 => "350",
                23 => "36",
                24 => "37",
                25 => "56",
                26 => "500",
                27 => "511",
                28 => "59",
                29 => "54",
                30 => "58",
                31 => "60",
                32 => "57",
                33 => "52",
                34 => "55",
                35 => "510",
                36 => "53",
                38 => "65",
                39 => "651",
                40 => "62",
                41 => "61",
                42 => "63",
                43 => "68",
                44 => "66",
                45 => "64",
                46 => "76",
                47 => "781",
                48 => "75",
                49 => "780",
                50 => "71",
                51 => "67",
                52 => "711",
                53 => "77",
                54 => "72",
                55 => "79",
                56 => "73",
                57 => "74",
                58 => "70",
            ],
            "branchcode" => [
                0 => 1,
                1 => 2,
                2 => 3,
                3 => 4,
                4 => 5,
                5 => 7,
                6 => 8,
                7 => 9,
                8 => 10,
                9 => 11,
                10 => 12,
                11 => 13,
                12 => 14,
                18 => 6,
                24 => 0,
                25 => 90,
            ],
            "branchcodeSalesMan" => [],
            "brandcodeSaleMan" => "",
            "contractNum" => "",
//            "type" => "1",
            "section_action" => "",
            "section_connected" => "",
            "CSATPointSale" => "",
            "CSATPointNVTK" => "",
            "CSATPointBT" => "",
            "CSATPointNet" => "",
            "CSATPointTV" => "",
            "userSurvey" => "",
            "RateNPS" => "",
            "NPSPoint" => "",
            "departmentType" => "5",
            "salerName" => "",
            "technicalStaff" => "",
            "reportedStatus" => "",
            "NetErrorType" => "",
            "TVErrorType" => "",
            "processingActionsTV" => "",
            "processingActionsInternet" => "",
            "allQuestion" => [
                1 => [
                    0 => 1,
                    1 => 28,
                ],
                3 => [
                    0 => 2,
                    1 => 22,
                    2 => 29,
                    3 => 38,
                ],
                4 => [
                    0 => 4,
                    1 => 30,
                ],
                9 => [
                    0 => 5,
                    1 => 7,
                    2 => 17,
                    3 => 25,
                    4 => 40,
                    5 => 44,
                ],
                10 => [
                    0 => 6,
                    1 => 8,
                    2 => 16,
                    3 => 24,
                    4 => 27,
                    5 => 34,
                    6 => 39,
                    7 => 45,
                ],
                5 => [
                    0 => 10,
                    1 => 12,
                    2 => 14,
                    3 => 20,
                    4 => 41,
                    5 => 46,
                ],
                6 => [
                    0 => 11,
                    1 => 13,
                    2 => 15,
                    3 => 21,
                    4 => 42,
                    5 => 47,
                ],
                11 => [
                    0 => 18,
                ],
                2 => [
                    0 => 23,
                    1 => 32,
                ],
                7 => [
                    0 => 26,
                ],
                8 => [
                    0 => 31,
                ],
                13 => [
                    0 => 33,
                ],
                14 => [
                    0 => 35,
                ],
                29 => [
                    0 => 37,
                ],
                30 => [
                    0 => 43,
                ],
            ],
            "channelConfirm" => "1",
            "CSATPointTransaction" => "",
            "transactionStaffName" => "",
            "transactionType" => "",
            "CSATPointChargeAtHomeStaff" => "",
            "chargeAtHomeStaffName" => "",
            "recordPerPage" => 0,
            "justOnlyLocation" => false,
            "arrayContractNumber" => array_keys($arrayExcel),
            "arraySurveyID" => [1, 6]
        ];
        $currentPage = 0;
        $modelSurveySections = new SurveySections();
        $infoSurvey = $modelSurveySections->searchListSurvey($condition, $currentPage);
        $param['arrayID'] = [];
        $infoSurveyKey = [];
        foreach ($infoSurvey as $val) {
            $param['arrayID'][] = $val->section_id;
            $infoSurveyKey[$val->section_id] = $val;
        }
        $surveyResultModel = new SurveyResult();
        $surveyResults = $surveyResultModel->getSurveyByParam($param);
        $infoSurvey = $this->convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults);
        foreach ($infoSurvey as $val) {
            $arrayExcel[$val->section_contract_num] = [
                'net' => $val->csat_net_point,
                'tv' => $val->csat_tv_point,
                'sale' => $val->csat_salesman_point,
                'deploy' => $val->csat_deployer_point,
            ];
        }
        return view("test/index", ['records' => $arrayExcel]);
    }

    private function convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults)
    {
        $modelOAns = new OutboundAnswers();
        $oAns = $modelOAns->getAnswerByGroup([1, 2]);
        $oAns = json_decode(json_encode($oAns), 1);
        $ansPoints = array_column($oAns, 'answers_point', 'answer_id');
        $ansPoints[-1] = null;

        //set field mặc định
        foreach ($infoSurveyKey as &$info) {
            if (!isset($info->violation_status)) {
                $info->violation_status = null;
            }

            $info->nps_point = null;

            $info->nps_improvement = null;
            $info->nps_improvement_note = null;

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
        if (isset($condition['type']) && $condition['type'] == 2) {
            $maintenance = '_maintenance';
        }

        foreach ($surveyResults as $result) {
            if (array_search($result->survey_result_question_id, array_merge($condition['allQuestion'][1], $condition['allQuestion'][2])) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->csat_salesman_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_salesman_note = $result->survey_result_note;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][3]) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->csat_deployer_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_deployer_note = $result->survey_result_note;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][4]) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->csat_maintenance_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_maintenance_staff_note = $result->survey_result_note;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][5]) !== false) {
                $keyP = 'csat' . $maintenance . '_net_point';
                $keyN = 'csat' . $maintenance . '_net_note';
                $keyA = 'csat' . $maintenance . '_net_answer_extra_id';
                $infoSurveyKey[$result->survey_result_section_id]->$keyP = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->$keyN = $result->survey_result_note;
                $infoSurveyKey[$result->survey_result_section_id]->$keyA = $result->survey_result_answer_extra_id;
                $infoSurveyKey[$result->survey_result_section_id]->result_action_net = $result->survey_result_action;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][6]) !== false) {
                $keyP = 'csat' . $maintenance . '_tv_point';
                $keyN = 'csat' . $maintenance . '_tv_note';
                $keyA = 'csat' . $maintenance . '_tv_answer_extra_id';
                $infoSurveyKey[$result->survey_result_section_id]->$keyP = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->$keyN = $result->survey_result_note;
                $infoSurveyKey[$result->survey_result_section_id]->$keyA = $result->survey_result_answer_extra_id;
                $infoSurveyKey[$result->survey_result_section_id]->result_action_tv = $result->survey_result_action;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][7]) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_note = $result->survey_result_note;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][8]) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_staff_note = $result->survey_result_note;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][9]) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->nps_improvement = $result->survey_result_answer_id;
                $infoSurveyKey[$result->survey_result_section_id]->nps_improvement_note = $result->survey_result_note;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][10]) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->nps_point = $ansPoints[$result->survey_result_answer_id];
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][13]) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_note = $result->survey_result_note;
            }
            if (array_search($result->survey_result_question_id, $condition['allQuestion'][14]) !== false) {
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_staff_note = $result->survey_result_note;
            }
        }
        return $infoSurveyKey;
    }

    public function testInfo($contract, $type, $code)
    {

        $apiISC = new Apiisc();
        $infoAcc = array('ObjID' => 0,
            'Contract' => $contract,
            'IDSupportlist' => $code,
            'Type' => $type
        );
//        $uri = $this->link_API . 'wscustomerinfo.asmx/spCEM_ObjectGetByObjID?';
        $uri = 'http://parapi.fpt.vn/api/RadAPI/spCEM_ObjectGetByObjID/?';
        $uri .= http_build_query($infoAcc);
//        $uri = $this->link_API . 'wscustomerinfo.asmx/spCEM_ObjectGetByObjID?Contract = ' . $contract . '&ID = ' . $code . '&Type = ' . $type;
//        var_dump($apiISC->getAPI($uri));
        dd(json_decode($apiISC->getAPI($uri)));
    }

    public function testExportExcel()
    {
        $user = new User();
        $resultUser = $user->getUserWithZoneRole();
        return view('test/index', ['data' => $resultUser]);
    }

    public function clearCache()
    {
        $data = Redis::keys('laravel:*:stan*');
        foreach ($data as $key) {
            Redis::del($key);
        }
    }

    public function testEmail()
    {
        return view('emails/templateEmail', [
                'zone' => 1,
                'day' => date('d/m/Y'),
                'records' => ['abc'],
            ]
        );


//        return view('emails/templateExcelCSATtv',
//            [
//                'zone' => 1,
//                'day' => date('d/m/Y'),
//                'records' => ['abc'],
//            ]
//        );
    }

    public function updateViolationPoints()
    {
        try {
            $result = DB::table('survey_violations AS s')
                ->select(DB::raw('distinct(s.section_id)'))
                ->where('s.insert_at', '>=', '2017-08-01 00:00:00')
                ->get();
            foreach ($result as $key => $value) {
                $violation = SurveyViolations::where('section_id', '=', $value->section_id)->get();
                foreach ($violation as $key2 => $value2) {
                    $partViolation = $value2;
                    $sectionResult = DB::table('outbound_survey_result AS osr')
                        ->select(DB::raw('
                    MAX(if(osr.survey_result_question_id in (1,23), osr.survey_result_answer_id, NULL)) "NVKD",
                    MAX(if(osr.survey_result_question_id in (2,22), osr.survey_result_answer_id, NULL)) "NVTK",
                    MAX(if(osr.survey_result_question_id in (4), osr.survey_result_answer_id, NULL)) "NVBT"'))
                        ->where('osr.survey_result_section_id', $partViolation->section_id)
                        ->groupBy('osr.survey_result_section_id')
                        ->get();
                    $partViolation->csat_salesman_point = ($sectionResult[0]->NVKD == -1 || $sectionResult[0]->NVKD == '-1') ? 0 : $sectionResult[0]->NVKD;
                    $partViolation->csat_deployer_point = ($sectionResult[0]->NVTK == -1 || $sectionResult[0]->NVTK == '-1') ? 0 : $sectionResult[0]->NVTK;
                    $partViolation->csat_maintenance_staff_point = ($sectionResult[0]->NVBT == -1 || $sectionResult[0]->NVBT == '-1') ? 0 : $sectionResult[0]->NVBT;
                    $partViolation->save();
                }
            }
            return 'Cap nhap thanh cong';
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function insertFromText()
    {
        $arrayExcel = [];
        $pathLocation = 'D:\test\listcus.txt';
        $fpD = @fopen($pathLocation, "r");
        // Kiểm tra file mở thành công không
        if (!$fpD) {
            var_dump('Mở file list không thành công');
            die;
        } else {
            $arrayHot = [
                '1' => '',
                '2' => '',
                '3' => '',
                '4' => '',
                '5' => '',
                '6' => '',
                '7' => ''
            ];
            while (!feof($fpD)) {
                $tempString = fgets($fpD);
                if ($tempString !== false) {
                    $tempExp = preg_split('/[\t]/', $tempString);
                    $temp['location_id'] = str_replace('V', '', trim($tempExp['0']));

                    if (empty(trim($tempExp['1']))) {
                        if (trim($arrayHot[$temp['location_id']]) != '') {
                            $arrayHot[$temp['location_id']] .= ';';
                        }
                        $arrayHot[$temp['location_id']] .= trim($tempExp['2']);
                    } else {
                        if ($temp['location_id'] == 1) {
                            $temp['branch_name'] = str_replace('HN', 'HNI', trim($tempExp['1']));;
                        } else {
                            $temp['branch_name'] = trim($tempExp['1']);
                        }
                        $temp['mail'] = trim($tempExp['2']);

                        array_push($arrayExcel, $temp);
                    }
                }
            }
        }
        fclose($fpD);

        $arrayMap = [];
        $modelBranch = new SummaryBranches();
        $result = $modelBranch->getAllBranch();
        foreach ($result as $val) {
            $arrayMap[$val->zone_id . ':' . $val->branch_code] = $val->branch_id;
        }

        $arrayWant = [];
        foreach ($arrayExcel as $val) {
            $temp = [];
            $temp['email_list'] = $val['mail'];
            if (!isset($arrayMap[$val['location_id'] . ':' . $val['branch_name']])) {
                dump($val['location_id'] . ':' . $val['branch_name']);
                die;
            }
            $temp['summary_branches_id'] = $arrayMap[$val['location_id'] . ':' . $val['branch_name']];
            if (isset($arrayWant[$val['location_id'] . ':' . $val['branch_name']])) {
                $arrayWant[$val['location_id'] . ':' . $val['branch_name']]['email_list'] .= ';' . $temp['email_list'];
            } else {
                $temp['email_list'] = $arrayHot[$val['location_id']] . ';' . $temp['email_list'];
                $arrayWant[$val['location_id'] . ':' . $val['branch_name']] = $temp;
            }
        }

        $modelList = new ListEmailCUS();
        $result = $modelList->insert($arrayWant);
        dump($result);
        die;
    }

    public function sendMailQGD()
    {
        $input = [
            'ObjID' => '1017327472'
        ];

        $http = 'http://parapiora.fpt.vn/api/ISMaintaince/GetOwnerTypeByPopManage';
        $extra = new ExtraFunction();
        $resCall = $extra->sendRequest($http, $extra->getHeader(), 'POST', $input);
        dump($resCall);

        die;
        $help = new ApiHelper();
        $param['sectionId'] = 3794531;
//        $param['num_type'] = 2;
//        $param['code'] = 1102781962;
//        $param['shd'] = 'DLD024029';

        $result = $help->checkSendMailCounter($param);
        dump($result);
        if ($result['status']) {
            $need = $help->sendMailCounter($param, $result);
        }
        die;
    }

    public function testUpdateInvalidCase()
    {
        $errorBrachCode = [];
        $errorUserName = [];
        $result = DB::table('list_invalid_survey_case')
            ->select('*')
            ->get();
        foreach ($result as $key => $value) {
            $arrayError = explode(',', $value->type_error);
            $caseToDelete = ListInvalidSurveyCase::find($value->id);
            if (in_array(1, $arrayError)) {
                $infoAcc = array('ObjID' => 0,
                    'Contract' => $value->contract_number,
                    'IDSupportlist' => $value->section_code,
                    'Type' => $value->survey_id
//                 'Contract' => 'BND029485',
//                'IDSupportlist' => '1114583322',
//                'Type' => 9
                );
//            DB::beginTransaction();
                /*
                 * Lấy thông tin khách hàng
                 */
                try {
                    $url = 'RPDeployment/spCEM_ObjectGetByObjID';
                    $result = json_decode($this->postAPI($infoAcc, $url), true);
                    $responseAccountInfo = $result['data'];
                    $testData = ['Supporter', 'SubSupporter', 'LocationID', 'BranchCode'];
                    $validData = true;
                    foreach ($testData as $key => $value2) {
                        if (!isset($responseAccountInfo[0][$value2]))
                            $validData = false;
                    }
                    //Có đủ dữ liệu trả về
                    if ($validData) {
                        $surveySection = SurveySections::find($value->section_id);
                        $surveySection->section_supporter = isset($responseAccountInfo[0]['Supporter']) ? $responseAccountInfo[0]['Supporter'] : null;
                        $surveySection->section_subsupporter = isset($responseAccountInfo[0]['SubSupporter']) ? $responseAccountInfo[0]['SubSupporter'] : null;
                        $surveySection->section_location_id = isset($responseAccountInfo[0]['LocationID']) ? $responseAccountInfo[0]['LocationID'] : null;
                        $surveySection->section_branch_code = isset($responseAccountInfo[0]['BranchCode']) ? $responseAccountInfo[0]['BranchCode'] : null;
                        if ($surveySection->save()) {
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
            if (in_array(2, $arrayError)) {
                array_push($errorBrachCode, $value->section_id);
            }
            if (in_array(3, $arrayError)) {
                array_push($errorUserName, $value->section_id);
            }
            $caseToDelete->delete();
        }
        if (!empty($errorBrachCode)) {
            Mail::send('emails.listInvalidCase', ['info' => $errorBrachCode, 'title' => 'Thông tin các case bị sai thông tin vùng miền, chi nhánh'], function ($message) {
                $message->from('rad.support@fpt.com.vn', 'Support');
                $message->to('huydp2@fpt.com.vn');
//                    $message->cc($cc);
                $message->subject('Thông tin các case bị sai thông tin vùng miền, chi nhánh');
            });
        }
        if (!empty($errorUserName)) {
            Mail::send('emails.listInvalidCase', ['info' => $errorUserName, 'title' => 'Thông tin các case bị thiếu thông tin người đăng nhập'], function ($message) {
                $message->from('rad.support@fpt.com.vn', 'Support');
                $message->to('huynl2@fpt.com.vn');
//                    $message->cc($cc);
                $message->subject('Thông tin các case bị thiếu thông tin người đăng nhập');
            });
        }
        echo 'Thanh cong';
    }

    private function postAPI($data, $url)
    {
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
//        if (FALSE === $result) {
//            throw new Exception(curl_error($ch), curl_errno($ch));
//            var_dump(curl_error($ch));
//            var_dump(curl_errno($ch));
//            die;
//            return curl_error($ch);
//        }
        // close the connection, release resources used
        curl_close($ch);
        return $result;

//          $resultCurlExt = Curl::to($uri)
//                ->withData($data)
//                ->returnResponseObject()
//                ->post();
//        if (isset($resultCurlExt->error))
//            return $resultCurlExt->error;
//        else
//            return $resultCurlExt->content;
    }

    public function getCsat12()
    {
        $nameFile = 'ChiTietBaoCaoXuLy';
//                $condition['survey_from']='2018-01-01 00:00:00';
//                  $condition['survey_to']='2018-01-31 23:59:59';
        $condition['sectionGeneralAction'] = 3;
        //Gán ngày nếu có
        if (isset($condition['survey_from'])) {
            $nameFile .= '_' . date('dmY', strtotime($condition['survey_from']));
        }

        if (isset($condition['survey_to'])) {
            $nameFile .= '_' . date('dmY', strtotime($condition['survey_to']));
        }
//                    var_dump($condition);die;
        $count = $infoSurvey = $this->CountGetCsatT1T3();
//                $count = $this->modelSurveySections->countListSurveyGeneral($condition);
//                var_dump($count);die;
        $currentPage = 0;
        $condition['recordPerPage'] = 3000; //ko cần phân trang
        $remain = $count % 3000;
        $numPage = ($count - $remain) / 3000;
        if ($remain != 0) {
            $numPage = $numPage + 1;
        }
        $listFileExel = [];
        for ($i = 0; $i < $numPage; $i++) {
            $nameExport = $nameFile;
            $nameExport .= strtotime(date('y-m-d h:i:s'));
            $infoSurvey = $this->getCsatT1T3($i, $condition);
//                      dump($infoSurvey);die;
//                    $infoSurvey = $this->modelSurveySections->searchListSurveyGeneral($condition, $i);
            $infoSurveyWithActionData = $this->attachActionDataToSurvey($infoSurvey, $condition);
            $PathExcel = Excel::create($nameExport, function ($excel) use ($infoSurveyWithActionData, $condition) {
                $excel->sheet('Sheet 1', function ($sheet) use ($infoSurveyWithActionData, $condition) {
                    $sheet->loadView('Csat.CsatServiceDetailExcel')->with('modelSurveySections', $infoSurveyWithActionData)
                        ->with('searchCondition', $condition);
                });
            })->store('xlsx', storage_path('app/public'), true);
            array_push($listFileExel, $PathExcel['file']);
        }
        return view("report/reportDownload", ['listFileExel' => $listFileExel])->render();
    }


    public function getCsatT1T3($numberPage, $condition)
    {
        $query = '(select oss.section_sub_parent_desc "Vung",
      oss.section_location as "ChiNhanh", 
      oss.section_survey_id,
      oss.section_code,
      oss.section_record_channel, 
      oss.section_contract_num,
      oss.section_acc_sale,
      oss.section_account_inf,
      oss.section_account_list,
      oss.section_time_completed,
      oss.section_location_id,
      oss.section_branch_code,
      oss.section_sale_branch_code,
      oss.section_region,
      oss.section_action,
      oss.section_connected,
      oss.section_user_name,
      oss.section_supporter,
      oss.violation_status,
       oss.section_note,
      MAX(if(osr.survey_result_question_id in (10,12,20,41,46) and osr.survey_result_answer_id <> -1 , osr.survey_result_answer_id, "")) "CSAT_Internet",
      MAX(if(osr.survey_result_question_id in (10,12,20,41,46) and osr.survey_result_answer_id in (1,2), osr.survey_result_answer_extra_id, ""))  "Loai_loi_internet",
      MAX(if(osr.survey_result_question_id in (10,12,20,41,46) and osr.survey_result_answer_id in (1,2), osr.survey_result_action, ""))  "Xu_ly_internet",
      MAX(if(osr.survey_result_question_id in (11,13,21,42,47) and osr.survey_result_answer_id <> -1, osr.survey_result_answer_id, "")) "CSAT_truyen_hinh",
      MAX(if(osr.survey_result_question_id in (11,13,21,42,47) and osr.survey_result_answer_id in (1,2), osr.survey_result_answer_extra_id, ""))  "Loai_loi_TV",
      MAX(if(osr.survey_result_question_id in (11,13,21,42,47) and osr.survey_result_answer_id in (1,2), osr.survey_result_action, ""))  "Xu_ly_TV"   
		from `outbound_survey_sections` as `oss` inner join `outbound_survey_result` as `osr` on `oss`.`section_id` = `osr`.`survey_result_section_id`
		 where `oss`.`section_time_completed_int` >= ' . strtotime('2018-03-01 00:00:00') . ' and `oss`.`section_time_completed_int` <= ' . strtotime('2018-03-28 23:59:59') .
            ' and `osr`.`survey_result_question_id` in (10, 11, 12, 13, 20, 21, 41, 42, 46, 47) and oss.section_survey_id = 1
                     and osr.survey_result_answer_id in (1,2)
		 group by `oss`.`section_id`) as t';
        $result = DB::table(DB::raw($query))
            ->select(DB::raw("*"));
        $result = $result->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage'])->get();
//$result = $result->get();
//dump($condition['recordPerPage'],$numberPage);
        foreach ($result as $key => $value) {
            $result[$key] = (array)$value;
        }
        return $result;

    }

    public function CountGetCsatT1T3()
    {
        $query = '(select oss.section_sub_parent_desc "Vung"
		from `outbound_survey_sections` as `oss` inner join `outbound_survey_result` as `osr` on `oss`.`section_id` = `osr`.`survey_result_section_id`
		 where `oss`.`section_time_completed_int` >= ' . strtotime('2018-03-01 00:00:00') . ' and `oss`.`section_time_completed_int` <= ' . strtotime('2018-03-28 23:59:59') .
            ' and `osr`.`survey_result_question_id` in (10, 11, 12, 13, 20, 21, 41, 42, 46, 47) and oss.section_survey_id = 1
                     and osr.survey_result_answer_id in (1,2)
		 group by `oss`.`section_id`) as t';
        $result = DB::table(DB::raw($query))
            ->select(DB::raw("*"));
        $result = $result->count();
        return $result;

    }

    public function attachActionDataToSurvey($result, $condition)
    {
        //Không có dữ liệu thì trả về luôn
        if (empty($result))
            return $result;
        //Chọn xử lý tạo checklist thường, indo, prechecklist
//        if ($condition['sectionGeneralAction'] == 3) {
//            DB::enableQueryLog();
        $preclResult = DB::table('prechecklist as pc')
            ->select(DB::raw("*")
            )
            ->where(function ($query) use ($result) {
                foreach ($result as $key => $value) {
                    $query->orWhere(function ($query) use ($value) {
                        $query->where('pc.section_contract_num', $value['section_contract_num']);
                        $query->where('pc.section_code', $value['section_code']);
                        $query->where('pc.section_survey_id', $value['section_survey_id']);
                    });
                }
            })
            ->get();
//                    $queryLog=DB::getQueryLog();
        //Chuyen sang mang key
        foreach ($preclResult as $key => $value) {
            $value = (array)$value;
            //Case dau tien
            if (!isset($keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']])) {
                $value['subkey'] = 0;
                $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']] = $value;
            } else {
//                $valueCheck=$preclResult[$value['section_contract_num'].$value['section_code'].$value['section_survey_id']];
//                if($valueCheck['subkey'])
                $subkey = $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'];
                $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'] = $subkey + 1;
//                $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id'] . 'plus' . ($subkey + 1)] = $value;
            }
        }
//            dump($preclResult);
//               dump($keyPreclResult);
//            die;
        $sectionSurveyPreCL = [];
//        Tong hop du lieu PreCl
        foreach ($result as $key1 => $survey) {
            $survey = (array)$survey;
            $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'];
            if (isset($keyPreclResult[$entry])) {
                $array_merge = array_merge($survey, $keyPreclResult[$entry]);
                array_push($sectionSurveyPreCL, $array_merge);
//                if($keyPreclResult[$entry]['subkey']
//                dump($keyPreclResult[$entry]['subkey']);die;
//                $numSubkey = $keyPreclResult[$entry]['subkey'];
//                for ($i = 1; $i <= $numSubkey; $i++) {
//                    $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'] . 'plus' . $i;
//                    $array_merge = array_merge($survey, $keyPreclResult[$entry]);
//                    array_push($sectionSurveyPreCL, $array_merge);
//                }
            } else {
                $preclArray = ['location_name' => '',
                    'first_status' => '',
                    'location_phone' => '',
                    'division_id' => '',
                    'description' => '',
                    'create_by' => '',
                    'update_date' => '',
                    'appointment_timer' => '',
                    'status' => '',
                    'sup_status_id' => '',
                    'created_at' => '',
                    'updated_at' => '',
                    'count_sup' => '',
                    'total_minute' => '',
                    'action_process' => '',
                    'id_prechecklist_isc' => '',
                    'sup_id_partner' => '',
                    'idChecklistIsc' => '',
                    'subkey' => 0
                ];
                $arrayEmptyPrecl = array_merge($survey, $preclArray);
                array_push($sectionSurveyPreCL, $arrayEmptyPrecl);
            }
        }
        //Gan them Checklist
        $SurveyPreclWithCl = [];
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
            'store_time' => '',
            'error_position' => '',
            'error_description' => '',
            'reason_description' => '',
            'way_solving' => '',
            'checklist_type' => '',
            'repeat_checklist' => '',
            'finish_date' => '',
        ];
        $listChecklistID = [];
        foreach ($sectionSurveyPreCL as $key => $value) {
            if ($value['sup_id_partner'] != NULL && $value['sup_id_partner'] != '' && $value['sup_id_partner'] > 0) {
                array_push($listChecklistID, $value['sup_id_partner']);
            }
            $sectionSurveyPreCL[$key] = (array)$value;
        }
//                dump($sectionSurveyPreCL);
//                dump($listChecklistID);die;
//            DB::enableQueryLog();
//            dump($listChecklistID);
        if (!empty($listChecklistID)) {
            $checklistResult = DB::table('checklist as c')
                ->select('*')
                ->where(function ($query) use ($listChecklistID) {
                    foreach ($listChecklistID as $key => $value) {
                        $query->orWhere(function ($query) use ($value) {
                            $query->where('c.id_checklist_isc', $value);
                        });
                    }
                })
                ->get();
        } else
            $checklistResult = [];

        foreach ($checklistResult as $key => $value) {
            $checklistResultKey[$value->id_checklist_isc] = $value;
        }
//                    dump($checklistResultKey);die;

        foreach ($sectionSurveyPreCL as $key => $value) {
            if ($value['sup_id_partner'] != NULL && $value['sup_id_partner'] != '' && $value['sup_id_partner'] > 0) {
                if (isset($checklistResultKey[$value['sup_id_partner']])) {
                    $checklistResult = (array)$checklistResultKey[$value['sup_id_partner']];
                    $array_merge = array_merge($value, $checklistResult);
                    array_push($SurveyPreclWithCl, $array_merge);
                } else {
                    $array_merge = array_merge($value, $checklistEmpty);
                    array_push($SurveyPreclWithCl, $array_merge);
                }
            } else {
                $array_merge = array_merge($value, $checklistEmpty);
                array_push($SurveyPreclWithCl, $array_merge);
            }
        }


        $result = $SurveyPreclWithCl;
//            dump($result);die;
//        }
//        }

        return $result;
    }

    public function getFileConverse()
    {
        $arrayExcel = [];
        $pathLocation = 'D:\vi_en.txt';
        $fpD = @fopen($pathLocation, "r");
        // Kiểm tra file mở thành công không
        if (!$fpD) {
            var_dump('Mở file DS không thành công');
            die;
        } else {
            while (!feof($fpD)) {
                $tempString = fgets($fpD);
                if ($tempString !== false) {
                    $key = '';
                    $tempExp = preg_split('/[\t]/', $tempString);
                    $splitString = explode(' ', $tempExp[1]);
                    if (count($splitString) > 1) {
                        foreach ($splitString as $index => $value) {
//                          $arrayText=str_split($value,2);
//                          dump($arrayText);
//                          dump($arrayText[0]);
//                          dump($key);

                            $key .= trim(ucfirst(str_replace('…', '', str_replace("'", '', str_replace('.', '', str_replace(',', '', $value))))));
//                          dump($key);
                        }
//                        dump($key);
//                        die;
                    } else
                        $key = trim($splitString[0]);
//                    echo '"'.$key .'" => '.$tempExp[0].',</br>';
                    echo '"' . $key . '" => "' . trim($tempExp[1]) . '",</br>';
                }
            }
        }
        fclose($fpD);

        die;
        echo "<table>";
        foreach ($arrayExcel as $key => $val) {
            echo "<tr>";
            echo "<td>" . $val['name'] . "</td>";
            echo "<td>" . $val['SLLLTK'] . "</td>";
            echo "<td>" . $val['SLKLLTK'] . "</td>";
            echo "<td>" . $val['SLLLBT'] . "</td>";
            echo "<td>" . $val['SLKLLBT'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function getVoiceData(Request $request)
    {
        $sectionID= $request->sectionID;
        $voiceRecord = new VoiceRecord();
        $hasExist = $voiceRecord->checkExistVoice($sectionID);
        //Có trong outbound_voice rồi
        if(!empty($hasExist))
        {
            //lấy ra decode, xài luôn
            $jsonDecodeVoiceRecords = json_decode($hasExist[0]->voice_records);
            return $jsonDecodeVoiceRecords->detail;
        }
        else
        {
            $surveySection = new SurveySections();
            $result = $surveySection->getSurveyInfoByID($sectionID);
            $timeStart = $result[0]->section_time_start;
            $timeCompleted = $result[0]->section_time_completed;
            $phone = $result[0]->section_contact_phone;
            //Lấy khoảng thời gian để khoanh vùng
            $fifteenMinuteBefore = date('Y-m-d H:i:s', strtotime('-15 minutes', strtotime($timeCompleted)));
            $fifteenMinuteAfter = date('Y-m-d H:i:s', strtotime('+15 minutes', strtotime($timeCompleted)));
            //Chuyển định dạng phone
            $phone = '00855'.substr($phone, 1);
            //Tim dữ liệu voiceDB
            $dataVoice = $voiceRecord->getVoiceRecord('sqlsrv_voice_cam_1', $fifteenMinuteBefore, $fifteenMinuteAfter, $phone);
            if(!empty($dataVoice))
            {
                $listVoiceRecord = [];
                $voiceRecord = [];
                foreach ($dataVoice as $voice)
                {
                    $idCharsetRecord = base64_encode('/vox/'.$voice->voiceId.'/'.$voice->Channel.'/'.$voice->RecordReference). '.wav';
                    $voiceRecord['StopRecordTime'] = $voice->StopRecordTime;
                    $voiceRecord['CalledID'] = $phone;
                    $voiceRecord['idCharsetRecord'] = $idCharsetRecord;
                    array_push($listVoiceRecord, $voiceRecord);
                }
                $viewRender = view('records/voiceCAMRecords', ['listVoiceRecord' => $listVoiceRecord ])->render();
                $voiceRecordRaw = ['state' => 'success', 'count' => count($listVoiceRecord), 'detail' => $viewRender];
                $voiceRecordRawJson = json_encode($voiceRecordRaw);
                $voiceRecord = new VoiceRecord();
                $voiceRecord->voice_survey_sections_id = $sectionID;
                $voiceRecord->voice_records = $voiceRecordRawJson;
                $voiceRecord->voice_section_time_start = $timeStart;
                $voiceRecord->save();
                return $viewRender;
            }
            else
            {
                $dataVoice2 = $voiceRecord->getVoiceRecord('sqlsrv_voice_cam_2', $fifteenMinuteBefore, $fifteenMinuteAfter, $phone);
                if(!empty($dataVoice2))
                {
                    $listVoiceRecord = [];
                    $voiceRecord = [];
                    foreach ($dataVoice2 as $voice)
                    {
                        $idCharsetRecord = base64_encode('/vox/'.$voice->voiceId.'/'.$voice->Channel.'/'.$voice->RecordReference). '.wav';
                        $voiceRecord['StopRecordTime'] = $voice->StopRecordTime;
                        $voiceRecord['CalledID'] = $phone;
                        $voiceRecord['idCharsetRecord'] = $idCharsetRecord;
                        array_push($listVoiceRecord, $voiceRecord);
                    }
                    $viewRender = view('records/voiceCAMRecords',  ['listVoiceRecord' => $listVoiceRecord ])->render();
                    $voiceRecordRaw = ['state' => 'success', 'count' => count($listVoiceRecord), 'detail' => $viewRender];
                    $voiceRecordRawJson = json_encode($voiceRecordRaw);
                    $voiceRecord = new VoiceRecord();
                    $voiceRecord->voice_survey_sections_id = $sectionID;
                    $voiceRecord->voice_records = $voiceRecordRawJson;
                    $voiceRecord->voice_section_time_start = $timeStart;
                    $voiceRecord->save();
                    return $viewRender;
                }
                else
                {
                    //Không tìm thấy dữ liệu voice
                    return    view('records/voiceCAMRecords', [])->render();
                }
            }

        }
    }

}
