<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ListInvalidSurveyCase;
use App\Models\SurveySections;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Exception;
use DB;
use App\Models\SurveyResult;
use App\Models\SurveySectionsEmail;
use App\Component\ExtraFunction;
use App\Models\Location;

class getAnonymousSurvey extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'survey:getAnonymousSurvey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data survey from anonymous user';

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
        $yesterday = date('Y-m-d', strtotime("-1 days"));
//        $yesterday = '2018-05-26';
//        Redis::set('anonymous_survey', json_encode(['date' => '', 'total_page' => '', 'page' => 0]));
        $anonymousSurvey = json_decode(Redis::get('anonymous_survey'));
        $eva1 = $anonymousSurvey->date == '' || $yesterday != $anonymousSurvey->date;
        $eva2 = $anonymousSurvey->total_page != $anonymousSurvey->page;
        //Lần gọi đầu tiên hoặc sang ngày mới hoặc gọi chưa hết
        if ($eva1 || $eva2) {
            $listLocation= Location::get()->toArray();
            foreach ($listLocation as $key => $value) {
                $locationNameArray = explode(' ', $value['region']);
                $listLocationEdit[$value['id']] = 'Vung ' . $locationNameArray[1];
            }
            $extraFunction = new ExtraFunction();
            $date = ($eva1) ? $yesterday : $anonymousSurvey->date;
            $page = ($eva1) ? 1 : $anonymousSurvey->page + 1;
            $url = 'http://api-survey.fpt.net/report/survey-rating/get-by-date?';
            $input = ['from' => $date, 'to' => $date, 'page' => $page, 'per_page' => 10];
            $url .= http_build_query($input);
            $result = $extraFunction->sendRequest($url, $extraFunction->getHeader());
            $dataSurveyResult = [];
            $dataSurveyEmail = [];
            DB::beginTransaction();
            try {
                foreach ($result['msg']['data']['data'] as $key => $value) {
                    //Khong xu ly du lieu bi loi
                    if (in_array($value['area_code'], ['4', '8']) && $value['branch_code'] == '0')
                        continue;
                    $surveySection = new SurveySections();
                    $surveySection->section_survey_id = 11;
//                    $surveySection->section_code = uniqid();
                    $surveySection->section_code = md5(uniqid() . $value['survey_date']);
                    $surveySection->section_contract_num = 'ANONYMOUS';
                    $surveySection->section_location_id = $value['area_code'];
                    $surveySection->section_branch_code = $value['branch_code'];
                    $surveySection->section_sale_branch_code = NULL;
                    $surveySection->section_sub_parent_desc = isset($listLocationEdit[$value['area_code']]) ? $listLocationEdit[$value['area_code']] : null ;                 
                    $surveySection->section_time_start = $value['survey_date'];
                    $surveySection->section_time_completed = $value['survey_date'];
                    $surveySection->section_time_completed_int = strtotime($value['survey_date']);
                    $surveySection->section_record_channel = 7;
                    $surveySection->sale_center_id = NULL;
                    $surveySection->save();
                    $sectionId = $surveySection->section_id;
                    array_push($dataSurveyResult, ['survey_result_section_id' => $sectionId, 'survey_result_question_id' => 48, 'survey_result_answer_id' => $value['rating_point']]);
                    array_push($dataSurveyEmail, ['section_id' => $sectionId, 'mac_address' => $value['mac_address'], 'counter_code' => $value['counter_code']]);
                }
                SurveyResult::insert($dataSurveyResult);
                SurveySectionsEmail::insert($dataSurveyEmail);
                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
                echo $ex->getLine();
                echo $ex->getMessage();
            }
            Redis::set('anonymous_survey', json_encode(['date' => $date, 'total_page' => $result['msg']['data']['total_page'], 'page' => $page]));
        }
        echo 'thanh cong';
    }

}
