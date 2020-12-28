<?php

namespace App\Http\Controllers\Csat;

use App\Component\BuildDataCSAT;
use App\Component\BuildDataCSATExcel;
use App\Component\HelpProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\SurveySections;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use App\Component\ExtraFunction;
use App\Models\OutboundAnswers;
use App\Models\SurveyReport;
use App\Models\PrecheckList;
use Illuminate\Support\Facades\Auth;
use App\Models\RecordChannel;
use App\Models\FowardDepartment;
use DB;

class CsatServiceController extends Controller {

    protected $modelSurveySections;
    protected $modelSurveyReports;
    protected $userGranted;
    protected $extraFunc;
    protected $modelStatus;
    protected $modelLocation;
    protected $helpProvider;
    protected $buildDataCsat;
    protected $buildDataCsatExcel;
    //Các thông số liên hoàn
    protected $statusesNet;
    protected $statusesTv;
    protected $action;
    protected $statuses;
    var $modelRecordChannel;
    var $selNPSImprovement;
    var $selErrorType;
    var $selProcessingActions;

    public function __construct() {
        $this->extraFunc = new ExtraFunction();
        $this->helpProvider = new HelpProvider();
        $this->modelSurveySections = new SurveySections();
        $this->modelSurveyReports = new SurveyReport();
        $this->modelStatus = new OutboundAnswers();
        $this->modelLocation = new Location();
        $this->userGranted = $this->extraFunc->getUserGranted();
        $this->modelRecordChannel = new RecordChannel();
        $this->statuses = $this->modelStatus->getAnswerByGroup([20, 22], [88]);
        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9, 10, 12, 13, 14, 17, 18, 19]);
        $this->selErrorType = $this->modelSurveySections->getErrorType([20]);
        $this->selProcessingActions = $this->modelSurveySections->getProcessingActions([21]);
        $this->statusesNet = [];
        $this->statusesTv = [];
        $this->action = [
            '1' => 'Không làm gì',
            '2' => 'Tạo Checklist',
            '3' => 'PreChecklist',
            '4' => 'Tạo Checklist INDO',
            '5' => 'Chuyển phòng ban khác',
        ];
        foreach ($this->statuses as $status) {
            if ($status->answer_group == 20) {
                $this->statusesNet[$status->answer_id] = $status;
            } else {
                $this->statusesTv[$status->answer_id] = $status;
            }
        }
        $this->buildDataCsat = new BuildDataCSAT();
        $this->buildDataCsatExcel = new BuildDataCSATExcel();
    }

    //-----------------------Tông hợp---------------------------------------------
    public function general(Request $request) {
        $info = $request->all();
        $from_date = (empty($info['from_date'])) ? date('Y-m-d 00:00:00') : $info['from_date'];
        $to_date = (empty($info['to_date'])) ? date('Y-m-d 23:59:59') : $info['to_date'];
        $region = (empty($info['region'])) ? implode(',', $this->userGranted['region']) : $info['region'];
        $branch = (empty($info['branch'])) ? [implode(',', $this->userGranted['location'])] : [$info['branch']];
        $branchcode = (empty($info['branchcode'])) ? [implode(',', $this->userGranted['branchcode'])] : [$info['branchcode']];
        $viewStatus = (empty($info['viewStatus'])) ? 0 : $info['viewStatus'];

        $result = $this->buildDataCsat->CSATServiceReport($from_date, $to_date, $region, $branch, $branchcode);
        $result['userGranted'] = $this->userGranted;
        $result['branch'] = $result['locations'];
        $result['viewStatus'] = $viewStatus;

        if ($request->isMethod('post')) {
            return view('Csat.CsatServiceGeneralPart1', $result);
        } else {
            return view('Csat.CsatServiceGeneral', $result);
        }
    }

    public function generalExport(Request $request) {
        try {
            $info = $request->all();
            $from_date = (empty($info['from_date'])) ? date('Y-m-d 00:00:00') : $info['from_date'];
            $to_date = (empty($info['to_date'])) ? date('Y-m-d 23:59:59') : $info['to_date'];
            $region = (empty($info['region'])) ? implode(',', $this->userGranted['region']) : $info['region'];
            $branch = (empty($info['branch'])) ? [implode(',', $this->userGranted['location'])] : [$info['branch']];
            $branchcode = (empty($info['branchcode'])) ? [implode(',', $this->userGranted['branchcode'])] : [$info['branchcode']];
            $viewStatus = (empty($info['viewStatus'])) ? 0 : $info['viewStatus'];

            $result = $this->buildDataCsat->CSATServiceReport($from_date, $to_date, $region, $branch, $branchcode);
            $result['userGranted'] = $this->userGranted;
            $result['branch'] = $result['locations'];
            $result['viewStatus'] = $viewStatus;

            $fileExcel = 'TongQuanDichVu_' . date('dmY', strtotime($from_date)) . '_' . date('dmY', strtotime($to_date));
            $dataExcel = $result;
            $template = 'Csat.CsatServiceGeneralExcel';
            if ($viewStatus == 0) {
                $template .= 'Zone';
            } else {
                $template .= 'Branch';
            }

            Excel::create($fileExcel, function ($excel) use ($dataExcel, $template) {
                $needObject = $this->buildDataCsatExcel->measureBorderCsatServiceExcel($dataExcel, $template);
                $excel->sheet('Tổng quan', function ($sheet) use ($needObject, $dataExcel) {
                    $this->buildDataCsatExcel->formatExcelCsatServiceGeneral($sheet, $needObject, $dataExcel['arrayTypeSurvey']);
                });
                $excel->getActiveSheet()->getStyle($needObject->columnName[0] . $needObject->rowBeginSubject . ':' . $needObject->columnName[$needObject->colMaxColTable - 1] . $needObject->rowEndTable6)->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle($needObject->columnName[0] . $needObject->rowBeginSubject . ':' . $needObject->columnName[0] . $needObject->rowBeginSubject)->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->setAutoSize(false)->setWidth($needObject->columnWidth);
            })->export('xls');
        } catch (\Exception $e) {
            if (env('APP_ENV') == 'local') {
                var_dump($e->getMessage());
            } else {
                return back();
            }
        }
    }

    //------------------------Chi tiết--------------------------------------------
    public function detail(Request $request) {
//        dump('sdsd');die;
        $modelLocation = new Location();
        $listLocation = $modelLocation->getAllLocation();
        $recordPerPage = 50;
        $infoSurveyWithActionData = $condition = null;
        $extraFunc = new ExtraFunction();
        $userGranted = $extraFunc->getUserGrantedDetail();
        $dataPage = [];
        if ($request->isMethod('post') || (isset($request->page) && Session::has('rawCondition'))) {//click vào nút tìm
            if ($request->isMethod('post'))//xóa session nếu có 
                Session::forget('rawCondition');
            if (Session::has('rawCondition')) {
                $condition = Session::get('rawCondition');
            } else {
//                $justOnlyLocation = false;
                $condition = $this->attachCondition($condition, $request);
//                if (!empty($condition['location']) && empty($condition['branchcode']) && empty($condition['branchcodeSales'])) {
//                    $justOnlyLocation = true;
//                }
                //nếu ktra thấy ko chọn vùng, chi nhánh thì gán lại các vùng, chi nhánh đã được phân cho user
                $condition['location'] = empty($condition['location']) ? $userGranted['branchLocationCode'] : $condition['location'];
                $locationID = [];
                foreach($condition['location'] as $key2 => $value2)
                {
                    $value2Array = (explode('_', $value2));
                    $value3 = $value2Array[0];
                    array_push($locationID, $value3);
                }
                $condition['location'] = $locationID;
                $condition['recordPerPage'] = $recordPerPage;
//                $condition['justOnlyLocation'] = $justOnlyLocation;
                //nếu tìm kiếm theo HĐ thì bỏ hết các đk tìm kiếm khác, trừ đk triển khai hoặc bảo trì.
                if (!empty($condition['contractNum'])) {
                    $arrayKeep = ['contractNum', 'type', 'departmentType', 'recordPerPage', 'userSurvey', 'sectionGeneralAction','survey_from_int','survey_to_int','survey_from','survey_to'];
                    foreach ($condition as $key => $val) {
                        if (!in_array($key, $arrayKeep)) {
                            $condition[$key] = '';
                        }
                    }
                }
                //edit lại location để search hiệu quả hơn
//                if (!empty($condition['location'])) {
//                    foreach ($condition['location'] as $k => $val) {
//                        if (strpos($val, '_') !== false) {
//                            $val = explode('_', $val);
//                            $condition['location'][$k] = $val[0];
//                        }
//                    }
//                    $condition['location'] = array_unique($condition['location']); //gộp tất cả các location giống nhau thành 1
//                }
                Session::put('rawCondition', $condition);
            }

            //nếu ktra thấy ko chọn vùng, chi nhánh thì gán lại các vùng, chi nhánh đã được phân cho user
//            $condition['region'] = empty($condition['region']) ? (count($condition['region']) == MAX_REGION ? '' : $userGranted['region']) : $condition['region'];
//            $condition['location'] = empty($condition['location']) ? (count($condition['location']) == MAX_BRANCHES ? '' : $userGranted['location']) : $condition['location'];
//            if (empty($condition['branchcode'])) {
//                if (count($condition['branchcode']) == MAX_BRANCHCODE) {
//                    $condition['branchcode'] = '';
//                } else {
//                    $condition['branchcode'] = $userGranted['branchcode'];
//                    //Bổ sung thêm id các chi nhánh để fifter được dữ liệu
//                    array_push($condition['branchcode'], 0);
//                    array_push($condition['branchcode'], 90);
//                }
//            } else {
//                $condition['branchcode'] = $condition['branchcode'];
//            }
//
//            $condition['branchcode'] = array_unique($condition['branchcode']);

//            if (($key = array_search('4', $condition['location'])) !== false) {
//                unset($condition['location'][$key]);
//            }
//            if (($key = array_search('8', $condition['location'])) !== false) {
//                unset($condition['location'][$key]);
//            }
//              Session::put('condition', $condition);
//  var_dump($condition);die;
            $currentPage = !empty($request->page) ? intval($request->page - 1) : 0;
//            DB::enableQueryLog();
//            dump($condition);die;
            $infoSurvey = $this->modelSurveySections->searchListSurveyGeneral($condition, $currentPage);
//            dump(DB::getQueryLog());die;
            $infoSurveyWithActionData = $this->modelSurveySections->attachActionDataToSurvey($infoSurvey['data'], $condition);

            $infoSurveyWithActionData = new LengthAwarePaginator($infoSurveyWithActionData, $infoSurvey['total'], $recordPerPage, $request->page, [
                'path' => $request->url(),
                'query' => $request->query()
            ]);

            //gán lại giá trị cho tìm kiếm
            if (Session::has('rawCondition')) {
                $condition = Session::get('rawCondition');
//               $condition= $rawCondition;
            } else {
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;
            }
        }
        $roleUser = DB::table('role_user')->select('role_id')->where('user_id', Auth::user()->id)->get();
//        $listBrandCodeSaleMan = $modelLocation->getBrandcodeSaleMan();
        $listRecordChannels = $this->modelRecordChannel->getAllRecordChannel();
//        dump($this->selNPSImprovement);
        return view("Csat/CsatServiceDetail", [
            'modelLocation' => $listLocation,
            'modelSurveySections' => $infoSurveyWithActionData,
            'searchCondition' => $condition,
            'currentPage' => !empty($currentPage) ? $currentPage : 0,
            'userGranted' => !empty($userGranted) ? $userGranted : '',
//            'brandcodeSaleMan' => $listBrandCodeSaleMan,
            'user' => Auth::user(),
            'roleUser' => $roleUser,
            'userGranted' => !empty($userGranted) ? $userGranted : '',
            'selNPSImprovement' => $this->selNPSImprovement,
            'selErrorType' => $this->selErrorType,
            'selProcessingActions' => $this->selProcessingActions,
            'listRecordChannels' => $listRecordChannels,
            'page' => $request->page
//            'statuses' => $this->statuses,
//            'statusesNet' => $this->statusesNet,
//            'statusesTv' => $this->statusesTv,
//            'action' => $this->action,
        ]);
    }

    public function detailExport(Request $request) {
        $modelLocation = new Location();
        $listLocation = $modelLocation->getAllLocation();
        $recordPerPage = 50;
        $infoSurvey = $condition = null;
        $extraFunc = new ExtraFunction();
        $userGranted = $extraFunc->getUserGrantedDetail();
        $dataPage = [];
        try {
            if ($request->isMethod('post') && Session::has('rawCondition')) {//click vào nút xuất Excel
                if (Session::has('rawCondition')) {
                    $condition = Session::get('rawCondition');
//                    $condition['region'] = empty($condition['region']) ? (count($condition['region']) == MAX_REGION ? '' : $userGranted['region']) : $condition['region'];
//                    $condition['location'] = empty($condition['location']) ? (count($condition['location']) == MAX_BRANCHES ? '' : $userGranted['location']) : $condition['location'];
//                    if (empty($condition['branchcode'])) {
//                        if (count($condition['branchcode']) == MAX_BRANCHCODE) {
//                            $condition['branchcode'] = '';
//                        } else {
//                            $condition['branchcode'] = $userGranted['branchcode'];
//                            //Bổ sung thêm id các chi nhánh để fifter được dữ liệu
//                            array_push($condition['branchcode'], 0);
//                            array_push($condition['branchcode'], 90);
//                        }
//                    } else {
//                        $condition['branchcode'] = $condition['branchcode'];
//                    }
//
//                    $condition['branchcode'] = array_unique($condition['branchcode']);
                }
                $nameFile = 'ChiTietBaoCaoXuLy';
                //Gán ngày nếu có
                if (isset($condition['survey_from'])) {
                    $nameFile .= '_' . date('dmY', strtotime($condition['survey_from']));
                }
                if (isset($condition['survey_to'])) {
                    $nameFile .= '_' . date('dmY', strtotime($condition['survey_to']));
                }
                $count = $this->modelSurveySections->countListSurveyGeneral($condition);
//                $currentPage = 0;
                $condition['recordPerPage'] = 1000; //ko cần phân trang
                $remain = $count % $condition['recordPerPage'];
                $numPage = ($count - $remain) / $condition['recordPerPage'];
                if ($remain != 0) {
                    $numPage = $numPage + 1;
                }
                $listFileExel = [];
                for ($i = 0; $i < $numPage; $i++) {
                    $nameExport = $nameFile;
                    $nameExport.= strtotime(date('y-m-d h:i:s'));
                    $infoSurvey = $this->modelSurveySections->searchListSurveyGeneral($condition, $i);
                    $infoSurveyWithActionData = $this->modelSurveySections->attachActionDataToSurvey($infoSurvey['data'], $condition);
                    $PathExcel = Excel::create($nameExport, function ($excel) use ($infoSurveyWithActionData, $condition) {
                        $excel->sheet('Sheet 1', function ($sheet) use ($infoSurveyWithActionData, $condition) {
                            $sheet->loadView('Csat.CsatServiceDetailExcel')
                                ->with('modelSurveySections', $infoSurveyWithActionData)
                                ->with('searchCondition', $condition);
                        });
                    })->store('xlsx', storage_path('app/public'), true);
                    array_push($listFileExel, $PathExcel['file']);
                }
            }
            return view("report/reportDownload", ['listFileExel' => $listFileExel])->render();
        } catch (\Exception $e) {
            if (env('APP_ENV') == 'local') {
                var_dump($e->getMessage());
            } else {
                return back();
            }
        }
    }

    private function attachCondition($condition, $request) {
//        $outQuestionModel = new OutboundQuestions();
//        $allQuestions = $outQuestionModel->getAllQuestion();
//        $questionNeed = [];
//        foreach($allQuestions as $question){
//            if(isset($questionNeed[$question->question_alias])){
//                array_push($questionNeed[$question->question_alias], $question->question_id);
//            }else{
//                $questionNeed[$question->question_alias] = [$question->question_id];
//            }
//        }
        $condition['survey_from'] = !empty($request->survey_from) ? date('Y-m-d 00:00:00', strtotime($request->survey_from)) : date('Y-m-d 00:00:00');
        $condition['survey_to'] = !empty($request->survey_to) ? date('Y-m-d 23:59:59', strtotime($request->survey_to)) : date('Y-m-d 23:59:59');

        $condition['survey_from_int'] = !empty($request->survey_from) ? strtotime($request->survey_from) : strtotime(date('Y-m-d 00:00:00'));
        $condition['survey_to_int'] = !empty($request->survey_to) ? strtotime($request->survey_to . '  23:59:59') : strtotime(date('Y-m-d 23:59:59'));
//        $condition['region'] = $request->region; //intval($request->region);
        $condition['location'] = $request->location;
//        $condition['locationSales'] = $request->locationSales;
//        $condition['branchcode'] = [];
//        $condition['branchcodeSalesMan'] = [];
        //nếu chọn các chi nhánh con của HNI hoặc HCM
//        if (!empty($condition['location'])) {
//            foreach ($condition['location'] as $val) {
//                if (strpos($val, '_') !== false) {
//                    $branchcode = explode('_', $val);
//                    array_push($condition['branchcode'], (int) $branchcode[1]);
////                    array_push($condition['branchcode'], 0); //bổ sung branchcode 0 cho trường hợp chọn các chi nhánh khác ngoài HNI & HCM
//                }
//            }
//        }

//        $condition['branchcode'] = array_unique($condition['branchcode']);
//        $condition['brandcodeSaleMan'] = isset($request->brandcodeSaleMan) ? array_unique($request->brandcodeSaleMan) : '';
        $condition['contractNum'] = !empty($request->contractNum) ? $request->contractNum : '';
        $condition['type'] = !empty($request->surveyType) ? $request->surveyType : '';
//        $condition['section_action'] = !empty($request->processingSurvey) ? $request->processingSurvey : '';
        $condition['section_connected'] = !empty($request->surveyStatus) ? $request->surveyStatus : '';
//        $condition['CSATPointSale'] = !empty($request->CSATPointSale) ? $request->CSATPointSale : '';
//        $condition['CSATPointNVTK'] = !empty($request->CSATPointNVTK) ? $request->CSATPointNVTK : '';
//        $condition['CSATPointBT'] = !empty($request->CSATPointBT) ? $request->CSATPointBT : '';
        $condition['CSATPointNet'] = !empty($request->CSATPointNet) ? $request->CSATPointNet : '';
//        $condition['CSATPointTV'] = !empty($request->CSATPointTV) ? $request->CSATPointTV : '';
        $condition['userSurvey'] = !empty($request->user_survey) ? $request->user_survey : '';
        $condition['RateNPS'] = !empty($request->RateNPS) ? $request->RateNPS : '';
        $condition['NPSPoint'] = !empty($request->NPSPoint) ? $request->NPSPoint : '';
        $condition['departmentType'] = !empty($request->departmentType) ? $request->departmentType : '';
        $condition['salerName'] = !empty($request->salerName) ? $request->salerName : '';
        $condition['technicalStaff'] = !empty($request->technicalStaff) ? $request->technicalStaff : '';
//        $condition['reportedStatus'] = !empty($request->reportedStatus) ? $request->reportedStatus : '';
        $condition['NetErrorType'] = !empty($request->NetErrorType) ? $request->NetErrorType : '';
//        $condition['TVErrorType'] = !empty($request->TVErrorType) ? $request->TVErrorType : '';
//        $condition['processingActionsTV'] = !empty($request->processingActionsTV) ? $request->processingActionsTV : '';
        $condition['processingActionsInternet'] = !empty($request->processingActionsInternet) ? $request->processingActionsInternet : '';
        $condition['sectionGeneralAction'] = isset($request->sectionGeneralAction) ? $request->sectionGeneralAction : '';
//        $condition['allQuestion'] = $questionNeed;
        return $condition;
    }

