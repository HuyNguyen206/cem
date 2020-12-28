<?php
$controller = 'csat-service';
$title = 'List Report';
$transFile = $controller;
$common = 'common';
$prefix = main_prefix;
$temp = $t = '';
//Thông tin quyền hạn user
$userRole = Session::get('userRole');
$typeSurvey = [
    1 => trans($transFile.'.AfterActive'),
    2 => trans($transFile.'.AfterChecklist'),
//        3 => 'Sau Thu cước tại nhà',
//        6 => 'Sau Triển khai TeleSale',
//        9 => 'Sau Triển khai Sale tại quầy',
//        10 => 'Sau Triển khai Swap'
];
$errorNet = [
    85 => trans('report.InternetIsNotStable'),
    87 =>  trans('report.EquipmentError'),
    88 =>  trans('report.VoiceError'),
    89 =>  trans('report.WifiWeakNotStable'),
    90 =>  trans('report.GameLagging'),
    91 =>  trans('report.CannotUsingWifi'),
    92 =>  trans('report.LoosingSignal'),
    94 =>  trans('report.HaveSignalButCannotAccess'),
    97 =>  trans('report.SlowInternet'),
    98 =>  trans('report.SignalIsNotStableSignalLoosingIsUnderStandard'),
    99 =>  trans('report.IntenationalInternetSlow'),
    100 =>  trans('report.Other')
];
//    $errorTV = [
//        99 => 'Xé hình',
//        102 => 'Giật,Đứng hình , chập chờn',
//        103 => 'Có hình không có tiếng hoặc có tiếng không có hình tất cả các kênh',
//        105 => 'Không xem được các kênh truyền hình',
//        106 => 'Không sử dụng được thiết bị lưu trữ , mạng chia sẻ',
//        111 => 'Hình ảnh bị sọc ngang, sọc chéo',
//        112 => 'Lỗi kho ứng dụng',
//        121 => 'Lỗi kết nối HDBox &TV',
//        122 => 'Điều khiển , app điều khiển',
//        123 => 'Đấu nối thiết bị amply sử dụng KaraTV',
//        124 => 'Thiết bị Hdbox khởi động chậm',
//        125 => 'Không có hình , không có tiếng một vài kênh',
//        126 => 'Không xem được kho Phim',
//        127 => 'Khác'
//    ];
$recordChannelArray = [
    1 => 'Happy Call',
    2 => 'Email-Web',
    3 => 'Hi FPT',
    4 => 'NVTC-MobiPay',
    5 => 'Giao dịch tại quầy',
    6 => 'Tablet'
];
$generalAction = [
    '0' => trans($transFile.'.ActionResolveStaff'),
    '1' =>  trans($transFile.'.NotYetDoAnything'),
    '2' =>  trans($transFile.'.CreateChecklist'),
    '3' =>  trans($transFile.'.CreatePreChecklist'),
    '5' =>  trans($transFile.'.SendToDepartment'),
];
$prechecklistStatus = [
    0 => trans($transFile.'.NotYetResolve'),
    2 => trans($transFile.'.Processing'),
    3 => trans($transFile.'.FinnishResolve'),
    99 => trans($transFile.'.CancelNotResolve')
];

