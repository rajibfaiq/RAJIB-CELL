<!-- Dashboard Utama -->
<div class="page-section" id="page-dashboard">
  
  <?php if(session()->get('role') !== 'pasien'): ?>
  <!-- ADMIN/PETUGAS/DOKTER SIDE -->
  <div class="stats-grid">
    <div class="stat-card blue">
      <div class="stat-info"><p>Total Pasien</p><h3><?= $totalPasien ?></h3></div>
      <div class="stat-icon blue"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-card green">
      <div class="stat-info"><p>Rawat Jalan</p><h3><?= $totalRawatJalan ?></h3></div>
      <div class="stat-icon green"><i class="fas fa-walking"></i></div>
    </div>
    <div class="stat-card amber">
      <div class="stat-info"><p>Rawat Inap</p><h3><?= $totalRawatInap ?></h3></div>
      <div class="stat-icon amber"><i class="fas fa-bed"></i></div>
    </div>
    <div class="stat-card teal">
      <div class="stat-info"><p>Total Dokter</p><h3><?= $totalDokter ?></h3></div>
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
            <?php if(!empty($pasien)): foreach($pasien as $p): ?>
            <tr>
              <td><?= $p['ID_PASIEN'] ?></td>
              <td><?= $p['NAMA_PASIEN'] ?></td>
              <td>-</td>
              <td><span class="badge badge-success">Aktif</span></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="4" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h3>Jadwal Dokter Hari Ini</h3></div>
      <div class="card-body no-pad" style="max-height: 350px; overflow-y: auto;">
        <table class="data-table">
          <thead><tr><th>Dokter</th><th>Jam / Hari Praktek</th><th>Status</th></tr></thead>
          <tbody>
            <?php if(!empty($dokter)): foreach($dokter as $d): ?>
            <tr>
              <td>
                <div style="font-weight: 700; color: #2c3e50;"><?= $d['NAMA_DOKTER'] ?></div>
                <div style="font-size: 11px; color: #4a7dc7; font-weight: 600; margin-top: 2px;"><i class="fas fa-clinic-medical"></i> <?= $d['NAMA_POLI'] ?: 'Poli Umum' ?></div>
              </td>
              <td><?= $d['JADWAL'] ?></td>
              <td><span class="badge <?= $d['STATUS'] === 'Aktif' ? 'badge-success' : 'badge-danger' ?>"><?= $d['STATUS'] ?></span></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="3" style="text-align:center;padding:30px;color:#999;">Belum ada data dokter bertugas</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <?php else: ?>
  <!-- PASIEN SIDE -->
  <div style="background: linear-gradient(135deg, #4a7dc7 0%, #355c96 100%); color: #fff; padding: 30px; border-radius: 16px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 8px 24px rgba(74,125,199,0.15);">
    <div>
      <h2 style="font-weight: 800; font-size: 26px; margin-bottom: 8px;">Selamat Datang, <?= session()->get('fullname') ?>!</h2>
      <p style="opacity: 0.9; font-size: 14px; margin-bottom: 15px;">Berikut adalah informasi ringkas layanan kesehatan Anda hari ini.</p>
      <div style="display: flex; gap: 20px; font-size: 13px;">
        <div><i class="far fa-id-badge"></i> No. Rekam Medis: <strong><?= $pasienProfil['ID_PASIEN'] ?? '-' ?></strong></div>
        <div><i class="far fa-id-card"></i> NIK: <strong><?= $pasienProfil['NIK'] ?? '-' ?></strong></div>
      </div>
    </div>
    <div style="display: flex; flex-direction: column; gap: 10px;">
      <button onclick="document.querySelector('.nav-item[data-page=\'pendaftaran\']').click()" class="btn btn-primary" style="background:#fff; color:#4a7dc7; border:none; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 12px 24px; font-weight: 700; width: auto;"><i class="fas fa-calendar-plus"></i> Daftar Kunjungan Baru</button>
    </div>
  </div>

  <div class="grid-2">
    <!-- Antrian Aktif Card -->
    <div class="card">
      <div class="card-header"><h3 style="color:#2c3e50; font-weight:700;"><i class="fas fa-clock" style="color:#4a7dc7; margin-right:8px;"></i> Tiket Kunjungan Aktif</h3></div>
      <div class="card-body" style="padding: 20px;">
        <?php if(!empty($activeQueues)): foreach($activeQueues as $aq): ?>
          <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 15px; position: relative;">
            <div style="position: absolute; top: 20px; right: 20px; text-align: right;">
              <span style="font-size: 11px; text-transform: uppercase; color: #7f8c8d; display:block;">Nomor Antrian</span>
              <span style="font-size: 40px; font-weight: 900; color: #4a7dc7; line-height: 1;"><?= $aq['NO_ANTRIAN'] ?></span>
            </div>
            
            <h4 style="font-size: 16px; font-weight: 700; color: #2c3e50; margin-bottom: 4px;"><?= $aq['NAMA_DOKTER'] ?></h4>
            <span style="font-size: 12px; font-weight: 600; color: #4a7dc7; background: #e8f0fe; padding: 3px 10px; border-radius: 20px;"><i class="fas fa-clinic-medical"></i> <?= $aq['NAMA_POLI'] ?></span>
            
            <div style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 12px; border-top: 1px dashed #e2e8f0; padding-top: 15px;">
              <div>Tgl Kunjungan: <strong><?= date('d M Y', strtotime($aq['TANGGAL_KUNJUNGAN'])) ?></strong></div>
              <div>Sesi: <strong>Sesi <?= $aq['SESI_KUNJUNGAN'] ?></strong></div>
              <div style="grid-column: 1 / -1; margin-top: 5px;">
                <?php
                  // Compute call time
                  $startMinutes = 0;
                  if ($aq['SESI_KUNJUNGAN'] === 'Pagi') $startMinutes = 8 * 60;
                  elseif ($aq['SESI_KUNJUNGAN'] === 'Siang') $startMinutes = 13 * 60;
                  elseif ($aq['SESI_KUNJUNGAN'] === 'Sore') $startMinutes = 17 * 60;

                  $estimasiMinutes = $startMinutes + (($aq['NO_ANTRIAN'] - 1) * 15);
                  $estHours = floor($estimasiMinutes / 60);
                  $estMins = $estimasiMinutes % 60;
                  $estJam = str_pad($estHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($estMins, 2, '0', STR_PAD_LEFT) . ' WIB';
                ?>
                Estimasi Jam Panggil: <strong style="color:#27ae60;"><?= $estJam ?></strong>
              </div>
            </div>

            <div style="margin-top: 15px; display: flex; gap: 10px;">
              <a href="<?= site_url('pendaftaran/cetak/'.$aq['NO_PENDAFTARAN']) ?>" target="_blank" class="btn btn-primary btn-sm" style="width: auto; padding: 8px 16px;"><i class="fas fa-print"></i> Cetak Tiket</a>
            </div>
          </div>
        <?php endforeach; else: ?>
          <div style="text-align: center; padding: 40px 20px; color: #95a5a6;">
            <i class="fas fa-calendar-check" style="font-size: 48px; color: #cbd5e0; margin-bottom: 12px;"></i>
            <p style="font-size: 13px; margin:0;">Anda belum memiliki jadwal pendaftaran kunjungan aktif saat ini.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Informasi Profil Card -->
    <div class="card">
      <div class="card-header"><h3 style="color:#2c3e50; font-weight:700;"><i class="fas fa-user-circle" style="color:#4a7dc7; margin-right:8px;"></i> Informasi Profil Pasien</h3></div>
      <div class="card-body" style="padding: 20px;">
        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f2f6; padding-bottom: 8px;">
            <span style="color:#7f8c8d;">Nama Lengkap:</span>
            <strong style="color:#2c3e50;"><?= $pasienProfil['NAMA_PASIEN'] ?? '-' ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f2f6; padding-bottom: 8px;">
            <span style="color:#7f8c8d;">Tanggal Lahir:</span>
            <strong><?= $pasienProfil['TGL_LAHIR'] ? date('d F Y', strtotime($pasienProfil['TGL_LAHIR'])) : '-' ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f2f6; padding-bottom: 8px;">
            <span style="color:#7f8c8d;">Jenis Kelamin:</span>
            <strong><?= $pasienProfil['JENIS_KELAMIN'] ?? '-' ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f2f6; padding-bottom: 8px;">
            <span style="color:#7f8c8d;">No. Telp:</span>
            <strong><?= $pasienProfil['NO_TELP'] ?? '-' ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f2f6; padding-bottom: 8px;">
            <span style="color:#7f8c8d;">Penjaminan Pembayaran:</span>
            <span class="badge badge-success" style="font-weight:700;"><?= $pasienProfil['JENIS_PEMBAYARAN'] ?? 'Umum' ?></span>
          </div>
          <?php if(($pasienProfil['JENIS_PEMBAYARAN'] ?? '') === 'BPJS'): ?>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f2f6; padding-bottom: 8px;">
              <span style="color:#7f8c8d;">No. Kartu BPJS:</span>
              <strong><?= $pasienProfil['NO_BPJS'] ?? '-' ?></strong>
            </div>
          <?php elseif(($pasienProfil['JENIS_PEMBAYARAN'] ?? '') === 'Asuransi'): ?>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f2f6; padding-bottom: 8px;">
              <span style="color:#7f8c8d;">Asuransi & No. Polis:</span>
              <strong><?= $pasienProfil['NAMA_ASURANSI'] . ' (Polis: ' . $pasienProfil['NO_POLIS'] . ')' ?></strong>
            </div>
          <?php endif; ?>
          <div style="display: flex; flex-direction:column; gap:4px;">
            <span style="color:#7f8c8d;">Alamat Lengkap:</span>
            <span style="line-height:1.4; font-weight: 600; color:#2c3e50;">
              <?= $pasienProfil['ALAMAT_PASIEN'] ?>, Kel. <?= $pasienProfil['KELURAHAN'] ?>, Kec. <?= $pasienProfil['KECAMATAN'] ?>, <?= $pasienProfil['KOTA'] ?>, <?= $pasienProfil['PROVINSI'] ?>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
  
</div>

