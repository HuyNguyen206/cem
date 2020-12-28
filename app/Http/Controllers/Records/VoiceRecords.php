<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SurveySections;
use App\Component\HelpProvider;
use Exception;
use App\Models\VoiceRecord;
use Illuminate\Support\Facades\Response;

class VoiceRecords extends Controller {
    public function reportForMinh(Request $request){
        $modelRecord = new VoiceRecord();
        $modelSection = new SurveySections();
        $condition['survey_from'] = 'abc';
        $condition['survey_to'] = 'abc';
        $condition['survey_from_int'] = strtotime('2017-10-10 00:00:00');
        $condition['survey_to_int'] = strtotime('2017-10-10 09:59:59');
        $condition['section_connected'] = [4];
        $condition['arraySurveyID'] = [1,2,6];
        $res = $modelSection->searchListSurvey($condition, 0);
        $arrayWant = [];
        echo "<table>";
        foreach($res as $section){
            $phone = $section->section_contact_phone;
            if(!empty($phone)){
                //Tăng khoảng thời gian tìm kiếm lên trước 30' và sau 30' so với thời điểm bắt đầu khảo sát
                $all_records = [];
                $date = date_create($section->section_time_completed);
                $date->modify("-30 minutes");
                $input['time_from'] = date_format($date, 'Y-m-d H:i:s');
                $date->modify("+60 minutes");
                $input['time_to'] = date_format($date, 'Y-m-d H:i:s');

                $templateUrlSG = 'http://118.69.241.36/media/%s/AUDIO/%s.mp3';
                $templateUrlHN = 'http://118.70.0.62/media/%s/AUDIO/%s.mp3';
                //Tìm các cuộc ghi âm với số điện thoại được tìm thấy
                $input['phone'] = $phone;
                //Lấy toàn bộ các cuộc ghi âm
                $resVoice = $modelRecord->getAllRecordOnInputInServerVoiceSG($input);
                if(!empty($resVoice)){
                    foreach($resVoice as $record){
                        //Tạo link đến file ghi âm
                        $date = date('Y-m-d/H/i',strtotime($record->calldate));
                        $temp['date'] = date('d-m-Y H:i:s',strtotime($record->calldate));
                        $temp['url'] = sprintf($templateUrlSG, $date, trim($record->fbasename));
                        $temp['phone'] = $input['phone'];
                        //Tập trung các cuộc ghi âm của nhiều số điện thoại vào một nơi
                        array_push($all_records, $temp);
                    }
                }else{
                    $resVoice = $modelRecord->getAllRecordOnInputInServerVoiceHN($input);
                    if(!empty($resVoice)){
                        foreach($resVoice as $record){
                            //Tạo link đến file ghi âm
                            $date = date('Y-m-d/H/i',strtotime($record->calldate));
                            $temp['date'] = date('d-m-Y H:i:s',strtotime($record->calldate));
                            $temp['url'] = sprintf($templateUrlHN, $date, trim($record->fbasename));
                            $temp['phone'] = $input['phone'];
                            //Tập trung các cuộc ghi âm của nhiều số điện thoại vào một nơi
                            array_push($all_records, $temp);
                        }
                    }
                }

                //Đếm số lượng file ghi âm
                $count = count($all_records);
                if($count == 0){
//                    dump('SectionId='.$section->section_id.'-SectionPhone='.$section->section_contact_phone.'-SectionTime='.$section->section_time_start.'-SectionRecord=null');
//                    $result = ['state' => 'fail', 'error' => 'Không tìm thấy cuộc ghi âm nào'];
//                    echo 'SectionId='.$section->section_id.'-SectionPhone='.$section->section_contact_phone.'-SectionTime='.$section->section_time_start.'-SectionRecord=null';
//                    $result = [
//                        'SectionId' => $section->section_id,
//                        'SectionPhone' => $section->section_contact_phone,
//                        'SectionTime' => $section->section_time_start,
//                        'SectionRecord' => null,
//                    ];

                    echo "<tr><td>".$section->section_id."</td><td>".$section->section_contact_phone."</td><td>".$section->section_time_completed."</td><td></td></tr>";
//                    array_push($arrayWant, $result);
                }else{
                    foreach($all_records as $key => $val){
//                        $file = $val['url'];
//                        $ch = curl_init($file);
//                        curl_setopt($ch, CURLOPT_NOBODY, true);
//                        curl_exec($ch);
//                        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//
//                        if($code != 200){
//                            $all_records[$key]['url'] = str_replace('mp3','wav', $val['url']);
//                        }
//                        curl_close($ch);
//                        dump('SectionId='.$section->section_id.'-SectionPhone='.$section->section_contact_phone.'-SectionTime='.$section->section_time_start.'-SectionRecord='.$all_records[$key]['url']);
//                        echo 'SectionId='.$section->section_id.'-SectionPhone='.$section->section_contact_phone.'-SectionTime='.$section->section_time_start.'-SectionRecord='.$all_records[$key]['url'];
//                        $result = [
//                            'SectionId' => $section->section_id,
//                            'SectionPhone' => $section->section_contact_phone,
//                            'SectionTime' => $section->section_time_start,
//                            'SectionRecord' => $all_records[$key]['url'],
//                        ];
                        echo "<tr><td>".$section->section_id."</td><td>".$section->section_contact_phone."</td><td>".$section->section_time_completed."</td><td>".$all_records[$key]['url']."</td></tr>";
//                        array_push($arrayWant, $result);
                    }

                }
            }else{
            }
        }

        echo "</table>";
        die;
//        return view('test/index', [
//            'records' => $arrayWant
//        ]);
    }

