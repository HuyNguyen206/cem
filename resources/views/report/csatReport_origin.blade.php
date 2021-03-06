<?php
//dump($totalCSATHMI);die;
$transfile = 'report';
$note = ['CSAT_12' => 'CSAT 1 & 2',
    'CSAT_3' => 'CSAT 3',
    'CSAT_45' => 'CSAT 4 & 5'];
$arrTotalPercent = $arrUnsatisfiedPercent = $arrNeutralPercent = $arrSatisfiedPercent = [
    'NVKinhDoanh' => 0,
    'NVTrienKhai' => 0,
    'DGDichVu_Net' => 0,
    'DGDichVu_TV' => 0,
    'NVKinhDoanhTS' => 0,
    'NVTrienKhaiTS' => 0,
    'DGDichVuTS_Net' => 0,
    'DGDichVuTS_TV' => 0,
    'NVBaoTriTIN' => 0,
    'NVBaoTriINDO' => 0,
    'DVBaoTriTIN_Net' => 0,
    'DVBaoTriTIN_TV' => 0,
    'DVBaoTriINDO_Net' => 0,
    'DVBaoTriINDO_TV' => 0,
    'NVBaoTriHIFPT_TIN' => 0,
    'NVBaoTriHIFPT_INDO' => 0,
    'DVBaoTriHIFPT_TIN_Net' => 0,
    'DVBaoTriHIFPT_TIN_TV' => 0,
    'DVBaoTriHIFPT_INDO_Net' => 0,
    'DVBaoTriHIFPT_INDO_TV' => 0,
    'NVThuCuoc' => 0,
    'DGDichVu_MobiPay_Net' => 0,
    'DGDichVu_MobiPay_TV' => 0,
    
    'DGDichVu_Counter' => 0,
    'NV_Counter' => 0,
    'TQ_HMI' => 0,
    
    'NVKinhDoanhSS' => 0,
    'NVTrienKhaiSS' => 0,
    'DGDichVuSS_Net' => 0,
    'DGDichVuSS_TV' => 0,
    'NVBT_SSW' => 0,
    'DGDichVuSSW_Net' => 0,
    'DGDichVuSSW_TV' => 0,];
