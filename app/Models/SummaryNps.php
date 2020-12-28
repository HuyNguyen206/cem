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

class SummaryNps extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_nps';
    protected $fillable = ['time_id', 'object_id', 'branch_id', 'channel_id', 'poc_id', 'nps_1', 'nps_2', 'nps_3', 'nps_4', 'nps_5', 'nps_6', 'nps_7', 'nps_8', 'nps_9', 'nps_10', 'nps_0'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getNpsByTime($fromDay, $toDay) {
        $resultCsat = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->select(DB::raw(' (((sum(sn.nps_9)+sum(sn.nps_10)) - (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6))) 
 /
 (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)+sum(sn.nps_9)+sum(sn.nps_10)+sum(sn.nps_7)+sum(sn.nps_8)) ) * 100
                        "Percent"

                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($fromDay))
                ->where('st.time_temp', '<=', strtotime($toDay))
//                ->groupBy(DB::raw('sc.poc_id, sc.object_id'))
                ->get();
        return $resultCsat[0]->Percent;
    }



    public function getNpsReportSummaryNps($dayFrom, $dayTo) {
//          DB::enableQueryLog();
        $resultNpsSurvey = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->select(DB::raw('case 
when sn.poc_id=1 and sn.object_id=10 then "SauTK"
when sn.poc_id=2 and sn.object_id=25 then "SauBTTIN"
when sn.poc_id=2 and sn.object_id=26 then "SauBTINDO"
when sn.poc_id=3 and sn.object_id=10 then "SauTC"
when sn.poc_id=6 and sn.object_id=10 then "SauTKTS"
when sn.poc_id=4 and sn.object_id=10 then "SauGDTQ"
when sn.poc_id=9 and sn.object_id=10 then "SauTKS"
when sn.poc_id=10 and sn.object_id=10 then "SauSSW"
else concat(sn.poc_id, sn.object_id)
end as "LoaiKhaoSat",
sum(sn.nps_0) as nps0,
sum(sn.nps_1) as nps1,
sum(sn.nps_2) as nps2,
sum(sn.nps_3) as nps3,
sum(sn.nps_4) as nps4,
sum(sn.nps_5) as nps5,
sum(sn.nps_6) as nps6,
sum(sn.nps_7) as nps7,
sum(sn.nps_8) as nps8,
sum(sn.nps_9) as nps9,
sum(sn.nps_10) as nps10,
sum(sn.nps_10) + sum(sn.nps_9)+sum(sn.nps_8)+sum(sn.nps_7)+sum(sn.nps_6)+sum(sn.nps_5)+sum(sn.nps_4)+sum(sn.nps_3)+sum(sn.nps_2)+sum(sn.nps_1)+sum(sn.nps_0) as TongCong

                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->groupBy(DB::raw('sn.poc_id, sn.object_id'))
                ->get();
//                $query=  DB::getQueryLog();
//                dump($query);die;
        return $resultNpsSurvey;
    }

    public function getNpsByRegionBranch($dayFrom, $dayTo) {
        $resultNpsByRegion = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('concat("Vùng ",sb.zone_id) "Vung",
round((((sum(sn.nps_9)+ sum(sn.nps_10)) - (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)))
/ (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)+sum(sn.nps_7)+sum(sn.nps_8)+sum(sn.nps_9)+sum(sn.nps_10))) * 100,2) "PhanTramNPS"

                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->groupBy(DB::raw(' sb.zone_id'))
                ->get();
        $result['npsRegion'] = $resultNpsByRegion;
        $resultNpsByBranch = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('concat("Vùng ",sb.zone_id) "Vung",
concat(sb.branch_code," - ",sb.branch_name) "ChiNhanh",
round((((sum(sn.nps_9)+ sum(sn.nps_10)) - (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)))
/ (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)+sum(sn.nps_7)+sum(sn.nps_8)+sum(sn.nps_9)+sum(sn.nps_10))) * 100,2) "PhanTramNPS"

                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->groupBy(DB::raw('sb.zone_id, sb.branch_code'))
                ->get();
        $result['npsBranches'] = $resultNpsByBranch;
        $resultNpsByContry = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"Toàn quốc" as "Vung",
round((((sum(sn.nps_9)+ sum(sn.nps_10)) - (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)))
/ (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)+sum(sn.nps_7)+sum(sn.nps_8)+sum(sn.nps_9)+sum(sn.nps_10))) * 100,2) "PhanTramNPS"


                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->get();
        $result['npsCountryBranches'] = $resultNpsByContry;
        return $result;
    }

    public function getTableColumns() {
        $result = DB::getSchemaBuilder()->getColumnListing($this->table);
        return $result;
    }

    public function getFieldName() {
        return $this->fillable;
    }

    public function getNPSStatisticReportByRegion($region, $from_date, $to_date, $branch, $branchcode = []) {
        $result = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('concat("Vung ", sb.zone_id) "Vung",
sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6) "KhongUngHo",

sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTK",

sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTKTS",

sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_6,0)) "KhongUngHoTINPNC",

sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_6,0)) "KhongUngHoINDO",

sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTC",

sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoSauGDTQ",

sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTKS",

sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoSSW",

sum(sn.nps_7)+sum(sn.nps_8) "TrungLap",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_8,0))  "TrungLapTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_8,0))  "TrungLapTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_8,0))  "TrungLapTINPNC",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_8,0))  "TrungLapINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_8,0))  "TrungLapTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_8,0))  "TrungLapSauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_8,0))  "TrungLapTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_8,0))  "TrungLapSSW",

sum(sn.nps_9)+sum(sn.nps_10) "UngHo",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_10,0))  "UngHoTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_10,0))  "UngHoTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_10,0))  "UngHoTINPNC",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_10,0))  "UngHoINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_10,0))  "UngHoTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_10,0))  "UngHoSauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_10,0))  "UngHoTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_10,0))  "UngHoSSW",

sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)
+ sum(sn.nps_7)+sum(sn.nps_8) +  sum(sn.nps_9)+sum(sn.nps_10) "TongCong",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_10,0)) "TongCongTK",

sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_6,0))+
 sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_8,0))
 + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_10,0)) "TongCongTKTS",
 
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_6,0))
+ sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_8,0)) 
+  sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_10,0))  "TongCongTINPNC",

sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_6,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_8,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_10,0)) "TongCongINDO",

sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_8,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_10,0))  "TongCongTC",

sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_8,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_10,0))  "TongCongSauGDTQ",

sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_10,0)) "TongCongTKS",

sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_10,0)) "TongCongSSW"

                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
                    if (count($branchcode) > 0) {
                        foreach ($branchcode as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_branch_code', $b);
                            }
                        }
                    }
                })
                ->groupBy(DB::raw('sb.zone_id'))
                ->get();
//               $query= DB::getQueryLog();
//               dump($query);die;
        return $result;
    }

    public function getNPSStatisticReportByBranches($region, $from_date, $to_date, $limit, $branch, $branchcode = []) {

        $result = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('concat("Vung ", sb.zone_id) "Vung",
concat(sb.branch_code,"-",sb.branch_name)	"ChiNhanh",
sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6) "KhongUngHo",

sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTK",

sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTKTS",

sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_6,0)) "KhongUngHoTINPNC",

sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_6,0)) "KhongUngHoINDO",

sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTC",

sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoSauGDTQ",

sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTKS",

sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_6,0)) "KhongUngHoSSW",


sum(sn.nps_7)+sum(sn.nps_8) "TrungLap",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_8,0))  "TrungLapTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_8,0))  "TrungLapTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_8,0))  "TrungLapTINPNC",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_8,0))  "TrungLapINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_8,0))  "TrungLapTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_8,0))  "TrungLapSauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_8,0))  "TrungLapTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_8,0))  "TrungLapSSW",

sum(sn.nps_9)+sum(sn.nps_10) "UngHo",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_10,0))  "UngHoTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_10,0))  "UngHoTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_10,0))  "UngHoTINPNC",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_10,0))  "UngHoINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_10,0))  "UngHoTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_10,0))  "UngHoSauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_10,0))  "UngHoTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_10,0))  "UngHoSSW",

sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)
+ sum(sn.nps_7)+sum(sn.nps_8) +  sum(sn.nps_9)+sum(sn.nps_10) "TongCong",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_10,0)) "TongCongTK",

sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_6,0))+
 sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_8,0))
 + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_10,0)) "TongCongTKTS",
 
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_6,0))
+ sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_8,0)) 
+  sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_10,0))  "TongCongTINPNC",

sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_6,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_8,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_10,0)) "TongCongINDO",

sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_8,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_10,0))  "TongCongTC",

sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_8,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_10,0))  "TongCongSauGDTQ",

sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_10,0)) "TongCongTKS",

sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_10,0)) "TongCongSSW",

round((((sum(sn.nps_9)+sum(sn.nps_10)) - (sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)))/(sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)
+ sum(sn.nps_7)+sum(sn.nps_8) +  sum(sn.nps_9)+sum(sn.nps_10))) * 100,2) "NPS"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
                    if (count($branchcode) > 0) {
                        foreach ($branchcode as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_branch_code', $b);
                            }
                        }
                    }
                })
                ->groupBy(DB::raw('sb.zone_id,sb.branch_code'))
                ->get();
//               $query= DB::getQueryLog();
//               dump($query);die;
        return $result;
    }

    public function getNPSStatisticReportByAll($from_date, $to_date) {
        $result = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw(' "ToanQuoc" as "Vung",
sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6) "KhongUngHo",

sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTK",

sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTKTS",

sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_6,0)) "KhongUngHoTINPNC",

sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_6,0)) "KhongUngHoINDO",

sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTC",

sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoSauGDTQ",

sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_6,0)) "KhongUngHoTKS",

sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=10  and sn.object_id=10,sn.nps_6,0)) "KhongUngHoSSW",

sum(sn.nps_7)+sum(sn.nps_8) "TrungLap",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_8,0))  "TrungLapTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_8,0))  "TrungLapTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_8,0))  "TrungLapTINPNC",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_8,0))  "TrungLapINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_8,0))  "TrungLapTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_8,0))  "TrungLapSauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_8,0))  "TrungLapTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_8,0))  "TrungLapSSW",

sum(sn.nps_9)+sum(sn.nps_10) "UngHo",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_10,0))  "UngHoTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_10,0))  "UngHoTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_10,0))  "UngHoTINPNC",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_10,0))  "UngHoINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_10,0))  "UngHoTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_10,0))  "UngHoSauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_10,0))  "UngHoTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_10,0))  "UngHoSSW",

sum(sn.nps_0)+sum(sn.nps_1)+sum(sn.nps_2)+sum(sn.nps_3)+sum(sn.nps_4)+sum(sn.nps_5)+sum(sn.nps_6)
+ sum(sn.nps_7)+sum(sn.nps_8) +  sum(sn.nps_9)+sum(sn.nps_10) "TongCong",

sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_10,0)) "TongCongTK",

sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_6,0))+
 sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_8,0))
 + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_10,0)) "TongCongTKTS",
 
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_6,0))
+ sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_8,0)) 
+  sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_10,0))  "TongCongTINPNC",

sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_1,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_2,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_3,0)) + sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_5,0))+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_6,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_8,0))
+sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_10,0)) "TongCongINDO",

sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_8,0))
+sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_10,0))  "TongCongTC",

sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_8,0))
+sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_10,0))  "TongCongSauGDTQ",

sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=9  and sn.object_id=10,sn.nps_10,0)) "TongCongTKS",

sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_1,0))+sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_2,0))
+sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_5,0))+sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_6,0))
+ sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_8,0))
+ sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=10   and sn.object_id=10,sn.nps_10,0)) "TongCongSSW"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('sb.zone_id', '=', $reg);
//                        }
//                    }
//                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('sb.isc_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('sb.isc_branch_code', $b);
//                            }
//                        }
//                    }
//                })
//                ->groupBy(DB::raw('sb.zone_id'))
                ->get();
