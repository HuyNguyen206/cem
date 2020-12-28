<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SummaryTransaction;
use DB;

class transactionGetCount extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:getCount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get count transaction';

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
    public function handle() {
       
          $urlMap = [1 => 'http://manapi.fpt.vn/api/SendMail/SendMailTransaction/Transaction',
            2 => 'http://manapi.fpt.vn/api/SendMail/SendMailTransaction/Success'];
        $typeInfo = [1, 2, 3];
        $typeInside = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $typeStatus = [1 => 'waiting', 2 => 'confirm', 3 => 'done', 4 => 'failed', 5 => 'error'];
        $dayToGet = date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day'));
        $result = DB::table('summary_time')
                ->select('*')
                ->where('date', $dayToGet)
                ->get();
        //Chưa có ngày hôm qua trong summary_time
        $timeId = !empty($result) ? $result[0]->id : null;
        //Loop qua mỗi ngày
        foreach ($result as $key => $value) {
            //Loop qua mỗi loại thông tin
            foreach ($typeInfo as $key2 => $value2) {
                //Loop qua mỗi loại khảo sát bên inside
                foreach ($typeInside as $key3 => $value3) {

                    switch ($value2) {
                        case 1:
                        case 2: {
                                //Api chi phuong ko co loai so 9 nen bo qua
                                if ($value3 == 9)
                                    continue;
                                $typeSurvey = in_array($value3, [2, 3, 4, 5, 6, 7, 8]) ? 4 : (in_array($value3, [1]) ? 7 : 99);
                                $url12 = $summaryTransaction = new SummaryTransaction();
                                $input = ['Type' => $value3, 'StartDate' => $value->date, 'EndDate' => date('Y-m-d', strtotime($value->date . ' +1 day'))];
                                $result1 = json_decode($this->postAPITransaction($urlMap[$value2], $input));
                                $summaryTransaction->time_id = $timeId;
                                $summaryTransaction->date = $value->date;
                                $summaryTransaction->type_info = $value2;
                                $summaryTransaction->type_transaction = $value3;
                                $summaryTransaction->type_survey = $typeSurvey;
                                $summaryTransaction->quantity = $result1->Result;
                                $summaryTransaction->error_code = $result1->ErrorCode;
                                $summaryTransaction->message = $result1->Error;
                                $summaryTransaction->save();
                                break;
                            }
                        case 3: {

//                                $url3 .= http_build_query($input);
//      dump($this->getAPI($url3));die;
//                                $result3 = json_decode($this->getAPI($url3));
                                $typeSurvey = in_array($value3, [1, 2, 3, 6, 7, 8, 9]) ? 4 : (in_array($value3, [4, 5]) ? 7 : 99);
                                foreach ($typeStatus as $key4 => $value4) {
                                    $url3 = 'https://fpt.vn/khaosat/api/survey/getCountSurvey?';
                                    $input = ['securityCode' => md5(date('Y-m-d') . 'ISC+R@D'), 'dateCreated' => $value->date, 'transactionType' => $value3, 'status' => $value4];
                                    $url3 .= http_build_query($input);
                                    $result3 = json_decode($this->getAPI($url3));
                                    $summaryTransaction = new SummaryTransaction();
                                    $summaryTransaction->time_id = $timeId;
                                    $summaryTransaction->date = $value->date;
                                    $summaryTransaction->type_info = $value2;
                                    $summaryTransaction->type_transaction = $value3;
                                    $summaryTransaction->type_survey = $typeSurvey;
                                    $summaryTransaction->status = $value4;
                                    $summaryTransaction->status_id = $key4;
                                    $summaryTransaction->quantity = $result3->data;
                                    $summaryTransaction->error_code = $result3->code;
                                    $summaryTransaction->message = $result3->message;
                                    $summaryTransaction->save();
                                }
                                break;
                            }
//                        }
                    }
                }
            }
        }
        echo 'Thanh Cong';
    }

  public function getAPI($uri, $params = '', $method = 'GET') {
$dataString = json_encode($params);
        $ch = curl_init();
        if (strtoupper($method) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        } else if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        } else if ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        }
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
//          curl_setopt($ch, CURLOPT_PROXY, "proxy.hcm.fpt.vn:80");
        if (curl_errno($ch)) {
            var_dump('track loi');
            var_dump(curl_error($ch));
            die;
        }
        $result = curl_exec($ch);
        if (FALSE === $result) {
//            throw new Exception(curl_error($ch), curl_errno($ch));
            var_dump(curl_error($ch));
            var_dump(curl_errno($ch));
            die;
        }
        return $result;
    }

    private function postAPITransaction($uri, $data) {
        $str_data = json_encode($data);
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


}
