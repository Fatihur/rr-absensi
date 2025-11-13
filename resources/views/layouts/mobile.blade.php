<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') - Absensi Karyawan</title>

  <!-- PWA Meta -->
  <meta name="theme-color" content="#6777ef">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('stisla/assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('stisla/assets/modules/fontawesome/css/all.min.css') }}">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background: #f5f5f5;
      padding-bottom: 70px;
      overflow-x: hidden;
    }

    /* Header */
    .mobile-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 60px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      z-index: 1000;
    }

    .mobile-header h1 {
      font-size: 18px;
      font-weight: 600;
      margin: 0;
    }

    .mobile-header .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .mobile-header .user-avatar {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: white;
      color: #667eea;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    /* Content */
    .mobile-content {
      margin-top: 60px;
      padding: 15px;
      min-height: calc(100vh - 130px);
    }

    /* Bottom Navigation */
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      height: 65px;
      background: white;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-around;
      align-items: center;
      z-index: 1000;
    }

    .bottom-nav-item {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      color: #8e8e93;
      font-size: 11px;
      transition: all 0.3s;
      padding: 5px;
    }

    .bottom-nav-item i {
      font-size: 22px;
      margin-bottom: 4px;
    }

    .bottom-nav-item.active {
      color: #667eea;
    }

    .bottom-nav-item:hover {
      color: #667eea;
      text-decoration: none;
    }

    /* Cards */
    .mobile-card {
      background: white;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .mobile-card-title {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 15px;
      color: #333;
    }

    /* Buttons */
    .btn-mobile {
      width: 100%;
      padding: 15px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      transition: all 0.3s;
    }

    .btn-mobile-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .btn-mobile-danger {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }

    .btn-mobile-success {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }

    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-bottom: 15px;
    }

    .stat-item {
      background: white;
      border-radius: 12px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .stat-value {
      font-size: 24px;
      font-weight: bold;
      color: #667eea;
    }

    .stat-label {
      font-size: 12px;
      color: #8e8e93;
      margin-top: 5px;
    }

    /* Alert */
    .alert-mobile {
      border-radius: 12px;
      padding: 12px 15px;
      margin-bottom: 15px;
      border: none;
    }

    /* Badge */
    .badge-mobile {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    /* Loading */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      display: none;
    }

    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #667eea;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (min-width: 768px) {
      .mobile-content {
        max-width: 500px;
        margin: 60px auto 0;
      }
      
      .mobile-header,
      .bottom-nav {
        max-width: 500px;
        left: 50%;
        transform: translateX(-50%);
      }
    }
  </style>

  @stack('styles')
</head>
<body>
  <!-- Header -->
  <div class="mobile-header">
    <h1>@yield('header-title', 'Absensi')</h1>
    <div class="user-info">
      <div class="user-avatar">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
      </div>
    </div>
  </div>

  <!-- Content -->
  <div class="mobile-content">
    @yield('content')
  </div>

  <!-- Bottom Navigation -->
  <div class="bottom-nav">
    <a href="{{ route('mobile.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('mobile.dashboard') ? 'active' : '' }}">
      <i class="fas fa-home"></i>
      <span>Beranda</span>
    </a>
    <a href="{{ route('mobile.attendance') }}" class="bottom-nav-item {{ request()->routeIs('mobile.attendance*') ? 'active' : '' }}">
      <i class="fas fa-fingerprint"></i>
      <span>Absen</span>
    </a>
    <a href="{{ route('mobile.history') }}" class="bottom-nav-item {{ request()->routeIs('mobile.history') ? 'active' : '' }}">
      <i class="fas fa-history"></i>
      <span>Riwayat</span>
    </a>
    <a href="{{ route('mobile.leave') }}" class="bottom-nav-item {{ request()->routeIs('mobile.leave*') ? 'active' : '' }}">
      <i class="fas fa-calendar-alt"></i>
      <span>Izin</span>
    </a>
    <a href="{{ route('mobile.profile') }}" class="bottom-nav-item {{ request()->routeIs('mobile.profile') ? 'active' : '' }}">
      <i class="fas fa-user"></i>
      <span>Profil</span>
    </a>
  </div>

  <!-- Loading Overlay -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('stisla/assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  
  <script>
    // Show loading
    function showLoading() {
      $('#loadingOverlay').fadeIn();
    }

    // Hide loading
    function hideLoading() {
      $('#loadingOverlay').fadeOut();
    }

    // CSRF Token for AJAX
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
  </script>

  @stack('scripts')
</body>
</html>
