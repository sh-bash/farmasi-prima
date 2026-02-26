<!-- Header -->
<header id="page-header">
    <!-- Header Content -->
    <div class="content-header">
      <!-- Left Section -->
      <div class="d-flex align-items-center">
        <!-- Toggle Sidebar -->
        <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
        <button type="button" class="btn btn-sm btn-alt-secondary me-2 d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
          <i class="fa fa-fw fa-bars"></i>
        </button>
        <!-- END Toggle Sidebar -->

        <!-- Open Search Section (visible on smaller screens) -->
        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
        <button type="button" class="btn btn-sm btn-alt-secondary d-sm-none" data-toggle="layout" data-action="header_search_on">
          <i class="si si-magnifier"></i>
        </button>
        <!-- END Open Search Section -->

        <!-- Search Form (visible on larger screens) -->
        <form class="d-none d-sm-inline-block" method="POST">
          <div class="input-group input-group-sm">
            <input type="text" class="form-control form-control-alt" placeholder="Search.." id="page-header-search-input2" name="page-header-search-input2">
            <span class="input-group-text bg-body border-0">
              <i class="si si-magnifier"></i>
            </span>
          </div>
        </form>
        <!-- END Search Form -->
      </div>
      <!-- END Left Section -->

      <!-- Right Section -->
      <div class="d-flex align-items-center">
        <!-- User Dropdown -->
        <div class="dropdown d-inline-block ms-2">
            <button type="button"
                    class="btn btn-sm btn-alt-secondary d-flex align-items-center"
                    id="page-header-user-dropdown"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">

                <img class="rounded-circle"
                     src="{{ asset('assets/media/avatars/avatar10.jpg') }}"
                     alt="Header Avatar"
                     style="width: 21px;">

                <span class="d-none d-sm-inline-block ms-2">
                    {{ auth()->user()->name }}
                </span>

                <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ms-1 mt-1"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0 border-0"
                 aria-labelledby="page-header-user-dropdown">

                {{-- Header User --}}
                <div class="p-3 text-center bg-body-light border-bottom rounded-top">
                    <img class="img-avatar img-avatar48 img-avatar-thumb"
                         src="{{ asset('assets/media/avatars/avatar10.jpg') }}"
                         alt="">

                    <p class="mt-2 mb-0 fw-medium">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="mb-0 text-muted fs-sm fw-medium">
                        {{ auth()->user()->getRoleNames()->first() }}
                    </p>
                </div>

                {{-- Menu --}}
                <div class="p-2">

                    @can('view user')
                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                       href="{{ route('account.users') }}">
                        <span class="fs-sm fw-medium">User Management</span>
                    </a>
                    @endcan

                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                       href="#">
                        <span class="fs-sm fw-medium">Profile</span>
                    </a>

                </div>

                <div role="separator" class="dropdown-divider m-0"></div>

                {{-- Bottom --}}
                <div class="p-2">

                    {{-- Lock (optional kalau kamu pakai) --}}
                    {{-- <a class="dropdown-item d-flex align-items-center justify-content-between"
                       href="{{ route('lock') }}">
                        <span class="fs-sm fw-medium">Lock Account</span>
                    </a> --}}

                    {{-- Logout --}}
                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                       href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="fs-sm fw-medium text-danger">Log Out</span>
                    </a>

                    <form id="logout-form"
                          action="{{ route('logout') }}"
                          method="POST"
                          class="d-none">
                        @csrf
                    </form>

                </div>

            </div>
        </div>
        <!-- END User Dropdown -->

        <!-- Notifications Dropdown -->
        <div class="dropdown d-inline-block ms-2">
          <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-fw fa-bell"></i>
            <span class="text-primary">•</span>
          </button>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 border-0 fs-sm" aria-labelledby="page-header-notifications-dropdown">
            <div class="p-2 bg-body-light border-bottom text-center rounded-top">
              <h5 class="dropdown-header text-uppercase">Notifications</h5>
            </div>
            <ul class="nav-items mb-0">
              <li>
                <a class="text-dark d-flex py-2" href="javascript:void(0)">
                  <div class="flex-shrink-0 me-2 ms-3">
                    <i class="fa fa-fw fa-check-circle text-success"></i>
                  </div>
                  <div class="flex-grow-1 pe-2">
                    <div class="fw-semibold">You have a new follower</div>
                    <span class="fw-medium text-muted">15 min ago</span>
                  </div>
                </a>
              </li>
              <li>
                <a class="text-dark d-flex py-2" href="javascript:void(0)">
                  <div class="flex-shrink-0 me-2 ms-3">
                    <i class="fa fa-fw fa-plus-circle text-primary"></i>
                  </div>
                  <div class="flex-grow-1 pe-2">
                    <div class="fw-semibold">1 new sale, keep it up</div>
                    <span class="fw-medium text-muted">22 min ago</span>
                  </div>
                </a>
              </li>
              <li>
                <a class="text-dark d-flex py-2" href="javascript:void(0)">
                  <div class="flex-shrink-0 me-2 ms-3">
                    <i class="fa fa-fw fa-times-circle text-danger"></i>
                  </div>
                  <div class="flex-grow-1 pe-2">
                    <div class="fw-semibold">Update failed, restart server</div>
                    <span class="fw-medium text-muted">26 min ago</span>
                  </div>
                </a>
              </li>
              <li>
                <a class="text-dark d-flex py-2" href="javascript:void(0)">
                  <div class="flex-shrink-0 me-2 ms-3">
                    <i class="fa fa-fw fa-plus-circle text-primary"></i>
                  </div>
                  <div class="flex-grow-1 pe-2">
                    <div class="fw-semibold">2 new sales, keep it up</div>
                    <span class="fw-medium text-muted">33 min ago</span>
                  </div>
                </a>
              </li>
              <li>
                <a class="text-dark d-flex py-2" href="javascript:void(0)">
                  <div class="flex-shrink-0 me-2 ms-3">
                    <i class="fa fa-fw fa-user-plus text-success"></i>
                  </div>
                  <div class="flex-grow-1 pe-2">
                    <div class="fw-semibold">You have a new subscriber</div>
                    <span class="fw-medium text-muted">41 min ago</span>
                  </div>
                </a>
              </li>
              <li>
                <a class="text-dark d-flex py-2" href="javascript:void(0)">
                  <div class="flex-shrink-0 me-2 ms-3">
                    <i class="fa fa-fw fa-check-circle text-success"></i>
                  </div>
                  <div class="flex-grow-1 pe-2">
                    <div class="fw-semibold">You have a new follower</div>
                    <span class="fw-medium text-muted">42 min ago</span>
                  </div>
                </a>
              </li>
            </ul>
            <div class="p-2 border-top text-center">
              <a class="d-inline-block fw-medium" href="javascript:void(0)">
                <i class="fa fa-fw fa-arrow-down me-1 opacity-50"></i> Load More..
              </a>
            </div>
          </div>
        </div>
        <!-- END Notifications Dropdown -->

        <!-- Toggle Side Overlay -->
        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
        <button type="button" class="btn btn-sm btn-alt-secondary ms-2" data-toggle="layout" data-action="side_overlay_toggle">
          <i class="fa fa-fw fa-list-ul fa-flip-horizontal"></i>
        </button>
        <!-- END Toggle Side Overlay -->
      </div>
      <!-- END Right Section -->
    </div>
    <!-- END Header Content -->

    <!-- Header Search -->
    <div id="page-header-search" class="overlay-header bg-body-extra-light">
      <div class="content-header">
        <form class="w-100" method="POST">
          <div class="input-group input-group-sm">
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-danger" data-toggle="layout" data-action="header_search_off">
              <i class="fa fa-fw fa-times-circle"></i>
            </button>
            <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
          </div>
        </form>
      </div>
    </div>
    <!-- END Header Search -->

    <!-- Header Loader -->
    <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
    <div id="page-header-loader" class="overlay-header bg-body-extra-light">
      <div class="content-header">
        <div class="w-100 text-center">
          <i class="fa fa-fw fa-circle-notch fa-spin"></i>
        </div>
      </div>
    </div>
    <!-- END Header Loader -->
  </header>
  <!-- END Header -->