//               $query= DB::getQueryLog();
//               dump($query);die;
        return $result;
    }

    public function getNPSStatisticReport($region, $from_date, $to_date, $branch, $branchcode) {
        $nps0 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"0" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_0,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_0,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_0,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_0,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_0,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_0,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_0,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_0,0)) "SauSSW",

sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_0,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_0,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_0,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_0,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_0,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_0,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_0,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_0,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });

        $nps1 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"1" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_1,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_1,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_1,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_1,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_1,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_1,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_1,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_1,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_1,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_1,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_1,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_1,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_1,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_1,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_1,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_1,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps2 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"2" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_2,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_2,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_2,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_2,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_2,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_2,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_2,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_2,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_2,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_2,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_2,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_2,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_2,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_2,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_2,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_2,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps3 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"3" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_3,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_3,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_3,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_3,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_3,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_3,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_3,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_3,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_3,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_3,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_3,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_3,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_3,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_3,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps4 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"4" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_4,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_4,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_4,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_4,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_4,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_4,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_4,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_4,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_4,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_4,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_4,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_4,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_4,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_4,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_4,0))  "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps5 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"5" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_5,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_5,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_5,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_5,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_5,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_5,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_5,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_5,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_5,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_5,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_5,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_5,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_5,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_5,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_5,0)) +sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_5,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps6 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"6" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_6,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_6,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_6,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_6,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_6,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_6,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_6,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_6,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_6,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_6,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_6,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_6,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_6,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_6,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_6,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_6,0))  "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps7 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"7" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_7,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_7,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_7,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_7,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_7,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_7,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_7,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_7,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_7,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_7,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_7,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_7,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_7,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_7,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_7,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_7,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps8 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"8" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_8,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_8,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_8,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_8,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_8,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_8,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_8,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_8,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_8,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_8,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_8,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_8,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_8,0)) +sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_8,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_8,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_8,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps9 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"9" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_9,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_9,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_9,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_9,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_9,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_9,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_9,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_9,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_9,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_9,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_9,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_9,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_9,0)) +sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_9,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_9,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
            if (count($branchcode) > 0) {
                foreach ($branchcode as $b) {
                    if (!empty($b)) {
                        $b = explode(',', $b);
                        $query->whereIn('sb.isc_branch_code', $b);
                    }
                }
            }
        });
        $nps10 = DB::table('summary_nps as sn')
                ->join('summary_time as st', 'sn.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sn.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"10" as "answers_point",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_10,0)) "SauTK",
sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_10,0)) "SauTKTS",
sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_10,0)) "SauBTTIN",
sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_10,0)) "SauBTINDO",
sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_10,0)) "SauTC",
sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_10,0)) "SauGDTQ",
sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_10,0)) "SauTKS",
sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_10,0)) "SauSSW",
sum(if(sn.poc_id=1 and sn.object_id=10,sn.nps_10,0)) + sum(if(sn.poc_id=6 and sn.object_id=10,sn.nps_10,0)) +sum(if(sn.poc_id=2 and sn.object_id=25,sn.nps_10,0))
+ sum(if(sn.poc_id=2 and sn.object_id=26,sn.nps_10,0)) + sum(if(sn.poc_id=3 and sn.object_id=10,sn.nps_10,0)) + sum(if(sn.poc_id=4 and sn.object_id=10,sn.nps_10,0)) + sum(if(sn.poc_id=9 and sn.object_id=10,sn.nps_10,0)) + sum(if(sn.poc_id=10 and sn.object_id=10,sn.nps_10,0)) "TongCong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('sb.zone_id', '=', $reg);
                        }
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
                    if (count($branchcode) > 0) {
                        foreach ($branchcode as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('sb.isc_branch_code', $b);
                            }
                        }
                    }
                })
                ->union($nps0)
                ->union($nps1)
                ->union($nps2)
                ->union($nps3)
                ->union($nps4)
                ->union($nps5)
                ->union($nps6)
                ->union($nps7)
                ->union($nps8)
                ->union($nps9)
                ->get();
//               $query= DB::getQueryLog();
//               dump($query);die;
        return $nps10;
    }

}
