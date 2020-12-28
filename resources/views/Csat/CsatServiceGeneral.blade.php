@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
    $controller = $transfile = 'report';
    $common = 'common';
    $prefix = main_prefix;
    ?>
    <!-- PAGE CONTENT BEGINS -->
    <form class="form-horizontal" role="form" method="POST" action="{{ url('/'.$prefix.'/csat-service/general') }}">
        {!! csrf_field() !!}

        <div class="space-4"></div>

        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="btn-group">
                        <span>
                            <select id="sel_region" class="btnRegion" name="sel_region" multiple="multiple" style="display: none;">
                                @for($i = 1; $i <= 7; $i++)
                                @if(!empty($userGranted['region']) && in_array($i, $userGranted['region']))
                                <option value="{{$i}}">Vùng {{$i}}</option>
                                @endif
                                @endfor
                            </select>
                            <div id="container_button_region" style="display: none;">
                                <div id="dummy_button_region"></div>
                                <div id="button_region">
                                    <button type="button" class="btn-success btn btn-small" style="margin-top: 1px;" id="btnChooseRegion">Chọn</button>
                                    <button type="button" class="btn-default btn btn-small" style="margin-top: 1px;" id="btnCancelRegion">Thoát</button>
                                </div>
                            </div>
                        </span>
                        <span>
                            <select id="sel_branch" class="btnBranch" name="sel_branch" multiple="multiple" style="display: none;">
                                @if(!empty($branch))
                                @foreach($branch as $state)
                                <?php
                                if (!empty($userGranted['branchID']) && in_array($state->id, [4, 8]) && in_array($state->branch_id, $userGranted['branchID'])) {
                                    $name = str_replace(' - ', $state->branchcode . ' - ', $state->name);
                                    $value = $state->id . '_' . $state->branchcode;
                                    ?>
                                    <option value="{{$value}}">
                                        {{$name}}
                                    </option>
                                <?php
                                }
                                if (!empty($userGranted['location']) && !in_array($state->id, [4, 8]) && in_array($state->id, $userGranted['location'])) {
                                    ?>
                                    <option value="{{$state->id}}">
                                        {{$state->name}}
                                    </option>
                                <?php } ?>
                                @endforeach
                                @endif
                            </select>
                            <div id="container_button_branch" style="display: none;">
                                <div id="dummy_button_region"></div>
                                <div id="button_region">
                                    <button type="button" class="btn-success btn btn-small" style="margin-top: 1px;" id="btnChooseBranch">Chọn</button>
                                    <button type="button" class="btn-default btn btn-small" style="margin-top: 1px;" id="btnCancelBranch">Thoát</button>
                                </div>
                            </div> 
                        </span>
                    </div>
                    <div class="btn-group" style="vertical-align: top!important;">
                        <button id="reportrange" type="button" class="btn btn-danger" style="height: 42px;margin-right: 3px;">{{trans($transfile.'.Choose Date')}}</button>
                        <input type="text" id="inputDate" disabled="disabled" style="width: auto;height: 42px;">
                    </div>
                    <div class="btn-group" style="vertical-align: top!important;">
                        <input name="viewStatus" id="viewStatusHidden" type="hidden" value="{{$viewStatus}}"/>
                        <button id="viewLocationButton" type="button" class="btn btn-success" style="height: 42px;margin-right: 3px;">Thống kê theo vùng</button>
                        <button id="viewBranchButton" type="button" class="btn btn-success" style="height: 42px;margin-right: 3px;">Thống kê theo chi nhánh</button>
                    </div>
                    <div style="float: right;"><a title="Xuất ra tệp Excel" href="#" id="exportToExcel" style="float: right;/* border-radius: 14px; */"><img style="width: 38px;" src="/assets/img/excel.png"></a></div>
                </div>
                <div class="center" id="spinner" style="display: none;"><img src="{{asset('assets/img/bluespinner.gif')}}" style="width: 5%;height: 5%" /></div>
                <div id='container_info_report'>
                    @include('Csat.CsatServiceGeneralPart1')
                </div>
            </div>
        </div>
        <input name="reg_hidden" id="reg_hidden" type="hidden" value=""/>
        <input name="type_hidden" id="type_hidden" type="hidden" value=""/>
        <input name="from_date_hidden" id="from_date_hidden" type="hidden" value="{{date('Y-m-d 00:00:00')}}"/>
        <input name="to_date_hidden" id="to_date_hidden" type="hidden" value="{{date('Y-m-d 23:59:59')}}"/>
        <input name="state_hidden" id="state_hidden" type="hidden" value=""/>
        <input name="branchcode_hidden" id="branchcode_hidden" type="hidden" value=""/>
