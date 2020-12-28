<?php
$transfile = 'report';
?>
<br />
<div id="chartNPSStatistic"></div>
<div class="table-responsive">
    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        {{trans($transfile.'.Rating Point NPS Statistic')}}
    </h3>
    <table id="table-NPS" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
        <thead>
            <tr>
                <th rowspan="2" class="text-center">{{trans($transfile.'.Rating Point')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Maintenance')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Total')}}</th>

            </tr>
            <tr>
                <th>{{trans($transfile.'.Quantity')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>
                <th>{{trans($transfile.'.Quantity')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>
                <th>{{trans($transfile.'.Quantity')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if (!empty($survey)) {
                $sauTKPercent = $sauBTPercent =  $totalPercent = 0;
                $arrPercent = [];
                $arrTotalPercent = ['SauTK' => 0,
                    'SauBT' => 0,
                    'Total' => 0];
                foreach ($survey as $res) {
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
                        $arrPercent[] = ['name' => trans($transfile . '.Point') . ' ' . $res->answers_point, 'y' => $totalPercent];
                        $arrTotalPercent['Total'] += $totalPercent;
                    }
                    ?>
                    <tr>
                        <td><span>{{$res->answers_point}}</span></td>
                        <td><span class="number">{{$res->SauTK}}</span></td>
                        <td><span class="number">{{$sauTKPercent.'%'}}</span></td>
                        <td><span class="number">{{$res->SauBT}}</span></td>
                        <td><span class="number">{{$sauBTPercent.'%'}}</span></td>
                        <td><span class="number">{{$res->Total}}</span></td>
                        <td><span class="number">{{$totalPercent.'%'}}</span></td>

                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
        <?php if (!empty($survey)) { ?>
            <tfoot class="foot">
                <tr>
                    <td><span>{{trans($transfile.'.Total')}}</span></td>
                    <?php
                    if (!empty($total)) {
                        foreach ($total as $k => $val) {
                            ?>
                            <td><span class="number">{{$val}}</span></td>
                            <td><span class="number">{{((string)$arrTotalPercent[$k] === '99.99') ?'100%' :round($arrTotalPercent[$k]).'%'}}</span></td>
                            <?php
                        }
                    }
                    ?>
                </tr>
            </tfoot>
        <?php } ?>
    </table>
    <?php if (isset($flagView)) { ?>
        @include('report.npsReport',['survey' => $groupNPS, 'total' => $total,  'locationSelected' => $locationSelected])
    <?php } ?>
</div>
<style type="text/css">
    .number {
        float:right;
    }
    .foot {
        font-weight: bolder;
        background-color: yellow;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        //table
        var tableNPS = $('#table-NPS').dataTable({
            "aoColumns": [
                {"sType": 'numeric', "bSortable": false}, null, null, null, null, null, null
            ],
            "bJQueryUI": false,
            "oLanguage": {
                "sLengthMenu": "Hiển thị _MENU_ dòng mỗi trang",
                "sZeroRecords": "Không tìm thấy",
                "sInfo": "Có _START_ tới _END_ của _TOTAL_ bản ghi",
                "sInfoEmpty": "Có 0 tới 0 của 0 bản ghi",
                "sInfoFiltered": "(lọc từ _MAX_ tổng số bản ghi)",
                "sSearch": "Tìm kiếm"
            },
            "bFilter": false,
            "bLengthChange": false,
            "bPaginate": false,
            "bInfo": false,
            "bSort": false,
            'sDom': '"top"i'
        });
<?php if (!isset($viewFrom)) { ?>
            //charts
            Highcharts.setOptions({
                colors: ['#6b79c4', '#4fc5ea', '#d8dfe1', '#fad735', '#f2546e', '#4ec95e', '#197d2c', '#fa9b35', '#bd6bc4', '#34b7cf', '#3F51B5']
            });
            // Build the chart
            $('#chartNPSStatistic').highcharts({
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
                            region = "<?php echo mb_convert_case(trans($transfile.'.Location'), MB_CASE_TITLE, 'UTF-8').': '?>" + tempRegion;
                        var text = "<?php echo mb_convert_case(trans($transfile.'.RateNetPromoterScoreStatisticalNPSPoint'), MB_CASE_TITLE, 'UTF-8')?><br />" + region + "<br />{{trans($transfile.'.Date')}}: {{date('d/m/Y',strtotime($from_date))}} - {{date('d/m/Y',strtotime($to_date))}}";
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
                exporting: {enabled: true},
                series: [{
                        name: 'Chiếm',
                        data: [
    <?php
    if (!empty($arrPercent)) {
        foreach ($arrPercent as $resSurvey) {
            echo json_encode($resSurvey) . ',';
        }
    }
    ?>
                        ]
                    }]
            });
<?php } ?>
    });
</script>