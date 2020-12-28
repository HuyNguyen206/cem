<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
//use Illuminate\Support\Facades\DB;
use App\Models\SummaryTime;
use App\Models\SummaryBranches;
use App\Models\SummaryNps;
use DB;

class summaryDataNps extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summaryData:Nps {fromDay} {numDays}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get statistics Nps of type survey';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    //Dữ liệu trong bảng summary_nps là tổng hợp từ đầu năm đến 13/8/2017
    public function handle() {
      set_time_limit(0);
         $numDays = $this->argument('numDays');
    $fromDay = $this->argument('fromDay');
           for ($i = 1; $i <= $numDays; $i++) {
               DB::beginTransaction();
               try {
                    $poc = $this->getPocNps();
        foreach ($poc as $k => $v) {
            $timeFrom = strtotime($fromDay . " 00:00:00");
            $timeTo = strtotime($fromDay . " 23:59:59");
            $questionList = (array) $v;
//            DB::connection()->enableQueryLog();
              if ($k != 2) {
                  $result = DB::table('outbound_survey_sections AS s')
                    ->join('outbound_survey_result AS r', 's.section_id', '=', 'r.survey_result_section_id')
                    ->join('outbound_questions AS q', 'r.survey_result_question_id', '=', 'q.question_id')
                    ->join('outbound_answers AS a', 'r.survey_result_answer_id', '=', 'a.answer_id')
                    ->where('s.section_time_completed_int', '>=', $timeFrom)
                    ->where('s.section_time_completed_int', '<=', $timeTo)
                    ->where('s.section_connected', '=', '4')
                    ->where('s.section_survey_id', '=', $k)
                    ->where('r.survey_result_answer_id', '<>', -1)
                    ->whereIn('q.question_id', $questionList)
                    ->groupBy('s.section_sub_parent_desc', 's.section_survey_id', 's.section_location_id', 's.section_branch_code', 's.section_record_channel', 'q.question_id')
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
              }
              else
              {
            $result = DB::table('outbound_survey_sections AS s')
                    ->join('outbound_survey_result AS r', 's.section_id', '=', 'r.survey_result_section_id')
                    ->join('outbound_questions AS q', 'r.survey_result_question_id', '=', 'q.question_id')
                    ->join('outbound_answers AS a', 'r.survey_result_answer_id', '=', 'a.answer_id')
                    ->where('s.section_time_completed_int', '>=', $timeFrom)
                    ->where('s.section_time_completed_int', '<=', $timeTo)
                    ->where('s.section_connected', '=', '4')
                    ->where('s.section_survey_id', '=', $k)
                    ->where('r.survey_result_answer_id', '<>', -1)
                    ->whereIn('q.question_id', $questionList)
                    ->groupBy(DB::raw('s.section_sub_parent_desc,s.section_survey_id,s.section_location_id,s.section_branch_code,s.section_record_channel,q.question_id,
               CASE WHEN s.section_supporter NOT LIKE "%INDO%"   THEN "TIN_PN"
               WHEN s.section_supporter LIKE "%INDO%" THEN "INDO"
          END'))
                    ->select(DB::raw('s.section_survey_id ,s.section_record_channel ,s.section_sub_parent_desc, s.section_location_id, s.section_branch_code, 
                        q.question_id, 
                       ( CASE WHEN s.section_supporter NOT LIKE "%INDO%"  THEN "TIN_PN"
             WHEN s.section_supporter LIKE "%INDO%" THEN "INDO"

        END) as LoaiKhaoSat,
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
              }
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
                    $summaryNps->object_id =$k==2 ?($row->LoaiKhaoSat== 'TIN_PN' ? 25 :26) : 10; 
//                     $summaryNps->object_id = 0;
                    $summaryNps->branch_id = $branchID;
                    $summaryNps->channel_id = $row->section_record_channel;
//                    $summaryNps->question_id = $row->question_id;
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
          echo 'done-' . $fromDay . "\n";
         $date = strtotime("+1 day", strtotime($fromDay));
            $fromDay= date("Y-m-d", $date);
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
        //}
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array('day', InputArgument::OPTIONAL, 'ngay thuc hien tong hop du lieu'),
        );
    }

    /*
     * lấy time id thông qua năm tháng ngày
     */

    protected function getTimeIdByDay($day) {
        return;
    }

    /**
     * lấy danh sách diểm tiếp xúc
     * point of contact
     * tương ứng với loại khảo sát - outbound_survey
     */
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

    /**
     * lấy danh sách diểm tiếp xúc
     * point of contact
     * tương ứng với loại khảo sát - outbound_survey
     */
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

    /**
     * Đối tượng khảo sát
     */
    protected function getObjects() {
        return array(
            '1', //;Direct Sale  staff
            '2', //;Telesale  staff
            '3', //;Deploy staff
            '4', //;maintenance staff
            '5', //;Internet service
            '6' //Television service
        );
    }

    /**
     * map từng câu hỏi vào các đối tượng
     * câu hỏi nào ứng với đối tượng nào
     */
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
            '26' => '4'
        );
        if (isset($questionList[$questionID]))
            return $questionList[$questionID];
        return 0;
    }
    
       protected function mapQuestionToObjectsNps($questionID) {
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
            '26' => '4'
        );
        if (isset($questionList[$questionID]))
            return $questionList[$questionID];
        return 0;
    }

    /*     * kênh ghi nhận
     * kênh dữ liệu đầu vào - outbound_survey_sections / section_record_channel
     */

    protected function getRecordChannels() {
        return array(
            '1', // happy call
            '2', // Email + web
            '3', // HiFpt
            '4', // cus
        );
    }

    /** Vùng
     * định nghĩa vùng tương tụ - outbound_zone giảm khả năng truy xuất database
     */
    protected function getZone() {
        return array(
            '1', //Vùng 1
            '2', //Vùng 2
            '3', //Vùng 3
            '4', //Vùng 4
            '5', //Vùng 5
            '6', //Vùng 6
            '7', //Vùng 7
        );
    }

    /**
     * Lấy brachID tư location id va branch code
     */
    protected function getBranchIDByLocationAndBranchCode() {
        
    }

    /**
     * danh sach các chi nhanh
     */
    protected function getBranches() {
        return array(
            '1', //HNI1-Hà Nội
            '2', //HNI2-Hà Nội
            '3', //HNI3-Hà Nội
            '4', //HNI4-Hà Nội
            '5', //HNI5-Hà Nội
            '6', //HNI6-Hà Nội
            '7', //HNI7-Hà Nội
            '8', //HNI8-Hà Nội
            '9', //HNI9-Hà Nội
            '10', //HNI10-Hà Nội
            '11', //HNI11-Hà Nội
            '12', //HNI12-Hà Nội
            '13', //HNI13-Hà Nội
            '14', //HNI14-Hà Nội
            '15', //LCI-Lào Cai
            '16', //SLA-Sơn La
            '17', //LSN-Lạng Sơn
            '18', //CBG-Cao Bằng
            '19', //TQG-Tuyên Quang
            '20', //YBI-Yên Bái
            '21', //QNH-Quảng Ninh
            '22', //PTO-Phú Thọ
            '23', //VPC-Vĩnh Phúc
            '24', //HBH-Hòa Bình
            '25', //DBN-Điện Biên
            '26', //BGG-Bắc Giang
            '27', //BNH-Bắc Ninh
            '28', //TNN-Thái Nguyên
            '29', //NBH-Ninh Bình
            '30', //HPG-Hải Phòng
            '31', //TBH-Thái Bình
            '32', //THA-Thanh Hóa
            '33', //NAN-Nghệ An
            '34', //HTH-Hà Tĩnh
            '35', //HDG-Hải Dương
            '36', //HYN-Hưng Yên
            '37', //NDH-Nam Định
            '38', //HNM-Hà Nam
            '39', //QBH-Quảng Bình
            '40', //QTI-Quảng Trị
            '41', //HUE-Huế
            '42', //QNI-Quảng Ngãi
            '43', //BDH-Bình Định
            '44', //PYN-Phú Yên
            '45', //KHA-Nha Trang
            '46', //GLI-Gia Lai
            '47', //KTM-Kon Tum
            '48', //DLK-Dak Lak
            '49', //QNM-Quảng Nam
            '50', //DNG-Đà Nẵng
            '51', //HCM1-Hồ Chí Minh
            '52', //HCM2-Hồ Chí Minh
            '53', //HCM3-Hồ Chí Minh
            '54', //HCM4-Hồ Chí Minh
            '55', //HCM5-Hồ Chí Minh
            '56', //HCM6-Hồ Chí Minh
            '57', //HCM7-Hồ Chí Minh
            '58', //HCM8-Hồ Chí Minh
            '59', //HCM9-Hồ Chí Minh
            '60', //HCM10-Hồ Chí Minh
            '61', //HCM11-Hồ Chí Minh
            '62', //DNI-Đồng Nai
            '63', //BTN-Bình Thuận
            '64', //LDG-Lâm Đồng
            '65', //VTU-Vũng Tàu
            '66', //BDG-Bình Dương
            '67', //TNH-Tây Ninh
            '68', //NTN-Ninh Thuận
            '69', //BPC-Bình Phước
            '70', //DTP-Đồng Tháp
            '71', //VLG-Vĩnh Long
            '72', //CTO-Cần Thơ
            '73', //LAN-Long An
            '74', //TGG-Tiền Giang
            '75', //TVH-Trà Vinh
            '76', //BTE-Bến Tre
            '77', //AGG-An Giang
            '78', //KGG-Kiên Giang
            '79', //STG-Sóc Trăng
            '80', //HGG-Hậu Giang
            '81', //CMU-Cà Mau
            '82' //BLU-Bạc Liêu
        );
    }
        
        

}
