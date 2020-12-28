<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Redis;
use App\Models\OutboundQuestions;
use App\Models\SummaryOpinion;

class SurveySections extends Model {

    protected $table = 'outbound_survey_sections';
    protected $primaryKey = 'section_id';
    public $timestamps = false;

    public function countListSurvey($condition) {
        $mainQuery = DB::table($this->table . ' as s')
                ->select("s.section_id")
                ->where(function($query) use ($condition) {
                    if (!empty($condition['surveyFrom']) && !empty($condition['surveyTo'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['surveyFromInt']);
                        $query->where('s.section_time_completed_int', '<=', $condition['surveyToInt']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        if ($condition['type'] == 3 && $condition['channelConfirm'] == 2) {
                            $query->where('s.section_survey_id', '=', '7');
                        } else {
                            $query->where('s.section_survey_id', '=', $condition['type']);
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['channelConfirm'])) {
                        $query->where('s.section_record_channel', '=', $condition['channelConfirm']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['saleName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['saleName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['locationSQL'])) {
                        $tempRaw = '(';
                        $isSecondRaw = false;
                        foreach ($condition['locationSQL'] as $key => $val) {
                            if ($isSecondRaw) {
                                $tempRaw .= ' or ';
                            }
                            $tempRaw .= '(s.section_branch_code = ' . $key . ' and s.section_location_id in (' . implode(',', $val) . '))';
                            $isSecondRaw = true;
                        }
                        $tempRaw .= ')';
                        $query->whereRaw($tempRaw);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['arraySurveyID'])) {
                        $query->WhereIn('s.section_survey_id', $condition['arraySurveyID']);
                    }
                })
                ->where(function($query) use ($condition) {
            if (!empty($condition['arrayContractNumber'])) {
                $query->WhereIn('s.section_contract_num', $condition['arrayContractNumber']);
            }
        })
        ;

        $subQueryRaw = str_replace(array('%', '?'), array('%%', '"%s"'), $mainQuery->toSql());
        $subQueryRaw = vsprintf($subQueryRaw, $mainQuery->getBindings());
        $temp = explode('where', $subQueryRaw);

        $subQuery = $temp['0'] . "left join outbound_survey_result as sr on s.section_id = sr.survey_result_section_id";
        if (($condition['type'] == 3 && $condition['channelConfirm'] == 2) || $condition['type'] == 4 || $condition['type'] == 11) {
            $subQuery .= " left join outbound_survey_sections_email as sse on sse.section_id = s.section_id";
        }
        $subQuery .= ' where' . $temp[1];

        $mainQuery->where(function($query) use ($condition, $subQuery) {
            if (!empty($condition['reportedStatus'])) {
                if ($condition['reportedStatus'] == 1) { // chưa báo cáo xử lý CSAT
                    // //IBB, Telesales
                    if (!empty($condition['CSATPointSale']) && (in_array(1, $condition['CSATPointSale']) || in_array(2, $condition['CSATPointSale']))) {
                        $res = $condition['allQuestion'][1];
                        $query->whereRaw("(s.violation_status LIKE '%\"sales\":null%' OR s.violation_status IS NULL)"
                            . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                    if (!empty($condition['CSATPointNVTK']) && (in_array(1, $condition['CSATPointNVTK']) || in_array(2, $condition['CSATPointNVTK']))) {
                        $res = $condition['allQuestion'][3];
                        $query->whereRaw("(s.violation_status LIKE '%\"deploy\":null%' OR s.violation_status IS NULL)"
                                . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                    if (!empty($condition['CSATPointBT']) && (in_array(1, $condition['CSATPointBT']) || in_array(2, $condition['CSATPointBT']))) {
                        $res = $condition['allQuestion'][4];
                        $query->whereRaw("(s.violation_status LIKE '%\"maintenance\":null%' OR s.violation_status IS NULL)"
                                . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                } elseif ($condition['reportedStatus'] == 2) {
                    if (!empty($condition['CSATPointSale']) && (in_array(1, $condition['CSATPointSale']) || in_array(2, $condition['CSATPointSale']))) {
                        $res = $condition['allQuestion'][1];
                        $query->whereRaw("s.violation_status LIKE '%sales\":2%'"
                                . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                    if (!empty($condition['CSATPointNVTK']) && (in_array(1, $condition['CSATPointNVTK']) || in_array(2, $condition['CSATPointNVTK']))) {
                        $res = $condition['allQuestion'][3];
                        $query->whereRaw("s.violation_status LIKE '%\"deploy\":2%'"
                                . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                    if (!empty($condition['CSATPointBT']) && (in_array(1, $condition['CSATPointBT']) || in_array(2, $condition['CSATPointBT']))) {
                        $res = $condition['allQuestion'][4];
                        $query->whereRaw("s.violation_status LIKE '%\"maintenance\":2%'"
                                . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                }
            }
        });
        $mainQuery->where(function($query) use ($condition, $subQuery) {
            if (!empty($condition['RateNPS'])) {
                // alias 9 làm thế nào để có nps 10
                $res = $condition['allQuestion'][9];
                $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id' . " like('%" . implode(",", $condition['RateNPS']) . "%'))");
            }
        });
        $mainQuery->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['NPSPoint'])) {
                        // alias 10 là điểm NPS
                        $res = $condition['allQuestion'][10];
                        $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['NPSPoint']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointSale'])) {
                        // alias 1 là nhân viên kinh doanh
                        $res = $condition['allQuestion'][1];
                        $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointSale']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        // alias 3 là nhân viên triển khai
                        $res = $condition['allQuestion'][3];
                        $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointNVTK']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointBT'])) {
                        // alias 4 là nhân viên bảo trì
                        $res = $condition['allQuestion'][4];
                        $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointBT']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointNet'])) {
                        // alias 5 là Internet
                        $res = $condition['allQuestion'][5];
                        $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointNet']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['NetErrorType'])) {
                        // alias 5 là internet
                        $res = $condition['allQuestion'][5];
                        $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_extra_id = ' . $condition['NetErrorType'] . ')');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (in_array("1", $condition['CSATPointNet']) || in_array("2", $condition['CSATPointNet'])) {
                            if (!empty($condition['processingActionsInternet'])) {
                                // alias 5 là internet
                                $res = $condition['allQuestion'][5];
                                $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_action = ' . $condition['processingActionsInternet'] . ')');
                            }
                        }
                    }
                })
        ;

        $result = $mainQuery->count();
        return $result;
    }

    public function searchListSurvey($condition, $numberPage) {
        $mainQuery = DB::table($this->table . ' as s');

        $raw = "distinct(s.section_id),s.section_subsupporter, s.section_supporter, s.section_acc_sale AS saleName, s.section_survey_id, s.section_connected,"
                . " s.section_action, s.section_user_name, s.section_sub_parent_desc, s.section_location, s.section_note, s.section_location_id,"
                . " s.section_time_start, s.section_time_completed, s.section_code,s.section_contract_num, s.section_contact_phone,"
                . " s.section_branch_code, s.section_sale_branch_code, s.section_count_connected,"
                . " s.section_account_inf, s.section_account_list,"
                . " s.violation_status";
        if (isset($condition['type']) && (($condition['type'] == 3 && $condition['channelConfirm'] == 2) || $condition['type'] == 4)) {
            $raw .= ",sse.section_user_create_transaction, sse.section_name_change, sse.section_office, sse.section_kind_service, sse.section_time_start_transaction";
        }
        if (isset($condition['type']) && $condition['type'] == 11) {
            $raw .= ",sse.mac_address, sse.counter_code";
        }

        $mainQuery->select(DB::raw($raw));
        if (isset($condition['type']) && (($condition['type'] == 3 && $condition['channelConfirm'] == 2) || $condition['type'] == 4 || $condition['type'] == 11)) {
            $mainQuery->leftJoin('outbound_survey_sections_email as sse', 'sse.section_id', '=', 's.section_id');
        }

        $mainQuery->where(function($query) use ($condition) {
                    if (!empty($condition['surveyFrom']) && !empty($condition['surveyTo'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['surveyFromInt']);
                        $query->where('s.section_time_completed_int', '<=', $condition['surveyToInt']);
                    }
                })
            ->where(function($query) use ($condition) {
                if (!empty($condition['contractNum'])) {
                    $query->where('s.section_contract_num', '=', $condition['contractNum']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['type'])) {
                    if ($condition['type'] == 3 && $condition['channelConfirm'] == 2) {
                        $query->where('s.section_survey_id', '=', '7');
                    } else {
                        $query->where('s.section_survey_id', '=', $condition['type']);
                    }
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['section_action'])) {
                    $query->whereIn('s.section_action', $condition['section_action']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['section_connected'])) {
                    $query->whereIn('s.section_connected', $condition['section_connected']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['channelConfirm'])) {
                    $query->where('s.section_record_channel', '=', $condition['channelConfirm']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['userSurvey'])) {
                    $query->where('s.section_user_name', '=', $condition['userSurvey']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['saleName'])) {
                    $query->where('s.section_acc_sale', '=', $condition['saleName']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['technicalStaff'])) {
                    $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                    $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['locationSQL'])) {
                    $tempRaw = '(';
                    $isSecondRaw = false;
                    foreach ($condition['locationSQL'] as $key => $val) {
                        if ($isSecondRaw) {
                            $tempRaw .= ' or ';
                        }
                        $tempRaw .= '(s.section_branch_code = ' . $key . ' and s.section_location_id in (' . implode(',', $val) . '))';
                        $isSecondRaw = true;
                    }
                    $tempRaw .= ')';
                    $query->whereRaw($tempRaw);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['arraySurveyID'])) {
                    $query->WhereIn('s.section_survey_id', $condition['arraySurveyID']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['arrayContractNumber'])) {
                    $query->WhereIn('s.section_contract_num', $condition['arrayContractNumber']);
                }
            })
        ;

        $subQueryRaw = str_replace(array('%', '?'), array('%%', '"%s"'), $mainQuery->toSql());
        $subQueryRaw = vsprintf($subQueryRaw, $mainQuery->getBindings());
        $temp = explode('where', $subQueryRaw);

        if (isset($condition['type']) && (($condition['type'] == 3 && $condition['channelConfirm'] == 2) || $condition['type'] == 4)) {
            $subQuery = "select distinct(s.section_id) from outbound_survey_sections as s left join outbound_survey_result as sr on s.section_id = sr.survey_result_section_id left join outbound_survey_sections_email as sse on sse.section_id = s.section_id" . ' where' . $temp[1];
        } else {
            $subQuery = "select distinct(s.section_id) from outbound_survey_sections as s left join outbound_survey_result as sr on s.section_id = sr.survey_result_section_id" . ' where' . $temp[1];
        }

        $mainQuery->where(function($query) use ($condition, $subQuery) {
            if (!empty($condition['reportedStatus'])) {
                if ($condition['reportedStatus'] == 1) { // chưa báo cáo xử lý CSAT
                    // //IBB, Telesales
                    if (!empty($condition['CSATPointSale']) && (in_array(1, $condition['CSATPointSale']) || in_array(2, $condition['CSATPointSale']))) {
                        $res = $condition['allQuestion'][1];
                        $query->whereRaw("(s.violation_status LIKE '%\"sales\":null%' OR s.violation_status IS NULL)"
                            . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                    if (!empty($condition['CSATPointNVTK']) && (in_array(1, $condition['CSATPointNVTK']) || in_array(2, $condition['CSATPointNVTK']))) {
                        $res = $condition['allQuestion'][3];
                        $query->whereRaw("(s.violation_status LIKE '%\"deploy\":null%' OR s.violation_status IS NULL)"
                            . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                    if (!empty($condition['CSATPointBT']) && (in_array(1, $condition['CSATPointBT']) || in_array(2, $condition['CSATPointBT']))) {
                        $res = $condition['allQuestion'][4];
                        $query->whereRaw("(s.violation_status LIKE '%\"maintenance\":null%' OR s.violation_status IS NULL)"
                            . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                } elseif ($condition['reportedStatus'] == 2) {
                    if (!empty($condition['CSATPointSale']) && (in_array(1, $condition['CSATPointSale']) || in_array(2, $condition['CSATPointSale']))) {
                        $res = $condition['allQuestion'][1];
                        $query->whereRaw("s.violation_status LIKE '%sales\":2%'"
                            . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                    if (!empty($condition['CSATPointNVTK']) && (in_array(1, $condition['CSATPointNVTK']) || in_array(2, $condition['CSATPointNVTK']))) {
                        $res = $condition['allQuestion'][3];
                        $query->whereRaw("s.violation_status LIKE '%\"deploy\":2%'"
                            . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                    if (!empty($condition['CSATPointBT']) && (in_array(1, $condition['CSATPointBT']) || in_array(2, $condition['CSATPointBT']))) {
                        $res = $condition['allQuestion'][4];
                        $query->whereRaw("s.violation_status LIKE '%\"maintenance\":2%'"
                            . " AND s.section_id in (" . $subQuery . " and sr.survey_result_question_id in (" . implode(',', $res) . ") and sr.survey_result_answer_id in (1,2)) ");
                    }
                }
            }
        });
        $mainQuery->where(function($query) use ($condition, $subQuery) {
            if (!empty($condition['RateNPS'])) {
                // alias 9 làm thế nào để có nps 10
                $res = $condition['allQuestion'][9];
                $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id' . " like('%" . implode(",", $condition['RateNPS']) . "%'))");
            }
        });
        $mainQuery->where(function($query) use ($condition, $subQuery) {
            if (!empty($condition['NPSPoint'])) {
                // alias 10 là điểm NPS
                $res = $condition['allQuestion'][10];
                $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['NPSPoint']) . '))');
            }
        })
            ->where(function($query) use ($condition, $subQuery) {
                if (!empty($condition['CSATPointSale'])) {
                    // alias 1 là nhân viên kinh doanh
                    $res = $condition['allQuestion'][1];
                    $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointSale']) . '))');
                }
            })
            ->where(function($query) use ($condition, $subQuery) {
                if (!empty($condition['CSATPointNVTK'])) {
                    // alias 3 là nhân viên triển khai
                    $res = $condition['allQuestion'][3];
                    $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointNVTK']) . '))');
                }
            })
            ->where(function($query) use ($condition, $subQuery) {
                if (!empty($condition['CSATPointBT'])) {
                    // alias 4 là nhân viên bảo trì
                    $res = $condition['allQuestion'][4];
                    $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointBT']) . '))');
                }
            })
            ->where(function($query) use ($condition, $subQuery) {
                if (!empty($condition['CSATPointNet'])) {
                    // alias 5 là Internet
                    $res = $condition['allQuestion'][5];
                    $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointNet']) . '))');
                }
            })
            ->where(function($query) use ($condition, $subQuery) {
                if (!empty($condition['NetErrorType'])) {
                    // alias 5 là internet
                    $res = $condition['allQuestion'][5];
                    $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_extra_id = ' . $condition['NetErrorType'] . ')');
                }
            })
            ->where(function($query) use ($condition, $subQuery) {
                if (!empty($condition['CSATPointNet'])) {
                    if (in_array("1", $condition['CSATPointNet']) || in_array("2", $condition['CSATPointNet'])) {
                        if (!empty($condition['processingActionsInternet'])) {
                            // alias 5 là internet
                            $res = $condition['allQuestion'][5];
                            $query->whereRaw("s.section_id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_action = ' . $condition['processingActionsInternet'] . ')');
                        }
                    }
                }
            })
        ;
        $mainQuery->orderBy('s.section_time_completed_int', 'DESC');
        if (!empty($condition['recordPerPage'])) {
            $mainQuery->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage']);
        }
        $result = $mainQuery->get();
        return $result;
    }

    public function countListSurveyViolations($condition) {
        $mainQuery = DB::table($this->table . ' as s')
                ->select("v.id")
                ->join('survey_violations as v', 's.section_id', '=', 'v.sectionID')
                ->where(function($query) use ($condition) {
                    if (!empty($condition['surveyFrom']) && !empty($condition['surveyTo'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['surveyFromInt']);
                        $query->where('s.section_time_completed_int', '<=', $condition['surveyToInt']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['channelConfirm'])) {
                        $query->where('s.section_record_channel', '=', $condition['channelConfirm']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        if ($condition['type'] == 3 && $condition['channelConfirm'] == 2) {
                            $query->where('s.section_survey_id', '=', '7');
                        } else {
                            $query->where('s.section_survey_id', '=', $condition['type']);
                        }
                    }
                    if (!empty($condition['object'])) {
                        $object = $condition['object'];
                        switch ($condition['type']) {
                            case 2:
                                $object = 3;
                                break;
                            case 3:
                                $object = 6;
                                break;
                            case 4:
                                $object = 5;
                                break;
                            case 6:
                                $object = 4;
                                break;
                            default:
                        }
                        $query->where('v.type', '=', $object);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['saleName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['saleName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['locationSQL'])) {
                        $tempRaw = '(';
                        $isSecondRaw = false;
                        foreach ($condition['locationSQL'] as $key => $val) {
                            if ($isSecondRaw) {
                                $tempRaw .= ' or ';
                            }
                            $tempRaw .= '(s.section_branch_code = ' . $key . ' and s.section_location_id in (' . implode(',', $val) . '))';
                            $isSecondRaw = true;
                        }
                        $tempRaw .= ')';
                        $query->whereRaw($tempRaw);
                    }
                })
                ;

        $subQueryRaw = str_replace(array('%', '?'), array('%%', '"%s"'), $mainQuery->toSql());
        $subQueryRaw = vsprintf($subQueryRaw, $mainQuery->getBindings());
        $temp = explode('where', $subQueryRaw);
        $subQuery = $temp['0'] . 'left join outbound_survey_result as sr on s.section_id = sr.survey_result_section_id where' . $temp[1];

        $mainQuery->where(function($query) use ($condition, $subQuery) {
            if (!empty($condition['RateNPS'])) {
                // alias 9 làm thế nào để có nps 10
                $res = $condition['allQuestion'][9];
                $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id' . " like('%" . implode(",", $condition['RateNPS']) . "%'))");
            }
        });
        $mainQuery->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['NPSPoint'])) {
                        // alias 10 là điểm NPS
                        $res = $condition['allQuestion'][10];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['NPSPoint']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointSale'])) {
                        // alias 1,2 là nhân viên kinh doanh
                        $res = array_merge($condition['allQuestion'][1], $condition['allQuestion'][2]);
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointSale']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        // alias 3 là nhân viên triển khai
                        $res = $condition['allQuestion'][3];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointNVTK']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointBT'])) {
                        // alias 4 là nhân viên bảo trì
                        $res = $condition['allQuestion'][4];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointBT']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointNet'])) {
                        // alias 5 là Internet
                        $res = $condition['allQuestion'][5];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointNet']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointTV'])) {
                        // alias 6 là truyền hình
                        $res = $condition['allQuestion'][6];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointTV']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['NetErrorType'])) {
                        // alias 5 là internet
                        $res = $condition['allQuestion'][5];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_extra_id = ' . $condition['NetErrorType'] . ')');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['TVErrorType'])) {
                        // alias 6 là truyền hình
                        $res = $condition['allQuestion'][6];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_extra_id = ' . $condition['TVErrorType'] . ')');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointTV'])) {
                        if (in_array("1", $condition['CSATPointTV']) || in_array("2", $condition['CSATPointTV'])) {
                            if (!empty($condition['processingActionsTV'])) {
                                // alias 6 là truyền hình
                                $res = $condition['allQuestion'][6];
                                $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_action = ' . $condition['processingActionsTV'] . ')');
                            }
                        }
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (in_array("1", $condition['CSATPointNet']) || in_array("2", $condition['CSATPointNet'])) {
                            if (!empty($condition['processingActionsInternet'])) {
                                // alias 5 là internet
                                $res = $condition['allQuestion'][5];
                                $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_action = ' . $condition['processingActionsInternet'] . ')');
                            }
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['violationsType'])) {
                        $query->where('v.violationsType', '=', $condition['violationsType']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['punishment'])) {
                        $query->where('v.punishment', '=', $condition['punishment']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userReported'])) {
                        $query->where('v.created_user', '=', $condition['userReported']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['punishmentAdditional'])) {
                        $query->where('v.punishmentAdditional', '=', $condition['punishmentAdditional']);
                    }
                });

        $result = $mainQuery->count();
        return $result;
    }

    public function searchListSurveyViolations($condition, $numberPage) {
        $mainQuery = DB::table($this->table . ' as s')
                ->select(DB::raw("s.section_subsupporter, s.section_supporter, s.section_acc_sale AS salename"
                                . ",s.section_survey_id, s.section_connected, s.section_action, s.section_user_name, s.section_sub_parent_desc"
                                . ",s.section_location, s.section_note, s.section_time_start, s.section_time_completed, s.section_id"
                                . ",s.section_code,s.section_contract_num, s.section_contact_phone"
                                . ",s.section_branch_code, s.section_sale_branch_code, s.violation_status, s.section_location_id"
                                . ",v.type, v.point, v.explanationDescription, v.qs_verify, v.punishment, v.punishmentDescription"
                                . ",v.remedy, v.description, v.insert_at, v.created_user, v.modify_count, v.violationsType"
                                . ",v.discipline_ftq, v.punishmentAdditional, v.supporterName, v.accept_staff_dont_has_mistake"
                                ))
                ->join('survey_violations as v', 's.section_id', '=', 'v.sectionID')
                ->where(function($query) use ($condition) {
                    if (!empty($condition['surveyFrom']) && !empty($condition['surveyTo'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['surveyFromInt']);
                        $query->where('s.section_time_completed_int', '<=', $condition['surveyToInt']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['channelConfirm'])) {
                        $query->where('s.section_record_channel', '=', $condition['channelConfirm']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        if ($condition['type'] == 3 && $condition['channelConfirm'] == 2) {
                            $query->where('s.section_survey_id', '=', '7');
                        } else {
                            $query->where('s.section_survey_id', '=', $condition['type']);
                        }
                    }
                    if (!empty($condition['object'])) {
                        $object = $condition['object'];
                        switch ($condition['type']) {
                            case 2:
                                $object = 3;
                                break;
                            case 3:
                                $object = 6;
                                break;
                            case 4:
                                $object = 5;
                                break;
                            case 6:
                                $object = 4;
                                break;
                            default:
                        }
                        $query->where('v.type', '=', $object);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['saleName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['saleName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['locationSQL'])) {
                        $tempRaw = '(';
                        $isSecondRaw = false;
                        foreach ($condition['locationSQL'] as $key => $val) {
                            if ($isSecondRaw) {
                                $tempRaw .= ' or ';
                            }
                            $tempRaw .= '(s.section_branch_code = ' . $key . ' and s.section_location_id in (' . implode(',', $val) . '))';
                            $isSecondRaw = true;
                        }
                        $tempRaw .= ')';
                        $query->whereRaw($tempRaw);
                    }
                })
        ;

        $subQueryRaw = str_replace(array('%', '?'), array('%%', '"%s"'), $mainQuery->toSql());
        $subQueryRaw = vsprintf($subQueryRaw, $mainQuery->getBindings());
        $temp = explode('where', $subQueryRaw);
        $subQuery = "select distinct(v.id) from survey_violations as v join outbound_survey_sections as s on v.section_id = s.section_id left join outbound_survey_result as sr on s.section_id = sr.survey_result_section_id where" . $temp[1];

        $mainQuery->where(function($query) use ($condition, $subQuery) {
            if (!empty($condition['RateNPS'])) {
                // alias 9 làm thế nào để có nps 10
                $res = $condition['allQuestion'][9];
                $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id' . " like('%" . implode(",", $condition['RateNPS']) . "%'))");
            }
        });
        $mainQuery->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['NPSPoint'])) {
                        // alias 10 là điểm NPS
                        $res = $condition['allQuestion'][10];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['NPSPoint']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointSale'])) {
                        // alias 1,2 là nhân viên kinh doanh
                        $res = array_merge($condition['allQuestion'][1], $condition['allQuestion'][2]);
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointSale']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        // alias 3 là nhân viên triển khai
                        $res = $condition['allQuestion'][3];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointNVTK']) . '))');
                    }
                })
                ->where(function($query) use ($condition, $subQuery) {
                    if (!empty($condition['CSATPointBT'])) {
                        // alias 4 là nhân viên bảo trì
                        $res = $condition['allQuestion'][4];
                        $query->whereRaw("v.id in (" . $subQuery . ' and sr.survey_result_question_id in(' . implode(',', $res) . ') and sr.survey_result_answer_id in( ' . implode(",", $condition['CSATPointBT']) . '))');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['violationsType'])) {
                        $query->where('v.violationsType', '=', $condition['violationsType']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['punishment'])) {
                        $query->where('v.punishment', '=', $condition['punishment']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userReported'])) {
                        $query->where('v.created_user', '=', $condition['userReported']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['punishmentAdditional'])) {
                        $query->where('v.punishmentAdditional', '=', $condition['punishmentAdditional']);
                    }
                })
        ;

        if (!empty($condition['recordPerPage'])) {
            $mainQuery->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage']);
        }
        $result = $mainQuery->get();
        return $result;
    }

    public function getListSurveyViolationAndTotal($condition, $currentPage) {
        $result = DB::table(DB::raw('survey_section_report as s '
                                . 'INNER JOIN checklist as c ON s.section_code = c.section_code AND s.section_contract_num = c.section_contract_num AND s.section_survey_id = c.section_survey_id'))
                ->select(DB::raw("s.section_subsupporter, s.section_supporter, s.section_acc_sale AS salename, s.section_account_inf, s.section_account_list, s.csat_net_answer_extra_id, s.csat_tv_answer_extra_id,  s.section_survey_id, s.section_connected, s.section_action, s.section_user_name, s.section_sub_parent_desc, s.section_location, s.section_note, s.section_time_start, s.section_time_completed, s.section_id, s.section_code,s.section_contract_num, s.section_contact_phone, s.section_branch_code, s.violation_status, "
                                . "s.nps_point, s.nps_improvement, s.csat_salesman_point, s.csat_deployer_point, s.csat_net_point, s.csat_tv_point, s.csat_maintenance_staff_point, s.csat_maintenance_net_point, s.csat_maintenance_tv_point, "
                                . "csat_salesman_note, csat_deployer_note, csat_maintenance_staff_note,"
                                . "c.i_type , c.s_create_by, c.i_lnit_status, c.s_description, c.dept_id, c.created_at, c.updated_at, c.final_status, c.total_minute, c.input_time, c.assign, c.store_time, c.error_position, c.error_description"
                                . ", c.reason_description, c.way_solving, c.checklist_type, c.repeat_checklist, c.finish_date, null, null, null, null, 'CL' AS typeAction"))
                ->where(function($query) use ($condition) {
                    if (!empty($condition['survey_from']) && !empty($condition['survey_to'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['survey_from_int']);
                        $query->where('s.section_time_completed_int', '<=', $condition['survey_to_int']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['region'])) {
                        foreach ($condition['region'] as &$val) {
                            $val = 'Vung ' . $val;
                        }
                        $query->whereIn('s.section_sub_parent_desc', $condition['region']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['location'])) {
                        $query->whereIn('s.section_location_id', $condition['location']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        $query->where('s.section_survey_id', '=', $condition['type']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['salerName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['salerName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['branchcode'])) {
                        $query->whereIn('s.section_branch_code', $condition['branchcode']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['departmentType']) && ($condition['departmentType'] == 2 || $condition['departmentType'] == 3)) {//TIN hoặc PNC
                        if ($condition['departmentType'] == 2) {//TIN: Vùng 1,2,3,4
                            $regionTIN = ['Vung 1', 'Vung 2', 'Vung 3', 'Vung 4'];
                            $query->whereIn('s.section_sub_parent_desc', $regionTIN);
                        } else if ($condition['departmentType'] == 3) {
                            $regionPNC = ['Vung 4', 'Vung 5', 'Vung 6', 'Vung 7'];
                            $query->whereIn('s.section_sub_parent_desc', $regionPNC);
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if ($condition['departmentType'] == 2)//TIN
                        $query->whereRaw("s.section_supporter LIKE '%TIN%'");
                    else if ($condition['departmentType'] == 3)//PNC
                        $query->whereRaw("s.section_supporter LIKE '%PhuongNam%'");
                    else if ($condition['departmentType'] == 4)//INDO
                        $query->whereRaw("s.section_supporter LIKE '%INDO%'");
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['RateNPS'])) {
                        $query->whereRaw("s.nps_improvement LIKE('%" . implode(",", $condition['RateNPS']) . "%')");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['NPSPoint'])) {
                        $query->whereRaw('s.nps_point in(' . implode(",", $condition['NPSPoint']) . ")");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointSale'])) {
                        $query->whereRaw('s.csat_salesman_point in( ' . implode(",", $condition['CSATPointSale']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        $query->whereRaw('s.csat_deployer_point in( ' . implode(",", $condition['CSATPointNVTK']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointBT'])) {
                        $query->whereRaw('s.csat_maintenance_staff_point in( ' . implode(",", $condition['CSATPointBT']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointTV'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        }
                    }
                })
                ->where(function($query) use ($condition) {
            if (isset($condition['staffType'])) {
                if ($condition['staffType'] == 0) {
                    $query->whereIn('s.csat_salesman_point', [1, 2]);
                } elseif ($condition['staffType'] == 1) {
                    $query->whereRaw('s.csat_deployer_point in (1,2) or s.csat_maintenance_staff_point in (1,2)');
                }
            }
        });

        $resultPCL = DB::table(DB::raw('survey_section_report as s '
                                . 'INNER JOIN prechecklist as pc ON s.section_code = pc.section_code AND s.section_contract_num = pc.section_contract_num AND s.section_survey_id = pc.section_survey_id'))
                ->select(DB::raw("s.section_subsupporter, s.section_supporter, s.section_acc_sale AS salename, s.section_account_inf, s.section_account_list, s.csat_net_answer_extra_id, s.csat_tv_answer_extra_id, s.section_survey_id, s.section_connected, s.section_action, s.section_user_name, s.section_sub_parent_desc, s.section_location, s.section_note, s.section_time_start, s.section_time_completed, s.section_id, s.section_code,s.section_contract_num, s.section_contact_phone, s.section_branch_code, s.violation_status, "
                                . "s.nps_point, s.nps_improvement, s.csat_salesman_point, s.csat_deployer_point, s.csat_net_point, s.csat_tv_point, s.csat_maintenance_staff_point, s.csat_maintenance_net_point, s.csat_maintenance_tv_point, "
                                . "csat_salesman_note, csat_deployer_note, csat_maintenance_staff_note,"
                                . "null , pc.create_by, pc.first_status, pc.description, null, pc.created_at, pc.updated_at, pc.status, pc.total_minute, null as input_time, null as assign, null as store_time , null as error_position , null as error_description , null as reason_description, null as way_solving , null as checklist_type, null as repeat_checklist, null as finish_date, pc.appointment_timer, pc.count_sup, pc.action_process, pc.update_date, 'PCL' AS typeAction"
                ))
                ->where(function($query) use ($condition) {
                    if (!empty($condition['survey_from']) && !empty($condition['survey_to'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['survey_from_int']);
                        $query->where('s.section_time_completed_int', '<=', $condition['survey_to_int']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['region'])) {
                        foreach ($condition['region'] as &$val) {
                            $val = 'Vung ' . $val;
                        }
                        $query->whereIn('s.section_sub_parent_desc', $condition['region']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['location'])) {
                        $query->whereIn('s.section_location_id', $condition['location']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        $query->where('s.section_survey_id', '=', $condition['type']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['salerName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['salerName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['branchcode'])) {
                        $query->whereIn('s.section_branch_code', $condition['branchcode']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['departmentType']) && ($condition['departmentType'] == 2 || $condition['departmentType'] == 3)) {//TIN hoặc PNC
                        if ($condition['departmentType'] == 2) {//TIN: Vùng 1,2,3,4
                            $regionTIN = ['Vung 1', 'Vung 2', 'Vung 3', 'Vung 4'];
                            $query->whereIn('s.section_sub_parent_desc', $regionTIN);
                        } else if ($condition['departmentType'] == 3) {
                            $regionPNC = ['Vung 4', 'Vung 5', 'Vung 6', 'Vung 7'];
                            $query->whereIn('s.section_sub_parent_desc', $regionPNC);
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if ($condition['departmentType'] == 2)//TIN
                        $query->whereRaw("s.section_supporter LIKE '%TIN%'");
                    else if ($condition['departmentType'] == 3)//PNC
                        $query->whereRaw("s.section_supporter LIKE '%PhuongNam%'");
                    else if ($condition['departmentType'] == 4)//INDO
                        $query->whereRaw("s.section_supporter LIKE '%INDO%'");
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['RateNPS'])) {
                        $query->whereRaw("s.nps_improvement LIKE('%" . implode(",", $condition['RateNPS']) . "%')");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['NPSPoint'])) {
                        $query->whereRaw('s.nps_point in(' . implode(",", $condition['NPSPoint']) . ")");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointSale'])) {
                        $query->whereRaw('s.csat_salesman_point in( ' . implode(",", $condition['CSATPointSale']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        $query->whereRaw('s.csat_deployer_point in( ' . implode(",", $condition['CSATPointNVTK']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointBT'])) {
                        $query->whereRaw('s.csat_maintenance_staff_point in( ' . implode(",", $condition['CSATPointBT']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointTV'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        }
                    }
                })
                ->where(function($query) use ($condition) {
            if (isset($condition['staffType'])) {
                if ($condition['staffType'] == 0) {
                    $query->whereIn('s.csat_salesman_point', [1, 2]);
                } elseif ($condition['staffType'] == 1) {
                    $query->whereRaw('s.csat_deployer_point in (1,2) or s.csat_maintenance_staff_point in (1,2)');
                }
            }
        });
        $resultClPlusPCL = $resultPCL->union($result);
        $resultClPlusPCLTotal = $resultClPlusPCL->get();
        if (!empty($condition['recordPerPage'])) {
            $resultClPlusPCL->take($condition['recordPerPage'])->skip($currentPage * $condition['recordPerPage']);
        }
        $resultClPlusPCL = $resultClPlusPCL->get();
        return ['listSurvey' => $resultClPlusPCL, 'total' => count($resultClPlusPCLTotal)];
    }

    public function searchListSurveyServiceViolations($condition, $numberPage) {
        $result = $this->searchListSurveyServiceCLViolations($condition, $numberPage);
        return $result;
//        $resultPCL = $this->searchListSurveyServicePCLViolations($condition, $numberPage);
//        $resultFD = $this->searchListSurveyServiceFDViolations;
    }

    //Tìm khảo sát Checklist
    public function searchListSurveyServiceCLViolations($condition, $numberPage) {
        $result = DB::table(DB::raw('survey_section_report as s '
                                . 'INNER JOIN checklist as c ON s.section_code = c.section_code AND s.section_contract_num = c.section_contract_num AND s.section_survey_id = c.section_survey_id'))
                ->select(DB::raw("s.section_subsupporter, s.section_supporter, s.section_acc_sale AS salename, s.section_account_inf, s.section_account_list, s.csat_net_answer_extra_id, s.csat_tv_answer_extra_id,  s.section_survey_id, s.section_connected, s.section_action, s.section_user_name, s.section_sub_parent_desc, s.section_location, s.section_note, s.section_time_start, s.section_time_completed, s.section_id, s.section_code,s.section_contract_num, s.section_contact_phone, s.section_branch_code, s.violation_status, "
                                . "s.nps_point, s.nps_improvement, s.csat_salesman_point, s.csat_deployer_point, s.csat_net_point, s.csat_tv_point, s.csat_maintenance_staff_point, s.csat_maintenance_net_point, s.csat_maintenance_tv_point, "
                                . "csat_salesman_note, csat_deployer_note, csat_maintenance_staff_note,"
                                . "c.i_type , c.s_create_by, c.i_lnit_status, c.s_description, c.dept_id, c.created_at, c.updated_at, c.final_status, c.total_minute, c.input_time, c.assign, c.store_time, c.error_position, c.error_description"
                                . ", c.reason_description, c.way_solving, c.checklist_type, c.repeat_checklist, c.finish_date, null, null, null, null, 'CL' AS typeAction"))
                ->where(function($query) use ($condition) {
                    if (!empty($condition['survey_from']) && !empty($condition['survey_to'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['survey_from_int']);
                        $query->where('s.section_time_completed_int', '<=', $condition['survey_to_int']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['region'])) {
                        foreach ($condition['region'] as &$val) {
                            $val = 'Vung ' . $val;
                        }
                        $query->whereIn('s.section_sub_parent_desc', $condition['region']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['location'])) {
                        $query->whereIn('s.section_location_id', $condition['location']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        $query->where('s.section_survey_id', '=', $condition['type']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['salerName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['salerName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['branchcode'])) {
                        $query->whereIn('s.section_branch_code', $condition['branchcode']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['departmentType']) && ($condition['departmentType'] == 2 || $condition['departmentType'] == 3)) {//TIN hoặc PNC
                        if ($condition['departmentType'] == 2) {//TIN: Vùng 1,2,3,4
                            $regionTIN = ['Vung 1', 'Vung 2', 'Vung 3', 'Vung 4'];
                            $query->whereIn('s.section_sub_parent_desc', $regionTIN);
                        } else if ($condition['departmentType'] == 3) {
                            $regionPNC = ['Vung 4', 'Vung 5', 'Vung 6', 'Vung 7'];
                            $query->whereIn('s.section_sub_parent_desc', $regionPNC);
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if ($condition['departmentType'] == 2)//TIN
                        $query->whereRaw("s.section_supporter LIKE '%TIN%'");
                    else if ($condition['departmentType'] == 3)//PNC
                        $query->whereRaw("s.section_supporter LIKE '%PhuongNam%'");
                    else if ($condition['departmentType'] == 4)//INDO
                        $query->whereRaw("s.section_supporter LIKE '%INDO%'");
                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['violations_type'])) {
//                        $query->where('v.violations_type', '=', $condition['violations_type']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['punish'])) {
//                        $query->where('v.punishment', '=', $condition['punish']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['discipline'])) {
//                        if ((int) ($condition['discipline']) > 0) {
//                            $query->whereRaw('v.additional_discipline IS NOT NULL');
//                        } else if ((int) ($condition['discipline']) === 0) {
//                            $query->whereRaw('v.additional_discipline IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['remedy'])) {
//                        if ((int) ($condition['remedy']) > 0) {
//                            $query->whereRaw('v.remedy IS NOT NULL');
//                        } else if ((int) ($condition['remedy']) === 0) {
//                            $query->whereRaw('v.remedy IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['userReported'])) {
//                        $query->where('v.created_user', '=', $condition['userReported']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['editedReport']) && (int) ($condition['editedReport']) > 0) {
//                        $query->where('v.modify_count', '=', $condition['editedReport']);
//                    }
//                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['RateNPS'])) {
                        $query->whereRaw("s.nps_improvement LIKE('%" . implode(",", $condition['RateNPS']) . "%')");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['NPSPoint'])) {
                        $query->whereRaw('s.nps_point in(' . implode(",", $condition['NPSPoint']) . ")");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointSale'])) {
                        $query->whereRaw('s.csat_salesman_point in( ' . implode(",", $condition['CSATPointSale']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        $query->whereRaw('s.csat_deployer_point in( ' . implode(",", $condition['CSATPointNVTK']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointBT'])) {
                        $query->whereRaw('s.csat_maintenance_staff_point in( ' . implode(",", $condition['CSATPointBT']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointTV'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        }
                    }
                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['disciplineFTQ'])) {
//                        if ((int) ($condition['disciplineFTQ']) > 0) {
//                            $query->whereRaw('v.discipline_ftq IS NOT NULL');
//                        } else if (($condition['disciplineFTQ']) == 0) {
//                            $query->whereRaw('v.discipline_ftq IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['punishAdditional'])) {
//                        $query->where('v.punishment_additional', '=', $condition['punishAdditional']);
//                    }
//                })
                ->where(function($query) use ($condition) {
            if (isset($condition['staffType'])) {
                if ($condition['staffType'] == 0) {
                    $query->whereIn('s.csat_salesman_point', [1, 2]);
                } elseif ($condition['staffType'] == 1) {
                    $query->whereRaw('s.csat_deployer_point in (1,2) or s.csat_maintenance_staff_point in (1,2)');
                }
            }
        });

        $resultPCL = DB::table(DB::raw('survey_section_report as s '
                                . 'INNER JOIN prechecklist as pc ON s.section_code = pc.section_code AND s.section_contract_num = pc.section_contract_num AND s.section_survey_id = pc.section_survey_id'))
                ->select(DB::raw("s.section_subsupporter, s.section_supporter, s.section_acc_sale AS salename, s.section_account_inf, s.section_account_list, s.csat_net_answer_extra_id, s.csat_tv_answer_extra_id, s.section_survey_id, s.section_connected, s.section_action, s.section_user_name, s.section_sub_parent_desc, s.section_location, s.section_note, s.section_time_start, s.section_time_completed, s.section_id, s.section_code,s.section_contract_num, s.section_contact_phone, s.section_branch_code, s.violation_status, "
                                . "s.nps_point, s.nps_improvement, s.csat_salesman_point, s.csat_deployer_point, s.csat_net_point, s.csat_tv_point, s.csat_maintenance_staff_point, s.csat_maintenance_net_point, s.csat_maintenance_tv_point, "
                                . "csat_salesman_note, csat_deployer_note, csat_maintenance_staff_note,"
                                . "null , pc.create_by, pc.first_status, pc.description, null, pc.created_at, pc.updated_at, pc.status, pc.total_minute, null as input_time, null as assign, null as store_time , null as error_position , null as error_description , null as reason_description, null as way_solving , null as checklist_type, null as repeat_checklist, null as finish_date, pc.appointment_timer, pc.count_sup, pc.action_process, pc.update_date, 'PCL' AS typeAction"
                ))
                ->where(function($query) use ($condition) {
                    if (!empty($condition['survey_from']) && !empty($condition['survey_to'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['survey_from_int']);
                        $query->where('s.section_time_completed_int', '<=', $condition['survey_to_int']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['region'])) {
                        foreach ($condition['region'] as &$val) {
                            $val = 'Vung ' . $val;
                        }
                        $query->whereIn('s.section_sub_parent_desc', $condition['region']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['location'])) {
                        $query->whereIn('s.section_location_id', $condition['location']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        $query->where('s.section_survey_id', '=', $condition['type']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['salerName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['salerName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['branchcode'])) {
                        $query->whereIn('s.section_branch_code', $condition['branchcode']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['departmentType']) && ($condition['departmentType'] == 2 || $condition['departmentType'] == 3)) {//TIN hoặc PNC
                        if ($condition['departmentType'] == 2) {//TIN: Vùng 1,2,3,4
                            $regionTIN = ['Vung 1', 'Vung 2', 'Vung 3', 'Vung 4'];
                            $query->whereIn('s.section_sub_parent_desc', $regionTIN);
                        } else if ($condition['departmentType'] == 3) {
                            $regionPNC = ['Vung 4', 'Vung 5', 'Vung 6', 'Vung 7'];
                            $query->whereIn('s.section_sub_parent_desc', $regionPNC);
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if ($condition['departmentType'] == 2)//TIN
                        $query->whereRaw("s.section_supporter LIKE '%TIN%'");
                    else if ($condition['departmentType'] == 3)//PNC
                        $query->whereRaw("s.section_supporter LIKE '%PhuongNam%'");
                    else if ($condition['departmentType'] == 4)//INDO
                        $query->whereRaw("s.section_supporter LIKE '%INDO%'");
                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['violations_type'])) {
//                        $query->where('v.violations_type', '=', $condition['violations_type']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['punish'])) {
//                        $query->where('v.punishment', '=', $condition['punish']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['discipline'])) {
//                        if ((int) ($condition['discipline']) > 0) {
//                            $query->whereRaw('v.additional_discipline IS NOT NULL');
//                        } else if ((int) ($condition['discipline']) === 0) {
//                            $query->whereRaw('v.additional_discipline IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['remedy'])) {
//                        if ((int) ($condition['remedy']) > 0) {
//                            $query->whereRaw('v.remedy IS NOT NULL');
//                        } else if ((int) ($condition['remedy']) === 0) {
//                            $query->whereRaw('v.remedy IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['userReported'])) {
//                        $query->where('v.created_user', '=', $condition['userReported']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['editedReport']) && (int) ($condition['editedReport']) > 0) {
//                        $query->where('v.modify_count', '=', $condition['editedReport']);
//                    }
//                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['RateNPS'])) {
                        $query->whereRaw("s.nps_improvement LIKE('%" . implode(",", $condition['RateNPS']) . "%')");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['NPSPoint'])) {
                        $query->whereRaw('s.nps_point in(' . implode(",", $condition['NPSPoint']) . ")");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointSale'])) {
                        $query->whereRaw('s.csat_salesman_point in( ' . implode(",", $condition['CSATPointSale']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        $query->whereRaw('s.csat_deployer_point in( ' . implode(",", $condition['CSATPointNVTK']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointBT'])) {
                        $query->whereRaw('s.csat_maintenance_staff_point in( ' . implode(",", $condition['CSATPointBT']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointTV'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        }
                    }
                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['disciplineFTQ'])) {
//                        if ((int) ($condition['disciplineFTQ']) > 0) {
//                            $query->whereRaw('v.discipline_ftq IS NOT NULL');
//                        } else if (($condition['disciplineFTQ']) == 0) {
//                            $query->whereRaw('v.discipline_ftq IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['punishAdditional'])) {
//                        $query->where('v.punishment_additional', '=', $condition['punishAdditional']);
//                    }
//                })
                ->where(function($query) use ($condition) {
            if (isset($condition['staffType'])) {
                if ($condition['staffType'] == 0) {
                    $query->whereIn('s.csat_salesman_point', [1, 2]);
                } elseif ($condition['staffType'] == 1) {
                    $query->whereRaw('s.csat_deployer_point in (1,2) or s.csat_maintenance_staff_point in (1,2)');
                }
            }
        });
        $resultClPlusPCL = $resultPCL->union($result);
//        $resultClPlusPCL=DB::table('users')
//            ->whereNull('last_name')
//            ->union($first)
//            ->get();
        if (!empty($condition['recordPerPage'])) {
            $resultClPlusPCL->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage']);
        }
        $resultClPlusPCL = $resultClPlusPCL->get();
        return $resultClPlusPCL;
    }

    //Tìm khảo sát Prechecklist
    public function searchListSurveyServicePCLViolations($condition, $numberPage) {
        $result = DB::table(DB::raw('survey_section_report as s '
                                . 'INNER JOIN prechecklist as pc ON s.section_id = pc.section_id'))
                ->select(DB::raw("s.section_subsupporter, s.section_supporter, s.section_acc_sale AS salename, s.section_survey_id, s.section_connected, s.section_action, s.section_user_name, s.section_sub_parent_desc, s.section_location, s.section_note, s.section_time_start, s.section_time_completed, s.section_id, s.section_code,s.section_contract_num, s.section_contact_phone, s.section_branch_code, s.violation_status, "
                                . "s.nps_point, s.nps_improvement, s.csat_salesman_point, s.csat_deployer_point, s.csat_net_point, s.csat_tv_point, s.csat_maintenance_staff_point, s.csat_maintenance_net_point, s.csat_maintenance_tv_point, "
                                . "csat_salesman_note, csat_deployer_note, csat_maintenance_staff_note,"
                                . "pc.*"))
                ->where(function($query) use ($condition) {
                    if (!empty($condition['survey_from']) && !empty($condition['survey_to'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['survey_from_int']);
                        $query->where('s.section_time_completed_int', '<=', $condition['survey_to_int']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['region'])) {
                        foreach ($condition['region'] as &$val) {
                            $val = 'Vung ' . $val;
                        }
                        $query->whereIn('s.section_sub_parent_desc', $condition['region']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['location'])) {
                        $query->whereIn('s.section_location_id', $condition['location']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        $query->where('s.section_survey_id', '=', $condition['type']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['salerName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['salerName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['branchcode'])) {
                        $query->whereIn('s.section_branch_code', $condition['branchcode']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['departmentType']) && ($condition['departmentType'] == 2 || $condition['departmentType'] == 3)) {//TIN hoặc PNC
                        if ($condition['departmentType'] == 2) {//TIN: Vùng 1,2,3,4
                            $regionTIN = ['Vung 1', 'Vung 2', 'Vung 3', 'Vung 4'];
                            $query->whereIn('s.section_sub_parent_desc', $regionTIN);
                        } else if ($condition['departmentType'] == 3) {
                            $regionPNC = ['Vung 4', 'Vung 5', 'Vung 6', 'Vung 7'];
                            $query->whereIn('s.section_sub_parent_desc', $regionPNC);
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if ($condition['departmentType'] == 2)//TIN
                        $query->whereRaw("s.section_supporter LIKE '%TIN%'");
                    else if ($condition['departmentType'] == 3)//PNC
                        $query->whereRaw("s.section_supporter LIKE '%PhuongNam%'");
                    else if ($condition['departmentType'] == 4)//INDO
                        $query->whereRaw("s.section_supporter LIKE '%INDO%'");
                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['violations_type'])) {
//                        $query->where('v.violations_type', '=', $condition['violations_type']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['punish'])) {
//                        $query->where('v.punishment', '=', $condition['punish']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['discipline'])) {
//                        if ((int) ($condition['discipline']) > 0) {
//                            $query->whereRaw('v.additional_discipline IS NOT NULL');
//                        } else if ((int) ($condition['discipline']) === 0) {
//                            $query->whereRaw('v.additional_discipline IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['remedy'])) {
//                        if ((int) ($condition['remedy']) > 0) {
//                            $query->whereRaw('v.remedy IS NOT NULL');
//                        } else if ((int) ($condition['remedy']) === 0) {
//                            $query->whereRaw('v.remedy IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['userReported'])) {
//                        $query->where('v.created_user', '=', $condition['userReported']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['editedReport']) && (int) ($condition['editedReport']) > 0) {
//                        $query->where('v.modify_count', '=', $condition['editedReport']);
//                    }
//                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['RateNPS'])) {
                        $query->whereRaw("s.nps_improvement LIKE('%" . implode(",", $condition['RateNPS']) . "%')");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['NPSPoint'])) {
                        $query->whereRaw('s.nps_point in(' . implode(",", $condition['NPSPoint']) . ")");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointSale'])) {
                        $query->whereRaw('s.csat_salesman_point in( ' . implode(",", $condition['CSATPointSale']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        $query->whereRaw('s.csat_deployer_point in( ' . implode(",", $condition['CSATPointNVTK']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointBT'])) {
                        $query->whereRaw('s.csat_maintenance_staff_point in( ' . implode(",", $condition['CSATPointBT']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointTV'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        }
                    }
                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['disciplineFTQ'])) {
//                        if ((int) ($condition['disciplineFTQ']) > 0) {
//                            $query->whereRaw('v.discipline_ftq IS NOT NULL');
//                        } else if (($condition['disciplineFTQ']) == 0) {
//                            $query->whereRaw('v.discipline_ftq IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['punishAdditional'])) {
//                        $query->where('v.punishment_additional', '=', $condition['punishAdditional']);
//                    }
//                })
                ->where(function($query) use ($condition) {
            if (isset($condition['staffType'])) {
                if ($condition['staffType'] == 0) {
                    $query->whereIn('s.csat_salesman_point', [1, 2]);
                } elseif ($condition['staffType'] == 1) {
                    $query->whereRaw('s.csat_deployer_point in (1,2) or s.csat_maintenance_staff_point in (1,2)');
                }
            }
        });
        if (!empty($condition['recordPerPage'])) {
            $result->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage']);
        }
        $result = $result->get();
        return $result;
    }

    //Tìm thông tin chuyển tiếp phòng ban
    public function searchListSurveyServiceFDViolations($condition, $numberPage) {
        $result = DB::table(DB::raw('survey_section_report as s '
                                . 'INNER JOIN foward_department as f ON f.section_id = c.section_id'))
                ->select(DB::raw("s.section_subsupporter, s.section_supporter, s.section_acc_sale AS salename, s.section_survey_id, s.section_connected, s.section_action, s.section_user_name, s.section_sub_parent_desc, s.section_location, s.section_note, s.section_time_start, s.section_time_completed, s.section_id, s.section_code,s.section_contract_num, s.section_contact_phone, s.section_branch_code, s.violation_status, "
                                . "s.nps_point, s.nps_improvement, s.csat_salesman_point, s.csat_deployer_point, s.csat_net_point, s.csat_tv_point, s.csat_maintenance_staff_point, s.csat_maintenance_net_point, s.csat_maintenance_tv_point, "
                                . "csat_salesman_note, csat_deployer_note, csat_maintenance_staff_note,"
                                . "f.*"))
                ->where(function($query) use ($condition) {
                    if (!empty($condition['survey_from']) && !empty($condition['survey_to'])) {
                        $query->where('s.section_time_completed_int', '>=', $condition['survey_from_int']);
                        $query->where('s.section_time_completed_int', '<=', $condition['survey_to_int']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['region'])) {
                        foreach ($condition['region'] as &$val) {
                            $val = 'Vung ' . $val;
                        }
                        $query->whereIn('s.section_sub_parent_desc', $condition['region']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['location'])) {
                        $query->whereIn('s.section_location_id', $condition['location']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['contractNum'])) {
                        $query->where('s.section_contract_num', '=', $condition['contractNum']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['type'])) {
                        $query->where('s.section_survey_id', '=', $condition['type']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_action'])) {
                        $query->whereIn('s.section_action', $condition['section_action']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['section_connected'])) {
                        $query->whereIn('s.section_connected', $condition['section_connected']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['userSurvey'])) {
                        $query->where('s.section_user_name', '=', $condition['userSurvey']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['salerName'])) {
                        $query->where('s.section_acc_sale', '=', $condition['salerName']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['technicalStaff'])) {
                        $query->orWhere('s.section_account_inf', '=', $condition['technicalStaff']);
                        $query->orWhere('s.section_account_list', '=', $condition['technicalStaff']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['branchcode'])) {
                        $query->whereIn('s.section_branch_code', $condition['branchcode']);
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['departmentType']) && ($condition['departmentType'] == 2 || $condition['departmentType'] == 3)) {//TIN hoặc PNC
                        if ($condition['departmentType'] == 2) {//TIN: Vùng 1,2,3,4
                            $regionTIN = ['Vung 1', 'Vung 2', 'Vung 3', 'Vung 4'];
                            $query->whereIn('s.section_sub_parent_desc', $regionTIN);
                        } else if ($condition['departmentType'] == 3) {
                            $regionPNC = ['Vung 4', 'Vung 5', 'Vung 6', 'Vung 7'];
                            $query->whereIn('s.section_sub_parent_desc', $regionPNC);
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if ($condition['departmentType'] == 2)//TIN
                        $query->whereRaw("s.section_supporter LIKE '%TIN%'");
                    else if ($condition['departmentType'] == 3)//PNC
                        $query->whereRaw("s.section_supporter LIKE '%PhuongNam%'");
                    else if ($condition['departmentType'] == 4)//INDO
                        $query->whereRaw("s.section_supporter LIKE '%INDO%'");
                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['violations_type'])) {
//                        $query->where('v.violations_type', '=', $condition['violations_type']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['punish'])) {
//                        $query->where('v.punishment', '=', $condition['punish']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['discipline'])) {
//                        if ((int) ($condition['discipline']) > 0) {
//                            $query->whereRaw('v.additional_discipline IS NOT NULL');
//                        } else if ((int) ($condition['discipline']) === 0) {
//                            $query->whereRaw('v.additional_discipline IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['remedy'])) {
//                        if ((int) ($condition['remedy']) > 0) {
//                            $query->whereRaw('v.remedy IS NOT NULL');
//                        } else if ((int) ($condition['remedy']) === 0) {
//                            $query->whereRaw('v.remedy IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['userReported'])) {
//                        $query->where('v.created_user', '=', $condition['userReported']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['editedReport']) && (int) ($condition['editedReport']) > 0) {
//                        $query->where('v.modify_count', '=', $condition['editedReport']);
//                    }
//                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['RateNPS'])) {
                        $query->whereRaw("s.nps_improvement LIKE('%" . implode(",", $condition['RateNPS']) . "%')");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['NPSPoint'])) {
                        $query->whereRaw('s.nps_point in(' . implode(",", $condition['NPSPoint']) . ")");
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointSale'])) {
                        $query->whereRaw('s.csat_salesman_point in( ' . implode(",", $condition['CSATPointSale']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNVTK'])) {
                        $query->whereRaw('s.csat_deployer_point in( ' . implode(",", $condition['CSATPointNVTK']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointBT'])) {
                        $query->whereRaw('s.csat_maintenance_staff_point in( ' . implode(",", $condition['CSATPointBT']) . ')');
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_net_point in( ' . implode(",", $condition['CSATPointNet']) . ')');
                        }
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['CSATPointTV'])) {
                        if (!empty($condition['type']) && $condition['type'] == 1) { // triển khai
                            $query->whereRaw('s.csat_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        } else {
                            $query->whereRaw('s.csat_maintenance_tv_point in( ' . implode(",", $condition['CSATPointTV']) . ')');
                        }
                    }
                })
//                ->where(function($query) use ($condition) {
//                    if (isset($condition['disciplineFTQ'])) {
//                        if ((int) ($condition['disciplineFTQ']) > 0) {
//                            $query->whereRaw('v.discipline_ftq IS NOT NULL');
//                        } else if (($condition['disciplineFTQ']) == 0) {
//                            $query->whereRaw('v.discipline_ftq IS NULL');
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['punishAdditional'])) {
//                        $query->where('v.punishment_additional', '=', $condition['punishAdditional']);
//                    }
//                })
                ->where(function($query) use ($condition) {
            if (isset($condition['staffType'])) {
                if ($condition['staffType'] == 0) {
                    $query->whereIn('s.csat_salesman_point', [1, 2]);
                } elseif ($condition['staffType'] == 1) {
                    $query->whereRaw('s.csat_deployer_point in (1,2) or s.csat_maintenance_staff_point in (1,2)');
                }
            }
        });
        if (!empty($condition['recordPerPage'])) {
            $result->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage']);
        }
        $result = $result->get();
        return $result;
    }

    public function countAllSurveyInfoUser($userId, $filter, $listIdResult, $listTypeSurvey, $listRegion, $listAction, $listNps, $listCsatEmp, $listCsatDep, $listCsatInt, $listCsatTv) {
        $subSelectRaw = '( select a.answers_point
				from outbound_survey_result r
				join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                WHERE r.survey_result_answer_id in ("6","7","8","9","10","11","12","13","14","15") 
                                    AND r.survey_result_question_id in ("6","8")
                                    AND r.survey_result_section_id = s.section_id
				 ) as nps,
    			( select a.answers_point
					from outbound_survey_result r
					join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                        WHERE s.section_id = r.survey_result_section_id
					 AND r.survey_result_answer_id in ("1","2","3","4","5") AND r.survey_result_question_id = 1 
				) as csat_kinhdoanh,	
    			( select a.answers_point
					from outbound_survey_result r
					join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                        WHERE s.section_id = r.survey_result_section_id
                                            AND r.survey_result_answer_id in ("1","2","3","4","5") AND r.survey_result_question_id = 2 
				) as csatkythuat,
    			( select a.answers_point
					from outbound_survey_result r
					join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                        WHERE s.section_id = r.survey_result_section_id
                                            AND r.survey_result_answer_id in ("1","2","3","4","5") AND r.survey_result_question_id in ("10","12")
				) as csatinternet,
    			( select a.answers_point
					from outbound_survey_result r
					join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                        WHERE s.section_id = r.survey_result_section_id
                                            AND r.survey_result_answer_id in ("1","2","3","4","5") AND r.survey_result_question_id in ("11","13")
				) as csattruyenhinh
    	';
        $result = DB::table('outbound_survey_sections as s')
//            ->groupBy('s.section_code')
                ->select(DB::raw("s.section_id,s.section_contract_num,s.section_customer_name,s.section_connected,s.section_note,s.section_action,s.section_survey_id,s.section_user_name,s.section_time_completed,s.section_time_completed," . $subSelectRaw))
                ->where('s.section_user_id', '=', $userId)
                ->where('s.section_time_completed_int', '>=', strtotime(date('Y-m-d', strtotime(str_replace('/', '-', $filter['startDate']))) . ' 00:00:00'))
                ->where('s.section_time_completed_int', '<=', strtotime(date('Y-m-d', strtotime(str_replace('/', '-', $filter['endDate']))) . ' 23:59:59'))
                ->where(function($query) use ($filter) {
                    if (isset($filter['contract']) && ($filter['contract'] != '')) {
                        $query->where('s.section_contract_num', '=', $filter['contract']);
                    }
                })
                ->where(function($query) use ($listIdResult) {
                    if (!empty($listIdResult)) {
                        $comma_separated = implode(",", $listIdResult);
                        $query->whereIn('s.section_connected', explode(',', $comma_separated));
//                    $query->where('survey_sections.section_connected', '=', $filter['contactResult']);
                    }
                })
                ->where(function($query) use ($listTypeSurvey) {
                    if (!empty($listTypeSurvey)) {
                        $comma_separated_type = implode(",", $listTypeSurvey);
                        $query->whereIn('section_survey_id', explode(',', $comma_separated_type));
//                    $query->where('survey_sections.section_connected', '=', $filter['contactResult']);
                    }
                })
                ->where(function($query) use ($listRegion) {
                    if (!empty($listRegion)) {
                        $comma_separated_region = implode(",", $listRegion);
                        $query->whereIn('s.section_sub_parent_desc', explode(',', $comma_separated_region));
//                    $query->where('survey_sections.section_connected', '=', $filter['contactResult']);
                    }
                })
                ->where(function($query) use ($listAction) {
            if (!empty($listAction)) {
                $comma_separated_action = implode(",", $listAction);
                $query->whereIn('s.section_action', explode(',', $comma_separated_action));
//                    $query->where('survey_sections.section_connected', '=', $filter['contactResult']);
            }
        });
        if (!empty($listNps)) {
            $result->havingRaw('nps in( ' . implode(",", $listNps) . ')');
        }
        if (!empty($listCsatEmp)) {
            $result->havingRaw('csat_kinhdoanh in( ' . implode(",", $listCsatEmp) . ')');
        }
        if (!empty($listCsatDep)) {
            $result->havingRaw('csatkythuat in( ' . implode(",", $listCsatDep) . ')');
        }
        if (!empty($listCsatInt)) {
            $result->havingRaw('csatinternet in( ' . implode(",", $listCsatInt) . ')');
        }
        if (!empty($listCsatTv)) {
            $result->havingRaw('csattruyenhinh in( ' . implode(",", $listCsatTv) . ')');
        }
        $result = $result->orderBy('s.section_time_completed', 'DESC')
                ->get();
//        echo "<pre>";
//        echo $result->toSql();
//        var_dump($result->getBindings());
//        die;
        return count($result);
    }

    public function getAllSurveyInfoUser($userId, $itemPer, $pageNum, $filter, $listIdResult, $listTypeSurvey, $listRegion, $listAction, $listNps, $listCsatEmp, $listCsatDep, $listCsatInt, $listCsatTv) {
        $subSelectRaw = '( select a.answers_point
				from outbound_survey_result r
				join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                WHERE r.survey_result_answer_id in ("6","7","8","9","10","11","12","13","14","15") 
                                    AND r.survey_result_question_id in ("6","8")
                                    AND r.survey_result_section_id = s.section_id
				 ) as nps,
    			( select a.answers_point
					from outbound_survey_result r
					join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                        WHERE s.section_id = r.survey_result_section_id
					 AND r.survey_result_answer_id in ("1","2","3","4","5") AND r.survey_result_question_id = 1 
				) as csat_kinhdoanh,	
    			( select a.answers_point
					from outbound_survey_result r
					join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                        WHERE s.section_id = r.survey_result_section_id
                                            AND r.survey_result_answer_id in ("1","2","3","4","5") AND r.survey_result_question_id = 2 
				) as csatkythuat,
    			( select a.answers_point
					from outbound_survey_result r
					join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                        WHERE s.section_id = r.survey_result_section_id
                                            AND r.survey_result_answer_id in ("1","2","3","4","5") AND r.survey_result_question_id in ("10","12")
				) as csatinternet,
    			( select a.answers_point
					from outbound_survey_result r
					join outbound_answers a on a.answer_id = r.survey_result_answer_id
                                        WHERE s.section_id = r.survey_result_section_id
                                            AND r.survey_result_answer_id in ("1","2","3","4","5") AND r.survey_result_question_id in ("11","13")
				) as csattruyenhinh
    	';
//        $date = str_replace('/', '-', $filter['startDate']);
//        var_dump(date('Y-m-d', strtotime(str_replace('/', '-', $filter['startDate']))));
        $offset = ($pageNum - 1) * $itemPer;
        $result = DB::table('outbound_survey_sections AS s')
                ->select(DB::raw("s.section_id,s.section_contract_num,s.section_customer_name,s.section_connected,s.section_note,s.section_action,s.section_survey_id,s.section_user_name,s.section_time_completed,s.section_time_completed," . $subSelectRaw))
                ->where('s.section_user_id', '=', $userId)
                ->where('s.section_time_completed_int', '>=', strtotime(date('Y-m-d', strtotime(str_replace('/', '-', $filter['startDate']))) . ' 00:00:00'))
                ->where('s.section_time_completed_int', '<=', strtotime(date('Y-m-d', strtotime(str_replace('/', '-', $filter['endDate']))) . '23:59:59'))
                ->where(function($query) use ($filter) {
                    if (isset($filter['contract']) && ($filter['contract'] != '')) {
                        $query->where('s.section_contract_num', '=', $filter['contract']);
                    }
                })
                ->where(function($query) use ($listIdResult) {
                    if (!empty($listIdResult)) {
                        $comma_separated = implode(",", $listIdResult);
                        $query->whereIn('section_connected', explode(',', $comma_separated));
//                    $query->where('survey_sections.section_connected', '=', $filter['contactResult']);
                    }
                })
                ->where(function($query) use ($listTypeSurvey) {
                    if (!empty($listTypeSurvey)) {
                        $comma_separated_type = implode(",", $listTypeSurvey);
                        $query->whereIn('section_survey_id', explode(',', $comma_separated_type));
//                    $query->where('survey_sections.section_connected', '=', $filter['contactResult']);
                    }
                })
                ->where(function($query) use ($listRegion) {
                    if (!empty($listRegion)) {
                        $comma_separated_region = implode(",", $listRegion);
                        $query->whereIn('section_sub_parent_desc', explode(',', $comma_separated_region));
//                    $query->where('survey_sections.section_connected', '=', $filter['contactResult']);
                    }
                })
                ->where(function($query) use ($listAction) {
            if (!empty($listAction)) {
                $comma_separated_action = implode(",", $listAction);
                $query->whereIn('section_action', explode(',', $comma_separated_action));
//                    $query->where('survey_sections.section_connected', '=', $filter['contactResult']);
            }
        });
        if (!empty($listNps)) {
            $result->havingRaw('nps in( ' . implode(",", $listNps) . ')');
        }
        if (!empty($listCsatEmp)) {
            $result->havingRaw('csat_kinhdoanh in( ' . implode(",", $listCsatEmp) . ')');
        }
        if (!empty($listCsatDep)) {
            $result->havingRaw('csatkythuat in( ' . implode(",", $listCsatDep) . ')');
        }
        if (!empty($listCsatInt)) {
            $result->havingRaw('csatinternet in( ' . implode(",", $listCsatInt) . ')');
        }
        if (!empty($listCsatTv)) {
            $result->havingRaw('csattruyenhinh in( ' . implode(",", $listCsatTv) . ')');
        }
        $result = $result->orderBy('section_time_completed', 'DESC')
                ->skip($offset)
                ->take($itemPer)
                ->get();

        $currentDate = new \DateTime();
        $arraySurvey = array();
        $roleID = User::getRole($userId);
        $timeLimit = ($roleID == 2) ? 'P30D' : 'PT5M';
//        $messageEdit = ($roleID == 2) ? 'Khảo sát này đã vượt quá 30 ngày để sửa' : 'Khảo sát này đã vượt quá 5 phút để sửa';
//        $messageRetry = ($roleID == 2) ? 'Khảo sát này đã vượt quá 30 ngày để khảo sát lại' : 'Khảo sát này đã vượt quá 5 phút để khảo sát lại';
        foreach ($result as $key => $value) {
            $value = (array) $value;
            if ($value['section_connected'] == 4) {
                $time_complete = new \DateTime($value['section_time_completed']);
                $time_complete->add(new \DateInterval($timeLimit));
                if ($time_complete >= $currentDate) {
                    $value['edit'] = 1;
                } else {
                    $value['edit'] = 2;
                }
                $value['retry'] = 3;
            } else {
//                $currentDate = new \DateTime();
//                $time_complete = new \DateTime($value['section_time_completed']);
//                $time_complete->add(new \DateInterval($timeLimit));
//                if ($time_complete < $currentDate) {
//                    $value['retry'] = 2;
//                } else {
                $value['retry'] = 1;
//                }
//                if($value['section_count_connected']<=2)
//                    $value['retry']=1;
//                else  $value['retry']=2;
                $value['edit'] = 3;
            }
            $value = (object) $value;
            array_push($arraySurvey, $value);
        }
        return $arraySurvey;
    }

    public function getAllSurveyInfoOfAccount($accountID) {
        $result = DB::table('outbound_survey_sections AS survey_sections')
                ->leftJoin('outbound_surveys AS survey', 'survey.survey_id', '=', 'survey_sections.section_survey_id')
                ->join('users', 'users.id', '=', 'survey_sections.section_user_id')
                ->select('survey_sections.*', 'survey.survey_title', 'users.name')
                ->where('section_account_id', '=', $accountID)
                ->orderBy('section_time_completed', 'DESC')
                ->get();
        return $result;
    }

    public function getAllDetailSurveyInfo($id) {
        $result = DB::table('outbound_survey_result AS survey_result')
            ->join('outbound_questions AS questions', 'questions.question_id', '=', 'survey_result.survey_result_question_id')
            ->join('outbound_survey_sections', 'outbound_survey_sections.section_id', '=', 'survey_result.survey_result_section_id')
            ->join('outbound_answers AS answers', DB::raw('1'), '=', DB::raw('1'))
            ->leftJoin('outbound_answers AS answers1', 'answers1.answer_id', '=', 'survey_result.survey_result_answer_extra_id')
            ->leftJoin('outbound_answers AS answers2', 'answers2.answer_id', '=', 'survey_result.survey_result_action')
            ->leftJoin('outbound_answers AS answers3', 'answers3.answer_id', '=', 'survey_result.survey_result_error')
            ->select('question_id', 'question_answer_group_id', 'question_title', 'question_title_short', 'question_note', 'question_key','survey_result_answer_id', 'survey_result_note','question_is_nps', 'question_orderby',
                DB::raw('answers.answers_title AS answers_title, answers.answers_key AS answers_key'),
                DB::raw('answers1.answers_title AS answers_extra_title, answers1.answers_key AS answers_extra_title_key'),
                DB::raw('answers2.answers_title AS answers_extra_action, answers2.answers_key AS answers_extra_action_key'),
                DB::raw('answers3.answers_title AS answers_extra_error, answers3.answers_key AS answers_extra_error_key'),
                'section_connected', 'section_contract_num', 'section_note', 'section_contact_phone'
            )
            ->where('question_active', '=', '1')
            ->whereRaw('FIND_IN_SET(answers.answer_id, survey_result.survey_result_answer_id)')
            ->where(['survey_result_section_id' => $id])
            ->orderBy('question_orderby', 'asc')
            ->get();
        return $result;
    }

    public function getSumSurvey($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->select(DB::raw("section_connected AS KQSurvey, 
                                        SUM(IF(section_survey_id = 1,1,0)) AS SauTK,  
                                        SUM(IF(section_survey_id = 2,1,0)) AS SauBT,
                                        SUM(IF(section_survey_id IN (1,2),1,0)) AS TongCong"))
                ->whereIn('section_survey_id', [1, 2])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->groupBy('section_connected')
                ->orderBy('KQSurvey', 'desc')
                ->get();
        return $result;
    }

    /**
     * hàm get thông tin KH đánh giá điểm
     */
    public function getSumSurveyNPS($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->join('outbound_questions AS q', 'q.question_id', '=', 'r.survey_result_question_id')
                ->select(DB::raw("'CustomerRated' AS KQSurveyNPS, 
                                        SUM(IF(s.section_survey_id = 1,1,0)) AS SauTK,
                                        SUM(IF(s.section_survey_id = 2,1,0)) AS SauBT,                                       
                                        SUM(IF(s.section_survey_id IN (1,2),1,0)) AS TongCong"))
                ->where('q.question_answer_group_id', '=', 2)//group answers đánh giá điểm NPS
                ->where('a.answer_group', '=', 2)
                ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
                ->whereRaw('r.survey_result_answer_id <> -1')
                ->whereIn('s.section_survey_id', [1, 2])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->get();
        return $result;
    }

    /**
     * hàm get thông tin KH không đánh giá điểm (Chưa đủ thời gian trải nghiệm dịch vụ - không đánh giá, KH hiểu ý câu hỏi nhưngkhông đồng ý cung cấp điểm,...
     */
    public function getSumSurveyNPSNoRated($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_extra_id')
                ->join('outbound_questions AS q', 'q.question_id', '=', 'r.survey_result_question_id')
                ->select(DB::raw("a.answers_key AS KQSurveyNPS, 
                                        SUM(IF(s.section_survey_id = 1,1,0)) AS SauTK, 
                                        SUM(IF(s.section_survey_id = 2,1,0)) AS SauBT,                                      
                                        SUM(IF(s.section_survey_id IN (1,2),1,0)) AS TongCong"))
                ->where('q.question_is_nps', '=', 1)//group answers không đánh giá điểm NPS
                ->where('r.survey_result_answer_id', '=', -1)
                ->whereIn('s.section_survey_id', [1, 2])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->groupBy('a.answer_id')
                ->get();
        return $result;
    }

    /**
     * hàm get thông tin đánh giá độ hài lòng khách hàng
     */
//    public function getCSATInfo($region, $from_date, $to_date, $branch, $branchcode) {
//        $result = DB::table('outbound_survey_sections AS s')
//                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
//                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
//                ->select(DB::raw("CONCAT(a.answers_title, ' ( CSAT ', a.answers_point ,' )') AS DanhGia,
//                                    SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0)) AS NVKinhDoanh,
//                                    SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0)) AS NVTrienKhai,
//                                    SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 10,1,0)) AS DGDichVu_Net,
//                                    SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 11,1,0)) AS DGDichVu_TV,
//
//                                    SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 23,1,0)) AS NVKinhDoanhTS,
//                                    SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 22,1,0)) AS NVTrienKhaiTS,
//                                    SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 20,1,0)) AS DGDichVuTS_Net,
//                                    SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 21,1,0)) AS DGDichVuTS_TV,
//
//                                    SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS NVBaoTriTIN,
//                                    SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter LIKE '%INDO%',1,0)) AS NVBaoTriINDO,
//                                    SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS DVBaoTriTIN_Net,
//                                    SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS DVBaoTriTIN_TV,
//                                    SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter LIKE '%INDO%',1,0)) AS DVBaoTriINDO_Net,
//                                    SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter LIKE '%INDO%',1,0)) AS DVBaoTriINDO_TV,
//
//                                    SUM(IF(s.section_survey_id = 7 AND r.survey_result_question_id = 35,1,0)) AS NVThuCuoc,
//                                    SUM(IF(s.section_survey_id = 3 AND r.survey_result_question_id = 14,1,0)) AS DGDichVu_MobiPay_Net,
//                                    SUM(IF(s.section_survey_id = 3 AND r.survey_result_question_id = 15,1,0)) AS DGDichVu_MobiPay_TV,
//
//                                    SUM(IF(s.section_survey_id = 4 AND r.survey_result_question_id = 31,1,0)) AS NV_Counter,
//                                    SUM(IF(s.section_survey_id = 4 AND r.survey_result_question_id = 26,1,0)) AS DGDichVu_Counter,
//
//                                    SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 37,1,0)) AS NVKinhDoanhSS,
//                                    SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 38,1,0)) AS NVTrienKhaiSS,
//                                    SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 41,1,0)) AS DGDichVuSS_Net,
//                                    SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 42,1,0)) AS DGDichVuSS_TV,
//
//                                    SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 43,1,0)) AS NVBT_SSW,
//                                    SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 46,1,0)) AS DGDichVuSSW_Net,
//                                    SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 47,1,0)) AS DGDichVuSSW_TV,
//                                    answers_point"))
//                ->where('r.survey_result_answer_id', '<>', '-1')
//                ->where('a.answer_group', '=', 1)//group độ hài lòng
//                ->whereIn('s.section_survey_id', [1, 2, 3, 6, 4, 7, 9, 10])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
//                ->where(function($query) use ($from_date, $to_date) {
//                    if (!empty($from_date) && !empty($to_date)) {
//                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
//                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
//                    }
//                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
//                ->groupBy('a.answer_id')
//                ->get();
//        return $result;
//    }

    public function getCSATInfo($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
            ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
            ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
            ->select(DB::raw("
            case
            when a.answers_point = '1' then 'VeryUnsatisfaction'
            when a.answers_point = '2' then 'Unsatisfaction'
            when a.answers_point = '3' then 'Neutral'
            when a.answers_point = '4' then 'Satisfaction'
            when a.answers_point = '5' then 'VerySatisfaction'
            else a.answers_point end as DanhGia, 
            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0)) AS NVKinhDoanh,
            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0)) AS NVTrienKhai,
            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5,1,0)) AS DGDichVu_Net,
                                      
            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6,1,0)) AS NVBaoTri,
            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9,1,0)) AS DVBaoTri_Net,

            answers_point"))
            ->where('r.survey_result_answer_id', '<>', '-1')
            ->where('a.answer_group', '=', 1)//group độ hài lòng
            ->whereIn('s.section_survey_id', [1, 2])
//            ->where(function($query) use ($region) {
//                if (!empty($region)) {
//                    $region = explode(',', $region);
//                    foreach ($region as $reg) {
//                        $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                    }
//                }
//            })
               ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('s.section_location_id', $locationID);
                }
            })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                }
            })

//            ->where(function($query) use ($branch) {
//                if (count($branch) > 0) {
//                    foreach ($branch as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('s.section_location_id', $b);
//                        }
//                    }
//                }
//            })
//            ->where(function($query) use ($branchcode) {
//                if (count($branchcode) > 0) {
//                    foreach ($branchcode as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('s.section_branch_code', $b);
//                        }
//                    }
//                }
//            })
            ->groupBy('a.answer_id')
            ->get();
        return $result;
    }

    public function getCSATInfoByProvince($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
            ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
            ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
            ->select(DB::raw("
            case
            when a.answers_point = '1' then 'VeryUnsatisfaction'
            when a.answers_point = '2' then 'Unsatisfaction'
            when a.answers_point = '3' then 'Neutral'
            when a.answers_point = '4' then 'Satisfaction'
            when a.answers_point = '5' then 'VerySatisfaction'
            else a.answers_point end as DanhGia, 
            
            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0)) AS Fcam,
            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0)) AS NVTrienKhai,
            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5,1,0)) AS DGDichVu_Net,
                                      
            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6,1,0)) AS NVBaoTri,
            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9,1,0)) AS DVBaoTri_Net,

            answers_point"))
            ->where('r.survey_result_answer_id', '<>', '-1')
            ->where('a.answer_group', '=', 1)//group độ hài lòng
            ->whereIn('s.section_survey_id', [1, 2])
//            ->where(function($query) use ($region) {
//                if (!empty($region)) {
//                    $region = explode(',', $region);
//                    foreach ($region as $reg) {
//                        $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                    }
//                }
//            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('s.section_location_id', $locationID);
                }
            })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                }
            })

//            ->where(function($query) use ($branch) {
//                if (count($branch) > 0) {
//                    foreach ($branch as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('s.section_location_id', $b);
//                        }
//                    }
//                }
//            })
//            ->where(function($query) use ($branchcode) {
//                if (count($branchcode) > 0) {
//                    foreach ($branchcode as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('s.section_branch_code', $b);
//                        }
//                    }
//                }
//            })
            ->groupBy('a.answer_id')
            ->get();
        return $result;
    }



    //Lấy điểm CSAT 1,2 nhân viên của các vùng
    public function getCSAT12($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->select(DB::raw("s.section_location, 
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1 AND r.survey_result_answer_id in (1),1,0)) AS NVKD_CSAT_1,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1 AND r.survey_result_answer_id in (2),1,0)) AS NVKD_CSAT_2, 																						
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1 AND r.survey_result_answer_id in (1,2),1,0)) AS NVKD_CSAT_12,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1 AND r.survey_result_answer_id in (1,2,3,4,5),1,0)) AS TOTAL_NVKD_CUS_CSAT,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1 AND r.survey_result_answer_id in (1,2,3,4,5),survey_result_answer_id,0)) AS TOTAL_NVKD_CSAT,

SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2 AND r.survey_result_answer_id in (1),1,0)) AS NVTK_CSAT_1,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2 AND r.survey_result_answer_id in (2),1,0)) AS NVTK_CSAT_2, 																						
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2 AND r.survey_result_answer_id in (1,2),1,0)) AS NVTK_CSAT_12,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2 AND r.survey_result_answer_id in (1,2,3,4,5),1,0)) AS TOTAL_NVTK_CUS_CSAT,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2 AND r.survey_result_answer_id in (1,2,3,4,5),survey_result_answer_id,0)) AS TOTAL_NVTK_CSAT,

SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6  AND r.survey_result_answer_id in (1),1,0)) AS NVBT_CSAT_1,
SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6  AND r.survey_result_answer_id in (2),1,0)) AS NVBT_CSAT_2, 																						
SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6  AND r.survey_result_answer_id in (1,2),1,0)) AS NVBT_CSAT_12,
SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6  AND r.survey_result_answer_id in (1,2,3,4,5),1,0)) AS TOTAL_NVBT_CUS_CSAT,
SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6  AND r.survey_result_answer_id in (1,2,3,4,5),survey_result_answer_id,0)) AS TOTAL_NVBT_CSAT

