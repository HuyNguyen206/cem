<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Models\SurveySections;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use App\Component\ExtraFunction;
use Illuminate\Support\Facades\Auth;
use App\Models\SurveyViolations;
use App\Models\SurveyReport;

class ReportViolationsCSAT12 extends Controller {

    var $modelSurveySections;
    var $columnView;
    var $columnNeedToHide;
    var $columnDefault;

    public function __construct() {
        $this->modelSurveySections = new SurveySections();

        $column = $this->showColumn();

        $this->columnDefault = $column['show'];
        $this->columnView =  $column['show'][0];
        $this->columnNeedToHide = $column['hide'];
    }

    public function index(Request $request) {
        $modelLocation = new Location();
        $listLocation = $modelLocation->getAllLocation();
        $recordPerPage = 50;
        $infoSurvey = $condition = null;
        $extraFunc = new ExtraFunction();
        $userGranted = $extraFunc->getUserGranted();

        $dataPage = [];
        if ($request->isMethod('post') || (isset($request->page) && Session::has('condition'))) {//click vào nút tìm
            if ($request->isMethod('post'))//xóa session nếu có
                Session::forget('condition');
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            } else {
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;

                //edit lại location để search hiệu quả hơn
                if (!empty($condition['location'])) {
                    foreach ($condition['location'] as $k => &$val) {
                        if (strpos($val, '_') !== false) {
                            $val = explode('_', $val);
                            $val = $val[0];
                        }
                    }
                    $condition['location'] = array_unique($condition['location']); //gộp tất cả các location giống nhau thành 1
                }
                Session::put('condition', $condition);
            }
            //nếu ktra thấy ko chọn vùng, chi nhánh thì gán lại các vùng, chi nhánh đã được phân cho user
            $condition['region'] = empty($condition['region']) ?(count($condition['region']) == MAX_REGION ?'' :$userGranted['region']) :$condition['region'];
            $condition['location'] = empty($condition['location']) ?(count($condition['location']) == MAX_BRANCHES ?'' :$userGranted['location']) :$condition['location'];
            $condition['branchcode'] = empty($condition['branchcode']) ?(count($condition['branchcode']) == MAX_BRANCHCODE ?'' :$userGranted['branchcode']) :$condition['branchcode'];

            $count = $this->modelSurveySections->countListSurveyViolations($condition);
            $currentPage = !empty($request->page) ? intval($request->page - 1) : 0;
//            dump($condition);die;
            $infoSurvey = $this->modelSurveySections->searchListSurveyViolations($condition, $currentPage);
            $infoSurvey = new LengthAwarePaginator($infoSurvey, $count, $recordPerPage, $request->page, [
                'path' => $request->url(),
                'query' => $request->query()
            ]);

            $dataPage = $this->repairDataForViewIndex($infoSurvey, $condition);
            //gán lại giá trị cho tìm kiếm
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            } else {
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;
            }
        }

        return view("report_violations_csat12/index", [
            'modelLocation' => $listLocation,
            'modelSurveySections' => $infoSurvey,
            'searchCondition' => $condition,
            'currentPage' => !empty($currentPage) ? $currentPage : 0,
            'userGranted' => !empty($userGranted) ? $userGranted : '',
            'dataPage' => $dataPage,
            'columnView' => $this->columnView,
        ]);
    }

    public function detail_survey(Request $request) {
        if ($request->ajax() && Session::token() === Input::get('_token')) {
            $data = Input::all();
            $contract = !empty($data['contract']) ? $data['contract'] : '';
            $phone = !empty($data['phone']) ? $data['phone'] : '';
            $modelDetailSurveyResult = new SurveySections();
            $detail = $modelDetailSurveyResult->getAllDetailSurveyInfo($data['survey']);
            echo view('report_history/detailSurvey', ['detail' => $detail, 'contract' => $contract, 'phone' => $phone])->render();
        }
        exit();
    }

    public function getTimeSurvey(Request $request) {
        if ($request->ajax() && Session::token() === Input::get('_token')) {
            $data = Input::all();
            $result = DB::table('survey_section_history')->select('section_connected', 'section_time_completed', 'section_user_name', 'section_user_modified')->where('section_id', $data['id'])->get();
            echo view('report_history/timeSurvey', ['timeHistory' => $result])->render();
        }
        exit();
    }

    public function detail_survey_frontend(Request $request) {
        $modelDetailSurveyResult = new SurveySections();
        $detail = $modelDetailSurveyResult->getAllDetailSurveyInfo($request->surveyID);
        $survey = SurveySections::find($request->surveyID);
        $connected = $survey->section_connected;
        $contactPhone = $survey->section_contact_phone;
        $mainNote = $survey->section_note;
        return view('report_history/detailSurveyFrontend', ['detail' => $detail, 'contract' => $request->contractNum, 'connected' => $connected, 'contactPhone' => $contactPhone, 'mainNote' => $mainNote])->render();
    }

    private function attachCondition($condition, $request) {
        $condition['survey_from'] = !empty($request->survey_from) ? date('Y-m-d 00:00:00', strtotime($request->survey_from)) : date('Y-m-d 00:00:00');
        $condition['survey_to'] = !empty($request->survey_to) ? date('Y-m-d 23:59:59', strtotime($request->survey_to)) : date('Y-m-d 23:59:59');

        $condition['survey_from_int'] = !empty($request->survey_from) ?  strtotime($request->survey_from) : strtotime( date('Y-m-d 00:00:00') ) ;
        $condition['survey_to_int'] = !empty($request->survey_to) ? strtotime($request->survey_to.'  23:59:59') : strtotime( date('Y-m-d 23:59:59') );
        $condition['region'] = $request->region; //intval($request->region);
        $condition['location'] = $request->location;
        $condition['staffType'] = $request->staffType;
        $condition['branchcode'] = [];
        //nếu chọn các chi nhánh con của HNI hoặc HCM
        if (!empty($condition['location'])) {
            foreach ($condition['location'] as $val) {
                if (strpos($val, '_') !== false) {
                    $branchcode = explode('_', $val);
                    array_push($condition['branchcode'], (int) $branchcode[1]);
                    array_push($condition['branchcode'], 0); //bổ sung branchcode 0 cho trường hợp chọn các chi nhánh khác ngoài HNI & HCM
                }
            }
        }
        $condition['branchcode'] = array_unique($condition['branchcode']);
        $condition['contractNum'] = !empty($request->contractNum) ? $request->contractNum : '';
        $condition['type'] = !empty($request->surveyType) ? $request->surveyType : '';
        $condition['section_action'] = !empty($request->processingSurvey) ? $request->processingSurvey : '';
        $condition['section_connected'] = !empty($request->surveyStatus) ? $request->surveyStatus : '';
        $condition['CSATPointSale'] = !empty($request->CSATPointSale) ? $request->CSATPointSale : '';
        $condition['CSATPointNVTK'] = !empty($request->CSATPointNVTK) ? $request->CSATPointNVTK : '';
        $condition['CSATPointBT'] = !empty($request->CSATPointBT) ? $request->CSATPointBT : '';
        $condition['CSATPointNet'] = !empty($request->CSATPointNet) ? $request->CSATPointNet : '';
        $condition['CSATPointTV'] = !empty($request->CSATPointTV) ? $request->CSATPointTV : '';
        $condition['userSurvey'] = !empty($request->user_survey) ? $request->user_survey : '';
        $condition['RateNPS'] = !empty($request->RateNPS) ? $request->RateNPS : '';
        $condition['NPSPoint'] = !empty($request->NPSPoint) ? $request->NPSPoint : '';
        $condition['departmentType'] = !empty($request->departmentType) ? $request->departmentType : '';
        $condition['salerName'] = !empty($request->salerName) ? $request->salerName : '';
        $condition['technicalStaff'] = !empty($request->technicalStaff) ? $request->technicalStaff : '';
        $condition['reportedStatus'] = !empty($request->reportedStatus) ? $request->reportedStatus : '';
        $condition['NetErrorType'] = !empty($request->NetErrorType) ? $request->NetErrorType : '';
        $condition['TVErrorType'] = !empty($request->TVErrorType) ? $request->TVErrorType : '';
        $condition['processingActionsTV'] = !empty($request->processingActionsTV) ? $request->processingActionsTV : '';
        $condition['processingActionsInternet'] = !empty($request->processingActionsInternet) ? $request->processingActionsInternet : '';

        return $condition;
    }

    public function exportViolations(Request $request) {
        if ($request->isMethod('post') && Session::token() === Input::get('_token')) {
            $condition = '';
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            }

            $currentPage = 0;
            $condition['recordPerPage'] = 0; //ko cần phân trang

            //tạo cache
            $infoSurvey = $this->modelSurveySections->searchListSurveyViolations($condition, $currentPage);

            $dataPage = $this->repairDataForViewHistoryExcel($infoSurvey, $condition);
            //export ra file excel
            Excel::create('BCKS CSAT12' . date('dmY', strtotime($condition['survey_from'])) . '_' . date('dmY', strtotime($condition['survey_to'])), function($excel) use($dataPage) {
                $excel->sheet('Sheet 1', function($sheet) use($dataPage) {
                    $sheet->loadView('export_excel.report_violations_csat12')->with('dataPage', $dataPage)
                        ->with('columnView', $this->columnView);
                });
            })->export('xlsx');
        }
        exit();
    }

    public function getViolations(Request $request) {
        if ($request->isMethod('post') && Session::token() === Input::get('_token')) {
            $detail = SurveyViolations::where('survey_violations.section_id', $request->id)
                ->where("type_report", $request->type)
                ->join('survey_section_report AS r', 'survey_violations.section_id', '=', 'r.section_id')
                ->select('survey_violations.*', 'r.*', 'survey_violations.section_supporter AS section_supporter', 'survey_violations.insert_at', 'survey_violations.updated_at')
                ->first();
            $flagCount = $flagModify = 0;
            if (!empty($detail) && $detail->modify_count > 2) {
                $flagCount = 1;
            }
            if (!empty($detail) && (strtotime($detail->updated_at) + (7 * 24 * 3600) <= strtotime(date("Y-m-d H:i:s")))) {
                $flagModify = 1;
            }
            echo view('report_history/detailViolation', ['id' => $request->id, 'type' => $request->type, 'status' => $request->status, 'detail' => $detail, 'flagCount' => $flagCount, 'flagModify' => $flagModify])->render();
        }
        exit();
    }

    public function saveViolations(Request $request) {
        if ($request->isMethod('post') && Session::token() === Input::get('_token')) {
            $modelViolation = new SurveyViolations();
            $date = date('Y-m-d H:i:s');
            foreach ($request->all()['data'] as $val) {
                $data[$val['name']] = $val['value'];
            }
            try {
                DB::beginTransaction();
                //update survey report
                $sectionReport = SurveyReport::find($data['sID']);
                if (!empty($sectionReport)) {//tìm thấy trong db
                    $status = !empty($sectionReport->violation_status) ? json_decode($sectionReport->violation_status, 1) : ['sales' => NULL, 'deployer' => NULL, 'maintenance' => NULL];
                    if ($data['type'] == 1) {//sales
                        $status[$this->sales] = 2; //cập nhật tình trạng báo cáo xử lý CSAT
                    } else if ($data['type'] == 2) {//deployer
                        $status[$this->deployer] = 2;
                    } else {//maintenance
                        $status[$this->maintenance] = 2;
                    }
                    $sectionReport->violation_status = json_encode($status); //cập nhật lại tình trạng báo cáo xử lý CSAT
                }
                $user = Auth::user();
                //$isFTQ = $user->hasRole('FTC');
                $isFTQ = ExtraFunction::checkHaveAuthenRole('FTC');
                $param['csat_salesman_point'] = (!empty($sectionReport) && !is_null($sectionReport->csat_salesman_point)) ? $sectionReport->csat_salesman_point : NULL;
                $param['csat_deployer_point'] = (!empty($sectionReport) && !is_null($sectionReport->csat_deployer_point)) ? $sectionReport->csat_deployer_point : NULL;
                $param['csat_maintenance_staff_point'] = (!empty($sectionReport) && !is_null($sectionReport->csat_maintenance_staff_point)) ? $sectionReport->csat_maintenance_staff_point : NULL;
                $param['section_id'] = !empty($data['sID']) ? $data['sID'] : '';
                $param['qs_verify'] = !empty($data['verify']) ? $data['verify'] : '';
                $param['section_supporter'] = !empty($data['supporter']) ? $data['supporter'] : '';
                $param['violations_type'] = !empty($data['optType']) ? $data['optType'] : '';
                $param['punishment'] = !empty($data['optPunish']) ? $data['optPunish'] : '';
                $param['additional_discipline'] = !empty($data['discipline']) ? $data['discipline'] : '';
                $param['remedy'] = !empty($data['remedy']) ? $data['remedy'] : '';
                $param['explanation_desc'] = !empty($data['explanation']) ? $data['explanation'] : '';
                $param['description'] = !empty($data['description']) ? $data['description'] : '';
                $param['punishment_desc'] = !empty($data['punishment_desc']) ? $data['punishment_desc'] : '';
                $param['discipline_ftq'] = (!empty($data['discipline_ftq']) && $isFTQ == TRUE) ? $data['discipline_ftq'] : '';
                $param['punishment_additional'] = (!empty($data['optPunishAdditional']) && $isFTQ == TRUE) ? $data['optPunishAdditional'] : '';
                $param['type_report'] = !empty($data['type']) ? $data['type'] : '';
                // lỗi không thuộc nhân viên và người dùng có quyền FTQ
                if( $param['violations_type'] == 12 && $isFTQ ) {
                    $param['accept_staff_dont_has_mistake'] = 'no';
                    if (isset($data['has_mistake']) && $data['has_mistake'] == 'yes') {
                        $param['accept_staff_dont_has_mistake'] = 'yes';
                    }
                }
                //trường hợp chưa báo cáo
                $violation = SurveyViolations::join('survey_section_report AS r', 'survey_violations.section_id', '=', 'r.section_id')
                    ->where('r.section_id', $data['sID'])
                    ->where("type_report", $data['type'])->first();
                if ($data['status'] == 1 && empty($violation)) {
                    $param['insert_at'] = $date;
                    $param['created_user'] = Auth::user()->name;
                    $param['updated_at'] = $date;
                    $res = $modelViolation->insertViolation($param);
                } else { //chỉnh sửa báo cáo
                    foreach ($param as $k => $val) {
                        $violation->$k = $val;
                    }
                    $name = $user->name;
                    $violation->modify_user = $name;
                    if ($isFTQ == TRUE) {
                        $violation->ftq_acc_modify = $name;
                        $violation->ftq_modify_count = $violation->ftq_modify_count + 1;
                    } else {
                        $violation->modify_count = $violation->modify_count + 1;
                    }
                    $violation->save();
                }
                $sectionReport->save();
                $resStatus = trans('history.Reported');
                DB::commit();
                echo json_encode(['resStatus' => $resStatus, 'id' => $data['sID'], 'object' => ($data['type'] == 1 ? $this->sales : ($data['type'] == 2 ? $this->deployer : $this->maintenance))]);
            } catch (Exception $ex) {
                DB:rollback();
            }
        }
        exit();
    }

    public function repairDataForViewIndex($infoSurvey, $condition){
        $columnView = $this->columnDefault[$condition['staffType']];
        $this->columnView = $columnView;

        $data = [];
        $arrayNotAllowNull = ['salename', 'csat_salesman_point'];
        $arrayNormal = [
            'section_sub_parent_desc', 'section_location', 'section_contract_num','section_note', 'section_time_completed', 'created_user', 'insert_at',
            'explanation_desc', 'qs_verify', 'punishment_desc', 'remedy', 'description', 'punishment_additional', 'discipline_ftq'
        ];
        $emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
        $surveyTitle = [1 => 'Sau triển khai DLS', 2 => 'Sau bảo trì', 5 => 'HiFPT', 6 => 'Sau triển khai TLS'];
        $violationTitle = [
            "1" => "Sai hẹn với khách hàng",
            "2"=> "Thái độ với khách hàng không tốt",
            "3" =>"Không thực hiện các yêu cầu phát sinh của khách hàng",
            "4" =>"Không hướng dẫn khách hàng",
            "5"=>"Làm bừa, bẩn nhà khách hàng",
            "6" => "Nghiệp vụ kỹ thuật",
            "7" => "Tiến độ xử lý chậm",
            "8" => "Vòi vĩnh khách hàng",
            "9" => "Tư vấn không rõ ràng, đầy đủ",
            "10" => "Tư vấn sai",
            "11" => "Khác",
            "12" => "Lỗi không thuộc về nhân viên",
        ];
        $punishTitle = [
            '1' => 'Phạt tiền',
            '2' => 'Cảnh cáo/Nhắc nhở',
            '3' => 'Buộc thôi việc',
            '4' => 'Không chế tài',
            '5' => 'Khác',
        ];

        foreach($infoSurvey as $index => $surveySections){
            $viStatus = json_decode($surveySections->violation_status, 1); //tình trạng báo cáo xử lý CSAT

            $dataRow = [];
            foreach($columnView as $key => $val){
                if(array_search($key, $arrayNotAllowNull) !== false){
                    $dataRow[$key] = (empty($surveySections->$key))? '' : $surveySections->$key;
                    if($key != 'salename' && $dataRow[$key] != '') {
                        $dataRow[$key] = "<span><strong><img src='" . asset("assets/img/".$emotions[$dataRow[$key]]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow[$key];
                    }
                }else{
                    if(array_search($key, $arrayNormal) !== false){
                        $dataRow[$key] = $surveySections->$key;
                    }else{
                        switch($key){
                            case 'violation_status':
                                if($dataRow['csat_salesman_point'] == '' || $dataRow['csat_salesman_point'] >= 3){
                                    $dataRow[$key] = 'Không cần báo cáo';
                                }elseif ($viStatus['sales'] == 2){
                                    $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(2, ".$surveySections->section_id.", 1)' role='button' data-toggle='modal'><span id='sales".$surveySections->section_id."'>Đã báo cáo</span></a>";
                                }else{
                                    $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(1, ".$surveySections->section_id.", 1)' role='button' data-toggle='modal'><span id='sales".$surveySections->section_id."'>Chưa báo cáo</span></a>";
                                }
                                break;
                            case 'section_supporter':
                                $dataRow[$key] = (!empty($surveySections->section_supporter) ?$surveySections->section_supporter :''). (!empty($surveySections->section_subsupporter) ?' '.$surveySections->section_subsupporter :'');
                                break;
                            case 'csat_deployer_point':
                                if(!empty($surveySections->$key)){
                                    $dataRow[$key] = $surveySections->$key;
                                }elseif(!empty($surveySections->csat_maintenance_staff_point)){
                                    $dataRow[$key] = $surveySections->csat_maintenance_staff_point;
                                }else{
                                    $dataRow[$key] = '';
                                }
                                if(!empty($dataRow[$key])) {
                                    $dataRow[$key] = "<span><strong><img src='" . asset("assets/img/".$emotions[$dataRow[$key]]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow[$key];
                                }
                                break;
                            case 'csat_tv_point':
                                if(!empty($surveySections->$key)){
                                    $dataRow[$key] = $surveySections->$key;
                                }elseif(!empty($surveySections->csat_maintenance_tv_point)){
                                    $dataRow[$key] = $surveySections->csat_maintenance_tv_point;
                                }else{
                                    $dataRow[$key] = '';
                                }
                                if(!empty($dataRow[$key])) {
                                    $dataRow[$key] = "<span><strong><img src='" . asset("assets/img/".$emotions[$dataRow[$key]]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow[$key];
                                }
                                break;
                            case 'csat_net_point':
                                if(!empty($surveySections->$key)){
                                    $dataRow[$key] = $surveySections->$key;
                                }elseif(!empty($surveySections->csat_maintenance_net_point)){
                                    $dataRow[$key] = $surveySections->csat_maintenance_net_point;
                                }else{
                                    $dataRow[$key] = '';
                                }
                                if(!empty($dataRow[$key])) {
                                    $dataRow[$key] = "<span><strong><img src='" . asset("assets/img/".$emotions[$dataRow[$key]]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow[$key];
                                }
                                break;
                            case 'channel_receive':
                                $dataRow[$key] = 'CS/HappyCall';
                                break;
                            case 'section_survey_id':
                                $dataRow[$key] = '<span class="'.$surveySections->section_survey_id.'">'. !empty($surveyTitle[$surveySections->section_survey_id]) ?$surveyTitle[$surveySections->section_survey_id] :''. '</span>';
                                break;
                            case 'violations_type':
                                $dataRow[$key] = $violationTitle[$surveySections->$key];
                                break;
                            case 'punishment':
                                $dataRow[$key] = $punishTitle[$surveySections->$key];
                                break;
                            case 'punishment_additional':
                                $dataRow[$key] = $punishTitle[$surveySections->$key];
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
            array_push($data, $dataRow);
        }
        return $data;
    }

    public function repairDataForViewHistoryExcel($infoSurvey, $condition){
        $columnView = $this->columnDefault[$condition['staffType']];
        $this->columnView = $columnView;

        $data = [];
        $arrayNotAllowNull = ['salename', 'csat_salesman_point'];
        $arrayNormal = [
            'section_sub_parent_desc', 'section_location', 'section_contract_num','section_note', 'section_time_completed', 'created_user', 'insert_at',
            'explanation_desc', 'qs_verify', 'punishment_desc', 'remedy', 'description', 'punishment_additional', 'discipline_ftq'
        ];
        $surveyTitle = [1 => 'Sau triển khai DLS', 2 => 'Sau bảo trì', 5 => 'HiFPT', 6 => 'Sau triển khai TLS'];
        $violationTitle = [
            "1" => "Sai hẹn với khách hàng",
            "2"=> "Thái độ với khách hàng không tốt",
            "3" =>"Không thực hiện các yêu cầu phát sinh của khách hàng",
            "4" =>"Không hướng dẫn khách hàng",
            "5"=>"Làm bừa, bẩn nhà khách hàng",
            "6" => "Nghiệp vụ kỹ thuật",
            "7" => "Tiến độ xử lý chậm",
            "8" => "Vòi vĩnh khách hàng",
            "9" => "Tư vấn không rõ ràng, đầy đủ",
            "10" => "Tư vấn sai",
            "11" => "Khác",
            "12" => "Lỗi không thuộc về nhân viên",
        ];
        $punishTitle = [
            '1' => 'Phạt tiền',
            '2' => 'Cảnh cáo/Nhắc nhở',
            '3' => 'Buộc thôi việc',
            '4' => 'Không chế tài',
            '5' => 'Khác',
        ];

        foreach($infoSurvey as $index => $surveySections){
            $viStatus = json_decode($surveySections->violation_status, 1); //tình trạng báo cáo xử lý CSAT

            $dataRow = [];
            foreach($columnView as $key => $val){
                if(array_search($key, $arrayNotAllowNull) !== false){
                    $dataRow[$key] = (empty($surveySections->$key))? '' : $surveySections->$key;
                }else{
                    if(array_search($key, $arrayNormal) !== false){
                        $dataRow[$key] = $surveySections->$key;
                    }else{
                        switch($key){
                            case 'violation_status':
                                if($dataRow['csat_salesman_point'] == '' || $dataRow['csat_salesman_point'] >= 3){
                                    $dataRow[$key] = 'Không cần báo cáo';
                                }elseif ($viStatus['sales'] == 2){
                                    $dataRow[$key] = "Đã báo cáo";
                                }else{
                                    $dataRow[$key] = "Chưa báo cáo";
                                }
                                break;
                            case 'section_supporter':
                                $dataRow[$key] = (!empty($surveySections->section_supporter) ?$surveySections->section_supporter :''). (!empty($surveySections->section_subsupporter) ?' '.$surveySections->section_subsupporter :'');
                                break;
                            case 'csat_deployer_point':
                                if(!empty($surveySections->$key)){
                                    $dataRow[$key] = $surveySections->$key;
                                }elseif(!empty($surveySections->csat_maintenance_staff_point)){
                                    $dataRow[$key] = $surveySections->csat_maintenance_staff_point;
                                }else{
                                    $dataRow[$key] = '';
                                }
                                break;
                            case 'csat_tv_point':
                                if(!empty($surveySections->$key)){
                                    $dataRow[$key] = $surveySections->$key;
                                }elseif(!empty($surveySections->csat_maintenance_tv_point)){
                                    $dataRow[$key] = $surveySections->csat_maintenance_tv_point;
                                }else{
                                    $dataRow[$key] = '';
                                }
                                break;
                            case 'csat_net_point':
                                if(!empty($surveySections->$key)){
                                    $dataRow[$key] = $surveySections->$key;
                                }elseif(!empty($surveySections->csat_maintenance_net_point)){
                                    $dataRow[$key] = $surveySections->csat_maintenance_net_point;
                                }else{
                                    $dataRow[$key] = '';
                                }
                                break;
                            case 'channel_receive':
                                $dataRow[$key] = 'CS/HappyCall';
                                break;
                            case 'section_survey_id':
                                $dataRow[$key] = !empty($surveyTitle[$surveySections->section_survey_id]) ?$surveyTitle[$surveySections->section_survey_id] :'';
                                break;
                            case 'violations_type':
                                $dataRow[$key] = $violationTitle[$surveySections->$key];
                                break;
                            case 'punishment':
                                $dataRow[$key] = $punishTitle[$surveySections->$key];
                                break;
                            case 'punishment_additional':
                                $dataRow[$key] = $punishTitle[$surveySections->$key];
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
            array_push($data, $dataRow);
        }
        return $data;
    }

    private function showColumn(){
        $result['show'][0] = [
            'section_sub_parent_desc' => 'Vùng',
            'section_location' => 'Chi nhánh',
            'section_survey_id' => 'Loại khảo sát',
            'channel_receive' => 'Kênh ghi nhận',
            'section_contract_num' => 'Số HĐ',
            'salename' => 'Nhân viên Kinh doanh',
            'csat_salesman_point' => 'CSAT',
            'section_note' => 'Ghi chú của NVCS',
            'csat_deployer_point' => 'CSAT NVKT',
            'csat_net_point' => 'CSAT Internet',
            'csat_tv_point' => 'CSAT TH',
            'section_time_completed' => 'Thời gian ghi nhận',
            'violation_status' => 'Báo cáo Xử lý',
            'created_user' => 'Người báo cáo',
            'insert_at' => 'Thời gian BC',
            'explanation_desc' => 'Giải trình của cá nhân vi phạm',
            'qs_verify' => 'Quản lý kiểm chứng',
            'violations_type' => 'Loại lỗi',
            'punishment' => 'Loại chế tài bổ sung',
            'punishment_desc' => 'Diễn giải chế tài',
            'remedy' => 'Hành động khắc phục với KH',
            'description' => 'Mô tả chi tiết',
            'punishment_additional' => 'FTQ Điều chỉnh/ Chế tài bổ sung',
            'discipline_ftq' => 'Diễn giải',
        ];

        $result['show'][1] = [
            'section_sub_parent_desc' => 'Vùng',
            'section_location' => 'Chi nhánh',
            'section_survey_id' => 'Loại khảo sát',
            'channel_receive' => 'Kênh ghi nhận',
            'section_contract_num' => 'Số HĐ',
            'section_supporter' => 'Nhân viên Kỹ thuật TK/BT',
            'csat_deployer_point' => 'CSAT',
            'section_note' => 'Ghi chú của NVCS',
            'csat_salesman_point' => 'CSAT NVKD',
            'csat_net_point' => 'CSAT Internet',
            'csat_tv_point' => 'CSAT TH',
            'section_time_completed' => 'Thời gian ghi nhận',
            'violation_status' => 'Báo cáo Xử lý',
            'created_user' => 'Người báo cáo',
            'insert_at' => 'Thời gian BC',
            'explanation_desc' => 'Giải trình của cá nhân vi phạm',
            'qs_verify' => 'Quản lý kiểm chứng',
            'violations_type' => 'Loại lỗi',
            'punishment' => 'Loại chế tài bổ sung',
            'punishment_desc' => 'Diễn giải chế tài',
            'remedy' => 'Hành động khắc phục với KH',
            'description' => 'Mô tả chi tiết',
            'punishment_additional' => 'FTQ Điều chỉnh/ Chế tài bổ sung',
            'discipline_ftq' => 'Diễn giải',
        ];

        $result['hide'] = [];

        return $result;
    }
}
