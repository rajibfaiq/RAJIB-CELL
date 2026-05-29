<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Pendaftaran Akun Pasien Baru - SIMRS RS Sejahtera">
  <title>Daftar Akun Pasien | SIMRS RS Sejahtera</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('css/simrs.css') ?>">
  <style>
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 15px;
    }
    .register-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 800px;
      padding: 40px;
      border: 1px solid rgba(255,255,255,0.8);
    }
    .register-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .register-header h1 {
      font-size: 26px;
      color: #2c3e50;
      font-weight: 800;
      margin-bottom: 5px;
    }
    .register-header p {
      color: #7f8c8d;
      font-size: 14px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      .register-card {
        padding: 24px;
      }
    }
    .section-title {
      grid-column: 1 / -1;
      font-size: 14px;
      font-weight: 700;
      color: #4a7dc7;
      border-bottom: 2px solid #eef2f5;
      padding-bottom: 8px;
      margin-top: 15px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-submit {
      grid-column: 1 / -1;
      margin-top: 20px;
      padding: 14px;
      font-size: 16px;
      font-weight: 700;
    }
    .back-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }
    .back-link a {
      color: #4a7dc7;
      text-decoration: none;
      font-weight: 700;
    }
    .back-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-card">
    <div class="register-header">
      <div class="hospital-icon" style="width:60px; height:60px; background: #e8f0fe; color: #4a7dc7; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:26px; margin: 0 auto 15px;"><i class="fas fa-hospital-user"></i></div>
      <h1>Pendaftaran Pasien Baru</h1>
      <p>Buat akun pasien baru untuk melakukan pendaftaran layanan rumah sakit secara mandiri</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div style="background:#fee2e2;color:#dc2626;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
      </div>
    <?php endif; ?>

    <form action="<?= site_url('auth/register') ?>" method="post">
      <?= csrf_field() ?>
      
      <div class="form-grid">
        
        <!-- SEC 1: AKUN LOGIN -->
        <div class="section-title"><i class="fas fa-key"></i> Informasi Akun Login</div>
        
        <div class="form-group">
          <label for="username">Username <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-user-tag"></i>
            <input type="text" id="username" name="username" placeholder="Buat username unik" value="<?= old('username') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Masukkan password akun" required>
          </div>
        </div>

        <!-- SEC 2: DATA DIRI -->
        <div class="section-title"><i class="fas fa-id-card"></i> Informasi Data Diri</div>

        <div class="form-group">
          <label for="fullname">Nama Lengkap Pasien <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-user"></i>
            <input type="text" id="fullname" name="fullname" placeholder="Nama lengkap sesuai KTP" value="<?= old('fullname') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label for="nik">NIK (Nomor Induk Kependudukan) <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-fingerprint"></i>
            <input type="text" id="nik" name="nik" placeholder="16 digit nomor NIK" maxlength="16" pattern="[0-9]{16}" value="<?= old('nik') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label for="tgl_lahir">Tanggal Lahir <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-calendar-alt"></i>
            <input type="date" id="tgl_lahir" name="tgl_lahir" value="<?= old('tgl_lahir') ?>" required onchange="hitungUsia()">
          </div>
        </div>

        <div class="form-group">
          <label for="usia">Usia Terhitung</label>
          <div class="input-wrapper">
            <i class="fas fa-hourglass-half"></i>
            <input type="text" id="usia" placeholder="Otomatis terhitung" readonly style="background:#f8f9fa;">
          </div>
        </div>

        <div class="form-group">
          <label for="jenis_kelamin">Jenis Kelamin <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-venus-mars"></i>
            <select id="jenis_kelamin" name="jenis_kelamin" required style="padding-left:35px; height: 42px;">
              <option value="">-- Pilih Jenis Kelamin --</option>
              <option value="Laki-laki" <?= old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
              <option value="Perempuan" <?= old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="no_telp">Nomor Telepon Pasien <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-phone"></i>
            <input type="tel" id="no_telp" name="no_telp" placeholder="Contoh: 081234567890" pattern="[0-9]+" value="<?= old('no_telp') ?>" required>
          </div>
        </div>

        <!-- SEC 3: ALAMAT -->
        <div class="section-title"><i class="fas fa-map-marker-alt"></i> Informasi Alamat Lengkap</div>

        <div class="form-group">
          <label for="provinsi">Provinsi <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-map"></i>
            <input type="text" id="provinsi" name="provinsi" placeholder="Provinsi" value="<?= old('provinsi') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label for="kota">Kabupaten / Kota <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-city"></i>
            <input type="text" id="kota" name="kota" placeholder="Kabupaten / Kota" value="<?= old('kota') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label for="kecamatan">Kecamatan <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-map-signs"></i>
            <input type="text" id="kecamatan" name="kecamatan" placeholder="Kecamatan" value="<?= old('kecamatan') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label for="kelurahan">Kelurahan <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-draw-polygon"></i>
            <input type="text" id="kelurahan" name="kelurahan" placeholder="Kelurahan" value="<?= old('kelurahan') ?>" required>
          </div>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="alamat_pasien">Alamat Detail (RT/RW, Blok, No. Rumah) <span style="color:#e74c3c;">*</span></label>
          <textarea id="alamat_pasien" name="alamat_pasien" placeholder="Masukkan alamat lengkap detail..." required style="width: 100%; border: 1px solid #dcdde1; border-radius: 8px; padding: 12px; font-family:inherit; min-height:80px; resize:vertical;"><?= old('alamat_pasien') ?></textarea>
        </div>

        <!-- SEC 4: PEMBAYARAN -->
        <div class="section-title"><i class="fas fa-wallet"></i> Metode Penjaminan Pembayaran</div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="jenis_pembayaran">Jenis Penjaminan <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-credit-card"></i>
            <select id="jenis_pembayaran" name="jenis_pembayaran" required style="padding-left:35px; height: 42px;" onchange="togglePembayaranFields()">
              <option value="Umum" <?= old('jenis_pembayaran') === 'Umum' ? 'selected' : '' ?>>Umum (Bayar Mandiri)</option>
              <option value="BPJS" <?= old('jenis_pembayaran') === 'BPJS' ? 'selected' : '' ?>>BPJS Kesehatan</option>
              <option value="Asuransi" <?= old('jenis_pembayaran') === 'Asuransi' ? 'selected' : '' ?>>Asuransi Swasta</option>
            </select>
          </div>
        </div>

        <div class="form-group" id="group-bpjs" style="display: none; grid-column: 1 / -1;">
          <label for="no_bpjs">Nomor Kartu BPJS <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-address-card"></i>
            <input type="text" id="no_bpjs" name="no_bpjs" placeholder="13 digit nomor BPJS Kesehatan" maxlength="13" pattern="[0-9]{13}" value="<?= old('no_bpjs') ?>">
          </div>
        </div>

        <div class="form-group id-asuransi-fields" style="display: none;">
          <label for="nama_asuransi">Nama Penyedia Asuransi <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-shield-alt"></i>
            <input type="text" id="nama_asuransi" name="nama_asuransi" placeholder="Contoh: Prudential, AIA, dll" value="<?= old('nama_asuransi') ?>">
          </div>
        </div>

        <div class="form-group id-asuransi-fields" style="display: none;">
          <label for="no_polis">Nomor Polis Asuransi <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-file-contract"></i>
            <input type="text" id="no_polis" name="no_polis" placeholder="Nomor polis asuransi aktif" value="<?= old('no_polis') ?>">
          </div>
        </div>

        <!-- SEC 5: KONTAK DARURAT -->
        <div class="section-title"><i class="fas fa-heartbeat"></i> Kontak Darurat (Keluarga / Kerabat)</div>

        <div class="form-group">
          <label for="kontak_darurat_nama">Nama Kontak Darurat <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-user-friends"></i>
            <input type="text" id="kontak_darurat_nama" name="kontak_darurat_nama" placeholder="Nama wali/kontak darurat" value="<?= old('kontak_darurat_nama') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label for="kontak_darurat_telp">Nomor Telp Kontak Darurat <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-phone-alt"></i>
            <input type="tel" id="kontak_darurat_telp" name="kontak_darurat_telp" placeholder="Contoh: 0812XXXXXXXX" pattern="[0-9]+" value="<?= old('kontak_darurat_telp') ?>" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-submit"><i class="fas fa-user-plus"></i> Daftar Sekarang</button>
      </div>
    </form>

    <div class="back-link">
      Sudah memiliki akun? <a href="<?= site_url('/') ?>">Masuk di sini</a>
    </div>
  </div>

  <script>
    function hitungUsia() {
      const birthDateVal = document.getElementById('tgl_lahir').value;
      if (!birthDateVal) return;

      const birthDate = new Date(birthDateVal);
      const today = new Date();
      
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();
      
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }

      document.getElementById('usia').value = age + ' Tahun';
    }

    function togglePembayaranFields() {
      const jenis = document.getElementById('jenis_pembayaran').value;
      const bpjsBox = document.getElementById('group-bpjs');
      const asuransiFields = document.querySelectorAll('.id-asuransi-fields');
      const inputBpjs = document.getElementById('no_bpjs');
      const inputNamaAsuransi = document.getElementById('nama_asuransi');
      const inputNoPolis = document.getElementById('no_polis');

      if (jenis === 'BPJS') {
        bpjsBox.style.display = 'block';
        inputBpjs.required = true;
        
        asuransiFields.forEach(el => el.style.display = 'none');
        inputNamaAsuransi.required = false;
        inputNoPolis.required = false;
      } else if (jenis === 'Asuransi') {
        bpjsBox.style.display = 'none';
        inputBpjs.required = false;

        asuransiFields.forEach(el => el.style.display = 'block');
        inputNamaAsuransi.required = true;
        inputNoPolis.required = true;
      } else {
        bpjsBox.style.display = 'none';
        inputBpjs.required = false;

        asuransiFields.forEach(el => el.style.display = 'none');
        inputNamaAsuransi.required = false;
        inputNoPolis.required = false;
      }
    }

    // Call onload in case of validations redirecting back
    window.onload = function() {
      hitungUsia();
      togglePembayaranFields();
    };
  </script>
</body>
</html>
