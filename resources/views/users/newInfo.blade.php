@extends('layouts.appNotBreadcrumb')

@section('content')
<div class="page-content">
	<?php 
		$controller = 'users'; 
		$title = 'New user';
		$transFile = $controller;
		$common = 'common';
	?>
	@include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])
	<!-- /.page-header -->
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<h4>{{trans($transFile.'.YouMustContactToYourManagerToHavePermissionToAccessOurSystem')}}</h4>
			<!-- PAGE CONTENT ENDS -->
		</div><!-- /.col -->
	</div><!-- /.row -->
</div><!-- /.page-content -->

@stop