<?php

/*
 * Controlers kết nối tới API của ISC
 * 
 */

namespace App\Http\Controllers\Test;

use App\Component\HelpProvider;
use App\Http\Controllers\Controller;
use App\Models\ListEmailCUS;
use App\Models\SummaryBranches;
use App\Models\SurveySections;
use App\Models\SurveyViolations;
use App\Models\Api\ApiHelper;
use Illuminate\Support\Facades\Redis;
use App\Models\Location;
use App\Component\ExtraFunction;
use App\Models\SurveyResult;
use App\Models\OutboundAnswers;
use Illuminate\Support\Facades\Session;
use App\Models\RecordChannel;
use App\Models\Authen\User;
use DB;
use App\Models\Authen\Department;
use Exception;
use Illuminate\Support\Facades\Mail;
<<<<<<< .mine
use Exception;
use DB;
use App\Models\Apiisc;
use Maatwebsite\Excel\Facades\Excel;
=======
use App\Models\ListEmailSale;
use App\Models\ListEmailSir;
use App\Models\PushNotification;
use App\Jobs\ReSendNotificationEmail;
>>>>>>> .r93

class TestController extends Controller
{

    var $link_API = 'http://cemcc.fpt.net/';
    var $sales;
    var $deployer;
    var $maintenance;
    var $teleSales;
    var $clQGD;
    var $chargeStaff;

<<<<<<< .mine
    public function test()
    {
        dump(date('Y-m-d H:i:s', strtotime('2017-05-25T11:30:40.49')));
            die;
        $infoAcc = array('ObjID' => 0,
            'Contract' => 'BBDF22017',
            'IDSupportlist' => '6633802',
            'Type' => 2
        );

        /*
         * Lấy thông tin khách hàng
         */
        $apiIsc = new Apiisc();
        $responseAccountInfo = $apiIsc->GetFullAccountInfo($infoAcc);
        dump($responseAccountInfo);die;

        $this->getFileConverse();


        dump(explode(',', '1'));
        dump(explode(',', '1,2'));
        die;
//        $day='2018-11-01';
//        if(Redis::exists('testArrayDay'))
//            Redis::rpush('testArrayDay','2017-01-02');
//            $get=Redis::lpop('testArrayDay');
//        else Redis::set('testArrayDay',[]);
//                    var_dump($get);die;
//        die;
//        var_dump(date('l'));die;
        $help = new ApiHelper();
        $param['sectionId'] = 3880648;
//        $param['sectionId'] = 3676902;
//        $param['sectionId'] = 3516787;
//        $param['num_type'] = 2;
//        $param['code'] = 1102781962;
//        $param['shd'] = 'DLD024029';
=======
    var $modelSurveySections;
    var $modelDepartment;
    var $selNPSImprovement;
    var $selErrorType;
    var $selProcessingActions;
    var $columnView;
    var $columnDefault;
    var $modelRecordChannel;
    var $columnNeedToShow;
>>>>>>> .r93

    public function __construct() {
        $this->sales = 'sales';
        $this->deployer = 'deployer';
        $this->maintenance = 'maintenance';
        $this->teleSales = 'teleSales';
        $this->clQGD = 'clQGD';
        $this->chargeStaff = 'chargeStaff';
        $this->modelSurveySections = new SurveySections();
        $this->modelRecordChannel = new RecordChannel();
        $this->modelDepartment = new Department();

        $this->selNPSImprovement = $this->modelSurveySections->getNPSImprovement([9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
        $this->selErrorType = $this->modelSurveySections->getErrorType([20, 22, 25]);
        $this->selProcessingActions = $this->modelSurveySections->getProcessingActions([21]);
        $this->columnView = $this->columnDefault = [
            'section_id' => '',
            'salename' => 'NV kinh doanh',
            'csat_salesman_point' => 'CSAT NV kinh doanh',
            'csat_salesman_note' => 'Ghi chú',
            'violation_status sale' => 'Báo cáo xử lý CSAT',
            'section_supporter deploy' => 'NV kỹ thuật',
            'csat_deployer_point' => 'CSAT NV kỹ thuật',
            'csat_deployer_note' => 'Ghi chú',
            'violation_status deploy' => 'Báo cáo xử lý CSAT',
            'section_supporter maintaince' => 'NV kỹ thuật',
            'csat_maintenance_staff_point' => 'CSAT NV kỹ thuật',
            'csat_maintenance_staff_note' => 'Ghi chú',
            'keyTechStaffErrorType' => 'Lỗi của nhân viên kỹ thuât',
            'violation_status maintaince' => 'Báo cáo xử lý CSAT',
            'csat_tv_point' => 'CSAT DV truyền hình',
            'csat_tv_note' => 'Ghi chú',
            'csat_maintenance_tv_point' => 'CSAT DV truyền hình',
            'csat_maintenance_tv_note' => 'Ghi chú',
            'keyTVErrorType' => 'Loại lỗi truyền hình',
            'result_action_tv' => 'Hành động xử lý truyền hình',
            'csat_net_point' => 'CSAT DV Internet',
            'csat_net_note' => 'Ghi chú',
            'csat_maintenance_net_point' => 'CSAT DV Internet',
            'csat_maintenance_net_note' => 'Ghi chú',
            'keyNetErrorType' => 'Loại lỗi Internet',
            'result_action_net' => 'Hành động xử lý Internet',
            'csat_transaction_point' => 'CSAT chất lượng giao dịch',
            'csat_transaction_note' => 'Ghi chú',
            'violation_status clQGD' => 'Báo cáo xử lý CSAT',
            'csat_transaction_staff_point' => 'CSAT NV giao dịch',
            'csat_transaction_staff_note' => 'Ghi chú',
            'csat_charge_at_home_point' => 'CSAT chất lượng thu cước',
            'csat_charge_at_home_note' => 'Ghi chú',
            'csat_charge_at_home_staff_point' => 'CSAT NV thu cước',
            'csat_charge_at_home_staff_note' => 'Ghi chú',
            'csat_hmi_point' => 'CSAT HMI',
            'counter_code' => 'Mã quầy',
            'mac_address' => 'Địa chỉ MAC',
            'violation_status chargeStaff' => 'Báo cáo xử lý CSAT',
            'section_time_start_transaction' => 'Thời gian giao dịch',
            'section_user_create_transaction' => 'NV giao dịch',
            'section_name_change' => 'Khách hàng giao dịch',
            'section_office' => 'Văn phòng giao dịch',
            'section_kind_service' => 'Loại giao dịch',
            'nps_point' => 'NPS',
            'nps_improvement' => 'Góp ý khách hàng',
            'section_survey_id' => 'Điểm tiếp xúc',
            'section_action' => 'Xử lý',
            'section_connected' => 'Tình trạng',
            'section_contract_num' => 'Số HĐ',
            'section_contact_phone' => 'Số ĐT',
            'section_user_name' => 'NVCS',
            'section_sub_parent_desc' => 'Vùng',
            'section_branch_code' => 'Chi nhánh quản lý',
            'section_sale_branch_code' => 'Chi nhánh bán',
            'section_note' => 'Ghi chú tổng hợp',
            'section_time_completed' => 'Thời gian khảo sát',
            'section_count_connected' => 'Số lần khảo sát',
            'special' => '',

        ];

        //Cột cần show của table Triển khai DirectSale - HappyCall
        $dls = [
            'section_id' => null,
            'salename' => null,
            'csat_salesman_point' => null,
            'csat_salesman_note' => null,
            'violation_status sale' => null,
            'section_supporter deploy' => null,
            'csat_deployer_point' => null,
            'csat_deployer_note' => null,
            'violation_status deploy' => null,
            'csat_tv_point' => null,
            'csat_tv_note' => null,
            'keyTVErrorType' => null,
            'result_action_tv' => null,
            'csat_net_point' => null,
            'csat_net_note' => null,
            'keyNetErrorType' => null,
            'result_action_net' => null,
            'nps_point' => null,
            'nps_improvement' => null,
            'section_survey_id' => null,
            'section_action' => null,
            'section_connected' => null,
            'section_contract_num' => null,
            'section_contact_phone' => null,
            'section_user_name' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_sale_branch_code' => null,
            'section_note' => null,
            'section_time_completed' => null,
            'section_count_connected' => null,
            'special' => null,
        ];

        //Cột cần show của table Bảo trì - HappyCall
        $bt = [
            'section_id' => null,
            'section_supporter maintaince' => null,
            'csat_maintenance_staff_point' => null,
            'csat_maintenance_staff_note' => null,
            'violation_status maintaince' => null,
            'csat_maintenance_tv_point' => null,
            'csat_maintenance_tv_note' => null,
            'keyTVErrorType' => null,
            'result_action_tv' => null,
            'csat_maintenance_net_point' => null,
            'csat_maintenance_net_note' => null,
            'keyNetErrorType' => null,
            'result_action_net' => null,
            'nps_point' => null,
            'nps_improvement' => null,
            'section_survey_id' => null,
            'section_action' => null,
            'section_connected' => null,
            'section_contract_num' => null,
            'section_contact_phone' => null,
            'section_user_name' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_note' => null,
            'section_time_completed' => null,
            'section_count_connected' => null,
            'special' => null,
        ];

        //Cột cần show của table Bảo trì - HiFPT
        $btHiFPT = [
            'section_id' => null,
            'section_supporter maintaince' => null,
            'csat_maintenance_staff_point' => null,
            'csat_maintenance_staff_note' => null,
            'keyTechStaffErrorType' => null,
            'violation_status maintaince' => null,
            'csat_maintenance_tv_point' => null,
            'csat_maintenance_tv_note' => null,
            'keyTVErrorType' => null,
            'result_action_tv' => null,
            'csat_maintenance_net_point' => null,
            'csat_maintenance_net_note' => null,
            'keyNetErrorType' => null,
            'result_action_net' => null,
            'section_survey_id' => null,
            'section_action' => null,
            'section_connected' => null,
            'section_contract_num' => null,
            'section_contact_phone' => null,
            'section_user_name' => 'NVCS/Nguồn CS',
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_note' => null,
            'section_time_completed' => null,
            'section_count_connected' => null,
        ];

        //Cột cần show của table Thu cước tại nhà - Email
        $cusMail = [
            'section_id' => null,
            'section_user_create_transaction' => 'Nhân viên thu cước',
            'csat_charge_at_home_staff_point' => null,
            'csat_charge_at_home_staff_note' => null,
            'violation_status chargeStaff' => null,
            'section_survey_id' => null,
            'section_contract_num' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_time_completed' => 'Thời gian KH đánh giá',
            'section_time_start_transaction' => 'Thời gian thu cước',
        ];

        //Cột cần show của table Thu cước tại nhà - Mobipay
        $cusMobi = [
            'section_id' => null,
            'csat_tv_point' => null,
            'csat_tv_note' => null,
            'keyTVErrorType' => null,
            'result_action_tv' => null,
            'csat_net_point' => null,
            'csat_net_note' => null,
            'keyNetErrorType' => null,
            'result_action_net' => null,
            'section_survey_id' => null,
            'section_contract_num' => null,
            'section_user_name' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_time_completed' => null,
        ];

        //Cột cần show của table Triển khai TeleSale - HappyCall
        $tls = [
            'section_id' => null,
            'salename' => null,
            'csat_salesman_point' => null,
            'csat_salesman_note' => null,
            'violation_status sale' => null,
            'section_supporter deploy' => null,
            'csat_deployer_point' => null,
            'csat_deployer_note' => null,
            'violation_status deploy' => null,
            'csat_tv_point' => null,
            'csat_tv_note' => null,
            'keyTVErrorType' => null,
            'result_action_tv' => null,
            'csat_net_point' => null,
            'csat_net_note' => null,
            'keyNetErrorType' => null,
            'result_action_net' => null,
            'nps_point' => null,
            'nps_improvement' => null,
            'section_survey_id' => null,
            'section_action' => null,
            'section_connected' => null,
            'section_contract_num' => null,
            'section_contact_phone' => null,
            'section_user_name' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_sale_branch_code' => null,
            'section_note' => null,
            'section_time_completed' => null,
            'section_count_connected' => null,
            'special' => null,
        ];

        //Cột cần show của table Giao dịch tại quầy - Email
        $qgdMail = [
            'section_id' => null,
            'csat_transaction_point' => null,
            'csat_transaction_note' => null,
            'violation_status clQGD' => null,
            'section_user_create_transaction' => null,
            'section_name_change' => null,
            'section_office' => null,
            'section_kind_service' => null,
            'nps_point' => null,
            'section_survey_id' => null,
            'section_contract_num' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_time_completed' => 'Thời gian KH đánh giá',
            'section_time_start_transaction' => null,
        ];

        //Cột cần show của table Giao dịch tại quầy - Tablet
        $qgdTab = [
            'section_id' => null,
            'csat_transaction_point' => null,
            'csat_transaction_note' => null,
            'csat_transaction_staff_point' => null,
            'csat_transaction_staff_note' => null,
            'section_user_create_transaction' => null,
            'section_name_change' => null,
            'section_office' => null,
            'section_kind_service' => null,
            'section_survey_id' => null,
            'section_contract_num' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_time_completed' => 'Thời gian KH đánh giá',
            'section_time_start_transaction' => null,
        ];

        //Cột cần show của table Triển khai sale tại quầy - Happy Call
        $qgdSale = [
            'section_id' => null,
            'salename' => null,
            'csat_salesman_point' => null,
            'csat_salesman_note' => null,
            'violation_status sale' => null,
            'section_supporter deploy' => null,
            'csat_deployer_point' => null,
            'csat_deployer_note' => null,
            'violation_status deploy' => null,
            'csat_tv_point' => null,
            'csat_tv_note' => null,
            'keyTVErrorType' => null,
            'result_action_tv' => null,
            'csat_net_point' => null,
            'csat_net_note' => null,
            'keyNetErrorType' => null,
            'result_action_net' => null,
            'nps_point' => null,
            'nps_improvement' => null,
            'section_survey_id' => null,
            'section_action' => null,
            'section_connected' => null,
            'section_contract_num' => null,
            'section_contact_phone' => null,
            'section_user_name' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_sale_branch_code' => null,
            'section_note' => null,
            'section_time_completed' => null,
            'section_count_connected' => null,
            'special' => null,
        ];

        //Cột cần show của table Triển khai sau swap - Happy Call
        $swap = [
            'section_id' => null,
            'section_supporter deploy' => null,
            'csat_deployer_point' => null,
            'csat_deployer_note' => null,
            'violation_status deploy' => null,
            'csat_tv_point' => null,
            'csat_tv_note' => null,
            'keyTVErrorType' => null,
            'result_action_tv' => null,
            'csat_net_point' => null,
            'csat_net_note' => null,
            'keyNetErrorType' => null,
            'result_action_net' => null,
            'nps_point' => null,
            'nps_improvement' => null,
            'section_survey_id' => null,
            'section_action' => null,
            'section_connected' => null,
            'section_contract_num' => null,
            'section_contact_phone' => null,
            'section_user_name' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_sale_branch_code' => null,
            'section_note' => null,
            'section_time_completed' => null,
            'section_count_connected' => null,
            'special' => null,
        ];

        $hmi = [
            'section_id' => null,
            'csat_hmi_point' => null,
            'counter_code' => null,
            'mac_address' => null,
            'section_survey_id' => null,
            'section_sub_parent_desc' => null,
            'section_branch_code' => null,
            'section_time_completed' => 'Thời gian KH đánh giá',
        ];

        $this->columnNeedToShow = [
            //IBB
            '1' => [
                // Sau tk IBB
                '1:1' => $dls,

                // Bảo trì
                '2:1' => [
                ],
                '2:3' => [
                ],

                //Thu cước tại nhà
                '3:2' => [
                ],
                '3:4' => [
                ],

                //Sau tk telesale
                '6:1' => [
                ],

                //Giao dịch tại quầy
                '4:2' => [
                ],
                '4:6' => [
                ],
                '4:7' => [],

                //Sale tại quầy
                '9:1' => [
                ],

                // Swap
                '10:1' => [],
            ],
            //TIN
            '2' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
                '2:3' => $btHiFPT,

                //Thu cước
                '3:2' => [
                ],
                '3:4' => [
                ],

                //Sau tk telesale
                '6:1' => $tls,

                //Giao dịch tại quầy
                '4:2' => [
                ],
                '4:6' => [
                ],
                '4:7' => [],

                // Sau sale tại quầy
                '9:1' => $qgdSale,

                // Swap
                '10:1' => $swap,
            ],
            //PNC
            '3' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
                '2:3' => $btHiFPT,

                //Thu cước
                '3:2' => [
                ],
                '3:4' => [
                ],

                //Sau tk telesale
                '6:1' => $tls,

                //Giao dịch tại quầy
                '4:2' => [
                ],
                '4:6' => [
                ],
                '4:7' => [],

                // Sau sale tại quầy
                '9:1' => $qgdSale,

                // Swap
                '10:1' => $swap,
            ],
            //INDO
            '4' => [
                // Sau tk IBB
                '1:1' => [
                ],

                // Bảo trì
                '2:1' => $bt,
                '2:3' => $btHiFPT,

                //Thu cước
                '3:2' => [
                ],
                '3:4' => [
                ],

                //Sau tk telesale
                '6:1' => [],

                //Giao dịch tại quầy
                '4:2' => [],
                '4:6' => [],
                '4:7' => [],

                // Sau Sale tại quầy
                '9:1' => [],

                // Swap
                '10:1' => [],
            ],
            //BĐH
            '5' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
                '2:3' => $btHiFPT,

                //Thu cước
                '3:2' => $cusMail,
                '3:4' => $cusMobi,

                //Sau tk telesale
                '6:1' => $tls,

                //Giao dịch tại quầy
                '4:2' => $qgdMail,
                '4:6' => $qgdTab,
                '4:7' => $hmi,

                // Sale tại quầy
                '9:1' => $qgdSale,

                // Swap
                '10:1' => $swap,
            ],
            //CS
            '6' => [
                //Triển khai DirectSale
                '1:1' => $dls,

                //Bảo trì
                '2:1' => $bt,
                '2:3' => $btHiFPT,

                //Thu cước
                '3:2' => [
                ],
                '3:4' => [
                ],

                //Sau tk telesale
                '6:1' => $tls,

                //Giao dịch tại quầy
                '4:2' => $qgdMail,
                '4:6' => $qgdTab,
                '4:7' => $hmi,

                // Sale tại quầy
                '9:1' => $qgdSale,

                // Swap
                '10:1' => $swap,
            ],
            //CUS
            '7' => [
                '1:1' => [
                ],

                // Bảo trì
                '2:1' => [
                ],
                '2:3' => [
                ],

                //Thu cước
                '3:2' => $cusMail,
                '3:4' => $cusMobi,

                //Sau tk telesale
                '6:1' => [
                ],

                //Giao dịch tại quầy
                '4:2' => [
                ],
                '4:6' => [
                ],
                '4:7' => [],

                // Sale tại quầy
                '9:1' => [
                ],

                // Swap
                '10:1' => [],
            ],
            //TELESALES
            '8' => [
                '1:1' => [
                ],

                // Bảo trì
                '2:1' => [
                ],
                '2:3' => [
                ],

                //Thu cước
                '3:2' => [
                ],
                '3:4' => [
                ],

                //Sau tk telesale
                '6:1' => $tls,

                //Giao dịch tại quầy
                '4:2' => [
                ],
                '4:6' => [
                ],
                '4:7' => [],

                // Sale tại quầy
                '9:1' => [
                ],

                // Swap
                '10:1' => [],
            ],
        ];
    }


