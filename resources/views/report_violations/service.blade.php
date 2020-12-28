@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
//    dd($modelSurveySections);die;
    $controller = 'violationsservice';
    $title = 'List Report';
    $transfile = $controller;
    $common = 'common';
    $prefix = main_prefix;
    $temp = '';

    //Array xử lý
    $typeNetError = [89 => 'Wifi  yếu, chập chờn', 98 => 'TÍn hiệu không ổn định suy hao không đạt chuẩn', 120 => 'NET quốc tế chậm',
        85 => 'Net Chập chờn', 97 => 'Net chậm', 92 => 'Mất tín hiệu', 87 => 'Lỗi thiết bị', 88 => 'Lỗi Ivoice (nghe / nói / tone)',
        91 => 'Không sử dụng được wifi', 86 => 'Khác', 90 => 'Game lag', 94 => 'Có tín hiệu không truy cập được'];

    $typeTVError = [122 => 'Điều khiển , app điều khiển',105 => 'Không xem được các kênh truyền hình',112 => 'Lỗi kho ứng dụng', 123 => 'Đấu nối thiết bị amply sử dụng KaraTV', 99 => 'Xé hình',
        106 => 'Không sử dụng được thiết bị lưu trữ , mạng chia sẻ', 125 => 'Không có hình , không có tiếng một vài kênh', 127 => 'Khác', 111 => 'Hình ảnh bị sọc ngang, sọc chéo', 102 => 'Giật,Đứng hình , chập chờn',
        103 => 'Có hình không có tiếng hoặc có tiếng không có hình tất cả các kênh',124=>'Thiết bị HDBOX khởi động chậm',121=>'Lỗi kết nối HDBox TV'];

    $initStatusCL = [29 => 'Co tieng khong co hinh tat ca cac kenh', 31 => 'Co hinh khong co tieng tat ca cac kenh', 33 => 'OneTV bi xe hinh, giat hinh',
        36 => 'Khong xem duoc cac kenh truyen hinh', 37 => 'Khong xem duoc VoD', 38 => 'Khong load duoc portal', 39 => 'Cac loi lien quan toi OneTV Player',
        40 => 'Loi ket noi STB & TV'];

    $initFirstPCL = [5 => 'Yeu cau nhap Checklist', 1 => 'Mat ket noi', 6 => 'IPTV',
        7 => 'Wifi', 2 => 'Mang cham', 3 => 'Mang chap chon', 4 => 'Tinh trang khac'];

    $CLFinalStatus = [1 => 'Đã xử lý', 0 => 'Đã phân công', 9 => 'Bảo trì lại, cần xử lý gấp', 10 => 'Đang xử lý'
        , 2 => 'Chưa phân công', 3 => 'Đã xử lý hoàn tất qua phone', 4 => 'Khách hàng tự online', 5 => 'Đã xử lý và đang theo dõi'];

    $PCLFinalStatus = [2 => 'Đang xử lý',0=>'Đang xử lý', 3 => 'Xử lý hoàn tất', 99 => 'Hủy không xử lý'];
    
    $PCLActionProcessing=[1=>'Đóng PreCL/Tạo CL',0=>'Bỏ trống'];

    $arrayAction = [0 => 'Không làm gì', 1 => 'Không làm gì', 2 => 'Tạo checklist', 3 => 'PreChecklist', 4 => 'Tạo checklist INDO', 5 => 'Chuyển phòng ban khác'];
    $arrayResult = [0 => "<span class='label label-warning'>Không cần liên hệ</span>", 1 => "<span class='label label-danger arrowed-in'>Không liên lạc được</span>", 2 => "<span class='label label-danger arrowed-in'>Gặp KH, KH từ chối CS</span>", 3 => "<span class='label label-danger arrowed-in'>Không gặp người SD</span>", 4 => "<span class='label label-success arrowed'>Gặp người SD</span>"];
    $classTypeSurvey = [0 => "", 1 => "label label-info arrowed arrowed-right", 2 => "label label-success arrowed arrowed-right"];
    $emotions = [1 => 'Point_01.png', 2 => 'Point_02.png', 3 => 'Point_03.png', 4 => 'Point_04.png', 5 => 'Point_05.png'];
    $surveyTitle = [1 => 'Sau triển khai DirectSale', 2 => 'Sau bảo trì', 6 => 'Sau triển khai Telesale'];
    $vioType = [1 => 'Sai hẹn với khách hàng', 2 => 'Thái độ với khách hàng không tốt', 3 => 'Không thực hiện các yêu cầu phát sinh của khách hàng', 4 => 'Không hướng dẫn khách hàng',
        5 => 'Làm bừa, bẩn nhà khách hàng', 6 => 'Nghiệp vụ kỹ thuật', 7 => 'Tiến độ xử lý chậm', 8 => 'Vòi vĩnh khách hàng', 9 => 'Tư vấn không rõ ràng, đầy đủ', 10 => 'Tư vấn sai', 11 => 'Khác',
        12 => 'Lỗi không thuộc về nhân viên'];
    $punishTitle = [1 => 'Phạt tiền', 2 => 'Cảnh cáo/nhắc nhở', 3 => 'Buộc thôi việc', 4 => 'Không chế tài', 5 => 'Khác'];
    //bổ sung vào TH 'chưa trả lời'
    if (!empty($selNPSImprovement)) {
        foreach ($selNPSImprovement as $value) {
            $surveyImprove[$value->answer_id] = $value->answers_title;
            $surveyImprove['-1'] = 'Chưa trả lời';
            $surveyImprove[''] = '';
        }
    }

    //Thông tin quyền hạn user
    $allPermission = Session::get('allPermission');
    $userRole = Session::get('userRole');
    ?>
    <!--@include('layouts.pageheader', ['controller' => $controller, 'title' => $title, 'transfile' => $transfile])-->
    <!-- /.page-header -->

    <!-- PAGE CONTENT BEGINS -->
    <form id='formsubmit' class="form-horizontal" role="form" method="POST" action="<?php echo url('/' . $prefix . '/violations-service' . '/index') ?>">
        {!! csrf_field() !!}
        <div class="">
            <div class="col-xs-12" style="overflow: hidden;">

                <div class="space-4"></div>

                <div class="row" style="overflow: hidden;">
                    <div class="row" id='advance_search'>
                        <div class="col-xs-12">

                            <div class="space-4"></div>

                            <div class="row">
                                <div class="col-xs-3" >
                                    <label for="departmentType">Bộ phận/ Trung tâm</label>
                                    <select name="departmentType" id="departmentType" class="search-select chosen-select">
                                        <option value="1" @if($searchCondition['departmentType'] == 1) selected @endif>IBB</option>
                                        <?php if ($userRole['display_name'] != 'Trưởng phòng') { ?>
                                            <option value="2" @if($searchCondition['departmentType'] == 2) selected @endif>TIN</option>
                                            <option value="3" @if($searchCondition['departmentType'] == 3) selected @endif>PNC</option>
                                            <option value="4" @if($searchCondition['departmentType'] == 4) selected @endif>INDO</option>
                                            <option value="5" @if($searchCondition['departmentType'] == 5) selected @endif>BĐH</option>
                                            <option value="6" @if($searchCondition['departmentType'] == 6) selected @endif>CS</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-xs-3" >
                                    <label for="surveyType">Loại khảo sát</label>
                                    <select data-placeholder="Tất cả" name="surveyType" id="surveyType" class="search-select chosen-select">
                                        <option value="1" @if($searchCondition['type'] == 1) selected @endif>Triển khai</option>
                                        <option value="2" @if($searchCondition['type'] == 2) selected @endif>Bảo trì</option>
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
                                                        <optgroup class="region{{$t}}" label="{{$location->region}}" >
                                                            <?php
                                                        }
                                                        if (in_array($location->id, [4, 8])) {
                                                            if (!empty($searchCondition['location']) && in_array($val, $searchCondition['location']) || in_array($location->branch_id, $userGranted['branchID']) && (count($userGranted['branchID']) + count($userGranted['location'])) == 1) {//nếu nhiều hơn 1 chi nhánh thì ko show
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
                                <div id='div_sale' class="col-xs-3" >
                                    <label for="CSATPointSale">CSAT NV kinh doanh</label>
                                    <select data-placeholder="Tất cả" name="CSATPointSale[]" id="CSATPointSale" class="search-select chosen-select" multiple>
                                        @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointSale']) && in_array($i, $searchCondition['CSATPointSale']))
                                        <option selected="selected" value="{{$i}}">Điểm {{$i}}</option>
                                        @else 
                                        <option value="{{$i}}">Điểm {{$i}}</option>
                                        @endif
                                        @endfor
                                    </select>
                                </div>
                                <div id="div_maintain" class="col-xs-3" hidden="" >
                                    <label for="CSATPointBT">CSAT NV bảo trì</label>
                                    <select data-placeholder="Tất cả" name="CSATPointBT[]" id="CSATPointBT" class="search-select chosen-select" multiple>
                                        @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointBT']) && in_array($i, $searchCondition['CSATPointBT']))
                                        <option selected="selected" value="{{$i}}">Điểm {{$i}}</option>
                                        @else 
                                        <option value="{{$i}}">Điểm {{$i}}</option>
                                        @endif
                                        @endfor
                                    </select>
                                </div>
                                <div id="div_deploy" class="col-xs-3" >
                                    <label for="CSATPointNVTK">CSAT NV triển khai</label>
                                    <select data-placeholder="Tất cả" name="CSATPointNVTK[]" id="CSATPointNVTK" class="search-select chosen-select" multiple>
                                        @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointNVTK']) && in_array($i, $searchCondition['CSATPointNVTK']))
                                        <option selected="selected" value="{{$i}}">Điểm {{$i}}</option>
                                        @else 
                                        <option value="{{$i}}">Điểm {{$i}}</option>
                                        @endif
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-xs-3" >
                                    <label for="CSATPointNet">CSAT DV Internet</label>
                                    <select data-placeholder="Tất cả" name="CSATPointNet[]" class="search-select chosen-select" multiple>
                                        @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointNet']) && in_array($i, $searchCondition['CSATPointNet']))
                                        <option selected="selected" value="{{$i}}">Điểm {{$i}}</option>
                                        @else 
                                        <option value="{{$i}}">Điểm {{$i}}</option>
                                        @endif
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-xs-3" >
                                    <label for="CSATPointTV">CSAT DV truyền hình</label>
                                    <select data-placeholder="Tất cả" name="CSATPointTV[]" class="search-select chosen-select" multiple>
                                        @for($i = 1; $i <= 5; $i++)
                                        @if(!empty($searchCondition['CSATPointTV']) && in_array($i, $searchCondition['CSATPointTV']))
                                        <option selected="selected" value="{{$i}}">Điểm {{$i}}</option>
                                        @else 
                                        <option value="{{$i}}">Điểm {{$i}}</option>
                                        @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="space-4"></div>

                            <div class="row">
                                <div class="col-xs-3" >
                                    <label for="NPSPoint">Điểm NPS</label>
                                    <select data-placeholder="Tất cả" name="NPSPoint[]" class="search-select chosen-select" multiple>
                                        @for($i = 0; $i <= 10; $i++)
                                        @if(!empty($searchCondition['NPSPoint']) && in_array($i, $searchCondition['NPSPoint']))
                                        <option selected="selected" value="{{$i}}">Điểm {{$i}}</option>
                                        @else 
                                        <option value="{{$i}}">Điểm {{$i}}</option>
                                        @endif
                                        @endfor
                                    </select>
                                </div>
                                  
<!--                               <div class="col-xs-3" >

                                </div>-->
                                <div class="col-xs-3" >
                                    <label for="processingSurvey">Xử lý</label>
                                    <select data-placeholder="Tất cả" name="processingSurvey[]" class="search-select chosen-select" multiple>
                                        <option value="1" @if(!empty($searchCondition['section_action']) && in_array(1,$searchCondition['section_action'])) selected @endif>Không làm gì</option>
                                        <option value="2" @if(!empty($searchCondition['section_action']) && in_array(2,$searchCondition['section_action']))) selected @endif>Tạo Checklist</option>
                                        <option value="3" @if(!empty($searchCondition['section_action']) && in_array(3,$searchCondition['section_action'])) selected @endif>PreChecklist</option>
                                        <option value="4" @if(!empty($searchCondition['section_action']) && in_array(4,$searchCondition['section_action'])) selected @endif>Tạo Checklist INDO</option>
                                        <option value="5" @if(!empty($searchCondition['section_action']) && in_array(5,$searchCondition['section_action'])) selected @endif>Chuyển phòng ban khác</option>
                                    </select>
                                </div>
                                {{--
                                <div class="col-xs-3" >
                                    <label for="surveyStatus">Tình trạng</label>
                                    <select data-placeholder="Tất cả" name="surveyStatus[]" class="search-select chosen-select" multiple>
                                        <option value="4" @if(!empty($searchCondition['section_connected']) && in_array(4,$searchCondition['section_connected'])) selected @endif>Gặp người SD</option>
                                        <option value="3" @if(!empty($searchCondition['section_connected']) && in_array(3,$searchCondition['section_connected'])) selected @endif>Không gặp người SD</option>
                                        <option value="2" @if(!empty($searchCondition['section_connected']) && in_array(2,$searchCondition['section_connected'])) selected @endif>Gặp KH, KH từ chối CS</option>
                                        <option value="1" @if(!empty($searchCondition['section_connected']) && in_array(1,$searchCondition['section_connected'])) selected @endif>Không liên lạc được</option>
                                        <option value="0" @if(!empty($searchCondition['section_connected']) && in_array(0,$searchCondition['section_connected'])) selected @endif>Không cần liên hệ</option>
                                    </select>
                                </div>
                        --}}
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
                                <div class="col-xs-3" >
                                    <label for="contractNum">Số HĐ</label>
                                    <input type="text" name="contractNum" class="form-control" 
                                           id="contractNum" maxlength="200" value="{{isset($searchCondition['contractNum']) ?$searchCondition['contractNum'] :''}}">
                                </div>
                                <div class="col-xs-3" >
                                    <label for="contractNum">{{trans($transfile.'.User Survey')}}</label>
                                    <input type="text" name="user_survey" class="form-control" 
                                           maxlength="200" value="{{isset($searchCondition['userSurvey']) ?$searchCondition['userSurvey'] :''}}">
                                </div>
                                <div class="col-xs-3" >
                                    <label for="salerName">{{trans($transfile.'.NVKD')}}</label>
                                    <input type="text" name="salerName" class="form-control" 
                                           maxlength="200" value="{{isset($searchCondition['salerName']) ?$searchCondition['salerName'] :''}}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-3" style="padding-top: 10px" >
                                    <label for="technicalStaff">{{trans($transfile.'.Technical Staff')}}</label>
                                    <input type="text" name="technicalStaff" class="form-control" 
                                           maxlength="200" value="{{isset($searchCondition['technicalStaff']) ?$searchCondition['technicalStaff'] :''}}">
                                </div>
                                {{--
                                <div class="col-xs-3" style="padding-top: 10px" >
                                    <label for="violationsType">{{trans($transfile.'.Violations Type')}}</label>
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
                                </select>
                            </div>
                            <div class="col-xs-3" style="padding-top: 10px" >
                                <label for="punish">{{trans($transfile.'.Punishment')}}</label>
                                <select data-placeholder="Tất cả" name="punish" class="search-select chosen-select">
                                    <option value="0">Tất cả</option>
                                    <option value="1" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 1) selected @endif>Phạt tiền</option>
                                    <option value="2" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 2) selected @endif>Cảnh cáo/nhắc nhở</option>
                                    <option value="3" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 3) selected @endif>Buộc thôi việc</option>
                                    <option value="4" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 4) selected @endif>Không chế tài bổ sung</option>
                                    <option value="5" @if(!is_null($searchCondition['punishment']) && $searchCondition['punishment'] == 5) selected @endif>Khác</option>
                                </select>
                            </div>
                            <div class="col-xs-3" style="padding-top: 10px" >
                                <label for="disciplineFTQ">{{trans($transfile.'.FTQ Verify')}}</label>
                                <select data-placeholder="Tất cả" name="disciplineFTQ" class="search-select chosen-select">
                                    <option value="-1">Tất cả</option>
                                    <option value="0" @if(!is_null($searchCondition['disciplineFTQ']) && $searchCondition['disciplineFTQ'] == 0) selected @endif>Không</option>
                                    <option value="1" @if(!is_null($searchCondition['disciplineFTQ']) && $searchCondition['disciplineFTQ'] == 1) selected @endif>Có</option>
                                </select>
                            </div>
                            <div class="col-xs-3" style="padding-top: 10px" >
                                <label for="discipline">{{trans($transfile.'.Additional Discipline')}}</label>
                                <select data-placeholder="Tất cả" name="discipline" class="search-select chosen-select">
                                    <option value="-1">Tất cả</option>
                                    <option value="0" @if(!is_null($searchCondition['discipline']) && $searchCondition['discipline'] == 0) selected @endif>Không</option>
                                    <option value="1" @if(!is_null($searchCondition['discipline']) && $searchCondition['discipline'] == 1) selected @endif>Có</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="row">
                            <div class="col-xs-3" style="padding-top: 10px" >
                                <label for="remedy">{{trans($transfile.'.Remedy')}}</label>
                                <select data-placeholder="Tất cả" name="remedy" class="search-select chosen-select">
                                    <option value="-1">Tất cả</option>
                                    <option value="0" @if(!is_null($searchCondition['remedy']) && $searchCondition['remedy'] == 0) selected @endif>Không</option>
                                    <option value="1" @if(!is_null($searchCondition['remedy']) && $searchCondition['remedy'] == 1) selected @endif>Có</option>
                                </select>
                            </div>
                            <div class="col-xs-3" style="padding-top: 10px" >
                                <label for="userReported">{{trans($transfile.'.User Reported')}}</label>
                                <input type="text" name="userReported" class="form-control" 
                                       maxlength="200" value="{{isset($searchCondition['userReported']) ?$searchCondition['userReported'] :''}}">
                            </div>
                            <div class="col-xs-3" style="padding-top: 10px" >
                                <label for="punishAdditional">{{trans($transfile.'.Punishment Additional')}}</label>
                                <select data-placeholder="Tất cả" name="punishAdditional" class="search-select chosen-select">
                                    <option value="0">Tất cả</option>
                                    <option value="1" @if(!is_null($searchCondition['punishAdditional']) && $searchCondition['punishAdditional'] == 1) selected @endif>Phạt tiền</option>
                                    <option value="2" @if(!is_null($searchCondition['punishAdditional']) && $searchCondition['punishAdditional'] == 2) selected @endif>Cảnh cáo/nhắc nhở</option>
                                    <option value="3" @if(!is_null($searchCondition['punishAdditional']) && $searchCondition['punishAdditional'] == 3) selected @endif>Buộc thôi việc</option>
                                    <option value="4" @if(!is_null($searchCondition['punishAdditional']) && $searchCondition['punishAdditional'] == 4) selected @endif>Không chế tài bổ sung</option>
                                    <option value="5" @if(!is_null($searchCondition['punishAdditional']) && $searchCondition['punishAdditional'] == 5) selected @endif>Khác</option>
                                </select>
                            </div>
                            <div class="col-xs-3" style="padding-top: 10px" >
                                <label for="editedReport">{{trans($transfile.'.Edited Report')}}</label>
                                <select data-placeholder="Tất cả" name="editedReport" class="search-select chosen-select">
                                    <option value="-1">Tất cả</option>
                                    <option value="0" @if(!is_null($searchCondition['editedReport']) && $searchCondition['editedReport'] == 0) selected @endif>0 lần</option>
                                    <option value="1" @if(!is_null($searchCondition['editedReport']) && $searchCondition['editedReport'] == 1) selected @endif>1 lần</option>
                                    <option value="2" @if(!is_null($searchCondition['editedReport']) && $searchCondition['editedReport'] == 2) selected @endif>2 lần</option>
                                </select>
                            </div>

                        </div>
                        --}}

                        <div class="space-4"></div>
                        <div class="row">

                        </div>

                        <div class="space-4"></div>
                        <div class="row">
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
                        <div class="col-xs-6" style='color: #307ecc;font-weight: bold; font-size: 20px; margin: 20px 0;'><div style="padding-left: 15px;">Tổng số dòng: {{$modelSurveySections->total()}}</div></div>
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
                                        <th class="center" colspan="17">Thông tin ghi nhận từ hệ thống khảo sát CSKH</th>
                                        <th class="center" colspan="9">Prechecklist</th>
                                        <th class="center" colspan="13">Checklist</th>
                                        <!--<th class="center" colspan="9">Chuyển phòng ban</th>-->
                                    </tr>
                                    <tr>
                                        <th>STT</th>
                                        <th>Vùng</th>
                                        <th>Chi nhánh</th>
                                        <th>Loại khảo sát</th>
                                        <th>Kênh ghi nhận</th>
                                        <th>Số hợp đồng</th>
                                        <th>Nhân viên kinh doanh</th>
                                        <th>Nhân viên triển khai</th>
                                        <th>Nhân viên bảo trì</th>
                                        <th>Thời gian ghi nhận</th>
                                        <th>CSAT nhân viên kinh doanh</th>
                                        <th>CSAT nhân viên triển khai</th>
                                        <th>CSAT nhân viên bảo trì</th>
                                        <th>Loại lỗi Internet</th>
                                        <th>Loại lỗi TV</th>
                                        <th>Ghi chú</th>
                                        <th>Hành động xử lý của CSKH</th>

                                        <th>Ghi nhận sự cố ban đầu</th>
                                        <th>Thông tin ghi nhận</th>
                                        <th>Lần hỗ trợ</th>
                                        <th>Tình trạng</th>
                                        <th>Nhân viên ghi nhận</th>
                                        <th>Thời gian xử lý</th>
                                        <th>Thời gian hẹn</th>
                                        <th>Tổng số phút</th>
                                        <th>Hành động xử lý</th>

                                        <th>Thời gian nhập</th>
                                        <th>Phân công</th>
                                        <th>Thời gian tồn</th>
                                        <th>Vị trí xảy ra lỗi</th>
                                        <th>Mô tả lỗi</th>
                                        <th>Mô tả nguyên nhân</th>
                                        <th>Hướng xử lý</th>
                                        <th>Ghi chú</th>
                                        <th>Loại CL</th>
                                        <th>CL lặp</th>
                                        <th>Tình trạng</th>
                                        <th>Thời gian hoàn tất</th>
                                        <th>Tổng số phút</th>

