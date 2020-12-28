<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Component\HelpProvider;
use App\Http\Controllers\Controller;
use App\Models\SurveySections;
use App\models\Surveys;
use App\models\SurveyResult;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Apiisc;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Models\OutboundAccount;
use App\Models\ContactProfile;
use App\Models\SurveySectionsEmail;
use App\Models\ApiTransactionLog;
use App\Models\OutboundQuestions;

class ApiTransactionController extends Controller {

    //Lấy thông tin giao dịch, hợp đồng này có NPS hay không, trả về màn hình khảo sát của FPT.vn
    public function getInfoContractQGD(Request $request) {
        try {
            $data = $request->input();

            //Giả định mảng tham số bắt buộc phải gửi qua
            $arrayValidate = ['ContractId', 'TransactionCode', 'Type', 'SecureCode'];
            $arrayError = [];
            foreach ($arrayValidate as $key => $value) {
                //Dữ liệu gửi qua không có hoặc có nhưng bằng rỗng
                if (!isset($data[$value]) || $data[$value] == '')
                    array_push($arrayError, $value);
//                $error.=$value . ', ';
            }

            //Đầy đủ dữ liệu , không có lỗi, SecureCode hợp lệ
            if (empty($arrayError)) {
                $authorizeValidate = md5($data['ContractId'] . $data['TransactionCode'] . $data['Type'] . 'fptvn&survey');
                if ($authorizeValidate == false || $authorizeValidate != $data['SecureCode']) {
                    if ($authorizeValidate == false) {
                        $message = 'Qua trinh tinh toan SecureCode bi loi';
                    } else {
                        $message = 'Mã SecureCode không đúng';
                    }

                    $dataApi = [
                        'id' => 'fail',
                        'status' => '503',
                        'detail' => $message,
                    ];
                    $status = 500;
                    return response()->json($dataApi, $status);
                }
                //Gọi Api ISC
                $api = new Apiisc();
                $arraySentToISC = array(
                    'contractId' => $data['ContractId'],
                    'transactionId' => $data['TransactionCode'],
                    'key' => $data['Type'],
                );

//                $arraySentToISC = array(
//                    'contractId' => 1003280733,
//                    'transactionId' => 1570832,
//                    'key' => 1
//                );

                $timeStartCall = date('Y-m-d H:i:s');
                $resultReturn = $api->GetInforContractQGDApi($arraySentToISC);
                $timeEndCall = date('Y-m-d H:i:s');
                // Ghi log gọi API ISC
                $logger = new Logger('my_logger');
                $logger->pushHandler(new StreamHandler(storage_path() . '/logs/API_ISC_QGD.log', Logger::INFO));
//                $logger->pushHandler(new FirePHPHandler());
                $logger->addInfo('Log Call API', array('TimeStartCall' => $timeStartCall, 'TimeEndCall' => $timeEndCall, 'input' => $arraySentToISC, 'output' => $resultReturn));

                //Gọi qua ISC thất bại
                if ($resultReturn['success'] == false) {
                    $dataApi = [
                        'id' => 'fail',
                        'status' => '500',
                        'detail' => $resultReturn['result'],
                    ];
                    $status = 500;
                    return response()->json($dataApi, $status);
                }
                $returnDataIsc = json_decode($resultReturn['result'])->data;
                $data['TransactionInfo'] = $returnDataIsc;
//                var_dump($data['TransactionInfo']);die;
                unset($data['ContractNum']);
                unset($data['SecureCode']);
                $dataApi = [
                    'id' => 'success',
                    'status' => '200',
                    'detail' => ($data),
                    'ques_ans' => $this->getQuesAns($data['Type']),
                    'nps' => $this->checkNPS($data['TransactionInfo']->ContractNumber),
                ];
                $status = 200;

                return response()->json($dataApi, $status);
            } else {
                $dataApi = [
                    'id' => 'fail',
                    'status' => '503',
                    'detail' => 'Truong ' . implode(',', $arrayError) . ' bi thieu hoac khong co du lieu',
                ];
                $status = 500;
                return response()->json($dataApi, $status);
            }
        } catch (Exception $e) {
            $dataApi = [
                'id' => 'fail',
                'status' => '500',
                'detail' => $e->getMessage(),
            ];
            $status = 500;
            return response()->json($dataApi, $status);
        }
    }

