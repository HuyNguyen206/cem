@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
    $controller = 'csat-staff';
    $title = 'List Report';
    $transFile = $controller;
    $common = 'common';
    $prefix = main_prefix;
    $temp = '';
    ?>
    <!--@include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])-->
    <!-- /.page-header -->
    
    <!-- PAGE CONTENT BEGINS -->
    <form id='formsubmit' class="form-horizontal" role="form" method="POST" action="{{url('/' . $prefix . '/' . $controller . '/detail')}}">
        {!! csrf_field() !!}
        <div class="">
            <div class="col-xs-12" style="overflow: hidden;">
                
                <div class="space-4"></div>
				
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div id="div_departmentType" class="col-xs-3" >
                                <label for="departmentType">{{trans($transFile.'.Department')}}</label>
                                <select name="departmentType" id="departmentType" class="search-select chosen-select">
                                    @foreach($listDepartmentAvailable as $val)
                                        <option value="{{$val->id}}" @if($searchCondition['departmentType'] == $val->id) selected @endif>{{$val->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="div_surveyType" class="col-xs-3" >
                                <label for="surveyType">{{trans($transFile.'.PointOfContact')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="surveyType" id="surveyType" class="search-select chosen-select">
                                    @foreach($listSurveyAvailable as $val)
                                        <option value="{{$val->survey_id}}" @if($searchCondition['type'] == $val->survey_id) selected @endif>{{trans('pointOfContact.'.$val->survey_key)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="div_channelConfirm" class="col-xs-3">
                                <label for="channelConfirm">{{trans($transFile.'.ChannelConfirm')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="channelConfirm" id="channelConfirm" class="search-select chosen-select">
                                    @foreach($listRecordChannels as $val)
                                        <option value="{{$val->record_channel_id}}" @if($searchCondition['channelConfirm'] == $val->record_channel_id) selected @endif>{{trans($transFile.'.'.$val->record_channel_key)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="div_object" class="col-xs-3">
                                <label for="object">{{trans($transFile.'.Object')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="object" id="object" class="search-select chosen-select">
                                    <option value="1" @if($searchCondition['object'] == 1) selected @endif>{{trans($transFile.'.Sales')}}</option>
                                    <option value="2" @if($searchCondition['object'] == 2) selected @endif>{{trans($transFile.'.SIR')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="padding-top: 10px">
                            <div class="col-xs-12">
                                <button class="btn" id="btnAdvanceSearch" type='button' onclick="clickAdvanceSearch()"><i class="icon-list bigger-110"></i>{{trans($transFile.'.AdvanceSearch')}}</button>
                            </div>
                        </div>
                        <div class="row" id="advanceSearch" style="display: none;">
                            <div id="div_location" class="col-xs-3" style="padding-top: 10px" >
                                <label for="location">{{trans($transFile.'.Branch')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="location[]" id='location' class="search-select chosen-select" multiple>
                                    @if (!empty($modelLocation))
                                        @foreach ($modelLocation as $location)
                                            <?php
                                            $val = $location->id.'_0';
                                            $name = $location->name;
                                            ?>
                                            @if (!empty($userGranted['location']) && in_array($val, $userGranted['branchLocationCode']))
                                                @if (!empty($searchCondition['location']) && in_array($val, $searchCondition['location']))
                                                    <option selected="selected" value="{{$val}}">{{$name}}</option>
                                                @else
                                                    <option value="{{$val}}">{{$name}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div id="div_surveyFrom_surveyTo" class="col-xs-3" style="padding-top: 10px">
                                <div class="col-xs-6 no-padding" >
                                    <label for="surveyFrom">{{trans($transFile.'.FromDate')}}</label>
                                    <div class="inner-addon right-addon">
                                        <i class="glyphicon glyphicon-calendar red"></i>
                                        <input type="text" name="surveyFrom" id="surveyFrom"  value="{{!empty($searchCondition['surveyFrom']) ?date('d-m-Y',strtotime($searchCondition['surveyFrom'])) :date('d-m-Y')}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xs-6 no-padding-right" >
                                    <label for="surveyTo">{{trans($transFile.'.ToDate')}}</label>
                                    <div class="inner-addon right-addon">
                                        <i class="glyphicon glyphicon-calendar red"></i>
                                        <input type="text" name="surveyTo" id="surveyTo" value="{{!empty($searchCondition['surveyTo']) ?date('d-m-Y',strtotime($searchCondition['surveyTo'])) :date('d-m-Y')}}"  class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div id='div_CSATPointSale' class="col-xs-3" style="padding-top: 10px" >
                                <label for="CSATPointSale">{{trans($transFile.'.SalePoint')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="CSATPointSale[]" id="CSATPointSale" class="search-select chosen-select" multiple>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointSale']) && in_array($i, $searchCondition['CSATPointSale']))
                                            <option selected="selected" value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @else
                                            <option value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>

                            <div id="div_saleName" class="col-xs-3" style="padding-top: 10px" >
                                <label for="saleName">{{trans($transFile.'.Sale')}}</label>
                                <input type="text" name="saleName" class="form-control" id="saleName" maxlength="200" value="{{isset($searchCondition['saleName']) ?$searchCondition['saleName'] :''}}">
                            </div>

                            <div id="div_CSATPointBT" class="col-xs-3" hidden="" style="padding-top: 10px" >
                                <label for="CSATPointBT">{{trans($transFile.'.TechPoint')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="CSATPointBT[]" id="CSATPointBT" class="search-select chosen-select" multiple>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointBT']) && in_array($i, $searchCondition['CSATPointBT']))
                                            <option selected="selected" value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @else
                                            <option value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>

                            <div id="div_CSATPointNVTK" class="col-xs-3" style="padding-top: 10px" >
                                <label for="CSATPointNVTK">{{trans($transFile.'.TechPoint')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="CSATPointNVTK[]" id="CSATPointNVTK" class="search-select chosen-select" multiple>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointNVTK']) && in_array($i, $searchCondition['CSATPointNVTK']))
                                            <option selected="selected" value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @else
                                            <option value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>

                            <div id="div_technicalStaff" class="col-xs-3" style="padding-top: 10px" >
                                <label for="technicalStaff">{{trans($transFile.'.Tech')}}</label>
                                <input type="text" name="technicalStaff" class="form-control" id="technicalStaff" maxlength="200" value="{{isset($searchCondition['technicalStaff']) ?$searchCondition['technicalStaff'] :''}}">
                            </div>

                            <div id="div_contractNum" class="col-xs-3" style="padding-top: 10px" >
                                <label for="contractNum">{{trans($transFile.'.Contract')}}</label>
                                <input type="text" name="contractNum" class="form-control" id="contractNum" maxlength="200" value="{{isset($searchCondition['contractNum']) ?$searchCondition['contractNum'] :''}}">
                            </div>

                            <div id="div_processingSurvey" class="col-xs-3" style="padding-top: 10px" >
                                <label for="processingSurvey">{{trans($transFile.'.Resolve')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="processingSurvey[]" id='processingSurvey' class="search-select chosen-select" multiple>
                                    @foreach($listActionAvailable as $id => $key)
                                        <option value="{{$id}}" @if(!empty($searchCondition['processingSurvey']) && in_array($id,$searchCondition['processingSurvey'])) selected @endif>{{trans($transFile.'.'.$key)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="div_violationsType" class="col-xs-3" style="padding-top: 10px">
                                <label for="violationsType">{{trans($transFile.'.ViolationsType')}}</label>
                                <select data-placeholder="Tất cả" name="violationsType" id="violationsType" class="search-select chosen-select">
                                    <option value="0">Tất cả</option>
                                    <option value="1" @if($searchCondition['violations_type'] == 1) selected @endif>Sai hẹn với khách hàng</option>
                                    <option value="2" @if($searchCondition['violations_type'] == 2) selected @endif>Thái độ với khách hàng không tốt</option>
                                    <option value="3" @if($searchCondition['violations_type'] == 3) selected @endif>Không thực hiện các yêu cầu phát sinh của khách hàng</option>
                                    <option value="4" @if($searchCondition['violations_type'] == 4) selected @endif>Không hướng dẫn khách hàng</option>
                                    <option value="5" @if($searchCondition['violations_type'] == 5) selected @endif>Làm bừa, bẩn nhà khách hàng</option>
                                    <option value="6" @if($searchCondition['violations_type'] == 6) selected @endif>Nghiệp vụ kỹ thuật</option>
                                    <option value="7" @if($searchCondition['violations_type'] == 7) selected @endif>Tiến độ xử lý chậm</option>
                                    <option value="8" @if($searchCondition['violations_type'] == 8) selected @endif>Vòi vĩnh khách hàng</option>
                                    <option value="9" @if($searchCondition['violations_type'] == 9) selected @endif>Tư vấn không rõ ràng, đầy đủ</option>
                                    <option value="10" @if($searchCondition['violations_type'] == 10) selected @endif>Tư vấn sai</option>
                                    <option value="12" @if($searchCondition['violations_type'] == 12) selected @endif>Lỗi không thuộc về nhân viên</option>
                                    <option value="11" @if($searchCondition['violations_type'] == 11) selected @endif>Khác</option>
                                    <option value="13" @if($searchCondition['violations_type'] == 13) selected @endif>Thái độ nhân viên</option>
                                    <option value="14" @if($searchCondition['violations_type'] == 14) selected @endif>Thủ tục, chính sách</option>
                                    <option value="15" @if($searchCondition['violations_type'] == 15) selected @endif>Tốc độ giao dịch</option>
                                    <option value="16" @if($searchCondition['violations_type'] == 16) selected @endif>Không gian quầy giao dịch</option>
                                    <option value="17" @if($searchCondition['violations_type'] == 17) selected @endif>Lý do khác liên quan đến QGD</option>
                                    <option value="18" @if($searchCondition['violations_type'] == 18) selected @endif>Không phải lỗi từ QGD, lỗi từ bộ phận khác</option>
                                    <option value="19" @if($searchCondition['violations_type'] == 19) selected @endif>Thái độ của nhân viên thu cước</option>
                                    <option value="20" @if($searchCondition['violations_type'] == 20) selected @endif>Thao tác thu cước chậm chễ</option>
                                    <option value="21" @if($searchCondition['violations_type'] == 21) selected @endif>Hóa đơn/giấy tờ không đầy đủ</option>
                                    <option value="22" @if($searchCondition['violations_type'] == 22) selected @endif>Sai hẹn</option>
                                    <option value="23" @if($searchCondition['violations_type'] == 23) selected @endif>Nhầm mail (người nhận mail không phải chủ HĐ)</option>
                                    <option value="24" @if($searchCondition['violations_type'] == 24) selected @endif>Khách hàng chọn nhầm đánh giá</option>
                                    <option value="25" @if($searchCondition['violations_type'] == 25) selected @endif>Không phải lỗi từ NVTC, lỗi từ bộ phận khác</option>
                                </select>
                            </div>

                            <div id="div_punish" class="col-xs-3" style="padding-top: 10px">
                                <label for="punish">{{trans($transFile.'.Punishment')}}</label>
                                <select data-placeholder="Tất cả" name="punishment" id="punishment"class="search-select chosen-select">
                                    <option value="0">Tất cả</option>
                                    <option value="1" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 1) selected @endif>Phạt tiền</option>
                                    <option value="2" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 2) selected @endif>Cảnh cáo/nhắc nhở</option>
                                    <option value="3" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 3) selected @endif>Buộc thôi việc</option>
                                    <option value="4" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 4) selected @endif>Không chế tài bổ sung</option>
                                    <option value="5" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 5) selected @endif>Khác</option>
                                </select>
                            </div>

                            <div id="div_userReported" class="col-xs-3" style="padding-top: 10px" >
                                <label for="userReported">{{trans($transFile.'.UserReported')}}</label>
                                <input type="text" name="userReported" class="form-control" id="userReported" maxlength="200" value="{{isset($searchCondition['userReported']) ?$searchCondition['userReported'] :''}}">
                            </div>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="row">
                        <div class="col-xs-12 center" >
                            <button class="btn btn-success" id="btnSubmit" type='submit' onclick="clickSubmit()"><i class="icon-search bigger-110"></i>{{trans('common.Search')}}</button>
                            @if (!empty($modelSurveySections) && count($modelSurveySections) > 0)
                                <button class="btn btn-info" id="btnExport" onclick="exportExcel()" type="button"><i class="icon-file bigger-110"></i>{{trans('common.Excel')}}</button>
                            @endif
                        </div>
                    </div>

                    <div class="space-4"></div>
                    @if (!empty($modelSurveySections) && count($modelSurveySections) > 0)
                        <div class="row">
                            <div class="col-xs-6" style='color: #307ecc;font-weight: bold; font-size: 20px; margin: 20px 0;'><div>{{trans($transFile.'.Total').': '.$modelSurveySections->total()}}</div></div>
                            <div class="col-xs-6"><div class="pull-right">{{$modelSurveySections->links()}}</div></div>
                        </div>
                    @endif
                    <div class="col-xs-12" style="overflow: hidden;">
                        <div class="wrapper1" style="height: 20px;">
                            <div class="div1"></div>
                        </div>
                        <div class="wrapper2">
                            <div class="table-responsive div2">
                                <table id="tableInfoSurvey" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr style="color: #0B6CBC;">
                                            <th>STT</th>
                                            @foreach ($columnView as $key => $val)
                                                @if ($key != 'section_id')
                                                    <th>{{$val}}</th>
                                                @endif
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($dataPage as $data)
                                        <tr>
                                            <td class="hidden-480"></td>
                                            @foreach ($data as $key => $val)
                                                @if ($key != 'section_id')
                                                    <td class="hidden-480">
                                                        <?php echo $val; ?>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    @if (!empty($modelSurveySections) && count($modelSurveySections) >= 0)
                                        <tfoot>
                                        <tr><td colspan="{{count($columnView)}}" class="td-footer">
                                                <span class="pull-left" style='color: #307ecc;font-weight: bold; font-size: 20px; margin: 20px 0;'>{{trans($transFile.'.Total').': '.$modelSurveySections->total()}}</span>
                                                <span class="pull-right">{{$modelSurveySections->links()}}</span>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </form>
    <!-- PAGE CONTENT ENDS -->
</div><!-- /.page-content -->

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

    #table-div {
        max-width:100%;
        overflow-x:auto;
    }

    #tableInfoSurvey {
        background-color:transparent;
        min-width:100%;
    }

    #tableInfoSurvey thead,
    #tableInfoSurvey tbody {
        display:block;
    }

    #tableInfoSurvey thead > tr {
        width:calc(100% - 17px);
    }

    #tableInfoSurvey tbody {
        min-height: 50px;
        overflow-x:hidden;
    }

    #tableInfoSurvey tr {
        display:flex;
    }

    #tableInfoSurvey td,
    #tableInfoSurvey th {
        overflow:hidden;
        min-width: 150px;
        width: 100%;
    }
    #tableInfoSurvey td.td-footer{
        width: 100%;
    }

    .wrapper1, .wrapper2 { width: 100%; overflow-x: scroll; overflow-y: hidden; }
    .wrapper1 { height: 20px; }
    .div1 { height: 20px; }
    .div2 { overflow: visible; }
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
            no_results_text: "{{trans('common.NotFound')}}"
        });
        ///custom css chosen
        $('.search-field').addClass('height-32');
        $('.chosen-single').css('height','34px');
        ///
        $('#region').change(function () {
            var id = $(this).val();
            branchMapByRegion(id)
        })
        ///
        $('#departmentType').change(function () {
            checkTypeSurvey($(this).val());
        });
        ///
        $('#surveyType').change(function () {
            checkConfirmChannel($(this).val());
        });
        ///
        $('#channelConfirm').change(function () {
            checkObject($('#surveyType').val());
        });
        ///
        $('#object').change(function () {
            checkViolationsType($('#object').val());
        });

        $('#btnSubmit').click(function(){
            $('#formsubmit').attr('action', '{{url('/' . $prefix . '/' . $controller . '/detail')}}');
            $('#formsubmit').submit();
        });
        $('#btnExport').click(function(){
            $('#formsubmit').attr('action', '{{url('/' . $prefix . '/' . $controller . '/exportDetail')}}');
            $('#formsubmit').submit();
        });

        var intViewPortHeight = window.innerHeight;
        var initHeightTable = $("#tableInfoSurvey tbody").height();
        var stillHeight = intViewPortHeight - 600;
        if(initHeightTable > stillHeight){
            if(stillHeight > 200){
                $("#tableInfoSurvey tbody").height(stillHeight);
            }else{
                $("#tableInfoSurvey tbody").height(200);
            }
        }
    });

    function clicksubmit(){
        <?php Session::put('click',1); ?>
        $('#formsubmit').submit();
    }

    function clickAdvanceSearch() {
        $('#advanceSearch').toggle();
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

    function init(){
        var id = $('#departmentType').val();
        checkTypeSurvey(id);

        id = $('#region').val();
        branchMapByRegion(id);

        @if (!empty($searchCondition['region']) && (in_array('1', $searchCondition['region']) || in_array('5', $searchCondition['region'])))
            $("#div_brandcodeSaleMan").css("display", "block");
        @endif

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

    function branchMapByRegion(id) {
        if (id != null) {
            if (id.indexOf("1") != -1 || id.indexOf("5") != -1) {
                $("#div_brandcodeSaleMan").css("display", "block");
            } else{
                $("#div_brandcodeSaleMan").css("display", "none");
            }

            $('#location optgroup').attr('disabled', true);
            $.each(id, function (key, val) {
                $('#location .region' + val).removeAttr('disabled');
                $('#location').find('optgroup:first').hide();
            });

            $('#brandcodeSaleMan optgroup').attr('disabled', true);
            $.each(id, function (key, val) {
                $('#brandcodeSaleMan .region' + val).removeAttr('disabled');
                $('#brandcodeSaleMan').find('optgroup:first').hide();
            });
        } else {
            $("#div_brandcodeSaleMan").css("display", "none");
            $('#location optgroup').removeAttr('disabled');
            $('#brandcodeSaleMan optgroup').removeAttr('disabled');
        }

        $('#brandcodeSaleMan').trigger('chosen:updated');
        $('#location').trigger('chosen:updated');
    }

    function checkViolationsType(idObject){
        $('#violationsType option').attr('disabled', true);
        <?php
            $arrayNV = [0,1,2,3,4,5,6,7,8,9,10,11,12];
            $arrayDV = [0,13,14,15,16,17,18];
            $arrayNVCharge = [0,19,20,21,22,23,24,25];
        ?>

        switch(idObject){
            case '1':
            case '2':
                $("#violationsType").val('0');
                @if (!empty($searchCondition['violationsType']) && in_array($searchCondition['violationsType'], $arrayNV))
                    var tempViolationsType = '{{$searchCondition['violationsType']}}';
                    $("#violationsType").val(tempViolationsType);
                @endif
                @foreach($arrayNV as $val)
                    $('#violationsType option[value="{{$val}}"]').removeAttr('disabled');
                @endforeach
                break;
            case '3':
                $("#violationsType").val('0');
                @if (!empty($searchCondition['violationsType']) && in_array($searchCondition['violationsType'], $arrayDV))
                    var tempViolationsType = '{{$searchCondition['violationsType']}}';
                    $("#violationsType").val(tempViolationsType);
                @endif
                @foreach($arrayDV as $val)
                    $('#violationsType option[value="{{$val}}"]').removeAttr('disabled');
                @endforeach
                break;
            case '4':
                $("#violationsType").val('0');
                @if (!empty($searchCondition['violationsType']) && in_array($searchCondition['violationsType'], $arrayNVCharge))
                    var tempViolationsType = '{{$searchCondition['violationsType']}}';
                    $("#violationsType").val(tempViolationsType);
                @endif
                @foreach($arrayNVCharge as $val)
                    $('#violationsType option[value="{{$val}}"]').removeAttr('disabled');
                @endforeach
                        break;
            default:
                console.log('khac idObject');
        }
        $('#violationsType').trigger("chosen:updated");
    }

    function checkObject(idContactPoint){
        var idDepartment = $('#departmentType').val();
        var idChannel = $('#channelConfirm').val();
        $('#object option').attr('disabled', true);
        switch(idContactPoint){
            case '1':
            case '9':
                $("#object").val('1');
                $('#object option[value="1"]').removeAttr('disabled');
                if(idDepartment !== '1'){
                    @if (!empty($searchCondition['object']) && in_array($searchCondition['object'], [1,2]))
                        var tempObject = '{{$searchCondition['object']}}';
                        $("#object").val(tempObject);
                    @endif
                    $('#object option[value="2"]').removeAttr('disabled');
                }
                break;
            case '2':
            case '10':
                $("#object").val('2');
                $('#object option[value="2"]').removeAttr('disabled');
                break;
            case '3':
                $("#object").val('4');
                $('#object option[value="4"]').removeAttr('disabled');
                break;
            case '4':
                $("#object").val('3');
                $('#object option[value="3"]').removeAttr('disabled');
                if(idChannel == '6'){
                    @if (!empty($searchCondition['object']) && in_array($searchCondition['object'], [1,3]))
                        var tempObject = '{{$searchCondition['object']}}';
                        $("#object").val(tempObject);
                    @endif
                    $('#object option[value="1"]').removeAttr('disabled');
                }
                break;
            case '6':
                $("#object").val('1');
                $('#object option[value="1"]').removeAttr('disabled');
                break;
            default:
                console.log('khac idContactPoint');
        }
        $('#object').trigger("chosen:updated");
        checkViolationsType($('#object').val());
        showHideInput(idContactPoint);
    }

    function checkConfirmChannel(idContactPoint) {
        $('#channelConfirm option').attr('disabled', true);
        switch(idContactPoint){
            case '1':
            case '2':
            case '6':
            case '9':
            case '10':
                $("#channelConfirm").val('1');
                $('#channelConfirm option[value="1"]').removeAttr('disabled');
                break;
            case '3':
                $("#channelConfirm").val('2');
                @if (!empty($searchCondition['channelConfirm']) && in_array($searchCondition['channelConfirm'], [2,4]))
                    var tempChannelConfirm = '{{$searchCondition['channelConfirm']}}';
                    $("#channelConfirm").val(tempChannelConfirm);
                @endif
                $('#channelConfirm option[value="2"]').removeAttr('disabled');
                $('#channelConfirm option[value="4"]').removeAttr('disabled');
                break;
            case '4':
                $("#channelConfirm").val('2');
                @if (!empty($searchCondition['channelConfirm']) && in_array($searchCondition['channelConfirm'], [2,6]))
                    var tempChannelConfirm = '{{$searchCondition['channelConfirm']}}';
                    $("#channelConfirm").val(tempChannelConfirm);
                @endif
                $('#channelConfirm option[value="2"]').removeAttr('disabled');
                $('#channelConfirm option[value="6"]').removeAttr('disabled');
                break;
            case '11':
                $("#channelConfirm").val('7');
                $('#channelConfirm option[value="7"]').removeAttr('disabled');
                break;
            default:
                $('#channelConfirm option').attr('disabled', false);
        }
        $('#channelConfirm').trigger("chosen:updated");
        checkObject(idContactPoint);
    }

    function checkTypeSurvey(idDepartment){
        $('#surveyType option').attr('disabled', true);
        switch(idDepartment){
            case '1':
                $("#surveyType").val('1');
                $('#surveyType option[value="1"]').removeAttr('disabled');
                break;
            case '2':
            case '3':
                $("#surveyType").val('1');
                @if (!empty($searchCondition['type']) && in_array($searchCondition['type'], [1,2,6,9,10]))
                    var tempSurveyType = '{{$searchCondition['type']}}';
                    $("#surveyType").val(tempSurveyType);
                @endif
                $('#surveyType option[value="1"]').removeAttr('disabled');
                $('#surveyType option[value="2"]').removeAttr('disabled');
                $('#surveyType option[value="6"]').removeAttr('disabled');
                $('#surveyType option[value="9"]').removeAttr('disabled');
                $('#surveyType option[value="10"]').removeAttr('disabled');
                break;
            case '4':
                $("#surveyType").val('2');
                $('#surveyType option[value="2"]').removeAttr('disabled');
                break;
            case '5':
                $("#surveyType").val('1');
                @if (!empty($searchCondition['type']))
                    var tempSurveyType = '{{$searchCondition['type']}}';
                    $("#surveyType").val(tempSurveyType);
                @endif
                $('#surveyType option').attr('disabled', false);
                break;
            case '7':
                $("#surveyType").val('3');
                $('#surveyType option[value="3"]').removeAttr('disabled');
                break;
            case '8':
                $("#surveyType").val('6');
                $('#surveyType option[value="6"]').removeAttr('disabled');
                break;
            default:
                $('#surveyType option').attr('disabled', false);
                $("#surveyType").val('1');
                @if (!empty($searchCondition['type']) && $searchCondition['type'] != 3)
                    var tempSurveyType = '{{$searchCondition['type']}}';
                    $("#surveyType").val(tempSurveyType);
                @endif
                $('#surveyType option[value="3"]').attr('disabled', true);
        }
        $('#surveyType').trigger('chosen:updated');

        var idSurvey = $('#surveyType').val();
        checkConfirmChannel(idSurvey);
    }

    function showHideInput(typeSurvey) {
        var idChannel = $('#channelConfirm').val();
        if (typeSurvey == 1) {
            $('#div_CSATPointSale').show();
            $('#div_salerName').show();

            $('#div_CSATPointNVTK').show();
            $('#div_CSATPointBT').hide();
            $('#div_technicalStaff').show();

            $('#div_contractNum').show();
            $('#div_processingSurvey').show();
            $('#div_violationsType').show();
            $('#div_punish').show();
            $('#div_userReported').show();

            $('#div_CSATPointTransaction').hide();
            $('#div_CSATPointTransactionStaff').hide();
            $('#div_transactionStaffName').hide();

            $('#div_CSATPointChargeAtHomeStaff').hide();
            $('#div_chargeAtHomeStaffName').hide();

            //set lại giá trị mặc định nul
            $('#CSATPointBT').val('').trigger('chosen:updated');

            $('#CSATPointTransaction').val('').trigger('chosen:updated');
            $('#CSATPointTransactionStaff').val('').trigger('chosen:updated');
            $('#transactionStaffName').val('');

            $('#CSATPointChargeAtHomeStaff').val('').trigger('chosen:updated');
            $('#chargeAtHomeStaffName').val('');
        } else if (typeSurvey == 2) {
            $('#div_CSATPointSale').hide();
            $('#div_salerName').hide();

            $('#div_CSATPointNVTK').hide();
            $('#div_CSATPointBT').show();
            $('#div_technicalStaff').show();

            $('#div_contractNum').show();
            $('#div_processingSurvey').show();
            $('#div_violationsType').show();
            $('#div_punish').show();
            $('#div_userReported').show();

            $('#div_CSATPointTransaction').hide();
            $('#div_CSATPointTransactionStaff').hide();
            $('#div_transactionStaffName').hide();

            $('#div_CSATPointChargeAtHomeStaff').hide();
            $('#div_chargeAtHomeStaffName').hide();

            //set lại giá trị mặc định nul
            $('#CSATPointSale').val('').trigger('chosen:updated');
            $('#salerName').val('');

            $('#CSATPointNVTK').val('').trigger('chosen:updated');

            $('#CSATPointTransaction').val('').trigger('chosen:updated');
            $('#CSATPointTransactionStaff').val('').trigger('chosen:updated');
            $('#transactionStaffName').val('');

            $('#CSATPointChargeAtHomeStaff').val('').trigger('chosen:updated');
            $('#chargeAtHomeStaffName').val('');
        }else if (typeSurvey == 3 && idChannel == 2) {
            $('#div_CSATPointSale').hide();
            $('#div_salerName').hide();

            $('#div_CSATPointNVTK').hide();
            $('#div_CSATPointBT').hide();
            $('#div_technicalStaff').hide();

            $('#div_contractNum').show();
            $('#div_processingSurvey').show();
            $('#div_violationsType').show();
            $('#div_punish').show();
            $('#div_userReported').show();

            $('#div_CSATPointTransaction').hide();
            $('#div_CSATPointTransactionStaff').hide();
            $('#div_transactionStaffName').hide();

            $('#div_CSATPointChargeAtHomeStaff').show();
            $('#div_chargeAtHomeStaffName').show();

            //set lại giá trị mặc định null
            $('#CSATPointSale').val('').trigger('chosen:updated');
            $('#salerName').val('');

            $('#CSATPointNVTK').val('').trigger('chosen:updated');
            $('#CSATPointBT').val('').trigger('chosen:updated');
            $('#technicalStaff').val('');

            $('#CSATPointTransaction').val('').trigger('chosen:updated');
            $('#CSATPointTransactionStaff').val('').trigger('chosen:updated');
            $('#transactionStaffName').val('');

            $('#CSATPointChargeAtHomeStaff').val('').trigger('chosen:updated');
            $('#chargeAtHomeStaffName').val('');
        } else if (typeSurvey == 4) {
            $('#div_CSATPointSale').hide();
            $('#div_salerName').hide();

            $('#div_CSATPointNVTK').hide();
            $('#div_CSATPointBT').hide();
            $('#div_technicalStaff').hide();

            $('#div_contractNum').show();
            $('#div_processingSurvey').show();
            $('#div_violationsType').show();
            $('#div_punish').show();
            $('#div_userReported').show();

            $('#div_CSATPointTransaction').show();
            $('#div_CSATPointTransactionStaff').hide();
            $('#div_transactionStaffName').show();

            $('#div_CSATPointChargeAtHomeStaff').hide();
            $('#div_chargeAtHomeStaffName').hide();

            //set lại giá trị mặc định nul
            $('#CSATPointSale').val('').trigger('chosen:updated');
            $('#salerName').val('');

            $('#CSATPointNVTK').val('').trigger('chosen:updated');
            $('#CSATPointBT').val('').trigger('chosen:updated');
            $('#technicalStaff').val('');

            $('#CSATPointTransactionStaff').val('').trigger('chosen:updated');

            $('#CSATPointChargeAtHomeStaff').val('').trigger('chosen:updated');
            $('#chargeAtHomeStaffName').val('');
        } else if (typeSurvey == 6) {
            $('#div_CSATPointSale').show();
            $('#div_salerName').show();

            $('#div_CSATPointNVTK').hide();
            $('#div_CSATPointBT').hide();
            $('#div_technicalStaff').hide();

            $('#div_contractNum').show();
            $('#div_processingSurvey').show();
            $('#div_violationsType').show();
            $('#div_punish').show();
            $('#div_userReported').show();

            $('#div_CSATPointTransaction').hide();
            $('#div_CSATPointTransactionStaff').hide();
            $('#div_transactionStaffName').hide();

            $('#div_CSATPointChargeAtHomeStaff').hide();
            $('#div_chargeAtHomeStaffName').hide();

            //set lại giá trị mặc định nul
            $('#CSATPointNVTK').val('').trigger('chosen:updated');
            $('#CSATPointBT').val('').trigger('chosen:updated');
            $('#technicalStaff').val('');

            $('#CSATPointTransaction').val('').trigger('chosen:updated');
            $('#CSATPointTransactionStaff').val('').trigger('chosen:updated');
            $('#transactionStaffName').val('');

            $('#CSATPointChargeAtHomeStaff').val('').trigger('chosen:updated');
            $('#chargeAtHomeStaffName').val('');
        }else if (typeSurvey == 9) {
            $('#div_CSATPointSale').show();
            $('#div_salerName').show();

            $('#div_CSATPointNVTK').show();
            $('#div_CSATPointBT').hide();
            $('#div_technicalStaff').show();

            $('#div_contractNum').show();
            $('#div_processingSurvey').show();
            $('#div_violationsType').show();
            $('#div_punish').show();
            $('#div_userReported').show();

            $('#div_CSATPointTransaction').hide();
            $('#div_CSATPointTransactionStaff').hide();
            $('#div_transactionStaffName').hide();

            $('#div_CSATPointChargeAtHomeStaff').hide();
            $('#div_chargeAtHomeStaffName').hide();

            //set lại giá trị mặc định nul
            $('#CSATPointBT').val('').trigger('chosen:updated');

            $('#CSATPointTransaction').val('').trigger('chosen:updated');
            $('#CSATPointTransactionStaff').val('').trigger('chosen:updated');
            $('#transactionStaffName').val('');

            $('#CSATPointChargeAtHomeStaff').val('').trigger('chosen:updated');
            $('#chargeAtHomeStaffName').val('');
        } else if (typeSurvey == 10) {
            $('#div_CSATPointSale').show();
            $('#div_salerName').show();

            $('#div_CSATPointNVTK').show();
            $('#div_CSATPointBT').hide();
            $('#div_technicalStaff').show();

            $('#div_contractNum').show();
            $('#div_processingSurvey').show();
            $('#div_violationsType').show();
            $('#div_punish').show();
            $('#div_userReported').show();

            $('#div_CSATPointTransaction').hide();
            $('#div_CSATPointTransactionStaff').hide();
            $('#div_transactionStaffName').hide();

            $('#div_CSATPointChargeAtHomeStaff').hide();
            $('#div_chargeAtHomeStaffName').hide();

            //set lại giá trị mặc định nul
            $('#CSATPointBT').val('').trigger('chosen:updated');

            $('#CSATPointTransaction').val('').trigger('chosen:updated');
            $('#CSATPointTransactionStaff').val('').trigger('chosen:updated');
            $('#transactionStaffName').val('');

            $('#CSATPointChargeAtHomeStaff').val('').trigger('chosen:updated');
            $('#chargeAtHomeStaffName').val('');
        }
    }

    function open_violation(status, id, type){
        if(type === 1){
            $('#headerViolation').html('Báo cáo xử lý CSAT');
        } else {
            $('#headerViolation').html('Chỉnh sửa báo cáo xử lý CSAT');
        }

        $.ajax({
            url: '<?php echo url('/' . $prefix . '/' . $controller . '/detail_violations') ?>',
            cache: false,
            type: "POST",
            dataType: "html",
            data: {'_token': $('input[name=_token]').val(), 'id': id, 'status': status, 'type': type},
            success: function (data) {
                $('.modal-body-violations').html(data);
            },
        });
    }

    function clickSave(){
        $('#formViolations').attr('action', '<?php echo url('/' . $prefix . '/' . $controller . '/save-violation') ?>');
        var formData = $('#formViolations').serializeArray(); // data
        $.ajax({
            url: '<?php echo url('/' . $prefix . '/' . $controller . '/save-violation') ?>',
            cache: false,
            type: "POST",
            dataType: "json",
            data: {'_token': $('input[name=_token]').val(), 'data': formData},
            success: function (data) {
                $('#'+ data.object + data.id).html(data.resStatus);
            },
        });
    }
</script>
<style>
    .answer{
        word-wrap: break-word !important;
    }
</style>
@stop