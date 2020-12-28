<div class="sidebar" id="sidebar">
    <script type="text/javascript">
        try {
            ace.settings.check('sidebar', 'fixed')
        } catch (e) {
        }
    </script>

    <div style="border-bottom: 1px #dddddd solid; background-color: white;">
        <div class="sidebar-shortcuts-large center" id="sidebar-shortcuts-large">
            <img width="170" src="{{asset('assets/img/logo_cem.png')}}" />
        </div>

        <div class="sidebar-shortcuts-mini center" id="sidebar-shortcuts-mini">
            <img width="40" src="{{asset('assets/img/logo_cem.png')}}" />
        </div>
    </div>
    <!-- #sidebar-shortcuts -->

    <ul class="nav nav-list">
        <?php
            $allPermission = Session::get('allPermission');
            $columnNeedCheck = array_column($allPermission['permission'], 'name');
            $canDashboardIndex = array_search('dashboardcontroller-index', $columnNeedCheck);

        ?>
        @if($canDashboardIndex !== false)
            <li class="{{(session('main_breadcrumb') === 'DashboardController')? "active":""}}">
                <a href="{{ url('/'.main_prefix) }}">
                    <i class="icon-dashboard"></i>
                    <span class="menu-text"> {{ trans('nav-sidebar.Dashboard') }} </span>
                </a>
            </li>
        @endif


        <?php
            $canRoleIndex = array_search('roles-index', $columnNeedCheck);
            $canRoleCreate = array_search('roles-create', $columnNeedCheck);
        ?>
        @if($canRoleIndex !== false || $canRoleCreate !== false)
            <li class="{{(session('main_breadcrumb') === 'Roles')? "active":""}}">
                <a href="#" class="dropdown-toggle">
                    <i class="icon-key"></i>
                    <span class="menu-text"> {{ trans('nav-sidebar.Roles') }} </span>
                </a>
                <ul class="submenu">
                    @if( $canRoleIndex !== false )
                        <li>
                            <a href="{{ url(main_prefix.'/roles') }}">
                                <i class="icon-double-angle-right"></i>
                                {{ trans('roles.Index') }}
                            </a>
                        </li>
                    @endif
                    @if($canRoleCreate !== false)
                        <li>
                            <a href="{{ url(main_prefix.'/roles/create') }}">
                                <i class="icon-double-angle-right"></i>
                                {{ trans('roles.Create') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        <?php
            $canUserIndex = array_search('users-index', $columnNeedCheck);
            $canUserCreate = array_search('users-create', $columnNeedCheck);
        ?>
        @if($canUserIndex !== false || $canUserCreate !== false)
            <li class="{{(session('main_breadcrumb') === 'Users')? "active":""}}">
                <a href="#" class="dropdown-toggle">
                    <i class="icon-user"></i>
                    <span class="menu-text"> {{trans('nav-sidebar.Users')}} </span>
                </a>
                <ul class="submenu">
                    @if($canUserIndex !== false)
                        <li>
                            <a href="{{ url(main_prefix.'/users') }}">
                                <i class="icon-double-angle-right"></i>
                                {{ trans('users.Index') }}
                            </a>
                        </li>
                    @endif
                    @if($canUserCreate !== false)
                        <li>
                            <a href="{{ url(main_prefix.'/users/create') }}">
                                <i class="icon-double-angle-right"></i>
                                {{ trans('users.Create') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        <?php
            $canPermissionIndex = array_search('permissions-index', $columnNeedCheck);
            $canPermissionCreate = array_search('permissions-create', $columnNeedCheck);
        ?>
        @if($canPermissionIndex !== false || $canPermissionCreate !== false)
            <li class="{{(session('main_breadcrumb') === 'Permissions')? "active":""}}">
                <a href="#" class="dropdown-toggle">
                    <i class="icon-unlock"></i>
                    <span class="menu-text"> {{trans('nav-sidebar.Permissions')}} </span>
                </a>
                <ul class="submenu">
                    @if($canPermissionIndex !== false)
                        <li>
                            <a href="{{ url(main_prefix.'/permissions') }}">
                                <i class="icon-double-angle-right"></i>
                                {{ trans('permissions.Index') }}
                            </a>
                        </li>
                    @endif
                    @if($canPermissionCreate !== false)
                        <li>
                            <a href="{{ url(main_prefix.'/permissions/create') }}">
                                <i class="icon-double-angle-right"></i>
                                {{ trans('permissions.Create') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        <?php
            $canAuthenGetRolePermission = array_search('authens-getrolepermission', $columnNeedCheck);
            $canAuthenGetUserPermisson = array_search('authens-getuserpermission', $columnNeedCheck);
        ?>
        @if($canAuthenGetRolePermission !== false || $canAuthenGetUserPermisson !== false)
            <li class="{{(session('main_breadcrumb') === 'Authens')? "active":""}}">
                <a href="#" class="dropdown-toggle">
                    <i class="icon-legal"></i>
                    <span class="menu-text"> {{trans('nav-sidebar.Authentications')}} </span>
                </a>
                <ul class="submenu">
                    @if($canAuthenGetRolePermission !== false)
                        <li>
                            <a href="{{ url(main_prefix.'/authens/view-role-permission') }}">
                                <i class="icon-double-angle-right"></i>
                                {{ trans('authens.role-permission') }}
                            </a>
                        </li>
                    @endif
                    @if($canAuthenGetUserPermisson !== false)
                        <li>
                            <a href="{{ url(main_prefix.'/authens/view-user-permission') }}">
                                <i class="icon-double-angle-right"></i>
                                {{ trans('authens.user-permission') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        <?php
            $canPermissionReport = array_search('report-index', $columnNeedCheck);
        ?>
        @if($canPermissionReport !== false)
        <li class="{{(session('main_breadcrumb') === 'Report')? "active":""}}">
            <a href="{{ url(main_prefix.'/report') }}">
                <i class="icon-bar-chart"></i>
                <span class="menu-text"> {{ trans('nav-sidebar.Report') }} </span>
            </a>
        </li>
        @endif

        <?php
            $canPermissionHistory = array_search('history-index', $columnNeedCheck);
        ?>
        @if($canPermissionHistory !== false)
            <li class="{{(session('main_breadcrumb') === 'History')? "active":""}}">
                <a href="{{ url(main_prefix.'/history') }}">
                    <i class="icon-briefcase"></i>
                    <span class="menu-text"> {{ trans('nav-sidebar.SurveyHistory') }} </span>
                </a>
            </li>
        @endif


        <!-- Báo cáo xử lý CSAT 1,2 -->

            <?php
            $canCsatServiceDetail = array_search('csatservicecontroller-detail', $columnNeedCheck);
            $canCsatServiceGeneral = array_search('csatservicecontroller-general', $columnNeedCheck);
            $canCsatStaffDetail = array_search('csatstaffcontroller-detail', $columnNeedCheck);
            $canCsatStaffGeneral = array_search('csatstaffcontroller-general', $columnNeedCheck);
            $main = session('main_breadcrumb');
            $active = session('active_breadcrumb');
            ?>
            @if($canCsatServiceDetail !== false || $canCsatServiceGeneral !== false || $canCsatStaffDetail !== false || $canCsatStaffGeneral !== false)
                @if ($main == 'CsatServiceController' || $main == 'CsatStaffController')
                    <li class="active">
                @else
                    <li class="">
                        @endif
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-bar-chart"></i>
                            <span class="menu-text">{{trans('nav-sidebar.ReportCSAT12')}}</span>
                        </a>
                        <ul class="submenu">
                            @if($canCsatStaffDetail !== false || $canCsatStaffGeneral !== false)
                                @if($main == 'CsatStaffController')
                                    <li class="active">
                                @else
                                    <li class="">
                                        @endif
                                        <a href="#" class="dropdown-toggle">
                                            <i class="icon-double-angle-right"></i>
                                            {{trans('nav-sidebar.ReportStaffCsat12')}}
                                        </a>
                                        <ul class="customSubSubMenu" @if($main != 'CsatStaffController') style="display: none;" @endif>
                                            @if($canCsatStaffGeneral !== false)
                                                <li>
                                                    <a href="{{ url(main_prefix.'/csat-staff/general') }}">
                                                        {{trans('nav-sidebar.Report')}}
                                                    </a>
                                                </li>
                                            @endif
                                            @if($canCsatStaffDetail !== false)
                                                <li>
                                                    <a href="{{ url(main_prefix.'/csat-staff/detail') }}">
                                                        {{trans('nav-sidebar.DetailSurveyCase')}}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                @endif
                                @if($canCsatServiceDetail !== false || $canCsatServiceGeneral !== false)
                                    @if($main == 'CsatServiceController')
                                        <li class="active">
                                    @else
                                        <li class="">
                                            @endif
                                            <a href="#" class="dropdown-toggle">
                                                <i class="icon-double-angle-right"></i>
                                                {{trans('nav-sidebar.ReportServiceCsat12')}}
                                            </a>
                                            <ul class="customSubSubMenu" @if($main != 'CsatServiceController') style="display: none;" @endif>
                                                @if($canCsatServiceGeneral !== false)
                                                    <li>
                                                        <a href="{{ url(main_prefix.'/csat-service/general') }}">
                                                            {{trans('nav-sidebar.Report')}}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($canCsatServiceDetail !== false)
                                                    <li>
                                                        <a href="{{ url(main_prefix.'/csat-service/detail') }}">
                                                            {{trans('nav-sidebar.DetailSurveyCase')}}
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif
                        </ul>
                    </li>
            @endif

            <!-- Kết thúc báo cáo xử lý khiếu nại -->

        <!-- /.nav-list -->

        <div class="sidebar-collapse" id="sidebar-collapse">
            <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
        </div>
    </ul>
    <script type="text/javascript">
        try {
            ace.settings.check('sidebar', 'collapsed')
        } catch (e) {
        }
    </script>

    <style type="text/css">
        .customSubSubMenu{
            padding-left: 30px;
        }
        .customSubSubMenu a{
            text-decoration: none;
        }
        .customSubSubMenu li{
            padding: 5px;
        }

    </style>
</div>
