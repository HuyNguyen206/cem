<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>CEM</title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- basic styles -->

		<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" />
		<link rel="stylesheet" href="{{asset('assets/css/font-awesome.min.css')}}" />

		<!-- ace styles -->

		<link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}" />
		
		<link rel="stylesheet" href="{{asset('assets/css/custom-chosen-content.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />
		
		<!-- inline styles related to this page -->

		<!-- ace settings handler -->

		<script type="text/javascript">
            window.jQuery || document.write("<script src='{{asset('assets/js/jquery-2.0.3.min.js')}}'>" + "<" + "/script>");
		</script>

		<!-- <![endif]-->

		<script type="text/javascript">
            if ("ontouchend" in document)
                document.write("<script src='{{asset('assets/js/jquery.mobile.custom.min.js')}}'>" + "<" + "/script>");
		</script>
		<script src="{{asset('assets/js/ace-extra.min.js')}}"></script>
	</head>

	<body>
		<div class="navbar navbar-default" id="navbar">
			<script type="text/javascript">
				try {
					ace.settings.check('navbar', 'fixed')
				} catch (e) {
				}
			</script>

			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">
					<a href="<?php echo url('/'.main_prefix);?>" class="navbar-brand no-padding" style="margin-top: 3px;">
						<img src="{{asset('assets/img/icon_cv.png')}}" height="40"/>
						<small>
							Customer Voice
						</small>
					</a><!-- /.brand -->
				</div><!-- /.navbar-header -->

				<div class="navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<img class="nav-user-photo" src="{{asset('assets/avatars/user.jpg')}}" alt="Jason's Photo" />
								<span class="user-info">
									<small>{{ trans('nav-sidebar.Welcome') }},</small>
									{{Auth::user()->name}}
								</span>
								<i class="icon-caret-down"></i>
							</a>
							<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="{{ url('/logout') }}">
										<i class="icon-off"></i>
										{{ trans('nav-sidebar.Logout')}}
									</a>
								</li>
							</ul>
						</li>
					</ul><!-- /.ace-nav -->
				</div><!-- /.navbar-header -->
			</div><!-- /.container -->
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
                try {
                    ace.settings.check('main-container', 'fixed')
                } catch (e) {
                }
			</script>

			<div class="main-container-inner">
				<a class="menu-toggler" id="menu-toggler" href="#">
					<span class="menu-text"></span>
				</a>

				@include('layouts.sidebar')

				<div class="main-content">
					@yield('content')
				</div><!-- /.main-content -->

				<div class="ace-settings-container" id="ace-settings-container">
					<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
						<i class="icon-cog bigger-150"></i>
					</div>

					<div class="ace-settings-box" id="ace-settings-box">
						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" />
							<label class="lbl" for="ace-settings-navbar"> {{trans('common.Fixed Navbar')}}</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
							<label class="lbl" for="ace-settings-sidebar"> {{trans('common.Fixed Sidebar')}}</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />
							<label class="lbl" for="ace-settings-breadcrumbs"> {{trans('common.Fixed Breadcrumbs')}}</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />
							<label class="lbl" for="ace-settings-rtl"> {{trans('common.Right To Left (rtl)')}}</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />
							<label class="lbl" for="ace-settings-add-container">
								{{trans('common.Inside')}}
								<b>.{{trans('common.container')}}</b>
							</label>
						</div>
					</div>
				</div><!-- /#ace-settings-container -->
			</div><!-- /.main-container-inner -->

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="icon-double-angle-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->

		
		<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
		<script src="{{asset('assets/js/typeahead-bs2.min.js')}}"></script>
		
		<!-- ace scripts -->

		<script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
		<script src="{{asset('assets/js/ace.min.js')}}"></script>
        <script type="text/javascript">
            $("#ace-settings-btn").hide();
        </script>        
	</body>
</html>

