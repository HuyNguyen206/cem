<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
//use Illuminate\Support\Facades\DB;
use App\Models\SummaryTime;
use App\Models\SummaryBranches;
use App\Models\SummaryReason;
use DB;

class SummaryDataReason extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summaryData:Reason {fromDay} {numDays}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get statistics ReasonCsat12';

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
    //Dữ liệu trong bảng summary_csat là tổng hợp từ đầu năm đến 15/8/2017
    public function handle() {
        $numDays = $this->argument('numDays');
        $fromDay = $this->argument('fromDay');
        set_time_limit(0);
        $listCauseNet=[85, 86,87,88,89,90,91,92,94,97,98,120];
        $listCauseTv=[99,102,103,105,106,111,112,121,122,123,124,125,126,127];
        $listCauseAction = [
            85 => ['Net Chập chờn', 20],
            86 => ['Khác NET', 20],
            87 => ['Lỗi thiết bị', 20],
            88 => ['Lỗi Ivoice (nghe / nói / tone)', 20],
            89 => ['Wifi  yếu, chập chờn', 20],
            90 => ['Game lag', 20],
            91 => ['Không sử dụng được wifi', 20],
            92 => ['Mất tín hiệu', 20],
            94 => ['Có tín hiệu không truy cập được', 20],
            97 => ['Net chậm', 20],
            98 => ['TÍn hiệu không ổn định suy hao không đạt chuẩn', 20],
            120 => ['NET quốc tế chậm', 20],
            
            99 => ['Xé hình', 22],
            102 => ['Giật,Đứng hình , chập chờn', 22],
            103 => ['Có hình không có tiếng hoặc có tiếng không có hình tất cả các kênh', 22],
            105 => ['Không xem được các kênh truyền hình', 22],
            106 => ['Không sử dụng được thiết bị lưu trữ , mạng chia sẻ', 22],
            111 => ['Hình ảnh bị sọc ngang, sọc chéo', 22],
            112 => ['Lỗi kho ứng dụng', 22],
            121 => ['Lỗi kết nối HDBox &TV', 22],
            122 => ['Điều khiển , app điều khiển', 22],
            123 => ['Đấu nối thiết bị amply sử dụng KaraTV', 22],
            124 => ['Thiết bị Hdbox khởi động chậm', 22],
            125 => ['Không có hình , không có tiếng một vài kênh', 22],
            126 => ['Không xem được kho Phim', 22],
            127 => ['Khác TV', 22]
        ];
        for ($i = 1; $i <= $numDays; $i++) {
            DB::beginTransaction();
            try {
                // lấy danh sách các điểm tiếp xúc
                $poc = $this->getPocService();
                foreach ($poc as $k => $v) {
                    $timeFrom = strtotime($fromDay . " 00:00:00");
                    $timeTo = strtotime($fromDay . " 23:59:59");
                    $questionList = (array) $v;
                        $result = DB::table('outbound_survey_sections AS s')
                                ->join('outbound_survey_result AS r', 's.section_id', '=', 'r.survey_result_section_id')
                                ->join('outbound_questions AS q', 'r.survey_result_question_id', '=', 'q.question_id')
                                ->join('outbound_answers AS a', 'r.survey_result_answer_id', '=', 'a.answer_id')
                                ->where('s.section_time_completed_int', '>=', $timeFrom)
                                ->where('s.section_time_completed_int', '<=', $timeTo)
                                ->where('s.section_connected', '=', '4')
                                ->where('s.section_survey_id', '=', $k)
                                ->whereIn('r.survey_result_answer_id', [1, 2])
                                ->whereIn('q.question_id', $questionList)
                                ->groupBy('s.section_sub_parent_desc', 's.section_survey_id', 's.section_location_id', 's.section_branch_code', 's.section_record_channel', 'r.survey_result_answer_extra_id')
                                ->select(DB::raw('s.section_survey_id ,s.section_record_channel ,s.section_sub_parent_desc, s.section_location_id, s.section_branch_code, 
                        r.survey_result_answer_extra_id,
                         SUM(case when r.survey_result_answer_extra_id = 85 then 1 else 0 end) as "Net Chập chờn",
                        SUM(case when r.survey_result_answer_extra_id = 86 then 1 else 0 end) as "Khác NET",
                        SUM(case when r.survey_result_answer_extra_id =87 then 1 else 0 end) as "Lỗi thiết bị",
                 SUM(case when r.survey_result_answer_extra_id =88 then 1 else 0 end) as "Lỗi Ivoice (nghe / nói / tone)",
                 SUM(case when r.survey_result_answer_extra_id =89 then 1 else 0 end) as "Wifi  yếu, chập chờn",
                 SUM(case when r.survey_result_answer_extra_id =90 then 1 else 0 end) as "Game lag",
                 SUM(case when r.survey_result_answer_extra_id =91 then 1 else 0 end) as "Không sử dụng được wifi",
                 SUM(case when r.survey_result_answer_extra_id =92 then 1 else 0 end) as "Mất tín hiệu",
                 SUM(case when r.survey_result_answer_extra_id =94 then 1 else 0 end) as "Có tín hiệu không truy cập được",
                 SUM(case when r.survey_result_answer_extra_id =97 then 1 else 0 end) as "Net chậm",
                 SUM(case when r.survey_result_answer_extra_id =98 then 1 else 0 end) as "TÍn hiệu không ổn định suy hao không đạt chuẩn",
                 SUM(case when r.survey_result_answer_extra_id =99 then 1 else 0 end) as "Xé hình",
                 SUM(case when r.survey_result_answer_extra_id =102 then 1 else 0 end) as "Giật,Đứng hình , chập chờn",
                 SUM(case when r.survey_result_answer_extra_id =103 then 1 else 0 end) as "Có hình không có tiếng hoặc có tiếng không có hình tất cả các kênh",
                 SUM(case when r.survey_result_answer_extra_id =105 then 1 else 0 end) as "Không xem được các kênh truyền hình",
                 SUM(case when r.survey_result_answer_extra_id =106 then 1 else 0 end) as "Không sử dụng được thiết bị lưu trữ , mạng chia sẻ",
                 SUM(case when r.survey_result_answer_extra_id =111 then 1 else 0 end) as "Hình ảnh bị sọc ngang, sọc chéo",
                 SUM(case when r.survey_result_answer_extra_id =112 then 1 else 0 end) as "Lỗi kho ứng dụng",
                 SUM(case when r.survey_result_answer_extra_id =120 then 1 else 0 end) as "NET quốc tế chậm",
                 SUM(case when r.survey_result_answer_extra_id =121 then 1 else 0 end) as "Lỗi kết nối HDBox &TV",
                 SUM(case when r.survey_result_answer_extra_id =122 then 1 else 0 end) as "Điều khiển , app điều khiển",
                 SUM(case when r.survey_result_answer_extra_id =123 then 1 else 0 end) as "Đấu nối thiết bị amply sử dụng KaraTV",
                 SUM(case when r.survey_result_answer_extra_id =124 then 1 else 0 end) as "Thiết bị Hdbox khởi động chậm",
                 SUM(case when r.survey_result_answer_extra_id =125 then 1 else 0 end) as "Không có hình , không có tiếng một vài kênh",
                 SUM(case when r.survey_result_answer_extra_id =126 then 1 else 0 end) as "Không xem được kho Phim",
                 SUM(case when r.survey_result_answer_extra_id =127 then 1 else 0 end) as "Khác TV"                
                        
'
                                ))
//                                   ->tosql();
//                dd($result);die;
                                ->get();
//                    }

//            $queries = DB::getQueryLog();
//            dd($queries);die;

                    foreach ($result as $row) {
                        $branch = new SummaryBranches();
                        $branchID = $branch->getBranchId($row->section_location_id, $row->section_branch_code);

//                                var_dump($branchID);die;
                        if ($branchID > 0) { // nhiều trường họp isc trả location_id = 0
                            $summaryTime = new SummaryTime();
                            $timeId = $summaryTime->getTimeIdByDay($fromDay);
//                            foreach ($listCauseAction as $id => $titleGroupId) {
                                $summaryReason = new SummaryReason();

                                $summaryReason->time_id = $timeId;
                                $summaryReason->object_id = (in_array($row->survey_result_answer_extra_id,$listCauseNet)? 18 :17);
//                     $summaryNps->object_id = 0;
                                $summaryReason->branch_id = $branchID;
                                $summaryReason->channel_id = $row->section_record_channel;
//                    $summaryNps->question_id = $row->question_id;
                                $summaryReason->poc_id = $k;
                                $summaryReason->reason_id = $row->survey_result_answer_extra_id;
                                $summaryReason->group_id = $listCauseAction[$row->survey_result_answer_extra_id][1];
                                $summaryReason->total = $row->$listCauseAction[$row->survey_result_answer_extra_id][0];

                                // var_dump($summaryCsat );
                                // die();
                                $summaryReason->save();
//                            }
                        }
                    }
                }
                echo 'done-' . $fromDay . "\n";
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
        }
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
    protected function getPocService() {
        return array(
            '1' => array(10, 11), // sau triển khai
            '2' => array(12, 13), // bảo trì
            '3' => array(14, 15), // mobipay
//            '4' => array(),// tại quầy
            // '5' => array(),// hifpt
            '6' => array(20, 21), // TLS
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
