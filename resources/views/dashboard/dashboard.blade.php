@extends('layouts.app')
@section('content')
<?php
$controller = 'DashboardController';
$title = 'Dashboard';
$transfile = 'dashboard';
$arrStatus = ['TongCong' => 'Tổng cộng', 'ThanhCong' => 'Thành công', 'KhongGapNguoiSD' => 'Không gặp người sử dụng',
    'KHTuChoiCS' => 'Khách hàng từ chối CS', 'KhongLienLacDuoc' => 'Không liên lạc được', 'KhongCanLienHe' => 'Không cần liên hệ'];
//            var_dump($arrCountry);
//            var_dump($arrCSATToday);
//            var_dump($arrCSATYesterday);
//            var_dump($survey_branches);
//            die;
?>
<div class="page-content">
    {!! csrf_field() !!}
    <!--@include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transfile])-->
        <div class="col-xs-12">
            <div class="col-xs-12 no-padding">
                <!-- PAGE CONTENT BEGINS -->
                <!--state overview start-->
                <div class="row state-overview">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <div class="widget-box">
                                <div class="widget-header widget-header-flat lighter smaller blue">
                                    <h4>{{trans($transfile.'.CUSTOMERSATISFACTIONCSATOFLASTEST7DAYS')}} ( {{date('d/m/Y',strtotime($from_date))}} - {{date('d/m/Y',strtotime($to_date))}})</h4>
                                    <!--
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Warning</button>
                                                                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                            <a class="dropdown-item" href="#">Xuất Excel sự hài lòng khách hàng, điểm CSAT toàn quốc, ý kiến đóng góp của khách hàng, số lượng khảo sát 7 ngày gần nhất</a>
                                                                            <a class="dropdown-item" href="#">Xuất Excel top CSAT & NPS 7 ngày gần nhất</a>
                                                                        </div>
                                                                    </div>-->

                                    <div class="btn-group"> <button title="Xuất Excel" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <img  style="
                                     width: 38px;
                                     cursor: pointer;" src="/assets/img/excel.png"> </button>
                                        <ul class="dropdown-menu">
                                            <li class="exportExcel" val="1"><a href="#">{{trans('report.ExportExcelCustomerSatisfactionCSATPointCustomersFeedbacksSurveyQuantityLastest7Days')}}</a></li>
                                            <li class="exportExcel" val="2"><a href="#">{{trans('report.ExportExcelTopCsatNpsLastest7Days')}}</a></li>

                                        </ul>

                                </div>
                                </div>
                                <div class="widget-body">
                                    <div class="widget-main" style="padding: 1px 12px 12px 12px !important;">
                                        <p class="title-data-service red" style="padding-left: 10px"><i class="icon-table"></i>&nbsp;&nbsp;{{trans($transfile.'.Deployment')}}</p>
                                        <div class="row">
                                            <div class="col-xs-12 row-data-service">
                                                <div class="content-table-service col-xs-6 col-sm-4 bg-nvkd">
                                                    @include($transfile.'.csat_by_date',['title' => trans($transfile.'.Saler'), 'pointCountry' =>$csatSTK['NVKD_STK_CSAT_SEVEN_DAY_AGO']['ĐTB'],
                                                    'pointToday' => $csatSTK['NVKD_STK_CSAT_TODAY']['ĐTB'], 'pointYesterday' => $csatSTK['NVKD_STK_CSAT_YESTERDAY']['ĐTB'],
                                                    'pointLastweek' =>$csatSTK['NVKD_STK_CSAT_LAST_WEEK']['ĐTB'], 'pointLastmonth' => $csatSTK['NVKD_STK_CSAT_LAST_MONTH']['ĐTB']])
                                                </div>
                                                <div class="content-table-service col-xs-6 col-sm-4 bg-nvtk">
                                                    @include($transfile.'.csat_by_date',['title' => trans($transfile.'.Deployer'), 'pointCountry' => $csatSTK['NVTK_STK_CSAT_SEVEN_DAY_AGO']['ĐTB'],
                                                    'pointToday' => $csatSTK['NVTK_STK_CSAT_TODAY']['ĐTB'], 'pointYesterday' => $csatSTK['NVTK_STK_CSAT_YESTERDAY']['ĐTB'],
                                                    'pointLastweek' => $csatSTK['NVTK_STK_CSAT_LAST_WEEK']['ĐTB'], 'pointLastmonth' => $csatSTK['NVTK_STK_CSAT_LAST_MONTH']['ĐTB']])
                                                </div>
                                                <div class="content-table-service col-xs-6 col-sm-4 bg-cldv-net">
                                                    @include($transfile.'.csat_by_date',['title' => 'Internet', 'pointCountry' =>  $csatSTK['Internet_STK_CSAT_SEVEN_DAY_AGO']['ĐTB'],
                                                    'pointToday' => $csatSTK['Internet_STK_CSAT_TODAY']['ĐTB'], 'pointYesterday' =>$csatSTK['Internet_STK_CSAT_YESTERDAY']['ĐTB'],
                                                    'pointLastweek' => $csatSTK['Internet_STK_CSAT_LAST_WEEK']['ĐTB'], 'pointLastmonth' => $csatSTK['Internet_STK_CSAT_LAST_MONTH']['ĐTB']])
                                                </div>
                                            </div>
                                        </div>
                                        <p class="title-data-service red" style="padding-left: 10px"><i class="icon-table"></i>&nbsp;&nbsp;{{trans($transfile.'.Maintenance')}}</p>
                            <div class="row">
                                            <div class="col-xs-12 row-data-service">
                                                <div class="content-table-service col-xs-6 col-sm-4">
                                                </div>
                                                <div class="content-table-service col-xs-6 col-sm-4 bg-nvtk">
                                                    @include($transfile.'.csat_by_date',['title' =>  trans($transfile.'.MaintainanceStaff'), 'pointCountry' =>$csatSBT['NVBT_SBT_CSAT_SEVEN_DAY_AGO']['ĐTB'],
                                                    'pointToday' => $csatSBT['NVBT_SBT_CSAT_TODAY']['ĐTB'], 'pointYesterday' =>  $csatSBT['NVBT_SBT_CSAT_YESTERDAY']['ĐTB'],
                                                    'pointLastweek' => $csatSBT['NVBT_SBT_CSAT_LAST_WEEK']['ĐTB'],
                                                    'pointLastmonth' =>$csatSBT['NVBT_SBT_CSAT_LAST_MONTH']['ĐTB']])
                                                </div>
                                                <div class="content-table-service col-xs-6 col-sm-4 bg-cldv-net">
                                                    @include($transfile.'.csat_by_date',['title' => 'Internet', 'pointCountry' =>$csatSBT['Internet_SBT_CSAT_SEVEN_DAY_AGO']['ĐTB'],
                                                    'pointToday' => $csatSBT['Internet_SBT_CSAT_TODAY']['ĐTB'], 'pointYesterday' =>$csatSBT['Internet_SBT_CSAT_YESTERDAY']['ĐTB'],
                                                    'pointLastweek' => $csatSBT['Internet_SBT_CSAT_LAST_WEEK']['ĐTB'],
                                                    'pointLastmonth' =>$csatSBT['Internet_SBT_CSAT_LAST_MONTH']['ĐTB']])
                                                </div>
                                            </div>
                                    </div>
                                            <div class="col-xs-12 row-data-service fix-padding-table" style="display: block;">
                                                @include('report.csatReport',['survey' => $detailCSAT['survey'], 'avg' => (array)$detailCSAT['avg'], 'total' => (array)$detailCSAT['total'], 'region' => '', 'viewFrom' => 1])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--state overview end-->
            </div>
        </div>

