@extends('layouts.app')

@section('content')
<div class="page-content">
	<?php 
		$controller = 'users'; 
		$title = 'List users';
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
									<th class="center" style='width: 8%;'>{{trans($common.'.Number')}}</th>
									<th class="visible-md visible-lg visible-sm hidden-xs" style='width: 10%;'>{{trans($transFile.'.Name')}}</th>
									<th>{{trans($transFile.'.Email')}}</th>
									
									<th class='width-20 visible-md visible-lg hidden-sm hidden-xs'>
										<i class="icon-star-half-full bigger-110 hidden-480"></i>
										{{trans($transFile.'.Level')}}
									</th>
									<th class='width-20 visible-md visible-lg hidden-sm hidden-xs'>
										<i class="icon-time bigger-110 hidden-480"></i>
										{{trans($common.'.CreatedAt')}}
									</th>
								</tr>
							</thead>

							<tbody>
								<?php $i = 1;?>
								@foreach($data as $user)
									<tr>
										<td class="center">{{$i++}}</td>
										<td class="visible-md visible-lg visible-sm hidden-xs">
											<strong>{{$user->name}}</strong>
										</td>
										<td>{{$user->email}}</td>
										<td class='visible-md visible-lg hidden-sm hidden-xs'>
											@if($user->level == '1')
												{{trans($transFile.'.Highest')}}
											@elseif($user->level > 1 && $user->level < 4)
												{{trans($transFile.'.High')}}
											@elseif($user->level == '4')
												{{trans($transFile.'.Medium')}}
											@elseif($user->level > '4' && $user->level < 10)
												{{trans($transFile.'.Low')}}
											@else
												{{trans($transFile.'.Lowest')}}
											@endif
										</td>
										<td class='visible-md visible-lg hidden-sm hidden-xs'>{{$user->created_at}}</td>
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
<script src="{{asset('assets/js/jquery.gritter.min.js')}}"></script>

@include('help.TranslateDatatableNotCheck')

<script type="text/javascript">
	function Delete(id){
		$('#action_process_alert').hide(300);
		$('#action_process_success').hide(300);
		$('#action_process_fail').hide(300);
		$.ajax({
			url: '{{url('/'.main_prefix.'/'.$controller).'/'}}' + id,
			cache: false,
			type: "delete",
			dataType: "json",
			data: {'_token':'{{csrf_token()}}'},
			success: function (data) {
				if(data.state === 'alert'){
					$('#action_process_alert_message').html(data.error);
					$('#action_process_alert').show(300);
				}
				else if(data.state === 'success'){
					window.location='{{url('/'.main_prefix.'/'.$controller)}}';
				}else{
					$('#action_process_fail_message').html(data.error);
					$('#action_process_fail').show(300);
				}
			},
			error: function(data){
				if(data.status === 200){
					location.href = '{{url('/error/auth')}}';
				}
			},
		});
	}
	
	function DeleteConfirm(id, name){
		var mess = '{{trans($transFile.'.User')}}<b class="red"> ' + name + ' </b>{{trans($transFile.'.will be deleted')}}';
		$('#delete_confirm_message').html(mess);
		$( "#delete-confirm" ).removeClass('hide').dialog({
			resizable: false,
			modal: true,
			title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i> {{trans($transFile.'.You wanna delete user')}}?</h4></div>",
			title_html: true,
			buttons: [
				{
					html: "<i class='icon-ok bigger-110'></i>&nbsp; {{trans('common.Delete')}}",
					"class" : "btn btn-danger btn-xs",
					click: function() {
						Delete(id);
						$( this ).dialog( "close" );
					}
				}
				,
				{
					html: "<i class='icon-remove bigger-110'></i>&nbsp; {{trans('common.Cancel')}}",
					"class" : "btn btn-xs",
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});
	}
	
</script>

@stop