<!-- Pendaftaran -->
<div class="page-section" id="page-pendaftaran" style="display:none;">
  
  <!-- ROLE CHECKS -->
  <?php 
    $userRole = session()->get('role');
    $isPatient = ($userRole === 'pasien');
  ?>

  <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <?php if (!$isPatient): ?>
      <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari pendaftaran..."></div>
    <?php else: ?>
      <div style="font-weight: 700; color: #2c3e50; font-size: 16px;"><i class="fas fa-history" style="color: #4a7dc7; margin-right: 5px;"></i> Riwayat Pendaftaran Anda</div>
    <?php endif; ?>
    <button class="btn btn-primary btn-sm" onclick="startRegistrationWizard()"><i class="fas fa-plus"></i> <?= $isPatient ? 'Daftar Kunjungan Baru' : 'Tambah Pendaftaran' ?></button>
  </div>
  
  <!-- REGISTRATION LIST TABLE -->
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead>
          <tr>
            <th>No. Daftar</th>
            <th>Nama Pasien</th>
            <th>Poli Tujuan</th>
            <th>Dokter</th>
            <th>Tgl & Sesi Kunjungan</th>
            <th>No. Antrian</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $displayList = $isPatient ? $historyQueues : $pendaftaran;
            if(!empty($displayList)): foreach($displayList as $pn): 
          ?>
          <tr>
            <td><span class="badge-status info" style="font-family: monospace; font-size: 12px; font-weight: bold;"><?= $pn['NO_PENDAFTARAN'] ?></span></td>
            <td>
              <?php if (!$isPatient): ?>
                <a href="javascript:void(0)" onclick="goToBillingFromPendaftaran('<?= $pn['NO_PENDAFTARAN'] ?>')" style="color: #4a7dc7; font-weight: bold; text-decoration: underline; cursor: pointer;" title="Klik untuk memproses/lihat rincian tagihan billing pasien">
                  <?= $pn['NAMA_PASIEN'] ?>
                </a>
              <?php else: ?>
                <strong><?= $pn['NAMA_PASIEN'] ?></strong>
              <?php endif; ?>
              <br><small style="color:#888;"><?= $pn['ID_PASIEN'] ?></small>
            </td>
            <td><i class="fas fa-clinic-medical" style="color: #4a7dc7; margin-right: 5px;"></i> <?= $pn['NAMA_POLI'] ?: '-' ?></td>
            <td><strong><?= $pn['NAMA_DOKTER'] ?: '-' ?></strong></td>
            <td>
              <strong><?= $pn['TANGGAL_KUNJUNGAN'] ? date('d M Y', strtotime($pn['TANGGAL_KUNJUNGAN'])) : '-' ?></strong>
              <br><small style="color:#666; font-weight:600;">Sesi <?= $pn['SESI_KUNJUNGAN'] ?: 'Pagi' ?></small>
            </td>
            <td>
              <?php if($pn['NO_ANTRIAN']): ?>
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #e8f0fe; color: #4a7dc7; border-radius: 50%; font-weight: bold;">
                  <?= $pn['NO_ANTRIAN'] ?>
                </div>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td>
              <?php
                $statusClass = 'pending';
                if ($pn['STATUS_ANTRIAN'] === 'sedang_diperiksa') $statusClass = 'info';
                if ($pn['STATUS_ANTRIAN'] === 'selesai') $statusClass = 'active';
                if ($pn['STATUS_ANTRIAN'] === 'batal') $statusClass = 'inactive';
              ?>
              <span class="badge-status <?= $statusClass ?>" style="text-transform: capitalize; font-weight: 600;">
                <?= str_replace('_', ' ', $pn['STATUS_ANTRIAN']) ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 8px;">
                <a href="<?= site_url('pendaftaran/cetak/'.$pn['NO_PENDAFTARAN']) ?>" target="_blank" class="btn-icon" style="background:#e8f0fe; color:#4a7dc7;" title="Cetak Tiket Antrian"><i class="fas fa-print"></i></a>
                
                <?php if (!$isPatient): ?>
                  <button type="button" class="btn-icon edit" onclick="openEditPendaftaranModal(<?= htmlspecialchars(json_encode($pn)) ?>)" title="Edit Pendaftaran"><i class="fas fa-edit"></i></button>
                  <?php if ($pn['STATUS_ANTRIAN'] !== 'batal' && $pn['STATUS_ANTRIAN'] !== 'selesai'): ?>
                    <a href="<?= site_url('pendaftaran/batal/'.$pn['NO_PENDAFTARAN']) ?>" class="btn-icon delete" onclick="return confirm('Batalkan pendaftaran ini?')" title="Batalkan Pendaftaran"><i class="fas fa-times-circle"></i></a>
                  <?php endif; ?>
                  <a href="<?= site_url('pendaftaran/delete/'.$pn['NO_PENDAFTARAN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data pendaftaran secara permanen?')" title="Hapus Permanen"><i class="fas fa-trash"></i></a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="8" style="text-align:center;padding:30px;color:#999;">Belum ada data pendaftaran</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ==========================================
     MODAL TAMBAH PENDAFTARAN WIZARD (MULTI-STEP)
     ========================================== -->
