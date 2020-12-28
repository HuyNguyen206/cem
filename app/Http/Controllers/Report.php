<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests;
use App\Models\SurveySections;
use App\Models\SurveyReport;
use App\Models\Location;
use App\Models\SummaryCsat;
use App\Models\SummaryNps;
use App\Component\ExtraFunction;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelDashboardController;
use App\Http\Controllers\ExcelReportController;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use PHPExcel_Style_Border;
use Illuminate\Support\Facades\Auth;
use File;

class Report extends Controller {

    protected $modelSurveySections;
    protected $extraFunc;
    protected $userGranted;
    protected $location;

    public function __construct() {
        $location = new Location();
        $this->modelSurveySections = new SurveySections();
        $this->extraFunc = new ExtraFunction();
        $this->location = $location->getAllLocationByPermission( Auth::user()->id);
    }

    public function index() {
        $from_date = date('Y-m-d 00:00:00'); //bắt đầu ngày hôm nay
        $to_date = date('Y-m-d 23:59:59'); //ngày hôm nay
        //ktra nếu số lượng vùng hoặc chi nhánh full, thì đk tìm kiếm liên quan ko thêm vào query, nhằm tối ưu query db
//        $region = (count($this->userGranted['region']) == MAX_REGION) ? '' : implode(',', $this->userGranted['region']);
//        $branch = (count($this->userGranted['location']) == MAX_BRANCHES) ? [] : [implode(',', $this->userGranted['location'])];
//        $branchcode = (count($this->userGranted['branchcode']) == MAX_BRANCHCODE) ? [] : [implode(',', $this->userGranted['branchcode'])];

//        $view = $this->CSATReport(1, $region, $from_date, $to_date, $branch, $branchcode, $this->userGranted);
        $locationID = $locationSelected = [];
        $view = $this->CSATReport(1, $from_date, $to_date, $this->location, $locationID, $locationSelected );
        return $view[0];
    }

    public function detail_report(Request $request) {
        if ($request->ajax() && Session::token() === Input::get('_token')) {
            $data = Input::all();
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $locationResult = $this->getCorrectLocation($data);
            $locationID = $locationResult['locationID'];
            $locationSelected = $locationResult['locationSelected'];
            switch ($data['type']) {
                case 0:
                    $result = $this->CSATReport(2, $from_date, $to_date, [], $locationID, $locationSelected);
                    $view = $result[0];
                    break;
                case 1:
                    $surveySection = new SurveySections();
                    $result1 = $this->NPSStatisticReport($from_date, $to_date, $locationID, $locationSelected);
                    $result2 = $surveySection->getCustommerCommentReport($from_date, $to_date, $locationID, $locationSelected, 1);
                    $view = $result1[0];
                    $view .= $result2[0];

                    break;
                case 2:
                    $result = $this->generalPointReport($from_date, $to_date, $locationID, $locationSelected);
                    $view = $result[0];
                    break;
                case 3:
                    $result = $this->SurveyQuantityReport($from_date, $to_date, $locationID);
                    $view = $result[0];
                    break;
                case 4://năng suất nhân viên
//                    $result = $this->productivityReport($region, $from_date, $to_date, $branch, $branchcode);
                    $result = $this->productivityReport($from_date, $to_date, $locationID);
                    $view = $result[0];
                    break;
//                case 5://Số lượng email, khách hàng, phản hồi, giao dịch tại quầyn
//                    $result = $this->transactionReport($region, $from_date, $to_date, $branch, $branchcode);
//                    $view = $result[0];
//                    break;
            }
            return $view;
        }
        exit();
    }

    public function SurveyQuantityReport($from_date, $to_date, $locationID) {
        $modelSurveySections = new SurveySections();
        $survey = $modelSurveySections->getSumSurvey($from_date, $to_date, $locationID); //lấy thông tin kết quả survey
        $total = $totalConnectedCus = $totalNoRated = ['SauTK' => 0, 'SauBT' => 0, 'TongCong' => 0];
        //lấy tổng các thông số KQ Survey
        foreach ($survey as $report) {
            $total['SauTK'] += intval($report->SauTK);
            $total['SauBT'] += intval($report->SauBT);
            $total['TongCong'] += intval($report->TongCong);
            //
            if ($report->KQSurvey == 4) {//gặp người sử dụng
                $totalConnectedCus['SauTK'] = $report->SauTK;
                $totalConnectedCus['SauBT'] = $report->SauBT;
                $totalConnectedCus['TongCong'] = $report->TongCong;
            }
        }
        //surveyNPS
        $surveyNPS = $modelSurveySections->getSumSurveyNPS($from_date, $to_date, $locationID);
        $surveyNPSNoRated = $modelSurveySections->getSumSurveyNPSNoRated($from_date, $to_date, $locationID);
        $surveyNPSNoRated_Note = $modelSurveySections->getSumSurveyNPSNoRated_Note($from_date, $to_date, $locationID);
        //total survey NPS
        $totalNPS = ['SauTK' => 0, 'SauBT' => 0, 'TongCong' => 0];
        //lấy tổng các thông số KQ Survey NPS
        foreach ($surveyNPS as &$nps) {
            $nps->SauTK = intval($nps->SauTK);
            $nps->SauBT = intval($nps->SauBT);
            $nps->TongCong = intval($nps->TongCong);
            $totalNPS['SauTK'] += $nps->SauTK;
            $totalNPS['SauBT'] += $nps->SauBT;
            $totalNPS['TongCong'] += $nps->TongCong;
        }

        foreach ($surveyNPSNoRated as $npsNorating) {
            $totalNPS['SauTK'] += intval($npsNorating->SauTK);
            $totalNPS['SauBT'] += intval($npsNorating->SauBT);
            $totalNPS['TongCong'] += intval($npsNorating->TongCong);
        }

        foreach ($surveyNPSNoRated_Note as $npsNorating_Note) {
            $totalNPS['SauTK'] += intval($npsNorating_Note->SauTK);
            $totalNPS['SauBT'] += intval($npsNorating_Note->SauBT);
            $totalNPS['TongCong'] += intval($npsNorating_Note->TongCong);
        }
        //KH đã đánh giá NPS, ko hỏi lại (TH trong 90 ngày ko hỏi lại NPS)
        $totalNoRated['SauTK'] = $totalConnectedCus['SauTK'] - $totalNPS['SauTK'];
        $totalNoRated['SauBT'] = $totalConnectedCus['SauBT'] - $totalNPS['SauBT'];
        $totalNoRated['TongCong'] = $totalConnectedCus['TongCong'] - $totalNPS['TongCong'];
        $totalNPS['SauTK'] += intval($totalNoRated['SauTK']);
        $totalNPS['SauBT'] += intval($totalNoRated['SauBT']);
        $totalNPS['TongCong'] += intval($totalNoRated['TongCong']);
        $param=['survey' => $survey, 'total' => $total, 'surveyNPS' => $surveyNPS, 'totalNPS' => $totalNPS, 'totalNoRated' => $totalNoRated,
                'surveyNPSNoRated' => $surveyNPSNoRated, 'surveyNPSNoRated_Note' => $surveyNPSNoRated_Note, 'region' => '', 'from_date' => $from_date, 'to_date' => $to_date, 'viewFrom' => 1];
        return [0 => view("report/detailReport",$param)->render(), 1=>$param];
    }

