<?php

namespace App\Component;

use App\Models\Role;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use App\Models\LogMG;
use App\Models\DeviceMG;
use Illuminate\Support\Facades\Session;
use App\Models\OutboundQuestions;
use Exception;

Class ExtraFunction {

    public static function customSubString($string, $lenght) {
        if (strlen($string) >= $lenght) {
            return mb_substr($string, 0, $lenght - 3, "utf-8") . '...';
        } else {
            return $string;
        }
    }

    public static function checkHaveAuthenAction($controllerName, $action) {
        $permissionName = strtolower($controllerName) . '-' . strtolower($action);
        $allPermission = Session::get('allPermission');
        $canAction = array_search($permissionName, array_column($allPermission['permission'], 'name'));
        if ($canAction !== false) {
            $canAction = true;
        }
        return $canAction;
    }

    public static function checkHaveAuthenRole($roleName) {
        $userRole = Session::get('userRole');
        if ($userRole['display_name'] == $roleName) {
            return true;
        }
        return false;
    }

    public static function checkCanAction($level) {
        $userRole = Session::get('userRole');
        if ($userRole['level'] >= $level && $userRole['level'] != '1') {
            return false;
        }
        return true;
    }

    public function getHeader() {
        return array(
            'Content-Type: application/json'
        );
    }

    public function response($status, $error, $msg) {
        return [
            'status' => $status,
            'error' => $error,
            'msg' => $msg,
        ];
    }

    public function sendRequest($uri, $headers = null, $method = 'GET', $params = null, $proxy = null) {
        $ch = curl_init();
        if (strtoupper($method) == 'POST') {
            if (!empty($params)) {
                $params = json_encode($params);
            } else {
                return $this->response('fail', true, 'Missing params');
            }
        }

        if (strtoupper($method) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if(!empty($proxy)){
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }


        $result = curl_exec($ch);
        if (!empty(curl_error($ch))) {
            return $this->response('fail', true, curl_error($ch));
        }

        $res = curl_getinfo($ch);
        if ($res['http_code'] != 200) {
            return $this->response('fail', true, 'Something happen');
        }
        return $this->response('success', false, json_decode($result, 1));
    }

    public function sortOnField(&$objects, $on, $order = 'ASC') {
        $comparer = ($order === 'DESC') ? "return -strcmp(\$a->{$on},\$b->{$on});" : "return strcmp(\$a->{$on},\$b->{$on});";
        usort($objects, create_function('$a,$b', $comparer));
    }

    public function getUserGrantedDetail() {
        $user = Auth::user();     
        $userLocation = !empty($user->user_zone) ? json_decode($user->user_zone, 1) : '';
        $userBranches = !empty($user->user_brand) ? json_decode($user->user_brand, 1) : [];
        $userBranchesSale = [];
        if (in_array('4', $userBranches)){
            array_push($userBranchesSale, '4');
        }
        if (in_array('8', $userBranches)){
            array_push($userBranchesSale, '8');
        }
        if (empty($userBranchesSale)){
            $userBranchesSale = '';
        }
        $userBranchID = !empty($user->user_brand_plus) ? json_decode($user->user_brand_plus, 1) : '';

        //lấy branchCode dựa vào id của chi nhánh
        $branchID = !empty($userBranchID) ? implode(',', $userBranchID) : '';
        $modelLocation = new Location();
        $branchCode = $modelLocation->getLocationByBranchID($branchID);
        $branchCodeSaleMan = $modelLocation->getBranchCodeSaleMan();
        $temp = [];
        $branchLocationCode = [];
        if (!empty($branchCode)) {
            foreach ($branchCode as $b) {
                $temp[] = $b->branchcode;
                $branchLocationCode[] = $b->location_id.'_'.$b->branchcode;
            }
        }
        foreach($userBranches as $location){
            $branchLocationCode[] = $location.'_0';
        }
        $tempBrandCodeSaleMan = [];
        if (!empty($branchCodeSaleMan)) {
            foreach ($branchCodeSaleMan as $b) {
                $tempBrandCodeSaleMan[] = $b;
            }
        }
        $userGranted['region'] = $userLocation;
        $userGranted['location'] = $userBranches;
        $userGranted['locationSales'] = $userBranchesSale;
        $userGranted['branchcode'] = $temp;
        $userGranted['brandcodeSaleMan'] = $tempBrandCodeSaleMan;
        $userGranted['branchID'] = $userBranchID;
        $userGranted['branchLocationCode'] = $branchLocationCode;
        return $userGranted;
    }
    
     public function getUserGranted(){
        $user = Auth::user();
        $userLocation = !empty($user->user_zone) ?json_decode($user->user_zone, 1) :'';
        $userBranches = !empty($user->user_brand) ?json_decode($user->user_brand, 1) :'';
        $userBranchID = !empty($user->user_brand_plus) ?json_decode($user->user_brand_plus, 1) :'';
        //lấy branchcode dựa vào id của chi nhánh
        $branchID = !empty($userBranchID) ?implode(',', $userBranchID) :'';
        $modelLocation = new Location();
        $branchcode = $modelLocation->getLocationByBranchID($branchID);
        $temp = [];
        if(!empty($branchcode)){
            foreach ($branchcode as $b){
                $temp[] = $b->branchcode;
            }
            //bổ sung vào brancode 0 (mặc định)
            array_push($temp, 0);
        }
        $userGranted['region'] = $userLocation;
        $userGranted['location'] = $userBranches;
        $userGranted['branchcode'] = $temp;
        $userGranted['branchID'] = $userBranchID;
        
        return $userGranted;
    }

    public function manualLog($request) {
        try {
            $user = Auth::check();
            $server = $request->server();
            if ($user === false) {
                $userName = $server['REMOTE_ADDR'];
            } else {
                $userName = Auth::user()->name;
            }

            $currentAction = app()->router->getCurrentRoute()->getActionName();
            list($controller, $action) = explode('@', $currentAction);
            $controllerName = preg_replace('/.*\\\/', '', $controller);

            $modelLog = new LogMG();
            $modelLog->log_user = $userName;
            $modelLog->log_method = $server['REQUEST_METHOD'];
            $modelLog->log_param = $request->all();
            $modelLog->log_created_at = date('Y-m-d H:i:s');
            $modelLog->log_request = $server;
            $modelLog->log_action = strtolower($action);
            $modelLog->log_controller = strtolower($controllerName);
			$modelLog->save();
        } catch (Exeption $e) {
            
        }
    }

    // Kiểm tra ip đang login
    public function checkDevice($request) {
        $user = Auth::check();
        $device = new DeviceMG();
        $server = $request->server();

        if ($user === false) {
            return false;
        }

        $paramDevice['deviceUser'] = Auth::user()->name;
        $paramDevice['deviceIp'] = $server['REMOTE_ADDR'];
        $get = $device->getDeviceByParam($paramDevice);
        if (empty($get)) {
            return false;
        }
        return true;
    }

    public function registerDevice($name, $ip) {
        $paramDevice['deviceUser'] = $name;
        $device = new DeviceMG();
        $get = $device->getDeviceByParam($paramDevice);
        $paramDevice['deviceIp'] = $ip;
        if (empty($get)) {
            $suc = $device->insertDevice($paramDevice);
        } else {
            $paramDevice['id'] = $get->id;
            $suc = $device->updateDevice($paramDevice);
        }
        return $suc;
    }

    public function array_unique_by_key(&$array, $key) {
        $tmp = [];
        $result = [];
        foreach ($array as $value) {
            if (!in_array($value->$key, $tmp)) {
                array_push($tmp, $value->$key);
                array_push($result, $value);
            }
        }
        $array = $result;
        return $array;
    }

    public function reRoundFloatNum($numRounded, $numRound) {
        $foundDot = strpos($numRounded, '.');
        if ($foundDot !== false) {
            $multi = 1;
            for ($i = 0; $i < $numRound; $i++) {
                $multi *= 10;
            }

            $numRounded = (int) ($numRounded * $multi * 10);
            $divided = $numRounded % 10;
            switch ($divided) {
                case 0:
                case 1:
                case 2:
                case 3:
                case 4:
                    $numRounded = (int) ($numRounded / 10) / $multi;
                    break;
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                    $numRounded = ((int) ($numRounded / 10) + 1) / $multi;
                    break;
            }
        }
        return $numRounded;
    }

    public function getFormatQuestionAliasID(){
        $outQuestionModel = new OutboundQuestions();
        $allQuestions = $outQuestionModel->getAllQuestion();
        $questionNeed = [];
        foreach($allQuestions as $question){
            if(isset($questionNeed[$question->question_alias])){
                array_push($questionNeed[$question->question_alias], $question->question_id);
            }else{
                $questionNeed[$question->question_alias] = [$question->question_id];
            }
        }
        return $questionNeed;
    }

    public function getFormatQuestionSurveyID(){
        $outQuestionModel = new OutboundQuestions();
        $allQuestions = $outQuestionModel->getAllQuestion();
        $questionNeed = [];
        foreach($allQuestions as $question){
            if(isset($questionNeed[$question->question_survey_id])){
                array_push($questionNeed[$question->question_survey_id], $question->question_id);
            }else{
                $questionNeed[$question->question_survey_id] = [$question->question_id];
            }
        }
        return $questionNeed;
    }

    public function getFormatQuestionByParam($param){
        $outQuestionModel = new OutboundQuestions();
        $allQuestions = $outQuestionModel->getQuestionByParam($param);
        $questionNeed = [];
        foreach($allQuestions as $question){
            if(isset($questionNeed[$question->question_survey_id])){
                array_push($questionNeed[$question->question_survey_id], $question->question_id);
            }else{
                $questionNeed[$question->question_survey_id] = [$question->question_id];
            }
        }
        return $questionNeed;
    }

    public function setColumn($char,$step, $sheet, $rowIndex)
    {
        $d=$char;
        for($i=1;$i<=$step;$i++)
        {
            if($rowIndex != 30)
            {
                $sheet->cell($d . ($rowIndex), function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            }

            $d= ++$char;
        }
        return $d;
    }

    public function setColumnTitleHeaderTable($char,$step, $sheet, $rowIndex)
    {
        $d=$char;
        for($i=1;$i<=$step;$i++)
        {
            if($rowIndex != 30)
            {
                $sheet->cell($d . ($rowIndex), function($cell) {
                    $this->setTitleHeaderTable($cell);
                });
            }

            $d= ++$char;
        }
        return $d;
    }

    public function setColumnByFormat($char,$step, $sheet, $rowIndex, $format)
    {
        $positionFormat = explode('-', $format);
        $d=$char;
        for($i=1;$i<=$step;$i++)
        {
            if($rowIndex != 30)
            {
                $sheet->cell($d . ($rowIndex), function($cell) use ($positionFormat) {
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setBorder($positionFormat[0], $positionFormat[1], $positionFormat[2], $positionFormat[3]);
                });
            }

            $d= ++$char;
        }
        return $d;
    }

    public function setTitleHeaderTable($cell) {
        $cell->setAlignment('center');
        $cell->setValignment('center');
        $cell->setBackground('#8DB4E2');
        $cell->setBorder('thin', 'thin', 'thin', 'thin');
        $cell->setFontWeight('bold');
    }
}
