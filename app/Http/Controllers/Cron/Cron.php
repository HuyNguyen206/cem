<?php

namespace App\Http\Controllers\Cron;

use App\Component\BuildDataCSAT;
use App\Component\BuildDataCSATExcel;
use App\Http\Controllers\Controller;
use App\Jobs\SendCsatEmailWeekFullZone;
use App\Models\SurveySections;
use App\Models\Trans\OutboundRateSumNPS;
use App\Models\Trans\OutboundRateSumCSAT;
use App\Models\Trans\OutboundRateObject;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use App\Models\SurveyReport;
use App\Models\OutboundAnswers;
use App\Jobs\SendCsatEmail;
use App\Jobs\SendCsatEmailWeek;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redis;
use App\Jobs\SendMailTotalRemindCUSJob;
use App\Jobs\SendMailRemindCUSJob;

class Cron extends Controller {

    protected $buildDataCsat;
    protected $buildDataCsatExcel;
    protected $modelLocation;
    protected $modelSurveyReport;
    protected $modelSurveySection;

    public function __construct() {
        $this->buildDataCsat = new BuildDataCSAT();
        $this->buildDataCsatExcel = new BuildDataCSATExcel();
        $this->modelLocation = new Location();
        $this->modelSurveyReport = new SurveyReport();
        $this->modelSurveySection = new SurveySections();
    }

    //////////////////////////////////////////Public function//////////////////////////////////////////////
    //--------------------------------------Chưa bao h xài-------------------------------------------
    public function getNewTableRateSumAfter1Month() {
        $dayDefault = '2016-04-14';
        $modelRateSumCSAT = new OutboundRateSumCSAT();
        $modelRateSumNPS = new OutboundRateSumNPS();

        $rateSumCSAT = $modelRateSumCSAT->getNewRecord();
        $rateSumNPS = $modelRateSumNPS->getNewRecord();
        if (!empty($rateSumCSAT) && !empty($rateSumNPS)) {
            if ($rateSumCSAT->rate_sum_csat_date > $rateSumNPS->rate_sum_nps_date) {
                $dayDefault = $rateSumCSAT->rate_sum_csat_date;
            } else {
                $dayDefault = $rateSumNPS->rate_sum_nps_date;
            }
        } else {
            if (!empty($rateSumCSAT)) {
                $dayDefault = $rateSumCSAT->rate_sum_csat_date;
            }
            if (!empty($rateSumNPS)) {
                $dayDefault = $rateSumNPS->rate_sum_nps_date;
            }
        }

        $dayStart = date_create($dayDefault);
        $dayEnd = date_create(date('Y-m-d'));
        date_add($dayEnd, date_interval_create_from_date_string("-1 month"));
        $dayEndFormat = date_format($dayEnd, 'Y-m-d');
        $day = 1;

        for ($i = 1; $i <= 10; $i++) {
            date_add($dayStart, date_interval_create_from_date_string($day . " day"));
            $dayStartFormat = date_format($dayStart, 'Y-m-d');
            if ($dayStartFormat < $dayEndFormat) {
                $this->tranferCSATAndNPSByDay($dayStartFormat);
                echo $dayStartFormat . '.Tranfer Done!';
            }
        }
    }

    public function getNewTableRateSumBefore1Month() {
        $dayDefault = date('Y-m-d');

        $dayStart = date_create($dayDefault);
        $dayEnd = date_create($dayDefault);
        date_add($dayStart, date_interval_create_from_date_string("-1 month"));
        $dayStartFormat = date_format($dayStart, 'Y-m-d');
        $dayEndFormat = date_format($dayEnd, 'Y-m-d');
        $day = 1;

        while ($dayStartFormat < $dayEndFormat) {
            $this->updateCSATAndNPSByDay($dayStartFormat);
            echo $dayStartFormat . '.Update Done!';

            date_add($dayStart, date_interval_create_from_date_string($day . " day"));
            $dayStartFormat = date_format($dayStart, 'Y-m-d');
        }
    }