    private function CSATReport($init = 1, $from_date, $to_date, $location , $locationID, $locationSelected,  $typeChild = 'all') {
        $modelSurveySections = new SurveySections();
        $summaryCsat = new SummaryCsat();
//        $hasCurrentDay = strtotime(date('y-m-d')) >= strtotime($from_date) && strtotime(date('y-m-d')) <= strtotime($to_date);
        //Lấy tất cả dữ liệu hoặc chỉ lấy csat nhân viên theo vùng, chi nhánh
        if ($typeChild == 'all' || $typeChild == 3) {
            $key = trim('3' . implode('_', $locationID) . '_' . explode(' ', $from_date)[0] . '_' . explode(' ', $to_date)[0]);
            if ($typeChild == 'all') {
                $surveyCSAT12 =$modelSurveySections->getCSAT12($from_date, $to_date, $locationID); //lấy thông tin CSAT nhân viên 1,2
                Redis::set($key, json_encode($surveyCSAT12));
                Redis::expire($key, 7200);
            } else if ($typeChild == 3) {
                $surveyCSAT12 = Redis::get($key);
                if (!empty($surveyCSAT12)) {
                    $surveyCSAT12 = (array)json_decode($surveyCSAT12);
                } else {
                    $surveyCSAT12 =  $modelSurveySections->getCSAT12($from_date, $to_date, $locationID) ; //lấy thông tin CSAT nhân viên 1,2
                }

                return [0 => ['surveyCSAT12' => isset($surveyCSAT12) ? $surveyCSAT12 : null, 'from_date' => $from_date, 'to_date' => $to_date, 'locationSelected' => $locationSelected]];
            }
        }

        //Lấy tất cả dữ liệu hoặc chỉ lấy csat dịch vụ theo vùng, chi nhánh
        if ($typeChild == 'all' || $typeChild == 2) {
            $key = trim('2' . implode('_', $locationID) . '_' . explode(' ', $from_date)[0] . '_' . explode(' ', $to_date)[0]);
            if ($typeChild == 'all') {
                $surveyCSATService12 = $modelSurveySections->getCSATService12($from_date, $to_date, $locationID) ; //lấy thông tin CSAT dịch vụ 1,2
                Redis::set($key, json_encode($surveyCSATService12));
                Redis::expire($key, 7200);
            } else if ($typeChild == 2) {
                $surveyCSATService12 = Redis::get($key);
                if (!empty($surveyCSATService12)) {
                    $surveyCSATService12 = (array)json_decode($surveyCSATService12);
                } else {
                    $surveyCSATService12 = $modelSurveySections->getCSATService12($from_date, $to_date, $locationID) ; //lấy thông tin CSAT dịch vụ 1,2
                }

                return [0 => ['surveyCSATService12' => isset($surveyCSATService12) ? $surveyCSATService12 : null, 'from_date' => $from_date, 'to_date' => $to_date, 'locationSelected' => $locationSelected]];
            }
        }



        $survey =  $modelSurveySections->getCSATInfo($from_date, $to_date, $locationID); //lấy thông tin CSAT
        //Có chứa ngày hiện tại thì lấy câu cũ, ko thì lấy trong SummaryCsat
//        $survey = ($hasCurrentDay) ? $modelSurveySections->getCSATInfo($region, $from_date, $to_date, $branch, $branchcode) : $summaryCsat->getCSATInfo($region, $from_date, $to_date, $branch, $branchcode); //lấy thông tin CSAT
        $surveyCSATActionService12 = $modelSurveySections->CSATActionService12($from_date, $to_date, $locationID); //lấy thông tin CSAT hành động xử lý dịch vụ 1,2

        $surveyCSATBranchData = $modelSurveySections->getCSATByBranch($from_date, $to_date, $locationID);
        $surveyCSATBranch = $this->getCsatBranchPercent($surveyCSATBranchData);
        $totalCSATActionService12 = ['action' => 'Total', 'INTERNET_CSAT_12' => 0, 'INTERNET_SBT_CSAT_12' => 0, 'TOTAL_INTERNET_CSAT_12' => 0];
        foreach ($surveyCSATActionService12 as $key => $value) {
            $totalCSATActionService12['INTERNET_CSAT_12']+=$value->INTERNET_CSAT_12;
            $totalCSATActionService12['INTERNET_SBT_CSAT_12']+=$value->INTERNET_SBT_CSAT_12;
            $totalCSATActionService12['TOTAL_INTERNET_CSAT_12']+=$value->TOTAL_INTERNET_CSAT_12;
        }
        $totalCSATActionService12 = (object) $totalCSATActionService12;
        array_push($surveyCSATActionService12, $totalCSATActionService12);
//        $total = $t = $avg = ['NVKinhDoanh' => 0, 'NVTrienKhai' => 0, 'DGDichVu_Net' => 0, 'DGDichVu_TV' => 0, 'NVKinhDoanhTS' => 0, 'NVTrienKhaiTS' => 0, 'DGDichVuTS_Net' => 0, 'DGDichVuTS_TV' => 0, 'NVBaoTriTIN' => 0, 'NVBaoTriINDO' => 0, 'DVBaoTriTIN_Net' => 0, 'DVBaoTriTIN_TV' => 0, 'DVBaoTriINDO_Net' => 0, 'DVBaoTriINDO_TV' => 0, 'NVThuCuoc' => 0, 'DGDichVu_MobiPay_Net' => 0, 'DGDichVu_MobiPay_TV' => 0, 'DGDichVu_Counter' => 0, 'NV_Counter' => 0, 'NVKinhDoanhSS' => 0, 'NVTrienKhaiSS' => 0, 'DGDichVuSS_Net' => 0, 'DGDichVuSS_TV' => 0, 'NVBT_SSW' => 0, 'DGDichVuSSW_Net' => 0, 'DGDichVuSSW_TV' => 0];
        $total = $t = $avg = ['NVKinhDoanh' => 0, 'NVTrienKhai' => 0, 'DGDichVu_Net' => 0, 'NVBaoTri' => 0,'DVBaoTri_Net' => 0];
        //lấy tổng các thông số đánh giá điểm CSAT
        foreach ($survey as $report) {
            $total['NVKinhDoanh'] += $report->NVKinhDoanh;
            $t['NVKinhDoanh'] += $report->NVKinhDoanh * $report->answers_point;
            $total['NVTrienKhai'] += $report->NVTrienKhai;
            $t['NVTrienKhai'] += $report->NVTrienKhai * $report->answers_point;
            $total['DGDichVu_Net'] += $report->DGDichVu_Net;
            $t['DGDichVu_Net'] += $report->DGDichVu_Net * $report->answers_point;


            $total['NVBaoTri'] += $report->NVBaoTri;
            $t['NVBaoTri'] += $report->NVBaoTri * $report->answers_point;
            $total['DVBaoTri_Net'] += $report->DVBaoTri_Net;
            $t['DVBaoTri_Net'] += $report->DVBaoTri_Net * $report->answers_point;

        }
        //điểm trung bình cộng
        foreach ($total as $k => $val) {
            if ($val > 0) {
                $avg[$k] = round($t[$k] / $val, 2);
            }
        }
        //bổ sung các điểm còn thiếu, nếu = 0 vẫn cho show ra màn hình
        $arr = $arr1 = [];
        foreach ($survey as $val) {
            $arr['arr' . $val->answers_point] = $val;
        }
        $tempRate = [1 => 'VeryUnsatisfaction', 2 => 'Unsatisfaction', 3 => 'Neutral', 4 => 'Satisfaction', 5 => 'VerySatisfaction'];
        for ($i = 1; $i <= 5; $i++) {
            $obj = new \stdClass();
            $obj->answers_point = $i;
//            $obj->NVKinhDoanh = $obj->NVTrienKhai = $obj->DGDichVu_Net = $obj->DGDichVu_TV = $obj->NVKinhDoanhTS = $obj->NVTrienKhaiTS = $obj->DGDichVuTS_Net = $obj->DGDichVuTS_TV = $obj->DVBaoTriTIN_Net = $obj->NVBaoTriTIN = $obj->DVBaoTriTIN_TV = $obj->NVBaoTriINDO = $obj->DVBaoTriINDO_Net = $obj->DVBaoTriINDO_TV = $obj->NVThuCuoc = $obj->DGDichVu_MobiPay_Net = $obj->DGDichVu_MobiPay_TV = $obj->DGDichVu_Counter = $obj->NV_Counter = $obj->NVKinhDoanhSS = $obj->NVTrienKhaiSS = $obj->DGDichVuSS_Net = $obj->DGDichVuSS_TV = $obj->NVBT_SSW = $obj->DGDichVuSSW_Net = $obj->DGDichVuSSW_TV = 0;
            $obj->NVKinhDoanh = $obj->NVTrienKhai = $obj->DGDichVu_Net = $obj->DVBaoTri_Net = $obj->NVBaoTri  = 0;
            $obj->DanhGia = $tempRate[$i];
            $arr1['arr' . $i] = $obj;
        }
        $survey = array_values(array_merge($arr1, $arr));
        $totalResult = $this->getCsatByObject($survey);
        if ($init === 1) {//
            return [0 => view("report/index", ['survey' => $survey, 'totalCSAT' => $totalResult['totalCSAT'], 'surveyCSATBranch' => $surveyCSATBranch, 'averagePoint' => $totalResult['averagePoint'], 'total' => $total, 'avg' => $avg, 'surveyCSAT12' => $surveyCSAT12, 'surveyCSATService12' => $surveyCSATService12, 'surveyCSATActionService12' => $surveyCSATActionService12,  'from_date' => $from_date, 'to_date' => $to_date, 'location' => $location, 'locationSelected' => $locationSelected])];
        } else {
            return [0 => view("report/csatReport", ['survey' => $survey, 'totalCSAT' => $totalResult['totalCSAT'], 'surveyCSATBranch' => $surveyCSATBranch, 'averagePoint' => $totalResult['averagePoint'], 'total' => $total, 'avg' => $avg, 'surveyCSAT12' => $surveyCSAT12, 'surveyCSATService12' => $surveyCSATService12, 'surveyCSATActionService12' => $surveyCSATActionService12,   'from_date' => $from_date, 'to_date' => $to_date , 'locationSelected' => $locationSelected])->render(),
                1=> ['survey' => $survey, 'totalCSAT' => $totalResult['totalCSAT'], 'surveyCSATBranch' => $surveyCSATBranch, 'averagePoint' => $totalResult['averagePoint'], 'total' => $total, 'avg' => $avg, 'surveyCSAT12' => $surveyCSAT12, 'surveyCSATService12' => $surveyCSATService12, 'surveyCSATActionService12' => $surveyCSATActionService12,  'from_date' => $from_date, 'to_date' => $to_date , 'locationSelected' => $locationSelected]];
            ;
        }
    }



