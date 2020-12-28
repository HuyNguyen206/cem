<?php $prefix = main_prefix;?>
<div class="sidebar" id="sidebar">
	<script type="text/javascript">
        try {
            ace.settings.check('sidebar', 'fixed')
        } catch (e) {
        }
	</script>

	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<button class="btn btn-success">
				<i class="icon-signal"></i>
			</button>

			<button class="btn btn-info">
				<i class="icon-pencil"></i>
			</button>

			<button class="btn btn-warning">
				<i class="icon-group"></i>
			</button>

			<button class="btn btn-danger">
				<i class="icon-cogs"></i>
			</button>
		</div>

		<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
			<span class="btn btn-success"></span>

			<span class="btn btn-info"></span>

			<span class="btn btn-warning"></span>

			<span class="btn btn-danger"></span>
		</div>
	</div>
	<!-- #sidebar-shortcuts -->

	<ul class="nav nav-list">
        @if (session('main_breadcrumb') === 'Dashboard')
        <li class="active">
        @else
		<li class="">
        @endif
			<a href="{{ url('/'.main_prefix) }}" class="dropdown-toggle">
				<i class="icon-dashboard"></i>
				<span class="menu-text"> {{ trans('nav-sidebar.Dashboard') }} </span>
			</a>
		</li>
        
		@if (session('main_breadcrumb') === 'Roles')
		<li class="active">
			@else
		<li class="">
			@endif

			<a href="#" class="dropdown-toggle">
				<i class="icon-key"></i>
				<span class="menu-text"> {{ trans('nav-sidebar.Roles') }} </span>
			</a>
			<ul class="submenu">
				<li>
					<a href="{{ url($prefix.'/roles') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('roles.Index') }}
					</a>
				</li>
				<li>
					<a href="{{ url($prefix.'/roles/create') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('roles.Create') }}
					</a>
				</li>
			</ul>
		</li>

		@if (session('main_breadcrumb') === 'Users')
		<li class="active">
			@else
		<li class="">
			@endif
			<a href="#" class="dropdown-toggle">
				<i class="icon-user"></i>
				<span class="menu-text"> {{trans('nav-sidebar.Users')}} </span>
			</a>
			<ul class="submenu">
				<li>
					<a href="{{ url($prefix.'/users') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('users.Index') }}
					</a>
				</li>
				<li>
					<a href="{{ url($prefix.'/users/create') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('users.Create') }}
					</a>
				</li>
			</ul>
		</li>

<!--		@if (session('main_breadcrumb') === 'Authens')
		<li class="active">
			@else
		<li class="">
			@endif
			<a href="#" class="dropdown-toggle">
				<i class="icon-legal"></i>
				<span class="menu-text"> {{trans('nav-sidebar.Authens')}} </span>
			</a>
			<ul class="submenu">
				<li>
					<a href="{{ url($prefix.'/authens') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('authens.Index') }}
					</a>
				</li>
				<li>
					<a href="{{ url($prefix.'/authens/create') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('authens.Create') }}
					</a>
				</li>
			</ul>
		</li>-->

<!--		@if (session('main_breadcrumb') === 'Permissions')
		<li class="active">
			@else
		<li class="">
			@endif
			<a href="#" class="dropdown-toggle">
				<i class="icon-unlock"></i>
				<span class="menu-text"> {{trans('nav-sidebar.Permissions')}} </span>
			</a>
			<ul class="submenu">
				<li>
					<a href="{{ url($prefix.'/permissions') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('permissions.Index') }}
					</a>
				</li>
				<li>
					<a href="{{ url($prefix.'/permissions/create') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('permissions.Create') }}
					</a>
				</li>
			</ul>
		</li>-->
        @if (session('main_breadcrumb') === 'History')
        <li class="active">
			@else
		<li class="">
			@endif
            <a href="#" class="dropdown-toggle">
				<i class="icon-unlock"></i>
				<span class="menu-text"> {{trans('nav-sidebar.Survey History')}} </span>
			</a>
			<ul class="submenu">
				<li>
					<a href="{{ url($prefix.'/history') }}">
						<i class="icon-double-angle-right"></i>
						{{ trans('permissions.Index') }}
					</a>
				</li>
			</ul>
		</li>
		
		<!-- /.nav-list -->

		<div class="sidebar-collapse" id="sidebar-collapse">
			<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
		</div>

		<script type="text/javascript">
            try {
                ace.settings.check('sidebar', 'collapsed')
            } catch (e) {
            }
		</script>
</div>