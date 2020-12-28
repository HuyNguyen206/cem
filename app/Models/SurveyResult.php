<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;

class SurveyResult extends Model {

    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'outbound_survey_result';

    public function getSurvey($id) {
        $result = DB::table($this->table)
            ->where('survey_id', '=', $id)
            ->first();
        return $result;
    }

    public function getQuestionBySurvey($surveyID) {
        $result = DB::table('outbound_questions')
            ->where('question_survey_id', '=', $surveyID)
            ->where('question_deleted', '=', '0')
            ->get();
        return $result;
    }

    public function saveSurveyResult($SurveyResult) {
        $id = DB::table('outbound_survey_result')->insertGetId($SurveyResult);
        return $id;
    }

    public function getDetailSurvey($surveyID) {
        $result = DB::table('outbound_survey_result')
            ->select('*')
            ->where('survey_result_section_id', '=', $surveyID)
            ->get();
        return $result;
    }

    public function updateDetailSurvey($idSurvey, $resultUpdate, $typeSurvey, $arrayAnswer, $section_con = '') {
        DB::beginTransaction();
        $result = DB::table('outbound_survey_result')
            ->select('*')
            ->where('survey_result_section_id', '=', $idSurvey)
            ->get();
        //Nếu chỉnh sửa lại bảng khảo sát(<5m)
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                if (isset($resultUpdate['answer' . $value->survey_result_question_id])) {
                    if ($value->survey_result_question_id == 5 || $value->survey_result_question_id == 7) {

                        if (!is_array($resultUpdate['answer' . $value->survey_result_question_id]))
                            $arrayResult = -1;
                        else {
                            $arrayResult = array();
//                            $arrayResult = implode(',', $resultUpdate['answer' . $value->survey_result_question_id]);
                            foreach ($resultUpdate['answer' . $value->survey_result_question_id] as $key => $value2) {
                                if ($value2 != false && $value2 != -1) {
                                    array_push($arrayResult, $value2);
                                }
                            }
                            if (count($arrayResult) > 0) {
                                $arrayResult = implode(',', $arrayResult);
                            } else {
                                $arrayResult = -1;
                            }
                        }

//                        var_dump($resultUpdate['answer' . $value->survey_result_question_id]);die;
                    } else {
                        $arrayResult = ($resultUpdate['answer' . $value->survey_result_question_id] == false || $resultUpdate['answer' . $value->survey_result_question_id] == '') ? -1 : $resultUpdate['answer' . $value->survey_result_question_id];
                    }
                } else
                    $arrayResult = -1;
                $resUpdateExtra = isset($resultUpdate['extraQuestion' . $value->survey_result_question_id]) ? $resultUpdate['extraQuestion' . $value->survey_result_question_id] : NULL;
                $resNote = isset($resultUpdate['subnote' . $value->survey_result_question_id]) ? $resultUpdate['subnote' . $value->survey_result_question_id] : '';

                $resultSearch = DB::table('outbound_survey_result')
                    ->where('survey_result_id', $value->survey_result_id)
                    ->where('survey_result_answer_id', $arrayResult)
                    ->where('survey_result_answer_extra_id', $resUpdateExtra)
                    ->where('survey_result_note', $resNote)
                    ->select('*')
                    ->first();

                if (empty($resultSearch)) {
                    $result = DB::table('outbound_survey_result')
                        ->where('survey_result_id', $value->survey_result_id)
                        ->update(['survey_result_answer_id' => $arrayResult, 'survey_result_answer_extra_id' => $resUpdateExtra, 'survey_result_note' => $resNote]);
                    if (!$result) {
                        DB::rollback();
                        throw new Exception(null, 500, null);
                    }
                }
            }

