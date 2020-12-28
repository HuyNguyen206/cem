@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
    $controller = 'authens';
    $title = 'List users - permission';
    $transFile = $controller;
    $common = 'common';
    ?>
    @include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])
    <!-- /.page-header -->

    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <form class="form-horizontal" role="form" method="POST" action="{{ url(main_prefix.'/'.$controller.'/view-user-permission') }}">
                {!! csrf_field() !!}

                @include('layouts.alert')

                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <label style="padding-top: 7px;" for="form-field-select-3">{{trans('users.Users')}}:</label>
                                <!--<br />-->
                                <input type="text" id="baseUserName" name="baseUserName"/>
                                <input type="hidden" id="baseUser" name="baseUser"/>
                                <input type="button" value="Kiểm tra" style="height: 28px;" onclick="checkPersonRole()"/>
                                <img id="loading" src="{{asset("assets/img/loading.gif")}}" style="display: none;">
                            </div>
                            
                            <div class="col-xs-12">
                                <label style="padding-top: 7px;" for="form-field-select-3">{{trans('roles.Roles')}}:</label>
                                <!--<br />-->
                                <select class="width-25 chosen-select" id="baseRole" name="baseRole">
                                    @foreach($roles as $role)
                                        <option @if(session('oldbaseRole') == $role['id']) selected @endif value="{{$role['id']}}" >{{$role['display_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="space-10"></div>
                            <div class='col-xs-12'>
                                <label>Nhóm quyền hạn:</label>
                                <input type='button' value='Hiển thị chi tiết' onclick="showGroupDetail()"/>
                            </div>
                            <?php $i = 1;?>
                            @foreach($allPermission as $val)
                            <div class="col-xs-4 GroupPermissionDetail" style='display: none;'>
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class='center'><h4>{{isset($val['groupName'])? $val['groupName']: 'Chưa xác định'}}</h4></th>
                                        </tr>
                                        <tr>
                                            <th class="center">
                                                <label>
                                                    <input id='group-{{$val['groupId']}}' type="checkbox" class="ace" />
                                                    <span class="lbl"></span>
                                                </label>
                                            </th>
                                            <th>Tên quyền hạn</th>
                                            <th>Mô tả quyền hạn</th>
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
                                                <input type="button" value="Hiển thị thêm" onclick="showGroup('{{'hideGroup'.$val['groupId']}}')"/>
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
                                <label>Vùng miền:</label>
                                <input type='button' value='Hiển thị chi tiết' onclick="showBrandDetail()"/>
                            </div>
                        </div>
                        <div class='row'>
                            <?php $i = 1; ?>
                            @foreach($zone as $key => $val)
                            <div class="col-xs-4 BrandDetail" style="display: none;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class='center'><h4>{{$val['zone_name']}}</h4></th>
                                        </tr>
                                        <tr>
                                            <th class="center">
                                                <label>
                                                    <input id='zone-{{$val['zone_id']}}' type="checkbox" class="ace" />
                                                    <span class="lbl"></span>
                                                </label>
                                            </th>
                                            <th>Chi nhánh</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $row = 1; ?>
                                        @foreach($brand as $values)
                                            @if($values->region === $val['zone_name'])
                                                @if(!empty($values->branchcode))
                                                    <tr @if($row >= 4) class="hideBrand{{$val['zone_id']}}" style="display:none;" @endif >
                                                        <td class="center">
                                                            <label>
                                                                <input type="checkbox" class="ace" id="{{'area-'. $values->area_id_plus .'plus'}}" name="zone_{{$val['zone_id']}}[]" value="{{$values->area_id_plus.':'.$values->id}}"/>
                                                                <span class="lbl"></span>
                                                            </label>
                                                        </td>

                                                        <td><strong>{{$values->area_name_plus}}</strong></td>
                                                    </tr>
                                                @else
                                                    <tr @if($row >= 4) class="hideBrand{{$val['zone_id']}}" style="display:none;" @endif >
                                                        <td class="center">
                                                            <label>
                                                                <input type="checkbox" class="ace" id="{{'area-'.$values->id}}" name="zone_{{$val['zone_id']}}[]" value="{{$values->id}}"/>
                                                                <span class="lbl"></span>
                                                            </label>
                                                        </td>

                                                        <td><strong>{{$values->name}}</strong></td>
                                                    </tr>
                                                @endif
                                                <?php $row++; ?>
                                            @endif
                                        @endforeach
                                        @if($row >= 4)
                                            <tr>
                                                <td class="center" colspan="3">
                                                    <input type="button" value="Hiển thị thêm" onclick="showBrand('{{'hideBrand'.$val['zone_id']}}')"/>
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

                        <div class="clearfix form-actions">
                            <div class="col-md-offset-3 col-md-9">
                                <button class="btn btn-info" type="submit">
                                    <i class="icon-ok bigger-110"></i>
                                    {{trans($common.'.Save')}}
                                </button>

                                &nbsp; &nbsp; &nbsp;
                                <a href="{{ url(main_prefix.'/'.$controller.'/view-user-permission') }}">
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
    var userPer = [<?php
        if(!empty($userPermission['permission'])){
            foreach ($userPermission['permission'] as $val) {
                echo '"'. $userPermission['id'].'@per-' . $val['id'] . '",';
            }
        } ?>
    ];

    var groupPer = [<?php
        foreach ($allPermission as $val) {
            if(!empty($val['permission'])){
                foreach($val['permission'] as $permission){
                    echo '"'.$val['groupId'].'@per-' .$permission['id'] . '",';
                }
            }
        } ?>
    ];

    var rolePer = [<?php
        foreach($perrole as $val){
            echo '"'.$val->role_id.'@per-' .$val->permission_id . '",';
        }?>
    ];

    var brand = [<?php
        foreach ($brand as $val) {
            if(isset($val->branchcode)){
                echo '"area-' . $val->area_id_plus . 'plus",';
            }else{
                echo '"area-' . $val->id . '",';
            }
        } ?>
    ];

    var zone = [<?php foreach ($zone as $val) {echo '"zone-' . $val['zone_id'] . '",';} ?>];

    var userarea = [<?php
        foreach ($allUserRole as $val) {
            if(!empty($val['user_brand'])){
                $brands = json_decode($val['user_brand']);
                foreach($brands as $brand){
                    echo '"'.$val['id'].'@area-' . $brand . '",';
                }
            }
        } ?>
    ];

    var userzone = [<?php
        foreach ($allUserRole as $val) {
            if(!empty($val['user_zone'])){
                $brands = json_decode($val['user_zone']);
                foreach($brands as $brand){
                    echo '"'.$val['id'].'@zone-' . $brand . '",';
                }
            }
        }?>
    ];

    var userbrandplus = [<?php
        foreach ($allUserRole as $val) {
            if(!empty($val['user_brand_plus'])){
                $brands = json_decode($val['user_brand_plus']);
                foreach($brands as $brand){
                    echo '"'.$val['id'].'@area-' . $brand . 'plus",';
                }
            }
        }?>
    ];

    var userrole = [<?php
        foreach($allUserRole as $val){
            echo '"'.$val['role_id'].'@' . $val['id'] . '",';
        }?>
    ];

    function checkPersonRole(){
        var name = $("#baseUserName").val();
        $('#loading').prop('src', '{{asset("assets/img/loading.gif")}}');
        $('#loading').show();
        $.ajax({
            url: '<?php echo url('/' . main_prefix . '/' . $controller . '/checkPersonRole') ?>',
            cache: false,
            type: "POST",
            dataType: "json",
            data: {'_token': $('input[name=_token]').val(), 'name': name},
            success: function (data) {
                if(data !== null){
                    changePerson(data);
                    $("#baseUser").val(data.id);
                    $('#loading').prop('src', '{{asset("assets/img/yes.png")}}');
                }else{
                    $('#loading').prop('src', '{{asset("assets/img/no.png")}}');
                }
            },
            error: function(data){
                $('#loading').prop('src', '{{asset("assets/img/no.png")}}');
            },
        });
    }

    function changePerson(data){
        var user = data.id;
        var i;
        var j;
        for(i = 0; i < groupPer.length; i++){
            var temp = groupPer[i].split('@');
            $("#group-" + temp[0]).removeAttr('checked');
            $("#" + temp[1]).removeAttr('checked');
        }

        for (i = 0; i < data.permission.length; i++) {
            var temp = "per-" + data.permission[i].id;
            $("#" + temp).prop('checked', 'checked');
            for(j = 0; j < groupPer.length; j++){
                var temp2 = groupPer[j].split('@');
                if(temp2[1] == temp){
                    $("#group-" + temp2[0]).prop('checked', 'checked');
                }
            }
        }

        for (i = 0; i < brand.length; i++) {
            $("#" + brand[i]).removeAttr('checked');
        }

        for (i = 0; i < zone.length; i++) {
            $("#" + zone[i]).removeAttr('checked');
        }

        for (i = 0; i < userzone.length; i++) {
            var temp = userzone[i].split('@');
            if(temp[0] == user){
                $("#" + temp[1]).prop('checked', 'checked');
            }
        }

        for (i = 0; i < userarea.length; i++) {
            var temp = userarea[i].split('@');
            if(temp[0] == user){
                $("#" + temp[1]).prop('checked', 'checked');
            }
        }

        for (i = 0; i < userbrandplus.length; i++) {
            var temp = userbrandplus[i].split('@');
            if(temp[0] == user){
                $("#" + temp[1]).prop('checked', 'checked');
            }
        }

        var sel = 1;
        for(i = 0; i < userrole.length;i++){
            var temp = userrole[i].split('@');
            if(temp[1] == user){
                sel = temp[0];
            }
        }

        //update select thông thường
        $('#baseRole option').removeAttr('selected');
        $('#baseRole option[value='+ sel + ']').prop('selected',true);

        //update chosen selected
        $('#baseRole').trigger('chosen:updated');
    }

    function showGroup(id){
        $("."+id).toggle();
    }
    function showBrand(id){
        $("."+id).toggle();
    }

    function showGroupDetail(){
        $(".GroupPermissionDetail").toggle();
    }

    function showBrandDetail(){
        $(".BrandDetail").toggle();
    }

    jQuery(function ($) {
        $(".chosen-select").chosen({
            no_results_text: "<?php echo trans($common . ".no_results_text_chosen_box"); ?>"
        });
        
        $("#baseRole").change(function() {
            var roleId = $('#baseRole').val();
            var i;
            var j;

            for(i = 0; i < groupPer.length; i++){
                var temp = groupPer[i].split('@');
                $("#group-" + temp[0]).removeAttr('checked');
                $("#" + temp[1]).removeAttr('checked');
            }
            
            for (i = 0; i < userPer.length; i++) {
                for(j = 0; j < rolePer.length; j++){
                    var temp1 = userPer[i].split('@');
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
        });

        $("#baseRole").trigger("change");
    });
</script>

@stop