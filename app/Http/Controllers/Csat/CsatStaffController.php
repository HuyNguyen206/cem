<?php

namespace App\Http\Controllers\Csat;

use App\Models\Authen\Department;
use App\Component\BuildDataCSAT;
use App\Component\BuildDataCSATExcel;
use App\Http\Controllers\Controller;
use App\Models\SurveyViolations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\SurveySections;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use App\Component\ExtraFunction;
use App\Models\OutboundAnswers;
use App\Component\HelpProvider;
use App\Models\OutboundQuestions;
use App\Models\RecordChannel;
use Illuminate\Support\Facades\Auth;
use App\Models\SurveyResult;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use App\models\Surveys;

class CsatStaffController extends Controller {

    protected $columnView;
    protected $columnNeedToShow;
    protected $columnDefault;

    protected $modelSurveySections;
    protected $modelSurveyReports;
    protected $modelSurveyViolations;
    protected $userGranted;
    protected $extraFunc;
    protected $modelStatus;
    protected $modelLocation;
    protected $helpProvider;
    protected $modelSurvey;

    protected $emotions;
    protected $surveyTitle;
    protected $violationTitle;
    protected $punishTitle;

    protected $listRecordChannels;
    protected $listSurveyAvailable;
    protected $listConnectedAvailable;
    protected $listActionAvailable;
    protected $listReportActionAvailable;

    protected $buildDataCsat;
    protected $buildDataCsatExcel;

    protected $sales;
    protected $deployer;
    protected $maintenance;
    protected $modelRecordChannel;
    protected $selNPSImprovement;
    protected $modelDepartment;

