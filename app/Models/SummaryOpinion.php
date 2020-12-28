<?php

/**
 * Created by PhpStorm.
 * User: Minh Tuan
 * Date: 2017-06-16
 * Time: 2:24 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;

class SummaryOpinion extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_opinion';
    protected $fillable = ['time_id', 'object_id', 'branch_id', 'channel_id', 'poc_id', 'opinion_id', 'group_id', 'total'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getOpinionSummaryByTime($from_date, $to_date, $locationID, $hasFifter) {
        $typeMap= [1 => 'SauTK', 2 => 'SauBT'];
        foreach ($typeMap as $key => $value)
        {
            //Tính tổng số khách hàng có góp ý, ko góp ý, được hỏi ở mỗi
//            loại khảo sát
            $resultGroupNPS = DB::table('outbound_survey_sections as oss 
')
                ->join('outbound_survey_result as osr',
                    'osr.survey_result_section_id', '=', 'oss.section_id')
                ->select(DB::raw('
                CASE
               WHEN osr.survey_result_answer_id <> 200 and osr.survey_result_answer_id <> -1   THEN "TongSoKHGopY_'.$key.'"
               WHEN osr.survey_result_answer_id = 200  THEN  "TongSoKHKhongGopY_'.$key.'"
            -- WHEN osr.survey_result_answer_id <> -1 THEN  "TongSoKHDuocHoi_'.$key.'"
               else osr.survey_result_answer_id end as "GroupTotal",          
               count(distinct(oss.section_id)) as "Quantity"'))
                ->where('oss.section_time_completed_int', '>=',strtotime($from_date))
                ->where('oss.section_time_completed_int', '<=', strtotime($to_date))
                ->whereIn('osr.survey_result_question_id', [12, 13])
                ->whereIn('oss.section_survey_id', [1, 2])
                ->where('oss.section_survey_id', $key)
                ->groupBy(DB::raw('CASE 
               WHEN osr.survey_result_answer_id <> 200 and osr.survey_result_answer_id <> -1   THEN "TongSoKHGopY_'. $key.'"
               WHEN osr.survey_result_answer_id = 200  THEN "TongSoKHKhongGopY_'.$key.'"
            -- WHEN osr.survey_result_answer_id <> -1 THEN  "TongSoKHDuocHoi_'.$key.'"
                END'))
            ;
            //Gọi từ Report có truyền tham số fifter
            if ($hasFifter == 1) {
                $resultGroupNPS = $resultGroupNPS
                    ->where(function($query) use ($locationID) {
                        if (!empty($locationID)) {
                            $query->whereIn('oss.section_location_id',
                                $locationID);
                        }
                    });
            }
            if($key == 1)
            {
//                $generalResult.$value = $resultGroupNPS;
                $resultGroupNPSAll = $resultGroupNPS;

            }
            else
            {
                $resultGroupNPSAll->unionAll($resultGroupNPS);
            }
        }

        $resultGroupNPSAll = $resultGroupNPSAll->get();
        $resultGroupNPSAll= $this->formatGroupNPSAllData($typeMap, $resultGroupNPSAll);
//        dump($resultGroupNPSAll);die;
//        $hasCurrentDay = strtotime(date('y-m-d')) >= strtotime($fromDay) && strtotime(date('y-m-d')) <= strtotime($toDay);
            $resultDetailGroupNPS = DB::table(DB::raw('outbound_survey_result osr join  outbound_survey_sections oss  on oss.section_id=osr.survey_result_section_id join outbound_answers as oa on 
            osr.survey_result_answer_id = oa.answer_id
     '))
                    ->select(DB::raw('CASE 
 WHEN oa.answer_group=9 THEN "Staffs" 
 WHEN oa.answer_group=10 THEN "InternetService"
 WHEN oa.answer_group=12 THEN "Equipment" 
 WHEN oa.answer_group=13 THEN "Policy"
 WHEN oa.answer_group=14 THEN "Price"
 WHEN oa.answer_group=17 THEN "SupportDuration"
 WHEN oa.answer_group=18 THEN "NoCommentGroup"
 WHEN oa.answer_group=19 THEN "Other" 
 ELSE oa.answer_group END AS "answers_group_title", 
  CASE 
 WHEN oa.answer_id=151 THEN "SIR" 
 WHEN oa.answer_id=152 THEN "IBB"
 WHEN oa.answer_id=153 THEN "CC" 
 WHEN oa.answer_id=154 THEN "CUS"
 WHEN oa.answer_id=155 THEN "Collector" 
 WHEN oa.answer_id=156 THEN "Onsite"
 WHEN oa.answer_id=160 THEN "InternetSpeed" 
 WHEN oa.answer_id=161 THEN "InternetStable" 
 WHEN oa.answer_id=162 THEN "Game" 
 WHEN oa.answer_id=170 THEN "Modem" 
 WHEN oa.answer_id=171 THEN "Router" 
 WHEN oa.answer_id=172 THEN "ONU" 
 WHEN oa.answer_id=180 THEN "RegisterPayment" 
 WHEN oa.answer_id=181 THEN "Promotion" 
 WHEN oa.answer_id=182 THEN "CustomerCareAfterSell" 
 WHEN oa.answer_id=183 THEN "MaintenanceCommitment" 
 WHEN oa.answer_id=190 THEN "Package" 
 WHEN oa.answer_id=191 THEN "EquipmentPrice" 
 WHEN oa.answer_id=192 THEN "Upgrade" 
 WHEN oa.answer_id=201 THEN "InstallationDuration" 
 WHEN oa.answer_id=202 THEN "MaintenanceDuration" 
 WHEN oa.answer_id=203 THEN "ComplainSolvingDuration" 
 WHEN oa.answer_id=200 THEN "NoComment" 
 WHEN oa.answer_id=210 THEN "Other"

 else oa.answer_id
 end as "Content",
  sum(if(oss.section_survey_id=1,1,0)) "SauTK",
 sum(if(oss.section_survey_id=2,1,0)) "SauBT",
 sum(if(oss.section_survey_id=1,1,0))+sum(if(oss.section_survey_id=2,1,0)) "Total"'))
                    ->where('oss.section_time_completed_int', '>=', strtotime($from_date))
                    ->where('oss.section_time_completed_int', '<=', strtotime($to_date))
                    ->whereIn('osr.survey_result_question_id', [12, 13])
                    ->where('osr.survey_result_answer_id', '<>', -1)
                    ->whereIn('oa.answer_group', [9, 10, 12, 13, 14, 17, 18, 19]);
            if ($hasFifter == 1) {
                $resultDetailGroupNPS = $resultDetailGroupNPS
                    ->where(function($query) use ($locationID) {
                        if (!empty($locationID)) {
                            $query->whereIn('oss.section_location_id', $locationID);
                        }
                    });
            }

        $resultDetailGroupNPS = $resultDetailGroupNPS->groupBy(DB::raw('oa.answer_group, oa.answer_id'))->get();


        return ['resultGroupNPS' => $resultGroupNPSAll, 'resultDetailGroupNPS' => $resultDetailGroupNPS,];
    }

    public function getTableColumns() {
        $result = DB::getSchemaBuilder()->getColumnListing($this->table);
        return $result;
    }

    public function getFieldName() {
        return $this->fillable;
    }

    public function formatGroupNPSAllData($typeMap, $data)
    {
        $formatNPS = [];
        foreach($data as $value)
        {
            $arrayValue = explode('_',$value->GroupTotal);
            if(!isset($formatNPS[$arrayValue[1]]))
            {
                $formatNPS[$arrayValue[1]]['TypeSurvey'] = $typeMap[$arrayValue[1]];
            }
            $formatNPS[$arrayValue[1]][$arrayValue[0]] = $value->Quantity;
        }
        foreach($formatNPS as $key => $value)
        {
            if(!isset($value['TongSoKHGopY']))
            {
                $formatNPS[$key]['TongSoKHGopY'] = 0;
            }
            if(!isset($value['TongSoKHKhongGopY']))
            {
                $formatNPS[$key]['TongSoKHKhongGopY'] = 0;
            }
        }
        foreach($formatNPS as $key => $value)
        {
            $formatNPS[$key]['TongSoKHDuocHoi'] = $value['TongSoKHGopY'] + $value['TongSoKHKhongGopY'];
            $formatNPS[$key] = (object) $formatNPS[$key];
        }
        return $formatNPS;

    }

}
