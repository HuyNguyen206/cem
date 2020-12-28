@extends('layouts.app')

@section('content')
    <div class="page-content">
    <?php
    //            dump($modelSurveySections->isEmpty());die;
    $controller = 'csat-service';
    $title = 'List Report';
    $transFile = $controller;
    $common = 'common';
    $prefix = main_prefix;
    $temp = $t = '';
    //Thông tin quyền hạn user
    $userRole = Session::get('userRole');
    $typeSurvey = [
        1 => trans($transFile.'.AfterActive'),
        2 => trans($transFile.'.AfterChecklist'),
//        3 => 'Sau Thu cước tại nhà',
//        6 => 'Sau Triển khai TeleSale',
//        9 => 'Sau Triển khai Sale tại quầy',
//        10 => 'Sau Triển khai Swap'
    ];
    $errorNet = [
        85 => trans('report.InternetIsNotStable'),
        87 =>  trans('report.EquipmentError'),
        88 =>  trans('report.VoiceError'),
        89 =>  trans('report.WifiWeakNotStable'),
        90 =>  trans('report.GameLagging'),
        91 =>  trans('report.CannotUsingWifi'),
        92 =>  trans('report.LoosingSignal'),
        94 =>  trans('report.HaveSignalButCannotAccess'),
        97 =>  trans('report.SlowInternet'),
        98 =>  trans('report.SignalIsNotStableSignalLoosingIsUnderStandard'),
        99 =>  trans('report.IntenationalInternetSlow'),
        100 =>  trans('report.Other')
    ];