<!--        <input name="excelValue" id="placeHolderExcel" type="hidden" value="<?php // echo ($jsonValue)  ?>"/>
        <input name="excelValue" id="NpsReport" type="hidden" value=""/>
        <input name="excelValue" id="CsatNpsBranch" type="hidden" value=""/>
        <input name="excelValue" id="QuantityReport" type="hidden" value=""/>
         <input name="excelValue" id="CsatReport" type="hidden" value=""/>-->
    </form>
    <!-- PAGE CONTENT ENDS -->
</div><!-- /.page-content -->

<link rel="stylesheet" href="{{asset('assets/css/daterangepicker2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-multiselect.css')}}" type="text/css">

<script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
<script src="{{asset('assets/js/highcharts.js')}}"></script>
<script src="{{asset('assets/js/exporting.js')}}"></script>
<script src="{{asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/bootstrap-multiselect.js')}}"></script>
<!-- Include Date Range Picker -->
<script type="text/javascript" src="{{asset('assets/js/daterangepicker.js')}}"></script>
<style type="text/css">
    #container_button_region,#container_button_branch{
        position: absolute;
    }
    #dummy_button_region{
        height: 305px;
    }
    ul.multiselect-container.dropdown-menu{
        width: 236px !important;
        margin-left: 195px !important;
        height: 302px;
        overflow: auto;
    }
    #button_region{
        border-width:2px;
        background-color:white;
        width: 160px;
        padding: 10px;
        position:absolute;
        z-index:1000;
        height:65px;
        margin-left: 195px !important;
        border-radius: .2em;
        border: 1px solid #cccccc;
        -webkit-transition: border linear .2s, box-shadow linear .2s;
        -moz-transition: border linear .2s, box-shadow linear .2s;
        -o-transition: border linear .2s, box-shadow linear .2s;
        transition: border linear .2s, box-shadow linear .2s;
    }
    #button_branch{
        border-width:2px;
        background-color:white;
        width: 160px;
        padding: 5px 10px;
        position:absolute;
        z-index:1000;
        height:55px;
        top: 305px;
        left: 195px;
        right: 68px;
        border-radius: .2em;
        margin-left: 0px !important;
        border: 1px solid #cccccc;
        -webkit-transition: border linear .2s, box-shadow linear .2s;
        -moz-transition: border linear .2s, box-shadow linear .2s;
        -o-transition: border linear .2s, box-shadow linear .2s;
        transition: border linear .2s, box-shadow linear .2s;
    }
