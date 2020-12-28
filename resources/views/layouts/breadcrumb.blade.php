<?php $prefix = main_prefix; ?>
@if (session('main_breadcrumb') !== null)
    <?php
		$bread = session('main_breadcrumb');
		if(session('main_breadcrumb') === 'DashboardController'){
			$bread = 'Dashboard';
			$prefix = '/customer-voice';
		}
	?>
	<div class="breadcrumbs" id="breadcrumbs">
		<script type="text/javascript">
	        try {
	            ace.settings.check('breadcrumbs', 'fixed')
	        } catch (e) {
	        }
		</script>

		<ul class="breadcrumb">
			<li>
				<i class="icon-home home-icon"></i>
				<a href="{{url($prefix.'/'.strtolower($bread))}}">
					{{trans('common.' . $bread)}}
				</a>
			</li>

			<li class="active">
				{{trans(strtolower($bread) . '.' . session('active_breadcrumb'))}}
			</li>
		</ul><!-- .breadcrumb -->
	</div>
@endif