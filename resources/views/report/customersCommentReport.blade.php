<?php
$transfile = 'report';
//            dump($survey);die;
?>
<br />
<div id="container"></div>
<div id="container_surveyNPS"></div>
<div class="table-responsive">
    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        {{trans($transfile.'.Customer Comments')}}
    </h3>
    <table id="table-CusComments" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
        <thead>
            <tr>
                <th rowspan="2" class="headerSecond text-center">{{trans($transfile.'.Group Content')}}</th>
                <th rowspan="2" class="headerSecond text-center">{{trans($transfile.'.Content')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Maintenance')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Total')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Total By Group')}}</th>
            </tr>
            <tr>
                <th class="headerSecond">{{trans($transfile.'.Quantity')}}</th>
                <th class="headerSecond">{{trans($transfile.'.Percent')}}</th>
                <th class="headerSecond">{{trans($transfile.'.Quantity')}}</th>
                <th class="headerSecond">{{trans($transfile.'.Percent')}}</th>
                <th class="headerSecond">{{trans($transfile.'.Quantity')}}</th>
                <th class="headerSecond">{{trans($transfile.'.Percent')}}</th>
                <th class="headerSecond">{{trans($transfile.'.Quantity')}}</th>
                <th class="headerSecond">{{trans($transfile.'.Percent')}}</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if (!empty($survey)) {
                $sauTKPercent = $sauBTPercent = $totalPercent = 0;
                $arrPercent = [];
                $arrTotalPercent = ['SauTK' => 0,
                    'SauBT' => 0,
                    'Total' => 0];
                $temp = $t = '';
                foreach ($survey as $a) {// tạo mảng chứa tên nhóm answer
                    if ($a->answers_group_title != $t) {
                        $ansGroup[$a->answers_group_title] = 1;
                        $totalByGroup[$a->answers_group_title] = (int) $a->Total;
                    } else {
                        $ansGroup[$t] ++;
                        $totalByGroup[$a->answers_group_title] += $a->Total;
                    }
                    //% các nhóm answer
                    $totalByGroupPercent[$a->answers_group_title] = ($total['Total'] > 0) ? round(($totalByGroup[$a->answers_group_title] / $total['Total']) * 100, 2) : 0;
                    $t = $a->answers_group_title;
                }
                foreach ($survey as $res) {
//                    if ($res->answer_id != 200) {
                        if ($total['SauTK'] > 0) {
                            $sauTKPercent = ($res->SauTK / $total['SauTK']) * 100;
                            $sauTKPercent = round($sauTKPercent, 2);
                            $arrTotalPercent['SauTK'] += $sauTKPercent;
                        }
                       
                        if ($total['SauBT'] > 0) {
                            $sauBTPercent = ($res->SauBT / $total['SauBT']) * 100;
                            $sauBTPercent = round($sauBTPercent, 2);
                            $arrTotalPercent['SauBT'] += $sauBTPercent;
                        }
                       
                        if ($total['Total'] > 0) {
                            $totalPercent = ($res->Total / $total['Total']) * 100;
                            $totalPercent = round($totalPercent, 2);
                            //tổng % của các kết quả survey
                            $arrPercent[] = ['name' => $res->Content, 'y' => $totalPercent];
                            $arrTotalPercent['Total'] += $totalPercent;
                        }
                        ?>
                        <tr>
                            <?php if ($temp != $res->answers_group_title) { ?>
                                <td rowspan="{{$ansGroup[$res->answers_group_title]}}"><span><b>{{trans($transfile.'.'.$res->answers_group_title)}}</b></span></td>
                            <?php } ?>
                            <td><span>{{trans($transfile.'.'.$res->Content)}}</span></td>
                            <td><span class="number">{{$res->SauTK}}</span></td>
                            <td><span class="number">{{$sauTKPercent.'%'}}</span></td>
                            <td><span class="number">{{$res->SauBT}}</span></td>
                            <td><span class="number">{{$sauBTPercent.'%'}}</span></td>
                            <td><span class="number">{{$res->Total}}</span></td>
                            <td><span class="number">{{$totalPercent.'%'}}</span></td>
                            <?php if ($temp != $res->answers_group_title) { ?>
                                <td rowspan="{{$ansGroup[$res->answers_group_title]}}"><span class="number">{{$totalByGroup[$res->answers_group_title]}}</span></td>
                                <td rowspan="{{$ansGroup[$res->answers_group_title]}}"><span class="number">{{$totalByGroupPercent[$res->answers_group_title].'%'}}</span></td>
                            <?php } ?>
                        </tr>
                        <?php
                        $temp = $res->answers_group_title;
//                    }
                }
            }
            ?>
        </tbody>
        <?php if (!empty($survey)) { ?>
            <tfoot class="foot">
                <tr>
                    <td colspan="2"><span>{{trans($transfile.'.Total comment')}}</span></td>
                    <?php
                    if (!empty($total)) {
                        foreach ($total as $k => $val) {
                            ?>
                            <td><span class="number">{{$val}}</span></td>
                            <td><span class="number">100%</span></td>
                            <?php
                        }
                    }
                    ?>
                    <td><span class="number">{{!empty($total['Total']) ?$total['Total'] :0}}</span></td>
                    <td><span class="number">100%</span></td>
                </tr>
                <tr>
                    <td colspan="2"><span>{{trans($transfile.'.Total customer comment')}}</span></td>
                    <?php
                    if (!empty($totalCusComment)) {
                        foreach ($totalCusComment as $k => $val) {
                            $totalPercentComment[$k] = ($totalConsulted[$k] > 0) ? ($val / $totalConsulted[$k]) * 100 : 0;
                            $totalPercentComment[$k] = round($totalPercentComment[$k], 2);
                            ?>
                            <td><span class="number">{{$val}}</span></td>
                            <td><span class="number">{{((string)$totalPercentComment[$k] === '99.99') ?'100%' :round($totalPercentComment[$k], 2).'%'}}</span></td>
                            <?php
                        }
                    }
                    ?>
                    <td><span class="number">{{!empty($totalCusComment['Total']) ?$totalCusComment['Total'] :0}}</span></td>
                    <?php $totalCommentPercent = ($totalConsulted['Total'] > 0) ? round((($totalCusComment['Total'] / $totalConsulted['Total']) * 100), 2) : 0; ?>
                    <td><span class="number">{{((string)$totalCommentPercent === '99.99') ?'100%' :round($totalCommentPercent, 2).'%'}}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><span>{{trans($transfile.'.Total customer no comment')}}</span></td>
                    <?php
                    if (!empty($totalCusNoComment)) {
                        foreach ($totalCusNoComment as $k => $val) {
                            $totalPercentNoComment[$k] = ($totalConsulted[$k] > 0) ? ($val / $totalConsulted[$k]) * 100 : 0;
                            $totalPercentNoComment[$k] = round($totalPercentNoComment[$k], 2);
                            ?>
                            <td><span class="number">{{$val}}</span></td>
                            <td><span class="number">{{((string)$totalPercentNoComment[$k] === '99.99') ?'100%' :round($totalPercentNoComment[$k], 2).'%'}}</span></td>
                            <?php
                        }
                    }
                    ?>
                    <td><span class="number">{{!empty($totalCusNoComment['Total']) ?$totalCusNoComment['Total'] :0}}</span></td>
                    <?php $totalNoCommentPercent = ($totalConsulted['Total'] > 0) ? round((($totalCusNoComment['Total'] / $totalConsulted['Total']) * 100), 2) : 0; ?>
                    <td><span class="number">{{((string)$totalNoCommentPercent === '99.99') ?'100%' :round($totalNoCommentPercent, 2).'%'}}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><span>{{trans($transfile.'.Total consulted')}}</span></td>
                    <?php
                    if (!empty($totalConsulted)) {
                        foreach ($totalConsulted as $k => $val) {
                            ?>
                            <td><span class="number">{{$val}}</span></td>
                            <td><span class="number">100%</span></td>
                            <?php
                        }
                    }
                    ?>
                    <td><span class="number">{{!empty($totalConsulted['Total']) ?$totalConsulted['Total'] :0}}</span></td>
                    <td><span class="number">100%</span></td>
                </tr>
            </tfoot>
        <?php } ?>
    </table>

</div>
<?php
//Chuyển định dạng nhóm góp ý để chuyển sang json vẽ biểu đồ
$totalByGroupPercentJson = [];
foreach ($totalByGroupPercent as $key => $value) {
    array_push($totalByGroupPercentJson, ['name' => trans($transfile.'.'.$key), 'y' => $value]);
}
?>
<style type="text/css">
    .number {
        float:right;
    }
    .foot {
        font-weight: bolder;
        background-color: yellow;
    }
    #table-CusComments th.headerSecond:hover {
        color: #547ea8;
    }
    #table-CusComments th.headerSecond {
        color: #307ecc;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        //table
