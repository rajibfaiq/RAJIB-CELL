<!-- Laboratorium -->
<div class="page-section" id="page-laboratorium" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari data lab..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-laboratorium"><i class="fas fa-plus"></i> Tambah Data Lab</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Lab</th><th>Nama Pasien</th><th>Jenis Pemeriksaan</th><th>Hasil</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($laboratorium)): foreach($laboratorium as $l): ?>
          <tr>
            <td><?= $l['ID_LABORATORIUM'] ?></td>
            <td><strong><?= $l['NAMA_PASIEN'] ?></strong> <br><small style="color:#888;"><?= $l['ID_PERIKSA'] ?></small></td>
            <td><?= $l['JENIS_PEMERIKSAAN'] ?></td>
            <td><?= $l['HASIL_LAB'] ?></td>
            <td>
              <button class="btn-icon edit" onclick='openEditLaboratoriumModal(<?= htmlspecialchars(json_encode($l), ENT_QUOTES, "UTF-8") ?>)' title="Edit Data Lab" style="margin-right: 5px;"><i class="fas fa-edit"></i></button>
              <a href="<?= site_url('laboratorium/delete/'.$l['ID_LABORATORIUM']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data?')"><i class="fas fa-trash"></i></a>
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

<!-- Modal Tambah Laboratorium -->
<div class="modal-overlay" id="modal-laboratorium">
  <div class="modal">
    <form action="<?= site_url('laboratorium/save') ?>" method="post">
      <input type="hidden" name="is_edit" value="0">
      <div class="modal-header">
        <h3>Tambah Data Lab</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Lab</label><input name="id_lab" class="form-control" value="<?= $nextLaboratoriumId ?>" readonly required></div>
          <div><label class="form-label">ID Periksa</label><input name="id_periksa" class="form-control" placeholder="PRK001" required></div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Jenis Pemeriksaan</label><input name="jenis_periksa" class="form-control" placeholder="Darah/Rontgen/dll" required></div>
          <div><label class="form-label">Hasil Lab</label><input name="hasil_lab" class="form-control" placeholder="Hasil pemeriksaan" required></div>
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
function openEditLaboratoriumModal(data) {
  const modal = document.getElementById('modal-laboratorium');
  if (!modal) return;
  
  const title = modal.querySelector('.modal-header h3');
  if (title) title.innerText = 'Edit Data Lab';
  
  const form = modal.querySelector('form');
  modal.querySelector('[name="id_lab"]').value = data.ID_LABORATORIUM;
  modal.querySelector('[name="id_periksa"]').value = data.ID_PERIKSA;
  modal.querySelector('[name="jenis_periksa"]').value = data.JENIS_PEMERIKSAAN;
  modal.querySelector('[name="hasil_lab"]').value = data.HASIL_LAB;
  
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