//var_dump($survey);
//die;
?>
<div class="table-responsive">
    <?php if (!isset($viewFrom)) { ?>
        <div class="row text-center" style="padding: 10px">
            <?php
            if (empty($region)) {
                $textRegion = 'Toàn quốc';
            } else {
                $textRegion = 'Vùng ' . $region;
            }
//            $name = '';
//            foreach ($branch as $v){arrayResultCombine
//                $name .= $v->name.', ';
//            }
//            $name = substr($name, 0, strlen($name) - 2);
//            $textBranches = 'Chi nhánh: '.$name;
            ?>
            <text x="910" text-anchor="middle" class="highcharts-title" zIndex="4" style="color:#333333;font-size:18px;fill:#333333;width:1756px;  font-family: 'Lucida Grande', 'Lucida Sans Unicode', Arial, Helvetica, sans-serif;" y="24">
            <span>Đánh giá độ hài lòng của khách hàng - Điểm CSAT</span>
            <br/>
            <span x="910" dy="21">{{$textRegion}}</span>
            <br/>
    <!--            <span x="910" dy="21"></span>
            <br/>-->
            <span x="910" dy="21">Thời gian: {{date('d/m/Y',strtotime($from_date)) .' - '. date('d/m/Y',strtotime($to_date))}}</span>
            </text>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p>
                    <span class="label label-info" style="background-color:#f2546e !important " >&emsp;</span>&emsp;{{$note['CSAT_12']}}&emsp;
                    <span class="label label-warning" style="background-color:#fad735  !important" >&emsp;</span>&emsp;{{$note['CSAT_3']}}&emsp;
                    <span class="label label-success" style="background-color:#4ec95e  !important" >&emsp;</span>&emsp;{{$note['CSAT_45']}}&emsp;
                </p>
            </div>
        </div>
        <div class="row">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">{{trans($transfile.'.Deployment')}}</div>
                        <div class="panel-body" style="padding: 0; ">
                            <div class="col-xs-3" style="padding: 0;text-align: center;">
                                <div id="chartCSAT" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Saler Rating')}}</label>
                            </div>
                            <div class="col-xs-3" style="padding: 0;text-align: center;">
                                <div id="chartCSAT2" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Deployer Rating')}}</label>
                            </div>
                            <div class="col-xs-3" style="padding: 0;text-align: center;">
                                <div id="chartCSAT3" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Net')}}</label>
                            </div>
                            <div class="col-xs-3" style="padding: 0;text-align: center;">
                                <div id="chartCSAT4"  style="height: 200px;"></div>
                                <label>{{trans($transfile.'.TV')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">{{trans($transfile.'.Telesale Deployment')}}</div>
                        <div class="panel-body" style="padding: 0; ">
                            <div class="col-xs-3" style="padding: 0;text-align: center;">
                                <div id="chartCSAT13" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Saler Rating')}}</label>
                            </div>
                            <div class="col-xs-3" style="padding: 0;text-align: center;">
                                <div id="chartCSAT14" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Deployer Rating')}}</label>
                            </div>
                            <div class="col-xs-3" style="padding: 0;text-align: center;">
                                <div id="chartCSAT15" style="height: 200px;"></div>
                                <label>{{trans($transfile.'.Net')}}</label>
                            </div>
                            <div class="col-xs-3" style="padding: 0;text-align: center;">
                                <div id="chartCSAT16"  style="height: 200px;"></div>
                                <label>{{trans($transfile.'.TV')}}</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!--
                        <div class="col-xs-2">
                            
                        </div>-->

            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.Maintenance TIN-PNC')}}</div>
                    <div class="panel-body" style="padding: 0">
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT5" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Maintainance Employer')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT6" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Net')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT7" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.TV')}}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.Maintenance INDO')}}</div>
                    <div class="panel-body" style="padding: 0">
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT8" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Maintainance Employer')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT9" style="height: 200px;" ></div>
                            <label>{{trans($transfile.'.Net')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT10" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.TV')}}</label>
                        </div>
                    </div>
                </div>
            </div>
            
             <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.SBTHITIN')}}</div>
                    <div class="panel-body" style="padding: 0">
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT28" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Maintainance Employer')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT29" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Net')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT30" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.TV')}}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.SBTHIINDO')}}</div>
                    <div class="panel-body" style="padding: 0">
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT31" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Maintainance Employer')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT32" style="height: 200px;" ></div>
                            <label>{{trans($transfile.'.Net')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT33" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.TV')}}</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.Maintenance MobiPay')}}</div>
                    <div class="panel-body" style="padding: 0">
                        <div class="col-xs-4" style="padding: 0;text-align: center;margin-bottom: 60px;">
                            <div id="chartCSAT11" style="height: 200px;" ></div>
                            <label>{{trans($transfile.'.Net')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT12" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.TV')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT17" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.TC')}}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.After Paid Counter')}}</div>
                    <div class="panel-body" style="padding: 0">
                        <div class="col-xs-4" style="padding: 0;text-align: center;margin-bottom: 60px;">
                            <div id="chartCSAT19" style="height: 200px;" ></div>
                            <label>{{trans($transfile.'.Transaction Staff Counter')}}</label>
                        </div>   
                        <div class="col-xs-4" style="padding: 0;text-align: center;margin-bottom: 60px;">
                            <div id="chartCSAT18" style="height: 200px;" ></div>
                            <label>{{trans($transfile.'.Rating Service')}}</label>
                        </div>   
                        <div class="col-xs-4" style="padding: 0;text-align: center;margin-bottom: 60px;">
                            <div id="chartCSAT27" style="height: 200px;" ></div>
                            <label>HMI</label>
                        </div>   

                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.After Sale Staff')}}</div>
                    <div class="panel-body" style="padding: 0; ">
                        <div class="col-xs-3" style="padding: 0;text-align: center;">
                            <div id="chartCSAT20" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Saler Rating')}}</label>
                        </div>
                        <div class="col-xs-3" style="padding: 0;text-align: center;">
                            <div id="chartCSAT21" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Deployer Rating')}}</label>
                        </div>
                        <div class="col-xs-3" style="padding: 0;text-align: center;">
                            <div id="chartCSAT22" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Net')}}</label>
                        </div>
                        <div class="col-xs-3" style="padding: 0;text-align: center;">
                            <div id="chartCSAT23"  style="height: 200px;"></div>
                            <label>{{trans($transfile.'.TV')}}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{trans($transfile.'.After Swap')}}</div>
                    <div class="panel-body" style="padding: 0">
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT24" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.SSW')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT25" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.Net')}}</label>
                        </div>
                        <div class="col-xs-4" style="padding: 0;text-align: center;">
                            <div id="chartCSAT26" style="height: 200px;"></div>
                            <label>{{trans($transfile.'.TV')}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        Sự hài lòng khách hàng - Tổng hợp điểm CSAT
    </h3>

<?php } ?>
<table id="table-CSATReport" class="table table-striped table-bordered table-hover" cellspacing="0">
    <thead>
        <tr>
            <th rowspan="4" class="text-center evaluate-cell">{{trans($transfile.'.Rating Point')}}</th>
            <th colspan="8" class="text-center">{{trans($transfile.'.Deployment')}}</th>
            <th colspan="8" class="text-center">{{trans($transfile.'.Telesale Deployment')}}</th>
            <th colspan="6" class="text-center">{{trans($transfile.'.Maintenance TIN-PNC')}}</th>
            <th colspan="6" class="text-center">{{trans($transfile.'.Maintenance INDO')}}</th>
            <th colspan="6" class="text-center">{{trans($transfile.'.SBTHITIN')}}</th>
            <th colspan="6" class="text-center">{{trans($transfile.'.SBTHIINDO')}}</th>
            <th colspan="6" class="text-center">{{trans($transfile.'.After Paid')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.After Paid Counter')}}</th>
            <th colspan="8"  class="text-center evaluate-cell">{{trans($transfile.'.After Sale Staff')}}</th>
            <th colspan="6" class="text-center">{{trans($transfile.'.After Swap')}}</th>
        </tr>
        <tr>

            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Saler')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Deployer')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Rating Service')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Saler')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Deployer')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Rating Service')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.TIN-PNC')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Service TIN-PNC')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.INDO')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Service INDO')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Maintenance Staff Hi FPT TIN-PNC')}} </th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Service TIN-PNC')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Maintenance Staff Hi FPT INDO')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Service INDO')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Staff')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Rating Service')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Transaction Staff Counter')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Rating Service')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Saler')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.Deployer')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Rating Service')}}</th>
            <th colspan="2" rowspan="2" class="text-center">{{trans($transfile.'.SSW')}}</th>
            <th colspan="4" class="text-center">{{trans($transfile.'.Rating Service')}}</th>
        </tr>
        <tr>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.Net')}}</th>
            <th colspan="2" class="text-center">{{trans($transfile.'.TV')}}</th>
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
            <th>{{trans($transfile.'.Quantity')}}</th>
            <th>{{trans($transfile.'.Percent')}}</th>
            <th>{{trans($transfile.'.Quantity')}}</th>
            <th>{{trans($transfile.'.Percent')}}</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if (!empty($survey)) {
            $NVKinhDoanhTSPercent = $NVTrienKhaiTSPercent = $DGDichVuTS_Net_Percent = $DGDichVuTS_TV_Percent = $NVKinhDoanhPercent = $NVTrienKhaiPercent = $DGDichVu_Net_Percent = $DGDichVu_TV_Percent 
            = $NVBaoTriTINPercent = $NVBaoTriINDOPercent = $DVBaoTriTIN_Net_Percent = $DVBaoTriTIN_TV_Percent = $DVBaoTriINDO_Net_Percent = $DVBaoTriINDO_TV_Percent 
            = $NVBaoTriHIFPT_TINPercent = $NVBaoTriHIFPT_INDOPercent = $DVBaoTriHIFPT_TIN_Net_Percent = $DVBaoTriHIFPT_TIN_TV_Percent = $DVBaoTriHIFPT_INDO_Net_Percent = $DVBaoTriHIFPT_INDO_TV_Percent
            = $DGDichVu_MobiPay_Net_Percent = $DGDichVu_MobiPay_TV_Percent = $NVThuCuoc_Percent = $DGDichVu_Counter_Percent = $NV_Counter_Percent= $TQ_HMI_Percent = $NVKinhDoanhSSPercent = $NVTrienKhaiSSPercent = $DGDichVuSS_Net_Percent = $DGDichVuSS_TV_Percent = $NVBTSSWPercent = $DGDichVuSSW_Net_Percent = $DGDichVuSSW_TV_Percent = 0;
            $arrCSAT = $arrCSATX = [];
            $emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
            foreach ($survey as $res) {
                ?>
                <tr>
                    <td><span>{{$res->DanhGia}} <img src="{{asset("assets/img/".$emotions[$res->answers_point])}}" style="width: 25px;height: 25px;float: right;"></span></td>
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
                    if ($total['DGDichVu_TV'] > 0) {
                        $DGDichVu_TV_Percent = ($res->DGDichVu_TV / $total['DGDichVu_TV']) * 100;
                        $DGDichVu_TV_Percent = round($DGDichVu_TV_Percent, 2);
                        $arrTotalPercent['DGDichVu_TV'] += $DGDichVu_TV_Percent;
                    }
                    if ($total['NVKinhDoanhTS'] > 0) {
                        $NVKinhDoanhTSPercent = ($res->NVKinhDoanhTS / $total['NVKinhDoanhTS']) * 100;
                        $NVKinhDoanhTSPercent = round($NVKinhDoanhTSPercent, 2);
                        $arrTotalPercent['NVKinhDoanhTS'] += $NVKinhDoanhTSPercent;
                    }
                    if ($total['NVTrienKhaiTS'] > 0) {
                        $NVTrienKhaiTSPercent = ($res->NVTrienKhaiTS / $total['NVTrienKhaiTS']) * 100;
                        $NVTrienKhaiTSPercent = round($NVTrienKhaiTSPercent, 2);
                        $arrTotalPercent['NVTrienKhaiTS'] += $NVTrienKhaiTSPercent;
                    }
                    if ($total['DGDichVuTS_Net'] > 0) {
                        $DGDichVuTS_Net_Percent = ($res->DGDichVuTS_Net / $total['DGDichVuTS_Net']) * 100;
                        $DGDichVuTS_Net_Percent = round($DGDichVuTS_Net_Percent, 2);
                        $arrTotalPercent['DGDichVuTS_Net'] += $DGDichVuTS_Net_Percent;
                    }
                    if ($total['DGDichVuTS_TV'] > 0) {
                        $DGDichVuTS_TV_Percent = ($res->DGDichVuTS_TV / $total['DGDichVuTS_TV']) * 100;
                        $DGDichVuTS_TV_Percent = round($DGDichVuTS_TV_Percent, 2);
                        $arrTotalPercent['DGDichVuTS_TV'] += $DGDichVuTS_TV_Percent;
                    }
                    if ($total['NVBaoTriTIN'] > 0) {
                        $NVBaoTriTINPercent = ($res->NVBaoTriTIN / $total['NVBaoTriTIN']) * 100;
                        $NVBaoTriTINPercent = round($NVBaoTriTINPercent, 2);
                        $arrTotalPercent['NVBaoTriTIN'] += $NVBaoTriTINPercent;
                    }
                    if ($total['NVBaoTriINDO'] > 0) {
                        $NVBaoTriINDOPercent = ($res->NVBaoTriINDO / $total['NVBaoTriINDO']) * 100;
                        $NVBaoTriINDOPercent = round($NVBaoTriINDOPercent, 2);
                        $arrTotalPercent['NVBaoTriINDO'] += $NVBaoTriINDOPercent;
                    }
                    if ($total['DVBaoTriTIN_Net'] > 0) {
                        $DVBaoTriTIN_Net_Percent = ($res->DVBaoTriTIN_Net / $total['DVBaoTriTIN_Net']) * 100;
                        $DVBaoTriTIN_Net_Percent = round($DVBaoTriTIN_Net_Percent, 2);
                        $arrTotalPercent['DVBaoTriTIN_Net'] += $DVBaoTriTIN_Net_Percent;
                    }
                    if ($total['DVBaoTriTIN_TV'] > 0) {
                        $DVBaoTriTIN_TV_Percent = ($res->DVBaoTriTIN_TV / $total['DVBaoTriTIN_TV']) * 100;
                        $DVBaoTriTIN_TV_Percent = round($DVBaoTriTIN_TV_Percent, 2);
                        $arrTotalPercent['DVBaoTriTIN_TV'] += $DVBaoTriTIN_TV_Percent;
                    }
                    if ($total['DVBaoTriINDO_Net'] > 0) {
                        $DVBaoTriINDO_Net_Percent = ($res->DVBaoTriINDO_Net / $total['DVBaoTriINDO_Net']) * 100;
                        $DVBaoTriINDO_Net_Percent = round($DVBaoTriINDO_Net_Percent, 2);
                        $arrTotalPercent['DVBaoTriINDO_Net'] += $DVBaoTriINDO_Net_Percent;
                    }
                    if ($total['DVBaoTriINDO_TV'] > 0) {
                        $DVBaoTriINDO_TV_Percent = ($res->DVBaoTriINDO_TV / $total['DVBaoTriINDO_TV']) * 100;
                        $DVBaoTriINDO_TV_Percent = round($DVBaoTriINDO_TV_Percent, 2);
                        $arrTotalPercent['DVBaoTriINDO_TV'] += $DVBaoTriINDO_TV_Percent;
                    }
                     if ($total['NVBaoTriHIFPT_TIN'] > 0) {
                        $NVBaoTriHIFPT_TINPercent = ($res->NVBaoTriHIFPT_TIN / $total['NVBaoTriHIFPT_TIN']) * 100;
                        $NVBaoTriHIFPT_TINPercent = round($NVBaoTriHIFPT_TINPercent, 2);
                        $arrTotalPercent['NVBaoTriHIFPT_TIN'] += $NVBaoTriHIFPT_TINPercent;
                    }
                    if ($total['NVBaoTriHIFPT_INDO'] > 0) {
                        $NVBaoTriHIFPT_INDOPercent = ($res->NVBaoTriHIFPT_INDO / $total['NVBaoTriHIFPT_INDO']) * 100;
                        $NVBaoTriHIFPT_INDOPercent = round($NVBaoTriHIFPT_INDOPercent, 2);
                        $arrTotalPercent['NVBaoTriHIFPT_INDO'] += $NVBaoTriHIFPT_INDOPercent;
                    }
                    if ($total['DVBaoTriHIFPT_TIN_Net'] > 0) {
                        $DVBaoTriHIFPT_TIN_Net_Percent = ($res->DVBaoTriHIFPT_TIN_Net / $total['DVBaoTriHIFPT_TIN_Net']) * 100;
                        $DVBaoTriHIFPT_TIN_Net_Percent = round($DVBaoTriHIFPT_TIN_Net_Percent, 2);
                        $arrTotalPercent['DVBaoTriHIFPT_TIN_Net'] += $DVBaoTriHIFPT_TIN_Net_Percent;
                    }
                    if ($total['DVBaoTriHIFPT_TIN_TV'] > 0) {
                        $DVBaoTriHIFPT_TIN_TV_Percent = ($res->DVBaoTriHIFPT_TIN_TV / $total['DVBaoTriHIFPT_TIN_TV']) * 100;
                        $DVBaoTriHIFPT_TIN_TV_Percent = round($DVBaoTriHIFPT_TIN_TV_Percent, 2);
                        $arrTotalPercent['DVBaoTriHIFPT_TIN_TV'] += $DVBaoTriHIFPT_TIN_TV_Percent;
                    }
                    if ($total['DVBaoTriHIFPT_INDO_Net'] > 0) {
                        $DVBaoTriHIFPT_INDO_Net_Percent = ($res->DVBaoTriHIFPT_INDO_Net / $total['DVBaoTriHIFPT_INDO_Net']) * 100;
                        $DVBaoTriHIFPT_INDO_Net_Percent = round($DVBaoTriHIFPT_INDO_Net_Percent, 2);
                        $arrTotalPercent['DVBaoTriHIFPT_INDO_Net'] += $DVBaoTriHIFPT_INDO_Net_Percent;
                    }
                    if ($total['DVBaoTriHIFPT_INDO_TV'] > 0) {
                        $DVBaoTriHIFPT_INDO_TV_Percent = ($res->DVBaoTriHIFPT_INDO_TV / $total['DVBaoTriHIFPT_INDO_TV']) * 100;
                        $DVBaoTriHIFPT_INDO_TV_Percent = round($DVBaoTriHIFPT_INDO_TV_Percent, 2);
                        $arrTotalPercent['DVBaoTriHIFPT_INDO_TV'] += $DVBaoTriHIFPT_INDO_TV_Percent;
                    }
                    if ($total['NVThuCuoc'] > 0) {
                        $NVThuCuoc_Percent = ($res->NVThuCuoc / $total['NVThuCuoc']) * 100;
                        $NVThuCuoc_Percent = round($NVThuCuoc_Percent, 2);
                        $arrTotalPercent['NVThuCuoc'] += $NVThuCuoc_Percent;
                    }
                    if ($total['DGDichVu_MobiPay_Net'] > 0) {
                        $DGDichVu_MobiPay_Net_Percent = ($res->DGDichVu_MobiPay_Net / $total['DGDichVu_MobiPay_Net']) * 100;
                        $DGDichVu_MobiPay_Net_Percent = round($DGDichVu_MobiPay_Net_Percent, 2);
                        $arrTotalPercent['DGDichVu_MobiPay_Net'] += $DGDichVu_Net_Percent;
                    }
                    if ($total['DGDichVu_MobiPay_TV'] > 0) {
                        $DGDichVu_MobiPay_TV_Percent = ($res->DGDichVu_MobiPay_TV / $total['DGDichVu_MobiPay_TV']) * 100;
                        $DGDichVu_MobiPay_TV_Percent = round($DGDichVu_MobiPay_TV_Percent, 2);
                        $arrTotalPercent['DGDichVu_MobiPay_TV'] += $DGDichVu_MobiPay_TV_Percent;
                    }
                    if ($total['DGDichVu_Counter'] > 0) {
                        $DGDichVu_Counter_Percent = ($res->DGDichVu_Counter / $total['DGDichVu_Counter']) * 100;
                        $DGDichVu_Counter_Percent = round($DGDichVu_Counter_Percent, 2);
                        $arrTotalPercent['DGDichVu_Counter'] += $DGDichVu_Counter_Percent;
                    }
                    if ($total['NV_Counter'] > 0) {
                        $NV_Counter_Percent = ($res->NV_Counter / $total['NV_Counter']) * 100;
                        $NV_Counter_Percent = round($NV_Counter_Percent, 2);
                        $arrTotalPercent['NV_Counter'] += $NV_Counter_Percent;
                    }
                     if ($total['TQ_HMI'] > 0) {
                        $TQ_HMI_Percent = ($res->TQ_HMI / $total['TQ_HMI']) * 100;
                        $TQ_HMI_Percent = round($TQ_HMI_Percent, 2);
                        $arrTotalPercent['TQ_HMI'] += $TQ_HMI_Percent;
                    }
                    if ($total['NVKinhDoanhSS'] > 0) {
                        $NVKinhDoanhSSPercent = ($res->NVKinhDoanhSS / $total['NVKinhDoanhSS']) * 100;
                        $NVKinhDoanhSSPercent = round($NVKinhDoanhSSPercent, 2);
                        $arrTotalPercent['NVKinhDoanhSS'] += $NVKinhDoanhSSPercent;
                    }
                    if ($total['NVTrienKhaiSS'] > 0) {
                        $NVTrienKhaiSSPercent = ($res->NVTrienKhaiSS / $total['NVTrienKhaiSS']) * 100;
                        $NVTrienKhaiSSPercent = round($NVTrienKhaiSSPercent, 2);
                        $arrTotalPercent['NVTrienKhaiSS'] += $NVTrienKhaiSSPercent;
                    }
                    if ($total['DGDichVuSS_Net'] > 0) {
                        $DGDichVuSS_Net_Percent = ($res->DGDichVuSS_Net / $total['DGDichVuSS_Net']) * 100;
                        $DGDichVuSS_Net_Percent = round($DGDichVuSS_Net_Percent, 2);
                        $arrTotalPercent['DGDichVuSS_Net'] += $DGDichVuSS_Net_Percent;
                    }
                    if ($total['DGDichVuSS_TV'] > 0) {
                        $DGDichVuSS_TV_Percent = ($res->DGDichVuSS_TV / $total['DGDichVuSS_TV']) * 100;
                        $DGDichVuSS_TV_Percent = round($DGDichVuSS_TV_Percent, 2);
                        $arrTotalPercent['DGDichVuSS_TV'] += $DGDichVuSS_TV_Percent;
                    }

                    if ($total['NVBT_SSW'] > 0) {
                        $NVBTSSWPercent = ($res->NVBT_SSW / $total['NVBT_SSW']) * 100;
                        $NVBTSSWPercent = round($NVBTSSWPercent, 2);
                        $arrTotalPercent['NVBT_SSW'] += $NVBTSSWPercent;
                    }
                    if ($total['DGDichVuSSW_Net'] > 0) {
                        $DGDichVuSSW_Net_Percent = ($res->DGDichVuSSW_Net / $total['DGDichVuSSW_Net']) * 100;
                        $DGDichVuSSW_Net_Percent = round($DGDichVuSSW_Net_Percent, 2);
                        $arrTotalPercent['DGDichVuSSW_Net'] += $DGDichVuSSW_Net_Percent;
                    }
                    if ($total['DGDichVuSSW_TV'] > 0) {
                        $DGDichVuSSW_TV_Percent = ($res->DGDichVuSSW_TV / $total['DGDichVuSSW_TV']) * 100;
                        $DGDichVuSSW_TV_Percent = round($DGDichVuSSW_TV_Percent, 2);
                        $arrTotalPercent['DGDichVuSSW_TV'] += $DGDichVuSSW_TV_Percent;
                    }

                    //% các điểm ko hài lòng, trung lập, hài lòng
                    if ($res->answers_point >= 1 && $res->answers_point <= 2) {//điểm không hài lòng
                        $arrUnsatisfiedPercent['NVKinhDoanh'] += $NVKinhDoanhPercent;
                        $arrUnsatisfiedPercent['NVTrienKhai'] += $NVTrienKhaiPercent;
                        $arrUnsatisfiedPercent['DGDichVu_Net'] += $DGDichVu_Net_Percent;
                        $arrUnsatisfiedPercent['DGDichVu_TV'] += $DGDichVu_TV_Percent;

                        $arrUnsatisfiedPercent['NVKinhDoanhTS'] += $NVKinhDoanhTSPercent;
                        $arrUnsatisfiedPercent['NVTrienKhaiTS'] += $NVTrienKhaiTSPercent;
                        $arrUnsatisfiedPercent['DGDichVuTS_Net'] += $DGDichVuTS_Net_Percent;
                        $arrUnsatisfiedPercent['DGDichVuTS_TV'] += $DGDichVuTS_TV_Percent;

                        $arrUnsatisfiedPercent['NVBaoTriTIN'] += $NVBaoTriTINPercent;
                        $arrUnsatisfiedPercent['NVBaoTriINDO'] += $NVBaoTriINDOPercent;
                        $arrUnsatisfiedPercent['DVBaoTriTIN_Net'] += $DVBaoTriTIN_Net_Percent;
                        $arrUnsatisfiedPercent['DVBaoTriTIN_TV'] += $DVBaoTriTIN_TV_Percent;
                        $arrUnsatisfiedPercent['DVBaoTriINDO_Net'] += $DVBaoTriINDO_Net_Percent;
                        $arrUnsatisfiedPercent['DVBaoTriINDO_TV'] += $DVBaoTriINDO_TV_Percent;
                        
                        $arrUnsatisfiedPercent['NVBaoTriHIFPT_TIN'] += $NVBaoTriHIFPT_TINPercent;
                        $arrUnsatisfiedPercent['NVBaoTriHIFPT_INDO'] += $NVBaoTriHIFPT_INDOPercent;
                        $arrUnsatisfiedPercent['DVBaoTriHIFPT_TIN_Net'] += $DVBaoTriHIFPT_TIN_Net_Percent;
                        $arrUnsatisfiedPercent['DVBaoTriHIFPT_TIN_TV'] += $DVBaoTriHIFPT_TIN_TV_Percent;
                        $arrUnsatisfiedPercent['DVBaoTriHIFPT_INDO_Net'] += $DVBaoTriHIFPT_INDO_Net_Percent;
                        $arrUnsatisfiedPercent['DVBaoTriHIFPT_INDO_TV'] += $DVBaoTriHIFPT_INDO_TV_Percent;

                        $arrUnsatisfiedPercent['DGDichVu_MobiPay_Net'] += $DGDichVu_MobiPay_Net_Percent;
                        $arrUnsatisfiedPercent['DGDichVu_MobiPay_TV'] += $DGDichVu_MobiPay_TV_Percent;
                        $arrUnsatisfiedPercent['NVThuCuoc'] += $NVThuCuoc_Percent;
                        $arrUnsatisfiedPercent['DGDichVu_Counter'] += $DGDichVu_Counter_Percent;
                        $arrUnsatisfiedPercent['NV_Counter'] += $NV_Counter_Percent;
                        
                        $arrUnsatisfiedPercent['TQ_HMI'] += $TQ_HMI_Percent;

                        $arrUnsatisfiedPercent['NVKinhDoanhSS'] += $NVKinhDoanhSSPercent;
                        $arrUnsatisfiedPercent['NVTrienKhaiSS'] += $NVTrienKhaiSSPercent;
                        $arrUnsatisfiedPercent['DGDichVuSS_Net'] += $DGDichVuSS_Net_Percent;
                        $arrUnsatisfiedPercent['DGDichVuSS_TV'] += $DGDichVuSS_TV_Percent;

                        $arrUnsatisfiedPercent['NVBT_SSW'] += $NVBTSSWPercent;
                        $arrUnsatisfiedPercent['DGDichVuSSW_Net'] += $DGDichVuSSW_Net_Percent;
                        $arrUnsatisfiedPercent['DGDichVuSSW_TV'] += $DGDichVuSSW_TV_Percent;
                    }
                    if ($res->answers_point == 3) {//điểm trung lập
                        $arrNeutralPercent['NVKinhDoanh'] += $NVKinhDoanhPercent;
                        $arrNeutralPercent['NVTrienKhai'] += $NVTrienKhaiPercent;
                        $arrNeutralPercent['DGDichVu_Net'] += $DGDichVu_Net_Percent;
                        $arrNeutralPercent['DGDichVu_TV'] += $DGDichVu_TV_Percent;

                        $arrNeutralPercent['NVKinhDoanhTS'] += $NVKinhDoanhTSPercent;
                        $arrNeutralPercent['NVTrienKhaiTS'] += $NVTrienKhaiTSPercent;
                        $arrNeutralPercent['DGDichVuTS_Net'] += $DGDichVuTS_Net_Percent;
                        $arrNeutralPercent['DGDichVuTS_TV'] += $DGDichVuTS_TV_Percent;

                        $arrNeutralPercent['NVBaoTriTIN'] += $NVBaoTriTINPercent;
                        $arrNeutralPercent['NVBaoTriINDO'] += $NVBaoTriINDOPercent;
                        $arrNeutralPercent['DVBaoTriTIN_Net'] += $DVBaoTriTIN_Net_Percent;
                        $arrNeutralPercent['DVBaoTriTIN_TV'] += $DVBaoTriTIN_TV_Percent;
                        $arrNeutralPercent['DVBaoTriINDO_Net'] += $DVBaoTriINDO_Net_Percent;
                        $arrNeutralPercent['DVBaoTriINDO_TV'] += $DVBaoTriINDO_TV_Percent;
                        
                        $arrNeutralPercent['NVBaoTriHIFPT_TIN'] += $NVBaoTriHIFPT_TINPercent;
                        $arrNeutralPercent['NVBaoTriHIFPT_INDO'] += $NVBaoTriHIFPT_INDOPercent;
                        $arrNeutralPercent['DVBaoTriHIFPT_TIN_Net'] += $DVBaoTriHIFPT_TIN_Net_Percent;
                        $arrNeutralPercent['DVBaoTriHIFPT_TIN_TV'] += $DVBaoTriHIFPT_TIN_TV_Percent;
                        $arrNeutralPercent['DVBaoTriHIFPT_INDO_Net'] += $DVBaoTriHIFPT_INDO_Net_Percent;
                        $arrNeutralPercent['DVBaoTriHIFPT_INDO_TV'] += $DVBaoTriHIFPT_INDO_TV_Percent;
                        
                        $arrNeutralPercent['DGDichVu_MobiPay_Net'] += $DGDichVu_MobiPay_Net_Percent;
                        $arrNeutralPercent['DGDichVu_MobiPay_TV'] += $DGDichVu_MobiPay_TV_Percent;

                        $arrNeutralPercent['NVThuCuoc'] += $NVThuCuoc_Percent;
                        $arrNeutralPercent['DGDichVu_Counter'] += $DGDichVu_Counter_Percent;
                        $arrNeutralPercent['NV_Counter'] += $NV_Counter_Percent;
                        
                        $arrNeutralPercent['TQ_HMI'] += $TQ_HMI_Percent;

                        $arrNeutralPercent['NVKinhDoanhSS'] += $NVKinhDoanhSSPercent;
                        $arrNeutralPercent['NVTrienKhaiSS'] += $NVTrienKhaiSSPercent;
                        $arrNeutralPercent['DGDichVuSS_Net'] += $DGDichVuSS_Net_Percent;
                        $arrNeutralPercent['DGDichVuSS_TV'] += $DGDichVuSS_TV_Percent;

                        $arrNeutralPercent['NVBT_SSW'] += $NVBTSSWPercent;
                        $arrNeutralPercent['DGDichVuSSW_Net'] += $DGDichVuSSW_Net_Percent;
                        $arrNeutralPercent['DGDichVuSSW_TV'] += $DGDichVuSSW_TV_Percent;
                    }
                    if ($res->answers_point >= 4 && $res->answers_point <= 5) {//điểm hài lòng
                        $arrSatisfiedPercent['NVKinhDoanh'] += $NVKinhDoanhPercent;
                        $arrSatisfiedPercent['NVTrienKhai'] += $NVTrienKhaiPercent;
                        $arrSatisfiedPercent['DGDichVu_Net'] += $DGDichVu_Net_Percent;
                        $arrSatisfiedPercent['DGDichVu_TV'] += $DGDichVu_TV_Percent;

                        $arrSatisfiedPercent['NVKinhDoanhTS'] += $NVKinhDoanhTSPercent;
                        $arrSatisfiedPercent['NVTrienKhaiTS'] += $NVTrienKhaiTSPercent;
                        $arrSatisfiedPercent['DGDichVuTS_Net'] += $DGDichVuTS_Net_Percent;
                        $arrSatisfiedPercent['DGDichVuTS_TV'] += $DGDichVuTS_TV_Percent;

                        $arrSatisfiedPercent['NVBaoTriTIN'] += $NVBaoTriTINPercent;
                        $arrSatisfiedPercent['NVBaoTriINDO'] += $NVBaoTriINDOPercent;
                        $arrSatisfiedPercent['DVBaoTriTIN_Net'] += $DVBaoTriTIN_Net_Percent;
                        $arrSatisfiedPercent['DVBaoTriTIN_TV'] += $DVBaoTriTIN_TV_Percent;
                        $arrSatisfiedPercent['DVBaoTriINDO_Net'] += $DVBaoTriINDO_Net_Percent;
                        $arrSatisfiedPercent['DVBaoTriINDO_TV'] += $DVBaoTriINDO_TV_Percent;
                        
                        $arrSatisfiedPercent['NVBaoTriHIFPT_TIN'] += $NVBaoTriHIFPT_TINPercent;
                        $arrSatisfiedPercent['NVBaoTriHIFPT_INDO'] += $NVBaoTriHIFPT_INDOPercent;
                        $arrSatisfiedPercent['DVBaoTriHIFPT_TIN_Net'] += $DVBaoTriHIFPT_TIN_Net_Percent;
                        $arrSatisfiedPercent['DVBaoTriHIFPT_TIN_TV'] += $DVBaoTriHIFPT_TIN_TV_Percent;
                        $arrSatisfiedPercent['DVBaoTriHIFPT_INDO_Net'] += $DVBaoTriHIFPT_INDO_Net_Percent;
                        $arrSatisfiedPercent['DVBaoTriHIFPT_INDO_TV'] += $DVBaoTriHIFPT_INDO_TV_Percent;
                        
                        $arrSatisfiedPercent['DGDichVu_MobiPay_Net'] += $DGDichVu_MobiPay_Net_Percent;
                        $arrSatisfiedPercent['DGDichVu_MobiPay_TV'] += $DGDichVu_MobiPay_TV_Percent;

                        $arrSatisfiedPercent['NVThuCuoc'] += $NVThuCuoc_Percent;
                        $arrSatisfiedPercent['DGDichVu_Counter'] += $DGDichVu_Counter_Percent;
                        $arrSatisfiedPercent['NV_Counter'] += $NV_Counter_Percent;
                        
                        $arrSatisfiedPercent['TQ_HMI'] += $TQ_HMI_Percent;
                         
                        $arrSatisfiedPercent['NVKinhDoanhSS'] += $NVKinhDoanhSSPercent;
                        $arrSatisfiedPercent['NVTrienKhaiSS'] += $NVTrienKhaiSSPercent;
                        $arrSatisfiedPercent['DGDichVuSS_Net'] += $DGDichVuSS_Net_Percent;
                        $arrSatisfiedPercent['DGDichVuSS_TV'] += $DGDichVuSS_TV_Percent;

                        $arrSatisfiedPercent['NVBT_SSW'] += $NVBTSSWPercent;
                        $arrSatisfiedPercent['DGDichVuSSW_Net'] += $DGDichVuSSW_Net_Percent;
                        $arrSatisfiedPercent['DGDichVuSSW_TV'] += $DGDichVuSSW_TV_Percent;
                    }
                    ?>
                    <td><span class="number">{{$res->NVKinhDoanh}}</span></td>
                    <td><span class="number">{{$NVKinhDoanhPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->NVTrienKhai}}</span></td>
                    <td><span class="number">{{$NVTrienKhaiPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVu_Net}}</span></td>
                    <td><span class="number">{{$DGDichVu_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVu_TV}}</span></td>
                    <td><span class="number">{{$DGDichVu_TV_Percent.'%'}}</span></td>

                    <td><span class="number">{{$res->NVKinhDoanhTS}}</span></td>
                    <td><span class="number">{{$NVKinhDoanhTSPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->NVTrienKhaiTS}}</span></td>
                    <td><span class="number">{{$NVTrienKhaiTSPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVuTS_Net}}</span></td>
                    <td><span class="number">{{$DGDichVuTS_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVuTS_TV}}</span></td>
                    <td><span class="number">{{$DGDichVuTS_TV_Percent.'%'}}</span></td>

                    <td><span class="number">{{$res->NVBaoTriTIN}}</span></td>
                    <td><span class="number">{{$NVBaoTriTINPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTriTIN_Net}}</span></td>
                    <td><span class="number">{{$DVBaoTriTIN_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTriTIN_TV}}</span></td>
                    <td><span class="number">{{$DVBaoTriTIN_TV_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->NVBaoTriINDO}}</span></td>
                    <td><span class="number">{{$NVBaoTriINDOPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTriINDO_Net}}</span></td>
                    <td><span class="number">{{$DVBaoTriINDO_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTriINDO_TV}}</span></td>
                    <td><span class="number">{{$DVBaoTriINDO_TV_Percent.'%'}}</span></td>
                    
                    <td><span class="number">{{$res->NVBaoTriHIFPT_TIN}}</span></td>
                    <td><span class="number">{{$NVBaoTriHIFPT_TINPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTriHIFPT_TIN_Net}}</span></td>
                    <td><span class="number">{{$DVBaoTriHIFPT_TIN_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTriHIFPT_TIN_TV}}</span></td>
                    <td><span class="number">{{$DVBaoTriHIFPT_TIN_TV_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->NVBaoTriHIFPT_INDO}}</span></td>
                    <td><span class="number">{{$NVBaoTriHIFPT_INDOPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTriHIFPT_INDO_Net}}</span></td>
                    <td><span class="number">{{$DVBaoTriHIFPT_INDO_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DVBaoTriHIFPT_INDO_TV}}</span></td>
                    <td><span class="number">{{$DVBaoTriHIFPT_INDO_TV_Percent.'%'}}</span></td>

                    <td><span class="number">{{$res->NVThuCuoc}}</span></td>
                    <td><span class="number">{{$NVThuCuoc_Percent.'%'}}</span></td>                   
                    <td><span class="number">{{$res->DGDichVu_MobiPay_Net}}</span></td>
                    <td><span class="number">{{$DGDichVu_MobiPay_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVu_MobiPay_TV}}</span></td>
                    <td><span class="number">{{$DGDichVu_MobiPay_TV_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->NV_Counter}}</span></td>
                    <td><span class="number">{{$NV_Counter_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVu_Counter}}</span></td>
                    <td><span class="number">{{$DGDichVu_Counter_Percent.'%'}}</span></td>

                    <td><span class="number">{{$res->NVKinhDoanhSS}}</span></td>
                    <td><span class="number">{{$NVKinhDoanhSSPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->NVTrienKhaiSS}}</span></td>
                    <td><span class="number">{{$NVTrienKhaiSSPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVuSS_Net}}</span></td>
                    <td><span class="number">{{$DGDichVuSS_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVuSS_TV}}</span></td>
                    <td><span class="number">{{$DGDichVuSS_TV_Percent.'%'}}</span></td>

                    <td><span class="number">{{$res->NVBT_SSW}}</span></td>
                    <td><span class="number">{{$NVBTSSWPercent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVuSSW_Net}}</span></td>
                    <td><span class="number">{{$DGDichVuSSW_Net_Percent.'%'}}</span></td>
                    <td><span class="number">{{$res->DGDichVuSSW_TV}}</span></td>
                    <td><span class="number">{{$DGDichVuSSW_TV_Percent.'%'}}</span></td>
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
                    <td><span class="number">{{$total['DGDichVu_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVu_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_TV']).'%'}}</span></td>

                    <td><span class="number">{{$total['NVKinhDoanhTS']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVKinhDoanhTS'] === '99.99') ?'100%' :round($arrTotalPercent['NVKinhDoanhTS']).'%'}}</span></td>
                    <td><span class="number">{{$total['NVTrienKhaiTS']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVTrienKhaiTS'] === '99.99') ?'100%' :round($arrTotalPercent['NVTrienKhaiTS']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVuTS_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVuTS_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuTS_Net']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVuTS_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVuTS_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuTS_TV']).'%'}}</span></td>

                    <td><span class="number">{{$total['NVBaoTriTIN']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVBaoTriTIN'] === '99.99') ?'100%' :round($arrTotalPercent['NVBaoTriTIN']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTriTIN_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTriTIN_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriTIN_Net']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTriTIN_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTriTIN_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriTIN_TV']).'%'}}</span></td>
                    <td><span class="number">{{$total['NVBaoTriINDO']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVBaoTriINDO'] === '99.99') ?'100%' :round($arrTotalPercent['NVBaoTriINDO']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTriINDO_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTriINDO_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriINDO_Net']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTriINDO_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTriINDO_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriINDO_TV']).'%'}}</span></td>
                    
                    <td><span class="number">{{$total['NVBaoTriHIFPT_TIN']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVBaoTriHIFPT_TIN'] === '99.99') ?'100%' :round($arrTotalPercent['NVBaoTriHIFPT_TIN']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTriHIFPT_TIN_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTriHIFPT_TIN_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriHIFPT_TIN_Net']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTriHIFPT_TIN_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTriHIFPT_TIN_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriHIFPT_TIN_TV']).'%'}}</span></td>
                    <td><span class="number">{{$total['NVBaoTriHIFPT_INDO']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVBaoTriHIFPT_INDO'] === '99.99') ?'100%' :round($arrTotalPercent['NVBaoTriHIFPT_INDO']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTriHIFPT_INDO_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTriHIFPT_INDO_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriHIFPT_INDO_Net']).'%'}}</span></td>
                    <td><span class="number">{{$total['DVBaoTriHIFPT_INDO_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DVBaoTriHIFPT_INDO_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DVBaoTriHIFPT_INDO_TV']).'%'}}</span></td>

                    <td><span class="number">{{$total['NVThuCuoc']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVThuCuoc'] === '99.99') ?'100%' :round($arrTotalPercent['NVThuCuoc']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVu_MobiPay_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVu_MobiPay_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_MobiPay_Net']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVu_MobiPay_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVu_MobiPay_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_MobiPay_TV']).'%'}}</span></td>
                    <td><span class="number">{{$total['NV_Counter']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NV_Counter'] === '99.99') ?'100%' :round($arrTotalPercent['NV_Counter']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVu_Counter']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVu_Counter'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVu_Counter']).'%'}}</span></td>

                    <td><span class="number">{{$total['NVKinhDoanhSS']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVKinhDoanhSS'] === '99.99') ?'100%' :round($arrTotalPercent['NVKinhDoanhSS']).'%'}}</span></td>
                    <td><span class="number">{{$total['NVTrienKhaiSS']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVTrienKhaiSS'] === '99.99') ?'100%' :round($arrTotalPercent['NVTrienKhaiSS']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVuSS_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVuSS_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuSS_Net']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVuSS_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVuSS_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuSS_TV']).'%'}}</span></td>

                    <td><span class="number">{{$total['NVBT_SSW']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['NVBT_SSW'] === '99.99') ?'100%' :round($arrTotalPercent['NVBT_SSW']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVuSSW_Net']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVuSSW_Net'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuSSW_Net']).'%'}}</span></td>
                    <td><span class="number">{{$total['DGDichVuSSW_TV']}}</span></td>
                    <td><span class="number">{{((string)$arrTotalPercent['DGDichVuSSW_TV'] === '99.99') ?'100%' :round($arrTotalPercent['DGDichVuSSW_TV']).'%'}}</span></td>
                </tr>
                <tr>
                    <td class="foot_average"><span>{{trans($transfile.'.Average Point')}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVKinhDoanh']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVTrienKhai']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_TV']}}</span></td>

                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVKinhDoanhTS']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVTrienKhaiTS']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuTS_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuTS_TV']}}</span></td>

                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVBaoTriTIN']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriTIN_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriTIN_TV']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVBaoTriINDO']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriINDO_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriINDO_TV']}}</span></td>
                    
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVBaoTriHIFPT_TIN']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriHIFPT_TIN_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriHIFPT_TIN_TV']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVBaoTriHIFPT_INDO']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriHIFPT_INDO_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DVBaoTriHIFPT_INDO_TV']}}</span></td>

                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVThuCuoc']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_MobiPay_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_MobiPay_TV']}}</span></td>

                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NV_Counter']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVu_Counter']}}</span></td>

                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVKinhDoanhSS']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVTrienKhaiSS']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuSS_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuSS_TV']}}</span></td>

                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['NVBT_SSW']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuSSW_Net']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=2><span class="number">{{$avg['DGDichVuSSW_TV']}}</span></td>
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
        Tổng hợp Sự hài lòng của Khách hàng toàn quốc với các đối tượng được đánh giá
    </h3>
    <table id="table-CSATObjectReport" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%" style="max-width: 100%;">
        <thead>
            <tr>
                <th rowspan="3" class="text-center evaluate-cell" style="color: #307ecc">{{trans($transfile.'.Rating Point')}}</th>
                <th colspan="9" class="text-center">Đánh giá Chất lượng Dịch vụ</th>
                <th colspan="21" class="text-center">Đánh giá Nhân viên</th>
            </tr>
            <tr>

                <th colspan="3" class="text-center">CLDV Internet</th>
                <th colspan="3" class="text-center">CLDV Truyền hình</th>
                <th colspan="3" class="text-center">Tổng hợp CLDV Internet & TH</th>
                <th colspan="3" class="text-center">NVKD Salesman</th>
                <th colspan="3" class="text-center">NVKT TIN/PNC</th>
                <th colspan="3" class="text-center">NVKD TeleSales</th>
                <th colspan="3" class="text-center">NVTC tại nhà</th>
                <th colspan="3" class="text-center">NVGD tại quầy</th>
                <th colspan="3" class="text-center">NVKD tại quầy</th>
                <th colspan="3" class="text-center">Tổng hợp</th>
            </tr>
            <tr style="color: #307ecc">
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
                        <td><span>{{$res['Csat']}} <?php if ($key != 'total') { ?><img src="{{asset("assets/img/".$emotions[$key])}}" style="width: 25px;height: 25px;float: right;"> <?php } ?></span></td>

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



                        <td><span class="number">{{$res['Tv']}}</span></td>
                        <td><span class="number">{{$res['TvPercent']}}</span></td>
                        <?php
                        if ($key == 3 || $key == 'total') {
                            ?>
                            <td><span class="number">
                                    <?php
                                    echo $res['TvPercent'];
                                    ?>
                                </span></td>
                        <?php } else if ($key == 1 || $key == 4) {
                            ?>
                            <td rowspan="2"><span class="number">
                                    <?php
                                    if ($key == 1)
                                        echo $res['TvPercent'] + $totalCSAT[2]['TvPercent'] . '%';
                                    else
                                        echo $res['TvPercent'] + $totalCSAT[5]['TvPercent'] . '%';
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

                        <td><span class="number">{{$res['NVKinhDoanhTS']}}</span></td>
                        <td><span class="number">{{$res['NVKinhDoanhTSPercent']}}</span></td>
                        <?php
                        if ($key == 3 || $key == 'total') {
                            ?>
                            <td><span class="number">
                                    <?php
                                    echo $res['NVKinhDoanhTSPercent'];
                                    ?>
                                </span></td>
                        <?php } else if ($key == 1 || $key == 4) {
                            ?>
                            <td rowspan="2"><span class="number">
                                    <?php
                                    if ($key == 1)
                                        echo $res['NVKinhDoanhTSPercent'] + $totalCSAT[2]['NVKinhDoanhTSPercent'] . '%';
                                    else
                                        echo $res['NVKinhDoanhTSPercent'] + $totalCSAT[5]['NVKinhDoanhTSPercent'] . '%';
                                    ?>
                                </span></td>     
                            <?php
                        }
                        ?>
                        <td><span class="number">{{$res['NVThuCuoc']}}</span></td>
                        <td><span class="number">{{$res['NVThuCuocPercent']}}</span></td>
                        <?php
                        if ($key == 3 || $key == 'total') {
                            ?>
                            <td><span class="number">
                                    <?php
                                    echo $res['NVThuCuocPercent'];
                                    ?>
                                </span></td>
                        <?php } else if ($key == 1 || $key == 4) {
                            ?>
                            <td rowspan="2"><span class="number">
                                    <?php
                                    if ($key == 1)
                                        echo $res['NVThuCuocPercent'] + $totalCSAT[2]['NVThuCuocPercent'] . '%';
                                    else
                                        echo $res['NVThuCuocPercent'] + $totalCSAT[5]['NVThuCuocPercent'] . '%';
                                    ?>
                                </span></td>     
                            <?php
                        }
                        ?>

                        <td><span class="number">{{$res['NV_Counter']}}</span></td>
                        <td><span class="number">{{$res['NV_CounterPercent']}}</span></td>
                        <?php
                        if ($key == 3 || $key == 'total') {
                            ?>
                            <td><span class="number">
                                    <?php
                                    echo $res['NV_CounterPercent'];
                                    ?>
                                </span></td>
                        <?php } else if ($key == 1 || $key == 4) {
                            ?>
                            <td rowspan="2"><span class="number">
                                    <?php
                                    if ($key == 1)
                                        echo $res['NV_CounterPercent'] + $totalCSAT[2]['NV_CounterPercent'] . '%';
                                    else
                                        echo $res['NV_CounterPercent'] + $totalCSAT[5]['NV_CounterPercent'] . '%';
                                    ?>
                                </span></td>     
                            <?php
                        }
                        ?>

                        <td><span class="number">{{$res['NVKinhDoanhSS']}}</span></td>
                        <td><span class="number">{{$res['NVKinhDoanhSSPercent']}}</span></td>
                        <?php
                        if ($key == 3 || $key == 'total') {
                            ?>
                            <td><span class="number">
                                    <?php
                                    echo $res['NVKinhDoanhSSPercent'];
                                    ?>
                                </span></td>
                        <?php } else if ($key == 1 || $key == 4) {
                            ?>
                            <td rowspan="2"><span class="number">
                                    <?php
                                    if ($key == 1)
                                        echo $res['NVKinhDoanhSSPercent'] + $totalCSAT[2]['NVKinhDoanhSSPercent'] . '%';
                                    else
                                        echo $res['NVKinhDoanhSSPercent'] + $totalCSAT[5]['NVKinhDoanhSSPercent'] . '%';
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
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_TV']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NetAndTV']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NVKinhDoanh']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NVKT']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NVKinhDoanhTS']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NVThuCuoc']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NV_Counter']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_NVKinhDoanhSS']}}</span></td>
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePoint['ĐTB_TongHopNV']}}</span></td>
                </tr>
                <?php
