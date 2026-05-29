<!-- Perawatan (Rawat Jalan) -->
<div class="page-section" id="page-rawatjalan" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari perawatan..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-perawatan"><i class="fas fa-plus"></i> Tambah Perawatan</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Perawatan</th><th>Nama Pasien</th><th>Nomor Kamar</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($perawatan)): foreach($perawatan as $rj): if($rj['RAWAT_JALAN'] == 1): ?>
          <tr>
            <td><?= $rj['ID_PERAWATAN'] ?></td>
            <td><strong><?= $rj['NAMA_PASIEN'] ?></strong> <br><small style="color:#888;"><?= $rj['ID_PASIEN'] ?></small></td>
            <td><?= !empty($rj['NOMOR_KAMAR']) ? 'Kamar ' . $rj['NOMOR_KAMAR'] : '<span style="color:#aaa;">-</span>' ?></td>
            <td>
              <a href="<?= site_url('perawatan/delete/'.$rj['ID_PERAWATAN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endif; endforeach; else: ?>
          <tr><td colspan="4" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Perawatan (Rawat Inap) -->
<div class="page-section" id="page-rawatinap" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari perawatan..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-perawatan"><i class="fas fa-plus"></i> Tambah Perawatan</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Perawatan</th><th>Nama Pasien</th><th>Nomor Kamar</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($perawatan)): foreach($perawatan as $ri): if($ri['RAWAT_INAP'] == 1): ?>
          <tr>
            <td><?= $ri['ID_PERAWATAN'] ?></td>
            <td><strong><?= $ri['NAMA_PASIEN'] ?></strong> <br><small style="color:#888;"><?= $ri['ID_PASIEN'] ?></small></td>
            <td><?= !empty($ri['NOMOR_KAMAR']) ? 'Kamar ' . $ri['NOMOR_KAMAR'] : '<span style="color:#aaa;">-</span>' ?></td>
            <td>
              <a href="<?= site_url('perawatan/delete/'.$ri['ID_PERAWATAN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endif; endforeach; else: ?>
          <tr><td colspan="4" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Perawatan -->
<div class="modal-overlay" id="modal-perawatan">
  <div class="modal">
    <form action="<?= site_url('perawatan/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Data Perawatan</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Perawatan</label><input name="id_perawatan" class="form-control" value="<?= $nextPerawatanId ?>" readonly required></div>
          <div><label class="form-label">Jenis Rawat</label>
            <select name="jenis_rawat" class="form-control">
              <option value="jalan">Rawat Jalan</option>
              <option value="inap">Rawat Inap</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Pilih Pasien</label>
            <select name="id_pasien" class="form-control" required>
              <option value="">-- Pilih Pasien --</option>
              <?php foreach($pasien as $p): ?>
                <option value="<?= $p['ID_PASIEN'] ?>"><?= $p['ID_PASIEN'] ?> - <?= $p['NAMA_PASIEN'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div><label class="form-label">Pilih Kamar (Opsional)</label>
            <select name="id_kamar" class="form-control">
              <option value="">-- Tanpa Kamar --</option>
              <?php foreach($kamar as $km): ?>
                <option value="<?= $km['ID_KAMAR'] ?>"><?= $km['NOMOR_KAMAR'] ?> - <?= $km['TIPE_KAMAR'] ?> (<?= $km['STATUS'] ?>)</option>
              <?php endforeach; ?>
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
