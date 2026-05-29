<!-- Data Dokter -->
<div class="page-section" id="page-dokter" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari dokter..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-dokter"><i class="fas fa-plus"></i> Tambah Dokter</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>NIP</th><th>Nama Dokter</th><th>Poli Spesialis</th><th>Jadwal</th><th>Kuota</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($dokter)): foreach($dokter as $d): ?>
          <tr>
            <td><?= $d['ID_DOKTER'] ?></td>
            <td><strong><?= $d['NAMA_DOKTER'] ?></strong></td>
            <td><span class="badge-status info" style="font-size: 11px; font-weight: 600;"><i class="fas fa-clinic-medical"></i> <?= $d['NAMA_POLI'] ?: '-' ?></span></td>
            <td><?= $d['JADWAL'] ?></td>
            <td style="font-weight: 700; color: #4a7dc7; text-align: center;"><?= $d['KUOTA_HARIAN'] ?></td>
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