    private function NPSStatisticReport($from_date, $to_date, $locationID, $locationSelected) {
        $modelSurveySections = new SurveySections();
        $summaryNps = new SummaryNps;
        //Có chứa ngày hiện tại thì lấy câu cũ, ko thì lấy trong SummaryCsat
//        $survey = ($hasCurrentDay) ? $modelSurveySections->getNPSStatisticReport($region, $from_date, $to_date, $branch, $branchcode) : $summaryNps->getNPSStatisticReport($region, $from_date, $to_date, $branch, $branchcode); //lấy thông tin CSAT
        $survey =$modelSurveySections->getNPSStatisticReport($from_date, $to_date, $locationID) ;
        $total = $newSurvey1 = $newSurvey2 = $newSurvey3 = ['SauTK' => 0, 'SauBT' => 0, 'Total' => 0];
        $newSurvey1['type'] = trans('report.Unsupported');
        $newSurvey2['type'] = trans('report.NeutralNPS');
        $newSurvey3['type'] = trans('report.Supported');
        //lấy tổng các thông số Thống kê điểm NPS
        foreach ($survey as $report) {
            if ($report->answers_point >= 0 && $report->answers_point <= 6) {
                $newSurvey1['SauTK'] += $report->SauTK;
                $newSurvey1['SauBT'] += $report->SauBT;
                $newSurvey1['Total'] += $report->Total;
            } else if ($report->answers_point >= 7 && $report->answers_point <= 8) {
                $newSurvey2['SauTK'] += $report->SauTK;
                $newSurvey2['SauBT'] += $report->SauBT;
                $newSurvey2['Total'] += $report->Total;
            } else if ($report->answers_point >= 9 && $report->answers_point <= 10) {
                $newSurvey3['SauTK'] += $report->SauTK;
                $newSurvey3['SauBT'] += $report->SauBT;
                $newSurvey3['Total'] += $report->Total;
            }
            //tổng
            $total['SauTK'] += $report->SauTK;
            $total['SauBT'] += $report->SauBT;
            $total['Total'] += $report->Total;
        }
        $groupNPS = [];
        $groupNPS[] = $newSurvey1;
        $groupNPS[] = $newSurvey2;
        $groupNPS[] = $newSurvey3;
        //bổ sung các điểm còn thiếu
        $arr = $arr1 = [];
        foreach ($survey as $val) {
            $arr['arr' . $val->answers_point] = $val;
        }

        for ($i = 0; $i <= 10; $i++) {
            $obj = new \stdClass();
            $obj->answers_point = $i;
            $obj->SauTK = $obj->SauBT = $obj->Total = 0;
            $arr1['arr' . $i] = $obj;
        }
        $survey = array_values(array_merge($arr1, $arr));

        $surveyBranchData =$modelSurveySections->getNPSStatisticBranchReport($from_date, $to_date, $locationID);

        $surveyBranch = $this->getNPSStatisticBranchPercent($surveyBranchData);

        $param=['survey' => $survey, 'surveyBranch' => $surveyBranch, 'groupNPS' => $groupNPS, 'total' => $total, 'locationSelected' => $locationSelected, 'from_date' => $from_date, 'to_date' => $to_date, 'flagView' => 1];
        return [0 => view("report/npsStatisticReport",$param)->render(), 1=>$param];
    }

    private function NPSReport($region, $from_date, $to_date, $branch, $branchcode) {
        $modelSurveySections = new SurveySections();
        $survey = $modelSurveySections->getNPSStatisticReport($region, $from_date, $to_date, $branch, $branchcode); //lấy thông tin độ ủng hộ NPS
        $total = $newSurvey1 = $newSurvey2 = $newSurvey3 = ['SauTK' => 0, 'SauBTTIN' => 0, 'SauBTINDO' => 0, 'SauTC' => 0, 'TongCong' => 0];
        $newSurvey1['type'] = trans('report.Unsupported');
        $newSurvey2['type'] = trans('report.Neutral');
        $newSurvey3['type'] = trans('report.Supported');
        //lấy tổng các thông số độ ủng hộ NPS
        foreach ($survey as $report) {
            if ($report->answers_point >= 0 && $report->answers_point <= 6) {
                $newSurvey1['SauTK'] += $report->SauTK;
                $newSurvey1['SauBTTIN'] += $report->SauBTTIN;
                $newSurvey1['SauBTINDO'] += $report->SauBTINDO;
                $newSurvey1['SauTC'] += $report->SauTC;
                $newSurvey1['TongCong'] += $report->TongCong;
            } else if ($report->answers_point >= 7 && $report->answers_point <= 8) {
                $newSurvey2['SauTK'] += $report->SauTK;
                $newSurvey2['SauBTTIN'] += $report->SauBTTIN;
                $newSurvey2['SauBTINDO'] += $report->SauBTINDO;
                $newSurvey2['SauTC'] += $report->SauTC;
                $newSurvey2['TongCong'] += $report->TongCong;
            } else if ($report->answers_point >= 9 && $report->answers_point <= 10) {
                $newSurvey3['SauTK'] += $report->SauTK;
                $newSurvey3['SauBTTIN'] += $report->SauBTTIN;
                $newSurvey3['SauBTINDO'] += $report->SauBTINDO;
                $newSurvey3['SauTC'] += $report->SauTC;
                $newSurvey3['TongCong'] += $report->TongCong;
            }
            $total['SauTK'] += $report->SauTK;
            $total['SauBTTIN'] += $report->SauBTTIN;
            $total['SauBTINDO'] += $report->SauBTINDO;
            $total['SauTC'] += $report->SauTC;
            $total['TongCong'] += $report->TongCong;
        }
        $newSurvey = [];
        $newSurvey[] = $newSurvey1;
        $newSurvey[] = $newSurvey2;
        $newSurvey[] = $newSurvey3;
        return [0 => view("report/npsReport", ['survey' => $newSurvey, 'total' => $total, 'region' => $region, 'from_date' => $from_date, 'to_date' => $to_date]), 1 => ['survey' => $newSurvey, 'total' => $total]];
    }