<div class="page-content">
    <div class="col-xs-12 no-padding">

        <div class="widget-box">
            <div class="widget-header widget-header-flat lighter smaller blue">
                <h4>{{trans($transfile.'.CUSTOMERADOVACYOFLASTEST7DAYS')}} ( {{date('d/m/Y',strtotime($from_date))}} - {{date('d/m/Y',strtotime($to_date))}})</h4>
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <div class="col-xs-12 row-data-service one-table-data">
                        <div class="content-table-service col-xs-6 col-sm-3 bg-nps">
                            @include($transfile.'.csat_by_date',['title' => 'NPS', 'pointCountry' => $allNpsInfo['Toàn Quốc'],
                            'pointToday' => $allNpsInfo['today'], 'pointYesterday' => $allNpsInfo['yesterday'], 'pointLastweek' => $allNpsInfo['lastweek'], 'pointLastmonth' => $allNpsInfo['lastmonth']])
                        </div>
                        <div class="col-xs-12 row-data-service one-table-data">
                            @include('report.npsStatisticReport',['survey' => $detailNPS['survey'], 'total' => $detailNPS['total'], 'region' => '', 'viewFrom' => 1])
                        </div>
                        <br />
                        <div class="col-xs-12 row-data-service one-table-data">
                            @include('report.npsReport',['survey' => json_decode(json_encode($detailNPS['groupNPS']),1), 'total' => $detailNPS['total'], 'region' => '', 'viewFrom' => 1])
                        </div>
                        <div class="col-xs-12 row-data-service one-table-data">
                            @include('report.customersCommentReport',['survey' => $customerComment->survey, 'total' => (array)$customerComment->total, 'totalCusComment' => (array)$customerComment->totalCusComment, 'totalCusNoComment' => (array)$customerComment->totalCusNoComment, 'totalConsulted' => (array)$customerComment->totalConsulted, 'region' => '', 'viewFrom' => 1])
                        </div>
                    </div>
                    <br />
                </div>
            </div>
        </div>

                                </div>
                            </div>

