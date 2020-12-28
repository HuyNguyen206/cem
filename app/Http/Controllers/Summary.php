<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\SummaryCsat;
use App\Models\SummaryTime;
use App\Models\SummaryBranches;
use App\models\SummaryNps;
use App\Models\SurveySections;
use App\Models\OutboundAnswers;
use App\Models\SurveyReport;
use App\Models\SurveyViolations;
use App\models\SummaryOpinion;
use DB;

class Summary extends Controller {

    public function getCsatSummary($fromDay, $numDays) {
        set_time_limit(0);
        for ($i = 1; $i <= $numDays; $i++) {
            DB::beginTransaction();
//        $day = $this->argument('day');
//        if( empty($day ) ){
//            $dayNow = date('Y-m-d',time());
//        }
//        $day='2017-07-14';
            //while ( $day != $dayNow) {
            // lấy danh sách các điểm tiếp xúc
            try {
                $poc = $this->getPoc();
                foreach ($poc as $k => $v) {
                    $timeFrom = strtotime($fromDay . " 00:00:00");
                    $timeTo = strtotime($fromDay . " 23:59:59");
                    $questionList = (array) $v;
                    $result = DB::table('outbound_survey_sections AS s')
                            ->join('outbound_survey_result AS r', 's.section_id', '=', 'r.survey_result_section_id')
                            ->join('outbound_questions AS q', 'r.survey_result_question_id', '=', 'q.question_id')
                            ->join('outbound_answers AS a', 'r.survey_result_answer_id', '=', 'a.answer_id')
                            ->where('s.section_time_completed_int', '>', $timeFrom)
                            ->where('s.section_time_completed_int', '<', $timeTo)
                            ->where('s.section_connected', '=', '4')
                            ->where('s.section_survey_id', '=', $k)
                            ->whereIn('q.question_id', $questionList)
                            ->groupBy('s.section_sub_parent_desc', 's.section_survey_id', 's.section_location_id', 's.section_branch_code', 's.section_record_channel', 'q.question_id')
                            ->select(DB::raw('s.section_survey_id ,s.section_record_channel ,s.section_sub_parent_desc, s.section_location_id, s.section_branch_code, 
                        q.question_id,
                        SUM(case when a.answers_point = 0 then 1 else 0 end) as csat_0,
                        SUM(case when a.answers_point = 1 then 1 else 0 end) as csat_1,
                        SUM(case when a.answers_point = 2 then 1 else 0 end) as csat_2,
                        SUM(case when a.answers_point = 3 then 1 else 0 end) as csat_3,
                        SUM(case when a.answers_point = 4 then 1 else 0 end) as csat_4,
                        SUM(case when a.answers_point = 5 then 1 else 0 end) as csat_5'))
                            ->get();

                    foreach ($result as $row) {
                        $branch = new SummaryBranches();
                        $branchID = $branch->getBranchId($row->section_location_id, $row->section_branch_code);
//                                var_dump($branchID);die;
                        if ($branchID > 0) { // nhiều trường họp isc trả location_id = 0
                            $summaryCsat = new SummaryCsat();
                            $summaryTime = new SummaryTime();
                            $summaryCsat->time_id = $summaryTime->getTimeIdByDay($fromDay);
                            $summaryCsat->object_id = $this->mapQuestionToObjects($row->question_id);
                            $summaryCsat->branch_id = $branchID;
                            $summaryCsat->channel_id = $row->section_record_channel;
//                        $summaryCsat->question_id = $row->question_id;
//                        $summaryCsat->answer_point = $row->answers_point;
                            $summaryCsat->poc_id = $k;
                            $summaryCsat->csat_1 = $row->csat_1;
                            $summaryCsat->csat_2 = $row->csat_2;
                            $summaryCsat->csat_3 = $row->csat_3;
                            $summaryCsat->csat_4 = $row->csat_4;
                            $summaryCsat->csat_5 = $row->csat_5;
                            $summaryCsat->csat_0 = $row->csat_0;
                            // var_dump($summaryCsat );
                            // die();
                            $summaryCsat->save();
                        }
                    }
                }
                echo 'done-' . $fromDay . "\n";
                //    $day = date( "Y-m-d", strtotime( $day ." +1 day" ) );
                //}
                $date = strtotime("+1 day", strtotime($fromDay));
                $fromDay = date("Y-m-d", $date);
                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
            }
        }
    }

    protected function getPoc() {
        return array(
            '1' => array(1, 2, 10, 11), // sau triển khai
            '2' => array(4, 12, 13), // bảo trì
            '3' => array(14, 15), // mobipay
            //'4' => array(),// tại quầy
            // '5' => array(),// hifpt
            '6' => array(20, 21, 22, 23), // TLS
        );
    }

    public function getNpsSummary($fromDay, $numDays) {
        set_time_limit(0);
        for ($i = 1; $i <= $numDays; $i++) {
            DB::beginTransaction();
            try {
//        $day = $this->argument('day');
//        if( empty($day ) ){
//            $dayNow = date('Y-m-d',time());
//        }
//        $day='2017-07-14';
                //while ( $day != $dayNow) {
                // lấy danh sách các điểm tiếp xúc
                $poc = $this->getPocNps();
                foreach ($poc as $k => $v) {
                    $timeFrom = strtotime($fromDay . " 00:00:00");
                    $timeTo = strtotime($fromDay . " 23:59:59");
                    $questionList = (array) $v;
//            DB::connection()->enableQueryLog();
                    $result = DB::table('outbound_survey_sections AS s')
                            ->join('outbound_survey_result AS r', 's.section_id', '=', 'r.survey_result_section_id')
                            ->join('outbound_questions AS q', 'r.survey_result_question_id', '=', 'q.question_id')
                            ->join('outbound_answers AS a', 'r.survey_result_answer_id', '=', 'a.answer_id')
                            ->where('s.section_time_completed_int', '>', $timeFrom)
                            ->where('s.section_time_completed_int', '<', $timeTo)
                            ->where('s.section_connected', '=', '4')
                            ->where('s.section_survey_id', '=', $k)
                            ->where('r.survey_result_answer_id', '<>', -1)
                            ->whereIn('q.question_id', $questionList)
                            ->groupBy('s.section_sub_parent_desc', 's.section_survey_id', 's.section_location_id', 's.section_branch_code', 's.section_record_channel', 'q.question_id', 'a.answers_point')
                            ->select(DB::raw('s.section_survey_id ,s.section_record_channel ,s.section_sub_parent_desc, s.section_location_id, s.section_branch_code, 
                        q.question_id, a.answers_point,
                        SUM(case when a.answers_point = 0 then 1 else 0 end) as nps_0,
                        SUM(case when a.answers_point = 1 then 1 else 0 end) as nps_1,
                        SUM(case when a.answers_point = 2 then 1 else 0 end) as nps_2,
                        SUM(case when a.answers_point = 3 then 1 else 0 end) as nps_3,
                        SUM(case when a.answers_point = 4 then 1 else 0 end) as nps_4,
                        SUM(case when a.answers_point = 5 then 1 else 0 end) as nps_5,
                        SUM(case when a.answers_point = 6 then 1 else 0 end) as nps_6,
                        SUM(case when a.answers_point = 7 then 1 else 0 end) as nps_7,
                        SUM(case when a.answers_point = 8 then 1 else 0 end) as nps_8,
                        SUM(case when a.answers_point = 9 then 1 else 0 end) as nps_9,
                        SUM(case when a.answers_point = 10 then 1 else 0 end) as nps_10'))
//                                   ->tosql();
//                dd($result);die;
                            ->get();
//            $queries = DB::getQueryLog();
//            dd($queries);die;
                    foreach ($result as $row) {
                        $branch = new SummaryBranches();
                        $branchID = $branch->getBranchId($row->section_location_id, $row->section_branch_code);

//                                var_dump($branchID);die;
                        if ($branchID > 0) { // nhiều trường họp isc trả location_id = 0
                            $summaryNps = new SummaryNps();
                            $summaryTime = new SummaryTime();

                            $summaryNps->time_id = $summaryTime->getTimeIdByDay($fromDay);
                            $summaryNps->object_id = $this->mapQuestionToObjects($row->question_id);
//                     $summaryNps->object_id = 0;
                            $summaryNps->branch_id = $branchID;
                            $summaryNps->channel_id = $row->section_record_channel;
//                            $summaryNps->question_id = $row->question_id;
                            $summaryNps->poc_id = $k;
                            $summaryNps->nps_0 = $row->nps_0;
                            $summaryNps->nps_1 = $row->nps_1;
                            $summaryNps->nps_2 = $row->nps_2;
                            $summaryNps->nps_3 = $row->nps_3;
                            $summaryNps->nps_4 = $row->nps_4;
                            $summaryNps->nps_5 = $row->nps_5;
                            $summaryNps->nps_6 = $row->nps_6;
                            $summaryNps->nps_7 = $row->nps_7;
                            $summaryNps->nps_8 = $row->nps_8;
                            $summaryNps->nps_9 = $row->nps_9;
                            $summaryNps->nps_10 = $row->nps_10;
                            // var_dump($summaryCsat );
                            // die();
                            $summaryNps->save();
                        }
                    }
                }
                $date = strtotime("+1 day", strtotime($fromDay));
                $fromDay = date("Y-m-d", $date);
                echo 'done-' . $fromDay . "<br>";
                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
            }
        }
        //    $day = date( "Y-m-d", strtotime( $day ." +1 day" ) );
        //}
    }

    protected function getPocNps() {
        return array(
            '1' => array(6), // sau triển khai
            '2' => array(8), // bảo trì
            '3' => array(16), // mobipay
            //'4' => array(),// tại quầy
            // '5' => array(),// hifpt
            '6' => array(24), // TLS
        );
    }

    public function getOpinionSummary($fromDay, $numDays) {
        set_time_limit(0);
        $listOpinion = [
            27 => ['NVTK', 9],
            28 => ['GiaCuoc', 14],
            29 => ['Modem', 12],
            30 => ['TocDoTruyCap', 10],
            31 => ['NVKD', 9],
            32 => ['Khac', 19],
            52 => ['NVBT', 9],
            53 => ['NVHTTD', 9],
            54 => ['NVGDTQ', 9],
            55 => ['NVTCTN', 9],
            57 => ['KetNoiQuocTe', 10],
            58 => ['ChatLuongAmThanhHinhAnh', 11],
            59 => ['KhoNoiDung', 11],
            60 => ['CacTinhNangCuaTruyenHinh', 11],
            62 => ['HDBox', 12],
            63 => ['DieuKhien', 12],
            64 => ['ThuTucGiaoDichDKTT', 13],
            65 => ['CTKM', 13],
            66 => ['ChinhSachCSKH', 13],
            68 => ['PhiHoaMang', 14],
            69 => ['GiaThietBi', 14],
            70 => ['TinhOnDinh', 10],
            71 => ['DapUngSuDungGame', 10],
            72 => ['SachHuongDan', 11],
            73 => ['CapThueBao', 12],
            74 => ['CapLanNoiDenHDB', 12],
            75 => ['ChinhSachBaoTri', 13],
            76 => ['BoSungGoiCuoc', 14],
            77 => ['DauTuHaTangMoRongVP', 15],
            78 => ['SwapHaTang', 15],
            79 => ['TienDoTrienKhai', 16],
            80 => ['TienDoBaoTri', 16],
            81 => ['TienDoXuLyKhieuNai', 16],
            82 => ['QuayGiaoDich', 17],
            83 => ['CacKenhThongTinKhac', 17],
            138 => ['TocDoTruyCapLan', 10],
            139 => ['TocDoTruyCapWifi', 10]
        ];
        for ($i = 1; $i <= $numDays; $i++) {
            DB::beginTransaction();
            try {
                $poc = $this->getPocOpinion();
                foreach ($poc as $k => $v) {
                    $timeFrom = strtotime($fromDay . " 00:00:00");
                    $timeTo = strtotime($fromDay . " 23:59:59");
                    $questionList = (array) $v;
//            DB::connection()->enableQueryLog();
                    $result = DB::table('outbound_survey_sections AS s')
                            ->join('outbound_survey_result AS r', 's.section_id', '=', 'r.survey_result_section_id')
                            ->join('outbound_questions AS q', 'r.survey_result_question_id', '=', 'q.question_id')
                            ->join('outbound_answers AS a', 'r.survey_result_answer_id', '=', 'a.answer_id')
                            ->where('s.section_time_completed_int', '>', $timeFrom)
                            ->where('s.section_time_completed_int', '<', $timeTo)
                            ->where('s.section_connected', '=', '4')
                            ->where('s.section_survey_id', '=', $k)
                            ->where('r.survey_result_answer_id', '<>', -1)
                            ->whereIn('q.question_id', $questionList)
                            ->groupBy('s.section_sub_parent_desc', 's.section_survey_id', 's.section_location_id', 's.section_branch_code', 's.section_record_channel', 'q.question_id')
                            ->select(DB::raw('s.section_survey_id ,s.section_record_channel ,s.section_sub_parent_desc, s.section_location_id, s.section_branch_code, 
                        q.question_id, a.answers_point,
                        SUM(case when r.survey_result_answer_id like "%27%" then 1 else 0 end) as "NVTK",
                        SUM(case when r.survey_result_answer_id like "%28%" then 1 else 0 end) as "GiaCuoc",
                        SUM(case when r.survey_result_answer_id like "%29%" then 1 else 0 end) as "Modem",
                 SUM(case when r.survey_result_answer_id like "%30%" then 1 else 0 end) as "TocDoTruyCap",
                 SUM(case when r.survey_result_answer_id like "%31%" then 1 else 0 end) as "NVKD",
                 SUM(case when r.survey_result_answer_id like "%32%" then 1 else 0 end) as "Khac",
                 SUM(case when r.survey_result_answer_id like "%52%" then 1 else 0 end) as "NVBT",
                 SUM(case when r.survey_result_answer_id like "%53%" then 1 else 0 end) as "NVHTTD",
                 SUM(case when r.survey_result_answer_id like "%54%" then 1 else 0 end) as "NVGDTQ",
                 SUM(case when r.survey_result_answer_id like "%55%" then 1 else 0 end) as "NVTCTN",
                 SUM(case when r.survey_result_answer_id like "%57%" then 1 else 0 end) as "KetNoiQuocTe",
                 SUM(case when r.survey_result_answer_id like "%58%" then 1 else 0 end) as "ChatLuongAmThanhHinhAnh",
                 SUM(case when r.survey_result_answer_id like "%59%" then 1 else 0 end) as "KhoNoiDung",
                 SUM(case when r.survey_result_answer_id like "%60%" then 1 else 0 end) as "CacTinhNangCuaTruyenHinh",
                 SUM(case when r.survey_result_answer_id like "%62%" then 1 else 0 end) as "HDBox",
                 SUM(case when r.survey_result_answer_id like "%63%" then 1 else 0 end) as "DieuKhien",
                 SUM(case when r.survey_result_answer_id like "%64%" then 1 else 0 end) as "ThuTucGiaoDichDKTT",
                 SUM(case when r.survey_result_answer_id like "%65%" then 1 else 0 end) as "CTKM",
                 SUM(case when r.survey_result_answer_id like "%66%" then 1 else 0 end) as "ChinhSachCSKH",
                 SUM(case when r.survey_result_answer_id like "%68%" then 1 else 0 end) as "PhiHoaMang",
                 SUM(case when r.survey_result_answer_id like "%69%" then 1 else 0 end) as "GiaThietBi",
                 SUM(case when r.survey_result_answer_id like "%70%" then 1 else 0 end) as "TinhOnDinh",
                 SUM(case when r.survey_result_answer_id like "%71%" then 1 else 0 end) as "DapUngSuDungGame",
                 SUM(case when r.survey_result_answer_id like "%72%" then 1 else 0 end) as "SachHuongDan",
                 SUM(case when r.survey_result_answer_id like "%73%" then 1 else 0 end) as "CapThueBao",
                 SUM(case when r.survey_result_answer_id like "%74%" then 1 else 0 end) as "CapLanNoiDenHDB",
                 SUM(case when r.survey_result_answer_id like "%75%" then 1 else 0 end) as "ChinhSachBaoTri",
                 SUM(case when r.survey_result_answer_id like "%76%" then 1 else 0 end) as "BoSungGoiCuoc",
                 SUM(case when r.survey_result_answer_id like "%77%" then 1 else 0 end) as "DauTuHaTangMoRongVP",
                 SUM(case when r.survey_result_answer_id like "%78%" then 1 else 0 end) as "SwapHaTang",
                 SUM(case when r.survey_result_answer_id like "%79%" then 1 else 0 end) as "TienDoTrienKhai",
                 SUM(case when r.survey_result_answer_id like "%80%" then 1 else 0 end) as "TienDoBaoTri",
                 SUM(case when r.survey_result_answer_id like "%81%" then 1 else 0 end) as "TienDoXuLyKhieuNai",
                 SUM(case when r.survey_result_answer_id like "%82%" then 1 else 0 end) as "QuayGiaoDich",
                 SUM(case when r.survey_result_answer_id like "%83%" then 1 else 0 end) as "CacKenhThongTinKhac",
                 SUM(case when r.survey_result_answer_id like "%138%" then 1 else 0 end) as "TocDoTruyCapLan",
                 SUM(case when r.survey_result_answer_id like "%139%" then 1 else 0 end) as "TocDoTruyCapWifi"
                 
                        
'
                            ))
//                                   ->tosql();
//                dd($result);die;
                            ->get();
//            $queries = DB::getQueryLog();
//            dd($queries);die;

                    foreach ($result as $row) {
                        $branch = new SummaryBranches();
                        $branchID = $branch->getBranchId($row->section_location_id, $row->section_branch_code);

//                                var_dump($branchID);die;
                        if ($branchID > 0) { // nhiều trường họp isc trả location_id = 0
                            $summaryTime = new SummaryTime();
                            $timeId = $summaryTime->getTimeIdByDay($fromDay);
                            foreach ($listOpinion as $id => $opinion_groupId) {
                                $summaryOpinion = new SummaryOpinion();

                                $summaryOpinion->time_id = $timeId;
                                $summaryOpinion->object_id = $this->mapQuestionToObjects($row->question_id);
//                     $summaryNps->object_id = 0;
                                $summaryOpinion->branch_id = $branchID;
                                $summaryOpinion->channel_id = $row->section_record_channel;
//                    $summaryNps->question_id = $row->question_id;
                                $summaryOpinion->poc_id = $k;
                                $summaryOpinion->opinion_id = $id;
                                $summaryOpinion->group_id = $opinion_groupId[1];
                                $summaryOpinion->total = $row->$opinion_groupId[0];

                                // var_dump($summaryCsat );
                                // die();
                                $summaryOpinion->save();
                            }
                        }
                    }
                }
                echo 'done-' . $fromDay . "<br>";
                $date = strtotime("+1 day", strtotime($fromDay));
                $fromDay = date("Y-m-d", $date);
                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
            }
//        $day = $this->argument('day');
//        if( empty($day ) ){
//            $dayNow = date('Y-m-d',time());
//        }
//        $day='2017-07-14';
            //while ( $day != $dayNow) {
            // lấy danh sách các điểm tiếp xúc
        }
        //    $day = date( "Y-m-d", strtotime( $day ." +1 day" ) );
        //}
    }

    protected function getPocOpinion() {
        return array(
            '1' => array(7), // sau triển khai
            '2' => array(5), // bảo trì
            '3' => array(17), // mobipay
//            '4' => array(),// tại quầy
            // '5' => array(),// hifpt
            '6' => array(25), // TLS
        );
    }

    protected function mapQuestionToObjects($questionID) {
        $questionList = array(
            '1' => '1',
            '2' => '3',
            '4' => '4',
            '10' => '5',
            '11' => '6',
            '12' => '5',
            '13' => '6',
            '14' => '5',
            '15' => '6',
            '18' => '', // hifpt
            '20' => '5',
            '21' => '6',
            '22' => '3',
            '23' => '2',
            '26' => '4',
            '6' => '10',
            '8' => '10',
            '16' => '10',
            '24' => '10',
            '7' => '9',
            '5' => '9',
            '17' => '9',
            '25' => '9'
        );
        if (isset($questionList[$questionID]))
            return $questionList[$questionID];
        return 0;
    }

}
