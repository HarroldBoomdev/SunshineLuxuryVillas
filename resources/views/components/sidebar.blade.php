<div class="page-wrapper chiller-theme toggled">
    <!-- Toggle Sidebar Button -->
    <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
        <i class="fas fa-bars"></i>
    </a>

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar-wrapper">
        <div class="sidebar-content">
            <!-- Sidebar Header -->
            <div class="sidebar-brand">
                <a href="#">SUNSHINE LUXURYV VILLAS</a>
                <div id="close-sidebar">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <div class="sidebar-header">
                <div class="user-pic">
                    <img class="img-responsive img-rounded"
                         src="https://raw.githubusercontent.com/azouaoui-med/pro-sidebar-template/gh-pages/src/img/user.jpg"
                         alt="User picture">
                </div>
                <div class="user-info">
                    <!-- <span class="user-name">Harrold
                        <strong>Martinez</strong>
                    </span> -->
                    <span class="user-name">
                        {{ Auth::user()->first_name }}
                        <strong>{{ Auth::user()->last_name }}</strong>
                    </span>
                    <span class="user-role">Administrator</span>
                    <span class="user-status">
                        <i class="fa fa-circle"></i>
                        <span>Online</span>
                    </span>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <div class="sidebar-menu">
                <ul>

                    <li>
                    <li>
                      <a href="{{ route('properties.index') }}">
                          <i class="fas fa-home"></i>
                          <span>Properties</span>
                      </a>
                    </li>
                    <li>
                      <a href="{{ route('clients.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li>
                      <a href="{{ route('developers.index') }}">
                            <i class="fas fa-user-cog"></i>
                            <span>Developers</span>
                        </a>
                    </li>
                    <li>
                      <a href="{{ route('agents.index') }}">
                            <i class="fas fa-user-tie"></i>
                            <span>Agents</span>
                        </a>
                    </li>
                    <li>
                      <a href="{{ route('banks.index') }}">
                            <i class="fas fa-bank"></i>
                            <span>Banks/Vendor</span>
                        </a>
                    </li>
                    <li>
                      <a href="{{ route('deals.index') }}">
                            <i class="fas fa-handshake"></i>
                            <span>Deals</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('diary.index') }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Diary</span>
                        </a>
                    </li>
                    <!-- Add Create Viewing link below Diary -->
                    <li>
                    <a href="{{ route('viewing.create') }}">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Create Viewing</span>  <!-- New link for Create Viewing -->
                    </a>
                    </li>
                    <!-- <li>
                      <a href="#">
                            <i class="fas fa-search"></i>
                            <span>Matches</span>
                        </a>
                    </li> -->
                    <li>
                        <a href="{{ route('matches.index') }}">
                                <i class="fas fa-search"></i>
                            <span>Matches</span>
                        </a>
                    </li>


                    <li>
                    <a href="{{ route('report.index') }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li>
                      <a href="{{ route('audit.index') }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Audit Log</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('access.index') }}">
                            <i class="fas fa-user-shield"></i>
                            <span>Access Log</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('api.docs') }}">
                            <i class="fa fa-book"></i>
                            <span>API Doc</span>
                        </a>
                    </li>
                    @can('manage-sections')
                        <li>
                            <a href="{{ route('sections.index') }}">
                                <i class="fas fa-th-large"></i>
                                <span>UI CMS</span>
                            </a>
                        </li>
                    @endcan
                     @can('manage-sections')
                        <li>
                            <a href="{{ route('inbox.index') }}">
                                <i class="fas fa-th-large"></i>
                                <span>Inbox</span>
                            </a>
                        </li>
                    @endcan

                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}">
                            <i class="fas fa-gear"></i>
                            <span>Users</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <a href="#">
                <i class="fa fa-bell"></i>
                <span class="badge badge-pill badge-warning notification">3</span>
            </a>
            <a href="#">
                <i class="fa fa-envelope"></i>
                <span class="badge badge-pill badge-success notification">7</span>
            </a>
            <a href="#">
                <i class="fa fa-cog"></i>
            </a>
            <a href="#">
                <i class="fa fa-power-off"></i>
            </a>
        </div>
    </nav>







    @push('scripts')
<script>
$(document).ready(function () {
  // Collapse the sidebar by default on page load
  $(".page-wrapper").removeClass("toggled");

  // Handle submenu toggling
  $(".sidebar-dropdown > a").click(function () {
    $(".sidebar-submenu").slideUp(200);
    if ($(this).parent().hasClass("active")) {
      $(".sidebar-dropdown").removeClass("active");
      $(this).parent().removeClass("active");
    } else {
      $(".sidebar-dropdown").removeClass("active");
      $(this).next(".sidebar-submenu").slideDown(200);
      $(this).parent().addClass("active");
    }
  });

  // Close sidebar
  $("#close-sidebar").click(function () {
    $(".page-wrapper").removeClass("toggled");
  });

  // Open sidebar
  $("#show-sidebar").click(function () {
    $(".page-wrapper").addClass("toggled");
  });
});
</script>
@endpush

