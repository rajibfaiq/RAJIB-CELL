<!-- Administrasi (Billing) -->
<div class="page-section" id="page-billing" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari billing..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-administrasi"><i class="fas fa-plus"></i> Tambah Billing</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Admin</th><th>Nama Pasien</th><th>Biaya</th><th>Jenis Pembayaran</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($administrasi)): foreach($administrasi as $a): ?>
          <tr>
            <td><?= $a['ID_ADMINISTRASI'] ?></td>
            <td><strong><?= $a['NAMA_PASIEN'] ?></strong> <br><small style="color:#888;"><?= $a['NO_PENDAFTARAN'] ?></small></td>
            <td>Rp <?= number_format($a['BIAYA'], 0, ',', '.') ?></td>
            <td><?= $a['JENIS_PEMBAYARAN'] ?></td>
            <td>
              <a href="<?= site_url('administrasi/delete/'.$a['ID_ADMINISTRASI']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data billing?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="5" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Administrasi -->
<div class="modal-overlay" id="modal-administrasi">
  <div class="modal">
    <form action="<?= site_url('administrasi/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Billing Pembayaran</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Administrasi</label><input name="id_admin" class="form-control" value="<?= $nextAdministrasiId ?>" readonly required></div>
          <div><label class="form-label">Pilih Pendaftaran</label>
            <select name="no_daftar" class="form-control" required>
              <option value="">-- Pilih No. Daftar --</option>
              <?php foreach($pendaftaran as $pn): ?>
                <option value="<?= $pn['NO_PENDAFTARAN'] ?>"><?= $pn['NO_PENDAFTARAN'] ?> - <?= $pn['NAMA_PASIEN'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Biaya</label><input type="number" name="biaya" class="form-control" placeholder="50000" required></div>
          <div><label class="form-label">Jenis Pembayaran</label>
            <select name="jenis_bayar" class="form-control">
              <option value="Tunai">Tunai</option>
              <option value="BPJS">BPJS</option>
              <option value="Asuransi">Asuransi</option>
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
