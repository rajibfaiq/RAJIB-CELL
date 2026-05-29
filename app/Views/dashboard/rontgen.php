<!-- Rontgen -->
<div class="page-section" id="page-rontgen" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari rontgen..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-rontgen"><i class="fas fa-plus"></i> Tambah Rontgen</button>
  </div>
  
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID Rontgen</th>
            <th>Biodata Pasien</th>
            <th>Dokter Perujuk</th>
            <th>Detail Rujukan</th>
            <th>Status</th>
            <th>Hasil Rontgen</th>
            <th>Kesimpulan / Catatan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($rontgen)): foreach($rontgen as $r): ?>
          <?php
            $statusClass = 'pending';
            if ($r['STATUS'] === 'proses') $statusClass = 'info';
            if ($r['STATUS'] === 'selesai') $statusClass = 'active';
          ?>
          <tr>
            <td><span class="badge-status info" style="font-family: monospace; font-size: 11px; font-weight: bold;"><?= $r['ID_RONTGEN'] ?></span></td>
            <td>
              <strong><?= $r['NAMA_PASIEN'] ?></strong>
              <br><span style="font-family: monospace; font-size: 11px; color:#888;">RM: <?= $r['ID_PERIKSA'] ?></span>
            </td>
            <td><strong><?= $r['NAMA_DOKTER'] ?: '-' ?></strong></td>
            <td>
              <strong style="color: #4a7dc7;"><?= $r['JENIS_RONTGEN'] ?: 'Umum' ?></strong>
              <?php if($r['KETERANGAN_KLINIS']): ?>
                <br><span style="font-size:11px; color:#666;">Klinis: <em><?= $r['KETERANGAN_KLINIS'] ?></em></span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge-status <?= $statusClass ?>" style="text-transform: capitalize; font-weight: 600;">
                <?= $r['STATUS'] ?>
              </span>
            </td>
            <td>
              <?php if($r['STATUS'] === 'selesai'): ?>
                <span style="font-weight: 600; color: #2e7d32;"><i class="fas fa-file-alt"></i> <?= $r['HASIL_RONTGEN'] ?></span>
              <?php else: ?>
                <span style="color:#aaa; font-style:italic;">Belum diisi</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if($r['STATUS'] === 'selesai'): ?>
                <span><?= $r['KETERANGAN'] ?: '-' ?></span>
              <?php else: ?>
                <span style="color:#aaa;">-</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display: flex; gap: 8px; align-items: center;">
                <?php if($r['STATUS'] !== 'selesai'): ?>
                  <button class="btn btn-success btn-sm" onclick="openIsiHasilRontgenModal('<?= $r['ID_RONTGEN'] ?>', '<?= addslashes($r['NAMA_PASIEN']) ?>', '<?= $r['JENIS_RONTGEN'] ?>')" style="padding: 4px 10px; font-size: 11px; border-radius: 4px;"><i class="fas fa-edit"></i> Isi Hasil</button>
                <?php endif; ?>
                <a href="<?= site_url('rontgen/delete/'.$r['ID_RONTGEN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus rujukan rontgen ini?')"><i class="fas fa-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="8" style="text-align:center;padding:30px;color:#999;">Belum ada data rontgen</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Rontgen Manual (Seperti Aslinya) -->
<div class="modal-overlay" id="modal-rontgen">
  <div class="modal">
    <form action="<?= site_url('rontgen/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Rontgen Baru</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Rontgen</label><input name="id_rontgen" class="form-control" value="<?= $nextRontgenId ?>" readonly required></div>
          <div><label class="form-label">ID Pemeriksaan</label>
            <select name="id_periksa" class="form-control" required>
              <option value="">-- Pilih Rekam Medis --</option>
              <?php foreach($pemeriksaan as $pm): ?>
                <option value="<?= $pm['ID_PERIKSA'] ?>"><?= $pm['ID_PERIKSA'] ?> - <?= $pm['NAMA_PASIEN'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div>
            <label class="form-label">Jenis Rontgen</label>
            <select name="jenis_rontgen" class="form-control" required>
              <option value="Thorax (Dada)">Thorax (Dada)</option>
              <option value="Abdomen (Perut)">Abdomen (Perut)</option>
              <option value="Panoramic (Gigi)">Panoramic (Gigi)</option>
              <option value="Cranium (Kepala)">Cranium (Kepala)</option>
              <option value="Extremitas (Tangan/Kaki)">Extremitas (Tangan/Kaki)</option>
            </select>
          </div>
          <div><label class="form-label">Keterangan Klinis</label><input name="keterangan_klinis" class="form-control" placeholder="Indikasi klinis"></div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Hasil Pemeriksaan</label><input name="hasil_rontgen" class="form-control" placeholder="Hasil scan/rontgen" required></div>
          <div><label class="form-label">Keterangan / Catatan</label><input name="keterangan" class="form-control" placeholder="Catatan hasil"></div>
        </div>
        <input type="hidden" name="status" value="selesai">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Isi Hasil Rontgen (Radiologi Staff Workflow) -->
<div class="modal-overlay" id="modal-isi-hasil-rontgen">
  <div class="modal">
    <form action="" method="post" id="form-isi-hasil-rontgen">
      <div class="modal-header">
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h3 style="font-weight: 700; color: #2c3e50;">Isi Hasil Pemeriksaan Rontgen</h3>
          <span style="font-size: 11px; color: #7f8c8d;" id="radiologi-subtitle">ID Rontgen: RTG001</span>
        </div>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div>
            <label class="form-label">Pasien</label>
            <input id="radiologi-nama-pasien" class="form-control" readonly style="background: #f1f2f6;">
          </div>
          <div>
            <label class="form-label">Jenis Rontgen</label>
            <input id="radiologi-jenis-rontgen" class="form-control" readonly style="background: #f1f2f6;">
          </div>
        </div>
        
        <div class="form-row single" style="margin-top: 14px;">
          <div>
            <label class="form-label" style="font-weight: 700; color: #2c3e50;">Hasil Rontgen / Pembacaan Citra</label>
            <input name="hasil_rontgen" class="form-control" placeholder="Contoh: Cardiomegaly ringan, infiltrat di paru kiri" required>
          </div>
        </div>
        
        <div class="form-row single">
          <div>
            <label class="form-label" style="font-weight: 700; color: #2c3e50;">Kesimpulan & Catatan Radiologi</label>
            <textarea name="keterangan" class="form-control" placeholder="Tuliskan keterangan lengkap, diagnosis banding, atau saran tindak lanjut..." required></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-success btn-sm" style="background: #28a745;"><i class="fas fa-check-circle"></i> Simpan & Kirim Hasil</button>
      </div>
    </form>
  </div>
</div>

<script>
function openIsiHasilRontgenModal(idRontgen, namaPasien, jenisRontgen) {
  document.getElementById('radiologi-subtitle').innerText = 'ID Rontgen: ' + idRontgen;
  document.getElementById('radiologi-nama-pasien').value = namaPasien;
  document.getElementById('radiologi-jenis-rontgen').value = jenisRontgen || 'Thorax (Dada)';
  
  // Set action URL dynamically
  const form = document.getElementById('form-isi-hasil-rontgen');
  form.action = '<?= site_url("rontgen/uploadHasil") ?>/' + idRontgen;
  
  const modal = document.getElementById('modal-isi-hasil-rontgen');
  if (modal) {
    modal.classList.add('show');
  }
}
</script>