"))
//                ->where('s.section_sub_parent_desc,!=,')
//                ->Where('s.section_sub_parent_desc,!=,Mien Nam')
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->groupBy('s.section_location_id')
                ->get();
        return $result;
    }

    //Lấy điểm CSAT 1,2 dịch vụ của các vùng
    public function getCSATService12($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->select(DB::raw("
               s.section_location, 
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5 AND r.survey_result_answer_id in (1),1,0)) AS INTERNET_CSAT_1,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5 AND r.survey_result_answer_id in (2),1,0)) AS INTERNET_CSAT_2, 																						
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5 AND r.survey_result_answer_id in (1,2),1,0)) AS INTERNET_CSAT_12,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5 AND r.survey_result_answer_id in (1,2,3,4,5),1,0)) AS TOTAL_INTERNET_CUS_CSAT,
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5 AND r.survey_result_answer_id in (1,2,3,4,5),survey_result_answer_id,0)) AS TOTAL_INTERNET_CSAT,

SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9 AND r.survey_result_answer_id in (1),1,0)) AS INTERNET_SBT_CSAT_1,
SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9 AND r.survey_result_answer_id in (2),1,0)) AS INTERNET_SBT_CSAT_2, 																						
SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9 AND r.survey_result_answer_id in (1,2),1,0)) AS INTERNET_SBT_CSAT_12,
SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9 AND r.survey_result_answer_id in (1,2,3,4,5),1,0)) AS TOTAL_SBT_INTERNET_CUS_CSAT,
SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9 AND r.survey_result_answer_id in (1,2,3,4,5),survey_result_answer_id,0)) AS TOTAL_SBT_INTERNET_CSAT
"
                ))
