@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
    $controller = 'history';
    $title = 'List Report';
    $transFile = $controller;
    $common = 'common';
    $prefix = main_prefix;
    $temp = $t = '';
    ?>
    <!--@include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])-->
    <!-- /.page-header -->

    <!-- PAGE CONTENT BEGINS -->
    <form id='formSubmit' class="form-horizontal" role="form" method="POST" action="{{url('/' . $prefix . '/' . $controller . '/index')}}">
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

                            <div id="div_type" class="col-xs-3" >
                                <label for="type">{{trans($transFile.'.PointOfContact')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="type" id="surveyType" class="search-select chosen-select">
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

                            <div id="div_surveyFrom_surveyTo" class="col-xs-3" >
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

                            <div id="div_CSATPointNet" class="col-xs-3" style="padding-top: 10px" >
                                <label for="CSATPointNet">{{trans($transFile.'.NetPoint')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="CSATPointNet[]" id="CSATPointNet" class="search-select chosen-select" multiple>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointNet']) && in_array($i, $searchCondition['CSATPointNet']))
                                            <option selected="selected" value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @else
                                            <option value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>

                            <div id="div_NetErrorType" class="col-xs-3" style="padding-top: 10px" >
                                <label for="NetErrorType">{{trans($transFile.'.NetErrorType')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="NetErrorType" id="NetErrorType" class="search-select chosen-select">
                                    <option value="0">{{trans($transFile.'.All')}}</option>
                                    @if (!empty($selErrorType))
                                        @foreach ($selErrorType as $val)
                                            @if ($val->answer_group == 20)
                                                @if (!empty($searchCondition['NetErrorType']) && $val->answer_id == $searchCondition['NetErrorType'])
                                                    <option selected="selected" value="{{$val->answer_id}}">{{trans('error.'.$val->answers_key)}}</option>
                                                @else
                                                    <option value="{{$val->answer_id}}">{{trans('error.'.$val->answers_key)}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div id="div_processingActionsInternet" class="col-xs-3  processingActionsInternet" style="padding-top: 10px; <?php
                            if (!empty($searchCondition['processingActionsInternet']) && (!empty($searchCondition['CSATPointNet'])) && (in_array("1", $searchCondition['CSATPointNet']) || in_array("2", $searchCondition['CSATPointNet'])))
                                echo "display:block";
                            else
                                echo "display:none";
                            ?>" >
                                <label for="processingActions">{{trans($transFile.'.Resolve')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="processingActionsInternet" id="processingActions" class="search-select chosen-select">
                                    <option value="0">{{trans($transFile.'.All')}}</option>
                                    @if (!empty($selProcessingActions))
                                        @foreach ($selProcessingActions as $val)
                                            @if (!empty($searchCondition['processingActionsInternet']) && $val->answer_id == $searchCondition['processingActionsInternet'])
                                                <option selected="selected" value="{{$val->answer_id}}">{{trans('action.'.$val->answers_key)}}</option>
                                            @else
                                                <option value="{{$val->answer_id}}">{{trans('action.'.$val->answers_key)}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div id="div_NPSPoint" class="col-xs-3" style="padding-top: 10px" >
                                <label for="NPSPoint">{{trans($transFile.'.NPSPoint')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="NPSPoint[]" id="NPSPoint" class="search-select chosen-select" multiple>
                                    @for($i = 0; $i <= 10; $i++)
                                        @if(!empty($searchCondition['NPSPoint']) && in_array($i, $searchCondition['NPSPoint']))
                                            <option selected="selected" value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @else
                                            <option value="{{$i}}">{{trans($transFile.'.Point').' '.$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>

                            <div id="div_RateNPS" class="col-xs-3" style="padding-top: 10px" >
                                <label for="RateNPS">{{trans($transFile.'.OpinionOfCustomer')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="RateNPS[]" id="RateNPS" class="search-select chosen-select" multiple>
                                    @if (!empty($selNPSImprovement))
                                        @foreach ($selNPSImprovement as $val)
                                            @if (!empty($searchCondition['RateNPS']) && in_array($val->answer_id, $searchCondition['RateNPS']))
                                                <option selected="selected" value="{{$val->answer_id}}">{{trans('answer.'.$val->answers_key)}}</option>
                                            @else
                                                <option value="{{$val->answer_id}}">{{trans('answer.'.$val->answers_key)}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div id="div_contractNum" class="col-xs-3" style="padding-top: 10px" >
                                <label for="contractNum">{{trans($transFile.'.Contract')}}</label>
                                <input type="text" name="contractNum" class="form-control" id="contractNum" maxlength="200" value="{{isset($searchCondition['contractNum']) ?$searchCondition['contractNum'] :''}}">
                            </div>

                            <div id="div_surveyStatus" class="col-xs-3" style="padding-top: 10px">
                                <label for="surveyStatus">{{trans($transFile.'.ContactResult')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="surveyStatus[]" id='surveyStatus' class="search-select chosen-select" multiple>
                                    @foreach($listConnectedAvailable as $id => $key)
                                        <option value="{{$id}}" @if(!empty($searchCondition['section_connected']) && in_array($id,$searchCondition['section_connected'])) selected @endif>{{trans($transFile.'.'.$key)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="div_userSurvey" class="col-xs-3" style="padding-top: 10px" >
                                <label for="userSurvey">{{trans($transFile.'.SurveyUser')}}</label>
                                <input type="text" name="userSurvey" class="form-control" id="userSurvey" maxlength="200" value="{{isset($searchCondition['userSurvey']) ?$searchCondition['userSurvey'] :''}}">
                            </div>

                            <div id="div_processingSurvey" class="col-xs-3" style="padding-top: 10px" >
                                <label for="processingSurvey">{{trans($transFile.'.Resolve')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="processingSurvey[]" id='processingSurvey' class="search-select chosen-select" multiple>
                                    @foreach($listActionAvailable as $id => $key)
                                        <option value="{{$id}}" @if(!empty($searchCondition['processingSurvey']) && in_array($id,$searchCondition['processingSurvey'])) selected @endif>{{trans($transFile.'.'.$key)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="div_reportedStatus" class="col-xs-3" style="padding-top: 10px; display: none;">
                                <label for="reportedStatus">{{trans($transFile.'.ReportedStatus')}}</label>
                                <select data-placeholder="{{trans($transFile.'.All')}}" name="reportedStatus" id="reportedStatus" class="search-select chosen-select">
                                    @foreach($listReportActionAvailable as $id => $key)
                                        <option value="{{$id}}" @if(!empty($searchCondition['reportedStatus']) && $id == $searchCondition['reportedStatus']) selected @endif>{{trans($transFile.'.'.$key)}}</option>
                                    @endforeach
                                </select>
                            </div>
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

                <div>
                    @if (!empty($modelSurveySections) && count($modelSurveySections) > 0)
                        <div class="row">
                            <div class="col-xs-6" style='color: #307ecc;font-weight: bold; font-size: 20px; margin: 20px 0;'><div>{{trans($transFile.'.Total').': '.$modelSurveySections->total()}}</div></div>
                            <div class="col-xs-6"><div class="pull-right">{{$modelSurveySections->links()}}</div></div>
                        </div>
                    @endif
                    <?php $i = 1 + 50 * $currentPage; ?>
                    <div class="col-xs-12">
                        <div class="wrapper1" style="height: 20px;">
                            <div class="div1"></div>
                        </div>
                        <div class="wrapper2">
                            <div class="table-responsive div2">
                                <table id="tableInfoSurvey" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr style="color: #0B6CBC;">
                                        <th>{{trans($transFile.'.Number')}}</th>
                                        @foreach ($columnView as $key => $val)
                                            @if ($key != 'section_id')
                                                <th>{{trans($transFile.'.'.$val)}}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($dataPage as $data)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            @foreach ($data as $key => $val)
                                                @if ($key != 'section_id')
                                                    <td><?php echo $val; ?></td>
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
        <!-- PAGE CONTENT ENDS -->
    </form>
    <!-- PAGE CONTENT ENDS -->
</div><!-- /.page-content -->

<div id="modal-table" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-padding">
                <div class="table-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span class="white">&times;</span>
                    </button>
                    {{trans($transFile.'.DetailSurvey')}}
                </div>
            </div>

            <div class="modal-body">

            </div>

            <div class="modal-footer no-margin-top">
                <button class="btn btn-sm btn-danger pull-left" data-dismiss="modal">
                    <i class="icon-remove"></i>
                    {{trans('common.Close')}}
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<form id="formViolations" class="form-horizontal" role="form" method="post">
    <div id="modal-table-violation" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header no-padding">
                    <div class="table-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <span class="white">&times;</span>
                        </button>
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
                    {{trans('common.Close')}}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="modal-table-export-excel" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-padding">
                <div class="table-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span class="white">&times;</span>
                    </button>
                    Danh sách các file excel
                </div>
            </div>

            <div class="modal-body" id="modal-table-export-excel-body">
            </div>

            <div class="modal-footer no-margin-top">
                <button class="btn btn-sm btn-danger pull-left" data-dismiss="modal">
                    <i class="icon-remove"></i>
                    {{trans('common.Close')}}
                </button>
            </div>
        </div>
    </div>
</div>

<link type="text/css" href="{{asset('assets/css/datetimepicker.css')}}" rel="stylesheet" media="screen">
<link rel="stylesheet" href="{{asset('assets/css/chosen.css')}}" type="text/css">
<style type="text/css">
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
    $(document).ready(function () {
        init();
        //
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
        $('.chosen-single').css('height', '34px');

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
            changeConfirmChannel($('#surveyType').val());
        });
        $('#btnSubmit').click(function () {
            $('#formSubmit').attr('action', '{{url('/' . $prefix . '/' . $controller . '/index')}}');
            $('#formSubmit').submit();
        });

        $('.speaker').click(function(){
            $(this).css('color', 'red');
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

    function clickSubmit() {
        <?php Session::put('click', 1); ?>
        $('#formSubmit').submit();
    }

    function clickAdvanceSearch() {
        $('#advanceSearch').toggle();
    }

    function open_tooltip(id) {
        $.ajax({
            url: '{{url('/' . $prefix . '/' . $controller . '/detail_survey')}}',
            cache: false,
            type: "POST",
            dataType: "html",
            data: {'_token': $('input[name=_token]').val(), 'survey': id},
            success: function (data) {
                $('.modal-body').html(data);
            },
            error: function () {
                $('#modal-table-export-excel-body').html('{{trans('error.SystemError')}}');
            }
        });
    }

    function exportExcel() {
        var a = '<div class="center" id="spinner"><img src="{{asset("assets/img/bluespinner.gif")}}" /></div>';
        $('#modal-table-export-excel-body').html(a);
        $('#modal-table-export-excel').modal().show();
        $.ajax({
            url: '<?php echo url('history/export') ?>',
            cache: false,
            type: "POST",
            dataType: "JSON",
            data: {'_token': $('input[name=_token]').val()},
            success: function (data) {
                if (data.state === 'fail') {
                    $('#modal-table-export-excel-body').html(data.error);
                    return;
                }

                var a = data.detail;
                $('#modal-table-export-excel-body').html(a);
            },
            error: function () {
                $('#modal-table-export-excel-body').html('{{trans('error.SystemError')}}');
            }
        });
    }

    function checkVoiceRecord(id) {
        var a = '<div class="center" id="spinner"><img src="{{asset("assets/img/bluespinner.gif")}}" /></div>';
        $('#modal-table-record-body').html(a);
        $('#modal-table-record').modal().show();
        $.ajax({
            url: '<?php echo url('get-voice-records-ajax') ?>',
            cache: false,
            type: "POST",
            dataType: "JSON",
            data: {'_token': $('input[name=_token]').val(), 'sectionID': id},
            success: function (data) {
                if (data.state === 'fail') {
                    $('#modal-table-record-body').html(data.error);
                    return;
                }

                var a = data.detail;
                $('#modal-table-record-body').html(a);
            },
            error: function () {
                $('#modal-table-record-body').html('{{trans('error.SystemError')}}');
            }
        });
    }

    function stopAll() {
        var a = '';
        $('#modal-table-record-body').html(a);
    }

    function init() {
        var id = $('#departmentType').val();
        checkTypeSurvey(id);

        changeReportStatus();

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

    function changeConfirmChannel(idContactPoint) {
        showHideInput(idContactPoint);
    }

    function checkConfirmChannel(idContactPoint) {
        $('#channelConfirm option').attr('disabled', true);
        switch(idContactPoint){
            default:
                $('#channelConfirm option').attr('disabled', false);
        }
        $('#channelConfirm').trigger("chosen:updated");
        showHideInput(idContactPoint);
    }

    function checkTypeSurvey(idDepartment){
        $('#surveyType option').attr('disabled', true);
        switch(idDepartment){
            case '1':
                $("#surveyType").val('1');
                $('#surveyType option[value="1"]').removeAttr('disabled');
                break;
            default:
                $('#surveyType option').attr('disabled', false);
                $("#surveyType").val('1');
                @if (!empty($searchCondition['type']) && $searchCondition['type'] != 3)
                    var tempSurveyType = '{{$searchCondition['type']}}';
                    $("#surveyType").val(tempSurveyType);
                @endif
        }
        $('#surveyType').trigger('chosen:updated');

        var idSurvey = $('#surveyType').val();
        checkConfirmChannel(idSurvey);
    }

    function showHideInput(typeSurvey) {
        if (typeSurvey == 1) {
            $('#div_CSATPointSale').show();
            $('#div_saleName').show();

            $('#div_CSATPointNVTK').show();
            $('#div_CSATPointBT').hide();
            $('#div_technicalStaff').show();

            $('#div_CSATPointNet').show();
            $('#div_NetErrorType').show();

            $('#div_NPSPoint').show();
            $('#div_RateNPS').show();

            $('#div_contractNum').show();
            $('#div_surveyStatus').show();
            $('#div_userSurvey').show();
            $('#div_processingSurvey').show();

            //set lại giá trị mặc định nul
            $('#CSATPointBT').val('').trigger('chosen:updated');
        } else {
            $('#div_CSATPointSale').hide();
            $('#div_saleName').hide();

            $('#div_CSATPointNVTK').hide();
            $('#div_CSATPointBT').show();
            $('#div_technicalStaff').show();

            $('#div_CSATPointNet').show();
            $('#div_NetErrorType').show();
            $('#div_CSATPointTV').show();
            $('#div_TVErrorType').show();

            $('#div_NPSPoint').show();
            $('#div_RateNPS').show();

            $('#div_contractNum').show();
            $('#div_surveyStatus').show();
            $('#div_userSurvey').show();
            $('#div_processingSurvey').show();

            //set lại giá trị mặc định null
            $('#CSATPointSale').val('').trigger('chosen:updated');
            $('#salerName').val('');

            $('#CSATPointNVTK').val('').trigger('chosen:updated');
        }
    }

    function open_violation(status, id, type) {
        $.ajax({
            url: '{{url('/' . $prefix . '/' . $controller . '/detail_violations')}}',
            cache: false,
            type: "POST",
            dataType: "html",
            data: {'_token': $('input[name=_token]').val(), 'id': id, 'status': status, 'type': type},
            success: function (data) {
                $('.modal-body-violations').html(data);
                if (status === 1) {
                    $('#headerViolation').html('{{trans('violations.CSATHandlingReport')}}');
                    $('#btnSave').html('{{trans('violations.Done')}}');
                } else {
                    $('#headerViolation').html('{{trans('violations.EditCSATHandlingReport')}}');
                    $('#btnSave').html('{{trans('violations.Edit')}}');
                }
            },
        });
    }

    $("#CSATPointNet").change(function () {
        var value = $("#CSATPointNet").val();
                //Có chọn
        if (value != null) {
            if (value.indexOf("1") != - 1 || value.indexOf("2") != - 1){
                $(".processingActionsInternet").css("display", "block");
            }
            else{
                $(".processingActionsInternet").css("display", "none");
            }
        } else{
            $(".processingActionsInternet").css("display", "none");
        }
    });

    $("#CSATPointSale").change(function () {
        changeReportStatus();
    });

    $("#CSATPointNVTK").change(function () {
        changeReportStatus();
    });

    $("#CSATPointBT").change(function () {
        changeReportStatus();
    });

    $("#CSATPointTransaction").change(function () {
        changeReportStatus();
    });

    $("#CSATPointChargeAtHomeStaff").change(function () {
        changeReportStatus();
    });

    function changeReportStatus(){
        var value1 = $("#CSATPointSale").val();
        var value2 = $("#CSATPointNVTK").val();
        var value3 = $("#CSATPointBT").val();

        var show = false;
        if (value1 != null) {
            if (value1.indexOf("1") != - 1 || value1.indexOf("2") != - 1){
                show = true;
            }
        }
        if (value2 != null) {
            if (value2.indexOf("1") != - 1 || value2.indexOf("2") != - 1){
                show = true;
            }
        }
        if (value3 != null) {
            if (value3.indexOf("1") != - 1 || value3.indexOf("2") != - 1){
                show = true;
            }
        }

        if(show === true){
            $("#div_reportedStatus").show();
        }else{
            $("#div_reportedStatus").hide();
        }
    }

    @if (!empty($searchCondition['CSATPointNet']) && (in_array(1, $searchCondition['CSATPointNet']) || in_array(2, $searchCondition['CSATPointNet'])))
        $(".processingActionsInternet").css("display", "block");
    @endif
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