<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Login - SIMRS RS Sejahtera">
  <title>Login | SIMRS RS Sejahtera</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('css/simrs.css') ?>">
</head>
<body>
  <div class="login-page">
    <div class="login-card">
      <div class="login-logo">
        <div class="hospital-icon"><i class="fas fa-hospital"></i></div>
        <h1>RS Sejahtera</h1>
        <p style="font-size:14px;font-weight:600;color:var(--blue-600);margin-top:4px;letter-spacing:1px;">Kelompok 2</p>
        <p>Sistem Informasi Manajemen Rumah Sakit</p>
      </div>
      <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#fee2e2;color:#dc2626;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
          <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>
      <form id="loginForm" action="<?= site_url('auth/login') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrapper">
            <i class="fas fa-user"></i>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
          </div>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
          </div>
        </div>
        <div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;">
          <label style="font-size:13px;color:var(--gray-600);display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" name="remember" style="accent-color:var(--blue-600);"> Ingat saya
          </label>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom: 12px;"><i class="fas fa-sign-in-alt"></i> Masuk</button>
        <a href="<?= site_url('register') ?>" class="btn btn-outline" style="display: block; text-align: center; text-decoration: none; padding: 12px; font-weight: 700; border-radius: 8px;"><i class="fas fa-user-plus"></i> Daftar Pasien Baru</a>
      </form>
      <div style="text-align:center;margin-top:20px;padding-top:15px;border-top:1px solid var(--gray-100);">
        <p style="font-size:12px;color:var(--gray-400);">© 2026 SIMRS RS Sejahtera v1.0</p>
      </div>
    </div>
  </div>
  <script src="<?= base_url('js/simrs.js') ?>"></script>
</body>
</html>
