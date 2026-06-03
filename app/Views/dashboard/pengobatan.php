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
            <th>Status Bayar</th>
            <th style="width: 150px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($pengobatan)): foreach($pengobatan as $ob): ?>
          <tr>
            <td><span class="badge-status info" style="font-family: monospace; font-size: 11px; font-weight: 600;"><?= $ob['ID_PENGOBATAN'] ?></span></td>
            <td>
              <?php if (!empty($ob['NO_PENDAFTARAN'])): ?>
                <a href="javascript:void(0)" onclick="goToBillingFromPendaftaran('<?= $ob['NO_PENDAFTARAN'] ?>')" style="color: #4a7dc7; font-weight: bold; text-decoration: underline; cursor: pointer;" title="Klik untuk memproses/lihat rincian tagihan billing pasien">
                  <?= esc($ob['NAMA_PASIEN']) ?>
                </a>
              <?php else: ?>
                <strong><?= esc($ob['NAMA_PASIEN']) ?></strong>
              <?php endif; ?>
              <br><small style="color:#888; font-family: monospace;"><?= esc($ob['ID_PERIKSA']) ?></small>
            </td>
            <td><strong style="color: #2c3e50;"><i class="fas fa-pills" style="color: #e056fd; margin-right: 5px;"></i> <?= $ob['NAMA_OBAT'] ?></strong></td>
            <td style="text-align: right; font-weight: 700; color: #2c3e50;">Rp <?= number_format($ob['HARGA_OBAT'] ?? 0, 0, ',', '.') ?></td>
            <td><span class="badge-status" style="background:#e8f0fe; color:#4a7dc7; font-weight: 700; font-size: 11px;"><?= $ob['DOSIS_OBAT'] ?></span></td>
            <td>
              <?php
                $isLunas = isset($ob['STATUS_PEMBAYARAN']) && $ob['STATUS_PEMBAYARAN'] === 'lunas';
                $statusClass = $isLunas ? 'active' : 'pending';
                $statusLabel = $isLunas ? 'Lunas' : 'Belum Bayar';
              ?>
              <span class="badge-status <?= $statusClass ?>" style="text-transform: uppercase; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                <?= $statusLabel ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 5px; align-items: center; justify-content: center;">
                <?php if(!$isLunas && !empty($ob['NO_PENDAFTARAN'])): ?>
                  <button class="btn btn-success btn-sm" onclick="goToBillingFromPendaftaran('<?= $ob['NO_PENDAFTARAN'] ?>')" title="Bayar di Kasir" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-cash-register"></i> Bayar
                  </button>
                <?php elseif($isLunas && !empty($ob['NO_PENDAFTARAN'])): ?>
                  <button class="btn btn-outline btn-sm" onclick="goToBillingFromPendaftaran('<?= $ob['NO_PENDAFTARAN'] ?>')" title="Lihat Kuitansi" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; color: #4a7dc7; border-color: #4a7dc7; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-print"></i> Kuitansi
                  </button>
                <?php endif; ?>
                <button class="btn-icon edit" onclick='openEditPengobatanModal(<?= htmlspecialchars(json_encode($ob), ENT_QUOTES, "UTF-8") ?>)' title="Edit Pengobatan"><i class="fas fa-edit"></i></button>
                <a href="<?= site_url('pengobatan/delete/'.$ob['ID_PENGOBATAN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data pengobatan?')"><i class="fas fa-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:#999;">Belum ada data pengobatan</td></tr>
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
            <select name="id_farmasi" id="select-id-farmasi" class="form-control" required style="padding: 10px; font-size: 13px;">
              <option value="">-- Pilih Obat --</option>
              <?php if (!empty($farmasi)): foreach($farmasi as $f): ?>
                <option value="<?= $f['ID_FARMASI'] ?>" data-nama="<?= esc($f['NAMA_OBAT']) ?>" data-harga="<?= (int)$f['HARGA_OBAT'] ?>">
                  <?= esc($f['NAMA_OBAT']) ?> (<?= esc($f['JENIS_OBAT']) ?>) — Rp <?= number_format($f['HARGA_OBAT'], 0, ',', '.') ?>
                </option>
              <?php endforeach; endif; ?>
            </select>
            <input type="hidden" name="nama_obat" id="hidden-nama-obat">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectFarmasi = document.getElementById('select-id-farmasi');
    const inputHarga = document.querySelector('#modal-pengobatan input[name="harga_obat"]');
    const hiddenNamaObat = document.getElementById('hidden-nama-obat');
    
    if (selectFarmasi) {
        selectFarmasi.addEventListener('change', function() {
            const selectedOption = selectFarmasi.options[selectFarmasi.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const harga = selectedOption.getAttribute('data-harga');
                const nama = selectedOption.getAttribute('data-nama');
                
                if (inputHarga) inputHarga.value = harga || 0;
                if (hiddenNamaObat) hiddenNamaObat.value = nama || '';
            } else {
                if (inputHarga) inputHarga.value = '';
                if (hiddenNamaObat) hiddenNamaObat.value = '';
            }
        });
    }
});

function openEditPengobatanModal(data) {
  const modal = document.getElementById('modal-pengobatan');
  if (!modal) return;
  
  const title = modal.querySelector('.modal-header h3');
  if (title) title.innerText = 'Edit Pengobatan / Resep Obat';
  
  const form = modal.querySelector('form');
  modal.querySelector('[name="id_pengobatan"]').value = data.ID_PENGOBATAN;
  modal.querySelector('[name="id_periksa"]').value = data.ID_PERIKSA;
  modal.querySelector('[name="id_farmasi"]').value = data.ID_FARMASI || '';
  modal.querySelector('[name="nama_obat"]').value = data.NAMA_OBAT;
  modal.querySelector('[name="dosis_obat"]').value = data.DOSIS_OBAT;
  modal.querySelector('[name="harga_obat"]').value = data.HARGA_OBAT || 0;
  
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