$actionNetTv = [
    115 =>trans('action.SaySorryToCustomerAndClosedCase'),
    117 => trans('action.CreatePreChecklist'),
    119 => trans('action.CreateChecklistOnSite'),
    118 => trans('action.CreateChecklist'),
    128 => trans('action.Other'),
    116 => trans('action.SendToDepartment'),
];
$firstStatusPreCL = [
    5 => trans($transFile.'.RequireUpdateChecklist'),
    1 => trans($transFile.'.LoosingConnection'),
    6 => 'IPTV',
    7 => 'Wifi',
    2 => trans($transFile.'.SlowConnection'),
    3 => trans($transFile.'.InternetIsNotStable'),
    4 => trans($transFile.'.Other'),
    '' => ''
];
$actionProcessPreCL = [
    1 => trans($transFile.'.ClosePreCLAndCreateCL'),
    2 => trans($transFile.'.ClosePreCLAndNotCreateCL'),
    3 => trans($transFile.'.PreCLIsProcessing'),
    4 => trans($transFile.'.PreCLNotResolve'),
    5 => trans($transFile.'.ClosePreCLWhenCreatePreCLWhichHasESCLIsProcessing'),
];
$departmentPreCL = [
    4 => 'IBB',
    7 => 'CUS',
    43 => 'CS-HO',
    430 => 'CS-CN',
    39 => 'Telesales',
];
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<div>
    <table>
        <thead>
        <tr>
            <th>{{trans($transFile.'.ON')}}</th>
            <th>{{trans($transFile.'.Branch')}}</th>
            <th>{{trans($transFile.'.PointOfContact')}}</th>
            <th>{{trans($transFile.'.ChannelConfirm')}}</th>
            <th>{{trans($transFile.'.ContractNumber')}}</th>
            <th>{{trans($transFile.'.Sale')}}</th>
            <th>{{trans($transFile.'.Tech')}}</th>
            <th>{{trans($transFile.'.TimeComplete')}}</th>
            <th>CSAT Internet</th>
            <th>{{trans($transFile.'.ResolveInternet')}}</th>
            <th>{{trans($transFile.'.NetErrorType')}}</th>
            <th style="text-align: center">{{trans($transFile.'.Note')}}</th>
            <!--<th style="text-align: center"><b style="color: #DD1144;">Hành động xử lý CSKH</b></th>-->



            <?php
            if (isset($searchCondition['sectionGeneralAction'])) {
            //Precl
            if ($searchCondition['sectionGeneralAction'] == 3) {
            ?>
            <th>Prechecklist</th>
            <th>{{trans($transFile.'.FirstIncident')}}</th>
            <th>{{trans($transFile.'.ConfirmInformation')}}</th>
            <th>{{trans($transFile.'.CountSupport')}}</th>
            <th>{{trans($transFile.'.Status')}}</th>
            <th>{{trans($transFile.'.SurveyAgent')}}</th>
            <th>{{trans($transFile.'.ProcessTime')}}</th>
            <th>{{trans($transFile.'.AppointmentTime')}}</th>
            <th>{{trans($transFile.'.TotalMinute')}}</th>
            <th>{{trans($transFile.'.ProcessAction')}}</th>
            <th>{{trans($transFile.'.NumberPre')}}</th>


            <?php
            }
            //CL
            else if($searchCondition['sectionGeneralAction'] == 2)
            {
            ?>
            <th>{{trans($transFile.'.InputTime')}}</th>
            <th>{{trans($transFile.'.Assign')}}</th>
            <th>{{trans($transFile.'.TimeStore')}}</th>
            <th>{{trans($transFile.'.ErrorPosition')}}</th>
            <th>{{trans($transFile.'.ErrorDescription')}}</th>
            <th>{{trans($transFile.'.ReasonDescription')}}</th>
            <th>{{trans($transFile.'.WaySolving')}}</th>
            <th>{{trans($transFile.'.Note')}}</th>
            <th>{{trans($transFile.'.ChecklistType')}}</th>
            <th>{{trans($transFile.'.QuantityOfChecklist')}}</th>
            <th>{{trans($transFile.'.RepeatCL')}}</th>
            <th>{{trans($transFile.'.Status')}}</th>
            <th>{{trans($transFile.'.FinishTime')}}</th>
            <th>{{trans($transFile.'.TotalMinute')}}</th>
            <?php
            }
            //Chuyen phong ban
//            else if ($searchCondition['sectionGeneralAction'] == 5) {
            //                        dump(0.1);
            ?>
            {{--<th>Ngày gửi</th>--}}
            {{--<th>Nội dung gửi</th>--}}
            {{--<th>Bộ phận chuyển tiếp</th>--}}
            {{--<th>Bộ phận tiếp nhận</th>--}}
            {{--<th>Nội dung xử lý</th>--}}
            {{--<th>Kết quả XL</th>--}}
            {{--<th>Người Xử lý</th>--}}
            {{--<th>Thời gian xử lý</th>--}}
            {{--<th>Tổng số phút</th>--}}
            {{--<th>Số lượng chuyển tiếp</th>--}}


            <?php
//            }
            }
            ?>

        </tr>
        </thead>
        <tbody>
        <?php
        //                                        dump($modelSurveySections);die;
        $i = 1;
        if (!empty($modelSurveySections)) {
        ?>
        @foreach($modelSurveySections as $sections)
            <tr >
                <td>{{$i}}</td>
                <td>{{$sections['ChiNhanh']}}</td>
                <td>
                    <?php
                    $nameSurvey = isset($typeSurvey[$sections['section_survey_id']]) ? $typeSurvey[$sections['section_survey_id']] : $sections['section_survey_id'];
                    echo $nameSurvey;
                    ?>
                </td>
                <td>{{$recordChannelArray[$sections['section_record_channel']]}}</td>
                <td>{{$sections['section_contract_num']}}</td>
                <td>{{$sections['section_acc_sale']}}</td>
                <td>{{$sections['section_account_inf'].$sections['section_account_list']}}</td>
                <td>{{$sections['section_time_completed']}}</td>
                <td>
                    {{$sections['CSAT_Internet']}}
                </td>

                <td style="text-align: center" class="more">
                    <?php
                    if (isset($actionNetTv[$sections['Xu_ly_internet']])) {
                    ?>
                    {{$actionNetTv[$sections['Xu_ly_internet']]}}
                    <?php } ?>
                </td>
                <td>
                    <?php
                    if (isset($errorNet[$sections['Loai_loi_internet']])) {
                    ?>
                    {{$errorNet[$sections['Loai_loi_internet']]}}
                    <?php } ?>
                </td>
                <td class="detailActionPopUp" style="text-align: left;">
                    {{$sections['section_note']}}
                </td>
            <!--                                                <td class="detailActionPopUp" style="text-align: left">
                                                    {{--{{$generalAction[$sections['section_action']]}} --}}
                    </td>-->
                <?php
                if ($searchCondition['sectionGeneralAction'] == 3) {
                ?>
                <td><?php
                    if ($sections['first_status'] == '' || $sections['first_status'] == NULL)
                        echo trans($transFile.'.No');
                    else
                        echo trans($transFile.'.Yes');
                    ?></td>
                <td>
                    <?php if (isset($firstStatusPreCL[$sections['first_status']])) { ?>
                    {{$firstStatusPreCL[$sections['first_status']]}}
                    <?php } ?>

                </td>
                <td >
                    {{$sections['description']}}
                </td>
                <td>
                    {{$sections['count_sup']}}
                </td>
                <td>
                    <?php if (isset($prechecklistStatus[$sections['sup_status_id']])) { ?>
                    {{$prechecklistStatus[$sections['sup_status_id']]}}
                    <?php } ?>

                </td>
                <td>
                    {{$sections['create_by']}}
                </td>
                <td>
                    {{$sections['update_date']}}
                </td>
                <td>
                    {{$sections['appointment_timer']}}
                </td>
                <td>
                    {{$sections['total_minute']}}
                </td>
                <td>
                    <?php if (isset($actionProcessPreCL[$sections['action_process']])) { ?>
                    {{$actionProcessPreCL[$sections['action_process']]}}
                    <?php
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ($sections['first_status'] != '' && $sections['first_status'] != NULL) {
                    ?>
                    {{($sections['subkey'] + 1)}}<br>
                    <?php
                    if ($sections['subkey'] > 0) {
                    ?>
                    <span style="color: red;cursor: pointer" data='<?php echo $sections['section_contract_num'] . '-' . $sections['section_code'] . '-' . $sections['section_survey_id']; ?>' class='showDetailPreclAndCl' >Xem thêm</span>
                    <?php
                    }
                    } else {
                    ?>
                    0
                    <?php
                    }
                    ?>
                </td>


                <?php
                } else if($searchCondition['sectionGeneralAction'] == 2)
                {
                ?>
                <td>
                    {{$sections['input_time']}}
                </td>
                <td>
                    {{$sections['assign']}}
                </td>
                <td>
                    <?php
                    //                                                    if ($sections['action_process'] == 1) {
                    if ($sections['finish_date'] != '' && $sections['finish_date'] != NULL) {
                        $storeTime = 0;
                    } else {
                        $currentTime = date('y-m-d h:i:s');
                        $date2Timestamp = strtotime($currentTime);
                        $date1Timestamp = strtotime($sections['created_at']);
                        $difference = $date2Timestamp - $date1Timestamp;

                        $storeTime = round($difference / (60 * 60), 0);
                    }
                    ?>
                    {{$storeTime . ' gio'}}
                    <?php
                    //                                                    }
                    ?>
                </td>

                <td>
                    {{$sections['error_position']}}
                </td>
                <td>
                    {{$sections['error_description']}}
                </td>
                <td>
                    {{$sections['reason_description']}}
                </td>
                <td>
                    {{$sections['way_solving']}}
                </td>
                <td >
                    {{$sections['s_description']}}
                </td>
                <td>
                    {{$sections['checklist_type']}}
                </td>
                <td>
                    <?php
                    if($sections['repeat_checklist'] === null)
                    {
                        echo 1;
                    }
                    else if ($sections['repeat_checklist'] === '')
                    {
                        echo 0;
                    } else
                    {
                        echo ($sections['repeat_checklist'] +1);
                    }
                    ?>
                </td>
                <td>
                    {{$sections['repeat_checklist']}}
                </td>
                <td>
                    {{$sections['final_status']}}
                </td>
                <td>
                    {{$sections['finish_date']}}
                </td>
                <td>
                    {{$sections['total_minute']}}
                </td>
                <?php
                }
                //                                                     dump($sections);die;
                ?>


            </tr>

            <?php $i++; ?>
        @endforeach
        <?php } ?>
        </tbody>

    </table>
</div>
</body>
</html>


