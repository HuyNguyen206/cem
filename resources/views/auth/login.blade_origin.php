@extends('layouts.appLogin')

@section('content')
<div class="main-content">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<div class="login-container">
				<div class="center">
					<h1>
						<span class="red">CEM</span>
						<!--<span class="white">Survey</span>-->
					</h1>
					<!--<h4 class="blue">&copy; RAD - VAS</h4>-->
					<div class="white">
						<img src="{{asset('assets/img/icon_cv.png')}}" height="40"/>
						<span style="vertical-align: bottom;font-size: 20px;"> CUSTOMER VOICE</span>
						
					</div>
				</div>

				<div class="space-6"></div>
				
				<div class="position-relative">
					<div id="login-box" class="login-box visible widget-box no-border">
						<div class="widget-body">
							<div class="widget-main">
								<h4 class="header blue lighter bigger">
									<i class="icon-key red"></i>
									<?php echo 'Sử dụng tài khoản Inside'; ?>
								</h4>


								
								<div class="space-8"></div>
								<form role="form" method="POST" action="{{ url('/login') }}">
									{!! csrf_field() !!}
									<fieldset>
										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
                                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Tài khoản inside" oninvalid="InvalidMsg(this)" oninput="InvalidMsg(this)" autofocus="true"/>
												<i class="icon-user"></i>
											</span>
											
											@if ($errors->has('name'))
												<span class="help-block">
													<strong>{{ $errors->first('name') }}</strong>
												</span>
											@endif
										</label>

										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input type="password" class="form-control" name="password" value="{{ old('password') }}" placeholder="<?php echo trans('login.Password'); ?>" oninvalid="this.setCustomValidity('<?php echo trans('login.require password'); ?>')" oninput="setCustomValidity('')" />
												<i class="icon-lock"></i>
											</span>
											@if ($errors->has('password'))
												<span class="help-block">
													<strong>{{ $errors->first('password') }}</strong>
												</span>
											@endif
										</label>

										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input type="text" class="form-control" name="otp" value="{{ old('otp') }}" placeholder="<?php echo 'Nhập mã OTP'; ?>" />
												<i class="icon-lock"></i>
											</span>
											@if ($errors->has('otp'))
												<span class="help-block">
													<strong>{{ $errors->first('otp') }}</strong>
												</span>
											@endif
										</label>
										
										<label class="block clearfix">
											<a href="https://member.hcm.fpt.vn/KOWJOIWITR/Huong_dan_su_dung_OTP.html" target="blank">Hướng dẫn sử dụng OTP</a>
										</label>
										
										<div class="space"></div>

										<div class="clearfix">
											<label class="inline">
												<input type="checkbox" class="ace" name="remember"/>
												<span class="lbl"> <?php echo trans('login.Remember Me'); ?></span>
											</label>

											<button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
												<i class="icon-key"></i>
												<?php echo trans('login.Login');?>
											</button>
										</div>

										<div class="space-4"></div>
									</fieldset>
								</form>
								
							</div><!-- /widget-main -->