    //---------------------------------------Xây dựng gửi mail hàng ngày, tuần------------------------
    public function buildDataToSendCsatMail() {
        $redisKey = 'dataExcelCSAT';
        $result = Redis::get($redisKey);
        if (empty($result)) {
            try {
                //Lấy ngày cần báo cáo
                $dayNeed = strtotime('yesterday');
                $day = date('d/m/Y', $dayNeed);
                $dayToSQL = date('Y-m-d', $dayNeed);
                $from_date = date('Y-m-d 00:00:00', $dayNeed);
                $to_date = date('Y-m-d 23:59:59', $dayNeed);
                //Lấy toàn bộ dữ liệu csat trong khoảng thời gian cần báo cáo
                $region = '1,2,3,4,5,6,7';
                $branch = [];
                $branchcode = [];
                $result = $this->buildDataCsat->CSATServiceReport($from_date, $to_date, $region, $branch, $branchcode);
                $reports = $this->modelSurveySection->getSurveySectionCSATByDay($dayToSQL);
                $reports = $this->convertDataRowToColumn($reports);
                $department = 'cs';
                for ($i = 1; $i <= 7; $i++) {
                    $region = (string) $i;
                    $locations = $this->modelLocation->getBranchLocationPlus($branch, $branchcode, $region);
                    $result['region'] = $region;
                    $result['branch'] = $locations;
                    $fileExcel = 'Vung' . $region . '-CSAT12Internet&Truyenhinh-' . date('Ymd', strtotime($from_date));
                    $dataExcel = $result;
                    $dataExcel['detailReports'] = $reports;
                    $dataExcel['day'] = $day;
                    $dataExcel['rowEnd'] = [
                        'net' => $result['csatNet']['Vùng '.$i][$department]['sta_t'],
                        'tv' => $result['csatTv']['Vùng '.$i][$department]['sta_t'],
                    ];
                    $dataExcel['viewStatus'] = 1;
                    $dataExcel['sendMail'] = true;
                    $dataExcel['arrayTypeSurvey'] = $result['arrayTypeSurvey'];
                    $template['total'] = 'Csat.CsatServiceGeneralExcelBranch';
                    $template['net'] = 'emails.templateExcelCSATNet';
                    $template['tv'] = 'emails.templateExcelCSATTv';
                    Excel::create($fileExcel, function ($excel) use ($dataExcel, $template) {
                        $needObject = $this->buildDataCsatExcel->measureBorderCsatServiceExcelDetailNet($dataExcel, $template['net']);
                        $excel->sheet('Chi tiết CSAT 1,2 Internet', function ($sheet) use ($needObject) {
                            $this->buildDataCsatExcel->formatExcelCsatServiceDetailNet($sheet, $needObject);
                        });
                        $needObject = $this->buildDataCsatExcel->measureBorderCsatServiceExcelDetailTv($dataExcel, $template['tv']);
                        $excel->sheet('Chi tiết CSAT 1,2 Truyền hình', function ($sheet) use ($needObject) {
                            $this->buildDataCsatExcel->formatExcelCsatServiceDetailTv($sheet, $needObject);
                        });
                        //Trang tổng quan
                        $needObject = $this->buildDataCsatExcel->measureBorderCsatServiceExcel($dataExcel, $template['total'], true);
                        $excel->sheet('Tổng hợp', function ($sheet) use ($needObject, $dataExcel) {
                            $this->buildDataCsatExcel->formatExcelCsatServiceGeneral($sheet, $needObject, $dataExcel['arrayTypeSurvey']);
                        });
                        $excel->getActiveSheet()->getStyle($needObject->columnName[0] . $needObject->rowBeginSubject . ':' . $needObject->columnName[$needObject->colMaxColTable - 1] . $needObject->rowEndTable6)->getAlignment()->setWrapText(true);
                        $excel->getActiveSheet()->getStyle($needObject->columnName[0] . $needObject->rowBeginSubject . ':' . $needObject->columnName[0] . $needObject->rowBeginSubject)->getAlignment()->setWrapText(true);
                        $excel->getActiveSheet()->setAutoSize(false)->setWidth($needObject->columnWidth);
                    })->store('xls');

                    $redisKeyCsatZone = $redisKey . $region;
                    Redis::set($redisKeyCsatZone, json_encode($dataExcel));
                    Redis::expire($redisKeyCsatZone, 43200);
                }
                Redis::set($redisKey, $day);
                Redis::expire($redisKey, 43200);
            } catch (Exception $ex) {
                var_dump($ex->getMessage());
                die;
            }
        }
        var_dump('Already build');
    }

