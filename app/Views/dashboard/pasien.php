<!-- Data Pasien -->
<div class="page-section" id="page-pasien" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari pasien..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-pasien"><i class="fas fa-plus"></i> Tambah Pasien</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>No. RM</th><th>Nama Lengkap</th><th>L/P</th><th>Tgl Lahir</th><th>Alamat</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($pasien)): foreach($pasien as $p): ?>
          <tr>
            <td><?= $p['ID_PASIEN'] ?></td>
            <td><?= $p['NAMA_PASIEN'] ?></td>
            <td><?= $p['JENIS_KELAMIN'] ?></td>
            <td><?= $p['TGL_LAHIR'] ?></td>
            <td><?= $p['ALAMAT_PASIEN'] ?></td>
            <td><span class="badge badge-success">Terdaftar</span></td>
            <td>
              <button class="btn-icon" onclick='editData("pasien", <?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>)'><i class="fas fa-edit"></i></button>
              <a href="<?= site_url('pasien/delete/'.$p['ID_PASIEN']) ?>" class="btn-icon delete" onclick="return confirm('Yakin ingin menghapus pasien ini?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:#999;">Belum ada data pasien</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Pasien -->
<div class="modal-overlay" id="modal-pasien">
  <div class="modal">
    <form action="<?= site_url('pasien/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Pasien Baru</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Pasien (RM)</label><input name="id_pasien" class="form-control" value="<?= $nextPasienId ?>" readonly required></div>
          <div><label class="form-label">Nama Lengkap</label><input name="nama_pasien" class="form-control" placeholder="Nama lengkap" required></div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-control">
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>
          <div><label class="form-label">Tanggal Lahir</label><input type="date" name="tgl_lahir" class="form-control"></div>
        </div>
        <div class="form-row single">
          <div><label class="form-label">Alamat</label><textarea name="alamat_pasien" class="form-control" placeholder="Alamat lengkap"></textarea></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>
