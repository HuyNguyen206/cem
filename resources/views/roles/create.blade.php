@extends('layouts.app')

@section('content')
<div class="page-content">
    <?php
    $controller = 'roles';
    $title = 'Create new role';
    $transFile = $controller;
    $common = 'common';
    ?>
    @include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])
    <!-- /.page-header -->

        
    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            
            <form class="form-horizontal" role="form" method="POST" action="{{ url(main_prefix.'/'.$controller) }}">
                {!! csrf_field() !!}

                @include('layouts.alert')

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right">{{trans($transFile.'.Group')}}: </label>

                    <div class="col-sm-9">
                        <div id="newInputGroup" class="col-xs-12 col-sm-5 no-padding" style="display: none;">
                            <input class="col-xs-12" Type="text" name="group0" style="height: 30px;" placeholder="{{trans($transFile.'.Input here')}}"/>
                        </div>
                        
                        <div id="oldSelectGroup" class="col-xs-12 col-sm-5 no-padding">
                            <select name="group1[]" multiple="" class="col-xs-12 no-padding-left chosen-select" id="form-field-select-4" data-placeholder="{{empty($group)? trans($transFile.'.NoGroupExist') : trans($transFile.'.PleaseChooseOneGroup')}}">
                                @foreach($group as $val)
                                    @if(!empty(old('group1')) && array_search($val->id, old('group1')) !== false)
                                        <option selected="selected" value="{{ $val->id }}">{{$val->display_name }}</option>
                                    @else
                                        <option value="{{$val->id }}">{{$val->display_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <span class="col-xs-12 col-sm-7">
                            <input type="radio" name="newGroup" checked  value="0" onclick="changeGroup(this)">
                            <label for="newGroup">{{trans($transFile.'.Exist')}}</label>
                            <input id="newGroup" type="radio" name="newGroup" @if(old('newGroup') == "1") checked  @endif value="1" onclick="changeGroup(this)">
                            <label for="newGroup">{{trans($transFile.'.New')}}</label>
                        </span>
                        @if ($errors->has('newGroup'))
                            <span class="col-xs-12 col-sm-12 no-padding-left red">
                                <strong>{{ $errors->first('newGroup') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div id="newDescriptionGroup" class="form-group" style="display: none;">
                    <div class="space-4"></div>

                    <label class="col-sm-3 control-label no-padding-right">{{trans($transFile.'.GroupDescription')}}: </label>

                    <div class="col-sm-9">
                        <div class="col-xs-12 col-sm-5 no-padding">
                            <input class="col-xs-12" Type="text" name="descriptionGroup" placeholder="{{trans($transFile.'.Input here')}}"/>
                        </div>
                        <span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle">{{trans($common.'.Can leave empty')}}</span>
                        </span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="name"> {{trans($transFile.'.Name')}}:</label>

                    <div class="col-sm-9">
                        <input type="text" class="col-xs-12 col-sm-5" name="name" value="{{ old('name') }}" placeholder="{{trans($transFile.'.Input here')}}" oninvalid="this.setCustomValidity('{{trans($transFile.'.require name')}}')" oninput="setCustomValidity('')">
                        @if ($errors->has('name'))
                            <span class="col-xs-12 col-sm-12 no-padding-left red">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif	
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="description"> {{trans($transFile.'.Description')}}: </label>

                    <div class="col-sm-9">
                        <input type="text" class="col-xs-12 col-sm-5" name="description" value="{{ old('description') }}" placeholder="{{trans($transFile.'.Input here')}}">
                        <span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle">{{trans($common.'.Can leave empty')}}</span>
                        </span>
                        @if ($errors->has('description'))
                            <span class="col-xs-12 col-sm-12 no-padding-left red">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="level">{{trans($transFile.'.Level')}}: </label>

                    <div class="col-sm-9">
                        <div class="col-xs-12 col-sm-5 no-padding">
                            <select name='level' class="col-xs-12 no-padding-left" id="form-field-select-1">
                                @foreach($role as $val)
                                    @if(old('level') == $val->level)
                                        <option selected="selected" value="{{ $val->level }}">{{$val->level.' - '. $val->display_name }}</option>
                                    @else
                                        <option value="{{ $val->level }}">{{$val->level.' - '.$val->display_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <span class="col-xs-12 col-sm-7">
                            <input type="radio" name="rate" checked value="0">
                            <label for="rate">{{trans($transFile.'.Lower')}}</label>
                            <input type="radio" name="rate" @if(old('rate') == "1") checked  @endif value="1">
                            <label for="rate">{{trans($transFile.'.Equal')}}</label>
                        </span>
                        @if ($errors->has('level'))
                            <span class="col-xs-12 col-sm-12 no-padding-left red">
                                <strong>{{ $errors->first('level') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-info" type="submit">
                            <i class="icon-ok bigger-110"></i>
                            {{trans($common.'.Create')}}
                        </button>

                        &nbsp; &nbsp; &nbsp;
                        <button class="btn" type="reset">
                            <i class="icon-undo bigger-110"></i>
                            {{trans($common.'.Reset')}}
                        </button>
                    </div>
                </div>
            </form>

            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.page-content -->

<link rel="stylesheet" href="{{asset('assets/css/chosen.css')}}" type="text/css">
<script type="text/javascript" src="{{asset('assets/js/chosen.jquery.min.js')}}"></script>


<script type="text/javascript">
    $(document).ready(function () {
        $(".chosen-select").chosen();
        $('#chosen-multiple-style').on('click', function (e) {
            var target = $(e.target).find('input[type=radio]');
            var which = parseInt(target.val());
            if (which === 2)
                $('#form-field-select-4').addClass('tag-input-style');
            else
                $('#form-field-select-4').removeClass('tag-input-style');
        });
        changeGroup();
    });

    function changeGroup(radio) {
        var check = $('#newGroup').prop('checked');
        if (check === true) {
            $('#newInputGroup').show();
            $('#newDescriptionGroup').show();
            $('#oldSelectGroup').hide();
        } else {
            $('#newInputGroup').hide();
            $('#newDescriptionGroup').hide();
            $('#oldSelectGroup').show();
        }
        $('#form_field_select_4_chosen').attr('style','width: 100%');
    }

    
</script>
@endsection