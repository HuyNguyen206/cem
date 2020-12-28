<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushNotification;
use Illuminate\Support\Facades\Auth;
use App\Component\HelpProvider;

class Notification extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function confirmView(Request $request, PushNotification $model_push){
		$input = $request->all();

		if(isset($input['code'])){
			$resPush = $model_push->getPushNotificationOnConfirmCode($input['code']);
			if(empty($resPush)){
					return view('notification/error', [
					'warning' => 'Confirm code not found!'
				]);
			}
			
			$user_name = strtolower(Auth::user()->name);
			$confirm = false;

            $resCheck = stripos($resPush->push_notification_inside_confirm, $user_name);
            if($resCheck !== false && empty($resPush->confirm_user)){
                $confirm = true;
            }

			$template = json_decode($resPush->push_notification_param, 1);
            switch($resPush->api_type){
                case 'Tech':
                case 'Tele':
                case 'Sale':
                    $mail = view('emails.sendNotification', ['param' => $template]);
                    break;
                case 'CL':
                    $mail = view('emails.sendNotificationCheckList', ['param' => $template]);
                    break;
                default:
                    return view('notification/error', [
                        'warning' => 'Come back later!!'
                    ]);
//                    return 'Vui lòng quay lại sau. Hiện tại chưa xây dựng template cho điểm tiếp xúc này!!';
            }

			$pos = strpos($mail, 'Manager ');
			if($pos){
				$temp = str_split($mail, $pos);
				$mail = $temp[0];
			}
		}
		else{
			return view('notification/error', [
				'warning' => 'Confirm code not found!'
			]);
		}
		return view('notification/confirm',[
			'mail' => $mail,
			'code' => $input['code'],
			'queue' => $resPush,
			'confirm' => $confirm,
		]);
	}
	
	public function confirm(Request $request){
		$input = $request->all();
		if(isset($input['code'])){
			try {
				$model_push = new PushNotification();
				$resPush = $model_push->getPushNotificationOnConfirmCode($input['code']);
				
				if(!empty($resPush)){
					$param['confirm_code'] = $input['code'];
					$param['confirm_note'] = NULL;
					$param['confirmed_at'] = date('Y-m-d H:i:s');
					
					$user = Auth::user();
					$user_name = $user->name;
					
					$param['confirm_user'] = $user_name;
					$param['api_is_reSend'] = 0;
					
					//Cập nhật thông tin push_notification đã nhận được
					$resUp = $model_push->updatePushNotificationOnConfirmNotification($param);
					if($resUp){
						$request->session()->flash('success', 'Confirm Successfully!');
						return redirect(url('confirm-notification?code='.$input['code']));
					}else{
						$request->session()->flash('fail', 'Could not confirm!');
						return redirect(url('confirm-notification?code='.$input['code']));
					}
				}else{
					return view('notification/error', [
						'warning' => 'Confirm code not found!'
					]);
				}
			} catch (Exception $e) {
				return view('notification/error', [
					'warning' => 'Confirm code not found!'
				]);
			}
		}else{
			return view('notification/error', [
				'warning' => 'Confirm code not found!'
			]);
		}
	}
}
