<!-- Pengobatan -->
<div class="page-section" id="page-pengobatan" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari pengobatan..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-pengobatan"><i class="fas fa-plus"></i> Tambah Pengobatan</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID Pengobatan</th>
            <th>Nama Pasien</th>
            <th>Nama Obat</th>
            <th style="text-align: right; width: 140px;">Harga Obat</th>
            <th>Dosis</th>
            <th style="width: 100px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($pengobatan)): foreach($pengobatan as $ob): ?>
          <tr>
            <td><span class="badge-status info" style="font-family: monospace; font-size: 11px; font-weight: 600;"><?= $ob['ID_PENGOBATAN'] ?></span></td>
            <td><strong><?= $ob['NAMA_PASIEN'] ?></strong> <br><small style="color:#888; font-family: monospace;"><?= $ob['ID_PERIKSA'] ?></small></td>
            <td><strong style="color: #2c3e50;"><i class="fas fa-pills" style="color: #e056fd; margin-right: 5px;"></i> <?= $ob['NAMA_OBAT'] ?></strong></td>
            <td style="text-align: right; font-weight: 700; color: #2c3e50;">Rp <?= number_format($ob['HARGA_OBAT'] ?? 0, 0, ',', '.') ?></td>
            <td><span class="badge-status" style="background:#e8f0fe; color:#4a7dc7; font-weight: 700; font-size: 11px;"><?= $ob['DOSIS_OBAT'] ?></span></td>
            <td style="text-align: center;">
              <a href="<?= site_url('pengobatan/delete/'.$ob['ID_PENGOBATAN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data pengobatan?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="6" style="text-align:center;padding:30px;color:#999;">Belum ada data pengobatan</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Pengobatan -->
<div class="modal-overlay" id="modal-pengobatan">
  <div class="modal" style="width: 500px;">
    <form action="<?= site_url('pengobatan/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Pengobatan / Resep Obat</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div>
            <label class="form-label">ID Pengobatan</label>
            <input name="id_pengobatan" class="form-control" value="<?= $nextPengobatanId ?>" readonly required>
          </div>
          <div>
            <label class="form-label">Pilih Rekam Medis (ID Periksa)</label>
            <select name="id_periksa" class="form-control" required style="padding: 10px; font-size: 13px;">
              <option value="">-- Pilih Rekam Medis --</option>
              <?php if (!empty($pemeriksaan)): foreach($pemeriksaan as $pm): ?>
                <option value="<?= $pm['ID_PERIKSA'] ?>">
                  <?= $pm['ID_PERIKSA'] ?> — <?= $pm['NAMA_PASIEN'] ?> (No. Daftar: <?= $pm['NO_PENDAFTARAN'] ?: '-' ?>)
                </option>
              <?php endforeach; endif; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div>
            <label class="form-label">Nama Obat</label>
            <input name="nama_obat" class="form-control" placeholder="Contoh: Paracetamol 500mg" required>
          </div>
          <div>
            <label class="form-label">Dosis Obat</label>
            <input name="dosis_obat" class="form-control" placeholder="Contoh: 3 x 1 tablet" required>
          </div>
        </div>
        <div class="form-row single" style="margin-top: 10px;">
          <div>
            <label class="form-label">Harga Obat (Rp)</label>
            <input type="number" name="harga_obat" class="form-control" placeholder="Contoh: 15000" min="0" required style="font-weight: 700;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan Resep</button>
      </div>
    </form>
  </div>
</div>