<!--							<div class="toolbar clearfix">
								<div>
									<a href="#" onclick="show_box('forgot-box'); return false;" class="forgot-password-link">
										<i class="icon-arrow-left"></i>
										<?php //echo trans('login.Forgot Your Password');?>?
									</a>
								</div>

								<div>
									<a href="#" onclick="show_box('signup-box'); return false;" class="user-signup-link">
										<?php// echo trans('login.Create an account');?>
										<i class="icon-arrow-right"></i>
									</a>
								</div>
							</div>-->
						</div><!-- /widget-body -->
					</div><!-- /login-box -->

					<div id="forgot-box" class="forgot-box widget-box no-border">
						<div class="widget-body">
							<div class="widget-main">
								<h4 class="header red lighter bigger">
									<i class="icon-key"></i>
									<?php echo trans('login.Reset Password'); ?>
								</h4>

								<div class="space-6"></div>
								<p>
									<?php echo trans('login.Enter your email and to receive instructions'); ?>
								</p>

								<form>
									<fieldset>
										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input required type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="<?php echo trans('login.Email'); ?>" oninvalid="InvalidMsg(this)" oninput="InvalidMsg(this)" />
												<i class="icon-envelope"></i>
											</span>
											
											@if ($errors->has('email'))
												<span class="help-block">
													<strong>{{ $errors->first('email') }}</strong>
												</span>
											@endif
										</label>

										<div class="clearfix">
											<button type="submit" class="width-35 pull-right btn btn-sm btn-danger">
												<i class="icon-lightbulb"></i>
												<?php echo trans('login.Send');?>
											</button>
										</div>
									</fieldset>
								</form>
							</div><!-- /widget-main -->

							<div class="toolbar center">
								<a href="#" onclick="show_box('login-box'); return false;" class="back-to-login-link">
									<?php echo trans('login.Back to login');?>
									<i class="icon-arrow-right"></i>
								</a>
							</div>
						</div><!-- /widget-body -->
					</div><!-- /forgot-box -->

					<div id="signup-box" class="signup-box widget-box no-border">
						<div class="widget-body">
							<div class="widget-main">
								<h4 class="header green lighter bigger">
									<i class="icon-group blue"></i>
									<?php echo trans('login.New User Registration');?>
								</h4>

								<div class="space-6"></div>
								<p> <?php echo trans('login.Enter your details to begin'); ?>: </p>

								<form>
									<fieldset>
										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input required type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="<?php echo trans('login.Name'); ?>" oninvalid="this.setCustomValidity('<?php echo trans('login.require name'); ?>')" oninput="setCustomValidity('')" >
												<i class="icon-envelope"></i>
											</span>
											
											@if ($errors->has('name'))
											<span class="help-block">
												<strong>{{ $errors->first('name') }}</strong>
											</span>
											@endif
										</label>

										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input required type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="<?php echo trans('login.Email'); ?>" oninvalid="InvalidMsg(this)" oninput="InvalidMsg(this)">
												<i class="icon-user"></i>
											</span>
											
											@if ($errors->has('email'))
											<span class="help-block">
												<strong>{{ $errors->first('email') }}</strong>
											</span>
											@endif
										</label>

										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input required type="password" class="form-control" name="password" placeholder="<?php echo trans('login.Password'); ?>" oninvalid="this.setCustomValidity('<?php echo trans('login.require password'); ?>')" oninput="setCustomValidity('')">
												<i class="icon-lock"></i>
											</span>
											
											@if ($errors->has('password'))
											<span class="help-block">
												<strong>{{ $errors->first('password') }}</strong>
											</span>
											@endif
										</label>

										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input required type="password" class="form-control" name="password_confirmation" placeholder="<?php echo trans('login.Confirm password'); ?>" oninvalid="this.setCustomValidity('<?php echo trans('login.require confirm password'); ?>')" oninput="setCustomValidity('')">
												<i class="icon-retweet"></i>
											</span>
											
											@if ($errors->has('password_confirmation'))
											<span class="help-block">
												<strong>{{ $errors->first('password_confirmation') }}</strong>
											</span>
											@endif
										</label>

										<label class="block">
											<input type="checkbox" class="ace" />
											<span class="lbl">
												I accept the
												<a href="#">User Agreement</a>
											</span>
										</label>

										<div class="space-24"></div>

										<div class="clearfix">
											<button type="reset" class="width-30 pull-left btn btn-sm">
												<i class="icon-refresh"></i>
												Reset
											</button>

											<button type="button" class="width-65 pull-right btn btn-sm btn-success">
												Register
												<i class="icon-arrow-right icon-on-right"></i>
											</button>
										</div>
									</fieldset>
								</form>
							</div>

							<div class="toolbar center">
								<a href="#" onclick="show_box('login-box'); return false;" class="back-to-login-link">
									<i class="icon-arrow-left"></i>
									Back to login
								</a>
							</div>
						</div><!-- /widget-body -->
					</div><!-- /signup-box -->
				</div><!-- /position-relative -->
			</div>
		</div><!-- /.col -->
	</div><!-- /.row -->
</div>

<script>
function InvalidMsg(textbox) {
    
    if (textbox.value == '') {
        textbox.setCustomValidity('<?php echo 'Vui lòng nhập thông tin'; ?>');
    }
    else if(textbox.validity.typeMismatch){
        textbox.setCustomValidity('<?php echo 'Vui lòng nhập thông tin'; ?>');
    }
    else {
        textbox.setCustomValidity('');
    }
    return true;
}
</script>

@endsection