<div class="col-xs-12 no-padding">
    <div class="widget-box">
        <div class="widget-header widget-header-flat lighter smaller blue">
            <h4>{{trans($transfile.'.TOPCSATNPSOFLASTEST7DAYS')}} ( {{date('d/m/Y',strtotime($from_date))}} - {{date('d/m/Y',strtotime($to_date))}})</h4>
        </div>

        <div class="widget-body">
            <div class="widget-main">
                <div class="row">
                    <div class="col-xs-12 col-lg-7">
                        <!--<div class="chart-wrapper" style="overflow: auto">-->
                        <div style="overflow: auto" class="table-responsive">
                            <div id="chartCSAT" style="height: 100%;">
                            </div>
                        </div>
                        <!--</div>-->

                        <div class="widget-box transparent" id="recent-box">
                            <div class="widget-header">
                                <h4 class="lighter smaller">
                                    <i class="icon-table orange"></i>
                                    {{trans($transfile.'.CSATPointOfSatisfaction')}}
                                </h4>

                            </div>

                            <div class="widget-body">
                                <div class="widget-main padding-4">
                                    <div class="tab-content padding-8 overflow-visible">
                                        <div id="branchesCSAT" >
                                            <h4 class="smaller lighter green">
                                                <i class="icon-list"></i>
                                                {{trans($transfile.'.Top10BranchCSAT')}}
                                            </h4>

                                            <table id="table-CSATBranches" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
                                                <thead>
                                                <tr>
                                                    <th id='name' rowspan="3" class="text-center">{{trans($transfile.'.Branches')}}</th>
                                                    <th colspan="3" class="text-center">{{trans($transfile.'.Deployment')}}</th>
                                                    <th colspan="2" class="text-center">{{trans($transfile.'.Maintenance')}}</th>
                                                </tr>
                                                <tr>
                                                    <th rowspan="2" id="NVKinhDoanh_AVGPoint" >{{trans($transfile.'.Saler')}}</th>
                                                    <th rowspan="2" id="NVTrienKhai_AVGPoint">{{trans($transfile.'.Deployer')}}</th>
                                                    <th colspan="1" class="text-center">{{trans($transfile.'.Rating Quality Service')}}</th>


                                                    <th rowspan="2" class="text-center" id="NVBaoTri_AVGPoint">{{trans($transfile.'.MaintainanceStaff')}}</th>
                                                    <th colspan="1" class="text-center">{{trans($transfile.'.Rating Quality Service')}}</th>

                                                </tr>
                                                <tr>
                                                    <th id='DGDichVu_Net_AVGPoint' class="text-center">{{trans($transfile.'.Net')}}</th>
                                                    <th id='DGDichVu_TV_AVGPoint' class="text-center">{{trans($transfile.'.Net')}}</th>



                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php
                                                if (!empty($survey_branches)) {
                                                $arrCSATBranches = $arrCSATBranchesX = [];
                                                foreach ($survey_branches as $val) {
                                                $branch = $val->KhuVuc;
                                                //lấy dữ liệu show ra biểu đồ cột
                                                $arrCSATBranches['NVKD'] = trans($transfile.'.Saler');
                                                $arrCSATBranches['NVTK'] = trans($transfile.'.Deployer');
                                                $arrCSATBranches['DVTK_Net'] = 'Internet';


                                                $arrCSATBranches['NVBT'] = trans($transfile.'.MaintainanceStaff');
                                                $arrCSATBranches['DVBT_Net'] = 'Internet';



                                                //
                                                $arrCSATBranchesX[$branch][] = $val;
                                                ?>
                                                <tr>
                                                    <td><span>{{$branch}}</span></td>
                                                    <td><span class="number">{{number_format($val->NVKinhDoanh_AVGPoint, 2)}}</span></td>
                                                    <td><span class="number">{{number_format($val->NVTrienKhai_AVGPoint, 2)}}</span></td>
                                                    <td><span class="number">{{number_format($val->DGDichVu_Net_AVGPoint, 2)}}</span></td>


                                                    <td><span class="number">{{number_format($val->NVBaoTri_AVGPoint, 2)}}</span></td>
                                                    <td><span class="number">{{number_format($val->DVBaoTri_Net_AVGPoint, 2)}}</span></td>


                                                </tr>
                                                <?php
                                                }
                                                }
                                                ?>
                                                </tbody>
                                                <tfoot class='foot'>
                                                <?php if (!empty($arrCountry)) { ?>
                                                <tr>
                                                    <td><span>{{($arrCountry['KhuVuc']  == 'WholeCountry' ? trans($transfile.'.WholeCountry') : $arrCountry['KhuVuc'] )}}</span></td>
                                                    <td><span class="number">{{number_format($arrCountry['NVKinhDoanh_AVGPoint'], 2)}}</span></td>
                                                    <td><span class="number">{{number_format($arrCountry['NVTrienKhai_AVGPoint'], 2)}}</span></td>
                                                    <td><span class="number">{{number_format($arrCountry['DGDichVu_Net_AVGPoint'], 2)}}</span></td>

                                                    <td><span class="number">{{number_format($arrCountry['NVBaoTri_AVGPoint'], 2)}}</span></td>
                                                    <td><span class="number">{{number_format($arrCountry['DVBaoTri_Net_AVGPoint'], 2)}}</span></td>


                                                </tr>
                                                <?php } ?>
                                                </tfoot>
                                            </table>

                                        </div><!-- branchesCSAT -->


                                    </div>
                                </div><!-- /widget-main -->
                            </div><!-- /widget-body -->
                        </div>
                    </div>


                    <div class="col-xs-12 col-lg-5">
                        <div style="overflow: auto" class="table-responsive">
                            <div id="chartNPS"></div>
                            </div>
                        <div class="widget-box transparent" id="recent-box">
                            <div class="widget-header">
                                <h4 class="lighter smaller">
                                    <i class="icon-table orange"></i>
                                    {{trans($transfile.'.NPSPointOfAdovacy')}}
                                </h4>

                            </div>
                            {{--<div class="col-xs-12">--}}
                            <div class="widget-body">
                                <div class="widget-main padding-4">
                                    <div class="tab-content padding-8 overflow-visible">
                                        <div id="branchesNPS">
                                            <h4 class="smaller lighter green">
                                                <i class="icon-list"></i>
                                                {{trans($transfile.'.Top10BranchNPS')}}
                                            </h4>

                                            <table id="table-NPSBranches" class="table table-striped table-bordered table-hover" cellspacing="0" width= "100%">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">{{trans($transfile.'.Branches')}}</th>
                                                    <th id="npsPoint">{{trans($transfile.'.NPS Points')}}</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php
                                                if (!empty($npsBranches)) {
                                                $arrNPSBranches = $arrNPSBranchesX = [];
                                                foreach ($npsBranches as $k => $val) {
                                                //lấy dữ liệu show ra biểu đồ cột
                                                $arrNPSBranches[$k] = $k;
                                                $arrNPSBranchesX[$k][] = $val;
                                                ?>
                                                <tr>
                                                    <td><span>{{$k}}</span></td>
                                                    <td><span class="number">{{number_format(round($val,2), 2)." %"}}</span></td>
                                                </tr>
                                                <?php
                                                }
                                                }
                                                ?>
                                                </tbody>
                                                <tfoot class='foot'>
                                                <?php if (!empty($npsCountryRegion)) { ?>
                                                <tr>
                                                    <td><span>{{trans($transfile.'.WholeCountry')}}</span></td>
                                                    <td><span class="number">{{number_format(round($npsCountryRegion['Toàn Quốc'],2), 2)." %"}}</span></td>
                                                </tr>
                                                <?php } ?>
                                                </tfoot>
                                            </table>
                                        </div><!-- branchesNPS -->
                                    </div>
                                </div><!-- /widget-main -->
                            </div><!-- /widget-body -->
                        </div>
                        </div>


                        </div>
                    </div>
                </div>
            </div>
        {{--</div>--}}
    </div>

