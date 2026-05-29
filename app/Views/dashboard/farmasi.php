<!-- Farmasi -->
<div class="page-section" id="page-farmasi" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari obat..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-farmasi"><i class="fas fa-plus"></i> Tambah Obat</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Farmasi</th><th>Nama Obat</th><th>Jenis Obat</th><th>Harga</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($farmasi)): foreach($farmasi as $f): ?>
          <tr>
            <td><?= $f['ID_FARMASI'] ?></td>
            <td><strong><?= !empty($f['NAMA_OBAT']) ? $f['NAMA_OBAT'] : '<span style="color:#aaa;">-</span>' ?></strong> <br><small style="color:#888;"><?= $f['ID_PENGOBATAN'] ?></small></td>
            <td><?= $f['JENIS_OBAT'] ?></td>
            <td><?= $f['HARGA_OBAT'] ?></td>
            <td>
              <a href="<?= site_url('farmasi/delete/'.$f['ID_FARMASI']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data obat?')"><i class="fas fa-trash"></i></a>
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

<!-- Modal Tambah Farmasi -->
<div class="modal-overlay" id="modal-farmasi">
  <div class="modal">
    <form action="<?= site_url('farmasi/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Data Obat</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Farmasi</label><input name="id_farmasi" class="form-control" value="<?= $nextFarmasiId ?>" readonly required></div>
          <div><label class="form-label">Jenis Obat</label><input name="jenis_obat" class="form-control" placeholder="Tablet/Sirup" required></div>
        </div>
        <div class="form-row single">
          <div><label class="form-label">Harga Obat</label><input name="harga_obat" class="form-control" placeholder="10000" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>
