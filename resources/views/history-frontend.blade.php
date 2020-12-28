@extends('layouts.app-frontend')

@section('content')
<div class="page-content">
    <?php
    $controller = 'history';
    $transfile = $controller;
    $common = 'common';
    $prefix = main_prefix;
    ?>
    <!-- /.page-header -->
    
    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/'.$prefix.'/'.$controller.'/detail_survey') }}">
                {!! csrf_field() !!}
                <div class="row">
                    <div class="col-xs-12">
                        
                        <div class="space-4"></div>

                        <div class="row">
                            <div class="col-xs-12">

                                <div class="table-responsive">
                                    <table id="sample-table-2" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{trans($transfile.'.Section Account')}}</th>
                                                <th>{{trans($transfile.'.Section Status')}}</th>
                                                <th>{{trans($transfile.'.Section Note')}}</th>
                                                <th>{{trans($transfile.'.Section Action')}}</th>
                                                <th>{{trans($transfile.'.Section Survey')}}</th>
                                                <th>{{trans($transfile.'.Supporter')}}</th>
                                                <th>
                                                    <i class="icon-time bigger-110 hidden-480"></i>
                                                    {{trans($common.'.Create at')}}
                                                </th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach($modelSurveySections as $surveySections)
                                            <tr>
                                                <td>
                                                    <?php echo ($surveySections->section_account_id == "-1") ?"Khách hàng" :"";?>
                                                </td>
                                                <td class="hidden-480">
                                                    <?php echo ($surveySections->section_connected === 0) ? "<span class='label label-warning'>Không liên hệ được KH</span>" : ($surveySections->section_connected === 1 ? "<span class='label label-danger arrowed-in'>KH không đồng ý khảo sát</span>" : "<span class='label label-success arrowed'>KH đồng ý khảo sát</span>") ?>
                                                </td>
                                                <td class="hidden-480">{{$surveySections->section_note}}</td>
                                                <td class="hidden-480">
                                                    <?php echo ($surveySections->section_action === 1) ?'Không làm gì' :($surveySections->section_action === 2 ?'Tạo checklist' :($surveySections->section_action === 3 ?'Tạo checklist INDO' :'Chuyển phòng ban khác'));?>
                                                </td>
                                                <td><strong>{{$surveySections->survey_title}}</strong></td>
                                                <td class="hidden-480">{{$surveySections->name}}</td>
                                                <td class="hidden-480">{{date("d-m-Y H:i:s", strtotime($surveySections->section_time_completed))}}</td>
                                                <td class="hidden-480">
                                                    <?php if($surveySections->section_survey_id > 0) {//chưa gặp người sử dụng  ?>
                                                    <div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
                                                        <a class="open-tooltip" href="#modal-table" role="button" data-toggle="modal" title="Chi Tiết"><span class="badge badge-info">i</span></a>
														<input type="hidden" value="{{$surveySections->section_id}}" name="survey" />
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
    <input type="button" id="btnBack" class="btn-primary" value="Quay về màn hình khảo sát" />
</div><!-- /.page-content -->

<link rel="stylesheet" href="{{asset('assets/css/chosen.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/css/font-awesome.min.css')}}" />

<script src="{{asset('assets/js/jquery-2.0.3.min.js')}}"></script>
<script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>

<script type="text/javascript">
jQuery(function ($) {
    $('#btnBack').click(function(){
        location.href = '<?php echo url('/'); ?>';
    });
    
    $(".chosen-select").chosen({
        no_results_text: "<?php echo trans($common . ".no_results_text_chosen_box"); ?>"
    });

    var oTable1 = $('#sample-table-2').dataTable({
        "aoColumns": [
            {"bSortable": false}, null, null, null, null, null, null, {"bSortable": false}
        ],
        "bJQueryUI": false,
        "oLanguage": {
            "sLengthMenu": "Hiển thị _MENU_ bản tin mỗi trang",
            "sZeroRecords": "Không tìm thấy",
            "sInfo": "Có _START_ tới _END_ của _TOTAL_ bản ghi",
            "sInfoEmpty": "Có 0 tới 0 của 0 bản ghi",
            "sInfoFiltered": "(Lọc từ _MAX_ tổng số bản ghi)",
            "sSearch": "Tìm kiếm"
        }
    });
    
    
    $( ".open-tooltip" ).click(function(){
        $.ajax({
            url: '<?php echo url('/'.$prefix.'/'.$controller.'/detail_survey') ?>',
            cache: false,
            type: "POST",
            dataType: "html",
            data: {'_token': $('input[name=_token]').val(), 'survey': $(this).parent().find('input[name=survey]').val()},
            success: function (data) {
                $('.modal-body').html(data);
            },
        });
    });
    
});
</script>

@stop