//    var tableCusComments = $('#table-CusComments').dataTable({
//        "aoColumns": [
//            {"sType": 'numeric', "bSortable": false}, null, null, null, null, null, null, null, null, null
//        ],
//        "bJQueryUI": false,
//        "oLanguage": {
//            "sLengthMenu": "Hiển thị _MENU_ dòng mỗi trang",
//            "sZeroRecords": "Không tìm thấy",
//            "sInfo": "Có _START_ tới _END_ của _TOTAL_ bản ghi",
//            "sInfoEmpty": "Có 0 tới 0 của 0 bản ghi",
//            "sInfoFiltered": "(lọc từ _MAX_ tổng số bản ghi)",
//            "sSearch": "Tìm kiếm"
//        },
//        "bFilter": false,
//        "bLengthChange": false,
//        "bPaginate": false,
//        "bInfo": false,
//        "bSort": false,
//        'sDom': '"top"i'
//    });
<?php if (!isset($viewFrom)) {
    ?>
            //charts
            // Build the chart
            $('#container').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: (function () {
                        var region = '';
                        var tempRegion = "{{implode(',', $locationSelected)}}";
                        if (tempRegion === '')
                            region = "<?php echo mb_convert_case(trans($transfile.'.WholeCountry'), MB_CASE_TITLE, 'UTF-8')?>";
                        else
                            region = "<?php echo mb_convert_case(trans($transfile.'.Location'), MB_CASE_TITLE, 'UTF-8')?>: " + tempRegion;
                        var text = "<?php echo mb_convert_case(trans($transfile.'.CustomerIdeaToImproveService'), MB_CASE_TITLE, 'UTF-8')?><br />" + region + "<br />{{trans($transfile.'.Date')}}: {{date('d/m/Y',strtotime($from_date))}} - {{date('d/m/Y',strtotime($to_date))}}";
                        return text;
                    })()
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.2f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                                fontSize: '13px'
                            },
                            connectorPadding: 0
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                        name: '{{trans($transfile.'.Take')}}',
                        data: [
    <?php
    if (!empty($totalByGroupPercentJson)) {
        foreach ($totalByGroupPercentJson as $resSurvey) {
            echo json_encode($resSurvey) . ',';
        }
    }
    ?>,
                        ]
                    }]
            });
<?php } ?>
    });
</script>