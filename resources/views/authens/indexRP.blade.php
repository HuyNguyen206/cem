@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
    $controller = 'authens';
    $title = 'List roles - permissions';
    $transFile = $controller;
    $common = 'common';
    ?>
    @include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])
    <!-- /.page-header -->

    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <form class="form-horizontal" role="form" method="POST" action="{{ url(main_prefix.'/'.$controller.'/view-role-permission') }}">
                {!! csrf_field() !!}

                @include('layouts.alert')

                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <label style="padding-top: 7px;" for="form-field-select-3">{{trans('roles.Roles')}}:</label>
                                <!--<br />-->
                                <select class="width-25 chosen-select" id="baseRole" name="baseRole">
                                    @foreach($roles as $role)
                                        <option @if(session('oldbaseRole') == $role['id']) selected @endif value="{{$role['id']}}">{{$role['display_name']}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-10"></div>
                            <div class='col-xs-12'>
                                <label>{{trans($transFile.'.PermissionGroup')}}:</label>
                                <input type='button' value='{{trans($transFile.'.MoreDetails')}}' onclick="showGroupDetail()"/>
                            </div>
                            <?php $i = 1;?>
                            @foreach($allPermission as $val)
                                <div class="col-xs-4 GroupPermissionDetail" style='display: none;'>
                                    <table class="table table-bordered ">
                                        <thead>
                                        <tr>
                                            <th colspan="3" class='center'><h4>{{isset($val['groupName'])? $val['groupName']: trans($transFile.'.Undefined')}}</h4></th>
                                        </tr>
                                        <tr>
                                            <th class="center">
                                                <label>
                                                    <input id='group-{{$val['groupId']}}' type="checkbox" class="ace" />
                                                    <span class="lbl"></span>
                                                </label>
                                            </th>
                                            <th>{{trans($transFile.'.PermissionName')}}</th>
                                            <th>{{trans($transFile.'.PermissionDescription')}}</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php $row = 1;?>
                                        @foreach($val['permission'] as $values)
                                            <?php $canAction = array_search($values['name'], array_column($userPermission['permission'], 'name')); ?>
                                            @if($canAction !== false)
                                                <tr @if($row >= 4) class="hideGroup{{$val['groupId']}}" style="display:none;" @endif >
                                                    <td class="center">
                                                        <label>
                                                            <input type="checkbox" class="ace" id="{{'per-'.$values['id']}}" name="per_{{$val['groupId']}}[]" value="{{$values['id']}}"/>
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </td>
                                                    <td><strong>{{$values['display_name']}}</strong></td>
                                                    <td><strong>{{$values['description']}}</strong></td>
                                                </tr>
                                                <?php $row++; ?>
                                            @endif
                                        @endforeach

                                        @if($row >= 4)
                                            <tr>
                                                <td class="center" colspan="3">
                                                    <input type="button" value="{{trans($transFile.'.MoreDetails')}}" onclick="showGroup('{{'hideGroup'.$val['groupId']}}')"/>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>

                                @if($i % 3 == 0)
                                    <div class="row"></div>
                                @endif
                                <?php $i++; ?>
                            @endforeach
                        </div>

                        <div class='row'>
                            <div class='col-xs-12'>
                                <label>{{trans($transFile.'.Departments')}}:</label>
                                <input type='button' value='{{trans($transFile.'.MoreDetails')}}' onclick="showDepartmentDetail()"/>
                            </div>
                            <div class="col-xs-4 GroupDepartmentDetail" style='display: none;'>
                                <table class="table table-bordered ">
                                    <thead>
                                    <tr>
                                        <th colspan="2" class='center'><h4>{{trans($transFile.'.Departments')}}</h4></th>
                                    </tr>
                                    <tr>
                                        <th class="center">
                                            <label>
                                                <input id='department' type="checkbox" class="ace" />
                                                <span class="lbl"></span>
                                            </label>
                                        </th>
                                        <th>{{trans($transFile.'.DepartmentName')}}</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($departments as $values)
                                        <tr>
                                            <td class="center">
                                                <label>
                                                    <input type="checkbox" class="ace" id="{{'dep-'.$values['id']}}" name="department[]" value="{{$values['id']}}"/>
                                                    <span class="lbl"></span>
                                                </label>
                                            </td>
                                            <td><strong>{{$values['name']}}</strong></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-md-offset-3 col-md-9">
                                <button class="btn btn-info" type="submit">
                                    <i class="icon-ok bigger-110"></i>
                                    {{trans($common.'.Save')}}
                                </button>

                                &nbsp; &nbsp; &nbsp;
                                <a href="{{ url(main_prefix.'/'.$controller.'/view-role-permission') }}">
                                    <button class="btn" type="button">
                                        <i class="icon-undo bigger-110"></i>
                                        {{trans($common.'.Reset')}}
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div><!-- /span -->
                </div><!-- /row -->

            </form>
                <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.page-content -->

<link rel="stylesheet" href="{{asset('assets/css/chosen.min.css')}}" />

<script src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
@include('help.TranslateDatatableFullView')

<script type="text/javascript">
    jQuery(function ($) {
        $(".chosen-select").chosen({
            no_results_text: "<?php echo trans($common . ".no_results_text_chosen_box"); ?>"
        });
        
        var per = [<?php 
            if(!empty($userPermission['permission'])){
                foreach ($userPermission['permission'] as $val) {
                    echo '"'. $userPermission['id'].'@per-' . $val['id'] . '",';
                }    
            } ?>];               		
        var groupPer = [<?php foreach ($allPermission as $val) {
            if(!empty($val['permission'])){
                foreach($val['permission'] as $permission){
                    echo '"'.$val['groupId'].'@per-' .$permission['id'] . '",';
                }
            }
        } ?>];           
        var rolePer = [<?php foreach($permissionRole as $val){
            echo '"'.$val->role_id.'@per-' .$val->permission_id . '",';
        }?>];                    

        var department = [<?php foreach($departments as $val){
            echo '"'.$val['id'].'",';
        }?>];

        var departmentRole = [<?php foreach($departmentRole as $val){
            echo '"'.$val->role_id.'@dep-'.$val->department_id.'",';
        }?>];

        $("#baseRole").change(function() {
            var roleId = $('#baseRole').val();
            for (var i = 0; i < per.length; i++) {
                var temp = per[i].split('@');
                $("#" + temp[1]).removeAttr('checked');
            }
            
            for(var s = 0; s < groupPer.length; s++){
                var temp = groupPer[s].split('@');
                $("#group-" + temp[0]).removeAttr('checked');
            }

            for(var j = 0; j < department.length;j++){
                $("#dep-" + department[j]).removeAttr('checked');
            }
            $("#department").removeAttr('checked');

            for (i = 0; i < per.length; i++) {
                for(j = 0; j < rolePer.length; j++){
                    var temp1 = per[i].split('@');
                    var temp2= rolePer[j].split('@');
                    if(temp1[1] === temp2[1]){
                        if(temp2[0] === roleId){
                            $("#" + temp2[1]).prop('checked', 'checked');
                            for(s = 0; s < groupPer.length; s++){
                                var temp3 = groupPer[s].split('@');
                                if(temp3[1] === temp2[1]){
                                    $("#group-" + temp3[0]).prop('checked', 'checked');
                                }
                            }
                        }
                    }
                }
            }

            var on = false;
            for(i = 0; i < departmentRole.length;i++){
                var temp = departmentRole[i].split('@');
                if(temp[0] === roleId){
                    $("#" + temp[1]).prop('checked', 'checked');
                    on = true;
                }
            }
            if(on === true){
                $("#department").prop('checked', 'checked');
            }
        });
        
        $("#baseRole").trigger("change");
        
    });
    
    function showGroup(id){
        $("."+id).toggle();
    }

    function showGroupDetail(){
        $(".GroupPermissionDetail").toggle();
    }

    function showDepartmentDetail(){
        $(".GroupDepartmentDetail").toggle();
    }
</script>

@stop