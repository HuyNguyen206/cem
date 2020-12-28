<?php

namespace App\Http\Controllers\Complains;

use App\Models\RecordChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Models\SurveySections;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use App\Component\ExtraFunction;
use Illuminate\Support\Facades\Auth;

use App\Models\OutboundQuestions;
use App\Models\SurveyResult;
use App\Models\OutboundAnswers;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class ComplainController extends Controller {

    var $sales;
    var $deployer;
    var $maintenance;
    var $teleSales;
    var $modelSurveySections;
//    var $selNPSImprovement;
//    var $selErrorType;
//    var $selProcessingActions;
    var $columnView;
    var $columnNeedToHide;
    var $columnDefault;
    var $modelRecordChannel;

    public function __construct() {
        $this->sales = 'sales';
        $this->deployer = 'deployer';
        $this->maintenance = 'maintenance';
        $this->teleSales = 'teleSales';
        $this->modelSurveySections = new SurveySections();
        $this->modelRecordChannel = new RecordChannel();

//        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
//        $this->selErrorType = $this->modelSurveySections->getErrorType([20, 22]);
//        $this->selProcessingActions = $this->modelSurveySections->getProcessingActions([21]);
        $this->columnView = $this->columnDefault = [
            'section_contract_num' => 'Số HĐ',
            "object_complain" => "Đối tượng bị khiếu nại",
            "note_complain" => "Nội dung khiếu nại",
            'section_sub_parent_desc' => 'Vùng',
            'section_branch_code' => 'Chi nhánh',
            'section_time_completed' => 'Thời gian ghi nhận',
        ];

        $this->columnNeedToHide = [
            "section_id",
            "section_subsupporter",
            "section_supporter",
            "salename",
            "section_survey_id",
            "section_connected",
            "section_action",
            "section_user_name",
            "section_location",
            "section_note",
            "section_time_start",
            "section_code",
            "section_contact_phone",
            "section_sale_branch_code",
            "section_count_connected",
            "violation_status",
        ];
    }

    public function anyIndex(Request $request) {
        $modelLocation = new Location();
        $listLocation = $modelLocation->getAllLocation();
        $recordPerPage = 50;
        $infoSurvey = $condition = null;
        $extraFunc = new ExtraFunction();
        $userGranted = $extraFunc->getUserGrantedDetail();
        $dataPage = [];
        if ($request->isMethod('post') || (isset($request->page) && Session::has('condition'))) {//click vào nút tìm
            if ($request->isMethod('post'))//xóa session nếu có 
                Session::forget('condition');
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            } else {
                $justOnlyLocation = false;
                $condition = $this->attachCondition($condition, $request);
                if (!empty($condition['location']) && empty($condition['branchcode']) && empty($condition['branchcodeSales'])){
                    $justOnlyLocation = true;
                }
                $condition['recordPerPage'] = $recordPerPage;
                $condition['justOnlyLocation'] = $justOnlyLocation;
                //nếu tìm kiếm theo HĐ thì bỏ hết các đk tìm kiếm khác, trừ đk triển khai hoặc bảo trì.
                if (!empty($condition['contractNum'])) {
                    $arrayKeep = ['contractNum', 'type','departmentType', 'recordPerPage', 'userSurvey', 'allQuestion'];
                    foreach($condition as $key => $val){
                        if(!in_array($key, $arrayKeep)){
                            $condition[$key] = '';
                        }
                    }
                }
                //edit lại location để search hiệu quả hơn
                if (!empty($condition['location'])) {
                    foreach ($condition['location'] as $k => $val) {
                        if (strpos($val, '_') !== false) {
                            $val = explode('_', $val);
                            $condition['location'][$k] = $val[0];
 
                        }
                    }
                    $condition['location'] = array_unique($condition['location']); //gộp tất cả các location giống nhau thành 1
                }
                Session::put('condition', $condition);
            }
            //nếu ktra thấy ko chọn vùng, chi nhánh thì gán lại các vùng, chi nhánh đã được phân cho user
            $condition['region'] = empty($condition['region']) ? (count($condition['region']) == MAX_REGION ? '' : $userGranted['region']) : $condition['region'];
            $condition['location'] = empty($condition['location']) ? (count($condition['location']) == MAX_BRANCHES ? '' : $userGranted['location']) : $condition['location'];
            if (empty($condition['branchcode'])) {
                if (count($condition['branchcode']) == MAX_BRANCHCODE){
                    $condition['branchcode'] = '';
                } else {
                    $condition['branchcode'] = $userGranted['branchcode'];
                     //Bổ sung thêm id các chi nhánh để fifter được dữ liệu
                    array_push($condition['branchcode'], 0);
                    array_push($condition['branchcode'], 90);
                }
            } else{
                $condition['branchcode'] = $condition['branchcode'];
            }
           
            $condition['branchcode'] = array_unique($condition['branchcode']);

            if (($key = array_search('4', $condition['location'])) !== false) {
                unset($condition['location'][$key]);
            }
            if (($key = array_search('8', $condition['location'])) !== false) {
                unset($condition['location'][$key]);
            }
            $currentPage = !empty($request->page) ? intval($request->page - 1) : 0;
            $count = $this->modelSurveySections->countListSurvey($condition);
            $infoSurvey = $this->modelSurveySections->searchListSurvey($condition, $currentPage);
            $param['arrayID'] = [];
            $infoSurveyKey = [];
            foreach($infoSurvey as $val){
                $param['arrayID'][] = $val->section_id;
                $infoSurveyKey[$val->section_id] = $val;
            }
            $surveyResultModel = new SurveyResult();
            $surveyResults = $surveyResultModel->getSurveyByParam($param);
            $infoSurvey = $this->convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults);
            $infoSurvey = new LengthAwarePaginator($infoSurvey, $count, $recordPerPage, $request->page, [
                'path' => $request->url(),
                'query' => $request->query()
            ]);

            $dataPage = $infoSurvey;
            //gán lại giá trị cho tìm kiếm
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            } else {
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;
            }
        }
        $roleUser = DB::table('role_user')->select('role_id')->where('user_id', Auth::user()->id)->get();
        $listRecordChannels = $this->modelRecordChannel->getAllRecordChannel();

        return view("Complain/index", [
            'modelLocation' => $listLocation,
            'modelSurveySections' => $infoSurvey,
            'searchCondition' => $condition,
            'currentPage' => !empty($currentPage) ? $currentPage : 0,
            'user' => Auth::user(),
            'roleUser' => $roleUser,
            'userGranted' => !empty($userGranted) ? $userGranted : '',
            'dataPage' => $dataPage,
            'listRecordChannels' => $listRecordChannels,
        ]);
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
        $condition['surveyFrom'] = !empty($request->surveyFrom) ? date('Y-m-d 00:00:00', strtotime($request->surveyFrom)) : date('Y-m-d 00:00:00');
        $condition['surveyTo'] = !empty($request->surveyTo) ? date('Y-m-d 23:59:59', strtotime($request->surveyTo)) : date('Y-m-d 23:59:59');

        $condition['surveyFromInt'] = !empty($request->surveyFrom) ? strtotime($request->surveyFrom) : strtotime(date('Y-m-d 00:00:00'));
        $condition['surveyToInt'] = !empty($request->surveyTo) ? strtotime($request->surveyTo . '  23:59:59') : strtotime(date('Y-m-d 23:59:59'));
        $condition['region'] = $request->region; //intval($request->region);
        $condition['location'] = $request->location;