<div class="modal-overlay" id="modal-pendaftaran">
  <div class="modal" style="max-width: 850px; width: 95%; max-height: 90vh;">
    <form action="<?= site_url('pendaftaran/save') ?>" method="post" id="form-pendaftaran-wizard" onsubmit="submitPendaftaranWizard(event)" novalidate>
      <!-- Wizard States -->
      <input type="hidden" name="is_new_patient" id="wizard-is-new-patient" value="0">
      <input type="hidden" name="id_pasien" id="wizard-id-pasien">
      <input type="hidden" name="id_poli" id="wizard-id-poli">
      <input type="hidden" name="id_dokter" id="wizard-id-dokter">
      
      <div class="modal-header">
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h3 style="font-weight: 700; color: #2c3e50;">Pendaftaran Kunjungan Pasien</h3>
          <span style="font-size: 12px; color: #7f8c8d;">Isi data langkah demi langkah untuk menyelesaikan pendaftaran</span>
        </div>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>

      <!-- Step Indicator Bar -->
      <div class="wizard-steps-header" style="display: flex; justify-content: space-around; background: #f8f9fa; padding: 15px; border-bottom: 1px solid #eee;">
        <div class="wizard-step-indicator active" id="wizard-step-ind-1" style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size:12px; color: #4a7dc7;">
          <span class="step-num" style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #4a7dc7; color: #fff;">1</span>
          Profil Pasien
        </div>
        <div class="wizard-step-indicator" id="wizard-step-ind-2" style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size:12px; color: #95a5a6;">
          <span class="step-num" style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #bdc3c7; color: #fff;">2</span>
          Poli Tujuan
        </div>
        <div class="wizard-step-indicator" id="wizard-step-ind-3" style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size:12px; color: #95a5a6;">
          <span class="step-num" style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #bdc3c7; color: #fff;">3</span>
          Tanggal & Sesi
        </div>
        <div class="wizard-step-indicator" id="wizard-step-ind-4" style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size:12px; color: #95a5a6;">
          <span class="step-num" style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #bdc3c7; color: #fff;">4</span>
          Dokter Spesialis
        </div>
        <div class="wizard-step-indicator" id="wizard-step-ind-5" style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size:12px; color: #95a5a6;">
          <span class="step-num" style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #bdc3c7; color: #fff;">5</span>
          Konfirmasi
        </div>
      </div>

      <div class="modal-body" style="padding: 24px; overflow-y: auto; max-height: 55vh;">
        
        <!-- ==========================================
             LANGKAH 1: PROFIL PASIEN (NEW / OLD SELECT)
             ========================================== -->
        <div class="wizard-panel" id="wizard-panel-1">
          <!-- Selection buttons -->
          <div style="display: flex; gap: 20px; margin-bottom: 25px; justify-content: center;">
            <button type="button" class="btn btn-outline" id="btn-select-old-patient" onclick="setPatientType('old')" style="flex: 1; padding: 20px; font-size:15px; border-color: #4a7dc7; color:#4a7dc7; background: #f0f5ff; font-weight:700;"><i class="fas fa-search-dollar" style="font-size:20px; margin-bottom: 8px; display:block;"></i> Pasien Lama (Cari NIK / Rekam Medis)</button>
            <button type="button" class="btn btn-outline" id="btn-select-new-patient" onclick="setPatientType('new')" style="flex: 1; padding: 20px; font-size:15px; font-weight:700;"><i class="fas fa-user-plus" style="font-size:20px; margin-bottom: 8px; display:block;"></i> Pasien Baru (Isi Data Lengkap)</button>
          </div>

          <!-- Pasien Lama: Search bar -->
          <div id="wizard-old-patient-section" style="background:#f8f9fa; padding:20px; border-radius:8px; border:1px solid #eef2f5; margin-bottom:15px;">
            <label class="form-label" style="font-weight: 700;">Masukkan NIK atau Nomor Rekam Medis Pasien</label>
            <div style="display: flex; gap: 10px; margin-top: 8px;">
              <input type="text" id="old-patient-search-query" class="form-control" placeholder="Contoh: 16 digit NIK atau RM001..." style="padding: 10px;">
              <button type="button" class="btn btn-primary" onclick="lookupOldPatient()" style="width: auto; padding: 10px 20px;"><i class="fas fa-search"></i> Cari Data</button>
            </div>
            <!-- Search Results Display -->
            <div id="old-patient-results" style="margin-top: 15px; display: none; background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #dcdde1;">
              <!-- Loaded via JS -->
            </div>
          </div>

          <!-- Pasien Baru: Form Fields -->
          <div id="wizard-new-patient-section" style="display:none; border-top:1px solid #eee; padding-top:20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div class="form-group" style="grid-column: 1 / -1; font-weight: 700; color: #4a7dc7; border-bottom: 1px solid #eee; padding-bottom: 6px; margin-bottom: 8px;"><i class="fas fa-user"></i> Identitas Diri Pasien Baru</div>
              
              <div class="form-group">
                <label class="form-label">Nama Lengkap Pasien <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="nama_pasien" class="form-control field-new-patient" placeholder="Nama lengkap sesuai KTP" required>
              </div>

              <div class="form-group">
                <label class="form-label">NIK (16 Digit) <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="nik" class="form-control field-new-patient" maxlength="16" placeholder="Masukkan 16 digit NIK" required>
              </div>

              <div class="form-group">
                <label class="form-label">Tanggal Lahir <span style="color:#e74c3c;">*</span></label>
                <input type="date" name="tgl_lahir" class="form-control field-new-patient" onchange="calculateWizardAge(this.value)" required>
              </div>

              <div class="form-group">
                <label class="form-label">Usia</label>
                <input type="text" id="wizard-calculated-age" class="form-control" readonly placeholder="Otomatis" style="background:#f8f9fa;">
              </div>

              <div class="form-group">
                <label class="form-label">Jenis Kelamin <span style="color:#e74c3c;">*</span></label>
                <select name="jenis_kelamin" class="form-control field-new-patient" style="height:42px;" required>
                  <option value="">-- Pilih Jenis Kelamin --</option>
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Nomor Telepon <span style="color:#e74c3c;">*</span></label>
                <input type="tel" name="no_telp" class="form-control field-new-patient" placeholder="Contoh: 08123456789" required>
              </div>

              <div class="form-group" style="grid-column: 1 / -1; font-weight: 700; color: #4a7dc7; border-bottom: 1px solid #eee; padding-bottom: 6px; margin-bottom: 8px;"><i class="fas fa-map-marker-alt"></i> Alamat Lengkap</div>

              <div class="form-group">
                <label class="form-label">Provinsi <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="provinsi" class="form-control field-new-patient" placeholder="Provinsi" required>
              </div>

              <div class="form-group">
                <label class="form-label">Kota / Kabupaten <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="kota" class="form-control field-new-patient" placeholder="Kota / Kabupaten" required>
              </div>

              <div class="form-group">
                <label class="form-label">Kecamatan <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="kecamatan" class="form-control field-new-patient" placeholder="Kecamatan" required>
              </div>

              <div class="form-group">
                <label class="form-label">Kelurahan <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="kelurahan" class="form-control field-new-patient" placeholder="Kelurahan" required>
              </div>

              <div class="form-group" style="grid-column: 1 / -1;">
                <label class="form-label">Alamat Lengkap Detail <span style="color:#e74c3c;">*</span></label>
                <textarea name="alamat_pasien" class="form-control field-new-patient" placeholder="RT/RW, Komplek, No Rumah..." style="min-height:70px; resize:vertical;" required></textarea>
              </div>

              <div class="form-group" style="grid-column: 1 / -1; font-weight: 700; color: #4a7dc7; border-bottom: 1px solid #eee; padding-bottom: 6px; margin-bottom: 8px;"><i class="fas fa-wallet"></i> Metode Penjaminan Pembayaran</div>

              <div class="form-group" style="grid-column: 1 / -1;">
                <label class="form-label">Pilih Penjaminan <span style="color:#e74c3c;">*</span></label>
                <select name="jenis_pembayaran" id="wizard-new-patient-payment-type" class="form-control field-new-patient" style="height:42px;" onchange="toggleWizardPaymentFields()">
                  <option value="Umum">Umum (Bayar Sendiri)</option>
                  <option value="BPJS">BPJS Kesehatan</option>
                  <option value="Asuransi">Asuransi Swasta</option>
                </select>
              </div>

              <div class="form-group" id="wizard-group-bpjs" style="display:none; grid-column: 1 / -1;">
                <label class="form-label">Nomor Kartu BPJS <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="no_bpjs" id="wizard-input-bpjs" class="form-control" maxlength="13" placeholder="13 digit nomor BPJS">
              </div>

              <div class="form-group wizard-group-asuransi" style="display:none;">
                <label class="form-label">Nama Asuransi <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="nama_asuransi" id="wizard-input-nama-asuransi" class="form-control" placeholder="Contoh: Prudential, AIA">
              </div>

              <div class="form-group wizard-group-asuransi" style="display:none;">
                <label class="form-label">Nomor Polis <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="no_polis" id="wizard-input-no-polis" class="form-control" placeholder="Nomor polis asuransi">
              </div>

              <div class="form-group" style="grid-column: 1 / -1; font-weight: 700; color: #4a7dc7; border-bottom: 1px solid #eee; padding-bottom: 6px; margin-bottom: 8px;"><i class="fas fa-phone-square-alt"></i> Kontak Darurat</div>

              <div class="form-group">
                <label class="form-label">Nama Kontak Darurat <span style="color:#e74c3c;">*</span></label>
                <input type="text" name="kontak_darurat_nama" class="form-control field-new-patient" placeholder="Nama wali/kerabat" required>
              </div>

              <div class="form-group">
                <label class="form-label">Nomor Telp Kontak Darurat <span style="color:#e74c3c;">*</span></label>
                <input type="tel" name="kontak_darurat_telp" class="form-control field-new-patient" placeholder="Nomor telepon kontak darurat" required>
              </div>
            </div>
          </div>
        </div>

        <!-- ==========================================
             LANGKAH 2: PILIH POLI
             ========================================== -->
        <div class="wizard-panel" id="wizard-panel-2" style="display:none;">
          <label class="form-label" style="font-weight: 700; font-size: 14px; margin-bottom: 15px; display: block;"><i class="fas fa-layer-group" style="color: #4a7dc7; margin-right: 5px;"></i> Pilih Poliklinik Spesialis Tujuan</label>
          <div class="poli-grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 14px;">
            <?php foreach($poli as $pl): ?>
              <div class="poli-card-item" id="poli-card-<?= $pl['ID_POLI'] ?>" onclick="selectWizardPoli('<?= $pl['ID_POLI'] ?>', '<?= $pl['NAMA_POLI'] ?>')" style="background: #fff; border: 2px solid #eaeeef; border-radius: 10px; padding: 20px 15px; text-align: center; cursor: pointer; transition: all 0.25s ease;">
                <div class="poli-icon-box" style="width: 50px; height: 50px; background: #f1f5fe; color: #4a7dc7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 20px; transition: all 0.25s;">
                  <i class="fas <?= $pl['ICON'] ?: 'fa-hospital' ?>"></i>
                </div>
                <h4 style="font-size: 13px; font-weight: 700; color: #2c3e50; margin-bottom: 4px;"><?= $pl['NAMA_POLI'] ?></h4>
                <p style="font-size: 11px; color: #888; margin: 0; line-height: 1.3;"><?= $pl['KETERANGAN'] ?: '-' ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ==========================================
             LANGKAH 3: TANGGAL & SESI KUNJUNGAN
             ========================================== -->
        <div class="wizard-panel" id="wizard-panel-3" style="display:none;">
          <div style="max-width: 500px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
            
            <div class="form-group">
              <label class="form-label" style="font-weight:700;"><i class="far fa-calendar-check" style="color:#4a7dc7;"></i> Tanggal Kunjungan <span style="color:#e74c3c;">*</span></label>
              <!-- Enforce min as today, max as today + 7 days -->
              <input type="date" id="wizard-tanggal-kunjungan" name="tanggal_kunjungan" class="form-control" style="height:45px;" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+7 days')) ?>" onchange="validateDateAndSesiEntered()">
            </div>

            <div class="form-group">
              <label class="form-label" style="font-weight:700;"><i class="far fa-clock" style="color:#4a7dc7;"></i> Sesi Kunjungan <span style="color:#e74c3c;">*</span></label>
              <select id="wizard-sesi-kunjungan" name="sesi_kunjungan" class="form-control" style="height:45px;" onchange="validateDateAndSesiEntered()">
                <option value="Pagi">Sesi Pagi (08:00 - 12:00 WIB)</option>
                <option value="Siang">Sesi Siang (13:00 - 16:00 WIB)</option>
                <option value="Sore">Sesi Sore (17:00 - 20:00 WIB)</option>
              </select>
            </div>
            
          </div>
        </div>

        <!-- ==========================================
             LANGKAH 4: PILIH DOKTER & REAL-TIME QUOTA
             ========================================== -->
        <div class="wizard-panel" id="wizard-panel-4" style="display:none;">
          <div style="margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 14px; font-weight: 700; color: #2c3e50;">
              Poli Terpilih: <span id="selected-wizard-poli-name" style="color: #4a7dc7; font-weight: 800;">-</span>
            </div>
          </div>

          <div id="wizard-doctor-loading" style="text-align: center; padding: 40px 0;">
            <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #4a7dc7; margin-bottom: 10px;"></i>
            <p style="color: #7f8c8d; font-size: 13px;">Mengecek ketersediaan dokter dan sisa kuota sesi...</p>
          </div>

          <div id="wizard-no-doctor-alert" style="background: #fdf2f2; border: 1px solid #fde2e2; border-radius: 8px; color: #c81e1e; padding: 20px; text-align: center; margin: 15px 0; display: none;">
            <i class="fas fa-exclamation-triangle" style="font-size: 32px; margin-bottom: 8px;"></i>
            <h4 style="font-weight: 700; font-size: 14px; margin-bottom: 4px;">Tidak Ada Dokter Aktif</h4>
            <p style="font-size: 12px; margin: 0;">Maaf, tidak ada dokter aktif atau bertugas di poli ini pada tanggal dan sesi terpilih.</p>
          </div>

          <div class="doctor-cards-container" id="wizard-panel-doctor-cards" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; padding: 5px 0;">
            <!-- Loaded via JS -->
          </div>
        </div>

        <!-- ==========================================
             LANGKAH 5: RINGKASAN & ESTIMASI BIAYA
             ========================================== -->
        <div class="wizard-panel" id="wizard-panel-5" style="display:none;">
          <div style="background: #f8f9fa; border: 1px solid #eef2f5; border-radius: 12px; padding: 25px; max-width: 600px; margin: 0 auto;">
            <h4 style="font-size: 16px; font-weight: 800; color: #2c3e50; border-bottom: 2px solid #eaeeef; padding-bottom: 10px; margin-bottom: 15px;"><i class="fas fa-file-invoice" style="color:#4a7dc7;"></i> Ringkasan Pendaftaran Kunjungan</h4>
            
            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
              <div style="display:flex; justify-content:space-between;"><span style="color:#7f8c8d;">Nama Pasien:</span><strong id="summary-patient-name">-</strong></div>
              <div style="display:flex; justify-content:space-between;"><span style="color:#7f8c8d;">Poli Spesialis:</span><strong id="summary-poli-name">-</strong></div>
              <div style="display:flex; justify-content:space-between;"><span style="color:#7f8c8d;">Dokter Bertugas:</span><strong id="summary-doctor-name">-</strong></div>
              <div style="display:flex; justify-content:space-between;"><span style="color:#7f8c8d;">Tanggal Kunjungan:</span><strong id="summary-visit-date">-</strong></div>
              <div style="display:flex; justify-content:space-between;"><span style="color:#7f8c8d;">Sesi Kunjungan:</span><strong id="summary-visit-sesi">-</strong></div>
              <div style="display:flex; justify-content:space-between;"><span style="color:#7f8c8d;">Metode Penjaminan:</span><span class="badge badge-success" id="summary-payment-type">-</span></div>
              
              <div style="border-top:1px dashed #dcdde1; margin-top:15px; padding-top:15px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:14px; font-weight:700; color:#2c3e50;">Estimasi Biaya Pendaftaran:</span>
                <span style="font-size:18px; font-weight:900; color:#27ae60;" id="summary-pendaftaran-cost">Rp 25.000</span>
              </div>
              <p style="font-size:11px; color:#95a5a6; margin-top:5px;" id="summary-cost-info">* Untuk penjaminan BPJS/Asuransi, administrasi pendaftaran tercover penuh (Rp 0).</p>
            </div>
          </div>
        </div>

        <!-- ==========================================
             LANGKAH 6: SUKSES & CETAK NOMOR ANTRIAN
             ========================================== -->
        <div class="wizard-panel" id="wizard-panel-6" style="display:none; text-align:center; padding: 20px;">
          <div style="width: 60px; height: 60px; background: #e6f7ed; color: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 26px;">
            <i class="fas fa-check-circle"></i>
          </div>
          <h3 style="font-weight: 800; color: #2c3e50; margin-bottom: 5px;">Pendaftaran Anda Berhasil!</h3>
          <p style="color: #7f8c8d; font-size: 13px; margin-bottom: 25px;">Simpan atau cetak tiket antrian pendaftaran Anda di bawah ini.</p>

          <div style="max-width: 380px; margin: 0 auto; border: 2px dashed #4a7dc7; border-radius: 12px; padding: 20px; background: #fbfdff; text-align: center; box-shadow: 0 4px 15px rgba(74,125,199,0.05);">
            <span style="font-size: 10px; font-family: monospace; background: #e8f0fe; color: #4a7dc7; padding: 2px 8px; border-radius: 10px; font-weight: bold;" id="ticket-reg-no">-</span>
            <div style="font-size: 11px; text-transform: uppercase; color: #7f8c8d; font-weight: 600; margin-top: 15px;">Nomor Antrian Anda</div>
            <div style="font-size: 56px; font-weight: 900; color: #4a7dc7; margin: 5px 0; line-height:1;" id="ticket-queue-no">-</div>
            
            <div style="text-align: left; background:#fff; border:1px solid #eef2f5; border-radius:8px; padding:12px; margin-top:15px; font-size:12px; display:flex; flex-direction:column; gap:6px;">
              <div>Dokter: <strong id="ticket-doctor-name">-</strong></div>
              <div>Poli: <strong id="ticket-poli-name">-</strong></div>
              <div>Tgl Kunjungan: <strong id="ticket-visit-date">-</strong></div>
              <div>Sesi: <strong id="ticket-visit-sesi">-</strong></div>
              <div style="background:#e8f0fe; color:#4a7dc7; padding:6px; border-radius:4px; font-weight:700; display:flex; justify-content:space-between; margin-top:5px;">
                <span>Estimasi Jam Panggil:</span>
                <span id="ticket-call-time">-</span>
              </div>
            </div>
          </div>

          <div style="margin-top:25px; display:flex; gap:12px; justify-content:center;">
            <a href="#" target="_blank" id="btn-print-queue-ticket" class="btn btn-primary" style="width: auto; padding: 12px 25px;"><i class="fas fa-print"></i> Cetak Tiket Antrian / Simpan PDF</a>
            <button type="button" class="btn btn-outline" onclick="closeWizardAndRefresh()" style="width: auto; padding: 12px 25px;">Tutup</button>
          </div>
        </div>

      </div>
      
      <!-- Wizard footer controls -->
      <div class="modal-footer" id="wizard-footer-controls" style="background: #fafbfc; border-top: 1px solid #eee; padding: 16px 24px; display: flex; justify-content: space-between;">
        <button type="button" class="btn btn-outline btn-sm" id="btn-wizard-prev" onclick="goToPrevWizardStep()" style="padding: 10px 20px; font-weight:600;"><i class="fas fa-chevron-left"></i> Kembali</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn-wizard-next" onclick="goToNextWizardStep()" style="padding: 10px 25px; border-radius: 6px; width: auto; font-weight: 700;">Lanjut <i class="fas fa-chevron-right"></i></button>
        <button type="submit" class="btn btn-primary btn-sm" id="btn-wizard-submit" style="display:none; padding: 10px 25px; border-radius: 6px; width: auto; font-weight: 700; background:#27ae60; border-color:#27ae60;"><i class="fas fa-check-circle"></i> Konfirmasi & Selesaikan</button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================
     MODAL EDIT PENDAFTARAN (STAFF ONLY)
     ========================================== -->