</style>
<script type="text/javascript">
$(document).ready(function () {
    //Taọ biến lưu các giá trị phục vụ cho chức năng export ra excel
//   var typeExport=

    function cb(start, end) {
        $('#inputDate').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }
    cb(moment(), moment());

    $('#reportrange').daterangepicker({
        "startDate": moment(),
        "endDate": moment(),
        ranges: {
            "{{trans($transfile.'.Today')}}": [moment(), moment()],
            "{{trans($transfile.'.Yesterday')}}": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            "{{trans($transfile.'.Last 7 Days')}}": [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
            "{{trans($transfile.'.Last week')}}": [moment().subtract(1, 'week').startOf('week').add(1, 'days'), moment().subtract(1, 'week').endOf('week').add(1, 'days')],
            "{{trans($transfile.'.Last 30 Days')}}": [moment(moment().subtract(30, 'days')), moment().subtract(1, 'days')],
            "{{trans($transfile.'.This Month')}}": [moment().startOf('month'), moment().endOf('month')],
            "{{trans($transfile.'.Last Month')}}": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'DD/MM/YYYY',
            cancelLabel: "{{trans($transfile.'.Cancel Button')}}",
            applyLabel: "{{trans($transfile.'.Apply Button')}}",
            customRangeLabel: "{{trans($transfile.'.Custom Range')}}",
        },
    }, cb);
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        $('#from_date_hidden').val(picker.startDate.format('YYYY-MM-DD H:mm:s'));
        $('#to_date_hidden').val(picker.endDate.format('YYYY-MM-DD H:mm:s'));
        getDataToChartAfterChoose();
    });
    $('input[name=daterangepicker_start]').attr('disabled', 'disabled');
    $('input[name=daterangepicker_end]').attr('disabled', 'disabled');
    //multiselect
    $('#sel_region').multiselect({
        templates: {
            filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text" style="height: 42px"></div></li>',
        },
        buttonContainer: '<span  />',
        includeSelectAllOption: true,
        selectAllText: 'Tất cả',
        selectAllValue: 0,
        enableFiltering: true,
        filterPlaceholder: 'Tìm ...',
        nonSelectedText: 'Vùng',
        allSelectedText: 'Tất cả vùng',
        nSelectedText: 'chọn lựa',
        buttonClass: 'btn btn-info',
        onDropdownShow: function (event) {
            var dropdown = $('.btnRegion ul.multiselect-container.dropdown-menu');
            var toggle = $('.multiselect.dropdown-toggle');
            dropdown.css({width: 150, position: 'absolute'});

            var dummy = $('#container_button_region');
            dummy.show();
        },
        onDropdownHide: function (event) {
            var dummy = $('#container_button_region');
            dummy.hide();
        },
        onChange: function (element, checked) {
            var t = [];
            $.each($("#sel_region option:selected"), function () {
                t.push($(this).val());
            });
            $("#reg_hidden").val(t.join(','));
            //////get location
            var cat_id = t.join(',');
            $.ajax({
                url: '<?php echo url('/' . $prefix . '/' . $controller . '/getLocationByRegion') ?>',
                cache: false,
                type: "POST",
                dataType: "json",
                data: {'_token': $('input[name=_token]').val(), 'id_region': cat_id},
                success: function (data) {
                    var html = '';
                    $.each(data, function (index, item) {
                        var t = data[index].name.split(' - ');
                        var name = '';
                        var val = '';

                        if (data[index].branchcode !== null && typeof data[index].branchcode != 'undefined') {
                            name = t[0] + data[index].branchcode + ' - ' + t[1];
                            val = data[index].id + '_' + data[index].branchcode;
                        } else {
                            name = data[index].name;
                            val = data[index].id;
                        }
                        html += "<option value='" + val + "'>" + name + '</option>';
                    });
                    $('#sel_branch').html(html);
                    $("#sel_branch option").attr('selected', 'selected');
                    $('#sel_branch').multiselect('rebuild');
                    //////
                    var h = [];
                    var b = [];
                    if (typeof $("#sel_region option:selected").val() != 'undefined')
                        b = [0];
                    $.each($("#sel_branch option:selected"), function () {
                        var x = $(this).val();
                        if (x.indexOf('_') !== -1) {
                            x = x.split('_');
                            h.push(x[0]);
                        } else {
                            h.push(x);
                        }
                    });
                    //bỏ các phần tử dư
                    h = h.filter(function (value, index, h) {
                        return h.indexOf(value) == index;
                    });
                    $("#state_hidden").val(h.join(','));
                    //
                    $.each(data, function (index, item) {
                        if (data[index].branchcode !== null && typeof data[index].branchcode != 'undefined') {
                            b.push(data[index].branchcode);
                        }
                    });
                    //bỏ các phần tử dư
                    b = b.filter(function (value, index, b) {
                        return b.indexOf(value) == index;
                    });
                    $("#branchcode_hidden").val(b.join(','));
                },
            });
        }
    });
    $("#sel_region").multiselect('selectAll', false);
    $("#sel_region").multiselect('updateButtonText');

    //multiselect branch
    $('#sel_branch').multiselect({
        templates: {
            filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text" style="height:42px"></div></li>',
        },
        buttonContainer: '<span  />',
        includeSelectAllOption: true,
        selectAllText: 'Tất cả',
        selectAllValue: 0,
        enableFiltering: true,
        filterPlaceholder: 'Tìm ...',
        nonSelectedText: 'Chi nhánh',
        allSelectedText: 'Tất cả chi nhánh',
        nSelectedText: 'chọn lựa',
        buttonClass: 'btn btn-info',
        onDropdownShow: function (event) {
            var dropdown = $('.btnBranch ul.multiselect-container.dropdown-menu');
            var toggle = $('.multiselect.dropdown-toggle');
            dropdown.css({width: 250, position: 'absolute'});

            var dummy = $('#container_button_branch');
            dummy.show();
        },
        onDropdownHide: function (event) {
            var dummy = $('#container_button_branch');
            dummy.hide();
        },
        onChange: function (element, checked) {
            var k = [];
            var j = [];
            $.each($("#sel_branch option:selected"), function () {
                var x = $(this).val();
                if (x.indexOf('_') !== -1) {
                    x = x.split('_');
                    k.push(x[0]);
                    j.push(x[1]);
                } else {
                    k.push(x);
                }
            });
            //bỏ các phần tử dư
            k = k.filter(function (value, index, k) {
                return k.indexOf(value) == index;
            });
            j = j.filter(function (value, index, j) {
                return j.indexOf(value) == index;
            });
            //thêm phần tử mặc định
            if (j.length > 0) {
                j.push(0);
            }
            $("#state_hidden").val(k.join(','));
            $("#branchcode_hidden").val(j.join(','));
        }
    });
    $("#sel_branch").multiselect('selectAll', false);
    $("#sel_branch").multiselect('updateButtonText');

    $("#btnChooseRegion").click(function () {
        getDataToChartAfterChoose();
    });
    ///Click select Branch
    $("#btnChooseBranch").click(function () {
        getDataToChartAfterChoose();
    });

    function getDataToChartAfterChoose() {
        var from_date = $('#from_date_hidden').val();
        var to_date = $('#to_date_hidden').val();
        $.ajax({
            url: '<?php echo url('/' . $prefix  . '/csat-service/general') ?>',
            cache: false,
            type: "POST",
            dataType: "html",
            data: {'_token': $('input[name=_token]').val(), 'type': $("#type_hidden").val(), 'region': $('#reg_hidden').val(), 'from_date': from_date, 'to_date': to_date, 'branch': $('#state_hidden').val(), 'branchcode': $('#branchcode_hidden').val(), 'viewStatus': $('#viewStatusHidden').val()},
            beforeSend: function () {
                $('#spinner').show();
                $('#container_info_report').html('');
            },
            complete: function () {
                $('#spinner').hide();
            },
            success: function (data) {
                $('#container_info_report').html(data);
            },
        });
    }
    // Sự kiện thay đổi nội dung combobox
    $("#exportToExcel").click(function () {
        $.redirect('<?php echo url('/' . $prefix  . '/csat-service/exportGeneral') ?>', {_token: $('input[name=_token]').val(), type: $("#typeReport").val(), region: $('#reg_hidden').val(), from_date: $('#from_date_hidden').val(), to_date: $('#to_date_hidden').val(), branch: $('#state_hidden').val(), branchcode: $('#branchcode_hidden').val(), viewStatus: $('#viewStatusHidden').val()});
    });

    $("#viewLocationButton").click(function () {
        $("#viewStatusHidden").val('0');
        $("#viewLocation").show();
        $("#viewBranch").hide();
    });

    $("#viewBranchButton").click(function () {
        $("#viewStatusHidden").val('1');
        $("#viewLocation").hide();
        $("#viewBranch").show();
    });
});
</script>

@stop