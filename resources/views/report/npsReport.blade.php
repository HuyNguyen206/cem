<?php $transfile = 'report';
?>
<br />
<div id="chartNPSReport"></div>
<div class="table-responsive">
    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        {{trans($transfile.'.Rating Point NPS')}}
    </h3>
    <table id="table-NPSReport" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
        <thead>
            <tr>
                <th rowspan="2" class="text-center">{{trans($transfile.'.Rating Point NPS Statistic')}}</th>
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
                $sauTKPercent = $sauBTPercent  = 0;
                $arrPercent = [];
                $npsPercent = [0 => ['SauTK' => 0, 'SauBT' => 0, 'Total' => 0],
                    1 => ['SauTK' => 0, 'SauBT' => 0, 'Total' => 0],
                    2 => ['SauTK' => 0, 'SauBT' => 0, 'Total' => 0]];
                $arrTotalPercent = ['SauTK' => 0,
                    'SauBT' => 0,
                    'Total' => 0];
                $i = 0;
                foreach ($survey as $res) {
                     $sauTKPercent = $sauBTPercent = $totalPercent = 0;
                    if ($total['SauTK'] > 0) {
                        $sauTKPercent = ($res['SauTK'] / $total['SauTK']) * 100;
                        $sauTKPercent = round($sauTKPercent, 2);
                        $arrTotalPercent['SauTK'] += $sauTKPercent;
                        $npsPercent[$i]['SauTK'] += $sauTKPercent;
                    }
                    if ($total['SauBT'] > 0) {
                        $sauBTPercent = ($res['SauBT'] / $total['SauBT']) * 100;
                        $sauBTPercent = round($sauBTPercent, 2);
                        $arrTotalPercent['SauBT'] += $sauBTPercent;
                        $npsPercent[$i]['SauBT'] += $sauBTPercent;
                    }
                    if ($total['Total'] > 0) {
                        $totalPercent = ($res['Total'] / $total['Total']) * 100;
                        $totalPercent = round($totalPercent, 2);
                        //tổng % của các kết quả survey
                        $arrPercent[] = ['name' => $res['type'], 'y' => $totalPercent];
                        $arrTotalPercent['Total'] += $totalPercent;
                        $npsPercent[$i]['Total'] += $totalPercent;
                    }
                    ?>
                    <tr>
                        <td><span>{{$res['type']}}</span></td>
                        <td><span class="number">{{$res['SauTK']}}</span></td>
                        <td><span class="number">{{$sauTKPercent.'%'}}</span></td>
                        <td><span class="number">{{$res['SauBT']}}</span></td>
                        <td><span class="number">{{$sauBTPercent.'%'}}</span></td>
                        <td><span class="number">{{$res['Total']}}</span></td>
                        <td><span class="number">{{$totalPercent.'%'}}</span></td>
                    </tr>
                    <?php
                    $i++;
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
                            if ($k != 'type') {
                                ?>
                                <td><span class="number">{{$val}}</span></td>
                                <td><span class="number">{{((string)$arrTotalPercent[$k] === '99.99') ?'100%' :round($arrTotalPercent[$k]).'%'}}</span></td>
                                <?php
                            }
                        }
                    }
                    ?>
                </tr>
                <tr class="foot_average">
                    <td><span>{{trans($transfile.'.NPS Points')}}</span></td>
                    <td colspan="2"><span class="number">{{$npsPercent[2]['SauTK'] - $npsPercent[0]['SauTK']}} %</span>
                    <td colspan="2"><span class="number">{{$npsPercent[2]['SauBT'] - $npsPercent[0]['SauBT']}} %</span>
                    <td colspan="2"><span class="number">{{$npsPercent[2]['Total'] - $npsPercent[0]['Total']}} %</span>
                </tr>
            </tfoot>
        <?php } ?>
    </table>
    <?php if (!isset($viewFrom)) { ?>
    <div>
        <h3 class="header smaller lighter red">
            <i class="icon-table"></i>
            {{trans($transfile.'.BranchNPS')}}
        </h3>
        <table id="table-NPSBranchReport" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
            <thead>
            <tr>
                <th class="text-center evaluate-cell" rowspan="2" style="color: #307ecc"> {{trans($transfile.'.Rating Point')}}
                @foreach($surveyBranch['allLocation'] as $value)
                    <th colspan="2" class="text-center">{{($value == 'WholeCountry') ?  trans($transfile.'.'.$value) : $value}}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($surveyBranch['allLocation'] as $value)
                    <th style="color: #307ecc">{{trans($transfile.'.Quantity')}}</th>
                    <th style="color: #307ecc">{{trans($transfile.'.Percent')}}</th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            <?php
            if(!empty($surveyBranch))
            {
            foreach ($surveyBranch['totalNPS'] as $key => $value)
            {
            ?>
            <tr class = '@if($key == 'Total') foot @endif' >
                <td>
                    {{trans($transfile.'.'.$key)}}
                </td>
                <?php
                foreach($surveyBranch['allLocation'] as $key2 => $value2)
                {
                ?>
                <td>
                    {{$value[$value2]}}
                </td>
                <td>
                    {{$value[$value2.'Percent']}}
                </td>

                <?php
                }
                ?>
            </tr>
            <?php
            }
            }
            ?>
            <tfoot class="foot">
            <tr class="foot_average">
                @if(!empty($surveyBranch))
                    <td><span>{{trans($transfile.'.NPS Points')}}</span></td>
                    @foreach($surveyBranch['allLocation'] as $value)
                        <td colspan="2"><span class="number">{{$surveyBranch['averagePoint']['AVG_'.$value]}}</span></td>
                    @endforeach
                @endif
            </tr>
            </tfoot>
        </table>
    </div>
    <?php } ?>
    <input type="hidden" id="typeReport" value="5">
</div>
<style type="text/css">
    .number {
        float:right;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        //table
        var tableNPSReport = $('#table-NPSReport').dataTable({
            "aoColumns": [
                {"sType": 'numeric', "bSortable": false}, null, null, null, null, null, null
            ],
            "bJQueryUI": false,
            "oLanguage": {
                "sLengthMenu": "{{trans($transfile.'.Display')}}",
                "sZeroRecords": "{{trans($transfile.'.NotFound')}}",
                "sInfo": "{{trans($transfile.'.HasStartToEndTotalRecord')}}",
                "sInfoEmpty": "{{trans($transfile.'.HasZeroToZeroRecord')}}",
                "sInfoFiltered": "{{trans($transfile.'.FilterFromMaxTotalRecord')}}()",
                "sSearch": "{{trans($transfile.'.Find')}}"
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
                colors: ['#f2546e', '#fad735', '#4ec95e', '#fad735', '#f2546e', '#4ec95e', '#197d2c', '#fa9b35', '#bd6bc4', '#34b7cf', '#3F51B5']
            });
            // Build the chart
            $('#chartNPSReport').highcharts({
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
                            region = "<?php echo mb_convert_case(trans($transfile.'.WholeCountry'), MB_CASE_TITLE, 'UTF-8')?>{{trans($transfile.'.WholeCountry')}}";
                        else
                            region = "<?php echo mb_convert_case(trans($transfile.'.Location'), MB_CASE_TITLE, 'UTF-8')?>: " + tempRegion;
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
    foreach ($arrPercent as $resSurvey) {
        echo json_encode($resSurvey) . ',';
    }
    ?>,
                        ]
                    }]
            });
<?php } ?>
    });
</script>