<div class="col-xs-12 no-padding">
    <div class="widget-box">
        <div class="widget-header widget-header-flat lighter smaller blue">
            <h4>{{trans($transfile.'.SurveyQuantityOfLastest7Days')}}( {{date('d/m/Y',strtotime($from_date))}} - {{date('d/m/Y',strtotime($to_date))}})</h4>
        </div>

        <div class="widget-body">
            <div class="widget-main">
                <div class="row">
                    @include('report.detailReport',$surveyNpsQuantity)
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-table-record" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-padding">
                <div class="table-header">
                    <button type="button" class="close" data-dismiss="modal" onclick="stopAll()" aria-hidden="true">
                        <span class="white">&times;</span>
                    </button>
                    Danh sách dữ liệu Excel
                </div>
            </div>

            <div class="modal-body" id="modal-table-record-body">
            </div>

            <div class="modal-footer no-margin-top">
                <button class="btn btn-sm btn-danger pull-left" data-dismiss="modal" onclick="stopAll()">
                    <i class="icon-remove"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

</div>

<!-- basic scripts -->

<!--[if !IE]> -->

<script type="text/javascript">
    window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>" + "<" + "/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

<script type="text/javascript">
    if ("ontouchend" in document)
        document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>

<!-- page specific plugin scripts -->