//    $errorTV = [
//        99 => 'Xé hình',
//        102 => 'Giật,Đứng hình , chập chờn',
//        103 => 'Có hình không có tiếng hoặc có tiếng không có hình tất cả các kênh',
//        105 => 'Không xem được các kênh truyền hình',
//        106 => 'Không sử dụng được thiết bị lưu trữ , mạng chia sẻ',
//        111 => 'Hình ảnh bị sọc ngang, sọc chéo',
//        112 => 'Lỗi kho ứng dụng',
//        121 => 'Lỗi kết nối HDBox &TV',
//        122 => 'Điều khiển , app điều khiển',
//        123 => 'Đấu nối thiết bị amply sử dụng KaraTV',
//        124 => 'Thiết bị Hdbox khởi động chậm',
//        125 => 'Không có hình , không có tiếng một vài kênh',
//        126 => 'Không xem được kho Phim',
//        127 => 'Khác'
//    ];
    $recordChannelArray = [
        1 => 'Happy Call',
        2 => 'Email-Web',
        3 => 'Hi FPT',
        4 => 'NVTC-MobiPay',
        5 => 'Giao dịch tại quầy',
        6 => 'Tablet'
    ];
    $generalAction = [
        '0' => trans($transFile.'.ActionResolveStaff'),
        '1' =>  trans($transFile.'.NotYetDoAnything'),
        '2' =>  trans($transFile.'.CreateChecklist'),
        '3' =>  trans($transFile.'.CreatePreChecklist'),
        '5' =>  trans($transFile.'.SendToDepartment'),
    ];
    $prechecklistStatus = [
        0 => trans($transFile.'.NotYetResolve'),
        2 => trans($transFile.'.Processing'),
        3 => trans($transFile.'.FinnishResolve'),
        99 => trans($transFile.'.CancelNotResolve')
    ];

    $actionNetTv = [
        115 =>trans('action.SaySorryToCustomerAndClosedCase'),
        117 => trans('action.CreatePreChecklist'),
        119 => trans('action.CreateChecklistOnSite'),
        118 => trans('action.CreateChecklist'),
        128 => trans('action.Other'),
        116 => trans('action.SendToDepartment'),
    ];
    $firstStatusPreCL = [
        5 => trans($transFile.'.RequireUpdateChecklist'),
        1 => trans($transFile.'.LoosingConnection'),
        6 => 'IPTV',
        7 => 'Wifi',
        2 => trans($transFile.'.SlowConnection'),
        3 => trans($transFile.'.InternetIsNotStable'),
        4 => trans($transFile.'.Other'),
        '' => ''
    ];
    $actionProcessPreCL = [
        1 => trans($transFile.'.ClosePreCLAndCreateCL'),
        2 => trans($transFile.'.ClosePreCLAndNotCreateCL'),
        3 => trans($transFile.'.PreCLIsProcessing'),
        4 => trans($transFile.'.PreCLNotResolve'),
        5 => trans($transFile.'.ClosePreCLWhenCreatePreCLWhichHasESCLIsProcessing'),
    ];
    $departmentPreCL = [
        4 => 'IBB',
        7 => 'CUS',
        43 => 'CS-HO',
        430 => 'CS-CN',
        39 => 'Telesales',
    ];
    ?>
    <!--@include('layouts.pageheader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])-->
        <!-- /.page-header -->

        <!-- PAGE CONTENT BEGINS -->
        <form id='formsubmit' class="form-horizontal" role="form" method="POST" action="<?php echo url('/' . $prefix . '/' . $controller . '/detail') ?>">
            {!! csrf_field() !!}
            <div class="">
                <div class="col-xs-12" style="overflow: hidden;">

                    <div class="space-4"></div>

                    <div class="row" style="overflow: hidden;">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="space-4"></div>

                                    <div class="col-xs-3" >
                                        <label for="departmentType">{{trans($transFile.'.Department')}}</label>
                                        <select name="departmentType" id="departmentType" class="search-select chosen-select">

                                            <option value="1" @if($searchCondition['departmentType'] == 1) selected @endif>Sale</option>
                                            <option value="2" @if($searchCondition['departmentType'] == 2) selected @endif>SIR</option>
                                            <option value="3" @if($searchCondition['departmentType'] == 3) selected @endif>CS</option>
                                            <option value="4" @if($searchCondition['departmentType'] == 4) selected @endif>CUS</option>
                                            <option value="5" @if($searchCondition['departmentType'] == 5) selected @endif>QA</option>
                                            <option value="6" @if($searchCondition['departmentType'] == 6) selected @endif>BOD</option>


                                        </select>
                                    </div>
                                    <div class="col-xs-3" >
                                        <label for="surveyType">{{trans($transFile.'.PointOfContact')}}</label>
                                        <select data-placeholder="Tất cả" name="surveyType" id="surveyType" class="search-select chosen-select">
                                            <option value="1" @if($searchCondition['type'] == 1) selected @endif>{{trans($transFile.'.AfterActive')}}</option>
                                            <option value="2" @if($searchCondition['type'] == 2) selected @endif>{{trans($transFile.'.AfterChecklist')}}</option>

                                        </select>
                                    </div>
                                    <div class="col-xs-3  channelConfirm">
                                        <label for="channelConfirm">{{trans($transFile.'.ChannelConfirm')}}</label>
                                        <select data-placeholder="Tất cả" name="channelConfirm" id="channelConfirm" class="search-select chosen-select">
                                            @foreach($listRecordChannels as $recordChannel)
                                                <option value="{{$recordChannel->record_channel_id}}">{{$recordChannel->record_channel_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="col-xs-6 no-padding" >
                                            <label for="survey_from">{{trans($transFile.'.FromDate')}}</label>
                                            <div class="inner-addon right-addon">
                                                <i class="glyphicon glyphicon-calendar red"></i>
                                                <input type="text" name="survey_from" id="surveyFrom"  value="{{!empty($searchCondition['survey_from']) ? date('d-m-Y',strtotime($searchCondition['survey_from'])) :date('d-m-Y')}}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-xs-6 no-padding-right" >
                                            <label for="survey_to">{{trans($transFile.'.ToDate')}}</label>
                                            <div class="inner-addon right-addon">
                                                <i class="glyphicon glyphicon-calendar red"></i>
                                                <input type="text" name="survey_to" value="{{!empty($searchCondition['survey_to']) ? date('d-m-Y',strtotime($searchCondition['survey_to'])) :date('d-m-Y')}}" id="surveyTo" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row" style="padding-top: 10px">
                                    <div class="col-xs-12">
                                        <button class="btn" id="btnAdvanceSearch" type='button' onclick="clickAdvanceSearch()"><i class="icon-list bigger-110"></i>{{trans($transFile.'.AdvanceSearch')}}</button>
                                    </div>
                                </div>
                                {{--</div>--}}

                                {{--<div class="space-4"></div>--}}

                                <div class="row" id='advanceSearch' style="display: none;">
                                    <div style="padding-top: 10px" >
                                        <div id="div_location" class="col-xs-3" >
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

                                        <div class="col-xs-3" >
                                            <label for="CSATPointNet">{{trans($transFile.'.NetPoint')}}</label>
                                            <select data-placeholder="{{trans($transFile.'.All')}}" name="CSATPointNet[]" id="CSATPointInternet" class="search-select chosen-select" multiple>
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if(!empty($searchCondition['CSATPointNet']) && in_array($i, $searchCondition['CSATPointNet']))
                                                        <option selected="selected" value="{{$i}}">{{trans($transFile.'.Point')}} {{$i}}</option>
                                                    @else
                                                        <option value="{{$i}}">{{trans($transFile.'.Point')}} {{$i}}</option>
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>



                                        {{--</div>--}}

                                        {{--<div class="space-4"></div>--}}

                                        {{--<div class="row">--}}

                                        <div class="col-xs-3 afterPaid" style="" >
                                            <label for="errorType">{{trans($transFile.'.NetErrorType')}}</label>
                                            <select data-placeholder="{{trans($transFile.'.All')}}" name="NetErrorType" id="NetErrorType" class="search-select chosen-select">
                                                <option value="0">{{trans($transFile.'.All')}}</option>
                                                <?php
                                                if (!empty($selErrorType)) {
                                                foreach ($selErrorType as $val) {
                                                if ($val->answer_group == 20) {//loại lỗi Internet
                                                if (!empty($searchCondition['NetErrorType']) && $val->answer_id == $searchCondition['NetErrorType']) {
                                                ?>
                                                <option selected="selected" value="{{$val->answer_id}}">{{trans('error.'.$val->answers_key)}}</option>
                                                <?php } else { ?>
                                                <option value="{{$val->answer_id}}">{{trans('error.'.$val->answers_key)}}</option>
                                                <?php
                                                }
                                                }
                                                }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-xs-3">
                                            <label for="RateNPS">{{trans($transFile.'.OpinionOfCustomer')}}</label>
                                            <select data-placeholder="{{trans($transFile.'.All')}}" name="RateNPS[]" id="RateNPS" class="search-select chosen-select" multiple>
                                                <?php
                                                if (!empty($selNPSImprovement)) {
                                                foreach ($selNPSImprovement as $val) {
                                                if (!empty($searchCondition['RateNPS']) && in_array($val->answer_id, $searchCondition['RateNPS'])) {
                                                ?>
                                                <option selected="selected" value="{{$val->answer_id}}">{{trans('answer.'.$val->answers_key)}}</option>
                                                <?php } else { ?>
                                                <option value="{{$val->answer_id}}">{{trans('answer.'.$val->answers_key)}}</option>
                                                <?php
                                                }
                                                }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div style="padding-top: 10px" >
                                        <div class="col-xs-3"  style="padding-top: 10px;">
                                            <label for="contractNum">{{trans($transFile.'.ContractNumber')}}</label>
                                            <input type="text" name="contractNum" class="form-control"
                                                   id="contractNum" maxlength="200" value="{{isset($searchCondition['contractNum']) ?$searchCondition['contractNum'] :''}}">
                                        </div>


                                        {{--<div class="col-xs-3" >--}}
                                        {{----}}
                                        {{--</div>--}}

                                        <div class="col-xs-3" style="padding-top: 10px;" >
                                            <label for="user_survey">{{trans($transFile.'.SurveyUser')}}</label>
                                            <input type="text" name="user_survey" class="form-control"
                                                   id="user_survey" maxlength="200" value="{{isset($searchCondition['userSurvey']) ?$searchCondition['userSurvey'] :''}}">
                                        </div>

                                        {{--</div>--}}

                                        {{--<div class="row">--}}

                                        <div class="col-xs-3 afterPaid" style="padding-top: 10px;" >
                                            <label for="salerName">{{trans($transFile.'.Sale')}}</label>
                                            <input type="text" name="salerName" class="form-control"
                                                   id="salerName" maxlength="200" value="{{isset($searchCondition['salerName']) ?$searchCondition['salerName'] :''}}">
                                        </div>
                                        <div class="col-xs-3 afterPaid" style="padding-top: 10px" >
                                            <label for="technicalStaff">{{trans($transFile.'.Tech')}}</label>
                                            <input type="text" name="technicalStaff" class="form-control"
                                                   id="technicalStaff" maxlength="200" value="{{isset($searchCondition['technicalStaff']) ?$searchCondition['technicalStaff'] :''}}">
                                        </div>
                                    </div>
                                    <div style="padding-top: 10px" >

                                        <div class="col-xs-3" style="    padding-top: 10px;">
                                            <label for="surveyStatus">{{trans($transFile.'.Resolve')}}</label>
                                            <select data-placeholder="{{trans($transFile.'.All')}}" name="surveyStatus[]" id='surveyStatus' class="search-select chosen-select" multiple>
                                                <option value="4" @if(!empty($searchCondition['section_connected']) && in_array(4,$searchCondition['section_connected'])) selected @endif>{{trans($transFile.'.MeetUser')}}</option>
                                                <option value="3" @if(!empty($searchCondition['section_connected']) && in_array(3,$searchCondition['section_connected'])) selected @endif>{{trans($transFile.'.DidNotMeetUser')}}</option>
                                                <option value="2" @if(!empty($searchCondition['section_connected']) && in_array(2,$searchCondition['section_connected'])) selected @endif>{{trans($transFile.'.MeetCustomerCustomerDeclinedToTakeSurvey')}}</option>
                                                <option value="1" @if(!empty($searchCondition['section_connected']) && in_array(1,$searchCondition['section_connected'])) selected @endif>{{trans($transFile.'.CannotContact')}}</option>
                                                <option value="0" @if(!empty($searchCondition['section_connected']) && in_array(0,$searchCondition['section_connected'])) selected @endif>{{trans($transFile.'.NoNeedContact')}}</option>
                                            </select>
                                        </div>

                                        <div class="col-xs-3  processingActionsInternet" style=" padding-top: 10px; <?php
                                        if (!empty($searchCondition['processingActionsInternet']) && (!empty($searchCondition['CSATPointNet'])) && (in_array("1", $searchCondition['CSATPointNet']) || in_array("2", $searchCondition['CSATPointNet'])))
                                            echo "display:block";
                                        else
                                            echo "display:none";
                                        ?>" >
                                            <label for="processingActions">{{trans($transFile.'.ResolveInternet')}}</label>
                                            <select data-placeholder="{{trans($transFile.'.All')}}" name="processingActionsInternet" id="processingActions" class="search-select chosen-select">
                                                <option value="0">{{trans($transFile.'.All')}}</option>
                                                <?php
                                                if (!empty($selProcessingActions)) {
                                                foreach ($selProcessingActions as $val) {
                                                if (!empty($searchCondition['processingActionsInternet']) && $val->answer_id == $searchCondition['processingActionsInternet']) {
                                                ?>
                                                <option selected="selected" value="{{$val->answer_id}}">{{trans('action.'.$val->answers_key)}}</option>
                                                <?php } else { ?>
                                                <option value="{{$val->answer_id}}">{{trans('action.'.$val->answers_key)}}</option>
                                                <?php
                                                }
                                                }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        {{--</div>--}}

                                        {{--<div class="space-4"></div>--}}

                                        {{--<div class="row">--}}


                                        <div class="col-xs-3" style="padding-top: 10px;">
                                            <label for="sectionGeneralAction">{{trans($transFile.'.GeneralResolve')}}</label>
                                            <select name="sectionGeneralAction" class="search-select chosen-select">


                                                <option value="1" @if(!empty($searchCondition['sectionGeneralAction']) && $searchCondition['sectionGeneralAction']==1) selected @endif>{{trans($transFile.'.NotYetDoAnything')}}</option>
                                                <option value="3" @if(!empty($searchCondition['sectionGeneralAction']) && $searchCondition['sectionGeneralAction']==3) selected @endif>{{trans($transFile.'.CreatePreChecklist')}} </option>
                                                <option value="2" @if(!empty($searchCondition['sectionGeneralAction']) && $searchCondition['sectionGeneralAction']==2) selected @endif>{{trans($transFile.'.CreateChecklist')}}</option>
                                                {{--<option value="5" @if(!empty($searchCondition['sectionGeneralAction']) && $searchCondition['sectionGeneralAction']==5) selected @endif>Chuyển phòng ban khác</option>--}}
                                            </select>
                                        </div>
                                    </div>



                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 10px;">
                                    <div class="col-xs-12 center" >
                                        <button class="btn btn-success" id="btnSubmit" type='submit' onclick="clicksubmit()"><i class="icon-search bigger-110"></i>{{trans($transFile.'.Find')}}</button>
                                        <?php if (!empty($modelSurveySections) && count($modelSurveySections) > 0) { ?>
                                        <button class="btn btn-info" id="btnExport" type='button'><i class="icon-search bigger-110"></i>{{trans($transFile.'.Excel')}}</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-4"></div>
                        <?php if (!empty($modelSurveySections) && count($modelSurveySections) > 0) { ?>
                        <div class="row">
                            <div class="col-xs-6" style='color: #307ecc;font-weight: bold; font-size: 20px; margin: 20px 0;'><div>{{trans($transFile.'.Total')}}: {{$modelSurveySections->total()}}</div></div>
                            <div class="col-xs-6"><div class="pull-right">{{$modelSurveySections->links()}}</div></div>
                        </div>
                        <?php } ?>
                        <div class="col-xs-12" style="overflow: hidden;">
                            <div class="wrapper1" style="height: 20px;">
                                <div class="div1"></div>
                            </div>
                            <div class="wrapper2">
                                {{--<div class="table-responsive div2" id="id="table-div"">--}}
                                <div class="table-responsive div2">
                                    <table id="tableInfoSurvey" class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr style="color: #0B6CBC;">
                                            <th>{{trans($transFile.'.ON')}}</th>
                                            <th>{{trans($transFile.'.Branch')}}</th>
                                            <th>{{trans($transFile.'.PointOfContact')}}</th>
                                            <th>{{trans($transFile.'.ChannelConfirm')}}</th>
                                            <th>{{trans($transFile.'.ContractNumber')}}</th>
                                            <th>{{trans($transFile.'.Sale')}}</th>
                                            <th>{{trans($transFile.'.Tech')}}</th>
                                            <th>{{trans($transFile.'.TimeComplete')}}</th>
                                            <th>CSAT Internet</th>
                                            <th>{{trans($transFile.'.ResolveInternet')}}</th>
                                            <th>{{trans($transFile.'.NetErrorType')}}</th>
                                            <th style="text-align: center">{{trans($transFile.'.Note')}}</th>
                                            <!--<th style="text-align: center"><b style="color: #DD1144;">Hành động xử lý CSKH</b></th>-->



                                            <?php
                                            if (isset($searchCondition['sectionGeneralAction'])) {
                                            //Precl
                                            if ($searchCondition['sectionGeneralAction'] == 3) {
                                            ?>
                                            <th>Prechecklist</th>
                                            <th>{{trans($transFile.'.FirstIncident')}}</th>
                                            <th>{{trans($transFile.'.ConfirmInformation')}}</th>
                                            <th>{{trans($transFile.'.CountSupport')}}</th>
                                            <th>{{trans($transFile.'.Status')}}</th>
                                            <th>{{trans($transFile.'.SurveyAgent')}}</th>
                                            <th>{{trans($transFile.'.ProcessTime')}}</th>
                                            <th>{{trans($transFile.'.AppointmentTime')}}</th>
                                            <th>{{trans($transFile.'.TotalMinute')}}</th>
                                            <th>{{trans($transFile.'.ProcessAction')}}</th>
                                            <th>{{trans($transFile.'.NumberPre')}}</th>


                                            <?php
                                            }
                                            //CL
                                            else if($searchCondition['sectionGeneralAction'] == 2)
                                            {
                                            ?>
                                            <th>{{trans($transFile.'.InputTime')}}</th>
                                            <th>{{trans($transFile.'.Assign')}}</th>
                                            <th>{{trans($transFile.'.TimeStore')}}</th>
                                            <th>{{trans($transFile.'.ErrorPosition')}}</th>
                                            <th>{{trans($transFile.'.ErrorDescription')}}</th>
                                            <th>{{trans($transFile.'.ReasonDescription')}}</th>
                                            <th>{{trans($transFile.'.WaySolving')}}</th>
                                            <th>{{trans($transFile.'.Note')}}</th>
                                            <th>{{trans($transFile.'.ChecklistType')}}</th>
                                            <th>{{trans($transFile.'.QuantityOfChecklist')}}</th>
                                            <th>{{trans($transFile.'.RepeatCL')}}</th>
                                            <th>{{trans($transFile.'.Status')}}</th>
                                            <th>{{trans($transFile.'.FinishTime')}}</th>
                                            <th>{{trans($transFile.'.TotalMinute')}}</th>
                                            <?php
                                            }
                                            //Chuyen phong ban
//                                            else if ($searchCondition['sectionGeneralAction'] == 5) {
                                            ?>
                                            {{--<th>Ngày gửi</th>--}}
                                            {{--<th>Nội dung gửi</th>--}}
                                            {{--<th>Bộ phận chuyển tiếp</th>--}}
                                            {{--<th>Bộ phận tiếp nhận</th>--}}
                                            {{--<th>Nội dung xử lý</th>--}}
                                            {{--<th>Kết quả XL</th>--}}
                                            {{--<th>Người Xử lý</th>--}}
                                            {{--<th>Thời gian xử lý</th>--}}
                                            {{--<th>Tổng số phút</th>--}}
                                            {{--<th>Số lượng chuyển tiếp</th>--}}


                                            <?php
//                                            }
                                            }
                                            ?>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        //                                        dump($modelSurveySections);die;
                                        $i = ($page != null )? ($page - 1) * 50 + 1 : 1;
                                        if ( !empty($modelSurveySections))
                                        {
                                        ?>
                                        @foreach($modelSurveySections as $sections)
                                            <tr >
                                                <td>{{$i}}</td>
                                                <td>{{$sections['ChiNhanh']}}</td>
                                                <td>
                                                    <?php
                                                    $nameSurvey = isset($typeSurvey[$sections['section_survey_id']]) ? $typeSurvey[$sections['section_survey_id']] : $sections['section_survey_id'];
                                                    echo $nameSurvey;
                                                    ?>
                                                </td>
                                                <td>{{$recordChannelArray[$sections['section_record_channel']]}}</td>
                                                <td>{{$sections['section_contract_num']}}</td>
                                                <td>{{$sections['section_acc_sale']}}</td>
                                                <td>{{$sections['section_account_inf'].$sections['section_account_list']}}</td>
                                                <td>{{$sections['section_time_completed']}}</td>
                                                <td>
                                                    {{$sections['CSAT_Internet']}}
                                                </td>

                                                <td style="text-align: center" class="more">
                                                    <?php
                                                    if (isset($actionNetTv[$sections['Xu_ly_internet']])) {
                                                    ?>
                                                    {{$actionNetTv[$sections['Xu_ly_internet']]}}
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (isset($errorNet[$sections['Loai_loi_internet']])) {
                                                    ?>
                                                    {{$errorNet[$sections['Loai_loi_internet']]}}
                                                    <?php } ?>
                                                </td>
                                                <td class="detailActionPopUp" style="text-align: left;">
                                                    {{$sections['section_note']}}
                                                </td>
                                            <!--                                                <td class="detailActionPopUp" style="text-align: left">
                                                    {{--{{$generalAction[$sections['section_action']]}} --}}
                                                    </td>-->
                                                <?php
                                                if ($searchCondition['sectionGeneralAction'] == 3) {
                                                ?>
                                                <td><?php
                                                    if ($sections['first_status'] == '' || $sections['first_status'] == NULL)
                                                        echo trans($transFile.'.No');
                                                    else
                                                        echo trans($transFile.'.Yes');
                                                    ?></td>
                                                <td>
                                                    <?php if (isset($firstStatusPreCL[$sections['first_status']])) { ?>
                                                    {{$firstStatusPreCL[$sections['first_status']]}}
                                                    <?php } ?>

                                                </td>
                                                <td >
                                                    {{$sections['description']}}
                                                </td>
                                                <td>
                                                    {{$sections['count_sup']}}
                                                </td>
                                                <td>
                                                    <?php if (isset($prechecklistStatus[$sections['sup_status_id']])) { ?>
                                                    {{$prechecklistStatus[$sections['sup_status_id']]}}
                                                    <?php } ?>

                                                </td>
                                                <td>
                                                    {{$sections['create_by']}}
                                                </td>
                                                <td>
                                                    {{$sections['update_date']}}
                                                </td>
                                                <td>
                                                    {{$sections['appointment_timer']}}
                                                </td>
                                                <td>
                                                    {{$sections['total_minute']}}
                                                </td>
                                                <td>
                                                    <?php if (isset($actionProcessPreCL[$sections['action_process']])) { ?>
                                                    {{$actionProcessPreCL[$sections['action_process']]}}
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($sections['first_status'] != '' && $sections['first_status'] != NULL) {
                                                    ?>
                                                    {{($sections['subkey'] + 1)}}<br>
                                                    <?php
                                                    if ($sections['subkey'] > 0) {
                                                    ?>
                                                    <span style="color: red;cursor: pointer" data='<?php echo $sections['section_contract_num'] . '-' . $sections['section_code'] . '-' . $sections['section_survey_id']; ?>' class='showDetailPreclAndCl' >Xem thêm</span>
                                                    <?php
                                                    }
                                                    } else {
                                                    ?>
                                                    0
                                                    <?php
                                                    }
                                                    ?>
                                                </td>


                                                <?php
                                                } else if($searchCondition['sectionGeneralAction'] == 2)
                                                {
                                                ?>
                                                <td>
                                                    {{$sections['input_time']}}
                                                </td>
                                                <td>
                                                    {{$sections['assign']}}
                                                </td>
                                                <td>
                                                    <?php
                                                    //                                                    if ($sections['action_process'] == 1) {
                                                    if ($sections['finish_date'] != '' && $sections['finish_date'] != NULL) {
                                                        $storeTime = 0;
                                                    } else {
                                                        $currentTime = date('y-m-d h:i:s');
                                                        $date2Timestamp = strtotime($currentTime);
                                                        $date1Timestamp = strtotime($sections['created_at']);
                                                        $difference = $date2Timestamp - $date1Timestamp;

                                                        $storeTime = round($difference / (60 * 60), 0);
                                                    }
                                                    ?>
                                                    {{$storeTime . ' gio'}}
                                                    <?php
                                                    //                                                    }
                                                    ?>
                                                </td>

                                                <td>
                                                    {{$sections['error_position']}}
                                                </td>
                                                <td>
                                                    {{$sections['error_description']}}
                                                </td>
                                                <td>
                                                    {{$sections['reason_description']}}
                                                </td>
                                                <td>
                                                    {{$sections['way_solving']}}
                                                </td>
                                                <td >
                                                    {{$sections['s_description']}}
                                                </td>
                                                <td>
                                                    {{$sections['checklist_type']}}
                                                </td>
                                                <td>
                                                    <?php
                                                    if($sections['repeat_checklist'] === null)
                                                    {
                                                        echo 1;
                                                    }
                                                    else if ($sections['repeat_checklist'] === '')
                                                    {
                                                        echo 0;
                                                    } else
                                                    {
                                                        echo ($sections['repeat_checklist'] +1);
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    {{$sections['repeat_checklist']}}
                                                </td>
                                                <td>
                                                    {{$sections['final_status']}}
                                                </td>
                                                <td>
                                                    {{$sections['finish_date']}}
                                                </td>
                                                <td>
                                                    {{$sections['total_minute']}}
                                                </td>
                                                <?php
                                                }
                                                //                                                     dump($sections);die;
                                                ?>


                                            </tr>

                                            <?php $i++; ?>
                                        @endforeach
                                        <?php
                                        }
                                        ?>
                                        </tbody>
                                        <?php if (!empty($modelSurveySections)) { ?>
                                        <tfoot>
                                        <tr class="td-footer"><td colspan="<?php
                                            if (!isset($searchCondition['sectionGeneralAction']) || (isset($searchCondition['sectionGeneralAction']) && $searchCondition['sectionGeneralAction'] == 1))
                                                echo "17";
                                            else if (isset($searchCondition['sectionGeneralAction']) && $searchCondition['sectionGeneralAction'] == 3)
                                                echo "41";

                                            ?>">
                                                <span class="pull-left" style='color: #307ecc;font-weight: bold; font-size: 20px; margin: 20px 0;'>{{trans($transFile.'.Total')}}: {{$modelSurveySections->total()}}</span>
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
                                {{trans($transFile.'.NetPoint')}}Chi tiết khảo sát
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
                        <span class="title"> </span>
                    </div>
                </div>

                <div class="modal-body" id="modal-table-record-body">
                </div>

                <div class="modal-footer no-margin-top">
                    <button class="btn btn-sm btn-danger pull-left" data-dismiss="modal" onclick="stopAll()">
                        <i class="icon-remove"></i>
                        {{trans($transFile.'.Close')}}
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
            max-height:500px;
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

        /* align glyph */
        .left-addon .glyphicon  { left:  0px;}
        .right-addon .glyphicon { right: 0px;}

        /* add padding  */
        .left-addon input  { padding-left:  30px; }
        .right-addon input { padding-right: 30px; }

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

            $('#region_sel').change(function () {
                var id = $(this).val();
                branchMapByRegion(id)
            });
            // $('#departmentType').change(function () {
            //     checkTypeSurvey($(this).val());
            // });
            ///
            $('#surveyType').change(function () {
                checkConfirmChannel($(this).val());
            });
            $('#btnSubmit').click(function () {
                $('#formsubmit').attr('action', '<?php echo url('/' . $prefix . '/' . $controller . '/detail') ?>');
                $('#formsubmit').submit();
            });
            $('#btnExport').click(function () {
                $('.title').html('{{trans($transFile.'.ListExcelFile')}}');
                var a = '<div class="center" id="spinner"><img src="{{asset("assets/img/bluespinner.gif")}}" /></div>';
                $('#modal-table-record-body').html(a);
                $('#modal-table-record').modal().show();
                $.ajax({
                    url: '<?php echo url('/' . $prefix . '/' . $controller . '/exportDetail') ?>',
                    cache: false,
                    type: "POST",
                    dataType: "html",
                    data: {_token: $('input[name=_token]').val()},
                    beforeSend: function () {
                    },
                    complete: function () {
                    },
                    success: function (data) {
                        $('#modal-table-record-body').html(data);
                        console.log(data);
                    },
                });
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

        function clickAdvanceSearch() {
            $('#advanceSearch').toggle();
        }

        $('.showDetailPreclAndCl').click(function () {
            $('.title').html('Chi tiết hành động xử lý');
            var a = '<div class="center" id="spinner"><img src="{{asset("assets/img/bluespinner.gif")}}" /></div>';
            $('#modal-table-record-body').html(a);
            $('#modal-table-record').modal().show();
            var data = $(this).attr('data');
            $.ajax({
                url: '<?php echo url('/' . $prefix . '/' . $controller . '/get-checklist-info') ?>',
                cache: false,
                type: "POST",
                dataType: "html",
                data: {'_token': $('input[name=_token]').val(), 'data': data, 'type':3},
                success: function (data) {
                    $('#modal-table-record-body').html(data);
                },
            });
        });

        $('.showDetailFD').click(function () {
            $('.title').html('Chi tiết hành động xử lý');
            var a = '<div class="center" id="spinner"><img src="{{asset("assets/img/bluespinner.gif")}}" /></div>';
            $('#modal-table-record-body').html(a);
            $('#modal-table-record').modal().show();
            var data = $('.showDetailFD').attr('data');
            $.ajax({
                url: '<?php echo url('/' . $prefix . '/' . $controller . '/get-checklist-info') ?>',
                cache: false,
                type: "POST",
                dataType: "html",
                data: {'_token': $('input[name=_token]').val(), 'data': data, 'type':5},
                success: function (data) {
                    $('#modal-table-record-body').html(data);
                },
            });
        });

        function clicksubmit() {
            <?php Session::put('click', 1); ?>
            $('#formsubmit').submit();
        }

        function stopAll() {
            var a = '';
            $('#modal-table-record-body').html(a);
        }

        function init() {
            var id = $('#departmentType').val();
            // checkTypeSurvey(id);
            // id = $('#region_sel').val();
            // branchMapByRegion(id);

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
                if (id.indexOf("1") != - 1 || id.indexOf("5") != - 1) {
                    $(".location_sales_sel_flag").css("display", "block");
                } else{
                    $(".location_sales_sel_flag").css("display", "none");
                }

                $('#location_sel optgroup').attr('disabled', true);
                $.each(id, function (key, val) {
                    $('#location_sel .region' + val).removeAttr('disabled');
                    $('#location_sel').find('optgroup:first').hide();
                });
                $('#location_sales_sel optgroup').attr('disabled', true);
                $.each(id, function (key, val) {
                    $('#location_sales_sel .region' + val).removeAttr('disabled');
                    $('#location_sales_sel').find('optgroup:first').hide();
                });
            } else {
                $(".location_sales_sel_flag").css("display", "none");
                $('#location_sel optgroup').removeAttr('disabled');
                $('#location_sales_sel optgroup').removeAttr('disabled');
            }

            $('#location_sales_sel').trigger('chosen:updated');
            $('#location_sel').trigger('chosen:updated');
        }

        function checkConfirmChannel(idContactPoint) {
            $('#channelConfirm option').attr('disabled', true);
            switch (idContactPoint){
                case '1':
                    $("#channelConfirm").val('1');
                    $('#channelConfirm option[value="1"]').removeAttr('disabled');
                    break;
                case '2':
                    $("#channelConfirm").val('1');
                    $('#channelConfirm option[value="1"]').removeAttr('disabled');
                    break;
                case '3':
                    $("#channelConfirm").val('4');
                    $('#channelConfirm option[value="4"]').removeAttr('disabled');
                    break;
                case '6':
                    $("#channelConfirm").val('1');
                    $('#channelConfirm option[value="1"]').removeAttr('disabled');
                    break;
                default:
                    $('#channelConfirm option').attr('disabled', false);
            }
            $('#channelConfirm').trigger("chosen:updated");
            showHideInput(idContactPoint);
        }

        function checkTypeSurvey(idDepartment){
            $('#surveyType option').attr('disabled', true);
            switch (idDepartment){
                case '1':
                    $("#surveyType").val('1');
                    $('#surveyType option[value="1"]').removeAttr('disabled');
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
            if (typeSurvey == 2) {
                $('#div_maintain').show(500);
                $('#div_deploy').hide(500);
                $('#div_sale').hide(500);
                $('.afterPaid').show(500);
                //set lại giá trị mặc định null
                $('#CSATPointSale').val('').trigger('chosen:updated');
                $('#CSATPointNVTK').val('').trigger('chosen:updated');
            } else if (typeSurvey == 3) {
                $('#div_maintain').hide(500);
                $('#div_deploy').hide(500);
                $('#div_sale').hide(500);
                $('.afterPaid').hide(500);
                //set lại giá trị mặc định null
                $('#CSATPointSale').val('').trigger('chosen:updated');
                $('#CSATPointNVTK').val('').trigger('chosen:updated');
                $('#surveyStatus').val('').trigger('chosen:updated');
                $('#salerName').val('');
                $('#technicalStaff').val('');
            } else {
                $('#div_maintain').hide(500);
                $('#div_deploy').show(500);
                $('#div_sale').show(500);
                $('.afterPaid').show(500);
                //set lại giá trị mặc định null
                $('#CSATPointBT').val('').trigger('chosen:updated');
            }
        }

        $("#CSATPointInternet").change(function () {
            var value = $("#CSATPointInternet").val();
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
        $("#CSATPointTV").change(function () {
            var value = $("#CSATPointTV").val();
            //Có chọn
            if (value != null) {
                if (value.indexOf("1") != - 1 || value.indexOf("2") != - 1){
                    $(".processingActionsTV").css("display", "block");
                }
                else{
                    $(".processingActionsTV").css("display", "none");
                }
            } else{
                $(".processingActionsTV").css("display", "none");
            }
        });
        @if (!empty($searchCondition['CSATPointNet']) && (in_array(1, $searchCondition['CSATPointNet']) || in_array(2, $searchCondition['CSATPointNet'])))
        $(".processingActionsInternet").css("display", "block");
        @endif
        @if (!empty($searchCondition['CSATPointTV']) && (in_array(1, $searchCondition['CSATPointTV']) || in_array(2, $searchCondition['CSATPointTV'])))
        $(".processingActionsTV").css("display", "block");
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