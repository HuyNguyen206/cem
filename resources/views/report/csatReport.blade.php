<?php
$transfile = 'report';
$note = ['CSAT_12' => 'CSAT 1 & 2',
    'CSAT_3' => 'CSAT 3',
    'CSAT_45' => 'CSAT 4 & 5'];
$arrTotalPercent = $arrUnsatisfiedPercent = $arrNeutralPercent = $arrSatisfiedPercent = [
    'NVKinhDoanh' => 0,
    'NVTrienKhai' => 0,
    'DGDichVu_Net' => 0,

//    'DGDichVu_TV' => 0,
//    'NVKinhDoanhTS' => 0,
//    'NVTrienKhaiTS' => 0,
//    'DGDichVuTS_Net' => 0,
//    'DGDichVuTS_TV' => 0,
    'NVBaoTri' => 0,
    'DVBaoTri_Net' => 0,
//    'DVBaoTriTIN_TV' => 0,
//    'DVBaoTriINDO_Net' => 0,
//    'DVBaoTriINDO_TV' => 0,
//    'NVThuCuoc' => 0,
//    'DGDichVu_MobiPay_Net' => 0,
//    'DGDichVu_MobiPay_TV' => 0,
//    'DGDichVu_Counter' => 0,
//    'NV_Counter' => 0,
//    'NVKinhDoanhSS' => 0,
//    'NVTrienKhaiSS' => 0,
//    'DGDichVuSS_Net' => 0,
//    'DGDichVuSS_TV' => 0,
//    'NVBT_SSW' => 0,
//    'DGDichVuSSW_Net' => 0,
//    'DGDichVuSSW_TV' => 0,
//    'NVBT_SSW' => 0,
//    'DGDichVuSSW_Net' => 0,
//    'DGDichVuSSW_TV' => 0
];
//var_dump($survey);
//die;
?>
<div class="table-responsive">
    <?php if (!isset($viewFrom)) { ?>
        <div class="text-center" style=": 10px">
            <?php
            if (empty($locationSelected)) {
                $textLocation= trans($transfile.'.AllBranch');
            } else {
                $textLocation = trans($transfile.'.Location') . ': '. implode(',', $locationSelected);
            }
//            $name = '';
//            foreach ($branch as $v){arrayResultCombine
//                $name .= $v->name.', ';
//            }
//            $name = substr($name, 0, strlen($name) - 2);
//            $textBranches = 'Chi nhánh: '.$name;
            ?>
            <text x="910" text-anchor="middle" class="highcharts-title" zIndex="4" style="color:#333333;font-size:18px;fill:#333333;width:1756px;  font-family: 'Lucida Grande', 'Lucida Sans Unicode', Arial, Helvetica, sans-serif;" y="24">
            <span>{{trans($transfile.'.RateSatisfactionOfCustomer')}}</span>
            <br/>
            <span x="910" dy="21">{{$textLocation}}</span>
            <br/>
    <!--            <span x="910" dy="21"></span>
            <br/>-->
            <span x="910" dy="21">{{trans($transfile.'.Date')}}: {{date('d/m/Y',strtotime($from_date)) .' - '. date('d/m/Y',strtotime($to_date))}}</span>
            </text>
        </div>
        {{--<div class="row">--}}
            <div class="col-xs-12">
                <p>
                    <span class="label label-info" style="background-color:#f2546e !important " >&emsp;</span>&emsp;{{$note['CSAT_12']}}&emsp;
                    <span class="label label-warning" style="background-color:#fad735  !important" >&emsp;</span>&emsp;{{$note['CSAT_3']}}&emsp;
                    <span class="label label-success" style="background-color:#4ec95e  !important" >&emsp;</span>&emsp;{{$note['CSAT_45']}}&emsp;
                </p>
            </div>
        {{--</div>--}}
        {{--<div class="row">--}}
            {{--<div class="row">--}}
                <div class="col-xs-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">{{trans($transfile.'.Deployment')}}</div>
                        <div class="panel-body" style="padding: 0; ">
                            <div class="col-xs-4" style="padding: 0;text-align: center;">
                                <div id="chartCSAT" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Saler Rating')}}</label>
                            </div>
                            <div class="col-xs-4" style="padding: 0;text-align: center;">
                                <div id="chartCSAT2" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Deployer Rating')}}</label>
                            </div>
                            <div class="col-xs-4" style="padding: 0;text-align: center;">
                                <div id="chartCSAT3" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Net')}}</label>
                            </div>
                        </div>
                    </div>
                </div>

            <!--
                        <div class="col-xs-2">
                            
                        </div>-->

            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.Maintenance')}}</div>
                    <div class="panel-body" style="padding: 0">
                        <div class="col-xs-6" style="padding: 0;text-align: center;">
                            <div id="chartCSAT5" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.MaintainanceStaff')}}</label>
                        </div>
                        <div class="col-xs-6" style="padding: 0;text-align: center;">
                            <div id="chartCSAT6" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.MaintainanceNet')}}</label>
                        </div>
                    </div>
                </div>
            </div>

            {{--<div class="col-xs-6">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">{{trans($transfile.'.Maintenance INDO')}}</div>--}}
                    {{--<div class="panel-body" style="padding: 0">--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT8" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.Maintainance Employer')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT9" style="height: 200px;" ></div>--}}
                            {{--<label>{{trans($transfile.'.Net')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT10" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.TV')}}</label>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-xs-6">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">{{trans($transfile.'.Maintenance MobiPay')}}</div>--}}
                    {{--<div class="panel-body" style="padding: 0">--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;margin-bottom: 60px;">--}}
                            {{--<div id="chartCSAT11" style="height: 200px;" ></div>--}}
                            {{--<label>{{trans($transfile.'.Net')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT12" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.TV')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT17" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.TC')}}</label>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="col-xs-6">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">{{trans($transfile.'.After Paid Counter')}}</div>--}}
                    {{--<div class="panel-body" style="padding: 0">--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;margin-bottom: 60px;">--}}
                            {{--<div id="chartCSAT19" style="height: 200px;" ></div>--}}
                            {{--<label>{{trans($transfile.'.Transaction Staff Counter')}}</label>--}}
                        {{--</div>   --}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;margin-bottom: 60px;">--}}
                            {{--<div id="chartCSAT18" style="height: 200px;" ></div>--}}
                            {{--<label>{{trans($transfile.'.Quality Service')}}</label>--}}
                        {{--</div>    --}}

                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-xs-12">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">{{trans($transfile.'.After Sale Staff')}}</div>--}}
                    {{--<div class="panel-body" style="padding: 0; ">--}}
                        {{--<div class="col-xs-3" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT20" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.Saler Rating')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-3" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT21" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.Deployer Rating')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-3" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT22" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.Net')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-3" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT23"  style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.TV')}}</label>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-xs-6">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">{{trans($transfile.'.After Swap')}}</div>--}}
                    {{--<div class="panel-body" style="padding: 0">--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT24" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.SSW')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT25" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.Net')}}</label>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4" style="padding: 0;text-align: center;">--}}
                            {{--<div id="chartCSAT26" style="height: 200px;"></div>--}}
                            {{--<label>{{trans($transfile.'.TV')}}</label>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}


    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        {{trans($transfile.'.SatisfactionOfCustomerStatistical')}}
    </h3>

<?php } ?>
<table id="table-CSATReport" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%" style="max-width: 100%;">
    <thead>
        <tr>
            <th class="text-center" >{{trans($transfile.'.TouchPoint')}}</th>
            <th colspan="6" class="text-center">{{trans($transfile.'.Deployment')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Maintenance')}}</th>
        </tr>
        <tr>
            <th rowspan="3" class="text-center evaluate-cell">{{trans($transfile.'.Rating Point')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Saler')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Deployer')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Rating Quality Service')}}</th>

            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.MaintainanceStaff')}}</th>
            <th colspan="2"  class="text-center">{{trans($transfile.'.Rating Quality Service')}}</th>

        </tr>
        <tr>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
        </tr>
        <tr>
            <th>{{trans($transfile.'.Quantity')}}</th>
            <th>{{trans($transfile.'.Percent')}}</th>
            <th>{{trans($transfile.'.Quantity')}}</th>
            <th>{{trans($transfile.'.Percent')}}</th>
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
//            $NVKinhDoanhTSPercent = $NVTrienKhaiTSPercent = $DGDichVuTS_Net_Percent = $DGDichVuTS_TV_Percent = $NVKinhDoanhPercent = $NVTrienKhaiPercent = $DGDichVu_Net_Percent = $DGDichVu_TV_Percent = $NVBaoTriTINPercent = $NVBaoTriINDOPercent = $DVBaoTriTIN_Net_Percent = $DVBaoTriTIN_TV_Percent = $DVBaoTriINDO_Net_Percent = $DVBaoTriINDO_TV_Percent = $DGDichVu_MobiPay_Net_Percent = $DGDichVu_MobiPay_TV_Percent = $NVThuCuoc_Percent = $DGDichVu_Counter_Percent = $NV_Counter_Percent = $NVKinhDoanhSSPercent = $NVTrienKhaiSSPercent = $DGDichVuSS_Net_Percent = $DGDichVuSS_TV_Percent = $NVBTSSWPercent = $DGDichVuSSW_Net_Percent = $DGDichVuSSW_TV_Percent = 0;
        $NVKinhDoanhPercent = $NVTrienKhaiPercent = $DGDichVu_Net_Percent  = $NVBaoTriPercent =  $DVBaoTri_Net_Percent = 0;
            $arrCSAT = $arrCSATX = [];
            $emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
            foreach ($survey as $res) {
                ?>
                <tr>
                    <td><span>{{trans($transfile.'.'.trim($res->DanhGia))}} <img src="{{asset("assets/img/".$emotions[$res->answers_point])}}" style="width: 25px;height: 25px;float: right;"></span></td>
                    <?php
                    if ($total['NVKinhDoanh'] > 0) {
                        $NVKinhDoanhPercent = ($res->NVKinhDoanh / $total['NVKinhDoanh']) * 100;
                        $NVKinhDoanhPercent = round($NVKinhDoanhPercent, 2);
                        $arrTotalPercent['NVKinhDoanh'] += $NVKinhDoanhPercent;
                    }
                    if ($total['NVTrienKhai'] > 0) {
                        $NVTrienKhaiPercent = ($res->NVTrienKhai / $total['NVTrienKhai']) * 100;
                        $NVTrienKhaiPercent = round($NVTrienKhaiPercent, 2);
                        $arrTotalPercent['NVTrienKhai'] += $NVTrienKhaiPercent;
                    }
                    if ($total['DGDichVu_Net'] > 0) {
                        $DGDichVu_Net_Percent = ($res->DGDichVu_Net / $total['DGDichVu_Net']) * 100;
                        $DGDichVu_Net_Percent = round($DGDichVu_Net_Percent, 2);
                        $arrTotalPercent['DGDichVu_Net'] += $DGDichVu_Net_Percent;
                    }
//                    if ($total['DGDichVu_TV'] > 0) {
//                        $DGDichVu_TV_Percent = ($res->DGDichVu_TV / $total['DGDichVu_TV']) * 100;
//                        $DGDichVu_TV_Percent = round($DGDichVu_TV_Percent, 2);
//                        $arrTotalPercent['DGDichVu_TV'] += $DGDichVu_TV_Percent;
//                    }
//                    if ($total['NVKinhDoanhTS'] > 0) {
//                        $NVKinhDoanhTSPercent = ($res->NVKinhDoanhTS / $total['NVKinhDoanhTS']) * 100;
//                        $NVKinhDoanhTSPercent = round($NVKinhDoanhTSPercent, 2);
//                        $arrTotalPercent['NVKinhDoanhTS'] += $NVKinhDoanhTSPercent;
//                    }
//                    if ($total['NVTrienKhaiTS'] > 0) {
//                        $NVTrienKhaiTSPercent = ($res->NVTrienKhaiTS / $total['NVTrienKhaiTS']) * 100;
//                        $NVTrienKhaiTSPercent = round($NVTrienKhaiTSPercent, 2);
//                        $arrTotalPercent['NVTrienKhaiTS'] += $NVTrienKhaiTSPercent;
//                    }
//                    if ($total['DGDichVuTS_Net'] > 0) {
//                        $DGDichVuTS_Net_Percent = ($res->DGDichVuTS_Net / $total['DGDichVuTS_Net']) * 100;
//                        $DGDichVuTS_Net_Percent = round($DGDichVuTS_Net_Percent, 2);
//                        $arrTotalPercent['DGDichVuTS_Net'] += $DGDichVuTS_Net_Percent;
//                    }
//                    if ($total['DGDichVuTS_TV'] > 0) {
//                        $DGDichVuTS_TV_Percent = ($res->DGDichVuTS_TV / $total['DGDichVuTS_TV']) * 100;
//                        $DGDichVuTS_TV_Percent = round($DGDichVuTS_TV_Percent, 2);
//                        $arrTotalPercent['DGDichVuTS_TV'] += $DGDichVuTS_TV_Percent;
//                    }
                    if ($total['NVBaoTri'] > 0) {
                        $NVBaoTriPercent = ($res->NVBaoTri / $total['NVBaoTri']) * 100;
                        $NVBaoTriPercent = round($NVBaoTriPercent, 2);
                        $arrTotalPercent['NVBaoTri'] += $NVBaoTriPercent;
                    }
//                    if ($total['NVBaoTriINDO'] > 0) {
//                        $NVBaoTriINDOPercent = ($res->NVBaoTriINDO / $total['NVBaoTriINDO']) * 100;
//                        $NVBaoTriINDOPercent = round($NVBaoTriINDOPercent, 2);
//                        $arrTotalPercent['NVBaoTriINDO'] += $NVBaoTriINDOPercent;
//                    }
                    if ($total['DVBaoTri_Net'] > 0) {
                        $DVBaoTri_Net_Percent = ($res->DVBaoTri_Net / $total['DVBaoTri_Net']) * 100;
                        $DVBaoTri_Net_Percent = round($DVBaoTri_Net_Percent, 2);
                        $arrTotalPercent['DVBaoTri_Net'] += $DVBaoTri_Net_Percent;
                    }
//                    if ($total['DVBaoTriTIN_TV'] > 0) {
//                        $DVBaoTriTIN_TV_Percent = ($res->DVBaoTriTIN_TV / $total['DVBaoTriTIN_TV']) * 100;
//                        $DVBaoTriTIN_TV_Percent = round($DVBaoTriTIN_TV_Percent, 2);
//                        $arrTotalPercent['DVBaoTriTIN_TV'] += $DVBaoTriTIN_TV_Percent;
//                    }
//                    if ($total['DVBaoTriINDO_Net'] > 0) {
//                        $DVBaoTriINDO_Net_Percent = ($res->DVBaoTriINDO_Net / $total['DVBaoTriINDO_Net']) * 100;
//                        $DVBaoTriINDO_Net_Percent = round($DVBaoTriINDO_Net_Percent, 2);
//                        $arrTotalPercent['DVBaoTriINDO_Net'] += $DVBaoTriINDO_Net_Percent;
//                    }
//                    if ($total['DVBaoTriINDO_TV'] > 0) {
//                        $DVBaoTriINDO_TV_Percent = ($res->DVBaoTriINDO_TV / $total['DVBaoTriINDO_TV']) * 100;
//                        $DVBaoTriINDO_TV_Percent = round($DVBaoTriINDO_TV_Percent, 2);
//                        $arrTotalPercent['DVBaoTriINDO_TV'] += $DVBaoTriINDO_TV_Percent;
//                    }
//                    if ($total['NVThuCuoc'] > 0) {
//                        $NVThuCuoc_Percent = ($res->NVThuCuoc / $total['NVThuCuoc']) * 100;
//                        $NVThuCuoc_Percent = round($NVThuCuoc_Percent, 2);
//                        $arrTotalPercent['NVThuCuoc'] += $NVThuCuoc_Percent;
//                    }
//                    if ($total['DGDichVu_MobiPay_Net'] > 0) {
//                        $DGDichVu_MobiPay_Net_Percent = ($res->DGDichVu_MobiPay_Net / $total['DGDichVu_MobiPay_Net']) * 100;
//                        $DGDichVu_MobiPay_Net_Percent = round($DGDichVu_MobiPay_Net_Percent, 2);
//                        $arrTotalPercent['DGDichVu_MobiPay_Net'] += $DGDichVu_Net_Percent;
//                    }
//                    if ($total['DGDichVu_MobiPay_TV'] > 0) {
//                        $DGDichVu_MobiPay_TV_Percent = ($res->DGDichVu_MobiPay_TV / $total['DGDichVu_MobiPay_TV']) * 100;
//                        $DGDichVu_MobiPay_TV_Percent = round($DGDichVu_MobiPay_TV_Percent, 2);
//                        $arrTotalPercent['DGDichVu_MobiPay_TV'] += $DGDichVu_MobiPay_TV_Percent;
//                    }
//                    if ($total['DGDichVu_Counter'] > 0) {
//                        $DGDichVu_Counter_Percent = ($res->DGDichVu_Counter / $total['DGDichVu_Counter']) * 100;
//                        $DGDichVu_Counter_Percent = round($DGDichVu_Counter_Percent, 2);
//                        $arrTotalPercent['DGDichVu_Counter'] += $DGDichVu_Counter_Percent;
//                    }
//                    if ($total['NV_Counter'] > 0) {
//                        $NV_Counter_Percent = ($res->NV_Counter / $total['NV_Counter']) * 100;
//                        $NV_Counter_Percent = round($NV_Counter_Percent, 2);
//                        $arrTotalPercent['NV_Counter'] += $NV_Counter_Percent;
//                    }
//                    if ($total['NVKinhDoanhSS'] > 0) {
//                        $NVKinhDoanhSSPercent = ($res->NVKinhDoanhSS / $total['NVKinhDoanhSS']) * 100;
//                        $NVKinhDoanhSSPercent = round($NVKinhDoanhSSPercent, 2);
//                        $arrTotalPercent['NVKinhDoanhSS'] += $NVKinhDoanhSSPercent;
//                    }
//                    if ($total['NVTrienKhaiSS'] > 0) {
//                        $NVTrienKhaiSSPercent = ($res->NVTrienKhaiSS / $total['NVTrienKhaiSS']) * 100;
//                        $NVTrienKhaiSSPercent = round($NVTrienKhaiSSPercent, 2);
//                        $arrTotalPercent['NVTrienKhaiSS'] += $NVTrienKhaiSSPercent;
//                    }
//                    if ($total['DGDichVuSS_Net'] > 0) {
//                        $DGDichVuSS_Net_Percent = ($res->DGDichVuSS_Net / $total['DGDichVuSS_Net']) * 100;
//                        $DGDichVuSS_Net_Percent = round($DGDichVuSS_Net_Percent, 2);
//                        $arrTotalPercent['DGDichVuSS_Net'] += $DGDichVuSS_Net_Percent;
//                    }
//                    if ($total['DGDichVuSS_TV'] > 0) {
//                        $DGDichVuSS_TV_Percent = ($res->DGDichVuSS_TV / $total['DGDichVuSS_TV']) * 100;
//                        $DGDichVuSS_TV_Percent = round($DGDichVuSS_TV_Percent, 2);
//                        $arrTotalPercent['DGDichVuSS_TV'] += $DGDichVuSS_TV_Percent;
//                    }
//
//                    if ($total['NVBT_SSW'] > 0) {
//                        $NVBTSSWPercent = ($res->NVBT_SSW / $total['NVBT_SSW']) * 100;
//                        $NVBTSSWPercent = round($NVBTSSWPercent, 2);
//                        $arrTotalPercent['NVBT_SSW'] += $NVBTSSWPercent;
//                    }
//                    if ($total['DGDichVuSSW_Net'] > 0) {
//                        $DGDichVuSSW_Net_Percent = ($res->DGDichVuSSW_Net / $total['DGDichVuSSW_Net']) * 100;
//                        $DGDichVuSSW_Net_Percent = round($DGDichVuSSW_Net_Percent, 2);
//                        $arrTotalPercent['DGDichVuSSW_Net'] += $DGDichVuSSW_Net_Percent;
//                    }
//                    if ($total['DGDichVuSSW_TV'] > 0) {
//                        $DGDichVuSSW_TV_Percent = ($res->DGDichVuSSW_TV / $total['DGDichVuSSW_TV']) * 100;
//                        $DGDichVuSSW_TV_Percent = round($DGDichVuSSW_TV_Percent, 2);
//                        $arrTotalPercent['DGDichVuSSW_TV'] += $DGDichVuSSW_TV_Percent;
//                    }

                    //% các điểm ko hài lòng, trung lập, hài lòng
                    if ($res->answers_point >= 1 && $res->answers_point <= 2) {//điểm không hài lòng
                        $arrUnsatisfiedPercent['NVKinhDoanh'] += $NVKinhDoanhPercent;
                        $arrUnsatisfiedPercent['NVTrienKhai'] += $NVTrienKhaiPercent;
                        $arrUnsatisfiedPercent['DGDichVu_Net'] += $DGDichVu_Net_Percent;
//                        $arrUnsatisfiedPercent['DGDichVu_TV'] += $DGDichVu_TV_Percent;
//
//                        $arrUnsatisfiedPercent['NVKinhDoanhTS'] += $NVKinhDoanhTSPercent;
//                        $arrUnsatisfiedPercent['NVTrienKhaiTS'] += $NVTrienKhaiTSPercent;
//                        $arrUnsatisfiedPercent['DGDichVuTS_Net'] += $DGDichVuTS_Net_Percent;
//                        $arrUnsatisfiedPercent['DGDichVuTS_TV'] += $DGDichVuTS_TV_Percent;

                        $arrUnsatisfiedPercent['NVBaoTri'] += $NVBaoTriPercent;
                        $arrUnsatisfiedPercent['DVBaoTri_Net'] += $DVBaoTri_Net_Percent;


//                        $arrUnsatisfiedPercent['DGDichVu_MobiPay_Net'] += $DGDichVu_MobiPay_Net_Percent;
//                        $arrUnsatisfiedPercent['DGDichVu_MobiPay_TV'] += $DGDichVu_MobiPay_TV_Percent;
//                        $arrUnsatisfiedPercent['NVThuCuoc'] += $NVThuCuoc_Percent;
//                        $arrUnsatisfiedPercent['DGDichVu_Counter'] += $DGDichVu_Counter_Percent;
//                        $arrUnsatisfiedPercent['NV_Counter'] += $NV_Counter_Percent;
//
//                        $arrUnsatisfiedPercent['NVKinhDoanhSS'] += $NVKinhDoanhSSPercent;
//                        $arrUnsatisfiedPercent['NVTrienKhaiSS'] += $NVTrienKhaiSSPercent;
//                        $arrUnsatisfiedPercent['DGDichVuSS_Net'] += $DGDichVuSS_Net_Percent;
//                        $arrUnsatisfiedPercent['DGDichVuSS_TV'] += $DGDichVuSS_TV_Percent;
//
//                        $arrUnsatisfiedPercent['NVBT_SSW'] += $NVBTSSWPercent;
//                        $arrUnsatisfiedPercent['DGDichVuSSW_Net'] += $DGDichVuSSW_Net_Percent;
//                        $arrUnsatisfiedPercent['DGDichVuSSW_TV'] += $DGDichVuSSW_TV_Percent;
                    }
                    if ($res->answers_point == 3) {//điểm trung lập
                        $arrNeutralPercent['NVKinhDoanh'] += $NVKinhDoanhPercent;
                        $arrNeutralPercent['NVTrienKhai'] += $NVTrienKhaiPercent;
                        $arrNeutralPercent['DGDichVu_Net'] += $DGDichVu_Net_Percent;
//                        $arrNeutralPercent['DGDichVu_TV'] += $DGDichVu_TV_Percent;
//
//                        $arrNeutralPercent['NVKinhDoanhTS'] += $NVKinhDoanhTSPercent;
//                        $arrNeutralPercent['NVTrienKhaiTS'] += $NVTrienKhaiTSPercent;
//                        $arrNeutralPercent['DGDichVuTS_Net'] += $DGDichVuTS_Net_Percent;
//                        $arrNeutralPercent['DGDichVuTS_TV'] += $DGDichVuTS_TV_Percent;

                        $arrNeutralPercent['NVBaoTri'] += $NVBaoTriPercent;
                        $arrNeutralPercent['DVBaoTri_Net'] += $DVBaoTri_Net_Percent;
//                        $arrNeutralPercent['DVBaoTriTIN_TV'] += $DVBaoTriTIN_TV_Percent;
//                        $arrNeutralPercent['DVBaoTriINDO_Net'] += $DVBaoTriINDO_Net_Percent;
//                        $arrNeutralPercent['DVBaoTriINDO_TV'] += $DVBaoTriINDO_TV_Percent;
//                        $arrNeutralPercent['DGDichVu_MobiPay_Net'] += $DGDichVu_MobiPay_Net_Percent;
//                        $arrNeutralPercent['DGDichVu_MobiPay_TV'] += $DGDichVu_MobiPay_TV_Percent;

//                        $arrNeutralPercent['NVThuCuoc'] += $NVThuCuoc_Percent;
//                        $arrNeutralPercent['DGDichVu_Counter'] += $DGDichVu_Counter_Percent;
//                        $arrNeutralPercent['NV_Counter'] += $NV_Counter_Percent;
//
//                        $arrNeutralPercent['NVKinhDoanhSS'] += $NVKinhDoanhSSPercent;
//                        $arrNeutralPercent['NVTrienKhaiSS'] += $NVTrienKhaiSSPercent;
//                        $arrNeutralPercent['DGDichVuSS_Net'] += $DGDichVuSS_Net_Percent;
//                        $arrNeutralPercent['DGDichVuSS_TV'] += $DGDichVuSS_TV_Percent;
//
//                        $arrNeutralPercent['NVBT_SSW'] += $NVBTSSWPercent;
//                        $arrNeutralPercent['DGDichVuSSW_Net'] += $DGDichVuSSW_Net_Percent;
//                        $arrNeutralPercent['DGDichVuSSW_TV'] += $DGDichVuSSW_TV_Percent;
                    }
                    if ($res->answers_point >= 4 && $res->answers_point <= 5) {//điểm hài lòng
                        $arrSatisfiedPercent['NVKinhDoanh'] += $NVKinhDoanhPercent;
                        $arrSatisfiedPercent['NVTrienKhai'] += $NVTrienKhaiPercent;
                        $arrSatisfiedPercent['DGDichVu_Net'] += $DGDichVu_Net_Percent;
//                        $arrSatisfiedPercent['DGDichVu_TV'] += $DGDichVu_TV_Percent;
//
//                        $arrSatisfiedPercent['NVKinhDoanhTS'] += $NVKinhDoanhTSPercent;
//                        $arrSatisfiedPercent['NVTrienKhaiTS'] += $NVTrienKhaiTSPercent;
//                        $arrSatisfiedPercent['DGDichVuTS_Net'] += $DGDichVuTS_Net_Percent;
//                        $arrSatisfiedPercent['DGDichVuTS_TV'] += $DGDichVuTS_TV_Percent;

                        $arrSatisfiedPercent['NVBaoTri'] += $NVBaoTriPercent;
                        $arrSatisfiedPercent['DVBaoTri_Net'] += $DVBaoTri_Net_Percent;
//                        $arrSatisfiedPercent['DVBaoTriTIN_TV'] += $DVBaoTriTIN_TV_Percent;
//                        $arrSatisfiedPercent['DVBaoTriINDO_Net'] += $DVBaoTriINDO_Net_Percent;
//                        $arrSatisfiedPercent['DVBaoTriINDO_TV'] += $DVBaoTriINDO_TV_Percent;
//                        $arrSatisfiedPercent['DGDichVu_MobiPay_Net'] += $DGDichVu_MobiPay_Net_Percent;
//                        $arrSatisfiedPercent['DGDichVu_MobiPay_TV'] += $DGDichVu_MobiPay_TV_Percent;
//
//                        $arrSatisfiedPercent['NVThuCuoc'] += $NVThuCuoc_Percent;
//                        $arrSatisfiedPercent['DGDichVu_Counter'] += $DGDichVu_Counter_Percent;
//                        $arrSatisfiedPercent['NV_Counter'] += $NV_Counter_Percent;
//
//                        $arrSatisfiedPercent['NVKinhDoanhSS'] += $NVKinhDoanhSSPercent;
//                        $arrSatisfiedPercent['NVTrienKhaiSS'] += $NVTrienKhaiSSPercent;
//                        $arrSatisfiedPercent['DGDichVuSS_Net'] += $DGDichVuSS_Net_Percent;
//                        $arrSatisfiedPercent['DGDichVuSS_TV'] += $DGDichVuSS_TV_Percent;
//
//                        $arrSatisfiedPercent['NVBT_SSW'] += $NVBTSSWPercent;
//                        $arrSatisfiedPercent['DGDichVuSSW_Net'] += $DGDichVuSSW_Net_Percent;
//                        $arrSatisfiedPercent['DGDichVuSSW_TV'] += $DGDichVuSSW_TV_Percent;
                    }
                    ?>
                    <td><span class="number">{{$res->NVKinhDoanh}}</span></td>
                    <td><span class="number">{{$NVKinhDoanhPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->NVTrienKhai}}</span></td>
                    <td><span class="number">{{$NVTrienKhaiPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVu_Net}}</span></td>
                    <td><span class="number">{{$DGDichVu_Net_Percent.'%'}}</span></td>
                    {{--<td><span class="number">{{$res->DGDichVu_TV}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVu_TV_Percent.'%'}}</span></td>--}}

                    {{--<td><span class="number">{{$res->NVKinhDoanhTS}}</span></td>--}}
                    {{--<td><span class="number">{{$NVKinhDoanhTSPercent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->NVTrienKhaiTS}}</span></td>--}}
                    {{--<td><span class="number">{{$NVTrienKhaiTSPercent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DGDichVuTS_Net}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVuTS_Net_Percent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DGDichVuTS_TV}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVuTS_TV_Percent.'%'}}</span></td>--}}

                    <td><span class="number">{{$res->NVBaoTri}}</span></td>
                    <td><span class="number">{{$NVBaoTriPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTri_Net}}</span></td>
                    <td><span class="number">{{$DVBaoTri_Net_Percent.'%'}}</span></td>
                    {{--<td><span class="number">{{$res->DVBaoTriTIN_TV}}</span></td>--}}
                    {{--<td><span class="number">{{$DVBaoTriTIN_TV_Percent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->NVBaoTriINDO}}</span></td>--}}
                    {{--<td><span class="number">{{$NVBaoTriINDOPercent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DVBaoTriINDO_Net}}</span></td>--}}
                    {{--<td><span class="number">{{$DVBaoTriINDO_Net_Percent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DVBaoTriINDO_TV}}</span></td>--}}
                    {{--<td><span class="number">{{$DVBaoTriINDO_TV_Percent.'%'}}</span></td>--}}

                    {{--<td><span class="number">{{$res->NVThuCuoc}}</span></td>--}}
                    {{--<td><span class="number">{{$NVThuCuoc_Percent.'%'}}</span></td>                   --}}
                    {{--<td><span class="number">{{$res->DGDichVu_MobiPay_Net}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVu_MobiPay_Net_Percent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DGDichVu_MobiPay_TV}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVu_MobiPay_TV_Percent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->NV_Counter}}</span></td>--}}
                    {{--<td><span class="number">{{$NV_Counter_Percent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DGDichVu_Counter}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVu_Counter_Percent.'%'}}</span></td>--}}

                    {{--<td><span class="number">{{$res->NVKinhDoanhSS}}</span></td>--}}
                    {{--<td><span class="number">{{$NVKinhDoanhSSPercent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->NVTrienKhaiSS}}</span></td>--}}
                    {{--<td><span class="number">{{$NVTrienKhaiSSPercent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DGDichVuSS_Net}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVuSS_Net_Percent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DGDichVuSS_TV}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVuSS_TV_Percent.'%'}}</span></td>--}}

                    {{--<td><span class="number">{{$res->NVBT_SSW}}</span></td>--}}
                    {{--<td><span class="number">{{$NVBTSSWPercent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DGDichVuSSW_Net}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVuSSW_Net_Percent.'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$res->DGDichVuSSW_TV}}</span></td>--}}
                    {{--<td><span class="number">{{$DGDichVuSSW_TV_Percent.'%'}}</span></td>--}}
                </tr>
                <?php
            }
            if (!empty($survey)) {
                ?>
                <tr class="foot">
                    <td><span>{{trans($transfile.'.Total')}}</span></td>
                    <td><span class="number">{{$total['NVKinhDoanh']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVKinhDoanh'] === '99.99') ?'100%' :round($arrTotalPercent['NVKinhDoanh']).'%'}}</span></td>
                    <td><span class="number">{{$total['NVTrienKhai']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVTrienKhai'] === '99.99') ?'100%' :round($arrTotalPercent['NVTrienKhai']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVu_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVu_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_Net']).'%'}}</span></td>
                    {{--<td><span class="number">{{$total['DGDichVu_TV']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVu_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_TV']).'%'}}</span></td>--}}

                    {{--<td><span class="number">{{$total['NVKinhDoanhTS']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['NVKinhDoanhTS'] === '99.99') ?'100%' :round($arrTotalPercent['NVKinhDoanhTS']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['NVTrienKhaiTS']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['NVTrienKhaiTS'] === '99.99') ?'100%' :round($arrTotalPercent['NVTrienKhaiTS']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVuTS_Net']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVuTS_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuTS_Net']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVuTS_TV']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVuTS_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuTS_TV']).'%'}}</span></td>--}}

                    <td><span class="number">{{$total['NVBaoTri']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVBaoTri'] === '99.99') ?'100%' :round($arrTotalPercent['NVBaoTri']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTri_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTri_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTri_Net']).'%'}}</span></td>
                    {{--<td><span class="number">{{$total['DVBaoTriTIN_TV']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DVBaoTriTIN_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriTIN_TV']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['NVBaoTriINDO']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['NVBaoTriINDO'] === '99.99') ?'100%' :round($arrTotalPercent['NVBaoTriINDO']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DVBaoTriINDO_Net']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DVBaoTriINDO_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriINDO_Net']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DVBaoTriINDO_TV']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DVBaoTriINDO_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriINDO_TV']).'%'}}</span></td>--}}

                    {{--<td><span class="number">{{$total['NVThuCuoc']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['NVThuCuoc'] === '99.99') ?'100%' :round($arrTotalPercent['NVThuCuoc']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVu_MobiPay_Net']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVu_MobiPay_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_MobiPay_Net']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVu_MobiPay_TV']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVu_MobiPay_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_MobiPay_TV']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['NV_Counter']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['NV_Counter'] === '99.99') ?'100%' :round($arrTotalPercent['NV_Counter']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVu_Counter']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVu_Counter'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_Counter']).'%'}}</span></td>--}}

                    {{--<td><span class="number">{{$total['NVKinhDoanhSS']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['NVKinhDoanhSS'] === '99.99') ?'100%' :round($arrTotalPercent['NVKinhDoanhSS']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['NVTrienKhaiSS']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['NVTrienKhaiSS'] === '99.99') ?'100%' :round($arrTotalPercent['NVTrienKhaiSS']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVuSS_Net']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVuSS_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuSS_Net']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVuSS_TV']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVuSS_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuSS_TV']).'%'}}</span></td>--}}

                    {{--<td><span class="number">{{$total['NVBT_SSW']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['NVBT_SSW'] === '99.99') ?'100%' :round($arrTotalPercent['NVBT_SSW']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVuSSW_Net']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVuSSW_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuSSW_Net']).'%'}}</span></td>--}}
                    {{--<td><span class="number">{{$total['DGDichVuSSW_TV']}}</span></td>--}}
                    {{--<td><span class="number">{{((string)$arrTotalPercent['DGDichVuSSW_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuSSW_TV']).'%'}}</span></td>--}}
                </tr>
                <tr>
                    <td class="foot_average"><span>{{trans($transfile.'.Average Point')}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVKinhDoanh']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVTrienKhai']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_Net']}}</span></td>
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_TV']}}</span></td>--}}

                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['NVKinhDoanhTS']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['NVTrienKhaiTS']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuTS_Net']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuTS_TV']}}</span></td>--}}

                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVBaoTri']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTri_Net']}}</span></td>
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriTIN_TV']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['NVBaoTriINDO']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriINDO_Net']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriINDO_TV']}}</span></td>--}}

                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['NVThuCuoc']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_MobiPay_Net']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_MobiPay_TV']}}</span></td>--}}

                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['NV_Counter']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_Counter']}}</span></td>--}}

                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['NVKinhDoanhSS']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['NVTrienKhaiSS']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuSS_Net']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuSS_TV']}}</span></td>--}}

                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['NVBT_SSW']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuSSW_Net']}}</span></td>--}}
                    {{--<td class="foot_average" style="display: none"></td>--}}
                    {{--<td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuSSW_TV']}}</span></td>--}}
                </tr>
                <?php
            }
        }
        ?>
    </tbody>