//                ->where('s.section_sub_parent_desc', '!=', '')
//                ->Where('s.section_sub_parent_desc', '!=', 'Mien Nam')
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->groupBy('s.section_location_id')
                ->get();
        return $result;
    }

    //Lấy điểm CSAT 1,2 dịch vụ theo hành động xử lý
//    public function CSATActionService12($region, $from_date, $to_date, $branch, $branchcode) {
//        $resultNET = DB::table('survey_section_report AS s')
//                ->select(DB::raw("
//                s.result_action_net,
//                sum(IF(s.csat_net_point in(1,2)  and s.section_survey_id=1,1,0)) Internet_IBB_CSAT_12,
//
//                sum(IF(s.csat_net_point in(1,2) and s.section_survey_id=6,1,0)) Internet_TS_CSAT_12,
//
//                sum(IF(s.csat_maintenance_net_point in(1,2) and s.section_survey_id=2 and s.section_supporter NOT LIKE '%INDO%'  ,1,0)) Internet_TIN_CSAT_12,
//
//                sum(IF(s.csat_maintenance_net_point in(1,2) and s.section_survey_id=2 and s.section_supporter LIKE '%INDO%'  ,1,0)) Internet_INDO_CSAT_12,
//
//                 sum(IF(s.csat_net_point in(1,2) and s.section_survey_id=3 ,1,0)) Internet_CUS_CSAT_12,
//
//                sum(IF(s.csat_net_point in(1,2) or s.csat_maintenance_net_point in(1,2),1,0)) Internet_TOTAL_CSAT_12
//                "))
//                ->whereNotNull('s.result_action_net')
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
//                ->where(function($query) use ($from_date, $to_date) {
//                    if (!empty($from_date) && !empty($to_date)) {
//                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
//                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
//                    }
//                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
//                ->groupBy('s.result_action_net')
//                ->get();
//
//        $resultTV = DB::table('survey_section_report AS s')
//                ->select(DB::raw("
//                 s.result_action_tv,
//                 sum(IF(s.csat_tv_point in(1,2) and s.section_survey_id=1 ,1,0)) TV_IBB_CSAT_12,
//
//                 sum(IF(s.csat_tv_point in(1,2) and s.section_survey_id=6 ,1,0)) TV_TS_CSAT_12,
//
//                 sum(IF(s.csat_maintenance_tv_point in(1,2) and s.section_survey_id=2 and s.section_supporter NOT LIKE '%INDO%'  ,1,0)) TV_TIN_CSAT_12,
//
//                 sum(IF(s.csat_maintenance_tv_point in(1,2) and s.section_survey_id=2 and s.section_supporter LIKE '%INDO%'  ,1,0)) TV_INDO_CSAT_12,
//
//                sum(IF(s.csat_tv_point in(1,2) and s.section_survey_id=3  ,1,0)) TV_CUS_CSAT_12,
//
//                 sum(IF(s.csat_tv_point in (1,2) or s.csat_maintenance_tv_point in(1,2),1,0)) TV_TOTAL_CSAT_12
//                "))
//                ->whereNotNull('s.result_action_tv')
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
//                ->where(function($query) use ($from_date, $to_date) {
//                    if (!empty($from_date) && !empty($to_date)) {
//                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
//                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
//                    }
//                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
//                ->groupBy('s.result_action_tv')
//                ->get();
//
//        return ['NET' => $resultNET, 'TV' => $resultTV];
//    }
    public function CSATActionService12($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->select(DB::raw("
                case
when r.survey_result_action=115 then 'SorryCustomerAndClose'
when r.survey_result_action=117 then 'CreatePrechecklist'
when r.survey_result_action=119 then 'CreateCLIndo' 
when r.survey_result_action=118 then 'CreateChecklist' 
when r.survey_result_action=119 then 'CreateChecklistOnSite' 
when r.survey_result_action=128 then 'Other'
when r.survey_result_action=116 then 'ForwardDepartment'
else r.survey_result_action end as 'action',
SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5 AND r.survey_result_answer_id in (1,2),1,0)) AS INTERNET_CSAT_12,

SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9 AND r.survey_result_answer_id in (1,2),1,0)) AS INTERNET_SBT_CSAT_12,


SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5 AND r.survey_result_answer_id in (1,2),1,0)) 
+ SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9 AND r.survey_result_answer_id in (1,2),1,0)) AS TOTAL_INTERNET_CSAT_12
"))
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('s.section_location_id', $locationID);
                }
            })
                ->WhereIn('r.survey_result_question_id', [5, 9])
                ->WhereIn('r.survey_result_answer_id', [1, 2])
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->WhereIn('r.survey_result_action', [115, 116, 117, 118, 119, 128])
                ->groupBy('r.survey_result_action')
                ->get();

        return $result;
    }

    /**
     * hàm get thông tin đánh giá độ hài lòng khách hàng
     */
    public function getNPSStatisticReport($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->select(DB::raw("a.answers_point, 
                                        SUM(IF(s.section_survey_id = 1,1,0)) as SauTK,
                                        SUM(IF(s.section_survey_id = 2,1,0)) as SauBT,                                     
                                        SUM(IF(s.section_survey_id IN (1,2),1,0)) as Total"))
                ->where('a.answer_group', '=', 2)
                ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
                ->whereRaw('r.survey_result_answer_id <> -1')
                ->whereIn('s.section_survey_id', [1, 2])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->groupBy('a.answers_point')
                ->orderBy('a.answers_point')
                ->get();
        return $result;
    }

    public function getNPSStatisticBranchReport($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
            ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
//            ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
            ->select(DB::raw("s.section_location as 'Location',
                sum(if(r.survey_result_answer_id >= 140 and r.survey_result_answer_id <= 146, 1, 0)) 'Unsupported',  
                sum(if(r.survey_result_answer_id >= 147 and r.survey_result_answer_id <= 148, 1, 0)) 'NeutralNPS',
                sum(if(r.survey_result_answer_id >= 149 and r.survey_result_answer_id <= 150, 1, 0)) 'Supported'"))
//            ->where('a.answer_group', '=', 2)
            ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
            ->whereRaw('r.survey_result_answer_id <> -1')
            ->whereIn('s.section_survey_id', [1, 2])
            ->whereIn('r.survey_result_question_id', [10, 11])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                }
            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('s.section_location_id', $locationID);
                }
            })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
            ->groupBy('s.section_location');
//            ->orderBy('a.answers_point')
//            ->get();
        $resultTotalNPS = DB::table('outbound_survey_sections AS s')
            ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
//            ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
            ->select(DB::raw("'WholeCountry' as 'Location',
                sum(if(r.survey_result_answer_id >= 140 and r.survey_result_answer_id <= 146, 1, 0)) 'Unsupported',  
                sum(if(r.survey_result_answer_id >= 147 and r.survey_result_answer_id <= 148, 1, 0)) 'NeutralNPS',
                sum(if(r.survey_result_answer_id >= 149 and r.survey_result_answer_id <= 150, 1, 0)) 'Supported'"))
//            ->where('a.answer_group', '=', 2)
            ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
            ->whereRaw('r.survey_result_answer_id <> -1')
            ->whereIn('s.section_survey_id', [1, 2])
            ->whereIn('r.survey_result_question_id', [10, 11])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                }
            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('s.section_location_id', $locationID);
                }
            })
            ->unionAll($result)
            ->get();
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
        return $resultTotalNPS;
    }

    /**
     * hàm get thông tin đóng góp của KH để hoàn thiện dịch vụ
     */
    public function getCustomersCommentReport($region, $from_date, $to_date, $branch, $branchcode) {
        $result = DB::table('survey_section_report AS r')
                ->select('r.section_id', 'r.section_survey_id', 'r.section_supporter', 'r.section_subsupporter', 'r.nps_improvement')
                ->where('r.nps_improvement', '<>', 84)//id KH ko góp ý
                ->where('r.nps_improvement', '<>', '-1')
                ->whereRaw('r.nps_improvement IS NOT NULL')
                ->whereIn('r.section_survey_id', [1, 2, 3, 6])
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('r.section_sub_parent_desc', '=', "Vung $reg");
                        }
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('r.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('r.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('r.section_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
                    if (count($branchcode) > 0) {
                        foreach ($branchcode as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('r.section_branch_code', $b);
                            }
                        }
                    }
                })
                ->get();
        return $result;
    }

    /**
     * Hàm get thông tin tổng KH góp ý, tổng KH ko góp ý, tổng KH được hỏi ý kiến
     */
    public function getAllTotalCusComment($region, $from_date, $to_date, $branch, $branchcode) {
        $result = DB::table('survey_section_report AS r')
                ->select(DB::raw("SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.nps_improvement <> 84 AND r.section_survey_id = 1, 1, 0)) AS KHGopYSauTK,
                             SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.nps_improvement <> 84 AND r.section_survey_id = 6, 1, 0)) AS KHGopYSauTKTS,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.nps_improvement <> 84 AND r.section_survey_id = 2 AND r.section_supporter NOT LIKE '%INDO%',1,0)) AS KHGopYSauBTTIN,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.nps_improvement <> 84 AND r.section_survey_id = 2 AND r.section_supporter LIKE '%INDO%',1,0)) AS KHGopYSauBTINDO,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.nps_improvement <> 84 AND r.section_survey_id = 3,1,0)) AS KHGopYSauTC,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.nps_improvement <> 84 AND r.section_survey_id IN (1,2,6),1,0)) AS TongCongGopY,
                            SUM(IF(r.nps_improvement = 84 AND r.section_survey_id = 1, 1, 0)) AS KHKoGopYSauTK,
                            SUM(IF(r.nps_improvement = 84 AND r.section_survey_id = 6, 1, 0)) AS KHKoGopYSauTKTS,
                            SUM(IF(r.nps_improvement = 84 AND r.section_survey_id = 2 AND r.section_supporter NOT LIKE '%INDO%',1,0)) AS KHKoGopYSauBTTIN,
                            SUM(IF(r.nps_improvement = 84 AND r.section_survey_id = 2 AND r.section_supporter LIKE '%INDO%',1,0)) AS KHKoGopYSauBTINDO,
                            SUM(IF(r.nps_improvement = 84 AND r.section_survey_id = 3,1,0)) AS KHKoGopYSauTC,
                            SUM(IF(r.nps_improvement = 84 AND r.section_survey_id IN (1,2,6),1,0)) AS TongCongKoGopY,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.section_survey_id = 1, 1, 0)) AS KHDcHoiYKienSauTK,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.section_survey_id = 6, 1, 0)) AS KHDcHoiYKienSauTKTS,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.section_survey_id = 2 AND r.section_supporter NOT LIKE '%INDO%',1,0)) AS KHDcHoiYKienSauBTTIN,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.section_survey_id = 2 AND r.section_supporter LIKE '%INDO%',1,0)) AS KHDcHoiYKienSauBTINDO,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.section_survey_id = 3,1,0)) AS KHDcHoiYKienSauTC,
                            SUM(IF(r.nps_improvement IS NOT NULL AND r.nps_improvement <> -1 AND r.section_survey_id IN (1,2, 6),1,0)) AS TongCongDcHoiYKien"))
                ->whereIn('r.section_survey_id', [1, 2, 3, 6])
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('r.section_sub_parent_desc', '=', "Vung $reg");
                        }
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('r.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('r.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('r.section_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
                    if (count($branchcode) > 0) {
                        foreach ($branchcode as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('r.section_branch_code', $b);
                            }
                        }
                    }
                })
                ->get();
        return $result;
    }

    /**
     * hàm get thông tin survey các trạng thái: thành công, không thành công, tổng (Dashboard)
     */
    public function getSurveyStatus($from_date, $to_date) {
        $result = DB::table('outbound_survey_sections AS survey_sections')
                ->select(DB::raw("SUM(IF(section_connected = 4,1,0)) AS ThanhCong, 
                                    SUM(IF(section_connected = 3,1,0)) AS KhongGapNguoiSD,
                                    SUM(IF(section_connected = 2,1,0)) AS KHTuChoiCS,
                                    SUM(IF(section_connected = 1,1,0)) AS KhongLienLacDuoc,
                                    SUM(IF(section_connected = 0,1,0)) AS KhongCanLienHe,
                                    SUM(IF(section_survey_id IN (1,2),1,0)) AS TongCong"))
                ->whereIn('section_survey_id', [1, 2])
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->get();
        return $result;
    }

    /**
     * hàm get thông tin đánh giá độ hài lòng khách hàng
     */
    public function getCSATInfoByBranches($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->select(DB::raw("s.section_location AS KhuVuc, 
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0) * a.answers_point) as NVKinhDoanhPoint,
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0) * a.answers_point) as NVTrienKhaiPoint,
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5,1,0) * a.answers_point) as DGDichVu_Net_Point,                                    
                                        
                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6,1,0) * a.answers_point) AS NVBaoTriPoint,
                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9,1,0) * a.answers_point) AS DVBaoTri_Net_Point,                                      
                                            
                                        
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0)) AS SoLuongKD,
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0)) AS SoLuongTK,                                       
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5,1,0)) AS SoLuongDGDV_Net,

                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6,1,0)) AS SoLuongNVBaoTri,
                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9,1,0)) AS SoLuongDVBaoTri_Net
                                      "))
                ->where('r.survey_result_answer_id', '<>', '-1')
                ->whereIn('s.section_survey_id', [1, 2])
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })

//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
//                ->where(function($query) use ($from_date, $to_date) {
//                    if (!empty($from_date) && !empty($to_date)) {
//                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
//                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
//                    }
//                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->groupBy('s.section_location_id')
                ->get();
        return $result;
    }

    /**
     * hàm get thông tin đánh giá độ hài lòng khách hàng
     */
    public function getCSATInfoByRegion_new($region, $from_date, $to_date, $branch, $branchcode = []) {
        $result = DB::table('survey_section_report AS s')
                ->select(DB::raw("s.section_sub_parent_desc AS Vung, 
                                        SUM(IF(s.section_survey_id = 1 AND s.csat_salesman_point IS NOT NULL, s.csat_salesman_point,0)) as NVKinhDoanhPoint,
                                        SUM(IF(s.section_survey_id = 1 AND s.csat_deployer_point IS NOT NULL, s.csat_deployer_point,0)) as NVTrienKhaiPoint,
                                        SUM(IF(s.section_survey_id = 1 AND s.csat_net_point IS NOT NULL, s.csat_net_point,0)) as DGDichVu_Net_Point,
                                        SUM(IF(s.section_survey_id = 1 AND s.csat_tv_point IS NOT NULL, s.csat_tv_point,0)) as DGDichVu_TV_Point,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_staff_point IS NOT NULL AND s.section_supporter NOT LIKE '%INDO%', s.csat_maintenance_staff_point,0)) AS NVBaoTriTINPoint,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_staff_point IS NOT NULL AND s.section_supporter LIKE '%INDO%', s.csat_maintenance_staff_point,0)) AS NVBaoTriINDOPoint,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_net_point IS NOT NULL AND s.section_supporter NOT LIKE '%INDO%', s.csat_maintenance_net_point,0)) AS DVBaoTriTIN_Net_Point,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_tv_point IS NOT NULL AND s.section_supporter NOT LIKE '%INDO%', s.csat_maintenance_tv_point,0)) AS DVBaoTriTIN_TV_Point,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_net_point IS NOT NULL AND s.section_supporter LIKE '%INDO%', s.csat_maintenance_net_point,0)) AS DVBaoTriINDO_Net_Point,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_tv_point IS NOT NULL AND s.section_supporter LIKE '%INDO%', s.csat_maintenance_tv_point,0)) AS DVBaoTriINDO_TV_Point,
                                        SUM(IF(s.section_survey_id = 1 AND s.csat_salesman_point IS NOT NULL,1,0)) AS SoLuongKD,
                                        SUM(IF(s.section_survey_id = 1 AND s.csat_deployer_point IS NOT NULL,1,0)) AS SoLuongTK,
                                        SUM(IF(s.section_survey_id = 1 AND s.csat_net_point IS NOT NULL,1,0)) AS SoLuongDGDV_Net,
                                        SUM(IF(s.section_survey_id = 1 AND s.csat_tv_point IS NOT NULL,1,0)) AS SoLuongDGDV_TV,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_staff_point IS NOT NULL AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongNVBaoTriTIN,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_staff_point IS NOT NULL AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongNVBaoTriINDO,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_net_point IS NOT NULL AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriTIN_Net,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_tv_point IS NOT NULL AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriTIN_TV,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_net_point IS NOT NULL AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriINDO_Net,
                                        SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_tv_point IS NOT NULL AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriINDO_TV"))
                ->where(function ($query) {
                    $query->where('s.csat_salesman_answer', '<>', '-1')
                    ->orWhere('s.csat_deployer_answer', '<>', '-1')
                    ->orWhere('s.csat_net_answer', '<>', '-1')
                    ->orWhere('s.csat_tv_answer', '<>', '-1')
                    ->orWhere('s.csat_maintenance_staff_answer', '<>', '-1')
                    ->orWhere('s.csat_maintenance_net_answer', '<>', '-1')
                    ->orWhere('s.csat_maintenance_tv_answer', '<>', '-1');
                })
                ->whereIn('s.section_survey_id', [1, 2])
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
                        }
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('s.section_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
                    if (count($branchcode) > 0) {
                        foreach ($branchcode as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('s.section_branch_code', $b);
                            }
                        }
                    }
                })
                ->groupBy('s.section_sub_parent_desc')
                ->get();
        return $result;
    }

    /**
     * hàm get thông tin đánh giá độ hài lòng khách hàng toàn quốc
     */
    public function getCSATInfoByAll($from_date, $to_date) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->select(DB::raw("'WholeCountry' AS KhuVuc, 
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0) * a.answers_point) as NVKinhDoanhPoint,
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0) * a.answers_point) as NVTrienKhaiPoint,
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5,1,0) * a.answers_point) as DGDichVu_Net_Point,
                                        
                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6,1,0) * a.answers_point) AS NVBaoTriPoint,
                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9,1,0) * a.answers_point) AS DVBaoTri_Net_Point,                                      
                                                                               
                                        
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0)) AS SoLuongKD,
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0)) AS SoLuongTK,
                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 5,1,0)) AS SoLuongDGDV_Net,                                        
                                        
                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 6,1,0)) AS SoLuongNVBaoTri,
                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 9,1,0)) AS SoLuongDVBaoTri_Net                                     
                                        "))
                ->where('r.survey_result_answer_id', '<>', '-1')
                ->whereIn('s.section_survey_id', [1, 2])
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->get();
        return $result;
    }

//    public function getCSATInfoByBranches($region, $from_date, $to_date, $limit, $branch, $branchcode = []) {
//        $result = DB::table('outbound_survey_sections AS s')
//                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
//                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
//                ->select(DB::raw('*, (NVKinhDoanhPoint / SoLuongKD) AS CSAT_NVKD'))
//                ->from(DB::raw("(SELECT s.section_sub_parent_desc AS Vung, CONCAT(s.section_location,'-',s.section_branch_code) as ChiNhanh,
//                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0) * a.answers_point) as NVKinhDoanhPoint,
//                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0) * a.answers_point) as NVTrienKhaiPoint,
//                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 10,1,0) * a.answers_point) as DGDichVu_Net_Point,
//                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 11,1,0) * a.answers_point) as DGDichVu_TV_Point,
//
//                                        SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 23,1,0) * a.answers_point) as NVKinhDoanhTSPoint,
//                                        SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 22,1,0) * a.answers_point) as NVTrienKhaiTSPoint,
//                                        SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 20,1,0) * a.answers_point) as DGDichVuTS_Net_Point,
//                                        SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 21,1,0) * a.answers_point) as DGDichVuTS_TV_Point,
//
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter NOT LIKE '%INDO%',1,0) * a.answers_point) AS NVBaoTriTINPoint,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter LIKE '%INDO%',1,0) * a.answers_point) AS NVBaoTriINDOPoint,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter NOT LIKE '%INDO%',1,0) * a.answers_point) AS DVBaoTriTIN_Net_Point,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter NOT LIKE '%INDO%',1,0) * a.answers_point) AS DVBaoTriTIN_TV_Point,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter LIKE '%INDO%',1,0) * a.answers_point) AS DVBaoTriINDO_Net_Point,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter LIKE '%INDO%',1,0) * a.answers_point) AS DVBaoTriINDO_TV_Point,
//
//                                        SUM(IF(s.section_survey_id = 7 AND r.survey_result_question_id = 35,1,0) * a.answers_point) as NVThuCuocPoint,
//                                        SUM(IF(s.section_survey_id = 3 AND r.survey_result_question_id = 14,1,0) * a.answers_point) as DGDichVu_MobiPay_Net_Point,
//                                        SUM(IF(s.section_survey_id = 3 AND r.survey_result_question_id = 15,1,0) * a.answers_point) as DGDichVu_MobiPay_TV_Point,
//
//                                        SUM(IF(s.section_survey_id = 4 AND r.survey_result_question_id = 26,1,0) * a.answers_point) as DGDichVu_Counter_Point,
//                                        SUM(IF(s.section_survey_id = 4 AND r.survey_result_question_id = 31,1,0) * a.answers_point) as NV_Counter_Point,
//
//                                        SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 37,1,0) * a.answers_point) as NVKinhDoanhSSPoint,
//                                        SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 38,1,0) * a.answers_point) as NVTrienKhaiSSPoint,
//                                        SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 41,1,0) * a.answers_point) as DGDichVuSS_Net_Point,
//                                        SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 42,1,0) * a.answers_point) as DGDichVuSS_TV_Point,
//
//                                        SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 43,1,0) * a.answers_point) as NVBT_SSWPoint,
//                                        SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 46,1,0) * a.answers_point) as DGDichVuSSW_Net_Point,
//                                        SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 47,1,0) * a.answers_point) as DGDichVuSSW_TV_Point,
//
//
//                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0)) AS SoLuongKD,
//                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0)) AS SoLuongTK,
//                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 10,1,0)) AS SoLuongDGDV_Net,
//                                        SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 11,1,0)) AS SoLuongDGDV_TV,
//
//                                        SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 23,1,0)) AS SoLuongKDTS,
//                                        SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 22,1,0)) AS SoLuongTKTS,
//                                        SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 20,1,0)) AS SoLuongDGDVTS_Net,
//                                        SUM(IF(s.section_survey_id = 6 AND r.survey_result_question_id = 21,1,0)) AS SoLuongDGDVTS_TV,
//
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongNVBaoTriTIN,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongNVBaoTriINDO,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriTIN_Net,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriTIN_TV,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriINDO_Net,
//                                        SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriINDO_TV,
//
//                                        SUM(IF(s.section_survey_id = 7 AND r.survey_result_question_id = 35,1,0)) AS SoLuongNVThuCuoc,
//                                        SUM(IF(s.section_survey_id = 3 AND r.survey_result_question_id = 14,1,0)) AS SoLuongDGDV_MobiPay_Net,
//                                        SUM(IF(s.section_survey_id = 3 AND r.survey_result_question_id = 15,1,0)) AS SoLuongDGDV_MobiPay_TV,
//
//                                        SUM(IF(s.section_survey_id = 4 AND r.survey_result_question_id = 26,1,0)) AS SoLuongDGDichVu_Counter,
//                                        SUM(IF(s.section_survey_id = 4 AND r.survey_result_question_id = 31,1,0)) AS SoLuongNV_Counter,
//
//                                        SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 37,1,0)) AS SoLuongKDSS,
//                                        SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 38,1,0)) AS SoLuongTKSS,
//                                        SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 41,1,0)) AS SoLuongDGDVSS_Net,
//                                        SUM(IF(s.section_survey_id = 9 AND r.survey_result_question_id = 42,1,0)) AS SoLuongDGDVSS_TV,
//
//                                        SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 43,1,0)) AS SoLuongSSW,
//                                        SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 46,1,0)) AS SoLuongDGDVSSW_Net,
//                                        SUM(IF(s.section_survey_id = 10 AND r.survey_result_question_id = 47,1,0)) AS SoLuongDGDVSSW_TV
//
//                            FROM outbound_survey_sections AS s"))
//                ->where('r.survey_result_answer_id', '<>', '-1')
//                ->where('a.answer_group', '=', 1)//group độ hài lòng
//                ->whereIn('s.section_survey_id', [1, 2, 3, 6, 4, 7, 9, 10])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
//                ->where(function($query) use ($from_date, $to_date) {
//                    if (!empty($from_date) && !empty($to_date)) {
//                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
//                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
//                    }
//                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
//                ->groupBy(DB::raw('s.section_location_id, s.section_branch_code ) AS B'))
//                ->orderBy(DB::raw('CSAT_NVKD'), 'DESC');
//        if (!empty($limit) && is_numeric($limit)) {
//            $result->take($limit);
//        }
//        $result = $result->get();
//        return $result;
//    }

    /**
     * Hàm lấy thông tin nps theo chi nhánh
     */
