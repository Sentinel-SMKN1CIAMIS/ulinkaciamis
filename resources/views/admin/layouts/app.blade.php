<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Panel') - UlinKa Ciamis</title>
  <link rel="icon" type="image/webp" href="{{ asset('images/ulinkaCiamis.webp') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
  @stack('styles')
</head>
<body>
  @include('admin.partials.sidebar')
  <div class="admin-main">
    @include('admin.partials.topbar', ['title' => $__env->yieldContent('title', 'Panel Administrator')])
    <div class="admin-content">
      @if (session('success'))
        <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 18px; border-radius:10px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-weight:600; font-size:13px;">
          <i class="fas fa-circle-check"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      @if (session('error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 18px; border-radius:10px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-weight:600; font-size:13px;">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      @yield('content')
    </div>
  </div>
  @stack('scripts')
</body>
</html>