</table>

        <?php if (!isset($viewFrom)) { ?>
        <h3 class="header smaller lighter red">
            <i class="icon-table"></i>
            {{trans($transfile.'.StatisticalOfSatisfactionCustomerForRatingObject')}}
        </h3>
        <table id="table-CSATObjectReport" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%" style="max-width: 100%;">
            <thead>
            <tr>
                <th rowspan="2" class="text-center" >{{trans($transfile.'.EvaluatedObject')}}</th>
                <th colspan="6" class="text-center"> {{trans($transfile.'.Rating Quality Service')}}</th>
                <th colspan="12" class="text-center">{{trans($transfile.'.Rating Staff')}}</th>
            </tr>
            <tr>

                <th colspan="3" class="text-center">{{trans($transfile.'.Net')}}</th>
                <th colspan="3" class="text-center">{{trans($transfile.'.Service Quality Statistical')}}</th>

                <th colspan="3" class="text-center">{{trans($transfile.'.Saler')}}</th>
                <th colspan="3" class="text-center">{{trans($transfile.'.Technical Staff')}}</th>
                <th colspan="3" class="text-center">{{trans($transfile.'.Staff Statistical')}}</th>
            </tr>
            <tr style="color: #307ecc">
                <th  class="text-center evaluate-cell" style="color: #307ecc">{{trans($transfile.'.Rating Point')}}</th>
                <th>{{trans($transfile.'.Quantity')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>

                <th>{{trans($transfile.'.Quantity')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>

                <th>{{trans($transfile.'.Quantity')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>

                <th>{{trans($transfile.'.Quantity')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>

                <th>{{trans($transfile.'.Quantity')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>
                <th>{{trans($transfile.'.Percent')}}</th>

            </tr>
            </thead>

            <tbody>
            <?php
            if (!empty($totalCSAT) && !empty($averagePoint)) {
            //            $NVKinhDoanhTSPercent = $NVTrienKhaiTSPercent = $DGDichVuTS_Net_Percent = $DGDichVuTS_TV_Percent = $NVKinhDoanhPercent = $NVTrienKhaiPercent = $DGDichVu_Net_Percent = $DGDichVu_TV_Percent = $NVBaoTriTINPercent = $NVBaoTriINDOPercent = $DVBaoTriTIN_Net_Percent = $DVBaoTriTIN_TV_Percent = $DVBaoTriINDO_Net_Percent = $DVBaoTriINDO_TV_Percent = $DGDichVu_MobiPay_Net_Percent = $DGDichVu_MobiPay_TV_Percent = $NVThuCuoc_Percent = $DGDichVu_Counter_Percent = $NV_Counter_Percent = $NVKinhDoanhSSPercent = $NVTrienKhaiSSPercent = $DGDichVuSS_Net_Percent = $DGDichVuSS_TV_Percent = $NVBTSSWPercent = $DGDichVuSSW_Net_Percent = $DGDichVuSSW_TV_Percent = 0;
            //            $arrCSAT = $arrCSATX = [];
            $emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
            foreach ($totalCSAT as $key => $res) {
            ?>
            <tr class="<?php if ($key == 'total')
                echo 'foot';
            ?>">
                <td><span>{{trans($transfile.'.'.trim($res['Csat']))}} <?php if ($key != 'total') { ?><img src="{{asset("assets/img/".$emotions[$key])}}" style="width: 25px;height: 25px;float: right;"> <?php } ?></span></td>

                <td><span class="number">{{$res['Net']}}</span></td>
                <td><span class="number">{{$res['NetPercent']}}</span></td>
                <?php
                if ($key == 3 || $key == 'total') {
                ?>
                <td><span class="number">
                                    <?php
                        echo $res['NetPercent'];
                        ?>
                                </span></td>
                <?php } else if ($key == 1 || $key == 4) {
                ?>
                <td rowspan="2"><span class="number">
                                    <?php
                        if ($key == 1)
                            echo $res['NetPercent'] + $totalCSAT[2]['NetPercent'] . '%';
                        else
                            echo $res['NetPercent'] + $totalCSAT[5]['NetPercent'] . '%';
                        ?>
                                </span></td>
                <?php
                }
                ?>

                <td><span class="number">{{$res['NetAndTV']}}</span></td>
                <td><span class="number">{{$res['NetAndTVPercent']}}</span></td>
                <?php
                if ($key == 3 || $key == 'total') {
                ?>
                <td><span class="number">
                                    <?php
                        echo $res['NetAndTVPercent'];
                        ?>
                                </span></td>
                <?php } else if ($key == 1 || $key == 4) {
                ?>
                <td rowspan="2"><span class="number">
                                    <?php
                        if ($key == 1)
                            echo $res['NetAndTVPercent'] + $totalCSAT[2]['NetAndTVPercent'] . '%';
                        else
                            echo $res['NetAndTVPercent'] + $totalCSAT[5]['NetAndTVPercent'] . '%';
                        ?>
                                </span></td>
                <?php
                }
                ?>

                <td><span class="number">{{$res['NVKinhDoanh']}}</span></td>
                <td><span class="number">{{$res['NVKinhDoanhPercent']}}</span></td>
                <?php
                if ($key == 3 || $key == 'total') {
                ?>
                <td><span class="number">
                                    <?php
                        echo $res['NVKinhDoanhPercent'];
                        ?>
                                </span></td>
                <?php } else if ($key == 1 || $key == 4) {
                ?>
                <td rowspan="2"><span class="number">
                                    <?php
                        if ($key == 1)
                            echo $res['NVKinhDoanhPercent'] + $totalCSAT[2]['NVKinhDoanhPercent'] . '%';
                        else
                            echo $res['NVKinhDoanhPercent'] + $totalCSAT[5]['NVKinhDoanhPercent'] . '%';
                        ?>
                                </span></td>
                <?php
                }
                ?>

                <td><span class="number">{{$res['NVKT']}}</span></td>
                <td><span class="number">{{$res['NVKTPercent']}}</span></td>
                <?php
                if ($key == 3 || $key == 'total') {
                ?>
                <td><span class="number">
                                    <?php
                        echo $res['NVKTPercent'];
                        ?>
                                </span></td>
                <?php } else if ($key == 1 || $key == 4) {
                ?>
                <td rowspan="2"><span class="number">
                                    <?php
                        if ($key == 1)
                            echo $res['NVKTPercent'] + $totalCSAT[2]['NVKTPercent'] . '%';
                        else
                            echo $res['NVKTPercent'] + $totalCSAT[5]['NVKTPercent'] . '%';
                        ?>
                                </span></td>
                <?php
                }
                ?>

                <td><span class="number">{{$res['TongHopNV']}}</span></td>
                <td><span class="number">{{$res['TongHopNVPercent']}}</span></td>
                <?php
                if ($key == 3 || $key == 'total') {
                ?>
                <td><span class="number">
                                    <?php
                        echo $res['TongHopNVPercent'];
                        ?>
                                </span></td>
                <?php } else if ($key == 1 || $key == 4) {
                ?>
                <td rowspan="2"><span class="number">
                                    <?php
                        if ($key == 1)
                            echo $res['TongHopNVPercent'] + $totalCSAT[2]['TongHopNVPercent'] . '%';
                        else
                            echo $res['TongHopNVPercent'] + $totalCSAT[5]['TongHopNVPercent'] . '%';
                        ?>
                                </span></td>
                <?php
                }
                ?>
            </tr>
            <?php
            }
            //            if (!empty($survey)) {
            ?>
            <tr>
                <td class="foot_average"><span>{{trans($transfile.'.Average Point')}}</span></td>
                <td class="foot_average" style="display: none"></td>
                <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NET']}}</span></td>
                <td class="foot_average" style="display: none"></td>
                <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NetAndTV']}}</span></td>
                <td class="foot_average" style="display: none"></td>
                <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NVKinhDoanh']}}</span></td>
                <td class="foot_average" style="display: none"></td>
                <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NVKT']}}</span></td>
                <td class="foot_average" style="display: none"></td>
                <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_TongHopNV']}}</span></td>
            </tr>
            <?php
            //            }
            }
            ?>
            </tbody>



        </table>
            <div style="overflow:auto">
                <h3 class="header smaller lighter red">
                    <i class="icon-table"></i>
                    {{trans($transfile.'.BranchCustomerSatisfactionofEachEvaluatedObject')}}

                </h3>
                <table class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%" style="max-width: 100%;">
                    <thead>
                    <tr>
                        <th class="text-center"  > {{trans($transfile.'.Location')}}
                        </th>
                        @foreach($surveyCSATBranch['all'] as $value)
                            <th colspan="9" class="text-center">{{($value == 'WholeCountry') ?  trans($transfile.'.'.$value) : $value}}</th>
                        @endforeach

                    </tr>
                    <tr >
                        <th class="text-center"> {{trans($transfile.'.EvaluatedObject')}}
                        @foreach($surveyCSATBranch['all'] as $value)
                            <th colspan="3">Internet</th>
                            <th colspan="3">{{trans($transfile.'.Saler')}}</th>
                            <th colspan="3">{{trans($transfile.'.Deployer')}}</th>
                        @endforeach
                    </tr>
                    <tr style="color: #307ecc">
                        <th class="text-center evaluate-cell"  style="color: #307ecc"> {{trans($transfile.'.Rating Point')}}
                        @foreach($surveyCSATBranch['all'] as $value)
                            <th> {{trans($transfile.'.Quantity')}}</th>
                            <th> {{trans($transfile.'.Percent')}}</th>
                            <th> {{trans($transfile.'.Percent')}}</th>
                            <th> {{trans($transfile.'.Quantity')}}</th>
                            <th> {{trans($transfile.'.Percent')}}</th>
                            <th> {{trans($transfile.'.Percent')}}</th>
                            <th> {{trans($transfile.'.Quantity')}}</th>
                            <th> {{trans($transfile.'.Percent')}}</th>
                            <th> {{trans($transfile.'.Percent')}}</th>
                        @endforeach
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    if (!empty($surveyCSATBranch['totalCSAT']) && !empty($surveyCSATBranch['averagePoint'])) {
                    //            $NVKinhDoanhTSPercent = $NVTrienKhaiTSPercent = $DGDichVuTS_Net_Percent = $DGDichVuTS_TV_Percent = $NVKinhDoanhPercent = $NVTrienKhaiPercent = $DGDichVu_Net_Percent = $DGDichVu_TV_Percent = $NVBaoTriTINPercent = $NVBaoTriINDOPercent = $DVBaoTriTIN_Net_Percent = $DVBaoTriTIN_TV_Percent = $DVBaoTriINDO_Net_Percent = $DVBaoTriINDO_TV_Percent = $DGDichVu_MobiPay_Net_Percent = $DGDichVu_MobiPay_TV_Percent = $NVThuCuoc_Percent = $DGDichVu_Counter_Percent = $NV_Counter_Percent = $NVKinhDoanhSSPercent = $NVTrienKhaiSSPercent = $DGDichVuSS_Net_Percent = $DGDichVuSS_TV_Percent = $NVBTSSWPercent = $DGDichVuSSW_Net_Percent = $DGDichVuSSW_TV_Percent = 0;
                    //            $arrCSAT = $arrCSATX = [];
                    $emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
                    foreach ($surveyCSATBranch['totalCSAT'] as $key => $res) {

                    ?>
                    <tr class="<?php if ($key == 'total')
                        echo 'foot';
                    ?>">
                        <td><span>{{trans($transfile.'.'.$res['Csat'])}} <?php if ($key != 'total') { ?><img src="{{asset("assets/img/".$emotions[$key])}}" style="width: 25px;height: 25px;float: right;"> <?php } ?></span></td>
                        <?php   foreach ($surveyCSATBranch['all'] as $key2 => $value2){
                        ?>
                        <td><span class="number">{{!empty($res[$value2.'Net']) ? $res[$value2.'Net'] : 0}}</span></td>
                        <td><span class="number">{{!empty($res[$value2.'Net'.'Percent']) ?  $res[$value2.'Net'.'Percent'] : 0}}</span></td>


                        <?php
                        if ($key == 3 || $key == 'total') {
                        ?>
                        <td><span class="number">
                                    <?php
                                echo !empty($res[$value2.'Net'.'Percent']) ?  $res[$value2.'Net'.'Percent'] : 0;
                                ?>
                                </span></td>

                        <?php } else if ($key == 1 || $key == 4) {
                        ?>
                        <td rowspan="2"><span class="number">
                                    <?php
                                if ($key == 1)
                                {
                                    echo (!empty($res[$value2.'Net'.'Percent']) ?  $res[$value2.'Net'.'Percent'] : 0)  + (!empty($surveyCSATBranch['totalCSAT'][2][$value2.'Net'.'Percent']) ?  $surveyCSATBranch['totalCSAT'][2][$value2.'Net'.'Percent'] : 0)  . '%';}

                                else
                                    echo  (!empty($res[$value2.'Net'.'Percent']) ?  $res[$value2.'Net'.'Percent'] : 0)  + (!empty($surveyCSATBranch['totalCSAT'][5][$value2.'Net'.'Percent']) ?  $surveyCSATBranch['totalCSAT'][5][$value2.'Net'.'Percent'] : 0)  . '%';
                                ?>
                                </span></td>

                        <?php
                        }
                        ?>

                        <td><span class="number">{{!empty($res[$value2.'SaleMan']) ? $res[$value2.'SaleMan'] : 0}}</span></td>
                        <td><span class="number">{{!empty($res[$value2.'SaleMan'.'Percent']) ?  $res[$value2.'SaleMan'.'Percent'] : 0}}</span></td>


                        <?php
                        if ($key == 3 || $key == 'total') {
                        ?>
                        <td><span class="number">
                                    <?php
                                echo !empty($res[$value2.'SaleMan'.'Percent']) ?  $res[$value2.'SaleMan'.'Percent'] : 0;
                                ?>
                                </span></td>

                        <?php } else if ($key == 1 || $key == 4) {
                        ?>
                        <td rowspan="2"><span class="number">
                                    <?php
                                if ($key == 1)
                                    echo (!empty($res[$value2.'SaleMan'.'Percent']) ?  $res[$value2.'SaleMan'.'Percent'] : 0)  + (!empty($surveyCSATBranch['totalCSAT'][2][$value2.'SaleMan'.'Percent']) ?  $surveyCSATBranch['totalCSAT'][2][$value2.'SaleMan'.'Percent'] : 0)  . '%';
                                else
                                    echo  (!empty($res[$value2.'SaleMan'.'Percent']) ?  $res[$value2.'SaleMan'.'Percent'] : 0)  + (!empty($surveyCSATBranch['totalCSAT'][5][$value2.'SaleMan'.'Percent']) ?  $surveyCSATBranch['totalCSAT'][5][$value2.'SaleMan'.'Percent'] : 0)  . '%';
                                ?>
                                </span></td>

                        <?php
                        }
                        ?>

                        <td><span class="number">{{!empty($res[$value2.'Sir']) ? $res[$value2.'Sir'] : 0}}</span></td>
                        <td><span class="number">{{!empty($res[$value2.'Sir'.'Percent']) ?  $res[$value2.'Sir'.'Percent'] : 0}}</span></td>


                        <?php
                        if ($key == 3 || $key == 'total') {
                        ?>
                        <td><span class="number">
                                    <?php
                                echo !empty($res[$value2.'Sir'.'Percent']) ?  $res[$value2.'Sir'.'Percent'] : 0;
                                ?>
                                </span></td>

                        <?php } else if ($key == 1 || $key == 4) {
                        ?>
                        <td rowspan="2"><span class="number">
                                    <?php
                                if ($key == 1)
                                    echo (!empty($res[$value2.'Sir'.'Percent']) ?  $res[$value2.'Sir'.'Percent'] : 0)  + (!empty($surveyCSATBranch['totalCSAT'][2][$value2.'Sir'.'Percent']) ?  $surveyCSATBranch['totalCSAT'][2][$value2.'Sir'.'Percent'] : 0)  . '%';
                                else
                                    echo  (!empty($res[$value2.'Sir'.'Percent']) ?  $res[$value2.'Sir'.'Percent'] : 0)  + (!empty($surveyCSATBranch['totalCSAT'][5][$value2.'Sir'.'Percent']) ?  $surveyCSATBranch['totalCSAT'][5][$value2.'Sir'.'Percent'] : 0)  . '%';
                                ?>
                                </span></td>

                        <?php
                        }

                        }
                        ?>
                    </tr>
                    <?php

                    }
                    //            if (!empty($survey)) {
                    ?>
                    <tr>
                        <td class="foot_average"><span>{{trans($transfile.'.Average Point')}}</span></td>
                        @foreach ($surveyCSATBranch['all'] as $key3 => $value3)
                            <td class="foot_average" style="display: none"></td>
                            <td class="foot_average" colspan=3><span class="number">{{$surveyCSATBranch['averagePoint']['AVG_'.$value3.'Net']}}</span></td>
                            <td class="foot_average" colspan=3><span class="number">{{$surveyCSATBranch['averagePoint']['AVG_'.$value3.'SaleMan']}}</span></td>
                            <td class="foot_average" colspan=3><span class="number">{{$surveyCSATBranch['averagePoint']['AVG_'.$value3.'Sir']}}</span></td>
                        @endforeach

                    </tr>
                    <?php
                    //            }
                    }
                    ?>
                    </tbody>

                </table>
            </div>

        {{--<div class="row">--}}
            <h3 class="header smaller lighter red btn-group">
                <i class="icon-table"></i>
                {{trans($transfile.'.StatisticalOfUnsatisfactionCustomerForStaff')}}
            </h3>
        {{--</div>--}}
        {{--Thống kê theo khu vực--}}
        <table  id ='CSAT12StaffReport' class="table table-striped table-bordered table-hover table-CSAT12StaffReport "  cellspacing="0" width= "100%" style="max-width: 100%;overflow: auto;">
            <thead>
            <tr>
                <th colspan="1" class="text-center">{{trans($transfile.'.TouchPoint')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance')}}</th>
            </tr>
            <tr>
                <th colspan="1" class="text-center">{{trans($transfile.'.Staff')}}</th>
                <th colspan="5"  class="text-center">{{trans($transfile.'.Saler')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Deployer')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.MaintainanceStaff')}}</th>
            </tr>
            <tr>
                <th  class="text-center evaluate-cell">{{trans($transfile.'.Location')}}</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.TotalCsat12')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.RatioOfSatisfaction')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.AverageCsat')}}</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.TotalCsat12')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.RatioOfSatisfaction')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.AverageCsat')}}</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.TotalCsat12')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.RatioOfSatisfaction')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.AverageCsat')}}</th>
            </tr>
            </thead>

            <tbody>
            <?php
            $NVKD_TQ_CSAT1 = $NVKD_TQ_CSAT2 = $NVKD_TQ_CSAT12 = $NVKD_TQ_CUS_CSAT = $NVKD_TQ_CSAT = $NVTK_TQ_CSAT1 = $NVTK_TQ_CSAT2 = $NVTK_TQ_CSAT12 = $NVTK_TQ_CUS_CSAT = $NVTK_TQ_CSAT 
                = $NVBT_TQ_CSAT1 = $NVBT_TQ_CSAT2 = $NVBT_TQ_CSAT12 = $NVBT_TQ_CUS_CSAT = $NVBT_TQ_CSAT = 0;
            foreach ($surveyCSAT12 as $key => $value) {
            $NVKD_TQ_CSAT1 += $value->NVKD_CSAT_1;
            $NVKD_TQ_CSAT2 += $value->NVKD_CSAT_2;
            $NVKD_TQ_CSAT12 += $value->NVKD_CSAT_12;
            $NVKD_TQ_CUS_CSAT += $value->TOTAL_NVKD_CUS_CSAT;
            $NVKD_TQ_CSAT += $value->TOTAL_NVKD_CSAT;

            $NVTK_TQ_CSAT1 += $value->NVTK_CSAT_1;
            $NVTK_TQ_CSAT2 += $value->NVTK_CSAT_2;
            $NVTK_TQ_CSAT12 += $value->NVTK_CSAT_12;
            $NVTK_TQ_CUS_CSAT += $value->TOTAL_NVTK_CUS_CSAT;
            $NVTK_TQ_CSAT += $value->TOTAL_NVTK_CSAT;
            
            $NVBT_TQ_CSAT1 += $value->NVBT_CSAT_1;
            $NVBT_TQ_CSAT2 += $value->NVBT_CSAT_2;
            $NVBT_TQ_CSAT12 += $value->NVBT_CSAT_12;
            $NVBT_TQ_CUS_CSAT += $value->TOTAL_NVBT_CUS_CSAT;
            $NVBT_TQ_CSAT += $value->TOTAL_NVBT_CSAT;
            
            ?>
            <tr>
                <td >
                    {{$value->section_location}}
                </td>
                <td>
                    {{$value->NVKD_CSAT_1}}
                </td>
                <td>
                    {{$value->NVKD_CSAT_2}}
                </td>
                <td>
                    {{$value->NVKD_CSAT_12}}
                </td>
                <td>
                    <?php
                    $rateNotSastisfied = (($value->TOTAL_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_CSAT_12 / $value->TOTAL_NVKD_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td>
                    <?php
                    $csatAverage = (($value->TOTAL_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_NVKD_CSAT / $value->TOTAL_NVKD_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td>
                    {{$value->NVTK_CSAT_1}}
                </td>
                <td>
                    {{$value->NVTK_CSAT_2}}
                </td>
                <td>
                    {{$value->NVTK_CSAT_12}}
                </td>
                <td>
                    <?php
                    $rateNotSastisfied = (($value->TOTAL_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_CSAT_12 / $value->TOTAL_NVTK_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td>
                    <?php
                    $csatAverage = (($value->TOTAL_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_NVTK_CSAT / $value->TOTAL_NVTK_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                
                <td>
                    {{$value->NVBT_CSAT_1}}
                </td>
                <td>
                    {{$value->NVBT_CSAT_2}}
                </td>
                <td>
                    {{$value->NVBT_CSAT_12}}
                </td>
                <td>
                    <?php
                    $rateNotSastisfied = (($value->TOTAL_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_CSAT_12 / $value->TOTAL_NVBT_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td>
                    <?php
                    $csatAverage = (($value->TOTAL_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_NVBT_CSAT / $value->TOTAL_NVBT_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                
            </tr>
            <?php
            }
            ?>
            <tr>
                <td class="foot_average">
                    {{trans($transfile.'.Total')}}
                </td>
                <td class="foot_average">
                    {{$NVKD_TQ_CSAT1}}
                </td>
                <td class="foot_average">
                    {{$NVKD_TQ_CSAT2}}
                </td>
                <td class="foot_average">
                    {{$NVKD_TQ_CSAT12}}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVKD_TQ_CUS_CSAT) != 0) ? round(($NVKD_TQ_CSAT12 / $NVKD_TQ_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVKD_TQ_CUS_CSAT) != 0) ? round(($NVKD_TQ_CSAT / $NVKD_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVTK_TQ_CSAT1}}
                </td>
                <td class="foot_average">
                    {{$NVTK_TQ_CSAT2}}
                </td>
                <td class="foot_average">
                    {{ $NVTK_TQ_CSAT12}}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVTK_TQ_CUS_CSAT) != 0) ? round(($NVTK_TQ_CSAT12 / $NVTK_TQ_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVTK_TQ_CUS_CSAT) != 0) ? round(($NVTK_TQ_CSAT / $NVTK_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

               
                <td class="foot_average">
                    {{$NVBT_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVBT_TQ_CUS_CSAT ) != 0) ? round(($NVBT_TQ_CSAT12 / $NVBT_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVBT_TQ_CUS_CSAT ) != 0) ? round(($NVBT_TQ_CSAT / $NVBT_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

               
               
            </tr>
            </tbody>

        </table>

        {{--<div class="row">--}}
            <h3 class="header smaller lighter red btn-group">
                <i class="icon-table"></i>
                {{trans($transfile.'.StatisticalOfUnsatisfactionCustomerForService')}}
            </h3>
        {{--</div>--}}
        {{--Thống kê theo vùng--}}
        <table id="CSAT12ServiceReportRegion" class="table table-striped table-bordered table-hover table-CSAT12ServiceReport "  cellspacing="0" width= "100%" style="max-width: 100%;">
            <thead>
            <tr>
                <th class="text-center">{{trans($transfile.'.TouchPoint')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.TotalOfUnsatisfactionCase')}}</th>
            </tr>
            <tr>
                <th class="text-center evaluate-cell">{{trans($transfile.'.Location')}}</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.TotalCsat12')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.RatioOfSatisfaction')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.AverageCsat')}}</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.TotalCsat12')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.RatioOfSatisfaction')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.AverageCsat')}}</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.TotalCsat12')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.RatioOfSatisfaction')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.AverageCsat')}}</th>

            </tr>
            </thead>

            <tbody>
            <?php
            $Internet_TQ_CSAT1 = $Internet_TQ_CSAT2 = $Internet_TQ_CSAT12 = $Internet_TQ_CUS_CSAT = $Internet_TQ_CSAT
                = $Internet_SBT_TQ_CSAT1 = $Internet_SBT_TQ_CSAT2 = $Internet_SBT_TQ_CSAT12 = $Internet_SBT_TQ_CUS_CSAT = $Internet_SBT_TQ_CSAT =
            $Internet_KHL_TQ_CSAT1 = $Internet_KHL_TQ_CSAT2 = $Internet_KHL_TQ_CSAT12 = $Internet_KHL_TQ_CUS_CSAT = $Internet_KHL_TQ_CSAT = 0;

            foreach ($surveyCSATService12 as $key => $value) {
            $Internet_TQ_CSAT1 += $value->INTERNET_CSAT_1;
            $Internet_TQ_CSAT2 += $value->INTERNET_CSAT_2;
            $Internet_TQ_CSAT12 += $value->INTERNET_CSAT_12;
            $Internet_TQ_CUS_CSAT += $value->TOTAL_INTERNET_CUS_CSAT;
            $Internet_TQ_CSAT += $value->TOTAL_INTERNET_CSAT;
            

            $Internet_SBT_TQ_CSAT1 += $value->INTERNET_SBT_CSAT_1;
            $Internet_SBT_TQ_CSAT2 += $value->INTERNET_SBT_CSAT_2;
            $Internet_SBT_TQ_CSAT12 += $value->INTERNET_SBT_CSAT_12;
            $Internet_SBT_TQ_CUS_CSAT += $value->TOTAL_SBT_INTERNET_CUS_CSAT;
            $Internet_SBT_TQ_CSAT += $value->TOTAL_SBT_INTERNET_CSAT;


            $Internet_KHL_TQ_CSAT1 += $value->INTERNET_CSAT_1 + $value->INTERNET_SBT_CSAT_1 ;
            $Internet_KHL_TQ_CSAT2 += $value->INTERNET_CSAT_2 + $value->INTERNET_SBT_CSAT_2 ;
            $Internet_KHL_TQ_CSAT12 += $value->INTERNET_CSAT_12 + $value->INTERNET_SBT_CSAT_12 ;
            $Internet_KHL_TQ_CUS_CSAT += $value->TOTAL_INTERNET_CUS_CSAT +  $value->TOTAL_SBT_INTERNET_CUS_CSAT ;
            $Internet_KHL_TQ_CSAT += $value->TOTAL_INTERNET_CSAT + $value->TOTAL_SBT_INTERNET_CSAT;

            ?>
            <tr>
                <td >
                    {{$value->section_location}}
                </td>
                <td>
                    {{$value->INTERNET_CSAT_1}}
                </td>
                <td>
                    {{$value->INTERNET_CSAT_2}}
                </td>
                <td>
                    {{$value->INTERNET_CSAT_12}}
                </td>
                <td>
                    <?php
                    $rateNotSastisfied = (($value->TOTAL_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_CSAT_12 / $value->TOTAL_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td>
                    <?php
                    $csatAverage = (($value->TOTAL_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_INTERNET_CSAT / $value->TOTAL_INTERNET_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td>
                    {{$value->INTERNET_SBT_CSAT_1}}
                </td>
                <td>
                    {{$value->INTERNET_SBT_CSAT_2}}
                </td>
                <td>
                    {{$value->INTERNET_SBT_CSAT_12}}
                </td>
                <td>
                    <?php
                    $rateNotSastisfied = (($value->TOTAL_SBT_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_SBT_CSAT_12 / $value->TOTAL_SBT_INTERNET_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td>
                    <?php
                    $csatAverage = (($value->TOTAL_SBT_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_SBT_INTERNET_CSAT / $value->TOTAL_SBT_INTERNET_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>


                <td>
                    {{$value->INTERNET_CSAT_1 + $value->INTERNET_SBT_CSAT_1}}
                </td>
                <td>
                    {{$value->INTERNET_CSAT_2 + $value->INTERNET_SBT_CSAT_2}}
                </td>
                <td>
                    {{$value->INTERNET_CSAT_12 + $value->INTERNET_SBT_CSAT_12}}
                </td>
                <td>
                    <?php
                    $sumTotal = $value->TOTAL_INTERNET_CUS_CSAT + $value->TOTAL_SBT_INTERNET_CUS_CSAT ;
                    $rateNotSastisfied = (($sumTotal) != 0) ? round((($value->INTERNET_CSAT_12  + $value->INTERNET_SBT_CSAT_12 ) / $sumTotal) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td>
                    <?php
                    $csatAverage = (($sumTotal) != 0) ? round(($value->TOTAL_INTERNET_CSAT  + $value->TOTAL_SBT_INTERNET_CSAT ) / $sumTotal, 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>


            </tr>
            <?php
            }
            ?>
            <tr>
                <td class="foot_average">
                    {{trans($transfile.'.Total')}}
                </td>
                <td class="foot_average">
                    {{$Internet_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$Internet_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$Internet_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_TQ_CUS_CSAT ) != 0) ? round(($Internet_TQ_CSAT12 / $Internet_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_TQ_CUS_CSAT ) != 0) ? round(($Internet_TQ_CSAT / $Internet_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

               
                <td class="foot_average">
                    {{$Internet_SBT_TQ_CSAT1  }}
                </td>
                <td class="foot_average">
                    {{$Internet_SBT_TQ_CSAT2  }}
                </td>
                <td class="foot_average">
                    {{$Internet_SBT_TQ_CSAT12  }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_SBT_TQ_CUS_CSAT ) != 0) ? round(($Internet_SBT_TQ_CSAT12 / $Internet_SBT_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_SBT_TQ_CUS_CSAT ) != 0) ? round(($Internet_SBT_TQ_CSAT / $Internet_SBT_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>


                <td class="foot_average">
                    {{$Internet_KHL_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$Internet_KHL_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$Internet_KHL_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_KHL_TQ_CUS_CSAT ) != 0) ? round(($Internet_KHL_TQ_CSAT12 / $Internet_KHL_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_KHL_TQ_CUS_CSAT ) != 0) ? round(($Internet_KHL_TQ_CSAT / $Internet_KHL_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

            </tr>
            </tbody>

        </table>

        <h3 class="header smaller lighter red">
            <i class="icon-table"></i>
            {{trans($transfile.'.StatisticalOfCsatAction12ForStaff')}}
        </h3>
        <table id="table-CSAT12ActionServiceReport" class="table table-striped table-bordered table-hover"  cellspacing="0" width= "100%" style="max-width: 100%;overflow: auto;">
            <thead>
            <tr>
                <th class="text-center evaluate-cell"> {{trans($transfile.'.TouchPoint')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.Maintenance')}}</th>
                <th colspan="2" class="text-center">  {{trans($transfile.'.Total')}}</th>
            </tr>
            <tr>
                <th rowspan="2" colspan="1" class="text-center evaluate-cell"> {{trans($transfile.'.ResolvedActionOfStaff')}}</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
            </tr>
            <tr>
                <th colspan="1" class="text-center">{{trans($transfile.'.Quantity')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.Percent')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.Quantity')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.Percent')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.Quantity')}}</th>
                <th colspan="1" class="text-center">{{trans($transfile.'.Percent')}}</th>
            </tr>
            </thead>
            <tbody>

            <?php
            //            $mapAction = [115 => 'Xin lỗi KH và Đóng', 116 => 'Chuyển phòng ban', 117 => 'Tạo Prechecklist',
            //                118 => 'Tạo checklist', 119 => 'Tạo CL Indo', 128 => 'Khác'];
            //            $netMap = $arrayResultCombine['TYPE']['NET'];
            //            $tvMap = $arrayResultCombine['TYPE']['TV'];
            //            $totalService = $arrayResultCombine['TOTAL'];
            foreach ($surveyCSATActionService12 as $key => $value) {
            ?>
            <tr>
                <td>
                    {{trans($transfile.'.'.$value->action)}}
                </td>
                <td >
                    {{$value->INTERNET_CSAT_12}}
                </td>
                <td>
                    <?php
                    //                        if (isset($netMap[$key]))
                    $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_CSAT_12) != 0) ? round(($value->INTERNET_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_CSAT_12) * 100, 2) : 0;
                    //                        else
                    //                            $rateAction = 0;
                    ?>
                    {{$rateAction.'%'}}
                </td>
                

                <td>
                    {{$value->INTERNET_SBT_CSAT_12}}
                </td>
                <td>
                    <?php
                    //                        if (isset($tvMap[$key]))
                    $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_SBT_CSAT_12) != 0) ? round(($value->INTERNET_SBT_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_SBT_CSAT_12) * 100, 2) : 0;
                    //                        else
                    //                            $rateAction = 0;
                    ?>
                    {{$rateAction.'%'}}
                </td>

             
                <td>
                    {{$value->TOTAL_INTERNET_CSAT_12}}
                </td>
                <td>
                    <?php
                    //                        if (isset($tvMap[$key]))
                    $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TOTAL_INTERNET_CSAT_12) != 0) ? round(($value->TOTAL_INTERNET_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TOTAL_INTERNET_CSAT_12) * 100, 2) : 0;
                    //                        else
                    //                            $rateAction = 0;
                    ?>
                    {{$rateAction.'%'}}
                </td>

              
            </tr>
            <?php } ?>

            </tbody>

        </table>


        <script type="text/javascript">
            $(document).ready(function () {
//        var tableCSATObjectReport = $('#table-CSATObjectReport').dataTable({
//        "bAutoWidth": false,
//                "aoColumns": [
//                {"sType": 'numeric', "bSortable": false}
//    <?php
                //    for ($i = 1; $i <= 31; $i++) {
                //        echo ",null";
                //    }
                ?>//
//                ],
//                "bJQueryUI": false,
//                "oLanguage": {
//                "sLengthMenu": "Hiển thị _MENU_ dòng mỗi trang",
//                        "sZeroRecords": "Không tìm thấy",
//                        "sInfo": "Có _START_ tới _END_ của _TOTAL_ bản ghi",
//                        "sInfoEmpty": "Có 0 tới 0 của 0 bản ghi",
//                        "sInfoFiltered": "(lọc từ _MAX_ tổng số bản ghi)",
//                        "sSearch": "Tìm kiếm"
//                },
//                "bFilter": false,
//                "bLengthChange": false,
//                "bPaginate": false,
//                "bInfo": false,
//                "bSort": false,
//                'sDom': '"top"i'
//        });
//                 $('#viewLocationStaffButton').click(function(){
//                     $('#CSAT12StaffReportRegion').css('display', 'inline-block')
//                     $('#CSAT12StaffReportBranch').css('display', 'none')
//                 })
//                 $('#viewBranchStaffButton').click(function(){
//                     $('#CSAT12StaffReportRegion').css('display', 'none')
//                     $('#CSAT12StaffReportBranch').css('display', 'inline-block')
//                 })

                // $('#viewLocationServiceButton').click(function(){
                //     $('#CSAT12ServiceReportRegion').css('display', 'inline-block')
                //     $('#CSAT12ServiceReportBranch').css('display', 'none')
                // })
                // $('#viewBranchServiceButton').click(function(){
                //     $('#CSAT12ServiceReportRegion').css('display', 'none')
                //     $('#CSAT12ServiceReportBranch').css('display', 'inline-block')
                // })
                var tableCSAT12StaffReport = $('.table-CSAT12StaffReport').dataTable({
                    "bAutoWidth": false,
                    "aoColumns": [
                        {"sType": 'numeric', "bSortable": false}
                        <?php
                        for ($i = 1; $i <= 15; $i++) {
                            echo ",null";
                        }
                        ?>
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
                var tableCSAT12ServiceReportRegion = $('.table-CSAT12ServiceReport').dataTable({
                    "bAutoWidth": false,
                    "aoColumns": [
                        {"sType": 'numeric', "bSortable": false}
                        <?php
                        for ($i = 1; $i <= 15; $i++) {
                            echo ",null";
                        }
                        ?>
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
                var tableCSAT12ActionServiceReport = $('#table-CSAT12ActionServiceReport').dataTable({
                    "bAutoWidth": false,
                    "aoColumns": [
                        {"sType": 'numeric', "bSortable": false}
                        <?php
                        for ($i = 1; $i <= 6; $i++) {
                            echo ",null";
                        }
                        ?>
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
            });</script>
        <?php } ?>


<input type="hidden" id="typeReport" value="4">
</div>
<script type="text/javascript">
    $(document).ready(function () {
    //table
    var tableCSATRegion = $('#table-CSATReport').dataTable({
    "bAutoWidth": false,
            "aoColumns": [
            {"sType": 'numeric', "bSortable": false}
<?php
for ($i = 1; $i <= 10; $i++) {
    echo ",null";
}
?>
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
        colors: ['#f2546e', '#fad735', '#4ec95e', '#9CAF18', '#FAD735', '#BE6BC5', '#426FB2', '#4EC95E', '#18902F', '#ED7D31', '#3F51B5']
        });
        // Build the chart
        var arr =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVKinhDoanh']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVKinhDoanh']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVKinhDoanh']; ?>
        }];
        var arr2 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVTrienKhai']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVTrienKhai']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVTrienKhai']; ?>
        }];
        var arr3 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVu_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVu_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVu_Net']; ?>
        }];

        var arr5 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVBaoTri']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVBaoTri']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVBaoTri']; ?>
        }];
        var arr6 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTri_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTri_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTri_Net']; ?>
        }];

        createChart('chartCSAT', arr);
        createChart('chartCSAT2', arr2);
        createChart('chartCSAT3', arr3);

        // createChart('chartCSAT4', arr4);
        // createChart('chartCSAT13', arr13);
        // createChart('chartCSAT14', arr14);
        // createChart('chartCSAT15', arr15);
        // createChart('chartCSAT16', arr16);
        // createChart('chartCSAT17', arr17);
        // createChart('chartCSAT18', arr18);
        // createChart('chartCSAT19', arr19);
        // createChart('chartCSAT20', arr20);
        // createChart('chartCSAT21', arr21);
        // createChart('chartCSAT22', arr22);
        // createChart('chartCSAT23', arr23);
        // createChart('chartCSAT24', arr24);
        // createChart('chartCSAT25', arr25);
        // createChart('chartCSAT26', arr26);

        createChart('chartCSAT5', arr5);
        createChart('chartCSAT6', arr6);

        // createChart('chartCSAT7', arr7);
        // createChart('chartCSAT8', arr8);
        // createChart('chartCSAT9', arr9);
        // createChart('chartCSAT10', arr10);
        // createChart('chartCSAT11', arr11);
        // createChart('chartCSAT12', arr12);
<?php } ?>
    });
    function createChart(id, data) {
    $('#' + id).highcharts({
    chart: {
    plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
    },
            title: false,
            tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
            },
            plotOptions: {
            pie: {
            allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                    enabled: false
                    },
                    showInLegend: true
            }
            },
            legend: {
            enabled: false
            },
            exporting: {enabled: true}, credits: {enabled: false},
            series: [{
            name: 'chiếm',
                    colorByPoint: true,
                    data:
                    data

            }]
    });
    }
</script>
<style>
    #table-CSATReport_wrapper, #table-CSAT12ServiceReport_wrapper,#table-CSAT12StaffReport_wrapper, #table-CSAT12ActionServiceReport_wrapper
    {
        overflow: auto;
    }
</style>