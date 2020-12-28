<?php $transfile = 'report' ;
//        dump($survey_branches);die;
        ?>
<div class="text-center" style="padding: 10px">
    <?php
    if (empty($locationSelected)) {
        $textRegion = trans($transfile.'.AllBranch');
    } else {
        $textRegion = trans($transfile.'.Location') . ': '. implode(',', $locationSelected);
    }
    ?>
    <text x="910" text-anchor="middle" class="highcharts-title" zIndex="4" style="color:#333333;font-size:18px;fill:#333333;width:1756px;  font-family: 'Lucida Grande', 'Lucida Sans Unicode', Arial, Helvetica, sans-serif;" y="24">
    <span>{{trans($transfile.'.CsatNpsPointOfLocation')}}</span>
    <br/>
    <span x="910" dy="21">{{$textRegion}}</span>
    <br/>
    <span x="910" dy="21">{{trans($transfile.'.Date')}}: {{date('d/m/Y',strtotime($from_date)) .' - '. date('d/m/Y',strtotime($to_date))}}</span>
    </text>
</div>
<div id="container"></div>
<div id="container_surveyNPS"></div>
<div class="table-responsive">
    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        {{trans($transfile.'.StatisticalOfCSATNPSPointofLocation')}}
    </h3>
    <table id="table-generalNPSCSAT" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
        <thead>
            <tr>
                <th rowspan="3" class="text-center">{{trans($transfile.'.Location')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="3" class="text-center">{{trans($transfile.'.Maintenance')}}</th>
                <th rowspan="3" class="text-center">{{trans($transfile.'.NPS Points')}}</th>
            </tr>
            <tr>
                <th rowspan="2" class="text-center">{{trans($transfile.'.Saler')}}</th>
                <th rowspan="2" class="text-center">{{trans($transfile.'.Deployer')}}</th>
                <th class="text-center">{{trans($transfile.'.Rating Quality Service')}}</th>
                <th rowspan="2" class="text-center">{{trans($transfile.'.NPS Points')}}</th>

                <th rowspan="2" class="text-center">{{trans($transfile.'.MaintainanceStaff')}}</th>
                <th class="text-center">{{trans($transfile.'.Rating Quality Service')}}</th>
                <th rowspan="2" class="text-center">{{trans($transfile.'.NPS Points')}}</th>
            </tr>
            <tr>
                <th class="text-center">{{trans($transfile.'.Net')}}</th>
                <th class="text-center">{{trans($transfile.'.Net')}}</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if (!empty($survey)) {
                foreach ($survey as $val) {
                    ?>
                    <tr>
                        <td><span>{{$val->KhuVuc}}</span></td>
                        <td><span class="number">{{$val->NVKinhDoanh_AVGPoint}}</span></td>
                        <td><span class="number">{{$val->NVTrienKhai_AVGPoint}}</span></td>
                        <td><span class="number">{{$val->DGDichVu_Net_AVGPoint}}</span></td>
                        <td><span class="number">{{isset($npsRegionTK[$val->KhuVuc]) ?$npsRegionTK[$val->KhuVuc].'%' :'0%'}}</span></td>

                        <td><span class="number">{{$val->NVBaoTri_AVGPoint}}</span></td>
                        <td><span class="number">{{$val->DVBaoTri_Net_AVGPoint}}</span></td>
                        <td><span class="number">{{isset($npsRegion[$val->KhuVuc]) ?$npsRegion[$val->KhuVuc].'%' :'0%'}}</span></td>

                        <td><span class="number">{{isset($npsRegion[$val->KhuVuc]) ?$npsRegion[$val->KhuVuc].'%' :'0%'}}</span></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <?php
            if (!empty($arrCountry)) {
                foreach ($arrCountry as $val) {
                    ?>
                    <tr>
                        <td><span>{{trans($transfile.'.'.$val['KhuVuc'])}}</span></td>
                        <td><span class="number">{{$val['NVKinhDoanh_AVGPoint']}}</span></td>
                        <td><span class="number">{{$val['NVTrienKhai_AVGPoint']}}</span></td>
                        <td><span class="number">{{$val['DGDichVu_Net_AVGPoint']}}</span></td>
                        <td><span class="number">{{isset($npsCountryRegion['ToanQuocTK']) ?$npsCountryRegion['ToanQuocTK'].'%' :'0%'}}</span></td>

                        <td><span class="number">{{$val['NVBaoTri_AVGPoint']}}</span></td>
                        <td><span class="number">{{$val['DVBaoTri_Net_AVGPoint']}}</span></td>
                        <td><span class="number">{{isset($npsCountryRegion['ToanQuoc']) ?$npsCountryRegion['ToanQuoc'].'%' :'0%'}}</span></td>

                        <td><span class="number">{{isset($npsCountryRegion[$val['KhuVuc']]) ?$npsCountryRegion[$val['KhuVuc']].'%' :'0%'}}</span></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>

    </table>
    <div style="overflow: auto">
        <h3 class="header smaller lighter red">
            <i class="icon-table"></i>
            {{trans($transfile.'.CustomerFeedbacksandHandlingsolutionsofCCagentsforCSAT12ofInternetQualityService')}}
        </h3>
        <table id="table-CsatActionError" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
            <thead>
            <tr>
                <th rowspan="2" class="text-center">{{trans($transfile.'.Location')}}</th>
                <th colspan="13" class="text-center">{{trans($transfile.'.CustomerFeedbacksrecordedbyCCagents')}}</th>
                <th colspan="7" class="text-center">{{trans($transfile.'.HandlingsolutionsofCCagents')}}</th>
            </tr>
            <tr>
                <th class="text-center">{{trans($transfile.'.InternetIsNotStable')}}</th>
                <th class="text-center">{{trans($transfile.'.EquipmentError')}}</th>
                <th class="text-center">{{trans($transfile.'.VoiceError')}}</th>
                <th class="text-center">{{trans($transfile.'.WifiWeakNotStable')}}</th>
                <th class="text-center">{{trans($transfile.'.GameLagging')}}</th>
                <th class="text-center">{{trans($transfile.'.CannotUsingWifi')}}</th>
                <th class="text-center">{{trans($transfile.'.LoosingSignal')}}</th>
                <th class="text-center">{{trans($transfile.'.HaveSignalButCannotAccess')}}</th>
                <th class="text-center">{{trans($transfile.'.SlowInternet')}}</th>
                <th class="text-center">{{trans($transfile.'.SignalIsNotStableSignalLoosingIsUnderStandard')}}</th>
                <th class="text-center">{{trans($transfile.'.IntenationalInternetSlow')}}</th>
                <th class="text-center">{{trans($transfile.'.Other')}}</th>
                <th class="text-center">{{trans($transfile.'.Total')}}</th>

                <th class="text-center">{{trans($transfile.'.SorryCustomerAndClose')}}</th>
                <th class="text-center">{{trans($transfile.'.ForwardDepartment')}}</th>
                <th class="text-center">{{trans($transfile.'.CreatePrechecklist')}}</th>
                <th class="text-center">{{trans($transfile.'.CreateChecklist')}}</th>
                <th class="text-center">{{trans($transfile.'.CreateCLIndo')}}</th>
                <th class="text-center">{{trans($transfile.'.Other')}}</th>
                <th class="text-center">{{trans($transfile.'.Total')}}</th>
            </tr>
            </thead>

            <tbody>
            <?php
            if (!empty($csatFinalTotal)) {
            foreach ($csatFinalTotal as $val) {
            ?>
            <tr>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span>{{$val['Location'] == 'WholeCountry' || $val['Location'] == 'Rate (%)' ? trans($transfile.'.'.$val['Location']) : $val['Location']}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['InternetIsNotStable']) ? $val['InternetIsNotStable'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['EquipmentError']) ? $val['EquipmentError'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['VoiceError']) ? $val['VoiceError'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['WifiWeakNotStable']) ? $val['WifiWeakNotStable'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['GameLagging']) ? $val['GameLagging'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['CannotUsingWifi']) ? $val['CannotUsingWifi'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['LoosingSignal']) ? $val['LoosingSignal'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['HaveSignalButCannotAccess']) ? $val['HaveSignalButCannotAccess'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['SlowInternet']) ? $val['SlowInternet'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['SignalIsNotStableSignalLoosingIsUnderStandard']) ? $val['SignalIsNotStableSignalLoosingIsUnderStandard'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['IntenationalInternetSlow']) ? $val['IntenationalInternetSlow'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['OtherError']) ? $val['OtherError'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['TotalError']) ? $val['TotalError'] : 0}}</span></td>

                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['SorryCustomerAndClose']) ? $val['SorryCustomerAndClose'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['ForwardDepartment']) ? $val['ForwardDepartment'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['CreatePrechecklist']) ? $val['CreatePrechecklist'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['CreateChecklist']) ? $val['CreateChecklist'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['CreateCLIndo']) ? $val['CreateCLIndo'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['OtherAction']) ? $val['OtherAction'] : 0}}</span></td>
                <td class="{{($val['Location'] == 'Rate (%)' || $val['Location'] == 'WholeCountry') ?  'foot_average' : '' }}"><span class="number ">{{isset($val['TotalAction']) ? $val['TotalAction'] : 0}}</span></td>
            </tr>
            <?php
            }
            }
            ?>

            </tbody>

        </table>
    </div>

    <input type="hidden" id="typeReport" value="6">
</div>
<style type="text/css">
    .number {
        float:right;
    }
    .foot {
        font-weight: bolder;
        background-color: yellow;
    }
    .foot_average {
        font-weight: bolder;
        background-color: orange;
        color: red;
    }
    #table-generalNPSCSAT_wrapper, #npsGeneralBranches_wrapper
    {
        overflow: auto !important;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
    //table
    var tableGeneralNPSCSAT = $('#table-CsatActionError').dataTable({
    "aoColumns": [ null
<?php
for ($i = 1; $i <= 20; $i++) {
    echo ',null';
}
?>
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

        var tableCSATErrorAction = $('#table-generalNPSCSAT').dataTable({
            "aoColumns": [ null
                <?php
                for ($i = 1; $i <= 8; $i++) {
                    echo ',null';
                }
                ?>
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
    //charts
    // Build the chart
//    $('#container').highcharts({
//        chart: {
//            plotBackgroundColor: null,
//            plotBorderWidth: null,
//            plotShadow: false,
//            type: 'pie'
//        },
//        title: {
//            text: 'Báo cáo số lượng Survey'
//        },
//        tooltip: {
//            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
//        },
//        plotOptions: {
//            pie: {
//                allowPointSelect: true,
//                cursor: 'pointer',
//                dataLabels: {
//                    enabled: true,
//                    format: '<b>{point.name}</b>: {point.percentage:.1f}%',
//                    style: {
//                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
//                        fontSize: '13px'
//                    },
//                    connectorPadding: 0
//                }
//            }
//        },
//        credits: {
//            enabled: false
//        },
//        series: [{
//            name: 'Chiếm',
//            data: [
//                <?php
//                    foreach ($arrPercent as $resSurvey){ 
//                        echo json_encode($resSurvey).',';
//                    }
//                
?>//,
//            ]
//        }]
//    });

    });
</script>