	public function getVoiceRecords(Request $request,$id) {
		$help = new HelpProvider();
		$modelRecord = new VoiceRecord();
		$input = $request->all();
		
		if(!isset($input['time_from'])){
			$input['time_from'] = date('Y-m-d 00:00:00');
		}
		if(!isset($input['time_to'])){
			$input['time_to'] = date('Y-m-d 23:59:59');
		}

		$resValid = $help->validateDateStartEnd($input['time_from'], $input['time_to']);
		if(!$resValid){
			$input['time_from'] = date('Y-m-d 00:00:00');
			$input['time_to'] = date('Y-m-d 23:59:59');
		}else{
			$input['time_from'] = date_format($input['time_from'], 'Y-m-d H:i:s');
			$input['time_to'] = date_format($input['time_to'], 'Y-m-d H:i:s');
		}

		$input['phone'] = $id;
		$resVoice = $modelRecord->getAllRecordOnInputInServerVoice($input);
		
		return view('records/voiceRecords', [
			'voice' => $resVoice,
			'input' => $input
		]);
	}

	public function searchVoiceRecords(Request $request) {
		$help = new HelpProvider();
		$modelRecord = new VoiceRecord();
		$input = $request->all();
		
		$resValid = $help->validateDateStartEnd($input['time_from'], $input['time_to']);
		if(!$resValid){
			$input['time_from'] = date('Y-m-d 00:00:00');
			$input['time_to'] = date('Y-m-d 23:59:59');
		}else{
			$input['time_from'] = date_format($input['time_from'], 'Y-m-d H:i:s');
			$input['time_to'] = date_format($input['time_to'], 'Y-m-d H:i:s');
		}
		
		$resVoice = $modelRecord->getAllRecordOnInputInServerVoice($input);
		
		if(!empty($input['phone'])){
			if(count($resVoice) >= 10){
				$request->session()->flash('alert', 'Bạn cần giới hạn thời gian ngắn hơn để kết quả được chính xác hơn');
				return redirect(url(main_prefix.'/get-voice-records/'.trim($input['phone']).'/?time_from='.$input['time_from'].'&time_to='.$input['time_to']));
			}
		}
		
		return view('records/voiceRecords', [
			'voice' => $resVoice,
			'input' => $input
		]);
	}