    //Lưu thông tin khảo sát và giao dịch đẩy qua từ FPT.vn
    public function saveInfoTransaction(Request $request) {
        try {
            $allData = $request->input();
            $input = json_encode($allData['data']);
            //Lưu log gọi api
            $source = 'ApiTransactionController/saveInfoTransaction';
            $apiLog = new ApiTransactionLog();
            $apiLog->survey_id = isset($data['contract']['Type']) ? ($data['contract']['Type'] == 4 ? 7 : 4) : null;
            $apiLog->source = $source;
            $apiLog->input = $input;
            $apiLog->save();
            $modelOutboundAccount = new OutboundAccount();
            $messageValidatePerTransaction = [];
            $messageErrorUpdate = [];
            $messageSuccessUpdate = [];
            if (!isset($allData['data']) || $allData['data'] == '') {
                $dataApi = [
                    'id' => 'fail',
                    'status' => '503',
                    'detail' => 'Thiếu data đầu vào',
                ];
                $status = 500;
                return response()->json($dataApi, $status);
            }
            foreach ($allData['data'] as $key => $data) {
//                $flagSuccess = false;
//                if (empty($data) || !isset($data['ques_ans']) || empty($data['ques_ans']) || !isset($data['contract']) || empty($data['contract'])) {
//                    $dataApi = [
//                        'id' => 'fail',
//                        'status' => '503',
//                        'detail' => 'Du lieu gui qua khong du',
//                    ];
//                    $status = 500;
//                    return response()->json($dataApi, $status);
//                } 
                if (!empty($data) && isset($data['ques_ans']) && !empty($data['ques_ans']) && isset($data['contract']) && !empty($data['contract'])) {
                    $validateArray = ['questionID', 'answerID', 'note'];
                    $ques_ans = $data['ques_ans'];
                    $arrayError = [];
                    foreach ($ques_ans as $key2 => $value2) {
                        foreach ($validateArray as $key3 => $value3) {
                            //Dữ liệu gửi qua không có hoặc có nhưng bằng rỗng
                            if ((!isset($value2[$value3]) || $value2[$value3] == '')) {
                                if (!in_array($value3, $arrayError))
                                    array_push($arrayError, $value3);
                            }
                        }
                    }
                    //Validate thành công
                    if (empty($arrayError)) {
                        DB::beginTransaction();
                        $surveySection = new SurveySections();
                        $dataQGD = $data['contract'];
                        $dataTransactionInfo = $dataQGD['TransactionInfo'];
                        $resultCodes = $surveySection->checkExistCodes($dataQGD['TransactionCode'], 4, $dataTransactionInfo['ContractNumber']);
                        //Đã lưu thông tin rồi thì cập nhập                      
                        if (!empty($resultCodes)) {
                            //Xóa dữ liệu cũ
                            DB::table('outbound_survey_result')->where('survey_result_section_id', '=', $resultCodes[0]->section_id)->delete();
                            foreach ($ques_ans as $key => $value) {
                                $surveyResult = new SurveyResult();
                                $surveyResult->survey_result_section_id = $resultCodes[0]->section_id;
                                $surveyResult->survey_result_question_id = $value['questionID'];
                                $surveyResult->survey_result_answer_id = $value['answerID'];
                                $surveyResult->survey_result_note = $value['note'];
                                //Rất ko hài lòng, ko hài lòng
                                // if ($value['answerID'] == 1 || $value['answerID'] == 2) {
                                //     $surveyResult->survey_result_answer_extra_id = isset($value['answerExtraID']) ? $value['answerExtraID'] : null;
                                //     $surveyResult->survey_result_note = isset($value['note']) ? $value['note'] : null;
                                //  }
                                //Lưu thất bại chi tiết khảo sát giao dịch đó
                                if (!$surveyResult->save()) {
//                                    array_push($arrayErrorUpdate, ['ContractNum' => $dataQGD['ContractNum'], 'TransactionCode' => $dataQGD['TransactionCode'], 'SecureCode' => $dataQGD['SecureCode']]);
                                    array_push($messageErrorUpdate, ['ContractNum' => $dataTransactionInfo['ContractNumber'], 'TransactionCode' => $dataQGD['TransactionCode']]);
//                                            $messageErrorUpdate.='Bộ dữ liệu khảo sát có ContractNum: ' . $dataQGD['ContractNum'] . ',TransactionCode:' . $dataQGD['TransactionCode'] . ', SecureCode:' . $dataQGD['SecureCode'] . ' cập nhập thất bại; ';
//                                    $flagError = true;
                                    DB::rollback();
                                    break;
                                }
                            }
                            array_push($messageSuccessUpdate, ['ContractNum' => $dataTransactionInfo['ContractNumber'], 'TransactionCode' => $dataQGD['TransactionCode']]);
                            DB::commit();
                            continue;
                        }
//                            $dataApi = [
//                                'id' => 'fail',
//                                'status' => '503',
//                                'detail' => 'Khảo sát '.$dataTransactionInfo['ContractNumber'].'/4/'.$dataQGD['TransactionCode'].' đã lưu thông tin',
//                            ];
//                            $status = 500;
//                            return response()->json($dataApi, $status);
//                            exit;
//                        }
                        $flagSuccess = true;


                        //Insert dư lieu isc tra ve vao survey_section
//                        $surveySection->section_contract_id = $dataQGD['ContractNum'];
                        $surveySection->section_code = $dataQGD['TransactionCode'];

                        $surveySection->section_contract_num = $dataTransactionInfo['ContractNumber'];
                        $surveySection->section_customer_name = $dataTransactionInfo['CustomerName'];
                        $surveySection->section_survey_id = $dataQGD['Type'] == 4 ? 7 : 4;
                        $surveySection->section_record_channel = 2;
                        $surveySection->sale_center_id = 3;
                        $surveySection->section_phone = $dataTransactionInfo['Phone'];
                        //$surveySection->section_note = $dataQGD['note'];
                        $surveySection->section_objAddress = $dataTransactionInfo['Address'];
                        $surveySection->section_sub_parent_desc = $dataTransactionInfo['SubParentDesc'];
                        $surveySection->section_location = $dataTransactionInfo['ChiNhanh'];
                        $surveySection->section_fee_local_type = $dataTransactionInfo['ContractTypeName'];
                        $surveySection->section_time_start = $dataQGD['SectionTimeStart'];
                        $surveySection->section_time_completed = $dataQGD['SectionTimeCompleted'];
                        $surveySection->section_time_completed_int = strtotime(date('Y-m-d H:i:s'));
                        $surveySection->section_connected = 4;
                        $surveySection->section_action = 1;
                        $surveySection->section_region = $dataTransactionInfo['Region'];
                        $surveySection->section_location_id = $dataTransactionInfo['LocationID'];
                        $surveySection->section_branch_code = $dataTransactionInfo['BranchCode'];
                        $surveySection->section_package_sal = $dataTransactionInfo['PackageSal'];
                        $surveySection->section_payment_type = $dataTransactionInfo['PaymentType'];
                        $surveySection->section_account_payment = $dataTransactionInfo['AccountPayment'];
                        $surveySection->section_use_service = $dataTransactionInfo['UseService'];
                        $accountInfo = $modelOutboundAccount->getAccountInfoByContract($dataTransactionInfo['ContractNumber']);
                        $surveySection->section_account_id = ($accountInfo == NULL) ? 0 : $accountInfo->id;
                        //Lưu thành công thông tin giao dịch
                        if ($surveySection->save()) {
                            $idDetail = $surveySection->section_id;
                            foreach ($ques_ans as $key => $value) {
                                $surveyResult = new SurveyResult();
                                $surveyResult->survey_result_section_id = $idDetail;
                                $surveyResult->survey_result_question_id = $value['questionID'];
                                $surveyResult->survey_result_answer_id = $value['answerID'];
                                $surveyResult->survey_result_note = $value['note'];
                                //Rất ko hài lòng, ko hài lòng
                                // if ($value['answerID'] == 1 || $value['answerID'] == 2) {
                                //     $surveyResult->survey_result_answer_extra_id = isset($value['answerExtraID']) ? $value['answerExtraID'] : null;
                                //     $surveyResult->survey_result_note = isset($value['note']) ? $value['note'] : null;
                                //  }
                                //Lưu thất bại chi tiết khảo sát giao dịch đó
                                if (!$surveyResult->save()) {
//                                    array_push($arrayErrorUpdate, ['ContractNum' => $dataQGD['ContractNum'], 'TransactionCode' => $dataQGD['TransactionCode'], 'SecureCode' => $dataQGD['SecureCode']]);
                                    array_push($messageErrorUpdate, ['ContractNum' => $dataTransactionInfo['ContractNumber'], 'TransactionCode' => $dataQGD['TransactionCode']]);
//                                            $messageErrorUpdate.='Bộ dữ liệu khảo sát có ContractNum: ' . $dataQGD['ContractNum'] . ',TransactionCode:' . $dataQGD['TransactionCode'] . ', SecureCode:' . $dataQGD['SecureCode'] . ' cập nhập thất bại; ';
//                                    $flagError = true;
                                    DB::rollback();
                                    break;
                                }
                            }
                            $surveySectionEmail = new SurveySectionsEmail();
                            $surveySectionEmail->section_id = $idDetail;
                            $surveySectionEmail->section_time_start_transaction = $dataTransactionInfo['ThoiGianGiaoDich'];
                            $surveySectionEmail->section_user_create_transaction = $dataTransactionInfo['NguoiTaoGD'];
                            if ($surveySectionEmail->save()) {
                                //Lưu thành công cả 2 bảng
                                array_push($messageSuccessUpdate, ['ContractNum' => $dataTransactionInfo['ContractNumber'], 'TransactionCode' => $dataQGD['TransactionCode']]);

//                            $messageSuccessUpdate.='Bộ dữ liệu khảo sát có ContractNum: ' . $dataQGD['ContractNum'] . ',TransactionCode:' . $dataQGD['TransactionCode'] . ', SecureCode:' . $dataQGD['SecureCode'] . ' cập nhập thành công; ';
                                DB::commit();
                            } else {
                                array_push($messageErrorUpdate, ['ContractNum' => $dataTransactionInfo['ContractNumber'], 'TransactionCode' => $dataQGD['TransactionCode']]);
//                                            $messageErrorUpdate.='Bộ dữ liệu khảo sát có ContractNum: ' . $dataQGD['ContractNum'] . ',TransactionCode:' . $dataQGD['TransactionCode'] . ', SecureCode:' . $dataQGD['SecureCode'] . ' cập nhập thất bại; ';
//                                    $flagError = true;
                                DB::rollback();
                                break;
                            }
                        } else {
//                            array_push($arrayErrorUpdate, ['ContractNum' => $dataQGD['ContractNum'], 'TransactionCode' => $dataQGD['TransactionCode'], 'SecureCode' => $dataQGD['SecureCode']]);
//                            $flagError = true;
                            array_push($messageErrorUpdate, ['ContractNum' => $dataTransactionInfo['ContractNumber'], 'TransactionCode' => $dataQGD['TransactionCode']]);

//                            $messageErrorUpdate.='Bộ dữ liệu khảo sát có ContractNum: ' . $dataQGD['ContractNum'] . ',TransactionCode:' . $dataQGD['TransactionCode'] . ', SecureCode:' . $dataQGD['SecureCode'] . ' cập nhập thất bại; ';
                            DB::rollback();
                            break;
                        }
                    } else {
                        (isset($dataTransactionInfo['ContractNumber']) || isset($data['contract']['TransactionCode'])) ?
                                        array_push($messageValidatePerTransaction, ['ContractNum' => isset($dataTransactionInfo['ContractNumber']) ? $dataTransactionInfo['ContractNumber'] : '', 'TransactionCode' => isset($data['contract']['TransactionCode']) ? $data['contract']['TransactionCode'] : ''
                                        ]) :
                                        array_push($messageValidatePerTransaction, 'Missing data ' . implode(',', $arrayError));
//                        array_push($messageValidatePerTransaction, 'Missing data ' . implode(',', $arrayError));
//                        $messageValidatePerTransaction.= 'Missing data ' . implode(',', $arrayError);
                    }
//                    else {
//                    DB::rollback();
//                    $dataApi = [
//                        'id' => 'fail',
//                        'status' => '503',
//                        'detail' => 'Trường ' . implode(',', $arrayError) . ' bi thieu hoac khong co du lieu',
//                    ];
//                    $status = 500;
//                    return response()->json($dataApi, $status);
//                    }
                } else {
                    (isset($dataTransactionInfo['ContractNumber']) || isset($data['contract']['TransactionCode'])) ?
                                    array_push($messageValidatePerTransaction, ['ContractNum' => isset($dataTransactionInfo['ContractNumber']) ? $dataTransactionInfo['ContractNumber'] : '', 'TransactionCode' => isset($data['contract']['TransactionCode']) ? $data['contract']['TransactionCode'] : ''
                                    ]) :
                                    array_push($messageValidatePerTransaction, 'Missing ques_ans, contract  data ');

//                    $messageValidatePerTransaction.= 'Missing ques_ans, contract  data ';
                }
            }
//            if (empty($arrayValidatePerTransaction)) {
//
//                if (empty($arrayErrorUpdate)) {
//                    $message = 'Cap nhap du lieu thanh cong',
//                } else
//                    $message = 'Quá trình cập nhập bộ dũ liệu thứ'. implode (',', $arrayError) . ' bi lỗi. Vui lòng gửi lại.',
//                $dataApi = [
//                    'id' => 'success',
//                    'status' => '200',
//                    'detail' => $message
//                ];
//                $status = 200;
//                return response()->json($dataApi, $status);
//            } else 
//                {
            $dataApi = [
                'id' => 'success',
                'status' => '200',
                'detail' => ['ErrorRecord:' => ($messageValidatePerTransaction != '') ? $messageValidatePerTransaction : 'None',
                    'SucessRecord' => ($messageSuccessUpdate != '') ? $messageSuccessUpdate : 'None',
                    'FailRecord' => ($messageErrorUpdate != '') ? $messageErrorUpdate : 'None',
                ]
            ];
            $status = 200;
            return response()->json($dataApi, $status);
//            }
            //Không có dữ liệu gửi qua, hoặc gửi không đủ dữ liệu
            //ques_ans:Bộ câu hỏi, trả lời khảo sát, dạng mảng
            //contract:Thông tin giao dịch trả về từ api đầu tiên
        } catch (Exception $ex) {
            //Lưu dữ liệu bị lỗi
//            if ($flagError == true) {
//                DB::rollback();
//            }
            DB::rollback();
            $dataApi = [
                'id' => 'fail',
                'status' => '500',
                'detail' => $ex->getMessage(),
            ];
            $status = 500;
            return response()->json($dataApi, $status);
        }
    }

