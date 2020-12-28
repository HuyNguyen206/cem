@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
    $controller = 'roles';
    $title = 'List roles';
    $transFile = $controller;
    $common = 'common';
    ?>
    @include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])
    <!-- /.page-header -->
    @include('layouts.modal.deleteConfirm')
    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->

            @include('layouts.alert')
            <div class="row">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="sample-table-2" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="center">{{trans($common.'.Number')}}</th>
                                    <th>{{trans($transFile.'.Name')}}</th>
                                    <th>{{trans($transFile.'.Description')}}</th>
                                    <th class='visible-md visible-lg hidden-sm hidden-xs'>
                                        <i class="icon-star-half-full bigger-110 hidden-480"></i>
                                        {{trans($transFile.'.Level')}}
                                    </th>
                                    <th>
                                        <i class="icon-time bigger-110 hidden-480"></i>
                                        {{trans($common.'.CreatedAt')}}
                                    </th>
                                    <th>{{trans($common.'.Action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $i = 1; ?>
                                @foreach($role as $val)
                                <tr>
                                    <td class="center">{{$i++}}</td>
                                    <td>
                                        <strong>{{$val->name}}</strong>
                                    </td>
                                    <td>{{$val->description}}</td>
                                    <td class='visible-md visible-lg hidden-sm hidden-xs'>
                                        {{$val->level}}
                                    </td>
                                    <td>{{$val->created_at}}</td>


                                    <td>
                                        <div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
                                            <a class="no-underline" href="{{url(main_prefix . '/' . $controller . '/' . $val->id . '/edit/')}}">
                                                <button class="btn btn-xs btn-info">
                                                    <i class="icon-edit bigger-120"></i>
                                                </button>
                                            </a>
                                        </div>

                                        <div class="visible-xs visible-sm hidden-md hidden-lg">
                                            <div class="inline position-relative">
                                                <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-cog icon-only bigger-110"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-only-icon dropdown-yellow pull-right dropdown-caret dropdown-close">
                                                    <li>
                                                        <a href="{{url(main_prefix . '/' . $controller . '/' . $val->id . '/edit/')}}" class="tooltip-success" data-rel="tooltip" title="{{trans($transFile.'.Edit')}}">
                                                            <span class="green">
                                                                <i class="icon-edit bigger-120"></i>
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div><!-- /span -->
            </div><!-- /row -->

            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.page-content -->

<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
<link rel="stylesheet" href="{{asset('assets/css/jquery-ui-1.10.3.full.min.css')}}" />
<script src="{{asset('assets/js/jquery-ui-1.10.3.full.min.js')}}"></script>
@include('help.TranslateDatatableNotCheck')

<script type="text/javascript">
$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
    _title: function (title) {
        var $title = this.options.title || '&nbsp;'
        if (("title_html" in this.options) && this.options.title_html == true)
            title.html($title);
        else
            title.text($title);
    }
}));

function Delete(id) {
    $('.alert').hide(300);
    $('#action_process_alert').hide(300);
    $('#action_process_success').hide(300);
    $('#action_process_fail').hide(300);
    $.ajax({
        url: '{{url('/' . main_prefix . '/' . $controller) . '/'}}' + id,
        cache: false,
        type: "delete",
        dataType: "json",
        data: {'_token': '{{csrf_token()}}'},
        success: function (data) {
            if (data.state === 'alert') {
                $('#action_process_alert_message').html(data.error);
                $('#action_process_alert').show(300);
            }
            else if (data.state === 'success') {
                window.location = '{{url('/' . main_prefix . '/' . $controller)}}';
            } else {
                $('#action_process_fail_message').html(data.error);
                $('#action_process_fail').show(300);
            }
        },
        error: function (error) {
            if (error.status === 200) {
                location.href = '{{url('/error/auth')}}';
            }
        }
    });
}

function DeleteConfirm(id, name) {
    var mess = '{{'Vai trò'}}<b class="red"> ' + name + ' </b>{{'sẽ bị xóa và không thể khôi phục'}}!';
    $('#delete_confirm_message').html(mess);
    $("#delete-confirm").removeClass('hide').dialog({
        resizable: false,
        modal: true,
        title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i> {{trans($transFile . '.You wanna delete role')}}?</h4></div>",
        title_html: true,
        buttons: [
            {
                html: "<i class='icon-ok bigger-110'></i>&nbsp; {{trans('common.Delete')}}",
                "class": "btn btn-danger btn-xs",
                click: function () {
                    Delete(id);
                    $(this).dialog("close");
                }
            }
            ,
            {
                html: "<i class='icon-remove bigger-110'></i>&nbsp; {{trans('common.Cancel')}}",
                "class": "btn btn-xs",
                click: function () {
                    $(this).dialog("close");
                }
            }
        ]
    });
}

</script>

@stop