<?= $this->include('templates/header') ?>

<div class="app-layout">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon"><i class="fas fa-hospital"></i></div>
      <div class="brand-text">
        <h2>RS Sejahtera</h2>
        <span>SIMRS v1.0</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">
        <div class="nav-section-title">Menu Utama</div>
        <a class="nav-item active" data-page="dashboard" data-title="Dashboard"><i class="fas fa-th-large"></i> Dashboard</a>
        <a class="nav-item" data-page="pasien" data-title="Data Pasien"><i class="fas fa-users"></i> Data Pasien</a>
        <a class="nav-item" data-page="rawatjalan" data-title="Rawat Jalan"><i class="fas fa-walking"></i> Rawat Jalan</a>
        <a class="nav-item" data-page="rawatinap" data-title="Rawat Inap"><i class="fas fa-bed"></i> Rawat Inap</a>
      </div>
      <div class="nav-section">
        <div class="nav-section-title">Medis</div>
        <a class="nav-item" data-page="dokter" data-title="Data Dokter"><i class="fas fa-user-md"></i> Data Dokter</a>
        <a class="nav-item" data-page="rekammedis" data-title="Rekam Medis"><i class="fas fa-file-medical"></i> Rekam Medis</a>
        <a class="nav-item" data-page="farmasi" data-title="Farmasi"><i class="fas fa-pills"></i> Farmasi</a>
        <a class="nav-item" data-page="laboratorium" data-title="Laboratorium"><i class="fas fa-flask"></i> Laboratorium</a>
      </div>
      <div class="nav-section">
        <div class="nav-section-title">Administrasi</div>
        <a class="nav-item" data-page="billing" data-title="Billing & Kasir"><i class="fas fa-cash-register"></i> Billing & Kasir</a>
        <a class="nav-item" data-page="laporan" data-title="Laporan"><i class="fas fa-chart-bar"></i> Laporan</a>
        <a class="nav-item" data-page="pengaturan" data-title="Pengaturan"><i class="fas fa-cog"></i> Pengaturan</a>
      </div>
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
      <!-- Dashboard -->
      <div class="page-section" id="page-dashboard">
        <div class="stats-grid">
          <div class="stat-card blue">
            <div class="stat-info"><p>Total Pasien</p><h3>0</h3></div>
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
          </div>
          <div class="stat-card green">
            <div class="stat-info"><p>Rawat Jalan Hari Ini</p><h3>0</h3></div>
            <div class="stat-icon green"><i class="fas fa-walking"></i></div>
          </div>
          <div class="stat-card amber">
            <div class="stat-info"><p>Rawat Inap Aktif</p><h3>0</h3></div>
            <div class="stat-icon amber"><i class="fas fa-bed"></i></div>
          </div>
          <div class="stat-card teal">
            <div class="stat-info"><p>Total Dokter</p><h3>0</h3></div>
            <div class="stat-icon teal"><i class="fas fa-user-md"></i></div>
          </div>
        </div>

        <div class="grid-2">
          <div class="card">
            <div class="card-header"><h3>Pasien Baru Hari Ini</h3></div>
            <div class="card-body no-pad">
              <table class="data-table">
                <thead><tr><th>No. RM</th><th>Nama</th><th>Poli</th><th>Status</th></tr></thead>
                <tbody>
                  <tr><td colspan="4" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>Jadwal Dokter Hari Ini</h3></div>
            <div class="card-body no-pad">
              <table class="data-table">
                <thead><tr><th>Dokter</th><th>Spesialis</th><th>Jam</th><th>Status</th></tr></thead>
                <tbody>
                  <tr><td colspan="4" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Data Pasien -->
      <div class="page-section" id="page-pasien" style="display:none;">
        <div class="toolbar">
          <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari pasien..."></div>
          <button class="btn btn-primary btn-sm" data-modal="modal-pasien"><i class="fas fa-plus"></i> Tambah Pasien</button>
        </div>
        <div class="card">
          <div class="card-body no-pad">
            <table class="data-table">
              <thead><tr><th>No. RM</th><th>Nama Lengkap</th><th>L/P</th><th>Tgl Lahir</th><th>Alamat</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr><td colspan="7" style="text-align:center;padding:30px;color:#999;">Belum ada data pasien</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Data Dokter -->
      <div class="page-section" id="page-dokter" style="display:none;">
        <div class="toolbar">
          <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari dokter..."></div>
          <button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Dokter</button>
        </div>
        <div class="card">
          <div class="card-body no-pad">
            <table class="data-table">
              <thead><tr><th>NIP</th><th>Nama Dokter</th><th>Spesialisasi</th><th>Jadwal</th><th>Status</th></tr></thead>
              <tbody>
                <tr><td colspan="5" style="text-align:center;padding:30px;color:#999;">Belum ada data dokter</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Placeholder pages -->
      <div class="page-section" id="page-rawatjalan" style="display:none;">
        <div class="card"><div class="card-body" style="text-align:center;padding:50px;">
          <i class="fas fa-walking" style="font-size:40px;color:#aaa;margin-bottom:12px;"></i>
          <h3 style="color:#555;margin-bottom:6px;">Rawat Jalan</h3>
          <p style="color:#999;font-size:13px;">Belum ada data.</p>
        </div></div>
      </div>
      <div class="page-section" id="page-rawatinap" style="display:none;">
        <div class="card"><div class="card-body" style="text-align:center;padding:50px;">
          <i class="fas fa-bed" style="font-size:40px;color:#aaa;margin-bottom:12px;"></i>
          <h3 style="color:#555;margin-bottom:6px;">Rawat Inap</h3>
          <p style="color:#999;font-size:13px;">Belum ada data.</p>
        </div></div>
      </div>
      <div class="page-section" id="page-rekammedis" style="display:none;">
        <div class="card"><div class="card-body" style="text-align:center;padding:50px;">
          <i class="fas fa-file-medical" style="font-size:40px;color:#aaa;margin-bottom:12px;"></i>
          <h3 style="color:#555;margin-bottom:6px;">Rekam Medis</h3>
          <p style="color:#999;font-size:13px;">Belum ada data.</p>
        </div></div>
      </div>
      <div class="page-section" id="page-farmasi" style="display:none;">
        <div class="card"><div class="card-body" style="text-align:center;padding:50px;">
          <i class="fas fa-pills" style="font-size:40px;color:#aaa;margin-bottom:12px;"></i>
          <h3 style="color:#555;margin-bottom:6px;">Farmasi</h3>
          <p style="color:#999;font-size:13px;">Belum ada data.</p>
        </div></div>
      </div>
      <div class="page-section" id="page-laboratorium" style="display:none;">
        <div class="card"><div class="card-body" style="text-align:center;padding:50px;">
          <i class="fas fa-flask" style="font-size:40px;color:#aaa;margin-bottom:12px;"></i>
          <h3 style="color:#555;margin-bottom:6px;">Laboratorium</h3>
          <p style="color:#999;font-size:13px;">Belum ada data.</p>
        </div></div>
      </div>
      <div class="page-section" id="page-billing" style="display:none;">
        <div class="card"><div class="card-body" style="text-align:center;padding:50px;">
          <i class="fas fa-cash-register" style="font-size:40px;color:#aaa;margin-bottom:12px;"></i>
          <h3 style="color:#555;margin-bottom:6px;">Billing & Kasir</h3>
          <p style="color:#999;font-size:13px;">Belum ada data.</p>
        </div></div>
      </div>
      <div class="page-section" id="page-laporan" style="display:none;">
        <div class="card"><div class="card-body" style="text-align:center;padding:50px;">
          <i class="fas fa-chart-bar" style="font-size:40px;color:#aaa;margin-bottom:12px;"></i>
          <h3 style="color:#555;margin-bottom:6px;">Laporan</h3>
          <p style="color:#999;font-size:13px;">Belum ada data.</p>
        </div></div>
      </div>
      <div class="page-section" id="page-pengaturan" style="display:none;">
        <div class="card"><div class="card-body" style="text-align:center;padding:50px;">
          <i class="fas fa-cog" style="font-size:40px;color:#aaa;margin-bottom:12px;"></i>
          <h3 style="color:#555;margin-bottom:6px;">Pengaturan</h3>
          <p style="color:#999;font-size:13px;">Belum ada data.</p>
        </div></div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Tambah Pasien -->
<div class="modal-overlay" id="modal-pasien">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Pasien Baru</h3>
      <button class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div><label class="form-label">Nama Lengkap</label><input class="form-control" placeholder="Nama lengkap"></div>
        <div><label class="form-label">Jenis Kelamin</label><select class="form-control"><option>Laki-laki</option><option>Perempuan</option></select></div>
      </div>
      <div class="form-row">
        <div><label class="form-label">Tanggal Lahir</label><input type="date" class="form-control"></div>
        <div><label class="form-label">No. Telepon</label><input class="form-control" placeholder="08xxxxxxxxxx"></div>
      </div>
      <div class="form-row single">
        <div><label class="form-label">Alamat</label><textarea class="form-control" placeholder="Alamat lengkap"></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm modal-close">Batal</button>
      <button class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
    </div>
  </div>
</div>

<?= $this->include('templates/footer') ?>