    //Hàm kiểm tra hợp đồng này có cần khảo sát câu NPS hay không
    //True:có
    //False:không
    public function checkNPS($contractNum) {
        $hasNPS = FALSE;
//        $contractModel = DB::table('outbound_accounts')->select('id')->where('contract_num', '=', $contractNum)->get();
//        if (isset($contractModel[0]->id)) {
        $SurveySections = new SurveySections();

        $accountInfoFromSurvey = $SurveySections->getAllSurveyInfoOfAccountQGD($contractNum);
        $historyOutboundSurvey = array();
        $dateSurveyTemp = FALSE;

        foreach ($accountInfoFromSurvey as $i) {
            // chi tiết từng khảo sát
            $i->resultDetail = $SurveySections->getAllDetailSurveyInfo($i->section_id);
            // kiểm tra câu hỏi NPS
            // nếu câu hỏi có NPS lấy thời gian hoàn so sánh với thời gian hoàn thanh NPS của các câu khảo sát khác.
            $content = '';
            $temp = $i->resultDetail;
            foreach ($i->resultDetail as $d) {
                $flag = NULL;

                if ($d->question_id != $flag) {
                    $flag = $d->question_id;
                    $content .= '<b>' . $d->question_title_short . ': </b>';
                }
                $content .= $d->answers_title . ", ";
                if ($d->question_is_nps == 1) {
                    if ($i->section_time_completed > $dateSurveyTemp) {
                        $dateSurveyTemp = $i->section_time_completed;
                    }
                }
            }
            $i->content = $content;
            $historyOutboundSurvey[] = (array) $i;
        }
        if ($dateSurveyTemp != FALSE) {

            $currentDate = new \DateTime();
            $lastest_survey_nps_time = new \DateTime($dateSurveyTemp);
            $interval = $lastest_survey_nps_time->diff($currentDate)->format("%a");
            if ($interval < 90) {
                $hasNPS = TRUE;
            }
        }
//        }
        return $hasNPS;
    }