//    public function getNPSStatisticReportByBranches($from_date, $to_date, $locationID, $limit) {
//        $result = DB::table('outbound_survey_sections AS s')
//                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
//                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
//                ->select(DB::raw('*, ((UngHo - KhongUngHo) / TongCong) * 100 as NPS'))
//                ->from(DB::raw("(SELECT s.section_location_id AS KhuVuc,
//                                        SUM(IF(a.answers_point >= 0 AND a.answers_point <= 6 AND s.section_survey_id IN (1,2),1,0)) AS KhongUngHo,
//                                        SUM(IF(a.answers_point >= 0 AND a.answers_point <= 6 AND s.section_survey_id = 1,1,0)) AS KhongUngHoTK,
//                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND s.section_survey_id = 2,1,0)) AS KhongUngHoSBT,
//
//                                        SUM(IF(a.answers_point >= 7 AND a.answers_point <= 8 AND s.section_survey_id IN (1,2),1,0)) AS TrungLap,
//                                        SUM(IF(a.answers_point >= 7 AND a.answers_point <= 8 AND s.section_survey_id = 1,1,0)) AS TrungLapTK,
//                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND s.section_survey_id = 2,1,0)) AS TrungLapSBT,
//
//                                        SUM(IF(a.answers_point >= 9 AND a.answers_point <= 10 AND s.section_survey_id IN (1,2),1,0)) AS UngHo,
//                                        SUM(IF(a.answers_point >= 9 AND a.answers_point <= 10 AND s.section_survey_id = 1,1,0)) AS UngHoTK,
//                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND s.section_survey_id = 2,1,0)) AS UngHoSBT,
//
//                                        SUM(IF(s.section_survey_id IN (1,2),1,0)) AS TongCong,
//                                        SUM(IF(s.section_survey_id = 1,1,0)) AS TongCongTK,
//                                        SUM(IF(s.section_survey_id = 2,1,0)) AS TongCongSBT
//                            FROM outbound_survey_sections AS s"))
//                ->where('a.answer_group', '=', 2)
//                ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
//                ->whereRaw('r.survey_result_answer_id <> -1')
//                ->whereIn('s.section_survey_id', [1, 2])
//                ->where(function($query) use ($locationID) {
//                    if (!empty($locationID)) {
//                        $query->whereIn('s.section_location_id', $locationID);
//                    }
//                })
//                ->where(function($query) use ($from_date, $to_date) {
//                    if (!empty($from_date) && !empty($to_date)) {
//                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
//                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
//                    }
//                })
////                ->whereNotIn('s.section_location', ['FTS_2TNB', 'FTS_3HCM', 'FTN_3HNI', 'FTS_1DNB','FTM','FTN_2V2','FTN_2V3'])
////                ->whereRaw("s.section_location like  '%-%'")
////                ->where(function($query) use ($region) {
////                    if (!empty($region)) {
////                        $region = explode(',', $region);
////                        foreach ($region as $reg) {
////                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
////                        }
////                    }
////                })
////                ->where(function($query) use ($from_date, $to_date) {
////                    if (!empty($from_date) && !empty($to_date)) {
////                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
////                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
////                    }
////                })
////                ->where(function($query) use ($branch) {
////                    if (count($branch) > 0) {
////                        foreach ($branch as $b) {
////                            if (!empty($b)) {
////                                $b = explode(',', $b);
////                                $query->whereIn('s.section_location_id', $b);
////                            }
////                        }
////                    }
////                })
////                ->where(function($query) use ($branchcode) {
////                    if (count($branchcode) > 0) {
////                        foreach ($branchcode as $b) {
////                            if (!empty($b)) {
////                                $b = explode(',', $b);
////                                $query->whereIn('s.section_branch_code', $b);
////                            }
////                        }
////                    }
////                })
//                ->groupBy(DB::raw('s.section_location_id) as B'));
////                 ->orderBy(DB::raw('s.section_location, s.section_branch_code'), 'ASC');
////                ->orderBy(DB::raw('NPS'), 'DESC');
//        if (!empty($limit) && is_numeric($limit)) {
//            $result->take($limit);
//        }
//        $result = $result->get();
//        return $result;
//    }

    /**
     * Hàm lấy thông tin nps theo vùng
     */
    public function getNPSStatisticReportByBranches($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->select(DB::raw("section_location AS KhuVuc , 
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND s.section_survey_id IN (1,2),1,0)) AS KhongUngHo,
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND section_survey_id = 1,1,0)) AS KhongUngHoTK,
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND section_survey_id = 2,1,0)) AS KhongUngHoSBT,
                                      
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND s.section_survey_id IN (1,2),1,0)) AS TrungLap,
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND section_survey_id = 1,1,0)) AS TrungLapTK,
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND section_survey_id = 2,1,0)) AS TrungLapSBT,
                                       
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND s.section_survey_id IN (1,2),1,0)) AS UngHo,
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND section_survey_id = 1,1,0)) AS UngHoTK,
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND section_survey_id = 2,1,0)) AS UngHoSBT,                                      
                                        
                                        SUM(IF(section_survey_id IN (1,2),1,0)) AS TongCong,
                                        SUM(IF(section_survey_id = 1,1,0)) AS TongCongTK,
                                        SUM(IF(section_survey_id = 2,1,0)) AS TongCongSBT
                                      "))
                ->where('a.answer_group', '=', 2)
                ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
                ->whereRaw('r.survey_result_answer_id <> -1')
                ->whereIn('s.section_survey_id', [1, 2])
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
//                ->where(function($query) use ($from_date, $to_date) {
//                    if (!empty($from_date) && !empty($to_date)) {
//                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
//                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
//                    }
//                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->groupBy('s.section_location_id')
                ->get();
        return $result;
    }

    public function getNPSStatisticReportByRegion_test($region, $from_date, $to_date, $branch, $branchcode = []) {
        $result = DB::table('survey_section_report AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->select(DB::raw("section_sub_parent_desc AS Vung , 
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6,1,0)) AS KhongUngHo,
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND section_survey_id = 1,1,0)) AS KhongUngHoTK,
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND section_survey_id = 2 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS KhongUngHoTINPNC,
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND section_survey_id = 2 AND s.section_supporter LIKE '%INDO%',1,0)) AS KhongUngHoINDO,
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8,1,0)) AS TrungLap,
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND section_survey_id = 1 AND section_survey_id = 1,1,0)) AS TrungLapTK,
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND section_survey_id = 2 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS TrungLapTINPNC,
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND section_survey_id = 2 AND s.section_supporter LIKE '%INDO%',1,0)) AS TrungLapINDO,
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10,1,0)) AS UngHo,
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND section_survey_id = 1,1,0)) AS UngHoTK,
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND section_survey_id = 2 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS UngHoTINPNC,
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND section_survey_id = 2 AND s.section_supporter LIKE '%INDO%',1,0)) AS UngHoINDO,
                                        SUM(IF(section_survey_id IN (1,2),1,0)) AS TongCong,
                                        SUM(IF(section_survey_id = 1,1,0)) AS TongCongTK,
                                        SUM(IF(section_survey_id = 2 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS TongCongTINPNC,
                                        SUM(IF(section_survey_id = 2 AND s.section_supporter LIKE '%INDO%',1,0)) AS TongCongINDO"))
                ->where('a.answer_group', '=', 2)
                ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
                ->whereRaw('r.survey_result_answer_id <> -1')
                ->whereIn('s.section_survey_id', [1, 2])
                ->where(function($query) use ($region) {
                    if (!empty($region)) {
                        $region = explode(',', $region);
                        foreach ($region as $reg) {
                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
                        }
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->where(function($query) use ($branch) {
                    if (count($branch) > 0) {
                        foreach ($branch as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('s.section_location_id', $b);
                            }
                        }
                    }
                })
                ->where(function($query) use ($branchcode) {
                    if (count($branchcode) > 0) {
                        foreach ($branchcode as $b) {
                            if (!empty($b)) {
                                $b = explode(',', $b);
                                $query->whereIn('s.section_branch_code', $b);
                            }
                        }
                    }
                })
                ->groupBy('s.section_sub_parent_desc')
                ->get();
        return $result;
    }

    /**
     * Hàm lấy thông tin nps theo vùng
     */
    public function getNPSStatisticReportByAll($from_date, $to_date) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->select(DB::raw("'ToanQuoc' AS KhuVuc , 
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND s.section_survey_id IN (1,2),1,0)) AS KhongUngHo,
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND section_survey_id = 1,1,0)) AS KhongUngHoTK,                                      
                                        SUM(IF(answers_point >= 0 AND answers_point <= 6 AND section_survey_id = 2,1,0)) AS KhongUngHoSBT,

                                        
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND s.section_survey_id IN (1,2),1,0)) AS TrungLap,
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND section_survey_id = 1,1,0)) AS TrungLapTK,                                   
                                        SUM(IF(answers_point >= 7 AND answers_point <= 8 AND section_survey_id = 2,1,0)) AS TrungLapSBT,                                       
                                         
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND s.section_survey_id IN (1,2),1,0)) AS UngHo,
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND section_survey_id = 1,1,0)) AS UngHoTK,            
                                        SUM(IF(answers_point >= 9 AND answers_point <= 10 AND section_survey_id = 2,1,0)) AS UngHoSBT,                                       
                                        
                                        SUM(IF(section_survey_id IN (1,2),1,0)) AS TongCong,
                                        SUM(IF(section_survey_id = 1,1,0)) AS TongCongTK,
                                        SUM(IF(section_survey_id = 2,1,0)) AS TongCongSBT
                                    "))
                ->where('a.answer_group', '=', 2)
                ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
                ->whereRaw('r.survey_result_answer_id <> -1')
                ->whereIn('s.section_survey_id', [1, 2])
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->get();
        return $result;
    }

    public function checkSurvey($userID, $surveyID, $roleID) {
        $result = DB::table('outbound_survey_sections')->select('*')->where('section_id', '=', $surveyID)->get();
        if (empty($result))
            return 1;
        else {
            $result = $result[0];

//Nếu người dùng không có quyền chỉnh sửa, retry
            if ($roleID != 2 && $result->section_user_id != $userID)
                return 2;
            else
                return $result;
        }
    }

    public function checkExistCodes($codes, $type) {
        $result = DB::table('outbound_survey_sections')->select('*')
                ->where('section_code', '=', $codes)
                ->where('section_survey_id', '=', $type)
                ->get();
//        var_dump($result);die;
        return $result;
    }

    public function checkSurveyApi($shd, $type, $code) {
        $result = DB::table('outbound_survey_sections as oss')
                ->join('outbound_accounts as oa', 'oa.id', '=', 'oss.section_account_id')
                ->select('*')
                ->where('oa.contract_num', '=', $shd)
                ->where('oss.section_survey_id', '=', $type)
                ->where('oss.section_code', '=', $code)
                ->first();
        if (empty($result))
            return false;
        else {
            return $result;
        }
    }

    public function checkSurveyApiUpgrade($shd) {
        $end = date_create(date('Y-m-d H:i:s'));
        $start = date_create(date('Y-m-d H:i:s'));
        date_add($start, date_interval_create_from_date_string('-3 months'));
        $result = DB::table('outbound_survey_sections as oss')
                ->join('outbound_accounts as oa', 'oa.id', '=', 'oss.section_account_id')
                ->select('*')
                ->where('oa.contract_num', '=', $shd)
                ->where('oss.section_time_completed_int', '>=', strtotime($start))
                ->where('oss.section_time_completed_int', '<=', strtotime($end))
                ->first();
        if (empty($result))
            return false;
        else {
            return $result;
        }
    }

    public function getFullQA() {
        $QA = [];
        $QA['type1']['title'] = 'Sau triển khai';
        $QA['type1']['ques_ans'] = [];
        $QA['type2']['title'] = 'Sau bảo trì';
        $QA['type2']['ques_ans'] = [];
        $resultQues = DB::table('outbound_questions')
                        ->select('*')->get();
        foreach ($resultQues as $key => $value) {
            $answerGroup = DB::table('outbound_answers')
                            ->select('*')->where('answer_group', '=', $value->question_answer_group_id)->get();
            $arrayANS = [];
            foreach ($answerGroup as $key2 => $value2) {
                $value2 = (array) $value2;
                array_push($arrayANS, $value2);
            }
            $value = (array) $value;
            if ($value['question_survey_id'] == 1) {
                $QA['type1']['ques_ans'][$value['question_id']] = [];
                $QA['type1']['ques_ans'][$value['question_id']]['question'] = [];
                $QA['type1']['ques_ans'][$value['question_id']]['answer_group'] = [];
                array_push($QA['type1']['ques_ans'][$value['question_id']]['question'], $value);
                array_push($QA['type1']['ques_ans'][$value['question_id']]['answer_group'], $arrayANS);
            } else {
                $QA['type2']['ques_ans'][$value['question_id']] = [];
                $QA['type2']['ques_ans'][$value['question_id']]['question'] = [];
                $QA['type2']['ques_ans'][$value['question_id']]['answer_group'] = [];
                array_push($QA['type2']['ques_ans'][$value['question_id']]['question'], $value);
                array_push($QA['type2']['ques_ans'][$value['question_id']]['answer_group'], $arrayANS);
            }
        }
        return $QA;
    }

    public function getSurvey() {
        $result = DB::table('outbound_surveys')
                ->where('survey_deleted', '=', '0')
                ->where('survey_active', '=', '1')
                ->select('survey_id', 'survey_type', 'survey_title', 'survey_description')
                ->get();
        return $result;
    }

    public function getQuest() {
        $result = DB::table('outbound_questions')
                ->where('question_deleted', '=', '0')
                ->where('question_active', '=', '1')
                ->select('question_id', 'question_type', 'question_survey_id'
                        , 'question_answer_group_id', 'question_answer_group_extra_id', 'question_title', 'question_title_short', 'question_orderby', 'question_note', 'question_is_nps', 'question_group_service')
                ->get();
        return $result;
    }

    public function getAnswer() {
        $result = DB::table('outbound_answers')
                ->select('answer_id', 'answer_question_id', 'answer_group', 'answers_title', 'answers_point', 'answers_position')
                ->get();
        return $result;
    }

    public function getAnswerGroup() {
        $result = DB::table('outbound_answers_group')
                ->select('answers_group_id', 'answers_group_title')
                ->get();
        return $result;
    }

    public function getAnswerOther() {
        $result = DB::table('outbound_answer_other')
                ->select('other_id', 'other_title', 'other_answer_id', 'other_position')
                ->get();
        return $result;
    }

    public function getNeedSurveySendEmail() {
        $result = DB::table('outbound_answers_group')
                ->join('', '', '')
                ->select('answers_group_id', 'answers_group_title')
                ->get();
        return $result;
    }

