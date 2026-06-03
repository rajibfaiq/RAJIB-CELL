<!-- Pemeriksaan (Rekam Medis) -->
<div class="page-section" id="page-rekammedis" style="display:none;">
  <div class="toolbar">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari rekam medis..."></div>
    <button class="btn btn-primary btn-sm" data-modal="modal-pemeriksaan"><i class="fas fa-plus"></i> Tambah Rekam Medis</button>
  </div>
  <div class="card">
    <div class="card-body no-pad">
      <table class="data-table">
        <thead><tr><th>ID Periksa</th><th>Nama Pasien</th><th>Nama Dokter</th><th>Tgl Periksa</th><th>Diagnosa</th><th>Biaya</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php if(!empty($pemeriksaan)): foreach($pemeriksaan as $rm): ?>
          <tr>
            <td><?= $rm['ID_PERIKSA'] ?></td>
            <td>
              <?php if (!empty($rm['NO_PENDAFTARAN'])): ?>
                <a href="javascript:void(0)" onclick="goToBillingFromPendaftaran('<?= $rm['NO_PENDAFTARAN'] ?>')" style="color: #4a7dc7; font-weight: bold; text-decoration: underline; cursor: pointer;" title="Klik untuk memproses/lihat rincian tagihan billing pasien">
                  <?= esc($rm['NAMA_PASIEN']) ?>
                </a>
              <?php else: ?>
                <strong><?= esc($rm['NAMA_PASIEN']) ?></strong>
              <?php endif; ?>
              <br><small style="color:#888;"><?= esc($rm['ID_PASIEN']) ?></small>
            </td>
            <td><strong><?= $rm['NAMA_DOKTER'] ?></strong> <br><small style="color:#888;"><?= $rm['ID_DOKTER'] ?></small></td>
            <td><?= $rm['TGL_PERIKSA'] ?></td>
            <td><?= $rm['DIAGNOSA'] ?></td>
            <td>Rp <?= number_format($rm['BIAYA_PEMBAYARAN'] ?? 50000, 0, ',', '.') ?></td>
            <td>
              <?php
                $isLunas = isset($rm['STATUS_PEMBAYARAN']) && $rm['STATUS_PEMBAYARAN'] === 'lunas';
                $statusClass = $isLunas ? 'active' : 'pending';
                $statusLabel = $isLunas ? 'Lunas' : 'Belum Bayar';
              ?>
              <span class="badge-status <?= $statusClass ?>" style="text-transform: uppercase; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                <?= $statusLabel ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 5px; align-items: center;">
                <?php if(!$isLunas && !empty($rm['NO_PENDAFTARAN'])): ?>
                  <button class="btn btn-success btn-sm" onclick="goToBillingFromPendaftaran('<?= $rm['NO_PENDAFTARAN'] ?>')" title="Bayar di Kasir" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-cash-register"></i> Bayar
                  </button>
                <?php elseif($isLunas && !empty($rm['NO_PENDAFTARAN'])): ?>
                  <button class="btn btn-outline btn-sm" onclick="goToBillingFromPendaftaran('<?= $rm['NO_PENDAFTARAN'] ?>')" title="Lihat Kuitansi" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; color: #4a7dc7; border-color: #4a7dc7; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-print"></i> Kuitansi
                  </button>
                <?php endif; ?>
                <button class="btn-icon edit" onclick='openEditPemeriksaanModal(<?= htmlspecialchars(json_encode($rm), ENT_QUOTES, "UTF-8") ?>)' title="Edit Rekam Medis"><i class="fas fa-edit"></i></button>
                <button class="btn btn-warning btn-sm" style="padding: 4px 8px; font-size: 11px; border-radius: 4px; color: #fff; background: #f0a500; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;" onclick="openRujukRontgenModal('<?= $rm['ID_PERIKSA'] ?>', '<?= addslashes($rm['NAMA_PASIEN']) ?>')"><i class="fas fa-x-ray"></i> Rujuk</button>
                <a href="<?= site_url('pemeriksaan/delete/'.$rm['ID_PERIKSA']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data?')"><i class="fas fa-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="8" style="text-align:center;padding:30px;color:#999;">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Rekam Medis -->
