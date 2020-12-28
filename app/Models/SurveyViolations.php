<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class SurveyViolations extends Model {

    protected $table = 'survey_violations';
    protected $primaryKey = 'id';

    public function insertViolation($param){
        $resIns = DB::table($this->table)->insert($param);
        return $resIns;
    }

    public function getSurveyViolationCSATNV($day, $dayTo){
        $violation = [
            'sales' => [
                1, 2, 3, 8, 9, 10, 11, 12,
            ],
            'staff' => [
                1, 2, 3, 4, 5, 6, 7, 8, 11, 12,
            ],
              'nvtc' => [
                1, 2, 3, 8, 9, 10, 11, 12,
            ]
        ];
        $punish = [
            1, 2, 3, 4, 5,
        ];

        $sqlAdd = '';
        foreach($violation['sales'] as $key){
            $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (1) and sv.violations_type = '.$key.',1,0)) as violation_sales_'.$key;
        }
        $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (1) and sv.violations_type in('.implode(',',$violation['sales']).'),1,0)) as violation_sales_t';
        
         foreach($violation['nvtc'] as $key){
            $sqlAdd .= ',sum(if(s.section_survey_id in (7) and sv.point in (1,2) and sv.type_report in (6) and sv.violations_type = '.$key.',1,0)) as violation_nvtc_'.$key;
        }
        $sqlAdd .= ',sum(if(s.section_survey_id in (7) and sv.point in (1,2) and sv.type_report in (6) and sv.violations_type in('.implode(',',$violation['nvtc']).'),1,0)) as violation_nvtc_t';
        
        foreach($violation['staff'] as $key){
            $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (2) and sv.violations_type = '.$key.',1,0)) as violation_deploy_'.$key;
            $sqlAdd .= ',sum(if(s.section_survey_id = 2 and sv.point in (1,2) and sv.type_report in (3) and sv.violations_type = '.$key.',1,0)) as violation_maintaince_'.$key;
            $sqlAdd .= ',sum(if(s.section_survey_id in (1,2) and sv.type_report in (2,3) and (sv.point in (1,2) or sv.point in (1,2)) and sv.violations_type = '.$key.',1,0)) as violation_deploy_maintaince_'.$key;
        }
        $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (2) and sv.violations_type in ('.implode(',',$violation['staff']).'),1,0)) as violation_deploy_t';
        $sqlAdd .= ',sum(if(s.section_survey_id = 2 and sv.point in (1,2) and sv.type_report in (3) and sv.violations_type in('.implode(',',$violation['staff']).'),1,0)) as violation_maintaince_t';
        $sqlAdd .= ',sum(if(s.section_survey_id in (1,2) and sv.type_report in (2,3) and (sv.point in (1,2) or sv.point in (1,2)) and sv.violations_type in('.implode(',',$violation['staff']).'),1,0)) as violation_deploy_maintaince_t';
        foreach($punish as $key){
            $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (1) and sv.punishment = '.$key.',1,0)) as punish_sales_'.$key;
            $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (2) and sv.punishment = '.$key.',1,0)) as punish_deploy_'.$key;
            $sqlAdd .= ',sum(if(s.section_survey_id = 2 and sv.point in (1,2) and sv.type_report in (3) and sv.punishment = '.$key.',1,0)) as punish_maintaince_'.$key;
            $sqlAdd .= ',sum(if(s.section_survey_id in (1,2) and sv.type_report in (2,3) and (sv.point in (1,2) or sv.point in (1,2)) and sv.punishment = '.$key.',1,0)) as punish_deploy_maintaince_'.$key;
        }
        $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (1) and sv.punishment in('.implode(',',$punish).'),1,0)) as punish_sales_t';
        $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (2) and sv.punishment in('.implode(',',$punish).'),1,0)) as punish_deploy_t';
        $sqlAdd .= ',sum(if(s.section_survey_id = 2 and sv.point in (1,2) and sv.type_report in (3) and sv.punishment in('.implode(',',$punish).'),1,0)) as punish_maintaince_t';
        $sqlAdd .= ',sum(if(s.section_survey_id in (1,2) and sv.type_report in (2,3) and (sv.point in (1,2) or sv.point in (1,2)) and sv.punishment in('.implode(',',$punish).'),1,0)) as punish_deploy_maintaince_t';

        $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (1),sv.ftq_modify_count,0)) as ftq_sales';
        $sqlAdd .= ',sum(if(s.section_survey_id in (1) and sv.point in (1,2) and sv.type_report in (2),sv.ftq_modify_count,0)) as ftq_deploy';
        $sqlAdd .= ',sum(if(s.section_survey_id = 2 and sv.point in (1,2) and sv.type_report in (3),sv.ftq_modify_count,0)) as ftq_maintaince';
        $sqlAdd .= ',sum(if(s.section_survey_id in (1,2) and sv.type_report in (2,3) and (sv.point in (1,2) or sv.point in (1,2)),sv.ftq_modify_count,0)) as ftq_deploy_maintaince';

        $sqlRaw = "s.section_sub_parent_desc, s.section_location_id, s.section_location, convert(s.section_branch_code, unsigned integer) as section_branch_code";
        $result = DB::table($this->table.' as sv')
            ->join('outbound_survey_sections as s', 's.section_id', '=','sv.section_id')
//            ->join('outbound_survey_result as sr', 'sr.survey_result_section_id','=','s.section_id')
            ->selectRaw($sqlRaw.$sqlAdd)
            ->whereIn('s.section_survey_id', [1,2])
            ->where('s.section_connected','=',4)
            ->whereNotNull('s.section_action')
            ->where('s.section_time_completed_int','<=', strtotime($dayTo))
            ->where('s.section_time_completed_int','>=', strtotime($day))
            ->groupBy('s.section_sub_parent_desc','s.section_location_id', 's.section_branch_code')
            ->orderBy('s.section_sub_parent_desc','s.section_location_id','s.section_branch_code')
            ->get();
        return $result;
    }

    public function searchViolationByParam($param){
        $mainQuery = DB::table($this->table.' as v')
            ->select("v.*")
            ->join('outbound_survey_sections as s', 's.section_id', '=', 'v.sectionID')
            ->where('v.sectionID', $param['id'])
            ->where("v.type", $param['type'])
        ;
        $res = $mainQuery->first();
        return $res;
    }
}
