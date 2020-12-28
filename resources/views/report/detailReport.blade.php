<?php
$arrSectionConnected = [4 => 'MeetUser', 3 => 'DidntMeetUser', 2 => 'MeetCustomerCustomerDeclinedToTakeSurvey', 1 => 'CannotContact', 0 => 'NoNeedContact'];
$totalConnectedCus = 0;
$transfile = 'report'
?>
<div class="row" style="margin-left: 10px">
    <div class="col-xs-12">
        <h3 class="header smaller lighter red">
            <i class="icon-table"></i>
            {{trans($transfile.'.Survey Quality')}}
        </h3>
        <div class="col-xs-6">
            <div id="survey_quality"></div>
        </div>
        <div class="col-xs-6">
            <div class="table-responsive">
                <table id="sample-table-2" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
                    <thead>
                    <tr>
                        <th class="text-center">
                            {{trans($transfile.'.SurveyCareChannel')}}
                        </th>
                        <th colspan="6" class="text-center">
                           Happy Call
                        </th>
                    </tr>
                        <tr>
                            <th rowspan="2" class="text-center">{{trans($transfile.'.SurveyQuantityCSAT')}}</th>
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
                            $sauTKPercent  = $sauBTPercent = $totalPercent = 0;
                            $arrPercent = [];
                            $arrTotalPercent = ['sauTKPercent' => ['name' => 'Triển khai', 'y' => 0],
                                'sauBTPercent' => ['name' => 'Bảo trì', 'y' => 0],
                                'allPercent' => ['name' => 'Tổng', 'y' => 0]];
                            foreach ($survey as $res) {
                                if ($total['SauTK'] > 0) {
                                    $sauTKPercent = ($res->SauTK / $total['SauTK']) * 100;
                                    $sauTKPercent = round($sauTKPercent, 2);
                                    $arrTotalPercent['sauTKPercent']['y'] += $sauTKPercent;
                                }
                                if ($total['SauBT'] > 0) {
                                    $sauBTPercent = ($res->SauBT / $total['SauBT']) * 100;
                                    $sauBTPercent = round($sauBTPercent, 2);
                                    $arrTotalPercent['sauBTPercent']['y'] += $sauBTPercent;
                                }
                                if ($total['TongCong'] > 0) {
                                    $totalPercent = ($res->TongCong / $total['TongCong']) * 100;
                                    $totalPercent = round($totalPercent, 2);
                                    //tổng % của các kết quả survey
                                    $arrPercent[] = ['name' => trans('report.'.$arrSectionConnected[$res->KQSurvey]), 'y' => $totalPercent];
                                    $arrTotalPercent['allPercent']['y'] += $totalPercent;
                                }
                                ?>
                                <tr>
                                    <td><span>{{trans($transfile.'.'.$arrSectionConnected[$res->KQSurvey])}}</span></td>
                                    <td><span class="number">{{$res->SauTK}}</span></td>
                                    <td><span class="number">{{$sauTKPercent.'%'}}</span></td>

                                    <td><span class="number">{{$res->SauBT}}</span></td>
                                    <td><span class="number">{{$sauBTPercent.'%'}}</span></td>

                                    <td><span class="number">{{$res->TongCong}}</span></td>
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
                                <td><span class="number">{{$total['SauTK']}}</span></td>
                                <td><span class="number">{{((string)$arrTotalPercent['sauTKPercent']['y'] === '99.99') ?'100%' :round($arrTotalPercent['sauTKPercent']['y']).'%'}}</span></td>

                                <td><span class="number">{{$total['SauBT']}}</span></td>
                                <td><span class="number">{{((string)$arrTotalPercent['sauBTPercent']['y'] === '99.99') ?'100%' :round($arrTotalPercent['sauBTPercent']['y']).'%'}}</span></td>
                                
                                <td><span class="number">{{$total['TongCong']}}</span></td>
                                <td><span class="number">{{((string)$arrTotalPercent['allPercent']['y'] === '99.99') ?'100%' :round($arrTotalPercent['allPercent']['y']).'%'}}</span></td>
                            </tr>
                        </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-left: 10px">
    <div class="col-xs-12">
        <h3 class="header smaller lighter red">
            <i class="icon-table"></i>
            {{trans($transfile.'.SurveyNPS Quality')}}
        </h3>
        <div class="col-xs-6">
            <div id="surveyNPS_quality"></div>
        </div>
        <div class="col-xs-6">
            <div class="table-responsive">
                <table id="table_surveyNPS" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
                    <thead>
                    <tr>
                        <th class="text-center">
                            {{trans($transfile.'.SurveyCareChannel')}}
                        </th>
                        <th colspan="6" class="text-center">
                            Happy Call
                        </th>
                    </tr>
                        <tr>
                            <th rowspan="2" class="text-center">{{trans($transfile.'.QuantitySurveyNPS')}}</th>
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
                        if (!empty($surveyNPS)) {
                            $sauTKPercent =  $sauBTPercent = $totalPercent = 0;
                            $arrPercentNPS = [];
                            $arrTotalPercent = ['sauTKPercent' => ['name' => 'Triển khai', 'y' => 0],
                                'sauBTPercent' => ['name' => 'Bảo trì', 'y' => 0],
                                'allPercent' => ['name' => 'Tổng', 'y' => 0]];
                            foreach ($surveyNPS as $nps) {
                        if ($totalNPS['SauTK'] > 0) {
                            $sauTKPercent = ($nps->SauTK / $totalNPS['SauTK']) * 100;
                            $sauTKPercent = round($sauTKPercent, 2);
                            $arrTotalPercent['sauTKPercent']['y'] += $sauTKPercent;
                        }
                        if ($totalNPS['SauBT'] > 0) {
                            $sauBTPercent = ($nps->SauBT / $totalNPS['SauBT']) * 100;
                            $sauBTPercent = round($sauBTPercent, 2);
                            $arrTotalPercent['sauBTPercent']['y'] += $sauBTPercent;
                        }
                        if ($totalNPS['TongCong'] > 0) {
                            $totalPercent = ($nps->TongCong / $totalNPS['TongCong']) * 100;
                            $totalPercent = round($totalPercent, 2);
                            //tổng % của các kết quả survey
                            $arrPercentNPS[] = ['name' => trans('report.'.$nps->KQSurveyNPS), 'y' => $totalPercent];
                            $arrTotalPercent['allPercent']['y'] += $totalPercent;
                        }
                                ?>
                                <tr>
                                    <td><span>{{trans($transfile.'.'.$nps->KQSurveyNPS)}}</span></td>
                                    <td><span class="number">{{$nps->SauTK}}</span></td>
                                    <td><span class="number">{{$sauTKPercent.'%'}}</span></td>

                                    <td><span class="number">{{$nps->SauBT}}</span></td>
                                    <td><span class="number">{{$sauBTPercent.'%'}}</span></td>
                                    
                                    <td><span class="number">{{$nps->TongCong}}</span></td>
                                    <td><span class="number">{{$totalPercent.'%'}}</span></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>

                        <?php
                        if (!empty($surveyNPSNoRated)) {
                            $arrPercentNPSNoRated = [];
                            foreach ($surveyNPSNoRated as $npsNorated) {
                                if ($totalNPS['SauTK'] > 0) {
                                    $sauTKPercent = ($npsNorated->SauTK / $totalNPS['SauTK']) * 100;
                                    $sauTKPercent = round($sauTKPercent, 2);
                                    $arrTotalPercent['sauTKPercent']['y'] += $sauTKPercent;
                                }
                                if ($totalNPS['SauBT'] > 0) {
                                    $sauBTPercent = ($npsNorated->SauBT / $totalNPS['SauBT']) * 100;
                                    $sauBTPercent = round($sauBTPercent, 2);
                                    $arrTotalPercent['sauBTPercent']['y'] += $sauBTPercent;
                                }

                                if ($totalNPS['TongCong'] > 0) {
                                    $totalPercent = ($npsNorated->TongCong / $totalNPS['TongCong']) * 100;
                                    $totalPercent = round($totalPercent, 2);
                                    //tổng % của các kết quả survey
                                    $arrPercentNPSNoRated[] = ['name' => trans('report.'.$npsNorated->KQSurveyNPS), 'y' => $totalPercent];
                                    $arrTotalPercent['allPercent']['y'] += $totalPercent;
                                }
                                ?>
                                <tr>
                                    <td><span>{{trans($transfile.'.'.$npsNorated->KQSurveyNPS)}}</span></td>
                                    <td><span class="number">{{$npsNorated->SauTK}}</span></td>
                                    <td><span class="number">{{$sauTKPercent.'%'}}</span></td>
                                   
                                    <td><span class="number">{{$npsNorated->SauBT}}</span></td>
                                    <td><span class="number">{{$sauBTPercent.'%'}}</span></td>
                                    
                                    <td><span class="number">{{$npsNorated->TongCong}}</span></td>
                                    <td><span class="number">{{$totalPercent.'%'}}</span></td>
                                </tr>
                                <?php
                            }
                        }

                        if (!empty($surveyNPSNoRated_Note)) {
                            $arrPercentNPSNoRated_Note = [];
                            foreach ($surveyNPSNoRated_Note as $npsNorated_Note) {
                                $totalNorated_Note = $npsNorated_Note->TongCong;
                                if ($totalNPS['SauTK'] > 0) {
                                    $sauTKPercent = ($npsNorated_Note->SauTK / $totalNPS['SauTK']) * 100;
                                    $sauTKPercent = round($sauTKPercent, 2);
                                    $arrTotalPercent['sauTKPercent']['y'] += $sauTKPercent;
                                }

                                if ($totalNPS['SauBT'] > 0) {
                                    $sauBTPercent = ($npsNorated_Note->SauBT / $totalNPS['SauBT']) * 100;
                                    $sauBTPercent = round($sauBTPercent, 2);
                                    $arrTotalPercent['sauBTPercent']['y'] += $sauBTPercent;
                                }

                                if ($totalNPS['TongCong'] > 0) {
                                    $totalPercent = ($npsNorated_Note->TongCong / $totalNPS['TongCong']) * 100;
                                    $totalPercent = round($totalPercent, 2);
                                    //tổng % của các kết quả survey
                                    $arrPercentNPSNoRated_Note[] = ['name' => trans('report.'.$npsNorated_Note->KQSurveyNPS), 'y' => $totalPercent];
                                    $arrTotalPercent['allPercent']['y'] += $totalPercent;
                                }
                                ?>
                                <tr>
                                    <td><span>{{trans($transfile.'.'.$npsNorated_Note->KQSurveyNPS)}}</span></td>
                                    <td><span class="number">{{!empty($npsNorated_Note->SauTK) ?$npsNorated_Note->SauTK :0}}</span></td>
                                    <td><span class="number">{{$sauTKPercent.'%'}}</span></td>
                                  
                                    <td><span class="number">{{!empty($npsNorated_Note->SauBT) ?$npsNorated_Note->SauBT :0}}</span></td>
                                    <td><span class="number">{{$sauBTPercent.'%'}}</span></td>
                                    
                                    <td><span class="number">{{!empty($npsNorated_Note->TongCong) ?$npsNorated_Note->TongCong :0}}</span></td>
                                    <td><span class="number">{{$totalPercent.'%'}}</span></td>
                                </tr>
                                <?php
                            }
                        }
                        $totalNoRated = json_decode(json_encode($totalNoRated), true);
                        if ($totalNoRated['TongCong'] > 0) {
                            $arrPercentRated90 = [];
                            $name = 'CustomerRatedNotAskAgain';
                                $sauTKNoRatedPercent = $sauBTTINNoRatedPercent = $sauBTINDONoRatedPercent = 0;
                                if ($totalNPS['SauTK'] > 0) {
                                    $sauTKNoRatedPercent = ($totalNoRated['SauTK'] / $totalNPS['SauTK']) * 100;
                                    $sauTKNoRatedPercent = round($sauTKNoRatedPercent, 2);
                                    $arrTotalPercent['sauTKPercent']['y'] += $sauTKNoRatedPercent;
                                } else {
                                    $sauTKNoRatedPercent = 0;
                                    $arrTotalPercent['sauTKPercent']['y'] += $sauTKNoRatedPercent;
                                }

                                if ($totalNPS['SauBT'] > 0) {
                                    $sauBTNoRatedPercent = ($totalNoRated['SauBT'] / $totalNPS['SauBT']) * 100;
                                    $sauBTNoRatedPercent = round($sauBTNoRatedPercent, 2);
                                    $arrTotalPercent['sauBTPercent']['y'] += $sauBTNoRatedPercent;
                                } else {
                                    $sauBTNoRatedPercent = 0;
                                    $arrTotalPercent['sauBTPercent']['y'] += $sauBTNoRatedPercent;
                                }

                                if ($totalNPS['TongCong'] > 0) {
                                    $totalPercent = ($totalNoRated['TongCong'] / $totalNPS['TongCong']) * 100;
                                    $totalPercent = round($totalPercent, 2);
                                    //tổng % của các kết quả survey
                                    $arrPercentRated90[] = ['name' => trans('report.'.$name), 'y' => $totalPercent];
                                    $arrTotalPercent['allPercent']['y'] += $totalPercent;
                                } else {
                                    $totalPercent = 0;
                                    //tổng % của các kết quả survey
                                    $arrPercentRated90[] = ['name' => trans('report.'.$name), 'y' => $totalPercent];
                                    $arrTotalPercent['allPercent']['y'] += $totalPercent;
                                }
                            ?>    
                            <tr>
                                <td>{{trans($transfile.'.'.$name)}}</td>
                                <td><span class="number">{{$totalNoRated['SauTK']}}</span></td>
                                <td><span class="number">{{$sauTKNoRatedPercent.' %'}}</span></td>

                                <td><span class="number">{{$totalNoRated['SauBT']}}</span></td>
                                <td><span class="number">{{$sauBTNoRatedPercent.' %'}}</span></td>
                                
                                <td><span class="number">{{$totalNoRated['TongCong']}}</span></td>
                                <td><span class="number">{{$totalPercent.' %'}}</span></td>
                            </tr>
                        <?php }
                        ?>
                    </tbody>
                    <?php if (!empty($surveyNPS)) { ?>
                        <tfoot class="foot">
                            <tr>
                                <td><span>{{trans($transfile.'.Total')}}</span></td>
                                <td><span class="number">{{$totalNPS['SauTK']}}</span></td>
                                <td><span class="number">{{((string)$arrTotalPercent['sauTKPercent']['y'] === '99.99') ?'100%' :round($arrTotalPercent['sauTKPercent']['y']).'%'}}</span></td>
                               
                                <td><span class="number">{{$totalNPS['SauBT']}}</span></td>
                                <td><span class="number">{{((string)$arrTotalPercent['sauBTPercent']['y'] === '99.99') ?'100%' :round($arrTotalPercent['sauBTPercent']['y']).'%'}}</span></td>

                                <td><span class="number">{{$totalNPS['TongCong']}}</span></td>
                                <td><span class="number">{{((string)$arrTotalPercent['allPercent']['y'] === '99.99') ?'100%' :round($arrTotalPercent['allPercent']['y']).'%'}}</span></td>
                            </tr>
                        </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" id="typeReport" value="7">
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
        var oTable1 = $('#sample-table-2').dataTable({
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

        var oTable2 = $('#table_surveyNPS').dataTable({
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
        //charts
        Highcharts.setOptions({
            colors: ['#6b79c4', '#4fc5ea', '#d8dfe1', '#fad735', '#f2546e', '#4ec95e', '#197d2c', '#fa9b35', '#bd6bc4', '#34b7cf', '#3F51B5']
        });
        // Build the chart
        $('#survey_quality').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: (function () {
                    var region = '';
                    var tempRegion = "{{$region}}";
                    if (tempRegion === '' || tempRegion === '1,2,3,4,5,6,7')
                        region = "Toàn quốc";
                    else
                        region = "Vùng " + tempRegion;
                    var text = "Số lượng khảo sát CSAT<br />" + region + "<br />Thời gian: {{date('d/m/Y',strtotime($from_date))}} - {{date('d/m/Y',strtotime($to_date))}}";
<?php if (isset($viewFrom)) { ?>
                        //text = 'Số lượng khảo sát CSAT';
                        text = '';
<?php } ?>
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
                    },
                    size: '40%'
                }
            },
            credits: {
                enabled: false
            },
            exporting: {enabled: true},
            series: [{
                    name: '{{trans($transfile.'.Take')}}',
                    data: [
<?php
if (!empty($arrPercent)) {
    foreach ($arrPercent as $resSurvey) {
        echo json_encode($resSurvey) . ',';
    }
}
?>,
                    ]
                }]
        });

        $('#surveyNPS_quality').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: (function () {
                    var region = '';
                    var tempRegion = "{{$region}}";
                    if (tempRegion === '' || tempRegion === '1,2,3,4,5,6,7')
                        region = "Toàn quốc";
                    else
                        region = "Vùng " + tempRegion;
                    var text = "Số lượng khảo sát NPS<br />" + region + "<br />Thời gian: {{date('d/m/Y',strtotime($from_date))}} - {{date('d/m/Y',strtotime($to_date))}}";
<?php if (isset($viewFrom)) { ?>
                        //text = 'Số lượng khảo sát NPS';
                        text = '';
<?php } ?>
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
                            fontSize: '13px',
                            width: '225px'
                        },
                        connectorPadding: 0
                    },
                    size: '40%'
                }
            },
            credits: {
                enabled: false
            },
            exporting: {enabled: true},
            series: [{
                    name: '{{trans($transfile.'.Take')}}',
                    data: [
<?php
if (!empty($arrPercentNPS)) {
    foreach ($arrPercentNPS as $resSurveyNPS) {
        echo json_encode($resSurveyNPS) . ',';
    }
}
if (!empty($arrPercentNPSNoRated)) {
    foreach ($arrPercentNPSNoRated as $resSurveyNPSNoRated) {
        echo json_encode($resSurveyNPSNoRated) . ',';
    }
}
if (!empty($arrPercentNPSNoRated_Note)) {
    foreach ($arrPercentNPSNoRated_Note as $resSurveyNPSNoRated_Note) {
        echo json_encode($resSurveyNPSNoRated_Note) . ',';
    }
}
if (!empty($arrPercentRated90)) {
    foreach ($arrPercentRated90 as $resSurveyRated90) {
        echo json_encode($resSurveyRated90) . ',';
    }
}
?>,
                    ]
                }]
        });
    });
</script>