//        $condition['locationSales'] = $request->locationSales;
        $condition['branchcode'] = [];
        $condition['branchcodeSalesMan'] = [];
        //nếu chọn các chi nhánh con của HNI hoặc HCM
        if (!empty($condition['location'])) {
            foreach ($condition['location'] as $val) {
                if (strpos($val, '_') !== false) {
                    $branchcode = explode('_', $val);
                    array_push($condition['branchcode'], (int) $branchcode[1]);
//                    array_push($condition['branchcode'], 0); //bổ sung branchcode 0 cho trường hợp chọn các chi nhánh khác ngoài HNI & HCM
                }
            }
        }

        $condition['branchcode'] = array_unique($condition['branchcode']);
        $condition['brandcodeSaleMan'] = isset($request->brandcodeSaleMan) ? array_unique($request->brandcodeSaleMan) : '';
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
        $condition['allQuestion'] = $questionNeed;
        return $condition;
    }
    private function convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults){
        $modelOAns = new OutboundAnswers();
        $oAns = $modelOAns->getAnswerByGroup([1,2]);
        $oAns = json_decode(json_encode($oAns),1);
        $ansPoints = array_column($oAns, 'answers_point', 'answer_id');
        $ansPoints[-1] = null;
        //set field mặc định
        foreach($infoSurveyKey as $key => $info){
            $temp = $info;
            $temp->object_complain = '';
            $temp->note_complain = '';
            $temp->section_sub_parent_desc = str_replace('Vung', 'Vùng',  $temp->section_sub_parent_desc);
            if($temp->section_branch_code != 0){
                $temp->section_location = str_replace(' - ', $temp->section_branch_code. '-',  $temp->section_location);
            }
            $infoSurveyKey[$key] = $temp;
        }

        foreach($surveyResults as $result){
            if(array_search($result->survey_result_question_id, array_merge($condition['allQuestion'][1], $condition['allQuestion'][2])) !== false){
                if(trim($infoSurveyKey[$result->survey_result_section_id]->object_complain) != ''){
                    $infoSurveyKey[$result->survey_result_section_id]->object_complain .= ', ';
                }
                $infoSurveyKey[$result->survey_result_section_id]->object_complain .= 'NV kinh doanh';

                if(trim($infoSurveyKey[$result->survey_result_section_id]->note_complain) != ''){
                    $infoSurveyKey[$result->survey_result_section_id]->note_complain .= ', ';
                }
                $infoSurveyKey[$result->survey_result_section_id]->note_complain .= $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][3]) !== false){
                if(trim($infoSurveyKey[$result->survey_result_section_id]->object_complain) != ''){
                    $infoSurveyKey[$result->survey_result_section_id]->object_complain .= ', ';
                }
                $infoSurveyKey[$result->survey_result_section_id]->object_complain .= 'NV triển khai';

                if(trim($infoSurveyKey[$result->survey_result_section_id]->note_complain) != ''){
                    $infoSurveyKey[$result->survey_result_section_id]->note_complain .= ', ';
                }
                $infoSurveyKey[$result->survey_result_section_id]->note_complain .= $result->survey_result_note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][4]) !== false){
                if(trim($infoSurveyKey[$result->survey_result_section_id]->object_complain) != ''){
                    $infoSurveyKey[$result->survey_result_section_id]->object_complain .= ', ';
                }
                $infoSurveyKey[$result->survey_result_section_id]->object_complain .= 'NV bảo trì';

                if(trim($infoSurveyKey[$result->survey_result_section_id]->note_complain) != ''){
                    $infoSurveyKey[$result->survey_result_section_id]->note_complain .= ', ';
                }
                $infoSurveyKey[$result->survey_result_section_id]->note_complain .= $result->survey_result_note;
            }
        }
        return $infoSurveyKey;
    }

    public function postExport(Request $request) {
        try{
            if ($request->isMethod('post') && Session::token() === Input::get('_token')) {
                $extraFunc = new ExtraFunction();
                $userGranted = $extraFunc->getUserGrantedDetail();
                $condition = '';
                if (Session::has('condition')) {
                    $condition = Session::get('condition');
                }
                $condition['recordPerPage'] = 3000; //ko cần phân trang
                //nếu ktra thấy ko chọn vùng, chi nhánh thì gán lại các vùng, chi nhánh đã được phân cho user
                //nếu ktra thấy ko chọn vùng, chi nhánh thì gán lại các vùng, chi nhánh đã được phân cho user
                $condition['region'] = empty($condition['region']) ? (count($condition['region']) == MAX_REGION ? '' : $userGranted['region']) : $condition['region'];
                $condition['location'] = empty($condition['location']) ? (count($condition['location']) == MAX_BRANCHES ? '' : $userGranted['location']) : $condition['location'];
                if (empty($condition['branchcode'])) {
                    if (count($condition['branchcode']) == MAX_BRANCHCODE)
                        $condition['branchcode'] = '';
                    else {
                        $condition['branchcode'] = $userGranted['branchcode'];
                        //Bổ sung thêm id các chi nhánh để fifter được dữ liệu
                        array_push($condition['branchcode'], 0);
                        array_push($condition['branchcode'], 90);
                    }
                } else
                    $condition['branchcode'] = $condition['branchcode'];

                $condition['branchcode'] = array_unique($condition['branchcode']);

                if (($key = array_search('4', $condition['location'])) !== false) {
                    unset($condition['location'][$key]);
                }
                if (($key = array_search('8', $condition['location'])) !== false) {
                    unset($condition['location'][$key]);
                }

                $user = Auth::user();

                $date = date('dmY', time());
                $dateFrom = date('dmY',strtotime($condition['survey_from']));
                $dateTo = date('dmY',strtotime($condition['survey_to']));
                $currentPage = 0;
                $infoSurvey = $this->modelSurveySections->searchListSurvey($condition, $currentPage);
                $param['arrayID'] = [];
                $infoSurveyKey = [];
                foreach($infoSurvey as $val){
                    $param['arrayID'][] = $val->section_id;
                    $infoSurveyKey[$val->section_id] = $val;
                }
                $surveyResultModel = new SurveyResult();
                $surveyResults = $surveyResultModel->getSurveyByParam($param);
                $infoSurvey = $this->convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults);
                $infoSurvey = $this->repairDataForViewHistoryExcel($infoSurvey, $condition);
                $dataPage = $infoSurvey;
                //chỉnh các thông số để xuất file excel
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment');
                header('Cache-Control: max-age=0');
                ini_set('memory_limit', '2048M');

                //export ra file excel
                $fileName ='ChiTietKhieuNai_' . date('dmY', strtotime($condition['survey_from'])) . '_' . date('dmY', strtotime($condition['survey_to']));
                Excel::create($fileName, function($excel) use($dataPage) {
                    $excel->sheet('Sheet 1', function($sheet) use($dataPage) {
                        $sheet->loadView('export_excel.report_complain')->with('dataPage', $dataPage)
                            ->with('columnView', $this->columnView);
                    });
                })->store('xlsx', storage_path('app/public/exports/'.$date.'/'.$dateFrom.'-'.$dateTo.'/'.$user->id));

                $path = 'public/exports/'.$date.'/'.$dateFrom.'-'.$dateTo.'/'.$user->id;
                $files = Storage::files($path);
                $template = $this->getTableExcelView($files, $path);
                return Response::json(array('state' => 'success', 'detail' => $template));
            }
            return Response::json(array('state' => 'fail', 'error' => 'Truy cập không hợp lệ'));
        }catch(Exception $e){
            return Response::json(array('state' => 'fail', 'error' => 'Lỗi xảy ra trên hệ thống'));
        }
    }

    private function getTableExcelView($files, $path){
        $templateViewTable = '<table class="table table-striped table-bordered table-hover">'
            . '<thead>'
            .	'<tr>'
            .		'<th class="center">STT</th>'
            .		'<th><i class="icon- bigger-120"></i>Thông tin</th>'
            .		'<th>Hành động</th>'
            .	'</tr>'
            . '</thead>'
            . '<tbody>%s</tbody>'
            . '</table>';

        $templatePlus = '';

        $templateViewMany = '<tr>'
            .	'<td class="center">%d</td>'
            .	'<td>%s</td>'
            .	'<td>'
            .		'<a href="%s" download style="text-decoration: none;"><i class="icon-file bigger-120"></i>Tải về</a>'
            .	'</td>'
            . '</tr>';


        foreach($files as $key => $file){
            $tempName = explode($path.'/', $file);
            $tempPath = explode('public/', $file);
            $url = asset('storage/'.$tempPath[1]);
            $templatePlus .= sprintf($templateViewMany, $key + 1, $tempName[1], $url);
        }
        $view = sprintf($templateViewTable, $templatePlus);
        return $view;
    }

