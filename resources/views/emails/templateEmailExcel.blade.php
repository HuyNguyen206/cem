<?php
    use App\Component\ExtraFunction;
    $ext = new ExtraFunction();
    $titleTinPNC = ['1' => 'Sau bảo trì TIN','2' => 'Sau bảo trì TIN','3'=> 'Sau bảo trì TIN','4'=>'Sau bảo trì TIN/PNC','5'=>'Sau bảo trì PNC','6'=>'Sau bảo trì PNC','7'=> 'Sau bảo trì PNC'];
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>

    <body>
        <div>
            <div>
                <div>
                    <b>Thông báo từ hệ thống Custome Voice</b>
                    <br/>	
                    <b style="color: #FF0000;">Anh Chị vui lòng tải file Excel để xem báo cáo chi tiết các trường hợp khách hàng không hài lòng về chất lượng dịch vụ Internet/Truyền hình và hành động xử lý của nhân viên CSKH.</b>
                    <br/>
                    Hệ thống CEM - Customer Voice
                    <br/>
                    Tổng hợp CSAT 1, 2 Chất lượng dịch vụ Internet và Truyền hình
                    <br/>
                    Vùng {{$zone}} ngày {{$day}}
                    <br/>
                    <br/>
                </div>
                <div>
                    <b style="color: #FF0000;">1. Thống kê CSAT 1,2 CLDV theo các điểm tiếp xúc</b><br/>   
                    <b style="color: #FF0000;">1.1. Đối với CSAT 1,2 CLDV Internet</b><br/><br/>
                </div>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>

                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    Sau triển khai DirectSales
                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    Sau Triển khai TeleSales
                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    <?php echo $titleTinPNC[$zone]; ?>
                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    Sau Bảo trì INDO
                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    Tổng hợp
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    Chi nhánh
                                </th>
                                <?php for ($i = 0; $i <= 4; $i++) { ?>
                                    <th>
                                        CSAT 1
                                    </th>
                                    <th>
                                        CSAT 2
                                    </th>
                                    <th >
                                        Tổng
                                    </th>
                                    <th >
                                        Tỷ lệ % <br/>không hài lòng
                                    </th>
                                    <th >
                                        CSAT TB
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $arrayFilter = ['tk', 'tele', 'tin', 'indo', 'th'];
                            foreach ($locations as $location) {
                                if ($location->region == "Vùng " . $zone) {
                                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                                    $temp = explode(' - ', $location->name);
                                    ?>
                                    <tr>
                                        <td>
                                            {{$temp[0].$location->branchcode}}
                                        </td>
                                        <?php foreach ($arrayFilter as $filter) { ?>
                                            <td>{{$csatNet[$keyZone]['csat1_'.$filter]}}</td>
                                            <td>{{$csatNet[$keyZone]['csat2_'.$filter]}}</td>
                                            <td>{{$csatNet[$keyZone]['csat1_'.$filter]+$csatNet[$keyZone]['csat2_'.$filter]}}</td>
                                            <td>{{$ext->reRoundFloatNum($csatNet[$keyZone]['csat_tl_'.$filter]/($csatNet[$keyZone]['solan_tb_'.$filter]==0?1:$csatNet[$keyZone]['solan_tb_'.$filter]) * 100 , 2) / 100}}</td>
                                            <td>{{$ext->reRoundFloatNum($csatNet[$keyZone]['csat_tb_'.$filter]/($csatNet[$keyZone]['solan_tb_'.$filter]==0?1:$csatNet[$keyZone]['solan_tb_'.$filter]),2)}}</td>
                                        <?php } ?>
                                    </tr>

                                <?php }
                            } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    Tổng V{{$zone}}
                                </td>
                                <?php foreach ($arrayFilter as $filter) { ?>
                                    <td>{{$csatNet['Vùng '.$zone]['csat1_'.$filter]}}</td>
                                    <td>{{$csatNet['Vùng '.$zone]['csat2_'.$filter]}}</td>
                                    <td>{{$csatNet['Vùng '.$zone]['csat1_'.$filter]+$csatNet['Vùng '.$zone]['csat2_'.$filter]}}</td>
                                    <td>{{$ext->reRoundFloatNum($csatNet['Vùng '.$zone]['csat_tl_'.$filter]/($csatNet['Vùng '.$zone]['solan_tb_'.$filter]==0?1:$csatNet['Vùng '.$zone]['solan_tb_'.$filter]) * 100, 2) / 100}}</td>
                                    <td>{{$ext->reRoundFloatNum($csatNet['Vùng '.$zone]['csat_tb_'.$filter]/($csatNet['Vùng '.$zone]['solan_tb_'.$filter]==0?1:$csatNet['Vùng '.$zone]['solan_tb_'.$filter]),2)}}</td>
                                <?php } ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <b style="color: #FF0000;">1.2. Đối với CSAT 1,2 CLDV Truyền hình</b><br/><br/>
                </div>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>

                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    Sau triển khai DirectSales
                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    Sau Triển khai TeleSales
                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    <?php echo $titleTinPNC[$zone]; ?>
                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    Sau Bảo trì INDO
                                </th>
                                <th colspan="5" style="text-align: center; vertical-align: central;">
                                    Tổng hợp
                                </th>
                            </tr>
                            <tr>
                                <th >
                                    Chi nhánh
                                </th>
                                <?php for ($i = 0; $i <= 4; $i++) { ?>
                                    <th >
                                        CSAT 1
                                    </th>
                                    <th >
                                        CSAT 2
                                    </th>
                                    <th >
                                        Tổng
                                    </th>
                                    <th >
                                        Tỷ lệ % <br/>không hài lòng
                                    </th>
                                    <th >
                                        CSAT TB
                                    </th>

                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($locations as $location) {
                                if ($location->region == "Vùng " . $zone) {
                                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                                    $temp = explode(' - ', $location->name);
                                    ?>
                                    <tr>
                                        <td>
                                            {{$temp[0].$location->branchcode}}
                                        </td>
                                        <?php foreach ($arrayFilter as $filter) { ?>
                                            <td>{{$csatTv[$keyZone]['csat1_'.$filter]}}</td>
                                            <td>{{$csatTv[$keyZone]['csat2_'.$filter]}}</td>
                                            <td>{{$csatTv[$keyZone]['csat1_'.$filter]+$csatTv[$keyZone]['csat2_'.$filter]}}</td>
                                            <td>{{$ext->reRoundFloatNum($csatTv[$keyZone]['csat_tl_'.$filter]/($csatTv[$keyZone]['solan_tb_'.$filter]==0?1:$csatTv[$keyZone]['solan_tb_'.$filter]) * 100, 2) / 100}}</td>
                                            <td>{{$ext->reRoundFloatNum($csatTv[$keyZone]['csat_tb_'.$filter]/($csatTv[$keyZone]['solan_tb_'.$filter]==0?1:$csatTv[$keyZone]['solan_tb_'.$filter]),2)}}</td>
                                        <?php } ?>
                                    </tr>

                                <?php }
                            } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    Tổng V{{$zone}}
                                </td>
                                <?php foreach ($arrayFilter as $filter) { ?>
                                    <td>{{$csatTv['Vùng '.$zone]['csat1_'.$filter]}}</td>
                                    <td>{{$csatTv['Vùng '.$zone]['csat2_'.$filter]}}</td>
                                    <td>{{$csatTv['Vùng '.$zone]['csat1_'.$filter]+$csatTv['Vùng '.$zone]['csat2_'.$filter]}}</td>
                                    <td>{{$ext->reRoundFloatNum($csatTv['Vùng '.$zone]['csat_tl_'.$filter]/($csatTv['Vùng '.$zone]['solan_tb_'.$filter]==0?1:$csatTv['Vùng '.$zone]['solan_tb_'.$filter]) * 100, 2) / 100}}</td>
                                    <td>{{$ext->reRoundFloatNum($csatTv['Vùng '.$zone]['csat_tb_'.$filter]/($csatTv['Vùng '.$zone]['solan_tb_'.$filter]==0?1:$csatTv['Vùng '.$zone]['solan_tb_'.$filter]),2)}}</td>
                                <?php } ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div>
                    <b style="color: #FF0000;">2. Thống kê Nguyên nhân và Hành động xử lý CSAT 1,2 CLDV bởi nhân viên CSKH</b><br/>   
                    <b style="color: #FF0000;">2.1. Đối với CSAT 1,2 CLDV Internet</b><br/><br/>
                </div>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="<?php echo count($statusesNet) + 1; ?>" style="text-align: center; vertical-align: central;">
                                    Nguyên nhân ghi nhận của nhân viên CSKH
                                </th>
                                <th colspan="<?php echo count($actions) + 1; ?>" style="text-align: center; vertical-align: central;">
                                    Hành động xử lý của nhân viên CSKH
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    Chi nhánh
                                </th>
                                <?php foreach ($statusesNet as $status) { ?>
                                    <th>
                                        <?php echo $status->answers_title; ?>
                                    </th>
                                <?php } ?>
                                <th >
                                    Tổng
                                </th>
                                <?php foreach ($actions as $action) { ?>
                                    <th >
                                        <?php echo $action->answers_title; ?>
                                    </th>
                                <?php } ?>
                                <th >
                                    Tổng
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($locations as $location) {
                                if ($location->region == "Vùng " . $zone) {
                                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                                    $temp = explode(' - ', $location->name);
                                    ?>
                                    <tr>
                                        <td>
                                            {{$temp[0].$location->branchcode}}
                                        </td>
                                        <?php foreach ($statusesNet as $status) { ?>
                                            <td>
                                                {{$csatNet[$keyZone]['sta-'.$status->answer_id]}}
                                            </td>
                                        <?php } ?>
                                        <td>
                                            {{$csatNet[$keyZone]['sta-t']}}
                                        </td>
                                        <?php foreach ($actions as $action) { ?>
                                            <td>
                                                {{$csatNet[$keyZone]['act-'.$action->answer_id]}}
                                            </td>
                                        <?php } ?>
                                        <td>
                                            {{$csatNet[$keyZone]['act-t']}}
                                        </td>
                                    </tr>

                                <?php }
                            } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    Tổng V{{$zone}}
                                </td>
                                <?php foreach ($statusesNet as $status) { ?>
                                    <td>
                                        {{$csatNet['Vùng '.$zone]['sta-'.$status->answer_id]}}
                                    </td>
                                <?php } ?>
                                <td>
                                    {{$csatNet['Vùng '.$zone]['sta-t']}}
                                </td>
                                <?php foreach ($actions as $action) { ?>
                                    <td>
                                        {{$csatNet['Vùng '.$zone]['act-'.$action->answer_id]}}
                                    </td>
                                <?php } ?>
                                <td>
                                    {{$csatNet['Vùng '.$zone]['act-t']}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tỷ lệ %
                                </td>
                                <?php foreach ($statusesNet as $status) { ?>
                                    <td>
                                        {{$ext->reRoundFloatNum($csatNet['Vùng '.$zone]['sta-'.$status->answer_id]/ ($csatNet['Vùng '.$zone]['sta-t'] == 0 ? 1:$csatNet['Vùng '.$zone]['sta-t']) *100, 2) / 100}}
                                    </td>
                                <?php } ?>
                                <td>
                                    1
                                </td>
                                <?php foreach ($actions as $action) { ?>
                                    <td>
                                        {{$ext->reRoundFloatNum($csatNet['Vùng '.$zone]['act-'.$action->answer_id]/ ($csatNet['Vùng '.$zone]['act-t'] == 0? 1:$csatNet['Vùng '.$zone]['act-t']) *100, 2) / 100}}
                                    </td>
                                <?php } ?>
                                <td>
                                    1
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div>
                    <b style="color: #FF0000;">2.2. Đối với CSAT 1,2 CLDV Truyền hình</b><br/><br/>
                </div>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>

                                </th>
                                <th colspan="<?php echo count($statusesTv) + 1; ?>" style="text-align: center; vertical-align: central;">
                                    Nguyên nhân ghi nhận của nhân viên CSKH
                                </th>
                                <th colspan="<?php echo count($actions) + 1; ?>" style="text-align: center; vertical-align: central;">
                                    Hành động xử lý của nhân viên CSKH
                                </th>
                            </tr>
                            <tr>
                                <th >
                                    Chi nhánh
                                </th>
                                <?php foreach ($statusesTv as $status) { ?>
                                    <th >
                                        <?php echo $status->answers_title; ?>
                                    </th>
                                <?php } ?>
                                <th >
                                    Tổng
                                </th>
                                <?php foreach ($actions as $action) { ?>
                                    <th >
                                        <?php echo $action->answers_title; ?>
                                    </th>
                                <?php } ?>
                                <th >
                                    Tổng
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($locations as $location) {
                                if ($location->region == "Vùng " . $zone) {
                                    $keyZone = $location->id . '-' . (empty($location->branchcode) ? 0 : $location->branchcode);
                                    $temp = explode(' - ', $location->name);
                                    ?>
                                    <tr>
                                        <td>
                                            {{$temp[0].$location->branchcode}}
                                        </td>
                                        <?php foreach ($statusesTv as $status) { ?>
                                            <td>
                                                {{$csatTv[$keyZone]['sta-'.$status->answer_id]}}
                                            </td>
                                        <?php } ?>
                                        <td>
                                            {{$csatTv[$keyZone]['sta-t']}}
                                        </td>
                                        <?php foreach ($actions as $action) { ?>
                                            <td>
                                                {{$csatTv[$keyZone]['act-'.$action->answer_id]}}
                                            </td>
                                        <?php } ?>
                                        <td>
                                            {{$csatTv[$keyZone]['act-t']}}
                                        </td>
                                    </tr>

                                <?php }
                            } ?>
                        </tbody>
                        <tfoot >
                            <tr>
                                <td>
                                    Tổng V{{$zone}}
                                </td>
                                <?php foreach ($statusesTv as $status) { ?>
                                    <td>
                                        {{$csatTv['Vùng '.$zone]['sta-'.$status->answer_id]}}
                                    </td>
                                <?php } ?>
                                <td>
                                    {{$csatTv['Vùng '.$zone]['sta-t']}}
                                </td>
                                <?php foreach ($actions as $action) { ?>
                                    <td>
                                        {{$csatTv['Vùng '.$zone]['act-'.$action->answer_id]}}
                                    </td>
                                <?php } ?>
                                <td>
                                    {{$csatTv['Vùng '.$zone]['act-t']}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tỷ lệ %
                                </td>
                                <?php foreach ($statusesTv as $status) { ?>
                                    <td>
                                        {{$ext->reRoundFloatNum($csatTv['Vùng '.$zone]['sta-'.$status->answer_id]/ ($csatTv['Vùng '.$zone]['sta-t'] == 0 ? 1:$csatTv['Vùng '.$zone]['sta-t']) *100, 2) / 100}}
                                    </td>
                                <?php } ?>
                                <td>
                                    1
                                </td>
                                <?php foreach ($actions as $action) { ?>
                                    <td>
                                        {{ $ext->reRoundFloatNum($csatTv['Vùng '.$zone]['act-'.$action->answer_id]/ ($csatTv['Vùng '.$zone]['act-t'] == 0?1:$csatTv['Vùng '.$zone]['act-t']) *100 ,2) / 100}}
                                    </td>
                                <?php } ?>
                                <td>
                                    1
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                
                <p><a href="https://cem.fpt.vn">Để xem chi tiết các trường hợp, Anh Chị vui lòng tải file Excel đính kèm và truy cập hệ thống Customer voice</a></p>
                <p>Xin cảm ơn Anh Chị.</p>

            </div>
        </div>
    </body>
</html>