<?php
use App\Component\ExtraFunction;
$ext = new ExtraFunction();

$tempZone = explode(',', $region);
if(count($tempZone) == 7){
    $textTitleRegion = 'Toàn quốc';
    $textRegion = 'toàn quốc';
}else{
    $textTitleRegion = 'Vùng '.$region;
    $textRegion = 'V'.$region;
}

$arrayPointContacts = [
    'Sau triển khai DirectSales',
    'Sau Triển khai TeleSales',
    'Sau bảo trì TIN/PNC',
    'Sau bảo trì INDO',
    'Sau thu cước tại nhà',
    'Sau triển khai Sales tại quầy',
    'Sau swap',
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
    <style type="text/css">
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }

        td, th {
            border: 1px solid black;
            padding: 8px;
        }
    </style>
</head>

<body>
<div class="table-responsive">
    <b style="font-size: 20px !important;">Thông báo từ hệ thống Customer Voice</b>
    <br/>
    <b style="font-size: 20px !important; color: red;">Anh Chị vui lòng tải file Excel để xem báo cáo chi tiết các trường hợp khách hàng không hài lòng về chất lượng dịch vụ Internet/Truyền hình và hành động xử lý của nhân viên CSKH.</b>

    <div  class="center bolder" style="font-size: 20px !important; margin: auto; text-align: center;">
        Hệ thống CEM - Customer Voice<br/>
        Tổng hợp CSAT 1, 2 Chất lượng dịch vụ Internet và Truyền hình<br/>
        <b style="color: #08c;">{{$textTitleRegion}}</b> Ngày: <b style="color: #08c;">{{date('d/m/Y',strtotime($from_date))}}</b> - <b style="color: #08c;">{{date('d/m/Y',strtotime($to_date))}}</b>
    </div>
</div>