    //Api lấy thông tin người liên hệ
    public function getContact(Request $request) {
        try {
            if (!isset($request->contractNum)) {
                return json_encode(['code' => 406, 'msg' => 'Missing input contractNum']);
            } else {
                if ($request->contractNum == '') {
                    return json_encode(['code' => 406, 'msg' => 'Empty input contractNum ']);
                } else {
                    $timeStartCall = date('Y-m-d H:i:s');
                    $contract = $request->contractNum;
                    $modelOutboundAccount = new OutboundAccount();
                    $accountInfo = $modelOutboundAccount->getAccountInfoByContract($contract);
                    $modelContactProfile = new ContactProfile();
                    $contractNumOrAccId = $accountInfo == NULL ? $request->contractNum : $accountInfo->id;
                    $response['data'] = $modelContactProfile->getContactApi($contractNumOrAccId);

                    // Ghi log gọi API từ ISC
                    $logger = new Logger('my_logger');
                    $logger->pushHandler(new StreamHandler(storage_path() . '/logs/API_ISC_GetContact.log', Logger::INFO));
//                $logger->pushHandler(new FirePHPHandler());
                    $returnData = json_encode(['code' => 200, 'msg' => 'Success', 'data' => $response['data']]);
                    $timeEndCall = date('Y-m-d H:i:s');
                    $logger->addInfo('Log Call API', array('TimeStartCall' => $timeStartCall, 'TimeEndCall' => $timeEndCall, 'input' => $request->input(), 'output' => $returnData, 'ip' => $request->ip()));
                    return $returnData;
                }
            }
        } catch (Exception $ex) {
            return json_encode(['code' => 500, 'msg' => 'Internal Server Error']);
        }
    }