<div class="modal-overlay" id="modal-pemeriksaan">
  <div class="modal">
    <form action="<?= site_url('pemeriksaan/save') ?>" method="post">
      <div class="modal-header">
        <h3>Tambah Rekam Medis</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Periksa</label><input name="id_periksa" class="form-control" value="<?= $nextPemeriksaanId ?>" readonly required></div>
          <div><label class="form-label">Pilih Dokter</label>
            <select name="id_dokter" class="form-control" required>
              <option value="">-- Pilih Dokter --</option>
              <?php foreach($dokter as $d): ?>
                <option value="<?= $d['ID_DOKTER'] ?>"><?= $d['NAMA_DOKTER'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div><label class="form-label">Pilih Pasien</label>
            <select name="id_pasien" class="form-control" required>
              <option value="">-- Pilih Pasien --</option>
              <?php foreach($pasien as $p): ?>
                <option value="<?= $p['ID_PASIEN'] ?>"><?= $p['NAMA_PASIEN'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div><label class="form-label">Diagnosa</label><input name="diagnosa" class="form-control" placeholder="Diagnosa dokter" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Rujuk Rontgen -->
<div class="modal-overlay" id="modal-rujuk-rontgen">
  <div class="modal">
    <form action="<?= site_url('pemeriksaan/rujukRontgen') ?>" method="post">
      <input type="hidden" name="id_periksa" id="rujuk-id-periksa">
      <div class="modal-header">
        <h3>Rujuk Rontgen / Radiologi</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row single">
          <div>
            <label class="form-label">Nama Pasien</label>
            <input id="rujuk-nama-pasien" class="form-control" readonly style="background: #f1f2f6;">
          </div>
        </div>
        <div class="form-row">
          <div>
            <label class="form-label">Jenis Rontgen</label>
            <select name="jenis_rontgen" class="form-control" required>
              <option value="">-- Pilih Jenis Rontgen --</option>
              <option value="Thorax (Dada)">Thorax (Dada)</option>
              <option value="Abdomen (Perut)">Abdomen (Perut)</option>
              <option value="Panoramic (Gigi)">Panoramic (Gigi)</option>
              <option value="Cranium (Kepala)">Cranium (Kepala)</option>
              <option value="Extremitas (Tangan/Kaki)">Extremitas (Tangan/Kaki)</option>
              <option value="Spine (Tulang Belakang)">Spine (Tulang Belakang)</option>
            </select>
          </div>
          <div>
            <label class="form-label">Keterangan Klinis</label>
            <input name="keterangan_klinis" class="form-control" placeholder="Contoh: Batuk kronis, sesak nafas" required>
          </div>
        </div>
        <div class="form-row single">
          <div>
            <label class="form-label">Catatan Tambahan (Opsional)</label>
            <textarea name="catatan" class="form-control" placeholder="Catatan tambahan untuk radiolog"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Kirim Rujukan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openRujukRontgenModal(idPeriksa, namaPasien) {
  document.getElementById('rujuk-id-periksa').value = idPeriksa;
  document.getElementById('rujuk-nama-pasien').value = namaPasien;
  
  const modal = document.getElementById('modal-rujuk-rontgen');
  if (modal) {
    modal.classList.add('show');
  }
}

function openEditPemeriksaanModal(data) {
  const modal = document.getElementById('modal-pemeriksaan');
  if (!modal) return;
  
  const title = modal.querySelector('.modal-header h3');
  if (title) title.innerText = 'Edit Rekam Medis';
  
  const form = modal.querySelector('form');
  modal.querySelector('[name="id_periksa"]').value = data.ID_PERIKSA;
  modal.querySelector('[name="id_dokter"]').value = data.ID_DOKTER;
  modal.querySelector('[name="id_pasien"]').value = data.ID_PASIEN;
  modal.querySelector('[name="diagnosa"]').value = data.DIAGNOSA;
  
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