<div class="modal-overlay" id="modal-edit-pendaftaran">
  <div class="modal" style="max-width: 500px; width: 95%;">
    <form action="<?= site_url('pendaftaran/updatePendaftaran') ?>" method="post">
      <input type="hidden" name="no_pendaftaran" id="edit-no-pendaftaran">
      <div class="modal-header">
        <h3>Edit Data Pendaftaran</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body" style="padding: 20px; display:flex; flex-direction:column; gap:15px;">
        <div class="form-group">
          <label class="form-label">No. Pendaftaran</label>
          <input type="text" id="edit-no-pendaftaran-display" class="form-control" readonly style="background:#f8f9fa;">
        </div>
        <div class="form-group">
          <label class="form-label">Nama Pasien</label>
          <input type="text" id="edit-nama-pasien" class="form-control" readonly style="background:#f8f9fa;">
        </div>
        <div class="form-group">
          <label class="form-label">Poli Spesialis</label>
          <select name="id_poli" id="edit-id-poli" class="form-control" required style="height:42px;">
            <?php foreach($poli as $pl): ?>
              <option value="<?= $pl['ID_POLI'] ?>"><?= $pl['NAMA_POLI'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Dokter Spesialis</label>
          <select name="id_dokter" id="edit-id-dokter" class="form-control" required style="height:42px;">
            <?php foreach($dokter as $dk): ?>
              <option value="<?= $dk['ID_DOKTER'] ?>"><?= $dk['NAMA_DOKTER'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Kunjungan</label>
          <input type="date" name="tanggal_kunjungan" id="edit-tanggal-kunjungan" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Sesi Kunjungan</label>
          <select name="sesi_kunjungan" id="edit-sesi-kunjungan" class="form-control" required style="height:42px;">
            <option value="Pagi">Pagi</option>
            <option value="Siang">Siang</option>
            <option value="Sore">Sore</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Status Antrian</label>
          <select name="status_antrian" id="edit-status-antrian" class="form-control" required style="height:42px;">
            <option value="menunggu">Menunggu</option>
            <option value="sedang_diperiksa">Sedang Diperiksa</option>
            <option value="selesai">Selesai</option>
            <option value="batal">Batal</option>
          </select>
        </div>
      </div>
      <div class="modal-footer" style="padding: 15px 20px;">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm" style="width:auto;"><i class="fas fa-save"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<style>
/* Custom Interactive Styles for Wizard */
.poli-card-item:hover {
  border-color: #4a7dc7 !important;
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(74, 125, 199, 0.08);
}
.poli-card-item:hover .poli-icon-box {
  background: #4a7dc7 !important;
  color: #fff !important;
}
.poli-card-item.selected {
  border-color: #4a7dc7 !important;
  background: #f4f8ff !important;
}
.poli-card-item.selected .poli-icon-box {
  background: #4a7dc7 !important;
  color: #fff !important;
}

.doctor-wizard-card {
  background: #fff;
  border: 2px solid #eaeeef;
  border-radius: 12px;
  padding: 18px;
  cursor: pointer;
  transition: all 0.25s ease;
  position: relative;
  overflow: hidden;
}
.doctor-wizard-card:hover {
  border-color: #4a7dc7;
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.05);
}
.doctor-wizard-card.selected {
  border-color: #4a7dc7;
  background: #f7faff;
  box-shadow: 0 4px 10px rgba(74, 125, 199, 0.1);
}
.doctor-wizard-card.selected::before {
  content: '\f058';
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  position: absolute;
  top: 12px;
  right: 12px;
  color: #4a7dc7;
  font-size: 18px;
}
.doctor-wizard-card.quota-exhausted {
  opacity: 0.65;
  cursor: not-allowed;
  background: #fcfcfc;
}
.doctor-wizard-card.quota-exhausted:hover {
  transform: none;
  border-color: #eaeeef;
  box-shadow: none;
}
</style>

<script>
// Wizard State Management
let currentStep = 1;
const totalSteps = 5;
const isLoggedAsPatient = <?= $isPatient ? 'true' : 'false' ?>;
let selectedPatientName = "";
let selectedPatientPaymentType = "Umum";

function startRegistrationWizard() {
  currentStep = 1;
  selectedPatientName = "";
  selectedPatientPaymentType = "Umum";
  
  // Reset fields in form FIRST
  document.getElementById('form-pendaftaran-wizard').reset();
  
  // Clear fields
  document.getElementById('wizard-id-pasien').value = "";
  document.getElementById('wizard-id-poli').value = "";
  document.getElementById('wizard-id-dokter').value = "";
  document.getElementById('wizard-is-new-patient').value = "0";
  document.getElementById('wizard-tanggal-kunjungan').value = "";
  
  document.getElementById('old-patient-search-query').value = "";
  document.getElementById('old-patient-results').innerHTML = "";
  document.getElementById('old-patient-results').style.display = "none";
  
  // Deselect grids
  document.querySelectorAll('.poli-card-item').forEach(el => el.classList.remove('selected'));
  document.querySelectorAll('.doctor-wizard-card').forEach(el => el.classList.remove('selected'));
  
  // Re-show header & footer that may have been hidden by success step
  const stepsHeader = document.querySelector('.wizard-steps-header');
  if (stepsHeader) stepsHeader.style.display = 'flex';
  const footerControls = document.getElementById('wizard-footer-controls');
  if (footerControls) footerControls.style.display = 'flex';
  
  if (isLoggedAsPatient) {
    // If patient logged in: Skip step 1, set info directly
    selectedPatientName = "<?= session()->get('fullname') ?>";
    selectedPatientPaymentType = "<?= $pasienProfil['JENIS_PEMBAYARAN'] ?? 'Umum' ?>";
    document.getElementById('wizard-id-pasien').value = "<?= session()->get('ID_PASIEN') ?>";
    document.getElementById('wizard-is-new-patient').value = "0";
    
    currentStep = 2; // Start directly at step 2 (Pilih Poli)
  } else {
    // Default staff wizard: Start at step 1 and default patient type old
    setPatientType('old');
  }

  showWizardStep(currentStep);
  
  // Open the modal
  const modal = document.getElementById('modal-pendaftaran');
  if (modal) modal.classList.add('show');
}

function setPatientType(type) {
  const isNew = (type === 'new');
  document.getElementById('wizard-is-new-patient').value = isNew ? "1" : "0";
  
  const oldBtn = document.getElementById('btn-select-old-patient');
  const newBtn = document.getElementById('btn-select-new-patient');
  
  const oldSec = document.getElementById('wizard-old-patient-section');
  const newSec = document.getElementById('wizard-new-patient-section');

  if (isNew) {
    newBtn.style.background = "#f0f5ff";
    newBtn.style.borderColor = "#4a7dc7";
    newBtn.style.color = "#4a7dc7";
    
    oldBtn.style.background = "#fff";
    oldBtn.style.borderColor = "#eaeeef";
    oldBtn.style.color = "#2c3e50";
    
    newSec.style.display = "block";
    oldSec.style.display = "none";
    
    // Clear old patient selection
    document.getElementById('wizard-id-pasien').value = "";
    selectedPatientName = "";
  } else {
    oldBtn.style.background = "#f0f5ff";
    oldBtn.style.borderColor = "#4a7dc7";
    oldBtn.style.color = "#4a7dc7";
    
    newBtn.style.background = "#fff";
    newBtn.style.borderColor = "#eaeeef";
    newBtn.style.color = "#2c3e50";
    
    oldSec.style.display = "block";
    newSec.style.display = "none";
  }

  // FIX: Update Lanjut button state after switching patient type
  const nextBtn = document.getElementById('btn-wizard-next');
  if (isNew) {
    nextBtn.disabled = false; // New patient can always proceed (validation happens on click)
  } else {
    nextBtn.disabled = !document.getElementById('wizard-id-pasien').value; // Old patient needs selection
  }
}

function lookupOldPatient() {
  const query = document.getElementById('old-patient-search-query').value.trim();
  if (!query) {
    alert('Masukkan NIK atau No Rekam Medis untuk mencari.');
    return;
  }

  const resultsDiv = document.getElementById('old-patient-results');
  resultsDiv.style.display = "block";
  resultsDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mencari data pasien...';

  fetch('<?= site_url("pendaftaran/find-pasien") ?>?query=' + encodeURIComponent(query))
    .then(res => res.json())
    .then(res => {
      if (res.status === 'success' && res.data) {
        const p = res.data;
        document.getElementById('wizard-id-pasien').value = p.ID_PASIEN;
        selectedPatientName = p.NAMA_PASIEN;
        selectedPatientPaymentType = p.JENIS_PEMBAYARAN || "Umum";

        // FIX: Enable the Lanjut button now that a patient is selected
        document.getElementById('btn-wizard-next').disabled = false;

        resultsDiv.innerHTML = `
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <h4 style="margin:0; font-weight:700; color:#2c3e50;"><i class="fas fa-check-circle" style="color:#28a745; margin-right:5px;"></i> ${p.NAMA_PASIEN}</h4>
              <span style="font-size:11px; color:#7f8c8d;">ID: <strong>${p.ID_PASIEN}</strong> | NIK: <strong>${p.NIK || '-'}</strong></span>
              <p style="margin:4px 0 0; font-size:11px; color:#888;">Penjaminan: <strong class="badge badge-success">${selectedPatientPaymentType}</strong></p>
            </div>
            <span style="background:#e6f7ed; color:#28a745; font-size:11px; padding:4px 10px; border-radius:12px; font-weight:bold;">Terpilih</span>
          </div>
        `;
      } else {
        document.getElementById('wizard-id-pasien').value = "";
        selectedPatientName = "";
        // FIX: Disable button again since no patient found
        document.getElementById('btn-wizard-next').disabled = true;
        resultsDiv.innerHTML = `<span style="color:#e74c3c;"><i class="fas fa-exclamation-triangle"></i> Data pasien tidak ditemukan.</span>`;
      }
    })
    .catch(err => {
      console.error(err);
      resultsDiv.innerHTML = `<span style="color:#e74c3c;">Terjadi kesalahan saat memuat data.</span>`;
    });
}

function calculateWizardAge(dob) {
  if (!dob) return;
  const birthDate = new Date(dob);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  document.getElementById('wizard-calculated-age').value = age + ' Tahun';
}

function toggleWizardPaymentFields() {
  const type = document.getElementById('wizard-new-patient-payment-type').value;
  const bpjs = document.getElementById('wizard-group-bpjs');
  const asuransi = document.querySelectorAll('.wizard-group-asuransi');
  
  if (type === 'BPJS') {
    bpjs.style.display = "block";
    document.getElementById('wizard-input-bpjs').required = true;
    asuransi.forEach(el => el.style.display = "none");
  } else if (type === 'Asuransi') {
    bpjs.style.display = "none";
    document.getElementById('wizard-input-bpjs').required = false;
    asuransi.forEach(el => el.style.display = "block");
  } else {
    bpjs.style.display = "none";
    asuransi.forEach(el => el.style.display = "none");
  }
}

function selectWizardPoli(idPoli, namePoli) {
  document.getElementById('wizard-id-poli').value = idPoli;
  document.getElementById('selected-wizard-poli-name').innerText = namePoli;
  
  document.querySelectorAll('.poli-card-item').forEach(el => el.classList.remove('selected'));
  document.getElementById('poli-card-' + idPoli).classList.add('selected');
  
  goToNextWizardStep();
}

function validateDateAndSesiEntered() {
  const tgl = document.getElementById('wizard-tanggal-kunjungan').value;
  const sesi = document.getElementById('wizard-sesi-kunjungan').value;
  const nextBtn = document.getElementById('btn-wizard-next');
  
  if (tgl && sesi) {
    nextBtn.disabled = false;
  }
}

function fetchDoctorsForWizard() {
  const idPoli = document.getElementById('wizard-id-poli').value;
  const tgl = document.getElementById('wizard-tanggal-kunjungan').value;
  const sesi = document.getElementById('wizard-sesi-kunjungan').value;
  
  const loading = document.getElementById('wizard-doctor-loading');
  const alertNoDoc = document.getElementById('wizard-no-doctor-alert');
  const container = document.getElementById('wizard-panel-doctor-cards');

  loading.style.display = "block";
  alertNoDoc.style.display = "none";
  container.innerHTML = "";

  fetch(`<?= site_url("poli/dokter") ?>/${idPoli}?tanggal=${tgl}&sesi=${sesi}`)
    .then(res => res.json())
    .then(result => {
      loading.style.display = "none";
      if (result.status === 'success' && result.data && result.data.length > 0) {
        result.data.forEach(doc => {
          const initials = doc.NAMA_DOKTER.replace('dr. ', '').substring(0, 2).toUpperCase();
          const quotaExhausted = doc.SISA_KUOTA <= 0;
          const isSelected = document.getElementById('wizard-id-dokter').value === doc.ID_DOKTER;
          
          let quotaBadge = "";
          if (doc.SISA_KUOTA > 3) {
            quotaBadge = `<span style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 12px; background: #e6f7ed; color: #28a745;"><i class="fas fa-check-circle"></i> Tersedia (${doc.SISA_KUOTA})</span>`;
          } else if (doc.SISA_KUOTA > 0) {
            quotaBadge = `<span style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 12px; background: #fdf6e2; color: #dd9003;"><i class="fas fa-exclamation-circle"></i> Hampir Penuh (${doc.SISA_KUOTA})</span>`;
          } else {
            quotaBadge = `<span style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 12px; background: #fde8e8; color: #dc3545;"><i class="fas fa-times-circle"></i> Kuota Penuh</span>`;
          }

          const cardHtml = `
            <div class="doctor-wizard-card ${isSelected ? 'selected' : ''} ${quotaExhausted ? 'quota-exhausted' : ''}" 
                 onclick="selectWizardDoctor('${doc.ID_DOKTER}', '${doc.NAMA_DOKTER}', ${quotaExhausted}, event)">
              <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #4a7dc7; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; box-shadow: 0 2px 5px rgba(74, 125, 199, 0.2);">
                  ${initials}
                </div>
                <div style="flex: 1;">
                  <h4 style="font-size: 13px; font-weight: 700; color: #2c3e50; margin: 0;">${doc.NAMA_DOKTER}</h4>
                </div>
              </div>
              <div style="margin-top: 14px; border-top: 1px solid #f1f2f6; padding-top: 10px; display: flex; flex-direction: column; gap: 6px;">
                <div style="font-size: 11px; color: #7f8c8d; display: flex; align-items: center; gap: 5px;">
                  <i class="far fa-clock" style="color: #4a7dc7;"></i> Jadwal: <strong>${doc.JADWAL}</strong>
                </div>
                <div style="margin-top: 4px; display: flex; justify-content: space-between; align-items: center;">
                  ${quotaBadge}
                </div>
              </div>
            </div>
          `;
          container.insertAdjacentHTML('beforeend', cardHtml);
        });
      } else {
        alertNoDoc.style.display = "block";
      }
    })
    .catch(err => {
      console.error(err);
      loading.style.display = "none";
      alert("Gagal memuat jadwal dokter.");
    });
}

function selectWizardDoctor(idDokter, docName, isQuotaExhausted, evt) {
  if (isQuotaExhausted) {
    alert("Kuota dokter tersebut sudah penuh. Pilih dokter lain.");
    return;
  }
  
  document.getElementById('wizard-id-dokter').value = idDokter;
  
  // Deselect all, then select clicked card
  document.querySelectorAll('.doctor-wizard-card').forEach(el => el.classList.remove('selected'));
  const clickedCard = evt ? evt.currentTarget : null;
  if (clickedCard) {
    clickedCard.classList.add('selected');
  }
  
  // Auto-advance to summary
  goToNextWizardStep();
}

function showWizardStep(step) {
  // Hide all panels
  document.querySelectorAll('.wizard-panel').forEach(el => el.style.display = "none");
  
  // Show active panel
  document.getElementById('wizard-panel-' + step).style.display = "block";

  // Update Indicator state
  document.querySelectorAll('.wizard-step-indicator').forEach((indicator, index) => {
    const stepNum = index + 1;
    indicator.classList.remove('active');
    
    const circle = indicator.querySelector('.step-num');
    if (stepNum < step) {
      // Completed Step
      indicator.style.color = '#27ae60';
      circle.style.background = '#27ae60';
      circle.innerHTML = '<i class="fas fa-check"></i>';
    } else if (stepNum === step) {
      // Current Step
      indicator.classList.add('active');
      indicator.style.color = '#4a7dc7';
      circle.style.background = '#4a7dc7';
      circle.innerHTML = stepNum;
    } else {
      // Future Step
      indicator.style.color = '#95a5a6';
      circle.style.background = '#bdc3c7';
      circle.innerHTML = stepNum;
    }
  });

  // Footer Button states
  const prevBtn = document.getElementById('btn-wizard-prev');
  const nextBtn = document.getElementById('btn-wizard-next');
  const submitBtn = document.getElementById('btn-wizard-submit');
  
  // Back button visibility
  if (step === 1 || (isLoggedAsPatient && step === 2)) {
    prevBtn.style.visibility = "hidden";
  } else {
    prevBtn.style.visibility = "visible";
  }

  // Next / Submit button visibility
  if (step === 5) {
    nextBtn.style.display = "none";
    submitBtn.style.display = "block";
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Konfirmasi & Selesaikan';
    loadSummaryPanel();
  } else {
    nextBtn.style.display = "block";
    submitBtn.style.display = "none";
    
    // Step checks to disable Next button if not filled
    nextBtn.disabled = false;
    if (step === 1 && !document.getElementById('wizard-id-pasien').value && document.getElementById('wizard-is-new-patient').value === "0") {
      nextBtn.disabled = true; // Old patient not searched yet
    } else if (step === 2 && !document.getElementById('wizard-id-poli').value) {
      nextBtn.disabled = true; // Poli not chosen
    } else if (step === 3 && (!document.getElementById('wizard-tanggal-kunjungan').value || !document.getElementById('wizard-sesi-kunjungan').value)) {
      nextBtn.disabled = true; // Date/session not chosen
    } else if (step === 4 && !document.getElementById('wizard-id-dokter').value) {
      nextBtn.disabled = true; // Doctor not chosen
    }
  }
}

function goToNextWizardStep() {
  try {
    if (currentStep === 1) {
      const isNew = document.getElementById('wizard-is-new-patient').value === "1";
      if (isNew) {
        // Validate all required new-patient fields are entered
        let valid = true;
        let firstInvalid = null;
        let missingField = '';
        document.querySelectorAll('#wizard-new-patient-section .field-new-patient').forEach(input => {
          if (!input.value || !input.value.trim()) {
            valid = false;
            input.style.borderColor = '#e74c3c';
            input.style.boxShadow = '0 0 0 2px rgba(231, 76, 60, 0.15)';
            if (!firstInvalid) {
              firstInvalid = input;
              // Get the label text for this field
              const label = input.closest('.form-group')?.querySelector('.form-label');
              missingField = label ? label.textContent.replace('*', '').trim() : 'field';
            }
          } else {
            input.style.borderColor = '';
            input.style.boxShadow = '';
          }
        });
        
        if (!valid) {
          alert('Field "' + missingField + '" belum diisi. Mohon isi semua field bertanda bintang (*).');
          if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => firstInvalid.focus(), 300);
          }
          return;
        }
        
        // Validate NIK is 16 digits
        const nikInput = document.querySelector('input[name="nik"]');
        const nikVal = nikInput.value.trim();
        if (nikVal.length !== 16 || !/^\d+$/.test(nikVal)) {
          alert('NIK harus berupa 16 digit angka.');
          nikInput.style.borderColor = '#e74c3c';
          nikInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
          setTimeout(() => nikInput.focus(), 300);
          return;
        }
        
        // Temporary patient name for summary
        selectedPatientName = document.querySelector('input[name="nama_pasien"]').value;
        selectedPatientPaymentType = document.getElementById('wizard-new-patient-payment-type').value;
      } else {
        // Old patient: must have selected a patient
        if (!document.getElementById('wizard-id-pasien').value) {
          alert('Silakan cari dan pilih pasien terlebih dahulu.');
          return;
        }
      }
    }
    
    if (currentStep === 2) {
      if (!document.getElementById('wizard-id-poli').value) {
        alert('Silakan pilih poli tujuan terlebih dahulu.');
        return;
      }
    }
    
    if (currentStep === 3) {
      if (!document.getElementById('wizard-tanggal-kunjungan').value) {
        alert('Silakan pilih tanggal kunjungan terlebih dahulu.');
        return;
      }
      fetchDoctorsForWizard();
    }
    
    if (currentStep === 4) {
      if (!document.getElementById('wizard-id-dokter').value) {
        alert('Silakan pilih dokter terlebih dahulu.');
        return;
      }
    }

    currentStep++;
    showWizardStep(currentStep);
  } catch (err) {
    console.error('Wizard error:', err);
    alert('Terjadi kesalahan: ' + err.message);
  }
}

