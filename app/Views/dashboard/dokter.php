<?php
// Group pendaftaran for today by dokter
$doctorPatientsToday = [];
if (!empty($pendaftaran)) {
    foreach ($pendaftaran as $pd) {
        if (!empty($pd['TANGGAL_KUNJUNGAN']) && date('Y-m-d', strtotime($pd['TANGGAL_KUNJUNGAN'])) === date('Y-m-d')) {
            $docId = $pd['ID_DOKTER'];
            if (!empty($docId)) {
                if (!isset($doctorPatientsToday[$docId])) {
                    $doctorPatientsToday[$docId] = [];
                }
                $doctorPatientsToday[$docId][] = [
                    'NO_ANTRIAN' => $pd['NO_ANTRIAN'],
                    'NAMA_PASIEN' => $pd['NAMA_PASIEN'],
                    'ID_PASIEN' => $pd['ID_PASIEN'],
                    'STATUS_ANTRIAN' => $pd['STATUS_ANTRIAN'],
                    'JAM_PENDAFTARAN' => date('H:i', strtotime($pd['JAM_PENDAFTARAN'] ?? $pd['TANGGAL_DAFTAR']))
                ];
            }
        }
    }
}
?>
<!-- Data Dokter -->
<div class="page-section" id="page-dokter" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari dokter..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-dokter"><i class="fas fa-plus"></i> Tambah Dokter</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>NIP</th><th>Nama Dokter</th><th>Poli Spesialis</th><th>Jadwal</th><th>Terdaftar / Kuota (Hari Ini)</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($dokter)): foreach($dokter as $d): ?>
          <tr>
            <td><?= $d['ID_DOKTER'] ?></td>
            <td><strong><?= $d['NAMA_DOKTER'] ?></strong></td>
            <td><span class="badge-status info" style="font-size: 11px; font-weight: 600;"><i class="fas fa-clinic-medical"></i> <?= $d['NAMA_POLI'] ?: '-' ?></span></td>
            <td><?= $d['JADWAL'] ?></td>
            <td style="font-weight: 700; text-align: center;">
              <span style="color:#28a745;" title="Jumlah pasien terdaftar hari ini"><?= $d['TERDAFTAR_HARI_INI'] ?? 0 ?></span> / <span style="color:#4a7dc7;" title="Kuota harian dokter"><?= $d['KUOTA_HARIAN'] ?></span>
              <?php if (($d['TERDAFTAR_HARI_INI'] ?? 0) > 0): ?>
                <br>
                <a href="javascript:void(0)" onclick='showDoctorPatients(<?= json_encode($d['ID_DOKTER']) ?>, <?= json_encode($d['NAMA_DOKTER']) ?>)' style="font-size: 11px; color:#4a7dc7; font-weight:600; text-decoration:underline; cursor:pointer;" title="Lihat daftar pasien yang harus ditangani"><i class="fas fa-list"></i> Lihat Pasien</a>
              <?php endif; ?>
            </td>
            <td><span class="badge <?= $d['STATUS'] === 'Aktif' ? 'badge-success' : 'badge-danger' ?>"><?= $d['STATUS'] ?></span></td>
            <td>
              <button class="btn-icon" onclick='editData("dokter", <?= htmlspecialchars(json_encode($d), ENT_QUOTES, "UTF-8") ?>)'><i class="fas fa-edit"></i></button>
              <a href="<?= site_url('dokter/delete/'.$d['ID_DOKTER']) ?>" class="btn-icon delete" onclick="return confirm('Yakin ingin menghapus dokter ini?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:#999;">Belum ada data dokter</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Dokter -->
