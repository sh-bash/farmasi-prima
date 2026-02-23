<!doctype html>
<html lang="en" class="remember-theme">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>Farmasi - RS Prima Husada</title>

    <meta name="description" content="Farmasi Rumah Sakit Prima Husada">
    <meta name="author" content="Prima Husada">
    <meta name="robots" content="index, follow">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="Farmasi Rumah Sakit Prima Husada">
    <meta property="og:site_name" content="Prima Husada">
    <meta property="og:description" content="Farmasi Rumah Sakit Prima Husada">
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:image" content="">

    @include('layouts.styles')
  </head>

  <body>
    <div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">

      @include('layouts.side-overlay')
      @include('layouts.sidebar')
      @include('layouts.page-header')

      <!-- Main Container -->
      <main id="main-container">
        <!-- Hero -->
        <div class="bg-body-light">
          <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
              <div class="flex-grow-1">
                <h1 class="h3 fw-bold mb-1">
                    {{ $title ?? 'Page Title' }}
                </h1>
                <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                    {{ $subtitle ?? 'Page Sub Title' }}
                </h2>
              </div>

              @isset($breadcrumb)
                <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3">
                    {{ $breadcrumb }}
                </nav>
              @endisset
            </div>
          </div>
        </div>
        <!-- END Hero -->

        <!-- Page Content -->
        <div class="content">
            {{ $slot }}
        </div>
        <!-- END Page Content -->
      </main>
      <!-- END Main Container -->

      @include('layouts.footer')
    </div>

    @include('layouts.scripts')
  </body>
</html>
