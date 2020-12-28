<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Console\Input\InputArgument;
use Exception;

use App\Models\SummaryBranches;
use App\Models\OutboundQuestions;
use App\Models\OutboundAnswers;
use App\Models\SummaryObjects;
use App\Models\SummaryAction;
use App\Models\SummaryReason;
use App\Models\SummaryService;
use App\Models\SummaryCsat;
use App\Models\SummaryTime;
use App\Models\SummaryNps;
use App\Models\SummaryOpinion;

use DB;

class summaryDataTotal extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summaryData:total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Thống kê điểm CSAT của các loại khảo sát';

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
        DB::beginTransaction();
        try{
            $timeBegin = '2017-01-01';
            $now = date('Y-m-d',time());

            $outQuestionModel = new OutboundQuestions();
            $allQuestions = $outQuestionModel->getAllQuestion();

            $modelAnswer = new OutboundAnswers();
            $answers = $modelAnswer->getAnswerByGroup([1,2,9,10,11,12,13,14,15,16,17,18,19,20,21,22],[]);

            $modelObject = new SummaryObjects();
            $objects = $modelObject->getAllObject();

            $modelBranch = new SummaryBranches();
            $branches = $modelBranch->getAllBranch();

            dump('here1');

            $modelSumTime = new SummaryTime();
            $maxTime = $modelSumTime->getMaxTimeID();
//            $update = Redis::get('day_update_summary');
            if(empty($maxTime)){
                $timeIDNeed = $modelSumTime->getTimeIdByDay($timeBegin);
            }else{
                $timeBegin = date('Y-m-d',$maxTime->time_temp + 86400);
                if($timeBegin == $now){
                    return;
                }
                $timeIDNeed = $modelSumTime->getTimeIdByDay($timeBegin);
            }

            dump($timeBegin);

            $dateFrom = strtotime($timeBegin.' 00:00:00');
            $dateTo = strtotime($timeBegin.' 23:59:59');

            $arrayBranch = [];
            $arrayTotalTable = [
                'arrayNPS' => [],
                'arrayCSAT' => [],
                'arrayOpinion' => [],
                'arrayService' => [],
                'arrayAction' => [],
                'arrayReason' => [],
            ];
            $arrayObject = [];
            $fieldObject = [];
            $arrayAnswerGroup = [];

            $arrayQuestion = [];
            foreach($allQuestions as $question){
                if(isset($arrayQuestion[$question->question_alias])){
                    array_push($arrayQuestion[$question->question_alias], $question->question_id);
                }else{
                    $arrayQuestion[$question->question_alias] = [$question->question_id];
                }
            }

            dump('here2');

            foreach($answers as $answer){
                $arrayAnswerGroup[$answer->answer_id] = $answer->answer_group;
                switch($answer->answer_group){
                    case 1:
                        $fieldObject['summary_csat'][$answer->answer_id] = 0;
                        break;
                    case 2:
                        $fieldObject['summary_nps'][$answer->answer_id] = 0;
                        break;
                    case 9:
                    case 10:
                    case 11:
                    case 12:
                    case 13:
                    case 14:
                    case 15:
                    case 16:
                    case 17:
                    case 18:
                    case 19:
                        $fieldObject['summary_opinion'][$answer->answer_id] = 0;
                        break;
                    case 20:
                    case 22:
                        $fieldObject['summary_reason'][$answer->answer_id] = 0;
                        break;
                    case 21:
                        $fieldObject['summary_action'][$answer->answer_id] = 0;
                        break;
                    default:
                        $fieldObject['summary_csat'][$answer->answer_id] = 0;
                        $fieldObject['summary_nps'][$answer->answer_id] = 0;
                        $fieldObject['summary_service'][$answer->answer_id] = 0;
                        $fieldObject['summary_opinion'][$answer->answer_id] = 0;
                        $fieldObject['summary_reason'][$answer->answer_id] = 0;
                        $fieldObject['summary_action'][$answer->answer_id] = 0;
                }
            }

            dump('here3');

            foreach($objects as $object){
                $arrayObject[$object->object_id] = isset($fieldObject[$object->object_table])?$fieldObject[$object->object_table]:null;
            }

            dump('here4');

            foreach($branches as $branch){
                $arrayBranch[$branch->isc_location_id.'-'.$branch->isc_branch_code] = $branch->branch_id;
            }

            dump('here5');

            $surveySections = DB::table('outbound_survey_sections as s')
                ->leftJoin('outbound_survey_result as sr', 'sr.survey_result_section_id','=','s.section_id')
                ->select("s.section_survey_id", "s.section_record_channel", "s.section_location_id", "s.section_branch_code","s.section_supporter"
                    ,"sr.survey_result_question_id", "sr.survey_result_answer_id", "sr.survey_result_answer_extra_id", "sr.survey_result_action"
                )
                ->where("s.section_time_completed_int",">=",$dateFrom)
                ->where("s.section_time_completed_int","<=",$dateTo)
                ->where("s.section_connected", "=", 4)
                ->get();

            dump('here6');

            foreach($surveySections as $surveySection){
                foreach($objects as $object){
                    $surveyId = $surveySection->section_survey_id;
                    $questionId = $surveySection->survey_result_question_id;
                    $stringSurvey = $object->object_survey_id_array;
                    $stringQuestion = $object->object_question_array;
                    switch($object->object_id){
                        // CSAT
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                        case 7:
                        case 8:
                        case 11:
                        case 13:
                        case 14:
                        case 29:
                        case 30:
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayCSAT'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        case 15:
                            if(empty($surveySection->section_supporter) || !str_contains($surveySection->section_supporter,'INDO')){
                                break;
                            }
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayCSAT'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        case 16:
                            if(empty($surveySection->section_supporter) || str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayCSAT'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        case 21:
                            if(empty($surveySection->section_supporter) || !str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayCSAT'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        case 22:
                            if(empty($surveySection->section_supporter) || !str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayCSAT'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        case 23:
                            if(empty($surveySection->section_supporter) || str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayCSAT'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        case 24:
                            if(empty($surveySection->section_supporter) || str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayCSAT'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        // Reason
                        case 17:
                            if(!in_array($surveySection->survey_result_answer_id, [1,2])){
                                break;
                            }
                            if($surveySection->survey_result_answer_extra_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayReason'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_extra_id);
                            }
                            break;
                        case 18:
                            if(!in_array($surveySection->survey_result_answer_id, [1,2])){
                                break;
                            }
                            if($surveySection->survey_result_answer_extra_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayReason'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_extra_id);
                            }
                            break;
                        //Action
                        case 19:
                            if(!in_array($surveySection->survey_result_answer_id, [1,2])){
                                break;
                            }
                            if($surveySection->survey_result_action != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayAction'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_action);
                            }
                            break;
                        case 20:
                            if(!in_array($surveySection->survey_result_answer_id, [1,2])){
                                break;
                            }
                            if($surveySection->survey_result_action != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayAction'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_action);
                            }
                            break;
                        // NPS
                        case 10:
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayNPS'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        case 25:
                            if(empty($surveySection->section_supporter) || str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayNPS'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        case 26:
                            if(empty($surveySection->section_supporter) || !str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $this->pushDataToArray($arrayTotalTable['arrayNPS'], $arrayBranch, $surveySection, $object, $arrayObject, $surveySection->survey_result_answer_id);
                            }
                            break;
                        // Opinion
                        case 9:
                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $tempArrayAnswerID = explode(',',$surveySection->survey_result_answer_id);
                                foreach($tempArrayAnswerID as $tempID){
                                    $this->pushDataToArray($arrayTotalTable['arrayOpinion'], $arrayBranch, $surveySection, $object, $arrayObject, $tempID);
                                }
                            }

                            break;
                        case 27:
                            if(empty($surveySection->section_supporter) || str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }

                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $tempArrayAnswerID = explode(',',$surveySection->survey_result_answer_id);
                                foreach($tempArrayAnswerID as $tempID){
                                    $this->pushDataToArray($arrayTotalTable['arrayOpinion'], $arrayBranch, $surveySection, $object, $arrayObject, $tempID);
                                }
                            }

                            break;
                        case 28:
                            if(empty($surveySection->section_supporter) || !str_contains($surveySection->section_supporter, 'INDO')){
                                break;
                            }

                            if($surveySection->survey_result_answer_id != -1 && $this->checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion)){
                                $tempArrayAnswerID = explode(',',$surveySection->survey_result_answer_id);
                                foreach($tempArrayAnswerID as $tempID){
                                    $this->pushDataToArray($arrayTotalTable['arrayOpinion'], $arrayBranch, $surveySection, $object, $arrayObject, $tempID);
                                }
                            }
                            break;

                        default:
                    }
                }

            }

            dump('here7');

            $arrayTotalTableInsert = [];
            foreach($arrayTotalTable as $keyArrayTable => $valArrayTable){
                foreach($valArrayTable as $keyRecord => $valRecord){
                    $this->pushDataToArrayTableInsert($arrayTotalTableInsert, $keyArrayTable, $keyRecord, $valRecord, $arrayAnswerGroup, $timeIDNeed);
                }
            }
            dump('here8');

            foreach($arrayTotalTableInsert as $keyArrayTable => $valArrayTable){
                switch($keyArrayTable){
                    case 'arrayNPS':
                        SummaryNps::insert($valArrayTable);
                        break;
                    case 'arrayCSAT':
                        SummaryCsat::insert($valArrayTable);
                        break;
                    case 'arrayOpinion':
                        SummaryOpinion::insert($valArrayTable);
                        break;
                    case 'arrayService':
                        SummaryService::insert($valArrayTable);
                        break;
                    case 'arrayAction':
                        SummaryAction::insert($valArrayTable);
                        break;
                    case 'arrayReason':
                        SummaryReason::insert($valArrayTable);
                        break;
                    default:
                }
            }

            dump('here9');
            DB::commit();
        }catch(Exception $e){
            dump($e->getMessage());
            dump($e->getLine());
            DB::rollback();
        }
    }

    private function checkIn2Array($surveyId, $questionId, $stringSurvey, $stringQuestion){
        $arraySurveyID = explode(',',$stringSurvey);
        $arrayQuestion = explode(',',$stringQuestion);
        if(in_array($surveyId, $arraySurveyID) && in_array($questionId, $arrayQuestion)){
            return true;
        }
        return false;
    }

    private function pushDataToArray(&$array, $arrayBranch, $surveySection, $object, $arrayObject, $keyWantToAdd){
        if(!isset($arrayBranch[$surveySection->section_location_id.'-'.$surveySection->section_branch_code])){
            return;
        }
        if(!isset($array[$object->object_id.":".$arrayBranch[$surveySection->section_location_id.'-'.$surveySection->section_branch_code].":".$surveySection->section_record_channel.":".$surveySection->section_survey_id])){
            $array[$object->object_id.":".$arrayBranch[$surveySection->section_location_id.'-'.$surveySection->section_branch_code].":".$surveySection->section_record_channel.":".$surveySection->section_survey_id] = $arrayObject[$object->object_id];
        }
        if(isset($array[$object->object_id.":".$arrayBranch[$surveySection->section_location_id.'-'.$surveySection->section_branch_code].":".$surveySection->section_record_channel.":".$surveySection->section_survey_id][$keyWantToAdd])){
            $array[$object->object_id.":".$arrayBranch[$surveySection->section_location_id.'-'.$surveySection->section_branch_code].":".$surveySection->section_record_channel.":".$surveySection->section_survey_id][$keyWantToAdd]++;
        }
    }

    private function pushDataToArrayTableInsert(&$arrayTotalTableInsert, $keyArrayTable, $keyRecord, $valRecord, $arrayAnswerGroup,$timeIDNeed){
        $modelSumCSAT = new SummaryCsat();
        $modelSumNPS = new SummaryNps();
        $modelSumAction = new SummaryAction();
        $modelSumReason = new SummaryReason();
        $modelSumOpinion = new SummaryOpinion();
        $modelSumService = new SummaryService();
        $fieldTable = [
            'arrayNPS' => $modelSumNPS->getFieldName(),
            'arrayCSAT' => $modelSumCSAT->getFieldName(),
            'arrayOpinion' => $modelSumOpinion->getFieldName(),
            'arrayService' => $modelSumService->getFieldName(),
            'arrayAction' => $modelSumAction->getFieldName(),
            'arrayReason' => $modelSumReason->getFieldName(),
        ];

        $timeID = $timeIDNeed;
        $tempValueTable = explode(':',$keyRecord);
        switch($keyArrayTable){
            case 'arrayNPS':
            case 'arrayCSAT':
                $temp[] = $timeID;
                foreach($tempValueTable as $val){
                    $temp[] = $val;
                }
                foreach($valRecord as $val){
                    $temp[] = $val;
                }
                $record = array_combine($fieldTable[$keyArrayTable], $temp);
                $arrayTotalTableInsert[$keyArrayTable][] = $record;
                break;
            case 'arrayOpinion':
            case 'arrayService':
            case 'arrayAction':
            case 'arrayReason':
                $temp[] = $timeID;
                foreach($tempValueTable as $val){
                    $temp[] = $val;
                }

                foreach($valRecord as $key => $val){
                    if($val == 0){
                        continue;
                    }
                    $tempFull = $temp;
                    $tempFull[] = $key;
                    $tempFull[] = $arrayAnswerGroup[$key];
                    $tempFull[] = $val;
                    $record = array_combine($fieldTable[$keyArrayTable], $tempFull);
                    $arrayTotalTableInsert[$keyArrayTable][] = $record;
                }
                break;
            default:
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
}