    private function generalPointReport($from_date, $to_date, $locationID, $locationSelected) {
        $modelSurveySections = new SurveySections();
        $extraFunc = new ExtraFunction();
        $summaryCsat = new SummaryCsat();
        $summaryNps = new SummaryNps();
//        $hasCurrentDay = strtotime(date('y-m-d')) >= strtotime($from_date) && strtotime(date('y-m-d')) <= strtotime($to_date);
        //Có chứa ngày hiện tại thì lấy câu cũ, ko thì lấy trong SummaryCsat
//        $survey = ($hasCurrentDay) ? $modelSurveySections->getCSATInfoByRegion($region, $from_date, $to_date, $select_branch) : $summaryCsat->getCSATInfoByRegion($region, $from_date, $to_date, $select_branch); //lấy thông tin CSAT
        $survey = $modelSurveySections->getCSATInfoByBranches($from_date, $to_date, $locationID, $locationSelected) ; //lấy thông tin CSAT
//        $surveyRegion = ($hasCurrentDay) ? $modelSurveySections->getNPSStatisticReportByRegion($region, $from_date, $to_date, $select_branch) : $summaryNps->getNPSStatisticReportByRegion($region, $from_date, $to_date, $select_branch); //lấy thông tin CSAT
        $surveyBranches= $modelSurveySections->getNPSStatisticReportByBranches($from_date, $to_date, $locationID) ; //lấy thông tin CSAT
        $arrCountry = $modelSurveySections->getCSATInfoByAll($from_date, $to_date); //lấy thông tin CSAT
        $arrCountry = json_decode(json_encode($arrCountry), 1); //chuyển về dạng array
        $arrCountry[0]['KhuVuc'] = 'WholeCountry';
        $arrCountry[0]['NVKinhDoanh_AVGPoint'] = ($arrCountry[0]['NVKinhDoanhPoint'] > 0) ? round($arrCountry[0]['NVKinhDoanhPoint'] / $arrCountry[0]['SoLuongKD'], 2) : 0; //Tat ca khu vuc
        $arrCountry[0]['NVTrienKhai_AVGPoint'] = ($arrCountry[0]['NVTrienKhaiPoint'] > 0) ? round($arrCountry[0]['NVTrienKhaiPoint'] / $arrCountry[0]['SoLuongTK'], 2) : 0;
        $arrCountry[0]['DGDichVu_Net_AVGPoint'] = ($arrCountry[0]['DGDichVu_Net_Point'] > 0) ? round($arrCountry[0]['DGDichVu_Net_Point'] / $arrCountry[0]['SoLuongDGDV_Net'], 2) : 0;

        $arrCountry[0]['NVBaoTri_AVGPoint'] = ($arrCountry[0]['NVBaoTriPoint'] > 0) ? round($arrCountry[0]['NVBaoTriPoint'] / $arrCountry[0]['SoLuongNVBaoTri'], 2) : 0;
        $arrCountry[0]['DVBaoTri_Net_AVGPoint'] = ($arrCountry[0]['DVBaoTri_Net_Point'] > 0) ? round($arrCountry[0]['DVBaoTri_Net_Point'] / $arrCountry[0]['SoLuongDVBaoTri_Net'], 2) : 0;
        //toàn quốc NPS
//        $sumNPSCountryRegion = ($hasCurrentDay) ? $modelSurveySections->getNPSStatisticReportByAll($from_date, $to_date) : $summaryNps->getNPSStatisticReportByAll($from_date, $to_date); //lấy thông tin CSAT
        $sumNPSCountryBranches = $modelSurveySections->getNPSStatisticReportByAll($from_date, $to_date); //lấy thông tin CSAT

        $sumNPSCountryBranches = json_decode(json_encode($sumNPSCountryBranches), 1); //chuyển về dạng array
        
        $npsCountryBranches = ['WholeCountry' => 0, 'ToanQuocTK' => 0, 'ToanQuocSBT' => 0];
        $npsCountryBranches['WholeCountry'] = ($sumNPSCountryBranches[0]['TongCong'] > 0) ? (($sumNPSCountryBranches[0]['UngHo'] - $sumNPSCountryBranches[0]['KhongUngHo']) / $sumNPSCountryBranches[0]['TongCong']) * 100 : 0;
        $npsCountryBranches['WholeCountry'] = round($npsCountryBranches['WholeCountry'], 2); //làm tròn số

        $npsCountryBranches['ToanQuocTK'] = ($sumNPSCountryBranches[0]['TongCongTK'] > 0) ? (($sumNPSCountryBranches[0]['UngHoTK'] - $sumNPSCountryBranches[0]['KhongUngHoTK']) / $sumNPSCountryBranches[0]['TongCongTK']) * 100 : 0;
        $npsCountryBranches['ToanQuocTK'] = round($npsCountryBranches['ToanQuocTK'], 2); //làm tròn số

        $npsCountryBranches['ToanQuocSBT'] = ($sumNPSCountryBranches[0]['TongCongSBT'] > 0) ? (($sumNPSCountryBranches[0]['UngHoSBT'] - $sumNPSCountryBranches[0]['KhongUngHoSBT']) / $sumNPSCountryBranches[0]['TongCongSBT']) * 100 : 0;
        $npsCountryBranches['ToanQuocSBT'] = round($npsCountryBranches['ToanQuocSBT'], 2); //làm tròn số
        foreach ($survey as &$report) {
            $report->NVKinhDoanh_AVGPoint = ($report->SoLuongKD > 0) ? round($report->NVKinhDoanhPoint / $report->SoLuongKD, 2) : 0;
            $report->NVTrienKhai_AVGPoint = ($report->SoLuongTK > 0) ? round($report->NVTrienKhaiPoint / $report->SoLuongTK, 2) : 0;
            $report->DGDichVu_Net_AVGPoint = ($report->SoLuongDGDV_Net > 0) ? round($report->DGDichVu_Net_Point / $report->SoLuongDGDV_Net, 2) : 0;

            $report->NVBaoTri_AVGPoint = ($report->SoLuongNVBaoTri > 0) ? round($report->NVBaoTriPoint / $report->SoLuongNVBaoTri, 2) : 0;
            $report->DVBaoTri_Net_AVGPoint = ($report->SoLuongDVBaoTri_Net > 0) ? round($report->DVBaoTri_Net_Point / $report->SoLuongDVBaoTri_Net, 2) : 0;
        }
        //sort giá trị theo field
//        $extraFunc->sortOnField($surveyBranches, 'Vung', 'ASC');
        //NPS chi nhanhs
        $npsBranches = $npsBranchesTK = $npsBranchesSBT = [];
        $sumNPSCountryBranches = ['UngHo' => 0, 'KhongUngHo' => 0, 'TongCong' => 0,
            'UngHoTK' => 0, 'UngHoSBT' => 0,  'KhongUngHoTK' => 0, 'KhongUngHoSBT' => 0, 'TongCongTK' => 0, 'TongCongSBT' => 0];
        //lấy tổng các thông số độ ủng hộ NPS
        foreach ($surveyBranches as $res) {
            if ($res->TongCong > 0) {
                $npsBranches[$res->KhuVuc] = (($res->UngHo - $res->KhongUngHo) / $res->TongCong) * 100; //tỉ lệ NPS
                $npsBranches[$res->KhuVuc] = round($npsBranches[$res->KhuVuc], 2); //làm tròn số
            } else
                $npsBranches[$res->KhuVuc] = 0;
            //NPS Triển khai
            if ($res->TongCongTK > 0) {
                $npsBranchesTK[$res->KhuVuc] = (($res->UngHoTK - $res->KhongUngHoTK) / $res->TongCongTK) * 100;
                $npsBranchesTK[$res->KhuVuc] = round($npsBranchesTK[$res->KhuVuc], 2); //làm tròn số
            } else
                $npsBranchesTK[$res->KhuVuc] = 0;
            //NPS bảo trì
            if ($res->TongCongSBT > 0) {
                $npsBranchesSBT[$res->KhuVuc] = (($res->UngHoSBT - $res->KhongUngHoSBT) / $res->TongCongSBT) * 100;
                $npsBranchesSBT[$res->KhuVuc] = round($npsBranchesSBT[$res->KhuVuc], 2); //làm tròn số
            } else
                $npsBranchesSBT[$res->KhuVuc] = 0;
        }
        //NPS chi nhánh
        $resultErrorAction = $modelSurveySections->getCsatErrorActionCsat12($from_date, $to_date, $locationID);
        $csatFinalTotal = $this->formatCsatErrorActionCsat12($resultErrorAction);

        $param=['survey' => $survey, 'npsBranches' => $npsBranches, 'npsCountryBranches' => $npsCountryBranches,
                'npsBranchesTK' => $npsBranchesTK,  'npsBranchesSBT' => $npsBranchesSBT,
                'arrCountry' => $arrCountry, 'csatFinalTotal' => $csatFinalTotal, 'locationSelected' => $locationSelected, 'from_date' => $from_date, 'to_date' => $to_date];
        
        return [0 => view("report/npsGeneralReport",$param)->render(), 1=>$param];
    }

