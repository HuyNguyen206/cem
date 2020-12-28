<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\SurveySections;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Report;
use App\Component\ExtraFunction;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SummaryCsat;
use App\Models\SummaryNps;
use App\Models\SummaryOpinion;
use App\Http\Controllers\ExcelDashboardController;
use DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller {

    var $today;
    var $lastweek;
    var $yesterday;
    var $yesterdayTime;
    var $lastMonthFirstDay;
    var $lastMonthLastDay;
    var $firstDayOfLastWeek;
    var $lastDayOfLastWeek;
    var $timeCache;
    var $modelSurveySections;
    var $extraFunc;
    var $CsatDashBoardByType;
    var $CsatReportDashBoard;

    public function __construct() {
        $this->today = date('Y-m-d 23:59:59');
        $this->lastweek = date('Y-m-d 00:00:00', strtotime('-7 days'));
//        $this->lastweek = date('Ym01', strtotime('last month'));
        $this->yesterday = date('Ymd', strtotime('-1 days'));
        $this->yesterdayTime = date('Y-m-d 23:59:59', strtotime('-1 days'));
        $this->lastMonthFirstDay = date('Ym01', strtotime('last month'));
        $this->lastMonthLastDay = date('Ymt', strtotime('last month'));
        $this->firstDayOfLastWeek = date('Ymd', strtotime("last week monday"));
        $this->lastDayOfLastWeek = date('Ymd', strtotime("last week sunday"));
        $this->timeCache = date('Ymd', strtotime($this->lastweek)) . '_' . date('Ymd', strtotime($this->yesterdayTime));
        $this->modelSurveySections = new SurveySections();
        $this->extraFunc = new ExtraFunction();
        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9, 10, 12, 13, 14, 17, 18, 19]);
        $temp = [];
        foreach ($this->selNPSImprovement as $k => $val) {
            $temp[$val->answer_id] = $val;
        }
        $this->selNPSImprovement = $temp;

        //Khởi tại và sắp xếp lại theo loại khảo sát
        $this->CsatDashBoardByType['STK']['NVKD_STK_CSAT_TODAY'] = 0;
        $this->CsatDashBoardByType['STK']['NVTK_STK_CSAT_TODAY'] = 0;
        $this->CsatDashBoardByType['STK']['Internet_STK_CSAT_TODAY'] = 0;
        $this->CsatDashBoardByType['STK']['NVKD_STK_CSAT_YESTERDAY'] = 0;
        $this->CsatDashBoardByType['STK']['NVTK_STK_CSAT_YESTERDAY'] = 0;
        $this->CsatDashBoardByType['STK']['Internet_STK_CSAT_YESTERDAY'] = 0;
        $this->CsatDashBoardByType['STK']['NVKD_STK_CSAT_LAST_WEEK'] = 0;
        $this->CsatDashBoardByType['STK']['NVTK_STK_CSAT_LAST_WEEK'] = 0;
        $this->CsatDashBoardByType['STK']['Internet_STK_CSAT_LAST_WEEK'] = 0;
        $this->CsatDashBoardByType['STK']['NVKD_STK_CSAT_LAST_MONTH'] = 0;
        $this->CsatDashBoardByType['STK']['NVTK_STK_CSAT_LAST_MONTH'] = 0;
        $this->CsatDashBoardByType['STK']['Internet_STK_CSAT_LAST_MONTH'] = 0;
        $this->CsatDashBoardByType['STK']['NVKD_STK_CSAT_SEVEN_DAY_AGO'] = 0;
        $this->CsatDashBoardByType['STK']['NVTK_STK_CSAT_SEVEN_DAY_AGO'] = 0;
        $this->CsatDashBoardByType['STK']['Internet_STK_CSAT_SEVEN_DAY_AGO'] = 0;

        $this->CsatDashBoardByType['SBT']['NVBT_SBT_CSAT_TODAY'] = 0;
        $this->CsatDashBoardByType['SBT']['NVBT_SBT_CSAT_YESTERDAY'] = 0;
        $this->CsatDashBoardByType['SBT']['NVBT_SBT_CSAT_LAST_WEEK'] = 0;
        $this->CsatDashBoardByType['SBT']['NVBT_SBT_CSAT_LAST_MONTH'] = 0;
        $this->CsatDashBoardByType['SBT']['NVBT_SBT_CSAT_SEVEN_DAY_AGO'] = 0;
        $this->CsatDashBoardByType['SBT']['Internet_SBT_CSAT_TODAY'] = 0;
        $this->CsatDashBoardByType['SBT']['Internet_SBT_CSAT_YESTERDAY'] = 0;
        $this->CsatDashBoardByType['SBT']['Internet_SBT_CSAT_LAST_MONTH'] = 0;
        $this->CsatDashBoardByType['SBT']['Internet_SBT_CSAT_LAST_WEEK'] = 0;
        $this->CsatDashBoardByType['SBT']['Internet_SBT_CSAT_SEVEN_DAY_AGO'] = 0;


        //Khởi tạo dữ liệu ReportDashBoard
        $csatText = [1 => 'Rất không hài lòng (CSAT 1)', 2 => 'Không hài lòng (CSAT 2)', 3 => 'Trung lập (CSAT 3)', 4 => 'Hài lòng (CSAT 4)', 5 => 'Rất hài lòng (CSAT 5)'];
        $j = 0;
        for ($i = 0; $i <= 4; $i++) {
            $j = $i + 1;
            $this->CsatReportDashBoard['survey'][$i] = ['answers_point' => $j,
                'NVThuCuoc' => 0,
                'DGDichVu_MobiPay_Net' => 0,
                'DGDichVu_MobiPay_TV' => 0,
                'DGDichVu_Counter' => 0,
                'NV_Counter' => 0,
                'TQ_HMI' => 0,
                'DVBaoTriINDO_TV' => 0,
                'DVBaoTriINDO_Net' => 0,
                'NVBaoTriINDO' => 0,
                'DVBaoTriTIN_TV' => 0,
                'NVBaoTriTIN' => 0,
                'DVBaoTriTIN_Net' => 0,
                'DVBaoTriHIFPT_INDO_TV' => 0,
                'DVBaoTriHIFPT_INDO_Net' => 0,
                'NVBaoTriHIFPT_INDO' => 0,
                'DVBaoTriHIFPT_TIN_TV' => 0,
                'NVBaoTriHIFPT_TIN' => 0,
                'DVBaoTriHIFPT_TIN_Net' => 0,
                'DGDichVu_TV' => 0,
                'DGDichVu_Net' => 0,
                'NVTrienKhai' => 0,
                'NVKinhDoanh' => 0,
                'DGDichVuTS_TV' => 0,
                'DGDichVuTS_Net' => 0,
                'NVTrienKhaiTS' => 0,
                'NVKinhDoanhTS' => 0,
                'DGDichVuSS_TV' => 0,
                'DGDichVuSS_Net' => 0,
                'NVTrienKhaiSS' => 0,
                'NVKinhDoanhSS' => 0,
                'DGDichVuSSW_TV' => 0,
                'DGDichVuSSW_Net' => 0,
                'NVBT_SSW' => 0,
                'DanhGia' => $csatText[$j],
            ];
        }
        $this->CsatReportDashBoard['avg'] = [
            'NVThuCuoc' => 0,
            'DGDichVu_MobiPay_Net' => 0,
            'DGDichVu_MobiPay_TV' => 0,
            'DGDichVu_Counter' => 0,
            'NV_Counter' => 0,
            'TQ_HMI' => 0,
            'DVBaoTriINDO_TV' => 0,
            'DVBaoTriINDO_Net' => 0,
            'NVBaoTriINDO' => 0,
            'DVBaoTriTIN_TV' => 0,
            'NVBaoTriTIN' => 0,
            'DVBaoTriTIN_Net' => 0,
            'DVBaoTriHIFPT_INDO_TV' => 0,
            'DVBaoTriHIFPT_INDO_Net' => 0,
            'NVBaoTriHIFPT_INDO' => 0,
            'DVBaoTriHIFPT_TIN_TV' => 0,
            'NVBaoTriHIFPT_TIN' => 0,
            'DVBaoTriHIFPT_TIN_Net' => 0,
            'DGDichVu_TV' => 0,
            'DGDichVu_Net' => 0,
            'NVTrienKhai' => 0,
            'NVKinhDoanh' => 0,
            'DGDichVuTS_TV' => 0,
            'DGDichVuTS_Net' => 0,
            'NVTrienKhaiTS' => 0,
            'NVKinhDoanhTS' => 0,
            'DGDichVuSS_TV' => 0,
            'DGDichVuSS_Net' => 0,
            'NVTrienKhaiSS' => 0,
            'NVKinhDoanhSS' => 0,
            'DGDichVuSSW_TV' => 0,
            'DGDichVuSSW_Net' => 0,
            'NVBT_SSW' => 0
        ];
        $this->CsatReportDashBoard['total'] = [
            'NVThuCuoc' => 0,
            'DGDichVu_MobiPay_Net' => 0,
            'DGDichVu_MobiPay_TV' => 0,
            'DGDichVu_Counter' => 0,
            'NV_Counter' => 0,
            'TQ_HMI' => 0,
            'DVBaoTriINDO_TV' => 0,
            'DVBaoTriINDO_Net' => 0,
            'NVBaoTriINDO' => 0,
            'DVBaoTriTIN_TV' => 0,
            'NVBaoTriTIN' => 0,
            'DVBaoTriTIN_Net' => 0,
            'DVBaoTriHIFPT_INDO_TV' => 0,
            'DVBaoTriHIFPT_INDO_Net' => 0,
            'NVBaoTriHIFPT_INDO' => 0,
            'DVBaoTriHIFPT_TIN_TV' => 0,
            'NVBaoTriHIFPT_TIN' => 0,
            'DVBaoTriHIFPT_TIN_Net' => 0,
            'DGDichVu_TV' => 0,
            'DGDichVu_Net' => 0,
            'NVTrienKhai' => 0,
            'NVKinhDoanh' => 0,
            'DGDichVuTS_TV' => 0,
            'DGDichVuTS_Net' => 0,
            'NVTrienKhaiTS' => 0,
            'NVKinhDoanhTS' => 0,
            'DGDichVuSS_TV' => 0,
            'DGDichVuSS_Net' => 0,
            'NVTrienKhaiSS' => 0,
            'NVKinhDoanhSS' => 0,
            'DGDichVuSSW_TV' => 0,
            'DGDichVuSSW_Net' => 0,
            'NVBT_SSW' => 0
        ];

        //Khởi tạo mảng dữ liệu NPS DashBoard
//        for ($i = 0; $i <= 10; $i++) {
//            $this->NpsReportDashBoard['survey'][$i] = ['answers_point' => $i,
//                'SauTK' => 0,
//                'SauTKTS' => 0,
//                'SauBTTIN' => 0,
//                'SauBTINDO' => 0,
//                'SauTC' => 0,
//                'SauGDTQ' => 0,
//                'SauTKS' => 0,
//                'SauSSW' => 0,
//                'TongCong' => 0
//            ];
//        }
//        $this->NpsReportDashBoard['total'] = ['SauTK' => 0,
//            'SauTKTS' => 0,
//            'SauBTTIN' => 0,
//            'SauBTINDO' => 0,
//            'SauTC' => 0,
//            'SauGDTQ' => 0,
//            'SauTKS' => 0,
//            'SauSSW' => 0,
//            'TongCong' => 0
//        ];
//        $this->NpsReportDashBoard['groupNPS'] = [
//            0 => [
//                'SauTK' => 0,
//                'SauTKTS' => 0,
//                'SauBTTIN' => 0,
//                'SauBTINDO' => 0,
//                'SauTC' => 0,
//                'SauGDTQ' => 0,
//                'SauTKS' => 0,
//                'SauSSW' => 0,
//                'TongCong' => 0,
//                'type' => 'Không ủng hộ (0 - 6)'
//            ],
//            1 => [
//                'SauTK' => 0,
//                'SauTKTS' => 0,
//                'SauBTTIN' => 0,
//                'SauBTINDO' => 0,
//                'SauTC' => 0,
//                'SauGDTQ' => 0,
//                'SauTKS' => 0,
//                'SauSSW' => 0,
//                'TongCong' => 0,
//                'type' => 'Trung lập (7-8)'
//            ],
//            2 => [
//                'SauTK' => 0,
//                'SauTKTS' => 0,
//                'SauBTTIN' => 0,
//                'SauBTINDO' => 0,
//                'SauTC' => 0,
//                'SauTKS' => 0,
//                'SauGDTQ' => 0,
//                'SauSSW' => 0,
//                'TongCong' => 0,
//                'type' => 'Ủng hộ (9-10)'
//            ]
//        ];
        for ($i = 1; $i <= 7; $i++) {
            $this->survey[$i] = [
                'Vung' => 'Vùng ' . $i,
                'NVKinhDoanhPoint' => 0,
                'NVTrienKhaiPoint' => 0,
                'DGDichVu_TV_Point' => 0,
                'NVKinhDoanhTSPoint' => 0,
                'NVTrienKhaiTSPoint' => 0,
                'DGDichVuTS_Net_Point' => 0,
                'DGDichVuTS_TV_Point' => 0,

                'NVBaoTriTINPoint' => 0,
                'NVBaoTriINDOPoint' => 0,
                'DVBaoTriTIN_Net_Point' => 0,
                'DVBaoTriTIN_TV_Point' => 0,
                'DVBaoTriINDO_Net_Point' => 0,
                'DVBaoTriINDO_TV_Point' => 0,

                'NVBaoTriHIFPT_TINPoint' => 0,
                'NVBaoTriHIFPT_INDOPoint' => 0,
                'DVBaoTriHIFPT_TIN_Net_Point' => 0,
                'DVBaoTriHIFPT_TIN_TV_Point' => 0,
                'DVBaoTriHIFPT_INDO_Net_Point' => 0,
                'DVBaoTriHIFPT_INDO_TV_Point' => 0,

                'SoLuongKD' => 0,
                'SoLuongTK' => 0,
                'SoLuongDGDV_Net' => 0,
                'SoLuongDGDV_TV' => 0,
                'SoLuongKDTS' => 0,
                'SoLuongTKTS' => 0,
                'SoLuongDGDVTS_Net' => 0,
                'SoLuongDGDVTS_TV' => 0,
                'SoLuongNVBaoTriTIN' => 0,
                'SoLuongNVBaoTriINDO' => 0,
                'SoLuongDVBaoTriTIN_Net' => 0,
                'SoLuongDVBaoTriTIN_TV' => 0,
                'SoLuongDVBaoTriINDO_Net' => 0,
                'SoLuongDVBaoTriINDO_TV' => 0,
                'DGDichVu_MobiPay_Net_Point' => 0,
                'DGDichVu_MobiPay_TV_Point' => 0,
                'SoLuongDGDV_MobiPay_Net' => 0,
                'SoLuongDGDV_MobiPay_TV' => 0,
                'NVKinhDoanh_AVGPoint' => 0,
                'NVTrienKhai_AVGPoint' => 0,
                'NVTC_AVGPoint' => 0,
                'NV_GDTQ_AVGPoint' => 0,
                'NVKinhDoanhSS_AVGPoint' => 0,
                'NVTrienKhaiSS_AVGPoint' => 0,
                'NVBT_SSW_AVGPoint' => 0,
                'DGDichVuSSW_Net_AVGPoint' => 0,
                'DGDichVuSSW_TV_AVGPoint' => 0,
                'DGDichVuSS_Net_AVGPoint' => 0,
                'DGDichVuSS_TV_AVGPoint' => 0,
                'DGDichVu_Net_AVGPoint' => 0,
                'DGDichVu_TV_AVGPoint' => 0,
                'NVKinhDoanhTS_AVGPoint' => 0,
                'NVTrienKhaiTS_AVGPoint' => 0,
                'DGDichVuTS_Net_AVGPoint' => 0,
                'DGDichVuTS_TV_AVGPoint' => 0,
                'DGDichVu_MobiPay_Net_AVGPoint' => 0,
                'DGDichVu_MobiPay_TV_AVGPoint' => 0,
                'DGDichVu_GDTQ_AVGPoint' => 0,

                'NVBaoTriTIN_AVGPoint' => 0,
                'NVBaoTriINDO_AVGPoint' => 0,
                'DVBaoTriTIN_Net_AVGPoint' => 0,
                'DVBaoTriTIN_TV_AVGPoint' => 0,
                'DVBaoTriINDO_Net_AVGPoint' => 0,
                'DVBaoTriINDO_TV_AVGPoint' => 0,

                'NVBaoTriHIFPT_TIN_AVGPoint' => 0,
                'NVBaoTriHIFPT_INDO_AVGPoint' => 0,
                'DVBaoTriHIFPT_TIN_Net_AVGPoint' => 0,
                'DVBaoTriHIFPT_TIN_TV_AVGPoint' => 0,
                'DVBaoTriHIFPT_INDO_Net_AVGPoint' => 0,
                'DVBaoTriHIFPT_INDO_TV_AVGPoint' => 0,
            ];
        }