	//Hàm cũ của anh Huy Đinh
//	public function getVoiceRecordsAjax(Request $request) {
//		$modelRecord = new VoiceRecord();
//		$input = $request->all();
//
//		try{
//			//Kiểm tra xem có tồn tại survey section id hay không
//			$section = SurveySections::find($input['id']);
//			if(empty($section)){
//				return Response::json(array('state' => 'fail', 'error' => 'Không tìm thấy Mã khảo sát'));
//			}
//
//			//Kiểm tra xem đã có người nghe qua record này hay không
//			$record = VoiceRecord::where([
//				'voice_survey_sections_id' => $input['id'],
//				'voice_section_time_start' => $section->section_time_completed
//			])->first();
//			if(!empty($record)){
//				$result = json_decode($record->voice_records);
//				return Response::json($result);
//			}
//
//			$phone = $section->section_contact_phone;
//			if(!empty($phone)){
//				//Tăng khoảng thời gian tìm kiếm lên trước 30' và sau 30' so với thời điểm bắt đầu khảo sát
//				$all_records = [];
//				$date = date_create($section->section_time_completed);
//				$date->modify("-30 minutes");
//				$input['time_from'] = date_format($date, 'Y-m-d H:i:s');
//				$date->modify("+60 minutes");
//				$input['time_to'] = date_format($date, 'Y-m-d H:i:s');
//
//				$templateUrlSG = 'http://118.69.241.36/media/%s/AUDIO/%s.mp3';
//				$templateUrlHN = 'http://118.70.0.62/media/%s/AUDIO/%s.mp3';
//				//Tìm các cuộc ghi âm với số điện thoại được tìm thấy
//				$input['phone'] = substr($phone,1);
//				//Lấy toàn bộ các cuộc ghi âm
//				$resVoice = $modelRecord->getAllRecordOnInputInServerVoiceSG($input);
//				if(!empty($resVoice)){
//					foreach($resVoice as $record){
//						//Tạo link đến file ghi âm
//						$date = date('Y-m-d/H/i',strtotime($record->calldate));
//						$temp['date'] = date('d-m-Y H:i:s',strtotime($record->calldate));
//						$temp['url'] = sprintf($templateUrlSG, $date, trim($record->fbasename));
//						$temp['phone'] = $phone;
//						//Tập trung các cuộc ghi âm của nhiều số điện thoại vào một nơi
//						array_push($all_records, $temp);
//					}
//				}else{
//					$resVoice = $modelRecord->getAllRecordOnInputInServerVoiceHN($input);
//					if(!empty($resVoice)){
//						foreach($resVoice as $record){
//							//Tạo link đến file ghi âm
//							$date = date('Y-m-d/H/i',strtotime($record->calldate));
//							$temp['date'] = date('d-m-Y H:i:s',strtotime($record->calldate));
//							$temp['url'] = sprintf($templateUrlHN, $date, trim($record->fbasename));
//                            $temp['phone'] = $phone;
//							//Tập trung các cuộc ghi âm của nhiều số điện thoại vào một nơi
//							array_push($all_records, $temp);
//						}
//					}
//				}
//
//				//Đếm số lượng file ghi âm
//				$count = count($all_records);
//				if($count == 0){
//					$result = ['state' => 'fail', 'error' => 'Không tìm thấy cuộc ghi âm nào'];
//				}else{
////				    foreach($all_records as $key => $val){
////                        $file = $val['url'];
////                        $ch = curl_init($file);
////                        curl_setopt($ch, CURLOPT_NOBODY, true);
////                        curl_exec($ch);
////                        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
////                        if($code != 200){
////                            $all_records[$key]['url'] = str_replace('wav','mp3', $val['url']);
////                        }
////                        curl_close($ch);
////                    }
//					$view = $this->getTableRecordView($all_records);
//					$result = ['state' => 'success', 'count' => $count,'detail' => $view];
//
//					$modelRecord->voice_survey_sections_id = $input['id'];
//					$modelRecord->voice_records = json_encode($result);
//					$modelRecord->voice_section_time_start = $section->section_time_completed;
//					$modelRecord->save();
//				}
//			}else{
//				$result = ['state' => 'fail', 'error' => "Không tìm thấy bất kỳ số điện thoại liên hệ nào"];
//			}
//			return Response::json($result);
//		}catch(Exception $e){
////			return Response::json(array('state' => 'fail', 'error' => $e->getMessage()));
//			return Response::json(array('state' => 'fail', 'error' => 'Lỗi xảy ra trên hệ thống'));
//		}
//	}

