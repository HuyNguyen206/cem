@extends('layouts.appBlank')

@section('content')
<div class="page-content">
	<div class="row">
		<!-- PAGE CONTENT BEGINS -->
		<div class="widget-box" style='width: 600px; margin: auto;'>
			<div class="widget-header widget-header-flat lighter smaller blue center">
				<h4>Xác nhận thông báo khảo sát</h4>
			</div>

			<div class="widget-body">
				<div class="widget-main">
					<div class="">
						<form class="form-horizontal" role="form" method="POST" action="<?php echo url('confirm');?>">
							{!! csrf_field() !!}
							<div class="form-group">
								<div class="col-xs-12">
									{{$warning}}
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- PAGE CONTENT ENDS -->
	</div><!-- /.row -->
</div><!-- /.page-content -->
@stop