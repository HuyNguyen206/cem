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

class SummaryCsat extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_csat';
    protected $fillable = ['time_id', 'object_id', 'branch_id', 'channel_id', 'poc_id', 'csat_1', 'csat_2', 'csat_3', 'csat_4', 'csat_5'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getCsatSummaryByMonth($firstDay, $lastDay) {
        $result = DB::table('summary_csat AS sc')
                ->join('summary_time AS st', 'sc.time_id', '=', 'r.id')
                ->where('sc.time_temp', '>', strtotime($firstDay))
                ->where('sc.time_temp', '<', strtotime($lastDay))
//                ->where('s.section_survey_id', '=', $k)
//                ->whereIn('q.question_id', $questionList)
                ->groupBy('sc.poc_id', 'sc.object_id')
                ->select(DB::raw('sc.poc_id ,sc.object_id,
                        
                        SUM(case when a.answers_point = 0 then 1 else 0 end) as csat_0,
                        SUM(case when a.answers_point = 1 then 1 else 0 end) as csat_1,
                        SUM(case when a.answers_point = 2 then 1 else 0 end) as csat_2,
                        SUM(case when a.answers_point = 3 then 1 else 0 end) as csat_3,
                        SUM(case when a.answers_point = 4 then 1 else 0 end) as csat_4,
                        SUM(case when a.answers_point = 5 then 1 else 0 end) as csat_5'
                ))
                ->get();
    }

    public function getCsatByTime($fromDay, $toDay) {
        $result = DB::table('outbound_survey_sections AS os')
                ->join('outbound_survey_result AS osr', 'osr.survey_result_section_id', '=', 'os.section_id')
                ->select(DB::raw(' case 
                                when os.section_survey_id=1 then "STK"
                                when os.section_survey_id=2 then "SBT"
                                else ""
                                end as "LoaiKhaoSat",
                                case
                                when osr.survey_result_question_id=1 then "NVKD_STK"
                                when osr.survey_result_question_id=2 then "NVTK_STK"
                                when osr.survey_result_question_id=5 then "Internet_STK"
                                
                                when osr.survey_result_question_id=6 then "NVBT"
                                when osr.survey_result_question_id=9 then "Internet_SBT"
                                else "" end as  "LoaiDoiTuong", 
                                round(sum(osr.survey_result_answer_id)/sum(if(osr.survey_result_answer_id !=-1,1,0)),2) "ĐTB"  

                        '))
                ->where('os.section_time_completed_int', '>=', strtotime($fromDay))
                ->where('os.section_time_completed_int', '<=', strtotime($toDay))
                ->where('osr.survey_result_answer_id', '<>', -1)//group độ hài lòng
                ->whereIn('osr.survey_result_question_id', [1, 2, 5, 6, 9])
                ->whereIn('os.section_survey_id', [1, 2])
                ->groupBy(DB::raw('os.section_survey_id, osr.survey_result_question_id'))
                ->get();


        return $result;
//                  dump($resultTIN_PN_INDO_ALL);die;
    }

    public function getCsatFromSummaryCsat($dayFrom, $dayTo) {
        $resultCsat = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->select(DB::raw(' case
                        when sc.poc_id=1 then "STK"
                        when sc.poc_id=3 or sc.poc_id=7  then "TC"
                        when sc.poc_id=6 then "STKTS"
                        when sc.poc_id=2 and sc.object_id in (16,23,24) then "TIN_PN"
                        when sc.poc_id=2 and  sc.object_id in (15,21,22) then "INDO"
                        when sc.poc_id=4 then "GDTQ"
                        when sc.poc_id=9 then "STKS"
                          when sc.poc_id=10 then "SSW"
                        else sc.poc_id
                        end as "LoaiKhaoSat",
                        case
                        when sc.object_id=1 and sc.poc_id=1  then "NVKD_STK"
                        when sc.object_id=3 and sc.poc_id=1 then "NVTK_STK"
                        when sc.object_id=5 and sc.poc_id=1 then "Internet_STK"
                        when sc.object_id=6 and sc.poc_id=1 then "TV_STK"

                        when sc.object_id=5 and sc.poc_id=3 then "Internet_TC"
                        when sc.object_id=6 and sc.poc_id=3 then "TV_TC"
                        when sc.object_id=14 and sc.poc_id=7 then "NVTC_TC"

                        when sc.object_id=2 and sc.poc_id=6  then "NVKD_STKTS"
                        when sc.object_id=3 and sc.poc_id=6 then "NVTK_STKTS"
                        when sc.object_id=5 and sc.poc_id=6 then "Internet_STKTS"
                        when sc.object_id=6 and sc.poc_id=6 then "TV_STKTS"

                        when sc.object_id=16 and sc.poc_id=2 then "NVBT_TIN_PN"
                        when sc.object_id=23 and sc.poc_id=2 then "Internet_TIN_PN"
                        when sc.object_id=24 and sc.poc_id=2 then "TV_TIN_PN"

                        when sc.object_id=15 and sc.poc_id=2 then "NVBT_INDO"
                        when sc.object_id=21 and sc.poc_id=2 then "Internet_INDO"
                        when sc.object_id=22 and sc.poc_id=2 then "TV_INDO"
                        
                        when sc.object_id=7 and sc.poc_id=4 then "DV_GDTQ"
                        when sc.object_id=8 and sc.poc_id=4 then "NV_GDTQ"
                        
                        when sc.object_id=29 and sc.poc_id=9  then "NVKD_SS"
                        when sc.object_id=3 and sc.poc_id=9 then "NVTK_SS"
                        when sc.object_id=5 and sc.poc_id=9 then "Internet_SS"
                        when sc.object_id=6 and sc.poc_id=9 then "TV_SS"
                        
                        when sc.object_id=30 and sc.poc_id=10 then "NVBT_SSW"
                        when sc.object_id=5 and sc.poc_id=10 then "Internet_SSW"
                        when sc.object_id=6 and sc.poc_id=10 then "TV_SSW"
                        
                        else concat(sc.poc_id,"-",sc.object_id)
                        end as  "LoaiDoiTuong",
                        round((sum(sc.csat_1)+2*sum(sc.csat_2)+3*sum(sc.csat_3)+4*sum(sc.csat_4)+5*sum(sc.csat_5))
                        /(sum(sc.csat_1)+sum(sc.csat_2)+sum(sc.csat_3)+sum(sc.csat_4)+sum(sc.csat_5)),2)
                        "ĐTB"

                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->groupBy(DB::raw('sc.poc_id, sc.object_id'))
                ->get();
        return $resultCsat;
    }

    public function getCsatReportSummaryCsat($dayFrom, $dayTo) {
        $resultCsatSurvey = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->select(DB::raw('case 
                when sc.object_id=1 and sc.poc_id=1 then "NVKinhDoanh"
                when sc.object_id=3 and sc.poc_id=1 then "NVTrienKhai"
                when sc.object_id=5 and sc.poc_id=1 then "DGDichVu_Net"
                when sc.object_id=6 and sc.poc_id=1 then "DGDichVu_TV"

                when sc.object_id=2 and sc.poc_id=6 then "NVKinhDoanhTS"
                when sc.object_id=3 and sc.poc_id=6 then "NVTrienKhaiTS"
                when sc.object_id=5 and sc.poc_id=6 then "DGDichVuTS_Net"
                when sc.object_id=6 and sc.poc_id=6 then "DGDichVuTS_TV"

                when sc.object_id=16 and sc.poc_id=2 then "NVBaoTriTIN"
                when sc.object_id=23 and sc.poc_id=2 then "DVBaoTriTIN_Net"
                when sc.object_id=24 and sc.poc_id=2 then "DVBaoTriTIN_TV"

                when sc.object_id=15 and sc.poc_id=2 then "NVBaoTriINDO"
                when sc.object_id=21 and sc.poc_id=2 then "DVBaoTriINDO_Net"
                when sc.object_id=22 and sc.poc_id=2 then "DVBaoTriINDO_TV"
                
                when sc.object_id=14 and sc.poc_id=7 then "NVThuCuoc"
                when sc.object_id=5 and sc.poc_id=3 then "DGDichVu_MobiPay_Net"
                when sc.object_id=6 and sc.poc_id=3 then "DGDichVu_MobiPay_TV"
                
                when sc.object_id=7 and sc.poc_id=4 then "DGDichVu_Counter"
                when sc.object_id=8 and sc.poc_id=4 then "NV_Counter"
                
                when sc.object_id=29 and sc.poc_id=9 then "NVKinhDoanhSS"
                when sc.object_id=3 and sc.poc_id=9 then "NVTrienKhaiSS"
                when sc.object_id=5 and sc.poc_id=9 then "DGDichVuSS_Net"
                when sc.object_id=6 and sc.poc_id=9 then "DGDichVuSS_TV"
                
                when sc.object_id=30 and sc.poc_id=10 then "NVBT_SSW"
                when sc.object_id=5 and sc.poc_id=10 then "DGDichVuSSW_Net"
                when sc.object_id=6 and sc.poc_id=10 then "DGDichVuSSW_TV"
                

                else concat(sc.object_id,sc.poc_id)
                end as "LoaiDoiTuong",
                sum(sc.csat_1) as "SoLuong_Csat1",
                sum(sc.csat_2) as "SoLuong_Csat2",
                sum(sc.csat_3) as "SoLuong_Csat3",
                sum(sc.csat_4) as "SoLuong_Csat4",
                sum(sc.csat_5) as "SoLuong_Csat5"

                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->groupBy(DB::raw('sc.poc_id, sc.object_id'))
                ->get();
        $result['survey'] = $resultCsatSurvey;
        $resultCsatAvg = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->select(DB::raw('case 
                    when sc.object_id=1 and sc.poc_id=1 then "NVKinhDoanh"
                    when sc.object_id=3 and sc.poc_id=1 then "NVTrienKhai"
                    when sc.object_id=5 and sc.poc_id=1 then "DGDichVu_Net"
                    when sc.object_id=6 and sc.poc_id=1 then "DGDichVu_TV"

                    when sc.object_id=2 and sc.poc_id=6 then "NVKinhDoanhTS"
                    when sc.object_id=3 and sc.poc_id=6 then "NVTrienKhaiTS"
                    when sc.object_id=5 and sc.poc_id=6 then "DGDichVuTS_Net"
                    when sc.object_id=6 and sc.poc_id=6 then "DGDichVuTS_TV"

                    when sc.object_id=16 and sc.poc_id=2 then "NVBaoTriTIN"
                    when sc.object_id=23 and sc.poc_id=2 then "DVBaoTriTIN_Net"
                    when sc.object_id=24 and sc.poc_id=2 then "DVBaoTriTIN_TV"

                    when sc.object_id=15 and sc.poc_id=2 then "NVBaoTriINDO"
                    when sc.object_id=21 and sc.poc_id=2 then "DVBaoTriINDO_Net"
                    when sc.object_id=22 and sc.poc_id=2 then "DVBaoTriINDO_TV"

                    when sc.object_id=14 and sc.poc_id=7 then "NVThuCuoc"
                    when sc.object_id=5 and sc.poc_id=3 then "DGDichVu_MobiPay_Net"
                    when sc.object_id=6 and sc.poc_id=3 then "DGDichVu_MobiPay_TV"
                
                    when sc.object_id=7 and sc.poc_id=4 then "DGDichVu_Counter"
                    when sc.object_id=8 and sc.poc_id=4 then "NV_Counter"
                
                    when sc.object_id=29 and sc.poc_id=9 then "NVKinhDoanhSS"
                    when sc.object_id=3 and sc.poc_id=9 then "NVTrienKhaiSS"
                    when sc.object_id=5 and sc.poc_id=9 then "DGDichVuSS_Net"
                    when sc.object_id=6 and sc.poc_id=9 then "DGDichVuSS_TV"
                    
                    when sc.object_id=30 and sc.poc_id=10 then "NVBT_SSW"
                    when sc.object_id=5 and sc.poc_id=10 then "DGDichVuSSW_Net"
                    when sc.object_id=6 and sc.poc_id=10 then "DGDichVuSSW_TV"        

                    else concat(sc.object_id,sc.poc_id)
                    end as "LoaiDoiTuong",
                    round((sum(sc.csat_1)+sum(sc.csat_2)*2+sum(sc.csat_3)*3+sum(sc.csat_4)*4+sum(sc.csat_5)*5)
                    /(sum(sc.csat_1)+sum(sc.csat_2)+sum(sc.csat_3)+sum(sc.csat_4)+sum(sc.csat_5)),2) as "DTB"

                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->groupBy(DB::raw('sc.poc_id, sc.object_id'))
                ->get();
        $result['avg'] = $resultCsatAvg;
        $resultCsatTotal = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->select(DB::raw('case 
                when sc.object_id=1 and sc.poc_id=1 then "NVKinhDoanh"
                when sc.object_id=3 and sc.poc_id=1 then "NVTrienKhai"
                when sc.object_id=5 and sc.poc_id=1 then "DGDichVu_Net"
                when sc.object_id=6 and sc.poc_id=1 then "DGDichVu_TV"

                when sc.object_id=2 and sc.poc_id=6 then "NVKinhDoanhTS"
                when sc.object_id=3 and sc.poc_id=6 then "NVTrienKhaiTS"
                when sc.object_id=5 and sc.poc_id=6 then "DGDichVuTS_Net"
                when sc.object_id=6 and sc.poc_id=6 then "DGDichVuTS_TV"

                when sc.object_id=16 and sc.poc_id=2 then "NVBaoTriTIN"
                when sc.object_id=23 and sc.poc_id=2 then "DVBaoTriTIN_Net"
                when sc.object_id=24 and sc.poc_id=2 then "DVBaoTriTIN_TV"

                when sc.object_id=15 and sc.poc_id=2 then "NVBaoTriINDO"
                when sc.object_id=21 and sc.poc_id=2 then "DVBaoTriINDO_Net"
                when sc.object_id=22 and sc.poc_id=2 then "DVBaoTriINDO_TV"

                when sc.object_id=14 and sc.poc_id=7 then "NVThuCuoc"
                when sc.object_id=5 and sc.poc_id=3 then "DGDichVu_MobiPay_Net"
                when sc.object_id=6 and sc.poc_id=3 then "DGDichVu_MobiPay_TV"
                
                when sc.object_id=7 and sc.poc_id=4 then "DGDichVu_Counter"
                when sc.object_id=8 and sc.poc_id=4 then "NV_Counter"
                
                when sc.object_id=29 and sc.poc_id=9 then "NVKinhDoanhSS"
                when sc.object_id=3 and sc.poc_id=9 then "NVTrienKhaiSS"
                when sc.object_id=5 and sc.poc_id=9 then "DGDichVuSS_Net"
                when sc.object_id=6 and sc.poc_id=9 then "DGDichVuSS_TV"
                
                when sc.object_id=30 and sc.poc_id=10 then "NVBT_SSW"
                when sc.object_id=5 and sc.poc_id=10 then "DGDichVuSSW_Net"
                when sc.object_id=6 and sc.poc_id=10 then "DGDichVuSSW_TV"

                else concat(sc.object_id,sc.poc_id)
                end as "LoaiDoiTuong",
                (sum(sc.csat_1)+sum(sc.csat_2)+sum(sc.csat_3)+sum(sc.csat_4)+sum(sc.csat_5)) as "SoLuong"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->groupBy(DB::raw('sc.poc_id, sc.object_id'))
                ->get();
        $result['total'] = $resultCsatTotal;
        return $result;
    }

    public function getCsatByRegionBranch($dayFrom, $dayTo) {
//        DB::enableQuerylog();
//        $resultCsatByRegion = DB::table('summary_csat as sc')
//                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
//                ->join('summary_branches as sb', 'sc.branch_id', '=', 'sb.branch_id')
//                ->select(DB::raw(' concat("Vùng ",sb.zone_id) "Vung",
//-- Tong diem csat
//sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhPoint",
//sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiPoint",
//sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_Net_Point",
//sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_TV_Point",
//
//sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhTSPoint",
//sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiTSPoint",
//sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuTS_Net_Point",
//sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuTS_TV_Point",
//
//sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBaoTriTINPoint",
//sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriTIN_Net_Point",
//sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriTIN_TV_Point",
//
//sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBaoTriINDOPoint",
//sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriINDO_Net_Point",
//sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriINDO_TV_Point",
//
//sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTC_Point",
//sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_MobiPay_Net_Point",
//sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_MobiPay_TV_Point",
//
//sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuGDTQ_Point",
//sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVGDTQ_Point",
//
//sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhSSPoint",
//sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiSSPoint",
//sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSS_Net_Point",
//sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSS_TV_Point",
//
//sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBTSSWPoint",
//sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSSW_Net_Point",
//sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSSW_TV_Point",
//
//-- Tinh so luong csat
//sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKD",
//sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTK",
//sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_Net",
//sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_TV",
//
//sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKDTS",
//sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTKTS",
//sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVTS_Net",
//sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVTS_TV",
//
//sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVBaoTriTIN",
//sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriTIN_Net",
//sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriTIN_TV",
//
//sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVBaoTriINDO",
//sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriINDO_Net",
//sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriINDO_TV",
//
//sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVTC",
//sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_MobiPay_Net",
//sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_MobiPay_TV",
//
//sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongGDTQ",
//sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVGDTQ",
//
//sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKDSS",
//sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTKSS",
//sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSS_Net",
//sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSS_TV",
//
//sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongSSW",
//sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSSW_Net",
//sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSSW_TV",
//-- Tinh so diem trung binh
//round((sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 ))),2) "NVKinhDoanh_AVGPoint",
//round(sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTrienKhai_AVGPoint",
//round(sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_Net_AVGPoint",
//round(sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_TV_AVGPoint",
//
//round(sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVKinhDoanhTS_AVGPoint",
//round(sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTrienKhaiTS_AVGPoint",
//round(sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVuTS_Net_AVGPoint",
//round(sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVuTS_TV_AVGPoint",
//
//round(sum(if(sc.poc_id in (2,12) and sc.object_id=16, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVBaoTriTIN_AVGPoint",
//round(sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriTIN_Net_AVGPoint",
//round(sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriTIN_TV_AVGPoint",
//
//round(sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVBaoTriINDO_AVGPoint",
//round(sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriINDO_Net_AVGPoint",
//round(sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriINDO_TV_AVGPoint",
//
//round(sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTC_AVGPoint",
//round(sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_MobiPay_Net_AVGPoint",
//round(sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)"DGDichVu_MobiPay_TV_AVGPoint",
//
//if(ISNULL(round(sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) )"DGDichVu_GDTQ_AVGPoint",
//if(ISNULL(round(sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) )"NV_GDTQ_AVGPoint",
//
//if(ISNULL(round(sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) )"NVKinhDoanhSS_AVGPoint",
//if(ISNULL(round(sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "NVTrienKhaiSS_AVGPoint",
//if(ISNULL(round(sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "DGDichVuSS_Net_AVGPoint",
//if(ISNULL(round(sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "DGDichVuSS_TV_AVGPoint",
//
//if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )),2) ) "NVBT_SSW_AVGPoint",
//if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "DGDichVuSSW_Net_AVGPoint",
//if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "DGDichVuSSW_TV_AVGPoint"
//                        '))
////                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
////                            os.section_id=osr.survey_result_section_id"))
//                ->where('st.time_temp', '>=', strtotime($dayFrom))
//                ->where('st.time_temp', '<=', strtotime($dayTo))
//                ->groupBy(DB::raw('sb.zone_id'))
//                ->get();
////                 $queries = DB::getQueryLog();
////                        dd($npsToUpdateAfter);
////                         dump($queries);die;
////        dump($resultCsatByRegion);die;
//        $result['survey'] = $resultCsatByRegion;
        $resultCsatByBranch = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('concat("Vùng ",sb.zone_id) "Vung",
concat(sb.branch_code," - ",sb.branch_name) "ChiNhanh",
-- Tong diem csat
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhPoint",
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiPoint",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_Net_Point",
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_TV_Point",

sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhTSPoint",
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiTSPoint",
sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuTS_Net_Point",
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuTS_TV_Point",

sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBaoTriTINPoint",
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriTIN_Net_Point",
sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriTIN_TV_Point",

sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBaoTriINDOPoint",
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriINDO_Net_Point",
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriINDO_TV_Point",

sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTC_Point",
sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_MobiPay_Net_Point",
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_MobiPay_TV_Point",

sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuGDTQ_Point",
sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVGDTQ_Point",

sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhSSPoint",
sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiSSPoint",
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSS_Net_Point",
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSS_TV_Point",

sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBTSSWPoint",
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSSW_Net_Point",
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSSW_TV_Point",

-- Tinh so luong csat
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKD",
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTK",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_Net",
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_TV",

sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKDTS",
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTKTS",
sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVTS_Net",
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVBaoTriTIN",
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriTIN_Net",
sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriTIN_TV",

sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVBaoTriINDO",
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriINDO_Net",
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriINDO_TV",

sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVTC",
sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_MobiPay_Net",
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongGDTQ",
sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVGDTQ",

sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKDSS",
sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTKSS",
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSS_Net",
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSS_TV",

sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongSSW",
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSSW_Net",
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSSW_TV",
-- Tinh so diem trung binh
round((sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 ))),2) "NVKinhDoanh_AVGPoint",
round(sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTrienKhai_AVGPoint",
round(sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_Net_AVGPoint",
round(sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_TV_AVGPoint",

round(sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVKinhDoanhTS_AVGPoint",
round(sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTrienKhaiTS_AVGPoint",
round(sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVuTS_Net_AVGPoint",
round(sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVuTS_TV_AVGPoint",

round(sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVBaoTriTIN_AVGPoint",
round(sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriTIN_Net_AVGPoint",
round(sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriTIN_TV_AVGPoint",

round(sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVBaoTriINDO_AVGPoint",
round(sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriINDO_Net_AVGPoint",
round(sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriINDO_TV_AVGPoint",

if(ISNULL(round(sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=3 and sc.object_id=14, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2))=1,"0.00",round(sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=3 and sc.object_id=14, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) "NVTC_AVGPoint",
round(sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_MobiPay_Net_AVGPoint",
round(sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)"DGDichVu_MobiPay_TV_AVGPoint",

if(ISNULL(round(sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2))=1,"0.00",round(sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2))"DGDichVu_GDTQ_AVGPoint",
if(ISNULL(round(sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2))=1,"0.00",round(sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2))"NV_GDTQ_AVGPoint",

if(ISNULL(round((sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 ))),2)) =1,"0.00",round((sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 ))),2)) "NVKinhDoanhSS_AVGPoint",
if(ISNULL(round(sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2))=1,"0.00",round(sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) "NVTrienKhaiSS_AVGPoint",
if(ISNULL(round(sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2))=1,"0.00",round(sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) "DGDichVuSS_Net_AVGPoint",
if(ISNULL(round(sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2))=1,"0.00",round(sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) "DGDichVuSS_TV_AVGPoint",
                       
if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )),2) ) "NVBT_SSW_AVGPoint",
if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "DGDichVuSSW_Net_AVGPoint",
if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "DGDichVuSSW_TV_AVGPoint"
'))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->groupBy(DB::raw('sb.zone_id, sb.branch_code'))
                ->get();
        $result['surveyBranches'] = $resultCsatByBranch;
        $resultCsatByCountry = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"Toàn quốc" as "Vung",
-- Tong diem csat
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhPoint",
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiPoint",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_Net_Point",
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_TV_Point",

sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhTSPoint",
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiTSPoint",
sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuTS_Net_Point",
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuTS_TV_Point",

sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBaoTriTINPoint",
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriTIN_Net_Point",
sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriTIN_TV_Point",

sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBaoTriINDOPoint",
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriINDO_Net_Point",
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DVBaoTriINDO_TV_Point",

sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTC_Point",
sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_MobiPay_Net_Point",
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVu_MobiPay_TV_Point",

sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuGDTQ_Point",
sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVGDTQ_Point",

sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVKinhDoanhSSPoint",
sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVTrienKhaiSSPoint",
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSS_Net_Point",
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSS_TV_Point",

sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "NVBTSSWPoint",
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSSW_Net_Point",
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) "DGDichVuSSW_TV_Point",

-- Tinh so luong csat
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKD",
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTK",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_Net",
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_TV",

sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKDTS",
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTKTS",
sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVTS_Net",
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVBaoTriTIN",
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriTIN_Net",
sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriTIN_TV",

sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVBaoTriINDO",
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriINDO_Net",
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDVBaoTriINDO_TV",

sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVTC",
sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_MobiPay_Net",
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDV_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongGDTQ",
sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongNVGDTQ",

sum(if(sc.poc_id=9 and sc.object_id=1, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongKDSS",
sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongTKSS",
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSS_Net",
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSS_TV",

sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongSSW",
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSSW_Net",
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )) "SoLuongDGDVSSW_TV",
-- Tinh so diem trung binh
round((sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 ))),2) "NVKinhDoanh_AVGPoint",
round(sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTrienKhai_AVGPoint",
round(sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_Net_AVGPoint",
round(sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_TV_AVGPoint",

round(sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVKinhDoanhTS_AVGPoint",
round(sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTrienKhaiTS_AVGPoint",
round(sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVuTS_Net_AVGPoint",
round(sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVuTS_TV_AVGPoint",

round(sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVBaoTriTIN_AVGPoint",
round(sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriTIN_Net_AVGPoint",
round(sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriTIN_TV_AVGPoint",

round(sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))/ sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVBaoTriINDO_AVGPoint",
round(sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriINDO_Net_AVGPoint",
round(sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DVBaoTriINDO_TV_AVGPoint",

round(sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=3 and sc.object_id=14, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTC_AVGPoint",
round(sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVu_MobiPay_Net_AVGPoint",
round(sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)"DGDichVu_MobiPay_TV_AVGPoint",

round(sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)"DGDichVu_GDTQ_AVGPoint",
round(sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)"NV_GDTQ_AVGPoint",

round((sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) /sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 ))),2) "NVKinhDoanhSS_AVGPoint",
round(sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "NVTrienKhaiSS_AVGPoint",
round(sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVuSS_Net_AVGPoint",
round(sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) "DGDichVuSS_TV_AVGPoint",
                        
if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )),2) ) "NVBT_SSW_AVGPoint",
if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 ))  / sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "DGDichVuSSW_Net_AVGPoint",
if(ISNULL(round(sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2)) = 1, "0.00",round(sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+2*sc.csat_2+3*sc.csat_3+4*sc.csat_4+5*sc.csat_5, 0 )) / sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1+sc.csat_2+sc.csat_3+sc.csat_4+sc.csat_5, 0 )),2) ) "DGDichVuSSW_TV_AVGPoint"
'))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($dayFrom))
                ->where('st.time_temp', '<=', strtotime($dayTo))
                ->get();
        $result['arrCountry'] = $resultCsatByCountry;
        return $result;
    }

    public function getTableColumns() {
        $result = DB::getSchemaBuilder()->getColumnListing($this->table);
        return $result;
    }

    public function getFieldName() {
        return $this->fillable;
    }

    public function getCSATTotalbyParam($params) {
        $sqlRaw = "b.zone_id, b.branch_name, b.branch_code, b.isc_location_id, b.isc_branch_code,
        c.branch_id, c.object_id, c.poc_id, sum(c.csat_1) as csat_1, sum(c.csat_2) as csat_2, sum(c.csat_3) as csat_3, sum(c.csat_4) as csat_4, sum(c.csat_5) as csat_5";
        $result = DB::table($this->table . ' as c')
                ->selectRaw($sqlRaw)
                ->join('summary_time as t', 'c.time_id', '=', 't.id')
                ->join('summary_branches as b', 'c.branch_id', '=', 'b.branch_id')
                ->where(function($query) use ($params) {
                    if (!empty($params['dayFrom'])) {
                        $query->where('t.time_temp', '>=', strtotime($params['dayFrom']));
                        $query->where('t.time_temp', '<=', strtotime($params['dayTo']));
                    }
                })
                ->where(function($query) use ($params) {
                    if (!empty($params['arrayPOC'])) {
                        $query->whereIn('c.poc_id', $params['arrayPOC']);
                    }
                })
                ->where(function($query) use ($params) {
                    if (!empty($params['arrayObject'])) {
                        $query->whereIn('c.object_id', $params['arrayObject']);
                    }
                })
                ->groupBy('c.branch_id', 'c.object_id', 'c.poc_id')
                ->orderBy('c.branch_id')
                ->get();
        return $result;
    }

    public function getCSATInfo($region, $from_date, $to_date, $branch, $branchcode) {
        $csat1 = DB::table('summary_csat AS sc')
                ->join('summary_time AS st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches AS sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw(' "Rất không hài lòng ( CSAT 1 )" as "DanhGia",
sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0)) "NVKinhDoanh",
sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_1,0)) "NVTrienKhai",
sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_1,0)) "DGDichVu_Net",
sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_1,0)) "DGDichVu_TV",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0)) "NVKinhDoanhTS",
sum(if(sc.poc_id=6 and sc.object_id=3,sc.csat_1,0)) "NVTrienKhaiTS",
sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_1,0)) "DGDichVuTS_Net",
sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_1,0)) "DGDichVuTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_1,0)) "NVBaoTriTIN",
sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_1,0)) "NVBaoTriINDO",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_1,0)) "DVBaoTriTIN_Net",
sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_1,0)) "DVBaoTriTIN_TV",

sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_1,0)) "DVBaoTriINDO_Net",
sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_1,0)) "DVBaoTriINDO_TV",

sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_1,0)) "NVThuCuoc",
sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_1,0)) "DGDichVu_MobiPay_Net",
sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_1,0)) "DGDichVu_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_1,0)) "NV_Counter",
sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_1,0)) "DGDichVu_Counter",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_1,0)) "NVKinhDoanhSS",
sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_1,0)) "NVTrienKhaiSS",
sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_1,0)) "DGDichVuSS_Net",
sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_1,0)) "DGDichVuSS_TV",

sum(if(sc.poc_id=10 and sc.object_id=30,sc.csat_1,0)) "NVBT_SSW",
sum(if(sc.poc_id=10 and sc.object_id=5,sc.csat_1,0)) "DGDichVuSSW_Net",
sum(if(sc.poc_id=10 and sc.object_id=6,sc.csat_1,0)) "DGDichVuSSW_TV",

"1" as "answers_point"

                        '))
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
        $csat2 = DB::table('summary_csat AS sc')
                ->join('summary_time AS st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches AS sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw(' "Không hài lòng ( CSAT 2 )" as "DanhGia",
sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0)) "NVKinhDoanh",
sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_2,0)) "NVTrienKhai",
sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_2,0)) "DGDichVu_Net",
sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_2,0)) "DGDichVu_TV",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0)) "NVKinhDoanhTS",
sum(if(sc.poc_id=6 and sc.object_id=3,sc.csat_2,0)) "NVTrienKhaiTS",
sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_2,0)) "DGDichVuTS_Net",
sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_2,0)) "DGDichVuTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_2,0)) "NVBaoTriTIN",
sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_2,0)) "NVBaoTriINDO",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_2,0)) "DVBaoTriTIN_Net",
sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_2,0)) "DVBaoTriTIN_TV",

sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_2,0)) "DVBaoTriINDO_Net",
sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_2,0)) "DVBaoTriINDO_TV",

sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_2,0)) "NVThuCuoc",
sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_2,0)) "DGDichVu_MobiPay_Net",
sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_2,0)) "DGDichVu_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_2,0)) "NV_Counter",
sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_2,0)) "DGDichVu_Counter",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_2,0)) "NVKinhDoanhSS",
sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_2,0)) "NVTrienKhaiSS",
sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_2,0)) "DGDichVuSS_Net",
sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_2,0)) "DGDichVuSS_TV",

sum(if(sc.poc_id=10 and sc.object_id=30,sc.csat_2,0)) "NVBT_SSW",
sum(if(sc.poc_id=10 and sc.object_id=5,sc.csat_2,0)) "DGDichVuSSW_Net",
sum(if(sc.poc_id=10 and sc.object_id=6,sc.csat_2,0)) "DGDichVuSSW_TV",

"2" as "answers_point"

                        '))
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
        $csat3 = DB::table('summary_csat AS sc')
                ->join('summary_time AS st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches AS sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw(' "Trung  lập ( CSAT 3 )" as "DanhGia",
sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0)) "NVKinhDoanh",
sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_3,0)) "NVTrienKhai",
sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_3,0)) "DGDichVu_Net",
sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_3,0)) "DGDichVu_TV",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0)) "NVKinhDoanhTS",
sum(if(sc.poc_id=6 and sc.object_id=3,sc.csat_3,0)) "NVTrienKhaiTS",
sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_3,0)) "DGDichVuTS_Net",
sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_3,0)) "DGDichVuTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_3,0)) "NVBaoTriTIN",
sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_3,0)) "NVBaoTriINDO",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_3,0)) "DVBaoTriTIN_Net",
sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_3,0)) "DVBaoTriTIN_TV",

sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_3,0)) "DVBaoTriINDO_Net",
sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_3,0)) "DVBaoTriINDO_TV",

sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_3,0)) "NVThuCuoc",
sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_3,0)) "DGDichVu_MobiPay_Net",
sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_3,0)) "DGDichVu_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_3,0)) "NV_Counter",
sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_3,0)) "DGDichVu_Counter",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_3,0)) "NVKinhDoanhSS",
sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_3,0)) "NVTrienKhaiSS",
sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_3,0)) "DGDichVuSS_Net",
sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_3,0)) "DGDichVuSS_TV",


sum(if(sc.poc_id=10 and sc.object_id=30,sc.csat_3,0)) "NVBT_SSW",
sum(if(sc.poc_id=10 and sc.object_id=5,sc.csat_3,0)) "DGDichVuSSW_Net",
sum(if(sc.poc_id=10 and sc.object_id=6,sc.csat_3,0)) "DGDichVuSSW_TV",
"3" as "answers_point"

                        '))
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
        $csat4 = DB::table('summary_csat AS sc')
                ->join('summary_time AS st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches AS sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw(' "Hài lòng ( CSAT 4 )" as "DanhGia",
sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0)) "NVKinhDoanh",
sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_4,0)) "NVTrienKhai",
sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_4,0)) "DGDichVu_Net",
sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_4,0)) "DGDichVu_TV",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0)) "NVKinhDoanhTS",
sum(if(sc.poc_id=6 and sc.object_id=3,sc.csat_4,0)) "NVTrienKhaiTS",
sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_4,0)) "DGDichVuTS_Net",
sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_4,0)) "DGDichVuTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_4,0)) "NVBaoTriTIN",
sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_4,0)) "NVBaoTriINDO",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_4,0)) "DVBaoTriTIN_Net",
sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_4,0)) "DVBaoTriTIN_TV",

sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_4,0)) "DVBaoTriINDO_Net",
sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_4,0)) "DVBaoTriINDO_TV",

sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_4,0)) "NVThuCuoc",
sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_4,0)) "DGDichVu_MobiPay_Net",
sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_4,0)) "DGDichVu_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_4,0)) "NV_Counter",
sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_4,0)) "DGDichVu_Counter",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_4,0)) "NVKinhDoanhSS",
sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_4,0)) "NVTrienKhaiSS",
sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_4,0)) "DGDichVuSS_Net",
sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_4,0)) "DGDichVuSS_TV",


sum(if(sc.poc_id=10 and sc.object_id=30,sc.csat_4,0)) "NVBT_SSW",
sum(if(sc.poc_id=10 and sc.object_id=5,sc.csat_4,0)) "DGDichVuSSW_Net",
sum(if(sc.poc_id=10 and sc.object_id=6,sc.csat_4,0)) "DGDichVuSSW_TV",
"4" as "answers_point"

                        '))
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
        $csat5 = DB::table('summary_csat AS sc')
                ->join('summary_time AS st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches AS sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw(' "Rất hài lòng ( CSAT 5 )" as "DanhGia",
sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0)) "NVKinhDoanh",
sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhai",
sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_5,0)) "DGDichVu_Net",
sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_5,0)) "DGDichVu_TV",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "NVKinhDoanhTS",
sum(if(sc.poc_id=6 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhaiTS",
sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_5,0)) "DGDichVuTS_Net",
sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_5,0)) "DGDichVuTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_5,0)) "NVBaoTriTIN",
sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_5,0)) "NVBaoTriINDO",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_5,0)) "DVBaoTriTIN_Net",
sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_5,0)) "DVBaoTriTIN_TV",

sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_5,0)) "DVBaoTriINDO_Net",
sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_5,0)) "DVBaoTriINDO_TV",

sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_5,0)) "NVThuCuoc",
sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_5,0)) "DGDichVu_MobiPay_Net",
sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_5,0)) "DGDichVu_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_5,0)) "NV_Counter",
sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_5,0)) "DGDichVu_Counter",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_5,0)) "NVKinhDoanhSS",
sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhaiSS",
sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_5,0)) "DGDichVuSS_Net",
sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_5,0)) "DGDichVuSS_TV",


sum(if(sc.poc_id=10 and sc.object_id=30,sc.csat_5,0)) "NVBT_SSW",
sum(if(sc.poc_id=10 and sc.object_id=5,sc.csat_5,0)) "DGDichVuSSW_Net",
sum(if(sc.poc_id=10 and sc.object_id=6,sc.csat_5,0)) "DGDichVuSSW_TV",
"5" as "answers_point"

                        '))
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
                ->union($csat1)
                ->union($csat2)
                ->union($csat3)
                ->union($csat4)
                ->get();
        return $csat5;
    }

    public function getCSAT12($region, $from_date, $to_date, $branch, $branchcode) {
//        DB::enableQueryLog();
        $resultCSAT12 = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('concat("Vung ",sb.zone_id) as "section_sub_parent_desc",
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1,0)) "NVKD_IBB_CSAT_1",
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_2,0)) "NVKD_IBB_CSAT_2",
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_2,0)) "NVKD_IBB_CSAT_12",
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_2,0)) + sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_3,0))
+ sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_4,0)) + sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_5,0)) "TOTAL_IBB_NVKD_CUS_CSAT",
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))*2 +
sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_3,0))*3+ sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_4,0))*4+ sum(if(sc.poc_id=1 and sc.object_id=1, sc.csat_5,0)) *5 "TOTAL_IBB_NVKD_CSAT",

sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1,0)) "NVTK_IBB_CSAT_1",
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_2,0)) "NVTK_IBB_CSAT_2",
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_2,0)) "NVTK_IBB_CSAT_12",
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_2,0)) +
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_3,0)) + sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_4,0)) + sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_5,0)) "TOTAL_IBB_NVTK_CUS_CSAT",
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_2,0))*2 +
sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_3,0))*3 + sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_4,0))*4 + sum(if(sc.poc_id=1 and sc.object_id=3, sc.csat_5,0))*5 "TOTAL_IBB_NVTK_CSAT",


sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1,0)) "NVKD_TS_CSAT_1",
sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_2,0)) "NVKD_TS_CSAT_2",
sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1,0)) + sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_2,0))  "NVKD_TS_CSAT_12",
sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1,0)) + sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_2,0))
+ sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_3,0)) + sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_4,0)) + sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_5,0)) "TOTAL_TS_NVKD_CUS_CSAT",
sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_1,0)) + 2*sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_2,0))
+ 3*sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_3,0)) + 4*sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_4,0)) + 5*sum(if(sc.poc_id=6 and sc.object_id=2, sc.csat_5,0)) "TOTAL_TS_NVKD_CSAT",

sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1,0)) "NVTK_TS_CSAT_1",
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_2,0)) "NVTK_TS_CSAT_2",
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1,0)) + sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_2,0))  "NVTK_TS_CSAT_12",
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1,0)) + sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_2,0)) +
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_3,0)) + sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_4,0)) + sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_5,0))"TOTAL_TS_NVTK_CUS_CSAT",
sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_1,0)) + 2*sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_2,0)) +
3*sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_3,0)) + 4*sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_4,0)) + 5*sum(if(sc.poc_id=6 and sc.object_id=3, sc.csat_5,0)) "TOTAL_TS_NVTK_CSAT",

sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1,0)) "NVBT_TIN_CSAT_1",
sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_2,0)) "NVBT_TIN_CSAT_2",
sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_2,0)) "NVBT_TIN_CSAT_12",
sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_2,0))+
sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_3,0)) + sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_4,0)) + sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_5,0)) "TOTAL_TIN_NVBT_CUS_CSAT",
sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_1,0)) + 2*sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_2,0))+
3*sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_3,0)) + 4*sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_4,0)) + 5*sum(if(sc.poc_id=2 and sc.object_id=16, sc.csat_5,0)) "TOTAL_TIN_NVBT_CSAT",

sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1,0)) "NVBT_INDO_CSAT_1",
sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_2,0)) "NVBT_INDO_CSAT_2",
sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1,0)) +sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_2,0)) "NVBT_INDO_CSAT_12",
sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1,0)) +sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_2,0)) 
+sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_3,0)) +sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_5,0))  "TOTAL_INDO_NVBT_CUS_CSAT",
sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_1,0)) +2*sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_2,0)) 
+3*sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_3,0)) +4*sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=15, sc.csat_5,0))  "TOTAL_INDO_NVBT_CSAT",

sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1,0)) "NVThuCuoc_CSAT_1",
sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_2,0)) "NVThuCuoc_CSAT_2",
sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1,0)) +sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_2,0)) "NVThuCuoc_CSAT_12",
sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1,0)) +sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_2,0)) 
+sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_3,0)) +sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_4,0))+sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_5,0))  "TOTAL_NVThuCuoc_CUS_CSAT",
sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_1,0)) +2*sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_2,0)) 
+3*sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_3,0)) +4*sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_4,0))+5*sum(if(sc.poc_id=7 and sc.object_id=14, sc.csat_5,0))  "TOTAL_NVThuCuoc_CSAT",

sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1,0)) "NVGDTQ_CSAT_1",
sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_2,0)) "NVGDTQ_CSAT_2",
sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1,0)) +sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_2,0)) "NVGDTQ_CSAT_12",
sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1,0)) +sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_2,0)) 
+sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_3,0)) +sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_4,0))+sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_5,0))  "TOTAL_NVGDTQ_CUS_CSAT",
sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_1,0)) +2*sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_2,0)) 
+3*sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_3,0)) +4*sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_4,0))+5*sum(if(sc.poc_id=4 and sc.object_id=8, sc.csat_5,0))  "TOTAL_NVGDTQ_CSAT",

sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1,0)) "NVKD_SS_CSAT_1",
sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_2,0)) "NVKD_SS_CSAT_2",
sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1,0)) +sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_2,0)) "NVKD_SS_CSAT_12",
sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1,0)) +sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_2,0)) 
+sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_3,0)) +sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_5,0))  "TOTAL_SS_NVKD_CUS_CSAT",
sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_1,0)) +2*sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_2,0)) 
+3*sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_3,0)) +4*sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=29, sc.csat_5,0))  "TOTAL_SS_NVKD_CSAT",

sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1,0)) "NVTK_SS_CSAT_1",
sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_2,0)) "NVTK_SS_CSAT_2",
sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1,0)) +sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_2,0)) "NVTK_SS_CSAT_12",
sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1,0)) +sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_2,0)) 
+sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_3,0)) +sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_5,0))  "TOTAL_SS_NVTK_CUS_CSAT",
sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_1,0)) +2*sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_2,0)) 
+3*sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_3,0)) +4*sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=3, sc.csat_5,0))  "TOTAL_SS_NVTK_CSAT",

sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1,0)) "NVBT_SSW_CSAT_1",
sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_2,0)) "NVBT_SSW_CSAT_2",
sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1,0)) +sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_2,0)) "NVBT_SSW_CSAT_12",
sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1,0)) +sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_2,0)) 
+sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_3,0)) +sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_4,0))+sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_5,0))  "TOTAL_SSW_NVBT_CUS_CSAT",
sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_1,0)) +2*sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_2,0)) 
+3*sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_3,0)) +4*sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_4,0))+5*sum(if(sc.poc_id=10 and sc.object_id=30, sc.csat_5,0))  "TOTAL_SSW_NVBT_CSAT"

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
        return $resultCSAT12;
    }

    public function getCSATService12($region, $from_date, $to_date, $branch, $branchcode) {
        //        DB::enableQueryLog();
        $resultCSATService12 = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('concat("Vung ",sb.zone_id) as "section_sub_parent_desc",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1,0)) "INTERNET_IBB_CSAT_1",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_2,0)) "INTERNET_IBB_CSAT_2",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_2,0)) "INTERNET_IBB_CSAT_12",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_2,0)) + sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_3,0))
+ sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_4,0)) + sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_5,0)) "TOTAL_IBB_INTERNET_CUS_CSAT",
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_2,0))*2 +
sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_3,0))*3+ sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_4,0))*4+ sum(if(sc.poc_id=1 and sc.object_id=5, sc.csat_5,0)) *5 "TOTAL_IBB_INTERNET_CSAT",

sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1,0)) "TV_IBB_CSAT_1",
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_2,0)) "TV_IBB_CSAT_2",
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_2,0)) "TV_IBB_CSAT_12",
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_2,0)) +
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_3,0)) + sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_4,0)) + sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_5,0)) "TOTAL_IBB_TV_CUS_CSAT",
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_2,0))*2 +
sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_3,0))*3 + sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_4,0))*4 + sum(if(sc.poc_id=1 and sc.object_id=6, sc.csat_5,0))*5 "TOTAL_IBB_TV_CSAT",


sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1,0)) "INTERNET_TS_CSAT_1",
sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_2,0)) "INTERNET_TS_CSAT_2",
sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_2,0))  "INTERNET_TS_CSAT_12",
sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_2,0))
+ sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_3,0)) + sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_4,0)) + sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_5,0)) "TOTAL_TS_INTERNET_CUS_CSAT",
sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_1,0)) + 2*sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_2,0))
+ 3*sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_3,0)) + 4*sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_4,0)) + 5*sum(if(sc.poc_id=6 and sc.object_id=5, sc.csat_5,0)) "TOTAL_TS_INTERNET_CSAT",

sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1,0)) "TV_TS_CSAT_1",
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_2,0)) "TV_TS_CSAT_2",
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_2,0))  "TV_TS_CSAT_12",
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_2,0)) +
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_3,0)) + sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_4,0)) + sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_5,0))"TOTAL_TS_TV_CUS_CSAT",
sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_1,0)) + 2*sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_2,0)) +
3*sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_3,0)) + 4*sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_4,0)) + 5*sum(if(sc.poc_id=6 and sc.object_id=6, sc.csat_5,0)) "TOTAL_TS_TV_CSAT",

sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1,0)) "INTERNET_TIN_CSAT_1",
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_2,0)) "INTERNET_TIN_CSAT_2",
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_2,0)) "INTERNET_TIN_CSAT_12",
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_2,0))+
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_3,0)) + sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_4,0)) + sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_5,0)) "TOTAL_TIN_INTERNET_CUS_CSAT",
sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_1,0)) + 2*sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_2,0))+
3*sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_3,0)) + 4*sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_4,0)) + 5*sum(if(sc.poc_id=2 and sc.object_id=23, sc.csat_5,0)) "TOTAL_TIN_INTERNET_CSAT",

sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1,0)) "TV_TIN_CSAT_1",
sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_2,0)) "TV_TIN_CSAT_2",
sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1,0)) +sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_2,0)) "TV_TIN_CSAT_12",
sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1,0)) +sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_2,0)) 
+sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_3,0)) +sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_5,0))  "TOTAL_TIN_TV_CUS_CSAT",
sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_1,0)) +2*sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_2,0)) 
+3*sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_3,0)) +4*sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=24, sc.csat_5,0))  "TOTAL_TIN_TV_CSAT",

sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1,0)) "INTERNET_INDO_CSAT_1",
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_2,0)) "INTERNET_INDO_CSAT_2",
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_2,0)) "INTERNET_INDO_CSAT_12",
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_2,0)) + sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_3,0))
+ sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_4,0)) + sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_5,0)) "TOTAL_INDO_INTERNET_CUS_CSAT",
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_2,0))*2 +
sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_3,0))*3+ sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_4,0))*4+ sum(if(sc.poc_id=2 and sc.object_id=21, sc.csat_5,0)) *5 "TOTAL_INDO_INTERNET_CSAT",

sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1,0)) "TV_INDO_CSAT_1",
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_2,0)) "TV_INDO_CSAT_2",
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_2,0)) "TV_INDO_CSAT_12",
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_2,0)) +
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_3,0)) + sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_4,0)) + sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_5,0)) "TOTAL_INDO_TV_CUS_CSAT",
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_1,0)) + sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_2,0))*2 +
sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_3,0))*3 + sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_4,0))*4 + sum(if(sc.poc_id=2 and sc.object_id=22, sc.csat_5,0))*5 "TOTAL_INDO_TV_CSAT",


sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1,0)) "INTERNET_CUS_CSAT_1",
sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_2,0)) "INTERNET_CUS_CSAT_2",
sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_2,0))  "INTERNET_CUS_CSAT_12",
sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_2,0))
+ sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_3,0)) + sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_4,0)) + sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_5,0)) "TOTAL_CUS_INTERNET_CUS_CSAT",
sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_1,0)) + 2*sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_2,0))
+ 3*sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_3,0)) + 4*sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_4,0)) + 5*sum(if(sc.poc_id=3 and sc.object_id=5, sc.csat_5,0)) "TOTAL_CUS_INTERNET_CSAT",

sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1,0)) "TV_CUS_CSAT_1",
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_2,0)) "TV_CUS_CSAT_2",
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_2,0))  "TV_CUS_CSAT_12",
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_2,0)) +
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_3,0)) + sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_4,0)) + sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_5,0))"TOTAL_CUS_TV_CUS_CSAT",
sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_1,0)) + 2*sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_2,0)) +
3*sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_3,0)) + 4*sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_4,0)) + 5*sum(if(sc.poc_id=3 and sc.object_id=6, sc.csat_5,0)) "TOTAL_CUS_TV_CSAT",

sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1,0)) "DGDichVu_Counter_CSAT_1",
sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_2,0)) "DGDichVu_Counter_CSAT_2",
sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1,0)) + sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_2,0))  "DGDichVu_Counter_CSAT_12",
sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1,0)) + sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_2,0)) +
sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_3,0)) + sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_4,0)) + sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_5,0))"TOTAL_DGDichVu_Counter_CUS_CSAT",
sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_1,0)) + 2*sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_2,0)) +
3*sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_3,0)) + 4*sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_4,0)) + 5*sum(if(sc.poc_id=4 and sc.object_id=7, sc.csat_5,0)) "TOTAL_DGDichVu_Counter_CSAT",

sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1,0)) "INTERNET_SS_CSAT_1",
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_2,0)) "INTERNET_SS_CSAT_2",
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_2,0)) "INTERNET_SS_CSAT_12",
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_2,0)) + sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_3,0))
+ sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_4,0)) + sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_5,0)) "TOTAL_SS_INTERNET_CUS_CSAT",
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_2,0))*2 +
sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_3,0))*3+ sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_4,0))*4+ sum(if(sc.poc_id=9 and sc.object_id=5, sc.csat_5,0)) *5 "TOTAL_SS_INTERNET_CSAT",

sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1,0)) "TV_SS_CSAT_1",
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_2,0)) "TV_SS_CSAT_2",
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_2,0)) "TV_SS_CSAT_12",
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_2,0)) +
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_3,0)) + sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_4,0)) + sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_5,0)) "TOTAL_SS_TV_CUS_CSAT",
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_2,0))*2 +
sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_3,0))*3 + sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_4,0))*4 + sum(if(sc.poc_id=9 and sc.object_id=6, sc.csat_5,0))*5 "TOTAL_SS_TV_CSAT",

sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1,0)) "INTERNET_SSW_CSAT_1",
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_2,0)) "INTERNET_SSW_CSAT_2",
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_2,0)) "INTERNET_SSW_CSAT_12",
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_2,0)) + sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_3,0))
+ sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_4,0)) + sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_5,0)) "TOTAL_SSW_INTERNET_CUS_CSAT",
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_1,0)) + sum(if(sc.poc_id=10 and sc.object_id=5,sc.csat_2,0))*2 +
sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_3,0))*3+ sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_4,0))*4+ sum(if(sc.poc_id=10 and sc.object_id=5, sc.csat_5,0)) *5 "TOTAL_SSW_INTERNET_CSAT",

sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1,0)) "TV_SSW_CSAT_1",
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_2,0)) "TV_SSW_CSAT_2",
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_2,0)) "TV_SSW_CSAT_12",
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_2,0)) +
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_3,0)) + sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_4,0)) + sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_5,0)) "TOTAL_SSW_TV_CUS_CSAT",
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_1,0)) + sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_2,0))*2 +
sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_3,0))*3 + sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_4,0))*4 + sum(if(sc.poc_id=10 and sc.object_id=6, sc.csat_5,0))*5 "TOTAL_SSW_TV_CSAT"
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
        return $resultCSATService12;
    }


    public function getCSATInfoByRegion($region, $from_date, $to_date, $branch, $branchcode = []) {
//        DB::enableQueryLog();
        $result = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('concat("Vung ", sb.zone_id) "Vung",
sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0)) "NVKinhDoanhPoint",

sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhaiPoint",

sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_5,0)) "DGDichVu_Net_Point",

sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_5,0)) "DGDichVu_TV_Point",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "NVKinhDoanhTSPoint",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "NVTrienKhaiTSPoint",

sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_5,0)) "DGDichVuTS_Net_Point",

sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=1,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_5,0)) "DGDichVuTS_TV_Point",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_5,0)) "NVBaoTriTINPoint",

sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_5,0)) "NVBaoTriINDOPoint",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_5,0)) "DVBaoTriTIN_Net_Point",

sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_5,0)) "DVBaoTriTIN_TV_Point",

 sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_5,0)) "DVBaoTriINDO_Net_Point",

sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_5,0)) "DVBaoTriINDO_TV_Point",

 sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_1,0))+2*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_2,0))+3*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_3,0))
+4*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_4,0))+5*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_5,0)) "NVThuCuocPoint",

 sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_5,0)) "DGDichVu_MobiPay_Net_Point",

sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_5,0)) "DGDichVu_MobiPay_TV_Point",

sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_1,0))+2*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_2,0))+3*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_3,0))
+4*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_4,0))+5*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_5,0)) "DGDichVu_Counter_Point",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_1,0))+2*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_2,0))+3*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_3,0))
+4*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_4,0))+5*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_5,0)) "NV_Counter_Point",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_5,0)) "NVKinhDoanhSSPoint",

sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhaiSSPoint",

sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_5,0)) "DGDichVuSS_Net_Point",

sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_5,0)) "DGDichVuSS_TV_Point",

sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_5,0)) "NVBT_SSWPoint",

sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_5,0)) "DGDichVuSSW_Net_Point",

sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_5,0)) "DGDichVuSSW_TV_Point",


sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0)) "SoLuongKD",

sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_5,0)) "SoLuongTK",

sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDV_Net",

sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDV_TV",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "SoLuongKDTS",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "SoLuongTKTS",

sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVTS_Net",

sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=1,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_5,0)) "SoLuongNVBaoTriTIN",

sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_5,0)) "SoLuongNVBaoTriINDO",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_5,0)) "SoLuongDVBaoTriTIN_Net",

sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_5,0)) "SoLuongDVBaoTriTIN_TV",

 sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_5,0)) "SoLuongDVBaoTriINDO_Net",

sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_5,0)) "SoLuongDVBaoTriINDO_TV",

 sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_1,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_2,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_3,0))
+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_4,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_5,0)) "SoLuongNVThuCuoc",

 sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDV_MobiPay_Net",

sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDV_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_1,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_2,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_3,0))
+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_4,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_5,0)) "SoLuongDGDichVu_Counter",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_1,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_2,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_3,0))
+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_4,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_5,0)) "SoLuongNV_Counter",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_5,0)) "SoLuongKDSS",

sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_5,0)) "SoLuongTKSS",

sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVSS_Net",

sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVSS_TV",

sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_5,0)) "SoLuongSSW",

sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVSSW_Net",

sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVSSW_TV"
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

    public function getCSATInfoByBranches($region, $from_date, $to_date, $limit, $branch, $branchcode = []) {
        $result = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw(' concat("Vung ", sb.zone_id) "Vung",
                    concat(sb.branch_code,"-",sb.branch_name)	"ChiNhanh",
sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0)) "NVKinhDoanhPoint",

sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhaiPoint",

sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_5,0)) "DGDichVu_Net_Point",

sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_5,0)) "DGDichVu_TV_Point",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "NVKinhDoanhTSPoint",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "NVTrienKhaiTSPoint",

sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_5,0)) "DGDichVuTS_Net_Point",

sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=1,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_5,0)) "DGDichVuTS_TV_Point",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_5,0)) "NVBaoTriTINPoint",

sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_5,0)) "NVBaoTriINDOPoint",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_5,0)) "DVBaoTriTIN_Net_Point",

sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_5,0)) "DVBaoTriTIN_TV_Point",

 sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_5,0)) "DVBaoTriINDO_Net_Point",

sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_5,0)) "DVBaoTriINDO_TV_Point",

 sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_1,0))+2*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_2,0))+3*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_3,0))
+4*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_4,0))+5*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_5,0)) "NVThuCuocPoint",

 sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_5,0)) "DGDichVu_MobiPay_Net_Point",

sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_5,0)) "DGDichVu_MobiPay_TV_Point",

sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_1,0))+2*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_2,0))+3*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_3,0))
+4*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_4,0))+5*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_5,0)) "DGDichVu_Counter_Point",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_1,0))+2*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_2,0))+3*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_3,0))
+4*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_4,0))+5*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_5,0)) "NV_Counter_Point",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_5,0)) "NVKinhDoanhSSPoint",

sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhaiSSPoint",

sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_5,0)) "DGDichVuSS_Net_Point",

sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_5,0)) "DGDichVuSS_TV_Point",

sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_5,0)) "NVBT_SSWPoint",

sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_5,0)) "DGDichVuSSW_Net_Point",

sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_5,0)) "DGDichVuSSW_TV_Point",



sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0)) "SoLuongKD",

sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_5,0)) "SoLuongTK",

sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDV_Net",

sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDV_TV",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "SoLuongKDTS",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "SoLuongTKTS",

sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVTS_Net",

sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=1,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_5,0)) "SoLuongNVBaoTriTIN",

sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_5,0)) "SoLuongNVBaoTriINDO",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_5,0)) "SoLuongDVBaoTriTIN_Net",

sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_5,0)) "SoLuongDVBaoTriTIN_TV",

 sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_5,0)) "SoLuongDVBaoTriINDO_Net",

sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_5,0)) "SoLuongDVBaoTriINDO_TV",

 sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_1,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_2,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_3,0))
+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_4,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_5,0)) "SoLuongNVThuCuoc",

 sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDV_MobiPay_Net",

sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDV_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_1,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_2,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_3,0))
+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_4,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_5,0)) "SoLuongDGDichVu_Counter",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_1,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_2,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_3,0))
+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_4,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_5,0)) "SoLuongNV_Counter",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_5,0)) "SoLuongKDSS",

sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_5,0)) "SoLuongTKSS",

sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVSS_Net",

sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVSS_TV",

sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_5,0)) "SoLuongSSW",

sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVSSW_Net",

sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVSSW_TV",

 (sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0))) /
(sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0))) "CSAT_NVKD"

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
                ->groupBy(DB::raw('sb.zone_id, sb.branch_code'))
                ->orderBy(DB::raw('CSAT_NVKD'), 'DESC')
                ->get();
//               $query= DB::getQueryLog();
//               dump($query);die;
        return $result;
    }

    public function getCSATInfoByAll($from_date, $to_date) {
        $result = DB::table('summary_csat as sc')
                ->join('summary_time as st', 'sc.time_id', '=', 'st.id')
                ->join('summary_branches as sb', 'sc.branch_id', '=', 'sb.branch_id')
                ->select(DB::raw('"ToanQuoc" as "Vung",
sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0)) "NVKinhDoanhPoint",

sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhaiPoint",

sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_5,0)) "DGDichVu_Net_Point",

sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_5,0)) "DGDichVu_TV_Point",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "NVKinhDoanhTSPoint",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "NVTrienKhaiTSPoint",

sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_5,0)) "DGDichVuTS_Net_Point",

sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=6 and sc.object_id=1,sc.csat_3,0))
+4*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_5,0)) "DGDichVuTS_TV_Point",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_5,0)) "NVBaoTriTINPoint",

sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_5,0)) "NVBaoTriINDOPoint",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_5,0)) "DVBaoTriTIN_Net_Point",

sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_5,0)) "DVBaoTriTIN_TV_Point",

 sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_5,0)) "DVBaoTriINDO_Net_Point",

sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_1,0))+2*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_2,0))+3*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_3,0))
+4*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_4,0))+5*sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_5,0)) "DVBaoTriINDO_TV_Point",

 sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_1,0))+2*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_2,0))+3*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_3,0))
+4*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_4,0))+5*sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_5,0)) "NVThuCuocPoint",

 sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_5,0)) "DGDichVu_MobiPay_Net_Point",

sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_5,0)) "DGDichVu_MobiPay_TV_Point",

sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_1,0))+2*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_2,0))+3*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_3,0))
+4*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_4,0))+5*sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_5,0)) "DGDichVu_Counter_Point",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_1,0))+2*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_2,0))+3*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_3,0))
+4*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_4,0))+5*sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_5,0)) "NV_Counter_Point",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_5,0)) "NVKinhDoanhSSPoint",

sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_5,0)) "NVTrienKhaiSSPoint",

sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_5,0)) "DGDichVuSS_Net_Point",

sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_5,0)) "DGDichVuSS_TV_Point",

sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_5,0)) "NVBT_SSWPoint",

sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_5,0)) "DGDichVuSSW_Net_Point",

sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_1,0))+2*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_2,0))+3*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_3,0))
+4*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_4,0))+5*sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_5,0)) "DGDichVuSSW_TV_Point",



sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=1,sc.csat_5,0)) "SoLuongKD",

sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=3,sc.csat_5,0)) "SoLuongTK",

sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDV_Net",

sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=1 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDV_TV",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "SoLuongKDTS",

sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=2,sc.csat_5,0)) "SoLuongTKTS",

sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVTS_Net",

sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=6 and sc.object_id=1,sc.csat_3,0))
+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=6 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVTS_TV",

sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=16,sc.csat_5,0)) "SoLuongNVBaoTriTIN",

sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=15,sc.csat_5,0)) "SoLuongNVBaoTriINDO",

sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=23,sc.csat_5,0)) "SoLuongDVBaoTriTIN_Net",

sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=24,sc.csat_5,0)) "SoLuongDVBaoTriTIN_TV",

 sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=21,sc.csat_5,0)) "SoLuongDVBaoTriINDO_Net",

sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_1,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_2,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_3,0))
+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_4,0))+sum(if(sc.poc_id=2 and sc.object_id=22,sc.csat_5,0)) "SoLuongDVBaoTriINDO_TV",

 sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_1,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_2,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_3,0))
+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_4,0))+sum(if(sc.poc_id=7 and sc.object_id=14,sc.csat_5,0)) "SoLuongNVThuCuoc",

 sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=3 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDV_MobiPay_Net",

sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=3 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDV_MobiPay_TV",

sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_1,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_2,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_3,0))
+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_4,0))+sum(if(sc.poc_id=4 and sc.object_id=7,sc.csat_5,0)) "SoLuongDGDichVu_Counter",

sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_1,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_2,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_3,0))
+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_4,0))+sum(if(sc.poc_id=4 and sc.object_id=8,sc.csat_5,0)) "SoLuongNV_Counter",

sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=29,sc.csat_5,0)) "SoLuongKDSS",

sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=3,sc.csat_5,0)) "SoLuongTKSS",

sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVSS_Net",

sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=9 and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVSS_TV",

sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=30 ,sc.csat_5,0)) "SoLuongSSW",

sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=5,sc.csat_5,0)) "SoLuongDGDVSSW_Net",

sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_1,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_2,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_3,0))
+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_4,0))+sum(if(sc.poc_id=10  and sc.object_id=6,sc.csat_5,0)) "SoLuongDGDVSSW_TV"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
                ->where('st.time_temp', '>=', strtotime($from_date))
                ->where('st.time_temp', '<=', strtotime($to_date))
//                ->groupBy(DB::raw('sb.zone_id'))
                ->get();
//               $query= DB::getQueryLog();
//               dump($query);die;
        return $result;
    }

}