    public function test() {
        try{
            $help = new HelpProvider();
            try {
                //Lấy ra danh sách api Net cần send lại
                $model_push = new PushNotification();
                $resPush = $model_push->getPushNotificationSendMailAgain();
                foreach ($resPush as $val) {
                    $input = (array) $val;
                    //Đưa vào hàng đợi gửi lại thông báo
                    $job = (new ReSendNotificationEmail($input))->onQueue('emails');
                    Bus::dispatch($job);
                }
                $result = 'Đã tiến hành gửi';
                return $help->responseSuccess($result);
            } catch (Exception $e) {
                return $help->responseFail($e->getCode(), $e->getMessage());
            }
        }
        catch(Exception $ex){
            dump($ex->getMessage());
        }
    }

    public function getSearchHistory(){
        $condition = [
            "surveyFrom" => "2018-10-01 00:00:00",
            "surveyTo" => "2018-10-17 23:59:59",
            "surveyFromInt" => strtotime("2018-10-01 00:00:00"),
            "surveyToInt" => strtotime("2018-10-17 23:59:59"),
            "region" => [
            ],
            "location" => [
            ],
            "branchcode" => [
            ],
            "branchcodeSalesMan" => [],
            "brandcodeSaleMan" => "",
            "contractNum" => "",
            "section_action" => "",
            "section_connected" => [
            ],
            "CSATPointSale" => "",
            "CSATPointNVTK" => "",
            "CSATPointBT" => "",
            "CSATPointNet" => "",
            "CSATPointTV" => "",
            "userSurvey" => "",
            "RateNPS" => "",
            "NPSPoint" => "",
            "departmentType" => "5",
            "salerName" => "",
            "technicalStaff" => "",
            "reportedStatus" => "",
            "NetErrorType" => "",
            "TVErrorType" => "",
            "processingActionsTV" => "",
            "processingActionsInternet" => "",
            "allQuestion" => [
                1 => [
                    0 => 1,
                    1 => 28,
                ],
                3 => [
                    0 => 2,
                    1 => 22,
                    2 => 29,
                    3 => 38,
                ],
                4 =>[
                    0 => 4,
                    1 => 30,
                    2 => 51,
                ],
                9 => [
                    0 => 5,
                    1 => 7,
                    2 => 17,
                    3 => 25,
                    4 => 40,
                    5 => 44,
                ],
                10 =>[
                    0 => 6,
                    1 => 8,
                    2 => 16,
                    3 => 24,
                    4 => 27,
                    5 => 34,
                    6 => 39,
                    7 => 45,
                ],
                5 => [
                    0 => 10,
                    1 => 12,
                    2 => 14,
                    3 => 20,
                    4 => 41,
                    5 => 46,
                    6 => 49,
                ],
                6 =>[
                    0 => 11,
                    1 => 13,
                    2 => 15,
                    3 => 21,
                    4 => 42,
                    5 => 47,
                    6 => 50,
                ],
                11 =>[
                    0 => 18,
                ],
                2 => [
                    0 => 23,
                    1 => 32,
                ],
                7 => [
                    0 => 26,
                ],
                8 => [
                    0 => 31,
                ],
                13 => [
                    0 => 33,
                ],
                14 => [
                    0 => 35,
                ],
                29 => [
                    0 => 37,
                ],
                30 => [
                    0 => 43,
                ],
                -1 => [
                    0 => 48,
                ],
            ],
            "channelConfirm" => "1",
            "CSATPointTransaction" => "",
            "transactionStaffName" => "",
            "transactionType" => "",
            "CSATPointChargeAtHomeStaff" => "",
            "chargeAtHomeStaffName" => "",
            "recordPerPage" => 0,
            "justOnlyLocation" => false,
            "locationSQL" => [
            ],
            "type" => 1,
        ];
        $currentPage = 0;
        $modelSurveySections = new SurveySections();
        $infoSurvey = $modelSurveySections->searchListSurvey($condition, $currentPage);

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

        echo "<table><thead><tr>";
        foreach($this->columnView as $key => $val) {
            if ($key != 'section_id') {
                if ($key == 'csat_salesman_note' || $key == 'csat_deployer_note' || $key == 'csat_maintenance_staff_note' || $key == 'csat_transaction_note' || $key == 'csat_transaction_staff_note' || $key == 'csat_charge_at_home_note' || $key == 'csat_charge_at_home_staff_note') {
                    echo '<th width = "50" >'.$val.'</th >';
                }else{
                    echo '<th>'.$val.'</th >';
                }
            }
        }
        echo '</tr></thead><tbody>';
        foreach($dataPage as $stt => $data) {
            echo '<tr>';
            foreach ($data as $key => $val) {
                if ($key != 'section_id') {
                    echo '<td>'.$val.'</td >';
                }
            }
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
    public function repairDataForViewHistoryExcel($infoSurvey, $condition) {
        $columnView = $this->columnNeedToShow[$condition['departmentType']][$condition['type'].':'.$condition['channelConfirm']];
        foreach($columnView as $key => $val){
            if(empty($val)){
                $columnView[$key] = $this->columnDefault[$key];
            }
        }
        $arrayAccountShowSomeColumn = [36];
        $userRole = Session::get('userRole');
        if (array_search($userRole['id'], $arrayAccountShowSomeColumn) === false) {
            if (isset($columnView['section_count_connected'])) {
                unset($columnView['section_count_connected']);
            }
        }
        $this->columnView = $columnView;

<<<<<<< .mine
    private function convertRowToColumnDetail($condition, $infoSurveyKey, $surveyResults)
    {
=======
        $data = [];
        $arrErrorType = json_decode(json_encode($this->selErrorType), 1);
        $arrActions = json_decode(json_encode($this->selProcessingActions), 1);
        $arrayAction = [0 => 'Không làm gì', 1 => 'Không làm gì', 2 => 'Tạo checklist', 3 => 'PreChecklist', 4 => 'Tạo checklist INDO', 5 => 'Chuyển phòng ban khác'];
        $arrayResult = [0 => "Không cần liên hệ", 1 => "Không liên lạc được", 2 => "Gặp KH, KH từ chối CS", 3 => "Không gặp người SD", 4 => "Gặp người SD"];
        $surveyTitle = [
            1 => 'Sau Triển khai DirectSale',
            2 => 'Sau Bảo trì',
            3 => 'Sau Thu cước tại nhà',
            4 => 'Sau Giao dịch tại quầy',
            5 => 'HiFPT',
            6 => 'Sau Triển khai TeleSale',
            7 => 'Sau Thu cước tại nhà',
            9 => 'Sau Triển khai Sale tại quầy',
            10 => 'Sau Triển khai Swap',
            11 => 'Sau Giao dịch tại quầy',
            12 => 'Sau Bảo trì',
        ];
        $SpecicalSaleBranchCode = [94=>'ITV', 200=>'Dai ly', 95=>'FN', 97=>'FTTH',98=>'KDDA',90=>'FTI',93=>'Ivoi'];
        foreach ($this->selNPSImprovement as $value) {
            $surveyImprove[$value->answer_id] = $value->answers_title;
            $surveyImprove['-1'] = 'Chưa trả lời';
            $surveyImprove[''] = '';
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

        foreach ($infoSurvey as $index => $surveySections) {
            if (strpos($surveySections->nps_improvement, ',') !== false) {
                $tempImprove = explode(',', $surveySections->nps_improvement);
                $surveySections->nps_improvement = '';
                foreach ($tempImprove as $val) {
                    $surveySections->nps_improvement .= $surveyImprove[$val] . ',';
                }
                $surveySections->nps_improvement = substr($surveySections->nps_improvement, 0, -1);
            } else {
                $surveySections->nps_improvement = $surveyImprove[$surveySections->nps_improvement];
            }
            $keyNetErrorType = (in_array($surveySections->section_survey_id, [1, 3, 6, 10])) ? 'csat_net_answer_extra_id' : 'csat_maintenance_net_answer_extra_id';
            $keyTVErrorType = (in_array($surveySections->section_survey_id, [1, 3, 6, 10])) ? 'csat_tv_answer_extra_id' : 'csat_maintenance_tv_answer_extra_id';
            $keyTVErr = array_search($surveySections->$keyTVErrorType, array_column($arrErrorType, 'answer_id'));
            $keyNetErr = array_search($surveySections->$keyNetErrorType, array_column($arrErrorType, 'answer_id'));
            $keyNetActions = array_search($surveySections->result_action_net, array_column($arrActions, 'answer_id'));
            $keyTVActions = array_search($surveySections->result_action_tv, array_column($arrActions, 'answer_id'));
            $keyTechStaffErrorType = (in_array($surveySections->section_survey_id, [1, 3, 6, 10])) ? 'csat_deploy_answer_extra_id' : 'csat_maintenance_staff_answer_extra_id';
            $keyTechStaffErr = array_search($surveySections->$keyTechStaffErrorType, array_column($arrErrorType, 'answer_id'));

            // Gắn lại tình trạng báo cáo xử lý
            $viStatus = ['sales' => null, 'deployer' => null, 'maintenance' => null, 'teleSales' => null, 'clQGD' => null, 'chargeStaff' => null];
            if(!empty($surveySections->violation_status)){
                $viStatus = array_merge($viStatus, json_decode($surveySections->violation_status, 1));
            }

            $dataRow = [];
            foreach ($columnView as $key => $val) {
                switch ($key) {
                    case 'violation_status sale':
                        if ($dataRow['csat_salesman_point'] == '' || $dataRow['csat_salesman_point'] >= 3) {
                            $dataRow[$key] = 'Không cần báo cáo';
                        } elseif ($viStatus['sales'] == 2 || $viStatus['teleSales'] == 2) {
                            $dataRow[$key] = "Đã báo cáo";
                        } else {
                            $dataRow[$key] = "Chưa báo cáo";
                        }
                        break;
                    case 'violation_status deploy':
                        if ($dataRow['csat_deployer_point'] == '' || $dataRow['csat_deployer_point'] >= 3) {
                            $dataRow[$key] = 'Không cần báo cáo';
                        } elseif ($viStatus['deployer'] == 2) {
                            $dataRow[$key] = "Đã báo cáo";
                        } else {
                            $dataRow[$key] = "Chưa báo cáo";
                        }
                        break;
                    case 'section_supporter deploy':
                        $dataRow[$key] = (!empty($surveySections->section_supporter) ? $surveySections->section_supporter : '') . (!empty($surveySections->section_subsupporter) ? ' ' . $surveySections->section_subsupporter : '');
                        break;
                    case 'section_supporter maintaince':
                        $dataRow[$key] = (!empty($surveySections->section_supporter) ? $surveySections->section_supporter : '') . (!empty($surveySections->section_subsupporter) ? ' ' . $surveySections->section_subsupporter : '');
                        break;
                    case 'violation_status maintaince':
                        if ($dataRow['csat_maintenance_staff_point'] == '' || $dataRow['csat_maintenance_staff_point'] >= 3) {
                            $dataRow[$key] = 'Không cần báo cáo';
                        } elseif ($viStatus['maintenance'] == 2) {
                            $dataRow[$key] = "Đã báo cáo";
                        } else {
                            $dataRow[$key] = "Chưa báo cáo";
                        }
                        break;
                    case 'keyTechStaffErrorType':
                        $dataRow[$key] = ($keyTechStaffErr !== false) ? $this->selErrorType[$keyTechStaffErr]->answers_title : '';
                        break;
                    case 'violation_status clQGD':
                        if ($dataRow['csat_transaction_point'] == '' || $dataRow['csat_transaction_point'] >= 3) {
                            $dataRow[$key] = 'Không cần báo cáo';
                        } elseif ($viStatus['clQGD'] == 2) {
                            $dataRow[$key] = 'Đã báo cáo';
                        } else {
                            $dataRow[$key] = 'Chưa báo cáo';
                        }
                        break;
                    case 'violation_status chargeStaff':
                        if ($dataRow['csat_charge_at_home_staff_point'] == '' || $dataRow['csat_charge_at_home_staff_point'] >= 3) {
                            $dataRow[$key] = 'Không cần báo cáo';
                        } elseif ($viStatus['chargeStaff'] == 2) {
                            $dataRow[$key] = 'Đã báo cáo';
                        } else {
                            $dataRow[$key] = 'Chưa báo cáo';
                        }
                        break;
                    case 'keyTVErrorType':
                        $dataRow[$key] = ($keyTVErr !== false) ? $this->selErrorType[$keyTVErr]->answers_title : '';
                        break;
                    case 'result_action_tv':
                        $dataRow[$key] = !empty($surveySections->result_action_tv) ? $this->selProcessingActions[$keyTVActions]->answers_title : '';
                        break;
                    case 'keyNetErrorType':
                        $dataRow[$key] = ($keyNetErr !== false) ? $this->selErrorType[$keyNetErr]->answers_title : '';
                        break;
                    case 'result_action_net':
                        $dataRow[$key] = !empty($surveySections->result_action_net) ? $this->selProcessingActions[$keyNetActions]->answers_title : '';
                        break;
                    case 'section_survey_id':
                        $dataRow[$key] = !empty($surveyTitle[$surveySections->section_survey_id]) ? $surveyTitle[$surveySections->section_survey_id] : '';
                        break;
                    case 'section_action':
                        $dataRow[$key] = $arrayAction[$surveySections->section_action];
                        break;
                    case 'section_connected':
                        $dataRow[$key] = $arrayResult[$surveySections->section_connected];
                        break;
                    case 'section_sub_parent_desc':
                        $dataRow[$key] = str_replace('Vung', 'Vùng', $surveySections->section_sub_parent_desc);
                        break;
                    case 'section_branch_code':
                        $dataRow[$key] = isset($allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_branch_code]) ? $allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_branch_code]: '';
                        break;
                    case 'section_sale_branch_code':
                        $locationName = '';
                        if(!empty($surveySections->section_sale_branch_code)){
                            if(in_array($surveySections->section_sale_branch_code, [94,200,95,97,98,90,93])){
                                $locationName = $SpecicalSaleBranchCode[$surveySections->section_sale_branch_code];
                            }else{
                                $locationName = isset($allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_sale_branch_code]) ? $allLocationKey[$surveySections->section_location_id.':'.$surveySections->section_sale_branch_code]: '';
                            }
                        }
                        $dataRow[$key] = $locationName;
                        break;
                    case 'special':
                        $dataRow[$key] = '';
                        break;
                    case 'csat_salesman_point':
                    case 'csat_deployer_point':
                    case 'csat_maintenance_staff_point':
                    case 'csat_tv_point':
                    case 'csat_net_point':
                    case 'csat_maintenance_tv_point':
                    case 'csat_maintenance_net_point':
                    case 'csat_transaction_point':
                    case 'csat_transaction_staff_point':
                    case 'csat_charge_at_home_point':
                    case 'csat_charge_at_home_staff_point':
                    case 'csat_hmi_point':
                        $dataRow[$key] = (empty($surveySections->$key)) ? '' : $surveySections->$key;
                        break;
                    case 'section_note':
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
>>>>>>> .r93
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
            $info->csat_deploy_answer_extra_id = null;

            $info->csat_maintenance_staff_point = null;
            $info->csat_maintenance_staff_note = null;
            $info->csat_maintenance_staff_answer_extra_id = null;

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

            $info->csat_hmi_point = null;

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

            if(array_search($result->survey_result_question_id, array_merge($condition['allQuestion'][1], $condition['allQuestion'][2], $condition['allQuestion'][29])) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_salesman_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_salesman_note = $note;
            }
            if(array_search($result->survey_result_question_id, array_merge($condition['allQuestion'][3], $condition['allQuestion'][30])) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_deployer_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_deployer_note = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][4]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_maintenance_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_maintenance_staff_note = $note;
                $infoSurveyKey[$result->survey_result_section_id]->csat_maintenance_staff_answer_extra_id = $result->survey_result_answer_extra_id;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][5]) !== false){
                $keyP = 'csat'.$maintenance.'_net_point';
                $keyN = 'csat'.$maintenance.'_net_note';
                $keyA = 'csat'.$maintenance.'_net_answer_extra_id';
                $infoSurveyKey[$result->survey_result_section_id]->$keyP = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->$keyN = $note;
                $infoSurveyKey[$result->survey_result_section_id]->$keyA = $result->survey_result_answer_extra_id;
                $infoSurveyKey[$result->survey_result_section_id]->result_action_net = $result->survey_result_action;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][6]) !== false){
                $keyP = 'csat'.$maintenance.'_tv_point';
                $keyN = 'csat'.$maintenance.'_tv_note';
                $keyA = 'csat'.$maintenance.'_tv_answer_extra_id';
                $infoSurveyKey[$result->survey_result_section_id]->$keyP = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->$keyN = $note;
                $infoSurveyKey[$result->survey_result_section_id]->$keyA = $result->survey_result_answer_extra_id;
                $infoSurveyKey[$result->survey_result_section_id]->result_action_tv = $result->survey_result_action;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][7]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_note = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][8]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_transaction_staff_note = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][9]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->nps_improvement = $result->survey_result_answer_id;
                $infoSurveyKey[$result->survey_result_section_id]->nps_improvement_note = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][10]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->nps_point = $ansPoints[$result->survey_result_answer_id];
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][13]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_note = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][14]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_staff_point = $ansPoints[$result->survey_result_answer_id];
                $infoSurveyKey[$result->survey_result_section_id]->csat_charge_at_home_staff_note = $note;
            }
            if(array_search($result->survey_result_question_id, $condition['allQuestion'][-1]) !== false){
                $infoSurveyKey[$result->survey_result_section_id]->csat_hmi_point = $ansPoints[$result->survey_result_answer_id];
            }
        }
        return $infoSurveyKey;
    }

    public function testInfo($contract, $type, $code)
    {

        $apiISC = new Apiisc();
        $infoAcc = array('ObjID' => 0,
            'Contract' => $contract,
            'IDSupportlist' => $code,
            'Type' => $type
        );
//        $uri = $this->link_API . 'wscustomerinfo.asmx/spCEM_ObjectGetByObjID?';
        $uri = 'http://parapi.fpt.vn/api/RadAPI/spCEM_ObjectGetByObjID/?';
        $uri .= http_build_query($infoAcc);
//        $uri = $this->link_API . 'wscustomerinfo.asmx/spCEM_ObjectGetByObjID?Contract = ' . $contract . '&ID = ' . $code . '&Type = ' . $type;
//        var_dump($apiISC->getAPI($uri));
        dd(json_decode($apiISC->getAPI($uri)));
    }

    public function testExportExcel()
    {
        $user = new User();
        $resultUser = $user->getUserWithZoneRole();
        return view('test/index', ['data' => $resultUser]);
    }

    public function clearCache()
    {
        $data = Redis::keys('laravel:*:stan*');
        foreach ($data as $key) {
            Redis::del($key);
        }
    }

    public function testEmail()
    {
<<<<<<< .mine
        return view('emails/templateEmail', [
                'zone' => 1,
                'day' => date('d/m/Y'),
                'records' => ['abc'],
            ]
        );


//        return view('emails/templateExcelCSATtv',
//            [
//                'zone' => 1,
//                'day' => date('d/m/Y'),
//                'records' => ['abc'],
//            ]
//        );
=======
        $data = [
            'abc'
        ];
//        $from = 'rad.support@fpt.com.vn';
        $from = 'customer-voice@opennet.com.kh';
        $mail = 'huydp2@fpt.com.vn';
        $cc = '';
        $subject = 'test';
        Mail::send('emails.test', $data ,function ($message) use ($from, $mail, $cc, $subject) {
            $message->from($from, 'Support');
            $message->to($mail);
            $message->subject($subject);
        });
>>>>>>> .r93
    }

    public function updateViolationPoints()
    {
        try {
            $result = DB::table('survey_violations AS s')
                ->select(DB::raw('distinct(s.section_id)'))
                ->where('s.insert_at', '>=', '2017-08-01 00:00:00')
                ->get();
            foreach ($result as $key => $value) {
                $violation = SurveyViolations::where('section_id', '=', $value->section_id)->get();
                foreach ($violation as $key2 => $value2) {
                    $partViolation = $value2;
                    $sectionResult = DB::table('outbound_survey_result AS osr')
                        ->select(DB::raw('
                    MAX(if(osr.survey_result_question_id in (1,23), osr.survey_result_answer_id, NULL)) "NVKD",
                    MAX(if(osr.survey_result_question_id in (2,22), osr.survey_result_answer_id, NULL)) "NVTK",
                    MAX(if(osr.survey_result_question_id in (4), osr.survey_result_answer_id, NULL)) "NVBT"'))
                        ->where('osr.survey_result_section_id', $partViolation->section_id)
                        ->groupBy('osr.survey_result_section_id')
                        ->get();
                    $partViolation->csat_salesman_point = ($sectionResult[0]->NVKD == -1 || $sectionResult[0]->NVKD == '-1') ? 0 : $sectionResult[0]->NVKD;
                    $partViolation->csat_deployer_point = ($sectionResult[0]->NVTK == -1 || $sectionResult[0]->NVTK == '-1') ? 0 : $sectionResult[0]->NVTK;
                    $partViolation->csat_maintenance_staff_point = ($sectionResult[0]->NVBT == -1 || $sectionResult[0]->NVBT == '-1') ? 0 : $sectionResult[0]->NVBT;
                    $partViolation->save();
                }
            }
            return 'Cap nhap thanh cong';
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function insertFromText()
    {
        $arrayExcel = [];
        $pathLocation = 'D:\test\listcus.txt';
        $fpD = @fopen($pathLocation, "r");
        // Kiểm tra file mở thành công không
        if (!$fpD) {
            var_dump('Mở file list không thành công');
            die;
        } else {
            $arrayHot = [
                '1' => '',
                '2' => '',
                '3' => '',
                '4' => '',
                '5' => '',
                '6' => '',
                '7' => ''
            ];
            while (!feof($fpD)) {
                $tempString = fgets($fpD);
                if ($tempString !== false) {
                    $tempExp = preg_split('/[\t]/', $tempString);
                    $temp['location_id'] = str_replace('V', '', trim($tempExp['0']));

                    if (empty(trim($tempExp['1']))) {
                        if (trim($arrayHot[$temp['location_id']]) != '') {
                            $arrayHot[$temp['location_id']] .= ';';
                        }
                        $arrayHot[$temp['location_id']] .= trim($tempExp['2']);
                    } else {
                        if ($temp['location_id'] == 1) {
                            $temp['branch_name'] = str_replace('HN', 'HNI', trim($tempExp['1']));;
                        } else {
                            $temp['branch_name'] = trim($tempExp['1']);
                        }
                        $temp['mail'] = trim($tempExp['2']);

                        array_push($arrayExcel, $temp);
                    }
                }
            }
        }
        fclose($fpD);

        $arrayMap = [];
        $modelBranch = new SummaryBranches();
        $result = $modelBranch->getAllBranch();
        foreach ($result as $val) {
            $arrayMap[$val->zone_id . ':' . $val->branch_code] = $val->branch_id;
        }

        $arrayWant = [];
        foreach ($arrayExcel as $val) {
            $temp = [];
            $temp['email_list'] = $val['mail'];
            if (!isset($arrayMap[$val['location_id'] . ':' . $val['branch_name']])) {
                dump($val['location_id'] . ':' . $val['branch_name']);
                die;
            }
            $temp['summary_branches_id'] = $arrayMap[$val['location_id'] . ':' . $val['branch_name']];
            if (isset($arrayWant[$val['location_id'] . ':' . $val['branch_name']])) {
                $arrayWant[$val['location_id'] . ':' . $val['branch_name']]['email_list'] .= ';' . $temp['email_list'];
            } else {
                $temp['email_list'] = $arrayHot[$val['location_id']] . ';' . $temp['email_list'];
                $arrayWant[$val['location_id'] . ':' . $val['branch_name']] = $temp;
            }
        }

        $modelList = new ListEmailCUS();
        $result = $modelList->insert($arrayWant);
        dump($result);
        die;
    }

    public function sendMailQGD()
    {
        $input = [
            'ObjID' => '1017327472'
        ];

        $http = 'http://parapiora.fpt.vn/api/ISMaintaince/GetOwnerTypeByPopManage';
        $extra = new ExtraFunction();
        $resCall = $extra->sendRequest($http, $extra->getHeader(), 'POST', $input);
        dump($resCall);

        die;
        $help = new ApiHelper();
        $param['sectionId'] = 3794531;
//        $param['num_type'] = 2;
//        $param['code'] = 1102781962;
//        $param['shd'] = 'DLD024029';

        $result = $help->checkSendMailCounter($param);
        dump($result);
        if ($result['status']) {
            $need = $help->sendMailCounter($param, $result);
        }
        die;
    }

<<<<<<< .mine
    public function testUpdateInvalidCase()
    {
=======
    public function testUpdateInvalidCase() {
>>>>>>> .r93
        $errorBrachCode = [];
        $errorUserName = [];
        $result = DB::table('list_invalid_survey_case')
            ->select('*')
            ->get();
        foreach ($result as $key => $value) {
            $arrayError = explode(',', $value->type_error);
            $caseToDelete = ListInvalidSurveyCase::find($value->id);
            if (in_array(1, $arrayError)) {
                $infoAcc = array('ObjID' => 0,
                    'Contract' => $value->contract_number,
                    'IDSupportlist' => $value->section_code,
                    'Type' => $value->survey_id
//                 'Contract' => 'BND029485',
//                'IDSupportlist' => '1114583322',
//                'Type' => 9
                );
//            DB::beginTransaction();
                /*
                 * Lấy thông tin khách hàng
                 */
                try {
                    $url = 'RPDeployment/spCEM_ObjectGetByObjID';
                    $result = json_decode($this->postAPI($infoAcc, $url), true);
                    $responseAccountInfo = $result['data'];
                    $testData = ['Supporter', 'SubSupporter', 'LocationID', 'BranchCode'];
                    $validData = true;
                    foreach ($testData as $key => $value2) {
                        if (!isset($responseAccountInfo[0][$value2]))
                            $validData = false;
                    }
                    //Có đủ dữ liệu trả về
                    if ($validData) {
                        $surveySection = SurveySections::find($value->section_id);
                        $surveySection->section_supporter = isset($responseAccountInfo[0]['Supporter']) ? $responseAccountInfo[0]['Supporter'] : null;
                        $surveySection->section_subsupporter = isset($responseAccountInfo[0]['SubSupporter']) ? $responseAccountInfo[0]['SubSupporter'] : null;
                        $surveySection->section_location_id = isset($responseAccountInfo[0]['LocationID']) ? $responseAccountInfo[0]['LocationID'] : null;
                        $surveySection->section_branch_code = isset($responseAccountInfo[0]['BranchCode']) ? $responseAccountInfo[0]['BranchCode'] : null;
                        if ($surveySection->save()) {
                            DB::commit();
                        } else {
                            DB::rollback();
                            $caseToDelete->updated_date_on_survey = date('Y-m-d H:i:s');
                            $caseToDelete->save();
                        }
                    } else {
                        DB::rollback();
                        $caseToDelete->updated_date_on_survey = date('Y-m-d H:i:s');
                        $caseToDelete->save();
                    }
                } catch (Exception $ex) {
                    echo 'loi';
                    echo $ex->getMessage();
                    DB::rollback();
                }
            }
            if (in_array(2, $arrayError)) {
                array_push($errorBrachCode, $value->section_id);
            }
            if (in_array(3, $arrayError)) {
                array_push($errorUserName, $value->section_id);
            }
            $caseToDelete->delete();
        }
        if (!empty($errorBrachCode)) {
            Mail::send('emails.listInvalidCase', ['info' => $errorBrachCode, 'title' => 'Thông tin các case bị sai thông tin vùng miền, chi nhánh'], function ($message) {
                $message->from('rad.support@fpt.com.vn', 'Support');
                $message->to('huydp2@fpt.com.vn');
//                    $message->cc($cc);
                $message->subject('Thông tin các case bị sai thông tin vùng miền, chi nhánh');
            });
        }
        if (!empty($errorUserName)) {
            Mail::send('emails.listInvalidCase', ['info' => $errorUserName, 'title' => 'Thông tin các case bị thiếu thông tin người đăng nhập'], function ($message) {
                $message->from('rad.support@fpt.com.vn', 'Support');
                $message->to('huynl2@fpt.com.vn');
//                    $message->cc($cc);
                $message->subject('Thông tin các case bị thiếu thông tin người đăng nhập');
            });
        }
        echo 'Thanh cong';
    }

    private function postAPI($data, $url)
    {
        $str_data = json_encode($data);
        $uri = 'http://parapiora.fpt.vn/api/' . $url;
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($ch, CURLOPT_PROXY, "");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
//        if (FALSE === $result) {
//            throw new Exception(curl_error($ch), curl_errno($ch));
//            var_dump(curl_error($ch));
//            var_dump(curl_errno($ch));
//            die;
//            return curl_error($ch);
//        }
        // close the connection, release resources used
        curl_close($ch);
        return $result;

//          $resultCurlExt = Curl::to($uri)
//                ->withData($data)
//                ->returnResponseObject()
//                ->post();
//        if (isset($resultCurlExt->error))
//            return $resultCurlExt->error;
//        else
//            return $resultCurlExt->content;
    }

    public function getCsat12()
    {
        $nameFile = 'ChiTietBaoCaoXuLy';
//                $condition['survey_from']='2018-01-01 00:00:00';
//                  $condition['survey_to']='2018-01-31 23:59:59';
        $condition['sectionGeneralAction'] = 3;
        //Gán ngày nếu có
        if (isset($condition['survey_from'])) {
            $nameFile .= '_' . date('dmY', strtotime($condition['survey_from']));
        }

        if (isset($condition['survey_to'])) {
            $nameFile .= '_' . date('dmY', strtotime($condition['survey_to']));
        }
//                    var_dump($condition);die;
        $count = $infoSurvey = $this->CountGetCsatT1T3();
//                $count = $this->modelSurveySections->countListSurveyGeneral($condition);
//                var_dump($count);die;
        $currentPage = 0;
        $condition['recordPerPage'] = 3000; //ko cần phân trang
        $remain = $count % 3000;
        $numPage = ($count - $remain) / 3000;
        if ($remain != 0) {
            $numPage = $numPage + 1;
        }
        $listFileExel = [];
        for ($i = 0; $i < $numPage; $i++) {
            $nameExport = $nameFile;
            $nameExport .= strtotime(date('y-m-d h:i:s'));
            $infoSurvey = $this->getCsatT1T3($i, $condition);
//                      dump($infoSurvey);die;
//                    $infoSurvey = $this->modelSurveySections->searchListSurveyGeneral($condition, $i);
            $infoSurveyWithActionData = $this->attachActionDataToSurvey($infoSurvey, $condition);
            $PathExcel = Excel::create($nameExport, function ($excel) use ($infoSurveyWithActionData, $condition) {
                $excel->sheet('Sheet 1', function ($sheet) use ($infoSurveyWithActionData, $condition) {
                    $sheet->loadView('Csat.CsatServiceDetailExcel')->with('modelSurveySections', $infoSurveyWithActionData)
                        ->with('searchCondition', $condition);
                });
            })->store('xlsx', storage_path('app/public'), true);
            array_push($listFileExel, $PathExcel['file']);
        }
        return view("report/reportDownload", ['listFileExel' => $listFileExel])->render();
    }
<<<<<<< .mine


    public function getCsatT1T3($numberPage, $condition)
=======
            
    
    public function getCsatT1T3($numberPage,$condition)
>>>>>>> .r93
    {
        $query = '(select oss.section_sub_parent_desc "Vung",
      oss.section_location as "ChiNhanh", 
      oss.section_survey_id,
      oss.section_code,
      oss.section_record_channel, 
      oss.section_contract_num,
      oss.section_acc_sale,
      oss.section_account_inf,
      oss.section_account_list,
      oss.section_time_completed,
      oss.section_location_id,
      oss.section_branch_code,
      oss.section_sale_branch_code,
      oss.section_region,
      oss.section_action,
      oss.section_connected,
      oss.section_user_name,
      oss.section_supporter,
      oss.violation_status,
       oss.section_note,
      MAX(if(osr.survey_result_question_id in (10,12,20,41,46) and osr.survey_result_answer_id <> -1 , osr.survey_result_answer_id, "")) "CSAT_Internet",
      MAX(if(osr.survey_result_question_id in (10,12,20,41,46) and osr.survey_result_answer_id in (1,2), osr.survey_result_answer_extra_id, ""))  "Loai_loi_internet",
      MAX(if(osr.survey_result_question_id in (10,12,20,41,46) and osr.survey_result_answer_id in (1,2), osr.survey_result_action, ""))  "Xu_ly_internet",
      MAX(if(osr.survey_result_question_id in (11,13,21,42,47) and osr.survey_result_answer_id <> -1, osr.survey_result_answer_id, "")) "CSAT_truyen_hinh",
      MAX(if(osr.survey_result_question_id in (11,13,21,42,47) and osr.survey_result_answer_id in (1,2), osr.survey_result_answer_extra_id, ""))  "Loai_loi_TV",
      MAX(if(osr.survey_result_question_id in (11,13,21,42,47) and osr.survey_result_answer_id in (1,2), osr.survey_result_action, ""))  "Xu_ly_TV"   
		from `outbound_survey_sections` as `oss` inner join `outbound_survey_result` as `osr` on `oss`.`section_id` = `osr`.`survey_result_section_id`
		 where `oss`.`section_time_completed_int` >= ' . strtotime('2018-03-01 00:00:00') . ' and `oss`.`section_time_completed_int` <= ' . strtotime('2018-03-28 23:59:59') .
            ' and `osr`.`survey_result_question_id` in (10, 11, 12, 13, 20, 21, 41, 42, 46, 47) and oss.section_survey_id = 1
                     and osr.survey_result_answer_id in (1,2)
		 group by `oss`.`section_id`) as t';
        $result = DB::table(DB::raw($query))
            ->select(DB::raw("*"));
        $result = $result->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage'])->get();
//$result = $result->get();
//dump($condition['recordPerPage'],$numberPage);
        foreach ($result as $key => $value) {
            $result[$key] = (array)$value;
        }
        return $result;

    }
<<<<<<< .mine

    public function CountGetCsatT1T3()
=======
    
    public function CountGetCsatT1T3()
>>>>>>> .r93
    {
        $query = '(select oss.section_sub_parent_desc "Vung"
		from `outbound_survey_sections` as `oss` inner join `outbound_survey_result` as `osr` on `oss`.`section_id` = `osr`.`survey_result_section_id`
		 where `oss`.`section_time_completed_int` >= ' . strtotime('2018-03-01 00:00:00') . ' and `oss`.`section_time_completed_int` <= ' . strtotime('2018-03-28 23:59:59') .
            ' and `osr`.`survey_result_question_id` in (10, 11, 12, 13, 20, 21, 41, 42, 46, 47) and oss.section_survey_id = 1
                     and osr.survey_result_answer_id in (1,2)
		 group by `oss`.`section_id`) as t';
        $result = DB::table(DB::raw($query))
            ->select(DB::raw("*"));
        $result = $result->count();
        return $result;

    }
<<<<<<< .mine

    public function attachActionDataToSurvey($result, $condition)
    {
=======

    public function attachActionDataToSurvey($result, $condition) {
>>>>>>> .r93
        //Không có dữ liệu thì trả về luôn
        if (empty($result))
            return $result;
        //Chọn xử lý tạo checklist thường, indo, prechecklist
//        if ($condition['sectionGeneralAction'] == 3) {
//            DB::enableQueryLog();
        $preclResult = DB::table('prechecklist as pc')
            ->select(DB::raw("*")
            )
            ->where(function ($query) use ($result) {
                foreach ($result as $key => $value) {
                    $query->orWhere(function ($query) use ($value) {
                        $query->where('pc.section_contract_num', $value['section_contract_num']);
                        $query->where('pc.section_code', $value['section_code']);
                        $query->where('pc.section_survey_id', $value['section_survey_id']);
                    });
                }
            })
            ->get();
//                    $queryLog=DB::getQueryLog();
        //Chuyen sang mang key
        foreach ($preclResult as $key => $value) {
            $value = (array)$value;
            //Case dau tien
            if (!isset($keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']])) {
                $value['subkey'] = 0;
                $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']] = $value;
            } else {
//                $valueCheck=$preclResult[$value['section_contract_num'].$value['section_code'].$value['section_survey_id']];
//                if($valueCheck['subkey'])
                $subkey = $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'];
                $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'] = $subkey + 1;
//                $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id'] . 'plus' . ($subkey + 1)] = $value;
            }
        }
//            dump($preclResult);
//               dump($keyPreclResult);
//            die;
        $sectionSurveyPreCL = [];
//        Tong hop du lieu PreCl
        foreach ($result as $key1 => $survey) {
            $survey = (array)$survey;
            $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'];
            if (isset($keyPreclResult[$entry])) {
                $array_merge = array_merge($survey, $keyPreclResult[$entry]);
                array_push($sectionSurveyPreCL, $array_merge);
//                if($keyPreclResult[$entry]['subkey']
//                dump($keyPreclResult[$entry]['subkey']);die;
//                $numSubkey = $keyPreclResult[$entry]['subkey'];
//                for ($i = 1; $i <= $numSubkey; $i++) {
//                    $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'] . 'plus' . $i;
//                    $array_merge = array_merge($survey, $keyPreclResult[$entry]);
//                    array_push($sectionSurveyPreCL, $array_merge);
//                }
            } else {
                $preclArray = ['location_name' => '',
                    'first_status' => '',
                    'location_phone' => '',
                    'division_id' => '',
                    'description' => '',
                    'create_by' => '',
                    'update_date' => '',
                    'appointment_timer' => '',
                    'status' => '',
                    'sup_status_id' => '',
                    'created_at' => '',
                    'updated_at' => '',
                    'count_sup' => '',
                    'total_minute' => '',
                    'action_process' => '',
                    'id_prechecklist_isc' => '',
                    'sup_id_partner' => '',
                    'idChecklistIsc' => '',
                    'subkey' => 0
                ];
                $arrayEmptyPrecl = array_merge($survey, $preclArray);
                array_push($sectionSurveyPreCL, $arrayEmptyPrecl);
            }
        }
        //Gan them Checklist
        $SurveyPreclWithCl = [];
        $checklistEmpty = ['i_type' => '',
            's_create_by' => '',
            'i_lnit_status' => '',
            's_description' => '',
            'i_modem_type' => '',
            'supporter' => '',
            'sub_supporter' => '',
            'dept_id' => '',
            'request_from' => '',
            'owner_type' => '',
            'created_at' => '',
            'updated_at' => '',
            'final_status' => '',
            'final_status_id' => '',
            'total_minute' => '',
            'input_time' => '',
            'assign' => '',
            'store_time' => '',
            'error_position' => '',
            'error_description' => '',
            'reason_description' => '',
            'way_solving' => '',
            'checklist_type' => '',
            'repeat_checklist' => '',
            'finish_date' => '',
        ];
        $listChecklistID = [];
        foreach ($sectionSurveyPreCL as $key => $value) {
            if ($value['sup_id_partner'] != NULL && $value['sup_id_partner'] != '' && $value['sup_id_partner'] > 0) {
                array_push($listChecklistID, $value['sup_id_partner']);
            }
            $sectionSurveyPreCL[$key] = (array)$value;
        }
//                dump($sectionSurveyPreCL);
//                dump($listChecklistID);die;
//            DB::enableQueryLog();
//            dump($listChecklistID);
        if (!empty($listChecklistID)) {
            $checklistResult = DB::table('checklist as c')
                ->select('*')
                ->where(function ($query) use ($listChecklistID) {
                    foreach ($listChecklistID as $key => $value) {
                        $query->orWhere(function ($query) use ($value) {
                            $query->where('c.id_checklist_isc', $value);
                        });
                    }
                })
                ->get();
        } else
            $checklistResult = [];

        foreach ($checklistResult as $key => $value) {
            $checklistResultKey[$value->id_checklist_isc] = $value;
        }
//                    dump($checklistResultKey);die;

        foreach ($sectionSurveyPreCL as $key => $value) {
            if ($value['sup_id_partner'] != NULL && $value['sup_id_partner'] != '' && $value['sup_id_partner'] > 0) {
                if (isset($checklistResultKey[$value['sup_id_partner']])) {
                    $checklistResult = (array)$checklistResultKey[$value['sup_id_partner']];
                    $array_merge = array_merge($value, $checklistResult);
                    array_push($SurveyPreclWithCl, $array_merge);
                } else {
                    $array_merge = array_merge($value, $checklistEmpty);
                    array_push($SurveyPreclWithCl, $array_merge);
                }
            } else {
                $array_merge = array_merge($value, $checklistEmpty);
                array_push($SurveyPreclWithCl, $array_merge);
            }
        }


        $result = $SurveyPreclWithCl;
//            dump($result);die;
//        }
//        }

        return $result;
    }

    public function getFileConverse()
    {
        $arrayExcel = [];
        $pathLocation = 'D:\test\SaleCAM.txt';
        $fpD = @fopen($pathLocation, "r");
        // Kiểm tra file mở thành công không
        if (!$fpD) {
<<<<<<< .mine
            var_dump('Mở file DS không thành công');
            die;
        } else {
            while (!feof($fpD)) {
=======
            var_dump('Mở file DS CAM không thành công');die;
        }
        else{
            while(!feof($fpD))
            {
>>>>>>> .r93
                $tempString = fgets($fpD);
                if ($tempString !== false) {
                    $key = '';
                    $tempExp = preg_split('/[\t]/', $tempString);
                    $splitString = explode(' ', $tempExp[1]);
                    if (count($splitString) > 1) {
                        foreach ($splitString as $index => $value) {
//                          $arrayText=str_split($value,2);
//                          dump($arrayText);
//                          dump($arrayText[0]);
//                          dump($key);

                            $key .= trim(ucfirst(str_replace('…', '', str_replace("'", '', str_replace('.', '', str_replace(',', '', $value))))));
//                          dump($key);
                        }
//                        dump($key);
//                        die;
                    } else
                        $key = trim($splitString[0]);
//                    echo '"'.$key .'" => '.$tempExp[0].',</br>';
                    echo '"' . $key . '" => "' . trim($tempExp[1]) . '",</br>';
                }
            }
        }
        fclose($fpD);

        die;
        echo "<table>";
        foreach ($arrayExcel as $key => $val) {
            echo "<tr>";
            echo "<td>" . $val['name'] . "</td>";
            echo "<td>" . $val['SLLLTK'] . "</td>";
            echo "<td>" . $val['SLKLLTK'] . "</td>";
            echo "<td>" . $val['SLLLBT'] . "</td>";
            echo "<td>" . $val['SLKLLBT'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    private function updateMailSaleSendDatabase(){
        $arrayExcel = [];
        $pathLocation = 'D:\test\SaleCAM.txt';
        $fpD = @fopen($pathLocation, "r");
        // Kiểm tra file mở thành công không
        if (!$fpD) {
            var_dump('Mở file DS Sale CAM không thành công');die;
        }
        else{

            $temp = [
                'Email' => null,
                'AccountConfirm' => null,
                'AccountInside' => null,
            ];
            $Before = null;
            $CaptainMail = '';
            $CaptainAccount = '';
            $Employee = '';
            while(!feof($fpD))
            {
                $tempString = fgets($fpD);
                if($tempString !== false){
                    $tempExp = preg_split('/[\t]/', $tempString);

                    $Now = $tempExp[1];
                    $Email = isset($tempExp[2])?strtolower(trim($tempExp[2])):'';
                    $Acc = isset($tempExp[3])?strtolower(trim($tempExp[3])):'';

                    if($Before === null){
                        $Before = $Now;
                    }

                    if($Before == $Now){
                        if($Now == '1'){
                            $CaptainMail .= $Email.'; ';
                            $CaptainAccount .= $Acc.'; ';
                        }else{
                            if(!empty($Acc)){
                                $Employee .= $Acc.'; ';
                            }
                        }
                    }else{
                        if($Now == '1'){
                            $temp['Email'] = trim($CaptainMail);
                            $temp['AccountConfirm'] = trim($CaptainAccount);
                            $temp['AccountInside'] = trim($Employee);
                            $arrayExcel[] = $temp;

                            $temp = [
                                'Email' => null,
                                'AccountConfirm' => null,
                                'AccountInside' => null,
                            ];
                            $CaptainMail = $Email.'; ';
                            $CaptainAccount = $Acc.'; ';
                            $Employee = '';
                        }else{
                            if(!empty($Acc)){
                                $Employee .= $Acc.'; ';
                            }
                        }
                    }

                    $Before = $Now;
                }
            }
        }
        fclose($fpD);

        dump($arrayExcel);
        $ModelEmailSale = new ListEmailSale();
        $ModelEmailSale->insert($arrayExcel);
    }

    private function updateMailSirSendDatabase(){
        $arrayExcel = [];
        $pathLocation = 'D:\test\SirCAM.txt';
        $fpD = @fopen($pathLocation, "r");
        // Kiểm tra file mở thành công không
        if (!$fpD) {
            var_dump('Mở file DS Sir CAM không thành công');die;
        }
        else{

            $temp = [
                'Email' => null,
                'AccountConfirm' => null,
                'AccountInside' => null,
            ];
            $Before = null;
            $CaptainMail = '';
            $CaptainAccount = '';
            $Employee = '';
            while(!feof($fpD))
            {
                $tempString = fgets($fpD);
                if($tempString !== false){
                    $tempExp = preg_split('/[\t]/', $tempString);

                    $Now = $tempExp[0];
                    $Email = isset($tempExp[1])?strtolower(trim($tempExp[1])):'';
                    $Acc = isset($tempExp[2])?strtolower(trim($tempExp[2])):'';

                    if($Before === null){
                        $Before = $Now;
                    }

                    if($Before == $Now){
                        if($Now == '1'){
                            $CaptainMail .= $Email.'; ';
                            $CaptainAccount .= $Acc.'; ';
                        }else{
                            if(!empty($Acc)){
                                $Employee .= $Acc.'; ';
                            }
                        }
                    }else{
                        if($Now == '1'){
                            $temp['Email'] = trim($CaptainMail);
                            $temp['AccountConfirm'] = trim($CaptainAccount);
                            $temp['AccountInside'] = trim($Employee);
                            $arrayExcel[] = $temp;

                            $temp = [
                                'Email' => null,
                                'AccountConfirm' => null,
                                'AccountInside' => null,
                            ];
                            $CaptainMail = $Email.'; ';
                            $CaptainAccount = $Acc.'; ';
                            $Employee = '';
                        }else{
                            if(!empty($Acc)){
                                $Employee .= $Acc.'; ';
                            }
                        }
                    }

                    $Before = $Now;
                }
            }
        }
        fclose($fpD);

        dump($arrayExcel);
        $ModelEmail = new ListEmailSir();
        $ModelEmail->insert($arrayExcel);
    }
}
