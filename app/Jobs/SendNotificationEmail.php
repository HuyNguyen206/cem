<?php

namespace App\Jobs;

use App\Component\HelpProvider;
use App\Models\Api\ApiTele;
use App\Models\ListEmailCUS;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Api\ApiMobi;
use App\Models\Api\ApiSale;
use App\Models\PushNotification;
use App\Models\ListEmailQGD;
use Exception;
use App\Models\ListEmailSale;
use App\Models\ListEmailSir;

class SendNotificationEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $input;

    public function __construct($input) {
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $typeSend = ['sale', 'tech', 'tele', 'cl', 'qgd', 'cus'];
        //Xét tất cả các trường hợp gửi api cho ISC
        foreach($typeSend as $type){
            if(!empty($this->input[$type])){
                //Kiểm tra xem có gửi trùng lặp hay không
                $iSsend = $this->isSendAPI($this->input['paramMail']);
                if($iSsend){
                    $this->sendAPI($type);
                }
            }
        }
    }

    private function sendAPI($type){
        $sale = new ApiSale();
        $mobi = new ApiMobi();
        $tele = new ApiTele();
        $modelListEmailQGD = new ListEmailQGD();
        $modelListEmailCUS = new ListEmailCUS();
        $modelListEmailSale = new ListEmailSale();
        $modelListEmailSir = new ListEmailSir();
        $tempCUS = false;
        $model_notification = new PushNotification();
        $modelHelper = new HelpProvider();
        $date = date('Y-m-d H:i:s');
        if ($this->attempts() >= 2) {
            return;
        }
        //Gọi api của sale, net tùy trường hợp
        switch($type){
            case 'sale':
                $paramSale['saleMan'] = strtolower($this->input['paramMail']['saleMan']);
                $resSale = $modelListEmailSale->getListEmail($paramSale);
                if(empty($resSale)){
                    $resSale = [
                        'Email' => 'tuyn.huynh@opennet.com.kh',
                        'AccountConfirm' => 'tuyn.huynh'
                    ];
                }
                $res['error'] = false;
                $res['output'] = json_encode($resSale);
                $res['msg'] = 'Có list mail';

                break;
            case 'tech':
                $paramTech['team'] = strtolower($this->input['paramMail']['team']);
                $resTech = $modelListEmailSir->getListEmail($paramTech);
                if(empty($resTech)){
                    $resTech = [
                        'Email' => 'viet.tran@opennet.com.kh; ngan.nguyen@opennet.com.kh',
                        'AccountConfirm' => 'viet.tran; ngannq;'
                    ];
                }
                $res['error'] = false;
                $res['output'] = json_encode($resTech);
                $res['msg'] = 'Có list mail';
                break;
            case 'tele':
                $res = $tele->pushNotificationToISCGetEmailList($this->input[$type]);
                break;
            case 'cl':
                $res = $mobi->pushNotificationToISCGetEmailList($this->input[$type]);
                break;
            case 'qgd':
                $paramQGD['location_id'] = $this->input['paramMail']['location_id'];
                $paramQGD['branch_code'] = $this->input['paramMail']['branch_code'];
                $resQGD = $modelListEmailQGD->getListEmail($paramQGD);
                $res['error'] = false;
                $res['output'] = json_encode($resQGD);
                $res['msg'] = 'Có list mail';
                if(empty($resQGD)){
                    $res['error'] = true;
                    $res['msg'] = 'Không có list mail';
                }
                break;
            case 'cus':
                $paramCUS['location_id'] = $this->input['paramMail']['location_id'];
                $paramCUS['branch_code'] = $this->input['paramMail']['branch_code'];
                $resCUS = $modelListEmailCUS->getListEmail($paramCUS);
                $res['error'] = false;
                $res['output'] = json_encode($resCUS);
                $res['msg'] = 'Có list mail';
                if(empty($resCUS)){
                    $tempCUS = true;
                }
                break;
            default:
                return;
        }

        if($res['error']){
            $status = 0;
        }else{
            $status = 1;
        }

        $param['confirm_code'] = $this->input['paramMail']['confirm_code'];
        $param['api_status'] = $status;
        $param['api_created_at'] = $date;
        $param['api_last_sent_at'] = $date;
        $param['api_output'] = $res['output'];
        $param['api_message'] = $res['msg'];
        $param['api_send_count'] = 1;

        if($status){
            $temp = json_decode($param['api_output'],1);
            //Bổ sung thông tin đối với trường hợp send mail sale
            if(isset($temp['Email'])){
                $param['push_notification_send_to'] = $temp['Email'];
            }
            if(isset($temp['AccountConfirm'])){
                $param['push_notification_inside_confirm'] = $temp['AccountConfirm'];
            }
        }

        //Cập nhật lại push_notification
        $model_notification->updatePushNotificationOnSendNotification($param);

        switch($type){
            case 'sale':
                if(!$res['error']){
                    $type = 'Sale';
                    $tempRes = json_decode($res['output'],1);
                    $mail = explode(';', $tempRes['Email']);
                    foreach($mail as $key => $value){
                        if(empty($value)){
                            unset($mail[$key]);
                        }
                    }
                    $realCc = [
                        'tuyn.huynh@opennet.com.kh',
                        'pheak.hean@opennet.com.kh',
                        'sreypich.chat@opennet.com.kh',
                        'sreyda.lee@opennet.com.kh',
                        'thuy.nguyen@opennet.com.kh'
                    ];

                    try{
                        $modelHelper->sendMail($this->input, $mail, $type, $realCc);
                    }catch(Exception $ex){
                        $param['push_notification_note'] = $ex->getMessage();
                    }
                }
                break;
            case 'tech':
                if(!$res['error']){
                    $type = 'Tech';
                    $tempRes = json_decode($res['output'],1);
                    $mail = explode(';', $tempRes['Email']);
                    foreach($mail as $key => $value){
                        if(empty($value)){
                            unset($mail[$key]);
                        }
                    }
                    $realCc = [
                        'tuyn.huynh@opennet.com.kh',
                        'pheak.hean@opennet.com.kh',
                        'sreypich.chat@opennet.com.kh',
                        'sreyda.lee@opennet.com.kh',
                        'thuy.nguyen@opennet.com.kh'
                    ];
                    try{
                        $modelHelper->sendMail($this->input, $mail, $type, $realCc);
                    }catch(Exception $ex){
                        $param['push_notification_note'] = $ex->getMessage();
                    }
                }
                break;
            case 'tele':
                if(!$res['error']){
                    $type = 'Tele';
                    $tempRes = json_decode($res['output'],1);
                    $mail = $tempRes['msg']['data'][0]['EmailLeader'];
                    $realCc = [];
                    try{
                        $modelHelper->sendMail($this->input, $mail, $type, $realCc);
                    }catch(Exception $ex){
                        $param['push_notification_note'] = $ex->getMessage();
                    }
                }
                break;
            case 'cl':
                if(!$res['error']){
                    $type = 'CL';
                    $tempRes = json_decode($res['output'],1);
                    $mail = $tempRes['msg']['email'];
                    $cc = $tempRes['msg']['ccemail'];
                    $realCc = [];

                    $hasOnePoint = false;
                    if($this->input['paramMail']['location_id'] == 4){
                        foreach($this->input['paramMail']['results']['badCL'] as $val){
                            if($val['point'] == 1){
                                $hasOnePoint = true;
                            }
                        }
                    }

                    $temp = explode(';', $cc);
                    foreach($temp as $val){
                        $val = strtolower($val);
                        if(!empty($val)){
                            if($hasOnePoint){
                                array_push($realCc, $val);
                            }else{
                                if(!in_array($val, ['quangnn10@fpt.com.vn', 'binhlt6@fpt.com.vn', 'anhnt8@fpt.com.vn', 'hieupq@fpt.com.vn'])){
                                    array_push($realCc, $val);
                                }
                            }
                        }
                    }

                    try{
                        $modelHelper->sendMail($this->input, $mail, $type, $realCc);
                    }catch(Exception $ex){
                        $param['push_notification_note'] = $ex->getMessage();
                    }
                }
                break;
            case 'qgd':
                if(!$res['error']){
                    $type = 'QGD';
                    $tempRes = json_decode($res['output'],1);
                    $mail = explode(';', $tempRes['email_list_to']);
                    $cc = explode(';', $tempRes['email_list_cc']);
                    $realCc = [];

                    $hasFivePoint = false;
                    foreach($this->input['paramMail']['results']['badQGD'] as $val){
                        if($val['point'] == 5){
                            $hasFivePoint = true;
                        }
                    }

                    foreach($cc as $val){
                        $val = trim(strtolower($val));
                        if(!empty($val)){
                            if(!$hasFivePoint){
                                array_push($realCc, $val);
                            }else{
                                if(!in_array($val, ['tuntt4@fpt.com.vn', ' thinhtp2@fpt.com.vn'])){
                                    array_push($realCc, $val);
                                }
                            }
                        }
                    }

                    try{
                        $modelHelper->sendMail($this->input, $mail, $type, $realCc);
                    }catch(Exception $ex){
                        $param['push_notification_note'] = $ex->getMessage();
                    }
                }
                break;
            case 'cus':
                if(!$res['error']){
                    $type = 'CUS';
                    $tempRes = json_decode($res['output'],1);
                    if($tempCUS){
                        $mail = [
                            'anhdv4@fpt.com.vn',
                            'hantp@fpt.com.vn',
                            'toannm@fpt.com.vn',
                            'dungntp15@fpt.com.vn',
                        ];
                        $realCc = [
                        ];
                    }else{
                        $mail = explode(';', $tempRes['email_list']);
                        $realCc = [
                            'anhdv4@fpt.com.vn',
                            'hantp@fpt.com.vn',
                            'toannm@fpt.com.vn',
                            'dungntp15@fpt.com.vn',
                        ];
                    }
                    try{
                        $modelHelper->sendMail($this->input, $mail, $type, $realCc);
                    }catch(Exception $ex){
                        $param['push_notification_note'] = $ex->getMessage();
                    }
                }
                break;
            default:
                return;
        }

        //Cập nhật lại push_notification
        $model_notification->updatePushNotificationOnSendNotification($param);
    }

    private function isSendAPI($param){
        $model_notification = new PushNotification();
        $out = $model_notification->getPushNotificationToCheckDuplicate($param);
        if(count($out) < 1){
            $send = false;
        }else{
            $send = true;
            if($out->confirm_code != $param['confirm_code']){
                $send = false;
            }
        }
        return $send;
    }
}
