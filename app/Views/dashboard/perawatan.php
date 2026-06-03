<!-- Perawatan (Gabungan Rawat Jalan & Rawat Inap) -->
<div class="page-section" id="page-rawatjalan" style="display:none;">
  
  <!-- Tab Buttons -->
  <div class="tabs-container" style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #eaeeef; padding-bottom: 0;">
    <button type="button" class="tab-btn active" id="btn-tab-rawatjalan" onclick="switchPerawatanTab('jalan')" style="background: none; border: none; padding: 12px 24px; font-weight: 700; font-size: 14px; color: #4a7dc7; border-bottom: 3px solid #4a7dc7; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;">
      <i class="fas fa-walking"></i> Rawat Jalan
    </button>
    <button type="button" class="tab-btn" id="btn-tab-rawatinap" onclick="switchPerawatanTab('inap')" style="background: none; border: none; padding: 12px 24px; font-weight: 700; font-size: 14px; color: #7f8c8d; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;">
      <i class="fas fa-procedures"></i> Rawat Inap
    </button>
  </div>

  <!-- Rawat Jalan Content -->
  <div id="content-rawatjalan">
    <div class="toolbar">
      <div class="search-box"><i class="fas fa-search"></i><input type="text" id="search-rawatjalan" placeholder="Cari rawat jalan..." oninput="filterPerawatanTable('rawatjalan')"></div>
      <button class="btn btn-primary btn-sm" data-modal="modal-perawatan" onclick="setJenisRawatModal('jalan')"><i class="fas fa-plus"></i> Tambah Perawatan</button>
    </div>
    <div class="card">
      <div class="card-body no-pad">
        <table class="data-table" id="table-rawatjalan">
          <thead><tr><th>ID Perawatan</th><th>Nama Pasien</th><th>Tgl Perawatan</th><th>Nomor Kamar</th><th>Biaya</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            <?php 
              $hasJalan = false;
              if(!empty($perawatan)): 
                foreach($perawatan as $rj): 
                  if($rj['RAWAT_JALAN'] == 1): 
                    $hasJalan = true;
                    $isLunas = isset($rj['STATUS_JALAN']) && $rj['STATUS_JALAN'] === 'lunas';
                    $statusClass = $isLunas ? 'active' : 'pending';
                    $statusLabel = $isLunas ? 'Lunas' : 'Belum Bayar';
            ?>
            <tr>
              <td><span class="badge" style="background:#e8f0fe;color:#4a7dc7;padding:3px 8px;border-radius:4px;font-weight:700;"><?= $rj['ID_PERAWATAN'] ?></span></td>
              <td>
                <?php if (!empty($rj['NO_PENDAFTARAN'])): ?>
                  <a href="javascript:void(0)" onclick="goToBillingFromPendaftaran('<?= $rj['NO_PENDAFTARAN'] ?>')" style="color: #4a7dc7; font-weight: bold; text-decoration: underline; cursor: pointer;" title="Klik untuk memproses/lihat rincian tagihan billing pasien">
                    <?= htmlspecialchars($rj['NAMA_PASIEN']) ?>
                  </a>
                <?php else: ?>
                  <strong><?= htmlspecialchars($rj['NAMA_PASIEN']) ?></strong>
                <?php endif; ?>
                <br><small style="color:#888;"><?= htmlspecialchars($rj['ID_PASIEN']) ?></small>
              </td>
              <td><?= !empty($rj['TGL_PERAWATAN']) ? date('d M Y', strtotime($rj['TGL_PERAWATAN'])) : '<span style="color:#aaa;">-</span>' ?></td>
              <td><?= !empty($rj['NOMOR_KAMAR']) ? 'Kamar ' . $rj['NOMOR_KAMAR'] : '<span style="color:#aaa;">-</span>' ?></td>
              <td>Rp 30.000</td>
              <td>
                <span class="badge-status <?= $statusClass ?>" style="text-transform: uppercase; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                  <?= $statusLabel ?>
                </span>
              </td>
              <td>
                <div style="display: flex; gap: 5px; align-items: center;">
                  <?php if(!$isLunas && !empty($rj['NO_PENDAFTARAN'])): ?>
                    <button class="btn btn-success btn-sm" onclick="goToBillingFromPendaftaran('<?= $rj['NO_PENDAFTARAN'] ?>')" title="Bayar di Kasir" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fas fa-cash-register"></i> Bayar
                    </button>
                  <?php elseif($isLunas && !empty($rj['NO_PENDAFTARAN'])): ?>
                    <button class="btn btn-outline btn-sm" onclick="goToBillingFromPendaftaran('<?= $rj['NO_PENDAFTARAN'] ?>')" title="Lihat Kuitansi" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; color: #4a7dc7; border-color: #4a7dc7; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fas fa-print"></i> Kuitansi
                    </button>
                  <?php endif; ?>
                  <button class="btn-icon edit" onclick='openEditPerawatanModal(<?= htmlspecialchars(json_encode($rj), ENT_QUOTES, "UTF-8") ?>)' title="Edit Perawatan"><i class="fas fa-edit"></i></button>
                  <a href="<?= site_url('perawatan/delete/'.$rj['ID_PERAWATAN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data perawatan ini?')"><i class="fas fa-trash"></i></a>
                </div>
              </td>
            </tr>
            <?php 
                  endif; 
                endforeach; 
              endif; 
              if (!$hasJalan):
            ?>
            <tr><td colspan="7" style="text-align:center;padding:30px;color:#999;"><i class="fas fa-inbox" style="font-size:24px;display:block;margin-bottom:8px;color:#ddd;"></i>Belum ada data Rawat Jalan</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Rawat Inap Content -->
  <div id="content-rawatinap" style="display: none;">
    <div class="toolbar">
      <div class="search-box"><i class="fas fa-search"></i><input type="text" id="search-rawatinap" placeholder="Cari rawat inap..." oninput="filterPerawatanTable('rawatinap')"></div>
      <button class="btn btn-primary btn-sm" data-modal="modal-perawatan" onclick="setJenisRawatModal('inap')"><i class="fas fa-plus"></i> Tambah Perawatan</button>
    </div>
    <div class="card">
      <div class="card-body no-pad">
        <table class="data-table" id="table-rawatinap">
          <thead><tr><th>ID Perawatan</th><th>Nama Pasien</th><th>Tgl Perawatan</th><th>Nomor Kamar</th><th>Biaya</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            <?php 
              $hasInap = false;
              if(!empty($perawatan)): 
                foreach($perawatan as $ri): 
                  if($ri['RAWAT_INAP'] == 1): 
                    $hasInap = true;
                    $isLunas = isset($ri['STATUS_KAMAR']) && $ri['STATUS_KAMAR'] === 'lunas';
                    $statusClass = $isLunas ? 'active' : 'pending';
                    $statusLabel = $isLunas ? 'Lunas' : 'Belum Bayar';
            ?>
            <tr>
              <td><span class="badge" style="background:#fef3e8;color:#e67e22;padding:3px 8px;border-radius:4px;font-weight:700;"><?= $ri['ID_PERAWATAN'] ?></span></td>
              <td>
                <?php if (!empty($ri['NO_PENDAFTARAN'])): ?>
                  <a href="javascript:void(0)" onclick="goToBillingFromPendaftaran('<?= $ri['NO_PENDAFTARAN'] ?>')" style="color: #4a7dc7; font-weight: bold; text-decoration: underline; cursor: pointer;" title="Klik untuk memproses/lihat rincian tagihan billing pasien">
                    <?= htmlspecialchars($ri['NAMA_PASIEN']) ?>
                  </a>
                <?php else: ?>
                  <strong><?= htmlspecialchars($ri['NAMA_PASIEN']) ?></strong>
                <?php endif; ?>
                <br><small style="color:#888;"><?= htmlspecialchars($ri['ID_PASIEN']) ?></small>
              </td>
              <td><?= !empty($ri['TGL_PERAWATAN']) ? date('d M Y', strtotime($ri['TGL_PERAWATAN'])) : '<span style="color:#aaa;">-</span>' ?></td>
              <td><?= !empty($ri['NOMOR_KAMAR']) ? '<span style="color:#27ae60;font-weight:600;"><i class="fas fa-bed" style="margin-right:4px;"></i>Kamar ' . $ri['NOMOR_KAMAR'] . '</span>' : '<span style="color:#aaa;">-</span>' ?></td>
              <td>Rp 250.000</td>
              <td>
                <span class="badge-status <?= $statusClass ?>" style="text-transform: uppercase; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                  <?= $statusLabel ?>
                </span>
              </td>
              <td>
                <div style="display: flex; gap: 5px; align-items: center;">
                  <?php if(!$isLunas && !empty($ri['NO_PENDAFTARAN'])): ?>
                    <button class="btn btn-success btn-sm" onclick="goToBillingFromPendaftaran('<?= $ri['NO_PENDAFTARAN'] ?>')" title="Bayar di Kasir" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fas fa-cash-register"></i> Bayar
                    </button>
                  <?php elseif($isLunas && !empty($ri['NO_PENDAFTARAN'])): ?>
                    <button class="btn btn-outline btn-sm" onclick="goToBillingFromPendaftaran('<?= $ri['NO_PENDAFTARAN'] ?>')" title="Lihat Kuitansi" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; color: #4a7dc7; border-color: #4a7dc7; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fas fa-print"></i> Kuitansi
                    </button>
                  <?php endif; ?>
                  <button class="btn-icon edit" onclick='openEditPerawatanModal(<?= htmlspecialchars(json_encode($ri), ENT_QUOTES, "UTF-8") ?>)' title="Edit Perawatan"><i class="fas fa-edit"></i></button>
                  <a href="<?= site_url('perawatan/delete/'.$ri['ID_PERAWATAN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus data perawatan ini?')"><i class="fas fa-trash"></i></a>
                </div>
              </td>
            </tr>
            <?php 
                  endif; 
                endforeach; 
              endif; 
              if (!$hasInap):
            ?>
            <tr><td colspan="7" style="text-align:center;padding:30px;color:#999;"><i class="fas fa-inbox" style="font-size:24px;display:block;margin-bottom:8px;color:#ddd;"></i>Belum ada data Rawat Inap</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
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
          <div><label class="form-label">Tanggal Perawatan</label><input type="date" name="tgl_perawatan" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
          <div><label class="form-label">Pilih Pasien</label>
            <select name="id_pasien" class="form-control" required>
              <option value="">-- Pilih Pasien --</option>
              <?php foreach($pasien as $p): ?>
                <option value="<?= $p['ID_PASIEN'] ?>"><?= $p['ID_PASIEN'] ?> - <?= htmlspecialchars($p['NAMA_PASIEN']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div style="flex:1"><label class="form-label">Pilih Kamar (Opsional)</label>
            <select name="id_kamar" class="form-control">
              <option value="">-- Tanpa Kamar --</option>
              <?php foreach($kamar as $km): ?>
                <?php 
                  $isAvailable = (in_array(strtolower($km['STATUS']), ['tersedia', 'kosong']));
                  if ($isAvailable): 
                ?>
                  <option value="<?= $km['ID_KAMAR'] ?>"><?= $km['NOMOR_KAMAR'] ?> - <?= $km['TIPE_KAMAR'] ?> (<?= $km['STATUS'] ?>)</option>
                <?php else: ?>
                  <option value="<?= $km['ID_KAMAR'] ?>" disabled style="color: #aaa; background-color: #f5f5f5;"><?= $km['NOMOR_KAMAR'] ?> - <?= $km['TIPE_KAMAR'] ?> (<?= $km['STATUS'] ?>) - Terpakai</option>
                <?php endif; ?>
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

<script>
function switchPerawatanTab(type) {
  const btnJalan = document.getElementById('btn-tab-rawatjalan');
  const btnInap = document.getElementById('btn-tab-rawatinap');
  const contentJalan = document.getElementById('content-rawatjalan');
  const contentInap = document.getElementById('content-rawatinap');

  if (type === 'jalan') {
    btnJalan.classList.add('active');
    btnJalan.style.color = '#4a7dc7';
    btnJalan.style.borderBottom = '3px solid #4a7dc7';

    btnInap.classList.remove('active');
    btnInap.style.color = '#7f8c8d';
    btnInap.style.borderBottom = 'none';

    contentJalan.style.display = 'block';
    contentInap.style.display = 'none';
  } else {
    btnInap.classList.add('active');
    btnInap.style.color = '#4a7dc7';
    btnInap.style.borderBottom = '3px solid #4a7dc7';

    btnJalan.classList.remove('active');
    btnJalan.style.color = '#7f8c8d';
    btnJalan.style.borderBottom = 'none';

    contentInap.style.display = 'block';
    contentJalan.style.display = 'none';
  }
}

function setJenisRawatModal(type) {
  const selectJenis = document.querySelector('#modal-perawatan select[name="jenis_rawat"]');
  if (selectJenis) {
    selectJenis.value = type;
  }
}

function filterPerawatanTable(tabId) {
  const input = document.getElementById('search-' + tabId);
  const filter = input.value.toLowerCase();
  const table = document.getElementById('table-' + tabId);
  const rows = table.querySelectorAll('tbody tr');
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
}

// Auto-switch tab based on URL query parameter on page load
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');
  if (tab === 'inap') {
    switchPerawatanTab('inap');
  } else {
    switchPerawatanTab('jalan');
  }
});

function openEditPerawatanModal(data) {
  const modal = document.getElementById('modal-perawatan');
  if (!modal) return;
  
  const title = modal.querySelector('.modal-header h3');
  if (title) title.innerText = 'Edit Data Perawatan';
  
  const form = modal.querySelector('form');
  modal.querySelector('[name="id_perawatan"]').value = data.ID_PERAWATAN;
  modal.querySelector('[name="id_pasien"]').value = data.ID_PASIEN;
  modal.querySelector('[name="tgl_perawatan"]').value = data.TGL_PERAWATAN || '';
  modal.querySelector('[name="jenis_rawat"]').value = data.RAWAT_INAP == 1 ? 'inap' : 'jalan';
  modal.querySelector('[name="id_kamar"]').value = data.ID_KAMAR || '';
  
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