function goToPrevWizardStep() {
  currentStep--;
  showWizardStep(currentStep);
}

function loadSummaryPanel() {
  document.getElementById('summary-patient-name').innerText = selectedPatientName;
  document.getElementById('summary-poli-name').innerText = document.getElementById('selected-wizard-poli-name').innerText;
  
  // Find doctor card text
  const idDokter = document.getElementById('wizard-id-dokter').value;
  const docCard = document.querySelector(`.doctor-wizard-card.selected h4`);
  document.getElementById('summary-doctor-name').innerText = docCard ? docCard.innerText : '-';

  const tgl = document.getElementById('wizard-tanggal-kunjungan').value;
  document.getElementById('summary-visit-date').innerText = new Date(tgl).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
  
  const sesi = document.getElementById('wizard-sesi-kunjungan').value;
  document.getElementById('summary-visit-sesi').innerText = 'Sesi ' + sesi;
  
  document.getElementById('summary-payment-type').innerText = selectedPatientPaymentType;
  
  const costSpan = document.getElementById('summary-pendaftaran-cost');
  if (selectedPatientPaymentType === 'BPJS' || selectedPatientPaymentType === 'Asuransi') {
    costSpan.innerText = "Rp 0";
  } else {
    costSpan.innerText = "Rp 25.000";
  }
}