    //Api thêm thông tin người liên hệ
    public function addContact(Request $request) {
        try {
            $arrayValidate = [];
            $arrayInput = ['contractNum', 'dataContact', 'createrName'];
            $arrayInputContact = ['name', 'phone', 'relationship'];
            $input = $request->input();
            foreach ($arrayInput as $key => $value) {
                if (!isset($input[$value]) || $input[$value] == '')
                    array_push($arrayValidate, $value);
            }
            if (!empty($arrayValidate)) {
                return json_encode(['code' => 406, 'msg' => 'Missing or empty input ' . implode(',', $arrayValidate)]);
            } else {
                $dataContactInput = $request->dataContact;
                foreach ($arrayInputContact as $key2 => $value2) {
                    if (!isset($dataContactInput[$value2]) || $dataContactInput[$value2] == '')
                        array_push($arrayValidate, $value2);
                }
                if (!empty($arrayValidate))
                    return json_encode(['code' => 406, 'msg' => 'Missing or empty input ' . implode(',', $arrayValidate)]);
                else {
                    $timeStartCall = date('Y-m-d H:i:s');
                    $info = $request->dataContact;
                    $modelOutboundAccount = new OutboundAccount();
                    $accountInfo = $modelOutboundAccount->getAccountInfoByContract($request->contractNum);
                    $contractNum = $request->contractNum;
                    $accountID = ($accountInfo == NULL) ? NULL : $accountInfo->id;
                    $userCreatedName = $request->createrName;
                    ;
                    $modelContactProfile = new ContactProfile();
                    $response = $modelContactProfile->saveContactProfile($info, $accountID, NULL, $userCreatedName, $contractNum);
                    if ($response['code'] == 200) {
                        $result = array('code' => 200, 'msg' => 'Success');
                    } else {
                        $result = array('code' => 500, 'msg' => 'Update fail');
                    }
                    $returnData = json_encode($result);
                    // Ghi log gọi API ISC
                    $logger = new Logger('my_logger');
                    $logger->pushHandler(new StreamHandler(storage_path() . '/logs/API_ISC_AddContact.log', Logger::INFO));
                    $timeEndCall = date('Y-m-d H:i:s');
//                $logger->pushHandler(new FirePHPHandler());
                    $logger->addInfo('Log Call API', array('TimeStartCall' => $timeStartCall, 'TimeEndCall' => $timeEndCall, 'input' => $request->input(), 'output' => $returnData, 'ip' => $request->ip()));
                    return $returnData;
                }
            }
        } catch (Exception $ex) {
            return json_encode(['code' => 500, 'msg' => 'Internal Server Error']);
        }
    }

