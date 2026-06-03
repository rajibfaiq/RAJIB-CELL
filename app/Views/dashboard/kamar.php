<!-- Data Kamar -->
<div class="page-section" id="page-kamarpage" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari kamar..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-kamar"><i class="fas fa-plus"></i> Tambah Kamar</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Kamar</th><th>Nomor Kamar</th><th>Tipe</th><th>Status</th><th>Pasien Terisi</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($kamar)): foreach($kamar as $k): ?>
          <tr>
            <td><?= $k['ID_KAMAR'] ?></td>
            <td><?= $k['NOMOR_KAMAR'] ?></td>
            <td><?= $k['TIPE_KAMAR'] ?></td>
            <td>
              <?php if (strtolower($k['STATUS']) == 'terisi'): ?>
                <span class="badge badge-danger" style="background-color: #dc3545; color: white;"><?= htmlspecialchars($k['STATUS']) ?></span>
              <?php else: ?>
                <span class="badge badge-success" style="background-color: #28a745; color: white;"><?= htmlspecialchars($k['STATUS']) ?></span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($k['NAMA_PASIEN'])): ?>
                <strong><?= htmlspecialchars($k['NAMA_PASIEN']) ?></strong> <br><small style="color:#888;"><?= htmlspecialchars($k['ID_PERIKSA']) ?></small>
              <?php elseif (!empty($k['NAMA_PASIEN_PERAWATAN'])): ?>
                <strong><?= htmlspecialchars($k['NAMA_PASIEN_PERAWATAN']) ?></strong> <br><small style="color:#e67e22; font-weight: bold;"><i class="fas fa-bed"></i> Perawatan</small>
              <?php else: ?>
                <span style="color:#aaa;">-</span>
              <?php endif; ?>
            </td>
             <td>
              <button class="btn-icon" onclick='editData("kamar", <?= htmlspecialchars(json_encode($k), ENT_QUOTES, "UTF-8") ?>)'><i class="fas fa-edit"></i></button>
              <a href="<?= site_url('kamar/delete/'.$k['ID_KAMAR']) ?>" class="btn-icon delete" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
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

<!-- Modal Tambah Kamar -->
<div class="modal-overlay" id="modal-kamar">
  <div class="modal">
    <form action="<?= site_url('kamar/save') ?>" method="post">
      <div class="modal-header"><h3>Tambah Kamar</h3><button type="button" class="modal-close"><i class="fas fa-times"></i></button></div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Kamar</label><input name="id_kamar" class="form-control" value="<?= $nextKamarId ?>" readonly required></div>
          <div><label class="form-label">Nomor Kamar</label><input name="nomor_kamar" class="form-control" placeholder="101" required></div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Tipe Kamar</label>
            <select name="tipe_kamar" class="form-control"><option value="VIP">VIP</option><option value="Kelas 1">Kelas 1</option><option value="Kelas 2">Kelas 2</option><option value="Kelas 3">Kelas 3</option></select>
          </div>
          <div><label class="form-label">Status</label>
            <select name="status" class="form-control"><option value="Tersedia">Tersedia</option><option value="Terisi">Terisi</option></select>
          </div>
        </div>
        <div class="form-row single">
          <div><label class="form-label">ID Periksa (Opsional)</label><input name="id_periksa" class="form-control" placeholder="PRK001"></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline btn-sm modal-close">Batal</button><button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button></div>
    </form>
  </div>
</div>