    public function buildDataToSendCsatMailWeek() {
        $redisKey = 'dataExcelCSATWeek';
        $result = Redis::get($redisKey);

        if (empty($result)) {
            //Lấy ngày cần báo cáo
            $lastWeekStart = strtotime('-1 week monday');
            $lastWeekEnd = strtotime('-1 week sunday');

            $from_date = date('Y-m-d 00:00:00', $lastWeekStart);
            $to_date = date('Y-m-d 23:59:59', $lastWeekEnd);

            //Lấy toàn bộ dữ liệu csat trong khoảng thời gian cần báo cáo
            $region = '1,2,3,4,5,6,7';
            $branch = [];
            $branchcode = [];

            $result = $this->buildDataCsat->CSATServiceReport($from_date, $to_date, $region, $branch, $branchcode);
            for ($i = 1; $i <= 7; $i++) {
                $region = (string) $i;
                $locations = $this->modelLocation->getBranchLocationPlus($branch, $branchcode, $region);
                $result['region'] = $region;
                $result['branch'] = $locations;

                $fileExcel = 'Vung' . $region . '-CSAT12Internet&Truyenhinh-' . date('Ymd', strtotime($from_date)) . '-' . date('Ymd', strtotime($to_date));
                $dataExcel = $result;
                $dataExcel['viewStatus'] = 1;
                $dataExcel['sendMail'] = true;
                $dataExcel['arrayTypeSurvey'] = $result['arrayTypeSurvey'];
                $template = 'Csat.CsatServiceGeneralExcelBranch';

                Excel::create($fileExcel, function ($excel) use ($dataExcel, $template) {
                    $needObject = $this->buildDataCsatExcel->measureBorderCsatServiceExcel($dataExcel, $template, true);
                    $excel->sheet('Tổng hợp', function ($sheet) use ($needObject, $dataExcel) {
                        $this->buildDataCsatExcel->formatExcelCsatServiceGeneral($sheet, $needObject, $dataExcel['arrayTypeSurvey']);
                    });
                    $excel->getActiveSheet()->getStyle($needObject->columnName[0] . $needObject->rowBeginSubject . ':' . $needObject->columnName[$needObject->colMaxColTable - 1] . $needObject->rowEndTable6)->getAlignment()->setWrapText(true);
                    $excel->getActiveSheet()->getStyle($needObject->columnName[0] . $needObject->rowBeginSubject . ':' . $needObject->columnName[0] . $needObject->rowBeginSubject)->getAlignment()->setWrapText(true);
                    $excel->getActiveSheet()->setAutoSize(false)->setWidth($needObject->columnWidth);
                })->store('xls');

                $redisKeyCsatZone = $redisKey . $region;
                Redis::set($redisKeyCsatZone, json_encode($dataExcel));
                Redis::expire($redisKeyCsatZone, 43200);
            }

            Redis::set($redisKey, $from_date);
            Redis::expire($redisKey, 43200);
        }
        $this->buildDataToSendCsatMailWeekFullZone();

        var_dump('Already build');
    }

    public function buildDataToSendCsatMailWeekFullZone() {
        $redisKey = 'dataExcelCSATWeekFullZone';
        $result = Redis::get($redisKey);
        if (empty($result)) {
            //Lấy ngày cần báo cáo
            $lastWeekStart = strtotime('-1 week monday 00:00:00');
            $lastWeekEnd = strtotime('-1 week sunday 23:59:59');

            $from_date = date('Y-m-d h:i:s', $lastWeekStart);
            $to_date = date('Y-m-d h:i:s', $lastWeekEnd);

            $region = '1,2,3,4,5,6,7';
            $branch = [];
            $branchcode = [];

            $result = $this->buildDataCsat->CSATServiceReport($from_date, $to_date, $region, $branch, $branchcode);
            $result['branch'] = $result['locations'];

            $fileExcel = 'ToanQuoc-CSAT12Internet&Truyenhinh-' . date('Ymd', strtotime($from_date)) . '-' . date('Ymd', strtotime($to_date));
            $dataExcel = $result;
            $dataExcel['viewStatus'] = 0;
            $dataExcel['sendMail'] = true;
            $dataExcel['arrayTypeSurvey'] = $result['arrayTypeSurvey'];
            $template = 'Csat.CsatServiceGeneralExcelZone';

            Excel::create($fileExcel, function ($excel) use ($dataExcel, $template) {
                $needObject = $this->buildDataCsatExcel->measureBorderCsatServiceExcel($dataExcel, $template, true);
                $excel->sheet('Tổng hợp', function ($sheet) use ($needObject, $dataExcel) {
                    $this->buildDataCsatExcel->formatExcelCsatServiceGeneral($sheet, $needObject,  $dataExcel['arrayTypeSurvey']);
                });
                $excel->getActiveSheet()->getStyle($needObject->columnName[0] . $needObject->rowBeginSubject . ':' . $needObject->columnName[$needObject->colMaxColTable - 1] . $needObject->rowEndTable6)->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle($needObject->columnName[0] . $needObject->rowBeginSubject . ':' . $needObject->columnName[0] . $needObject->rowBeginSubject)->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->setAutoSize(false)->setWidth($needObject->columnWidth);
            })->store('xls');

            Redis::set($redisKey, json_encode($dataExcel));
            Redis::expire($redisKey, 43200);
        }
        dump('Already build');
    }

