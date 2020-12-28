<?php

namespace App\Http\Controllers;

use App\Models\Authen\Department;
use App\Models\RecordChannel;
use App\models\Surveys;
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
use App\Models\SurveyViolations;

use App\Models\OutboundQuestions;
use App\Models\SurveyResult;
use App\Models\OutboundAnswers;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class History extends Controller {

    var $sales;
    var $deploy;
    var $maintenance;

    var $modelSurveySections;
    var $modelRecordChannel;
    var $modelDepartment;
    var $modelSurvey;

    var $listConnectedAvailable;
    var $listActionAvailable;
    var $listReportActionAvailable;
    var $listRecordChannels;
    var $listSurveyAvailable;
    var $violationStatus;

    var $selNPSImprovement;
    var $selErrorType;
    var $selProcessingActions;

    var $columnView;
    var $columnDefault;
    var $columnNeedToShow;

    public function __construct() {
        $this->sales = 'sales';
        $this->deploy = 'deploy';
        $this->maintenance = 'maintenance';

        $this->modelSurveySections = new SurveySections();
        $this->modelRecordChannel = new RecordChannel();
        $this->modelDepartment = new Department();
        $this->modelSurvey = new Surveys();

        $this->listConnectedAvailable = [
            4 => 'MeetUser',
            3 => 'DidNotMeetUser',
            2 => 'MeetCustomerCustomerDeclinedToTakeSurvey',
            1 => 'CannotContact',
            0 => 'NoNeedContact',
        ];
        $this->listActionAvailable = [
            1 => 'NotYetDoAnything',
            2 => 'CreateChecklist',
            3 => 'CreatePreChecklist',
        ];
        $this->listReportActionAvailable = [
            0 => 'All',
            1 => 'NotYet',
            2 => 'Already',
        ];
        $this->listRecordChannels = $this->modelRecordChannel->getAllRecordChannel();
        $this->listSurveyAvailable = $this->modelSurvey->getAllSurvey();
        $this->violationStatus = [
            $this->sales => null,
            $this->deploy => null,
            $this->maintenance => null,
        ];

        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
        $this->selErrorType = $this->modelSurveySections->getErrorType([20, 22, 25]);
        $this->selProcessingActions = $this->modelSurveySections->getProcessingActions([21]);

        $this->columnView = $this->columnDefault = [
            'section_id' => '',

            'saleName' => 'Sale',
            'saleManPoint' => 'SalePoint',
            'saleManNote' => 'Note',
            'reportStatusSale' => 'Report',

            'supporterDeploy' => 'Tech',
            'deployStaffPoint' => 'TechPoint',
            'deployStaffNote' => 'Note',
            'reportStatusDeployStaff' => 'Report',

            'supporterMaintenance' => 'Tech',
            'maintenanceStaffPoint' => 'TechPoint',
            'maintenanceStaffNote' => 'Note',
            'reportStatusMaintenanceStaff' => 'Report',

            'netPoint' => 'NetPoint',
            'netNote' => 'Note',
            'netAnswerExtra' => 'NetErrorType',
            'resultActionNet' => 'InternetResolve',

            'npsPoint' => 'NPSPoint',
            'npsImprovement' => 'OpinionOfCustomer',
            'npsImprovementNote' => 'Note',

            'section_survey_id' => 'PointOfContact',
            'section_action' => 'Resolve',
            'section_connected' => 'ContactResult',
            'section_contract_num' => 'Contract',
            'section_contact_phone' => 'Phone',
            'section_user_name' => 'SurveyAgent',
            'section_branch_code' => 'Branch',
            'section_note' => 'SumNote',
            'section_time_completed' => 'TimeComplete',
            'section_count_connected' => 'SurveyCount',
            'special' => '',
        ];

        //Cột cần show của table Triển khai DirectSale - HappyCall
        $dls = [
            'section_id' => '',

            'saleName' => 'Sale',
            'saleManPoint' => 'SalePoint',
            'saleManNote' => 'Note',
            'reportStatusSale' => 'Report',

            'supporterDeploy' => 'Tech',
            'deployStaffPoint' => 'TechPoint',
            'deployStaffNote' => 'Note',
            'reportStatusDeployStaff' => 'Report',

            'netPoint' => 'NetPoint',
            'netNote' => 'Note',
            'netAnswerExtra' => 'NetErrorType',
            'resultActionNet' => 'InternetResolve',

            'npsPoint' => 'NPSPoint',
            'npsImprovement' => 'OpinionOfCustomer',
            'npsImprovementNote' => 'Note',

            'section_survey_id' => 'PointOfContact',
            'section_action' => 'Resolve',
            'section_connected' => 'ContactResult',
            'section_contract_num' => 'Contract',
            'section_contact_phone' => 'Phone',
            'section_user_name' => 'SurveyAgent',
            'section_branch_code' => 'Branch',
            'section_note' => 'SumNote',
            'section_time_completed' => 'TimeComplete',
            'section_count_connected' => 'SurveyCount',
            'special' => '',
        ];

        //Cột cần show của table Bảo trì - HappyCall
        $bt = [
            'section_id' => '',

            'supporterMaintenance' => 'Tech',
            'maintenanceStaffPoint' => 'TechPoint',
            'maintenanceStaffNote' => 'Note',
            'reportStatusMaintenanceStaff' => 'Report',

            'netPoint' => 'NetPoint',
            'netNote' => 'Note',
            'netAnswerExtra' => 'NetErrorType',
            'resultActionNet' => 'InternetResolve',

            'npsPoint' => 'NPSPoint',
            'npsImprovement' => 'OpinionOfCustomer',
            'npsImprovementNote' => 'Note',

            'section_survey_id' => 'PointOfContact',
            'section_action' => 'Resolve',
            'section_connected' => 'ContactResult',
            'section_contract_num' => 'Contract',
            'section_contact_phone' => 'Phone',
            'section_user_name' => 'SurveyAgent',
            'section_branch_code' => 'Branch',
            'section_note' => 'SumNote',
            'section_time_completed' => 'TimeComplete',
            'section_count_connected' => 'SurveyCount',
            'special' => '',
        ];

        $this->columnNeedToShow = [
            //Sale
            '1' => [
                // Sau tk IBB
                '1:1' => $dls,

                // Bảo trì
                '2:1' => [
                ],
            ],
            //SIR
            '2' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
            ],
            //CS
            '3' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
            ],
            //CUS
            '4' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
            ],
            //QA
            '5' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
            ],
            //BOD
            '6' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
            ],
        ];
    }

    public function index(Request $request) {
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
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;
                //nếu tìm kiếm theo HĐ thì bỏ hết các đk tìm kiếm khác, trừ đk triển khai hoặc bảo trì.
                if (!empty($condition['contractNum'])) {
                    $arrayKeep = ['contractNum', 'type','departmentType', 'recordPerPage', 'userSurvey', 'allQuestion', 'channelConfirm'];
                    foreach($condition as $key => $val){
                        if(!in_array($key, $arrayKeep)){
                            $condition[$key] = '';
                        }
                    }
                }
                Session::put('condition', $condition);
            }
            //nếu ktra thấy ko chọn vùng, chi nhánh thì gán lại các vùng, chi nhánh đã được phân cho user
            $condition['location'] = empty($condition['location']) ? $userGranted['branchLocationCode'] : $condition['location'];
            $condition['locationSQL'] = [];

            //Điều chỉnh lại location để search hiệu quả
            foreach($condition['location'] as $location){
                $temp = explode('_', $location);
                $condition['locationSQL'][$temp[1]][] = $temp[0];
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
            $dataPage = $this->repairDataForViewHistoryIndex($infoSurvey, $condition);
            //gán lại giá trị cho tìm kiếm
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            } else {
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;
            }
        }

        $user = Auth::user();
        $userRole = Session::get('userRole');
        $listDepartmentAvailable = $this->modelDepartment->getDepartmentsByRoleID($userRole['id']);
        $listRecordChannels = $this->listRecordChannels;
        $listSurveyAvailable = $this->listSurveyAvailable;

        $listConnectedAvailable = $this->listConnectedAvailable;
        $listActionAvailable = $this->listActionAvailable;
        $listReportActionAvailable = $this->listReportActionAvailable;

        $data = [
            'modelLocation' => $listLocation,
            'modelSurveySections' => $infoSurvey,
            'searchCondition' => $condition,
            'currentPage' => !empty($currentPage) ? $currentPage : 0,
            'user' => $user,
            'userRole' => $userRole,
            'userGranted' => !empty($userGranted) ? $userGranted : '',
            'selNPSImprovement' => $this->selNPSImprovement,
            'selErrorType' => $this->selErrorType,
            'selProcessingActions' => $this->selProcessingActions,
            'dataPage' => $dataPage,
            'columnView' => $this->columnView,
            'listRecordChannels' => $listRecordChannels,
            'listDepartmentAvailable' => $listDepartmentAvailable,
            'listSurveyAvailable' => $listSurveyAvailable,
            'listConnectedAvailable' => $listConnectedAvailable,
            'listActionAvailable' => $listActionAvailable,
            'listReportActionAvailable' => $listReportActionAvailable,
        ];

        return view("report_history/index", $data);
    }

    public function detail_survey(Request $request) {
        if ($request->ajax() && Session::token() === Input::get('_token')) {
            $input = Input::all();
            $modelSurveySection = new SurveySections();

            $sectionID = $input['survey'];
            $detailResult = $modelSurveySection->getAllDetailSurveyInfo($sectionID);
            $surveySection = $modelSurveySection->getSurveySections(['sectionId' => $sectionID]);

            $data = [
                'detail' => $detailResult,
                'contract' => $surveySection->section_contract_num,
                'connected' => $surveySection->section_connected,
                'contactPhone' => $surveySection->section_contact_phone,
                'mainNote' => $surveySection->section_note,
            ];
            echo view('report_history/detailSurvey', $data)->render();
        }
        exit();
    }

    public function getTimeSurvey(Request $request) {
        if ($request->ajax() && Session::token() === Input::get('_token')) {
            $data = Input::all();
            $result = DB::table('survey_section_history')
                ->select('section_connected', 'section_time_completed', 'section_user_name', 'section_user_modified')
                ->where('section_id', $data['id'])
                ->get();

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
        $condition['location'] = $request->location;

        $condition['contractNum'] = !empty($request->contractNum) ? $request->contractNum : '';
        $condition['type'] = !empty($request->type) ? $request->type : '';
        $condition['section_action'] = !empty($request->processingSurvey) ? $request->processingSurvey : '';
        $condition['section_connected'] = !empty($request->surveyStatus) ? $request->surveyStatus : '';
        $condition['CSATPointSale'] = !empty($request->CSATPointSale) ? $request->CSATPointSale : '';
        $condition['CSATPointNVTK'] = !empty($request->CSATPointNVTK) ? $request->CSATPointNVTK : '';
        $condition['CSATPointBT'] = !empty($request->CSATPointBT) ? $request->CSATPointBT : '';
        $condition['CSATPointNet'] = !empty($request->CSATPointNet) ? $request->CSATPointNet : '';
        $condition['userSurvey'] = !empty($request->userSurvey) ? $request->userSurvey : '';
        $condition['RateNPS'] = !empty($request->RateNPS) ? $request->RateNPS : '';
        $condition['NPSPoint'] = !empty($request->NPSPoint) ? $request->NPSPoint : '';
        $condition['departmentType'] = !empty($request->departmentType) ? $request->departmentType : '';
        $condition['saleName'] = !empty($request->saleName) ? $request->saleName : '';
        $condition['technicalStaff'] = !empty($request->technicalStaff) ? $request->technicalStaff : '';
        $condition['reportedStatus'] = !empty($request->reportedStatus) ? $request->reportedStatus : '';
        $condition['NetErrorType'] = !empty($request->NetErrorType) ? $request->NetErrorType : '';
        $condition['processingActionsInternet'] = !empty($request->processingActionsInternet) ? $request->processingActionsInternet : '';
        $condition['allQuestion'] = $questionNeed;
		
		$condition['channelConfirm'] = !empty($request->channelConfirm)?$request->channelConfirm:'';

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
            if(!isset($info->violation_status)){
                $info->violation_status = null;
            }

            $info->npsPoint = null;

            $info->npsImprovement = null;
            $info->npsImprovementNote = null;

            $info->saleManPoint = null;
            $info->saleManNote = null;

            $info->deployStaffPoint = null;
            $info->deployStaffNote = null;
            $info->deployStaffAnswerExtra = null;

            $info->maintenanceStaffPoint = null;
            $info->maintenanceStaffNote = null;
            $info->maintenanceStaffAnswerExtra = null;

            $info->netPoint = null;
            $info->netNote = null;
            $info->netAnswerExtra = null;
            $info->resultActionNet = null;
        }

        foreach($surveyResults as $result){
            // Loại bỏ kí tự "=" đầu tiên khi CS nhập liệu
            $note = $result->survey_result_note;
            while(stripos($note, "=") === 0){
                $note = str_replace_first("=","",$note);
            }

            if(array_search($result->survey_result_question_id, $condition['allQuestion'][1]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->saleManPoint = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->saleManNote = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][3]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->deployStaffPoint = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->deployStaffNote = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][4]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->maintenanceStaffPoint = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->maintenanceStaffNote = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][5]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->netPoint = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->netNote = $note;
                $infoSurveyKey[$result->survey_result_section_id]->netAnswerExtra = $result->survey_result_answer_extra_id;
                $infoSurveyKey[$result->survey_result_section_id]->resultActionNet = $result->survey_result_action;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][9]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->npsImprovement = $result->survey_result_answer_id;
                $infoSurveyKey[$result->survey_result_section_id]->npsImprovementNote = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][10]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->npsPoint = $ansPoints[$result->survey_result_answer_id];
            }
        }
        return $infoSurveyKey;
    }

    public function exportSurvey(Request $request) {
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
                $condition['location'] = empty($condition['location']) ? $userGranted['branchLocationCode'] : $condition['location'];
                $condition['locationSQL'] = [];

                //Điều chỉnh lại location để search hiệu quả
                foreach($condition['location'] as $location){
                    $temp = explode('_', $location);
                    $condition['locationSQL'][$temp[1]][] = $temp[0];
                }

                $user = Auth::user();

                $date = date('dmY', time());
                $dateFrom = date('dmY',strtotime($condition['surveyFrom']));
                $dateTo = date('dmY',strtotime($condition['surveyTo']));

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
                $dataPage = $this->repairDataForViewHistoryExcel($infoSurvey, $condition);
                //chỉnh các thông số để xuất file excel
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment');
                header('Cache-Control: max-age=0');
                ini_set('memory_limit', '2048M');

                //export ra file excel
                $fileName ='ChiTietDanhGia_' . date('dmY', strtotime($condition['surveyFrom'])) . '_' . date('dmY', strtotime($condition['surveyTo']));

                Excel::create($fileName, function($excel) use($dataPage) {
                    $excel->sheet('Sheet 1', function($sheet) use($dataPage) {
                        $sheet->loadView('export_excel.report_history')
                            ->with('dataPage', $dataPage)
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

    public function getViolations(Request $request) {
        $input = $request->all();
        if ($request->isMethod('post') && Session::token() === Input::get('_token')) {
            $surveyViolation = new SurveyViolations();

            $id = $input['id'];
            $type = $input['type'];
            $paramSearch = [
                'id' => $id,
                'type' => $type,
            ];

            $detail = $surveyViolation->searchViolationByParam($paramSearch);
            $flagCount = $flagModify = 0;
            if (!empty($detail) && $detail->modify_count > 2) {
                $flagCount = 1;
            }
            if (!empty($detail) && (strtotime($detail->updated_at) + (7 * 24 * 3600) <= strtotime(date("Y-m-d H:i:s")))) {
                $flagModify = 1;
            }

            $groupId = null;
            switch($type){
                case 1:
                    $groupId = [22,24];
                    break;
                case 2:
                case 3:
                    $groupId = [23,24];
                    break;
                default:
            }

            $violationTypes =  $punishments = [];
            $modelAnswers = new OutboundAnswers();
            $answers = $modelAnswers->getAnswerByGroup($groupId);
            foreach($answers as $answer){
                if($answer->answer_group == 24){
                    $punishments[] = $answer;
                }else{
                    $violationTypes[] = $answer;
                }
            }

            if(!is_array($detail)){
                $detail = (array)$detail;
            }
            $data = [
                'id' => $input['id'],
                'type' => $input['type'],
                'status' => $input['status'],
                'detail' => $detail,
                'flagCount' => $flagCount,
                'flagModify' => $flagModify,
                'violationTypes' => $violationTypes,
                'punishments' => $punishments,
            ];

            echo view('report_history/detailViolation', $data)->render();
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
                $type = null;
                switch($data['type']){
                    case 1:
                        $type = $this->sales;
                        break;
                    case 2:
                        $type = $this->deploy;
                        break;
                    case 3:
                        $type = $this->maintenance;
                        break;
                    default:
                }

                $surveySection = SurveySections::find($data['sID']);
                if (!empty($surveySection)) {//tìm thấy trong db
                    // Gắn lại tình trạng báo cáo xử lý
                    $viStatus = [
                        'sales' => null,
                        'deploy' => null,
                        'maintenance' => null,
                    ];
                    if(!empty($surveySection->violation_status)){
                        $viStatus = array_merge($viStatus, json_decode($surveySection->violation_status, 1));
                    }
                    $viStatus[$type] = 2;
                    $surveySection->violation_status = json_encode($viStatus); //cập nhật lại tình trạng báo cáo xử lý CSAT
                }
                $user = Auth::user();
                $isFTQ = ( ExtraFunction::checkHaveAuthenRole('FTC') || ExtraFunction::checkHaveAuthenRole('QA HO') || ExtraFunction::checkHaveAuthenRole('QA chi nhánh'));
                $sectionResultPoint = DB::table('outbound_survey_result AS osr')
                    ->select(DB::raw('
                        MAX(if(osr.survey_result_question_id in (1), osr.survey_result_answer_id, NULL)) "'.$this->sales.'",
                        MAX(if(osr.survey_result_question_id in (2), osr.survey_result_answer_id, NULL)) "'.$this->deploy.'",
                        MAX(if(osr.survey_result_question_id in (6), osr.survey_result_answer_id, NULL)) "'.$this->maintenance.'"
                    '))
                    ->where('osr.survey_result_section_id', $data['sID'])
                    ->groupBy('osr.survey_result_section_id')
                    ->get();


                $param['sectionID'] = !empty($data['sID']) ? $data['sID'] : '';
                $param['supporterName'] = !empty($data['supporterName']) ? $data['supporterName'] : '';
                $param['supporterID'] = !empty($data['supporterID']) ? $data['supporterID'] : '';
                $param['type'] = !empty($data['type']) ? $data['type'] : '';
                $param['point'] = $sectionResultPoint[0]->$type;

                $param['explanationDescription'] = !empty($data['explanationDescription']) ? $data['explanationDescription'] : '';
                $param['qs_verify'] = !empty($data['verify']) ? $data['verify'] : '';
                $param['violationsType'] = !empty($data['optType']) ? $data['optType'] : '';
                $param['punishment'] = !empty($data['optPunish']) ? $data['optPunish'] : '';
                $param['punishmentDescription'] = !empty($data['punishmentDescription']) ? $data['punishmentDescription'] : '';

                $param['remedy'] = !empty($data['remedy']) ? $data['remedy'] : '';
                $param['description'] = !empty($data['description']) ? $data['description'] : '';

                $param['discipline_ftq'] = (!empty($data['discipline_ftq']) && $isFTQ == TRUE) ? $data['discipline_ftq'] : '';
                $param['punishmentAdditional'] = (!empty($data['optPunishAdditional']) && $isFTQ == TRUE) ? $data['optPunishAdditional'] : '';

                // lỗi không thuộc nhân viên và người dùng có quyền FTQ
                if (in_array($param['violationsType'], [217, 227]) && $isFTQ) {
                    $param['accept_staff_dont_has_mistake'] = 'no';
                    if (isset($data['has_mistake']) && $data['has_mistake'] == 'yes') {
                        $param['accept_staff_dont_has_mistake'] = 'yes';
                    }
                }
                //trường hợp chưa báo cáo
                $violation = SurveyViolations::join('outbound_survey_sections AS s', 'survey_violations.sectionID', '=', 's.section_id')
                    ->where('s.section_id', $data['sID'])
                    ->where("type", $data['type'])
                    ->first();
                if ($data['status'] == 1 && empty($violation)) {
                    $param['insert_at'] = $date;
                    $param['created_user'] = Auth::user()->name;
                    $param['updated_at'] = $date;
                    $modelViolation->insertViolation($param);
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
                $surveySection->save();
                $resStatus = trans('history.Already');
                DB::commit();
                echo json_encode(['resStatus' => $resStatus, 'id' => $data['sID'], 'object' => $type]);
            } catch (Exception $ex) {
                DB:rollback();
            }
        }
        exit();
    }

    public function repairDataForViewHistoryIndex($infoSurvey, $condition) {
        $columnView = $this->columnNeedToShow[$condition['departmentType']][$condition['type'].':'.$condition['channelConfirm']];
        foreach($columnView as $key => $val){
            if(empty($val)){
                $columnView[$key] = $this->columnDefault[$key];
            }
        }

        $this->columnView = $columnView;

        $data = [];
        $arrErrorType = json_decode(json_encode($this->selErrorType), 1);
        $arrActions = json_decode(json_encode($this->selProcessingActions), 1);

        $arrayAction = $this->listActionAvailable;
        $arrayResult = $this->listConnectedAvailable;

        $emotions = [
            1 => 'Point_01.png',
            2 => 'Point_02.png',
            3 => 'Point_03.png',
            4 => 'Point_04.png',
            5 => 'Point_05.png'
        ];

        $surveyTitle =  null;
        foreach($this->listSurveyAvailable as $survey){
            $surveyTitle[$survey->survey_id] = $survey->survey_key;
        }

        $modelLoc = new Location();
        $resLoc = $modelLoc->getAllLocation();
        $allLocationKey = [];
        foreach($resLoc as $val){
            $locationName = $val->name;
            $locationID = $val->id;
            $branchCode = (empty($val->branchcode))?0:$val->branchcode;
            if($branchCode != 0){
                $locationName = str_replace(' - ', $branchCode.' - ' , $locationName);
            }
            $allLocationKey[$locationID.':'.$branchCode] = $locationName;
        }

        $surveyImprove = null;
        foreach ($this->selNPSImprovement as $value) {
            $surveyImprove[$value->answer_id] = trans('answer.'.$value->answers_key);
            $surveyImprove['-1'] = trans('answer.NotYet');
            $surveyImprove[''] = '';
        }

        foreach ($infoSurvey as $index => $surveySections) {
            if (strpos($surveySections->npsImprovement, ',') !== false) {
                $tempImprove = explode(',', $surveySections->npsImprovement);
                $surveySections->npsImprovement = '';
                foreach ($tempImprove as $val) {
                    $surveySections->npsImprovement .= $surveyImprove[$val] . ',';
                }
                $surveySections->npsImprovement = substr($surveySections->npsImprovement, 0, -1);
            } else {
                $surveySections->npsImprovement = $surveyImprove[$surveySections->npsImprovement];
            }

            $keyNetErr = array_search($surveySections->netAnswerExtra, array_column($arrErrorType, 'answer_id'));
            $keyNetActions = array_search($surveySections->resultActionNet, array_column($arrActions, 'answer_id'));

            // Gắn lại tình trạng báo cáo xử lý
            $viStatus = $this->violationStatus;

            if(!empty($surveySections->violation_status)){
                $viStatus = array_merge($viStatus, json_decode($surveySections->violation_status, 1));
            }

            $dataRow = [];
            foreach ($columnView as $key => $val) {
                switch ($key) {
                    case 'reportStatusSale':
                        if ($surveySections->saleManPoint == '' || $surveySections->saleManPoint >= 3) {
                            $dataRow[$key] = trans('history.NoNeedToReport');
                        } elseif ($viStatus['sales'] == 2) {
                            $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(2, " . $surveySections->section_id . ", 1)' role='button' data-toggle='modal'><span id='".$this->sales . $surveySections->section_id . "'>".trans('history.Already')."</span></a>";
                        } else {
                            $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(1, " . $surveySections->section_id . ", 1)' role='button' data-toggle='modal'><span id='".$this->sales . $surveySections->section_id . "'>".trans('history.NotYet')."</span></a>";
                        }
                        break;
                    case 'reportStatusDeployStaff':
                        if ($surveySections->deployStaffPoint == '' || $surveySections->deployStaffPoint >= 3) {
                            $dataRow[$key] = trans('history.NoNeedToReport');
                        } elseif ($viStatus['deploy'] == 2) {
                            $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(2, " . $surveySections->section_id . ", 2)' role='button' data-toggle='modal'><span id='".$this->deploy . $surveySections->section_id . "'>".trans('history.Already')."</span></a>";
                        } else {
                            $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(1, " . $surveySections->section_id . ", 2)' role='button' data-toggle='modal'><span id='".$this->deploy . $surveySections->section_id . "'>".trans('history.NotYet')."</span></a>";
                        }
                        break;
                    case 'reportStatusMaintenanceStaff':
                        if ($surveySections->maintenanceStaffPoint == '' || $surveySections->maintenanceStaffPoint >= 3) {
                            $dataRow[$key] = trans('history.NoNeedToReport');
                        } elseif ($viStatus['maintenance'] == 2) {
                            $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(2, " . $surveySections->section_id . ", 3)' role='button' data-toggle='modal'><span id='". $this->maintenance . $surveySections->section_id . "'>".trans('history.Already')."</span></a>";
                        } else {
                            $dataRow[$key] = "<a class='open-tooltip' href='#modal-table-violation' onclick='open_violation(1, " . $surveySections->section_id . ", 3)' role='button' data-toggle='modal'><span id='". $this->maintenance  . $surveySections->section_id . "'>".trans('history.NotYet')."</span></a>";
                        }
                        break;
                    case 'supporterDeploy':
                        $dataRow[$key] = '';
                        break;
                    case 'supporterMaintenance':
                        $dataRow[$key] = (!empty($surveySections->section_account_list) ? $surveySections->section_account_list : '');
                        break;
                    case 'netAnswerExtra':
                        $dataRow[$key] = ($keyNetErr !== false) ? trans('error.'.$this->selProcessingActions[$keyNetErr]->answers_key) : '';
                        break;
                    case 'resultActionNet':
                        $dataRow[$key] = !empty($surveySections->result_action_net) ? trans('action.'.$this->selProcessingActions[$keyNetActions]->answers_key) : '';
                        break;
                    case 'section_survey_id':
                        $dataRow[$key] = '<span class="' . $surveySections->section_survey_id . '">' . !empty($surveyTitle[$surveySections->section_survey_id]) ? trans('pointOfContact.'.$surveyTitle[$surveySections->section_survey_id]) : '' . '</span>';
                        break;
                    case 'section_action':
                        $dataRow[$key] = trans('action.'.$arrayAction[$surveySections->section_action]);
                        break;
                    case 'section_connected':
                        $dataRow[$key] = trans('history.'.$arrayResult[$surveySections->section_connected]);
                        break;
                    case 'section_branch_code':
                        $dataRow[$key] = isset($allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_branch_code]) ? $allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_branch_code]: '';
                        break;
                    case 'special':
                        $dataRow[$key] = '';
                        if ($surveySections->section_connected > 0) {
                            $dataRow[$key] .= '<a class="open-tooltip" href="#modal-table" onclick="open_tooltip(' . $surveySections->section_id .')" role="button" data-toggle="modal" title="'.trans('history.Detail').'"><span class="badge badge-info">i</span></a>';
                        }
                        if ($surveySections->section_connected == 4 && !empty($surveySections->section_contact_phone)) {
                            $dataRow[$key] .= ' - ';
                            $dataRow[$key] .= '<a class="speaker" style="cursor: pointer; text-decoration: none;" onclick="checkVoiceRecord(' . $surveySections->section_id . ')"><span class="icon-headphones bigger-110">'.trans('history.ListenToRecord').'</span></a>';
                        }
                        break;
                    case 'saleManPoint':
                    case 'deployStaffPoint':
                    case 'maintenanceStaffPoint':
                    case 'netPoint':
                        $dataRow[$key] = (empty($surveySections->$key)) ? '' : $surveySections->$key;
                        if (!empty($surveySections->$key)) {
                            $dataRow[$key] = "<img src='" . asset("assets/img/" . $emotions[$dataRow[$key]]) . "' style='width: 25px;height: 25px' />  " . $dataRow[$key];
                        }
                        break;
                    default:
                        $dataRow[$key] = $surveySections->$key;
                        break;
                }
            }
            array_push($data, $dataRow);
        }
        
        return $data;
    }

    public function repairDataForViewHistoryExcel($infoSurvey, $condition) {
        $columnView = $this->columnNeedToShow[$condition['departmentType']][$condition['type'].':'.$condition['channelConfirm']];
        foreach($columnView as $key => $val){
            if(empty($val)){
                $columnView[$key] = $this->columnDefault[$key];
            }
        }

        $this->columnView = $columnView;

        $data = [];
        $arrErrorType = json_decode(json_encode($this->selErrorType), 1);
        $arrActions = json_decode(json_encode($this->selProcessingActions), 1);

        $arrayAction = $this->listActionAvailable;
        $arrayResult = $this->listConnectedAvailable;

        $surveyTitle =  null;
        foreach($this->listSurveyAvailable as $survey){
            $surveyTitle[$survey->survey_id] = $survey->survey_key;
        }

        $modelLoc = new Location();
        $resLoc = $modelLoc->getAllLocation();
        $allLocationKey = [];
        foreach($resLoc as $val){
            $locationName = $val->name;
            $locationID = $val->id;
            $branchCode = (empty($val->branchcode))?0:$val->branchcode;
            if($branchCode != 0){
                $locationName = str_replace(' - ', $branchCode.' - ' , $locationName);
            }
            $allLocationKey[$locationID.':'.$branchCode] = $locationName;
        }

        $surveyImprove = null;
        foreach ($this->selNPSImprovement as $value) {
            $surveyImprove[$value->answer_id] = trans('answer.'.$value->answers_key);
            $surveyImprove['-1'] = trans('answer.NotYet');
            $surveyImprove[''] = '';
        }

        foreach ($infoSurvey as $index => $surveySections) {
            if (strpos($surveySections->npsImprovement, ',') !== false) {
                $tempImprove = explode(',', $surveySections->npsImprovement);
                $surveySections->npsImprovement = '';
                foreach ($tempImprove as $val) {
                    $surveySections->npsImprovement .= $surveyImprove[$val] . ',';
                }
                $surveySections->npsImprovement = substr($surveySections->npsImprovement, 0, -1);
            } else {
                $surveySections->npsImprovement = $surveyImprove[$surveySections->npsImprovement];
            }

            $keyNetErr = array_search($surveySections->netAnswerExtra, array_column($arrErrorType, 'answer_id'));
            $keyNetActions = array_search($surveySections->resultActionNet, array_column($arrActions, 'answer_id'));

            // Gắn lại tình trạng báo cáo xử lý
            $viStatus = $this->violationStatus;

            if(!empty($surveySections->violation_status)){
                $viStatus = array_merge($viStatus, json_decode($surveySections->violation_status, 1));
            }

            $dataRow = [];
            foreach ($columnView as $key => $val) {
                switch ($key) {
                    case 'reportStatusSale':
                        if ($surveySections->saleManPoint == '' || $surveySections->saleManPoint >= 3) {
                            $dataRow[$key] = trans('history.NoNeedToReport');
                        } elseif ($viStatus['sales'] == 2) {
                            $dataRow[$key] = trans('history.Already');
                        } else {
                            $dataRow[$key] = trans('history.NotYet');
                        }
                        break;
                    case 'reportStatusDeployStaff':
                        if ($surveySections->deployStaffPoint == '' || $surveySections->deployStaffPoint >= 3) {
                            $dataRow[$key] = trans('history.NoNeedToReport');
                        } elseif ($viStatus['deploy'] == 2) {
                            $dataRow[$key] = trans('history.Already');
                        } else {
                            $dataRow[$key] = trans('history.NotYet');
                        }
                        break;
                    case 'reportStatusMaintenanceStaff':
                        if ($surveySections->maintenanceStaffPoint == '' || $surveySections->maintenanceStaffPoint >= 3) {
                            $dataRow[$key] = trans('history.NoNeedToReport');
                        } elseif ($viStatus['maintenance'] == 2) {
                            $dataRow[$key] = trans('history.Already');
                        } else {
                            $dataRow[$key] = trans('history.NotYet');
                        }
                        break;
                    case 'supporterDeploy':
                        $dataRow[$key] = '';
                        break;
                    case 'supporterMaintenance':
                        $dataRow[$key] = (!empty($surveySections->section_account_list) ? $surveySections->section_account_list : '');
                        break;
                    case 'netAnswerExtra':
                        $dataRow[$key] = ($keyNetErr !== false) ? trans('error.'.$this->selProcessingActions[$keyNetErr]->answers_key) : '';
                        break;
                    case 'resultActionNet':
                        $dataRow[$key] = !empty($surveySections->result_action_net) ? trans('action.'.$this->selProcessingActions[$keyNetActions]->answers_key) : '';
                        break;
                    case 'section_survey_id':
                        $dataRow[$key] = !empty($surveyTitle[$surveySections->section_survey_id]) ? trans('pointOfContact.'.$surveyTitle[$surveySections->section_survey_id]) : '';
                        break;
                    case 'section_action':
                        $dataRow[$key] = trans('action.'.$arrayAction[$surveySections->section_action]);
                        break;
                    case 'section_connected':
                        $dataRow[$key] = trans('history.'.$arrayResult[$surveySections->section_connected]);
                        break;
                    case 'section_branch_code':
                        $dataRow[$key] = isset($allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_branch_code]) ? $allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_branch_code]: '';
                        break;
                    case 'special':
                        break;
                    case 'saleManPoint':
                    case 'deployStaffPoint':
                    case 'maintenanceStaffPoint':
                    case 'netPoint':
                        $dataRow[$key] = (empty($surveySections->$key)) ? '' : $surveySections->$key;
                        break;
                    default:
                        $dataRow[$key] = $surveySections->$key;
                        break;
                }
            }
            array_push($data, $dataRow);
        }

        return $data;
    }

}
