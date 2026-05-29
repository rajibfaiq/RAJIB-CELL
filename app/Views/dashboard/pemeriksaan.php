<!-- Pemeriksaan (Rekam Medis) -->
<div class="page-section" id="page-rekammedis" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari rekam medis..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-pemeriksaan"><i class="fas fa-plus"></i> Tambah Rekam Medis</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Periksa</th><th>Nama Pasien</th><th>Nama Dokter</th><th>Tgl Periksa</th><th>Diagnosa</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($pemeriksaan)): foreach($pemeriksaan as $rm): ?>
          <tr>
            <td><?= $rm['ID_PERIKSA'] ?></td>
            <td><strong><?= $rm['NAMA_PASIEN'] ?></strong> <br><small style="color:#888;"><?= $rm['ID_PASIEN'] ?></small></td>
            <td><strong><?= $rm['NAMA_DOKTER'] ?></strong> <br><small style="color:#888;"><?= $rm['ID_DOKTER'] ?></small></td>
            <td><?= $rm['TGL_PERIKSA'] ?></td>
            <td><?= $rm['DIAGNOSA'] ?></td>
            <td>
              <button class="btn btn-warning btn-sm" style="padding: 4px 8px; font-size: 11px; margin-right: 5px; border-radius: 4px; color: #fff; background: #f0a500;" onclick="openRujukRontgenModal('<?= $rm['ID_PERIKSA'] ?>', '<?= addslashes($rm['NAMA_PASIEN']) ?>')"><i class="fas fa-x-ray"></i> Rujuk Rontgen</button>
              <a href="<?= site_url('pemeriksaan/delete/'.$rm['ID_PERIKSA']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="6" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Rekam Medis -->
<div class="modal-overlay" id="modal-pemeriksaan">
  <div class="modal">
    <form action="<?= site_url('pemeriksaan/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Rekam Medis</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Periksa</label><input name="id_periksa" class="form-control" value="<?= $nextPemeriksaanId ?>" readonly required></div>
          <div><label class="form-label">Pilih Dokter</label>
            <select name="id_dokter" class="form-control" required>
              <option value="">-- Pilih Dokter --</option>
              <?php foreach($dokter as $d): ?>
                <option value="<?= $d['ID_DOKTER'] ?>"><?= $d['NAMA_DOKTER'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Pilih Pasien</label>
            <select name="id_pasien" class="form-control" required>
              <option value="">-- Pilih Pasien --</option>
              <?php foreach($pasien as $p): ?>
                <option value="<?= $p['ID_PASIEN'] ?>"><?= $p['NAMA_PASIEN'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div><label class="form-label">Diagnosa</label><input name="diagnosa" class="form-control" placeholder="Diagnosa dokter" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Rujuk Rontgen -->
<div class="modal-overlay" id="modal-rujuk-rontgen">
  <div class="modal">
    <form action="<?= site_url('pemeriksaan/rujukRontgen') ?>" method="post">
      <input type="hidden" name="id_periksa" id="rujuk-id-periksa">
      <div class="modal-header">
        <h3>Rujuk Rontgen / Radiologi</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row single">
          <div>
            <label class="form-label">Nama Pasien</label>
            <input id="rujuk-nama-pasien" class="form-control" readonly style="background: #f1f2f6;">
          </div>
        </div>
        <div class="form-row">
          <div>
            <label class="form-label">Jenis Rontgen</label>
            <select name="jenis_rontgen" class="form-control" required>
              <option value="">-- Pilih Jenis Rontgen --</option>
              <option value="Thorax (Dada)">Thorax (Dada)</option>
              <option value="Abdomen (Perut)">Abdomen (Perut)</option>
              <option value="Panoramic (Gigi)">Panoramic (Gigi)</option>
              <option value="Cranium (Kepala)">Cranium (Kepala)</option>
              <option value="Extremitas (Tangan/Kaki)">Extremitas (Tangan/Kaki)</option>
              <option value="Spine (Tulang Belakang)">Spine (Tulang Belakang)</option>
            </select>
          </div>
          <div>
            <label class="form-label">Keterangan Klinis</label>
            <input name="keterangan_klinis" class="form-control" placeholder="Contoh: Batuk kronis, sesak nafas" required>
          </div>
        </div>
        <div class="form-row single">
          <div>
            <label class="form-label">Catatan Tambahan (Opsional)</label>
            <textarea name="catatan" class="form-control" placeholder="Catatan tambahan untuk radiolog"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Kirim Rujukan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openRujukRontgenModal(idPeriksa, namaPasien) {
  document.getElementById('rujuk-id-periksa').value = idPeriksa;
  document.getElementById('rujuk-nama-pasien').value = namaPasien;
  
  const modal = document.getElementById('modal-rujuk-rontgen');
  if (modal) {
    modal.classList.add('show');
  }
}
</script>

