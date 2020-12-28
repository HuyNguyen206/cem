<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VoiceRecord extends Model
{
    protected $table = 'outbound_voice';
    protected $fillable = ['voice_survey_sections_id', 'voice_records', 'voice_section_time_start'];
	
	public function getAllRecordOnInputInServerVoiceSG($input){
		$voice = DB::connection('sqlsrv_voice_cam_1')
				->table('RecordOriginalData as r')
				->where('c.calldate','>=',$input['time_from'])
				->where('c.calldate','<=',$input['time_to'])
				->where('c.duration','>',0)
				->orderBy('c.calldate', 'ASC')
				->limit('10')
				->select('c.calldate', 'c.called', 'n.fbasename');
		if(!empty($input['phone'])){
//			$voice->where('c.called','=',trim($input['phone']));
            $raw = "c.called LIKE '%".trim($input['phone'])."%'";
            $voice->whereRaw($raw);
		}
		return $voice->get();
	}
	
	public function getAllRecordOnInputInServerVoiceHN($input){
		$voice = DB::connection('sqlsrv_voice_cam_2')
				->table('cdr as c')
				->where('c.calldate','>=',$input['time_from'])
				->where('c.calldate','<=',$input['time_to'])
				->where('c.duration','>',0)
				->orderBy('c.calldate', 'ASC')
				->limit('10')
				->select('c.calldate', 'c.called', 'n.fbasename');
		if(!empty($input['phone'])){
//			$voice->where('c.called','=',trim($input['phone']));
            $raw = "c.called LIKE '%".trim($input['phone'])."%'";
            $voice->whereRaw($raw);
		}
		return $voice->get();
	}

	public function checkExistVoice($sectionID)
    {
        $hasExist = DB::table('outbound_voice as ov')->select('ov.voice_records')
            ->where('ov.voice_survey_sections_id', $sectionID)
            ->get();
        return $hasExist;
    }

    public function getVoiceRecord($connection, $fifteenMinuteBefore, $fifteenMinuteAfter, $phone)
    {
        $dataVoice = DB::connection($connection)->table('RecordOriginalData')->select('voiceId', 'Channel', 'RecordReference', 'StopRecordTime')
            ->where('StopRecordTime', '>=' , $fifteenMinuteBefore)
            ->where('StopRecordTime', '<=' , $fifteenMinuteAfter)
            ->where('CalledID', $phone)
            ->get();
        return $dataVoice;
    }
}