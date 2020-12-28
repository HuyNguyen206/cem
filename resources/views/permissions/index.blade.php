@extends('layouts.app')

@section('content')
<div class="page-content">
	<?php 
		$controller = 'permissions';
		$title = 'List permissions';
		$transFile = $controller;
		$common = 'common';
	?>
	@include('layouts.pageHeader', ['controller' => $controller, 'title' => $title, 'transFile' => $transFile])
	<!-- /.page-header -->

	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->

			<div class="row">
				<div class="col-xs-12">
					<div class="table-responsive">
						<table id="sample-table-2" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th class="center">{{trans($common.'.Number')}}</th>
									<th>{{trans($transFile.'.Name')}}</th>
									<th>{{trans($transFile.'.Description')}}</th>
									<th>
										<i class="icon-time bigger-110 hidden-480"></i>
										{{trans($common.'.CreatedAt')}}
									</th>
									<th>
										<i class="icon-time bigger-110 hidden-480"></i>
										{{trans($common.'.UpdatedAt')}}
									</th>
									<th>
										<i class="icon-time bigger-110 hidden-480"></i>
										{{trans($common.'.Action')}}
									</th>
								</tr>
							</thead>

							<tbody>
								<?php $i = 1; ?>
								@foreach($data as $permission)
								<tr>
									<td class="center">{{$i++}}</td>
									<td>
										<strong>{{$permission->display_name}}</strong>
									</td>
									<td>{{$permission->description}}</td>
									<td>{{$permission->created_at}}</td>
									<td>{{$permission->updated_at}}</td>
									<td>
										<div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
											<a class="no-underline" href="{{url(main_prefix.'/'.$controller.'/'.$permission->id.'/edit/')}}">
												<button class="btn btn-xs btn-info">
													<i class="icon-edit bigger-120"></i>
												</button>
											</a>
											<a class="no-underline" onclick="DeleteConfirm({{$permission->id.",'".$permission->display_name."'"}})">
												<button class="btn btn-xs btn-danger">
													<i class="icon-remove bigger-120"></i>
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
														<a href="{{url(main_prefix.'/'.$controller.'/'.$permission->id.'/edit/')}}" class="tooltip-success" data-rel="tooltip" title="{{trans($transFile.'.Edit')}}">
															<span class="green">
																<i class="icon-edit bigger-120"></i>
															</span>
														</a>
													</li>
													<li>
														<a class="tooltip-error" data-rel="tooltip" title="Edit" onclick="DeleteConfirm({{$permission->id.",'".$permission->display_name."'"}})">
															<span class="red">
																<i class="icon-remove bigger-120"></i>
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
					</div>
				</div><!-- /span -->
			</div><!-- /row -->
			
			<!-- PAGE CONTENT ENDS -->
		</div><!-- /.col -->
	</div><!-- /.row -->
</div><!-- /.page-content -->

<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.dataTables.bootstrap.js')}}"></script>
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
            }
        });
    }

    function DeleteConfirm(id, name){
        var mess = '{{trans($transFile.'.Permission')}}<b class="red"> ' + name + ' </b>{{trans($transFile.'.will be deleted')}}!';
        $('#delete_confirm_message').html(mess);
        $( "#delete-confirm" ).removeClass('hide').dialog({
            resizable: false,
            modal: true,
            title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i> {{trans($transFile.'.You wanna delete permission')}}?</h4></div>",
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