<div class="modal-overlay" id="modal-dokter">
  <div class="modal">
    <form action="<?= site_url('dokter/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Dokter Baru</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Dokter / NIP</label><input name="id_dokter" class="form-control" value="<?= $nextDokterId ?>" readonly required></div>
          <div><label class="form-label">Nama Lengkap</label><input name="nama_dokter" class="form-control" placeholder="dr. Nama" required></div>
        </div>
        <div class="form-row single">
          <div><label class="form-label">No. Izin Praktek</label><input name="no_izin_praktek" class="form-control" placeholder="SIP/xxx/xxx"></div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Poli Spesialis</label>
            <select name="id_poli" class="form-control" required>
              <option value="">-- Pilih Poli --</option>
              <?php foreach($poli as $pl): ?>
                <option value="<?= $pl['ID_POLI'] ?>"><?= $pl['NAMA_POLI'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div><label class="form-label">Kuota Harian</label><input type="number" name="kuota_harian" class="form-control" value="20" required min="1"></div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Jadwal Praktek</label><input name="jadwal" class="form-control" placeholder="Senin - Jumat, 08:00 - 14:00" required></div>
          <div><label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="Aktif">Aktif</option>
              <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Detail Pasien Dokter -->
<div class="modal-overlay" id="modal-lihat-pasien">
  <div class="modal" style="width: 600px;">
    <div class="modal-header">
      <div style="display: flex; flex-direction: column; gap: 4px;">
        <h3 id="doc-patient-modal-title" style="font-weight: 700; color:#2c3e50;">Daftar Pasien</h3>
        <span style="font-size: 11px; color:#888;">Pasien yang harus ditangani hari ini (<?= date('d M Y') ?>)</span>
      </div>
      <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" style="padding: 20px; max-height: 400px; overflow-y: auto;">
      <table class="data-table" style="font-size: 13px;">
        <thead>
          <tr>
            <th style="width: 80px; text-align: center;">No. Antrian</th>
            <th>Nama Pasien</th>
            <th>ID Pasien</th>
            <th>Jam Terdaftar</th>
            <th style="width: 100px; text-align: center;">Status</th>
          </tr>
        </thead>
        <tbody id="doc-patient-list-body">
          <!-- Dynamically populated -->
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline btn-sm modal-close">Tutup</button>
    </div>
  </div>
</div>

<script>
const doctorPatients = <?= json_encode($doctorPatientsToday) ?>;

function showDoctorPatients(docId, docName) {
  const modal = document.getElementById('modal-lihat-pasien');
  if (!modal) return;

  document.getElementById('doc-patient-modal-title').innerText = 'Daftar Pasien - ' + docName;
  
  const tbody = document.getElementById('doc-patient-list-body');
  tbody.innerHTML = '';
  
  const patients = doctorPatients[docId] || [];
  
  if (patients.length > 0) {
    // Sort patients by queue number
    patients.sort((a, b) => Number(a.NO_ANTRIAN) - Number(b.NO_ANTRIAN));
    
    patients.forEach(p => {
      let statusClass = 'pending';
      if (p.STATUS_ANTRIAN === 'sedang_diperiksa') statusClass = 'info';
      if (p.STATUS_ANTRIAN === 'selesai') statusClass = 'active';
      if (p.STATUS_ANTRIAN === 'batal') statusClass = 'inactive';
      
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="text-align: center;">
          <div style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:50%; background:#e8f0fe; color:#4a7dc7; font-weight:800; font-size:12px;">
            ${p.NO_ANTRIAN}
          </div>
        </td>
        <td><strong>${p.NAMA_PASIEN}</strong></td>
        <td style="font-family: monospace;">${p.ID_PASIEN}</td>
        <td>${p.JAM_PENDAFTARAN}</td>
        <td style="text-align: center;">
          <span class="badge-status ${statusClass}" style="text-transform: capitalize; font-size: 10px; font-weight: 700;">
            ${p.STATUS_ANTRIAN.replace('_', ' ')}
          </span>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } else {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:#aaa;padding:20px;">Tidak ada pasien hari ini.</td></tr>`;
  }
  
  modal.classList.add('show');
}
</script>