    public function saveInfoTransactionCounter(Request $request) {
        try {
            $allData = $request->input();
            $input = json_encode($allData['data']);
            //Lưu log gọi api
            $source = 'ApiTransactionController/saveInfoTransactionCounter';
            $apiLog = new ApiTransactionLog();
            $apiLog->survey_id = 8;
            $apiLog->source = $source;
            $apiLog->input = $input;
            $apiLog->save();
            $modelOutboundAccount = new OutboundAccount();
            $messageValidatePerTransaction = [];
            $messageErrorUpdate = [];
            $messageSuccessUpdate = [];
            if (!isset($allData['data']) || $allData['data'] == '') {
                $dataApi = [
                    'id' => 'fail',
                    'status' => '503',
                    'detail' => 'Thiếu data đầu vào',
                ];
                $status = 500;
                return response()->json($dataApi, $status);
            }

            foreach ($allData['data'] as $key => $data) {
//                $flagSuccess = false;
//                if (empty($data) || !isset($data['ques_ans']) || empty($data['ques_ans']) || !isset($data['contract']) || empty($data['contract'])) {
//                    $dataApi = [
//                        'id' => 'fail',
//                        'status' => '503',
//                        'detail' => 'Du lieu gui qua khong du',
//                    ];
//                    $status = 500;
//                    return response()->json($dataApi, $status);
//                } 
                if (isset($data['ques_ans']) && !empty($data['ques_ans']) && isset($data['survey_info']) && !empty($data['survey_info'])) {
                    $validateArray = ['questionID', 'answerID', 'note'];
                    $ques_ans = $data['ques_ans'];
                    $arrayError = [];

                    foreach ($ques_ans as $key2 => $value2) {
                        foreach ($validateArray as $key3 => $value3) {
                            //Dữ liệu gửi qua không có hoặc có nhưng bằng rỗng
                            if ((!isset($value2[$value3]) || $value2[$value3] == '')) {
                                if (!in_array($value3, $arrayError))
                                    array_push($arrayError, $value3);
                            }
                        }
                    }
                    //Validate thành công
//                    if (isset($ques_ans['questionID']) && $ques_ans['questionID'] != '' && isset($ques_ans['answerID']) && $ques_ans['answerID'] != '' && isset($ques_ans['note']) && $ques_ans['note'] != '') {
                    if (empty($arrayError)) {
                        DB::beginTransaction();
                        $surveySection = new SurveySections();
                        $resultCodes = $surveySection->checkExistCodes($data['survey_info']['SectionCode'], 8, $data['survey_info']['ContractNumber']);

                        //Đã lưu thông tin rồi nên bỏ qua
                        if (!empty($resultCodes)) {

                            //Xóa dữ liệu cũ
                            DB::table('outbound_survey_result')->where('survey_result_section_id', '=', $resultCodes[0]->section_id)->delete();
//                             var_dump($resultCodes);die;
                            foreach ($ques_ans as $key => $value) {
                                $surveyResult = new SurveyResult();
                                $surveyResult->survey_result_section_id = $resultCodes[0]->section_id;
                                $surveyResult->survey_result_question_id = $value['questionID'];
                                $surveyResult->survey_result_answer_id = $value['answerID'];
                                $surveyResult->survey_result_note = $value['note'];
                                //Rất ko hài lòng, ko hài lòng
                                // if ($value['answerID'] == 1 || $value['answerID'] == 2) {
                                //     $surveyResult->survey_result_answer_extra_id = isset($value['answerExtraID']) ? $value['answerExtraID'] : null;
                                //     $surveyResult->survey_result_note = isset($value['note']) ? $value['note'] : null;
                                //  }
                                //Lưu thất bại chi tiết khảo sát giao dịch đó
                                if (!$surveyResult->save()) {
//                                    array_push($arrayErrorUpdate, ['ContractNum' => $dataQGD['ContractNum'], 'TransactionCode' => $dataQGD['TransactionCode'], 'SecureCode' => $dataQGD['SecureCode']]);
                                    array_push($messageErrorUpdate, ['ContractNum' => $dataTransactionInfo['ContractNumber'], 'TransactionCode' => $dataQGD['TransactionCode']]);
//                                            $messageErrorUpdate.='Bộ dữ liệu khảo sát có ContractNum: ' . $dataQGD['ContractNum'] . ',TransactionCode:' . $dataQGD['TransactionCode'] . ', SecureCode:' . $dataQGD['SecureCode'] . ' cập nhập thất bại; ';
//                                    $flagError = true;
                                    DB::rollback();
                                    break;
                                }
                            }
                            array_push($messageSuccessUpdate, ['ContractNum' => $data['survey_info']['ContractNumber'], 'SectionCode' => $data['survey_info']['SectionCode'], 'Type' => $data['survey_info']['Type']]);
                            DB::commit();
                            continue;
                        }
                        $infoAcc = array('ObjID' => 0,
                            'Contract' => $data['survey_info']['ContractNumber'],
                            'IDSupportlist' => $data['survey_info']['SectionCode'],
                            'Type' => $data['survey_info']['Type']
                        );

                        /*
                         * Lấy thông tin khách hàng
                         */
                        $apiIsc = new Apiisc();

                        $responseAccountInfo = $apiIsc->GetFullAccountInfo($infoAcc);
                        $responseAccountInfo = $responseAccountInfo['result']['data'];
                        $dataTransactionInfo = (array) $this->processDataFromISC($responseAccountInfo[0]);


                        //Insert dư lieu isc tra ve vao survey_section                      
//                        $surveySection->section_contract_id = $dataQGD['ContractNum'];
                        $surveySection->section_code = $data['survey_info']['SectionCode'];
                        $surveySection->section_contract_num = $data['survey_info']['ContractNumber'];

                        $surveySection->section_customer_name = isset($dataTransactionInfo['CustomerName']) ? $dataTransactionInfo['CustomerName'] : null;
                        $surveySection->section_survey_id = 8;
                        $surveySection->section_record_channel = 5;
//                        $surveySection->sale_center_id = 3;
                        $surveySection->section_phone = isset($dataTransactionInfo['Phone']) ? $dataTransactionInfo['Phone'] : null;
                        //$surveySection->section_note = $dataQGD['note'];
                        $surveySection->section_objAddress = isset($dataTransactionInfo['Address']) ? $dataTransactionInfo['Address'] : null;
                        $surveySection->section_sub_parent_desc = isset($dataTransactionInfo['SubParentDesc']) ? $dataTransactionInfo['SubParentDesc'] : null;
                        $surveySection->section_location = isset($dataTransactionInfo['Location']) ? $dataTransactionInfo['Location'] : null;
                        $surveySection->section_fee_local_type = isset($dataTransactionInfo['ContractTypeName']) ? $dataTransactionInfo['ContractTypeName'] : null;
                        $surveySection->section_time_start = $data['survey_info']['SectionTimeStart'];
                        $surveySection->section_time_completed = $data['survey_info']['SectionTimeCompleted'];
                        $surveySection->section_time_completed_int = strtotime($data['survey_info']['SectionTimeCompleted']);
                        $surveySection->section_connected = 4;
                        $surveySection->section_action = 1;
                        $surveySection->section_region = isset($dataTransactionInfo['Region']) ? $dataTransactionInfo['Region'] : null;
                        $surveySection->section_location_id = isset($dataTransactionInfo['LocationID']) ? $dataTransactionInfo['LocationID'] : null;
                        $surveySection->section_branch_code = isset($dataTransactionInfo['BranchCode']) ? $dataTransactionInfo['BranchCode'] : null;
                        $surveySection->section_supporter = isset($dataTransactionInfo['Supporter']) ? $dataTransactionInfo['Supporter'] : null;
                        $surveySection->section_subsupporter = isset($dataTransactionInfo['SubSupporter']) ? $dataTransactionInfo['SubSupporter'] : null;
                        $surveySection->section_finish_date_list = isset($dataTransactionInfo['FinishDateList']) ? $dataTransactionInfo['FinishDateList'] : null;
                        $surveySection->section_finish_date_inf = isset($dataTransactionInfo['FinishDateINF']) ? $dataTransactionInfo['FinishDateINF'] : null;
                        if (isset($dataTransactionInfo['BranchCodeSale'])) {
                            //Vùng 1 hoặc 5
                            if ($surveySection->section_location_id == 4 || $surveySection->section_location_id == 8) {
                                //Trả dữ liệu sai
                                if ($dataTransactionInfo['BranchCodeSale'] == 0 || empty($dataTransactionInfo['BranchCodeSale']))
                                    $brancodeSale = $surveySection->section_branch_code;
                                else
                                    $brancodeSale = $dataTransactionInfo['BranchCodeSale'];
                            }
                            else {
                                $brancodeSale = $dataTransactionInfo['BranchCodeSale'];
                            }
                        } else
                            $brancodeSale = $surveySection->section_branch_code;
                        $surveySection->section_sale_branch_code = $brancodeSale;
                        $surveySection->section_package_sal = isset($dataTransactionInfo['PackageSal']) ? $dataTransactionInfo['PackageSal'] : null;
                        $surveySection->section_payment_type = isset($dataTransactionInfo['PaymentType']) ? $dataTransactionInfo['PaymentType'] : null;
                        $surveySection->section_account_payment = isset($dataTransactionInfo['AccountPayment']) ? $dataTransactionInfo['AccountPayment'] : null;
                        $surveySection->section_use_service = isset($dataTransactionInfo['UseService']) ? $dataTransactionInfo['UseService'] : null;
                        $surveySection->section_acc_sale = isset($dataTransactionInfo['AccountSale']) ? $dataTransactionInfo['AccountSale'] : null;
                        $surveySection->section_account_list = isset($dataTransactionInfo['AccountList']) ? $dataTransactionInfo['AccountList'] : null;
                        $surveySection->section_account_inf = isset($dataTransactionInfo['AccountINF']) ? $dataTransactionInfo['AccountINF'] : null;
                        $accountInfo = $modelOutboundAccount->getAccountInfoByContract($dataTransactionInfo['ContractNum']);
                        $surveySection->section_account_id = ($accountInfo == NULL) ? 0 : $accountInfo->id;
                        //Lưu thành công thông tin giao dịch
                        if ($surveySection->save()) {
                            $idDetail = $surveySection->section_id;
                            $flagSuccess = true;
//                        foreach ($ques_ans as $key => $value) {
                            foreach ($ques_ans as $key => $value) {
                                $surveyResult = new SurveyResult();
                                $surveyResult->survey_result_section_id = $idDetail;
                                $surveyResult->survey_result_question_id = $value['questionID'];
                                $surveyResult->survey_result_answer_id = $value['answerID'];
                                $surveyResult->survey_result_note = $value['note'];
                                if (!$surveyResult->save()) {
//                                    array_push($arrayErrorUpdate, ['ContractNum' => $dataQGD['ContractNum'], 'TransactionCode' => $dataQGD['TransactionCode'], 'SecureCode' => $dataQGD['SecureCode']]);
//                                            $messageErrorUpdate.='Bộ dữ liệu khảo sát có ContractNum: ' . $dataQGD['ContractNum'] . ',TransactionCode:' . $dataQGD['TransactionCode'] . ', SecureCode:' . $dataQGD['SecureCode'] . ' cập nhập thất bại; ';
                                    $flagSuccess = false;
                                    array_push($messageErrorUpdate, ['ContractNum' => $data['survey_info']['ContractNumber'], 'SectionCode' => $data['survey_info']['SectionCode'], 'Type' => $data['survey_info']['Type']]);
                                    DB::rollback();
                                    break;
                                }
                            }
                            if ($flagSuccess) {
                                array_push($messageSuccessUpdate, ['ContractNum' => $data['survey_info']['ContractNumber'], 'SectionCode' => $data['survey_info']['SectionCode'], 'Type' => $data['survey_info']['Type']]);
                                DB::commit();
                            }
                            //Rất ko hài lòng, ko hài lòng
                            // if ($value['answerID'] == 1 || $value['answerID'] == 2) {
                            //     $surveyResult->survey_result_answer_extra_id = isset($value['answerExtraID']) ? $value['answerExtraID'] : null;
                            //     $surveyResult->survey_result_note = isset($value['note']) ? $value['note'] : null;
                            //  }
                            //Lưu thất bại chi tiết khảo sát giao dịch đó
//                        }
                        } else {
//                            array_push($arrayErrorUpdate, ['ContractNum' => $dataQGD['ContractNum'], 'TransactionCode' => $dataQGD['TransactionCode'], 'SecureCode' => $dataQGD['SecureCode']]);
//                            $flagError = true;
                            array_push($messageErrorUpdate, ['ContractNum' => $data['survey_info']['ContractNumber'], 'SectionCode' => $data['survey_info']['SectionCode'], 'Type' => $data['survey_info']['Type']]);
//                            $messageErrorUpdate.='Bộ dữ liệu khảo sát có ContractNum: ' . $dataQGD['ContractNum'] . ',TransactionCode:' . $dataQGD['TransactionCode'] . ', SecureCode:' . $dataQGD['SecureCode'] . ' cập nhập thất bại; ';
                            DB::rollback();
                            break;
                        }
                    } else {
                        array_push($messageValidatePerTransaction, ['ContractNum' => $data['survey_info']['ContractNumber'], 'SectionCode' => $data['survey_info']['SectionCode'], 'Type' => $data['survey_info']['Type']]);
                    }
                } else {
                    $message = '';
                    if (!isset($data['ques_ans']) || empty($data['ques_ans']))
                        $message.='Thiếu ques_ans truyền vào hoặc truyền vào bằng rỗng /n';
                    else
                        $message.='Thiếu survey_info truyền vào hoặc truyền vào bằng rỗng';
                    array_push($messageValidatePerTransaction, $message);
                }
            }

            $dataApi = [
                'id' => 'success',
                'status' => '200',
                'detail' => ['ErrorRecord:' => ($messageValidatePerTransaction != '') ? $messageValidatePerTransaction : 'None',
                    'SucessRecord' => ($messageSuccessUpdate != '') ? $messageSuccessUpdate : 'None',
                    'FailRecord' => ($messageErrorUpdate != '') ? $messageErrorUpdate : 'None',
                ]
            ];
            $status = 200;
            return response()->json($dataApi, $status);
//            }
            //Không có dữ liệu gửi qua, hoặc gửi không đủ dữ liệu
            //ques_ans:Bộ câu hỏi, trả lời khảo sát, dạng mảng
            //contract:Thông tin giao dịch trả về từ api đầu tiên
        } catch (Exception $ex) {
            //Lưu dữ liệu bị lỗi
//            if ($flagError == true) {
//                DB::rollback();
//            }
            DB::rollback();
            $dataApi = [
                'id' => 'fail',
                'status' => '500',
                'detail' => $ex->getMessage(),
            ];
            $status = 500;
            return response()->json($dataApi, $status);
        }
    }