    public function sendCsatMail($type = 'day') {
        if (env('APP_ENV') != 'local') {
            $arrayMailWeek = [
                '1' => [
                    'FTEL.V1.BGD@fpt.com.vn',
                    'v1.quanly@vienthongtin.com',
                ],
                '2' => [
                    'FTEL.V2.BGD@fpt.com.vn',
                ],
                '3' => [
                    'FTEL.V3.BGD@fpt.com.vn',
                ],
                '4' => [
                    'FTEL.V4.BGD@fpt.com.vn',
                ],
                '5' => [
                    'FTEL.V5.BGD@fpt.com.vn',
                ],
                '6' => [
                    'FTEL.V6.BGD@fpt.com.vn',
                ],
                '7' => [
                    'FTEL.V7.BGD@fpt.com.vn',
                ]
            ];
            $arrayMail = [
                '1' => [
                    'Ftel.CLDV.V1@fpt.com.vn',
                    'v1.quanly@vienthongtin.com',
                ],
                '2' => [
                    'Ftel.CLDV.V2@fpt.com.vn',
                ],
                '3' => [
                    'Ftel.CLDV.V3@fpt.com.vn',
                ],
                '4' => [
                    'Ftel.CLDV.V4@fpt.com.vn',
                ],
                '5' => [
                    'Ftel.CLDV.V5@fpt.com.vn',
                ],
                '6' => [
                    'Ftel.CLDV.V6@fpt.com.vn',
                ],
                '7' => [
                    'Ftel.CLDV.V7@fpt.com.vn',
                ]
            ];
        } else {
            $arrayMailWeek = [
                '1' => [
                    'huydp2@fpt.com.vn',
                ]
            ];
            $arrayMail = [
                '1' => [
                    'huydp2@fpt.com.vn',
                ]
            ];
        }
        switch ($type) {
            case "week":
                foreach ($arrayMailWeek as $key => $mails) {
                    if (count($mails) != 0) {
                        $job = (new SendCsatEmailWeek($mails, $key))->onQueue('emails')->delay(60);
                        $this->dispatch($job);
                    }
                }
                $this->sendCsatMailFullZone();
                break;
            default:
                foreach ($arrayMail as $key => $mails) {
                    if (count($mails) != 0) {
                        $job = (new SendCsatEmail($mails, $key))->onQueue('emails')->delay(60);
                        $this->dispatch($job);
                    }
                }
        }
    }

    public function sendCsatMailFullZone() {
        $job = (new SendCsatEmailWeekFullZone())->onQueue('emails')->delay(60);
        $this->dispatch($job);
    }

