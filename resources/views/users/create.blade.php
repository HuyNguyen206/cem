@extends('layouts.app')

@section('content')

<div class="page-content">
	<?php 
		$controller = 'users'; 
		$title = 'Create new user';
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
					<label class="col-sm-3 control-label no-padding-right" for="name"> {{trans($transFile.'.Name')}}:</label>

					<div class="col-sm-9">
						<input type="text" class="col-xs-10 col-sm-5" name="name" value="{{ old('name') }}" placeholder="{{trans($transFile.'.Input here')}}" oninvalid="this.setCustomValidity('{{trans($transFile.'.require name')}}')" oninput="setCustomValidity('')">
						@if ($errors->has('name'))
							<span class="col-xs-12 col-sm-12 no-padding-left red">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
						@endif	
					</div>
				</div>
				
				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="email"> {{trans($transFile.'.Email')}}: </label>

					<div class="col-sm-9">
						<input type="email" class="col-xs-10 col-sm-5" name="email" value="{{ old('email') }}" placeholder="{{trans($transFile.'.Input here')}}" oninvalid="InvalidMsg(this)" oninput="InvalidMsg(this)">
						@if ($errors->has('email'))
							<span class="col-xs-12 col-sm-12 no-padding-left red">
								<strong>{{ $errors->first('email') }}</strong>
							</span>
						@endif
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="password"> {{trans($transFile.'.Password')}}: </label>

					<div class="col-sm-9">
						<input type="password" class="col-xs-10 col-sm-5" name="password" value="{{ old('password') }}" placeholder="{{trans($transFile.'.Input here')}}" oninvalid="this.setCustomValidity('{{trans($transFile.'.require password')}}')" oninput="setCustomValidity('')">
						@if ($errors->has('password'))
							@if ($errors->first('password') !== 'Thông tin mật khẩu xác nhận không chính xác.')
								<span class="col-xs-12 col-sm-12 no-padding-left red">
									<strong>{{ $errors->first('password') }}</strong>
								</span>
							@endif
						@endif
					</div>
				</div>
				
				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="password_confirmation"> {{trans($transFile.'.Confirm password')}}: </label>

					<div class="col-sm-9">
						<input type="password" class="col-xs-10 col-sm-5" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="{{trans($transFile.'.Input here')}}" oninvalid="this.setCustomValidity('{{trans($transFile.'.require password')}}')" oninput="setCustomValidity('')">
						@if ($errors->has('password'))
							@if ($errors->first('password') === 'Thông tin mật khẩu xác nhận không chính xác.')
								<span class="col-xs-12 col-sm-12 no-padding-left red">
									<strong>{{ $errors->first('password') }}</strong>
								</span>
							@endif
						@endif
					</div>
				</div>
				
				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="role"> {{trans($transFile.'.Role')}}: </label>

					<div class="col-sm-9">
						<div>
							<select name='role' class="col-xs-10 col-sm-5 no-padding-left" id="form-field-select-1" title="">
								@foreach($roles as $role)
									@if(old('role') == $role->id)
										<option selected="selected" value="{{$role->id}}">{{$role->display_name}}</option>
									@else
										<option value="{{$role->id}}">{{$role->display_name}}</option>
									@endif
								@endforeach
							</select>
						</div>
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

<script>
function InvalidMsg(textBox) {
    if (textBox.value == '') {
		textBox.setCustomValidity('{{trans($common.'.fill email')}}');
    }
    else if(textBox.validity.typeMismatch){
		textBox.setCustomValidity('{{trans($common.'.valid email')}}');
    }
    else {
		textBox.setCustomValidity('');
    }
}
</script>

@endsection