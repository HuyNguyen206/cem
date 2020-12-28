<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests;
use App\Models\SurveySections;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;
use App\Component\ExtraFunction;
use Illuminate\Support\Facades\Auth;
use App\Models\SurveyViolations;
use App\Models\SurveyReport;

class ViolationsService extends Controller {
    
    var $sales;
    var $deployer;
    var $maintenance;
    var $modelSurveySections;
    var $selNPSImprovement;
    public function __construct()
    {
        $this->sales = 'sales';
        $this->deployer = 'deployer';
        $this->maintenance = 'maintenance';
        $this->modelSurveySections = new SurveySections();
        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9,10,11,12,13,14,15,16,17,18,19]);
    }

    public function index(Request $request) {
        $modelLocation = new Location();
        $listLocation = $modelLocation->getAllLocation();
        $recordPerPage = 50;
        $infoSurvey = $condition = null;
        $extraFunc = new ExtraFunction();
        $userGranted = $extraFunc->getUserGranted();
//        var_dump('adasd');die;
        if ($request->isMethod('post') || (isset($request->page) && Session::has('condition'))) {
//click vào nút tìm
            if ($request->isMethod('post'))//xóa session nếu có 
                Session::forget('condition');
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            } else {
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;
                //nếu tìm kiếm theo HĐ thì bỏ hết các đk tìm kiếm khác, trừ đk triển khai hoặc bảo trì.
                if (!empty($condition['contractNum'])) {
                    $condition = array_map(function() {
                        return '';
                    }, $condition); // gán giá trị rỗng cho tất cả value
                    //gán lại giá trị cho tìm kiếm
                    $condition['contractNum'] = !empty($request->contractNum) ? $request->contractNum : '';
                    $condition['type'] = !empty($request->surveyType) ? $request->surveyType : '';
                    $condition['recordPerPage'] = $recordPerPage;
                }
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
//            $count = $this->modelSurveySections->countListSurveyServiceViolations($condition);
            $currentPage = !empty($request->page) ? intval($request->page - 1) : 0;
             $infoSurveyViolationAndTotal=$this->modelSurveySections->getListSurveyViolationAndTotal($condition, $currentPage);
              $count=$infoSurveyViolationAndTotal['total'];
              $infoSurvey=$infoSurveyViolationAndTotal['listSurvey'];
//            $infoSurvey = $this->modelSurveySections->searchListSurveyServiceViolations($condition, $currentPage);           
            $infoSurvey = new LengthAwarePaginator($infoSurvey, $count, $recordPerPage, $request->page, [
                'path' => $request->url(),
                'query' => $request->query()
            ]);

            //gán lại giá trị cho tìm kiếm
            if (Session::has('condition')) {
                $condition = Session::get('condition');
            } else {
                $condition = $this->attachCondition($condition, $request);
                $condition['recordPerPage'] = $recordPerPage;
            }
        }
//        var_dump('sdsd');die;
        return view("report_violations/service", ['modelLocation' => $listLocation,
            'modelSurveySections' => $infoSurvey,
            'searchCondition' => $condition,
            'currentPage' => !empty($currentPage) ? $currentPage : 0,
            'user' => Auth::user(),
            'userGranted' => !empty($userGranted) ?$userGranted :'',
            'selNPSImprovement' => $this->selNPSImprovement]);
    }
    
    
    private function attachCondition($condition, $request) {
        $condition['survey_from'] = !empty($request->survey_from) ? date('Y-m-d 00:00:00', strtotime($request->survey_from)) : date('Y-m-d 00:00:00');
        $condition['survey_to'] = !empty($request->survey_to) ? date('Y-m-d 23:59:59', strtotime($request->survey_to)) : date('Y-m-d 23:59:59');
        $condition['survey_from_int'] = !empty($request->survey_from) ?  strtotime($request->survey_from) : strtotime( date('Y-m-d 00:00:00') ) ;
        $condition['survey_to_int'] = !empty($request->survey_to) ? strtotime($request->survey_to) : strtotime( date('Y-m-d 23:59:59') );
        $condition['region'] = $request->region; //intval($request->region);
        $condition['location'] = $request->location;
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
//        $condition['technicalStaff'] = !empty($request->technicalStaff) ? $request->technicalStaff : '';
//        $condition['violations_type'] = !empty($request->violationsType) ? $request->violationsType : '';
//        $condition['punishment'] = !empty($request->punish) ? $request->punish : '';
//        $condition['discipline'] = !empty($request->discipline) ? $request->discipline : 0;
//        $condition['remedy'] = !empty($request->remedy) ? $request->remedy : '';
//        $condition['userReported'] = !empty($request->userReported) ? $request->userReported : '';
//        $condition['editedReport'] = !empty($request->editedReport) ? $request->editedReport : '';
//        $condition['disciplineFTQ'] = !empty($request->disciplineFTQ) ? $request->disciplineFTQ : 0;
//        $condition['punishAdditional'] = !empty($request->punishAdditional) ? $request->punishAdditional : '';
        return $condition;
    }
    
    public function exportViolations(Request $request){
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
            $infoViolations = Redis::get($redisKey);//key redis kq tìm kiếm chi tiết khảo sát
            if(empty($infoViolations)){
                //tạo cache
                $infoViolations = $this->modelSurveySections->searchListSurveyViolations($condition, $currentPage);
                Redis::set($redisKey, json_encode($infoViolations));
                Redis::expire($redisKey, 1800);
            }
            //ktra chuỗi json
            if(is_string($infoViolations)){
                $infoViolations = json_decode($infoViolations);
            }

//            dump($infoViolations);die;
            //export ra file excel
            Excel::create('ChiTietBaoCaoXuLy_'.date('dmY', strtotime($condition['survey_from'])).'_'.date('dmY', strtotime($condition['survey_to'])), function($excel) use($infoViolations, $condition) {
                $excel->sheet('Sheet 1', function($sheet) use($infoViolations, $condition) {
                    $sheet->loadView('export_excel.report_violations')->with('modelSurveySections', $infoViolations)
                                                                    ->with('searchCondition', $condition)
                                                                    ->with('selNPSImprovement', $this->selNPSImprovement);
                });
            })->export('xlsx');
        }
        exit();
    }
}