    private function productivityReport($from_date, $to_date,  $locationID) {
        $condition['fromDate'] = $from_date;
        $condition['toDate'] = $to_date;
        $condition['locationID);'] = $locationID;
        $result = $this->modelSurveySections->getProductivity($condition);
        return [0 => view("report/productivityReport", ['result' => $result])->render(), 1=>$result];
    }

    private function transactionReport($region, $from_date, $to_date, $branch, $branchcode) {
        $condition['fromDate'] = $from_date;
        $condition['toDate'] = $to_date;
        $result = $this->modelSurveySections->getTransaction($condition);
        return [0 => view("report/transactionReport", ['result' => $result])->render(), 1=>$result];
    }

    public function getLocationByRegion(Request $request) {
        $branch = [];
        if (!empty($request->id_region)) {
            $id_region = $request->id_region;
            $modelLocation = new Location();
            $extraFunc = new ExtraFunction();

            $branch = $modelLocation->getBranchLocationPlus([implode(',', $this->userGranted['location'])], [implode(',', $this->userGranted['branchcode'])], $request->id_region);
        }
        return json_encode($branch);
    }

    public function exportToExcelReport(Request $request) {
        $data = $request->input();
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];
        $type = $data['type'];
        $typeChild = isset($data['typeChild']) ? $data['typeChild'] : null;
        $locationResult = $this->getCorrectLocation($data);
        $locationID = $locationResult['locationID'];
        $locationSelected = $locationResult['locationSelected'];
        $extraFunc = new ExtraFunction();
        $excelReport = new ExcelReportController();
        $excelReportPart2 = new ExcelReportPart2Controller();
        //ktra nếu ko chọn vùng thì mặc định là các vùng/ chi nhánh user được cấp quyền xem
//        $region = empty($region) ? implode(',', $this->userGranted['region']) : $region;
//        $branch = empty($branch[0]) ? [implode(',', $this->userGranted['location'])] : $branch;
//        $branchcode = empty($branchcode[0]) ? [implode(',', $this->userGranted['branchcode'])] : $branchcode;
        if (empty($locationSelected)) {
            $textRegion = trans('report.AllBranch');
        } else {
            $textRegion =trans('report.Location')  . ': '. implode(',', $locationSelected);
        }
        switch ($type) {
            case 4:
                $result = $this->CSATReport(2, $from_date, $to_date,  [] , $locationID, $locationSelected);
                $dataCoreValue = $result[1];
                $PathExcel = Excel::create('CEM_CSAT_Report_' . strtotime(date("Y-m-d h:i:s")), function ($excel) use ($dataCoreValue, $textRegion, $from_date, $to_date, $excelReport, $excelReportPart2, $typeChild) {
                    $excel->sheet('TH', function ($sheet) use ($dataCoreValue, $textRegion, $from_date, $to_date, $excelReport, $excelReportPart2, $typeChild) {
                        $sheet->mergeCells('C1:J1')->cell('C1', function ($cell) {
                            $cell->setValue(trans('report.RateSatisfactionOfCustomer'));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C2:J2')->cell('C2', function ($cell) use ($textRegion) {
                            $cell->setValue($textRegion);
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C3:J3')->cell('C3', function ($cell) use ($from_date, $to_date) {
                            $cell->setValue(date('d/m/Y', strtotime($from_date)) . ' - ' . date('d/m/Y', strtotime($to_date)));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        if ($typeChild == 1) {
                            $rowIndex = $excelReport->createDetailCsat($sheet, $dataCoreValue, 5);
                            $rowIndex2 = $excelReportPart2->createDetailObjectCsat($sheet, $dataCoreValue, $rowIndex + 2);
                            $rowIndex3 = $excelReportPart2->createDetailBranchCsat($sheet, $dataCoreValue['surveyCSATBranch'], $rowIndex2 + 2);
                            $rowIndex4= $excelReport->createDetailActionCsat12($sheet, $dataCoreValue, $rowIndex3 + 2);


                        } else if ($typeChild == 2) {
                            $rowIndex = $excelReport->createDetailServiceCsat12Location($sheet, $dataCoreValue, 5);


                        } else {
                            $rowIndex = $excelReport->createDetailStaffCsat12Location($sheet, $dataCoreValue, 5);

                        }

                    });
                });
                $PathExcel = $PathExcel->string('xlsx'); //change xlsx for the format you want, default is xls
                $response = array(
                    'name' => 'CEM_CSAT_Report_' . strtotime(date("Y-m-d h:i:s")), //no extention needed
                    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($PathExcel) //mime type of used format
                );
                return response()->json($response);
                break;
            case 5:
                $surveySection = new SurveySections();
                $result1 = $this->NPSStatisticReport($from_date, $to_date , $locationID, $locationSelected);
                $result2 = $surveySection->getCustommerCommentReport($from_date, $to_date, $locationID, $locationSelected, 1);
                $dataCoreValue = [0 => $result1[1], 1 => $result2[1]];
                $PathExcel = Excel::create('CEM_NPS_CUSCOMMENT_Report_' . date("Y-m-d", time()), function ($excel) use ($dataCoreValue, $textRegion, $from_date, $to_date) {
                    $excel->sheet('TH', function ($sheet) use ($dataCoreValue, $textRegion, $from_date, $to_date) {
                        $sheet->mergeCells('C1:J1')->cell('C1', function ($cell) {
                            $cell->setValue(trans('report.RateNetPromoterScoreStatisticalNPSPoint'));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C2:J2')->cell('C2', function ($cell) use ($textRegion) {
                            $cell->setValue($textRegion);
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C3:J3')->cell('C3', function ($cell) use ($from_date, $to_date) {
                            $cell->setValue(date('d/m/Y', strtotime($from_date)) . ' - ' . date('d/m/Y', strtotime($to_date)));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });

                        $exceldashboard = new ExcelDashboardController();
                        $goIndex = $exceldashboard->createDetailNps($sheet, $dataCoreValue[0], 5);
                        $goIndex2 = $exceldashboard->createGroupNps($sheet, $dataCoreValue[0], $goIndex + 2);
                        $goIndex3 = $exceldashboard->createGroupBranchNps($sheet, $dataCoreValue[0]['surveyBranch'], $goIndex2 + 2);
                        $exceldashboard->createEvaluateCus($sheet, $dataCoreValue[1], $goIndex3 + 2);
                    });
                });

                $PathExcel = $PathExcel->string('xlsx'); //change xlsx for the format you want, default is xls
                $response = array(
                    'name' => 'CEM_NPS_CUSCOMMENT_Report_' . date("Y-m-d", time()), //no extention needed
                    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($PathExcel) //mime type of used format
                );
                return response()->json($response);
                break;
            case 6:
                $result = $this->generalPointReport($from_date, $to_date , $locationID, $locationSelected);
                $dataCoreValue = $result[1];
                $PathExcel = Excel::create('CEM_NPS_CSAT_REGION_BRANCH_Report_' . date("Y-m-d", time()), function ($excel) use ($dataCoreValue, $textRegion, $from_date, $to_date, $excelReport) {
                    $excel->sheet('TH', function ($sheet) use ($dataCoreValue, $textRegion, $from_date, $to_date, $excelReport) {
                        $sheet->mergeCells('C1:J1')->cell('C1', function ($cell) {
                            $cell->setValue(trans('report.CsatNpsPointOfLocation'));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C2:J2')->cell('C2', function ($cell) use ($textRegion) {
                            $cell->setValue($textRegion);
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C3:J3')->cell('C3', function ($cell) use ($from_date, $to_date) {
                            $cell->setValue(date('d/m/Y', strtotime($from_date)) . ' - ' . date('d/m/Y', strtotime($to_date)));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        foreach ($dataCoreValue['arrCountry'] as $key => $value) {
                            $dataCoreValue['arrCountry'][$key] = (object)$value;
                        }
//                                $excelReport->createCsatBranchReport($sheet, $dataCoreValue['survey_branches'], $dataCoreValue['survey'], 5, (array) $dataCoreValue['npsBranches'], $goIndex1[1], $dataCoreValue['arrCountry']);
                        $goIndex1 = $excelReport->createCsatLocationReport($sheet, $dataCoreValue['survey'], 5, $dataCoreValue['npsCountryBranches'], $dataCoreValue['npsBranches'], $dataCoreValue['arrCountry']);
                        $excelReport->createCsatErrorActionReport($sheet, $dataCoreValue['csatFinalTotal'], $goIndex1[0] + 2);
                    });
                });

                $PathExcel = $PathExcel->string('xlsx'); //change xlsx for the format you want, default is xls
                $response = array(
                    'name' => 'CEM_NPS_CSAT_REGION_BRANCH_Report_' . date("Y-m-d", time()), //no extention needed
                    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($PathExcel) //mime type of used format
                );
                return response()->json($response);
                break;
            case 7:
                $result = $this->SurveyQuantityReport($from_date, $to_date , $locationID);
                $dataCoreValue = $result[1];
                $PathExcel = Excel::create('CEM_NPS_CSAT_QUANTITY_Report_' . date("Y-m-d", time()), function ($excel) use ($dataCoreValue, $textRegion, $from_date, $to_date) {
                    $excel->sheet('TH', function ($sheet) use ($dataCoreValue, $textRegion, $from_date, $to_date) {
                        $sheet->mergeCells('C1:J1')->cell('C1', function ($cell) {
                            $cell->setValue(trans('report.QuantityOfCustomerCare'));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C2:J2')->cell('C2', function ($cell) use ($textRegion) {
                            $cell->setValue($textRegion);
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C3:J3')->cell('C3', function ($cell) use ($from_date, $to_date) {
                            $cell->setValue(date('d/m/Y', strtotime($from_date)) . ' - ' . date('d/m/Y', strtotime($to_date)));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        //Tạo báo cáo số lượng khảo sát CSAT
                        $genExcel = new ExcelDashboardController();
                        $goIndex1 = $genExcel->createAmountCsat($sheet, $dataCoreValue, 5);
                        //Tạo báo cáo số lượng khảo sát NPS
                        $genExcel->createAmountNps($sheet, $dataCoreValue, $goIndex1 + 2);
                    });
                });

                $PathExcel = $PathExcel->string('xlsx'); //change xlsx for the format you want, default is xls
                $response = array(
                    'name' => 'CEM_NPS_CSAT_QUANTITY_Report_' . date("Y-m-d", time()), //no extention needed
                    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($PathExcel) //mime type of used format
                );
                return response()->json($response);
                break;
            case 8:
                $result = $this->productivityReport($from_date, $to_date , $locationID);
                $dataCoreValue = $result[1];
                $PathExcel = Excel::create('CEM_Productivity_Report_' . date("Y-m-d", time()), function ($excel) use ($dataCoreValue, $textRegion, $from_date, $to_date, $excelReport) {
                    $excel->sheet('TH', function ($sheet) use ($dataCoreValue, $textRegion, $from_date, $to_date, $excelReport) {
                        $sheet->mergeCells('C1:J1')->cell('C1', function ($cell) {
                            $cell->setValue(trans('report.Productivity'));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C2:J2')->cell('C2', function ($cell) use ($textRegion) {
                            $cell->setValue($textRegion);
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('C3:J3')->cell('C3', function ($cell) use ($from_date, $to_date) {
                            $cell->setValue(date('d/m/Y', strtotime($from_date)) . ' - ' . date('d/m/Y', strtotime($to_date)));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });

                        //Tạo báo cáo năng suất nhân viên
                        $excelReport->createProductivityReport($sheet, $dataCoreValue, 5);
                    });
                });

                $PathExcel = $PathExcel->string('xlsx'); //change xlsx for the format you want, default is xls
                $response = array(
                    'name' => 'CEM_Productivity_Report_' . date("Y-m-d", time()), //no extention needed
                    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($PathExcel) //mime type of used format
                );
                return response()->json($response);
                break;
//            case 9:
//                $result = $this->transactionReport($region, $from_date, $to_date, $branch, $branchcode);
//                $dataCoreValue = $result[1];
//                if (empty($region)) {
//                    $textRegion = 'Toàn quốc';
//                } else {
//                    $textRegion = 'Vùng ' . $region;
//                }
//
//                $PathExcel = Excel::create('CEM_Transaction_Report_' . date("Y-m-d", time()), function ($excel) use ($dataCoreValue, $textRegion, $from_date, $to_date, $excelReport) {
//                    $excel->sheet('TH', function ($sheet) use ($dataCoreValue, $textRegion, $from_date, $to_date, $excelReport) {
//                        $sheet->mergeCells('C1:J1')->cell('C1', function ($cell) {
//                            $cell->setValue('Số lượng email, khách hàng, phản hồi, giao dịch tại quầy');
//                            $cell->setFontSize(16);
//                            $cell->setFontWeight('bold');
//                            $cell->setAlignment('center');
//                            $cell->setValignment('center');
//                        });
//                        $sheet->mergeCells('C2:J2')->cell('C2', function ($cell) use ($textRegion) {
//                            $cell->setValue($textRegion);
//                            $cell->setFontSize(16);
//                            $cell->setFontWeight('bold');
//                            $cell->setAlignment('center');
//                            $cell->setValignment('center');
//                        });
//                        $sheet->mergeCells('C3:J3')->cell('C3', function ($cell) use ($from_date, $to_date) {
//                            $cell->setValue(date('d/m/Y', strtotime($from_date)) . ' - ' . date('d/m/Y', strtotime($to_date)));
//                            $cell->setFontSize(16);
//                            $cell->setFontWeight('bold');
//                            $cell->setAlignment('center');
//                            $cell->setValignment('center');
//                        });
//                        //Tạo báo cáo năng suất nhân viên
//                        $excelReport->createTransactionReport($sheet, $dataCoreValue, 5);
//                    });
//                });
//                $PathExcel = $PathExcel->string('xlsx'); //change xlsx for the format you want, default is xls
//                $response = array(
//                    'name' => 'CEM_Productivity_Report_' . date("Y-m-d", time()), //no extention needed
//                    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($PathExcel) //mime type of used format
//                );
//                return response()->json($response);
//                break;
        }
    }

    // Bộ api lấy thông tin báo cáo csat hifpt
    public function reportExcelHiFPT($day, $dayTo) {
        try {

            $dayStart = date_create(date("Y-m-d 00:00:00", strtotime($day)));
            $dayEnd = date_create(date("Y-m-d 23:59:59", strtotime($dayTo)));
            $start = date_format($dayStart, "Y-m-d H:i:s");
            $end = date_format($dayEnd, "Y-m-d H:i:s");
            $ssModel = new SurveySections();
            $survey = $ssModel->getSurveySectionHiFPTForReportByDay($start, $end);
            $name = 'CSATreport' . date_format($dayStart, 'd-m-Y') . 'to' . date_format($dayEnd, 'd-m-Y');
            return Excel::create($name, function($excel) use ($survey) {
                        $excel->sheet('point', function($sheet) use ($survey) {
                            $sheet->loadView('report.csatReportHiFPT')->with(['survey' => $survey]);
                        });
                    })->download('xlsx');
        } catch (Exception $ex) {
            dump($ex->getMessage());
        }
    }

    public function repostCsatCustom($day, $dayTo) {
        try {

            $dayStart = date_create(date("Y-m-d 00:00:00", strtotime($day)));
            $dayEnd = date_create(date("Y-m-d 23:59:59", strtotime($dayTo)));
            $start = date_format($dayStart, "Y-m-d H:i:s");
            $end = date_format($dayEnd, "Y-m-d H:i:s");
            $modelLoc = new Location();
            $locations = $modelLoc->getAllLocation();
            $arrayLocationTin = [];
            foreach ($locations as $location) {
                if ($location->region == 'Vùng 1' || $location->region == 'Vùng 2' || $location->region == 'Vùng 3') {
                    array_push($arrayLocationTin, $location->id);
                } else {
                    if ($location->id == 52 || $location->id == 53 || $location->id == 54 || $location->id == 511) {
                        array_push($arrayLocationTin, $location->id);
                    }
                }
            }
            $ssModel = new SurveyReport();
            $survey = $ssModel->getSurveyReportCSATTin($start, $end, $arrayLocationTin);

            return view('report.csatReportCustom', ['survey' => $survey]);
        } catch (Exception $ex) {
            dump($ex->getMessage());
        }
    }

    public function deleteExcelFile(Request $request) {
        try {
            $data = $request->input();
            if (file_exists(public_path() . '/storage/' . $data['fileName'])) {
                $result = File::delete(public_path() . '/storage/' . $data['fileName']);
                $fileExist = true;
            } else
                $fileExist = false;
            if ($fileExist == true)
                $message = ($result == true) ? 'Xoa file ' . $data['fileName'] . ' thanh cong' : 'Xoa file ' . $data['fileName'] . ' that bai';
            else
                $message = 'File khong ton tai';
            return ['message' => $message];
        } catch (Exception $ex) {
            return ['message' => $ex->getMessage()];
        }
    }

    public function getCsatByObject($survey)
    {

        $surveyObject = [];
        foreach ($survey as $key => $value) {
            $typeCsat = $value->answers_point;
            $surveyObject[$typeCsat]['Csat'] = $value->DanhGia;
            $surveyObject[$typeCsat]['Net'] = $value->DGDichVu_Net + $value->DVBaoTri_Net ;
            $surveyObject[$typeCsat]['NetAndTV'] = $surveyObject[$typeCsat]['Net'];
            $surveyObject[$typeCsat]['NVKinhDoanh'] = $value->NVKinhDoanh;
            $surveyObject[$typeCsat]['NVKT'] = $value->NVTrienKhai + $value->NVBaoTri;
            $surveyObject[$typeCsat]['TongHopNV'] = $surveyObject[$typeCsat]['NVKinhDoanh'] + $surveyObject[$typeCsat]['NVKT'];
        }
        $surveyObjectTotal['total']['Csat'] = 'Total';
        $surveyObjectTotal['total']['Net'] = 0;
        $surveyObjectTotal['total']['NetPercent'] = '100%';
        $surveyObjectTotal['total']['NetAndTV'] = 0;
        $surveyObjectTotal['total']['NetAndTVPercent'] = '100%';
        $surveyObjectTotal['total']['NVKinhDoanh'] = 0;
        $surveyObjectTotal['total']['NVKinhDoanhPercent'] = '100%';
        $surveyObjectTotal['total']['NVKT'] = 0;
        $surveyObjectTotal['total']['NVKTPercent'] = '100%';
        $surveyObjectTotal['total']['TongHopNV'] = 0;
        $surveyObjectTotal['total']['TongHopNVPercent'] = '100%';
        foreach ($surveyObject as $key => $value) {
            $surveyObjectTotal['total']['Net'] += $value['Net'];
            $surveyObjectTotal['total']['NetAndTV'] += $value['NetAndTV'];
            $surveyObjectTotal['total']['NVKinhDoanh'] += $value['NVKinhDoanh'];
            $surveyObjectTotal['total']['NVKT'] += $value['NVKT'];
            $surveyObjectTotal['total']['TongHopNV'] += $value['TongHopNV'];
        }
        $totalPointNet =  $totalPointNetAndTV = $totalPointNVKinhDoanh = $totalPointNVKT = $totalPointTongHopNV = 0;
        foreach ($surveyObject as $key => $value) {
            $surveyObject[$key]['NetPercent'] = round($surveyObjectTotal['total']['Net'] != 0 ? ($value['Net'] / $surveyObjectTotal['total']['Net']) * 100 : 0, 2) . '%';
            $surveyObject[$key]['NetAndTVPercent'] = round($surveyObjectTotal['total']['NetAndTV'] != 0 ? ($value['NetAndTV'] / $surveyObjectTotal['total']['NetAndTV']) * 100 : 0, 2) . '%';
            $surveyObject[$key]['NVKinhDoanhPercent'] = round($surveyObjectTotal['total']['NVKinhDoanh'] != 0 ? ($value['NVKinhDoanh'] / $surveyObjectTotal['total']['NVKinhDoanh']) * 100 : 0, 2) . '%';
            $surveyObject[$key]['NVKTPercent'] = round($surveyObjectTotal['total']['NVKT'] != 0 ? ($value['NVKT'] / $surveyObjectTotal['total']['NVKT']) * 100 : 0, 2) . '%';
            $surveyObject[$key]['TongHopNVPercent'] = round($surveyObjectTotal['total']['TongHopNV'] != 0 ? ($value['TongHopNV'] / $surveyObjectTotal['total']['TongHopNV']) * 100 : 0, 2) . '%';

            //Tính tổng số điểm
            $totalPointNet += $value['Net'] * $key;
            $totalPointNetAndTV += $value['NetAndTV'] * $key;
            $totalPointNVKinhDoanh += $value['NVKinhDoanh'] * $key;
            $totalPointNVKT += $value['NVKT'] * $key;
            $totalPointTongHopNV += $value['TongHopNV'] * $key;
        }
        $surveyDTB['ĐTB']['ĐTB_NET'] = ($surveyObjectTotal['total']['Net'] != 0) ? round($totalPointNet / $surveyObjectTotal['total']['Net'], 2) : 0;
        $surveyDTB['ĐTB']['ĐTB_NetAndTV'] = ($surveyObjectTotal['total']['NetAndTV'] != 0) ? round($totalPointNetAndTV / $surveyObjectTotal['total']['NetAndTV'], 2) : 0;
        $surveyDTB['ĐTB']['ĐTB_NVKinhDoanh'] = ($surveyObjectTotal['total']['NVKinhDoanh'] != 0) ? round($totalPointNVKinhDoanh / $surveyObjectTotal['total']['NVKinhDoanh'], 2) : 0;
        $surveyDTB['ĐTB']['ĐTB_NVKT'] = ($surveyObjectTotal['total']['NVKT'] != 0) ? round($totalPointNVKT / $surveyObjectTotal['total']['NVKT'], 2) : 0;
        $surveyDTB['ĐTB']['ĐTB_TongHopNV'] = ($surveyObjectTotal['total']['TongHopNV'] != 0) ? round($totalPointTongHopNV / $surveyObjectTotal['total']['TongHopNV'], 2) : 0;
//        $totalResult = array_merge($surveyObject, $surveyObjectTotal, $surveyDTB);
        $totalResult = $surveyObject + $surveyObjectTotal;
        return ['totalCSAT' => $totalResult, 'averagePoint' => $surveyDTB['ĐTB']];
    }

    public function getCsatBranchPercent($resultTQCsat)
    {
        $all = [];
        $allLocation = [];
        $csat1['Csat'] = 'VeryUnsatisfaction';
        $csat1['answerPoint'] = '1';
        $csat2['Csat'] = 'Unsatisfaction';
        $csat2['answerPoint'] = '2';
        $csat3['Csat'] = 'Neutral';
        $csat3['answerPoint'] = '3';
        $csat4['Csat'] = 'Satisfaction';
        $csat4['answerPoint'] = '4';
        $csat5['Csat'] = 'VerySatisfaction';
        $csat5['answerPoint'] = '5';
        foreach ($resultTQCsat as $key => $value) {
            $csat1[$value->Location.'Net'] = $value->VeryUnsatisfactionNet;
            $csat2[$value->Location.'Net'] = $value->UnsatisfactionNet;
            $csat3[$value->Location.'Net'] = $value->NeutralNet;
            $csat4[$value->Location.'Net'] = $value->SatisfactionNet;
            $csat5[$value->Location.'Net'] = $value->VerySatisfactionNet;

            $csat1[$value->Location.'SaleMan'] = $value->VeryUnsatisfactionSaleMan;
            $csat2[$value->Location.'SaleMan'] = $value->UnsatisfactionSaleMan;
            $csat3[$value->Location.'SaleMan'] = $value->NeutralSaleMan;
            $csat4[$value->Location.'SaleMan'] = $value->SatisfactionSaleMan;
            $csat5[$value->Location.'SaleMan'] = $value->VerySatisfactionSaleMan;

            $csat1[$value->Location.'Sir'] = $value->VeryUnsatisfactionSir;
            $csat2[$value->Location.'Sir'] = $value->UnsatisfactionSir;
            $csat3[$value->Location.'Sir'] = $value->NeutralSir;
            $csat4[$value->Location.'Sir'] = $value->SatisfactionSir;
            $csat5[$value->Location.'Sir'] = $value->VerySatisfactionSir;
            array_push($all, $value->Location.'Net');
            array_push($all, $value->Location.'SaleMan');
            array_push($all, $value->Location.'Sir');
            array_push($allLocation, $value->Location);
        }
        $csatAll = [(object)$csat1, (object)$csat2, (object)$csat3, (object)$csat4, (object)$csat5];
        $surveyCSATBranchObject = ['csatAll' => $csatAll, 'all' => $all];
        $all = $surveyCSATBranchObject['all'];
        $surveyCSATBranch = [];
        foreach ($surveyCSATBranchObject['csatAll'] as $key => $value) {
            $typeCsat = $value->answerPoint;
            $surveyCSATBranch[$typeCsat] = (array)$value;
        }
        $surveyCSATBranchTotal['total']['Csat'] = 'Total';
        foreach ($all as $key2 => $value2) {
            $surveyCSATBranchTotal['total'][$value2] = 0;
            $surveyCSATBranchTotal['total'][$value2 . 'Percent'] = '100%';
        }

        foreach ($surveyCSATBranch as $key => $value) {
            foreach ($all as $key3 => $value3) {
                $surveyCSATBranchTotal['total'][$value3] += $value[$value3];
            }
        }
//        dump($surveyCSATBranch,$surveyCSATBranchTotal);die;
        foreach ($all as $key4 => $value4) {
            $totalPoint[$value4] = 0;
        }
        foreach ($surveyCSATBranch as $key => $value) {
            foreach ($all as $key5 => $value5) {
                $surveyCSATBranch[$key][$value5 . 'Percent'] = round($surveyCSATBranchTotal['total'][$value5] != 0 ? ($value[$value5] / $surveyCSATBranchTotal['total'][$value5]) * 100 : 0, 2) . '%';
            }

            //Tính tổng số điểm
            foreach ($totalPoint as $key6 => $value6) {
                $totalPoint[$key6] += $value[$key6] * $key;
            }
        }
        foreach ($all as $key7 => $value7) {
            $surveyDTB['AVG']['AVG_' . $value7] = ($surveyCSATBranchTotal['total'][$value7] != 0) ? round($totalPoint[$value7] / $surveyCSATBranchTotal['total'][$value7], 2) : 0;
        }
        $totalResult = $surveyCSATBranch + $surveyCSATBranchTotal;
        return ['totalCSAT' => $totalResult, 'averagePoint' => $surveyDTB['AVG'], 'all' => $allLocation];
    }

    public function getNPSStatisticBranchPercent($resultTQNPS)
    {
        $allLocation = [];
        $nps06['Nps'] = '0-6';
        $nps06['answerPoint'] = 'Unsupported';
        $nps78['Nps'] = '7-8';
        $nps78['answerPoint'] = 'NeutralNPS';
        $nps910['Nps'] = '9-10';
        $nps910['answerPoint'] = 'Supported';

        foreach ($resultTQNPS as $key => $value) {
            $nps06[$value->Location] = $value->Unsupported;
            $nps78[$value->Location] = $value->NeutralNPS;
            $nps910[$value->Location] = $value->Supported;
            array_push($allLocation, $value->Location);
        }
        $npsAll = [(object)$nps06, (object)$nps78, (object)$nps910];
        $surveyNPSBranchObject = ['npsAll' => $npsAll, 'allLocation' => $allLocation];
        $allLocation = $surveyNPSBranchObject['allLocation'];
        $surveyNPSBranch = [];
        foreach ($surveyNPSBranchObject['npsAll'] as $key => $value) {
            $typeNps = $value->answerPoint;
            $surveyNPSBranch[$typeNps] = (array)$value;
        }
        $surveyNPSBranchTotal['Total']['Nps'] = 'Total';
        foreach ($allLocation as $key2 => $value2) {
            $surveyNPSBranchTotal['Total'][$value2] = 0;
            $surveyNPSBranchTotal['Total'][$value2 . 'Percent'] = '100%';
        }

        foreach ($surveyNPSBranch as $key => $value) {
            foreach ($allLocation as $key3 => $value3) {
                $surveyNPSBranchTotal['Total'][$value3] += $value[$value3];
            }
        }
        foreach ($allLocation as $key4 => $value4) {
            $totalPoint[$value4] = 0;
        }
        foreach ($surveyNPSBranch as $key => $value) {
            foreach ($allLocation as $key5 => $value5) {
                $surveyNPSBranch[$key][$value5 . 'Percent'] = round($surveyNPSBranchTotal['Total'][$value5] != 0 ? ($value[$value5] / $surveyNPSBranchTotal['Total'][$value5]) * 100 : 0, 2) . '%';
            }

            //Tính tổng số điểm
            foreach ($totalPoint as $key6 => $value6) {
                $totalPoint[$key6] += $value[$key6] * $key;
            }
        }
        foreach ($allLocation as $key7 => $value7) {
            $surveyDTB['AVG']['AVG_' . $value7] = ($surveyNPSBranch['Supported'][$value7.'Percent'] - $surveyNPSBranch['Unsupported'][$value7.'Percent']). '%';
        }
        $totalResult = $surveyNPSBranch + $surveyNPSBranchTotal;
        return ['totalNPS' => $totalResult, 'averagePoint' => $surveyDTB['AVG'], 'allLocation' => $allLocation];
    }

    public function formatCsatErrorActionCsat12($resultErrorAction)
    {
        $csatAction = $csatError = [];
        foreach($resultErrorAction['csatAction'] as $key => $value)
        {
            $csatAction[$value->Location] = $value;
        }
        foreach($resultErrorAction['csatError'] as $key => $value)
        {
            $csatError[$value->Location] = $value;
        }
        $csatActionWholeCountry =(array) $csatAction['WholeCountry'];
        foreach($csatActionWholeCountry as $key => $value)
        {
            if($key != 'Location' && $key != 'TotalAction')
            {
                $rateAction[$key] = ($csatActionWholeCountry['TotalAction'] != 0 ?  round(($value/ $csatActionWholeCountry['TotalAction']) * 100, 2) : 0) . '%';
            }
        }
        $rateAction['Location'] = 'Rate (%)';
        $rateAction['TotalAction'] = '100%';
        $csatAction['Rate (%)'] = (object) $rateAction;


        $csatErrorWholeCountry =(array) $csatError['WholeCountry'];
        foreach($csatErrorWholeCountry as $key => $value)
        {
            if($key != 'Location' && $key != 'TotalError')
            {
                $rateError[$key] = ($csatErrorWholeCountry['TotalError'] != 0 ?  round(($value/ $csatErrorWholeCountry['TotalError']) * 100, 2) : 0) . '%';
            }
        }
        $rateError['Location'] = 'Rate (%)';
        $rateError['TotalError'] = '100%';
//        array_push($csatError, (object) $rateError );
        $csatError['Rate (%)'] = (object) $rateError;
        foreach($csatAction as $key => $value)
        {
            $csatActionArray[$key] = (array) $value;
        }
        foreach($csatError as $key => $value)
        {
            $csatErrorArray[$key] = (array) $value;
        }

        foreach($resultErrorAction['allLocation'] as $key =>$value)
        {
            $csatFinalTotal[$value->Location] = (isset($csatActionArray[$value->Location]) ? $csatActionArray[$value->Location]: [])  + (isset($csatErrorArray[$value->Location]) ? $csatErrorArray[$value->Location]: []);
        }
        $wholeCountry = $csatFinalTotal['WholeCountry'];
        unset($csatFinalTotal['WholeCountry']);
        $csatFinalTotal['WholeCountry'] = $wholeCountry;
        $csatFinalTotal['Rate (%)'] = (isset($csatActionArray['Rate (%)']) ? $csatActionArray['Rate (%)']: [])  + (isset($csatErrorArray['Rate (%)']) ? $csatErrorArray['Rate (%)']: []);
        return $csatFinalTotal;
    }

    public function getCorrectLocation($data)
    {
        $modelLocation = new Location();
        $locationByPermission = $this->location;
        $arrayIdLocation= [];
        foreach($locationByPermission as $val)
        {
            array_push($arrayIdLocation, $val->id);
        }
        $location = empty($data['location']) ? implode(',',  $arrayIdLocation) : $data['location'];
//            $branchcode = array($data['branchcode']);
//            $selectBranchTemp = str_replace(',', '_', $branch);
//            $selectBranchcodeTemp = str_replace(',', '_', $branchcode);
        //ktra nếu ko chọn vùng thì mặc định là các vùng/ chi nhánh user được cấp quyền xem
//            $region = empty($region) ? implode(',', $this->userGranted['region']) : $region;
//            $branch = empty($branch[0]) ? [implode(',', $this->userGranted['location'])] : $branch;
//            $branchcode = empty($branchcode[0]) ? [implode(',', $this->userGranted['branchcode'])] : $branchcode;
        $locationID = explode(',', $location);
        $locationSelected = $modelLocation->getNameLocationByID($locationID);
        return ['locationID' => $locationID, 'locationSelected' => $locationSelected, 'location'];
    }

}
