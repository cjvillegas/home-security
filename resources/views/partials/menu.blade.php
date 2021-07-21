<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            Employee Tracker
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.home") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>
        @can('user_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/permissions*") ? "c-show" : "" }} {{ request()->is("admin/roles*") ? "c-show" : "" }} {{ request()->is("admin/users*") ? "c-show" : "" }} {{ request()->is("admin/audit-logs*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon"></i>
                    User Management
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.permissions.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                Permission
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.roles.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                Roles
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.users.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                Users
                            </a>
                        </li>
                    @endcan
                    @can('audit_log_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.audit-logs.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/audit-logs") || request()->is("admin/audit-logs/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-file-alt c-sidebar-nav-icon">

                                </i>
                                Audit Logs
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('user_alert_access')
            {{-- <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.user-alerts.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/user-alerts") || request()->is("admin/user-alerts/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userAlert.title') }}
                </a>
            </li> --}}
        @endcan
        @if (auth()->user()->can('employee_access') && auth()->user()->canAny(['team_access', 'shift_access']))
            <li class="c-sidebar-nav-dropdown  {{ request()->is("admin/employees*") || request()->is("admin/teams*") || request()->is("admin/shifts*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon"></i>
                    Employee Management
                </a>
                <ul  class="c-sidebar-nav-dropdown-items">
                    @can('employee_access')
                        <li class="c-sidebar-nav-item">
                                <a href="{{ route("admin.employees.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/employees") || request()->is("admin/employees/*") ? "c-active" : "" }}">
                                    <i class="fa-fw fas fa-user-circle c-sidebar-nav-icon"></i>
                                    Employees
                                </a>
                        </li>
                    @endcan
                    @can('team_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.teams.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/teams") || request()->is("admin/teams/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-users c-sidebar-nav-icon"></i>
                                Teams
                            </a>
                        </li>
                    @endcan
                    @can('shift_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.shifts.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/shifts") || request()->is("admin/shifts/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-calendar-check-o c-sidebar-nav-icon"></i>
                                Shifts
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif


        @can('process_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/processes*") || request()->is("admin/settings*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-people-carry c-sidebar-nav-icon"></i>
                    Process Management
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.processes.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/processes") || request()->is("admin/processes/*") ? "c-active" : "" }}">
                            <i class="fa-fw fas fa-people-carry c-sidebar-nav-icon"></i>
                            Process
                        </a>
                    </li>
                </ul>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.settings.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/settings") ? "c-active" : "" }}">
                            <i class="fas fa-cogs fa-fw c-sidebar-nav-icon"></i>
                            Categories
                        </a>
                    </li>
                </ul>
            </li>
        @endcan
        @can('scanner_access')
            {{-- <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.scanners.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/scanners") || request()->is("admin/scanners/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-barcode c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.scanner.title') }}
                </a>
            </li> --}}
        @endcan
        @can('order_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/orders*") ? "c-show" : "" }} {{ request()->is("admin/orderhistories*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-boxes c-sidebar-nav-icon"></i>
                    Order Management
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('order_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.orders.index') }}" class="c-sidebar-nav-link {{ request()->is("admin/orders") || request()->is("admin/orders/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-shopping-cart c-sidebar-nav-icon"></i>
                                Orders
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan


        <li class="c-sidebar-nav-dropdown {{ request()->is("admin/orders*") ? "c-show" : "" }} {{ request()->is("admin/orderhistories*") ? "c-show" : "" }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-rocket c-sidebar-nav-icon"></i>
                Machine Management
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                <li class="c-sidebar-nav-item">
                    <a href="{{ url('/admin/machines/') }}" class="c-sidebar-nav-link {{ request()->is("admin/orders") || request()->is("admin/orders/*") ? "c-active" : "" }}">
                        <i class="fa-fw fas fa-shopping-cart c-sidebar-nav-icon"></i>
                        Packing Machine
                    </a>
                </li>
                <li class="c-sidebar-nav-item">
                    <a href="{{ url('/admin/machine-counters/') }}" class="c-sidebar-nav-link {{ request()->is("admin/orders") || request()->is("admin/orders/*") ? "c-active" : "" }}">
                        <i class="fa-fw fas fa-area-chart c-sidebar-nav-icon"></i>
                        Machine Statistics
                    </a>
                </li>
            </ul>
        </li>

        @can('order_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/reports*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-chart-bar c-sidebar-nav-icon"></i>
                    Reports
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('order_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.reports.work-analytics.index') }}" class="c-sidebar-nav-link {{ request()->is("admin.reports.work-analytics.index") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-project-diagram c-sidebar-nav-icon"></i>
                                Work Analytics
                            </a>
                        </li>
                    @endcan

                    @can('order_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.reports.data-export.index') }}" class="c-sidebar-nav-link {{ request()->is("admin.reports.work-analytics.index") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-file-export c-sidebar-nav-icon"></i>
                                Data Export
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

        {{-- @can('settings_access') --}}
            {{-- <li class="c-sidebar-nav-item {{ request()->is('admin/settings*') ? 'c-show' : '' }}">
                <a href="{{ route("admin.settings.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/settings") ? "c-active" : "" }}">
                    <i class="fas fa-cogs fa-fw c-sidebar-nav-icon"></i>
                    Settings
                </a>
            </li> --}}
        {{-- @endcan --}}

        @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}" href="{{ route('profile.password.edit') }}">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon"></i>
                        Change Password
                    </a>
                </li>
            @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt"></i>
                Logout
            </a>
        </li>
    </ul>

</div>
