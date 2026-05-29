<!-- Data Perawat -->
<div class="page-section" id="page-perawat" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari perawat..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-perawat"><i class="fas fa-plus"></i> Tambah Perawat</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Perawat</th><th>Nama Perawat</th><th>Spesialis</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($perawat)): foreach($perawat as $pr): ?>
          <tr>
            <td><?= $pr['ID_PERAWAT'] ?></td>
            <td><?= $pr['NAMA_PERAWAT'] ?></td>
            <td><?= $pr['SPESIALIS_PERAWAT'] ?></td>
            <td>
              <button class="btn-icon" onclick='editData("perawat", <?= htmlspecialchars(json_encode($pr), ENT_QUOTES, "UTF-8") ?>)'><i class="fas fa-edit"></i></button>
              <a href="<?= site_url('perawat/delete/'.$pr['ID_PERAWAT']) ?>" class="btn-icon delete" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="4" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Perawat -->
<div class="modal-overlay" id="modal-perawat">
  <div class="modal">
    <form action="<?= site_url('perawat/save') ?>" method="post">
      <div class="modal-header"><h3>Tambah Perawat</h3><button type="button" class="modal-close"><i class="fas fa-times"></i></button></div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Perawat</label><input name="id_perawat" class="form-control" value="<?= $nextPerawatId ?>" readonly required></div>
          <div><label class="form-label">Nama Perawat</label><input name="nama_perawat" class="form-control" placeholder="Nama lengkap" required></div>
        </div>
        <div class="form-row single">
          <div><label class="form-label">Spesialis</label><input name="spesialis_perawat" class="form-control" placeholder="ICU/Anak/Umum"></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline btn-sm modal-close">Batal</button><button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button></div>
    </form>
  </div>
</div>
