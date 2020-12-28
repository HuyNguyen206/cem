<?php

    use App\Component\ExtraFunction;

    $ext = new ExtraFunction();
    $tempZone = explode(',', $region);
    if (count($tempZone) == 7) {
        $textTitleRegion = 'Toàn quốc';
        $textRegion = 'toàn quốc';
    } else {
        $textTitleRegion = 'Vùng ' . $region;
        $textRegion = 'V' . $region;
    }

    $arrayPointContacts = [
        'Sau Triển khai DirectSales',
        'Sau Triển khai TeleSales',
        'Sau Bảo trì TIN/PNC',
        'Sau Bảo trì INDO',
        'Sau Thu cước tại nhà',
        'Sau Triển khai Sales tại quầy',
        'Sau Swap',
        'Tổng hợp',
    ];

    $arrayFilter = $arrayTypeSurvey;
    $cs = 'cs';
    $cus = 'cus';
    $sta = 'sta';
    $act = 'act';
    $tempVariables = 'temp';
?>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>

    <body>
        <div>
            <div>
                <div>
                    @if(isset($sendMail) && $sendMail === true)
                    <b>Thông báo từ hệ thống Custome Voice</b>
                    <br/>
                    <b style="color: #FF0000;">Anh Chị vui lòng tải file Excel để xem báo cáo chi tiết các trường hợp khách hàng không hài lòng về chất lượng dịch vụ Internet/Truyền hình và hành động xử lý của nhân viên CSKH.</b>
                    <br/>
                    @endif
                    Hệ thống CEM - Customer Voice
                    <br/>
                    Tổng hợp CSAT 1,2 chất lượng dịch vụ Internet và Truyền hình
                    <br/>
                    {{$textTitleRegion}} Ngày: {{date('d/m/Y',strtotime($from_date)) .' - '. date('d/m/Y',strtotime($to_date))}}
                    <br/>
                    <br/>
                </div>
                <div>
                    <b>1. Thống kê CSAT 1,2 CLDV theo các điểm tiếp xúc</b><br/>
                    <b>1.1. Đối với CSAT 1,2 CLDV Internet</b><br/><br/>
                </div>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>

                                </th>
                                @foreach($arrayPointContacts as $pointContact)
                                <th colspan="5" style="text-align: center; vertical-align: middle">
                                    {{$pointContact}}
                                </th>
                                @endforeach
                            </tr>
                            <tr>
                                <th>
                                    Vùng
                                </th>
                                @foreach($arrayPointContacts as $pointContact)
                                <th>
                                    CSAT 1
                                </th>
                                <th>
                                    CSAT 2
                                </th>
                                <th >
                                    Tổng
                                </th>
                                <th>
                                    Tỷ lệ % <br/>không hài lòng
                                </th>
                                <th >
                                    CSAT TB
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tempZone as $location)
                                <?php $keyZone = 'Vùng ' . $location;?>
                                <tr>
                                    <td>
                                        {{$keyZone}}
                                    </td>
                                    @foreach ($arrayFilter as $filter)
                                        <td>{{$csatNet[$keyZone]['csat1_'.$filter]}}</td>
                                        <td>{{$csatNet[$keyZone]['csat2_'.$filter]}}</td>
                                        <td>{{$csatNet[$keyZone]['csat_t12_'.$filter]}}</td>
                                        <td>{{$csatNet[$keyZone]['csat_t12_'.$filter]/($csatNet[$keyZone]['csat_sl_'.$filter]==0?1:$csatNet[$keyZone]['csat_sl_'.$filter])}}</td>
                                        <td>{{$csatNet[$keyZone]['csat_d_'.$filter]/($csatNet[$keyZone]['csat_sl_'.$filter]==0?1:$csatNet[$keyZone]['csat_sl_'.$filter])}}</td>
                                        <?php
                                            $csatNet[$tempVariables]['csat1_' . $filter] += $csatNet[$keyZone]['csat1_' . $filter];
                                            $csatNet[$tempVariables]['csat2_' . $filter] += $csatNet[$keyZone]['csat2_' . $filter];
                                            $csatNet[$tempVariables]['csat_t12_' . $filter] += $csatNet[$keyZone]['csat_t12_' . $filter];
                                            $csatNet[$tempVariables]['csat_d_' . $filter] += $csatNet[$keyZone]['csat_d_' . $filter];
                                            $csatNet[$tempVariables]['csat_sl_' . $filter] += $csatNet[$keyZone]['csat_sl_' . $filter];
                                        ?>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    Tổng {{$textRegion}}
                                </td>
                                @foreach ($arrayFilter as $filter)
                                    <td>{{$csatNet[$tempVariables]['csat1_'.$filter]}}</td>
                                    <td>{{$csatNet[$tempVariables]['csat2_'.$filter]}}</td>
                                    <td>{{$csatNet[$tempVariables]['csat_t12_'.$filter]}}</td>
                                    <td>{{$csatNet[$tempVariables]['csat_t12_'.$filter]/($csatNet[$tempVariables]['csat_sl_'.$filter]==0?1:$csatNet[$tempVariables]['csat_sl_'.$filter])}}</td>
                                    <td>{{$csatNet[$tempVariables]['csat_d_'.$filter]/($csatNet[$tempVariables]['csat_sl_'.$filter]==0?1:$csatNet[$tempVariables]['csat_sl_'.$filter])}}</td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <b>1.2. Đối với CSAT 1,2 CLDV Truyền hình</b><br/><br/>
                </div>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>

                                </th>
                                @foreach($arrayPointContacts as $pointContact)
                                <th colspan="5" style="text-align: center; vertical-align: middle">
                                    {{$pointContact}}
                                </th>
                                @endforeach
                            </tr>
                            <tr>
                                <th>
                                    Vùng
                                </th>
                                @foreach($arrayPointContacts as $pointContact)
                                <th>
                                    CSAT 1
                                </th>
                                <th>
                                    CSAT 2
                                </th>
                                <th >
                                    Tổng
                                </th>
                                <th>
                                    Tỷ lệ % <br/>không hài lòng
                                </th>
                                <th >
                                    CSAT TB
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tempZone as $location)
                                <?php $keyZone = 'Vùng ' . $location; ?>
                                <tr>
                                    <td>
                                        {{$keyZone}}
                                    </td>
                                    @foreach ($arrayFilter as $filter)
                                        <td>{{$csatTv[$keyZone]['csat1_'.$filter]}}</td>
                                        <td>{{$csatTv[$keyZone]['csat2_'.$filter]}}</td>
                                        <td>{{$csatTv[$keyZone]['csat_t12_'.$filter]}}</td>
                                        <td>{{$csatTv[$keyZone]['csat_t12_'.$filter]/($csatTv[$keyZone]['csat_sl_'.$filter]==0?1:$csatTv[$keyZone]['csat_sl_'.$filter])}}</td>
                                        <td>{{$csatTv[$keyZone]['csat_d_'.$filter]/($csatTv[$keyZone]['csat_sl_'.$filter]==0?1:$csatTv[$keyZone]['csat_sl_'.$filter])}}</td>
                                        <?php
                                            $csatTv[$tempVariables]['csat1_' . $filter] += $csatTv[$keyZone]['csat1_' . $filter];
                                            $csatTv[$tempVariables]['csat2_' . $filter] += $csatTv[$keyZone]['csat2_' . $filter];
                                            $csatTv[$tempVariables]['csat_t12_' . $filter] += $csatTv[$keyZone]['csat_t12_' . $filter];
                                            $csatTv[$tempVariables]['csat_d_' . $filter] += $csatTv[$keyZone]['csat_d_' . $filter];
                                            $csatTv[$tempVariables]['csat_sl_' . $filter] += $csatTv[$keyZone]['csat_sl_' . $filter];
                                        ?>
                                    @endforeach

                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    Tổng {{$textRegion}}
                                </td>
                                @foreach ($arrayFilter as $filter)
                                    <td>{{$csatTv[$tempVariables]['csat1_'.$filter]}}</td>
                                    <td>{{$csatTv[$tempVariables]['csat2_'.$filter]}}</td>
                                    <td>{{$csatTv[$tempVariables]['csat_t12_'.$filter]}}</td>
                                    <td>{{$csatTv[$tempVariables]['csat_t12_'.$filter]/($csatTv[$tempVariables]['csat_sl_'.$filter]==0?1:$csatTv[$tempVariables]['csat_sl_'.$filter])}}</td>
                                    <td>{{$csatTv[$tempVariables]['csat_d_'.$filter]/($csatTv[$tempVariables]['csat_sl_'.$filter]==0?1:$csatTv[$tempVariables]['csat_sl_'.$filter])}}</td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <b>2. Thống kê Nguyên nhân và Hành động xử lý CSAT 1,2 CLDV bởi nhân viên CSKH Happy Call</b><br/>
                    <b>2.1. Đối với CSAT 1,2 CLDV Internet</b><br/><br/>
                </div>
                <?php $department = $cs; ?>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th colspan="{{count($statusesNet) + 1}}" style="text-align: center; vertical-align: middle">
                                    Nguyên nhân ghi nhận của nhân viên CSKH
                                </th>
                                <th colspan="{{count($actions[$department]) + 1}}" style="text-align: center; vertical-align: middle">
                                    Hành động xử lý của nhân viên CSKH
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    Vùng
                                </th>
                                @foreach ($statusesNet as $status)
                                    <th>
                                        {{$status->answers_title}}
                                    </th>
                                @endforeach
                                <th >
                                    Tổng
                                </th>
                                @foreach ($actions[$department] as $action)
                                    <th >
                                        {{$action->answers_title}}
                                    </th>
                                @endforeach
                                <th >
                                    Tổng
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tempZone as $location)
                                <?php $keyZone = 'Vùng ' . $location; ?>
                                <tr>
                                    <td>
                                        {{$keyZone}}
                                    </td>
                                    @foreach ($statusesNet as $status)
                                        <td>
                                            {{$csatNet[$keyZone][$department][$sta.'_'.$status->answer_id]}}
                                        </td>
                                        <?php $csatNet[$tempVariables][$department][$sta.'_' . $status->answer_id] += $csatNet[$keyZone][$department][$sta.'_' . $status->answer_id]; ?>
                                    @endforeach
                                    <td>
                                        {{$csatNet[$keyZone][$department][$sta.'_t']}}
                                    <?php $csatNet[$tempVariables][$department][$sta.'_t'] += $csatNet[$keyZone][$department][$sta.'_t']; ?>
                                    </td>
                                    @foreach ($actions[$department] as $action)
                                        <td>
                                            {{$csatNet[$keyZone][$department][$act.'_'.$action->answer_id]}}
                                        </td>
                                        <?php $csatNet[$tempVariables][$department][$act.'_' . $action->answer_id] += $csatNet[$keyZone][$department][$act.'_' . $action->answer_id];?>
                                    @endforeach
                                    <td>
                                        {{$csatNet[$keyZone][$department][$act.'_t']}}
                                        <?php $csatNet[$tempVariables][$department][$act.'_t'] += $csatNet[$keyZone][$department][$act.'_t']; ?>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    Tổng {{$textRegion}}
                                </td>
                                @foreach ($statusesNet as $status)
                                    <td>
                                        {{$csatNet[$tempVariables][$department][$sta.'_'.$status->answer_id]}}
                                    </td>
                                @endforeach
                                <td>
                                    {{$csatNet[$tempVariables][$department][$sta.'_t']}}
                                </td>
                                @foreach ($actions[$department] as $action)
                                    <td>
                                        {{$csatNet[$tempVariables][$department][$act.'_'.$action->answer_id]}}
                                    </td>
                                @endforeach
                                <td>
                                    {{$csatNet[$tempVariables][$department][$act.'_t']}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tỷ lệ %
                                </td>
                                @foreach ($statusesNet as $status)
                                    <td>
                                        {{$csatNet[$tempVariables][$department][$sta.'_'.$status->answer_id]/ ($csatNet[$tempVariables][$department][$sta.'_t'] == 0 ? 1:$csatNet[$tempVariables][$department][$sta.'_t'])}}
                                    </td>
                                @endforeach
                                <td>
                                    1
                                </td>
                                @foreach ($actions[$department] as $action)
                                    <td>
                                        {{$csatNet[$tempVariables][$department][$act.'_'.$action->answer_id]/ ($csatNet[$tempVariables][$department][$act.'_t'] == 0? 1:$csatNet[$tempVariables][$department][$act.'_t'])}}
                                    </td>
                                @endforeach
                                <td>
                                    1
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <b>2.2. Đối với CSAT 1,2 CLDV Truyền hình</b><br/><br/>
                </div>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>

                                </th>
                                <th colspan="{{count($statusesTv) + 1}}" style="text-align: center; vertical-align: middle">
                                    Nguyên nhân ghi nhận của nhân viên CSKH
                                </th>
                                <th colspan="{{count($actions[$department]) + 1}}" style="text-align: center; vertical-align: middle">
                                    Hành động xử lý của nhân viên CSKH
                                </th>
                            </tr>
                            <tr>
                                <th >
                                    Vùng
                                </th>
                                @foreach ($statusesTv as $status)
                                    <th >
                                    {{$status->answers_title}}
                                    </th>
                                @endforeach
                                <th >
                                    Tổng
                                </th>
                                @foreach ($actions[$department] as $action)
                                    <th >
                                    {{$action->answers_title}}
                                    </th>
                                @endforeach
                                <th >
                                    Tổng
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tempZone as $location)
                                <?php $keyZone = 'Vùng ' . $location; ?>
                                <tr>
                                    <td>
                                        {{$keyZone}}
                                    </td>
                                    @foreach ($statusesTv as $status)
                                        <td>
                                            {{$csatTv[$keyZone][$department][$sta.'_'.$status->answer_id]}}
                                        </td>
                                        <?php $csatTv[$tempVariables][$department][$sta.'_' . $status->answer_id] += $csatTv[$keyZone][$department][$sta.'_' . $status->answer_id]; ?>
                                    @endforeach
                                    <td>
                                        {{$csatTv[$keyZone][$department][$sta.'_t']}}
                                    </td>
                                    <?php $csatTv[$tempVariables][$department][$sta.'_t'] += $csatTv[$keyZone][$department][$sta.'_t']; ?>

                                    @foreach ($actions[$department] as $action)
                                        <td>
                                            {{$csatTv[$keyZone][$department][$act.'_'.$action->answer_id]}}
                                        </td>
                                        <?php $csatTv[$tempVariables][$department][$act.'_' . $action->answer_id] += $csatTv[$keyZone][$department][$act.'_' . $action->answer_id]; ?>
                                    @endforeach
                                    <td>
                                        {{$csatTv[$keyZone][$department][$act.'_t']}}
                                    </td>
                                    <?php $csatTv[$tempVariables][$department][$act.'_t'] += $csatTv[$keyZone][$department][$act.'_t']; ?>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot >
                            <tr>
                                <td>
                                    Tổng {{$textRegion}}
                                </td>
                                @foreach ($statusesTv as $status)
                                    <td>
                                        {{$csatTv[$tempVariables][$department][$sta.'_'.$status->answer_id]}}
                                    </td>
                                @endforeach
                                <td>
                                    {{$csatTv[$tempVariables][$department][$sta.'_t']}}
                                </td>
                                @foreach ($actions[$department] as $action)
                                    <td>
                                        {{$csatTv[$tempVariables][$department][$act.'_'.$action->answer_id]}}
                                    </td>
                                @endforeach
                                <td>
                                    {{$csatTv[$tempVariables][$department][$act.'_t']}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tỷ lệ %
                                </td>
                                @foreach ($statusesTv as $status)
                                    <td>
                                        {{$csatTv[$tempVariables][$department][$sta.'_'.$status->answer_id]/ ($csatTv[$tempVariables][$department][$sta.'_t'] == 0 ? 1:$csatTv[$tempVariables][$department][$sta.'_t'])}}
                                    </td>
                                @endforeach
                                <td>
                                    1
                                </td>
                                @foreach ($actions[$department] as $action)
                                    <td>
                                        {{$csatTv[$tempVariables][$department][$act.'_'.$action->answer_id]/ ($csatTv[$tempVariables][$department][$act.'_t'] == 0?1:$csatTv[$tempVariables][$department][$act.'_t'])}}
                                    </td>
                                @endforeach
                                <td>
                                    1
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <b>3. Thống kê Nguyên nhân và Hành động xử lý CSAT 1,2 CLDV bởi nhân viên thu cước tại nhà</b><br/>
                    <b>3.1. Đối với CSAT 1,2 CLDV Internet</b><br/><br/>
                </div>
                <?php $department = $cus; ?>
                <div>
                    <table>
                        <thead>
                        <tr>
                            <th>

                            </th>
                            <th colspan="{{count($statusesNet) + 1}}" style="text-align: center; vertical-align: middle">
                                Nguyên nhân ghi nhận của nhân viên thu cước
                            </th>
                            <th colspan="{{count($actions[$department]) + 1}}" style="text-align: center; vertical-align: middle">
                                Hành động xử lý của nhân viên thu cước
                            </th>
                        </tr>
                        <tr>
                            <th>
                                Vùng
                            </th>
                            @foreach ($statusesNet as $status)
                                <th>
                                    {{$status->answers_title}}
                                </th>
                            @endforeach
                            <th >
                                Tổng
                            </th>
                            @foreach ($actions[$department] as $action)
                                <th >
                                    {{$action->answers_title}}
                                </th>
                            @endforeach
                            <th >
                                Tổng
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($tempZone as $location)
                            <?php $keyZone = 'Vùng ' . $location; ?>
                            <tr>
                                <td>
                                    {{$keyZone}}
                                </td>
                                @foreach ($statusesNet as $status)
                                    <td>
                                        {{$csatNet[$keyZone][$department][$sta.'_'.$status->answer_id]}}
                                    </td>
                                    <?php $csatNet[$tempVariables][$department][$sta.'_' . $status->answer_id] += $csatNet[$keyZone][$department][$sta.'_' . $status->answer_id]; ?>
                                @endforeach
                                <td>
                                    {{$csatNet[$keyZone][$department][$sta.'_t']}}
                                    <?php $csatNet[$tempVariables][$department][$sta.'_t'] += $csatNet[$keyZone][$department][$sta.'_t']; ?>
                                </td>
                                @foreach ($actions[$department] as $action)
                                    <td>
                                        {{$csatNet[$keyZone][$department][$act.'_'.$action->answer_id]}}
                                    </td>
                                    <?php $csatNet[$tempVariables][$department][$act.'_' . $action->answer_id] += $csatNet[$keyZone][$department][$act.'_' . $action->answer_id];?>
                                @endforeach
                                <td>
                                    {{$csatNet[$keyZone][$department][$act.'_t']}}
                                    <?php $csatNet[$tempVariables][$department][$act.'_t'] += $csatNet[$keyZone][$department][$act.'_t']; ?>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                        <tfoot>
                        <tr>
                            <td>
                                Tổng {{$textRegion}}
                            </td>
                            @foreach ($statusesNet as $status)
                                <td>
                                    {{$csatNet[$tempVariables][$department][$sta.'_'.$status->answer_id]}}
                                </td>
                            @endforeach
                            <td>
                                {{$csatNet[$tempVariables][$department][$sta.'_t']}}
                            </td>
                            @foreach ($actions[$department] as $action)
                                <td>
                                    {{$csatNet[$tempVariables][$department][$act.'_'.$action->answer_id]}}
                                </td>
                            @endforeach
                            <td>
                                {{$csatNet[$tempVariables][$department][$act.'_t']}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Tỷ lệ %
                            </td>
                            @foreach ($statusesNet as $status)
                                <td>
                                    {{$csatNet[$tempVariables][$department][$sta.'_'.$status->answer_id]/ ($csatNet[$tempVariables][$department][$sta.'_t'] == 0 ? 1:$csatNet[$tempVariables][$department][$sta.'_t'])}}
                                </td>
                            @endforeach
                            <td>
                                1
                            </td>
                            @foreach ($actions[$department] as $action)
                                <td>
                                    {{$csatNet[$tempVariables][$department][$act.'_'.$action->answer_id]/ ($csatNet[$tempVariables][$department][$act.'_t'] == 0? 1:$csatNet[$tempVariables][$department][$act.'_t'])}}
                                </td>
                            @endforeach
                            <td>
                                1
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <b>3.2. Đối với CSAT 1,2 CLDV Truyền hình</b><br/><br/>
                </div>
                <div>
                    <table>
                        <thead>
                        <tr>
                            <th>

                            </th>
                            <th colspan="{{count($statusesTv) + 1}}" style="text-align: center; vertical-align: middle">
                                Nguyên nhân ghi nhận của nhân viên thu cước
                            </th>
                            <th colspan="{{count($actions[$department]) + 1}}" style="text-align: center; vertical-align: middle">
                                Hành động xử lý của nhân viên thu cước
                            </th>
                        </tr>
                        <tr>
                            <th >
                                Vùng
                            </th>
                            @foreach ($statusesTv as $status)
                                <th >
                                    {{$status->answers_title}}
                                </th>
                            @endforeach
                            <th >
                                Tổng
                            </th>
                            @foreach ($actions[$department] as $action)
                                <th >
                                    {{$action->answers_title}}
                                </th>
                            @endforeach
                            <th >
                                Tổng
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($tempZone as $location)
                            <?php $keyZone = 'Vùng ' . $location; ?>
                            <tr>
                                <td>
                                    {{$keyZone}}
                                </td>
                                @foreach ($statusesTv as $status)
                                    <td>
                                        {{$csatTv[$keyZone][$department][$sta.'_'.$status->answer_id]}}
                                    </td>
                                    <?php $csatTv[$tempVariables][$department][$sta.'_' . $status->answer_id] += $csatTv[$keyZone][$department][$sta.'_' . $status->answer_id]; ?>
                                @endforeach
                                <td>
                                    {{$csatTv[$keyZone][$department][$sta.'_t']}}
                                </td>
                                <?php $csatTv[$tempVariables][$department][$sta.'_t'] += $csatTv[$keyZone][$department][$sta.'_t']; ?>

                                @foreach ($actions[$department] as $action)
                                    <td>
                                        {{$csatTv[$keyZone][$department][$act.'_'.$action->answer_id]}}
                                    </td>
                                    <?php $csatTv[$tempVariables][$department][$act.'_' . $action->answer_id] += $csatTv[$keyZone][$department][$act.'_' . $action->answer_id]; ?>
                                @endforeach
                                <td>
                                    {{$csatTv[$keyZone][$department][$act.'_t']}}
                                </td>
                                <?php $csatTv[$tempVariables][$department][$act.'_t'] += $csatTv[$keyZone][$department][$act.'_t']; ?>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot >
                        <tr>
                            <td>
                                Tổng {{$textRegion}}
                            </td>
                            @foreach ($statusesTv as $status)
                                <td>
                                    {{$csatTv[$tempVariables][$department][$sta.'_'.$status->answer_id]}}
                                </td>
                            @endforeach
                            <td>
                                {{$csatTv[$tempVariables][$department][$sta.'_t']}}
                            </td>
                            @foreach ($actions[$department] as $action)
                                <td>
                                    {{$csatTv[$tempVariables][$department][$act.'_'.$action->answer_id]}}
                                </td>
                            @endforeach
                            <td>
                                {{$csatTv[$tempVariables][$department][$act.'_t']}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Tỷ lệ %
                            </td>
                            @foreach ($statusesTv as $status)
                                <td>
                                    {{$csatTv[$tempVariables][$department][$sta.'_'.$status->answer_id]/ ($csatTv[$tempVariables][$department][$sta.'_t'] == 0 ? 1:$csatTv[$tempVariables][$department][$sta.'_t'])}}
                                </td>
                            @endforeach
                            <td>
                                1
                            </td>
                            @foreach ($actions[$department] as $action)
                                <td>
                                    {{$csatTv[$tempVariables][$department][$act.'_'.$action->answer_id]/ ($csatTv[$tempVariables][$department][$act.'_t'] == 0?1:$csatTv[$tempVariables][$department][$act.'_t'])}}
                                </td>
                            @endforeach
                            <td>
                                1
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>