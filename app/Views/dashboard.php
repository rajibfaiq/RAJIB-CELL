<?= $this->include('templates/header') ?>

<div class="app-layout">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon"><i class="fas fa-hospital"></i></div>
      <div class="brand-text">
        <h2>RS Sejahtera</h2>
        <span style="font-size:12px;font-weight:600;color:var(--blue-400);letter-spacing:1px;display:block;">Kelompok 2</span>
        <span>SIMRS TUGAS AKHIR BASIS DATA</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">
        <div class="nav-section-title">Utama</div>
        <a class="nav-item active" data-page="dashboard" data-title="Dashboard"><i class="fas fa-th-large"></i> Dashboard</a>
      </div>
      <?php if(session()->get('role') === 'dokter' || session()->get('ID_DOKTER')): ?>
      <div class="nav-section">
        <div class="nav-section-title">Dokter</div>
        <a class="nav-item" data-page="antrian" data-title="Antrian Saya"><i class="fas fa-user-clock"></i> Antrian Saya</a>
      </div>
      <?php endif; ?>
      
      <?php if(session()->get('role') !== 'pasien'): ?>
      <div class="nav-section">
        <div class="nav-section-title">Data Master</div>
        <a class="nav-item" data-page="pasien" data-title="Data Pasien"><i class="fas fa-users"></i> Pasien</a>
        <a class="nav-item" data-page="dokter" data-title="Data Dokter"><i class="fas fa-user-md"></i> Dokter</a>
        <a class="nav-item" data-page="perawat" data-title="Data Perawat"><i class="fas fa-user-nurse"></i> Perawat</a>
        <a class="nav-item" data-page="kamarpage" data-title="Data Kamar"><i class="fas fa-door-open"></i> Kamar</a>
        <a class="nav-item" data-page="billing" data-title="Administrasi"><i class="fas fa-file-invoice-dollar"></i> Administrasi</a>
        <a class="nav-item" data-page="farmasi" data-title="Farmasi"><i class="fas fa-pills"></i> Farmasi</a>
        <a class="nav-item" data-page="laboratorium" data-title="Laboratorium"><i class="fas fa-flask"></i> Laboratorium</a>
      </div>
      <?php endif; ?>

      <div class="nav-section">
        <div class="nav-section-title">Transaksi</div>
        <a class="nav-item" data-page="pendaftaran" data-title="Pendaftaran"><i class="fas fa-clipboard-list"></i> Pendaftaran</a>
        <?php if(session()->get('role') !== 'pasien'): ?>
        <a class="nav-item" data-page="rekammedis" data-title="Pemeriksaan"><i class="fas fa-stethoscope"></i> Pemeriksaan</a>
        <a class="nav-item" data-page="rontgen" data-title="Rontgen"><i class="fas fa-x-ray"></i> Rontgen</a>
        <a class="nav-item" data-page="rawatjalan" data-title="Perawatan"><i class="fas fa-procedures"></i> Perawatan</a>
        <a class="nav-item" data-page="pengobatan" data-title="Pengobatan"><i class="fas fa-prescription-bottle-alt"></i> Pengobatan</a>
        <a class="nav-item" data-page="pembayaran" data-title="Pembayaran"><i class="fas fa-money-bill-wave"></i> Pembayaran</a>
        <?php endif; ?>
      </div>

      <?php if(session()->get('role') !== 'pasien'): ?>
      <div class="nav-section">
        <div class="nav-section-title">Laporan</div>
        <a class="nav-item" data-page="laporan" data-title="Laporan"><i class="fas fa-chart-bar"></i> Laporan</a>
      </div>
      <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="avatar"><?= strtoupper(substr(session()->get('fullname'), 0, 2)) ?></div>
        <div class="user-info">
          <h4><?= session()->get('fullname') ?></h4>
          <span><?= ucfirst(session()->get('role')) ?></span>
        </div>
        <a href="<?= site_url('logout') ?>" style="color:#8899aa;font-size:14px;" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
      </div>
    </div>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <div>
          <h2 id="page-title">Dashboard</h2>
          <div class="breadcrumb"><a href="#">Home</a> <span>/</span> <span>Dashboard</span></div>
        </div>
      </div>
      <div class="topbar-right">
        <div class="topbar-icon"><i class="fas fa-bell"></i></div>
      </div>
    </header>

    <div class="page-content">
      <?php if(session()->getFlashdata('success')): ?>
        <div style="background:#d4edda;color:#155724;padding:12px;border-radius:5px;margin-bottom:20px;border:1px solid #c3e6cb;">
          <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>
      
      <?php if(session()->getFlashdata('error')): ?>
        <div style="background:#f8d7da;color:#721c24;padding:12px;border-radius:5px;margin-bottom:20px;border:1px solid #f5c6cb;">
          <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <!-- Load Sub-Views -->
      <?= $this->include('dashboard/utama') ?>
      <?= $this->include('dashboard/antrian_dokter') ?>
      <?= $this->include('dashboard/pasien') ?>
      <?= $this->include('dashboard/dokter') ?>
      <?= $this->include('dashboard/perawat') ?>
      <?= $this->include('dashboard/kamar') ?>
      <?= $this->include('dashboard/pendaftaran') ?>
      <?= $this->include('dashboard/pemeriksaan') ?>
      <?= $this->include('dashboard/farmasi') ?>
      <?= $this->include('dashboard/laboratorium') ?>
      <?= $this->include('dashboard/administrasi') ?>
      <?= $this->include('dashboard/rontgen') ?>
      <?= $this->include('dashboard/perawatan') ?>
      <?= $this->include('dashboard/pengobatan') ?>
      <?= $this->include('dashboard/pembayaran') ?>
      <?= $this->include('dashboard/laporan') ?>
      <?= $this->include('dashboard/pengaturan') ?>
      
    </div>
  </main>
</div>

<?= $this->include('templates/footer') ?>