///Lay tat ca chi nhanh
    public function getLocation() {
        $result = DB::table('location')
                ->select('id', 'name')
                ->get();
        return $result;
    }

    public function getNumSurvey($surveyId) {
        $result = DB::table('outbound_survey_sections')
                ->where('section_id', '=', $surveyId)
                ->select('section_account_id')
                ->first();
        $resultTotal = DB::table('outbound_survey_sections')
                ->where('section_account_id', '=', $result->section_account_id)
                ->count();
        return $resultTotal;
    }

    public function getSumSurveyNPSNoRated_Note($from_date, $to_date, $locationID) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_questions AS q', 'q.question_id', '=', 'r.survey_result_question_id')
                ->select(DB::raw("'CustomerNotAnswerNoReasonHasNote' AS KQSurveyNPS, 
                                        SUM(IF(s.section_survey_id = 1,1,0)) AS SauTK, 
                                        SUM(IF(s.section_survey_id = 2,1,0)) AS SauBT,                                      
                                        SUM(IF(s.section_survey_id IN (1,2),1,0)) AS TongCong"))
                ->where('q.question_is_nps', '=', 1)//group answers đánh giá điểm NPS
                ->where('r.survey_result_answer_id', '=', -1)
                ->where('s.section_connected', '=', 4)
                ->whereRaw('r.survey_result_answer_extra_id IS NULL')
                ->whereIn('s.section_survey_id', [1, 2])
//                ->where(function($query) use ($region) {
//                    if (!empty($region)) {
//                        $region = explode(',', $region);
//                        foreach ($region as $reg) {
//                            $query->orWhere('s.section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
                ->where(function($query) use ($locationID) {
                    if (!empty($locationID)) {
                        $query->whereIn('s.section_location_id', $locationID);
                    }
                })
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
//                ->where(function($query) use ($branch) {
//                    if (count($branch) > 0) {
//                        foreach ($branch as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($branchcode) {
//                    if (count($branchcode) > 0) {
//                        foreach ($branchcode as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('s.section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->get();
        return $result;
    }

    public function getAllCSATInfoByDate($from_date, $to_date) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->select(DB::raw("SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0) * a.answers_point) as NVKinhDoanhPoint,
                            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0) * a.answers_point) as NVTrienKhaiPoint,
                            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 10,1,0) * a.answers_point) as DGDichVu_Net_Point,
                            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 11,1,0) * a.answers_point) as DGDichVu_TV_Point,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter NOT LIKE '%INDO%',1,0) * a.answers_point) AS NVBaoTriTINPoint,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter LIKE '%INDO%',1,0) * a.answers_point) AS NVBaoTriINDOPoint,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter NOT LIKE '%INDO%',1,0) * a.answers_point) AS DVBaoTriTIN_Net_Point,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter NOT LIKE '%INDO%',1,0) * a.answers_point) AS DVBaoTriTIN_TV_Point,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter LIKE '%INDO%',1,0) * a.answers_point) AS DVBaoTriINDO_Net_Point,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter LIKE '%INDO%',1,0) * a.answers_point) AS DVBaoTriINDO_TV_Point,
                            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 1,1,0)) AS SoLuongKD,
                            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 2,1,0)) AS SoLuongTK,
                            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 10,1,0)) AS SoLuongDGDV_Net,
                            SUM(IF(s.section_survey_id = 1 AND r.survey_result_question_id = 11,1,0)) AS SoLuongDGDV_TV,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongNVBaoTriTIN,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 4 AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongNVBaoTriINDO,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriTIN_Net,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter NOT LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriTIN_TV,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 12 AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriINDO_Net,
                            SUM(IF(s.section_survey_id = 2 AND r.survey_result_question_id = 13 AND s.section_supporter LIKE '%INDO%',1,0)) AS SoLuongDVBaoTriINDO_TV"))
                ->where('r.survey_result_answer_id', '<>', '-1')
                ->where('a.answer_group', '=', 1)//group độ hài lòng
                ->whereIn('s.section_survey_id', [1, 2])
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->get();
        return $result;
    }

    public function getAllCSATInfoByDate_new($from_date, $to_date) {
        $result = DB::table('survey_section_report AS s')
                ->select(DB::raw("
                            SUM(IF(s.section_survey_id = 1 AND s.csat_salesman_question = 1, s.csat_salesman_point,0)) AS NVKinhDoanhPoint,
                            SUM(IF(s.section_survey_id = 1 AND s.csat_deployer_question = 2, s.csat_deployer_point,0)) AS NVTrienKhaiPoint,
                            SUM(IF(s.section_survey_id = 1 AND s.csat_net_question = 10, s.csat_net_point,0)) AS DGDichVu_Net_Point,
                            SUM(IF(s.section_survey_id = 1 AND s.csat_tv_question = 11, s.csat_tv_point,0)) AS DGDichVu_TV_Point,
                            
                            SUM(IF(s.section_survey_id = 6 AND s.csat_salesman_question = 23, s.csat_salesman_point,0)) AS NVKinhDoanhTSPoint,
                            SUM(IF(s.section_survey_id = 6 AND s.csat_deployer_question = 22, s.csat_deployer_point,0)) AS NVTrienKhaiTSPoint,
                            SUM(IF(s.section_survey_id = 6 AND s.csat_net_question = 20, s.csat_net_point,0)) AS DGDichVuTS_Net_Point,
                            SUM(IF(s.section_survey_id = 6 AND s.csat_tv_question = 21, s.csat_tv_point,0)) AS DGDichVuTS_TV_Point,
                            
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_staff_question = 4 AND s.section_supporter NOT LIKE '%INDO%', s.csat_maintenance_staff_point,0)) AS NVBaoTriTINPoint,
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_staff_question = 4 AND s.section_supporter LIKE '%INDO%', s.csat_maintenance_staff_point,0)) AS NVBaoTriINDOPoint,
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_net_question = 12 AND s.section_supporter NOT LIKE '%INDO%', s.csat_maintenance_net_point,0)) AS DVBaoTriTIN_Net_Point,
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_tv_question = 13 AND s.section_supporter NOT LIKE '%INDO%', s.csat_maintenance_tv_point,0)) AS DVBaoTriTIN_TV_Point,
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_net_question = 12 AND s.section_supporter LIKE '%INDO%', s.csat_maintenance_net_point,0)) AS DVBaoTriINDO_Net_Point,
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_tv_question = 13 AND s.section_supporter LIKE '%INDO%', s.csat_maintenance_tv_point,0)) AS DVBaoTriINDO_TV_Point,
                            SUM(IF(s.section_survey_id = 1 AND s.csat_salesman_question = 1 AND `s`.`csat_salesman_point` IS NOT NULL,1,0)) AS SoLuongKD,
                            SUM(IF(s.section_survey_id = 1 AND s.csat_deployer_question = 2 AND `s`.`csat_deployer_point` IS NOT NULL,1,0)) AS SoLuongTK, 
                            SUM(IF(s.section_survey_id = 1 AND s.csat_net_question = 10 AND `s`.`csat_net_point` IS NOT NULL,1,0)) AS SoLuongDGDV_Net,
                            SUM(IF(s.section_survey_id = 1 AND s.csat_tv_question = 11 AND `s`.`csat_tv_point` IS NOT NULL,1,0)) AS SoLuongDGDV_TV,
                            
                            SUM(IF(s.section_survey_id = 6 AND s.csat_salesman_question = 23 AND `s`.`csat_salesman_point` IS NOT NULL,1,0)) AS SoLuongKDTS,
                            SUM(IF(s.section_survey_id = 6 AND s.csat_deployer_question = 22 AND `s`.`csat_deployer_point` IS NOT NULL,1,0)) AS SoLuongTKTS, 
                            SUM(IF(s.section_survey_id = 6 AND s.csat_net_question = 20 AND `s`.`csat_net_point` IS NOT NULL,1,0)) AS SoLuongDGDVTS_Net,
                            SUM(IF(s.section_survey_id = 6 AND s.csat_tv_question = 21 AND `s`.`csat_tv_point` IS NOT NULL,1,0)) AS SoLuongDGDVTS_TV,
                            
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_staff_question = 4 AND s.section_supporter NOT LIKE '%INDO%' AND `s`.`csat_maintenance_staff_point` IS NOT NULL,1,0)) AS SoLuongNVBaoTriTIN, 
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_staff_question = 4 AND s.section_supporter LIKE '%INDO%' AND `s`.`csat_maintenance_staff_point` IS NOT NULL,1,0)) AS SoLuongNVBaoTriINDO, 
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_net_question = 12 AND s.section_supporter NOT LIKE '%INDO%' AND `s`.`csat_maintenance_net_point` IS NOT NULL,1,0)) AS SoLuongDVBaoTriTIN_Net, 
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_tv_question = 13 AND s.section_supporter NOT LIKE '%INDO%' AND `s`.`csat_maintenance_tv_point` IS NOT NULL,1,0)) AS SoLuongDVBaoTriTIN_TV, 
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_net_question = 12 AND s.section_supporter LIKE '%INDO%' AND `s`.`csat_maintenance_net_point` IS NOT NULL,1,0)) AS SoLuongDVBaoTriINDO_Net, 
                            SUM(IF(s.section_survey_id = 2 AND s.csat_maintenance_tv_question = 13 AND s.section_supporter LIKE '%INDO%' AND `s`.`csat_maintenance_tv_point` IS NOT NULL,1,0)) AS SoLuongDVBaoTriINDO_TV,
                            SUM(IF(s.section_survey_id = 3 AND s.csat_net_question = 14, s.csat_net_point,0)) AS DGDichVu_MobiPay_Net_Point,
                            SUM(IF(s.section_survey_id = 3 AND s.csat_tv_question = 15, s.csat_tv_point,0)) AS DGDichVu_MobiPay_TV_Point,
                            SUM(IF(s.section_survey_id = 3 AND s.csat_net_question = 14 AND `s`.`csat_net_point` IS NOT NULL,1,0)) AS SoLuongDGDV_MobiPay_Net,
                            SUM(IF(s.section_survey_id = 3 AND s.csat_tv_question = 15 AND `s`.`csat_tv_point` IS NOT NULL,1,0)) AS SoLuongDGDV_MobiPay_TV"))
                ->where(function ($query) {
                    $query->where('s.csat_salesman_answer', '<>', '-1')
                    ->orWhere('s.csat_deployer_answer', '<>', '-1')
                    ->orWhere('s.csat_net_answer', '<>', '-1')
                    ->orWhere('s.csat_tv_answer', '<>', '-1')
                    ->orWhere('s.csat_maintenance_staff_answer', '<>', '-1')
                    ->orWhere('s.csat_maintenance_net_answer', '<>', '-1')
                    ->orWhere('s.csat_maintenance_tv_answer', '<>', '-1');
                })
                //group độ hài lòng
                ->whereIn('s.section_survey_id', [1, 2, 3, 6])
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->get();
        return $result;
    }

    public function getAllNPSInfoByDate($from_date, $to_date) {
        $result = DB::table('outbound_survey_sections AS s')
                ->join('outbound_survey_result AS r', 'r.survey_result_section_id', '=', 's.section_id')
                ->join('outbound_answers AS a', 'a.answer_id', '=', 'r.survey_result_answer_id')
                ->join('outbound_questions AS q', 'q.question_id', '=', 'r.survey_result_question_id')
                ->select(DB::raw("SUM(IF(a.answers_point >= 0 AND a.answers_point <= 6,1,0)) AS KhongUngHo, 
                                SUM(IF(a.answers_point >= 7 AND a.answers_point <= 8,1,0)) AS TrungLap,
                                SUM(IF(a.answers_point >= 9 AND a.answers_point <= 10,1,0)) AS UngHo,
                                SUM(IF(s.section_survey_id IN (1,2),1,0)) AS TongCong"))
                ->where('q.question_answer_group_id', '=', 2)//group answers độ hài lòng
                ->whereRaw('(r.survey_result_answer_extra_id IS NULL OR r.survey_result_answer_extra_id <> 0)')
                ->whereRaw('r.survey_result_answer_id <> -1')
                ->whereIn('s.section_survey_id', [1, 2])
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->get();
        return $result;
    }

    public function getAllNPSInfoByDate_new($from_date, $to_date) {
        $result = DB::table('survey_section_report AS s')
                ->select(DB::raw("SUM(IF(s.nps_point >= 0 AND s.nps_point <= 6 AND s.nps_point IS NOT NULL,1,0)) AS KhongUngHo, 
                                    SUM(IF(s.nps_point >= 7 AND s.nps_point <= 8 AND s.nps_point IS NOT NULL,1,0)) AS TrungLap, 
                                    SUM(IF(s.nps_point >= 9 AND s.nps_point <= 10 AND s.nps_point IS NOT NULL,1,0)) AS UngHo, 
                                    SUM(IF(s.section_survey_id IN (1,2,6),1,0)) AS TongCong"))
                ->whereIn('s.nps_question', [6, 8, 24])//group answers độ hài lòng
                ->whereRaw('(s.nps_answer_extra_id IS NULL OR s.nps_answer_extra_id <> 0)')
                ->whereRaw('s.nps_answer <> -1')
                ->whereIn('s.section_survey_id', [1, 2, 3, 6])
                ->where(function($query) use ($from_date, $to_date) {
                    if (!empty($from_date) && !empty($to_date)) {
                        $query->where('s.section_time_completed_int', '>=', strtotime($from_date));
                        $query->where('s.section_time_completed_int', '<=', strtotime($to_date));
                    }
                })
                ->get();
        return $result;
    }

    public function getNPSImprovement($arrGroup) {
        $redisKey = 'listNPSImprovement';
        $result = Redis::get($redisKey); //key redis kq tìm kiếm chi tiết khảo sát
        if (empty($result)) {
//tạo cache
            $result = DB::table(DB::raw('outbound_answers AS a '
                                    . 'INNER JOIN outbound_answers_group AS ag ON a.answer_group = ag.answers_group_id'))
                    ->whereIn('a.answer_group', $arrGroup)//nhóm câu trả lời để phát triển
                    ->orderBy(DB::raw('a.answer_group'))
                    ->get();
            Redis::set($redisKey, json_encode($result));
            Redis::expire($redisKey, 86400);
        }
//ktra chuỗi json
        if (is_string($result)) {
            $result = json_decode($result);
        }
        return $result;
    }

    public function getErrorType($arrGroup) {
        $redisKey = 'listErrorType';
        $result = Redis::get($redisKey); //key redis kq tìm kiếm chi tiết khảo sát
        if (empty($result)) {
//tạo cache
            $result = DB::table(DB::raw('outbound_answers AS a '
                                    . 'INNER JOIN outbound_answers_group AS ag ON a.answer_group = ag.answers_group_id'))
                    ->whereIn('a.answer_group', $arrGroup)//nhóm câu trả lời để phát triển
                    ->orderBy(DB::raw('a.answer_group'))
                    ->get();
            Redis::set($redisKey, json_encode($result));
            Redis::expire($redisKey, 86400);
        }
//ktra chuỗi json
        if (is_string($result)) {
            $result = json_decode($result);
        }
        return $result;
    }

    public function getProcessingActions($arrGroup) {
        $redisKey = 'listProcessingActions';
        $result = Redis::get($redisKey); //key redis kq tìm kiếm chi tiết khảo sát
        if (empty($result)) {
//tạo cache
            $result = DB::table(DB::raw('outbound_answers AS a '
                                    . 'INNER JOIN outbound_answers_group AS ag ON a.answer_group = ag.answers_group_id'))
                    ->whereIn('a.answer_group', $arrGroup)//nhóm câu trả lời để phát triển
                    ->orderBy(DB::raw('a.answer_group'))
                    ->get();
            Redis::set($redisKey, json_encode($result));
            Redis::expire($redisKey, 86400);
        }
//ktra chuỗi json
        if (is_string($result)) {
            $result = json_decode($result);
        }
        return $result;
    }

    public function getProductivity($condition) {
        $result = DB::table('outbound_survey_sections AS s')
                ->select(DB::raw("section_user_name, 
                    SUM(IF(section_survey_id = 1, 1, 0)) AS 'TongKhaoSat_STK',
                    SUM(IF(section_connected = 4 AND section_survey_id = 1, 1, 0)) AS GapNguoiSD_STK,
                    SUM(IF(section_connected = 3 AND section_survey_id = 1, 1, 0)) AS KhongGapNguoiSD_STK,
                    SUM(IF(section_connected = 2 AND section_survey_id = 1, 1, 0)) AS KHTuChoiCS_STK,
                    SUM(IF(section_connected = 1 AND section_survey_id = 1, 1, 0)) AS KhongLienLacDuoc_STK,
                    SUM(IF(section_connected = 0 AND section_survey_id = 1, 1, 0)) AS KhongCanLienHe_STK,
                    
                   
                    
                    SUM(IF(section_survey_id = 2, 1, 0)) AS 'TongKhaoSat_SBT',
                    SUM(IF(section_connected = 4 AND section_survey_id = 2, 1, 0)) AS GapNguoiSD_SBT,
                    SUM(IF(section_connected = 3 AND section_survey_id = 2, 1, 0)) AS KhongGapNguoiSD_SBT,
                    SUM(IF(section_connected = 2 AND section_survey_id = 2, 1, 0)) AS KHTuChoiCS_SBT,
                    SUM(IF(section_connected = 1 AND section_survey_id = 2, 1, 0)) AS KhongLienLacDuoc_SBT,
                    SUM(IF(section_connected = 0 AND section_survey_id = 2, 1, 0)) AS KhongCanLienHe_SBT"))
//                ->leftJoin('users_region AS ur', 's.section_user_name', '=', 'ur.name')
                ->where(function($query) use ($condition) {
                    if (!empty($condition['fromDate']) && !empty($condition['toDate'])) {
                        $query->where('section_time_completed_int', '>=', strtotime($condition['fromDate']));
                        $query->where('section_time_completed_int', '<=', strtotime($condition['toDate']));
                    }
                })
                ->where(function($query) use ($condition) {
                    if (!empty($condition['locationID'])) {
                        $query->whereIn('s.section_location_id', $condition['locationID']);
                    }
                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['section_survey_id'])) {
//                        $query->where('section_survey_id', '=', $condition['section_survey_id']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['region'])) {
//                        $region = explode(',', $condition['region']);
//                        foreach ($region as $reg) {
//                            $query->orWhere('section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (count($condition['branch']) > 0) {
//                        foreach ($condition['branch'] as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (count($condition['branchcode']) > 0) {
//                        foreach ($condition['branchcode'] as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
                ->whereIn('section_survey_id', [1, 2])
                ->groupBy('section_user_name')
//                ->orderBy('region', 'DESC')
                ->get();

//        $resultTotal = DB::table('outbound_survey_sections AS s')
//            ->select(DB::raw("section_user_name,
//                    SUM(IF(section_survey_id = 1, 1, 0)) AS 'Sau Triển khai',
//                    SUM(IF(section_connected = 4 AND section_survey_id = 1, 1, 0)) AS GapNguoiSD,
//                    SUM(IF(section_connected = 3 AND section_survey_id = 1, 1, 0)) AS KhongGapNguoiSD,
//                    SUM(IF(section_connected = 2 AND section_survey_id = 1, 1, 0)) AS KHTuChoiCS,
//                    SUM(IF(section_connected = 1 AND section_survey_id = 1, 1, 0)) AS KhongLienLacDuoc,
//                    SUM(IF(section_connected = 0 AND section_survey_id = 1, 1, 0)) AS KhongCanLienHe,
//                    SUM(IF(section_connected = 4 AND section_survey_id = 1, 1, 0)) + SUM(IF(section_connected = 3 AND section_survey_id = 1, 1, 0)) + SUM(IF(section_connected = 2 AND section_survey_id = 1, 1, 0))  as LienLacDuoc,
//                    concat(round(((SUM(IF(section_connected = 4 AND section_survey_id = 1, 1, 0)) + SUM(IF(section_connected = 3 AND section_survey_id = 1, 1, 0)) + SUM(IF(section_connected = 2 AND section_survey_id = 1, 1, 0))) /
//                    (SUM(IF(section_survey_id = 1, 1, 0))))*100, 2), '%') as 'TiLeLienLacDuoc',
//
//                    SUM(IF(section_survey_id = 2, 1, 0)) AS 'Sau Bảo trì',
//                    SUM(IF(section_connected = 4 AND section_survey_id = 2, 1, 0)) AS GapNguoiSDSBT,
//                    SUM(IF(section_connected = 3 AND section_survey_id = 2, 1, 0)) AS KhongGapNguoiSDSBT,
//                    SUM(IF(section_connected = 2 AND section_survey_id = 2, 1, 0)) AS KHTuChoiCSSBT,
//                    SUM(IF(section_connected = 1 AND section_survey_id = 2, 1, 0)) AS KhongLienLacDuocSBT,
//                    SUM(IF(section_connected = 0 AND section_survey_id = 2, 1, 0)) AS KhongCanLienHeSBT,
//                    SUM(IF(section_connected = 4 AND section_survey_id = 2, 1, 0)) + SUM(IF(section_connected = 3 AND section_survey_id = 2, 1, 0)) + SUM(IF(section_connected = 2 AND section_survey_id = 2, 1, 0)) as LienLacDuocSBT,
//
//                     concat(round(((SUM(IF(section_connected = 4 AND section_survey_id = 2, 1, 0)) + SUM(IF(section_connected = 3 AND section_survey_id = 2, 1, 0)) + SUM(IF(section_connected = 2 AND section_survey_id = 2, 1, 0)) ) /
//                     (SUM(IF(section_survey_id = 2, 1, 0)))) * 100 , 2), '%') as 'TiLeLienLacDuocSBT'"))
////                ->leftJoin('users_region AS ur', 's.section_user_name', '=', 'ur.name')
//            ->where(function($query) use ($condition) {
//                if (!empty($condition['fromDate']) && !empty($condition['toDate'])) {
//                    $query->where('section_time_completed_int', '>=', strtotime($condition['fromDate']));
//                    $query->where('section_time_completed_int', '<=', strtotime($condition['toDate']));
//                }
//            })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['section_survey_id'])) {
//                        $query->where('section_survey_id', '=', $condition['section_survey_id']);
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (!empty($condition['region'])) {
//                        $region = explode(',', $condition['region']);
//                        foreach ($region as $reg) {
//                            $query->orWhere('section_sub_parent_desc', '=', "Vung $reg");
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (count($condition['branch']) > 0) {
//                        foreach ($condition['branch'] as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('section_location_id', $b);
//                            }
//                        }
//                    }
//                })
//                ->where(function($query) use ($condition) {
//                    if (count($condition['branchcode']) > 0) {
//                        foreach ($condition['branchcode'] as $b) {
//                            if (!empty($b)) {
//                                $b = explode(',', $b);
//                                $query->whereIn('section_branch_code', $b);
//                            }
//                        }
//                    }
//                })
//            ->whereIn('section_survey_id', [1, 2])
//            ->groupBy('section_user_name');
//                ->orderBy('region', 'DESC')
//                ->get();
//        ->union($resultDetail)
        return $result;
    }

    public function updateSurveySectionByText($param) {
        $resIns = DB::table($this->table)
                ->where('section_id', $param['section_id'])
                ->update($param);
        return $resIns;
    }

    public function getAllSurveyInfoOfAccountQGD($contractNum) {
        $result = DB::table('outbound_survey_sections AS survey_sections')
                ->leftJoin('outbound_surveys AS survey', 'survey.survey_id', '=', 'survey_sections.section_survey_id')
//            ->join('users', 'users.id', '=', 'survey_sections.section_user_id')
                ->select('survey_sections.section_time_start', 'survey_sections.section_time_completed', 'survey_sections.section_action', 'survey_sections.section_connected', 'survey_sections.section_survey_id', 'survey_sections.section_id', 'survey.survey_title', 'survey_sections.section_user_name')
                ->where('survey_sections.section_contract_num', '=', $contractNum)->Where('survey_sections.section_survey_id', '=', 4)
                ->orderBy('survey_sections.section_time_start', 'DESC')
                ->get();
        return $result;
    }

    public function getSurveySectionHiFPTForReportByDay($dayStart, $dayEnd) {
        $result = DB::table('outbound_survey_sections AS oss')
                ->leftJoin('outbound_survey_result AS osr', 'osr.survey_result_section_id', '=', 'oss.section_id')
                ->select('oss.section_contract_num as hopdong', 'oss.section_note as ghichu', 'osr.survey_result_answer_id as diem', 'oss.section_time_completed as ngay', 'oss.section_code')
                ->where('oss.section_survey_id', '=', 5)
                ->where('osr.survey_result_question_id', '=', 18)
                ->where('oss.section_time_completed', '>=', $dayStart)
                ->where('oss.section_time_completed', '<=', $dayEnd)
                ->orderBy('oss.section_time_completed', 'DESC')
                ->get();
        return $result;
    }

    public function checkMissedSurveys() {
        $dateStart = date('Y-m-d H:i:s');
        $dateEnd = date('Y-m-d H:i:s');
        $dateStartInt = strtotime($dateStart) - 1200;
        $dateEndInt = strtotime($dateEnd) - 300;
        $result = DB::table($this->table)
                ->select('section_id')
                ->whereNotIn('section_id', function($query) use ($dateStartInt, $dateEndInt) {
                    $query->select('r.section_id')
                    ->from('survey_section_report AS r')
                    ->where('r.section_time_completed_int', '>=', $dateStartInt)
                    ->where('r.section_time_completed_int', '<=', $dateEndInt)
                    ->whereIn('r.section_survey_id', [1, 2, 3]);
                })
                ->where('section_time_completed_int', '>=', $dateStartInt)
                ->where('section_time_completed_int', '<=', $dateEndInt)
                ->whereIn('section_survey_id', [1, 2, 3])
                ->get();
        return $result;
    }

    public function getSurveySectionsAndResult($param) {
        $sql = DB::table($this->table . ' as oss')
                ->join('outbound_survey_result as osr', 'oss.section_id', '=', 'osr.survey_result_section_id');
        if (isset($param['sectionId'])) {
            $sql->where('oss.section_id', '=', $param['sectionId']);
        }
        if (isset($param['num_type'])) {
            $sql->where('oss.section_survey_id', '=', $param['num_type']);
        }
        if (isset($param['code'])) {
            $sql->where('oss.section_code', '=', $param['code']);
        }
        if (isset($param['shd'])) {
            $sql->where('oss.section_contract_num', '=', $param['shd']);
        }
        $result = $sql->get();
        return $result;
    }

    public function getSurveySections($param) {
        $sql = DB::table($this->table . ' as oss');
        if (isset($param['sectionId'])) {
            $sql->where('oss.section_id', '=', $param['sectionId']);
        }
        if (isset($param['num_type'])) {
            $sql->where('oss.section_survey_id', '=', $param['num_type']);
        }
        if (isset($param['code'])) {
            $sql->where('oss.section_code', '=', $param['code']);
        }
        if (isset($param['shd'])) {
            $sql->where('oss.section_contract_num', '=', $param['shd']);
        }
        $result = $sql->first();
        return $result;
    }

    public function getSurveySectionsAndResultHaveCheckList($param) {
        $sql = DB::table($this->table . ' as oss')
                ->join('outbound_survey_result as osr', 'oss.section_id', '=', 'osr.survey_result_section_id');
        $sql->join('checklist as cl', function($join) {
            $join->on('oss.section_survey_id', '=', 'cl.section_survey_id');
            $join->on('oss.section_code', '=', 'cl.section_code');
            $join->on('oss.section_contract_num', '=', 'cl.section_contract_num');
        });
        if (isset($param['sectionId'])) {
            $sql->where('oss.section_id', '=', $param['sectionId']);
        }
        if (isset($param['num_type'])) {
            $sql->where('oss.section_survey_id', '=', $param['num_type']);
        }
        if (isset($param['code'])) {
            $sql->where('oss.section_code', '=', $param['code']);
        }
        if (isset($param['shd'])) {
            $sql->where('oss.section_contract_num', '=', $param['shd']);
        }
        $result = $sql->get();
        return $result;
    }

    public function getCustommerCommentReport($from_date, $to_date, $locationID, $locationSelected, $hasFifter) {
        $summaryOpinion = new SummaryOpinion();
//        DB::enableQueryLog();
        $resultOpinionSummary = $summaryOpinion->getOpinionSummaryByTime($from_date, $to_date, $locationID, $hasFifter);
//        $query=DB::getQueryLog();
//        dump($resultOpinionSummary);die;
        $opinionList = [
            'SIR' => [151, 'Staffs'],
            'IBB' => [152, 'Staffs'],
            'CC' => [153, 'Staffs'],
            'CUS' => [154, 'Staffs'],
            'Collector' => [155, 'Staffs'],
            'Onsite' => [156, 'Staffs'],
            'InternetSpeed' => [160, 'InternetService'],
            'InternetStable' => [161, 'InternetService'],
            'Game' => [162, 'InternetService'],
            'Modem' => [170, 'Equipment'],
            'Router' => [171, 'Equipment'],
            'ONU' => [172, 'Equipment'],
            'RegisterPayment' => [180, 'Policy'],
            'Promotion' => [181, 'Policy'],
            'CustomerCareAfterSell' => [182, 'Policy'],
            'MaintenanceCommitment' => [183, 'Policy'],
            'Package' => [190, 'Price'],
            'EquipmentPrice' => [191, 'Price'],
            'Upgrade' => [192, 'Price'],
            'InstallationDuration' => [201, 'SupportDuration'],
            'MaintenanceDuration' => [202, 'SupportDuration'],
            'ComplainSolvingDuration' => [203, 'SupportDuration'],
            'NoComment' => [200, 'NoCommentGroup'],
            'Other' => [210, 'Other'],
        ];
        foreach ($resultOpinionSummary['resultDetailGroupNPS'] as $key => $value) {
            $resultOpinionSummary['resultDetailGroupNPS'][$key] = (array) $value;
        }
        foreach ($resultOpinionSummary['resultDetailGroupNPS'] as $key => $value) {
            $resultOpinionSummary['resultDetailGroupNPS'][$key]['answer_id'] = $opinionList[$value['Content']][0];
        }

        foreach ($resultOpinionSummary['resultDetailGroupNPS'] as $value) {
            $resultOpinionSummaryKey[$value['answer_id']] = $value;
        }
        foreach ($opinionList as $key => $value) {
            $resultOpinionSummaryInitiate[$value[0]] = [
                'answer_id' => $value[0],
                'Content' => $key,
                'answers_group_title' => $value[1],
                'Total' => "0",
                'SauBT' => "0",
                'SauTK' => "0",
            ];
            ;
        }
        foreach ($resultOpinionSummaryInitiate as $key => $value) {
            if (isset($resultOpinionSummaryKey[$key])) {
                $resultOpinionSummaryInitiate[$key] = $resultOpinionSummaryKey[$key];
            }
        }
        $resultOpinionSummary['resultDetailGroupNPS'] = $resultOpinionSummaryInitiate;
//        foreach ($opinionList as $key => $value) {
//          if(!isset($resultOpinionSummary['resultSummaryOpinion'][$value[0]]))
//          {
//              $resultOpinionSummary['resultSummaryOpinion'][$value[0]]=[
//                    'answer_id' => $value[0],
//                    'NoiDung' => $key,
//                    'answers_group_title' => $value[1],
//                    'TongCong' => "0",
//                    'SauTC' => "0",
//                    'SauBTINDO' => "0",
//                    'SauBTTIN' => "0",
//                    'SauTKTS' => "0",
//                    'SauTK' => "0",
//                ];
//          }
//        }
        foreach ($resultOpinionSummary['resultDetailGroupNPS'] as $key => $value) {
            $resultOpinionSummary['resultDetailGroupNPS'][$key] = (object) $value;
        }
        //Tinh tong cong y kien theo loai khao sat
        $SauTK = $SauBT = 0;
        foreach ($resultOpinionSummary['resultDetailGroupNPS'] as $key => $value) {
            $SauTK+=$value->SauTK;
            $SauBT+=$value->SauBT;
        }
        $total = [
            'SauTK' => $SauTK,
            'SauBT' => $SauBT,
            'Total' => $SauTK + $SauBT
        ];
        $totalCusComment = [];
        $totalCusNoComment = [];
        $totalConsulted = [];
        $totalCusCombine = [$totalCusComment, $totalCusNoComment, $totalConsulted];
        foreach ($totalCusCombine as $key => $value) {
            $totalCusCombine[$key]['SauTK'] = 0;
            $totalCusCombine[$key]['SauBT'] = 0;
        }
        $totalCusComment = $totalCusCombine[0];
        $totalCusNoComment = $totalCusCombine[1];
        $totalConsulted = $totalCusCombine[2];
        foreach ($resultOpinionSummary['resultGroupNPS'] as $key => $value) {
            $totalCusComment[$value->TypeSurvey] = $value->TongSoKHGopY;
            $totalCusNoComment[$value->TypeSurvey] = $value->TongSoKHKhongGopY;
            $totalConsulted[$value->TypeSurvey] = $value->TongSoKHDuocHoi;
        }
        $totalCusComment['Total'] = 0;
        foreach ($totalCusComment as $key => $value) {
            $totalCusComment['Total']+=$value;
        }
        $totalCusNoComment['Total'] = 0;
        foreach ($totalCusNoComment as $key => $value) {
            $totalCusNoComment['Total']+=$value;
        }
        $totalConsulted['Total'] = 0;
        foreach ($totalConsulted as $key => $value) {
            $totalConsulted['Total']+=$value;
        }
        if (empty($resultOpinionSummary['resultDetailGroupNPS'])) {
            $i = 1;
            foreach ($opinionList as $key => $value) {
                $resultOpinionSummary['resultDetailGroupNPS'][$i] = [
                    'answer_id' => $value[0],
                    'Content' => $key,
                    'answers_group_title' => $value[1],
                    'Total' => 0,
                    'SauTK' => 0,
                    'SauBT' => 0,
                ];
                $i++;
            }
            foreach ($resultOpinionSummary['resultDetailGroupNPS'] as $key => $value) {
                $resultOpinionSummary['resultDetailGroupNPS'][$key] = (object) $value;
            }
        }


        $result = ['survey' => $resultOpinionSummary['resultDetailGroupNPS'], 'total' => $total, 'totalCusComment' => $totalCusComment, 'totalCusNoComment' => $totalCusNoComment, 'totalConsulted' => $totalConsulted];
        $customerComment = (object) $result;
//        dump('adasd');
//        dump($customerComment);die;
        $param = ['survey' => $customerComment->survey, 'total' => (array) $customerComment->total, 'totalCusComment' => (array) $customerComment->totalCusComment, 'totalCusNoComment' => (array) $customerComment->totalCusNoComment, 'totalConsulted' => (array) $customerComment->totalConsulted, 'locationSelected' => $locationSelected, 'from_date' => $from_date, 'to_date' => $to_date];
        //Lấy từ Report có fiflter
        if ($hasFifter == 1) {
            return [0 => view("report/customersCommentReport", $param)->render(), 1 => $param];
        } else {
            return $customerComment;
        }
    }

    public function getSurveySectionCSATByDay($day) {
        $result = DB::table($this->table . ' as s')
                ->join('outbound_survey_result as sr', 'sr.survey_result_section_id', '=', 's.section_id')
                ->select('s.section_id', 's.section_survey_id as loaiKhaoSat', 's.section_location_id as soViTri', 's.section_location as tenViTri', 's.section_branch_code as chiNhanh', 's.section_sub_parent_desc as vung', 's.section_contract_num as soHopDong', 's.section_objAddress as diaChiKhachHang', 's.section_contact_phone as dienThoaiKhachHang', 's.section_acc_sale as nhanVienKinhDoanh', 's.section_time_completed_int as thoiGianGhiNhan', 's.section_action', 's.section_supporter', 's.section_subsupporter', 'sr.survey_result_question_id', 'sr.survey_result_answer_id', 'sr.survey_result_answer_extra_id', 'sr.survey_result_note', 'sr.survey_result_action')
//                ->whereIn('s.section_survey_id', [1, 2, 3, 6, 9, 10])
                ->whereIn('s.section_survey_id', [1, 2, 6, 9, 10])
                ->where('s.section_time_completed_int', '<=', strtotime($day . ' 23:59:59'))
                ->where('s.section_time_completed_int', '>=', strtotime($day . ' 00:00:00'))
                ->where('s.section_connected', '=', 4)
                ->whereIn('sr.survey_result_answer_id', [1, 2])
//                ->whereIn('sr.survey_result_question_id', [10, 11, 12, 13, 14, 15, 20, 21, 41, 42, 46, 47])
                ->whereIn('sr.survey_result_question_id', [10, 11, 12, 13, 20, 21, 41, 42, 46, 47])
                ->whereRaw('sr.survey_result_answer_extra_id <> 88')
                ->get();
        return $result;
    }

    public function getSurveySectionCSATNV($day, $dayTo) {
        $likeSale = '"sales":null';
        $likeDeploy = '"deployer":null';
        $likeMaintenance = '"maintenance":null';
        $likeChargeStaff = '"nvtc":null';
        $sqlRaw = "s.section_sub_parent_desc, s.section_location_id, s.section_location, convert(s.section_branch_code, unsigned integer) as section_branch_code,
                sum(if(sr.survey_result_question_id = 1 and s.section_survey_id = 1,1,0)) as csat_tong_sales,
                sum(if(sr.survey_result_answer_id = 1 and sr.survey_result_question_id in (1) and s.section_survey_id in (1),1,0)) as csat1_sales,
                sum(if(sr.survey_result_answer_id = 2 and sr.survey_result_question_id in (1) and s.section_survey_id in (1),1,0)) as csat2_sales,
                sum(if(sr.survey_result_answer_id in (1,2) and sr.survey_result_question_id in (1) and s.section_survey_id in (1),1,0)) as csat12_sales,
                sum(if((sr.survey_result_question_id = 1 and s.section_survey_id = 1) and sr.survey_result_answer_id in (1,2,3,4,5),sr.survey_result_answer_id,0)) as csat12_diem_sales,
                sum(if(sr.survey_result_answer_id = 1 and sr.survey_result_question_id in (1) and s.section_survey_id in (1) and (s.violation_status like '%" . $likeSale . "%' or s.violation_status is null),1,0)) as csat1_cbc_sales,
                sum(if(sr.survey_result_answer_id = 2 and sr.survey_result_question_id in (1) and s.section_survey_id in (1) and (s.violation_status like '%" . $likeSale . "%' or s.violation_status is null),1,0)) as csat2_cbc_sales,
                sum(if(sr.survey_result_answer_id in (1,2) and sr.survey_result_question_id in (1) and s.section_survey_id in (1) and (s.violation_status like '%" . $likeSale . "%' or s.violation_status is null),1,0)) as csat12_cbc_sales,
                
                sum(if(sr.survey_result_question_id = 2 and s.section_survey_id = 1,1,0)) as csat_tong_deploy,
                sum(if(sr.survey_result_answer_id = 1 and sr.survey_result_question_id in (2) and s.section_survey_id in (1),1,0)) as csat1_deploy,
                sum(if(sr.survey_result_answer_id = 2 and sr.survey_result_question_id in (2) and s.section_survey_id in (1),1,0)) as csat2_deploy,
                sum(if(sr.survey_result_answer_id in (1,2) and sr.survey_result_question_id in (2) and s.section_survey_id in (1),1,0)) as csat12_deploy,
                sum(if((sr.survey_result_question_id = 2 and s.section_survey_id = 1) and sr.survey_result_answer_id in (1,2,3,4,5),sr.survey_result_answer_id,0)) as csat12_diem_deploy,
                sum(if(sr.survey_result_answer_id = 1 and sr.survey_result_question_id in (2) and s.section_survey_id in (1) and (s.violation_status like '%" . $likeDeploy . "%' or s.violation_status is null),1,0)) as csat1_cbc_deploy,
                sum(if(sr.survey_result_answer_id = 2 and sr.survey_result_question_id in (2) and s.section_survey_id in (1) and (s.violation_status like '%" . $likeDeploy . "%' or s.violation_status is null),1,0)) as csat2_cbc_deploy,
                sum(if(sr.survey_result_answer_id in (1,2) and sr.survey_result_question_id in (2) and s.section_survey_id in (1) and (s.violation_status like '%" . $likeDeploy . "%' or s.violation_status is null),1,0)) as csat12_cbc_deploy,
                
                sum(if(s.section_survey_id = 2 and sr.survey_result_question_id = 4,1,0)) as csat_tong_maintain,
                sum(if(sr.survey_result_answer_id = 1 and sr.survey_result_question_id = 4 and s.section_survey_id = 2,1,0)) as csat1_maintain,
                sum(if(sr.survey_result_answer_id = 2 and sr.survey_result_question_id = 4 and s.section_survey_id = 2,1,0)) as csat2_maintain,
                sum(if(sr.survey_result_answer_id in (1,2) and sr.survey_result_question_id = 4 and s.section_survey_id = 2,1,0)) as csat12_maintain,
                sum(if(s.section_survey_id = 2 and sr.survey_result_question_id = 4 and sr.survey_result_answer_id in (1,2,3,4,5),sr.survey_result_answer_id,0)) as csat12_diem_maintain,
                sum(if(sr.survey_result_answer_id = 1 and sr.survey_result_question_id = 4 and s.section_survey_id = 2 and (s.violation_status like '%" . $likeMaintenance . "%' or s.violation_status is null),1,0)) as csat1_cbc_maintain,
                sum(if(sr.survey_result_answer_id = 2 and sr.survey_result_question_id = 4 and s.section_survey_id = 2 and (s.violation_status like '%" . $likeMaintenance . "%' or s.violation_status is null),1,0)) as csat2_cbc_maintain,
                sum(if(sr.survey_result_answer_id in (1,2) and sr.survey_result_question_id = 4 and s.section_survey_id = 2 and (s.violation_status like '%" . $likeMaintenance . "%' or s.violation_status is null),1,0)) as csat12_cbc_maintain,
                    
                sum(if(sr.survey_result_question_id = 35 and s.section_survey_id = 7,1,0)) as csat_tong_nvtc,
                sum(if(sr.survey_result_answer_id = 1 and sr.survey_result_question_id in (35) and s.section_survey_id in (7),1,0)) as csat1_nvtc,
                sum(if(sr.survey_result_answer_id = 2 and sr.survey_result_question_id in (35) and s.section_survey_id in (7),1,0)) as csat2_nvtc,
                sum(if(sr.survey_result_answer_id in (1,2) and sr.survey_result_question_id in (35) and s.section_survey_id in (7),1,0)) as csat12_nvtc,
                sum(if((sr.survey_result_question_id = 35 and s.section_survey_id = 7) and sr.survey_result_answer_id in (1,2,3,4,5),sr.survey_result_answer_id,0)) as csat12_diem_nvtc,
                sum(if(sr.survey_result_answer_id = 1 and sr.survey_result_question_id in (35) and s.section_survey_id in (7) and (s.violation_status like '%" . $likeChargeStaff . "%' or s.violation_status is null),1,0)) as csat1_cbc_nvtc,
                sum(if(sr.survey_result_answer_id = 2 and sr.survey_result_question_id in (35) and s.section_survey_id in (7) and (s.violation_status like '%" . $likeChargeStaff . "%' or s.violation_status is null),1,0)) as csat2_cbc_nvtc,
                sum(if(sr.survey_result_answer_id in (1,2) and sr.survey_result_question_id in (35) and s.section_survey_id in (7) and (s.violation_status like '%" . $likeChargeStaff . "%' or s.violation_status is null),1,0)) as csat12_cbc_nvtc
               ";
        $result = DB::table($this->table . ' as s')
                ->join('outbound_survey_result as sr', 'sr.survey_result_section_id', '=', 's.section_id')
                ->selectRaw($sqlRaw)
                ->whereIn('s.section_survey_id', [1, 2, 7])
                ->where('s.section_connected', '=', 4)
                ->whereNotNull('s.section_action')
                ->whereIn('sr.survey_result_question_id', [1, 2, 4, 35])
                ->where('s.section_time_completed_int', '<=', strtotime($dayTo))
                ->where('s.section_time_completed_int', '>=', strtotime($day))
                ->groupBy('s.section_sub_parent_desc', 's.section_location_id', 's.section_branch_code')
                ->orderBy('s.section_sub_parent_desc', 's.section_location_id', 's.section_branch_code')
                ->get();
        return $result;
    }

    public function countListSurveyGeneral($condition) {
        $result = $this->createQueryDetail($condition, null);
        $result =$result['total'];
        return $result;
    }

    public function searchListSurveyGeneral($condition, $numberPage) {
        $dataPerPage = $this->createQueryDetail($condition, $numberPage);
//        if (!empty($condition['recordPerPage'])) {
        //Lấy bình thường 50 record
//        $result->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage']);
//        }
        $total = $dataPerPage['total'];
        $dataFullPerPage = $this->attachSectionData($dataPerPage['dataPerPage']);
//        $total =  $dataFullPerPage['total'];
//        $result = $result->get();
//        foreach ($result as $key => $value) {
//            $result[$key] = (array) $value;
//        }
//        return $result;
        return ['data' => $dataFullPerPage, 'total' => $total] ;
    }

    private function createQueryDetail($condition, $numberPage) {

        $result = DB::table('outbound_survey_sections as oss')
            ->select(DB::raw("oss.section_id"))
            ->where('oss.section_time_completed_int' ,'>=',  $condition['survey_from_int'])
            ->where('oss.section_time_completed_int' ,'<=',  $condition['survey_to_int'])
//                ->join('outbound_survey_result as osr', 'oss.section_id','=','osr.survey_result_section_id')
//            ->where(function($query) use ($condition) {
//                if (!empty($condition['departmentType']) && ($condition['departmentType'] == 2 || $condition['departmentType'] == 3)) {//TIN hoặc PNC
//                    if ($condition['departmentType'] == 2) {//TIN: Vùng 1,2,3,4
//                        $regionTIN = ['Vung 1', 'Vung 2', 'Vung 3', 'Vung 4'];
//                        $query->whereIn('oss.section_sub_parent_desc', $regionTIN);
//                    } else if ($condition['departmentType'] == 3) {
//                        $regionPNC = ['Vung 4', 'Vung 5', 'Vung 6', 'Vung 7'];
//                        $query->whereIn('oss.section_sub_parent_desc', $regionPNC);
//                    }
//                }
//                if (!empty($condition['region'])) {
//                    foreach ($condition['region'] as &$val) {
//                        $val = 'Vung ' . $val;
//                    }
//                    $query->whereIn('oss.section_sub_parent_desc', $condition['region']);
//                }
//            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['contractNum'])) {
                    $query->where('oss.section_contract_num', '=', $condition['contractNum']);
                }
            })
            ->where(function($query) use ($condition) {
//                if (!empty($condition['type'])) {
//                    if ($condition['departmentType'] == 8) {//TELESALES
//                        $query->where("oss.section_survey_id", '=', 6);
//                    } else {
                        $query->where('oss.section_survey_id', '=', $condition['type']);
//                    }
//                }
            })
//            ->where(function($query) use ($condition) {
//                if (!empty($condition['section_action'])) {
//                    $query->whereIn('t.section_action', $condition['section_action']);
//                }
//            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['section_connected'])) {
                    $query->whereIn('oss.section_connected', $condition['section_connected']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['userSurvey'])) {
                    $query->where('oss.section_user_name', '=', $condition['userSurvey']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['salerName'])) {
                    $query->where('oss.section_acc_sale', '=', $condition['salerName']);
                }
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['technicalStaff'])) {
                    $query->orWhere('oss.section_account_inf', '=', $condition['technicalStaff']);
                    $query->orWhere('oss.section_account_list', '=', $condition['technicalStaff']);
                }
            })
            ->where(function($query) use ($condition) {
//                if (!empty($condition['branchcode'])) {
//                    $query->whereIn('oss.section_branch_code', $condition['branchcode']);
//                }
//                if (!empty($condition['brandcodeSaleMan'])) {
//                    $query->whereIn('oss.section_sale_branch_code', $condition['brandcodeSaleMan']);
//                }
//                if (isset($condition['justOnlyLocation'])) {
//                    if (!empty($condition['location'])) {
//                        $query->WhereIn('oss.section_location_id', $condition['location']);
//                    }
//                } else {
                    if (!empty($condition['location'])) {
                        $query->orWhereIn('oss.section_location_id', $condition['location']);
                    }
//                }
            })
//            ->where(function($query) use ($condition) {
//                if ($condition['departmentType'] == 2)//TIN
//                    $query->whereRaw("oss.section_supporter LIKE '%TIN%'");
//                else if ($condition['departmentType'] == 3)//PNC
//                    $query->whereRaw("oss.section_supporter LIKE '%PhuongNam%'");
//                else if ($condition['departmentType'] == 4)//INDO
//                    $query->whereRaw("oss.section_supporter LIKE '%INDO%'");
//            })
            ->where('oss.section_action', $condition['sectionGeneralAction'])
            ->orderBy('oss.section_time_completed_int', 'desc')
            ->get();
//        dump($result);die;
        $rawSectionId=[];
        foreach ($result as $sectionId)
        {
            array_push($rawSectionId, $sectionId->section_id );
        }
        //Có dữ liệu
        if(!empty($rawSectionId)) {
            $selectTableRaw = '(select osr.survey_result_section_id as "section_id",
      MAX(if(osr.survey_result_question_id in (5, 9) and osr.survey_result_answer_id <> -1 , osr.survey_result_answer_id, "")) "CSAT_Internet",
      MAX(if(osr.survey_result_question_id in (5, 9) and osr.survey_result_answer_id in (1,2), osr.survey_result_answer_extra_id, ""))  "Loai_loi_internet",
      MAX(if(osr.survey_result_question_id in (5, 9) and osr.survey_result_answer_id in (1,2), osr.survey_result_action, ""))  "Xu_ly_internet" from outbound_survey_result as osr
      where osr.survey_result_question_id in (5, 9) and osr.survey_result_section_id in (' . implode(',', $rawSectionId) . ') group by osr.survey_result_section_id) as t';
            $resultDetail = DB::table(DB::raw($selectTableRaw))
                ->select('*')
                ->where(function ($query) use ($condition) {
                    if (!empty($condition['CSATPointNet'])) {
                        // alias 5 là Internet
                        $query->WhereIn('t.CSAT_Internet', $condition['CSATPointNet']);
                    } else {
                        if ($condition['sectionGeneralAction'] == 1) {
                            $query->WhereIn('t.CSAT_Internet', [1, 2]);
                        }
                    }
                })
//                ->where(function ($query) use ($condition) {
//                    if (!empty($condition['CSATPointTV'])) {
//                        // alias 6 là truyền hình
//                        $query->WhereIn('t.CSAT_truyen_hinh', $condition['CSATPointTV']);
//                    } else {
//                        if ($condition['sectionGeneralAction'] == 1) {
//                            $query->WhereIn('t.CSAT_truyen_hinh', [1, 2]);
//                        }
//                    }
//                })
                ->where(function ($query) use ($condition) {
                    if (!empty($condition['NetErrorType'])) {
                        // alias 5 là internet
                        $query->Where('t.Loai_loi_internet', $condition['NetErrorType']);
                    }
                })
//                ->where(function ($query) use ($condition) {
//                    if (!empty($condition['TVErrorType'])) {
//                        // alias 6 là truyền hình
//                        $query->Where('t.Loai_loi_TV', $condition['TVErrorType']);
//                    }
//                })
//                ->where(function ($query) use ($condition) {
//                    if (!empty($condition['CSATPointTV'])) {
//                        if (in_array("1", $condition['CSATPointTV']) || in_array("2", $condition['CSATPointTV'])) {
//                            if (!empty($condition['processingActionsTV'])) {
//                                // alias 6 là truyền hình
//                                $query->Where('t.Xu_ly_TV', $condition['processingActionsTV']);
//                            }
//                        }
//                    }
//                })
                ->where(function ($query) use ($condition) {
                    if (!empty($condition['CSATPointNet'])) {
                        if (in_array("1", $condition['CSATPointNet']) || in_array("2", $condition['CSATPointNet'])) {
                            if (!empty($condition['processingActionsInternet'])) {
                                // alias 5 là internet
                                $query->Where('t.Xu_ly_internet', $condition['processingActionsInternet']);
                            }
                        }
                    }
                })->orderBy('t.section_id', 'desc')->get();
        }
        else
        {
            $resultDetail = [];
        }
        $total=count($resultDetail);
        //Count số trang để xuất excel
        if($numberPage === null)
        {
            return ['total' =>$total] ;
        }
        $dataPerPage = array_slice($resultDetail,$numberPage * $condition['recordPerPage'],$condition['recordPerPage']);
        return ['dataPerPage' => $dataPerPage, 'total' =>$total] ;
//        dump($resultDetail); die;
    }

    public function attachSectionData($dataPerPage)
    {
//        dump($dataPerPage);
        $sectionId=[];
        $dataPerPageKey = [];
        $emptyData =[
            'section_id' => '',
            'ChiNhanh' => '',
            'section_survey_id' => '',
            'section_code' => '',
            'section_record_channel'=> '',
            'section_contract_num'=> '',
            'section_acc_sale'=> '',
            'section_account_inf'=> '',
            'section_account_list'=> '',
            'section_time_completed'=> '',
            'Created_date'=> '',
            'section_location_id'=> '',
            'section_branch_code'=> '',
            'section_sale_branch_code'=> '',
            'section_region'=> '',
            'section_action'=> '',
            'section_connected'=> '',
            'section_user_name'=> '',
            'section_supporter'=> '',
            'violation_status'=> '',
            'section_note'=> ''
        ];
        foreach($dataPerPage as $detail)
        {
            array_push($sectionId, $detail->section_id );
            $dataPerPageKey[$detail->section_id] = (array)$detail;
        }
        $sectionResult = DB::table('outbound_survey_sections as oss')
            ->select(DB::raw(' 
          oss.section_id,
          oss.section_location as "ChiNhanh",
          oss.section_survey_id,
          oss.section_code,
          oss.section_record_channel,
          oss.section_contract_num,
          oss.section_acc_sale,
          oss.section_account_inf,
          oss.section_account_list,
          oss.section_time_completed,
          oss.section_time_completed_int as "Created_date",
          oss.section_location_id,
          oss.section_region,
          oss.section_action,
          oss.section_connected,
          oss.section_user_name,
          oss.section_supporter,
          oss.violation_status,
           oss.section_note'))
            ->whereIn('oss.section_id',$sectionId)
            ->get();
        $finalResult = [];
        foreach($sectionResult as $key => $section)
        {
            $sectionArray = (array) $section;
            if(isset($dataPerPageKey[$section->section_id]))
            {
                array_push($finalResult, $sectionArray + $dataPerPageKey[$section->section_id]);
            }
            else
            {
                array_push($finalResult, $sectionArray + $emptyData);
            }
        }

//        dump($finalResult);die;
        return $finalResult;

    }

    public function getCsatT1T3($numberPage, $condition) {
        $query = '(select oss.section_sub_parent_desc "Vung",
      oss.section_location as "ChiNhanh", 
      oss.section_survey_id,
      oss.section_code,
      oss.section_record_channel, 
      oss.section_contract_num,
      oss.section_acc_sale,
      oss.section_account_inf,
      oss.section_account_list,
      oss.section_time_completed,
      oss.section_location_id,
      oss.section_branch_code,
      oss.section_sale_branch_code,
      oss.section_region,
      oss.section_action,
      oss.section_connected,
      oss.section_user_name,
      oss.section_supporter,
      oss.violation_status,
       oss.section_note,
      MAX(if(osr.survey_result_question_id in (10,12,20,41,46) and osr.survey_result_answer_id <> -1 , osr.survey_result_answer_id, "")) "CSAT_Internet",
      MAX(if(osr.survey_result_question_id in (11,13,21,42,47) and osr.survey_result_answer_id <> -1, osr.survey_result_answer_id, "")) "CSAT_truyen_hinh"
  
		from `outbound_survey_sections` as `oss` inner join `outbound_survey_result` as `osr` on `oss`.`section_id` = `osr`.`survey_result_section_id`
		 where `oss`.`section_time_completed_int` >= ' . strtotime('2018-03-01 00:00:00') . ' and `oss`.`section_time_completed_int` <= ' . strtotime('2018-03-31 23:59:59') .
                ' and `osr`.`survey_result_question_id` in (10, 11, 12, 13, 20, 21, 41, 42, 46, 47) and oss.section_survey_id in (1,2,6,9,10)
                     and osr.survey_result_answer_id in (4,5)
		 group by `oss`.`section_id`) as t';
        $result = DB::table(DB::raw($query))
                ->select(DB::raw("*"));
        $result = $result->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage'])->get();
//$result = $result->get();
//dump($condition['recordPerPage'],$numberPage);
        foreach ($result as $key => $value) {
            $result[$key] = (array) $value;
        }
        return $result;
    }

    public function CountGetCsatT1T3() {
        $query = '(select oss.section_sub_parent_desc "Vung"
		from `outbound_survey_sections` as `oss` inner join `outbound_survey_result` as `osr` on `oss`.`section_id` = `osr`.`survey_result_section_id`
		 where `oss`.`section_time_completed_int` >= ' . strtotime('2018-03-01 00:00:00') . ' and `oss`.`section_time_completed_int` <= ' . strtotime('2018-03-31 23:59:59') .
                ' and `osr`.`survey_result_question_id` in (10, 11, 12, 13, 20, 21, 41, 42, 46, 47) and oss.section_survey_id in (1,2,6,9,10)
                     and osr.survey_result_answer_id in (4,5)
		 group by `oss`.`section_id`) as t';
        $result = DB::table(DB::raw($query))
                ->select(DB::raw("*"));
        $result = $result->count();
        return $result;
    }

    public function attachActionDataToSurvey($result, $condition)
    {
        //Không có dữ liệu thì trả về luôn
        if (empty($result))
            return $result;
        if ($condition['sectionGeneralAction'] == 1) {
            $finalResult = $result;
        } else
        {
            $listContractNum = $listSurveyID = $listSectionCode = [];
            foreach ($result as $key => $value) {
                array_push($listContractNum, $value['section_contract_num']);
                array_push($listSectionCode, $value['section_code']);
                array_push($listSurveyID, $value['section_survey_id']);

            }
            //Chọn xử lý prechecklist
            if ($condition['sectionGeneralAction'] == 3) {
//            DB::enableQueryLog();


                $preclResult = DB::table('prechecklist as pc')
                    ->select("*")
                    ->whereIn('pc.section_contract_num', $listContractNum)
                    ->whereIn('pc.section_code', $listSectionCode)
                    ->whereIn('pc.section_survey_id', $listSurveyID)
                    ->get();
                if (empty($preclResult)) {
                    $sectionSurveyPreCL = [];
//        Tong hop du lieu PreCl
                    foreach ($result as $key1 => $survey) {
                        $survey = (array)$survey;
                        $preclArray = ['location_name' => '',
                            'first_status' => '',
                            'location_phone' => '',
                            'division_id' => '',
                            'description' => '',
                            'create_by' => '',
                            'update_date' => '',
                            'appointment_timer' => '',
                            'status' => '',
                            'sup_status_id' => '',
                            'created_at' => '',
                            'updated_at' => '',
                            'count_sup' => '',
                            'total_minute' => '',
                            'action_process' => '',
                            'id_prechecklist_isc' => '',
                            'sup_id_partner' => '',
                            'idChecklistIsc' => '',
                            'subkey' => 0
                        ];
                        $arrayEmptyPrecl = array_merge($survey, $preclArray);
                        array_push($sectionSurveyPreCL, $arrayEmptyPrecl);
                    }
                } else {
                    $keyPreclResult = [];
//                    $queryLog=DB::getQueryLog();
                    //Chuyen sang mang key
                    foreach ($preclResult as $key => $value) {
                        $value = (array)$value;
                        //Case dau tien
                        if (!isset($keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']])) {
                            $value['subkey'] = 0;
                            $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']] = $value;
                        } else {
//                $valueCheck=$preclResult[$value['section_contract_num'].$value['section_code'].$value['section_survey_id']];
//                if($valueCheck['subkey'])
                            $subkey = $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'];
                            $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'] = $subkey + 1;
//                $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id'] . 'plus' . ($subkey + 1)] = $value;
                        }
                    }
//            dump($preclResult);
//               dump($keyPreclResult);
//            die;
                    $sectionSurveyPreCL = [];
//        Tong hop du lieu PreCl
                    foreach ($result as $key1 => $survey) {
                        $survey = (array)$survey;
                        $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'];
                        if (isset($keyPreclResult[$entry])) {
                            $array_merge = array_merge($survey, $keyPreclResult[$entry]);
                            array_push($sectionSurveyPreCL, $array_merge);
//                if($keyPreclResult[$entry]['subkey']
//                dump($keyPreclResult[$entry]['subkey']);die;
//                $numSubkey = $keyPreclResult[$entry]['subkey'];
//                for ($i = 1; $i <= $numSubkey; $i++) {
//                    $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'] . 'plus' . $i;
//                    $array_merge = array_merge($survey, $keyPreclResult[$entry]);
//                    array_push($sectionSurveyPreCL, $array_merge);
//                }
                        } else {
                            $preclArray = ['location_name' => '',
                                'first_status' => '',
                                'location_phone' => '',
                                'division_id' => '',
                                'description' => '',
                                'create_by' => '',
                                'update_date' => '',
                                'appointment_timer' => '',
                                'status' => '',
                                'sup_status_id' => '',
                                'created_at' => '',
                                'updated_at' => '',
                                'count_sup' => '',
                                'total_minute' => '',
                                'action_process' => '',
                                'id_prechecklist_isc' => '',
                                'sup_id_partner' => '',
                                'idChecklistIsc' => '',
                                'subkey' => 0
                            ];
                            $arrayEmptyPrecl = array_merge($survey, $preclArray);
                            array_push($sectionSurveyPreCL, $arrayEmptyPrecl);
                        }
                    }
                }

//            dump($result);die;
                $finalResult = $sectionSurveyPreCL;
            } //Chọn CL
            else if ($condition['sectionGeneralAction'] == 2) {
                //Gan Checklist
                $checklistResult = DB::table('checklist as c')
                    ->select('*')
                    ->whereIn('c.section_contract_num', $listContractNum)
                    ->whereIn('c.section_code', $listSectionCode)
                    ->whereIn('c.section_survey_id', $listSurveyID)
                    ->get();
                if (empty($checklistResult)) {
                    $sectionSurveyCL = [];
                    foreach ($result as $key1 => $survey) {
                        $survey = (array)$survey;
                        $clArray = ['i_type' => '',
                            's_create_by' => '',
                            'i_lnit_status' => '',
                            's_description' => '',
                            'i_modem_type' => '',
                            'supporter' => '',
                            'sub_supporter' => '',
                            'dept_id' => '',
                            'request_from' => '',
                            'owner_type' => '',
                            'created_at' => '',
                            'updated_at' => '',
                            'final_status' => '',
                            'final_status_id' => '',
                            'total_minute' => '',
                            'input_time' => '',
                            'assign' => '',
                            'store_time' => '',
                            'error_position' => '',
                            'error_description' => '',
                            'reason_description' => '',
                            'way_solving' => '',
                            'checklist_type' => '',
                            'repeat_checklist' => '',
                            'finish_date' => '',
                        ];
                        $arrayEmptyCl = array_merge($survey, $clArray);
                        array_push($sectionSurveyCL, $arrayEmptyCl);

                    }
                }
//            } else
//                $checklistResult = [];
//            dump($checklistResult);
                else {
                    $keyClResult = [];
                    //Chuyen sang mang key
                    foreach ($checklistResult as $key => $value) {
                        $value = (array)$value;
                        //Case dau tien
                        if (!isset($keyClResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']])) {
                            $value['subkey'] = 0;
                            $keyClResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']] = $value;
                        } else {
//                $valueCheck=$preclResult[$value['section_contract_num'].$value['section_code'].$value['section_survey_id']];
//                if($valueCheck['subkey'])
                            $subkey = $keyClResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'];
                            $keyClResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'] = $subkey + 1;
//                $keyPreclResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id'] . 'plus' . ($subkey + 1)] = $value;
                        }
                    }
//            dump($preclResult);
//               dump($keyPreclResult);
//            die;
                    $sectionSurveyCL = [];
//        Tong hop du lieu Cl
                    foreach ($result as $key1 => $survey) {
                        $survey = (array)$survey;
                        $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'];
                        if (isset($keyClResult[$entry])) {

                            $array_merge = array_merge($survey, $keyClResult[$entry]);
                            array_push($sectionSurveyCL, $array_merge);
//                if($keyPreclResult[$entry]['subkey']
//                dump($keyPreclResult[$entry]['subkey']);die;
//                $numSubkey = $keyPreclResult[$entry]['subkey'];
//                for ($i = 1; $i <= $numSubkey; $i++) {
//                    $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'] . 'plus' . $i;
//                    $array_merge = array_merge($survey, $keyPreclResult[$entry]);
//                    array_push($sectionSurveyPreCL, $array_merge);
//                }
                        } else {
                            $clArray = ['i_type' => '',
                                's_create_by' => '',
                                'i_lnit_status' => '',
                                's_description' => '',
                                'i_modem_type' => '',
                                'supporter' => '',
                                'sub_supporter' => '',
                                'dept_id' => '',
                                'request_from' => '',
                                'owner_type' => '',
                                'created_at' => '',
                                'updated_at' => '',
                                'final_status' => '',
                                'final_status_id' => '',
                                'total_minute' => '',
                                'input_time' => '',
                                'assign' => '',
                                'store_time' => '',
                                'error_position' => '',
                                'error_description' => '',
                                'reason_description' => '',
                                'way_solving' => '',
                                'checklist_type' => '',
                                'repeat_checklist' => '',
                                'finish_date' => '',
                            ];
                            $arrayEmptyCl = array_merge($survey, $clArray);
                            array_push($sectionSurveyCL, $arrayEmptyCl);
                        }
                    }
                }
//            dump($sectionSurveyCL);die;
                $finalResult = $sectionSurveyCL;
//            dump($finalResult);die;
            }
//        }
            //Chọn xử lý chuyển phòng ban
//            else if ($condition['sectionGeneralAction'] == 5) {
////            DB::enableQueryLog();
//                $fdResult = DB::table('foward_department as fd')
//                    ->select("*")
//                    ->whereIn('fd.section_contract_num', $listContractNum)
//                    ->whereIn('fd.section_code', $listSectionCode)
//                    ->whereIn('fd.section_survey_id', $listSurveyID)
//                    ->get();
//                $keyFdResult = [];
//                //Chuyen sang mang key
//                foreach ($fdResult as $key => $value) {
//                    $value = (array)$value;
//                    //Case dau tien
//                    if (!isset($keyFdResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']])) {
//                        $value['subkey'] = 0;
//                        $keyFdResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']] = $value;
//                    } else {
////                $valueCheck=$preclResult[$value['section_contract_num'].$value['section_code'].$value['section_survey_id']];
////                if($valueCheck['subkey'])
//                        $subkey = $keyFdResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'];
//                        $keyFdResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id']]['subkey'] = $subkey + 1;
////                $keyFdResult[$value['section_contract_num'] . $value['section_code'] . $value['section_survey_id'] . 'plus' . ($subkey + 1)] = $value;
//                    }
//                }
//                $sectionSurveyFd = [];
////        Tong hop du lieu FD
//                foreach ($result as $key1 => $survey) {
//                    $survey = (array)$survey;
//                    $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'];
//                    if (isset($keyFdResult[$entry])) {
//                        $array_merge = array_merge($survey, $keyFdResult[$entry]);
//                        array_push($sectionSurveyFd, $array_merge);
////                if($keyFdResult[$entry]['subkey']
////                dump($keyFdResult[$entry]['subkey']);die;
////                $numSubkey = $keyFdResult[$entry]['subkey'];
////                for ($i = 1; $i <= $numSubkey; $i++) {
////                    $entry = $survey['section_contract_num'] . $survey['section_code'] . $survey['section_survey_id'] . 'plus' . $i;
////                    $array_merge = array_merge($survey, $keyFdResult[$entry]);
////                    array_push($sectionSurveyFd, $array_merge);
////                }
//                    } else {
//                        $fdArray = ['foward_id' => '',
//                            'obj_id' => '',
//                            'table_id' => '',
//                            'department_transfer' => '',
//                            'logon_user' => '',
//                            'department' => '',
//                            'reason' => '',
//                            'description' => '',
//                            'updated_at' => '',
//                            'created_at' => '',
//                            'create_date' => '',
//                            'content' => '',
//                            'status' => '',
//                            'status_id' => '',
//                            'update_by' => '',
//                            'update_date' => '',
//                            'total_minute' => '',
//                            'subkey' => 0
//                        ];
//                        $arrayEmptyFd = array_merge($survey, $fdArray);
//                        array_push($sectionSurveyFd, $arrayEmptyFd);
//                    }
//                }
//                $finalResult = $sectionSurveyFd;
////            $result = $sectionSurveyFd;
//            }
        }
//        else {
//            $finalResult=$result;
//        }


//        if (!empty($condition['recordPerPage'])) {
//            $result->take($condition['recordPerPage'])->skip($numberPage * $condition['recordPerPage']);
//        }
//        $result = $result->get();
//        dump($result);die;
        return $finalResult;
    }

    public function getSurveySectionsWithEmailTransaction($param) {
        $sql = DB::table($this->table . ' as oss')
                ->join('outbound_survey_sections_email as osse', 'oss.section_id', '=', 'osse.section_id');
        if (isset($param['sectionId'])) {
            $sql->where('oss.section_id', '=', $param['sectionId']);
        }
        if (isset($param['num_type'])) {
            $sql->where('oss.section_survey_id', '=', $param['num_type']);
        }
        if (isset($param['code'])) {
            $sql->where('oss.section_code', '=', $param['code']);
        }
        if (isset($param['shd'])) {
            $sql->where('oss.section_contract_num', '=', $param['shd']);
        }
        $result = $sql->first();
        return $result;
    }

    public function getTransaction($condition) {
        //Khởi tạo mảng dữ liệu tại quầy
        $initEmailQuantity = [
            'Số lượng giao dịch' => ['SLGDTQ' => 0],
            'Số lượng email được gửi' => ['SLGDTQ' => 0],
            'Số lượng phản hồi' => ['SLGDTQ' => 0],
            'Số lượng khách hàng vào link khảo sát' => ['SLGDTQ' => 0],
            'Tỉ lệ phản hồi' => ['SLGDTQ' => '0%']
        ];
        //Lây dữ liệu tại quầy
        $transactionEmailQuantity = DB::table(DB::raw('summary_transaction as str '
                                . 'INNER JOIN summary_time as st ON str.time_id=st.id'))
                ->select(DB::raw("
                    case
                    when str.type_info =1 then 'Số lượng giao dịch'
                    when str.type_info =2 then 'Số lượng email được gửi'
                    else str.type_info end as 'Nội dung',
                    sum(str.quantity) 'Tong so luong' "))
                ->where('st.time_temp', '>=', strtotime($condition['fromDate']))
                ->where('st.time_temp', '<=', strtotime($condition['toDate']))
                ->whereIn('str.type_info', [1, 2])
                ->where('str.type_transaction', 2)
                ->groupBy('str.type_info', 'str.type_transaction');

        $respondQuantity = DB::table(DB::raw('outbound_survey_sections as oss'))
                ->select(DB::raw("'Số lượng phản hồi' as 'Nội dung',
                                    count(*) as 'Tong so luong' "
                ))
                ->where('oss.section_time_completed_int', '>=', strtotime($condition['fromDate']))
                ->where('oss.section_time_completed_int', '<=', strtotime($condition['toDate']))
                ->where('oss.section_survey_id', 4);

        $emailRespondQuantity = $transactionEmailQuantity->union($respondQuantity)->get();
        $emailRespondQuantityEdited = [];
        foreach ($emailRespondQuantity as $key => $value) {
            $emailRespondQuantityEdited[$value->{'Nội dung'}]['SLGDTQ'] = $value->{'Tong so luong'};
        }
        $waitingCustomerQuantity = DB::table(DB::raw('summary_transaction as str '
                                . 'INNER JOIN summary_time as st ON str.time_id=st.id'))
                ->select(DB::raw("
                   sum(str.quantity) tsl "))
                ->where('st.time_temp', '>=', strtotime($condition['fromDate']))
                ->where('st.time_temp', '<=', strtotime($condition['toDate']))
                ->where('str.type_info', 3)
                ->where('str.status_id', 1)
                ->where('str.type_survey', 4)
                ->get();
        $tslWaitingQuantity = ($waitingCustomerQuantity[0]->tsl == null) ? 0 : $waitingCustomerQuantity[0]->tsl;
        $customerLinkSurveyQuantity = $tslWaitingQuantity + $emailRespondQuantityEdited['Số lượng phản hồi']['SLGDTQ'];
        $emailRespondQuantityEdited['Số lượng khách hàng vào link khảo sát']['SLGDTQ'] = $customerLinkSurveyQuantity;
        $emailRespondQuantityEdited['Tỉ lệ phản hồi']['SLGDTQ'] = ((!isset($emailRespondQuantityEdited['Số lượng email được gửi']['SLGDTQ']) || ($emailRespondQuantityEdited['Số lượng email được gửi']['SLGDTQ'] == 0 )) ? 0 : round(($emailRespondQuantityEdited['Số lượng phản hồi']['SLGDTQ'] / $emailRespondQuantityEdited['Số lượng email được gửi']['SLGDTQ']) * 100, 2)) . '%';
        foreach ($emailRespondQuantityEdited as $key => $value) {
            $initEmailQuantity[$key] = $value;
        }
        //Khởi tạo mảng dữ liệu thu cước
        $initPaymentQuantity = [
            'Số lượng giao dịch' => ['SLGDTCTN' => 0],
            'Số lượng email được gửi' => ['SLGDTCTN' => 0],
            'Số lượng phản hồi' => ['SLGDTCTN' => 0],
            'Số lượng khách hàng vào link khảo sát' => ['SLGDTCTN' => 0],
            'Tỉ lệ phản hồi' => ['SLGDTCTN' => '0%']
        ];
        //Lây dữ liệu thu cước
        $homePaymentQuantity = DB::table(DB::raw('summary_transaction as str '
                                . 'INNER JOIN summary_time as st ON str.time_id=st.id'))
                ->select(DB::raw("
                    case
                    when str.type_info =1 then 'Số lượng giao dịch'
                    when str.type_info =2 then 'Số lượng email được gửi'
                    else str.type_info end as 'Nội dung',
                    sum(str.quantity) 'Tong so luong' "))
                ->where('st.time_temp', '>=', strtotime($condition['fromDate']))
                ->where('st.time_temp', '<=', strtotime($condition['toDate']))
                ->whereIn('str.type_info', [1, 2])
                ->where('str.type_transaction', 1)
                ->groupBy('str.type_info', 'str.type_transaction');

        $respondPaymentQuantity = DB::table(DB::raw('outbound_survey_sections as oss'))
                ->select(DB::raw("'Số lượng phản hồi' as 'Nội dung',
                                    count(*) as 'Tong so luong' "
                ))
                ->where('oss.section_time_completed_int', '>=', strtotime($condition['fromDate']))
                ->where('oss.section_time_completed_int', '<=', strtotime($condition['toDate']))
                ->where('oss.section_survey_id', 7);
        $emailRespondPaymentQuantity = $homePaymentQuantity->union($respondPaymentQuantity)->get();
        $emailRespondPaymentQuantityEdited = [];
        foreach ($emailRespondPaymentQuantity as $key => $value) {
            $emailRespondPaymentQuantityEdited[$value->{'Nội dung'}]['SLGDTCTN'] = $value->{'Tong so luong'};
        }
        $waitingCustomerPaymentQuantity = DB::table(DB::raw('summary_transaction as str '
                                . 'INNER JOIN summary_time as st ON str.time_id=st.id'))
                ->select(DB::raw("
                   sum(str.quantity) tsl "))
                ->where('st.time_temp', '>=', strtotime($condition['fromDate']))
                ->where('st.time_temp', '<=', strtotime($condition['toDate']))
                ->where('str.type_info', 3)
                ->where('str.status_id', 1)
                ->where('str.type_survey', 7)
                ->get();
        $tslWaitingPaymentQuantity = ($waitingCustomerPaymentQuantity[0]->tsl == null) ? 0 : $waitingCustomerPaymentQuantity[0]->tsl;
        $cusLinkSurveyPaymentQuantity = $tslWaitingPaymentQuantity + $emailRespondPaymentQuantityEdited['Số lượng phản hồi']['SLGDTCTN'];
        $emailRespondPaymentQuantityEdited['Số lượng khách hàng vào link khảo sát']['SLGDTCTN'] = $cusLinkSurveyPaymentQuantity;
        $emailRespondPaymentQuantityEdited['Tỉ lệ phản hồi']['SLGDTCTN'] = ((!isset($emailRespondPaymentQuantityEdited['Số lượng email được gửi']['SLGDTCTN']) || $emailRespondPaymentQuantityEdited['Số lượng email được gửi']['SLGDTCTN'] == 0 ) ? 0 : round(($emailRespondPaymentQuantityEdited['Số lượng phản hồi']['SLGDTCTN'] / $emailRespondPaymentQuantityEdited['Số lượng email được gửi']['SLGDTCTN']) * 100, 2)) . '%';
        foreach ($emailRespondPaymentQuantityEdited as $key => $value) {
            $initPaymentQuantity[$key] = $value;
        }
        //Ghép dữ liệu hai loại giao dịch lại
        foreach ($initPaymentQuantity as $key => $value) {
            $initEmailQuantity[$key]['SLGDTCTN'] = $value['SLGDTCTN'];
        }
//        dump($emailRespondQuantityEdited);
//        die;
        return $initEmailQuantity;
    }

    public function getChargeStaffViolationNotProcessing($condition){
        $mainQuery = DB::table($this->table . ' as s')
            ->select("s.section_contract_num", "s.section_branch_code", "s.section_location_id","s.section_time_completed","c.email_list","b.branch_id", "b.branch_name", "b.branch_code", 'b.isc_location_id', 'b.isc_branch_code')
            ->join('outbound_survey_result as r', 's.section_id', '=', 'r.survey_result_section_id')
            ->join('summary_branches as b', function ($join) {
                $join->on('s.section_location_id', '=', 'b.isc_location_id');
                $join->on('s.section_branch_code', '=', 'b.isc_branch_code');
            })
            ->join('list_mail_cus as c', 'b.branch_id', '=', 'c.summary_branches_id')
            ->where(function($query) use ($condition) {
                if (!empty($condition['surveyFromInt']) && !empty($condition['surveyToInt'])) {
                    $query->where('s.section_time_completed_int', '>=', $condition['surveyFromInt']);
                    $query->where('s.section_time_completed_int', '<=', $condition['surveyToInt']);
                }
            })
            ->whereIn('r.survey_result_answer_id', [1,2])
            ->whereIn('r.survey_result_question_id', [35])
            ->where(function($query){
                $query->whereNULL('s.violation_status');
                $query->orWhere('s.violation_status', 'like', '%chargeStaff":null%');
            })
            ->orderByRaw('b.isc_location_id, b.isc_branch_code')
        ;
        $result = $mainQuery->get();
        return $result;
    }

    public function countChargeStaffViolationNotProcessing($condition){
        $mainQuery = DB::table($this->table . ' as s')
            ->select("s.section_contract_num")
            ->join('outbound_survey_result as r', 's.section_id', '=', 'r.survey_result_section_id')
            ->join('summary_branches as b', function ($join) {
                $join->on('s.section_location_id', '=', 'b.isc_location_id');
                $join->on('s.section_branch_code', '=', 'b.isc_branch_code');
            })
            ->where(function($query) use ($condition) {
                if (!empty($condition['surveyFromInt']) && !empty($condition['surveyToInt'])) {
                    $query->where('s.section_time_completed_int', '>=', $condition['surveyFromInt']);
                    $query->where('s.section_time_completed_int', '<=', $condition['surveyToInt']);
                }
            })
            ->whereIn('r.survey_result_answer_id', [1,2])
            ->whereIn('r.survey_result_question_id', [35])
            ->where(function($query) use ($condition) {
                $query->whereNULL('s.violation_status');
                $query->orWhere('s.violation_status', 'like', '%chargeStaff":null%');
            })
        ;
        $result = $mainQuery->count();
        return $result;
    }

    public function getCSATByBranch($from_date, $to_date, $locationID) {
        $resultCsat = DB::table('outbound_survey_sections AS oss')
            ->join('outbound_survey_result AS osr', 'osr.survey_result_section_id', '=', 'oss.section_id')
            ->select(DB::raw("oss.section_location 'Location',
                                sum(if(osr.survey_result_answer_id =1 and osr.survey_result_question_id in (5, 9), 1, 0))   'VeryUnsatisfactionNet',
                                sum(if(osr.survey_result_answer_id =1 and osr.survey_result_question_id = 1, 1, 0))  'VeryUnsatisfactionSaleMan',
                                sum(if(osr.survey_result_answer_id =1 and osr.survey_result_question_id in (2, 6), 1, 0)) 'VeryUnsatisfactionSir',
                                
                                sum(if(osr.survey_result_answer_id =2 and osr.survey_result_question_id in (5, 9), 1, 0))   'UnsatisfactionNet',
                                sum(if(osr.survey_result_answer_id =2 and osr.survey_result_question_id = 1, 1, 0))  'UnsatisfactionSaleMan',
                                sum(if(osr.survey_result_answer_id =2 and osr.survey_result_question_id in (2, 6), 1, 0)) 'UnsatisfactionSir',
                                
                                sum(if(osr.survey_result_answer_id =3 and osr.survey_result_question_id in (5, 9), 1, 0))   'NeutralNet',
                                sum(if(osr.survey_result_answer_id =3 and osr.survey_result_question_id = 1, 1, 0))  'NeutralSaleMan',
                                sum(if(osr.survey_result_answer_id =3 and osr.survey_result_question_id in (2, 6), 1, 0)) 'NeutralSir',
                                
                                sum(if(osr.survey_result_answer_id =4 and osr.survey_result_question_id in (5, 9), 1, 0))   'SatisfactionNet',
                                sum(if(osr.survey_result_answer_id =4 and osr.survey_result_question_id = 1, 1, 0))  'SatisfactionSaleMan',
                                sum(if(osr.survey_result_answer_id =4 and osr.survey_result_question_id in (2, 6), 1, 0)) 'SatisfactionSir',
                                
                                sum(if(osr.survey_result_answer_id =5 and osr.survey_result_question_id in (5, 9), 1, 0))   'VerySatisfactionNet',
                                sum(if(osr.survey_result_answer_id =5 and osr.survey_result_question_id = 1, 1, 0))  'VerySatisfactionSaleMan',
                                sum(if(osr.survey_result_answer_id =5 and osr.survey_result_question_id in (2, 6), 1, 0)) 'VerySatisfactionSir'

"))
//                ->where('s.section_sub_parent_desc,!=,')
//                ->Where('s.section_sub_parent_desc,!=,Mien Nam')
//            ->where('oss.section_survey_id', 11)
            ->whereIn('osr.survey_result_question_id', [5, 9, 1, 2, 6])
            ->where('osr.survey_result_answer_id', '<>', -1)
//            ->where(function($query) use ($region) {
//                if (!empty($region)) {
//                    $region = explode(',', $region);
//                    foreach ($region as $reg) {
//                        $query->orWhere('oss.section_sub_parent_desc', '=', "Vung $reg");
//                    }
//                }
//            })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('oss.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('oss.section_time_completed_int', '<=', strtotime($to_date));
                }
            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('oss.section_location_id', $locationID);
                }
            })
//            ->where(function($query) use ($branch) {
//                if (count($branch) > 0) {
//                    foreach ($branch as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('oss.section_location_id', $b);
//                        }
//                    }
//                }
//            })
//            ->where(function($query) use ($branchcode) {
//                if (count($branchcode) > 0) {
//                    foreach ($branchcode as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('oss.section_branch_code', $b);
//                        }
//                    }
//                }
//            })
            ->groupBy('oss.section_location_id');

        $resultTQCsat = DB::table('outbound_survey_sections AS oss')
            ->join('outbound_survey_result AS osr', 'osr.survey_result_section_id', '=', 'oss.section_id')
            ->select(DB::raw("'WholeCountry' as 'Location',
                                sum(if(osr.survey_result_answer_id =1 and osr.survey_result_question_id in (5, 9), 1, 0))   'VeryUnsatisfactionNet',
                                sum(if(osr.survey_result_answer_id =1 and osr.survey_result_question_id = 1, 1, 0))  'VeryUnsatisfactionSaleMan',
                                sum(if(osr.survey_result_answer_id =1 and osr.survey_result_question_id in (2, 6), 1, 0)) 'VeryUnsatisfactionSir',
                                
                                sum(if(osr.survey_result_answer_id =2 and osr.survey_result_question_id in (5, 9), 1, 0))   'UnsatisfactionNet',
                                sum(if(osr.survey_result_answer_id =2 and osr.survey_result_question_id = 1, 1, 0))  'UnsatisfactionSaleMan',
                                sum(if(osr.survey_result_answer_id =2 and osr.survey_result_question_id in (2, 6), 1, 0)) 'UnsatisfactionSir',
                                
                                sum(if(osr.survey_result_answer_id =3 and osr.survey_result_question_id in (5, 9), 1, 0))   'NeutralNet',
                                sum(if(osr.survey_result_answer_id =3 and osr.survey_result_question_id = 1, 1, 0))  'NeutralSaleMan',
                                sum(if(osr.survey_result_answer_id =3 and osr.survey_result_question_id in (2, 6), 1, 0)) 'NeutralSir',
                                
                                sum(if(osr.survey_result_answer_id =4 and osr.survey_result_question_id in (5, 9), 1, 0))   'SatisfactionNet',
                                sum(if(osr.survey_result_answer_id =4 and osr.survey_result_question_id = 1, 1, 0))  'SatisfactionSaleMan',
                                sum(if(osr.survey_result_answer_id =4 and osr.survey_result_question_id in (2, 6), 1, 0)) 'SatisfactionSir',
                                
                                sum(if(osr.survey_result_answer_id =5 and osr.survey_result_question_id in (5, 9), 1, 0))   'VerySatisfactionNet',
                                sum(if(osr.survey_result_answer_id =5 and osr.survey_result_question_id = 1, 1, 0))  'VerySatisfactionSaleMan',
                                sum(if(osr.survey_result_answer_id =5 and osr.survey_result_question_id in (2, 6), 1, 0)) 'VerySatisfactionSir'

"))
//                ->where('s.section_sub_parent_desc,!=,')
//                ->Where('s.section_sub_parent_desc,!=,Mien Nam')
//            ->where('oss.section_survey_id', 11)
            ->whereIn('osr.survey_result_question_id', [5, 9, 1, 2, 6])
            ->where('osr.survey_result_answer_id', '<>', -1)
//            ->where(function($query) use ($region) {
//                if (!empty($region)) {
//                    $region = explode(',', $region);
//                    foreach ($region as $reg) {
//                        $query->orWhere('oss.section_sub_parent_desc', '=', "Vung $reg");
//                    }
//                }
//            })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('oss.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('oss.section_time_completed_int', '<=', strtotime($to_date));
                }
            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('oss.section_location_id', $locationID);
                }
            })
            ->union($resultCsat)->get();
//        dump($resultTQCsat);die;
        return $resultTQCsat;
//             return   $result;
    }

    public function getCsatErrorActionCsat12($from_date, $to_date, $locationID)
    {
        $resultCsatError = DB::table('outbound_survey_sections AS oss')
            ->join('outbound_survey_result AS osr', 'osr.survey_result_section_id', '=', 'oss.section_id')
            ->select(DB::raw("oss.section_location 'Location',
                            sum(if(osr.survey_result_error = 85, 1, 0)) 'InternetIsNotStable',
                            sum(if(osr.survey_result_error = 87, 1, 0)) 'EquipmentError',
                            sum(if(osr.survey_result_error = 88, 1, 0)) 'VoiceError',
                            sum(if(osr.survey_result_error = 89, 1, 0)) 'WifiWeakNotStable',
                            sum(if(osr.survey_result_error = 90, 1, 0)) 'GameLagging',
                            sum(if(osr.survey_result_error = 91, 1, 0)) 'CannotUsingWifi',
                            sum(if(osr.survey_result_error = 92, 1, 0)) 'LoosingSignal',
                            sum(if(osr.survey_result_error = 94, 1, 0)) 'HaveSignalButCannotAccess',
                            sum(if(osr.survey_result_error = 97, 1, 0)) 'SlowInternet',
                            sum(if(osr.survey_result_error = 98, 1, 0)) 'SignalIsNotStableSignalLoosingIsUnderStandard',
                            sum(if(osr.survey_result_error = 99, 1, 0)) 'IntenationalInternetSlow',
                            sum(if(osr.survey_result_error = 100, 1, 0)) 'OtherError',
                            sum(if(osr.survey_result_error in  (85, 87, 88, 89, 90, 91, 92, 94, 97, 98, 99, 100), 1, 0)) 'TotalError'
"))
//                ->where('s.section_sub_parent_desc,!=,')
//                ->Where('s.section_sub_parent_desc,!=,Mien Nam')
//            ->where('oss.section_survey_id', 11)
            ->whereIn('osr.survey_result_question_id', [5, 9])
            ->whereIn('osr.survey_result_answer_id', [1, 2])
//            ->where(function($query) use ($region) {
//                if (!empty($region)) {
//                    $region = explode(',', $region);
//                    foreach ($region as $reg) {
//                        $query->orWhere('oss.section_sub_parent_desc', '=', "Vung $reg");
//                    }
//                }
//            })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('oss.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('oss.section_time_completed_int', '<=', strtotime($to_date));
                }
            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('oss.section_location_id', $locationID);
                }
            })
//            ->where(function($query) use ($branch) {
//                if (count($branch) > 0) {
//                    foreach ($branch as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('oss.section_location_id', $b);
//                        }
//                    }
//                }
//            })
//            ->where(function($query) use ($branchcode) {
//                if (count($branchcode) > 0) {
//                    foreach ($branchcode as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('oss.section_branch_code', $b);
//                        }
//                    }
//                }
//            })
            ->groupBy('oss.section_location_id');

        $resultTQCsatError = DB::table('outbound_survey_sections AS oss')
            ->join('outbound_survey_result AS osr', 'osr.survey_result_section_id', '=', 'oss.section_id')
            ->select(DB::raw("'WholeCountry' as 'Location',
                            sum(if(osr.survey_result_error = 85, 1, 0)) 'InternetIsNotStable',
                            sum(if(osr.survey_result_error = 87, 1, 0)) 'EquipmentError',
                            sum(if(osr.survey_result_error = 88, 1, 0)) 'VoiceError',
                            sum(if(osr.survey_result_error = 89, 1, 0)) 'WifiWeakNotStable',
                            sum(if(osr.survey_result_error = 90, 1, 0)) 'GameLagging',
                            sum(if(osr.survey_result_error = 91, 1, 0)) 'CannotUsingWifi',
                            sum(if(osr.survey_result_error = 92, 1, 0)) 'LoosingSignal',
                            sum(if(osr.survey_result_error = 94, 1, 0)) 'HaveSignalButCannotAccess',
                            sum(if(osr.survey_result_error = 97, 1, 0)) 'SlowInternet',
                            sum(if(osr.survey_result_error = 98, 1, 0)) 'SignalIsNotStableSignalLoosingIsUnderStandard',
                            sum(if(osr.survey_result_error = 99, 1, 0)) 'IntenationalInternetSlow',
                            sum(if(osr.survey_result_error = 100, 1, 0)) 'OtherError',
                            sum(if(osr.survey_result_error in  (85, 87, 88, 89, 90, 91, 92, 94, 97, 98, 99, 100), 1, 0)) 'TotalError'
"))
//                ->where('s.section_sub_parent_desc,!=,')
//                ->Where('s.section_sub_parent_desc,!=,Mien Nam')
//            ->where('oss.section_survey_id', 11)
            ->whereIn('osr.survey_result_question_id', [5, 9])
            ->whereIn('osr.survey_result_answer_id', [1, 2])
//            ->where(function($query) use ($region) {
//                if (!empty($region)) {
//                    $region = explode(',', $region);
//                    foreach ($region as $reg) {
//                        $query->orWhere('oss.section_sub_parent_desc', '=', "Vung $reg");
//                    }
//                }
//            })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('oss.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('oss.section_time_completed_int', '<=', strtotime($to_date));
                }
            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('oss.section_location_id', $locationID);
                }
            })
            ->union($resultCsatError)->get();

        $resultCsatAction = DB::table('outbound_survey_sections AS oss')
            ->join('outbound_survey_result AS osr', 'osr.survey_result_section_id', '=', 'oss.section_id')
            ->select(DB::raw("oss.section_location 'Location',
                    sum(if(osr.survey_result_action = 115, 1, 0)) 'SorryCustomerAndClose',
                    sum(if(osr.survey_result_action = 116, 1, 0)) 'ForwardDepartment',
                    sum(if(osr.survey_result_action = 117, 1, 0)) 'CreatePrechecklist',
                    sum(if(osr.survey_result_action = 118, 1, 0)) 'CreateChecklist',
                    sum(if(osr.survey_result_action = 119, 1, 0)) 'CreateCLIndo',
                    sum(if(osr.survey_result_action = 128, 1, 0)) 'OtherAction',
                    sum(if(osr.survey_result_action in  (115, 116, 117, 118, 119, 128), 1, 0)) 'TotalAction'
"))
//                ->where('s.section_sub_parent_desc,!=,')
//                ->Where('s.section_sub_parent_desc,!=,Mien Nam')
//            ->where('oss.section_survey_id', 11)
            ->whereIn('osr.survey_result_question_id', [5, 9])
            ->whereIn('osr.survey_result_answer_id', [1, 2])
//            ->where(function($query) use ($region) {
//                if (!empty($region)) {
//                    $region = explode(',', $region);
//                    foreach ($region as $reg) {
//                        $query->orWhere('oss.section_sub_parent_desc', '=', "Vung $reg");
//                    }
//                }
//            })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('oss.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('oss.section_time_completed_int', '<=', strtotime($to_date));
                }
            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('oss.section_location_id', $locationID);
                }
            })
//            ->where(function($query) use ($branch) {
//                if (count($branch) > 0) {
//                    foreach ($branch as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('oss.section_location_id', $b);
//                        }
//                    }
//                }
//            })
//            ->where(function($query) use ($branchcode) {
//                if (count($branchcode) > 0) {
//                    foreach ($branchcode as $b) {
//                        if (!empty($b)) {
//                            $b = explode(',', $b);
//                            $query->whereIn('oss.section_branch_code', $b);
//                        }
//                    }
//                }
//            })
            ->groupBy('oss.section_location_id');

        $resultTQCsatAction = DB::table('outbound_survey_sections AS oss')
            ->join('outbound_survey_result AS osr', 'osr.survey_result_section_id', '=', 'oss.section_id')
            ->select(DB::raw("'WholeCountry' as 'Location',
                     sum(if(osr.survey_result_action = 115, 1, 0)) 'SorryCustomerAndClose',
                    sum(if(osr.survey_result_action = 116, 1, 0)) 'ForwardDepartment',
                    sum(if(osr.survey_result_action = 117, 1, 0)) 'CreatePrechecklist',
                    sum(if(osr.survey_result_action = 118, 1, 0)) 'CreateChecklist',
                    sum(if(osr.survey_result_action = 119, 1, 0)) 'CreateCLIndo',
                    sum(if(osr.survey_result_action = 128, 1, 0)) 'OtherAction',
                    sum(if(osr.survey_result_action in  (115, 116, 117, 118, 119, 128), 1, 0)) 'TotalAction'
"))
//                ->where('s.section_sub_parent_desc,!=,')
//                ->Where('s.section_sub_parent_desc,!=,Mien Nam')
//            ->where('oss.section_survey_id', 11)
            ->whereIn('osr.survey_result_question_id', [5, 9])
            ->whereIn('osr.survey_result_answer_id', [1, 2])
//            ->where(function($query) use ($region) {
//                if (!empty($region)) {
//                    $region = explode(',', $region);
//                    foreach ($region as $reg) {
//                        $query->orWhere('oss.section_sub_parent_desc', '=', "Vung $reg");
//                    }
//                }
//            })
            ->where(function($query) use ($from_date, $to_date) {
                if (!empty($from_date) && !empty($to_date)) {
                    $query->where('oss.section_time_completed_int', '>=', strtotime($from_date));
                    $query->where('oss.section_time_completed_int', '<=', strtotime($to_date));
                }
            })
            ->where(function($query) use ($locationID) {
                if (!empty($locationID)) {
                    $query->whereIn('oss.section_location_id', $locationID);
                }
            })
            ->union($resultCsatAction)->get();

//        dump($resultTQCsat);die;
        return ['csatAction' => $resultTQCsatAction, 'csatError' => $resultTQCsatError, 'allLocation' => count($resultTQCsatAction) >= count($resultTQCsatError) ? $resultTQCsatAction : $resultTQCsatError];
    }

    public function getNpsByTimeFromSurvey($fromDay, $toDay) {
        $result = DB::table('outbound_survey_sections as oss')
            ->join('outbound_survey_result as osr', 'oss.section_id', '=', 'osr.survey_result_section_id')
            ->select(DB::raw('((sum(if(osr.survey_result_answer_id in (149, 150), 1, 0)) - sum(if(osr.survey_result_answer_id in (140, 141, 142, 143, 144, 145, 146), 1, 0)) ) /
(sum(if(osr.survey_result_answer_id in (140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150), 1, 0)))) * 100 as "Percent"
                        '))
//                ->from(DB::raw("outbound_survey_sections os join outbound_survey_result osr on
//                            os.section_id=osr.survey_result_section_id"))
            ->where('oss.section_time_completed_int', '>=', strtotime($fromDay))
            ->where('oss.section_time_completed_int', '<=', strtotime($toDay))
            ->whereIn('osr.survey_result_question_id', [10, 11])
            ->where('osr.survey_result_answer_id', '<>', -1)
//                ->groupBy(DB::raw('sc.poc_id, sc.object_id'))
            ->get();
        return $result[0]->Percent;
    }

    public function getSurveyInfoByID($sectionID)
    {
        $result = DB::table('outbound_survey_sections as os')->select('os.section_contact_phone', 'os.section_time_completed', 'os.section_time_start')
            ->where('os.section_id', $sectionID)
            ->get();
        return $result;
    }
}