    public function sendRemindCUS(){
        $today = strtotime('today');
        $theDayBegin = strtotime('2018-04-20 00:00:00');
        $yesterday = strtotime('yesterday');
        $theDayBeforeYesterday = $yesterday - 1;

        $modelSection = new SurveySections();
        $condition = [
            'surveyFromInt' => $theDayBegin,
            'surveyToInt' => $theDayBeforeYesterday,
        ];
        $resultCountBeforeYesterday = $modelSection->countChargeStaffViolationNotProcessing($condition);
        $resultGetBeforeYesterday = $modelSection->getChargeStaffViolationNotProcessing($condition);

        $resultTotalBeforeYesterday = [
            'total' => $resultCountBeforeYesterday,
            'detail' => $resultGetBeforeYesterday,
        ];

        $condition = [
            'surveyFromInt' => $yesterday,
            'surveyToInt' => $today - 1,
        ];
        $resultGetYesterday = $modelSection->getChargeStaffViolationNotProcessing($condition);

        $cc = [
            'anhdv4@fpt.com.vn',
            'hantp@fpt.com.vn',
            'toannm@fpt.com.vn',
            'phutm@fpt.com.vn',
            'huydp2@fpt.com.vn',
        ];
        $toBeforeYesterday = [];

        foreach($resultGetBeforeYesterday as $val){
            if(!in_array($val->email_list,$toBeforeYesterday)){
                $emails = explode(';',trim($val->email_list));
                foreach($emails as $email){
                    $toBeforeYesterday[] = $email;
                }
            }
        }

        if($resultCountBeforeYesterday != 0 && !empty($resultGetBeforeYesterday)){
            $input = [
                'mail' => $toBeforeYesterday,
                'cc' => $cc,
                'subject' => '[CEM – Warning] – Danh sách các hợp đồng CSAT 1,2 chưa được báo cáo xử lý',
                'param' => $resultTotalBeforeYesterday,
            ];
            $job = (new SendMailTotalRemindCUSJob($input))->onQueue('emailsRemindCus');
            $this->dispatch($job);
        }
        if(!empty($resultGetYesterday)){
            foreach($resultGetYesterday as $val){
                $input = [
                    'mail' => explode(';',$val->email_list),
                    'cc' => $cc,
                    'subject' => '[CEM – Warning] – Lưu ý nhân viên quản lý - Hợp đồng CSAT 1,2 chưa được báo cáo xử lý trong ngày',
                    'param' => $val,
                ];
                $job = (new SendMailRemindCUSJob($input))->onQueue('emailsRemindCus');
                $this->dispatch($job);
            }
        }
    }

