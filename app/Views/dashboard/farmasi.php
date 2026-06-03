<!-- Farmasi -->
<div class="page-section" id="page-farmasi" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari obat..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-farmasi"><i class="fas fa-plus"></i> Tambah Obat</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Farmasi</th><th>Nama Obat</th><th>Jenis Obat</th><th>Harga</th><th style="width: 100px; text-align: center;">Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($farmasi)): foreach($farmasi as $f): ?>
          <tr>
            <td><span class="badge-status info" style="font-family: monospace; font-size: 11px; font-weight: 600;"><?= $f['ID_FARMASI'] ?></span></td>
            <td><strong><?= esc($f['NAMA_OBAT'] ?? '-') ?></strong></td>
            <td><span class="badge-status" style="background:#e8f0fe; color:#4a7dc7; font-weight: 700; font-size: 11px;"><?= esc($f['JENIS_OBAT'] ?? '-') ?></span></td>
            <td style="font-weight: 700; color: #2c3e50;">Rp <?= number_format((float)($f['HARGA_OBAT'] ?? 0), 0, ',', '.') ?></td>
            <td style="text-align: center;">
              <button class="btn-icon edit" onclick='openEditFarmasiModal(<?= htmlspecialchars(json_encode($f), ENT_QUOTES, "UTF-8") ?>)' title="Edit Obat" style="margin-right: 5px;"><i class="fas fa-edit"></i></button>
              <a href="<?= site_url('farmasi/delete/'.$f['ID_FARMASI']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data obat?')"><i class="fas fa-trash"></i></a>
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

<!-- Modal Tambah Farmasi -->
<div class="modal-overlay" id="modal-farmasi">
  <div class="modal" style="width: 500px;">
    <form action="<?= site_url('farmasi/save') ?>" method="post">
      <input type="hidden" name="is_edit" value="0">
      <div class="modal-header">
        <h3>Tambah Data Obat</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div>
            <label class="form-label">ID Farmasi</label>
            <input name="id_farmasi" class="form-control" value="<?= $nextFarmasiId ?>" readonly required>
          </div>
          <div>
            <label class="form-label">Nama Obat</label>
            <input name="nama_obat" class="form-control" placeholder="Contoh: Paracetamol 500mg" required>
          </div>
        </div>
        <div class="form-row">
          <div>
            <label class="form-label">Jenis Obat</label>
            <input name="jenis_obat" class="form-control" placeholder="Contoh: Tablet / Sirup / Kapsul" required>
          </div>
          <div>
            <label class="form-label">Harga Obat (Rp)</label>
            <input type="number" name="harga_obat" class="form-control" placeholder="Contoh: 10000" min="0" required style="font-weight: 700;">
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

<script>
function openEditFarmasiModal(data) {
  const modal = document.getElementById('modal-farmasi');
  if (!modal) return;
  
  const title = modal.querySelector('.modal-header h3');
  if (title) title.innerText = 'Edit Data Obat';
  
  const form = modal.querySelector('form');
  modal.querySelector('[name="id_farmasi"]').value = data.ID_FARMASI;
  modal.querySelector('[name="nama_obat"]').value = data.NAMA_OBAT || '';
  modal.querySelector('[name="jenis_obat"]').value = data.JENIS_OBAT || '';
  modal.querySelector('[name="harga_obat"]').value = data.HARGA_OBAT || '';
  
  let isEditInput = form.querySelector('[name="is_edit"]');
  if (!isEditInput) {
      isEditInput = document.createElement('input');
      isEditInput.type = 'hidden';
      isEditInput.name = 'is_edit';
      form.appendChild(isEditInput);
  }
  isEditInput.value = '1';
  
  modal.classList.add('show');
}
</script>