//            }
            }
            ?>
        </tbody>

    </table>

    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        Tổng hợp Sự hài lòng của Khách hàng toàn quốc đối với trường hợp khảo sát tại quầy qua kênh ghi nhận HMI
    </h3>
    <table id="table-CSATHMIReport" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%" style="max-width: 100%;">
        <thead>
            <tr>
                <th class="text-center evaluate-cell" style="color: #307ecc">Điểm tiếp xúc Quầy Giao dịch<br> Kênh Màn hình cảm ứng
            </th>
            @foreach($allQGD as $value)
                <th colspan="3" class="text-center">{{$value}}</th>
            @endforeach
                
            </tr>
            <tr style="color: #307ecc">
                <th class="text-center evaluate-cell" style="color: #307ecc">Điểm đánh giá</th>
                @foreach($allQGD as $value)
                <th>Số lượng</th>
                <th>Tỷ lệ (%)</th>               
                <th>Tỷ lệ (%)</th>  
                @endforeach
            </tr>
        </thead>

        <tbody>
            <?php
            if (!empty($totalCSATHMI) && !empty($averagePointHMI)) {
//            $NVKinhDoanhTSPercent = $NVTrienKhaiTSPercent = $DGDichVuTS_Net_Percent = $DGDichVuTS_TV_Percent = $NVKinhDoanhPercent = $NVTrienKhaiPercent = $DGDichVu_Net_Percent = $DGDichVu_TV_Percent = $NVBaoTriTINPercent = $NVBaoTriINDOPercent = $DVBaoTriTIN_Net_Percent = $DVBaoTriTIN_TV_Percent = $DVBaoTriINDO_Net_Percent = $DVBaoTriINDO_TV_Percent = $DGDichVu_MobiPay_Net_Percent = $DGDichVu_MobiPay_TV_Percent = $NVThuCuoc_Percent = $DGDichVu_Counter_Percent = $NV_Counter_Percent = $NVKinhDoanhSSPercent = $NVTrienKhaiSSPercent = $DGDichVuSS_Net_Percent = $DGDichVuSS_TV_Percent = $NVBTSSWPercent = $DGDichVuSSW_Net_Percent = $DGDichVuSSW_TV_Percent = 0;
//            $arrCSAT = $arrCSATX = [];
                $emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
                foreach ($totalCSATHMI as $key => $res) {
                  
                    ?>
                    <tr class="<?php if ($key == 'total')
                echo 'foot';
                    ?>">
                        <td><span>{{$res['Csat']}} <?php if ($key != 'total') { ?><img src="{{asset("assets/img/".$emotions[$key])}}" style="width: 25px;height: 25px;float: right;"> <?php } ?></span></td>
<?php   foreach ($allQGD as $key2 => $value2){
    ?>
                        <td><span class="number">{{!empty($res[$value2]) ? $res[$value2] : 0}}</span></td>
                        <td><span class="number">{{!empty($res[$value2.'Percent']) ?  $res[$value2.'Percent'] : 0}}</span></td>
                        <?php
                        if ($key == 3 || $key == 'total') {
                            ?>
                            <td><span class="number">
                                    <?php
                                    echo !empty($res[$value2.'Percent']) ?  $res[$value2.'Percent'] : 0;
                                    ?>
                                </span></td>
                        <?php } else if ($key == 1 || $key == 4) {
                            ?>
                            <td rowspan="2"><span class="number">
                                    <?php
                                    if ($key == 1)
                                        echo !empty($res[$value2.'Percent']) ?  $res[$value2.'Percent'] : 0  + !empty($totalCSATHMI[2][$value2.'Percent']) ?  $totalCSATHMI[2][$value2.'Percent'] : 0  . '%';
                                    else
                                        echo  !empty($res[$value2.'Percent']) ?  $res[$value2.'Percent'] : 0  + !empty($totalCSATHMI[5][$value2.'Percent']) ?  $totalCSATHMI[5][$value2.'Percent'] : 0  . '%';
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
                    @foreach ($allQGD as $key3 => $value3)
                    <td class="foot_average" style="display: none"></td>
                    <td class="foot_average" colspan=3><span class="number">{{$averagePointHMI['ĐTB_'.$value3]}}</span></td>
                    @endforeach

                </tr>
                <?php
//            }
            }
            ?>
        </tbody>

    </table>

    <div class="row">
    <h3 class="header smaller lighter red btn-group">
        <i class="icon-table"></i>
        Khách hàng không hài lòng với nhân viên - CSAT 1, 2 nhân viên
    </h3>
    <div class="btn-group">
        <input name="viewStatus" id="viewStatusHidden" type="hidden" value=""/>
        <button id="viewLocationStaffButton" type="button" class="btn btn-success" style="height: 42px;margin-right: 3px;">Thống kê theo vùng</button>
        <button id="viewBranchStaffButton" type="button" class="btn btn-success" style="height: 42px;margin-right: 3px">Thống kê theo chi nhánh</button>
    </div>
    </div>
    {{--Thống kê theo vùng--}}
    <table  id ='CSAT12StaffReportRegion' class="table table-striped table-bordered table-hover table-CSAT12StaffReport "  cellspacing="0" width= "100%" style="max-width: 100%;overflow: auto;">
        <thead>
            <tr>
                <th rowspan="3" colspan="1" class="text-center evaluate-cell">Vùng</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.Telesale Deployment')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance TIN-PNC')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance INDO')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.SBTHITIN')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.SBTHIINDO')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.After Paid')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.After Paid Counter')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.After Sale Staff')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.After Swap')}}</th>
            </tr>
            <tr>

                <th colspan="5"  class="text-center">{{trans($transfile.'.Saler')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Deployer')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Saler')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Deployer')}}</th>
                <th colspan="5" class="text-center">NV bảo trì TIN-PNC</th>
                <th colspan="5" class="text-center">NV bảo trì INDO</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance Staff Hi FPT TIN-PNC')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance Staff Hi FPT INDO')}}</th>
                <th colspan="5" class="text-center">NV thu cước</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Transaction Staff Counter')}}</th>
                <th colspan="5"  class="text-center">{{trans($transfile.'.Saler')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.Deployer')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.SSW')}}</th>

            </tr>
            <tr>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $NVKD_IBB_TQ_CSAT1 = $NVKD_IBB_TQ_CSAT2 = $NVKD_IBB_TQ_CSAT12 = $NVKD_IBB_TQ_CUS_CSAT = $NVKD_IBB_TQ_CSAT = $NVTK_IBB_TQ_CSAT1 = $NVTK_IBB_TQ_CSAT2 = $NVTK_IBB_TQ_CSAT12 = $NVTK_IBB_TQ_CUS_CSAT = $NVTK_IBB_TQ_CSAT = $NVKD_TS_TQ_CSAT1 = $NVKD_TS_TQ_CSAT2 = $NVKD_TS_TQ_CSAT12 = $NVKD_TS_TQ_CUS_CSAT = $NVKD_TS_TQ_CSAT = $NVTK_TS_TQ_CSAT1 = $NVTK_TS_TQ_CSAT2 = $NVTK_TS_TQ_CSAT12 = $NVTK_TS_TQ_CUS_CSAT = $NVTK_TS_TQ_CSAT = $NVBT_TIN_TQ_CSAT1 = $NVBT_TIN_TQ_CSAT2 = $NVBT_TIN_TQ_CSAT12 = $NVBT_TIN_TQ_CUS_CSAT = $NVBT_TIN_TQ_CSAT = $NVBT_INDO_TQ_CSAT1 = $NVBT_INDO_TQ_CSAT2 = $NVBT_INDO_TQ_CSAT12 = $NVBT_INDO_TQ_CUS_CSAT = $NVBT_INDO_TQ_CSAT 
                    = $NVBT_HIFPT_TIN_TQ_CSAT1 = $NVBT_HIFPT_TIN_TQ_CSAT2 = $NVBT_HIFPT_TIN_TQ_CSAT12 = $NVBT_HIFPT_TIN_TQ_CUS_CSAT = $NVBT_HIFPT_TIN_TQ_CSAT = $NVBT_HIFPT_INDO_TQ_CSAT1 = $NVBT_HIFPT_INDO_TQ_CSAT2 = $NVBT_HIFPT_INDO_TQ_CSAT12 = $NVBT_HIFPT_INDO_TQ_CUS_CSAT = $NVBT_HIFPT_INDO_TQ_CSAT
                    = $NVThuCuoc_TQ_CSAT1 = $NVThuCuoc_TQ_CSAT2 = $NVThuCuoc_TQ_CSAT12 = $NVThuCuoc_TQ_CUS_CSAT = $NVThuCuoc_TQ_CSAT = $NVGDTQ_TQ_CSAT1 = $NVGDTQ_TQ_CSAT2 = $NVGDTQ_TQ_CSAT12 = $NVGDTQ_TQ_CUS_CSAT = $NVGDTQ_TQ_CSAT = $NVKD_SS_TQ_CSAT1 = $NVKD_SS_TQ_CSAT2 = $NVKD_SS_TQ_CSAT12 = $NVKD_SS_TQ_CUS_CSAT = $NVKD_SS_TQ_CSAT = $NVTK_SS_TQ_CSAT1 = $NVTK_SS_TQ_CSAT2 = $NVTK_SS_TQ_CSAT12 = $NVTK_SS_TQ_CUS_CSAT = $NVTK_SS_TQ_CSAT = $NVBT_SSW_TQ_CSAT1 = $NVBT_SSW_TQ_CSAT2 = $NVBT_SSW_TQ_CSAT12 = $NVBT_SSW_TQ_CUS_CSAT = $NVBT_SSW_TQ_CSAT = 0;
            foreach ($surveyCSAT12['resultStaffCsat12Region'] as $key => $value) {
                $NVKD_IBB_TQ_CSAT1 += $value->NVKD_IBB_CSAT_1;
                $NVKD_IBB_TQ_CSAT2 += $value->NVKD_IBB_CSAT_2;
                $NVKD_IBB_TQ_CSAT12 += $value->NVKD_IBB_CSAT_12;
                $NVKD_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_NVKD_CUS_CSAT;
                $NVKD_IBB_TQ_CSAT += $value->TOTAL_IBB_NVKD_CSAT;

                $NVTK_IBB_TQ_CSAT1 += $value->NVTK_IBB_CSAT_1;
                $NVTK_IBB_TQ_CSAT2 += $value->NVTK_IBB_CSAT_2;
                $NVTK_IBB_TQ_CSAT12 += $value->NVTK_IBB_CSAT_12;
                $NVTK_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_NVTK_CUS_CSAT;
                $NVTK_IBB_TQ_CSAT += $value->TOTAL_IBB_NVTK_CSAT;

                $NVKD_TS_TQ_CSAT1 += $value->NVKD_TS_CSAT_1;
                $NVKD_TS_TQ_CSAT2 += $value->NVKD_TS_CSAT_2;
                $NVKD_TS_TQ_CSAT12 += $value->NVKD_TS_CSAT_12;
                $NVKD_TS_TQ_CUS_CSAT += $value->TOTAL_TS_NVKD_CUS_CSAT;
                $NVKD_TS_TQ_CSAT += $value->TOTAL_TS_NVKD_CSAT;

                $NVTK_TS_TQ_CSAT1 += $value->NVTK_TS_CSAT_1;
                $NVTK_TS_TQ_CSAT2 += $value->NVTK_TS_CSAT_2;
                $NVTK_TS_TQ_CSAT12 += $value->NVTK_TS_CSAT_12;
                $NVTK_TS_TQ_CUS_CSAT += $value->TOTAL_TS_NVTK_CUS_CSAT;
                $NVTK_TS_TQ_CSAT += $value->TOTAL_TS_NVTK_CSAT;

                $NVBT_TIN_TQ_CSAT1 += $value->NVBT_TIN_CSAT_1;
                $NVBT_TIN_TQ_CSAT2 += $value->NVBT_TIN_CSAT_2;
                $NVBT_TIN_TQ_CSAT12 += $value->NVBT_TIN_CSAT_12;
                $NVBT_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_NVBT_CUS_CSAT;
                $NVBT_TIN_TQ_CSAT += $value->TOTAL_TIN_NVBT_CSAT;

                $NVBT_INDO_TQ_CSAT1 += $value->NVBT_INDO_CSAT_1;
                $NVBT_INDO_TQ_CSAT2 += $value->NVBT_INDO_CSAT_2;
                $NVBT_INDO_TQ_CSAT12 += $value->NVBT_INDO_CSAT_12;
                $NVBT_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_NVBT_CUS_CSAT;
                $NVBT_INDO_TQ_CSAT += $value->TOTAL_INDO_NVBT_CSAT;
                
                $NVBT_HIFPT_TIN_TQ_CSAT1 += $value->NVBT_HIFPT_TIN_CSAT_1;
                $NVBT_HIFPT_TIN_TQ_CSAT2 += $value->NVBT_HIFPT_TIN_CSAT_2;
                $NVBT_HIFPT_TIN_TQ_CSAT12 += $value->NVBT_HIFPT_TIN_CSAT_12;
                $NVBT_HIFPT_TIN_TQ_CUS_CSAT += $value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT;
                $NVBT_HIFPT_TIN_TQ_CSAT += $value->TOTAL_HIFPT_TIN_NVBT_CSAT;

                $NVBT_HIFPT_INDO_TQ_CSAT1 += $value->NVBT_HIFPT_INDO_CSAT_1;
                $NVBT_HIFPT_INDO_TQ_CSAT2 += $value->NVBT_HIFPT_INDO_CSAT_2;
                $NVBT_HIFPT_INDO_TQ_CSAT12 += $value->NVBT_HIFPT_INDO_CSAT_12;
                $NVBT_HIFPT_INDO_TQ_CUS_CSAT += $value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT;
                $NVBT_HIFPT_INDO_TQ_CSAT += $value->TOTAL_HIFPT_INDO_NVBT_CSAT;

                $NVThuCuoc_TQ_CSAT1 += $value->NVThuCuoc_CSAT_1;
                $NVThuCuoc_TQ_CSAT2 += $value->NVThuCuoc_CSAT_2;
                $NVThuCuoc_TQ_CSAT12 += $value->NVThuCuoc_CSAT_12;
                $NVThuCuoc_TQ_CUS_CSAT += $value->TOTAL_NVThuCuoc_CUS_CSAT;
                $NVThuCuoc_TQ_CSAT += $value->TOTAL_NVThuCuoc_CSAT;

                $NVGDTQ_TQ_CSAT1 += $value->NVGDTQ_CSAT_1;
                $NVGDTQ_TQ_CSAT2 += $value->NVGDTQ_CSAT_2;
                $NVGDTQ_TQ_CSAT12 += $value->NVGDTQ_CSAT_12;
                $NVGDTQ_TQ_CUS_CSAT += $value->TOTAL_NVGDTQ_CUS_CSAT;
                $NVGDTQ_TQ_CSAT += $value->TOTAL_NVGDTQ_CSAT;

                $NVKD_SS_TQ_CSAT1 += $value->NVKD_SS_CSAT_1;
                $NVKD_SS_TQ_CSAT2 += $value->NVKD_SS_CSAT_2;
                $NVKD_SS_TQ_CSAT12 += $value->NVKD_SS_CSAT_12;
                $NVKD_SS_TQ_CUS_CSAT += $value->TOTAL_SS_NVKD_CUS_CSAT;
                $NVKD_SS_TQ_CSAT += $value->TOTAL_SS_NVKD_CSAT;

                $NVTK_SS_TQ_CSAT1 += $value->NVTK_SS_CSAT_1;
                $NVTK_SS_TQ_CSAT2 += $value->NVTK_SS_CSAT_2;
                $NVTK_SS_TQ_CSAT12 += $value->NVTK_SS_CSAT_12;
                $NVTK_SS_TQ_CUS_CSAT += $value->TOTAL_SS_NVTK_CUS_CSAT;
                $NVTK_SS_TQ_CSAT += $value->TOTAL_SS_NVTK_CSAT;

                $NVBT_SSW_TQ_CSAT1 += $value->NVBT_SSW_CSAT_1;
                $NVBT_SSW_TQ_CSAT2 += $value->NVBT_SSW_CSAT_2;
                $NVBT_SSW_TQ_CSAT12 += $value->NVBT_SSW_CSAT_12;
                $NVBT_SSW_TQ_CUS_CSAT += $value->TOTAL_SSW_NVBT_CUS_CSAT;
                $NVBT_SSW_TQ_CSAT += $value->TOTAL_SSW_NVBT_CSAT;
                ?>
                <tr>
                    <td >
                        {{$value->section_sub_parent_desc}}
                    </td>
                    <td>
                        {{$value->NVKD_IBB_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVKD_IBB_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVKD_IBB_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_IBB_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_IBB_CSAT_12 / $value->TOTAL_IBB_NVKD_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_IBB_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_NVKD_CSAT / $value->TOTAL_IBB_NVKD_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->NVTK_IBB_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVTK_IBB_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVTK_IBB_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_IBB_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_IBB_CSAT_12 / $value->TOTAL_IBB_NVTK_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_IBB_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_NVTK_CSAT / $value->TOTAL_IBB_NVTK_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->NVKD_TS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVKD_TS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVKD_TS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_TS_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_TS_CSAT_12 / $value->TOTAL_TS_NVKD_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_TS_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_NVKD_CSAT / $value->TOTAL_TS_NVKD_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->NVTK_TS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVTK_TS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVTK_TS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_TS_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_TS_CSAT_12 / $value->TOTAL_TS_NVTK_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_TS_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_NVTK_CSAT / $value->TOTAL_TS_NVTK_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->NVBT_TIN_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVBT_TIN_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVBT_TIN_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_TIN_CSAT_12 / $value->TOTAL_TIN_NVBT_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_NVBT_CSAT / $value->TOTAL_TIN_NVBT_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->NVBT_INDO_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVBT_INDO_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVBT_INDO_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_INDO_CSAT_12 / $value->TOTAL_INDO_NVBT_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_NVBT_CSAT / $value->TOTAL_INDO_NVBT_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    
                    <td>
                        {{$value->NVBT_HIFPT_TIN_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVBT_HIFPT_TIN_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVBT_HIFPT_TIN_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_HIFPT_TIN_CSAT_12 / $value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_TIN_NVBT_CSAT / $value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->NVBT_HIFPT_INDO_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVBT_HIFPT_INDO_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVBT_HIFPT_INDO_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_HIFPT_INDO_CSAT_12 / $value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_INDO_NVBT_CSAT / $value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->NVThuCuoc_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVThuCuoc_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVThuCuoc_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_NVThuCuoc_CUS_CSAT) != 0) ? round(($value->NVThuCuoc_CSAT_12 / $value->TOTAL_NVThuCuoc_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_NVThuCuoc_CUS_CSAT) != 0) ? round(($value->TOTAL_NVThuCuoc_CSAT / $value->TOTAL_NVThuCuoc_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->NVGDTQ_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVGDTQ_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVGDTQ_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_NVGDTQ_CUS_CSAT) != 0) ? round(($value->NVGDTQ_CSAT_12 / $value->TOTAL_NVGDTQ_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_NVGDTQ_CUS_CSAT) != 0) ? round(($value->TOTAL_NVGDTQ_CSAT / $value->TOTAL_NVGDTQ_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->NVKD_SS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVKD_SS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVKD_SS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_SS_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_SS_CSAT_12 / $value->TOTAL_SS_NVKD_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_SS_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_NVKD_CSAT / $value->TOTAL_SS_NVKD_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->NVTK_SS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVTK_SS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVTK_SS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_SS_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_SS_CSAT_12 / $value->TOTAL_SS_NVTK_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_SS_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_NVTK_CSAT / $value->TOTAL_SS_NVTK_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->NVBT_SSW_CSAT_1}}
                    </td>
                    <td>
                        {{$value->NVBT_SSW_CSAT_2}}
                    </td>
                    <td>
                        {{$value->NVBT_SSW_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_SSW_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_SSW_CSAT_12 / $value->TOTAL_SSW_NVBT_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_SSW_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_NVBT_CSAT / $value->TOTAL_SSW_NVBT_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                </tr>
                <?php
            }
            ?>
            <tr>
                <td class="foot_average">
                    Tổng cộng
                </td>
                <td class="foot_average">
                    {{$NVKD_IBB_TQ_CSAT1}}
                </td>
                <td class="foot_average">
                    {{$NVKD_IBB_TQ_CSAT2}}
                </td>
                <td class="foot_average">
                    {{$NVKD_IBB_TQ_CSAT12}}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVKD_IBB_TQ_CUS_CSAT) != 0) ? round(($NVKD_IBB_TQ_CSAT12 / $NVKD_IBB_TQ_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVKD_IBB_TQ_CUS_CSAT) != 0) ? round(($NVKD_IBB_TQ_CSAT / $NVKD_IBB_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVTK_IBB_TQ_CSAT1}}
                </td>
                <td class="foot_average">
                    {{$NVTK_IBB_TQ_CSAT2}}
                </td>
                <td class="foot_average">
                    {{ $NVTK_IBB_TQ_CSAT12}}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVTK_IBB_TQ_CUS_CSAT) != 0) ? round(($NVTK_IBB_TQ_CSAT12 / $NVTK_IBB_TQ_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVTK_IBB_TQ_CUS_CSAT) != 0) ? round(($NVTK_IBB_TQ_CSAT / $NVTK_IBB_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVKD_TS_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVKD_TS_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVKD_TS_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVKD_TS_TQ_CUS_CSAT ) != 0) ? round(($NVKD_TS_TQ_CSAT12 / $NVKD_TS_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVKD_TS_TQ_CUS_CSAT ) != 0) ? round(($NVKD_TS_TQ_CSAT / $NVKD_TS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVTK_TS_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVTK_TS_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVTK_TS_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVTK_TS_TQ_CUS_CSAT ) != 0) ? round(($NVTK_TS_TQ_CSAT12 / $NVTK_TS_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVTK_TS_TQ_CUS_CSAT ) != 0) ? round(($NVTK_TS_TQ_CSAT / $NVTK_TS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVBT_TIN_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_TIN_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_TIN_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVBT_TIN_TQ_CUS_CSAT ) != 0) ? round(($NVBT_TIN_TQ_CSAT12 / $NVBT_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVBT_TIN_TQ_CUS_CSAT ) != 0) ? round(($NVBT_TIN_TQ_CSAT / $NVBT_TIN_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVBT_INDO_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_INDO_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_INDO_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVBT_INDO_TQ_CUS_CSAT ) != 0) ? round(($NVBT_INDO_TQ_CSAT12 / $NVBT_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVBT_INDO_TQ_CUS_CSAT ) != 0) ? round(($NVBT_INDO_TQ_CSAT / $NVBT_INDO_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                  <td class="foot_average">
                    {{$NVBT_HIFPT_TIN_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_HIFPT_TIN_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_HIFPT_TIN_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVBT_HIFPT_TIN_TQ_CUS_CSAT ) != 0) ? round(($NVBT_HIFPT_TIN_TQ_CSAT12 / $NVBT_HIFPT_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVBT_HIFPT_TIN_TQ_CUS_CSAT ) != 0) ? round(($NVBT_HIFPT_TIN_TQ_CSAT / $NVBT_HIFPT_TIN_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVBT_HIFPT_INDO_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_HIFPT_INDO_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVBT_HIFPT_INDO_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVBT_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($NVBT_HIFPT_INDO_TQ_CSAT12 / $NVBT_HIFPT_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVBT_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($NVBT_HIFPT_INDO_TQ_CSAT / $NVBT_HIFPT_INDO_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                
                <td class="foot_average">
                    {{$NVThuCuoc_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVThuCuoc_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVThuCuoc_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVThuCuoc_TQ_CUS_CSAT ) != 0) ? round(($NVThuCuoc_TQ_CSAT12 / $NVThuCuoc_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVThuCuoc_TQ_CUS_CSAT ) != 0) ? round(($NVThuCuoc_TQ_CSAT / $NVThuCuoc_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVGDTQ_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$NVGDTQ_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$NVGDTQ_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVGDTQ_TQ_CUS_CSAT ) != 0) ? round(($NVGDTQ_TQ_CSAT12 / $NVGDTQ_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVGDTQ_TQ_CUS_CSAT ) != 0) ? round(($NVGDTQ_TQ_CSAT / $NVGDTQ_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVKD_SS_TQ_CSAT1}}
                </td>
                <td class="foot_average">
                    {{$NVKD_SS_TQ_CSAT2}}
                </td>
                <td class="foot_average">
                    {{$NVKD_SS_TQ_CSAT12}}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVKD_SS_TQ_CUS_CSAT) != 0) ? round(($NVKD_SS_TQ_CSAT12 / $NVKD_SS_TQ_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVKD_SS_TQ_CUS_CSAT) != 0) ? round(($NVKD_SS_TQ_CSAT / $NVKD_SS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$NVTK_SS_TQ_CSAT1}}
                </td>
                <td class="foot_average">
                    {{$NVTK_SS_TQ_CSAT2}}
                </td>
                <td class="foot_average">
                    {{ $NVTK_SS_TQ_CSAT12}}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVTK_SS_TQ_CUS_CSAT) != 0) ? round(($NVTK_SS_TQ_CSAT12 / $NVTK_SS_TQ_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVTK_SS_TQ_CUS_CSAT) != 0) ? round(($NVTK_SS_TQ_CSAT / $NVTK_SS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td class="foot_average">
                    {{$NVBT_SSW_TQ_CSAT1}}
                </td>
                <td class="foot_average">
                    {{$NVBT_SSW_TQ_CSAT2}}
                </td>
                <td class="foot_average">
                    {{ $NVBT_SSW_TQ_CSAT12}}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($NVBT_SSW_TQ_CUS_CSAT) != 0) ? round(($NVBT_SSW_TQ_CSAT12 / $NVBT_SSW_TQ_CUS_CSAT) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($NVBT_SSW_TQ_CUS_CSAT) != 0) ? round(($NVBT_SSW_TQ_CSAT / $NVBT_SSW_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
            </tr>
        </tbody>

    </table>

    {{--Thống kê theo chi nhánh--}}
    <table id = "CSAT12StaffReportBranch" class="table table-striped table-bordered table-hover table-CSAT12StaffReport "  cellspacing="0" width= "100%" style="max-width: 100%;overflow: auto;display: none">
    <thead>
    <tr>
        <th rowspan="3" colspan="1" class="text-center evaluate-cell">Chi nhánh</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.Deployment')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.Telesale Deployment')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance TIN-PNC')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance INDO')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.SBTHITIN')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.SBTHIINDO')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.After Paid')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.After Paid Counter')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.After Sale Staff')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.After Swap')}}</th>
    </tr>
    <tr>

        <th colspan="5"  class="text-center">{{trans($transfile.'.Saler')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Deployer')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Saler')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Deployer')}}</th>
        <th colspan="5" class="text-center">NV bảo trì TIN-PNC</th>
        <th colspan="5" class="text-center">NV bảo trì INDO</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance Staff Hi FPT TIN-PNC')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Maintenance Staff Hi FPT INDO')}}</th>
        <th colspan="5" class="text-center">NV thu cước</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Transaction Staff Counter')}}</th>
        <th colspan="5"  class="text-center">{{trans($transfile.'.Saler')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.Deployer')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.SSW')}}</th>

    </tr>
    <tr>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $NVKD_IBB_TQ_CSAT1 = $NVKD_IBB_TQ_CSAT2 = $NVKD_IBB_TQ_CSAT12 = $NVKD_IBB_TQ_CUS_CSAT = $NVKD_IBB_TQ_CSAT = $NVTK_IBB_TQ_CSAT1 = $NVTK_IBB_TQ_CSAT2 = $NVTK_IBB_TQ_CSAT12 = $NVTK_IBB_TQ_CUS_CSAT = $NVTK_IBB_TQ_CSAT = $NVKD_TS_TQ_CSAT1 = $NVKD_TS_TQ_CSAT2 = $NVKD_TS_TQ_CSAT12 = $NVKD_TS_TQ_CUS_CSAT = $NVKD_TS_TQ_CSAT = $NVTK_TS_TQ_CSAT1 = $NVTK_TS_TQ_CSAT2 = $NVTK_TS_TQ_CSAT12 = $NVTK_TS_TQ_CUS_CSAT = $NVTK_TS_TQ_CSAT = $NVBT_TIN_TQ_CSAT1 = $NVBT_TIN_TQ_CSAT2 = $NVBT_TIN_TQ_CSAT12 = $NVBT_TIN_TQ_CUS_CSAT = $NVBT_TIN_TQ_CSAT = $NVBT_INDO_TQ_CSAT1 = $NVBT_INDO_TQ_CSAT2 = $NVBT_INDO_TQ_CSAT12 = $NVBT_INDO_TQ_CUS_CSAT = $NVBT_INDO_TQ_CSAT
        = $NVBT_HIFPT_TIN_TQ_CSAT1 = $NVBT_HIFPT_TIN_TQ_CSAT2 = $NVBT_HIFPT_TIN_TQ_CSAT12 = $NVBT_HIFPT_TIN_TQ_CUS_CSAT = $NVBT_HIFPT_TIN_TQ_CSAT = $NVBT_HIFPT_INDO_TQ_CSAT1 = $NVBT_HIFPT_INDO_TQ_CSAT2 = $NVBT_HIFPT_INDO_TQ_CSAT12 = $NVBT_HIFPT_INDO_TQ_CUS_CSAT = $NVBT_HIFPT_INDO_TQ_CSAT
        = $NVThuCuoc_TQ_CSAT1 = $NVThuCuoc_TQ_CSAT2 = $NVThuCuoc_TQ_CSAT12 = $NVThuCuoc_TQ_CUS_CSAT = $NVThuCuoc_TQ_CSAT = $NVGDTQ_TQ_CSAT1 = $NVGDTQ_TQ_CSAT2 = $NVGDTQ_TQ_CSAT12 = $NVGDTQ_TQ_CUS_CSAT = $NVGDTQ_TQ_CSAT = $NVKD_SS_TQ_CSAT1 = $NVKD_SS_TQ_CSAT2 = $NVKD_SS_TQ_CSAT12 = $NVKD_SS_TQ_CUS_CSAT = $NVKD_SS_TQ_CSAT = $NVTK_SS_TQ_CSAT1 = $NVTK_SS_TQ_CSAT2 = $NVTK_SS_TQ_CSAT12 = $NVTK_SS_TQ_CUS_CSAT = $NVTK_SS_TQ_CSAT = $NVBT_SSW_TQ_CSAT1 = $NVBT_SSW_TQ_CSAT2 = $NVBT_SSW_TQ_CSAT12 = $NVBT_SSW_TQ_CUS_CSAT = $NVBT_SSW_TQ_CSAT = 0;
    foreach ($surveyCSAT12['resultStaffCsat12Branch'] as $key => $value) {
    $NVKD_IBB_TQ_CSAT1 += $value->NVKD_IBB_CSAT_1;
    $NVKD_IBB_TQ_CSAT2 += $value->NVKD_IBB_CSAT_2;
    $NVKD_IBB_TQ_CSAT12 += $value->NVKD_IBB_CSAT_12;
    $NVKD_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_NVKD_CUS_CSAT;
    $NVKD_IBB_TQ_CSAT += $value->TOTAL_IBB_NVKD_CSAT;

    $NVTK_IBB_TQ_CSAT1 += $value->NVTK_IBB_CSAT_1;
    $NVTK_IBB_TQ_CSAT2 += $value->NVTK_IBB_CSAT_2;
    $NVTK_IBB_TQ_CSAT12 += $value->NVTK_IBB_CSAT_12;
    $NVTK_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_NVTK_CUS_CSAT;
    $NVTK_IBB_TQ_CSAT += $value->TOTAL_IBB_NVTK_CSAT;

    $NVKD_TS_TQ_CSAT1 += $value->NVKD_TS_CSAT_1;
    $NVKD_TS_TQ_CSAT2 += $value->NVKD_TS_CSAT_2;
    $NVKD_TS_TQ_CSAT12 += $value->NVKD_TS_CSAT_12;
    $NVKD_TS_TQ_CUS_CSAT += $value->TOTAL_TS_NVKD_CUS_CSAT;
    $NVKD_TS_TQ_CSAT += $value->TOTAL_TS_NVKD_CSAT;

    $NVTK_TS_TQ_CSAT1 += $value->NVTK_TS_CSAT_1;
    $NVTK_TS_TQ_CSAT2 += $value->NVTK_TS_CSAT_2;
    $NVTK_TS_TQ_CSAT12 += $value->NVTK_TS_CSAT_12;
    $NVTK_TS_TQ_CUS_CSAT += $value->TOTAL_TS_NVTK_CUS_CSAT;
    $NVTK_TS_TQ_CSAT += $value->TOTAL_TS_NVTK_CSAT;

    $NVBT_TIN_TQ_CSAT1 += $value->NVBT_TIN_CSAT_1;
    $NVBT_TIN_TQ_CSAT2 += $value->NVBT_TIN_CSAT_2;
    $NVBT_TIN_TQ_CSAT12 += $value->NVBT_TIN_CSAT_12;
    $NVBT_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_NVBT_CUS_CSAT;
    $NVBT_TIN_TQ_CSAT += $value->TOTAL_TIN_NVBT_CSAT;

    $NVBT_INDO_TQ_CSAT1 += $value->NVBT_INDO_CSAT_1;
    $NVBT_INDO_TQ_CSAT2 += $value->NVBT_INDO_CSAT_2;
    $NVBT_INDO_TQ_CSAT12 += $value->NVBT_INDO_CSAT_12;
    $NVBT_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_NVBT_CUS_CSAT;
    $NVBT_INDO_TQ_CSAT += $value->TOTAL_INDO_NVBT_CSAT;

    $NVBT_HIFPT_TIN_TQ_CSAT1 += $value->NVBT_HIFPT_TIN_CSAT_1;
    $NVBT_HIFPT_TIN_TQ_CSAT2 += $value->NVBT_HIFPT_TIN_CSAT_2;
    $NVBT_HIFPT_TIN_TQ_CSAT12 += $value->NVBT_HIFPT_TIN_CSAT_12;
    $NVBT_HIFPT_TIN_TQ_CUS_CSAT += $value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT;
    $NVBT_HIFPT_TIN_TQ_CSAT += $value->TOTAL_HIFPT_TIN_NVBT_CSAT;

    $NVBT_HIFPT_INDO_TQ_CSAT1 += $value->NVBT_HIFPT_INDO_CSAT_1;
    $NVBT_HIFPT_INDO_TQ_CSAT2 += $value->NVBT_HIFPT_INDO_CSAT_2;
    $NVBT_HIFPT_INDO_TQ_CSAT12 += $value->NVBT_HIFPT_INDO_CSAT_12;
    $NVBT_HIFPT_INDO_TQ_CUS_CSAT += $value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT;
    $NVBT_HIFPT_INDO_TQ_CSAT += $value->TOTAL_HIFPT_INDO_NVBT_CSAT;

    $NVThuCuoc_TQ_CSAT1 += $value->NVThuCuoc_CSAT_1;
    $NVThuCuoc_TQ_CSAT2 += $value->NVThuCuoc_CSAT_2;
    $NVThuCuoc_TQ_CSAT12 += $value->NVThuCuoc_CSAT_12;
    $NVThuCuoc_TQ_CUS_CSAT += $value->TOTAL_NVThuCuoc_CUS_CSAT;
    $NVThuCuoc_TQ_CSAT += $value->TOTAL_NVThuCuoc_CSAT;

    $NVGDTQ_TQ_CSAT1 += $value->NVGDTQ_CSAT_1;
    $NVGDTQ_TQ_CSAT2 += $value->NVGDTQ_CSAT_2;
    $NVGDTQ_TQ_CSAT12 += $value->NVGDTQ_CSAT_12;
    $NVGDTQ_TQ_CUS_CSAT += $value->TOTAL_NVGDTQ_CUS_CSAT;
    $NVGDTQ_TQ_CSAT += $value->TOTAL_NVGDTQ_CSAT;

    $NVKD_SS_TQ_CSAT1 += $value->NVKD_SS_CSAT_1;
    $NVKD_SS_TQ_CSAT2 += $value->NVKD_SS_CSAT_2;
    $NVKD_SS_TQ_CSAT12 += $value->NVKD_SS_CSAT_12;
    $NVKD_SS_TQ_CUS_CSAT += $value->TOTAL_SS_NVKD_CUS_CSAT;
    $NVKD_SS_TQ_CSAT += $value->TOTAL_SS_NVKD_CSAT;

    $NVTK_SS_TQ_CSAT1 += $value->NVTK_SS_CSAT_1;
    $NVTK_SS_TQ_CSAT2 += $value->NVTK_SS_CSAT_2;
    $NVTK_SS_TQ_CSAT12 += $value->NVTK_SS_CSAT_12;
    $NVTK_SS_TQ_CUS_CSAT += $value->TOTAL_SS_NVTK_CUS_CSAT;
    $NVTK_SS_TQ_CSAT += $value->TOTAL_SS_NVTK_CSAT;

    $NVBT_SSW_TQ_CSAT1 += $value->NVBT_SSW_CSAT_1;
    $NVBT_SSW_TQ_CSAT2 += $value->NVBT_SSW_CSAT_2;
    $NVBT_SSW_TQ_CSAT12 += $value->NVBT_SSW_CSAT_12;
    $NVBT_SSW_TQ_CUS_CSAT += $value->TOTAL_SSW_NVBT_CUS_CSAT;
    $NVBT_SSW_TQ_CSAT += $value->TOTAL_SSW_NVBT_CSAT;
    ?>
    <tr>
        <td >
            {{$value->section_sub_parent_desc}}
        </td>
        <td>
            {{$value->NVKD_IBB_CSAT_1}}
        </td>
        <td>
            {{$value->NVKD_IBB_CSAT_2}}
        </td>
        <td>
            {{$value->NVKD_IBB_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_IBB_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_IBB_CSAT_12 / $value->TOTAL_IBB_NVKD_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_IBB_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_NVKD_CSAT / $value->TOTAL_IBB_NVKD_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->NVTK_IBB_CSAT_1}}
        </td>
        <td>
            {{$value->NVTK_IBB_CSAT_2}}
        </td>
        <td>
            {{$value->NVTK_IBB_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_IBB_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_IBB_CSAT_12 / $value->TOTAL_IBB_NVTK_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_IBB_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_NVTK_CSAT / $value->TOTAL_IBB_NVTK_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->NVKD_TS_CSAT_1}}
        </td>
        <td>
            {{$value->NVKD_TS_CSAT_2}}
        </td>
        <td>
            {{$value->NVKD_TS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_TS_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_TS_CSAT_12 / $value->TOTAL_TS_NVKD_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_TS_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_NVKD_CSAT / $value->TOTAL_TS_NVKD_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->NVTK_TS_CSAT_1}}
        </td>
        <td>
            {{$value->NVTK_TS_CSAT_2}}
        </td>
        <td>
            {{$value->NVTK_TS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_TS_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_TS_CSAT_12 / $value->TOTAL_TS_NVTK_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_TS_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_NVTK_CSAT / $value->TOTAL_TS_NVTK_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->NVBT_TIN_CSAT_1}}
        </td>
        <td>
            {{$value->NVBT_TIN_CSAT_2}}
        </td>
        <td>
            {{$value->NVBT_TIN_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_TIN_CSAT_12 / $value->TOTAL_TIN_NVBT_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_NVBT_CSAT / $value->TOTAL_TIN_NVBT_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->NVBT_INDO_CSAT_1}}
        </td>
        <td>
            {{$value->NVBT_INDO_CSAT_2}}
        </td>
        <td>
            {{$value->NVBT_INDO_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_INDO_CSAT_12 / $value->TOTAL_INDO_NVBT_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_NVBT_CSAT / $value->TOTAL_INDO_NVBT_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->NVBT_HIFPT_TIN_CSAT_1}}
        </td>
        <td>
            {{$value->NVBT_HIFPT_TIN_CSAT_2}}
        </td>
        <td>
            {{$value->NVBT_HIFPT_TIN_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_HIFPT_TIN_CSAT_12 / $value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_TIN_NVBT_CSAT / $value->TOTAL_HIFPT_TIN_NVBT_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->NVBT_HIFPT_INDO_CSAT_1}}
        </td>
        <td>
            {{$value->NVBT_HIFPT_INDO_CSAT_2}}
        </td>
        <td>
            {{$value->NVBT_HIFPT_INDO_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_HIFPT_INDO_CSAT_12 / $value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_INDO_NVBT_CSAT / $value->TOTAL_HIFPT_INDO_NVBT_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->NVThuCuoc_CSAT_1}}
        </td>
        <td>
            {{$value->NVThuCuoc_CSAT_2}}
        </td>
        <td>
            {{$value->NVThuCuoc_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_NVThuCuoc_CUS_CSAT) != 0) ? round(($value->NVThuCuoc_CSAT_12 / $value->TOTAL_NVThuCuoc_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_NVThuCuoc_CUS_CSAT) != 0) ? round(($value->TOTAL_NVThuCuoc_CSAT / $value->TOTAL_NVThuCuoc_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->NVGDTQ_CSAT_1}}
        </td>
        <td>
            {{$value->NVGDTQ_CSAT_2}}
        </td>
        <td>
            {{$value->NVGDTQ_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_NVGDTQ_CUS_CSAT) != 0) ? round(($value->NVGDTQ_CSAT_12 / $value->TOTAL_NVGDTQ_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_NVGDTQ_CUS_CSAT) != 0) ? round(($value->TOTAL_NVGDTQ_CSAT / $value->TOTAL_NVGDTQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->NVKD_SS_CSAT_1}}
        </td>
        <td>
            {{$value->NVKD_SS_CSAT_2}}
        </td>
        <td>
            {{$value->NVKD_SS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_SS_NVKD_CUS_CSAT) != 0) ? round(($value->NVKD_SS_CSAT_12 / $value->TOTAL_SS_NVKD_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_SS_NVKD_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_NVKD_CSAT / $value->TOTAL_SS_NVKD_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->NVTK_SS_CSAT_1}}
        </td>
        <td>
            {{$value->NVTK_SS_CSAT_2}}
        </td>
        <td>
            {{$value->NVTK_SS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_SS_NVTK_CUS_CSAT) != 0) ? round(($value->NVTK_SS_CSAT_12 / $value->TOTAL_SS_NVTK_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_SS_NVTK_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_NVTK_CSAT / $value->TOTAL_SS_NVTK_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->NVBT_SSW_CSAT_1}}
        </td>
        <td>
            {{$value->NVBT_SSW_CSAT_2}}
        </td>
        <td>
            {{$value->NVBT_SSW_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_SSW_NVBT_CUS_CSAT) != 0) ? round(($value->NVBT_SSW_CSAT_12 / $value->TOTAL_SSW_NVBT_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_SSW_NVBT_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_NVBT_CSAT / $value->TOTAL_SSW_NVBT_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

    </tr>
    <?php
    }
    ?>
    <tr>
        <td class="foot_average">
            Tổng cộng
        </td>
        <td class="foot_average">
            {{$NVKD_IBB_TQ_CSAT1}}
        </td>
        <td class="foot_average">
            {{$NVKD_IBB_TQ_CSAT2}}
        </td>
        <td class="foot_average">
            {{$NVKD_IBB_TQ_CSAT12}}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVKD_IBB_TQ_CUS_CSAT) != 0) ? round(($NVKD_IBB_TQ_CSAT12 / $NVKD_IBB_TQ_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVKD_IBB_TQ_CUS_CSAT) != 0) ? round(($NVKD_IBB_TQ_CSAT / $NVKD_IBB_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVTK_IBB_TQ_CSAT1}}
        </td>
        <td class="foot_average">
            {{$NVTK_IBB_TQ_CSAT2}}
        </td>
        <td class="foot_average">
            {{ $NVTK_IBB_TQ_CSAT12}}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVTK_IBB_TQ_CUS_CSAT) != 0) ? round(($NVTK_IBB_TQ_CSAT12 / $NVTK_IBB_TQ_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVTK_IBB_TQ_CUS_CSAT) != 0) ? round(($NVTK_IBB_TQ_CSAT / $NVTK_IBB_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVKD_TS_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$NVKD_TS_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$NVKD_TS_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVKD_TS_TQ_CUS_CSAT ) != 0) ? round(($NVKD_TS_TQ_CSAT12 / $NVKD_TS_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVKD_TS_TQ_CUS_CSAT ) != 0) ? round(($NVKD_TS_TQ_CSAT / $NVKD_TS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVTK_TS_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$NVTK_TS_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$NVTK_TS_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVTK_TS_TQ_CUS_CSAT ) != 0) ? round(($NVTK_TS_TQ_CSAT12 / $NVTK_TS_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVTK_TS_TQ_CUS_CSAT ) != 0) ? round(($NVTK_TS_TQ_CSAT / $NVTK_TS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVBT_TIN_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$NVBT_TIN_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$NVBT_TIN_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVBT_TIN_TQ_CUS_CSAT ) != 0) ? round(($NVBT_TIN_TQ_CSAT12 / $NVBT_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVBT_TIN_TQ_CUS_CSAT ) != 0) ? round(($NVBT_TIN_TQ_CSAT / $NVBT_TIN_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVBT_INDO_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$NVBT_INDO_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$NVBT_INDO_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVBT_INDO_TQ_CUS_CSAT ) != 0) ? round(($NVBT_INDO_TQ_CSAT12 / $NVBT_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVBT_INDO_TQ_CUS_CSAT ) != 0) ? round(($NVBT_INDO_TQ_CSAT / $NVBT_INDO_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$NVBT_HIFPT_TIN_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$NVBT_HIFPT_TIN_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$NVBT_HIFPT_TIN_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVBT_HIFPT_TIN_TQ_CUS_CSAT ) != 0) ? round(($NVBT_HIFPT_TIN_TQ_CSAT12 / $NVBT_HIFPT_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVBT_HIFPT_TIN_TQ_CUS_CSAT ) != 0) ? round(($NVBT_HIFPT_TIN_TQ_CSAT / $NVBT_HIFPT_TIN_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVBT_HIFPT_INDO_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$NVBT_HIFPT_INDO_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$NVBT_HIFPT_INDO_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVBT_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($NVBT_HIFPT_INDO_TQ_CSAT12 / $NVBT_HIFPT_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVBT_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($NVBT_HIFPT_INDO_TQ_CSAT / $NVBT_HIFPT_INDO_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVThuCuoc_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$NVThuCuoc_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$NVThuCuoc_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVThuCuoc_TQ_CUS_CSAT ) != 0) ? round(($NVThuCuoc_TQ_CSAT12 / $NVThuCuoc_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVThuCuoc_TQ_CUS_CSAT ) != 0) ? round(($NVThuCuoc_TQ_CSAT / $NVThuCuoc_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVGDTQ_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$NVGDTQ_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$NVGDTQ_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVGDTQ_TQ_CUS_CSAT ) != 0) ? round(($NVGDTQ_TQ_CSAT12 / $NVGDTQ_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVGDTQ_TQ_CUS_CSAT ) != 0) ? round(($NVGDTQ_TQ_CSAT / $NVGDTQ_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVKD_SS_TQ_CSAT1}}
        </td>
        <td class="foot_average">
            {{$NVKD_SS_TQ_CSAT2}}
        </td>
        <td class="foot_average">
            {{$NVKD_SS_TQ_CSAT12}}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVKD_SS_TQ_CUS_CSAT) != 0) ? round(($NVKD_SS_TQ_CSAT12 / $NVKD_SS_TQ_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVKD_SS_TQ_CUS_CSAT) != 0) ? round(($NVKD_SS_TQ_CSAT / $NVKD_SS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$NVTK_SS_TQ_CSAT1}}
        </td>
        <td class="foot_average">
            {{$NVTK_SS_TQ_CSAT2}}
        </td>
        <td class="foot_average">
            {{ $NVTK_SS_TQ_CSAT12}}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVTK_SS_TQ_CUS_CSAT) != 0) ? round(($NVTK_SS_TQ_CSAT12 / $NVTK_SS_TQ_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVTK_SS_TQ_CUS_CSAT) != 0) ? round(($NVTK_SS_TQ_CSAT / $NVTK_SS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$NVBT_SSW_TQ_CSAT1}}
        </td>
        <td class="foot_average">
            {{$NVBT_SSW_TQ_CSAT2}}
        </td>
        <td class="foot_average">
            {{ $NVBT_SSW_TQ_CSAT12}}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($NVBT_SSW_TQ_CUS_CSAT) != 0) ? round(($NVBT_SSW_TQ_CSAT12 / $NVBT_SSW_TQ_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($NVBT_SSW_TQ_CUS_CSAT) != 0) ? round(($NVBT_SSW_TQ_CSAT / $NVBT_SSW_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
    </tr>
    </tbody>

</table>
<div class="row">
    <h3 class="header smaller lighter red btn-group">
        <i class="icon-table"></i>
        Khách hàng không hài lòng với CLDV - CSAT 1,2 CLDV
    </h3>
<div class="btn-group">
    <input name="viewStatus" id="viewStatusHidden" type="hidden" value=""/>
    <button id="viewLocationServiceButton" type="button" class="btn btn-success" style="height: 42px;margin-right: 3px;">Thống kê theo vùng</button>
    <button id="viewBranchServiceButton" type="button" class="btn btn-success" style="height: 42px;margin-right: 3px">Thống kê theo chi nhánh</button>
</div>
</div>
    {{--Thống kê theo vùng--}}
    <table id="CSAT12ServiceReportRegion" class="table table-striped table-bordered table-hover table-CSAT12ServiceReport "  cellspacing="0" width= "100%" style="max-width: 100%;">
        <thead>
            <tr>
                <th rowspan="3" colspan="1" class="text-center evaluate-cell">Vùng</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.Telesale Deployment')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.Maintenance TIN-PNC')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.Maintenance INDO')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.SBTHITIN')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.SBTHIINDO')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.CUS')}}</th>
                <th colspan="5" class="text-center">{{trans($transfile.'.After Paid Counter')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.After Sale Staff')}}</th>
                <th colspan="10" class="text-center">{{trans($transfile.'.After Swap')}}</th>
                <th colspan="10" class="text-center">Tổng cộng các trường hơp khách hàng không hài lòng</th>
            </tr>
            <tr>
                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>

                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>

                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>

                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>

                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>

                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>

                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>

                <th colspan="5" class="text-center">Chất lượng DV</th>

                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>
                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>
                <th colspan="5"  class="text-center">CLDV Internet</th>
                <th colspan="5" class="text-center">CLDV Truyền hình</th>



            </tr>
            <tr>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>

                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
                <th colspan="1" class="text-center">CSAT 1</th>
                <th colspan="1" class="text-center">CSAT 2</th>
                <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
                <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
                <th colspan="1" class="text-center">CSAT Trung bình</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $Internet_IBB_TQ_CSAT1 = $Internet_IBB_TQ_CSAT2 = $Internet_IBB_TQ_CSAT12 = $Internet_IBB_TQ_CUS_CSAT = $Internet_IBB_TQ_CSAT = $TV_IBB_TQ_CSAT1 = $TV_IBB_TQ_CSAT2 = $TV_IBB_TQ_CSAT12 = $TV_IBB_TQ_CUS_CSAT = $TV_IBB_TQ_CSAT = $Internet_TS_TQ_CSAT1 = $Internet_TS_TQ_CSAT2 = $Internet_TS_TQ_CSAT12 = $Internet_TS_TQ_CUS_CSAT = $Internet_TS_TQ_CSAT = $TV_TS_TQ_CSAT1 = $TV_TS_TQ_CSAT2 = $TV_TS_TQ_CSAT12 = $TV_TS_TQ_CUS_CSAT = $TV_TS_TQ_CSAT 
                    = $Internet_TIN_TQ_CSAT1 = $Internet_TIN_TQ_CSAT2 = $Internet_TIN_TQ_CSAT12 = $Internet_TIN_TQ_CUS_CSAT = $Internet_TIN_TQ_CSAT = $TV_TIN_TQ_CSAT1 = $TV_TIN_TQ_CSAT2 = $TV_TIN_TQ_CSAT12 = $TV_TIN_TQ_CUS_CSAT = $TV_TIN_TQ_CSAT = $Internet_INDO_TQ_CSAT1 = $Internet_INDO_TQ_CSAT2 = $Internet_INDO_TQ_CSAT12 = $Internet_INDO_TQ_CUS_CSAT = $Internet_INDO_TQ_CSAT = $TV_INDO_TQ_CSAT1 = $TV_INDO_TQ_CSAT2 = $TV_INDO_TQ_CSAT12 = $TV_INDO_TQ_CUS_CSAT = $TV_INDO_TQ_CSAT 
                    = $Internet_HIFPT_TIN_TQ_CSAT1 = $Internet_HIFPT_TIN_TQ_CSAT2 = $Internet_HIFPT_TIN_TQ_CSAT12 = $Internet_HIFPT_TIN_TQ_CUS_CSAT = $Internet_HIFPT_TIN_TQ_CSAT = $TV_HIFPT_TIN_TQ_CSAT1 = $TV_HIFPT_TIN_TQ_CSAT2 = $TV_HIFPT_TIN_TQ_CSAT12 = $TV_HIFPT_TIN_TQ_CUS_CSAT = $TV_HIFPT_TIN_TQ_CSAT = $Internet_HIFPT_INDO_TQ_CSAT1 = $Internet_HIFPT_INDO_TQ_CSAT2 = $Internet_HIFPT_INDO_TQ_CSAT12 = $Internet_HIFPT_INDO_TQ_CUS_CSAT = $Internet_HIFPT_INDO_TQ_CSAT = $TV_HIFPT_INDO_TQ_CSAT1 = $TV_HIFPT_INDO_TQ_CSAT2 = $TV_HIFPT_INDO_TQ_CSAT12 = $TV_HIFPT_INDO_TQ_CUS_CSAT = $TV_HIFPT_INDO_TQ_CSAT
                    = $Internet_CUS_TQ_CSAT1 = $Internet_CUS_TQ_CSAT2 = $Internet_CUS_TQ_CSAT12 = $Internet_CUS_TQ_CUS_CSAT = $Internet_CUS_TQ_CSAT = $TV_CUS_TQ_CSAT1 = $TV_CUS_TQ_CSAT2 = $TV_CUS_TQ_CSAT12 = $TV_CUS_TQ_CUS_CSAT = $TV_CUS_TQ_CSAT = $DGDichVu_Counter_TQ_CSAT1 = $DGDichVu_Counter_TQ_CSAT2 = $DGDichVu_Counter_TQ_CSAT12 = $DGDichVu_Counter_TQ_CUS_CSAT = $DGDichVu_Counter_TQ_CSAT = $Internet_KHL_TQ_CSAT1 = $Internet_KHL_TQ_CSAT2 = $Internet_KHL_TQ_CSAT12 = $Internet_KHL_TQ_CUS_CSAT = $Internet_KHL_TQ_CSAT = $TV_KHL_TQ_CSAT1 = $TV_KHL_TQ_CSAT2 = $TV_KHL_TQ_CSAT12 = $TV_KHL_TQ_CUS_CSAT = $TV_KHL_TQ_CSAT = $Internet_SS_TQ_CSAT1 = $Internet_SS_TQ_CSAT2 = $Internet_SS_TQ_CSAT12 = $Internet_SS_TQ_CUS_CSAT = $Internet_SS_TQ_CSAT = $TV_SS_TQ_CSAT1 = $TV_SS_TQ_CSAT2 = $TV_SS_TQ_CSAT12 = $TV_SS_TQ_CUS_CSAT = $TV_SS_TQ_CSAT = $Internet_SSW_TQ_CSAT1 = $Internet_SSW_TQ_CSAT2 = $Internet_SSW_TQ_CSAT12 = $Internet_SSW_TQ_CUS_CSAT = $Internet_SSW_TQ_CSAT = $TV_SSW_TQ_CSAT1 = $TV_SSW_TQ_CSAT2 = $TV_SSW_TQ_CSAT12 = $TV_SSW_TQ_CUS_CSAT = $TV_SSW_TQ_CSAT = 0;

            foreach ($surveyCSATService12['resultServiceCsat12Region'] as $key => $value) {
                $Internet_IBB_TQ_CSAT1 += $value->INTERNET_IBB_CSAT_1;
                $Internet_IBB_TQ_CSAT2 += $value->INTERNET_IBB_CSAT_2;
                $Internet_IBB_TQ_CSAT12 += $value->INTERNET_IBB_CSAT_12;
                $Internet_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_INTERNET_CUS_CSAT;
                $Internet_IBB_TQ_CSAT += $value->TOTAL_IBB_INTERNET_CSAT;

                $TV_IBB_TQ_CSAT1 += $value->TV_IBB_CSAT_1;
                $TV_IBB_TQ_CSAT2 += $value->TV_IBB_CSAT_2;
                $TV_IBB_TQ_CSAT12 += $value->TV_IBB_CSAT_12;
                $TV_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_TV_CUS_CSAT;
                $TV_IBB_TQ_CSAT += $value->TOTAL_IBB_TV_CSAT;

                $Internet_TS_TQ_CSAT1 += $value->INTERNET_TS_CSAT_1;
                $Internet_TS_TQ_CSAT2 += $value->INTERNET_TS_CSAT_2;
                $Internet_TS_TQ_CSAT12 += $value->INTERNET_TS_CSAT_12;
                $Internet_TS_TQ_CUS_CSAT += $value->TOTAL_TS_INTERNET_CUS_CSAT;
                $Internet_TS_TQ_CSAT += $value->TOTAL_TS_INTERNET_CSAT;

                $TV_TS_TQ_CSAT1 += $value->TV_TS_CSAT_1;
                $TV_TS_TQ_CSAT2 += $value->TV_TS_CSAT_2;
                $TV_TS_TQ_CSAT12 += $value->TV_TS_CSAT_12;
                $TV_TS_TQ_CUS_CSAT += $value->TOTAL_TS_TV_CUS_CSAT;
                $TV_TS_TQ_CSAT += $value->TOTAL_TS_TV_CSAT;

                $Internet_TIN_TQ_CSAT1 += $value->INTERNET_TIN_CSAT_1;
                $Internet_TIN_TQ_CSAT2 += $value->INTERNET_TIN_CSAT_2;
                $Internet_TIN_TQ_CSAT12 += $value->INTERNET_TIN_CSAT_12;
                $Internet_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_INTERNET_CUS_CSAT;
                $Internet_TIN_TQ_CSAT += $value->TOTAL_TIN_INTERNET_CSAT;

                $TV_TIN_TQ_CSAT1 += $value->TV_TIN_CSAT_1;
                $TV_TIN_TQ_CSAT2 += $value->TV_TIN_CSAT_2;
                $TV_TIN_TQ_CSAT12 += $value->TV_TIN_CSAT_12;
                $TV_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_TV_CUS_CSAT;
                $TV_TIN_TQ_CSAT += $value->TOTAL_TIN_TV_CSAT;

                $Internet_INDO_TQ_CSAT1 += $value->INTERNET_INDO_CSAT_1;
                $Internet_INDO_TQ_CSAT2 += $value->INTERNET_INDO_CSAT_2;
                $Internet_INDO_TQ_CSAT12 += $value->INTERNET_INDO_CSAT_12;
                $Internet_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_INTERNET_CUS_CSAT;
                $Internet_INDO_TQ_CSAT += $value->TOTAL_INDO_INTERNET_CSAT;

                $TV_INDO_TQ_CSAT1 += $value->TV_INDO_CSAT_1;
                $TV_INDO_TQ_CSAT2 += $value->TV_INDO_CSAT_2;
                $TV_INDO_TQ_CSAT12 += $value->TV_INDO_CSAT_12;
                $TV_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_TV_CUS_CSAT;
                $TV_INDO_TQ_CSAT += $value->TOTAL_INDO_TV_CSAT;
                
                  $Internet_HIFPT_TIN_TQ_CSAT1 += $value->INTERNET_HIFPT_TIN_CSAT_1;
                $Internet_HIFPT_TIN_TQ_CSAT2 += $value->INTERNET_HIFPT_TIN_CSAT_2;
                $Internet_HIFPT_TIN_TQ_CSAT12 += $value->INTERNET_HIFPT_TIN_CSAT_12;
                $Internet_HIFPT_TIN_TQ_CUS_CSAT += $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT;
                $Internet_HIFPT_TIN_TQ_CSAT += $value->TOTAL_HIFPT_TIN_INTERNET_CSAT;

                $TV_HIFPT_TIN_TQ_CSAT1 += $value->TV_HIFPT_TIN_CSAT_1;
                $TV_HIFPT_TIN_TQ_CSAT2 += $value->TV_HIFPT_TIN_CSAT_2;
                $TV_HIFPT_TIN_TQ_CSAT12 += $value->TV_HIFPT_TIN_CSAT_12;
                $TV_HIFPT_TIN_TQ_CUS_CSAT += $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT;
                $TV_HIFPT_TIN_TQ_CSAT += $value->TOTAL_HIFPT_TIN_TV_CSAT;

                $Internet_HIFPT_INDO_TQ_CSAT1 += $value->INTERNET_HIFPT_INDO_CSAT_1;
                $Internet_HIFPT_INDO_TQ_CSAT2 += $value->INTERNET_HIFPT_INDO_CSAT_2;
                $Internet_HIFPT_INDO_TQ_CSAT12 += $value->INTERNET_HIFPT_INDO_CSAT_12;
                $Internet_HIFPT_INDO_TQ_CUS_CSAT += $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT;
                $Internet_HIFPT_INDO_TQ_CSAT += $value->TOTAL_HIFPT_INDO_INTERNET_CSAT;

                $TV_HIFPT_INDO_TQ_CSAT1 += $value->TV_HIFPT_INDO_CSAT_1;
                $TV_HIFPT_INDO_TQ_CSAT2 += $value->TV_HIFPT_INDO_CSAT_2;
                $TV_HIFPT_INDO_TQ_CSAT12 += $value->TV_HIFPT_INDO_CSAT_12;
                $TV_HIFPT_INDO_TQ_CUS_CSAT += $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT;
                $TV_HIFPT_INDO_TQ_CSAT += $value->TOTAL_HIFPT_INDO_TV_CSAT;

                $Internet_CUS_TQ_CSAT1 += $value->INTERNET_CUS_CSAT_1;
                $Internet_CUS_TQ_CSAT2 += $value->INTERNET_CUS_CSAT_2;
                $Internet_CUS_TQ_CSAT12 += $value->INTERNET_CUS_CSAT_12;
                $Internet_CUS_TQ_CUS_CSAT += $value->TOTAL_CUS_INTERNET_CUS_CSAT;
                $Internet_CUS_TQ_CSAT += $value->TOTAL_CUS_INTERNET_CSAT;

                $TV_CUS_TQ_CSAT1 += $value->TV_CUS_CSAT_1;
                $TV_CUS_TQ_CSAT2 += $value->TV_CUS_CSAT_2;
                $TV_CUS_TQ_CSAT12 += $value->TV_CUS_CSAT_12;
                $TV_CUS_TQ_CUS_CSAT += $value->TOTAL_CUS_TV_CUS_CSAT;
                $TV_CUS_TQ_CSAT += $value->TOTAL_CUS_TV_CSAT;

                $DGDichVu_Counter_TQ_CSAT1 += $value->DGDichVu_Counter_CSAT_1;
                $DGDichVu_Counter_TQ_CSAT2 += $value->DGDichVu_Counter_CSAT_2;
                $DGDichVu_Counter_TQ_CSAT12 += $value->DGDichVu_Counter_CSAT_12;
                $DGDichVu_Counter_TQ_CUS_CSAT += $value->TOTAL_DGDichVu_Counter_CUS_CSAT;
                $DGDichVu_Counter_TQ_CSAT += $value->TOTAL_DGDichVu_Counter_CSAT;

                $Internet_SS_TQ_CSAT1 += $value->INTERNET_SS_CSAT_1;
                $Internet_SS_TQ_CSAT2 += $value->INTERNET_SS_CSAT_2;
                $Internet_SS_TQ_CSAT12 += $value->INTERNET_SS_CSAT_12;
                $Internet_SS_TQ_CUS_CSAT += $value->TOTAL_SS_INTERNET_CUS_CSAT;
                $Internet_SS_TQ_CSAT += $value->TOTAL_SS_INTERNET_CSAT;

                $TV_SS_TQ_CSAT1 += $value->TV_SS_CSAT_1;
                $TV_SS_TQ_CSAT2 += $value->TV_SS_CSAT_2;
                $TV_SS_TQ_CSAT12 += $value->TV_SS_CSAT_12;
                $TV_SS_TQ_CUS_CSAT += $value->TOTAL_SS_TV_CUS_CSAT;
                $TV_SS_TQ_CSAT += $value->TOTAL_SS_TV_CSAT;

                $Internet_SSW_TQ_CSAT1 += $value->INTERNET_SSW_CSAT_1;
                $Internet_SSW_TQ_CSAT2 += $value->INTERNET_SSW_CSAT_2;
                $Internet_SSW_TQ_CSAT12 += $value->INTERNET_SSW_CSAT_12;
                $Internet_SSW_TQ_CUS_CSAT += $value->TOTAL_SSW_INTERNET_CUS_CSAT;
                $Internet_SSW_TQ_CSAT += $value->TOTAL_SSW_INTERNET_CSAT;

                $TV_SSW_TQ_CSAT1 += $value->TV_SSW_CSAT_1;
                $TV_SSW_TQ_CSAT2 += $value->TV_SSW_CSAT_2;
                $TV_SSW_TQ_CSAT12 += $value->TV_SSW_CSAT_12;
                $TV_SSW_TQ_CUS_CSAT += $value->TOTAL_SSW_TV_CUS_CSAT;
                $TV_SSW_TQ_CSAT += $value->TOTAL_SSW_TV_CSAT;


                $Internet_KHL_TQ_CSAT1 += $value->INTERNET_IBB_CSAT_1 + $value->INTERNET_TS_CSAT_1 + $value->INTERNET_TIN_CSAT_1 + $value->INTERNET_INDO_CSAT_1 + $value->INTERNET_HIFPT_TIN_CSAT_1 + $value->INTERNET_HIFPT_INDO_CSAT_1 + $value->INTERNET_CUS_CSAT_1 + $value->INTERNET_SS_CSAT_1 + $value->INTERNET_SSW_CSAT_1;
                $Internet_KHL_TQ_CSAT2 += $value->INTERNET_IBB_CSAT_2 + $value->INTERNET_TS_CSAT_2 + $value->INTERNET_TIN_CSAT_2 + $value->INTERNET_INDO_CSAT_2 + $value->INTERNET_HIFPT_TIN_CSAT_2 + $value->INTERNET_HIFPT_INDO_CSAT_2 + $value->INTERNET_CUS_CSAT_2 + $value->INTERNET_SS_CSAT_2 + $value->INTERNET_SSW_CSAT_2;
                $Internet_KHL_TQ_CSAT12 += $value->INTERNET_IBB_CSAT_12 + $value->INTERNET_TS_CSAT_12 + $value->INTERNET_TIN_CSAT_12 + $value->INTERNET_INDO_CSAT_12 + $value->INTERNET_HIFPT_TIN_CSAT_12 + $value->INTERNET_HIFPT_INDO_CSAT_12 + $value->INTERNET_CUS_CSAT_12 + $value->INTERNET_SS_CSAT_12 + $value->INTERNET_SSW_CSAT_12;
                $Internet_KHL_TQ_CUS_CSAT += $value->TOTAL_IBB_INTERNET_CUS_CSAT + $value->TOTAL_TS_INTERNET_CUS_CSAT + $value->TOTAL_TIN_INTERNET_CUS_CSAT + $value->TOTAL_INDO_INTERNET_CUS_CSAT + $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT + $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT + $value->TOTAL_CUS_INTERNET_CUS_CSAT + $value->TOTAL_SS_INTERNET_CUS_CSAT + $value->TOTAL_SSW_INTERNET_CUS_CSAT;
                $Internet_KHL_TQ_CSAT += $value->TOTAL_IBB_INTERNET_CSAT + $value->TOTAL_TS_INTERNET_CSAT + $value->TOTAL_TIN_INTERNET_CSAT + $value->TOTAL_INDO_INTERNET_CSAT + $value->TOTAL_HIFPT_TIN_INTERNET_CSAT + $value->TOTAL_HIFPT_INDO_INTERNET_CSAT + $value->TOTAL_CUS_INTERNET_CSAT + $value->TOTAL_SS_INTERNET_CSAT + $value->TOTAL_SSW_INTERNET_CSAT;

                $TV_KHL_TQ_CSAT1 += $value->TV_IBB_CSAT_1 + $value->TV_TS_CSAT_1 + $value->TV_TIN_CSAT_1 + $value->TV_INDO_CSAT_1 + $value->TV_HIFPT_TIN_CSAT_1 + $value->TV_HIFPT_INDO_CSAT_1 + $value->TV_CUS_CSAT_1 + $value->TV_SS_CSAT_1 + $value->TV_SSW_CSAT_1;
                $TV_KHL_TQ_CSAT2 += $value->TV_IBB_CSAT_2 + $value->TV_TS_CSAT_2 + $value->TV_TIN_CSAT_2 + $value->TV_INDO_CSAT_2 + $value->TV_HIFPT_TIN_CSAT_2 + $value->TV_HIFPT_INDO_CSAT_2 + $value->TV_CUS_CSAT_2 + $value->TV_SS_CSAT_2 + $value->TV_SSW_CSAT_2;
                $TV_KHL_TQ_CSAT12 += $value->TV_IBB_CSAT_12 + $value->TV_TS_CSAT_12 + $value->TV_TIN_CSAT_12 + $value->TV_INDO_CSAT_12 + $value->TV_HIFPT_TIN_CSAT_12 + $value->TV_HIFPT_INDO_CSAT_12 + $value->TV_CUS_CSAT_12 + $value->TV_SS_CSAT_12 + $value->TV_SSW_CSAT_12;
                $TV_KHL_TQ_CUS_CSAT += $value->TOTAL_IBB_TV_CUS_CSAT + $value->TOTAL_TS_TV_CUS_CSAT + $value->TOTAL_TIN_TV_CUS_CSAT + $value->TOTAL_INDO_TV_CUS_CSAT + $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT + $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT + $value->TOTAL_CUS_TV_CUS_CSAT + $value->TOTAL_SS_TV_CUS_CSAT + $value->TOTAL_SSW_TV_CUS_CSAT;
                $TV_KHL_TQ_CSAT += $value->TOTAL_IBB_TV_CSAT + $value->TOTAL_TS_TV_CSAT + $value->TOTAL_TIN_TV_CSAT + $value->TOTAL_INDO_TV_CSAT + $value->TOTAL_HIFPT_TIN_TV_CSAT + $value->TOTAL_HIFPT_INDO_TV_CSAT + $value->TOTAL_CUS_TV_CSAT + $value->TOTAL_SS_TV_CSAT + $value->TOTAL_SSW_TV_CSAT;
                ?>
                <tr>
                    <td >
                        {{$value->section_sub_parent_desc}}
                    </td>
                    <td>
                        {{$value->INTERNET_IBB_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_IBB_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_IBB_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_IBB_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_IBB_CSAT_12 / $value->TOTAL_IBB_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_IBB_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_INTERNET_CSAT / $value->TOTAL_IBB_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->TV_IBB_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_IBB_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_IBB_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_IBB_TV_CUS_CSAT) != 0) ? round(($value->TV_IBB_CSAT_12 / $value->TOTAL_IBB_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_IBB_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_TV_CSAT / $value->TOTAL_IBB_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->INTERNET_TS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_TS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_TS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_TS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_TS_CSAT_12 / $value->TOTAL_TS_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_TS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_INTERNET_CSAT / $value->TOTAL_TS_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->TV_TS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_TS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_TS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_TS_TV_CUS_CSAT) != 0) ? round(($value->TV_TS_CSAT_12 / $value->TOTAL_TS_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_TS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_TV_CSAT / $value->TOTAL_TS_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->INTERNET_TIN_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_TIN_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_TIN_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_TIN_CSAT_12 / $value->TOTAL_TIN_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_INTERNET_CSAT / $value->TOTAL_TIN_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->TV_TIN_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_TIN_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_TIN_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_TIN_TV_CUS_CSAT) != 0) ? round(($value->TV_TIN_CSAT_12 / $value->TOTAL_TIN_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_TIN_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_TV_CSAT / $value->TOTAL_TIN_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->INTERNET_INDO_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_INDO_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_INDO_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_INDO_CSAT_12 / $value->TOTAL_INDO_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_INTERNET_CSAT / $value->TOTAL_INDO_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->TV_INDO_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_INDO_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_INDO_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_INDO_TV_CUS_CSAT) != 0) ? round(($value->TV_INDO_CSAT_12 / $value->TOTAL_INDO_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_INDO_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_TV_CSAT / $value->TOTAL_INDO_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    
                     <td>
                        {{$value->INTERNET_HIFPT_TIN_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_HIFPT_TIN_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_HIFPT_TIN_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_HIFPT_TIN_CSAT_12 / $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_TIN_INTERNET_CSAT / $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->TV_HIFPT_TIN_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_HIFPT_TIN_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_HIFPT_TIN_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_HIFPT_TIN_TV_CUS_CSAT) != 0) ? round(($value->TV_HIFPT_TIN_CSAT_12 / $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_HIFPT_TIN_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_TIN_TV_CSAT / $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->INTERNET_HIFPT_INDO_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_HIFPT_INDO_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_HIFPT_INDO_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_HIFPT_INDO_CSAT_12 / $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_INDO_INTERNET_CSAT / $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->TV_HIFPT_INDO_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_HIFPT_INDO_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_HIFPT_INDO_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_HIFPT_INDO_TV_CUS_CSAT) != 0) ? round(($value->TV_HIFPT_INDO_CSAT_12 / $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_HIFPT_INDO_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_INDO_TV_CSAT / $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->INTERNET_CUS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_CUS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_CUS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_CUS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_CUS_CSAT_12 / $value->TOTAL_CUS_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_CUS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_CUS_INTERNET_CSAT / $value->TOTAL_CUS_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->TV_CUS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_CUS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_CUS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_CUS_TV_CUS_CSAT) != 0) ? round(($value->TV_CUS_CSAT_12 / $value->TOTAL_CUS_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_CUS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_CUS_TV_CSAT / $value->TOTAL_CUS_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->DGDichVu_Counter_CSAT_1}}
                    </td>
                    <td>
                        {{$value->DGDichVu_Counter_CSAT_2}}
                    </td>
                    <td>
                        {{$value->DGDichVu_Counter_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_DGDichVu_Counter_CUS_CSAT) != 0) ? round(($value->DGDichVu_Counter_CSAT_12 / $value->TOTAL_DGDichVu_Counter_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_DGDichVu_Counter_CUS_CSAT) != 0) ? round(($value->TOTAL_DGDichVu_Counter_CSAT / $value->TOTAL_DGDichVu_Counter_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                    <td>
                        {{$value->INTERNET_SS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_SS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_SS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_SS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_SS_CSAT_12 / $value->TOTAL_SS_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_SS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_INTERNET_CSAT / $value->TOTAL_SS_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->TV_SS_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_SS_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_SS_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_SS_TV_CUS_CSAT) != 0) ? round(($value->TV_SS_CSAT_12 / $value->TOTAL_SS_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_SS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_TV_CSAT / $value->TOTAL_SS_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->INTERNET_SSW_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_SSW_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_SSW_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_SSW_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_SSW_CSAT_12 / $value->TOTAL_SSW_INTERNET_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_SSW_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_INTERNET_CSAT / $value->TOTAL_SSW_INTERNET_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->TV_SSW_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_SSW_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_SSW_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $rateNotSastisfied = (($value->TOTAL_SSW_TV_CUS_CSAT) != 0) ? round(($value->TV_SSW_CSAT_12 / $value->TOTAL_SSW_TV_CUS_CSAT) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($value->TOTAL_SSW_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_TV_CSAT / $value->TOTAL_SSW_TV_CUS_CSAT), 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->INTERNET_IBB_CSAT_1+$value->INTERNET_TS_CSAT_1+$value->INTERNET_TIN_CSAT_1+$value->INTERNET_INDO_CSAT_1+$value->INTERNET_HIFPT_TIN_CSAT_1+$value->INTERNET_HIFPT_INDO_CSAT_1+$value->INTERNET_CUS_CSAT_1+$value->INTERNET_SS_CSAT_1 + +$value->INTERNET_SSW_CSAT_1}}
                    </td>
                    <td>
                        {{$value->INTERNET_IBB_CSAT_2+$value->INTERNET_TS_CSAT_2+$value->INTERNET_TIN_CSAT_2+$value->INTERNET_INDO_CSAT_2+$value->INTERNET_HIFPT_TIN_CSAT_2+$value->INTERNET_HIFPT_INDO_CSAT_2+$value->INTERNET_CUS_CSAT_2+$value->INTERNET_SS_CSAT_2 + +$value->INTERNET_SSW_CSAT_2}}
                    </td>
                    <td>
                        {{$value->INTERNET_IBB_CSAT_12+$value->INTERNET_TS_CSAT_12+$value->INTERNET_TIN_CSAT_12+$value->INTERNET_INDO_CSAT_12+$value->INTERNET_HIFPT_TIN_CSAT_12+$value->INTERNET_HIFPT_INDO_CSAT_12+$value->INTERNET_CUS_CSAT_12+$value->INTERNET_SS_CSAT_12 +$value->INTERNET_SSW_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $sumTotal = $value->TOTAL_IBB_INTERNET_CUS_CSAT + $value->TOTAL_TS_INTERNET_CUS_CSAT + $value->TOTAL_TIN_INTERNET_CUS_CSAT + $value->TOTAL_INDO_INTERNET_CUS_CSAT + $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT + $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT + $value->TOTAL_CUS_INTERNET_CUS_CSAT + $value->TOTAL_SS_INTERNET_CUS_CSAT + $value->TOTAL_SSW_INTERNET_CUS_CSAT;
                        $rateNotSastisfied = (($sumTotal) != 0) ? round((($value->INTERNET_IBB_CSAT_12 + $value->INTERNET_TS_CSAT_12 + $value->INTERNET_TIN_CSAT_12 + $value->INTERNET_INDO_CSAT_12 + $value->INTERNET_HIFPT_TIN_CSAT_12 + $value->INTERNET_HIFPT_INDO_CSAT_12 + $value->INTERNET_CUS_CSAT_12 + $value->INTERNET_SS_CSAT_12 + $value->INTERNET_SSW_CSAT_12) / $sumTotal) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($sumTotal) != 0) ? round(($value->TOTAL_IBB_INTERNET_CSAT + $value->TOTAL_TS_INTERNET_CSAT + $value->TOTAL_TIN_INTERNET_CSAT + $value->TOTAL_INDO_INTERNET_CSAT + $value->TOTAL_HIFPT_TIN_INTERNET_CSAT + $value->TOTAL_HIFPT_INDO_INTERNET_CSAT + $value->TOTAL_CUS_INTERNET_CSAT + $value->TOTAL_SS_INTERNET_CSAT + $value->TOTAL_SSW_INTERNET_CSAT) / $sumTotal, 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>
                    <td>
                        {{$value->TV_IBB_CSAT_1+$value->TV_TS_CSAT_1+$value->TV_TIN_CSAT_1+$value->TV_INDO_CSAT_1+$value->TV_HIFPT_TIN_CSAT_1+$value->TV_HIFPT_INDO_CSAT_1+$value->TV_CUS_CSAT_1+$value->TV_SS_CSAT_1 +$value->TV_SSW_CSAT_1}}
                    </td>
                    <td>
                        {{$value->TV_IBB_CSAT_2+$value->TV_TS_CSAT_2+$value->TV_TIN_CSAT_2+$value->TV_INDO_CSAT_2+$value->TV_HIFPT_TIN_CSAT_2+$value->TV_HIFPT_INDO_CSAT_2+$value->TV_CUS_CSAT_2+$value->TV_SS_CSAT_2+$value->TV_SSW_CSAT_2}}
                    </td>
                    <td>
                        {{$value->TV_IBB_CSAT_12+$value->TV_TS_CSAT_12+$value->TV_TIN_CSAT_12+$value->TV_INDO_CSAT_12+$value->TV_HIFPT_TIN_CSAT_12+$value->TV_HIFPT_INDO_CSAT_12+$value->TV_CUS_CSAT_12+$value->TV_SS_CSAT_12+$value->TV_SSW_CSAT_12}}
                    </td>
                    <td>
                        <?php
                        $sumTotal = $value->TOTAL_IBB_TV_CUS_CSAT + $value->TOTAL_TS_TV_CUS_CSAT + $value->TOTAL_TIN_TV_CUS_CSAT + $value->TOTAL_INDO_TV_CUS_CSAT + $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT + $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT + $value->TOTAL_CUS_TV_CUS_CSAT + $value->TOTAL_SS_TV_CUS_CSAT + $value->TOTAL_SSW_TV_CUS_CSAT;
                        $rateNotSastisfied = (($sumTotal) != 0) ? round((($value->TV_IBB_CSAT_12 + $value->TV_TS_CSAT_12 + $value->TV_TIN_CSAT_12 + $value->TV_INDO_CSAT_12 + $value->TV_HIFPT_TIN_CSAT_12 + $value->TV_HIFPT_INDO_CSAT_12 + $value->TV_CUS_CSAT_12 + $value->TV_SS_CSAT_12 + $value->TV_SSW_CSAT_12) / $sumTotal) * 100, 2) : 0;
                        ?>
                        {{$rateNotSastisfied.'%'}}
                    </td>
                    <td>
                        <?php
                        $csatAverage = (($sumTotal) != 0) ? round(($value->TOTAL_IBB_TV_CSAT + $value->TOTAL_TS_TV_CSAT + $value->TOTAL_TIN_TV_CSAT + $value->TOTAL_INDO_TV_CSAT  + $value->TOTAL_HIFPT_TIN_TV_CSAT + $value->TOTAL_HIFPT_INDO_TV_CSAT + $value->TOTAL_CUS_TV_CSAT + $value->TOTAL_SS_TV_CSAT + $value->TOTAL_SSW_TV_CSAT) / $sumTotal, 2) : 0;
                        ?>
                        {{$csatAverage}}
                    </td>

                </tr>
                <?php
            }
            ?>
            <tr>
                <td class="foot_average">
                    Tổng cộng
                </td>
                <td class="foot_average">
                    {{$Internet_IBB_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$Internet_IBB_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$Internet_IBB_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_IBB_TQ_CUS_CSAT ) != 0) ? round(($Internet_IBB_TQ_CSAT12 / $Internet_IBB_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_IBB_TQ_CUS_CSAT ) != 0) ? round(($Internet_IBB_TQ_CSAT / $Internet_IBB_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$TV_IBB_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$TV_IBB_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{ $TV_IBB_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_IBB_TQ_CUS_CSAT ) != 0) ? round(($TV_IBB_TQ_CSAT12 / $TV_IBB_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_IBB_TQ_CUS_CSAT ) != 0) ? round(($TV_IBB_TQ_CSAT / $TV_IBB_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$Internet_TS_TQ_CSAT1  }}
                </td>
                <td class="foot_average">
                    {{$Internet_TS_TQ_CSAT2  }}
                </td>
                <td class="foot_average">
                    {{$Internet_TS_TQ_CSAT12  }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_TS_TQ_CUS_CSAT ) != 0) ? round(($Internet_TS_TQ_CSAT12 / $Internet_TS_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_TS_TQ_CUS_CSAT ) != 0) ? round(($Internet_TS_TQ_CSAT / $Internet_TS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$TV_TS_TQ_CSAT1  }}
                </td>
                <td class="foot_average">
                    {{$TV_TS_TQ_CSAT2  }}
                </td>
                <td class="foot_average">
                    {{$TV_TS_TQ_CSAT12  }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_TS_TQ_CUS_CSAT ) != 0) ? round(($TV_TS_TQ_CSAT12 / $TV_TS_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_TS_TQ_CUS_CSAT ) != 0) ? round(($TV_TS_TQ_CSAT / $TV_TS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$Internet_TIN_TQ_CSAT1  }}
                </td>
                <td class="foot_average">
                    {{$Internet_TIN_TQ_CSAT2  }}
                </td>
                <td class="foot_average">
                    {{$Internet_TIN_TQ_CSAT12  }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_TIN_TQ_CUS_CSAT ) != 0) ? round(($Internet_TIN_TQ_CSAT12 / $Internet_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_TIN_TQ_CUS_CSAT ) != 0) ? round(($Internet_TIN_TQ_CSAT / $Internet_TIN_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$TV_TIN_TQ_CSAT1  }}
                </td>
                <td class="foot_average">
                    {{$TV_TIN_TQ_CSAT2  }}
                </td>
                <td class="foot_average">
                    {{$TV_TIN_TQ_CSAT12  }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_TIN_TQ_CUS_CSAT ) != 0) ? round(($TV_TIN_TQ_CSAT12 / $TV_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_TIN_TQ_CUS_CSAT ) != 0) ? round(($TV_TIN_TQ_CSAT / $TV_TIN_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td class="foot_average">
                    {{$Internet_INDO_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$Internet_INDO_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$Internet_INDO_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_INDO_TQ_CUS_CSAT ) != 0) ? round(($Internet_INDO_TQ_CSAT12 / $Internet_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_INDO_TQ_CUS_CSAT ) != 0) ? round(($Internet_INDO_TQ_CSAT / $Internet_INDO_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td class="foot_average">
                    {{$TV_INDO_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$TV_INDO_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$TV_INDO_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_INDO_TQ_CUS_CSAT ) != 0) ? round(($TV_INDO_TQ_CSAT12 / $TV_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_INDO_TQ_CUS_CSAT ) != 0) ? round(($TV_INDO_TQ_CSAT / $TV_INDO_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                
                  <td class="foot_average">
                    {{$Internet_TIN_TQ_CSAT1  }}
                </td>
                <td class="foot_average">
                    {{$Internet_TIN_TQ_CSAT2  }}
                </td>
                <td class="foot_average">
                    {{$Internet_TIN_TQ_CSAT12  }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_TIN_TQ_CUS_CSAT ) != 0) ? round(($Internet_TIN_TQ_CSAT12 / $Internet_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_TIN_TQ_CUS_CSAT ) != 0) ? round(($Internet_TIN_TQ_CSAT / $Internet_TIN_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$TV_HIFPT_TIN_TQ_CSAT1  }}
                </td>
                <td class="foot_average">
                    {{$TV_HIFPT_TIN_TQ_CSAT2  }}
                </td>
                <td class="foot_average">
                    {{$TV_HIFPT_TIN_TQ_CSAT12  }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_HIFPT_TIN_TQ_CUS_CSAT ) != 0) ? round(($TV_HIFPT_TIN_TQ_CSAT12 / $TV_HIFPT_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_HIFPT_TIN_TQ_CUS_CSAT ) != 0) ? round(($TV_HIFPT_TIN_TQ_CSAT / $TV_HIFPT_TIN_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td class="foot_average">
                    {{$Internet_HIFPT_INDO_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$Internet_HIFPT_INDO_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$Internet_HIFPT_INDO_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($Internet_HIFPT_INDO_TQ_CSAT12 / $Internet_HIFPT_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($Internet_HIFPT_INDO_TQ_CSAT / $Internet_HIFPT_INDO_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td class="foot_average">
                    {{$TV_HIFPT_INDO_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$TV_HIFPT_INDO_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$TV_HIFPT_INDO_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($TV_HIFPT_INDO_TQ_CSAT12 / $TV_HIFPT_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($TV_HIFPT_INDO_TQ_CSAT / $TV_HIFPT_INDO_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                
                <td class="foot_average">
                    {{$Internet_CUS_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$Internet_CUS_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$Internet_CUS_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_CUS_TQ_CUS_CSAT ) != 0) ? round(($Internet_CUS_TQ_CSAT12 / $Internet_CUS_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_CUS_TQ_CUS_CSAT ) != 0) ? round(($Internet_CUS_TQ_CSAT / $Internet_CUS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td class="foot_average">
                    {{$TV_CUS_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$TV_CUS_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$TV_CUS_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_CUS_TQ_CUS_CSAT ) != 0) ? round(($TV_CUS_TQ_CSAT12 / $TV_CUS_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_CUS_TQ_CUS_CSAT ) != 0) ? round(($TV_CUS_TQ_CSAT / $TV_CUS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$DGDichVu_Counter_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$DGDichVu_Counter_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$DGDichVu_Counter_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($DGDichVu_Counter_TQ_CUS_CSAT ) != 0) ? round(($DGDichVu_Counter_TQ_CSAT12 / $DGDichVu_Counter_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($DGDichVu_Counter_TQ_CUS_CSAT ) != 0) ? round(($DGDichVu_Counter_TQ_CSAT / $DGDichVu_Counter_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td class="foot_average">
                    {{$Internet_SS_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$Internet_SS_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$Internet_SS_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_SS_TQ_CUS_CSAT ) != 0) ? round(($Internet_SS_TQ_CSAT12 / $Internet_SS_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_SS_TQ_CUS_CSAT ) != 0) ? round(($Internet_SS_TQ_CSAT / $Internet_SS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$TV_SS_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$TV_SS_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{ $TV_SS_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_SS_TQ_CUS_CSAT ) != 0) ? round(($TV_SS_TQ_CSAT12 / $TV_SS_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_SS_TQ_CUS_CSAT ) != 0) ? round(($TV_SS_TQ_CSAT / $TV_SS_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
                <td class="foot_average">
                    {{$Internet_SSW_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$Internet_SSW_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{$Internet_SSW_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($Internet_SSW_TQ_CUS_CSAT ) != 0) ? round(($Internet_SSW_TQ_CSAT12 / $Internet_SSW_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($Internet_SSW_TQ_CUS_CSAT ) != 0) ? round(($Internet_SSW_TQ_CSAT / $Internet_SSW_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>

                <td class="foot_average">
                    {{$TV_SSW_TQ_CSAT1 }}
                </td>
                <td class="foot_average">
                    {{$TV_SSW_TQ_CSAT2 }}
                </td>
                <td class="foot_average">
                    {{ $TV_SSW_TQ_CSAT12 }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_SSW_TQ_CUS_CSAT ) != 0) ? round(($TV_SSW_TQ_CSAT12 / $TV_SSW_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_SSW_TQ_CUS_CSAT ) != 0) ? round(($TV_SSW_TQ_CSAT / $TV_SSW_TQ_CUS_CSAT), 2) : 0;
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
                <td class="foot_average">
                    {{$TV_KHL_TQ_CSAT1   }}
                </td>
                <td class="foot_average">
                    {{$TV_KHL_TQ_CSAT2   }}
                </td>
                <td class="foot_average">
                    {{$TV_KHL_TQ_CSAT12   }}
                </td>
                <td class="foot_average">
                    <?php
                    $rateNotSastisfied = (($TV_KHL_TQ_CUS_CSAT ) != 0) ? round(($TV_KHL_TQ_CSAT12 / $TV_KHL_TQ_CUS_CSAT ) * 100, 2) : 0;
                    ?>
                    {{$rateNotSastisfied.'%'}}
                </td>
                <td class="foot_average">
                    <?php
                    $csatAverage = (($TV_KHL_TQ_CUS_CSAT ) != 0) ? round(($TV_KHL_TQ_CSAT / $TV_KHL_TQ_CUS_CSAT), 2) : 0;
                    ?>
                    {{$csatAverage}}
                </td>
            </tr>
        </tbody>

    </table>

    {{--Thống kê theo chi nhánh--}}
    <table id="CSAT12ServiceReportBranch" class="table table-striped table-bordered table-hover table-CSAT12ServiceReport "  cellspacing="0" width= "100%" style="max-width: 100%;display: none">
    <thead>
    <tr>
        <th rowspan="3" colspan="1" class="text-center evaluate-cell">Chi nhánh</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.Deployment')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.Telesale Deployment')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.Maintenance TIN-PNC')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.Maintenance INDO')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.SBTHITIN')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.SBTHIINDO')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.CUS')}}</th>
        <th colspan="5" class="text-center">{{trans($transfile.'.After Paid Counter')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.After Sale Staff')}}</th>
        <th colspan="10" class="text-center">{{trans($transfile.'.After Swap')}}</th>
        <th colspan="10" class="text-center">Tổng cộng các trường hơp khách hàng không hài lòng</th>
    </tr>
    <tr>
        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>

        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>

        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>

        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>

        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>

        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>

        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>

        <th colspan="5" class="text-center">Chất lượng DV</th>

        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>
        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>
        <th colspan="5"  class="text-center">CLDV Internet</th>
        <th colspan="5" class="text-center">CLDV Truyền hình</th>


    </tr>
    <tr>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>

        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
        <th colspan="1" class="text-center">CSAT 1</th>
        <th colspan="1" class="text-center">CSAT 2</th>
        <th colspan="1" class="text-center">Tổng CSAT 1,2</th>
        <th colspan="1" class="text-center">Tỉ lệ không hài lòng(%)</th>
        <th colspan="1" class="text-center">CSAT Trung bình</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $Internet_IBB_TQ_CSAT1 = $Internet_IBB_TQ_CSAT2 = $Internet_IBB_TQ_CSAT12 = $Internet_IBB_TQ_CUS_CSAT = $Internet_IBB_TQ_CSAT = $TV_IBB_TQ_CSAT1 = $TV_IBB_TQ_CSAT2 = $TV_IBB_TQ_CSAT12 = $TV_IBB_TQ_CUS_CSAT = $TV_IBB_TQ_CSAT = $Internet_TS_TQ_CSAT1 = $Internet_TS_TQ_CSAT2 = $Internet_TS_TQ_CSAT12 = $Internet_TS_TQ_CUS_CSAT = $Internet_TS_TQ_CSAT = $TV_TS_TQ_CSAT1 = $TV_TS_TQ_CSAT2 = $TV_TS_TQ_CSAT12 = $TV_TS_TQ_CUS_CSAT = $TV_TS_TQ_CSAT
        = $Internet_TIN_TQ_CSAT1 = $Internet_TIN_TQ_CSAT2 = $Internet_TIN_TQ_CSAT12 = $Internet_TIN_TQ_CUS_CSAT = $Internet_TIN_TQ_CSAT = $TV_TIN_TQ_CSAT1 = $TV_TIN_TQ_CSAT2 = $TV_TIN_TQ_CSAT12 = $TV_TIN_TQ_CUS_CSAT = $TV_TIN_TQ_CSAT = $Internet_INDO_TQ_CSAT1 = $Internet_INDO_TQ_CSAT2 = $Internet_INDO_TQ_CSAT12 = $Internet_INDO_TQ_CUS_CSAT = $Internet_INDO_TQ_CSAT = $TV_INDO_TQ_CSAT1 = $TV_INDO_TQ_CSAT2 = $TV_INDO_TQ_CSAT12 = $TV_INDO_TQ_CUS_CSAT = $TV_INDO_TQ_CSAT
        = $Internet_HIFPT_TIN_TQ_CSAT1 = $Internet_HIFPT_TIN_TQ_CSAT2 = $Internet_HIFPT_TIN_TQ_CSAT12 = $Internet_HIFPT_TIN_TQ_CUS_CSAT = $Internet_HIFPT_TIN_TQ_CSAT = $TV_HIFPT_TIN_TQ_CSAT1 = $TV_HIFPT_TIN_TQ_CSAT2 = $TV_HIFPT_TIN_TQ_CSAT12 = $TV_HIFPT_TIN_TQ_CUS_CSAT = $TV_HIFPT_TIN_TQ_CSAT = $Internet_HIFPT_INDO_TQ_CSAT1 = $Internet_HIFPT_INDO_TQ_CSAT2 = $Internet_HIFPT_INDO_TQ_CSAT12 = $Internet_HIFPT_INDO_TQ_CUS_CSAT = $Internet_HIFPT_INDO_TQ_CSAT = $TV_HIFPT_INDO_TQ_CSAT1 = $TV_HIFPT_INDO_TQ_CSAT2 = $TV_HIFPT_INDO_TQ_CSAT12 = $TV_HIFPT_INDO_TQ_CUS_CSAT = $TV_HIFPT_INDO_TQ_CSAT
        = $Internet_CUS_TQ_CSAT1 = $Internet_CUS_TQ_CSAT2 = $Internet_CUS_TQ_CSAT12 = $Internet_CUS_TQ_CUS_CSAT = $Internet_CUS_TQ_CSAT = $TV_CUS_TQ_CSAT1 = $TV_CUS_TQ_CSAT2 = $TV_CUS_TQ_CSAT12 = $TV_CUS_TQ_CUS_CSAT = $TV_CUS_TQ_CSAT = $DGDichVu_Counter_TQ_CSAT1 = $DGDichVu_Counter_TQ_CSAT2 = $DGDichVu_Counter_TQ_CSAT12 = $DGDichVu_Counter_TQ_CUS_CSAT = $DGDichVu_Counter_TQ_CSAT = $Internet_KHL_TQ_CSAT1 = $Internet_KHL_TQ_CSAT2 = $Internet_KHL_TQ_CSAT12 = $Internet_KHL_TQ_CUS_CSAT = $Internet_KHL_TQ_CSAT = $TV_KHL_TQ_CSAT1 = $TV_KHL_TQ_CSAT2 = $TV_KHL_TQ_CSAT12 = $TV_KHL_TQ_CUS_CSAT = $TV_KHL_TQ_CSAT = $Internet_SS_TQ_CSAT1 = $Internet_SS_TQ_CSAT2 = $Internet_SS_TQ_CSAT12 = $Internet_SS_TQ_CUS_CSAT = $Internet_SS_TQ_CSAT = $TV_SS_TQ_CSAT1 = $TV_SS_TQ_CSAT2 = $TV_SS_TQ_CSAT12 = $TV_SS_TQ_CUS_CSAT = $TV_SS_TQ_CSAT = $Internet_SSW_TQ_CSAT1 = $Internet_SSW_TQ_CSAT2 = $Internet_SSW_TQ_CSAT12 = $Internet_SSW_TQ_CUS_CSAT = $Internet_SSW_TQ_CSAT = $TV_SSW_TQ_CSAT1 = $TV_SSW_TQ_CSAT2 = $TV_SSW_TQ_CSAT12 = $TV_SSW_TQ_CUS_CSAT = $TV_SSW_TQ_CSAT = 0;

    foreach ($surveyCSATService12['resultServiceCsat12Branch'] as $key => $value) {
    $Internet_IBB_TQ_CSAT1 += $value->INTERNET_IBB_CSAT_1;
    $Internet_IBB_TQ_CSAT2 += $value->INTERNET_IBB_CSAT_2;
    $Internet_IBB_TQ_CSAT12 += $value->INTERNET_IBB_CSAT_12;
    $Internet_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_INTERNET_CUS_CSAT;
    $Internet_IBB_TQ_CSAT += $value->TOTAL_IBB_INTERNET_CSAT;

    $TV_IBB_TQ_CSAT1 += $value->TV_IBB_CSAT_1;
    $TV_IBB_TQ_CSAT2 += $value->TV_IBB_CSAT_2;
    $TV_IBB_TQ_CSAT12 += $value->TV_IBB_CSAT_12;
    $TV_IBB_TQ_CUS_CSAT += $value->TOTAL_IBB_TV_CUS_CSAT;
    $TV_IBB_TQ_CSAT += $value->TOTAL_IBB_TV_CSAT;

    $Internet_TS_TQ_CSAT1 += $value->INTERNET_TS_CSAT_1;
    $Internet_TS_TQ_CSAT2 += $value->INTERNET_TS_CSAT_2;
    $Internet_TS_TQ_CSAT12 += $value->INTERNET_TS_CSAT_12;
    $Internet_TS_TQ_CUS_CSAT += $value->TOTAL_TS_INTERNET_CUS_CSAT;
    $Internet_TS_TQ_CSAT += $value->TOTAL_TS_INTERNET_CSAT;

    $TV_TS_TQ_CSAT1 += $value->TV_TS_CSAT_1;
    $TV_TS_TQ_CSAT2 += $value->TV_TS_CSAT_2;
    $TV_TS_TQ_CSAT12 += $value->TV_TS_CSAT_12;
    $TV_TS_TQ_CUS_CSAT += $value->TOTAL_TS_TV_CUS_CSAT;
    $TV_TS_TQ_CSAT += $value->TOTAL_TS_TV_CSAT;

    $Internet_TIN_TQ_CSAT1 += $value->INTERNET_TIN_CSAT_1;
    $Internet_TIN_TQ_CSAT2 += $value->INTERNET_TIN_CSAT_2;
    $Internet_TIN_TQ_CSAT12 += $value->INTERNET_TIN_CSAT_12;
    $Internet_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_INTERNET_CUS_CSAT;
    $Internet_TIN_TQ_CSAT += $value->TOTAL_TIN_INTERNET_CSAT;

    $TV_TIN_TQ_CSAT1 += $value->TV_TIN_CSAT_1;
    $TV_TIN_TQ_CSAT2 += $value->TV_TIN_CSAT_2;
    $TV_TIN_TQ_CSAT12 += $value->TV_TIN_CSAT_12;
    $TV_TIN_TQ_CUS_CSAT += $value->TOTAL_TIN_TV_CUS_CSAT;
    $TV_TIN_TQ_CSAT += $value->TOTAL_TIN_TV_CSAT;

    $Internet_INDO_TQ_CSAT1 += $value->INTERNET_INDO_CSAT_1;
    $Internet_INDO_TQ_CSAT2 += $value->INTERNET_INDO_CSAT_2;
    $Internet_INDO_TQ_CSAT12 += $value->INTERNET_INDO_CSAT_12;
    $Internet_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_INTERNET_CUS_CSAT;
    $Internet_INDO_TQ_CSAT += $value->TOTAL_INDO_INTERNET_CSAT;

    $TV_INDO_TQ_CSAT1 += $value->TV_INDO_CSAT_1;
    $TV_INDO_TQ_CSAT2 += $value->TV_INDO_CSAT_2;
    $TV_INDO_TQ_CSAT12 += $value->TV_INDO_CSAT_12;
    $TV_INDO_TQ_CUS_CSAT += $value->TOTAL_INDO_TV_CUS_CSAT;
    $TV_INDO_TQ_CSAT += $value->TOTAL_INDO_TV_CSAT;

    $Internet_HIFPT_TIN_TQ_CSAT1 += $value->INTERNET_HIFPT_TIN_CSAT_1;
    $Internet_HIFPT_TIN_TQ_CSAT2 += $value->INTERNET_HIFPT_TIN_CSAT_2;
    $Internet_HIFPT_TIN_TQ_CSAT12 += $value->INTERNET_HIFPT_TIN_CSAT_12;
    $Internet_HIFPT_TIN_TQ_CUS_CSAT += $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT;
    $Internet_HIFPT_TIN_TQ_CSAT += $value->TOTAL_HIFPT_TIN_INTERNET_CSAT;

    $TV_HIFPT_TIN_TQ_CSAT1 += $value->TV_HIFPT_TIN_CSAT_1;
    $TV_HIFPT_TIN_TQ_CSAT2 += $value->TV_HIFPT_TIN_CSAT_2;
    $TV_HIFPT_TIN_TQ_CSAT12 += $value->TV_HIFPT_TIN_CSAT_12;
    $TV_HIFPT_TIN_TQ_CUS_CSAT += $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT;
    $TV_HIFPT_TIN_TQ_CSAT += $value->TOTAL_HIFPT_TIN_TV_CSAT;

    $Internet_HIFPT_INDO_TQ_CSAT1 += $value->INTERNET_HIFPT_INDO_CSAT_1;
    $Internet_HIFPT_INDO_TQ_CSAT2 += $value->INTERNET_HIFPT_INDO_CSAT_2;
    $Internet_HIFPT_INDO_TQ_CSAT12 += $value->INTERNET_HIFPT_INDO_CSAT_12;
    $Internet_HIFPT_INDO_TQ_CUS_CSAT += $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT;
    $Internet_HIFPT_INDO_TQ_CSAT += $value->TOTAL_HIFPT_INDO_INTERNET_CSAT;

    $TV_HIFPT_INDO_TQ_CSAT1 += $value->TV_HIFPT_INDO_CSAT_1;
    $TV_HIFPT_INDO_TQ_CSAT2 += $value->TV_HIFPT_INDO_CSAT_2;
    $TV_HIFPT_INDO_TQ_CSAT12 += $value->TV_HIFPT_INDO_CSAT_12;
    $TV_HIFPT_INDO_TQ_CUS_CSAT += $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT;
    $TV_HIFPT_INDO_TQ_CSAT += $value->TOTAL_HIFPT_INDO_TV_CSAT;

    $Internet_CUS_TQ_CSAT1 += $value->INTERNET_CUS_CSAT_1;
    $Internet_CUS_TQ_CSAT2 += $value->INTERNET_CUS_CSAT_2;
    $Internet_CUS_TQ_CSAT12 += $value->INTERNET_CUS_CSAT_12;
    $Internet_CUS_TQ_CUS_CSAT += $value->TOTAL_CUS_INTERNET_CUS_CSAT;
    $Internet_CUS_TQ_CSAT += $value->TOTAL_CUS_INTERNET_CSAT;

    $TV_CUS_TQ_CSAT1 += $value->TV_CUS_CSAT_1;
    $TV_CUS_TQ_CSAT2 += $value->TV_CUS_CSAT_2;
    $TV_CUS_TQ_CSAT12 += $value->TV_CUS_CSAT_12;
    $TV_CUS_TQ_CUS_CSAT += $value->TOTAL_CUS_TV_CUS_CSAT;
    $TV_CUS_TQ_CSAT += $value->TOTAL_CUS_TV_CSAT;

    $DGDichVu_Counter_TQ_CSAT1 += $value->DGDichVu_Counter_CSAT_1;
    $DGDichVu_Counter_TQ_CSAT2 += $value->DGDichVu_Counter_CSAT_2;
    $DGDichVu_Counter_TQ_CSAT12 += $value->DGDichVu_Counter_CSAT_12;
    $DGDichVu_Counter_TQ_CUS_CSAT += $value->TOTAL_DGDichVu_Counter_CUS_CSAT;
    $DGDichVu_Counter_TQ_CSAT += $value->TOTAL_DGDichVu_Counter_CSAT;

    $Internet_SS_TQ_CSAT1 += $value->INTERNET_SS_CSAT_1;
    $Internet_SS_TQ_CSAT2 += $value->INTERNET_SS_CSAT_2;
    $Internet_SS_TQ_CSAT12 += $value->INTERNET_SS_CSAT_12;
    $Internet_SS_TQ_CUS_CSAT += $value->TOTAL_SS_INTERNET_CUS_CSAT;
    $Internet_SS_TQ_CSAT += $value->TOTAL_SS_INTERNET_CSAT;

    $TV_SS_TQ_CSAT1 += $value->TV_SS_CSAT_1;
    $TV_SS_TQ_CSAT2 += $value->TV_SS_CSAT_2;
    $TV_SS_TQ_CSAT12 += $value->TV_SS_CSAT_12;
    $TV_SS_TQ_CUS_CSAT += $value->TOTAL_SS_TV_CUS_CSAT;
    $TV_SS_TQ_CSAT += $value->TOTAL_SS_TV_CSAT;

    $Internet_SSW_TQ_CSAT1 += $value->INTERNET_SSW_CSAT_1;
    $Internet_SSW_TQ_CSAT2 += $value->INTERNET_SSW_CSAT_2;
    $Internet_SSW_TQ_CSAT12 += $value->INTERNET_SSW_CSAT_12;
    $Internet_SSW_TQ_CUS_CSAT += $value->TOTAL_SSW_INTERNET_CUS_CSAT;
    $Internet_SSW_TQ_CSAT += $value->TOTAL_SSW_INTERNET_CSAT;

    $TV_SSW_TQ_CSAT1 += $value->TV_SSW_CSAT_1;
    $TV_SSW_TQ_CSAT2 += $value->TV_SSW_CSAT_2;
    $TV_SSW_TQ_CSAT12 += $value->TV_SSW_CSAT_12;
    $TV_SSW_TQ_CUS_CSAT += $value->TOTAL_SSW_TV_CUS_CSAT;
    $TV_SSW_TQ_CSAT += $value->TOTAL_SSW_TV_CSAT;


    $Internet_KHL_TQ_CSAT1 += $value->INTERNET_IBB_CSAT_1 + $value->INTERNET_TS_CSAT_1 + $value->INTERNET_TIN_CSAT_1 + $value->INTERNET_INDO_CSAT_1 + $value->INTERNET_HIFPT_TIN_CSAT_1 + $value->INTERNET_HIFPT_INDO_CSAT_1 + $value->INTERNET_CUS_CSAT_1 + $value->INTERNET_SS_CSAT_1 + $value->INTERNET_SSW_CSAT_1;
    $Internet_KHL_TQ_CSAT2 += $value->INTERNET_IBB_CSAT_2 + $value->INTERNET_TS_CSAT_2 + $value->INTERNET_TIN_CSAT_2 + $value->INTERNET_INDO_CSAT_2 + $value->INTERNET_HIFPT_TIN_CSAT_2 + $value->INTERNET_HIFPT_INDO_CSAT_2 + $value->INTERNET_CUS_CSAT_2 + $value->INTERNET_SS_CSAT_2 + $value->INTERNET_SSW_CSAT_2;
    $Internet_KHL_TQ_CSAT12 += $value->INTERNET_IBB_CSAT_12 + $value->INTERNET_TS_CSAT_12 + $value->INTERNET_TIN_CSAT_12 + $value->INTERNET_INDO_CSAT_12 + $value->INTERNET_HIFPT_TIN_CSAT_12 + $value->INTERNET_HIFPT_INDO_CSAT_12 + $value->INTERNET_CUS_CSAT_12 + $value->INTERNET_SS_CSAT_12 + $value->INTERNET_SSW_CSAT_12;
    $Internet_KHL_TQ_CUS_CSAT += $value->TOTAL_IBB_INTERNET_CUS_CSAT + $value->TOTAL_TS_INTERNET_CUS_CSAT + $value->TOTAL_TIN_INTERNET_CUS_CSAT + $value->TOTAL_INDO_INTERNET_CUS_CSAT + $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT + $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT + $value->TOTAL_CUS_INTERNET_CUS_CSAT + $value->TOTAL_SS_INTERNET_CUS_CSAT + $value->TOTAL_SSW_INTERNET_CUS_CSAT;
    $Internet_KHL_TQ_CSAT += $value->TOTAL_IBB_INTERNET_CSAT + $value->TOTAL_TS_INTERNET_CSAT + $value->TOTAL_TIN_INTERNET_CSAT + $value->TOTAL_INDO_INTERNET_CSAT + $value->TOTAL_HIFPT_TIN_INTERNET_CSAT + $value->TOTAL_HIFPT_INDO_INTERNET_CSAT + $value->TOTAL_CUS_INTERNET_CSAT + $value->TOTAL_SS_INTERNET_CSAT + $value->TOTAL_SSW_INTERNET_CSAT;

    $TV_KHL_TQ_CSAT1 += $value->TV_IBB_CSAT_1 + $value->TV_TS_CSAT_1 + $value->TV_TIN_CSAT_1 + $value->TV_INDO_CSAT_1 + $value->TV_HIFPT_TIN_CSAT_1 + $value->TV_HIFPT_INDO_CSAT_1 + $value->TV_CUS_CSAT_1 + $value->TV_SS_CSAT_1 + $value->TV_SSW_CSAT_1;
    $TV_KHL_TQ_CSAT2 += $value->TV_IBB_CSAT_2 + $value->TV_TS_CSAT_2 + $value->TV_TIN_CSAT_2 + $value->TV_INDO_CSAT_2 + $value->TV_HIFPT_TIN_CSAT_2 + $value->TV_HIFPT_INDO_CSAT_2 + $value->TV_CUS_CSAT_2 + $value->TV_SS_CSAT_2 + $value->TV_SSW_CSAT_2;
    $TV_KHL_TQ_CSAT12 += $value->TV_IBB_CSAT_12 + $value->TV_TS_CSAT_12 + $value->TV_TIN_CSAT_12 + $value->TV_INDO_CSAT_12 + $value->TV_HIFPT_TIN_CSAT_12 + $value->TV_HIFPT_INDO_CSAT_12 + $value->TV_CUS_CSAT_12 + $value->TV_SS_CSAT_12 + $value->TV_SSW_CSAT_12;
    $TV_KHL_TQ_CUS_CSAT += $value->TOTAL_IBB_TV_CUS_CSAT + $value->TOTAL_TS_TV_CUS_CSAT + $value->TOTAL_TIN_TV_CUS_CSAT + $value->TOTAL_INDO_TV_CUS_CSAT + $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT + $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT + $value->TOTAL_CUS_TV_CUS_CSAT + $value->TOTAL_SS_TV_CUS_CSAT + $value->TOTAL_SSW_TV_CUS_CSAT;
    $TV_KHL_TQ_CSAT += $value->TOTAL_IBB_TV_CSAT + $value->TOTAL_TS_TV_CSAT + $value->TOTAL_TIN_TV_CSAT + $value->TOTAL_INDO_TV_CSAT + $value->TOTAL_HIFPT_TIN_TV_CSAT + $value->TOTAL_HIFPT_INDO_TV_CSAT + $value->TOTAL_CUS_TV_CSAT + $value->TOTAL_SS_TV_CSAT + $value->TOTAL_SSW_TV_CSAT;
    ?>
    <tr>
        <td >
            {{$value->section_sub_parent_desc}}
        </td>
        <td>
            {{$value->INTERNET_IBB_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_IBB_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_IBB_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_IBB_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_IBB_CSAT_12 / $value->TOTAL_IBB_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_IBB_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_INTERNET_CSAT / $value->TOTAL_IBB_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->TV_IBB_CSAT_1}}
        </td>
        <td>
            {{$value->TV_IBB_CSAT_2}}
        </td>
        <td>
            {{$value->TV_IBB_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_IBB_TV_CUS_CSAT) != 0) ? round(($value->TV_IBB_CSAT_12 / $value->TOTAL_IBB_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_IBB_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_IBB_TV_CSAT / $value->TOTAL_IBB_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->INTERNET_TS_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_TS_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_TS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_TS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_TS_CSAT_12 / $value->TOTAL_TS_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_TS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_INTERNET_CSAT / $value->TOTAL_TS_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->TV_TS_CSAT_1}}
        </td>
        <td>
            {{$value->TV_TS_CSAT_2}}
        </td>
        <td>
            {{$value->TV_TS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_TS_TV_CUS_CSAT) != 0) ? round(($value->TV_TS_CSAT_12 / $value->TOTAL_TS_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_TS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_TS_TV_CSAT / $value->TOTAL_TS_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->INTERNET_TIN_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_TIN_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_TIN_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_TIN_CSAT_12 / $value->TOTAL_TIN_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_INTERNET_CSAT / $value->TOTAL_TIN_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->TV_TIN_CSAT_1}}
        </td>
        <td>
            {{$value->TV_TIN_CSAT_2}}
        </td>
        <td>
            {{$value->TV_TIN_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_TIN_TV_CUS_CSAT) != 0) ? round(($value->TV_TIN_CSAT_12 / $value->TOTAL_TIN_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_TIN_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_TIN_TV_CSAT / $value->TOTAL_TIN_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->INTERNET_INDO_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_INDO_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_INDO_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_INDO_CSAT_12 / $value->TOTAL_INDO_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_INTERNET_CSAT / $value->TOTAL_INDO_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->TV_INDO_CSAT_1}}
        </td>
        <td>
            {{$value->TV_INDO_CSAT_2}}
        </td>
        <td>
            {{$value->TV_INDO_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_INDO_TV_CUS_CSAT) != 0) ? round(($value->TV_INDO_CSAT_12 / $value->TOTAL_INDO_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_INDO_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_INDO_TV_CSAT / $value->TOTAL_INDO_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->INTERNET_HIFPT_TIN_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_HIFPT_TIN_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_HIFPT_TIN_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_HIFPT_TIN_CSAT_12 / $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_TIN_INTERNET_CSAT / $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->TV_HIFPT_TIN_CSAT_1}}
        </td>
        <td>
            {{$value->TV_HIFPT_TIN_CSAT_2}}
        </td>
        <td>
            {{$value->TV_HIFPT_TIN_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_HIFPT_TIN_TV_CUS_CSAT) != 0) ? round(($value->TV_HIFPT_TIN_CSAT_12 / $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_HIFPT_TIN_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_TIN_TV_CSAT / $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->INTERNET_HIFPT_INDO_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_HIFPT_INDO_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_HIFPT_INDO_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_HIFPT_INDO_CSAT_12 / $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_INDO_INTERNET_CSAT / $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->TV_HIFPT_INDO_CSAT_1}}
        </td>
        <td>
            {{$value->TV_HIFPT_INDO_CSAT_2}}
        </td>
        <td>
            {{$value->TV_HIFPT_INDO_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_HIFPT_INDO_TV_CUS_CSAT) != 0) ? round(($value->TV_HIFPT_INDO_CSAT_12 / $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_HIFPT_INDO_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_HIFPT_INDO_TV_CSAT / $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->INTERNET_CUS_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_CUS_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_CUS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_CUS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_CUS_CSAT_12 / $value->TOTAL_CUS_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_CUS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_CUS_INTERNET_CSAT / $value->TOTAL_CUS_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->TV_CUS_CSAT_1}}
        </td>
        <td>
            {{$value->TV_CUS_CSAT_2}}
        </td>
        <td>
            {{$value->TV_CUS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_CUS_TV_CUS_CSAT) != 0) ? round(($value->TV_CUS_CSAT_12 / $value->TOTAL_CUS_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_CUS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_CUS_TV_CSAT / $value->TOTAL_CUS_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->DGDichVu_Counter_CSAT_1}}
        </td>
        <td>
            {{$value->DGDichVu_Counter_CSAT_2}}
        </td>
        <td>
            {{$value->DGDichVu_Counter_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_DGDichVu_Counter_CUS_CSAT) != 0) ? round(($value->DGDichVu_Counter_CSAT_12 / $value->TOTAL_DGDichVu_Counter_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_DGDichVu_Counter_CUS_CSAT) != 0) ? round(($value->TOTAL_DGDichVu_Counter_CSAT / $value->TOTAL_DGDichVu_Counter_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td>
            {{$value->INTERNET_SS_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_SS_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_SS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_SS_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_SS_CSAT_12 / $value->TOTAL_SS_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_SS_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_INTERNET_CSAT / $value->TOTAL_SS_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->TV_SS_CSAT_1}}
        </td>
        <td>
            {{$value->TV_SS_CSAT_2}}
        </td>
        <td>
            {{$value->TV_SS_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_SS_TV_CUS_CSAT) != 0) ? round(($value->TV_SS_CSAT_12 / $value->TOTAL_SS_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_SS_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_SS_TV_CSAT / $value->TOTAL_SS_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->INTERNET_SSW_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_SSW_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_SSW_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_SSW_INTERNET_CUS_CSAT) != 0) ? round(($value->INTERNET_SSW_CSAT_12 / $value->TOTAL_SSW_INTERNET_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_SSW_INTERNET_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_INTERNET_CSAT / $value->TOTAL_SSW_INTERNET_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->TV_SSW_CSAT_1}}
        </td>
        <td>
            {{$value->TV_SSW_CSAT_2}}
        </td>
        <td>
            {{$value->TV_SSW_CSAT_12}}
        </td>
        <td>
            <?php
            $rateNotSastisfied = (($value->TOTAL_SSW_TV_CUS_CSAT) != 0) ? round(($value->TV_SSW_CSAT_12 / $value->TOTAL_SSW_TV_CUS_CSAT) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($value->TOTAL_SSW_TV_CUS_CSAT) != 0) ? round(($value->TOTAL_SSW_TV_CSAT / $value->TOTAL_SSW_TV_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->INTERNET_IBB_CSAT_1+$value->INTERNET_TS_CSAT_1+$value->INTERNET_TIN_CSAT_1+$value->INTERNET_INDO_CSAT_1+$value->INTERNET_HIFPT_TIN_CSAT_1+$value->INTERNET_HIFPT_INDO_CSAT_1+$value->INTERNET_CUS_CSAT_1+$value->INTERNET_SS_CSAT_1 + +$value->INTERNET_SSW_CSAT_1}}
        </td>
        <td>
            {{$value->INTERNET_IBB_CSAT_2+$value->INTERNET_TS_CSAT_2+$value->INTERNET_TIN_CSAT_2+$value->INTERNET_INDO_CSAT_2+$value->INTERNET_HIFPT_TIN_CSAT_2+$value->INTERNET_HIFPT_INDO_CSAT_2+$value->INTERNET_CUS_CSAT_2+$value->INTERNET_SS_CSAT_2 + +$value->INTERNET_SSW_CSAT_2}}
        </td>
        <td>
            {{$value->INTERNET_IBB_CSAT_12+$value->INTERNET_TS_CSAT_12+$value->INTERNET_TIN_CSAT_12+$value->INTERNET_INDO_CSAT_12+$value->INTERNET_HIFPT_TIN_CSAT_12+$value->INTERNET_HIFPT_INDO_CSAT_12+$value->INTERNET_CUS_CSAT_12+$value->INTERNET_SS_CSAT_12 +$value->INTERNET_SSW_CSAT_12}}
        </td>
        <td>
            <?php
            $sumTotal = $value->TOTAL_IBB_INTERNET_CUS_CSAT + $value->TOTAL_TS_INTERNET_CUS_CSAT + $value->TOTAL_TIN_INTERNET_CUS_CSAT + $value->TOTAL_INDO_INTERNET_CUS_CSAT + $value->TOTAL_HIFPT_TIN_INTERNET_CUS_CSAT + $value->TOTAL_HIFPT_INDO_INTERNET_CUS_CSAT + $value->TOTAL_CUS_INTERNET_CUS_CSAT + $value->TOTAL_SS_INTERNET_CUS_CSAT + $value->TOTAL_SSW_INTERNET_CUS_CSAT;
            $rateNotSastisfied = (($sumTotal) != 0) ? round((($value->INTERNET_IBB_CSAT_12 + $value->INTERNET_TS_CSAT_12 + $value->INTERNET_TIN_CSAT_12 + $value->INTERNET_INDO_CSAT_12 + $value->INTERNET_HIFPT_TIN_CSAT_12 + $value->INTERNET_HIFPT_INDO_CSAT_12 + $value->INTERNET_CUS_CSAT_12 + $value->INTERNET_SS_CSAT_12 + $value->INTERNET_SSW_CSAT_12) / $sumTotal) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($sumTotal) != 0) ? round(($value->TOTAL_IBB_INTERNET_CSAT + $value->TOTAL_TS_INTERNET_CSAT + $value->TOTAL_TIN_INTERNET_CSAT + $value->TOTAL_INDO_INTERNET_CSAT + $value->TOTAL_HIFPT_TIN_INTERNET_CSAT + $value->TOTAL_HIFPT_INDO_INTERNET_CSAT + $value->TOTAL_CUS_INTERNET_CSAT + $value->TOTAL_SS_INTERNET_CSAT + $value->TOTAL_SSW_INTERNET_CSAT) / $sumTotal, 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td>
            {{$value->TV_IBB_CSAT_1+$value->TV_TS_CSAT_1+$value->TV_TIN_CSAT_1+$value->TV_INDO_CSAT_1+$value->TV_HIFPT_TIN_CSAT_1+$value->TV_HIFPT_INDO_CSAT_1+$value->TV_CUS_CSAT_1+$value->TV_SS_CSAT_1 +$value->TV_SSW_CSAT_1}}
        </td>
        <td>
            {{$value->TV_IBB_CSAT_2+$value->TV_TS_CSAT_2+$value->TV_TIN_CSAT_2+$value->TV_INDO_CSAT_2+$value->TV_HIFPT_TIN_CSAT_2+$value->TV_HIFPT_INDO_CSAT_2+$value->TV_CUS_CSAT_2+$value->TV_SS_CSAT_2+$value->TV_SSW_CSAT_2}}
        </td>
        <td>
            {{$value->TV_IBB_CSAT_12+$value->TV_TS_CSAT_12+$value->TV_TIN_CSAT_12+$value->TV_INDO_CSAT_12+$value->TV_HIFPT_TIN_CSAT_12+$value->TV_HIFPT_INDO_CSAT_12+$value->TV_CUS_CSAT_12+$value->TV_SS_CSAT_12+$value->TV_SSW_CSAT_12}}
        </td>
        <td>
            <?php
            $sumTotal = $value->TOTAL_IBB_TV_CUS_CSAT + $value->TOTAL_TS_TV_CUS_CSAT + $value->TOTAL_TIN_TV_CUS_CSAT + $value->TOTAL_INDO_TV_CUS_CSAT + $value->TOTAL_HIFPT_TIN_TV_CUS_CSAT + $value->TOTAL_HIFPT_INDO_TV_CUS_CSAT + $value->TOTAL_CUS_TV_CUS_CSAT + $value->TOTAL_SS_TV_CUS_CSAT + $value->TOTAL_SSW_TV_CUS_CSAT;
            $rateNotSastisfied = (($sumTotal) != 0) ? round((($value->TV_IBB_CSAT_12 + $value->TV_TS_CSAT_12 + $value->TV_TIN_CSAT_12 + $value->TV_INDO_CSAT_12 + $value->TV_HIFPT_TIN_CSAT_12 + $value->TV_HIFPT_INDO_CSAT_12 + $value->TV_CUS_CSAT_12 + $value->TV_SS_CSAT_12 + $value->TV_SSW_CSAT_12) / $sumTotal) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td>
            <?php
            $csatAverage = (($sumTotal) != 0) ? round(($value->TOTAL_IBB_TV_CSAT + $value->TOTAL_TS_TV_CSAT + $value->TOTAL_TIN_TV_CSAT + $value->TOTAL_INDO_TV_CSAT  + $value->TOTAL_HIFPT_TIN_TV_CSAT + $value->TOTAL_HIFPT_INDO_TV_CSAT + $value->TOTAL_CUS_TV_CSAT + $value->TOTAL_SS_TV_CSAT + $value->TOTAL_SSW_TV_CSAT) / $sumTotal, 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

    </tr>
    <?php
    }
    ?>
    <tr>
        <td class="foot_average">
            Tổng cộng
        </td>
        <td class="foot_average">
            {{$Internet_IBB_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$Internet_IBB_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$Internet_IBB_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_IBB_TQ_CUS_CSAT ) != 0) ? round(($Internet_IBB_TQ_CSAT12 / $Internet_IBB_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_IBB_TQ_CUS_CSAT ) != 0) ? round(($Internet_IBB_TQ_CSAT / $Internet_IBB_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$TV_IBB_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$TV_IBB_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{ $TV_IBB_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_IBB_TQ_CUS_CSAT ) != 0) ? round(($TV_IBB_TQ_CSAT12 / $TV_IBB_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_IBB_TQ_CUS_CSAT ) != 0) ? round(($TV_IBB_TQ_CSAT / $TV_IBB_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$Internet_TS_TQ_CSAT1  }}
        </td>
        <td class="foot_average">
            {{$Internet_TS_TQ_CSAT2  }}
        </td>
        <td class="foot_average">
            {{$Internet_TS_TQ_CSAT12  }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_TS_TQ_CUS_CSAT ) != 0) ? round(($Internet_TS_TQ_CSAT12 / $Internet_TS_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_TS_TQ_CUS_CSAT ) != 0) ? round(($Internet_TS_TQ_CSAT / $Internet_TS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$TV_TS_TQ_CSAT1  }}
        </td>
        <td class="foot_average">
            {{$TV_TS_TQ_CSAT2  }}
        </td>
        <td class="foot_average">
            {{$TV_TS_TQ_CSAT12  }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_TS_TQ_CUS_CSAT ) != 0) ? round(($TV_TS_TQ_CSAT12 / $TV_TS_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_TS_TQ_CUS_CSAT ) != 0) ? round(($TV_TS_TQ_CSAT / $TV_TS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$Internet_TIN_TQ_CSAT1  }}
        </td>
        <td class="foot_average">
            {{$Internet_TIN_TQ_CSAT2  }}
        </td>
        <td class="foot_average">
            {{$Internet_TIN_TQ_CSAT12  }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_TIN_TQ_CUS_CSAT ) != 0) ? round(($Internet_TIN_TQ_CSAT12 / $Internet_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_TIN_TQ_CUS_CSAT ) != 0) ? round(($Internet_TIN_TQ_CSAT / $Internet_TIN_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$TV_TIN_TQ_CSAT1  }}
        </td>
        <td class="foot_average">
            {{$TV_TIN_TQ_CSAT2  }}
        </td>
        <td class="foot_average">
            {{$TV_TIN_TQ_CSAT12  }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_TIN_TQ_CUS_CSAT ) != 0) ? round(($TV_TIN_TQ_CSAT12 / $TV_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_TIN_TQ_CUS_CSAT ) != 0) ? round(($TV_TIN_TQ_CSAT / $TV_TIN_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$Internet_INDO_TQ_CSAT1   }}
        </td>
        <td class="foot_average">
            {{$Internet_INDO_TQ_CSAT2   }}
        </td>
        <td class="foot_average">
            {{$Internet_INDO_TQ_CSAT12   }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_INDO_TQ_CUS_CSAT ) != 0) ? round(($Internet_INDO_TQ_CSAT12 / $Internet_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_INDO_TQ_CUS_CSAT ) != 0) ? round(($Internet_INDO_TQ_CSAT / $Internet_INDO_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$TV_INDO_TQ_CSAT1   }}
        </td>
        <td class="foot_average">
            {{$TV_INDO_TQ_CSAT2   }}
        </td>
        <td class="foot_average">
            {{$TV_INDO_TQ_CSAT12   }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_INDO_TQ_CUS_CSAT ) != 0) ? round(($TV_INDO_TQ_CSAT12 / $TV_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_INDO_TQ_CUS_CSAT ) != 0) ? round(($TV_INDO_TQ_CSAT / $TV_INDO_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$Internet_TIN_TQ_CSAT1  }}
        </td>
        <td class="foot_average">
            {{$Internet_TIN_TQ_CSAT2  }}
        </td>
        <td class="foot_average">
            {{$Internet_TIN_TQ_CSAT12  }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_TIN_TQ_CUS_CSAT ) != 0) ? round(($Internet_TIN_TQ_CSAT12 / $Internet_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_TIN_TQ_CUS_CSAT ) != 0) ? round(($Internet_TIN_TQ_CSAT / $Internet_TIN_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$TV_HIFPT_TIN_TQ_CSAT1  }}
        </td>
        <td class="foot_average">
            {{$TV_HIFPT_TIN_TQ_CSAT2  }}
        </td>
        <td class="foot_average">
            {{$TV_HIFPT_TIN_TQ_CSAT12  }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_HIFPT_TIN_TQ_CUS_CSAT ) != 0) ? round(($TV_HIFPT_TIN_TQ_CSAT12 / $TV_HIFPT_TIN_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_HIFPT_TIN_TQ_CUS_CSAT ) != 0) ? round(($TV_HIFPT_TIN_TQ_CSAT / $TV_HIFPT_TIN_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$Internet_HIFPT_INDO_TQ_CSAT1   }}
        </td>
        <td class="foot_average">
            {{$Internet_HIFPT_INDO_TQ_CSAT2   }}
        </td>
        <td class="foot_average">
            {{$Internet_HIFPT_INDO_TQ_CSAT12   }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($Internet_HIFPT_INDO_TQ_CSAT12 / $Internet_HIFPT_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($Internet_HIFPT_INDO_TQ_CSAT / $Internet_HIFPT_INDO_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$TV_HIFPT_INDO_TQ_CSAT1   }}
        </td>
        <td class="foot_average">
            {{$TV_HIFPT_INDO_TQ_CSAT2   }}
        </td>
        <td class="foot_average">
            {{$TV_HIFPT_INDO_TQ_CSAT12   }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($TV_HIFPT_INDO_TQ_CSAT12 / $TV_HIFPT_INDO_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_HIFPT_INDO_TQ_CUS_CSAT ) != 0) ? round(($TV_HIFPT_INDO_TQ_CSAT / $TV_HIFPT_INDO_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$Internet_CUS_TQ_CSAT1   }}
        </td>
        <td class="foot_average">
            {{$Internet_CUS_TQ_CSAT2   }}
        </td>
        <td class="foot_average">
            {{$Internet_CUS_TQ_CSAT12   }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_CUS_TQ_CUS_CSAT ) != 0) ? round(($Internet_CUS_TQ_CSAT12 / $Internet_CUS_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_CUS_TQ_CUS_CSAT ) != 0) ? round(($Internet_CUS_TQ_CSAT / $Internet_CUS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$TV_CUS_TQ_CSAT1   }}
        </td>
        <td class="foot_average">
            {{$TV_CUS_TQ_CSAT2   }}
        </td>
        <td class="foot_average">
            {{$TV_CUS_TQ_CSAT12   }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_CUS_TQ_CUS_CSAT ) != 0) ? round(($TV_CUS_TQ_CSAT12 / $TV_CUS_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_CUS_TQ_CUS_CSAT ) != 0) ? round(($TV_CUS_TQ_CSAT / $TV_CUS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$DGDichVu_Counter_TQ_CSAT1   }}
        </td>
        <td class="foot_average">
            {{$DGDichVu_Counter_TQ_CSAT2   }}
        </td>
        <td class="foot_average">
            {{$DGDichVu_Counter_TQ_CSAT12   }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($DGDichVu_Counter_TQ_CUS_CSAT ) != 0) ? round(($DGDichVu_Counter_TQ_CSAT12 / $DGDichVu_Counter_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($DGDichVu_Counter_TQ_CUS_CSAT ) != 0) ? round(($DGDichVu_Counter_TQ_CSAT / $DGDichVu_Counter_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$Internet_SS_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$Internet_SS_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$Internet_SS_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_SS_TQ_CUS_CSAT ) != 0) ? round(($Internet_SS_TQ_CSAT12 / $Internet_SS_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_SS_TQ_CUS_CSAT ) != 0) ? round(($Internet_SS_TQ_CSAT / $Internet_SS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$TV_SS_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$TV_SS_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{ $TV_SS_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_SS_TQ_CUS_CSAT ) != 0) ? round(($TV_SS_TQ_CSAT12 / $TV_SS_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_SS_TQ_CUS_CSAT ) != 0) ? round(($TV_SS_TQ_CSAT / $TV_SS_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
        <td class="foot_average">
            {{$Internet_SSW_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$Internet_SSW_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{$Internet_SSW_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($Internet_SSW_TQ_CUS_CSAT ) != 0) ? round(($Internet_SSW_TQ_CSAT12 / $Internet_SSW_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($Internet_SSW_TQ_CUS_CSAT ) != 0) ? round(($Internet_SSW_TQ_CSAT / $Internet_SSW_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>

        <td class="foot_average">
            {{$TV_SSW_TQ_CSAT1 }}
        </td>
        <td class="foot_average">
            {{$TV_SSW_TQ_CSAT2 }}
        </td>
        <td class="foot_average">
            {{ $TV_SSW_TQ_CSAT12 }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_SSW_TQ_CUS_CSAT ) != 0) ? round(($TV_SSW_TQ_CSAT12 / $TV_SSW_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_SSW_TQ_CUS_CSAT ) != 0) ? round(($TV_SSW_TQ_CSAT / $TV_SSW_TQ_CUS_CSAT), 2) : 0;
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
        <td class="foot_average">
            {{$TV_KHL_TQ_CSAT1   }}
        </td>
        <td class="foot_average">
            {{$TV_KHL_TQ_CSAT2   }}
        </td>
        <td class="foot_average">
            {{$TV_KHL_TQ_CSAT12   }}
        </td>
        <td class="foot_average">
            <?php
            $rateNotSastisfied = (($TV_KHL_TQ_CUS_CSAT ) != 0) ? round(($TV_KHL_TQ_CSAT12 / $TV_KHL_TQ_CUS_CSAT ) * 100, 2) : 0;
            ?>
            {{$rateNotSastisfied.'%'}}
        </td>
        <td class="foot_average">
            <?php
            $csatAverage = (($TV_KHL_TQ_CUS_CSAT ) != 0) ? round(($TV_KHL_TQ_CSAT / $TV_KHL_TQ_CUS_CSAT), 2) : 0;
            ?>
            {{$csatAverage}}
        </td>
    </tr>
    </tbody>

</table>
    <h3 class="header smaller lighter red">
        <i class="icon-table"></i>
        Tổng hợp hành động xử lý CSAT 1, 2 CLDV của nhân viên CSKH
    </h3>
    <table id="table-CSAT12ActionServiceReport" class="table table-striped table-bordered table-hover"  cellspacing="0" width= "100%" style="max-width: 100%;overflow: auto;">
        <thead>
            <tr>
                <th rowspan="3" colspan="1" class="text-center evaluate-cell">Hành động đã xử lý của nhân viên CSKH</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.Telesale Deployment')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.Maintenance TIN-PNC')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.Maintenance INDO')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.SBTHITIN')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.SBTHIINDO')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.CUS')}}</th>
                <th colspan="2" class="text-center">{{trans($transfile.'.After Paid Counter')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.After Sale Staff')}}</th>
                <th colspan="4" class="text-center">{{trans($transfile.'.After Swap')}}</th>
                <th colspan="4" class="text-center">Tổng cộng</th>
            </tr>
            <tr>

                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                <th colspan="2" class="text-center">Chất lượng DV</th>
                <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                 <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
                 <th colspan="2"  class="text-center">CSAT 1,2 Internet</th>
                <th colspan="2" class="text-center">CSAT 1,2 Truyền hình</th>
            </tr>
            <tr>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
                <th colspan="1" class="text-center">Số lượng</th>
                <th colspan="1" class="text-center">Tỉ lệ(%)</th>
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
                        {{$value->action}}
                    </td>
                    <td >
                        {{$value->INTERNET_IBB_CSAT_12}}                       
                    </td>
                    <td>
                        <?php
//                        if (isset($netMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_IBB_CSAT_12) != 0) ? round(($value->INTERNET_IBB_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_IBB_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>
                    <td>
                        {{$value->TV_IBB_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_IBB_CSAT_12) != 0) ? round(($value->TV_IBB_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_IBB_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>
                    <td>
                        {{$value->INTERNET_TS_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_TS_CSAT_12) != 0) ? round(($value->INTERNET_TS_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_TS_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->TV_TS_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_TS_CSAT_12) != 0) ? round(($value->TV_TS_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_TS_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->INTERNET_TIN_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_TIN_CSAT_12) != 0) ? round(($value->INTERNET_TIN_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_TIN_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->TV_TIN_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_TIN_CSAT_12) != 0) ? round(($value->TV_TIN_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_TIN_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->INTERNET_INDO_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_INDO_CSAT_12) != 0) ? round(($value->INTERNET_INDO_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_INDO_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->TV_INDO_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_HIFPT_INDO_CSAT_12) != 0) ? round(($value->TV_HIFPT_INDO_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_HIFPT_INDO_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>
                    
                     <td>
                        {{$value->INTERNET_HIFPT_TIN_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_HIFPT_TIN_CSAT_12) != 0) ? round(($value->INTERNET_HIFPT_TIN_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_HIFPT_TIN_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->TV_HIFPT_TIN_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_HIFPT_TIN_CSAT_12) != 0) ? round(($value->TV_HIFPT_TIN_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_HIFPT_TIN_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->INTERNET_HIFPT_INDO_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_HIFPT_INDO_CSAT_12) != 0) ? round(($value->INTERNET_HIFPT_INDO_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_HIFPT_INDO_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->TV_HIFPT_INDO_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_HIFPT_INDO_CSAT_12) != 0) ? round(($value->TV_HIFPT_INDO_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_HIFPT_INDO_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>                    

                    <td>
                        {{$value->INTERNET_CUS_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_CUS_CSAT_12) != 0) ? round(($value->INTERNET_CUS_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_CUS_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->TV_CUS_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_CUS_CSAT_12) != 0) ? round(($value->TV_CUS_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_CUS_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>

                    <td>
                        {{$value->DGDichVu_Counter_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->DGDichVu_Counter_CSAT_12) != 0) ? round(($value->TV_CUS_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->DGDichVu_Counter_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>
                    <td >
                        {{$value->INTERNET_SS_CSAT_12}}                       
                    </td>
                    <td>
                        <?php
//                        if (isset($netMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_SS_CSAT_12) != 0) ? round(($value->INTERNET_SS_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_SS_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>
                    <td>
                        {{$value->TV_SS_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_SS_CSAT_12) != 0) ? round(($value->TV_SS_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_SS_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>
                    <td >
                        {{$value->INTERNET_SSW_CSAT_12}}                       
                    </td>
                    <td>
                        <?php
//                        if (isset($netMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_SSW_CSAT_12) != 0) ? round(($value->INTERNET_SSW_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->INTERNET_SSW_CSAT_12) * 100, 2) : 0;
//                        else
//                            $rateAction = 0;
                        ?>
                        {{$rateAction.'%'}}
                    </td>
                    <td>
                        {{$value->TV_SSW_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_SSW_CSAT_12) != 0) ? round(($value->TV_SSW_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TV_SSW_CSAT_12) * 100, 2) : 0;
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

                    <td>
                        {{$value->TOTAL_TV_CSAT_12}}    
                    </td>
                    <td>
                        <?php
//                        if (isset($tvMap[$key]))
                        $rateAction = (($surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TOTAL_TV_CSAT_12) != 0) ? round(($value->TOTAL_TV_CSAT_12 / $surveyCSATActionService12[count($surveyCSATActionService12) - 1]->TOTAL_TV_CSAT_12) * 100, 2) : 0;
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
            $('#viewLocationStaffButton').click(function(){
                $('#CSAT12StaffReportRegion').css('display', 'inline-block')
                $('#CSAT12StaffReportBranch').css('display', 'none')
            })
            $('#viewBranchStaffButton').click(function(){
                $('#CSAT12StaffReportRegion').css('display', 'none')
                $('#CSAT12StaffReportBranch').css('display', 'inline-block')
            })
            
            $('#viewLocationServiceButton').click(function(){
                $('#CSAT12ServiceReportRegion').css('display', 'inline-block')
                $('#CSAT12ServiceReportBranch').css('display', 'none')
            })
            $('#viewBranchServiceButton').click(function(){
                $('#CSAT12ServiceReportRegion').css('display', 'none')
                $('#CSAT12ServiceReportBranch').css('display', 'inline-block')
            })
        var tableCSAT12StaffReport = $('.table-CSAT12StaffReport').dataTable({
        "bAutoWidth": false,
                "aoColumns": [
                {"sType": 'numeric', "bSortable": false}
    <?php
    for ($i = 1; $i <= 65; $i++) {
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
    for ($i = 1; $i <= 105; $i++) {
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
    for ($i = 1; $i <= 42; $i++) {
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
for ($i = 1; $i <= 64; $i++) {
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
        var arr4 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVu_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVu_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVu_TV']; ?>
        }];
        var arr13 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVKinhDoanhTS']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVKinhDoanhTS']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVKinhDoanhTS']; ?>
        }];
        var arr14 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVTrienKhaiTS']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVTrienKhaiTS']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVTrienKhaiTS']; ?>
        }];
        var arr15 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVuTS_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVuTS_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVuTS_Net']; ?>
        }];
        var arr16 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVuTS_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVuTS_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVuTS_TV']; ?>
        }];
        var arr5 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVBaoTriTIN']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVBaoTriTIN']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVBaoTriTIN']; ?>
        }];
        var arr6 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTriTIN_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTriTIN_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTriTIN_Net']; ?>
        }];
        var arr7 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTriTIN_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTriTIN_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTriTIN_TV']; ?>
        }];
        var arr8 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVBaoTriINDO']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVBaoTriINDO']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVBaoTriINDO']; ?>
        }];
        var arr9 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTriINDO_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTriINDO_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTriINDO_Net']; ?>
        }];
        var arr10 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTriINDO_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTriINDO_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTriINDO_TV']; ?>
        }];
        var arr11 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVu_MobiPay_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVu_MobiPay_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVu_MobiPay_Net']; ?>
        }];
        var arr12 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVu_MobiPay_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVu_MobiPay_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVu_MobiPay_TV']; ?>
        }];
        var arr17 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVThuCuoc']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVThuCuoc']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVThuCuoc']; ?>
        }];
        var arr18 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVu_Counter']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVu_Counter']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVu_Counter']; ?>
        }];
        var arr19 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NV_Counter']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NV_Counter']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NV_Counter']; ?>
        }];
        var arr20 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVKinhDoanhSS']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVKinhDoanhSS']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVKinhDoanhSS']; ?>
        }];
        var arr21 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVTrienKhaiSS']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVTrienKhaiSS']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVTrienKhaiSS']; ?>
        }];
        var arr22 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVuSS_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVuSS_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVuSS_Net']; ?>
        }];
        var arr23 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVuSS_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVuSS_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVuSS_TV']; ?>
        }];
        var arr24 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVBT_SSW']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVBT_SSW']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVBT_SSW']; ?>
        }];
        var arr25 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVuSSW_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVuSSW_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVuSSW_Net']; ?>
        }];
        var arr26 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DGDichVuSSW_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DGDichVuSSW_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DGDichVuSSW_TV']; ?>
        }];
         var arr27 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['TQ_HMI']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['TQ_HMI']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['TQ_HMI']; ?>
        }];
         var arr28 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVBaoTriHIFPT_TIN']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVBaoTriHIFPT_TIN']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVBaoTriHIFPT_TIN']; ?>
        }];
        var arr29 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTriHIFPT_TIN_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTriHIFPT_TIN_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTriHIFPT_TIN_Net']; ?>
        }];
        var arr30 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTriHIFPT_TIN_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTriHIFPT_TIN_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTriHIFPT_TIN_TV']; ?>
        }];
        var arr31 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['NVBaoTriHIFPT_INDO']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['NVBaoTriHIFPT_INDO']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['NVBaoTriHIFPT_INDO']; ?>
        }];
        var arr32 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTriHIFPT_INDO_Net']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTriHIFPT_INDO_Net']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTriHIFPT_INDO_Net']; ?>
        }];
        var arr33 =
        [{
        name: '<?php echo $note['CSAT_12']; ?>',
                y: <?php echo $arrUnsatisfiedPercent['DVBaoTriHIFPT_INDO_TV']; ?>,
                sliced: true,
                selected: true
        }, {
        name: '<?php echo $note['CSAT_3']; ?>',
                y: <?php echo $arrNeutralPercent['DVBaoTriHIFPT_INDO_TV']; ?>
        }, {
        name: '<?php echo $note['CSAT_45']; ?>',
                y: <?php echo $arrSatisfiedPercent['DVBaoTriHIFPT_INDO_TV']; ?>
        }];
        
        createChart('chartCSAT', arr);
        createChart('chartCSAT2', arr2);
        createChart('chartCSAT3', arr3);
        createChart('chartCSAT4', arr4);
        createChart('chartCSAT13', arr13);
        createChart('chartCSAT14', arr14);
        createChart('chartCSAT15', arr15);
        createChart('chartCSAT16', arr16);
        createChart('chartCSAT17', arr17);
        createChart('chartCSAT18', arr18);
        createChart('chartCSAT27', arr27);
        createChart('chartCSAT19', arr19);
        createChart('chartCSAT20', arr20);
        createChart('chartCSAT21', arr21);
        createChart('chartCSAT22', arr22);
        createChart('chartCSAT23', arr23);
        createChart('chartCSAT24', arr24);
        createChart('chartCSAT25', arr25);
        createChart('chartCSAT26', arr26);       
        createChart('chartCSAT5', arr5);
        createChart('chartCSAT6', arr6);
        createChart('chartCSAT7', arr7);
        createChart('chartCSAT8', arr8);
        createChart('chartCSAT9', arr9);
        createChart('chartCSAT10', arr10);
        createChart('chartCSAT11', arr11);
        createChart('chartCSAT12', arr12);
        createChart('chartCSAT28', arr28);
        createChart('chartCSAT29', arr29);
        createChart('chartCSAT30', arr30);
        createChart('chartCSAT31', arr31);
        createChart('chartCSAT32', arr32);
        createChart('chartCSAT33', arr33);
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
    #table-CSATReport_wrapper, #DataTables_Table_24_wrapper, #DataTables_Table_25_wrapper, #DataTables_Table_26_wrapper, #DataTables_Table_27_wrapper, #table-CSATObjectReport, #table-CSATHMIReport, #CSAT12ServiceReportRegion_wrapper,
    #CSAT12ServiceReportBranch_wrapper, #CSAT12StaffReportRegion_wrapper, #CSAT12StaffReportBranch_wrapper,  .table-CSAT12StaffReport_wrapper, #table-CSAT12ActionServiceReport_wrapper, .table-responsive
    {
        overflow: auto;
    }
</style>