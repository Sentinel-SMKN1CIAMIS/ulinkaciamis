<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk - Portal Wilayah UlinKa Ciamis</title>
  <link rel="icon" type="image/webp" href="{{ asset('images/ulinkaCiamis.webp') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <style>
    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      min-height: 100vh;
      background: radial-gradient(circle at 20% 20%, #2e0327 0%, #150212 50%, #0a0109 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      color: #1e293b;
      position: relative;
      overflow-x: hidden;
    }

    /* Ambient glowing orbs */
    .glow-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(120px);
      pointer-events: none;
      z-index: 0;
    }
    .glow-1 {
      width: 450px;
      height: 450px;
      background: rgba(158, 11, 132, 0.28);
      top: -100px;
      left: -100px;
    }
    .glow-2 {
      width: 400px;
      height: 400px;
      background: rgba(220, 38, 38, 0.15);
      bottom: -80px;
      right: -80px;
    }

    /* Outer Wrapper Card */
    .auth-container {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 980px;
      background: #ffffff;
      border-radius: 24px;
      box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45);
      display: flex;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    /* Left Brand Panel */
    .brand-panel {
      width: 45%;
      background: linear-gradient(145deg, #7a0d66 0%, #48063d 60%, #280222 100%);
      color: #ffffff;
      padding: 48px 36px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
    }
    .brand-panel::after {
      content: '';
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
      background-size: 20px 20px;
      opacity: 0.7;
      pointer-events: none;
    }
    .brand-content {
      position: relative;
      z-index: 2;
    }
    .shield-badge {
      width: 68px;
      height: 68px;
      background: rgba(255, 255, 255, 0.12);
      border: 1.5px solid rgba(255, 255, 255, 0.25);
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 8px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      margin-bottom: 24px;
    }
    .shield-badge img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
    .brand-title {
      font-size: 23px;
      font-weight: 800;
      letter-spacing: -0.5px;
      line-height: 1.25;
      margin-bottom: 6px;
    }
    .brand-tagline {
      font-size: 13px;
      color: #fbcfe8;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 16px;
    }
    .gold-divider {
      width: 48px;
      height: 3px;
      background: #fbbf24;
      border-radius: 2px;
      margin-bottom: 20px;
    }
    .brand-desc {
      font-size: 13px;
      line-height: 1.6;
      color: #fce7f3;
      margin-bottom: 28px;
    }
    .feature-pills {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .pill-item {
      font-size: 12px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 7px 12px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #fdf2f8;
      font-weight: 500;
    }
    .pill-item i {
      color: #fbbf24;
      font-size: 13px;
    }

    /* Right Form Panel */
    .form-panel {
      width: 55%;
      padding: 48px 42px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #ffffff;
    }
    .form-header {
      margin-bottom: 26px;
    }
    .logo-container {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }
    .logo-container img {
      height: 56px;
      max-height: 62px;
      width: auto;
      max-width: 220px;
      object-fit: contain;
    }
    .header-badge {
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      background: #fdf2fa;
      color: #9e0b84;
      border: 1px solid rgba(158, 11, 132, 0.2);
      padding: 3px 8px;
      border-radius: 6px;
      letter-spacing: 0.5px;
    }
    .form-title {
      font-size: 22px;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.4px;
      margin-bottom: 4px;
    }
    .form-subtitle {
      font-size: 13px;
      color: #64748b;
    }

    /* Alerts */
    .alert-box {
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-radius: 10px;
      padding: 10px 14px;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #b91c1c;
      font-size: 12.5px;
      font-weight: 600;
    }

    /* Form Fields */
    .form-group {
      margin-bottom: 16px;
    }
    .form-label {
      display: block;
      font-size: 12.5px;
      font-weight: 700;
      color: #334155;
      margin-bottom: 6px;
    }
    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    .input-icon {
      position: absolute;
      left: 14px;
      color: #94a3b8;
      font-size: 14px;
      pointer-events: none;
      transition: color 0.2s ease;
    }
    .form-input {
      width: 100%;
      height: 44px;
      padding: 0 42px 0 40px;
      font-family: inherit;
      font-size: 13.5px;
      color: #0f172a;
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      outline: none;
      transition: all 0.2s ease;
    }
    .form-input:focus {
      background: #ffffff;
      border-color: #9e0b84;
      box-shadow: 0 0 0 3px rgba(158, 11, 132, 0.12);
    }
    .toggle-pwd-btn {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      font-size: 14px;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.2s ease;
    }
    .toggle-pwd-btn:hover {
      color: #9e0b84;
    }

    /* Submit Button */
    .btn-submit {
      width: 100%;
      height: 46px;
      background: linear-gradient(135deg, #9e0b84 0%, #7a0d66 100%);
      color: #ffffff;
      border: none;
      border-radius: 10px;
      font-family: inherit;
      font-size: 14px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(158, 11, 132, 0.35);
      transition: all 0.25s ease;
      margin-top: 6px;
    }
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(158, 11, 132, 0.45);
      background: linear-gradient(135deg, #b21497 0%, #9e0b84 100%);
    }

    /* Quick Fill Demo Bar */
    .demo-section {
      margin-top: 20px;
      padding-top: 16px;
      border-top: 1px solid #f1f5f9;
    }
    .demo-header {
      font-size: 11px;
      font-weight: 700;
      color: #64748b;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .demo-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }
    .demo-chip {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      color: #334155;
      font-size: 11px;
      font-weight: 600;
      padding: 5px 10px;
      border-radius: 8px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all 0.2s ease;
    }
    .demo-chip:hover {
      background: #fdf2fa;
      border-color: #9e0b84;
      color: #9e0b84;
      transform: translateY(-1px);
    }
    .demo-chip.primary {
      background: #fdf2fa;
      border-color: rgba(158, 11, 132, 0.3);
      color: #9e0b84;
    }

    .back-nav {
      margin-top: 16px;
      text-align: center;
    }
    .back-nav a {
      font-size: 12px;
      font-weight: 600;
      color: #64748b;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .back-nav a:hover {
      color: #9e0b84;
    }

    @media (max-width: 880px) {
      .auth-container {
        flex-direction: column;
        max-width: 460px;
      }
      .brand-panel {
        width: 100%;
        padding: 36px 24px;
      }
      .form-panel {
        padding: 36px 24px;
      }
    }
  </style>
</head>
<body>

  <div class="glow-orb glow-1"></div>
  <div class="glow-orb glow-2"></div>

  <div class="auth-container">
    
    <!-- LEFT: BRAND HERO PANEL -->
    <div class="brand-panel">
      <div class="brand-content">
        <div class="shield-badge">
          <img src="{{ asset('assets/images/lambang_ciamis.png') }}" alt="Lambang Kabupaten Ciamis">
        </div>

        <h1 class="brand-title">Pemerintah Kabupaten Ciamis</h1>
        <div class="brand-tagline">Mahayunan Ayuna Kadatuan</div>

        <div class="gold-divider"></div>

        <p class="brand-desc">
          Portal Administrasi Terpadu Direktori Pemetaan &amp; Fasilitas Publik Tatar Galuh Ciamis. Dikelola oleh Superadmin &amp; Staff Wilayah Per Kecamatan.
        </p>

        <div class="feature-pills">
          <span class="pill-item"><i class="fa-solid fa-map-location-dot"></i> 27 Kecamatan Terdata</span>
          <span class="pill-item"><i class="fa-solid fa-layer-group"></i> Pemetaan Mandiri Per Wilayah</span>
          <span class="pill-item"><i class="fa-solid fa-shield-halved"></i> Hak Akses Petugas Daerah</span>
        </div>
      </div>
    </div>

    <!-- RIGHT: LOGIN FORM PANEL -->
    <div class="form-panel">
      <div class="form-header">
        <div class="logo-container">
          <img src="{{ asset('images/ulinkaCiamis.webp') }}" alt="UlinKa Ciamis">
          <span class="header-badge">Admin Panel</span>
        </div>
        <h2 class="form-title">Masuk ke Sistem</h2>
        <p class="form-subtitle">Silakan masukkan username dan password akun pengurus Anda</p>
      </div>

      @if ($errors->any())
        <div class="alert-box">
          <i class="fa-solid fa-circle-exclamation"></i>
          <span>{{ $errors->first() }}</span>
        </div>
      @endif

      @if (request('logout'))
        <div class="alert-box" style="background:#ecfdf5; border-color:#a7f3d0; color:#065f46;">
          <i class="fa-solid fa-circle-check"></i>
          <span>Sesi Anda telah berhasil diakhiri.</span>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf
        <div class="form-group">
          <label class="form-label" for="username">Username atau Email Akun</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-user input-icon"></i>
            <input 
              type="text" 
              id="username" 
              name="username" 
              class="form-input" 
              placeholder="Contoh: superadmin atau staff_panjalu" 
              value="{{ old('username') }}" 
              required 
              autofocus 
              autocomplete="username"
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-lock input-icon"></i>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-input" 
              placeholder="Masukkan password" 
              required 
              autocomplete="current-password"
            >
            <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility()" title="Lihat password">
              <i class="fa-regular fa-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit">
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
          <span>Masuk ke Dashboard</span>
        </button>
      </form>

      <!-- Demo Accounts Quick Fill -->
      <div class="demo-section">
        <div class="demo-header">
          <span><i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i> Klik Cepat Akun Demo Petugas:</span>
        </div>
        <div class="demo-grid">
          <button type="button" class="demo-chip primary" onclick="fillDemo('superadmin', 'password')">
            <i class="fa-solid fa-crown"></i> Superadmin (Pusat)
          </button>
          <button type="button" class="demo-chip" onclick="fillDemo('staff_panjalu', 'password')">
            <i class="fa-solid fa-map-pin"></i> Staff Kec. Panjalu
          </button>
          <button type="button" class="demo-chip" onclick="fillDemo('staff_kawali', 'password')">
            <i class="fa-solid fa-map-pin"></i> Staff Kec. Kawali
          </button>
          <button type="button" class="demo-chip" onclick="fillDemo('staff_sadananya', 'password')">
            <i class="fa-solid fa-map-pin"></i> Staff Kec. Sadananya
          </button>
          <button type="button" class="demo-chip" onclick="fillDemo('staff_ciamis', 'password')">
            <i class="fa-solid fa-map-pin"></i> Staff Kec. Ciamis
          </button>
        </div>
      </div>

      <!-- Back to Public Map -->
      <div class="back-nav">
        <a href="{{ url('/') }}">
          <i class="fa-solid fa-arrow-left"></i> Kembali ke Peta Publik UlinKa Ciamis
        </a>
      </div>

    </div>
  </div>

  <script>
    function togglePasswordVisibility() {
      const pwdInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        eyeIcon.className = 'fa-regular fa-eye-slash';
      } else {
        pwdInput.type = 'password';
        eyeIcon.className = 'fa-regular fa-eye';
      }
    }

    function fillDemo(username, password) {
      const u = document.getElementById('username');
      const p = document.getElementById('password');
      u.value = username;
      p.value = password;
      u.focus();

      [u, p].forEach(el => {
        el.style.borderColor = '#9e0b84';
        el.style.backgroundColor = '#fdf2fa';
        setTimeout(() => {
          el.style.borderColor = '';
          el.style.backgroundColor = '';
        }, 500);
      });
    }
  </script>
</body>
</html>