    public function __construct() {
        $this->modelSurveySections = new SurveySections();
        $this->extraFunc = new ExtraFunction();

        $this->modelStatus = new OutboundAnswers();
        $this->modelLocation = new Location();
        $this->userGranted = $this->extraFunc->getUserGrantedDetail();
        $this->modelSurveyViolations = new SurveyViolations();
        $this->helpProvider = new HelpProvider();
        $this->buildDataCsat = new BuildDataCSAT();
        $this->buildDataCsatExcel = new BuildDataCSATExcel();
        $this->modelSurvey = new Surveys();

        $this->sales = 'sales';
        $this->deployer = 'deployer';
        $this->maintenance = 'maintenance';
        $this->modelSurveySections = new SurveySections();
        $this->modelRecordChannel = new RecordChannel();
        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9,10,11,12,13,14,15,16,17,18,19]);
        $this->modelDepartment = new Department();

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


        $this->columnView = $this->columnDefault = [
            'section_id' => '',

            'saleName' => 'Sale',
            'saleManPoint' => 'SalePoint',
            'saleManNote' => 'Note',

            'supporterDeploy' => 'Tech',
            'deployStaffPoint' => 'TechPoint',
            'deployStaffNote' => 'Note',

            'supporterMaintenance' => 'Tech',
            'maintenanceStaffPoint' => 'TechPoint',
            'maintenanceStaffNote' => 'Note',

            'section_note' => 'SumNote',
            'section_survey_id' => 'PointOfContact',
            'section_contract_num' => 'Contract',
            'section_branch_code' => 'Branch',
            'section_time_completed' => 'TimeComplete',
            'supporterName' => 'ObjectNameViolation',
            'explanation_desc' => 'Thông tin giải trình của cá nhân/tổ/đơn vị liên quan',
            'qs_verify' => 'Quản lý kiểm chứng',
            'violationsType' => 'Loại lỗi',
            'punishmentDescription' => 'Diễn giải chế tài',
            'punishment' => 'Loại chế tài bổ sung',
            'remedy' => 'Hành động khắc phục với KH',
            'created_user' => 'Người báo cáo',
            'description' => 'Mô tả chi tiết',
            'modify_count' => 'Số lần chỉnh sửa',
            'insert_at' => 'Thời gian làm báo cáo',
            'discipline_ftq' => 'FTQ điều chỉnh, kiểm chứng',
            'punishmentAdditional' => 'Hành động điều chỉnh của FTQ',
            'accept_staff_dont_has_mistake' => 'Xác nhận nhân viên không có lỗi',
        ];

        //Cột cần show của table Triển khai DirectSale - HappyCall
        $dls = [
            'section_id' => null,
            'salename' => null,
            'csat_salesman_point' => null,
            'csat_salesman_note' => null,
            'section_supporter deploy' => null,
            'csat_deployer_point' => null,
            'csat_deployer_note' => null,
            'section_note' => null,
            'section_survey_id' => null,
            'section_contract_num' => null,
            'section_sub_parent_desc' => null,
            'section_location' => null,
            'section_time_completed' => null,
            'supporter_name' => null,
            'explanation_desc' => null,
            'qs_verify' => null,
            'violations_type' => null,
            'punishment_desc' => null,
            'punishment' => null,
            'remedy' => null,
            'created_user' => null,
            'description' => null,
            'modify_count' => null,
            'insert_at' => null,
            'discipline_ftq' => null,
            'punishment_additional' => null,
            'accept_staff_dont_has_mistake' => null,
        ];

        //Cột cần show của table Bảo trì - HappyCall
        $bt = [
            'section_id' => null,
            'section_supporter maintaince' => null,
            'csat_maintenance_staff_point' => null,
            'csat_maintenance_staff_note' => null,
            'section_note' => null,
            'section_survey_id' => null,
            'section_contract_num' => null,
            'section_sub_parent_desc' => null,
            'section_location' => null,
            'section_time_completed' => null,
            'supporter_name' => null,
            'explanation_desc' => null,
            'qs_verify' => null,
            'violations_type' => null,
            'punishment_desc' => null,
            'punishment' => null,
            'remedy' => null,
            'created_user' => null,
            'description' => null,
            'modify_count' => null,
            'insert_at' => null,
            'discipline_ftq' => null,
            'punishment_additional' => null,
            'accept_staff_dont_has_mistake' => null,
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

        $this->emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
        $this->surveyTitle = [1 => 'Sau Triển khai DirectSale', 2 => 'Sau Bảo trì', 3 => 'Sau Thu cước tại nhà', 4 => 'Sau Giao dịch tại quầy',5 => 'HiFPT', 6 => 'Sau Triển khai TeleSale', 7 => 'Sau Thu cước tại nhà', 9 => 'Sau Triển khai Sale tại quầy', 10 => 'Sau Triển khai Swap'];
        $this->listRecordChannels = $this->modelRecordChannel->getAllRecordChannel();
        $this->listSurveyAvailable = $this->modelSurvey->getAllSurvey();
        $this->violationTitle = [
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

            "13" => "Thái độ nhân viên",
            "14" => "Thủ tục, chính sách",
            "15" => "Tốc độ giao dịch",
            "16" => "Không gian quầy giao dịch",
            "17" => "Lý do khác liên quan đến QGD",
            "18" => "Không phải lỗi từ QGD, lỗi từ bộ phận khác",

            "19" => "Thái độ của nhân viên thu cước",
            "20" => "Thao tác thu cước chậm chễ",
            "21" => "Hóa đơn/giấy tờ không đầy đủ",
            "22" => "Sai hẹn",
            "23" => "Nhầm mail (người nhận mail không phải chủ HĐ)",
            "24" => "Khách hàng chọn nhầm đánh giá",
            "25" => "Không phải lỗi từ NVTC, lỗi từ bộ phận khác",
        ];
        $this->punishTitle = [
            '1' => 'Phạt tiền',
            '2' => 'Cảnh cáo/Nhắc nhở',
            '3' => 'Buộc thôi việc',
            '4' => 'Không chế tài',
            '5' => 'Khác',
        ];
    }

    //----------------------------Tổng hợp------------------------------
    public function general(Request $request){
        $info = $request->all();
        $from_date = (empty($info['from_date'])) ?  date('Y-m-d 00:00:00'): $info['from_date'];
        $to_date = (empty($info['to_date'])) ?  date('Y-m-d 23:59:59'): $info['to_date'];
        $region = (empty($info['region'])) ? implode(',',$this->userGranted['region']) : $info['region'];
        $branch = (empty($info['branch'])) ? [implode(',',$this->userGranted['location'])] : [$info['branch']];
        $branchcode = (empty($info['branchcode'])) ? [implode(',',$this->userGranted['branchcode'])] : [$info['branchcode']];
        $viewStatus = (empty($info['viewStatus'])) ? 0 : $info['viewStatus'];

        $result = $this->buildDataCsat->CSATStaffReport($from_date, $to_date, $region, $branch, $branchcode);
        $result['userGranted'] = $this->userGranted;
        $result['branch'] = $result['locations'];
        $result['viewStatus'] = $viewStatus;

        if ($request->isMethod('post')) {
            return view('Csat.CsatStaffGeneralPart1', $result);
        }else{
            return view('Csat.CsatStaffGeneral', $result);
        }
    }
    public function generalExport(Request $request){
        try {
            $info = $request->all();
            $from_date = (empty($info['from_date'])) ?  date('Y-m-d 00:00:00'): $info['from_date'];
            $to_date = (empty($info['to_date'])) ?  date('Y-m-d 23:59:59'): $info['to_date'];
            $region = (empty($info['region'])) ? implode(',',$this->userGranted['region']) : $info['region'];
            $branch = (empty($info['branch'])) ? [implode(',',$this->userGranted['location'])] : [$info['branch']];
            $branchcode = (empty($info['branchcode'])) ? [implode(',',$this->userGranted['branchcode'])] : [$info['branchcode']];
            $viewStatus = (empty($info['viewStatus'])) ? 0 : $info['viewStatus'];

            $result = $this->buildDataCsat->CSATStaffReport($from_date, $to_date, $region, $branch, $branchcode);
            $result['userGranted'] = $this->userGranted;
            $result['branch'] = $result['locations'];
            $result['viewStatus'] = $viewStatus;

            $fileExcel = 'TongQuanNhanVien_' . date('dmY', strtotime($from_date)) . '_' . date('dmY', strtotime($to_date));
            $dataExcel = $result;
            $template = 'Csat.CsatStaffGeneralExcel';
            if($viewStatus == 0){
                $template .= 'Zone';
            }else{
                $template .= 'Branch';
            }

            Excel::create($fileExcel, function($excel) use ($dataExcel, $template) {
                $needObject = $this->buildDataCsatExcel->measureBorderCsatStaffExcel($dataExcel, $template);
                $excel->sheet('Tổng quan', function($sheet) use ($needObject) {
                    $this->buildDataCsatExcel->formatExcelCsatStaffGeneral($sheet,$needObject);
                });
                $excel->getActiveSheet()->getStyle($needObject->columnName[0].$needObject->rowBeginSubject.':'.$needObject->columnName[$needObject->colMaxColTable-1].$needObject->rowEndTable8)->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle($needObject->columnName[0].$needObject->rowBeginSubject.':'.$needObject->columnName[0].$needObject->rowBeginSubject)->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->setAutoSize(false)->setWidth($needObject->columnWidth);
            })->export('xls');
        } catch (\Exception $e) {
            if (env('APP_ENV') == 'local') {
                var_dump($e->getMessage());
            }else{
                return back();
            }
        }
    }

    //----------------------------Chi tiết------------------------------
    public function detail(Request $request) {
        $modelLocation = new Location();
        $listLocation = $modelLocation->getAllLocation();
        $recordPerPage = 50;
        $infoSurvey = $condition = null;
        $userGranted = $this->userGranted;
        $dataPage = [];

        if ($request->isMethod('post') || (isset($request->page) && Session::has('condition'))) {//click vào nút tìm
            if ($request->isMethod('post'))//xóa session nếu có 
                Session::forget('condition');
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            } else {
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;
                //nếu tìm kiếm theo HĐ thì bỏ hết các đk tìm kiếm khác
                if (!empty($condition['contractNum'])) {
                    $arrayKeep = ['contractNum', 'type','departmentType', 'recordPerPage', 'userSurvey', 'allQuestion', 'object', 'channelConfirm'];
                    foreach($condition as $key => $val){
                        if(!in_array($key, $arrayKeep)){
                            $condition[$key] = '';
                        }
                    }
                }
                Session::put('condition', $condition);
            }
            //nếu ktra thấy ko chọn vùng, chi nhánh thì gán lại các vùng, chi nhánh đã được phân cho user
            $condition['region'] = empty($condition['region']) ? $userGranted['region'] : $condition['region'];
            $condition['location'] = empty($condition['location']) ? $userGranted['branchLocationCode'] : $condition['location'];
            $condition['locationSQL'] = [];

            //Điều chỉnh lại location để search hiệu quả
            foreach($condition['location'] as $location){
                $temp = explode('_', $location);
                $condition['locationSQL'][$temp[1]][] = $temp[0];
            }

            $currentPage = !empty($request->page) ? intval($request->page - 1) : 0;
            $count = $this->modelSurveySections->countListSurveyViolations($condition);
            $infoSurvey = $this->modelSurveySections->searchListSurveyViolations($condition, $currentPage);
            dump($infoSurvey);
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
            $dataPage = $this->repairDataForViewIndex($infoSurvey, $condition);

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
        $listRecordChannels = $this->modelRecordChannel->getAllRecordChannel();
        $listDepartmentAvailable = $this->modelDepartment->getDepartmentsByRoleID($userRole['id']);
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
            'userGranted' => !empty($userGranted) ?$userGranted :'',
            'selNPSImprovement' => $this->selNPSImprovement,
            'dataPage' => $dataPage,
            'columnView' => $this->columnView,
            'listRecordChannels' => $listRecordChannels,
            'listDepartmentAvailable' => $listDepartmentAvailable,
            'listSurveyAvailable' => $listSurveyAvailable,
            'listConnectedAvailable' => $listConnectedAvailable,
            'listActionAvailable' => $listActionAvailable,
            'listReportActionAvailable' => $listReportActionAvailable,
        ];

        return view("Csat.CsatStaffDetail", $data);
    }
    public function detailExport(Request $request){
       if ($request->isMethod('post') && Session::token() === Input::get('_token')){
            set_time_limit(0);
            $condition = '';
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            }
            $strCache = md5(json_encode($condition));//mã hóa chuỗi redis key
            $currentPage = 0;
            $condition['recordPerPage'] = 0;//ko cần phân trang 
            $redisKey = 'exportExcelViolations_'.$strCache;
            $dataPage = Redis::get($redisKey);//key redis kq tìm kiếm chi tiết khảo sát
            if(empty($dataPage)){
                //tạo cache
                $infoSurvey = $this->modelSurveySections->searchListSurveyViolations($condition, $currentPage);
                $param['arrayID'] = [];
                $infoSurveyKey = [];
                foreach($infoSurvey as $val){
                    $param['arrayID'][] = $val->section_id;
                    $infoSurveyKey[$val->section_id] = $val;
                }
                $surveyResultModel = new SurveyResult();
                $surveyResults = $surveyResultModel->getSurveyByParam($param);
                $infoSurvey = $this->convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults);
                $dataPage = $this->repairDataForViewViolationExcel($infoSurvey, $condition);
                Redis::set($redisKey, json_encode($dataPage));
                Redis::expire($redisKey, 1800);
            }
            //ktra chuỗi json
            if(is_string($dataPage)){
                $dataPage = json_decode($dataPage);
            }

            //export ra file excel
            Excel::create('ChiTietBaoCaoXuLy_'.date('dmY', strtotime($condition['surveyFrom'])).'_'.date('dmY', strtotime($condition['surveyTo'])), function($excel) use($condition, $dataPage) {
                $excel->sheet('Sheet 1', function($sheet) use($condition, $dataPage) {
                    $sheet->loadView('export_excel.report_violations')
                        ->with('searchCondition', $condition)
                        ->with('selNPSImprovement', $this->selNPSImprovement)
                        ->with('columnView',$this->columnView)
                        ->with('dataPage' , $dataPage);
                });
            })->export('xlsx');
        }
        exit();
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
        $condition['saleName'] = !empty($request->saleName) ? $request->saleName : '';
        $condition['technicalStaff'] = !empty($request->technicalStaff) ? $request->technicalStaff : '';
        $condition['violationsType'] = !empty($request->violationsType) ? $request->violationsType : '';
        $condition['punishment'] = !empty($request->punishment) ? $request->punishment : '';
        $condition['remedy'] = !empty($request->remedy) ? $request->remedy : '';
        $condition['userReported'] = !empty($request->userReported) ? $request->userReported : '';
        $condition['disciplineFTQ'] = !empty($request->disciplineFTQ) ? $request->disciplineFTQ : 0;
        $condition['punishmentAdditional'] = !empty($request->punishmentAdditional) ? $request->punishmentAdditional : '';

        $condition['object'] = !empty($request->object) ? $request->object : '';
        $condition['channelConfirm'] = !empty($request->channelConfirm)?$request->channelConfirm:'';

        $condition['allQuestion'] = $questionNeed;
        return $condition;
    }

    public function repairDataForViewIndex($infoSurvey, $condition){
        $columnView = $this->columnNeedToShow[$condition['departmentType']][$condition['type'].':'.$condition['channelConfirm']];
        foreach($columnView as $key => $val){
            if(empty($val)){
                $columnView[$key] = $this->columnDefault[$key];
            }
        }

        $this->columnView = $columnView;

        $data = [];
        $emotions = $this->emotions;
        $surveyTitle = $this->surveyTitle;
        $violationTitle = $this->violationTitle;
        $punishTitle = $this->punishTitle;

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

        foreach($infoSurvey as $index => $surveySections){
            $dataRow = [];
            foreach ($columnView as $key => $val) {
                switch ($key) {
                    case 'section_supporter deploy':
                        $dataRow[$key] = (!empty($surveySections->section_supporter) ? $surveySections->section_supporter : '') . (!empty($surveySections->section_subsupporter) ? ' ' . $surveySections->section_subsupporter : '');
                        break;
                    case 'section_supporter maintaince':
                        $dataRow[$key] = (!empty($surveySections->section_supporter) ? $surveySections->section_supporter : '') . (!empty($surveySections->section_subsupporter) ? ' ' . $surveySections->section_subsupporter : '');
                        break;
                    case 'section_survey_id':
                        $dataRow[$key] = '<span class="' . $surveySections->section_survey_id . '">' . !empty($surveyTitle[$surveySections->section_survey_id]) ? $surveyTitle[$surveySections->section_survey_id] : '' . '</span>';
                        break;
                    case 'section_sub_parent_desc':
                        $dataRow[$key] = str_replace('Vung', 'Vùng', $surveySections->section_sub_parent_desc);
                        break;
                    case 'section_location':
                        $dataRow[$key] = $allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_branch_code];
                        break;
                    case 'violations_type':
                        $dataRow[$key] = !empty($violationTitle[$surveySections->violations_type]) ? $violationTitle[$surveySections->violations_type] :'';
                        break;
                    case 'punishment':
                        $dataRow[$key] = !empty($punishTitle[$surveySections->punishment]) ?$punishTitle[$surveySections->punishment] :'';
                        break;
                    case 'remedy':
                        $dataRow[$key] = !empty($surveySections->remedy) ?$surveySections->remedy :'Không có';
                        break;
                    case 'insert_at':
                        $dataRow[$key] = date('d-m-Y H:i:s', strtotime($surveySections->insert_at));
                        break;
                    case 'punishment_additional':
                        $dataRow[$key] = !empty($punishTitle[$surveySections->punishment_additional]) ?$punishTitle[$surveySections->punishment_additional] :'';
                        break;
                    case 'accept_staff_dont_has_mistake':
                        $dataRow[$key] = (isset($surveySections->accept_staff_dont_has_mistake))? (($surveySections->accept_staff_dont_has_mistake== 'yes')? 'Có':'Không') : '';
                        break;
                    case 'csat_salesman_point':
                    case 'csat_deployer_point':
                    case 'csat_maintenance_staff_point':
                        $dataRow[$key] = (empty($surveySections->$key)) ? '' : $surveySections->$key;
                        if (!empty($surveySections->$key)) {
                            $dataRow[$key] = "<span><strong><img src='" . asset("assets/img/" . $emotions[$dataRow[$key]]) . "' style='width: 25px;height: 25px' /></strong></span><br/>" . $dataRow[$key];
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
    public function repairDataForViewViolationExcel($infoSurvey, $condition){
        $columnView = $this->columnNeedToShow[$condition['departmentType']][$condition['type'].':'.$condition['channelConfirm']];
        foreach($columnView as $key => $val){
            if(empty($val)){
                $columnView[$key] = $this->columnDefault[$key];
            }
        }

        $this->columnView = $columnView;

        $data = [];
        $surveyTitle = $this->surveyTitle;
        $violationTitle = $this->violationTitle;
        $punishTitle = $this->punishTitle;

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

        foreach($infoSurvey as $index => $surveySections){
            $dataRow = [];
            foreach ($columnView as $key => $val) {
                switch ($key) {
                    case 'section_supporter deploy':
                        $dataRow[$key] = (!empty($surveySections->section_supporter) ? $surveySections->section_supporter : '') . (!empty($surveySections->section_subsupporter) ? ' ' . $surveySections->section_subsupporter : '');
                        break;
                    case 'section_supporter maintaince':
                        $dataRow[$key] = (!empty($surveySections->section_supporter) ? $surveySections->section_supporter : '') . (!empty($surveySections->section_subsupporter) ? ' ' . $surveySections->section_subsupporter : '');
                        break;
                    case 'section_survey_id':
                        $dataRow[$key] = '<span class="' . $surveySections->section_survey_id . '">' . !empty($surveyTitle[$surveySections->section_survey_id]) ? $surveyTitle[$surveySections->section_survey_id] : '' . '</span>';
                        break;
                    case 'section_sub_parent_desc':
                        $dataRow[$key] = str_replace('Vung', 'Vùng', $surveySections->section_sub_parent_desc);
                        break;
                    case 'section_location':
                        $dataRow[$key] = $allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_branch_code];
                        break;
                    case 'violations_type':
                        $dataRow[$key] = !empty($violationTitle[$surveySections->violations_type]) ? $violationTitle[$surveySections->violations_type] :'';
                        break;
                    case 'punishment':
                        $dataRow[$key] = !empty($punishTitle[$surveySections->punishment]) ?$punishTitle[$surveySections->punishment] :'';
                        break;
                    case 'remedy':
                        $remedy = trim($surveySections->$key);
                        while(stripos($remedy, "=") === 0){
                            $remedy = str_replace_first("=","",$remedy);
                        }
                        $dataRow[$key] = !empty($remedy) ?$remedy :'Không có';
                        break;
                    case 'insert_at':
                        $dataRow[$key] = date('d-m-Y H:i:s', strtotime($surveySections->insert_at));
                        break;
                    case 'punishment_additional':
                        $dataRow[$key] = !empty($punishTitle[$surveySections->punishment_additional]) ?$punishTitle[$surveySections->punishment_additional] :'';
                        break;
                    case 'accept_staff_dont_has_mistake':
                        $dataRow[$key] = (isset($surveySections->accept_staff_dont_has_mistake))? (($surveySections->accept_staff_dont_has_mistake== 'yes')? 'Có':'Không') : '';
                        break;
                    case 'csat_salesman_point':
                    case 'csat_deployer_point':
                    case 'csat_maintenance_staff_point':
                    case 'csat_transaction_point':
                    case 'csat_transaction_staff_point':
                    case 'csat_charge_at_home_point':
                    case 'csat_charge_at_home_staff_point':
                        $dataRow[$key] = (empty($surveySections->$key)) ? '' : $surveySections->$key;
                        break;
                    case 'section_note':
                    case 'explanation_desc':
                    case 'qs_verify':
                    case 'punishment_desc':
                        $note = trim($surveySections->$key);
                        while(stripos($note, "=") === 0){
                            $note = str_replace_first("=","",$note);
                        }
                        $dataRow[$key] = $note;
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
        if($condition['type'] == 2){
            $maintenance = '_maintenance';
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
}
