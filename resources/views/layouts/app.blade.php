{{--
  Master layout for the application dashboard
  Includes loader, header, sidebar, notification dropdown, and main content area
  Styles and scripts for UI/UX enhancements are included
--}}
<!DOCTYPE html>
<html lang="en">
  <head>
    {{-- Meta tags and page title --}}
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Radiology Report Management System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <style>
    /* Loader and theme styles for the initial loading screen */
    .page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        flex-direction: column;
    }

    .dental-loader-container {
        position: relative;
        width: 200px;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .dental-loader-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid transparent;
        border-top-color: #3498db;
        border-right-color: #3498db;
        animation: spin 2s linear infinite;
    }

    .dental-loader-ring:nth-child(2) {
        width: 80%;
        height: 80%;
        border-top-color: #2ecc71;
        border-right-color: #2ecc71;
        animation-duration: 1.5s;
        animation-direction: reverse;
    }

    .dental-loader-ring:nth-child(3) {
        width: 60%;
        height: 60%;
        border-top-color: #e74c3c;
        border-right-color: #e74c3c;
        animation-duration: 1s;
    }

    .dental-icon {
        position: absolute;
        width: 80px;
        height: 80px;
        background: url('/images/loader-logo-1.png') no-repeat center center;
        background-size: contain;
        animation: pulse 2s ease-in-out infinite;
        filter: drop-shadow(0 0 8px rgba(52, 152, 219, 0.3));
    }

    .loader-text-container {
        margin-top: 30px;
        text-align: center;
    }

    .loader-text {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        animation: slideUp 0.5s ease-out;
    }

    .loader-subtext {
        font-size: 16px;
        color: #7f8c8d;
        margin-top: 10px;
        animation: slideUp 0.5s ease-out 0.2s both;
    }

    .loading-dots {
        display: inline-block;
        animation: dots 1.5s infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes pulse {
        0% { transform: scale(0.95); filter: drop-shadow(0 0 8px rgba(52, 152, 219, 0.3)); }
        50% { transform: scale(1.05); filter: drop-shadow(0 0 12px rgba(52, 152, 219, 0.5)); }
        100% { transform: scale(0.95); filter: drop-shadow(0 0 8px rgba(52, 152, 219, 0.3)); }
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes dots {
        0%, 20% { content: '.'; }
        40% { content: '..'; }
        60% { content: '...'; }
        80%, 100% { content: ''; }
    }

    .main-content {
        display: none;
    }

    .main-content.loaded {
        display: block;
    }
    </style>
    
    {{-- Stylesheets for Bootstrap, custom UI, fonts, DataTables, Quill, etc. --}}
    <link
      rel="stylesheet"
      href="{{ asset('css/bootstrap-select.min.css') }}"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      href="{{ asset('css/jquery.mCustomScrollbar.css') }}"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      href="{{ asset('css/jquery-ui.css') }}"
      type="text/css"
      media="all"
    />
    
    <!-- <link
      rel="stylesheet"
      href="{{ asset('fonts/stylesheet.css') }}"
      type="text/css"
      media="all"
    /> -->

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Radio+Canada:ital,wght@0,300..700;1,300..700&display=swap"
      rel="stylesheet"
    />

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />


    <link rel="SHORTCUT ICON" href="{{ asset('images/favicon.png') }}" />

    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?ver={{ time() }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('css/media.css') }}?ver={{ time() }}" type="text/css" media="all" />
  </head>

  <body>
    {{-- Loader shown while the page is loading --}}
    <div class="page-loader">
        <div class="dental-loader-container">
            <div class="dental-loader-ring"></div>
            <div class="dental-loader-ring"></div>
            <div class="dental-loader-ring"></div>
            <div class="dental-icon"></div>
        </div>
        <div class="loader-text-container">
            <h2 class="loader-text"></h2>
            <p class="loader-subtext">Loading<span class="loading-dots">...</span></p>
        </div>
    </div>

    <div class="main-container main-content">
      <!--HEADER START-->
      {{-- Main header with logo, navigation, breadcrumbs, notifications, and user profile --}}
      <header class="db-header">
        <div class="CP-headerleftOuter">
          <div class="CP-logo-con">
            {{-- Logo links to dashboard based on user role --}}
            <a href=@role('admin') "{{ route('request-listing.index') }}" @endrole
            @role('user') "{{ route('request-listing.indexuser') }}" @endrole><img src="{{ asset('images/site-logo.png') }}" alt="Logo" style="width: 200px; height: 60px;"/></a>
          </div>
        </div>
        <div class="CP-headerRightOuter">
          <div class="CP-HeaderSearchOuter">
            <div class="right-navv-btn">
              {{-- Quick nav button for dashboard based on user role --}}
              @role('admin')
                <a href="{{ route('request-listing.index') }}"><img src="{{ asset('images/bnav-icon.png') }}" alt="Icon" /> </a>
              @endrole
              @role('user')
                <a href="{{ route('request-listing.indexuser') }}"><img src="{{ asset('images/bnav-icon.png') }}" alt="Icon" /> </a>
              @endrole
            </div>
            <div class="header-breadcrumb">
              {{-- Breadcrumb navigation for current page context --}}
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    {{-- Dynamic breadcrumbs based on current route --}}
                    @if (request()->routeIs('request-listing.index'))
                      <li class="breadcrumb-item" aria-current="page">My Tasks</li>
                      
                    @elseif (request()->routeIs('request-listing.indexuser'))
                      <li class="breadcrumb-item" aria-current="page">My Tasks</li>

                    @elseif (request()->routeIs('scan.upload'))
                      <li class="breadcrumb-item" aria-current="page">My Examination</li>

                    @elseif (request()->routeIs('scan.upload.page'))
                      <li class="breadcrumb-item"><a href="{{ route('request-listing.indexuser') }}">My Tasks</a></li>
                      <li class="breadcrumb-item active" aria-current="page">My Examination</li>

                    @elseif (request()->routeIs('request-listing.view'))
                      @if(auth()->user()->hasRole('user'))
                      <li class="breadcrumb-item"><a href="{{ route('request-listing.indexuser') }}">My Tasks</a></li>
                      @else
                      <li class="breadcrumb-item"><a href="{{ route('request-listing.index') }}">My Tasks</a></li>
                      @endif
                      <li class="breadcrumb-item active" aria-current="page">View</li>

                    @elseif (request()->routeIs('profile.show'))
                      <li class="breadcrumb-item" aria-current="page">Profile</li>

                    @elseif (request()->routeIs('modalities.index'))
                        <li class="breadcrumb-item" aria-current="page">Modalities</li>

                    @elseif (request()->routeIs('modalities.create'))
                      <li class="breadcrumb-item"><a href="{{ route('modalities.index') }}">Modalities</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Create</li>

                    @elseif (request()->routeIs('modalities.edit'))
                      <li class="breadcrumb-item"><a href="{{ route('modalities.index') }}">Modalities</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    @endif

                    @yield('breadcrumb')
                </ol>
              </nav>
            </div>
          </div>

          <div class="CP-HeaderRightInner">
            {{-- Notification dropdown (AJAX loaded) --}}
            <div class="CP-HeaderRightInner-grid all-notifications1">
              <div class="CP-NotificationCon">
                  <a href="#" class="dropdown-toggle" id="dropdown03" data-bs-toggle="dropdown" aria-expanded="false">
                      <img src="{{ asset('images/notifications.png') }}" alt="bell" />
                      <span id="notification-count" style="display:none;"></span>
                  </a>
                  <div class="dropdown-menu CP-NotificationDropdownCon notificationDrpDwn" aria-labelledby="dropdown03">
                      <ul id="notification-list">
                          <li class="list-group-item text-center text-muted mt-5 mb-5">
                              Loading notifications...
                          </li>
                      </ul>
                  </div>
              </div>
            </div>

            {{-- User profile dropdown (avatar, profile, logout) --}}
            <div class="CP-HeaderRightInner-grid profilePicOuter-grid">
              <div class="CP-profilePicOuter nav-item dropdown">
                <a
                  class="dropdown-toggle"
                  href="#"
                  id="dropdown04"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  title="{{ auth()->user()->username }}"
                >
                  <img src="{{ auth()->user()->avatar ? route('user.avatar', ['filename' => auth()->user()->avatar]) : asset('images/default-doc-profile.jpg') }}" alt="Profile Pic" />
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdown04">
                  <li>
                    <a class="dropdown-item" href="{{ route('profile.show') }}"
                      ><img src="{{ asset('images/img_navbar_icon05.png') }}" alt="setting" />Profile</a>
                  </li>
                   <li>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                      onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                     <img src="{{ asset('images/img_logout_icon.png') }}" alt="logout" />
                      Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                  </li>
                </ul>
              </div>
            </div>

            <!--Toggle-Button for mobile nav-->
            <button class="navbar-toggle" style="display: none">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
          </div>
        </div>
      </header>
      <!--HEADER END-->

      <!--CONTENT PART START-->
      <section class="CP-dashboardContentOuterPanel">
        <!--LEFT-SIDEBAR-->
        {{-- Sidebar navigation for operations/tasks, varies by user role --}}
        <div class="left-fix-con">
          <div class="left-side-con">
            <h3>OPERATION</h3>
            <div class="left-nav-con">
              <ul>
                {{-- Admin/sub-admin links --}}
                @role(['admin', 'sub-admin'])
                  <li>
                    <a href="{{ route('request-listing.index') }}"
                      ><span><img src="{{ asset('images/plus-icon.svg') }}" alt="Icon" /></span>
                      <span>Tasks</span></a>
                  </li>
                  <li>
                    <a href="{{ route('modalities.index') }}"
                      ><span><img src="{{ asset('images/plus-icon.svg') }}" alt="Icon" /></span>
                      <span>Modalities</span></a>
                  </li>
                  @role('admin')
                  <li>
                    <a href="{{ route('users.index') }}"
                      ><span><img src="{{ asset('images/plus-icon.svg') }}" alt="Icon" /></span>
                      <span>Users</span></a>
                  </li>
                  @endrole
                @endrole
                {{-- User links --}}
                 @role('user')
                 <li>
                    <a href="{{ route('scan.upload') }}"
                      ><span><img src="{{ asset('images/plus-icon.svg') }}" alt="Icon" /></span>
                      <span>My Examination </span></a>
                  </li>
                 <li>
                    <a href="{{ route('request-listing.indexuser') }}"
                      ><span><img src="{{ asset('images/plus-icon.svg') }}" alt="Icon" /></span>
                      <span>Tasks </span></a>
                  </li>
                @endrole
              </ul>
            </div>
            <div class="CP-HeaderSearchOuter mobile-view1" style="display: none">
              <button>
                <img src="images/search-icon-b.svg" alt="Seacrch Icon" />
              </button>
              <input type="text" placeholder="Search..." />
            </div>
          </div>
        </div>
        <!--LEFT-SIDEBAR-END-->

        <!--RIGHT-SIDEBAR-->
        {{-- Main content area, includes settings button and page content --}}
        <div class="right-side-con">
          <a href="javascript:;" class="setting-right-btn"
            ><img src="{{ asset('images/setting-icon.svg') }}" /></a>

          <!-- This is where the page content goes  -->
          @yield('content')
        </div>
        <!--RIGHT-SIDEBAR-END-->
      </section>
    </div>

    {{-- Scripts for jQuery, validation, SweetAlert, Dropzone, DataTables, Quill, Bootstrap, custom JS, etc. --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <!-- <script src="js/jquery.min.js"></script> -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}?ver={{ time() }}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}?ver={{ time() }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.js"></script>
    <script src="{{ asset('js/jquery-ui.js') }}?ver={{ time() }}"></script>
    <script src="{{ asset('js/custom.js') }}?ver={{ time() }}"></script>

    
<script>
  
  Dropzone.autoDiscover = false;

  
        $(document).ready(function() {
            // Function to show SweetAlert with a 2-second timer
            function showAlert(type, title, message) {
                Swal.fire({
                    icon: type,
                    title: title,
                    text: message,
                    confirmButtonText: "OK"
                });
            }

            // Check for Laravel session messages and show appropriate alert
            @if (session('success'))
                showAlert('success', 'Success', "{{ session('success') }}");
            @endif

            @if (session('error'))
                showAlert('error', 'Error', "{{ session('error') }}");
            @endif

            @if (session('info'))
                showAlert('info', 'Information', "{{ session('info') }}");
            @endif

            @if (session('warning'))
                showAlert('warning', 'Warning', "{{ session('warning') }}");
            @endif

        });
        
    </script>

  @stack('scripts')

  <script>
    function fetchNotifications() {
        $.ajax({
            url: "{{ route('notifications.unread') }}",
            method: 'GET',
            success: function(data) {
                let html = '';
                if (data.length > 0) {
                    $('#notification-count').text(data.length).show();
                    html += `<li>
                        <div class="hdr-all-notifications1">
                            <strong>All Notifications</strong>
                            <div class="Notifiactions-read-unread">
                                <div class="toggle-container">
                                    <div class="toggle-labl-wrap">Only show unread</div>
                                    <div class="toggle-switch active" id="unreadToggle"></div>
                                </div>
                            </div>
                        </div>
                    </li>`;

                    data.forEach(function(n) {
                        html += `
                            <li class="${n.class}">
                                <a class="dropdown-item d-flex align-items-start" href="${n.url}">
                                    <div class="notf-leftiiocns me-2">
                                        <img src="${n.icon}" alt="icon" style="width: auto; height: auto;" />
                                    </div>
                                    <div class="text-wrap" style="flex: 1;">
                                        <div class="fw-bold">${n.title}</div>
                                        <div class="SubText">${n.message}</div>
                                    </div>
                                </a>
                            </li>`;
                    });
                } else {
                    $('#notification-count').hide();
                    html = `<li class="list-group-item text-center text-muted">No new notifications</li>`;
                }

                $('#notification-list').html(html);
            },
            error: function() {
                $('#notification-list').html('<li class="text-danger text-center mt-3">Failed to load notifications</li>');
            }
        });
    }

    $(document).ready(function(){
        fetchNotifications(); // initial fetch
        setInterval(fetchNotifications, 10000); // repeat every second
    });
</script>
  
  <script>
    $(document).ready(function() {
        // Hide loader and show content when document is ready
        setTimeout(function() {
            $('.page-loader').fadeOut(800, function() {
                $('.main-content').addClass('loaded');
            });
        }, 1000);
    });

    // Backup in case window load event is needed
    $(window).on('load', function() {
        $('.page-loader').fadeOut(800, function() {
            $('.main-content').addClass('loaded');
        });
    });
  </script>
  
  </body>
</html>