<div id="viewLocation">
    <div>
        <div class="red bolder" style="color: red;font-weight: bold; margin: auto;">
            <p>1. Thống kê CSAT 1,2 CLDV theo các điểm tiếp xúc<br/>   1.1. Đối với CSAT 1,2 CLDV Internet</p>
        </div>
        <div>
            <table width="100%">
                <thead>
                <tr style="background-color: #9BC2E6;">
                    <th>

                    </th>
                    @foreach($arrayPointContacts as $pointContact)
                        <th colspan="5" style="text-align: center; vertical-align: central;">
                            {{$pointContact}}
                        </th>
                    @endforeach
                </tr>
                <tr style="background-color: #BDD7EE;">
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
                    <?php
                        $keyZone = 'Vùng '.$location;
                    ?>
                    <tr>
                        <td>
                            {{$keyZone}}
                        </td>
                        @foreach ($arrayFilter as $filter)
                            <td>{{$csatNet[$keyZone]['csat1_'.$filter]}}</td>
                            <td>{{$csatNet[$keyZone]['csat2_'.$filter]}}</td>
                            <td>{{$csatNet[$keyZone]['csat_t12_'.$filter]}}</td>
                            <td>{{$ext->reRoundFloatNum($csatNet[$keyZone]['csat_t12_'.$filter]/($csatNet[$keyZone]['csat_sl_'.$filter]==0?1:$csatNet[$keyZone]['csat_sl_'.$filter]) * 100 , 2)}}%</td>
                            <td>{{$ext->reRoundFloatNum($csatNet[$keyZone]['csat_d_'.$filter]/($csatNet[$keyZone]['csat_sl_'.$filter]==0?1:$csatNet[$keyZone]['csat_sl_'.$filter]),2)}}</td>
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
                <tr style="background-color: #FFC000; color: #FF0000;">
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    @foreach ($arrayFilter as $filter)
                        <td>{{$csatNet[$tempVariables]['csat1_'.$filter]}}</td>
                        <td>{{$csatNet[$tempVariables]['csat2_'.$filter]}}</td>
                        <td>{{$csatNet[$tempVariables]['csat_t12_'.$filter]}}</td>
                        <td>{{$ext->reRoundFloatNum($csatNet[$tempVariables]['csat_t12_'.$filter]/($csatNet[$tempVariables]['csat_sl_'.$filter]==0?1:$csatNet[$tempVariables]['csat_sl_'.$filter]) * 100, 2)}}%</td>
                        <td>{{$ext->reRoundFloatNum($csatNet[$tempVariables]['csat_d_'.$filter]/($csatNet[$tempVariables]['csat_sl_'.$filter]==0?1:$csatNet[$tempVariables]['csat_sl_'.$filter]),2)}}</td>
                    @endforeach
                </tr>
                </tfoot>
            </table>
        </div>
        <br/>
        <div class="red bolder" style="color: red; font-weight: bold;">
            <p>   1.2. Đối với CSAT 1,2 CLDV Truyền hình</p>
        </div>
        <div>
            <table width="100%">
                <thead>
                <tr style="background-color: #9BC2E6;">
                    <th>

                    </th>
                    @foreach($arrayPointContacts as $pointContact)
                        <th colspan="5" style="text-align: center; vertical-align: central;">
                            {{$pointContact}}
                        </th>
                    @endforeach
                </tr>
                <tr style="background-color: #BDD7EE;">
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
                    <?php
                        $keyZone = 'Vùng '.$location;
                    ?>
                    <tr>
                        <td>
                            {{$keyZone}}
                        </td>
                        @foreach ($arrayFilter as $filter)
                            <td>{{$csatTv[$keyZone]['csat1_'.$filter]}}</td>
                            <td>{{$csatTv[$keyZone]['csat2_'.$filter]}}</td>
                            <td>{{$csatTv[$keyZone]['csat_t12_'.$filter]}}</td>
                            <td>{{$ext->reRoundFloatNum($csatTv[$keyZone]['csat_t12_'.$filter]/($csatTv[$keyZone]['csat_sl_'.$filter]==0?1:$csatTv[$keyZone]['csat_sl_'.$filter]) * 100, 2)}}%</td>
                            <td>{{$ext->reRoundFloatNum($csatTv[$keyZone]['csat_d_'.$filter]/($csatTv[$keyZone]['csat_sl_'.$filter]==0?1:$csatTv[$keyZone]['csat_sl_'.$filter]),2)}}</td>
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
                <tr style="background-color: #FFC000; color: #FF0000;">
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    @foreach ($arrayFilter as $filter)
                        <td>{{$csatTv[$tempVariables]['csat1_'.$filter]}}</td>
                        <td>{{$csatTv[$tempVariables]['csat2_'.$filter]}}</td>
                        <td>{{$csatTv[$tempVariables]['csat_t12_'.$filter]}}</td>
                        <td>{{$ext->reRoundFloatNum($csatTv[$tempVariables]['csat_t12_'.$filter]/($csatTv[$tempVariables]['csat_sl_'.$filter]==0?1:$csatTv[$tempVariables]['csat_sl_'.$filter]) * 100, 2)}}%</td>
                        <td>{{$ext->reRoundFloatNum($csatTv[$tempVariables]['csat_d_'.$filter]/($csatTv[$tempVariables]['csat_sl_'.$filter]==0?1:$csatTv[$tempVariables]['csat_sl_'.$filter]),2)}}</td>
                    @endforeach
                </tr>
                </tfoot>
            </table>
        </div>
        <br/>
        <div class="red bolder" style="color: red; font-weight: bold;">
            <p>2. Thống kê Nguyên nhân và Hành động xử lý CSAT 1,2 CLDV bởi nhân viên CSKH<br/>   2.1. Đối với CSAT 1,2 CLDV Internet</p>
        </div>
        <?php $department = $cs; ?>
        <div>
            <table width="100%">
                <thead>
                <tr style="background-color: #9BC2E6;">
                    <th></th>
                    <th colspan="{{count($statusesNet) + 1}}" style="text-align: center; vertical-align: central;">
                        Nguyên nhân ghi nhận của nhân viên CSKH
                    </th>
                    <th colspan="{{count($actions[$department]) + 1}}" style="text-align: center; vertical-align: central;">
                        Hành động xử lý của nhân viên CSKH
                    </th>
                </tr>
                <tr style="background-color: #BDD7EE;">
                    <th  style="text-align: center; vertical-align: central;">
                        Chi nhánh
                    </th>
                    @foreach ($statusesNet as $status)
                        <th  style="text-align: center; vertical-align: central;">
                            {{$status['answers_title']}}
                        </th>
                    @endforeach
                    <th  style="text-align: center; vertical-align: central; color: #FF0000;">
                        Tổng
                    </th>
                    @foreach ($actions[$department] as $action)
                        <th  style="text-align: center; vertical-align: central;">
                            {{$action['answers_title']}}
                        </th>
                    @endforeach
                    <th  style="text-align: center; vertical-align: central; color: #FF0000;">
                        Tổng
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tempZone as $location)
                    <?php
                        $keyZone = 'Vùng '.$location;
                    ?>
                    <tr>
                        <td>
                            {{$keyZone}}
                        </td>
                        @foreach ($statusesNet as $status)
                            <td>
                                {{$csatNet[$keyZone][$department][$sta.'_'.$status['answer_id']]}}
                            </td>
                            <?php
                            $csatNet[$tempVariables][$department][$sta.'_' . $status['answer_id']] += $csatNet[$keyZone][$department][$sta.'_' . $status['answer_id']];
                            ?>
                        @endforeach
                        <td style="color: #FF0000;">
                            {{$csatNet[$keyZone][$department][$sta.'_t']}}
                            <?php $csatNet[$tempVariables][$department][$sta.'_t'] += $csatNet[$keyZone][$department][$sta.'_t']; ?>
                        </td>
                        @foreach ($actions[$department] as $action)
                            <td>
                                {{$csatNet[$keyZone][$department][$act.'_'.$action['answer_id']]}}
                            </td>
                            <?php
                            $csatNet[$tempVariables][$department][$act.'_' . $action['answer_id']] += $csatNet[$keyZone][$department][$act.'_' . $action['answer_id']];
                            ?>
                        @endforeach
                        <td style="color: #FF0000;">
                            {{$csatNet[$keyZone][$department][$act.'_t']}}
                            <?php $csatNet[$tempVariables][$department][$act.'_t'] += $csatNet[$keyZone][$department][$act.'_t']; ?>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr style="background-color: #FFC000;">
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    @foreach ($statusesNet as $status)
                        <td>
                            {{$csatNet[$tempVariables][$department][$sta.'_'.$status['answer_id']]}}
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        {{$csatNet[$tempVariables][$department][$sta.'_t']}}
                    </td>
                    @foreach ($actions[$department] as $action)
                        <td>
                            {{$csatNet[$tempVariables][$department][$act.'_'.$action['answer_id']]}}
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        {{$csatNet[$tempVariables][$department][$act.'_t']}}
                    </td>
                </tr>
                <tr style="background-color: #FFC000;">
                    <td>
                        Tỷ lệ %
                    </td>
                    @foreach ($statusesNet as $status)
                        <td>
                            {{$ext->reRoundFloatNum($csatNet[$tempVariables][$department][$sta.'_'.$status['answer_id']]/ ($csatNet[$tempVariables][$department][$sta.'_t'] == 0 ? 1:$csatNet[$tempVariables][$department][$sta.'_t']) *100, 2)}}%
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        100%
                    </td>
                    @foreach ($actions[$department] as $action)
                        <td>
                            {{$ext->reRoundFloatNum($csatNet[$tempVariables][$department][$act.'_'.$action['answer_id']]/ ($csatNet[$tempVariables][$department][$act.'_t'] == 0? 1:$csatNet[$tempVariables][$department][$act.'_t']) *100, 2)}}%
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        100%
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <br/>
        <div class="red bolder" style="color: red; font-weight: bold;">
            <p>   2.2. Đối với CSAT 1,2 CLDV Truyền hình</p>
        </div>
        <div>
            <table width="100%">
                <thead>
                <tr style="background-color: #9BC2E6;">
                    <th>

                    </th>
                    <th colspan="{{count($statusesTv) + 1}}" style="text-align: center; vertical-align: central;">
                        Nguyên nhân ghi nhận của nhân viên CSKH
                    </th>
                    <th colspan="{{count($actions[$department]) + 1}}" style="text-align: center; vertical-align: central;">
                        Hành động xử lý của nhân viên CSKH
                    </th>
                </tr>
                <tr style="background-color: #BDD7EE;">
                    <th  style="text-align: center; vertical-align: central;">
                        Chi nhánh
                    </th>
                    @foreach ($statusesTv as $status)
                        <th  style="text-align: center; vertical-align: central;">
                            {{$status['answers_title']}}
                        </th>
                    @endforeach
                    <th  style="text-align: center; vertical-align: central;color: #FF0000;">
                        Tổng
                    </th>
                    @foreach ($actions[$department] as $action)
                        <th  style="text-align: center; vertical-align: central;">
                            {{$action['answers_title']}}
                        </th>
                    @endforeach
                    <th  style="text-align: center; vertical-align: central; color: #FF0000;">
                        Tổng
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tempZone as $location)
                    <?php
                    $keyZone = 'Vùng '.$location;
                    ?>
                    <tr>
                        <td>
                            {{$keyZone}}
                        </td>
                        @foreach ($statusesTv as $status)
                            <td>
                                {{$csatTv[$keyZone][$department][$sta.'_'.$status['answer_id']]}}
                            </td>
                            <?php
                            $csatTv[$tempVariables][$department][$sta.'_' . $status['answer_id']] += $csatTv[$keyZone][$department][$sta.'_' . $status['answer_id']];
                            ?>
                        @endforeach
                        <td style="color: #FF0000;">
                            {{$csatTv[$keyZone][$department][$sta.'_t']}}
                            <?php $csatTv[$tempVariables][$department][$sta.'_t'] += $csatTv[$keyZone][$department][$sta.'_t']; ?>
                        </td>
                        @foreach ($actions[$department] as $action)
                            <td>
                                {{$csatTv[$keyZone][$department][$act.'_'.$action['answer_id']]}}
                            </td>
                            <?php
                            $csatTv[$tempVariables][$department][$act.'_' . $action['answer_id']] += $csatTv[$keyZone][$department][$act.'_' . $action['answer_id']];
                            ?>
                        @endforeach
                        <td style="color: #FF0000;">
                            {{$csatTv[$keyZone][$department][$act.'_t']}}
                            <?php $csatTv[$tempVariables][$department][$act.'_t'] += $csatTv[$keyZone][$department][$act.'_t']; ?>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot >
                <tr style="background-color: #FFC000;">
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    @foreach ($statusesTv as $status)
                        <td>
                            {{$csatTv[$tempVariables][$department][$sta.'_'.$status['answer_id']]}}
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        {{$csatTv[$tempVariables][$department][$sta.'_t']}}
                    </td>
                    @foreach ($actions[$department] as $action)
                        <td>
                            {{$csatTv[$tempVariables][$department][$act.'_'.$action['answer_id']]}}
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        {{$csatTv[$tempVariables][$department][$act.'_t']}}
                    </td>
                </tr>
                <tr style="background-color: #FFC000;">
                    <td>
                        Tỷ lệ %
                    </td>
                    @foreach ($statusesTv as $status)
                        <td>
                            {{$ext->reRoundFloatNum($csatTv[$tempVariables][$department][$sta.'_'.$status['answer_id']]/ ($csatTv[$tempVariables][$department][$sta.'_t'] == 0 ? 1:$csatTv[$tempVariables][$department][$sta.'_t']) *100, 2)}}%
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        100%
                    </td>
                    @foreach ($actions[$department] as $action)
                        <td>
                            {{ $ext->reRoundFloatNum($csatTv[$tempVariables][$department][$act.'_'.$action['answer_id']]/ ($csatTv[$tempVariables][$department][$act.'_t'] == 0?1:$csatTv[$tempVariables][$department][$act.'_t']) *100 ,2)}}%
                        </td>
                    @endforeach
                    <td style="color: red;">
                        100%
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <br/>
        <div class="red bolder" style="color: red; font-weight: bold;">
            <p>3. Thống kê Nguyên nhân và Hành động xử lý CSAT 1,2 CLDV bởi nhân viên thu cước tại nhà<br/>   3.1. Đối với CSAT 1,2 CLDV Internet</p>
        </div>
        <?php $department = $cus; ?>
        <div>
            <table width="100%">
                <thead>
                <tr style="background-color: #9BC2E6;">
                    <th></th>
                    <th colspan="{{count($statusesNet) + 1}}" style="text-align: center; vertical-align: central;">
                        Nguyên nhân ghi nhận của nhân viên thu cước
                    </th>
                    <th colspan="{{count($actions[$department]) + 1}}" style="text-align: center; vertical-align: central;">
                        Hành động xử lý của nhân viên thu cước
                    </th>
                </tr>
                <tr style="background-color: #BDD7EE;">
                    <th  style="text-align: center; vertical-align: central;">
                        Chi nhánh
                    </th>
                    @foreach ($statusesNet as $status)
                        <th  style="text-align: center; vertical-align: central;">
                            {{$status['answers_title']}}
                        </th>
                    @endforeach
                    <th  style="text-align: center; vertical-align: central; color: #FF0000;">
                        Tổng
                    </th>
                    @foreach ($actions[$department] as $action)
                        <th  style="text-align: center; vertical-align: central;">
                            {{$action['answers_title']}}
                        </th>
                    @endforeach
                    <th  style="text-align: center; vertical-align: central; color: #FF0000;">
                        Tổng
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tempZone as $location)
                    <?php
                    $keyZone = 'Vùng '.$location;
                    ?>
                    <tr>
                        <td>
                            {{$keyZone}}
                        </td>
                        @foreach ($statusesNet as $status)
                            <td>
                                {{$csatNet[$keyZone][$department][$sta.'_'.$status['answer_id']]}}
                            </td>
                            <?php
                            $csatNet[$tempVariables][$department][$sta.'_' . $status['answer_id']] += $csatNet[$keyZone][$department][$sta.'_' . $status['answer_id']];
                            ?>
                        @endforeach
                        <td style="color: #FF0000;">
                            {{$csatNet[$keyZone][$department][$sta.'_t']}}
                            <?php $csatNet[$tempVariables][$department][$sta.'_t'] += $csatNet[$keyZone][$department][$sta.'_t']; ?>
                        </td>
                        @foreach ($actions[$department] as $action)
                            <td>
                                {{$csatNet[$keyZone][$department][$act.'_'.$action['answer_id']]}}
                            </td>
                            <?php
                            $csatNet[$tempVariables][$department][$act.'_' . $action['answer_id']] += $csatNet[$keyZone][$department][$act.'_' . $action['answer_id']];
                            ?>
                        @endforeach
                        <td style="color: #FF0000;">
                            {{$csatNet[$keyZone][$department][$act.'_t']}}
                            <?php $csatNet[$tempVariables][$department][$act.'_t'] += $csatNet[$keyZone][$department][$act.'_t']; ?>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr style="background-color: #FFC000;">
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    @foreach ($statusesNet as $status)
                        <td>
                            {{$csatNet[$tempVariables][$department][$sta.'_'.$status['answer_id']]}}
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        {{$csatNet[$tempVariables][$department][$sta.'_t']}}
                    </td>
                    @foreach ($actions[$department] as $action)
                        <td>
                            {{$csatNet[$tempVariables][$department][$act.'_'.$action['answer_id']]}}
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        {{$csatNet[$tempVariables][$department][$act.'_t']}}
                    </td>
                </tr>
                <tr style="background-color: #FFC000;">
                    <td>
                        Tỷ lệ %
                    </td>
                    @foreach ($statusesNet as $status)
                        <td>
                            {{$ext->reRoundFloatNum($csatNet[$tempVariables][$department][$sta.'_'.$status['answer_id']]/ ($csatNet[$tempVariables][$department][$sta.'_t'] == 0 ? 1:$csatNet[$tempVariables][$department][$sta.'_t']) *100, 2)}}%
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        100%
                    </td>
                    @foreach ($actions[$department] as $action)
                        <td>
                            {{$ext->reRoundFloatNum($csatNet[$tempVariables][$department][$act.'_'.$action['answer_id']]/ ($csatNet[$tempVariables][$department][$act.'_t'] == 0? 1:$csatNet[$tempVariables][$department][$act.'_t']) *100, 2)}}%
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        100%
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <br/>
        <div class="red bolder" style="color: red; font-weight: bold;">
            <p>   3.2. Đối với CSAT 1,2 CLDV Truyền hình</p>
        </div>
        <div>
            <table width="100%">
                <thead>
                <tr style="background-color: #9BC2E6;">
                    <th></th>
                    <th colspan="{{count($statusesTv) + 1}}" style="text-align: center; vertical-align: central;">
                        Nguyên nhân ghi nhận của nhân viên thu cước
                    </th>
                    <th colspan="{{count($actions[$department]) + 1}}" style="text-align: center; vertical-align: central;">
                        Hành động xử lý của nhân viên thu cước
                    </th>
                </tr>
                <tr style="background-color: #BDD7EE;">
                    <th  style="text-align: center; vertical-align: central;">
                        Chi nhánh
                    </th>
                    @foreach ($statusesTv as $status)
                        <th  style="text-align: center; vertical-align: central;">
                            {{$status['answers_title']}}
                        </th>
                    @endforeach
                    <th  style="text-align: center; vertical-align: central;color: #FF0000;">
                        Tổng
                    </th>
                    @foreach ($actions[$department] as $action)
                        <th  style="text-align: center; vertical-align: central;">
                            {{$action['answers_title']}}
                        </th>
                    @endforeach
                    <th  style="text-align: center; vertical-align: central; color: #FF0000;">
                        Tổng
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tempZone as $location)
                    <?php
                    $keyZone = 'Vùng '.$location;
                    ?>
                    <tr>
                        <td>
                            {{$keyZone}}
                        </td>
                        @foreach ($statusesTv as $status)
                            <td>
                                {{$csatTv[$keyZone][$department][$sta.'_'.$status['answer_id']]}}
                            </td>
                            <?php
                            $csatTv[$tempVariables][$department][$sta.'_' . $status['answer_id']] += $csatTv[$keyZone][$department][$sta.'_' . $status['answer_id']];
                            ?>
                        @endforeach
                        <td style="color: #FF0000;">
                            {{$csatTv[$keyZone][$department][$sta.'_t']}}
                            <?php $csatTv[$tempVariables][$department][$sta.'_t'] += $csatTv[$keyZone][$department][$sta.'_t']; ?>
                        </td>
                        @foreach ($actions[$department] as $action)
                            <td>
                                {{$csatTv[$keyZone][$department][$act.'_'.$action['answer_id']]}}
                            </td>
                            <?php
                            $csatTv[$tempVariables][$department][$act.'_' . $action['answer_id']] += $csatTv[$keyZone][$department][$act.'_' . $action['answer_id']];
                            ?>
                        @endforeach
                        <td style="color: #FF0000;">
                            {{$csatTv[$keyZone][$department][$act.'_t']}}
                            <?php $csatTv[$tempVariables][$department][$act.'_t'] += $csatTv[$keyZone][$department][$act.'_t']; ?>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot >
                <tr style="background-color: #FFC000;">
                    <td>
                        Tổng {{$textRegion}}
                    </td>
                    @foreach ($statusesTv as $status)
                        <td>
                            {{$csatTv[$tempVariables][$department][$sta.'_'.$status['answer_id']]}}
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        {{$csatTv[$tempVariables][$department][$sta.'_t']}}
                    </td>
                    @foreach ($actions[$department] as $action)
                        <td>
                            {{$csatTv[$tempVariables][$department][$act.'_'.$action['answer_id']]}}
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        {{$csatTv[$tempVariables][$department][$act.'_t']}}
                    </td>
                </tr>
                <tr style="background-color: #FFC000;">
                    <td>
                        Tỷ lệ %
                    </td>
                    @foreach ($statusesTv as $status)
                        <td>
                            {{$ext->reRoundFloatNum($csatTv[$tempVariables][$department][$sta.'_'.$status['answer_id']]/ ($csatTv[$tempVariables][$department][$sta.'_t'] == 0 ? 1:$csatTv[$tempVariables][$department][$sta.'_t']) *100, 2)}}%
                        </td>
                    @endforeach
                    <td style="color: #FF0000;">
                        100%
                    </td>
                    @foreach ($actions[$department] as $action)
                        <td>
                            {{ $ext->reRoundFloatNum($csatTv[$tempVariables][$department][$act.'_'.$action['answer_id']]/ ($csatTv[$tempVariables][$department][$act.'_t'] == 0?1:$csatTv[$tempVariables][$department][$act.'_t']) *100 ,2)}}%
                        </td>
                    @endforeach
                    <td style="color: red;">
                        100%
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<p><a href="https://cem.fpt.vn">Để xem chi tiết các trường hợp, Anh Chị vui lòng tải file Excel đính kèm và truy cập hệ thống Customer voice</a></p>
<p>Xin cảm ơn Anh Chị.</p>

</body>
</html>