<!--                                        <th>Ngày gửi</th>
                                        <th>Nội dung gửi</th>
                                        <th>Bộ phận chuyển tiếp</th>
                                        <th>Bộ phận tiếp nhận</th>
                                        <th>Nội dung xử lý</th>
                                        <th>Kết quả xử lý</th>
                                        <th>Người xử lý</th>
                                        <th>Thời gian xử lý</th>
                                        <th>Tổng số phút</th>-->


                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($modelSurveySections)) {
                                        $i=1;
                                        ?>
                                        @foreach($modelSurveySections as $surveySections)
                                        <tr>
                                            <td class="hidden-480">{{$i++}}</td>
                                            <td class="hidden-480">{{$surveySections->section_sub_parent_desc}}</td>
                                            <td class="hidden-480">{{$surveySections->section_branch_code}}</td>
                                             <td class="hidden-480">{{$surveyTitle[$surveySections->section_survey_id]}}</td>                                          
                                            <td class="hidden-480">CS/HappyCall </td>
                                            <td class="hidden-480">{{$surveySections->section_contract_num}}</td>
                                            <td class="hidden-480">{{$surveySections->salename}}</td>
                                            <td class="hidden-480">{{$surveySections->section_account_inf}}</td>
                                             <td class="hidden-480">{{$surveySections->section_account_list}}</td>
                                            <td class="hidden-480">{{$surveySections->section_time_completed}}</td>
                                            <td class="hidden-480">{{$surveySections->csat_salesman_point}}</td>
                                            <td class="hidden-480">{{$surveySections->csat_deployer_point}}</td>
                                            <td class="hidden-480">{{$surveySections->csat_maintenance_staff_point}}</td>                                         
                                            <td class="hidden-480">{{($surveySections->csat_net_point==1 || $surveySections->csat_net_point==2) ? $typeNetError[$surveySections->csat_net_answer_extra_id]:''}}</td>
                                            <td class="hidden-480">{{($surveySections->csat_tv_point==1 || $surveySections->csat_tv_point==2) ? $typeTVError[$surveySections->csat_tv_answer_extra_id]:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->section_note}}</td>
                                            <td class="hidden-480" >{{$arrayAction[$surveySections->section_action]}}</td>

                                            <td class="hidden480" >{{$surveySections->typeAction=='PCL' ? $initFirstPCL[$surveySections->first_status]:''}}</td>
                                            <td class="hidden480" >{{$surveySections->typeAction=='PCL' ? $surveySections->description:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='PCL' ? $surveySections->count_sup:''}}</td>
                                            <td class="hidden-480" >{{($surveySections->typeAction=='PCL' && $surveySections->status!=null) ? $PCLFinalStatus[$surveySections->status]:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='PCL' ? $surveySections->section_user_name:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='PCL' ? $surveySections->update_date:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='PCL' ? $surveySections->appointment_timer:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='PCL' ? $surveySections->total_minute:''}}</td>
                                             <td class="hidden-480" >{{($surveySections->typeAction=='PCL' && $surveySections->action_process!=null) ? $PCLActionProcessing[$surveySections->action_process]:''}}</td>
                                            

                                            <td class="hidden480" >{{$surveySections->typeAction=='CL'? $surveySections->input_time:''}}</td>
                                            <td class="hidden480" >{{$surveySections->typeAction=='CL'? $surveySections->assign:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->store_time:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->error_position:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->error_description:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->reason_description:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->way_solving:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->description:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->checklist_type:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->repeat_checklist:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->status:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->finish_date:''}}</td>
                                            <td class="hidden-480" >{{$surveySections->typeAction=='CL'? $surveySections->total_minute:''}}</td>

              
                                        </tr>
                                        @endforeach
                                    <?php } ?>
                                </tbody>
                                <?php if (!empty($modelSurveySections) && count($modelSurveySections) > 0) { ?>
                                    <tfoot>
                                        <tr><td colspan="{{($searchCondition['departmentType'] == 5 && $searchCondition['type'] == 1) ?24 :22}}">
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

    .wrapper1, .wrapper2 { width: 100%; overflow-x: scroll; overflow-y: hidden; }
    .wrapper1 { height: 20px; }
    .div1 { height: 20px; }
    .div2 { overflow: none; }
</style>
<script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
<script src="{{asset('assets/js/jquery.shorten.1.0.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('assets/js/moment.min.js')}}"></script>
<script src="{{asset('assets/js/fnPagingInfo.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/chosen.jquery.js')}}"></script>
<script type="text/javascript">
                                    $(document).ready(function () {
                                        init();
                                        var oTable1 = $('#tableInfoSurvey').dataTable({
                                            "aoColumns": [
                                                 null, null, null, null, null,null,null,null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null
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
                                            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                                                var page = <?php echo $currentPage; ?>;
                                                var length = <?php echo (Session::has('condition')) ? Session::get('condition')['recordPerPage'] : 15; ?>; //this.fnPagingInfo().iLength;
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
                                        $('#region_sel').change(function () {
                                            var id = $(this).val();
                                            if (id != null) {
                                                $('#location_sel optgroup').attr('disabled', true);
                                                $.each(id, function (key, val) {
                                                    $('#location_sel .region' + val).removeAttr('disabled');
                                                    $('#location_sel').find('optgroup:first').hide();
                                                });
                                            } else {
                                                $('#location_sel optgroup').removeAttr('disabled');
                                            }
                                            $('#location_sel').trigger('chosen:updated');
                                        })
                                        ///
                                        $('#departmentType').change(function () {
                                            var a = $(this).val();
                                            var t = 1;
                                            if (a == 1) {//IBB
                                                $('#surveyType option[value=1]').show();
                                                $('#surveyType option[value=2]').hide();
                                                //loại lỗi
                                                $('#violationsType option[value=9], #violationsType option[value=10]').show();
                                                $('#violationsType option[value=4], #violationsType option[value=5], #violationsType option[value=6], #violationsType option[value=7]').hide();
                                            } else if (a == 4) {//INDO
                                                $('#surveyType option[value=1]').hide();
                                                $('#surveyType option[value=2]').show();
                                                t = 2;
                                                //loại lỗi
                                                $('#violationsType option[value=4], #violationsType option[value=5], #violationsType option[value=6], #violationsType option[value=7]').show();
                                                $('#violationsType option[value=9], #violationsType option[value=10]').hide();
                                            } else {
                                                $('#surveyType option[value=1]').show();
                                                $('#surveyType option[value=2]').show();
                                                //loại lỗi
                                                $('#violationsType option[value=4], #violationsType option[value=5], #violationsType option[value=6], #violationsType option[value=7]').show();
                                                $('#violationsType option[value=9], #violationsType option[value=10]').hide();
                                            }
                                            $('#surveyType').val(t);
                                            $('#surveyType').trigger("change");
                                            $('#surveyType').trigger('chosen:updated');
                                            $('#violationsType').trigger("change");
                                            $('#violationsType').trigger('chosen:updated');
                                        });
                                        ///
                                        $('#surveyType').change(function () {
                                            showHideInput($(this).val());
                                        });
                                        $('#btnSubmit').click(function () {
                                            $('#formsubmit').attr('action', '<?php echo url('/' . $prefix . '/violations-service' . '/index') ?>');
                                            $('#formsubmit').submit();
                                        });
                                        $('#btnExport').click(function () {
                                            $('#formsubmit').attr('action', '<?php echo url('/' . $prefix . '/violations-service' . '/export') ?>');
                                            $('#formsubmit').submit();
                                        });
                                    });
                                    function clicksubmit() {
<?php Session::put('click', 1); ?>
                                        $('#formsubmit').submit();
                                    }
                                    function open_tooltip(id, contract, phone) {
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

                                    function checkVoiceRecord(id) {
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
                                                if (data.state === 'fail') {
                                                    $('#modal-table-record-body').html(data.error);
                                                    return;
                                                }

                                                var a = data.detail;
                                                $('#modal-table-record-body').html(a);
                                            },
                                            error: function () {
                                                $('#modal-table-record-body').html('Lỗi hệ thống');
                                            }
                                        });
                                    }

                                    function stopAll() {
                                        var a = '';
                                        $('#modal-table-record-body').html(a);
                                    }

                                    function init() {
<?php if (!empty($searchCondition['departmentType']) && $searchCondition['departmentType'] != 1) { ?>
                                        $('#surveyType option[value=2]').show();
                                                $('#violationsType option[value=9], #violationsType option[value=10]').hide();
                                                $('#violationsType option[value=4], #violationsType option[value=5], #violationsType option[value=6], #violationsType option[value=7]').show();
<?php } else { ?>
                                        $('#surveyType option[value=2]').hide();
                                                $('#violationsType option[value=9], #violationsType option[value=10]').show();
                                                $('#violationsType option[value=4], #violationsType option[value=5], #violationsType option[value=6], #violationsType option[value=7]').hide();
    <?php
}
if (!empty($searchCondition['type'])) {
    ?>
                                        showHideInput({{$searchCondition['type']}}
                                        );
<?php } ?>
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
                                    function showHideInput(typeSurvey) {
                                    if (typeSurvey == 2) {
                                    $('#div_maintain').show(500);
                                            $('#div_deploy').hide(500);
                                            $('#div_sale').hide(500);
                                            //set lại giá trị mặc định null
                                            $('#CSATPointSale').val('').trigger('chosen:updated');
                                            $('#CSATPointNVTK').val('').trigger('chosen:updated');
                                    } else {
                                    $('#div_maintain').hide(500);
                                            $('#div_deploy').show(500);
                                            $('#div_sale').show(500);
                                            //set lại giá trị mặc định null
                                            $('#CSATPointBT').val('').trigger('chosen:updated');
                                    }
                                    }
                                    function open_violation(status, id, type) {
                                    if (type === 1) {
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
                                    function clickSave() {
                                    $('#formViolations').attr('action', '<?php echo url('/' . $prefix . '/' . $controller . '/save-violation') ?>');
                                            var formData = $('#formViolations').serializeArray(); // data 
                                            $.ajax({
                                            url: '<?php echo url('/' . $prefix . '/' . $controller . '/save-violation') ?>',
                                                    cache: false,
                                                    type: "POST",
                                                    dataType: "json",
                                                    data: {'_token': $('input[name=_token]').val(), 'data': formData},
                                                    success: function (data) {
                                                    $('#' + data.object + data.id).html(data.resStatus);
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