    private function processDataFromISC($data) {
        $dateFormat = 'Y-m-d h:i:s'; //config('app.datetime_format');
        if (!empty($data->ContractDate)) {
            $data->ContractDate = date($dateFormat, strtotime($data->ContractDate));
        }

        // kiểm tra là bảo trì hay triển khai.
        // nếu $data->FinishDateList == null => triển khai
        // Ngày thi công $data->FinishDateINF > ngày bảo trì $data->FinishDateList => triển khai
//    	$data->isCheckList = 1; // mặc định là bảo trì
//    	if ( empty($data->FinishDateList)){
//    		$data->isCheckList = 0; // triển khai
//    	}else if ( $data->FinishDateINF > $data->FinishDateList){
//    		$data->isCheckList = 0;
//    	}
        // end kiểm tra triển khai, bảo trì	
        if (!empty($data->FinishDateINF)) {
            $data->FinishDateINF = date($dateFormat, strtotime($data->FinishDateINF));
        }
        if (!empty($data->FinishDateList)) {
            $data->FinishDateList = date($dateFormat, strtotime($data->FinishDateList));
        }
        return $data;
    }

    private function getQuesAns($type) {
        $question = new OutboundQuestions();
        $resultSet = $question->getQuestionAnswer($type);
        return $resultSet;
    }

}
