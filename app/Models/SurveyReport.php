<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SurveyReport extends Model {

    protected $table = 'survey_section_report';
    protected $primaryKey = 'section_id';

    public function getSurveyReportCSATNV($day, $dayTo) {
        $likeSale = 'sales":null';
        $likeDeploy = 'deployer":null';
        $likeMaintaince = 'maintenance":null';
        $sqlRaw = "s.section_sub_parent_desc, s.section_location_id, s.section_location, convert(s.section_branch_code, unsigned integer) as section_branch_code,
                sum(if(s.section_survey_id in (1,6),1,0)) as csat_tong_sales,
                sum(if(s.csat_salesman_point = 1 and s.section_survey_id in (1,6),1,0)) as csat1_sales,
                sum(if(s.csat_salesman_point = 2 and s.section_survey_id in (1,6),1,0)) as csat2_sales,
                sum(if(s.csat_salesman_point in (1,2) and s.section_survey_id in (1,6),1,0)) as csat12_sales,
                sum(if(s.section_survey_id in (1,6),s.csat_salesman_point,0)) as csat12_diem_sales,
                sum(if(s.csat_salesman_point = 1 and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeSale . "%',1,0)) as csat1_cbc_sales,
                sum(if(s.csat_salesman_point = 2 and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeSale . "%',1,0)) as csat2_cbc_sales,
                sum(if(s.csat_salesman_point in (1,2) and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeSale . "%',1,0)) as csat12_cbc_sales,
                
                sum(if(s.section_survey_id in (1,6),1,0)) as csat_tong_deploy,
                sum(if(s.csat_deployer_point = 1 and s.section_survey_id in (1,6),1,0)) as csat1_deploy,
                sum(if(s.csat_deployer_point = 2 and s.section_survey_id in (1,6),1,0)) as csat2_deploy,
                sum(if(s.csat_deployer_point in (1,2) and s.section_survey_id in (1,6),1,0)) as csat12_deploy,
                sum(if(s.section_survey_id in (1,6),s.csat_deployer_point,0)) as csat12_diem_deploy,
                sum(if(s.csat_deployer_point = 1 and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeDeploy . "%',1,0)) as csat1_cbc_deploy,
                sum(if(s.csat_deployer_point = 2 and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeDeploy . "%',1,0)) as csat2_cbc_deploy,
                sum(if(s.csat_deployer_point in (1,2) and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeDeploy . "%',1,0)) as csat12_cbc_deploy,
                
                sum(if(s.section_survey_id = 2,1,0)) as csat_tong_maintain,
                sum(if(s.csat_maintenance_staff_point = 1 and s.section_survey_id = 2,1,0)) as csat1_maintain,
                sum(if(s.csat_maintenance_staff_point = 2 and s.section_survey_id in (1,6),1,0)) as csat2_maintain,
                sum(if(s.csat_maintenance_staff_point in (1,2) and s.section_survey_id in (1,6),1,0)) as csat12_maintain,
                sum(if(s.section_survey_id = 2,s.csat_maintenance_staff_point,0)) as csat12_diem_maintain,
                sum(if(s.csat_maintenance_staff_point = 1 and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeMaintaince . "%',1,0)) as csat1_cbc_maintain,
                sum(if(s.csat_maintenance_staff_point = 2 and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeMaintaince . "%',1,0)) as csat2_cbc_maintain,
                sum(if(s.csat_maintenance_staff_point in (1,2) and s.section_survey_id in (1,6) and s.violation_status like '%" . $likeMaintaince . "%',1,0)) as csat12_cbc_maintain
               ";
        $result = DB::table($this->table . ' as s')
                ->selectRaw($sqlRaw)
                ->whereIn('s.section_survey_id', [1, 2, 6])
                ->where('s.section_connected', '=', 4)
                ->whereNotNull('s.section_action')
                ->where('s.section_time_completed_int', '<=', strtotime($dayTo))
                ->where('s.section_time_completed_int', '>=', strtotime($day))
                ->groupBy('s.section_sub_parent_desc', 's.section_location_id', 's.section_branch_code')
                ->orderBy('s.section_sub_parent_desc', 's.section_location_id', 's.section_branch_code')
                ->get();
        return $result;
    }

    public function getSurveyReportCSATNet($day, $dayTo, $statuses, $actions) {
        $statusSql = "";
        $arrayKeySta = array_keys($statuses);
        foreach ($statuses as $key => $status) {
            $statusSql .= ",sum(if(s.csat_net_answer_extra_id = " . $key . " or s.csat_maintenance_net_answer_extra_id = " . $key . ",1,0)) as sta_" . $key;
        }
        $statusSql .= ",sum(if(s.csat_net_answer_extra_id in(" . implode(',', $arrayKeySta) . ") or s.csat_maintenance_net_answer_extra_id in(" . implode(',', $arrayKeySta) . "),1,0)) as sta_t";

        $actionSql = "";
        $arrayKeyAct = array_keys($actions);
        foreach ($actions as $key => $action) {
            $actionSql .= ",sum(if(s.result_action_net = " . $key . ",1,0)) as act_" . $key;
        }
        $actionSql .= ",sum(if(s.result_action_net in (" . implode(',', $arrayKeyAct) . "),1,0)) as act_t";

        $sqlRaw = "s.section_sub_parent_desc, s.section_location_id, s.section_location, convert(s.section_branch_code, unsigned integer) as section_branch_code,
                sum(if(s.csat_net_point = 1 and s.section_survey_id = 1,1,0)) as csat1_tk_dr,
                sum(if(s.csat_net_point = 2 and s.section_survey_id = 1,1,0)) as csat2_tk_dr,
                sum(if(s.csat_net_point in (1,2) and s.section_survey_id = 1,1,0)) as csat_t12_tk_dr,
                sum(if(s.section_survey_id = 1 and s.csat_net_point is not null,s.csat_net_point,0)) as csat_d_tk_dr,
                sum(if(s.section_survey_id = 1 and s.csat_net_point is not null,1, 0)) as csat_sl_tk_dr,
                
                sum(if(s.csat_net_point = 1 and s.section_survey_id = 6,1,0)) as csat1_tk_tl,
                sum(if(s.csat_net_point = 2 and s.section_survey_id = 6,1,0)) as csat2_tk_tl,
                sum(if(s.csat_net_point in (1,2) and s.section_survey_id = 6,1,0)) as csat_t12_tk_tl,
                sum(if(s.section_survey_id = 6 and s.csat_net_point is not null,s.csat_net_point,0)) as csat_d_tk_tl,
                sum(if(s.section_survey_id = 6 and s.csat_net_point is not null,1, 0)) as csat_sl_tk_tl,
                
                sum(if(s.csat_maintenance_net_point = 1 and s.section_supporter not like '%INDO%',1,0)) as csat1_tin,
                sum(if(s.csat_maintenance_net_point = 2 and s.section_supporter not like '%INDO%',1,0)) as csat2_tin,
                sum(if(s.csat_maintenance_net_point in (1,2) and s.section_supporter not like '%INDO%',1,0)) as csat_t12_tin,
                sum(if(s.section_survey_id = 2 and s.section_supporter not like '%INDO%' and s.csat_maintenance_net_point is not null,s.csat_maintenance_net_point,0)) as csat_d_tin,
                sum(if(s.section_survey_id = 2 and s.section_supporter not like '%INDO%' and s.csat_maintenance_net_point is not null,1, 0)) as csat_sl_tin,
                
                sum(if(s.csat_maintenance_net_point = 1 and s.section_supporter like '%INDO%',1,0)) as csat1_in,
                sum(if(s.csat_maintenance_net_point = 2 and s.section_supporter like '%INDO%',1,0)) as csat2_in,
                sum(if(s.csat_maintenance_net_point in (1,2) and s.section_supporter like '%INDO%',1,0)) as csat_t12_in,
                sum(if(s.section_survey_id = 2 and s.section_supporter like '%INDO%' and s.csat_maintenance_net_point is not null,s.csat_maintenance_net_point,0)) as csat_d_in,
                sum(if(s.section_survey_id = 2 and s.section_supporter like '%INDO%' and s.csat_maintenance_net_point is not null,1, 0)) as csat_sl_in,
                               
                sum(if(s.csat_net_point = 1 and s.section_survey_id = 3,1,0)) as csat1_mobi,
                sum(if(s.csat_net_point = 2 and s.section_survey_id = 3,1,0)) as csat2_mobi,
                sum(if(s.csat_net_point in (1,2) and s.section_survey_id = 3,1,0)) as csat_t12_mobi,
                sum(if(s.section_survey_id = 3 and s.csat_net_point is not null,s.csat_net_point,0)) as csat_d_mobi,
                sum(if(s.section_survey_id = 3 and s.csat_net_point is not null,1, 0)) as csat_sl_mobi,
                               
                sum(if(s.csat_net_point = 1 or s.csat_maintenance_net_point = 1,1,0)) as csat1_th,
                sum(if(s.csat_net_point = 2 or s.csat_maintenance_net_point = 2,1,0)) as csat2_th,
                sum(if( s.csat_maintenance_net_point in(1,2) or s.csat_net_point in (1,2), 1,0)) as csat_t12_th,
                sum(if(s.section_survey_id = 2 and s.csat_maintenance_net_point is not null,s.csat_maintenance_net_point,0)) + sum(if(s.section_survey_id in (1,6) and s.csat_net_point is not null,s.csat_net_point,0)) as csat_d_th,
                sum(if(s.section_survey_id in (1,2,6) and (s.csat_maintenance_net_point is not null or s.csat_net_point is not null),1,0)) as csat_sl_th";
        $result = DB::table($this->table . ' as s')
                ->selectRaw($sqlRaw . $statusSql . $actionSql)
                ->whereIn('s.section_survey_id', [1, 2, 3, 6])
                ->where('s.section_connected', '=', 4)
                ->whereNotNull('s.section_action')
                ->where('s.section_time_completed_int', '<=', strtotime($dayTo))
                ->where('s.section_time_completed_int', '>=', strtotime($day))
                ->groupBy('s.section_sub_parent_desc', 's.section_location_id', 's.section_branch_code')
                ->orderBy('s.section_sub_parent_desc', 's.section_location_id', 's.section_branch_code')
                ->get();
        return $result;
    }

    public function getSurveyReportCSATTv($day, $dayTo, $statuses, $actions) {
        $statusSql = "";
        $arrayKeySta = array_keys($statuses);
        foreach ($statuses as $key => $status) {
            $statusSql .= ",sum(if(s.csat_tv_answer_extra_id = " . $key . " or s.csat_maintenance_tv_answer_extra_id = " . $key . ",1,0)) as sta_" . $key;
        }
        $statusSql .= ",sum(if(s.csat_tv_answer_extra_id in(" . implode(',', $arrayKeySta) . ") or s.csat_maintenance_tv_answer_extra_id in(" . implode(',', $arrayKeySta) . "),1,0)) as sta_t";

        $actionSql = "";
        $arrayKeyAct = array_keys($actions);
        foreach ($actions as $key => $action) {
            $actionSql .= ",sum(if(s.result_action_tv = " . $key . ",1,0)) as act_" . $key;
        }
        $actionSql .= ",sum(if(s.result_action_tv in (" . implode(',', $arrayKeyAct) . "),1,0)) as act_t";

        $sqlRaw = "s.section_sub_parent_desc,s.section_location_id, s.section_location, convert(s.section_branch_code, unsigned integer) as section_branch_code,
                sum(if(s.csat_tv_point = 1 and s.section_survey_id = 1,1,0)) as csat1_tk_dr,
                sum(if(s.csat_tv_point = 2 and s.section_survey_id = 1,1,0)) as csat2_tk_dr,
                sum(if(s.csat_tv_point in (1,2) and s.section_survey_id = 1,1,0)) as csat_t12_tk_dr,
                sum(if(s.section_survey_id = 1 and s.csat_tv_point is not null,s.csat_tv_point,0)) as csat_d_tk_dr,
                sum(if(s.section_survey_id = 1 and s.csat_tv_point is not null,1, 0)) as csat_sl_tk_dr,
                
                sum(if(s.csat_tv_point = 1 and s.section_survey_id = 6,1,0)) as csat1_tk_tl,
                sum(if(s.csat_tv_point = 2 and s.section_survey_id = 6,1,0)) as csat2_tk_tl,
                sum(if(s.csat_tv_point in (1,2) and s.section_survey_id = 6,1,0)) as csat_t12_tk_tl,
                sum(if(s.section_survey_id = 6 and s.csat_tv_point is not null,s.csat_tv_point,0)) as csat_d_tk_tl,
                sum(if(s.section_survey_id = 6 and s.csat_tv_point is not null,1, 0)) as csat_sl_tk_tl,
                
                sum(if(s.csat_maintenance_tv_point = 1 and s.section_supporter not like '%INDO%',1,0)) as csat1_tin,
                sum(if(s.csat_maintenance_tv_point = 2 and s.section_supporter not like '%INDO%',1,0)) as csat2_tin,
                sum(if(s.csat_maintenance_tv_point in (1,2) and s.section_supporter not like '%INDO%',1,0)) as csat_t12_tin,
                sum(if(s.section_survey_id = 2 and s.section_supporter not like '%INDO%' and s.csat_maintenance_tv_point is not null,s.csat_maintenance_tv_point,0)) as csat_d_tin,
                sum(if(s.section_survey_id = 2 and s.section_supporter not like '%INDO%' and s.csat_maintenance_tv_point is not null,1, 0)) as csat_sl_tin,
                
                sum(if(s.csat_maintenance_tv_point = 1 and s.section_supporter like '%INDO%',1,0)) as csat1_in,
                sum(if(s.csat_maintenance_tv_point = 2 and s.section_supporter like '%INDO%',1,0)) as csat2_in,
                sum(if(s.csat_maintenance_tv_point in (1,2) and s.section_supporter like '%INDO%',1,0)) as csat_t12_in,
                sum(if(s.section_survey_id = 2 and s.section_supporter like '%INDO%' and s.csat_maintenance_tv_point is not null,s.csat_maintenance_tv_point,0)) as csat_d_in,
                sum(if(s.section_survey_id = 2 and s.section_supporter like '%INDO%' and s.csat_maintenance_tv_point is not null,1, 0)) as csat_sl_in,
                               
                sum(if(s.csat_net_point = 1 and s.section_survey_id = 3,1,0)) as csat1_mobi,
                sum(if(s.csat_net_point = 2 and s.section_survey_id = 3,1,0)) as csat2_mobi,
                sum(if(s.csat_net_point in (1,2) and s.section_survey_id = 3,1,0)) as csat_t12_mobi,
                sum(if(s.section_survey_id = 3 and s.csat_net_point is not null,s.csat_net_point,0)) as csat_d_mobi,
                sum(if(s.section_survey_id = 3 and s.csat_net_point is not null,1, 0)) as csat_sl_mobi,
                               
                sum(if(s.csat_tv_point = 1 or s.csat_maintenance_tv_point = 1,1,0)) as csat1_th,
                sum(if(s.csat_tv_point = 2 or s.csat_maintenance_tv_point = 2,1,0)) as csat2_th,
                sum(if( s.csat_maintenance_tv_point in(1,2) or s.csat_tv_point in (1,2), 1,0)) as csat_t12_th,
                sum(if(s.section_survey_id = 2 and s.csat_maintenance_tv_point is not null,s.csat_maintenance_tv_point,0)) + sum(if(s.section_survey_id in (1,6) and s.csat_tv_point is not null,s.csat_tv_point,0)) as csat_d_th,
                sum(if(s.section_survey_id in (1,2,6) and (s.csat_maintenance_tv_point is not null or s.csat_tv_point is not null),1,0)) as csat_sl_th";
        $result = DB::table($this->table . ' as s')
                ->selectRaw($sqlRaw . $statusSql . $actionSql)
                ->whereIn('s.section_survey_id', [1, 2, 3, 6])
                ->where('s.section_connected', '=', 4)
                ->whereNotNull('s.section_action')
                ->where('s.section_time_completed_int', '<=', strtotime($dayTo))
                ->where('s.section_time_completed_int', '>=', strtotime($day))
                ->groupBy('s.section_sub_parent_desc', 's.section_location_id', 's.section_branch_code')
                ->orderBy('s.section_sub_parent_desc', 's.section_location_id', 's.section_branch_code')
                ->get();
        return $result;
    }

    public function getSurveyReportCSATByDay($day) {
        $result = DB::table($this->table . ' as s')
                ->join('outbound_survey_sections as os', 'os.section_id', '=', 's.section_id')
                ->select('s.section_id', 's.section_survey_id as loaiKhaoSat', 's.section_location_id as soViTri',
                    's.section_location as tenViTri', 's.section_branch_code as chiNhanh', 's.section_sub_parent_desc as vung',
                    's.section_contract_num as soHopDong', 'os.section_objAddress as diaChiKhachHang',
                    's.section_contact_phone as dienThoaiKhachHang', 's.section_acc_sale as nhanVienKinhDoanh',
                    's.section_supporter', 's.section_subsupporter', 's.csat_maintenance_tv_point', 's.csat_maintenance_net_point',
                    's.csat_tv_point', 's.csat_net_point', 's.csat_net_answer_extra_id', 's.csat_tv_answer_extra_id',
                    's.csat_maintenance_net_answer_extra_id', 's.csat_maintenance_tv_answer_extra_id', 's.csat_net_note',
                    's.csat_tv_note', 's.csat_maintenance_net_note', 's.csat_maintenance_tv_note',
                    's.section_time_completed as thoiGianGhiNhan', 's.section_action', 's.result_action_net', 's.result_action_tv'
                )
                ->whereIn('s.section_survey_id', [1, 2, 3, 6])
                ->where('s.section_time_completed_int', '<=', strtotime($day . ' 23:59:59'))
                ->where('s.section_time_completed_int', '>=', strtotime($day . ' 00:00:00'))
                ->where('s.section_connected', '=', 4)
                ->whereRaw('(s.csat_maintenance_tv_point in (1,2) or s.csat_maintenance_net_point in (1,2) or s.csat_tv_point in (1,2) or s.csat_net_point in (1,2))')
                ->whereRaw('(s.csat_net_answer_extra_id <> 88 or s.csat_maintenance_net_answer_extra_id <> 88)')
                ->get();
        return $result;
    }

    public function getSurveyReportCSATByWeek($day, $dayTo) {
        $result = DB::table($this->table . ' as s')
                ->select('s.section_id', 's.section_survey_id as loaiKhaoSat', 's.section_location_id as soViTri',
                    's.section_location as tenViTri', 's.section_branch_code as chiNhanh', 's.section_sub_parent_desc as vung',
                    's.section_contract_num as soHopDong', 's.section_contact_phone as dienThoaiKhachHang',
                    's.section_acc_sale as nhanVienKinhDoanh', 's.section_supporter', 's.section_subsupporter',
                    's.csat_maintenance_tv_point', 's.csat_maintenance_net_point', 's.csat_tv_point',
                    's.csat_net_point', 's.csat_net_answer_extra_id', 's.csat_tv_answer_extra_id',
                    's.csat_maintenance_net_answer_extra_id', 's.csat_maintenance_tv_answer_extra_id',
                    's.section_time_completed as thoiGianGhiNhan', 's.section_action', 's.result_action_net', 's.result_action_tv'
                )
                ->whereIn('s.section_survey_id', [1, 2, 6])
                ->where('s.section_time_completed_int', '<=', strtotime($dayTo . ' 23:59:59'))
                ->where('s.section_time_completed_int', '>=', strtotime($day . ' 00:00:00'))
                ->where('s.section_connected', '=', 4)
                ->get();
        return $result;
    }

    public function getSurveyReportCSATTin($day, $dayTo, $loc) {
        $result = DB::table($this->table . ' as s')
                ->select('s.section_id', 's.section_location_id as soViTri', 's.section_location as tenViTri', 's.section_branch_code as chiNhanh', 's.section_sub_parent_desc as vung', 's.section_contract_num as soHopDong', 's.section_supporter', 's.section_subsupporter', 's.csat_maintenance_tv_point', 's.csat_maintenance_net_point', 's.csat_tv_point', 's.csat_net_point', 's.section_time_completed as thoiGianGhiNhan'
                )
                ->whereIn('s.section_survey_id', [1, 2])
                ->whereIn('s.section_location_id', $loc)
                ->where('s.section_time_completed', '<=', $dayTo)
                ->where('s.section_time_completed', '>=', $day)
                ->where('s.section_connected', '=', 4)
                ->whereRaw('s.section_supporter not like "%INDO%"')
                ->orderBy('s.section_time_completed', 'DESC')
                ->get();
        return $result;
    }

 

}
