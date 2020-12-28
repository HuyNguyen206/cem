<?php $transfile = 'report';
//dump($result);die;
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<div>
    {{--<h3>--}}
        {{--<i></i>--}}
        {{--{{trans($transfile.'.Productivity')}}--}}
    {{--</h3>--}}

    <table border="1">
        <thead>
        <tr>
             <th colspan="17">  {{ mb_strtoupper(trans($transfile.'.Productivity'), 'UTF-8')}} </th>
        </tr>
        <tr>
            <th colspan="17"></th>
        </tr>
            <tr>
                <th colspan="1"></th>
                <th colspan="8" style="text-align: center;">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="8" style="text-align: center;" >{{trans($transfile.'.Maintenance')}}</th>
                {{--<th colspan="8" class="text-center">{{trans($transfile.'.Maintenance TIN-PNC')}}</th>--}}
                {{--<th colspan="8" class="text-center">{{trans($transfile.'.Maintenance INDO')}}</th>--}}
                {{--<th colspan="8" class="text-center">{{trans($transfile.'.After Sale Staff')}}</th>--}}
                   {{--<th colspan="8" class="text-center">{{trans($transfile.'.After Swap')}}</th>--}}
            </tr>
            <tr>
                {{--<th class="headerSecond">Vùng</th>--}}
                <th>{{trans($transfile.'.UserName')}}</th>

                <th>{{trans($transfile.'.TotalQuantityOfSurveys')}}</th>
                <th>{{trans($transfile.'.MeetUser')}}</th>
                <th>{{trans($transfile.".DidntMeetUser")}}</th>
                <th>{{trans($transfile.'.MeetCustomerCustomerDeclinedToTakeSurvey')}}</th>
                <th>{{trans($transfile.'.CannotContact')}}</th>
                <th>{{trans($transfile.'.NoNeedContact')}}</th>
                <th>{{trans($transfile.'.ContactSuccess')}}</th>
                <th>{{trans($transfile.'.ContactSuccessPercent')}}</th>


                <th>{{trans($transfile.'.TotalQuantityOfSurveys')}}</th>
                <th>{{trans($transfile.'.MeetUser')}}</th>
                <th>{{trans($transfile.".DidntMeetUser")}}</th>
                <th>{{trans($transfile.'.MeetCustomerCustomerDeclinedToTakeSurvey')}}</th>
                <th>{{trans($transfile.'.CannotContact')}}</th>
                <th>{{trans($transfile.'.NoNeedContact')}}</th>
                <th>{{trans($transfile.'.ContactSuccess')}}</th>
                <th>{{trans($transfile.'.ContactSuccessPercent')}}</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $temp = $t = '';
            if (!empty($result)) {
                $contact_STK = $contact_SBT = 0;
//                $total['HCM'] = $total['HNI'] =
                $total = ['TongKhaoSat_STK' => 0, 'GapNguoiSD_STK' => 0, 'KhongGapNguoiSD_STK' => 0, 'KHTuChoiCS_STK' => 0, 'KhongLienLacDuoc_STK' => 0, 'KhongCanLienHe_STK' => 0, 'LienLacDuoc_STK' => 0, 'TyLeLienLacDuoc_STK' => 0,
                    'TongKhaoSat_SBT' => 0, 'GapNguoiSD_SBT' => 0, 'KhongGapNguoiSD_SBT' => 0, 'KHTuChoiCS_SBT' => 0, 'KhongLienLacDuoc_SBT' => 0, 'KhongCanLienHe_SBT' => 0, 'LienLacDuoc_SBT' => 0,
                    'TyLeLienLacDuoc_SBT' => 0];
//                $regionGroup = ['HNI' => 0, 'HCM' => 0, '' => 0];
//            $regionGroup = ['' => 0];
                $totalContact_STK = $totalContact_SBT = 0;
                $i = 0;
//                foreach ($result as $a) {
//                    // tạo mảng chứa tên vùng
//                    $regionGroup[$a->region] ++;
//                }
                foreach ($result as $res) {
                    $i++;
                    $contact_STK = $res->GapNguoiSD_STK + $res->KhongGapNguoiSD_STK + $res->KHTuChoiCS_STK;
                    $contact_SBT = $res->GapNguoiSD_SBT + $res->KhongGapNguoiSD_SBT + $res->KHTuChoiCS_SBT;
//                    $contactBT_TIN = $res->GapNguoiSD_BT_TIN + $res->KhongGapNguoiSD_BT_TIN + $res->KHTuChoiCS_BT_TIN;
//                    $contactBT_INDO = $res->GapNguoiSD_BT_INDO + $res->KhongGapNguoiSD_BT_INDO + $res->KHTuChoiCS_BT_INDO;
//                    $contactSS = $res->GapNguoiSDSS + $res->KhongGapNguoiSDSS + $res->KHTuChoiCSSS;
//                    $contactSSW = $res->GapNguoiSDSSW + $res->KhongGapNguoiSDSSW + $res->KHTuChoiCSSSW;
                    
                    $contact_STK_Percent = ($res->TongKhaoSat_STK != 0) ? ($contact_STK / $res->TongKhaoSat_STK) * 100 : 0;
                    $contact_SBT_Percent = ($res->TongKhaoSat_SBT != 0) ? ($contact_SBT / $res->TongKhaoSat_SBT) * 100 : 0;
//                    $contactBT_TIN_Percent = ($res->TongKhaoSat_BT_TIN != 0) ? ($contactBT_TIN / $res->TongKhaoSat_BT_TIN) * 100 : 0;
//                    $contactBT_INDO_Percent = ($res->TongKhaoSat_BT_INDO != 0) ? ($contactBT_INDO / $res->TongKhaoSat_BT_INDO) * 100 : 0;
//                    $contactSSPercent = ($res->TongKhaoSatSS != 0) ? ($contactSS / $res->TongKhaoSatSS) * 100 : 0;
//                    $contactSSWPercent = ($res->TongKhaoSatSSW != 0) ? ($contactSSW / $res->TongKhaoSatSSW) * 100 : 0;
                    //total
//                    $total[$res->region]['TongKhaoSat'] += $res->TongKhaoSat;
//                    $total[$res->region]['GapNguoiSD'] += $res->GapNguoiSD;
//                    $total[$res->region]['KhongGapNguoiSD'] += $res->KhongGapNguoiSD;
//                    $total[$res->region]['KHTuChoiCS'] += $res->KHTuChoiCS;
//                    $total[$res->region]['KhongLienLacDuoc'] += $res->KhongLienLacDuoc;
//                    $total[$res->region]['KhongCanLienHe'] += $res->KhongCanLienHe;
//                    $total[$res->region]['LienLacDuoc'] += $contact;
//
//                    $total[$res->region]['TongKhaoSatTS'] += $res->TongKhaoSatTS;
//                    $total[$res->region]['GapNguoiSDTS'] += $res->GapNguoiSDTS;
//                    $total[$res->region]['KhongGapNguoiSDTS'] += $res->KhongGapNguoiSDTS;
//                    $total[$res->region]['KHTuChoiCSTS'] += $res->KHTuChoiCSTS;
//                    $total[$res->region]['KhongLienLacDuocTS'] += $res->KhongLienLacDuocTS;
//                    $total[$res->region]['KhongCanLienHeTS'] += $res->KhongCanLienHeTS;
//                    $total[$res->region]['LienLacDuocTS'] += $contactTS;
            $total['TongKhaoSat_STK'] += $res->TongKhaoSat_STK;
            $total['GapNguoiSD_STK'] += $res->GapNguoiSD_STK;
            $total['KhongGapNguoiSD_STK'] += $res->KhongGapNguoiSD_STK;
            $total['KHTuChoiCS_STK'] += $res->KHTuChoiCS_STK;
            $total['KhongLienLacDuoc_STK'] += $res->KhongLienLacDuoc_STK;
            $total['KhongCanLienHe_STK'] += $res->KhongCanLienHe_STK;
            $total['LienLacDuoc_STK'] += $contact_STK;

            $total['TongKhaoSat_SBT'] += $res->TongKhaoSat_SBT;
            $total['GapNguoiSD_SBT'] += $res->GapNguoiSD_SBT;
            $total['KhongGapNguoiSD_SBT'] += $res->KhongGapNguoiSD_SBT;
            $total['KHTuChoiCS_SBT'] += $res->KHTuChoiCS_SBT;
            $total['KhongLienLacDuoc_SBT'] += $res->KhongLienLacDuoc_SBT;
            $total['KhongCanLienHe_SBT'] += $res->KhongCanLienHe_SBT;
            $total['LienLacDuoc_SBT'] += $contact_SBT;

                   
                    ?>
                    <tr>
                        <td><span>{{$res->section_user_name or ''}}</span></td>
                        <td><span>{{$res->TongKhaoSat_STK or 0}}</span></td>
                        <td><span>{{$res->GapNguoiSD_STK or 0}}</span></td>
                        <td><span>{{$res->KhongGapNguoiSD_STK or 0}}</span></td>
                        <td><span>{{$res->KHTuChoiCS_STK or 0}}</span></td>
                        <td><span >{{$res->KhongLienLacDuoc_STK or 0}}</span></td>
                        <td><span >{{$res->KhongCanLienHe_STK or 0}}</span></td>
                        <td><span >{{$contact_STK}}</span></td>
                        <td><span >{{number_format(round($contact_STK_Percent, 2),2).'%'}}</span></td>

                        <td><span >{{$res->TongKhaoSat_SBT or 0}}</span></td>
                        <td><span >{{$res->GapNguoiSD_SBT or 0}}</span></td>
                        <td><span >{{$res->KhongGapNguoiSD_SBT or 0}}</span></td>
                        <td><span >{{$res->KHTuChoiCS_SBT or 0}}</span></td>
                        <td><span >{{$res->KhongLienLacDuoc_SBT or 0}}</span></td>
                        <td><span >{{$res->KhongCanLienHe_SBT or 0}}</span></td>
                        <td><span >{{$contact_SBT}}</span></td>
                        <td><span >{{number_format(round($contact_SBT_Percent, 2),2).'%'}}</span></td>
                    </tr>
                    <?php if ($i >= count($result)) { ?>
                        <tr class="totalAttribute">
                            <td colspan="1">{{trans($transfile.'.Total')}}</td>
                            <td><span >{{$total['TongKhaoSat_STK'] or 0}}</span></td>
                            <td><span >{{$total['GapNguoiSD_STK'] or 0}}</span></td>
                            <td><span >{{$total['KhongGapNguoiSD_STK'] or 0}}</span></td>
                            <td><span >{{$total['KHTuChoiCS_STK'] or 0}}</span></td>
                            <td><span >{{$total['KhongLienLacDuoc_STK'] or 0}}</span></td>
                            <td><span >{{$total['KhongCanLienHe_STK'] or 0}}</span></td>
                            <td><span >{{$total['LienLacDuoc_STK'] or 0}}</span></td>
                            <td><span >{{($total['TongKhaoSat_STK'] > 0) ? number_format(round(($total['LienLacDuoc_STK']/$total['TongKhaoSat_STK'])*100, 2), 2).'%' :'0%'}}</span></td>

                            <td><span >{{$total['TongKhaoSat_SBT'] or 0}}</span></td>
                            <td><span >{{$total['GapNguoiSD_SBT'] or 0}}</span></td>
                            <td><span >{{$total['KhongGapNguoiSD_SBT'] or 0}}</span></td>
                            <td><span >{{$total['KHTuChoiCS_SBT'] or 0}}</span></td>
                            <td><span >{{$total['KhongLienLacDuoc_SBT'] or 0}}</span></td>
                            <td><span >{{$total['KhongCanLienHe_SBT'] or 0}}</span></td>
                            <td><span >{{$total['LienLacDuoc_SBT'] or 0}}</span></td>
                            <td><span >{{($total['TongKhaoSat_SBT'] > 0) ? number_format(round(($total['LienLacDuoc_SBT']/$total['TongKhaoSat_SBT'])*100, 2), 2).'%' :'0%'}}</span></td>
                        </tr>
                        <?php
                        $i = 0;
                    }
//                    $temp = $res->region;
                }
            }
            ?>
        </tbody>
    </table>
</div>
<style>
    .totalAttribute, .totalAttribute td, .totalAttribute td span
    {
        font-weight: bold;
    }
</style>
</body>
</html>
