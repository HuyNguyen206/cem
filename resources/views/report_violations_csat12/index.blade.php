@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
    $controller = 'reportviolationscsat12';
    $title = 'List Report';
    $transfile = $controller;
    $common = 'common';
    $prefix = main_prefix;
    $temp = $t = '';

    //Thông tin quyền hạn user
    $userRole = Session::get('userRole');
    ?>
    <!--@include('layouts.pageheader', ['controller' => $controller, 'title' => $title, 'transfile' => $transfile])-->
    <!-- /.page-header -->

    <!-- PAGE CONTENT BEGINS -->
    <form id='formsubmit' class="form-horizontal" role="form" method="POST" action="<?php echo url('/' . $prefix . '/' . $controller . '/index') ?>">
        {!! csrf_field() !!}
        <div class="">
            <div class="col-xs-12" style="overflow: hidden;">
                <div class="row" style="overflow: hidden;">
                    <div class="row" id='advance_search'>
                        <div class="col-xs-12">

                            <div class="space-4"></div>
                            <div class="row">
                                <div class="col-xs-3" >
                                    <label for="surveyType">Loại khảo sát</label>
                                    <select data-placeholder="Tất cả" name="surveyType" id="surveyType" class="search-select chosen-select">
                                        <option value="1" @if($searchCondition['type'] == 1) selected @endif>Sau Triển khai DirectSale</option>
                                        <option value="6" @if($searchCondition['type'] == 6) selected @endif>Sau Triển khai Telesale</option>
                                        <option value="2" @if($searchCondition['type'] == 2) selected @endif>Bảo trì</option>
                                        <option value="3" @if($searchCondition['type'] == 3) selected @endif>Thu cước</option>
                                    </select>
                                </div>
                                <div class="col-xs-3" >
                                    <label for="staffType">Loại nhân viên</label>
                                    <select name="staffType" id="staffType" class="chosen-select">
                                        <option value="0" @if($searchCondition['staffType'] == 0) selected @endif>Nhân viên kinh doanh</option>
                                        <option value="1" @if($searchCondition['staffType'] == 1) selected @endif>Nhân viên kĩ thuật</option>
                                    </select>
                                </div>
                                <div class="col-xs-3" >
                                    <label for="region">Vùng</label>
                                    <select data-placeholder="Tất cả" name="region[]" id='region_sel' class="search-select chosen-select" multiple>
                                        @for($i = 1; $i <= 7; $i++)
                                        @if(!empty($userGranted['region']) && in_array($i, $userGranted['region']))
                                        @if(!empty($searchCondition['region']) && in_array($i, $searchCondition['region']) || in_array($i, $userGranted['region']) && count($userGranted['region']) == 1)//nếu nhiều hơn 1 vùng thì ko show
                                        <option selected="selected" value="{{$i}}">Vùng {{$i}}</option>
                                        @else
                                        <option value="{{$i}}">Vùng {{$i}}</option>
                                        @endif
                                        @endif
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-xs-3">
                                    <label for="location">Chi nhánh</label>
                                    <select data-placeholder="Tất cả" name="location[]" id='location_sel' class="search-select chosen-select" multiple>
                                        <?php
                                        if (!empty($modelLocation)) {
                                            foreach ($modelLocation as $location) {
                                                if (!empty($userGranted['region']) && !empty($userGranted['location']) && (in_array($location->id, $userGranted['location']) || in_array($location->branch_id, $userGranted['branchID']))) {
                                                    $t = substr($location->region, -1);
                                                    $val = $location->id;
                                                    $name = $location->name;
                                                    if (!empty($location->branchcode)) {
                                                        $val = $location->id . '_' . $location->branchcode;
                                                        $name = str_replace(' - ', $location->branchcode . '-', $location->name);
                                                    }
                                                    if ($location->region != $temp) {
                                                        ?>
                                                        <optgroup class="region{{$t}}" label="{{$location->region}}" />
                                                            <?php
                                                        }
                                                        if (in_array($location->id, [4, 8])) {
                                                            if (!empty($searchCondition['location']) && (in_array($location->branchcode, $searchCondition['branchcode']) && in_array($location->id, $searchCondition['location'])) || in_array($location->branch_id, $userGranted['branchID']) && (count($userGranted['branchID']) + count($userGranted['location'])) == 1) {//nếu nhiều hơn 1 chi nhánh thì ko show
                                                                ?>
                                                                <option selected="selected" value="{{$val}}">{{$name}}</option>
                                                            <?php } else { ?>
                                                                <option value="{{$val}}">{{$name}}</option>
                                                                <?php
                                                            }
                                                        } else {
                                                            if (!empty($searchCondition['location']) && in_array($location->id, $searchCondition['location']) || in_array($location->id, $userGranted['location']) && (count($userGranted['branchID']) + count($userGranted['location'])) == 1) {//nếu nhiều hơn 1 chi nhánh thì ko show
                                                                ?>
                                                                <option selected="selected" value="{{$location->id}}">{{$location->name}}</option>
                                                            <?php } else { ?>
                                                                <option value="{{$location->id}}">{{$location->name}}</option>
                                                                <?php
                                                            }
                                                        }
                                                        $temp = $location->region;
                                                    }
                                                }
                                            }
                                            ?>
                                    </select>
                                </div>
                            </div>

                            <div class="space-4"></div>

                            <div class="row">
                                <div class="col-xs-3" >
                                    <div class="col-xs-6 no-padding" >
                                        <label for="survey_from">Từ ngày khảo sát</label>
                                        <div class="inner-addon right-addon">
                                            <i class="glyphicon glyphicon-calendar red"></i>
                                            <input type="text" name="survey_from" id="surveyFrom"  value="{{!empty($searchCondition['survey_from']) ?date('d-m-Y',strtotime($searchCondition['survey_from'])) :date('d-m-Y')}}" class="form-control"> 
                                        </div>
                                    </div>
                                    <div class="col-xs-6 no-padding-right" >
                                        <label for="survey_to">Đến ngày khảo sát</label>
                                        <div class="inner-addon right-addon">
                                            <i class="glyphicon glyphicon-calendar red"></i>
                                            <input type="text" name="survey_to" value="{{!empty($searchCondition['survey_to']) ?date('d-m-Y',strtotime($searchCondition['survey_to'])) :date('d-m-Y')}}" id="surveyTo" class="form-control"> 
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 10px;">
                                <div class="col-xs-12 center" >
                                    <button class="btn btn-success" id="btnSubmit" type='submit' onclick="clicksubmit()"><i class="icon-search bigger-110"></i>Tìm</button>
                                    <?php if (!empty($modelSurveySections) && count($modelSurveySections) > 0) { ?>
                                        <button class="btn btn-info" id="btnExport" type='submit'><i class="icon-search bigger-110"></i>Xuất Excel</button>
                                    <?php } ?>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <div class="space-4"></div>
                    <?php if (!empty($modelSurveySections) && count($modelSurveySections) > 0) { ?>
                        <div class="row">
                            <div class="col-xs-6" style='color: #307ecc;font-weight: bold; font-size: 20px; margin: 20px 0;'><div>Tổng số dòng: {{$modelSurveySections->total()}}</div></div>
                            <div class="col-xs-6"><div class="pull-right">{{$modelSurveySections->links()}}</div></div>
                        </div>
                    <?php } ?>
                    <div class="col-xs-12" style="overflow: hidden;">
                        <div class="wrapper1" style="height: 20px;">
                            <div class="div1"></div>
                        </div>
                        <div class="wrapper2">
                            <div class="table-responsive div2">
                                <table id="tableInfoSurvey" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <?php foreach($columnView as $key => $val){ ?>
                                                <?php if($key != 'section_id') {?>
                                                    <th>{{$val}}</th>
                                                <?php } ?>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($dataPage as $stt => $data){ ?>
                                        <tr>
                                            <td class="hidden-480">{{$stt + 1}}</td>
                                            <?php foreach($data as $key => $val){
                                                echo '<td class="hidden-480">'.$val.'</td>';
                                            }?>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                <?php if (!empty($modelSurveySections) && count($modelSurveySections) > 0) { ?>
                                    <tfoot>
                                        <tr><td colspan="{{count($columnView)}}">
                                                <span class="pull-left" style='color: #307ecc;font-weight: bold; font-size: 20px; margin: 20px 0;'>Tổng số dòng: {{$modelSurveySections->total()}}</span>
                                                <span class="pull-right">{{$modelSurveySections->links()}}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<div id="modal-table" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-padding">
                <div class="table-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span class="white">&times;</span>
                    </button>
                    Chi tiết khảo sát
                </div>
            </div>

            <div class="modal-body">

            </div>

            <div class="modal-footer no-margin-top">
                <button class="btn btn-sm btn-danger pull-left" data-dismiss="modal">
                    <i class="icon-remove"></i>
                    Close
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- PAGE CONTENT ENDS -->
</form>
<form id="formViolations" class="form-horizontal" role="form" method="post">
    <div id="modal-table-violation" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header no-padding">
                    <div class="table-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <span class="white">&times;</span>
                        </button>
                        <a href="{{url('7.0.5qtin10-MTQT Tiep nhan va xu ly cac diem CSAT 1,2 cua nhan vien.docx')}}" class="pull-right" style="padding-right: 10px; text-decoration: none"><span style="color: white">Hướng dẫn xử lý CSAT</span></a>
                        <span id='headerViolation'></span>
                    </div>
                </div>

                <div class="modal-body-violations">

                </div>

                <div class="modal-footer no-margin-top">
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- PAGE CONTENT ENDS -->
</form>
<!-- PAGE CONTENT ENDS -->
</div><!-- /.page-content -->


<div id="modal-table-record" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-padding">
                <div class="table-header">
                    <button type="button" class="close" data-dismiss="modal" onclick="stopAll()" aria-hidden="true">
                        <span class="white">&times;</span>
                    </button>
                    Chi tiết các cuộc gọi
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
<div id="modal-table2" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-padding">
                <div class="table-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span class="white">&times;</span>
                    </button>
                    Lịch sử thời gian khảo sát
                </div>
            </div>

            <div class="modal-body2" style="text-align: center">
                <div class="content">

                </div>
                <img class="loading" src="{{asset("assets/img/bluespinner.gif")}}">
            </div>

            <div class="modal-footer no-margin-top">
                <button class="btn btn-sm btn-danger pull-left" data-dismiss="modal">
                    <i class="icon-remove"></i>
                    Close
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- PAGE CONTENT ENDS -->


<link type="text/css" href="{{asset('assets/css/datetimepicker.css')}}" rel="stylesheet" media="screen">
<link rel="stylesheet" href="{{asset('assets/css/chosen.css')}}" type="text/css">
<style type="text/css">
    .morecontent span {
        display: none;
    }
    .morelink {
        display: block;
    }
    .height-32{
        height: 32px !important;
    }
    /* enable absolute positioning */
    .inner-addon {
        position: relative;
    }

    /* style glyph */
    .inner-addon .glyphicon {
        position: absolute;
        padding: 10px;
        pointer-events: none;
    }

    /* align glyph */
    .left-addon .glyphicon  { left:  0px;}
    .right-addon .glyphicon { right: 0px;}

    /* add padding  */
    .left-addon input  { padding-left:  30px; }
    .right-addon input { padding-right: 30px; }

    .wrapper1, .wrapper2 { width: 100%; overflow-x: scroll; overflow-y: hidden; }
    .wrapper1 { height: 20px; }
    .div1 { height: 20px; }
    .div2 { overflow: inherit; }
</style>
<script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
<script src="{{asset('assets/js/jquery.shorten.1.0.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('assets/js/moment.min.js')}}"></script>
<script src="{{asset('assets/js/fnPagingInfo.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/chosen.jquery.js')}}"></script>
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/additional-methods.min.js')}}"></script>
<script type="text/javascript">
                    $(document).ready(function() {
                    init();
                    var oTable1 = $('#tableInfoSurvey').dataTable({
                    "aoColumns": [
                        <?php for($i = 0; $i <= count($columnView); $i++){
                            if($i == 0){
                                echo 'null';
                            }else{
                                echo ',null';
                            }
                        } ?>
                    ],
                            //"aaSorting": [[19, "desc"]],
                            "bInfo": false,
                            "bSort": false,
                            "bPaginate": false,
                            "bJQueryUI": false,
                            "oLanguage": {
                            "sLengthMenu": "Hiển thị _MENU_ dòng mỗi trang",
                                    "sZeroRecords": "Không tìm thấy",
                                    "sInfo": "Có _START_ tới _END_ của _TOTAL_ bản ghi",
                                    "sInfoEmpty": "Có 0 tới 0 của 0 bản ghi",
                                    "sInfoFiltered": "(lọc từ _MAX_ tổng số bản ghi)",
                                    "sSearch": "Tìm kiếm"
                            },
                            "bServerSide": false,
                            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                            var page = '<?php echo $currentPage; ?>';
                            var length = '<?php echo (Session::has('condition')) ? Session::get('condition')['recordPerPage'] : 15; ?>'; //this.fnPagingInfo().iLength;
                            var index = (page * length + (iDisplayIndex + 1));
                            $('td:eq(0)', nRow).html(index);
                            }
                    });
                    //
                    $('.more').shorten({
                    moreText: 'Xem thêm <i class=\"icon-plus\"></i>',
                            lessText: 'Rút gọn <i class=\"icon-minus\"></i>',
                            showChars: 300
                    });
                    $('#surveyFrom').datetimepicker({
                    format: 'dd-mm-yyyy',
                            autoclose: true,
                            minView: 2
                    });
                    $('#surveyTo').datetimepicker({
                    format: 'dd-mm-yyyy',
                            autoclose: true,
                            minView: 2
                    });
                    $('.chosen-select').chosen({
                    width: "100%",
                            no_results_text: "Không tìm thấy",
                    });
                    ///custom css chosen
                    $('.search-field').addClass('height-32');
                    $('.chosen-single').css('height', '34px');
                    ///
                    $('#region_sel').change(function(){
                    var id = $(this).val();
                    if (id != null) {
                    $('#location_sel optgroup').attr('disabled', true);
                    $.each(id, function(key, val) {
                    $('#location_sel .region' + val).removeAttr('disabled');
                    $('#location_sel').find('optgroup:first').hide();
                    });
                    } else{
                    $('#location_sel optgroup').removeAttr('disabled');
                    }
                    $('#location_sel').trigger('chosen:updated');
                    })

                    $('#btnSubmit').click(function(){
                    $('#formsubmit').attr('action', '<?php echo url('/' . $prefix . '/' . $controller . '/index') ?>');
                    $('#formsubmit').submit();
                    });
                    $('#btnExport').click(function(){
                    $('#formsubmit').attr('action', '<?php echo url('/' . $prefix . '/' . $controller . '/export') ?>');
                    $('#formsubmit').submit();
                    });
                    });
                    function clicksubmit(){
<?php Session::put('click', 1); ?>
                    $('#formsubmit').submit();
                    }
                    function open_tooltip(id, contract, phone){
                    $.ajax({
                    url: '<?php echo url('/' . $prefix . '/' . $controller . '/detail_survey') ?>',
                            cache: false,
                            type: "POST",
                            dataType: "html",
                            data: {'_token': $('input[name=_token]').val(), 'survey': id, 'contract': contract, 'phone': phone},
                            success: function (data) {
                            $('.modal-body').html(data);
                            },
                    });
                    }
                    function open_tooltip_statictic_time(id){
                    $('.content').html('');
                    $('#modal-table2').modal('show');
                    $('.loading').css('visibility', 'visible');
                    $.ajax({
                    url: '<?php echo url('/' . $prefix . '/' . $controller . '/get-time-survey') ?>',
                            cache: false,
                            type: "POST",
                            dataType: "html",
                            data: {'_token': $('input[name=_token]').val(), 'id': id},
                            success: function (data) {
                            $('.loading').css('visibility', 'hidden');
                            $('.content').html(data);
                            },
                    });
                    }


                    function checkVoiceRecord(id){
                    var a = '<div class="center" id="spinner"><img src="{{asset("assets/img/bluespinner.gif")}}" /></div>';
                    $('#modal-table-record-body').html(a);
                    $('#modal-table-record').modal().show();
                    $.ajax({
                    url: '<?php echo url('get-voice-records-ajax') ?>',
                            cache: false,
                            type: "POST",
                            dataType: "JSON",
                            data: {'_token': $('input[name=_token]').val(), 'id': id},
                            success: function (data) {
                            if (data.state === 'fail'){
                            $('#modal-table-record-body').html(data.error);
                            return;
                            }

                            var a = data.detail;
                            $('#modal-table-record-body').html(a);
                            },
                            error: function(){
                            $('#modal-table-record-body').html('Lỗi hệ thống');
                            }
                    });
                    }

                    function stopAll(){
                    var a = '';
                    $('#modal-table-record-body').html(a);
                    }

                    function init(){

                    //scroll    
                    $('.div1').width($('table').width());
                    $('.div2').width($('table').width());
                    $('.wrapper1').on('scroll', function (e) {
                    $('.wrapper2').scrollLeft($('.wrapper1').scrollLeft());
                    });
                    $('.wrapper2').on('scroll', function (e) {
                    $('.wrapper1').scrollLeft($('.wrapper2').scrollLeft());
                    });
                    }

                    function open_violation(status, id, type){
                    $.ajax({
                    url: '<?php echo url('/' . $prefix . '/' . $controller . '/detail_violations') ?>',
                            cache: false,
                            type: "POST",
                            dataType: "html",
                            data: {'_token': $('input[name=_token]').val(), 'id': id, 'status': status, 'type': type},
                            success: function (data) {
                            $('.modal-body-violations').html(data);
                            if (status === 1){
                            $('#headerViolation').html('Báo cáo xử lý CSAT');
                            $('#btnSave').html('Hoàn tất');
                            } else {
                            $('#headerViolation').html('Chỉnh sửa báo cáo xử lý CSAT');
                            $('#btnSave').html('Sửa');
                            }
                            },
                    });
                    }
                    $("#CSATPointInternet").change(function() {
                    var value = $("#CSATPointInternet").val();
                    //Có chọn
                    if (value != null)
                    {
                    if (value.indexOf("1") != - 1 || value.indexOf("2") != - 1)
                            $(".processingActionsInternet").css("display", "block");
                    else
                            $(".processingActionsInternet").css("display", "none");
                    }
                    else
                            $(".processingActionsInternet").css("display", "none");
                    });
                    $("#CSATPointTV").change(function() {
                    var value = $("#CSATPointTV").val();
                    //Có chọn
                    if (value != null)
                    {
                    if (value.indexOf("1") != - 1 || value.indexOf("2") != - 1)
                            $(".processingActionsTV").css("display", "block");
                    else
                            $(".processingActionsTV").css("display", "none");
                    }
                    else
                            $(".processingActionsTV").css("display", "none");
                    });
                    <?php 
                    if(!empty($searchCondition['CSATPointNet'])&&(in_array(1, $searchCondition['CSATPointNet'])||in_array(2, $searchCondition['CSATPointNet'])))
                    {
                     ?>
                                  $(".processingActionsInternet").css("display", "block");
                     <?php
                    }
                      if(!empty($searchCondition['CSATPointTV'])&&(in_array(1, $searchCondition['CSATPointTV'])||in_array(2, $searchCondition['CSATPointTV'])))
                    {
                    ?>
                           $(".processingActionsTV").css("display", "block");
                        <?php 
                    }
                    ?>
                    
</script>
<style>
    .answer{
        word-wrap: break-word !important;
    }
    .chosen-results{
        max-height: 175px !important;
    }
</style>
@stop