function submitPendaftaranWizard(e) {
  e.preventDefault();
  
  const form = document.getElementById('form-pendaftaran-wizard');
  const formData = new FormData(form);
  
  const submitBtn = document.getElementById('btn-wizard-submit');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
  
  fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(res => res.json())
  .then(res => {
    if (res.status === 'success') {
      // Transition to step 6 (Success ticket)
      currentStep = 6;
      document.getElementById('wizard-panel-5').style.display = "none";
      document.getElementById('wizard-panel-6').style.display = "block";
      
      // Hide headers and footer
      document.querySelector('.wizard-steps-header').style.display = "none";
      document.getElementById('wizard-footer-controls').style.display = "none";
      
      // Fill ticket
      document.getElementById('ticket-reg-no').innerText = res.data.NO_PENDAFTARAN;
      document.getElementById('ticket-queue-no').innerText = res.data.NO_ANTRIAN;
      document.getElementById('ticket-doctor-name').innerText = res.data.NAMA_DOKTER;
      document.getElementById('ticket-poli-name').innerText = res.data.NAMA_POLI;
      document.getElementById('ticket-visit-date').innerText = res.data.TANGGAL_KUNJUNGAN;
      document.getElementById('ticket-visit-sesi').innerText = 'Sesi ' + res.data.SESI_KUNJUNGAN;
      document.getElementById('ticket-call-time').innerText = res.data.ESTIMASI_JAM;
      
      // Print link
      document.getElementById('btn-print-queue-ticket').href = '<?= site_url("pendaftaran/cetak") ?>/' + res.data.NO_PENDAFTARAN;
    } else {
      alert('Error: ' + res.message);
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Konfirmasi & Selesaikan';
    }
  })
  .catch(err => {
    console.error(err);
    alert('Terjadi kesalahan saat memproses pendaftaran.');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Konfirmasi & Selesaikan';
  });
}

function closeWizardAndRefresh() {
  document.getElementById('modal-pendaftaran').classList.remove('show');
  window.location.reload();
}

// Staff Edit modal
function openEditPendaftaranModal(data) {
  const modal = document.getElementById('modal-edit-pendaftaran');
  if (modal) {
    document.getElementById('edit-no-pendaftaran').value = data.NO_PENDAFTARAN;
    document.getElementById('edit-no-pendaftaran-display').value = data.NO_PENDAFTARAN;
    document.getElementById('edit-nama-pasien').value = data.NAMA_PASIEN;
    document.getElementById('edit-id-poli').value = data.ID_POLI;
    document.getElementById('edit-id-dokter').value = data.ID_DOKTER;
    document.getElementById('edit-tanggal-kunjungan').value = data.TANGGAL_KUNJUNGAN ? data.TANGGAL_KUNJUNGAN.substring(0, 10) : "";
    document.getElementById('edit-sesi-kunjungan').value = data.SESI_KUNJUNGAN || "Pagi";
    document.getElementById('edit-status-antrian').value = data.STATUS_ANTRIAN;
    
    modal.classList.add('show');
  }
}
</script>