<!--[if lte IE 8]>
  <script src="assets/js/excanvas.min.js"></script>
<![endif]-->

<script src="{{asset('assets/js/jquery-ui-1.10.3.custom.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.ui.touch-punch.min.js')}}"></script>
<script src="{{asset('assets/js/highcharts.js')}}"></script>
<script src="{{asset('assets/js/exporting.js')}}"></script>
<script src="{{asset('assets/js/grouped-categories.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
<script src="{{asset('assets/js/drilldown.js')}}"></script>
<script src="{{asset('assets/js/jquery.sort-1.1.js')}}"></script>
<script src="{{asset('assets/js/num-html.js')}}"></script>

<!-- inline scripts related to this page -->

<script type="text/javascript">
    $(document).ready(function () {
        function toogleSeeMore()
        {
            if($('#seeMore').attr('more') == '1')
            {
                // $('.see').hide(1000);
                $('.see').css('display','none')
                $('#seeMore').text('Xem thêm')
                $('#seeMore').attr('more','0')
            }
            else
            {
                $('.see').css('display','inline-block')
                $('#seeMore').text('Ẩn bớt')
                $('#seeMore').attr('more','1')
            }
        }
        toogleSeeMore()
        $('#seeMore').click(function(){
            toogleSeeMore()
        })
        var column = [null, {"sType": "num-html"}];
        var morecolumn = [null, null, null, null, null, null];
        var columnSurveyStatus = [null, null, null];
        var sorting = [[1, "desc"]];
        var sorting2 = [[1, "desc"]];
        // create_datatable('table-NPSRegion', column, sorting, true, false);
        create_datatable('table-NPSBranches', column, sorting, true, true);
        // create_datatable('table-CSATRegion', morecolumn, sorting2, true, false);
        create_datatable('table-CSATBranches', morecolumn, sorting2, true, true);
        // create_datatable('table-surveyStatus', columnSurveyStatus, sorting, false, false);

        function create_datatable(id, column, sorting, isSort, isPaginate) {
            $("#" + id).dataTable({
                "aoColumns": column,
                "aaSorting": sorting,
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
                "bPaginate": isPaginate,
                "bInfo": false,
                "bSort": isSort,
                "sDom": "lfrti"
            });
        }
                var npsOptionsBranches = {
                chart: {
                type: 'column',
                renderTo: 'chartNPS'
                },
                title: {
                text: '<?php echo mb_convert_case(trans($transfile.'.NPSChartOfBranchADOVACY'), MB_CASE_TITLE, 'UTF-8');?>'
                },
                credits: {
                enabled: false
                },
                exporting: {enabled: true},
                xAxis: {
                categories: [
                <?php
                if (!empty($arrNPSBranches)) {
                foreach ($arrNPSBranches as $res) {
                echo "'$res'" . ',';
                }
                }
                ?>
                ],
                crosshair: true,
                labels: {
                enabled: false,
                }
                },
                yAxis: {
                min: 0,
                title: {
                text: '<?php echo mb_convert_case(trans($transfile.'.AdovacyIndex'), MB_CASE_TITLE, 'UTF-8');?>'
                },
                plotLines: [{
                value: [<?php echo number_format(round($npsCountryRegion['Toàn Quốc'], 2), 2) ?>], // Value of where the line will appear
                width: 2,
                color: '#ff0000',
                zIndex: 999,
                label: {text: '{{trans($transfile.'.WholeCountry')}}'}
                }]
                },
                tooltip: {
                headerFormat: '<table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.2f} %</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
                },
                plotOptions: {
                column: {
                pointPadding: 0.2,
                borderWidth: 0
                }
                },
                series: [
                <?php
                if (!empty($arrNPSBranchesX)) {
                $c = 1;
                foreach ($arrNPSBranchesX as $k => $nps) {
                if ($c <= 10) {
                echo "{ name: 'NPS " . $k . "', data: [" . $nps[0] . "]},";
                }
                $c++;
                }
                }
                ?>
                ]
                };
                var chartNPS = new Highcharts.Chart(npsOptionsBranches);


        var csatOptionsBranches = {
            chart: {
                type: 'column',
                renderTo: 'chartCSAT',
                width: 1000
            },
            title: {
                text: '<?php echo mb_convert_case(trans($transfile.'.CSATChartOfBranchSatisfaction'), MB_CASE_TITLE, 'UTF-8');?>'
            },
            credits: {
                enabled: false
            },
            exporting: {enabled: true},
            xAxis: {
                tickPixelInterval: 4,
                categories: [{
                    name: "<?php echo mb_convert_case(trans($transfile.'.Deployment'), MB_CASE_TITLE, 'UTF-8');?>",
                    categories: [
                        <?php
                        if (!empty($arrCSATBranches)) {
                            foreach ($arrCSATBranches as $key => $csat) {
                                if (in_array($key, ['NVKD', 'NVTK', "DVTK_Net"]))
                                    echo "'$csat'" . ',';
                            }
                        }
                        ?>
                    ],

                },
                    {
                        name: "<?php echo mb_convert_case(trans($transfile.'.Maintenance'), MB_CASE_TITLE, 'UTF-8');?>",
                        categories: [
                            <?php
                            if (!empty($arrCSATBranches)) {
                                foreach ($arrCSATBranches as $key => $csat) {
                                    if (in_array($key, ['NVBT', "DVBT_Net"]))
                                        echo "'$csat'" . ',';
                                }
                            }
                            ?>
                        ]
                    }],
//                     labels: {
//          useHTML:true,
//                style:{
//                    width:'50px',
//                },
//                step: 1
//            },
//            labels: {
//            useHTML:true,//Set to true
//            style:{
//                width:'200px',
//                whiteSpace:'normal'//set to normal
//            },
//            step: 1,
//            formatter: function () {//use formatter to break word.
//                return '<div align="center" style="word-wrap: break-word;word-break: break-all;width:150px">' + this.value + '</div>';
//            }
//        },
                crosshair: true,
            },
            yAxis: {
                min: 3,
                title: {
                    text: '<?php echo mb_convert_case(trans($transfile.'.SatisfactionIndex'), MB_CASE_TITLE, 'UTF-8');?>'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.2f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [

                <?php
                //dump($arrCSATBranchesX);die;
                if (!empty($arrCSATBranchesX)) {
                    $c = 1; //đếm
                    foreach ($arrCSATBranchesX as $k => $csat) {
                        if ($c <= 10) {
                            echo "{ name: '" . $k . "', data: [" . $csat[0]->NVKinhDoanh_AVGPoint . "," . $csat[0]->NVTrienKhai_AVGPoint . "," . $csat[0]->DGDichVu_Net_AVGPoint . "," .
                                $csat[0]->NVBaoTri_AVGPoint . "," . $csat[0]->DVBaoTri_Net_AVGPoint . "]},";
                            $c++;
                        }
                    }
                }
                if (!empty($arrCountry)) {
                    echo "{ name: '" . $arrCountry['KhuVuc'] . "', type: 'spline', data: [" . $arrCountry['NVKinhDoanh_AVGPoint'] . "," . $arrCountry['NVTrienKhai_AVGPoint'] . "," .
                        $arrCountry['DGDichVu_Net_AVGPoint'] . "," .
                        $arrCountry['NVBaoTri_AVGPoint'] . "," . $arrCountry['DVBaoTri_Net_AVGPoint'] . "]},";
                }
                ?>
            ]
        }
        // var chartCSAT = new Highcharts.Chart(csatOptions);
        //
        // $('a[href=#branchesNPS]').click(function () {
        //     chartNPS = new Highcharts.Chart(npsOptionsBranches);
        // });
        // $('a[href=#regionNPS]').click(function () {
        //     chartNPS = new Highcharts.Chart(npsOptions);
        // });
        // $('a[href=#branchesCSAT]').click(function () {
            var chartCSAT = new Highcharts.Chart(csatOptionsBranches);
        // });
        // $('a[href=#regionCSAT]').click(function () {
        //     chartCSAT = new Highcharts.Chart(csatOptions);
        // });

        $('#name, #NVKinhDoanh_AVGPoint, #NVTrienKhai_AVGPoint, #DGDichVu_Net_AVGPoint, #NVBaoTri_AVGPoint, #DVBaoTri_Net_AVGPoint').click(function () {
            var strSort = '';
            if ($(this).hasClass('sorting_asc') || $(this).hasClass('sorting_desc')) {
                strSort = $(this).attr('class').split('_');
                strSort = strSort[1];//kiểu sort
            }
            var csatOptionsBranchesNew = csatOptionsBranches;
            var temp = [<?php
                if (!empty($arrCSATBranchesX)) {
                    foreach ($arrCSATBranchesX as $k => $csat) {
                        echo "{ name: '" . $k . "', data: [" . $csat[0]->NVKinhDoanh_AVGPoint . "," . $csat[0]->NVTrienKhai_AVGPoint . "," . $csat[0]->DGDichVu_Net_AVGPoint. "," .
                            $csat[0]->NVBaoTri_AVGPoint . "," . $csat[0]->DVBaoTri_Net_AVGPoint . "]"
                            . ", NVKinhDoanh_AVGPoint: '{$csat[0]->NVKinhDoanh_AVGPoint}', NVTrienKhai_AVGPoint: '{$csat[0]->NVTrienKhai_AVGPoint}', DGDichVu_Net_AVGPoint: '{$csat[0]->DGDichVu_Net_AVGPoint}'"
                            . ", NVBaoTri_AVGPoint: '{$csat[0]->NVBaoTri_AVGPoint}', DVBaoTri_Net_AVGPoint: '{$csat[0]->DVBaoTri_Net_AVGPoint}'},"
                           ;
                        ;
                    }
                }
                ?>];
            csatOptionsBranchesNew.series = $(temp).sort($(this).attr('id'), strSort);
            csatOptionsBranchesNew.series = csatOptionsBranchesNew.series.slice(0, 10);//lấy 10 phần tử đầu tiên

            csatOptionsBranchesNew.series.push(
                <?php
                if (!empty($arrCountry)) {
                    echo "{ name: '" . $arrCountry['KhuVuc'] . "', type: 'spline', data: [" . $arrCountry['NVKinhDoanh_AVGPoint'] . "," . $arrCountry['NVTrienKhai_AVGPoint'] . "," . $arrCountry['DGDichVu_Net_AVGPoint']  . "," .
                        $arrCountry['NVBaoTri_AVGPoint'] . "," . $arrCountry['DVBaoTri_Net_AVGPoint'] . "
    ]}";
                }
                ?>);
            chartCSAT = new Highcharts.Chart(csatOptionsBranchesNew);
        });
        
        $('#npsPoint').click(function () {
            var strSort = '';
            if ($(this).hasClass('sorting_asc') || $(this).hasClass('sorting_desc')) {
                strSort = $(this).attr('class').split('_');
                strSort = strSort[1];//kiểu sort
            }
            var temp = [
                <?php
                if (!empty($arrNPSBranchesX)) {
                    foreach ($arrNPSBranchesX as $k => $nps) {
                        echo "{ name: 'NPS " . $k . "', data: [" . $nps[0] . "], npsPoint: {$nps[0]}},";
                    }
                }
                ?>
            ];
            npsOptionsBranches.series = $(temp).sort($(this).attr('id'), strSort);
            npsOptionsBranches.series = npsOptionsBranches.series.slice(0, 10);//lấy 10 phần tử đầu tiên
            chartNPS = new Highcharts.Chart(npsOptionsBranches);
        });
    });

    // Sự kiện thay đổi nội dung combobox
    $(".exportExcel").click(function () {
        var a = '<div class="center" id="spinner"><img src="{{asset("assets/img/bluespinner.gif")}}" /></div>';
        var type=$(this).attr('val');
        $('#modal-table-record-body').html(a);
        $('#modal-table-record').modal().show();

        $.ajax({
            url: '<?php echo url('/customer-voice/dashboard/exportToExcel') ?>',
            cache: false,
            type: "GET",
//                                    dataType: "xlsx",
            data: {_token: $('input[name=_token]').val(), type:type },
            beforeSend: function () {
            },
            complete: function () {
            },
            success: function (response, textStatus, request) {
                $('#modal-table-record').modal('hide');
                var a = document.createElement("a");
                a.href = response.file;
                a.download = response.name;
                document.body.appendChild(a);
                a.click();
                a.remove();
            },
            error: function (ajaxContext) {
                toastr.error('Export error: ' + ajaxContext.responseText);
            }
        });


    });
</script>
<style>
    #table-CSATBranches_wrapper, #table_surveyNPS_wrapper, #table-CSATRegion_wrapper, #sample-table-2_wrapper
    {
        overflow: auto !important;
    }
    .exportExcel a
    {
        font-size: 14px !important;
    }
    .chart-wrapper {
        position: relative;
        padding-bottom: 40%;
        width:50%;
        float:left;
    }

    .chart-inner {
        position: absolute;
        width: 60%; height: 100%;
    }
    /*    .highcharts-axis-labels span{
      word-break:break-all!important;
      width:100px!important;
      white-space:normal!important;
    }*/
    #highcharts-6 svg
    {
        width: 1900px !important;
    }

</style>
@stop