//        dump( $this->CsatReportDashBoard);die;
    }

    public function index() {
        Session::forget('sessionData');
        $surveySection = new SurveySections();
        //get số lượng survey các trạng thái: thành công, không thành công, tổng
//        $result = $this->checkAndSetCache('resultDashboard' . $this->timeCache, 3, $this->lastweek, $this->yesterdayTime);
        $result=$this->SurveyQuantityReport($this->lastweek, $this->yesterdayTime, []);
        $sessionData['result'] = $result;
        //Khởi tạo mảng chứa CSAT theo thời gian
        $CsatDashBoard = [];
        //điểm CSAT toàn quốc hôm nay
        $arrCSATToday = $this->getCsatToDay();
        $CsatDashBoard['CSAT_TODAY'] = $arrCSATToday;
        //điểm CSAT toàn quốc hôm qua
        $arrCSATYesterday = $this->getCsatYesterday();
        $CsatDashBoard['CSAT_YESTERDAY'] = $arrCSATYesterday;
        //điểm CSAT toàn quốc tuần trước
        $arrCSATLastweek = $this->getCsatLastWeek();
        $CsatDashBoard['CSAT_LAST_WEEK'] = $arrCSATLastweek;
        //điểm CSAT toàn quốc tháng trước
        $arrCSATLastmonth = $this->getCsatLastmonth();
        $CsatDashBoard['CSAT_LAST_MONTH'] = $arrCSATLastmonth;
        //điểm CSAT 7 ngày gần đây nhất
        $arrCSATSevenDayAgo = $this->getCsatSevenDayAgo();
        $CsatDashBoard['CSAT_SEVEN_DAY_AGO'] = $arrCSATSevenDayAgo;
        foreach ($CsatDashBoard as $timeString => $csatArray) {
            foreach ($csatArray as $csatInfo) {
                $this->CsatDashBoardByType[$csatInfo->LoaiKhaoSat][($csatInfo->LoaiDoiTuong) . '_' . ($timeString)] = ['ThoiGian' => $timeString, 'LoaiDoiTuong' => $csatInfo->LoaiDoiTuong, 'ĐTB' => $csatInfo->ĐTB];
            }
        }
        $sessionData['CsatDashBoardByType'] = $this->CsatDashBoardByType;
        // điểm Csat theo vùng
        $survey = array();
        //CSAT chi nhánh
        //lấy điểm NPS toàn quốc hôm nay, hôm qua, tuần trước, tháng trước ..
        $allNpsInfo = $this->getAllNPS();
        $sessionData['allNpsInfo'] = $allNpsInfo;
        //gọi hàm lấy thông tin điểm CSAT toàn quốc chi tiết
        $this->getCsatReport($this->lastweek, $this->yesterdayTime);
        $sessionData['CsatReportDashBoard'] = $this->CsatReportDashBoard;

        $this->getNpsReport($this->lastweek, $this->yesterdayTime);
        $sessionData['NpsReportDashBoard'] = $this->NpsReportDashBoard;
        $customerComment = $surveySection->getCustommerCommentReport($this->lastweek, $this->yesterdayTime, '', '', 0);
        $sessionData['customerComment'] = $customerComment;
        $resultCsatAll = $this->getCsatByRegionBranch($this->lastweek, $this->yesterdayTime);
        $sessionData['resultCsatAll'] = $resultCsatAll;

        $resultNpsAll = $this->getNpsByRegionBranch($this->lastweek, $this->yesterdayTime);
//        dump($resultNpsAll);
        $sessionData['resultNpsAll'] = $resultNpsAll;
        Session::put('sessionData', $sessionData);
        $report = new Report();
        $surveyNpsQuantity = $report->SurveyQuantityReport($this->lastweek, $this->yesterdayTime, []);
        return view('dashboard/dashboard', ['result' => $result,
            'from_date' => $this->lastweek, 'to_date' => $this->yesterdayTime,
            'survey_branches' => $resultCsatAll['survey_branches'],
            'arrCountry' => $resultCsatAll['arrCountry'][0],
            'surveyNpsQuantity' => $surveyNpsQuantity[1],
            'npsBranches' => $resultNpsAll,
            'npsCountryRegion' => $allNpsInfo,
            'csatSTK' => $this->CsatDashBoardByType['STK'],
            'csatSBT' => $this->CsatDashBoardByType['SBT'],
            'allNpsInfo' => $allNpsInfo,
            'detailCSAT' => $this->CsatReportDashBoard,
            'detailNPS' => $this->NpsReportDashBoard,
            'customerComment' => $customerComment
        ]);
    }

    private function getCsatToDayOld() {
        //điểm CSAT toàn quốc hôm nay
        $todayCSAT = $this->checkAndSetCache('todayCSATDashboard_' . date('Ymd') . '_' . date('Ymd'), 1, date('Y-m-d 00:00:00'), $this->today);

        $arrCSATToday['NVKinhDoanh_AVGPoint'] = ($todayCSAT[0]->NVKinhDoanhPoint > 0) ? round($todayCSAT[0]->NVKinhDoanhPoint / $todayCSAT[0]->SoLuongKD, 2) : 0; //7 vùng
        $arrCSATToday['NVTrienKhai_AVGPoint'] = ($todayCSAT[0]->NVTrienKhaiPoint > 0) ? round($todayCSAT[0]->NVTrienKhaiPoint / $todayCSAT[0]->SoLuongTK, 2) : 0;
        $arrCSATToday['DGDichVu_Net_AVGPoint'] = ($todayCSAT[0]->DGDichVu_Net_Point > 0) ? round($todayCSAT[0]->DGDichVu_Net_Point / $todayCSAT[0]->SoLuongDGDV_Net, 2) : 0;
        $arrCSATToday['DGDichVu_TV_AVGPoint'] = ($todayCSAT[0]->DGDichVu_TV_Point > 0) ? round($todayCSAT[0]->DGDichVu_TV_Point / $todayCSAT[0]->SoLuongDGDV_TV, 2) : 0;

        $arrCSATToday['NVKinhDoanhTS_AVGPoint'] = ($todayCSAT[0]->NVKinhDoanhTSPoint > 0) ? round($todayCSAT[0]->NVKinhDoanhTSPoint / $todayCSAT[0]->SoLuongKDTS, 2) : 0; //7 vùng
        $arrCSATToday['NVTrienKhaiTS_AVGPoint'] = ($todayCSAT[0]->NVTrienKhaiTSPoint > 0) ? round($todayCSAT[0]->NVTrienKhaiTSPoint / $todayCSAT[0]->SoLuongTKTS, 2) : 0;
        $arrCSATToday['DGDichVuTS_Net_AVGPoint'] = ($todayCSAT[0]->DGDichVuTS_Net_Point > 0) ? round($todayCSAT[0]->DGDichVuTS_Net_Point / $todayCSAT[0]->SoLuongDGDVTS_Net, 2) : 0;
        $arrCSATToday['DGDichVuTS_TV_AVGPoint'] = ($todayCSAT[0]->DGDichVuTS_TV_Point > 0) ? round($todayCSAT[0]->DGDichVuTS_TV_Point / $todayCSAT[0]->SoLuongDGDVTS_TV, 2) : 0;

        $arrCSATToday['NVBaoTriTIN_AVGPoint'] = ($todayCSAT[0]->NVBaoTriTINPoint > 0) ? round($todayCSAT[0]->NVBaoTriTINPoint / $todayCSAT[0]->SoLuongNVBaoTriTIN, 2) : 0;
        $arrCSATToday['NVBaoTriINDO_AVGPoint'] = ($todayCSAT[0]->NVBaoTriINDOPoint > 0) ? round($todayCSAT[0]->NVBaoTriINDOPoint / $todayCSAT[0]->SoLuongNVBaoTriINDO, 2) : 0;
        $arrCSATToday['DVBaoTriTIN_Net_AVGPoint'] = ($todayCSAT[0]->DVBaoTriTIN_Net_Point > 0) ? round($todayCSAT[0]->DVBaoTriTIN_Net_Point / $todayCSAT[0]->SoLuongDVBaoTriTIN_Net, 2) : 0;
        $arrCSATToday['DVBaoTriTIN_TV_AVGPoint'] = ($todayCSAT[0]->DVBaoTriTIN_TV_Point > 0) ? round($todayCSAT[0]->DVBaoTriTIN_TV_Point / $todayCSAT[0]->SoLuongDVBaoTriTIN_TV, 2) : 0;
        $arrCSATToday['DVBaoTriINDO_Net_AVGPoint'] = ($todayCSAT[0]->DVBaoTriINDO_Net_Point > 0) ? round($todayCSAT[0]->DVBaoTriINDO_Net_Point / $todayCSAT[0]->SoLuongDVBaoTriINDO_Net, 2) : 0;
        $arrCSATToday['DVBaoTriINDO_TV_AVGPoint'] = ($todayCSAT[0]->DVBaoTriINDO_TV_Point > 0) ? round($todayCSAT[0]->DVBaoTriINDO_TV_Point / $todayCSAT[0]->SoLuongDVBaoTriINDO_TV, 2) : 0;
        $arrCSATToday['DGDichVu_MobiPay_Net_AVGPoint'] = ($todayCSAT[0]->SoLuongDGDV_MobiPay_Net > 0) ? round($todayCSAT[0]->DGDichVu_MobiPay_Net_Point / $todayCSAT[0]->SoLuongDGDV_MobiPay_Net, 2) : 0;
        $arrCSATToday['DGDichVu_MobiPay_TV_AVGPoint'] = ($todayCSAT[0]->SoLuongDGDV_MobiPay_TV > 0) ? round($todayCSAT[0]->DGDichVu_MobiPay_TV_Point / $todayCSAT[0]->SoLuongDGDV_MobiPay_TV, 2) : 0;
        return $arrCSATToday;
    }

    private function getCsatToDay() {
        //điểm CSAT toàn quốc hôm nay
        $summaryCsat = new SummaryCsat();
        $resultCsatToday = $summaryCsat->getCsatByTime(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'));
        return $resultCsatToday;
    }

    private function getCsatYesterdayOld() {

        //điểm CSAT toàn quốc hôm qua
        $yesterdayCSAT = $this->checkAndSetCache('yesterdayCSATDashboard_' . $this->yesterday, 1, date('Y-m-d 00:00:00', strtotime('-1 days')), date('Y-m-d 23:59:59', strtotime('-1 days')));

        $arrCSATYesterday['NVKinhDoanh_AVGPoint'] = ($yesterdayCSAT[0]->NVKinhDoanhPoint > 0) ? round($yesterdayCSAT[0]->NVKinhDoanhPoint / $yesterdayCSAT[0]->SoLuongKD, 2) : 0; //7 vùng
        $arrCSATYesterday['NVTrienKhai_AVGPoint'] = ($yesterdayCSAT[0]->NVTrienKhaiPoint > 0) ? round($yesterdayCSAT[0]->NVTrienKhaiPoint / $yesterdayCSAT[0]->SoLuongTK, 2) : 0;
        $arrCSATYesterday['DGDichVu_Net_AVGPoint'] = ($yesterdayCSAT[0]->DGDichVu_Net_Point > 0) ? round($yesterdayCSAT[0]->DGDichVu_Net_Point / $yesterdayCSAT[0]->SoLuongDGDV_Net, 2) : 0;
        $arrCSATYesterday['DGDichVu_TV_AVGPoint'] = ($yesterdayCSAT[0]->DGDichVu_TV_Point > 0) ? round($yesterdayCSAT[0]->DGDichVu_TV_Point / $yesterdayCSAT[0]->SoLuongDGDV_TV, 2) : 0;

        $arrCSATYesterday['NVKinhDoanhTS_AVGPoint'] = ($yesterdayCSAT[0]->NVKinhDoanhTSPoint > 0) ? round($yesterdayCSAT[0]->NVKinhDoanhTSPoint / $yesterdayCSAT[0]->SoLuongKDTS, 2) : 0;
        $arrCSATYesterday['NVTrienKhaiTS_AVGPoint'] = ($yesterdayCSAT[0]->NVTrienKhaiTSPoint > 0) ? round($yesterdayCSAT[0]->NVTrienKhaiTSPoint / $yesterdayCSAT[0]->SoLuongTKTS, 2) : 0;
        $arrCSATYesterday['DGDichVuTS_Net_AVGPoint'] = ($yesterdayCSAT[0]->DGDichVuTS_Net_Point > 0) ? round($yesterdayCSAT[0]->DGDichVuTS_Net_Point / $yesterdayCSAT[0]->SoLuongDGDVTS_Net, 2) : 0;
        $arrCSATYesterday['DGDichVuTS_TV_AVGPoint'] = ($yesterdayCSAT[0]->DGDichVuTS_TV_Point > 0) ? round($yesterdayCSAT[0]->DGDichVuTS_TV_Point / $yesterdayCSAT[0]->SoLuongDGDVTS_TV, 2) : 0;

        $arrCSATYesterday['NVBaoTriTIN_AVGPoint'] = ($yesterdayCSAT[0]->NVBaoTriTINPoint > 0) ? round($yesterdayCSAT[0]->NVBaoTriTINPoint / $yesterdayCSAT[0]->SoLuongNVBaoTriTIN, 2) : 0;
        $arrCSATYesterday['NVBaoTriINDO_AVGPoint'] = ($yesterdayCSAT[0]->NVBaoTriINDOPoint > 0) ? round($yesterdayCSAT[0]->NVBaoTriINDOPoint / $yesterdayCSAT[0]->SoLuongNVBaoTriINDO, 2) : 0;
        $arrCSATYesterday['DVBaoTriTIN_Net_AVGPoint'] = ($yesterdayCSAT[0]->DVBaoTriTIN_Net_Point > 0) ? round($yesterdayCSAT[0]->DVBaoTriTIN_Net_Point / $yesterdayCSAT[0]->SoLuongDVBaoTriTIN_Net, 2) : 0;
        $arrCSATYesterday['DVBaoTriTIN_TV_AVGPoint'] = ($yesterdayCSAT[0]->DVBaoTriTIN_TV_Point > 0) ? round($yesterdayCSAT[0]->DVBaoTriTIN_TV_Point / $yesterdayCSAT[0]->SoLuongDVBaoTriTIN_TV, 2) : 0;
        $arrCSATYesterday['DVBaoTriINDO_Net_AVGPoint'] = ($yesterdayCSAT[0]->DVBaoTriINDO_Net_Point > 0) ? round($yesterdayCSAT[0]->DVBaoTriINDO_Net_Point / $yesterdayCSAT[0]->SoLuongDVBaoTriINDO_Net, 2) : 0;
        $arrCSATYesterday['DVBaoTriINDO_TV_AVGPoint'] = ($yesterdayCSAT[0]->DVBaoTriINDO_TV_Point > 0) ? round($yesterdayCSAT[0]->DVBaoTriINDO_TV_Point / $yesterdayCSAT[0]->SoLuongDVBaoTriINDO_TV, 2) : 0;
        $arrCSATYesterday['DGDichVu_MobiPay_Net_AVGPoint'] = ($yesterdayCSAT[0]->SoLuongDGDV_MobiPay_Net > 0) ? round($yesterdayCSAT[0]->DGDichVu_MobiPay_Net_Point / $yesterdayCSAT[0]->SoLuongDGDV_MobiPay_Net, 2) : 0;
        $arrCSATYesterday['DGDichVu_MobiPay_TV_AVGPoint'] = ($yesterdayCSAT[0]->SoLuongDGDV_MobiPay_TV > 0) ? round($yesterdayCSAT[0]->DGDichVu_MobiPay_TV_Point / $yesterdayCSAT[0]->SoLuongDGDV_MobiPay_TV, 2) : 0;
        return $arrCSATYesterday;
    }

    private function getCsatYesterday() {
        $summaryCsat = new SummaryCsat();
        $resultCsatYesterday = $summaryCsat->getCsatByTime(date('Ymd', strtotime('-1 days')), date('Ymd', strtotime('-1 days')));
        return $resultCsatYesterday;
    }

    private function getCsatLastWeek() {
        $summaryCsat = new SummaryCsat();
        $resultCsatLastWeek = $summaryCsat->getCsatByTime($this->firstDayOfLastWeek, $this->lastDayOfLastWeek);
        return $resultCsatLastWeek;
    }

    private function getCsatLastMonth() {
        $summaryCsat = new SummaryCsat();
        $resultCsatLastMonth = $summaryCsat->getCsatByTime($this->lastMonthFirstDay, $this->lastMonthLastDay);
        return $resultCsatLastMonth;
    }

    private function getCsatSevenDayAgo() {
        $summaryCsat = new SummaryCsat();
        $resultCsatSevenDayAgo = $summaryCsat->getCsatByTime($this->lastweek, $this->yesterdayTime);
        return $resultCsatSevenDayAgo;
    }

    /**
     * @param $fromDay
     * @param $toDay
     */
    private function getCsatReport($fromDay, $toDay) {
        $summaryCsat = new SummaryCsat();
        //điểm CSAT toàn quốc hôm qua
//        $detailCSAT = $summaryCsat->getCsatReportSummaryCsat($fromDay, $toDay);
        $survey = $this->modelSurveySections->getCSATInfo($fromDay, $toDay, []);
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
        $this->CsatReportDashBoard['survey'] = $survey;
        $this->CsatReportDashBoard['avg'] = $avg;
        $this->CsatReportDashBoard['total'] = $total;
    }

    private function getNpsReport($from_date, $to_date) {
        $modelSurveySections = new SurveySections();
        $summaryNps = new SummaryNps;
        $survey =$modelSurveySections->getNPSStatisticReport($from_date, $to_date, []) ;
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
        $param=['survey' => $survey,  'groupNPS' => $groupNPS, 'total' => $total];
        $this->NpsReportDashBoard = $param;
    }

    private function getCsatByRegionBranch($fromDay, $today) {
        $summaryCsat = new SummaryCsat();
//        $result = $summaryCsat->getCsatByRegionBranch($fromDay, $today);
        $survey = $this->modelSurveySections->getCSATInfoByBranches($fromDay, $today, [], []) ; //lấy thông tin CSAT
        foreach ($survey as &$report) {
            $report->NVKinhDoanh_AVGPoint = ($report->SoLuongKD > 0) ? round($report->NVKinhDoanhPoint / $report->SoLuongKD, 2) : 0;
            $report->NVTrienKhai_AVGPoint = ($report->SoLuongTK > 0) ? round($report->NVTrienKhaiPoint / $report->SoLuongTK, 2) : 0;
            $report->DGDichVu_Net_AVGPoint = ($report->SoLuongDGDV_Net > 0) ? round($report->DGDichVu_Net_Point / $report->SoLuongDGDV_Net, 2) : 0;

            $report->NVBaoTri_AVGPoint = ($report->SoLuongNVBaoTri > 0) ? round($report->NVBaoTriPoint / $report->SoLuongNVBaoTri, 2) : 0;
            $report->DVBaoTri_Net_AVGPoint = ($report->SoLuongDVBaoTri_Net > 0) ? round($report->DVBaoTri_Net_Point / $report->SoLuongDVBaoTri_Net, 2) : 0;
        }
        $arrCountry = $this->modelSurveySections->getCSATInfoByAll($fromDay, $today); //lấy thông tin CSAT
        $arrCountry = json_decode(json_encode($arrCountry), 1); //chuyển về dạng array
        $arrCountry[0]['KhuVuc'] = 'WholeCountry';
        $arrCountry[0]['NVKinhDoanh_AVGPoint'] = ($arrCountry[0]['NVKinhDoanhPoint'] > 0) ? round($arrCountry[0]['NVKinhDoanhPoint'] / $arrCountry[0]['SoLuongKD'], 2) : 0; //Tat ca khu vuc
        $arrCountry[0]['NVTrienKhai_AVGPoint'] = ($arrCountry[0]['NVTrienKhaiPoint'] > 0) ? round($arrCountry[0]['NVTrienKhaiPoint'] / $arrCountry[0]['SoLuongTK'], 2) : 0;
        $arrCountry[0]['DGDichVu_Net_AVGPoint'] = ($arrCountry[0]['DGDichVu_Net_Point'] > 0) ? round($arrCountry[0]['DGDichVu_Net_Point'] / $arrCountry[0]['SoLuongDGDV_Net'], 2) : 0;

        $arrCountry[0]['NVBaoTri_AVGPoint'] = ($arrCountry[0]['NVBaoTriPoint'] > 0) ? round($arrCountry[0]['NVBaoTriPoint'] / $arrCountry[0]['SoLuongNVBaoTri'], 2) : 0;
        $arrCountry[0]['DVBaoTri_Net_AVGPoint'] = ($arrCountry[0]['DVBaoTri_Net_Point'] > 0) ? round($arrCountry[0]['DVBaoTri_Net_Point'] / $arrCountry[0]['SoLuongDVBaoTri_Net'], 2) : 0;


        return ['survey_branches' => $survey, 'arrCountry' => $arrCountry];
    }

    private function getNpsByRegionBranch($from_date, $to_date) {
        $surveyBranches = $this->modelSurveySections->getNPSStatisticReportByBranches($from_date, $to_date, []) ;
        $npsBranches = $npsBranchesTK = $npsBranchesSBT = [];
        $sumNPSCountryRegion = ['UngHo' => 0, 'KhongUngHo' => 0, 'TongCong' => 0,
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

        return $npsBranches;
    }

    private function getCsatLastWeekOld() {
        $lastweekCSAT = $this->checkAndSetCache('lastweekCSATDashboard_' . $this->firstDayOfLastWeek . '_' . $this->lastDayOfLastWeek, 1, date('Y-m-d 00:00:00', strtotime("last week monday")), date('Y-m-d 23:59:59', strtotime("last week sunday")));

        $arrCSATLastweek['NVKinhDoanh_AVGPoint'] = ($lastweekCSAT[0]->NVKinhDoanhPoint > 0) ? round($lastweekCSAT[0]->NVKinhDoanhPoint / $lastweekCSAT[0]->SoLuongKD, 2) : 0; //7 vùng
        $arrCSATLastweek['NVTrienKhai_AVGPoint'] = ($lastweekCSAT[0]->NVTrienKhaiPoint > 0) ? round($lastweekCSAT[0]->NVTrienKhaiPoint / $lastweekCSAT[0]->SoLuongTK, 2) : 0;
        $arrCSATLastweek['DGDichVu_Net_AVGPoint'] = ($lastweekCSAT[0]->DGDichVu_Net_Point > 0) ? round($lastweekCSAT[0]->DGDichVu_Net_Point / $lastweekCSAT[0]->SoLuongDGDV_Net, 2) : 0;
        $arrCSATLastweek['DGDichVu_TV_AVGPoint'] = ($lastweekCSAT[0]->DGDichVu_TV_Point > 0) ? round($lastweekCSAT[0]->DGDichVu_TV_Point / $lastweekCSAT[0]->SoLuongDGDV_TV, 2) : 0;

        $arrCSATLastweek['NVKinhDoanhTS_AVGPoint'] = ($lastweekCSAT[0]->NVKinhDoanhTSPoint > 0) ? round($lastweekCSAT[0]->NVKinhDoanhTSPoint / $lastweekCSAT[0]->SoLuongKDTS, 2) : 0;
        $arrCSATLastweek['NVTrienKhaiTS_AVGPoint'] = ($lastweekCSAT[0]->NVTrienKhaiTSPoint > 0) ? round($lastweekCSAT[0]->NVTrienKhaiTSPoint / $lastweekCSAT[0]->SoLuongTKTS, 2) : 0;
        $arrCSATLastweek['DGDichVuTS_Net_AVGPoint'] = ($lastweekCSAT[0]->DGDichVuTS_Net_Point > 0) ? round($lastweekCSAT[0]->DGDichVuTS_Net_Point / $lastweekCSAT[0]->SoLuongDGDVTS_Net, 2) : 0;
        $arrCSATLastweek['DGDichVuTS_TV_AVGPoint'] = ($lastweekCSAT[0]->DGDichVuTS_TV_Point > 0) ? round($lastweekCSAT[0]->DGDichVuTS_TV_Point / $lastweekCSAT[0]->SoLuongDGDVTS_TV, 2) : 0;

        $arrCSATLastweek['NVBaoTriTIN_AVGPoint'] = ($lastweekCSAT[0]->NVBaoTriTINPoint > 0) ? round($lastweekCSAT[0]->NVBaoTriTINPoint / $lastweekCSAT[0]->SoLuongNVBaoTriTIN, 2) : 0;
        $arrCSATLastweek['NVBaoTriINDO_AVGPoint'] = ($lastweekCSAT[0]->NVBaoTriINDOPoint > 0) ? round($lastweekCSAT[0]->NVBaoTriINDOPoint / $lastweekCSAT[0]->SoLuongNVBaoTriINDO, 2) : 0;
        $arrCSATLastweek['DVBaoTriTIN_Net_AVGPoint'] = ($lastweekCSAT[0]->DVBaoTriTIN_Net_Point > 0) ? round($lastweekCSAT[0]->DVBaoTriTIN_Net_Point / $lastweekCSAT[0]->SoLuongDVBaoTriTIN_Net, 2) : 0;
        $arrCSATLastweek['DVBaoTriTIN_TV_AVGPoint'] = ($lastweekCSAT[0]->DVBaoTriTIN_TV_Point > 0) ? round($lastweekCSAT[0]->DVBaoTriTIN_TV_Point / $lastweekCSAT[0]->SoLuongDVBaoTriTIN_TV, 2) : 0;
        $arrCSATLastweek['DVBaoTriINDO_Net_AVGPoint'] = ($lastweekCSAT[0]->DVBaoTriINDO_Net_Point > 0) ? round($lastweekCSAT[0]->DVBaoTriINDO_Net_Point / $lastweekCSAT[0]->SoLuongDVBaoTriINDO_Net, 2) : 0;
        $arrCSATLastweek['DVBaoTriINDO_TV_AVGPoint'] = ($lastweekCSAT[0]->DVBaoTriINDO_TV_Point > 0) ? round($lastweekCSAT[0]->DVBaoTriINDO_TV_Point / $lastweekCSAT[0]->SoLuongDVBaoTriINDO_TV, 2) : 0;
        $arrCSATLastweek['DGDichVu_MobiPay_Net_AVGPoint'] = ($lastweekCSAT[0]->SoLuongDGDV_MobiPay_Net > 0) ? round($lastweekCSAT[0]->DGDichVu_MobiPay_Net_Point / $lastweekCSAT[0]->SoLuongDGDV_MobiPay_Net, 2) : 0;
        $arrCSATLastweek['DGDichVu_MobiPay_TV_AVGPoint'] = ($lastweekCSAT[0]->SoLuongDGDV_MobiPay_TV > 0) ? round($lastweekCSAT[0]->DGDichVu_MobiPay_TV_Point / $lastweekCSAT[0]->SoLuongDGDV_MobiPay_TV, 2) : 0;

        return $arrCSATLastweek;
    }

//
//    /*
//     * lấy điểm Csat của tháng trước
//     */
//
    private function getCsatLastmonthOld() {
        //điểm CSAT toàn quốc tháng trước
        $lastmonthCSAT = $this->checkAndSetCache('lastmonthCSATDashboard_' . $this->lastMonthFirstDay . '_' . $this->lastMonthLastDay, 1, date('Y-m-01 00:00:00', strtotime('last month')), date('Y-m-t 23:59:59', strtotime('last month')));

        $arrCSATLastmonth['NVKinhDoanh_AVGPoint'] = ($lastmonthCSAT[0]->NVKinhDoanhPoint > 0) ? round($lastmonthCSAT[0]->NVKinhDoanhPoint / $lastmonthCSAT[0]->SoLuongKD, 2) : 0; //7 vùng
        $arrCSATLastmonth['NVTrienKhai_AVGPoint'] = ($lastmonthCSAT[0]->NVTrienKhaiPoint > 0) ? round($lastmonthCSAT[0]->NVTrienKhaiPoint / $lastmonthCSAT[0]->SoLuongTK, 2) : 0;
        $arrCSATLastmonth['DGDichVu_Net_AVGPoint'] = ($lastmonthCSAT[0]->DGDichVu_Net_Point > 0) ? round($lastmonthCSAT[0]->DGDichVu_Net_Point / $lastmonthCSAT[0]->SoLuongDGDV_Net, 2) : 0;
        $arrCSATLastmonth['DGDichVu_TV_AVGPoint'] = ($lastmonthCSAT[0]->DGDichVu_TV_Point > 0) ? round($lastmonthCSAT[0]->DGDichVu_TV_Point / $lastmonthCSAT[0]->SoLuongDGDV_TV, 2) : 0;

        $arrCSATLastmonth['NVKinhDoanhTS_AVGPoint'] = ($lastmonthCSAT[0]->NVKinhDoanhTSPoint > 0) ? round($lastmonthCSAT[0]->NVKinhDoanhTSPoint / $lastmonthCSAT[0]->SoLuongKDTS, 2) : 0;
        $arrCSATLastmonth['NVTrienKhaiTS_AVGPoint'] = ($lastmonthCSAT[0]->NVTrienKhaiTSPoint > 0) ? round($lastmonthCSAT[0]->NVTrienKhaiTSPoint / $lastmonthCSAT[0]->SoLuongTKTS, 2) : 0;
        $arrCSATLastmonth['DGDichVuTS_Net_AVGPoint'] = ($lastmonthCSAT[0]->DGDichVuTS_Net_Point > 0) ? round($lastmonthCSAT[0]->DGDichVuTS_Net_Point / $lastmonthCSAT[0]->SoLuongDGDVTS_Net, 2) : 0;
        $arrCSATLastmonth['DGDichVuTS_TV_AVGPoint'] = ($lastmonthCSAT[0]->DGDichVuTS_TV_Point > 0) ? round($lastmonthCSAT[0]->DGDichVuTS_TV_Point / $lastmonthCSAT[0]->SoLuongDGDVTS_TV, 2) : 0;

        $arrCSATLastmonth['NVBaoTriTIN_AVGPoint'] = ($lastmonthCSAT[0]->NVBaoTriTINPoint > 0) ? round($lastmonthCSAT[0]->NVBaoTriTINPoint / $lastmonthCSAT[0]->SoLuongNVBaoTriTIN, 2) : 0;
        $arrCSATLastmonth['NVBaoTriINDO_AVGPoint'] = ($lastmonthCSAT[0]->NVBaoTriINDOPoint > 0) ? round($lastmonthCSAT[0]->NVBaoTriINDOPoint / $lastmonthCSAT[0]->SoLuongNVBaoTriINDO, 2) : 0;
        $arrCSATLastmonth['DVBaoTriTIN_Net_AVGPoint'] = ($lastmonthCSAT[0]->DVBaoTriTIN_Net_Point > 0) ? round($lastmonthCSAT[0]->DVBaoTriTIN_Net_Point / $lastmonthCSAT[0]->SoLuongDVBaoTriTIN_Net, 2) : 0;
        $arrCSATLastmonth['DVBaoTriTIN_TV_AVGPoint'] = ($lastmonthCSAT[0]->DVBaoTriTIN_TV_Point > 0) ? round($lastmonthCSAT[0]->DVBaoTriTIN_TV_Point / $lastmonthCSAT[0]->SoLuongDVBaoTriTIN_TV, 2) : 0;
        $arrCSATLastmonth['DVBaoTriINDO_Net_AVGPoint'] = ($lastmonthCSAT[0]->DVBaoTriINDO_Net_Point > 0) ? round($lastmonthCSAT[0]->DVBaoTriINDO_Net_Point / $lastmonthCSAT[0]->SoLuongDVBaoTriINDO_Net, 2) : 0;
        $arrCSATLastmonth['DVBaoTriINDO_TV_AVGPoint'] = ($lastmonthCSAT[0]->DVBaoTriINDO_TV_Point > 0) ? round($lastmonthCSAT[0]->DVBaoTriINDO_TV_Point / $lastmonthCSAT[0]->SoLuongDVBaoTriINDO_TV, 2) : 0;
        $arrCSATLastmonth['DGDichVu_MobiPay_Net_AVGPoint'] = ($lastmonthCSAT[0]->SoLuongDGDV_MobiPay_Net > 0) ? round($lastmonthCSAT[0]->DGDichVu_MobiPay_Net_Point / $lastmonthCSAT[0]->SoLuongDGDV_MobiPay_Net, 2) : 0;
        $arrCSATLastmonth['DGDichVu_MobiPay_TV_AVGPoint'] = ($lastmonthCSAT[0]->SoLuongDGDV_MobiPay_TV > 0) ? round($lastmonthCSAT[0]->DGDichVu_MobiPay_TV_Point / $lastmonthCSAT[0]->SoLuongDGDV_MobiPay_TV, 2) : 0;
        return $arrCSATLastmonth;
    }

    /*
     * Lấy đầy đủ thông tin NPS hôm nay, hôm qua, tuần trước, tháng trước, trung bình
     */

    private function getAllNPS() {
        $summaryNps = new SummaryNps();

        $resultNps = [];
        $resultNps['today'] = $this->modelSurveySections->getNpsByTimeFromSurvey(date('Y-m-d 00:00:00'), $this->today);
        $resultNps['yesterday'] = $this->modelSurveySections->getNpsByTimeFromSurvey(date('Y-m-d 00:00:00', strtotime('-1 days')), date('Y-m-d 23:59:59', strtotime('-1 days')));
        $resultNps['lastweek'] = $this->modelSurveySections->getNpsByTimeFromSurvey(date('Y-m-d 00:00:00', strtotime("last week monday")), date('Y-m-d 23:59:59', strtotime("last week sunday")));
        $resultNps['lastmonth'] = $this->modelSurveySections->getNpsByTimeFromSurvey(date('Y-m-01 00:00:00', strtotime('last month')), date('Y-m-t 23:59:59', strtotime('last month')));
        $resultNps['Toàn Quốc'] = $this->modelSurveySections->getNpsByTimeFromSurvey($this->lastweek, $this->yesterdayTime);
        return $resultNps;
    }

    /*
     * Lấy điểm Csat theo từng vùng
     */

    private function getCsatCountry(&$survey) {
        $extraFunc = new ExtraFunction();
        //CSAT vùng
        $survey = $this->checkAndSetCache('surveyDashboard' . $this->timeCache, 6, $this->lastweek, $this->yesterdayTime);
        //CSAT vùng
        $arrCountry = ['NVKinhDoanhPoint' => 0, 'NVTrienKhaiPoint' => 0, 'DGDichVu_Net_Point' => 0, 'DGDichVu_TV_Point' => 0, 'NVBaoTriTINPoint' => 0, 'NVBaoTriINDOPoint' => 0,
            'NVKinhDoanhTSPoint' => 0, 'NVTrienKhaiTSPoint' => 0, 'DGDichVuTS_Net_Point' => 0, 'DGDichVuTS_TV_Point' => 0,
            'DVBaoTriTIN_Net_Point' => 0, 'DVBaoTriTIN_TV_Point' => 0, 'DVBaoTriINDO_Net_Point' => 0, 'DVBaoTriINDO_TV_Point' => 0, 'DGDichVu_MobiPay_Net_Point' => 0, 'DGDichVu_MobiPay_TV_Point' => 0,
            'SoLuongKD' => 0, 'SoLuongTK' => 0, 'SoLuongDGDV_Net' => 0, 'SoLuongDGDV_TV' => 0, 'SoLuongNVBaoTriTIN' => 0, 'SoLuongNVBaoTriINDO' => 0,
            'SoLuongKDTS' => 0, 'SoLuongTKTS' => 0, 'SoLuongDGDVTS_Net' => 0, 'SoLuongDGDVTS_TV' => 0,
            'SoLuongDVBaoTriTIN_Net' => 0, 'SoLuongDVBaoTriTIN_TV' => 0, 'SoLuongDVBaoTriINDO_Net' => 0, 'SoLuongDVBaoTriINDO_TV' => 0, 'SoLuongDGDV_MobiPay_Net' => 0, 'SoLuongDGDV_MobiPay_TV' => 0];
        foreach ($survey as &$report) {
            //toàn quốc
            $arrCountry['NVKinhDoanhPoint'] += $report->NVKinhDoanhPoint; //7 vùng
            $arrCountry['NVTrienKhaiPoint'] += $report->NVTrienKhaiPoint;
            $arrCountry['DGDichVu_Net_Point'] += $report->DGDichVu_Net_Point;
            $arrCountry['DGDichVu_TV_Point'] += $report->DGDichVu_TV_Point;

            $arrCountry['NVKinhDoanhTSPoint'] += $report->NVKinhDoanhTSPoint;
            $arrCountry['NVTrienKhaiTSPoint'] += $report->NVTrienKhaiTSPoint;
            $arrCountry['DGDichVuTS_Net_Point'] += $report->DGDichVuTS_Net_Point;
            $arrCountry['DGDichVuTS_TV_Point'] += $report->DGDichVuTS_TV_Point;

            $arrCountry['NVBaoTriTINPoint'] += $report->NVBaoTriTINPoint;
            $arrCountry['NVBaoTriINDOPoint'] += $report->NVBaoTriINDOPoint;
            $arrCountry['DVBaoTriTIN_Net_Point'] += $report->DVBaoTriTIN_Net_Point;
            $arrCountry['DVBaoTriTIN_TV_Point'] += $report->DVBaoTriTIN_TV_Point;
            $arrCountry['DVBaoTriINDO_Net_Point'] += $report->DVBaoTriINDO_Net_Point;
            $arrCountry['DVBaoTriINDO_TV_Point'] += $report->DVBaoTriINDO_TV_Point;
            $arrCountry['DGDichVu_MobiPay_Net_Point'] += $report->DGDichVu_MobiPay_Net_Point;
            $arrCountry['DGDichVu_MobiPay_TV_Point'] += $report->DGDichVu_MobiPay_TV_Point;
            $arrCountry['SoLuongKD'] += $report->SoLuongKD;
            $arrCountry['SoLuongTK'] += $report->SoLuongTK;
            $arrCountry['SoLuongDGDV_Net'] += $report->SoLuongDGDV_Net;
            $arrCountry['SoLuongDGDV_TV'] += $report->SoLuongDGDV_TV;

            $arrCountry['SoLuongKDTS'] += $report->SoLuongKDTS;
            $arrCountry['SoLuongTKTS'] += $report->SoLuongTKTS;
            $arrCountry['SoLuongDGDVTS_Net'] += $report->SoLuongDGDVTS_Net;
            $arrCountry['SoLuongDGDVTS_TV'] += $report->SoLuongDGDVTS_TV;

            $arrCountry['SoLuongNVBaoTriTIN'] += $report->SoLuongNVBaoTriTIN;
            $arrCountry['SoLuongNVBaoTriINDO'] += $report->SoLuongNVBaoTriINDO;
            $arrCountry['SoLuongDVBaoTriTIN_Net'] += $report->SoLuongDVBaoTriTIN_Net;
            $arrCountry['SoLuongDVBaoTriTIN_TV'] += $report->SoLuongDVBaoTriTIN_TV;
            $arrCountry['SoLuongDVBaoTriINDO_Net'] += $report->SoLuongDVBaoTriINDO_Net;
            $arrCountry['SoLuongDVBaoTriINDO_TV'] += $report->SoLuongDVBaoTriINDO_TV;
            $arrCountry['SoLuongDGDV_MobiPay_Net'] += $report->SoLuongDGDV_MobiPay_Net;
            $arrCountry['SoLuongDGDV_MobiPay_TV'] += $report->SoLuongDGDV_MobiPay_TV;

            $report->Vung = str_replace('Vung', 'Vùng', $report->Vung);
            $report->NVKinhDoanh_AVGPoint = ($report->NVKinhDoanhPoint > 0) ? round($report->NVKinhDoanhPoint / $report->SoLuongKD, 2) : 0;
            $report->NVTrienKhai_AVGPoint = ($report->NVTrienKhaiPoint > 0) ? round($report->NVTrienKhaiPoint / $report->SoLuongTK, 2) : 0;
            $report->DGDichVu_Net_AVGPoint = ($report->SoLuongDGDV_Net > 0) ? round($report->DGDichVu_Net_Point / $report->SoLuongDGDV_Net, 2) : 0;
            $report->DGDichVu_TV_AVGPoint = ($report->SoLuongDGDV_TV > 0) ? round($report->DGDichVu_TV_Point / $report->SoLuongDGDV_TV, 2) : 0;

            $report->NVKinhDoanhTS_AVGPoint = ($report->NVKinhDoanhTSPoint > 0) ? round($report->NVKinhDoanhTSPoint / $report->SoLuongKDTS, 2) : 0;
            $report->NVTrienKhaiTS_AVGPoint = ($report->NVTrienKhaiTSPoint > 0) ? round($report->NVTrienKhaiTSPoint / $report->SoLuongTKTS, 2) : 0;
            $report->DGDichVuTS_Net_AVGPoint = ($report->DGDichVuTS_Net_Point > 0) ? round($report->DGDichVuTS_Net_Point / $report->SoLuongDGDVTS_Net, 2) : 0;
            $report->DGDichVuTS_TV_AVGPoint = ($report->DGDichVuTS_TV_Point > 0) ? round($report->DGDichVuTS_TV_Point / $report->SoLuongDGDVTS_TV, 2) : 0;


            $report->DGDichVu_MobiPay_Net_AVGPoint = ($report->SoLuongDGDV_MobiPay_Net > 0) ? round($report->DGDichVu_MobiPay_Net_Point / $report->SoLuongDGDV_MobiPay_Net, 2) : 0;
            $report->DGDichVu_MobiPay_TV_AVGPoint = ($report->SoLuongDGDV_MobiPay_TV > 0) ? round($report->DGDichVu_MobiPay_TV_Point / $report->SoLuongDGDV_MobiPay_TV, 2) : 0;
            $report->NVBaoTriTIN_AVGPoint = ($report->NVBaoTriTINPoint > 0) ? round($report->NVBaoTriTINPoint / $report->SoLuongNVBaoTriTIN, 2) : 0;
            $report->NVBaoTriINDO_AVGPoint = ($report->NVBaoTriINDOPoint > 0) ? round($report->NVBaoTriINDOPoint / $report->SoLuongNVBaoTriINDO, 2) : 0;
            $report->DVBaoTriTIN_Net_AVGPoint = ($report->DVBaoTriTIN_Net_Point > 0) ? round($report->DVBaoTriTIN_Net_Point / $report->SoLuongDVBaoTriTIN_Net, 2) : 0;
            $report->DVBaoTriTIN_TV_AVGPoint = ($report->DVBaoTriTIN_TV_Point > 0) ? round($report->DVBaoTriTIN_TV_Point / $report->SoLuongDVBaoTriTIN_TV, 2) : 0;
            $report->DVBaoTriINDO_Net_AVGPoint = ($report->SoLuongDVBaoTriINDO_Net > 0) ? round($report->DVBaoTriINDO_Net_Point / $report->SoLuongDVBaoTriINDO_Net, 2) : 0;
            $report->DVBaoTriINDO_TV_AVGPoint = ($report->SoLuongDVBaoTriINDO_TV > 0) ? round($report->DVBaoTriINDO_TV_Point / $report->SoLuongDVBaoTriINDO_TV, 2) : 0;
        }
        //sort giá trị theo field
        $extraFunc->sortOnField($survey, 'NVKinhDoanh_AVGPoint', 'DESC');

        $arrCountry['Vung'] = 'Toàn Quốc';
        $arrCountry['NVKinhDoanh_AVGPoint'] = ($arrCountry['NVKinhDoanhPoint'] > 0) ? round($arrCountry['NVKinhDoanhPoint'] / $arrCountry['SoLuongKD'], 2) : 0; //7 vùng
        $arrCountry['NVTrienKhai_AVGPoint'] = ($arrCountry['NVTrienKhaiPoint'] > 0) ? round($arrCountry['NVTrienKhaiPoint'] / $arrCountry['SoLuongTK'], 2) : 0;
        $arrCountry['DGDichVu_Net_AVGPoint'] = ($arrCountry['DGDichVu_Net_Point'] > 0) ? round($arrCountry['DGDichVu_Net_Point'] / $arrCountry['SoLuongDGDV_Net'], 2) : 0;
        $arrCountry['DGDichVu_TV_AVGPoint'] = ($arrCountry['DGDichVu_TV_Point'] > 0) ? round($arrCountry['DGDichVu_TV_Point'] / $arrCountry['SoLuongDGDV_TV'], 2) : 0;

        $arrCountry['NVKinhDoanhTS_AVGPoint'] = ($arrCountry['NVKinhDoanhTSPoint'] > 0) ? round($arrCountry['NVKinhDoanhTSPoint'] / $arrCountry['SoLuongKDTS'], 2) : 0;
        $arrCountry['NVTrienKhaiTS_AVGPoint'] = ($arrCountry['NVTrienKhaiTSPoint'] > 0) ? round($arrCountry['NVTrienKhaiTSPoint'] / $arrCountry['SoLuongTKTS'], 2) : 0;
        $arrCountry['DGDichVuTS_Net_AVGPoint'] = ($arrCountry['DGDichVuTS_Net_Point'] > 0) ? round($arrCountry['DGDichVuTS_Net_Point'] / $arrCountry['SoLuongDGDVTS_Net'], 2) : 0;
        $arrCountry['DGDichVuTS_TV_AVGPoint'] = ($arrCountry['DGDichVuTS_TV_Point'] > 0) ? round($arrCountry['DGDichVuTS_TV_Point'] / $arrCountry['SoLuongDGDVTS_TV'], 2) : 0;

        $arrCountry['NVBaoTriTIN_AVGPoint'] = ($arrCountry['NVBaoTriTINPoint'] > 0) ? round($arrCountry['NVBaoTriTINPoint'] / $arrCountry['SoLuongNVBaoTriTIN'], 2) : 0;
        $arrCountry['NVBaoTriINDO_AVGPoint'] = ($arrCountry['NVBaoTriINDOPoint'] > 0) ? round($arrCountry['NVBaoTriINDOPoint'] / $arrCountry['SoLuongNVBaoTriINDO'], 2) : 0;
        $arrCountry['DVBaoTriTIN_Net_AVGPoint'] = ($arrCountry['DVBaoTriTIN_Net_Point'] > 0) ? round($arrCountry['DVBaoTriTIN_Net_Point'] / $arrCountry['SoLuongDVBaoTriTIN_Net'], 2) : 0;
        $arrCountry['DVBaoTriTIN_TV_AVGPoint'] = ($arrCountry['DVBaoTriTIN_TV_Point'] > 0) ? round($arrCountry['DVBaoTriTIN_TV_Point'] / $arrCountry['SoLuongDVBaoTriTIN_TV'], 2) : 0;
        $arrCountry['DVBaoTriINDO_Net_AVGPoint'] = ($arrCountry['DVBaoTriINDO_Net_Point'] > 0) ? round($arrCountry['DVBaoTriINDO_Net_Point'] / $arrCountry['SoLuongDVBaoTriINDO_Net'], 2) : 0;
        $arrCountry['DVBaoTriINDO_TV_AVGPoint'] = ($arrCountry['DVBaoTriINDO_TV_Point'] > 0) ? round($arrCountry['DVBaoTriINDO_TV_Point'] / $arrCountry['SoLuongDVBaoTriINDO_TV'], 2) : 0;
        $arrCountry['DGDichVu_MobiPay_Net_AVGPoint'] = ($arrCountry['DGDichVu_MobiPay_Net_Point'] > 0) ? round($arrCountry['DGDichVu_MobiPay_Net_Point'] / $arrCountry['SoLuongDGDV_MobiPay_Net'], 2) : 0;
        $arrCountry['DGDichVu_MobiPay_TV_AVGPoint'] = ($arrCountry['DGDichVu_MobiPay_TV_Point'] > 0) ? round($arrCountry['DGDichVu_MobiPay_TV_Point'] / $arrCountry['SoLuongDGDV_MobiPay_TV'], 2) : 0;
        return $arrCountry;
    }

    /*
     * lấy điểm CSAT theo từng chi nhánh
     */

    private function getCsatbranches() {
        $extraFunc = new ExtraFunction();
        //CSAT chi nhánh
        $surveyBranches = $this->checkAndSetCache('surveyBranchesDashboard' . $this->timeCache, 7, $this->lastweek, $this->yesterdayTime);

        foreach ($surveyBranches as &$branches) {
            $branches->Vung = str_replace('Vung', 'Vùng', $branches->Vung);
            $branches->NVKinhDoanh_AVGPoint = ($branches->NVKinhDoanhPoint > 0) ? round($branches->NVKinhDoanhPoint / $branches->SoLuongKD, 2) : 0;
            $branches->NVTrienKhai_AVGPoint = ($branches->NVTrienKhaiPoint > 0) ? round($branches->NVTrienKhaiPoint / $branches->SoLuongTK, 2) : 0;
            $branches->DGDichVu_Net_AVGPoint = ($branches->DGDichVu_Net_Point > 0) ? round($branches->DGDichVu_Net_Point / $branches->SoLuongDGDV_Net, 2) : 0;
            $branches->DGDichVu_TV_AVGPoint = ($branches->DGDichVu_TV_Point > 0) ? round($branches->DGDichVu_TV_Point / $branches->SoLuongDGDV_TV, 2) : 0;

            $branches->NVKinhDoanhTS_AVGPoint = ($branches->NVKinhDoanhTSPoint > 0) ? round($branches->NVKinhDoanhTSPoint / $branches->SoLuongKDTS, 2) : 0;
            $branches->NVTrienKhaiTS_AVGPoint = ($branches->NVTrienKhaiTSPoint > 0) ? round($branches->NVTrienKhaiTSPoint / $branches->SoLuongTKTS, 2) : 0;
            $branches->DGDichVuTS_Net_AVGPoint = ($branches->DGDichVuTS_Net_Point > 0) ? round($branches->DGDichVuTS_Net_Point / $branches->SoLuongDGDVTS_Net, 2) : 0;
            $branches->DGDichVuTS_TV_AVGPoint = ($branches->DGDichVuTS_TV_Point > 0) ? round($branches->DGDichVuTS_TV_Point / $branches->SoLuongDGDVTS_TV, 2) : 0;

            $branches->NVBaoTriTIN_AVGPoint = ($branches->NVBaoTriTINPoint > 0) ? round($branches->NVBaoTriTINPoint / $branches->SoLuongNVBaoTriTIN, 2) : 0;
            $branches->NVBaoTriINDO_AVGPoint = ($branches->NVBaoTriINDOPoint > 0) ? round($branches->NVBaoTriINDOPoint / $branches->SoLuongNVBaoTriINDO, 2) : 0;
            $branches->DVBaoTriTIN_Net_AVGPoint = ($branches->DVBaoTriTIN_Net_Point > 0) ? round($branches->DVBaoTriTIN_Net_Point / $branches->SoLuongDVBaoTriTIN_Net, 2) : 0;
            $branches->DVBaoTriTIN_TV_AVGPoint = ($branches->DVBaoTriTIN_TV_Point > 0) ? round($branches->DVBaoTriTIN_TV_Point / $branches->SoLuongDVBaoTriTIN_TV, 2) : 0;
            $branches->DVBaoTriINDO_Net_AVGPoint = ($branches->DVBaoTriINDO_Net_Point > 0) ? round($branches->DVBaoTriINDO_Net_Point / $branches->SoLuongDVBaoTriINDO_Net, 2) : 0;
            $branches->DVBaoTriINDO_TV_AVGPoint = ($branches->DVBaoTriINDO_TV_Point > 0) ? round($branches->DVBaoTriINDO_TV_Point / $branches->SoLuongDVBaoTriINDO_TV, 2) : 0;
            $branches->DGDichVu_MobiPay_Net_AVGPoint = ($branches->DGDichVu_MobiPay_Net_Point > 0) ? round($branches->DGDichVu_MobiPay_Net_Point / $branches->SoLuongDGDV_MobiPay_Net, 2) : 0;
            $branches->DGDichVu_MobiPay_TV_AVGPoint = ($branches->DGDichVu_MobiPay_TV_Point > 0) ? round($branches->DGDichVu_MobiPay_TV_Point / $branches->SoLuongDGDV_MobiPay_TV, 2) : 0;
        }
        //sort giá trị theo field
        $extraFunc->sortOnField($surveyBranches, 'NVKinhDoanh_AVGPoint', 'DESC');
        return $surveyBranches;
    }

    /*
     * điểm trung bình NPS của 7 vùng
     * trả về 2 mảng chứa danh sách NPS của vùng và Tổng
     */

    private function npsRegion() {
        $surveyRegion = $this->checkAndSetCache('surveyRegionDashboard' . $this->timeCache, 4, $this->lastweek, $this->yesterdayTime);
        $npsRegion = [];
        $sumNPSCountryRegion = ['UngHo' => 0, 'KhongUngHo' => 0, 'TongCong' => 0];
        foreach ($surveyRegion as $res) {
            $res->Vung = str_replace('Vung', 'Vùng', $res->Vung);
            $npsRegion[$res->Vung] = ($res->TongCong > 0) ? (($res->UngHo - $res->KhongUngHo) / $res->TongCong) * 100 : 0; //tỉ lệ NPS
            //toàn quốc NPS theo vùng miền
            $sumNPSCountryRegion['UngHo'] += $res->UngHo;
            $sumNPSCountryRegion['KhongUngHo'] += $res->KhongUngHo;
            $sumNPSCountryRegion['TongCong'] += $res->TongCong;
        }
        //sort
        arsort($npsRegion);
        $npsCountryRegion['Toàn Quốc'] = 0;
        if ($sumNPSCountryRegion['TongCong'] > 0) {
            $npsCountryRegion['Toàn Quốc'] = (($sumNPSCountryRegion['UngHo'] - $sumNPSCountryRegion['KhongUngHo']) / $sumNPSCountryRegion['TongCong']) * 100;
            $npsCountryRegion['Toàn Quốc'] = round($npsCountryRegion['Toàn Quốc'], 2); //làm tròn số
        }

        return array('npsRegion' => $npsRegion,
            'npsCountryRegion' => $npsCountryRegion
        );
    }

    private function npsBranches() {
        $surveyNPSBranches = $this->checkAndSetCache('surveyNPSBranchesDashboard' . $this->timeCache, 5, $this->lastweek, $this->yesterdayTime);
//        dump($surveyNPSBranches);die;
        $npsBranches = [];
        $sumNPSCountryBranches = ['UngHo' => 0, 'KhongUngHo' => 0, 'TongCong' => 0];
        //lấy tổng các thông số độ ủng hộ NPS
//        dump($surveyNPSBranches);die;
        foreach ($surveyNPSBranches as $res) {
            $branch = explode('-', $res->ChiNhanh);
            $branch = isset($branch[2]) ? (($branch[2] != 0) ? trim($branch[0]) . trim($branch[2]) . " - " . trim($branch[1]) : trim($branch[0]) . " - " . trim($branch[1])) : $branch[0];
//            $branch=$branch[0].'-'.$branch[1];
            $npsBranches[$branch] = round($res->NPS, 2); //tỉ lệ NPS
            //toàn quốc NPS theo vùng miền
            $sumNPSCountryBranches['UngHo'] += $res->UngHo;
            $sumNPSCountryBranches['KhongUngHo'] += $res->KhongUngHo;
            $sumNPSCountryBranches['TongCong'] += $res->TongCong;
        }
        //sort
        arsort($npsBranches);

        $npsCountryBranches['Toàn Quốc'] = 0;
        if ($sumNPSCountryBranches['TongCong'] > 0) {
            $npsCountryBranches['Toàn Quốc'] = (($sumNPSCountryBranches['UngHo'] - $sumNPSCountryBranches['KhongUngHo']) / $sumNPSCountryBranches['TongCong']) * 100;
            $npsCountryBranches['Toàn Quốc'] = round($npsCountryBranches['Toàn Quốc'], 2); //làm tròn số
        }
        return array('npsBranches' => $npsBranches,
            'npsCountryBranches' => $npsCountryBranches,
        );
    }

    //hàm kiểm tra & tạo cache
    private function checkAndSetCache($keyName, $type, $fromDate, $toDate) {
        $modelSurvey = new SurveySections();
        $result = Redis::get($keyName);
        if (empty($result)) {
            switch ($type) {
                case 1:
                    $result = $modelSurvey->getAllCSATInfoByDate_new($fromDate, $toDate);
                    break;
                case 2:
                    $result = $modelSurvey->getAllNPSInfoByDate_new($fromDate, $toDate);
                    break;
                case 3:
                    $result = $this->SurveyQuantityReport('', $fromDate, $toDate, [], []);
                    break;
                case 4:
                    $result = $modelSurvey->getNPSStatisticReportByBranches('', $fromDate, $toDate, []); //lấy thông tin theo vùng miền
                    break;
                case 5:
                case 12:
                    $result = $modelSurvey->getNPSStatisticReportByBranches('', $fromDate, $toDate, '', []); //lấy thông tin NPS theo chi nhánh
//
                    break;
                case 6:
                    $result = $modelSurvey->getCSATInfoByRegion('', $fromDate, $toDate, []); //lấy thông tin CSAT theo vùng
//                    dump($result);die;
                    break;
                case 8:
                    $result = $this->getCSATReportDetail('', $fromDate, $toDate, [], []);
                    break;
                case 9:
                    $result = $this->getNPSStatisticReportDetail('', $fromDate, $toDate, [], []);
                    break;
                case 10:
//                    $result = $this->getCustomersCommentReportDetail('', $fromDate, $toDate, [], []);
                    $result = $modelSurvey->getCustommerCommentReport($fromDate, $toDate, '', '', 0);
                    break;
                default:
                    $result = array();
            }

            Redis::set($keyName, json_encode($result));
            Redis::expire($keyName, 3600);
        }
        //ktra chuỗi json
        if (is_string($result)) {
            $result = json_decode($result);
        }
        return $result;
    }

    //hàm lấy thông tin CSAT toàn quốc chi tiết
    private function getCSATReportDetail($region, $fromDate, $toDate, $branch, $branchcode) {
        $survey = $this->modelSurveySections->getCSATInfo($region, $fromDate, $toDate, $branch, $branchcode); //lấy thông tin CSAT
        $total = $t = $avg = ['NVKinhDoanh' => 0, 'NVTrienKhai' => 0, 'DGDichVu_Net' => 0, 'DGDichVu_TV' => 0,
            'NVKinhDoanhTS' => 0, 'NVTrienKhaiTS' => 0, 'DGDichVuTS_Net' => 0, 'DGDichVuTS_TV' => 0,
            'NVBaoTriTIN' => 0, 'NVBaoTriINDO' => 0, 'DVBaoTriTIN_Net' => 0, 'DVBaoTriTIN_TV' => 0, 'DVBaoTriINDO_Net' => 0, 'DVBaoTriINDO_TV' => 0, 'DGDichVu_MobiPay_Net' => 0, 'DGDichVu_MobiPay_TV' => 0, 'NVThuCuoc' => 0, 'NV_Counter' => 0, 'DGDichVu_Counter' => 0, 'NVKinhDoanhSS' => 0, 'NVTrienKhaiSS' => 0, 'DGDichVuSS_Net' => 0, 'DGDichVuSS_TV' => 0, 'NVBT_SSW' => 0, 'DGDichVuSSW_Net' => 0, 'DGDichVuSSW_TV' => 0];
        //lấy tổng các thông số đánh giá điểm CSAT
        foreach ($survey as $report) {
            $total['NVKinhDoanhTS'] += $report->NVKinhDoanhTS;
            $t['NVKinhDoanhTS'] += $report->NVKinhDoanhTS * $report->answers_point;
            $total['NVTrienKhaiTS'] += $report->NVTrienKhaiTS;
            $t['NVTrienKhaiTS'] += $report->NVTrienKhaiTS * $report->answers_point;
            $total['DGDichVuTS_Net'] += $report->DGDichVuTS_Net;
            $t['DGDichVuTS_Net'] += $report->DGDichVuTS_Net * $report->answers_point;
            $total['DGDichVuTS_TV'] += $report->DGDichVuTS_TV;
            $t['DGDichVuTS_TV'] += $report->DGDichVuTS_TV * $report->answers_point;

            $total['NVKinhDoanh'] += $report->NVKinhDoanh;
            $t['NVKinhDoanh'] += $report->NVKinhDoanh * $report->answers_point;
            $total['NVTrienKhai'] += $report->NVTrienKhai;
            $t['NVTrienKhai'] += $report->NVTrienKhai * $report->answers_point;
            $total['DGDichVu_Net'] += $report->DGDichVu_Net;
            $t['DGDichVu_Net'] += $report->DGDichVu_Net * $report->answers_point;
            $total['DGDichVu_TV'] += $report->DGDichVu_TV;
            $t['DGDichVu_TV'] += $report->DGDichVu_TV * $report->answers_point;

            $total['NVBaoTriTIN'] += $report->NVBaoTriTIN;
            $t['NVBaoTriTIN'] += $report->NVBaoTriTIN * $report->answers_point;
            $total['NVBaoTriINDO'] += $report->NVBaoTriINDO;
            $t['NVBaoTriINDO'] += $report->NVBaoTriINDO * $report->answers_point;
            $total['DVBaoTriTIN_Net'] += $report->DVBaoTriTIN_Net;
            $t['DVBaoTriTIN_Net'] += $report->DVBaoTriTIN_Net * $report->answers_point;
            $total['DVBaoTriTIN_TV'] += $report->DVBaoTriTIN_TV;
            $t['DVBaoTriTIN_TV'] += $report->DVBaoTriTIN_TV * $report->answers_point;
            $total['DVBaoTriINDO_Net'] += $report->DVBaoTriINDO_Net;
            $t['DVBaoTriINDO_Net'] += $report->DVBaoTriINDO_Net * $report->answers_point;
            $total['DVBaoTriINDO_TV'] += $report->DVBaoTriINDO_TV;
            $t['DVBaoTriINDO_TV'] += $report->DVBaoTriINDO_TV * $report->answers_point;
            $total['DGDichVu_MobiPay_Net'] += $report->DGDichVu_MobiPay_Net;
            $t['DGDichVu_MobiPay_Net'] += $report->DGDichVu_MobiPay_Net * $report->answers_point;
            $total['DGDichVu_MobiPay_TV'] += $report->DGDichVu_MobiPay_TV;
            $t['DGDichVu_MobiPay_TV'] += $report->DGDichVu_MobiPay_TV * $report->answers_point;

            $total['NVThuCuoc'] += $report->NVThuCuoc;
            $t['NVThuCuoc'] += $report->NVThuCuoc * $report->answers_point;

            $total['NV_Counter'] += $report->NV_Counter;
            $t['NV_Counter'] += $report->NV_Counter * $report->answers_point;
            $total['DGDichVu_Counter'] += $report->DGDichVu_Counter;
            $t['DGDichVu_Counter'] += $report->DGDichVu_Counter * $report->answers_point;

            $total['NVKinhDoanhSS'] += $report->NVKinhDoanhSS;
            $t['NVKinhDoanhSS'] += $report->NVKinhDoanhSS * $report->answers_point;
            $total['NVTrienKhaiSS'] += $report->NVTrienKhaiSS;
            $t['NVTrienKhaiSS'] += $report->NVTrienKhaiSS * $report->answers_point;
            $total['DGDichVuSS_Net'] += $report->DGDichVuSS_Net;
            $t['DGDichVuSS_Net'] += $report->DGDichVuSS_Net * $report->answers_point;
            $total['DGDichVuSS_TV'] += $report->DGDichVuSS_TV;
            $t['DGDichVuSS_TV'] += $report->DGDichVuSS_TV * $report->answers_point;

            $total['NVBT_SSW'] += $report->NVBT_SSW;
            $t['NVBT_SSW'] += $report->NVBT_SSW * $report->answers_point;
            $total['DGDichVuSSW_Net'] += $report->DGDichVuSSW_Net;
            $t['DGDichVuSSW_Net'] += $report->DGDichVuSSW_Net * $report->answers_point;
            $total['DGDichVuSSW_TV'] += $report->DGDichVuSSW_TV;
            $t['DGDichVuSSW_TV'] += $report->DGDichVuSSW_TV * $report->answers_point;
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
        $tempRate = [1 => 'Rất không hài lòng (CSAT 1)', 2 => 'Không hài lòng (CSAT 2)', 3 => 'Trung lập (CSAT 3)', 4 => 'Hài lòng (CSAT 4)', 5 => 'Rất hài lòng (CSAT 5)'];
        for ($i = 1; $i <= 5; $i++) {
            $obj = new \stdClass();
            $obj->answers_point = $i;
            $obj->NVKinhDoanhTS = $obj->NVTrienKhaiTS = $obj->DGDichVuTS_Net = $obj->DGDichVuTS_TV = $obj->NVKinhDoanh = $obj->NVTrienKhai = $obj->DGDichVu_Net = $obj->DGDichVu_TV = $obj->DVBaoTriTIN_Net = $obj->NVBaoTriTIN = $obj->DVBaoTriTIN_TV = $obj->NVBaoTriINDO = $obj->DVBaoTriINDO_Net = $obj->DVBaoTriINDO_TV = $obj->DGDichVu_MobiPay_Net = $obj->DGDichVu_MobiPay_TV = $obj->NVThuCuoc = $obj->NV_Counter = $obj->DGDichVu_Counter = $obj->NVKinhDoanhSS = $obj->NVTrienKhaiSS = $obj->DGDichVuSS_Net = $obj->DGDichVuSS_TV = $obj->NVBT_SSW = $obj->DGDichVuSSW_Net = $obj->DGDichVuSSW_TV = 0;
            $obj->DanhGia = $tempRate[$i];
            $arr1['arr' . $i] = $obj;
        }
        $survey = array_values(array_merge($arr1, $arr));
        $result = new \stdClass();
        $result->survey = !empty($survey) ? $survey : '';
        $result->avg = !empty($avg) ? $avg : 0;
        $result->total = !empty($total) ? $total : 0;
        return $result;
    }

    //hàm lấy thông tin NPS toàn quốc chi tiết
    private function getNPSStatisticReportDetail($region, $fromDate, $toDate, $branch, $branchcode) {
        $survey = $this->modelSurveySections->getNPSStatisticReport($region, $fromDate, $toDate, $branch, $branchcode); //lấy thông tin NPS thống kê
        $total = $newSurvey1 = $newSurvey2 = $newSurvey3 = ['SauTK' => 0, 'SauTKTS' => 0, 'SauBTTIN' => 0, 'SauBTINDO' => 0, 'SauTC' => 0, 'SauGDTQ' => 0, 'SauTKS' => 0, 'SauSSW' => 0, 'TongCong' => 0];
        $newSurvey1['type'] = trans('report.Unsupported');
        $newSurvey2['type'] = trans('report.Neutral');
        $newSurvey3['type'] = trans('report.Supported');
        //lấy tổng các thông số Thống kê điểm NPS
        foreach ($survey as $report) {
            if ($report->answers_point >= 0 && $report->answers_point <= 6) {
                $newSurvey1['SauTK'] += $report->SauTK;
                $newSurvey1['SauTKTS'] += $report->SauTKTS;
                $newSurvey1['SauBTTIN'] += $report->SauBTTIN;
                $newSurvey1['SauBTINDO'] += $report->SauBTINDO;
                $newSurvey1['SauTC'] += $report->SauTC;
                $newSurvey1['SauGDTQ'] += $report->SauGDTQ;
                $newSurvey1['SauTKS'] += $report->SauTKS;
                $newSurvey1['SauSSW'] += $report->SauSSW;
                $newSurvey1['TongCong'] += $report->TongCong;
            } else if ($report->answers_point >= 7 && $report->answers_point <= 8) {
                $newSurvey2['SauTK'] += $report->SauTK;
                $newSurvey2['SauTKTS'] += $report->SauTKTS;
                $newSurvey2['SauBTTIN'] += $report->SauBTTIN;
                $newSurvey2['SauBTINDO'] += $report->SauBTINDO;
                $newSurvey2['SauTC'] += $report->SauTC;
                $newSurvey2['SauGDTQ'] += $report->SauGDTQ;
                $newSurvey2['SauTKS'] += $report->SauTKS;
                $newSurvey2['SauSSW'] += $report->SauSSW;
                $newSurvey2['TongCong'] += $report->TongCong;
            } else if ($report->answers_point >= 9 && $report->answers_point <= 10) {
                $newSurvey3['SauTK'] += $report->SauTK;
                $newSurvey3['SauTKTS'] += $report->SauTKTS;
                $newSurvey3['SauBTTIN'] += $report->SauBTTIN;
                $newSurvey3['SauBTINDO'] += $report->SauBTINDO;
                $newSurvey3['SauTC'] += $report->SauTC;
                $newSurvey3['SauGDTQ'] += $report->SauGDTQ;
                $newSurvey3['SauTKS'] += $report->SauTKS;
                $newSurvey3['SauSSW'] += $report->SauSSW;
                $newSurvey3['TongCong'] += $report->TongCong;
            }
            //tổng
            $total['SauTK'] += $report->SauTK;
            $total['SauTKTS'] += $report->SauTKTS;
            $total['SauBTTIN'] += $report->SauBTTIN;
            $total['SauBTINDO'] += $report->SauBTINDO;
            $total['SauTC'] += $report->SauTC;
            $total['SauGDTQ'] += $report->SauGDTQ;
            $total['SauTKS'] += $report->SauTKS;
            $total['SauSSW'] += $report->SauSSW;
            $total['TongCong'] += $report->TongCong;
        }
        //bổ sung các điểm còn thiếu
        $arr = $arr1 = [];
        foreach ($survey as $val) {
            $arr['arr' . $val->answers_point] = $val;
        }

        for ($i = 0; $i <= 10; $i++) {
            $obj = new \stdClass();
            $obj->answers_point = $i;
            $obj->SauTK = $obj->SauTKTS = $obj->SauBTTIN = $obj->SauBTINDO = $obj->SauTC = $obj->SauGDTQ = $obj->SauTKS = $obj->SauSSW = $obj->TongCong = 0;
            $arr1['arr' . $i] = $obj;
        }
        $survey = array_values(array_merge($arr1, $arr));

        $result = new \stdClass();
        $result->survey = !empty($survey) ? $survey : '';
        $result->total = !empty($total) ? $total : '';
        $result->groupNPS[] = $newSurvey1;
        $result->groupNPS[] = $newSurvey2;
        $result->groupNPS[] = $newSurvey3;
        return $result;
    }

    //hàm lấy thông tin ý kiến đóng góp KH
    private function getCustomersCommentReportDetail($region, $from_date, $to_date, $branch, $branchcode) {
        $result = $this->modelSurveySections->getCustomersCommentReport($region, $from_date, $to_date, $branch, $branchcode);
        $survey = [];
        $key = '';
        foreach ($result as $val) {
            if ($val->section_survey_id == 1) {//sau triển khai
                $key = 'SauTK';
            } else if ($val->section_survey_id == 6) {//sau triển khai telesale
                $key = 'SauTKTS';
            } else if ($val->section_survey_id == 2 && strpos($val->section_supporter, 'INDO') === FALSE) {//sau bảo trì TIN
                $key = 'SauBTTIN';
            } else if ($val->section_survey_id == 2 && strpos($val->section_supporter, 'INDO') !== FALSE) {//sau bảo trì INDO
                $key = 'SauBTINDO';
            } else if ($val->section_survey_id == 3) {// sau thu cước
                $key = 'SauTC';
            }

            if (strpos($val->nps_improvement, ',') !== FALSE) {
                $item = explode(',', $val->nps_improvement);
                foreach ($item as $val) {
                    if (!empty($val)) {
                        $survey[$val][$key] = isset($survey[$val][$key]) ? $survey[$val][$key] + 1 : 1;
                    }
                }
            } else {
                $survey[$val->nps_improvement][$key] = isset($survey[$val->nps_improvement][$key]) ? $survey[$val->nps_improvement][$key] + 1 : 1;
            }
        }
        foreach ($survey as $k => &$val) {
            $val['answer_id'] = $this->selNPSImprovement[$k]->answer_id;
            $val['answers_position'] = $this->selNPSImprovement[$k]->answers_position;
            $val['NoiDung'] = $this->selNPSImprovement[$k]->answers_title;
            $val['answers_group_title'] = $this->selNPSImprovement[$k]->answers_group_title;
            $val['answer_group'] = $this->selNPSImprovement[$k]->answer_group;
            $val['SauTK'] = isset($val['SauTK']) ? $val['SauTK'] : 0;
            $val['SauTKTS'] = isset($val['SauTKTS']) ? $val['SauTKTS'] : 0;
            $val['SauBTTIN'] = isset($val['SauBTTIN']) ? $val['SauBTTIN'] : 0;
            $val['SauBTINDO'] = isset($val['SauBTINDO']) ? $val['SauBTINDO'] : 0;
            $val['SauTC'] = isset($val['SauTC']) ? $val['SauTC'] : 0;
            $val['TongCong'] = $val['SauTK'] + $val['SauTKTS'] + $val['SauBTTIN'] + $val['SauBTINDO'] + $val['SauTC'];
            //
            $val = json_decode(json_encode($val), 0); //chuyển từ array -> object
        }
        $total = ['SauTK' => 0, 'SauTKTS' => 0, 'SauBTTIN' => 0, 'SauBTINDO' => 0, 'SauTC' => 0, 'TongCong' => 0];
        //bổ sung các góp ý khác nếu = 0
        $arrTitle = [];
        foreach ($this->selNPSImprovement as $a) {
            $item = new \stdClass();
            $item->answer_id = $a->answer_id;
            $item->answers_position = $a->answers_position;
            $item->NoiDung = $a->answers_title;
            $item->answers_group_title = $a->answers_group_title;
            $item->SauTK = $item->SauTKTS = $item->SauBTTIN = $item->SauBTINDO = $item->SauTC = $item->TongCong = 0;
            $arr[] = $item;
            //mảng chứa title của nhóm ý kiến khách hàng
            array_push($arrTitle, $a->answers_group_title);
        }
        $arrTitle = array_unique($arrTitle);

        $survey = array_merge($survey, $arr);
        $survey = $this->extraFunc->array_unique_by_key($survey, 'answer_id');

        foreach ($survey as $key => $report) {
            $survey[$key]->rowNum = array_search($report->answers_group_title, $arrTitle);
            $groupTitle[$key] = $survey[$key]->rowNum;
            $position[$key] = $report->answers_position;
            if ($report->answer_id != 84) {
                //lấy tổng các thông số Thống kê điểm NPS
                $total['SauTK'] += $report->SauTK;
                $total['SauTKTS'] += $report->SauTKTS;
                $total['SauBTTIN'] += $report->SauBTTIN;
                $total['SauBTINDO'] += $report->SauBTINDO;
                $total['SauTC'] += $report->SauTC;
                $total['TongCong'] += $report->TongCong;
            }
        }
        //sort ra đúng thứ tự ngoài màn hình
        array_multisort($groupTitle, SORT_ASC, $position, SORT_ASC, $survey);
        //get tổng số KH góp ý, tổng số KH ko góp ý, tổng số KH được hỏi ý kiến
        $totalCustomerComment = $this->modelSurveySections->getAllTotalCusComment($region, $from_date, $to_date, $branch, $branchcode);

        $totalCusComment['SauTK'] = $totalCustomerComment[0]->KHGopYSauTK;
        $totalCusComment['SauTKTS'] = $totalCustomerComment[0]->KHGopYSauTKTS;
        $totalCusComment['SauBTTIN'] = $totalCustomerComment[0]->KHGopYSauBTTIN;
        $totalCusComment['SauBTINDO'] = $totalCustomerComment[0]->KHGopYSauBTINDO;
        $totalCusComment['SauTC'] = $totalCustomerComment[0]->KHGopYSauTC;
        $totalCusComment['TongCong'] = $totalCustomerComment[0]->TongCongGopY;

        $totalCusNoComment['SauTK'] = $totalCustomerComment[0]->KHKoGopYSauTK;
        $totalCusNoComment['SauTKTS'] = $totalCustomerComment[0]->KHKoGopYSauTKTS;
        $totalCusNoComment['SauBTTIN'] = $totalCustomerComment[0]->KHKoGopYSauBTTIN;
        $totalCusNoComment['SauBTINDO'] = $totalCustomerComment[0]->KHKoGopYSauBTINDO;
        $totalCusNoComment['SauTC'] = $totalCustomerComment[0]->KHKoGopYSauTC;
        $totalCusNoComment['TongCong'] = $totalCustomerComment[0]->TongCongKoGopY;

        $totalConsulted['SauTK'] = $totalCustomerComment[0]->KHDcHoiYKienSauTK;
        $totalConsulted['SauTKTS'] = $totalCustomerComment[0]->KHDcHoiYKienSauTKTS;
        $totalConsulted['SauBTTIN'] = $totalCustomerComment[0]->KHDcHoiYKienSauBTTIN;
        $totalConsulted['SauBTINDO'] = $totalCustomerComment[0]->KHDcHoiYKienSauBTINDO;
        $totalConsulted['SauTC'] = $totalCustomerComment[0]->KHDcHoiYKienSauTC;
        $totalConsulted['TongCong'] = $totalCustomerComment[0]->TongCongDcHoiYKien;

        $result = new \stdClass();
        $result->survey = !empty($survey) ? $survey : '';
        $result->total = !empty($total) ? $total : '';
        $result->totalCusComment = !empty($totalCusComment) ? $totalCusComment : '';
        $result->totalCusNoComment = !empty($totalCusNoComment) ? $totalCusNoComment : '';
        $result->totalConsulted = !empty($totalConsulted) ? $totalConsulted : '';

        return $result;
    }

    private function SurveyQuantityReport($from_date, $to_date, $branch) {
        $survey = $this->modelSurveySections->getSumSurvey($from_date, $to_date, $branch); //lấy thông tin kết quả survey
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
        $surveyNPS = $this->modelSurveySections->getSumSurveyNPS($from_date, $to_date, $branch);
        $surveyNPSNoRated = $this->modelSurveySections->getSumSurveyNPSNoRated($from_date, $to_date, $branch);
        $surveyNPSNoRated_Note = $this->modelSurveySections->getSumSurveyNPSNoRated_Note($from_date, $to_date, $branch);
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

        $result = new \stdClass();
        $result->survey = !empty($survey) ? $survey : [];
        $result->total = !empty($total) ? $total : [];
        $result->surveyNPS = !empty($surveyNPS) ? $surveyNPS : [];
        $result->totalNPS = !empty($totalNPS) ? $totalNPS : [];
        $result->totalNoRated = !empty($totalNoRated) ? $totalNoRated : [];
        $result->surveyNPSNoRated = !empty($surveyNPSNoRated) ? $surveyNPSNoRated : [];
        $result->surveyNPSNoRated_Note = !empty($surveyNPSNoRated_Note) ? $surveyNPSNoRated_Note : [];
        $result->region = '';
        $result->from_date = '';
        $result->to_date = '';

        return $result;
    }

    public function exportToExcel(Request $request) {
        $sessionData = Session::get('sessionData');
        $modelSurvey = new SurveySections();
        $excelExport = new ExcelDashboardController();
        $excelReport = new ExcelReportController();
        $lastweek = $this->lastweek;
        $yesterdayTime = $this->yesterdayTime;
        $timeCache = $this->timeCache;
        $type = $request->input()['type'];
        switch ($type) {
            //Xuất Excel sự hài lòng khách hàng, điểm CSAT toàn quốc, ý kiến đóng góp của khách hàng, số lượng khảo sát 7 ngày gần nhất
            case 1: {
                //đã chỉnh
                $detailCSAT = $sessionData['CsatReportDashBoard'];
                //đã chỉnh
                $detailNPS = $sessionData['NpsReportDashBoard'];
                $detailNPS = (object) $detailNPS;
                foreach ($detailNPS->survey as $key => $value) {
                    $detailNPS->survey[$key] = (object) $value;
                }
                foreach ($detailNPS->groupNPS as $key => $value) {
                    $detailNPS->groupNPS[$key] = (object) $value;
                }
                $detailNPS->total = (object) $detailNPS->total;
                //đã chỉnh
                $customerComment = $sessionData['customerComment'];

                $result = $sessionData['result'];
                $name = 'CSAT_NPS_CustomerComment_' . date('d-m', strtotime($lastweek)) . '_' . date('d-m', strtotime($yesterdayTime)) . '_' . strtotime(date('y-m-d H:i:s'));
                $PathExcel = Excel::create($name, function($excel) use($detailCSAT, $detailNPS, $customerComment, $result, $lastweek, $yesterdayTime, $excelExport, $excelReport) {
                    // Set the title
                    $excel->setTitle('Thống kê số liệu DashBoard');
                    // Chain the setters
                    $excel->setCreator('VAS')
                        ->setCompany('FPT Telecom');
                    // Call them separately
                    $excel->setDescription('Thông kê số liệu báo cáo chi tiết 7 ngày gần nhất');
                    $excel->sheet('Tổng hợp', function($sheet) use($detailCSAT, $detailNPS, $customerComment, $result, $lastweek, $yesterdayTime, $excelExport, $excelReport) {
                        $sheet->setWidth('A', 68);
                        $sheet->mergeCells('A1:M1')->cell('A1', function($cell) use($lastweek, $yesterdayTime) {
                            $cell->setValue('BÁO CÁO CEM - CUSTOMER VOICE TỪ ' . date('d/m/Y', strtotime($lastweek)) . ' - ' . date('d/m/Y', strtotime($yesterdayTime)));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        //Tạo báo cáo số lượng khảo sát CSAT
                        $rowStart = $excelExport->createAmountCsat($sheet, $result, 3);
                        //Tạo báo cáo số lượng khảo sát NPS
                        $rowstart2 = $excelExport->createAmountNps($sheet, $result, $rowStart + 2);
                        //Tạo bảng chi tiết CSAT
                        $rowStart3 = $excelReport->createDetailCsat($sheet, $detailCSAT, $rowstart2 + 2);
                        //Tạo điểm đánh giá NPS
                        $rowStart4 = $excelExport->createDetailNps($sheet, $detailNPS, $rowStart3 + 2);
                       //Tạo báo cáo theo nhóm NPS
                        $rowStart8 = $excelExport->createGroupNps($sheet, $detailNPS, $rowStart4 + 2);
                       //Tạo bảng đánh giá của khách hàng
                        $excelExport->createEvaluateCus($sheet, $customerComment, $rowStart8 + 2);
                    });
                });
                break;
            }
            //Xuất Excel top CSAT & NPS 7 ngày gần nhất
            case 2: {
                //đã chỉnh
                $resultCsatAll = $sessionData['resultCsatAll'];
                //NPS Vùng, chi nhánh, toàn quốc
                $resultNpsAll = $sessionData['resultNpsAll'];
                $npsCountry = $sessionData['allNpsInfo'];
                $surveyBranches = $resultCsatAll['survey_branches'];
                $arrCountry = $resultCsatAll['arrCountry'][0];
                $name = 'Top_CSAT_NPS_Country_' . date('d-m', strtotime($lastweek)) . '_' . date('d-m', strtotime($yesterdayTime)) . '_' . strtotime(date('y-m-d H:i:s'));
                $PathExcel = Excel::create($name, function($excel) use($surveyBranches, $arrCountry, $resultNpsAll, $npsCountry, $lastweek, $yesterdayTime, $excelExport) {
                    // Set the title
                    $excel->setTitle('Thống kê số liệu DashBoard');
                    // Chain the setters
                    $excel->setCreator('VAS')
                        ->setCompany('FPT Telecom');
                    // Call them separately
                    $excel->setDescription('Thông kê số liệu báo cáo chi tiết 7 ngày gần nhất');
                    $excel->sheet('Điểm CSAT, NPS Vùng, Chi nhánh', function($sheet) use($arrCountry, $surveyBranches, $resultNpsAll, $npsCountry, $lastweek, $yesterdayTime, $excelExport) {
                        $sheet->mergeCells('B1:X1')->cell('B1', function($cell) use($lastweek, $yesterdayTime) {
                            $cell->setValue('BÁO CÁO CEM - CUSTOMER VOICE TỪ ' . date('d/m/Y', strtotime($lastweek)) . ' - ' . date('d/m/Y', strtotime($yesterdayTime)));
                            $cell->setFontSize(16);
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });
                        //format cell number
                        $sheet->setColumnFormat(array(
                            'B7:Z14' => '0.00',
                            'B20:Z103' => '0.00'
                        ));
                        //Tạo bảng CSAT NV kinh doanh theo vùng
//                        $rowStart6 = $excelExport->createCsatRegion($sheet, 3, $resultNpsAll, $arrCountry);
                        //Tạo Bảng CSAT nhân viên kinh doanh theo chi nhánh
                        $rowStart7 = $excelExport->createCsatBranch($sheet, $surveyBranches, $arrCountry, 3, $resultNpsAll, $npsCountry);
                    });
                });

                break;
            }
        }
//                ->store('xlsx', storage_path('app/public'), true);
//          $PathExcel=["full" => "D:\wamp\www\CEM\storage\exports/CEM_CSAT_Report_1506572856.xls",  "path" => "D:\wamp\www\CEM\storage\exports" , "file" => "Dashboard_Customer_Voice_Country_25.09_01.10_1506893971.xls" , "title" => "CEM_CSAT_Report_1506572856"  ,"ext" => "xls"];
        $PathExcel = $PathExcel->string('xlsx'); //change xlsx for the format you want, default is xls
        $response = array(
            'name' => $name,
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($PathExcel) //mime type of used format
        );
        return response()->json($response);
    }

    private function getsurveyDashboard($lastweek, $yesterdayTime) {
        $survey = $this->checkAndSetCache('surveyDashboard' . $timeCache, 6, $lastweek, $yesterdayTime);
        $surveyOrigin = $survey;
        //CSAT vùng
        $arrCountry = ['NVKinhDoanhPoint' => 0, 'NVTrienKhaiPoint' => 0, 'DGDichVu_Net_Point' => 0, 'DGDichVu_TV_Point' => 0, 'NVBaoTriTINPoint' => 0, 'NVBaoTriINDOPoint' => 0,
            'NVKinhDoanhTSPoint' => 0, 'NVTrienKhaiTSPoint' => 0, 'DGDichVuTS_Net_Point' => 0, 'DGDichVuTS_TV_Point' => 0,
            'DVBaoTriTIN_Net_Point' => 0, 'DVBaoTriTIN_TV_Point' => 0, 'DVBaoTriINDO_Net_Point' => 0, 'DVBaoTriINDO_TV_Point' => 0, 'DGDichVu_MobiPay_Net_Point' => 0, 'DGDichVu_MobiPay_TV_Point' => 0,
            'SoLuongKD' => 0, 'SoLuongTK' => 0, 'SoLuongDGDV_Net' => 0, 'SoLuongDGDV_TV' => 0, 'SoLuongNVBaoTriTIN' => 0, 'SoLuongNVBaoTriINDO' => 0,
            'SoLuongKDTS' => 0, 'SoLuongTKTS' => 0, 'SoLuongDGDVTS_Net' => 0, 'SoLuongDGDVTS_TV' => 0,
            'SoLuongDVBaoTriTIN_Net' => 0, 'SoLuongDVBaoTriTIN_TV' => 0, 'SoLuongDVBaoTriINDO_Net' => 0, 'SoLuongDVBaoTriINDO_TV' => 0, 'SoLuongDGDV_MobiPay_Net' => 0, 'SoLuongDGDV_MobiPay_TV' => 0];
        foreach ($survey as &$report) {
            //toàn quốc
            $arrCountry['NVKinhDoanhPoint'] += $report->NVKinhDoanhPoint; //7 vùng
            $arrCountry['NVTrienKhaiPoint'] += $report->NVTrienKhaiPoint;
            $arrCountry['DGDichVu_Net_Point'] += $report->DGDichVu_Net_Point;
            $arrCountry['DGDichVu_TV_Point'] += $report->DGDichVu_TV_Point;

            $arrCountry['NVKinhDoanhTSPoint'] += $report->NVKinhDoanhTSPoint;
            $arrCountry['NVTrienKhaiTSPoint'] += $report->NVTrienKhaiTSPoint;
            $arrCountry['DGDichVuTS_Net_Point'] += $report->DGDichVuTS_Net_Point;
            $arrCountry['DGDichVuTS_TV_Point'] += $report->DGDichVuTS_TV_Point;

            $arrCountry['NVBaoTriTINPoint'] += $report->NVBaoTriTINPoint;
            $arrCountry['NVBaoTriINDOPoint'] += $report->NVBaoTriINDOPoint;
            $arrCountry['DVBaoTriTIN_Net_Point'] += $report->DVBaoTriTIN_Net_Point;
            $arrCountry['DVBaoTriTIN_TV_Point'] += $report->DVBaoTriTIN_TV_Point;
            $arrCountry['DVBaoTriINDO_Net_Point'] += $report->DVBaoTriINDO_Net_Point;
            $arrCountry['DVBaoTriINDO_TV_Point'] += $report->DVBaoTriINDO_TV_Point;
            $arrCountry['DGDichVu_MobiPay_Net_Point'] += $report->DGDichVu_MobiPay_Net_Point;
            $arrCountry['DGDichVu_MobiPay_TV_Point'] += $report->DGDichVu_MobiPay_TV_Point;
            $arrCountry['SoLuongKD'] += $report->SoLuongKD;
            $arrCountry['SoLuongTK'] += $report->SoLuongTK;
            $arrCountry['SoLuongDGDV_Net'] += $report->SoLuongDGDV_Net;
            $arrCountry['SoLuongDGDV_TV'] += $report->SoLuongDGDV_TV;

            $arrCountry['SoLuongKDTS'] += $report->SoLuongKDTS;
            $arrCountry['SoLuongTKTS'] += $report->SoLuongTKTS;
            $arrCountry['SoLuongDGDVTS_Net'] += $report->SoLuongDGDVTS_Net;
            $arrCountry['SoLuongDGDVTS_TV'] += $report->SoLuongDGDVTS_TV;

            $arrCountry['SoLuongNVBaoTriTIN'] += $report->SoLuongNVBaoTriTIN;
            $arrCountry['SoLuongNVBaoTriINDO'] += $report->SoLuongNVBaoTriINDO;
            $arrCountry['SoLuongDVBaoTriTIN_Net'] += $report->SoLuongDVBaoTriTIN_Net;
            $arrCountry['SoLuongDVBaoTriTIN_TV'] += $report->SoLuongDVBaoTriTIN_TV;
            $arrCountry['SoLuongDVBaoTriINDO_Net'] += $report->SoLuongDVBaoTriINDO_Net;
            $arrCountry['SoLuongDVBaoTriINDO_TV'] += $report->SoLuongDVBaoTriINDO_TV;
            $arrCountry['SoLuongDGDV_MobiPay_Net'] += $report->SoLuongDGDV_MobiPay_Net;
            $arrCountry['SoLuongDGDV_MobiPay_TV'] += $report->SoLuongDGDV_MobiPay_TV;

            $report->Vung = str_replace('Vung', 'Vùng', $report->Vung);
            $report->NVKinhDoanh_AVGPoint = ($report->NVKinhDoanhPoint > 0) ? round($report->NVKinhDoanhPoint / $report->SoLuongKD, 2) : 0;
            $report->NVTrienKhai_AVGPoint = ($report->NVTrienKhaiPoint > 0) ? round($report->NVTrienKhaiPoint / $report->SoLuongTK, 2) : 0;
            $report->DGDichVu_Net_AVGPoint = ($report->SoLuongDGDV_Net > 0) ? round($report->DGDichVu_Net_Point / $report->SoLuongDGDV_Net, 2) : 0;
            $report->DGDichVu_TV_AVGPoint = ($report->SoLuongDGDV_TV > 0) ? round($report->DGDichVu_TV_Point / $report->SoLuongDGDV_TV, 2) : 0;

            $report->NVKinhDoanhTS_AVGPoint = ($report->NVKinhDoanhTSPoint > 0) ? round($report->NVKinhDoanhTSPoint / $report->SoLuongKDTS, 2) : 0;
            $report->NVTrienKhaiTS_AVGPoint = ($report->NVTrienKhaiTSPoint > 0) ? round($report->NVTrienKhaiTSPoint / $report->SoLuongTKTS, 2) : 0;
            $report->DGDichVuTS_Net_AVGPoint = ($report->DGDichVuTS_Net_Point > 0) ? round($report->DGDichVuTS_Net_Point / $report->SoLuongDGDVTS_Net, 2) : 0;
            $report->DGDichVuTS_TV_AVGPoint = ($report->DGDichVuTS_TV_Point > 0) ? round($report->DGDichVuTS_TV_Point / $report->SoLuongDGDVTS_TV, 2) : 0;


            $report->DGDichVu_MobiPay_Net_AVGPoint = ($report->SoLuongDGDV_MobiPay_Net > 0) ? round($report->DGDichVu_MobiPay_Net_Point / $report->SoLuongDGDV_MobiPay_Net, 2) : 0;
            $report->DGDichVu_MobiPay_TV_AVGPoint = ($report->SoLuongDGDV_MobiPay_TV > 0) ? round($report->DGDichVu_MobiPay_TV_Point / $report->SoLuongDGDV_MobiPay_TV, 2) : 0;
            $report->NVBaoTriTIN_AVGPoint = ($report->NVBaoTriTINPoint > 0) ? round($report->NVBaoTriTINPoint / $report->SoLuongNVBaoTriTIN, 2) : 0;
            $report->NVBaoTriINDO_AVGPoint = ($report->NVBaoTriINDOPoint > 0) ? round($report->NVBaoTriINDOPoint / $report->SoLuongNVBaoTriINDO, 2) : 0;
            $report->DVBaoTriTIN_Net_AVGPoint = ($report->DVBaoTriTIN_Net_Point > 0) ? round($report->DVBaoTriTIN_Net_Point / $report->SoLuongDVBaoTriTIN_Net, 2) : 0;
            $report->DVBaoTriTIN_TV_AVGPoint = ($report->DVBaoTriTIN_TV_Point > 0) ? round($report->DVBaoTriTIN_TV_Point / $report->SoLuongDVBaoTriTIN_TV, 2) : 0;
            $report->DVBaoTriINDO_Net_AVGPoint = ($report->SoLuongDVBaoTriINDO_Net > 0) ? round($report->DVBaoTriINDO_Net_Point / $report->SoLuongDVBaoTriINDO_Net, 2) : 0;
            $report->DVBaoTriINDO_TV_AVGPoint = ($report->SoLuongDVBaoTriINDO_TV > 0) ? round($report->DVBaoTriINDO_TV_Point / $report->SoLuongDVBaoTriINDO_TV, 2) : 0;
        }
        //sort giá trị theo field
        $extraFunc->sortOnField($survey, 'NVKinhDoanh_AVGPoint', 'DESC');

        $arrCountry['Vung'] = 'Toàn Quốc';
        $arrCountry['NVKinhDoanh_AVGPoint'] = ($arrCountry['NVKinhDoanhPoint'] > 0) ? round($arrCountry['NVKinhDoanhPoint'] / $arrCountry['SoLuongKD'], 2) : 0; //7 vùng
        $arrCountry['NVTrienKhai_AVGPoint'] = ($arrCountry['NVTrienKhaiPoint'] > 0) ? round($arrCountry['NVTrienKhaiPoint'] / $arrCountry['SoLuongTK'], 2) : 0;
        $arrCountry['DGDichVu_Net_AVGPoint'] = ($arrCountry['DGDichVu_Net_Point'] > 0) ? round($arrCountry['DGDichVu_Net_Point'] / $arrCountry['SoLuongDGDV_Net'], 2) : 0;
        $arrCountry['DGDichVu_TV_AVGPoint'] = ($arrCountry['DGDichVu_TV_Point'] > 0) ? round($arrCountry['DGDichVu_TV_Point'] / $arrCountry['SoLuongDGDV_TV'], 2) : 0;

        $arrCountry['NVKinhDoanhTS_AVGPoint'] = ($arrCountry['NVKinhDoanhTSPoint'] > 0) ? round($arrCountry['NVKinhDoanhTSPoint'] / $arrCountry['SoLuongKDTS'], 2) : 0;
        $arrCountry['NVTrienKhaiTS_AVGPoint'] = ($arrCountry['NVTrienKhaiTSPoint'] > 0) ? round($arrCountry['NVTrienKhaiTSPoint'] / $arrCountry['SoLuongTKTS'], 2) : 0;
        $arrCountry['DGDichVuTS_Net_AVGPoint'] = ($arrCountry['DGDichVuTS_Net_Point'] > 0) ? round($arrCountry['DGDichVuTS_Net_Point'] / $arrCountry['SoLuongDGDVTS_Net'], 2) : 0;
        $arrCountry['DGDichVuTS_TV_AVGPoint'] = ($arrCountry['DGDichVuTS_TV_Point'] > 0) ? round($arrCountry['DGDichVuTS_TV_Point'] / $arrCountry['SoLuongDGDVTS_TV'], 2) : 0;

        $arrCountry['NVBaoTriTIN_AVGPoint'] = ($arrCountry['NVBaoTriTINPoint'] > 0) ? round($arrCountry['NVBaoTriTINPoint'] / $arrCountry['SoLuongNVBaoTriTIN'], 2) : 0;
        $arrCountry['NVBaoTriINDO_AVGPoint'] = ($arrCountry['NVBaoTriINDOPoint'] > 0) ? round($arrCountry['NVBaoTriINDOPoint'] / $arrCountry['SoLuongNVBaoTriINDO'], 2) : 0;
        $arrCountry['DVBaoTriTIN_Net_AVGPoint'] = ($arrCountry['DVBaoTriTIN_Net_Point'] > 0) ? round($arrCountry['DVBaoTriTIN_Net_Point'] / $arrCountry['SoLuongDVBaoTriTIN_Net'], 2) : 0;
        $arrCountry['DVBaoTriTIN_TV_AVGPoint'] = ($arrCountry['DVBaoTriTIN_TV_Point'] > 0) ? round($arrCountry['DVBaoTriTIN_TV_Point'] / $arrCountry['SoLuongDVBaoTriTIN_TV'], 2) : 0;
        $arrCountry['DVBaoTriINDO_Net_AVGPoint'] = ($arrCountry['DVBaoTriINDO_Net_Point'] > 0) ? round($arrCountry['DVBaoTriINDO_Net_Point'] / $arrCountry['SoLuongDVBaoTriINDO_Net'], 2) : 0;
        $arrCountry['DVBaoTriINDO_TV_AVGPoint'] = ($arrCountry['DVBaoTriINDO_TV_Point'] > 0) ? round($arrCountry['DVBaoTriINDO_TV_Point'] / $arrCountry['SoLuongDVBaoTriINDO_TV'], 2) : 0;
        $arrCountry['DGDichVu_MobiPay_Net_AVGPoint'] = ($arrCountry['DGDichVu_MobiPay_Net_Point'] > 0) ? round($arrCountry['DGDichVu_MobiPay_Net_Point'] / $arrCountry['SoLuongDGDV_MobiPay_Net'], 2) : 0;
        $arrCountry['DGDichVu_MobiPay_TV_AVGPoint'] = ($arrCountry['DGDichVu_MobiPay_TV_Point'] > 0) ? round($arrCountry['DGDichVu_MobiPay_TV_Point'] / $arrCountry['SoLuongDGDV_MobiPay_TV'], 2) : 0;
        return [$surveyOrigin, $arrCountry];
    }

  public function setLocale($locale) {
        $language='en';
        $locales=['en', 'vi'];
        if (in_array($locale, $locales)) {
            $language = $locale;
        }
        Session::put('languageLocale',$language );
        return redirect()->back();
    }

}