    /////////////////////////////////////////Private function//////////////////////////////////////////////
    private function tranferCSATAndNPSByDay($day) {
        $dateDefault = $day;
        $dateStart = $dateDefault . ' 00:00:00';
        $dateTo = $dateDefault . ' 23:59:59';

        $modelLocation = new Location();
        $location = $modelLocation->getAllLocation();

        $modelSurvey = new SurveySections();
        $modelObject = new OutboundRateObject();
        $modelCSAT = new OutboundRateSumCSAT();
        $modelNPS = new OutboundRateSumNPS();

        $csats = $modelObject->getRateObjectCSAT();
        $npss = $modelObject->getRateObjectNPS();
        DB::beginTransaction();
        try {
            foreach ($location as $val) {
                $temp = explode(' ', $val->region);
                $region = $temp[1];
                $branch = [$val->id];
                $branchcode = [$val->branchcode];
                if (empty($val->branchcode)) {
                    $branchcode = [0];
                }

                $resCSAT = $modelSurvey->getCSATInfo($region, $dateStart, $dateTo, $branch, $branchcode);
                $resNPS = $modelSurvey->getNPSStatisticReport($region, $dateStart, $dateTo, $branch, $branchcode);
                if (!empty($resCSAT)) {
                    $resPointCSAT = [1, 2, 3, 4, 5];
                    $resCSATFormat = [];
                    foreach ($resCSAT as $oneRes) {
                        $resCSATFormat['ans' . $oneRes->answers_point] = $oneRes;
                    }

                    foreach ($csats as $csat) {
                        $paramCSAT = null;
                        $paramCSAT['rate_sum_rate_object_id'] = $csat->rate_object_id;
                        $paramCSAT['rate_sum_date'] = $dateDefault;
                        foreach ($resPointCSAT as $point) {
                            $paramCSAT['rate_sum_point_' . $point] = isset($resCSATFormat['ans' . $point]) ? $resCSATFormat['ans' . $point]->$csat->rate_object_name : 0;
                        }
                        $paramCSAT['rate_sum_region'] = 'Vung ' . $region;
                        $paramCSAT['rate_sum_location'] = $val->id;
                        $paramCSAT['rate_sum_branch'] = $val->branchcode;
                        if (empty($val->branchcode)) {
                            $paramCSAT['rate_sum_branch'] = 0;
                        }
                        $modelCSAT->insertRateSumCSAT($paramCSAT);
                    }
                }

                if (!empty($resNPS)) {
                    $resPointNPS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
                    $resNPSFormat = [];
                    foreach ($resNPS as $oneRes) {
                        $resNPSFormat['ans' . $oneRes->answers_point] = $oneRes;
                    }

                    foreach ($npss as $nps) {
                        $paramNPS = null;
                        $paramNPS['rate_sum_rate_object_id'] = $nps->rate_object_id;
                        $paramNPS['rate_sum_date'] = $dateDefault;
                        foreach ($resPointNPS as $point) {
                            $paramNPS['rate_sum_point_' . $point] = isset($resNPSFormat['ans' . $point]) ? $resNPSFormat['ans' . $point]->$nps->rate_object_name : 0;
                        }
                        $paramNPS['rate_sum_region'] = 'Vung ' . $region;
                        $paramNPS['rate_sum_location'] = $val->id;
                        $paramNPS['rate_sum_branch'] = $val->branchcode;
                        if (empty($val->branchcode)) {
                            $paramNPS['rate_sum_branch'] = 0;
                        }
                        $modelNPS->insertRateSumNPS($paramNPS);
                    }
                }
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            dump($ex->getMessage());
            die;
        }
    }

    private function updateCSATAndNPSByDay($day) {
        $dateDefault = $day;
        $dateStart = $dateDefault . ' 00:00:00';
        $dateTo = $dateDefault . ' 23:59:59';

        $modelLocation = new Location();
        $location = $modelLocation->getAllLocation();

        $modelSurvey = new SurveySections();
        $modelObject = new OutboundRateObject();
        $modelCSAT = new OutboundRateSumCSAT();
        $modelNPS = new OutboundRateSumNPS();

        $csats = $modelObject->getRateObjectCSAT();
        $npss = $modelObject->getRateObjectNPS();
        DB::beginTransaction();
        try {
            $modelCSAT->removeRecord($day);
            $modelNPS->removeRecord($day);

            foreach ($location as $val) {
                $temp = explode(' ', $val->region);
                $region = $temp[1];
                $branch = [$val->id];
                $branchcode = [$val->branchcode];
                if (empty($val->branchcode)) {
                    $branchcode = [0];
                }

                $resCSAT = $modelSurvey->getCSATInfo($region, $dateStart, $dateTo, $branch, $branchcode);
                $resNPS = $modelSurvey->getNPSStatisticReport($region, $dateStart, $dateTo, $branch, $branchcode);
                if (!empty($resCSAT)) {
                    $resPointCSAT = [1, 2, 3, 4, 5];
                    $resCSATFormat = [];
                    foreach ($resCSAT as $oneRes) {
                        $resCSATFormat['ans' . $oneRes->answers_point] = $oneRes;
                    }

                    foreach ($csats as $csat) {
                        $paramCSAT = null;
                        $paramCSAT['rate_sum_rate_object_id'] = $csat->rate_object_id;
                        $paramCSAT['rate_sum_date'] = $dateDefault;
                        foreach ($resPointCSAT as $point) {
                            $paramCSAT['rate_sum_point_' . $point] = isset($resCSATFormat['ans' . $point]) ? $resCSATFormat['ans' . $point]->$csat->rate_object_name : 0;
                        }
                        $paramCSAT['rate_sum_region'] = 'Vung ' . $region;
                        $paramCSAT['rate_sum_location'] = $val->id;
                        $paramCSAT['rate_sum_branch'] = $val->branchcode;
                        if (empty($val->branchcode)) {
                            $paramCSAT['rate_sum_branch'] = 0;
                        }
                        $modelCSAT->insertRateSumCSAT($paramCSAT);
                    }
                }

                if (!empty($resNPS)) {
                    $resPointNPS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
                    $resNPSFormat = [];
                    foreach ($resNPS as $oneRes) {
                        $resNPSFormat['ans' . $oneRes->answers_point] = $oneRes;
                    }

                    foreach ($npss as $nps) {
                        $paramNPS = null;
                        $paramNPS['rate_sum_rate_object_id'] = $nps->rate_object_id;
                        $paramNPS['rate_sum_date'] = $dateDefault;
                        foreach ($resPointNPS as $point) {
                            $paramNPS['rate_sum_point_' . $point] = isset($resNPSFormat['ans' . $point]) ? $resNPSFormat['ans' . $point]->$nps->rate_object_name : 0;
                        }
                        $paramNPS['rate_sum_region'] = 'Vung ' . $region;
                        $paramNPS['rate_sum_location'] = $val->id;
                        $paramNPS['rate_sum_branch'] = $val->branchcode;
                        if (empty($val->branchcode)) {
                            $paramNPS['rate_sum_branch'] = 0;
                        }
                        $modelNPS->insertRateSumNPS($paramNPS);
                    }
                }
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            dump($ex->getMessage());
            die;
        }
    }

    private function convertDataRowToColumn($reports) {
        $arrayNeed = [];
        $arrayFieldRepeat = [
            'section_id', 'loaiKhaoSat', 'soViTri', 'tenViTri', 'chiNhanh', 'vung', 'soHopDong', 'diaChiKhachHang',
            'dienThoaiKhachHang', 'nhanVienKinhDoanh', 'section_supporter', 'section_subsupporter', 'thoiGianGhiNhan',
            'section_action'
        ];
        $arrayFieldNotRepeat = [
            'csat_maintenance_tv_point', 'csat_maintenance_net_point', 'csat_tv_point', 'csat_net_point',
            'csat_net_answer_extra_id', 'csat_tv_answer_extra_id', 'csat_maintenance_net_answer_extra_id', 'csat_maintenance_tv_answer_extra_id',
            'csat_net_note', 'csat_tv_note', 'csat_maintenance_net_note', 'csat_maintenance_tv_note', 'result_action_net', 'result_action_tv'
        ];
        foreach ($reports as $report) {
            $temp = new \stdClass;
            foreach ($arrayFieldRepeat as $fieldRepeat) {
                $temp->$fieldRepeat = $report->$fieldRepeat;
            }
            foreach ($arrayFieldNotRepeat as $fieldNotRepeat) {
                $temp->$fieldNotRepeat = null;
            }

            // Loại bỏ kí tự "=" đầu tiên khi CS nhập liệu
            $note = $report->survey_result_note;
            while(stripos($note, "=") === 0){
                $note = str_replace_first("=","",$note);
            }

            if (in_array($report->survey_result_question_id, [10, 12, 14, 20, 41, 46])) {
                if ($temp->loaiKhaoSat == 2) {
                    $temp->csat_maintenance_net_point = $report->survey_result_answer_id;
                    $temp->csat_maintenance_net_answer_extra_id = $report->survey_result_answer_extra_id;
                    $temp->csat_maintenance_net_note = $note;
                    $temp->result_action_net = $report->survey_result_action;
                } else {
                    $temp->csat_net_point = $report->survey_result_answer_id;
                    $temp->csat_net_answer_extra_id = $report->survey_result_answer_extra_id;
                    $temp->csat_net_note = $note;
                    $temp->result_action_net = $report->survey_result_action;
                }
            } else if (in_array($report->survey_result_question_id, [11, 13, 15, 21, 42, 47])) {
                if ($temp->loaiKhaoSat == 2) {
                    $temp->csat_maintenance_tv_point = $report->survey_result_answer_id;
                    $temp->csat_maintenance_tv_answer_extra_id = $report->survey_result_answer_extra_id;
                    $temp->csat_maintenance_tv_note = $note;
                    $temp->result_action_tv = $report->survey_result_action;
                } else {
                    $temp->csat_tv_point = $report->survey_result_answer_id;
                    $temp->csat_tv_answer_extra_id = $report->survey_result_answer_extra_id;
                    $temp->csat_tv_note = $note;
                    $temp->result_action_tv = $report->survey_result_action;
                }
            }

            if (isset($arrayNeed[$report->section_id])) {
                foreach ($arrayFieldNotRepeat as $fieldNotRepeat) {
                    if (!empty($temp->$fieldNotRepeat)) {
                        $arrayNeed[$report->section_id]->$fieldNotRepeat = $temp->$fieldNotRepeat;
                    }
                };
            } else {
                $arrayNeed[$report->section_id] = $temp;
            }
        }
        return $arrayNeed;
    }

}