            DB::commit();
            return 'Cập nhật khảo sát thành công';
        }
        //Nếu retry khảo sát lại, insert dữ liệu mới
        else {
            $arrayIdQuestion = explode(' ', $arrayAnswer);
            array_pop($arrayIdQuestion);
            foreach ($arrayIdQuestion as $key => $value) {
                if ($value == "5" || $value == "7") {
                    //Edit
                    if ($section_con == 4) {
                        if (!is_array($resultUpdate['answer' . $value]))
                            $answer = -1;
                        else {
                            $answer = array();
//                            $arrayResult = implode(',', $resultUpdate['answer' . $value->survey_result_question_id]);
                            foreach ($resultUpdate['answer' . $value] as $key => $value2) {
                                if ($value2 != false && $value2 != -1) {
                                    array_push($answer, $value2);
                                }
                            }
                            if (count($answer) > 0) {
                                $answer = implode(',', $answer);
                            } else {
                                $answer = -1;
                            }
                        }
                    }
                    //Retry
                    else {
                        if (isset($resultUpdate['answer' . $value])) {
                            $answer = implode(',', $resultUpdate['answer' . $value]);
                        } else
                            $answer = -1;
                    }
                    $subnote = isset($resultUpdate['subnote' . $value]) ? $resultUpdate['subnote' . $value] : NULL;
                    $extraQues = isset($resultUpdate['extraQuestion' . $value]) ? $resultUpdate['extraQuestion' . $value] : NULL;
//                    var_dump($answer.','.$subnote.','.$extraQues);
//                    die;
                } else {
//                    $answer = isset($resultUpdate['answer' . $value]) ? $resultUpdate['answer' . $value] : -1;
                    $answer = (!isset($resultUpdate['answer' . $value]) || $resultUpdate['answer' . $value] == 0) ? -1 : $resultUpdate['answer' . $value];
                    $subnote = isset($resultUpdate['subnote' . $value]) ? $resultUpdate['subnote' . $value] : NULL;
                    $extraQues = isset($resultUpdate['extraQuestion' . $value]) ? $resultUpdate['extraQuestion' . $value] : NULL;
                }
                $valueint = (int) $value;
                //Vừa chấm điểm vừa chọn lý do 
                if ($extraQues != NULL) {
                    if ($answer != -1)
                        $extraQues = NULL;
                }
                $result = DB::table('outbound_survey_result')->insert([
                    ['survey_result_section_id' => $idSurvey, 'survey_result_question_id' => $valueint, 'survey_result_answer_id' => $answer, 'survey_result_answer_extra_id' => $extraQues, 'survey_result_note' => $subnote],
                ]);
                if (!$result) {
                    DB::rollback();
                    throw new Exception(null, 500, null);
                }
            }

            DB::commit();
            return 'Khảo sát lại thành công';
        }
    }

    public function checkIsEva($idSurvey, $type) {
        if ($type == 1) {
            $result = DB::table('outbound_survey_result')
                ->select('*')
                ->where('survey_result_section_id', '=', $idSurvey)
                ->Where(function ($query) {
                    $query->where('survey_result_question_id', '=', 7)
                    ->orWhere('survey_result_question_id', '=', 6);
                })
                ->get();
        } else {
            $result = DB::table('outbound_survey_result')
                ->select('*')
                ->where('survey_result_section_id', '=', $idSurvey)
                ->Where(function ($query) {
                    $query->where('survey_result_question_id', '=', 5)
                    ->orWhere('survey_result_question_id', '=', 8);
                })
                ->get();
        }
        if (empty($result)) {
            return true;
        } else
            return false;
    }

    public function apiGetInfoSurveySalaryIBB($question_id, $answer_id, $date_start, $date_end) {
        $result = DB::table('outbound_survey_result as osr')
            ->join('outbound_survey_sections as oss', 'oss.section_id', '=', 'osr.survey_result_section_id')
            ->join('outbound_answers as oa', 'oa.answer_id', '=', 'osr.survey_result_answer_id')
            ->whereRaw('osr.survey_result_question_id in (' . $question_id . ')')
            ->whereRaw('osr.survey_result_answer_id in (' . $answer_id . ')')
            ->where('oss.section_time_completed', '>=', $date_start)
            ->where('oss.section_time_completed', '<=', $date_end)
            ->select('oss.section_contract_num', 'oa.answers_point', 'oss.section_time_completed')
//				->toSql();
            ->get();
        return $result;
    }

    public function apiGetInfoSurveySalaryTinPNC($question_id, $answer_id, $date_start, $date_end) {
        $result = DB::table('outbound_survey_result as osr')
            ->join('outbound_survey_sections as oss', 'oss.section_id', '=', 'osr.survey_result_section_id')
            ->join('outbound_answers as oa', 'oa.answer_id', '=', 'osr.survey_result_answer_id')
            ->join('outbound_accounts as oac', 'oac.id', '=', 'oss.section_account_id')
            ->whereRaw('osr.survey_result_question_id in (' . $question_id . ')')
            ->whereRaw('osr.survey_result_answer_id in (' . $answer_id . ')')
            ->where('oss.section_time_completed', '>=', $date_start)
            ->where('oss.section_time_completed', '<=', $date_end)
            ->select('oac.objid as objId', 'oss.section_contract_num as contract', 'oa.answers_point as point', 'oss.section_time_completed as time', 'oss.section_supporter as supporter', 'oss.section_subsupporter as subSupporter', 'oss.section_code as code', 'oss.section_survey_id', 'oss.section_account_inf as accDeploy', 'oss.section_account_list as accMaintaince'
            )
//				->toSql();
            ->get();
        return $result;
    }

    public function getSurveyByParam($param) {
        $sql= DB::table($this->table);
        if(isset($param['arrayID'])){
            $sql->whereIn('survey_result_section_id', $param['arrayID']);
        }
        $sql->orderBy('survey_result_section_id', 'DESC');
        $result = $sql->get();
        return $result;
    }

    public function apiGetInfoSurveySalaryTinPNCAndNetTV($question_id, $answer_id, $date_start, $date_end) {
        $result = DB::table('outbound_survey_result as osr')
            ->join('outbound_survey_sections as oss', 'oss.section_id', '=', 'osr.survey_result_section_id')
            ->join('outbound_answers as oa', 'oa.answer_id', '=', 'osr.survey_result_answer_id')
            ->join('outbound_accounts as oac', 'oac.id', '=', 'oss.section_account_id')
            ->where('oss.section_time_completed_int', '>=', strtotime(date_format($date_start, 'Y-m-d 00:00:00')))
            ->where('oss.section_time_completed_int', '<=', strtotime(date_format($date_end, 'Y-m-d 23:59:59')))
            ->whereIn('osr.survey_result_question_id', $question_id)
            ->whereIn('osr.survey_result_answer_id', $answer_id)
            ->select('osr.survey_result_question_id','oac.objid as objId', 'oss.section_contract_num as contract',
                'oa.answers_point as point', 'oss.section_time_start as time', 'oss.section_supporter as supporter',
                'oss.section_subsupporter as subSupporter', 'oss.section_code as code', 'oss.section_survey_id',
                'oss.section_account_inf as accDeploy', 'oss.section_account_list as accMaintaince',
                'oss.section_time_completed_int as createdAtInt'
            )
            ->get();
        return $result;
    }
}