//    public function repairDataForViewHistoryIndex($infoSurvey, $condition) {
//        $columnView = $this->columnDefault;
//        $columnNeedHid = $this->columnNeedToHide;
//        foreach ($columnNeedHid[$condition['departmentType']][$condition['type']] as $column) {
//            unset($columnView[$column]);
//        }
//        $arrayAccountShowSomeColumn = [36];
//
//        $userRole = Session::get('userRole');
//        if (array_search($userRole['id'], $arrayAccountShowSomeColumn) === false) {
//            if (isset($columnView['section_count_connected'])) {
//                unset($columnView['section_count_connected']);
//            }
//        }
//        $this->columnView = $columnView;
//
//        $data = [];
//        $arrayNotAllowNull = ['salename', 'csat_salesman_point', 'csat_deployer_point', 'csat_maintenance_staff_point'];
//        $arrErrorType = json_decode(json_encode($this->selErrorType), 1);
//        $arrActions = json_decode(json_encode($this->selProcessingActions), 1);
//        $arrayAction = [0 => 'Không làm gì', 1 => 'Không làm gì', 2 => 'Tạo checklist', 3 => 'PreChecklist', 4 => 'Tạo checklist INDO', 5 => 'Chuyển phòng ban khác'];
//        $arrayResult = [0 => "Không cần liên hệ", 1 => "Không liên lạc được", 2 => "Gặp KH, KH từ chối CS", 3 => "Không gặp người SD", 4 => "Gặp người SD"];
//        $emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
//        $surveyTitle = [1 => 'Sau triển khai DLS', 2 => 'Sau bảo trì', 3 => 'Sau thu cước tại nhà', 5 => 'HiFPT', 6 => 'Sau triển khai TLS'];
//		$SpecicalSaleBranchCode = [94=>'ITV', 200=>'Dai ly', 95=>'FN', 97=>'FTTH',98=>'KDDA',90=>'FTI',93=>'Ivoi'];
//        foreach ($this->selNPSImprovement as $value) {
//            $surveyImprove[$value->answer_id] = $value->answers_title;
//            $surveyImprove['-1'] = 'Chưa trả lời';
//            $surveyImprove[''] = '';
//        }
//
//        foreach ($infoSurvey as $index => $surveySections) {
//            if (strpos($surveySections->nps_improvement, ',') !== false) {
//                $tempImprove = explode(',', $surveySections->nps_improvement);
//                $surveySections->nps_improvement = '';
//                foreach ($tempImprove as $val) {
//                    $surveySections->nps_improvement .= $surveyImprove[$val] . ',';
//                }
//                $surveySections->nps_improvement = substr($surveySections->nps_improvement, 0, -1);
//            } else {
//                $surveySections->nps_improvement = $surveyImprove[$surveySections->nps_improvement];
//            }
//            $keyNet = (in_array($surveySections->section_survey_id, [1, 3, 6])) ? 'csat_net_point' : 'csat_maintenance_net_point';
//            $keyTV = (in_array($surveySections->section_survey_id, [1, 3, 6])) ? 'csat_tv_point' : 'csat_maintenance_tv_point';
//            $keyNetErrorType = (in_array($surveySections->section_survey_id, [1, 3, 6])) ? 'csat_net_answer_extra_id' : 'csat_maintenance_net_answer_extra_id';
//            $keyTVErrorType = (in_array($surveySections->section_survey_id, [1, 3, 6])) ? 'csat_tv_answer_extra_id' : 'csat_maintenance_tv_answer_extra_id';
//            $keyTVErr = array_search($surveySections->$keyTVErrorType, array_column($arrErrorType, 'answer_id'));
//            $keyNetErr = array_search($surveySections->$keyNetErrorType, array_column($arrErrorType, 'answer_id'));
//            $keyNetActions = array_search($surveySections->result_action_net, array_column($arrActions, 'answer_id'));
//            $keyTVActions = array_search($surveySections->result_action_tv, array_column($arrActions, 'answer_id'));
//            $viStatus = !empty($surveySections->violation_status) ? json_decode($surveySections->violation_status, 1) : ['sales' => null, 'deployer' => null, 'maintenance' => null, 'teleSales' => null];
//            //tình trạng báo cáo xử lý CSAT
//            if (!isset($viStatus['teleSales']))
//                $viStatus['teleSales'] = null;
//            if (!isset($viStatus['deployer']))
//                $viStatus['deployer'] = null;
//            if (!isset($viStatus['sales']))
//                $viStatus['sales'] = null;
//            if (!isset($viStatus['maintenance']))
//                $viStatus['maintenance'] = null;
//            $dataRow = [];
//
//            foreach ($columnView as $key => $val) {
//                if (array_search($key, $arrayNotAllowNull) !== false) {
//                    $dataRow[$key] = (empty($surveySections->$key)) ? '' : $surveySections->$key;
//                } else {
//                    switch ($key) {
//                        case 'violation_status sale':
//                            //DirectSales
//                            if ($surveySections->section_survey_id == 1) {
//                                if ($dataRow['csat_salesman_point'] == '' || $dataRow['csat_salesman_point'] >= 3) {
//                                    $dataRow[$key] = 'Không cần báo cáo';
//                                } elseif ($viStatus['sales'] == 2) {
//                                    $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(2, " . $surveySections->section_id . ", 1)' role='button' data-toggle='modal'><span id='sales" . $surveySections->section_id . "'>Đã báo cáo</span></a>";
//                                } else {
//                                    $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(1, " . $surveySections->section_id . ", 1)' role='button' data-toggle='modal'><span id='sales" . $surveySections->section_id . "'>Chưa báo cáo</span></a>";
//                                }
//                                if ($dataRow['csat_salesman_point'] != '') {
//                                    $dataRow['csat_salesman_point'] = "<span><strong><img src='" . asset("assets/img/" . $emotions[$dataRow['csat_salesman_point']]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow['csat_salesman_point'];
//                                }
//                            }
//                            //Telesales
//                            else if ($surveySections->section_survey_id == 6) {
//                                if ($dataRow['csat_salesman_point'] == '' || $dataRow['csat_salesman_point'] >= 3) {
//                                    $dataRow[$key] = 'Không cần báo cáo';
//                                } elseif ($viStatus['teleSales'] == 2) {
//                                    $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(2, " . $surveySections->section_id . ", 4)' role='button' data-toggle='modal'><span id='teleSales" . $surveySections->section_id . "'>Đã báo cáo</span></a>";
//                                } else {
//                                    $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(1, " . $surveySections->section_id . ", 4)' role='button' data-toggle='modal'><span id='teleSales" . $surveySections->section_id . "'>Chưa báo cáo</span></a>";
//                                }
//                                if ($dataRow['csat_salesman_point'] != '') {
//                                    $dataRow['csat_salesman_point'] = "<span><strong><img src='" . asset("assets/img/" . $emotions[$dataRow['csat_salesman_point']]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow['csat_salesman_point'];
//                                }
//                            }
//                            break;
//                        case 'violation_status deploy':
//
//                            if ($dataRow['csat_deployer_point'] == '' || $dataRow['csat_deployer_point'] >= 3) {
//                                $dataRow[$key] = 'Không cần báo cáo';
//                            } elseif ($viStatus['deployer'] == 2) {
//                                $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(2, " . $surveySections->section_id . ", 2)' role='button' data-toggle='modal'><span id='deployer" . $surveySections->section_id . "'>Đã báo cáo</span></a>";
//                            } else {
//                                $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(1, " . $surveySections->section_id . ", 2)' role='button' data-toggle='modal'><span id='deployer" . $surveySections->section_id . "'>Chưa báo cáo</span></a>";
//                            }
//                            if ($dataRow['csat_deployer_point'] != '') {
//                                $dataRow['csat_deployer_point'] = "<span><strong><img src='" . asset("assets/img/" . $emotions[$dataRow['csat_deployer_point']]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow['csat_deployer_point'];
//                            }
//
//                            break;
//                        case 'section_supporter deploy':
//                            $dataRow[$key] = (!empty($surveySections->section_supporter) ? $surveySections->section_supporter : '') . (!empty($surveySections->section_subsupporter) ? ' ' . $surveySections->section_subsupporter : '');
//                            break;
//                        case 'section_supporter maintaince':
//                            $dataRow[$key] = (!empty($surveySections->section_supporter) ? $surveySections->section_supporter : '') . (!empty($surveySections->section_subsupporter) ? ' ' . $surveySections->section_subsupporter : '');
//                            break;
//                        case 'violation_status maintaince':
//                            if ($dataRow['csat_maintenance_staff_point'] == '' || $dataRow['csat_maintenance_staff_point'] >= 3) {
//                                $dataRow[$key] = 'Không cần báo cáo';
//                            } elseif ($viStatus['maintenance'] == 2) {
//                                $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(2, " . $surveySections->section_id . ", 3)' role='button' data-toggle='modal'><span id='sales" . $surveySections->section_id . "'>Đã báo cáo</span></a>";
//                            } else {
//                                $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(1, " . $surveySections->section_id . ", 3)' role='button' data-toggle='modal'><span id='sales" . $surveySections->section_id . "'>Chưa báo cáo</span></a>";
//                            }
//                            if ($dataRow['csat_maintenance_staff_point'] != '') {
//                                $dataRow['csat_maintenance_staff_point'] = "<span><strong><img src='" . asset("assets/img/" . $emotions[$dataRow['csat_maintenance_staff_point']]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow['csat_maintenance_staff_point'];
//                            }
//                            break;
//                        case 'csat_tv_point':
//                            $dataRow[$key] = (empty($surveySections->$keyTV)) ? '' : $surveySections->$keyTV;
//                            if (!empty($surveySections->$keyTV)) {
//                                $dataRow[$key] = "<span><strong><img src='" . asset("assets/img/" . $emotions[$dataRow[$key]]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow[$key];
//                            }
//                            break;
//                        case 'keyTVErrorType':
//                            $dataRow[$key] = ($keyTVErr !== false) ? $this->selErrorType[$keyTVErr]->answers_title : '';
//                            break;
//                        case 'result_action_tv':
//                            $dataRow[$key] = !empty($surveySections->result_action_tv) ? $this->selProcessingActions[$keyTVActions]->answers_title : '';
//                            break;
//                        case 'csat_net_point':
//                            $dataRow[$key] = (empty($surveySections->$keyNet)) ? '' : $surveySections->$keyNet;
//                            if (!empty($surveySections->$keyNet)) {
//                                $dataRow[$key] = "<span><strong><img src='" . asset("assets/img/" . $emotions[$dataRow[$key]]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow[$key];
//                            }
//                            break;
//                        case 'keyNetErrorType':
//                            $dataRow[$key] = ($keyNetErr !== false) ? $this->selErrorType[$keyNetErr]->answers_title : '';
//                            break;
//                        case 'result_action_net':
//                            $dataRow[$key] = !empty($surveySections->result_action_net) ? $this->selProcessingActions[$keyNetActions]->answers_title : '';
//                            break;
//                        case 'section_survey_id':
//                            $dataRow[$key] = '<span class="' . $surveySections->section_survey_id . '">' . !empty($surveyTitle[$surveySections->section_survey_id]) ? $surveyTitle[$surveySections->section_survey_id] : '' . '</span>';
//                            break;
//                        case 'section_action':
//                            $dataRow[$key] = $arrayAction[$surveySections->section_action];
//                            break;
//                        case 'section_connected':
//                            $dataRow[$key] = $arrayResult[$surveySections->section_connected];
//                            break;
//                        case 'section_sub_parent_desc':
//                            $dataRow[$key] = str_replace('Vung', 'Vùng', $surveySections->section_sub_parent_desc);
//                            break;
//                        case 'section_branch_code':
//                            $locationName = $surveySections->section_location;
//                            if (!empty($surveySections->section_branch_code)) {//HNI, HCM
//                                $locationName = str_replace(' - ', $surveySections->section_branch_code . '-', $surveySections->section_location);
//                            }
//                            $dataRow[$key] = $locationName;
//                            break;
//                        case 'section_sale_branch_code':
//                            $locationName = $surveySections->section_location;
//                            if (!empty($surveySections->section_sale_branch_code)) {//HNI, HCM
//                                if(in_array($surveySections->section_sale_branch_code, [94,200,95,97,98,90,93]))
//                                        $locationName=$SpecicalSaleBranchCode[$surveySections->section_sale_branch_code];
//                                else
//                                $locationName = str_replace(' - ', $surveySections->section_sale_branch_code . '-', $surveySections->section_location);
//                            }
//                            $dataRow[$key] = $locationName;
//                            break;
//                        case 'special':
//                            $dataRow[$key] = '';
//                            if ($surveySections->section_survey_id > 0) {//chưa gặp người sử dụng
//                                $dataRow[$key] .= '<span class="visible-md visible-lg hidden-sm hidden-xs btn-group"><a class="open-tooltip" href="#modal-table" onclick="open_tooltip(' . $surveySections->section_id . ',\'' . $surveySections->section_contract_num . '\',\'' . $surveySections->section_contact_phone . '\')" role="button" data-toggle="modal" title="Chi Tiết"><span class="badge badge-info">i</span></a></span>';
//                            }
//                            if ($surveySections->section_connected == 4 && !empty($surveySections->section_contact_phone)) {
//                                $dataRow[$key] .= '<span class="visible-md visible-lg hidden-sm hidden-xs btn-group"><a style="cursor: pointer; text-decoration: none;" onclick="checkVoiceRecord(' . $surveySections->section_id . ')"><span class="icon-headphones bigger-110"></span></a></span>';
//                            }
//                            break;
//                        default:
//                            $dataRow[$key] = $surveySections->$key;
//                            break;
//                    }
//                }
//            }
//            array_push($data, $dataRow);
//        }
//        return $data;
//    }
//
    public function repairDataForViewHistoryExcel($infoSurvey, $condition) {
        $columnView = $this->columnDefault;
        $columnNeedHid = $this->columnNeedToHide;
        foreach ($columnNeedHid as $column) {
            unset($columnView[$column]);
        }
        $arrayAccountShowSomeColumn = [36];
        $userRole = Session::get('userRole');
        if (array_search($userRole['id'], $arrayAccountShowSomeColumn) === false) {
            if (isset($columnView['section_count_connected'])) {
                unset($columnView['section_count_connected']);
            }
        }

        $columnExcel = '';
        foreach ($columnView as $key => $val) {
            switch ($key) {
                case 'csat_salesman_point':
                    $columnExcel[$key] = $val;
                    $columnExcel['csat_salesman_note'] = 'Ghi chú nhân viên kinh doanh';
                    break;
                case 'csat_deployer_point':
                    $columnExcel[$key] = $val;
                    $columnExcel['csat_deployer_note'] = 'Ghi chú nhân viên triển khai';
                    break;
                case 'csat_maintenance_staff_point':
                    $columnExcel[$key] = $val;
                    $columnExcel['csat_maintenance_staff_note'] = 'Ghi chú nhân viên bảo trì';
                    break;
                default:
                    $columnExcel[$key] = $val;
            }
        }

        $columnView = $columnExcel;
        $this->columnView = $columnExcel;

        $data = [];
        $arrayNotAllowNull = ['salename', 'csat_salesman_point', 'csat_deployer_point', 'csat_maintenance_staff_point'];
        $arrayAction = [0 => 'Không làm gì', 1 => 'Không làm gì', 2 => 'Tạo checklist', 3 => 'PreChecklist', 4 => 'Tạo checklist INDO', 5 => 'Chuyển phòng ban khác'];
        $arrayResult = [0 => "Không cần liên hệ", 1 => "Không liên lạc được", 2 => "Gặp KH, KH từ chối CS", 3 => "Không gặp người SD", 4 => "Gặp người SD"];
        $surveyTitle = [1 => 'Sau triển khai DirectSales', 2 => 'Sau bảo trì', 3 => 'Sau thu cước tại nhà', 5 => 'HiFPT', 6 => 'Sau triển khai Telesales'];
        $SpecicalSaleBranchCode = [94=>'ITV', 200=>'Dai ly', 95=>'FN', 97=>'FTTH',98=>'KDDA',90=>'FTI',93=>'Ivoi'];

        foreach ($infoSurvey as $index => $surveySections) {
            $dataRow = [];
            foreach ($columnView as $key => $val) {
                if (array_search($key, $arrayNotAllowNull) !== false) {
                    $dataRow[$key] = (empty($surveySections->$key)) ? '' : $surveySections->$key;
                } else {
                    switch ($key) {
                        case 'section_survey_id':
                            $dataRow[$key] = !empty($surveyTitle[$surveySections->section_survey_id]) ? $surveyTitle[$surveySections->section_survey_id] : '';
                            break;
                        case 'section_action':
                            $dataRow[$key] = $arrayAction[$surveySections->section_action];
                            break;
                        case 'section_sub_parent_desc':
                            $dataRow[$key] = str_replace('Vung', 'Vùng', $surveySections->section_sub_parent_desc);
                            break;
                        case 'section_branch_code':
                            $locationName = $surveySections->section_location;
                            if (!empty($surveySections->section_branch_code)) {//HNI, HCM
                                $locationName = str_replace(' - ', $surveySections->section_branch_code . '-', $surveySections->section_location);
                            }
                            $dataRow[$key] = $locationName;
                            break;
                        case 'section_sale_branch_code':
                            $locationName = $surveySections->section_location;
                            if(in_array($surveySections->section_sale_branch_code, [94,200,95,97,98,90,93]))
                                        $locationName=$SpecicalSaleBranchCode[$surveySections->section_sale_branch_code];
                                else
                                $locationName = str_replace(' - ', $surveySections->section_sale_branch_code . '-', $surveySections->section_location);

                            $dataRow[$key] = $locationName;
                            break;
                        default:
                            $dataRow[$key] = $surveySections->$key;
                            break;
                    }
                }
            }
            array_push($data, $dataRow);
        }
        return $data;
    }

}