//    public function getChecklistInfo(Request $request) {
//        if ($request->ajax() && Session::token() === Input::get('_token')) {
//            $data = Input::all();
//            $result = DB::table('checklist')->select('*')->where('id_checklist_isc', $data['id'])->get();
//            echo view('Csat/checklistInfo', ['checklistInfo' => $result])->render();
//        }
//        exit();
//    }

    public function getChecklistInfo(Request $request) {
        if ($request->ajax() && Session::token() === Input::get('_token')) {
            $data = Input::all();
            $input = explode('-', $data['data']);
            $type = $data['type'];
            //Thong tin Prechecklist
            if ($type == 3) {
                $preCL = new PrecheckList();
                $result = $preCL->getPreCLWithCL($input);
                echo view('Csat/checklistInfo', ['result' => $result, 'type' => $type])->render();
            } else {
                $fd = new FowardDepartment();
                $result = $fd->getFD($input);
                echo view('Csat/checklistInfo', ['result' => $result, 'type' => $type])->render();
            }
//            $contract = !empty($data['contract']) ? $data['contract'] : '';
//            $phone = !empty($data['phone']) ? $data['phone'] : '';
//            $modelDetailSurveyResult = new SurveySections();
//            $detail = $modelDetailSurveyResult->getAllDetailSurveyInfo($data['survey']);
//            echo view('report_history/detailSurvey', ['detail' => $detail, 'contract' => $contract, 'phone' => $phone])->render();
        }
        exit();
    }

}