    public function getVoiceRecordsAjax(Request $request) {
        try
        {
            $sectionID= $request->sectionID;
            $voiceRecord = new VoiceRecord();
            $hasExist = $voiceRecord->checkExistVoice($sectionID);
            //Có trong outbound_voice rồi
            if(!empty($hasExist))
            {
                //lấy ra decode, xài luôn
                $jsonDecodeVoiceRecords = json_decode($hasExist[0]->voice_records);
                return $jsonDecodeVoiceRecords->detail;
            }
            else
            {
                $surveySection = new SurveySections();
                $result = $surveySection->getSurveyInfoByID($sectionID);
                $timeStart = $result[0]->section_time_start;
                $timeCompleted = $result[0]->section_time_completed;
                $phone = $result[0]->section_contact_phone;
                //Lấy khoảng thời gian để khoanh vùng
                $fifteenMinuteBefore = date('Y-m-d H:i:s', strtotime('-15 minutes', strtotime($timeCompleted)));
                $fifteenMinuteAfter = date('Y-m-d H:i:s', strtotime('+15 minutes', strtotime($timeCompleted)));
                //Chuyển định dạng phone
                $phone = '00855'.substr($phone, 1);
                //Tim dữ liệu voiceDB
                $dataVoice = $voiceRecord->getVoiceRecord('sqlsrv_voice_cam_1', $fifteenMinuteBefore, $fifteenMinuteAfter, $phone);
                if(!empty($dataVoice))
                {
                    $listVoiceRecord = [];
                    $voiceRecord = [];
                    foreach ($dataVoice as $voice)
                    {
                        $idCharsetRecord = base64_encode('/vox/'.$voice->voiceId.'/'.$voice->Channel.'/'.$voice->RecordReference). '.wav';
                        $voiceRecord['StopRecordTime'] = $voice->StopRecordTime;
                        $voiceRecord['CalledID'] = $phone;
                        $voiceRecord['idCharsetRecord'] = $idCharsetRecord;
                        array_push($listVoiceRecord, $voiceRecord);
                    }
                    $viewRender = view('records/voiceCAMRecords', ['listVoiceRecord' => $listVoiceRecord ])->render();
                    $voiceRecordRaw = ['state' => 'success', 'count' => count($listVoiceRecord), 'detail' => $viewRender];
                    $voiceRecordRawJson = json_encode($voiceRecordRaw);
                    $voiceRecord = new VoiceRecord();
                    $voiceRecord->voice_survey_sections_id = $sectionID;
                    $voiceRecord->voice_records = $voiceRecordRawJson;
                    $voiceRecord->voice_section_time_start = $timeStart;
                    $voiceRecord->save();
                    return $viewRender;
                }
                else
                {
                    $dataVoice2 = $voiceRecord->getVoiceRecord('sqlsrv_voice_cam_2', $fifteenMinuteBefore, $fifteenMinuteAfter, $phone);
                    if(!empty($dataVoice2))
                    {
                        $listVoiceRecord = [];
                        $voiceRecord = [];
                        foreach ($dataVoice2 as $voice)
                        {
                            $idCharsetRecord = base64_encode('/vox/'.$voice->voiceId.'/'.$voice->Channel.'/'.$voice->RecordReference). '.wav';
                            $voiceRecord['StopRecordTime'] = $voice->StopRecordTime;
                            $voiceRecord['CalledID'] = $phone;
                            $voiceRecord['idCharsetRecord'] = $idCharsetRecord;
                            array_push($listVoiceRecord, $voiceRecord);
                        }
                        $viewRender = view('records/voiceCAMRecords',  ['listVoiceRecord' => $listVoiceRecord ])->render();
                        $voiceRecordRaw = ['state' => 'success', 'count' => count($listVoiceRecord), 'detail' => $viewRender];
                        $voiceRecordRawJson = json_encode($voiceRecordRaw);
                        $voiceRecord = new VoiceRecord();
                        $voiceRecord->voice_survey_sections_id = $sectionID;
                        $voiceRecord->voice_records = $voiceRecordRawJson;
                        $voiceRecord->voice_section_time_start = $timeStart;
                        $voiceRecord->save();
                        return $viewRender;
                    }
                    else
                    {
                        //Không tìm thấy dữ liệu voice
                        return  view('records/voiceCAMRecords', [])->render();
                    }
                }

            }
        }
        catch (Exception $ex)
        {
            return Response::json(array('state' => 'fail', 'error' =>trans('common.SystemError')));
        }

    }
	
	private function getTableRecordView($all_records){
		$templateViewTable = '<table class="table table-striped table-bordered table-hover">'
			. '<thead>'
			.	'<tr>'
			.		'<th class="center">STT</th>'
			.		'<th><i class="icon-time bigger-120"></i>Ngày ghi âm</th>'
			.		'<th><i class="icon-phone bigger-120"></i>Số điện thoại</th>'
			.		'<th>Hành động</th>'
			.	'</tr>'
			. '</thead>'
			. '<tbody>%s</tbody>'
			. '</table>';
		
		$templatePlus = '';
		$templateViewOne = '<audio class="audio_class" controls autoplay>'
			.	'<source src="%s" type="audio/mp3">'
			.	'</audio>';
		
		$templateViewMany = '<tr>'
			.	'<td class="center">%d</td>'
			.	'<td>%s</td>'
			.	'<td>%s</td>'
			.	'<td>'
			.		'<audio class="audio_class" controls id="audio_control_%d">'
			.			'<source src="%s" type="audio/mp3">'
			.		'</audio>'
			.	'</td>'
			. '</tr>';
		
		$count = count($all_records);
		
		if($count == 1){
			$view = sprintf($templateViewOne,$all_records[0]['url']);
		}else{
			foreach($all_records as $key => $record){
				$templatePlus .= sprintf($templateViewMany, $key + 1, $record['date'],$record['phone'], $key + 1,$record['url']);
			}
			$view = sprintf($templateViewTable, $templatePlus);
		}
		
		return $view;
	}
}
