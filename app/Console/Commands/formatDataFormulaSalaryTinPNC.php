<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;

use App\Models\FormulaSalary\FormulaSalaryTinPNC;
use App\Models\SurveyResult;
use App\Component\ExtraFunction;
use DB;

class formatDataFormulaSalaryTinPNC extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'formatData:salaryTinPNC';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record for formula salary Tin PNC';

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
            $timeBegin = '2018-04-01';
            $now = date('Y-m-d',time());

            $extra = new ExtraFunction();
            $param['type'] = [1,2,6,9,10];
            $param['alias'] = [3,4,5,6,30];
            $format = $extra->getFormatQuestionByParam($param);

            $modelFormulaSalaryTinPNC = new FormulaSalaryTinPNC();
            $newestRecord = $modelFormulaSalaryTinPNC->getNewestRecord();
            if(!empty($newestRecord)){
                $maxTime = date('Y-m-d', strtotime($newestRecord->createdAtDate) + 86400);
                if($now == $maxTime){
                    return;
                }
                $timeBegin =  $maxTime;
            }

            $dateFrom = date_create($timeBegin.' 00:00:00');
            $dateTo = date_create($timeBegin.' 23:59:59');

            $sr = new SurveyResult();
            foreach($format as $questionID){
                $resGet = $sr->apiGetInfoSurveySalaryTinPNCAndNetTV($questionID, [1,2,3,4,5], $dateFrom, $dateTo);
                $result = $this->filterResponseSalaryTinPNC($resGet);
                $arrayChunks = array_chunk($result, 2000);
                foreach($arrayChunks as $arrayRecordInsert){
                    FormulaSalaryTinPNC::insert($arrayRecordInsert);
                }
            }
            FormulaSalaryTinPNC::insert(['createdAtDate' => $timeBegin]);
            DB::commit();
        }catch(Exception $e){
            dump($e->getMessage());
            dump($e->getLine());
            DB::rollback();
        }
    }

    private function filterResponseSalaryTinPNC($result) {
        $arrayWant = [];
        foreach ($result as $val) {
            if(isset($arrayWant[$val->contract.':'.$val->code.':'.$val->section_survey_id])){
                if(in_array($val->survey_result_question_id ,[10,12,20,41,46])){
                    $arrayWant[$val->contract.':'.$val->code.':'.$val->section_survey_id]['netPoint'] = $val->point;
                }
                if(in_array($val->survey_result_question_id ,[11,13,21,42,47])){
                    $arrayWant[$val->contract.':'.$val->code.':'.$val->section_survey_id]['tvPoint'] = $val->point;
                }
                if(in_array($val->survey_result_question_id ,[2,4,22,38,43])){
                    $arrayWant[$val->contract.':'.$val->code.':'.$val->section_survey_id]['point'] = $val->point;
                }
            }else{
                $temp = new \stdClass();
                $temp->objId = $val->objId;
                $temp->contract = $val->contract;
                $temp->time = $val->time;
                $temp->supporter = $val->supporter;
                $temp->subSupporter = $val->subSupporter;
                $temp->code = $val->code;
                $temp->section_survey_id = $val->section_survey_id;
                $temp->accDeploy = $val->accDeploy;
                $temp->accMaintaince = $val->accMaintaince;
                $temp->tvPoint = null;
                $temp->netPoint = null;
                $temp->point = null;
                $temp->createdAtInt = $val->createdAtInt;
                $temp->createdAtDate = date('Y-m-d', $val->createdAtInt);
                if(in_array($val->survey_result_question_id ,[10,12,20,41,46])){
                    $temp->netPoint = $val->point;
                }
                if(in_array($val->survey_result_question_id ,[11,13,21,42,47])){
                    $temp->tvPoint = $val->point;
                }
                if(in_array($val->survey_result_question_id ,[2,4,22,38,43])){
                    $temp->point = $val->point;
                }
                $temp = (array)$temp;
                $arrayWant[$val->contract.':'.$val->code.':'.$val->section_survey_id] = $temp;
            }
        }

        return array_values($arrayWant);
    }
}
