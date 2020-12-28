@extends('layouts.appError')

@section('content')
    <div class="page-content">
        <?php 
            $permission = session('permission');
        ?>
        <div class="title">{{trans('error.AccessDenied')}}</div>
        
        <div class="center">
            <img width="20%" src="{{asset('assets/img/no_access.jpg')}}" />
        </div>
        
        <div class="center">
            @if(empty($permission))
                <h3>{{trans('error.YouDoNotHavePermissionForTheseAction')}}</h3>
            @else
                <h3>{{trans('error.YouDoNotHavePermissionForAction').' '. strtolower($permission[0]->description)}}</h3>
            @endif
        </div>
    </div>
<style>
    .page-content {
        height: 100%;
        text-align: center;
        display: inline-block;
        margin: 0;
        padding: 0;
        width: 100%;
        color: #B0BEC5;
        display: table;
        font-weight: 100;
        font-family: 'Lato';
    }

    .title {
        font-size: 72px;
        margin-bottom: 40px;
    }
</style>
@stop