@extends('layouts.app')

@section('content')

<div class="page-content">
    <?php
    $controller = 'permissions';
    $title = 'Create new permission';
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

                @include('layouts.alert',['controller' => $controller, 'title' => $title, 'transFile' => $transFile])

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right"> {{trans($transFile.'.Group')}}: </label>

                    <div class="col-sm-9">
                        <div id="newInputGroup" class="col-xs-12 col-sm-5 no-padding" style="display: none;">
                            <input class="col-xs-12" Type="text" name="group0" style="height: 30px;" placeholder="{{trans($transFile.'.Input here')}}"/>
                        </div>
                        <div id="oldSelectGroup" class="col-xs-12 col-sm-5 no-padding">
                            <select name='group1' class="col-xs-12 no-padding-left" title="">
                                @foreach($group as $val)
                                    @if(old('group1') == $val->id )
                                        <option selected="selected" value="{{ $val->id }}">{{$val->display_name }}</option>
                                    @else
                                        <option value="{{ $val->id }}">{{$val->display_name }}</option>
                                    @endif
                                @endforeach
                                @if (empty($group))
                                    <option value="0">{{trans($transFile.'.NoGroupExist')}}</option>
                                @endif
                            </select>
                        </div>
                        <span class="col-xs-12 col-sm-7">
                            <input id="newGroup" type="radio" name="newGroup" @if(old('newGroup') == "1") checked  @endif value="1" onclick="changeGroup()">
                            <label for="newGroup">{{trans($transFile.'.New')}}</label>
                            <input type="radio" name="newGroup" checked  value="0" onclick="changeGroup()" title="">
                            <label for="newGroup">{{trans($transFile.'.Exist')}}</label>
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

                    <label class="col-sm-3 control-label no-padding-right"> {{trans($transFile.'.GroupDescription')}}: </label>

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
                        <input type="text" class="col-xs-10 col-sm-5" name="name" value="{{ old('name') }}" placeholder="{{trans($transFile.'.Input here')}}" autofocus>
                        <span class="help-inline col-xs-12 col-sm-7">
                            <span class="middle">{{trans($transFile.'.format name')}}</span>
                        </span>

                        @if ($errors->has('name'))
                            <span class="col-xs-12 col-sm-12 no-padding-left red">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif	
                    </div>

                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="display_name"> {{trans($transFile.'.Display name')}}:</label>

                    <div class="col-sm-9">
                        <input type="text" class="col-xs-10 col-sm-5" name="display_name" value="{{ old('display_name') }}" placeholder="{{trans($transFile.'.Input here')}}">

                        @if ($errors->has('display_name'))
                            <span class="col-xs-12 col-sm-12 no-padding-left red">
                                <strong>{{ $errors->first('display_name') }}</strong>
                            </span>
                        @endif	
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="description"> {{trans($transFile.'.Description')}}: </label>

                    <div class="col-sm-9">
                        <input type="text" class="col-xs-10 col-sm-5" name="description" value="{{ old('description') }}" placeholder="{{trans($transFile.'.Input here')}}">
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
<script type="text/javascript">
    $(document).ready(function () {
        changeGroup();
    });

    function changeGroup() {
        var check = $('#newGroup').prop('checked');
        if (check == true) {
            $('#newInputGroup').show();
            $('#newDescriptionGroup').show();
            $('#oldSelectGroup').hide();

        } else {
            $('#newInputGroup').hide();
            $('#newDescriptionGroup').hide();
            $('#oldSelectGroup').show();
        }
    }


</script>
@endsection