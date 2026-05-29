<!-- Pembayaran Baru (Billing Per Layanan) -->
<div class="page-section" id="page-pembayaran" style="display:none;">
  
  <!-- Search by Pendaftaran to view individual patient visit bills -->
  <div class="card" style="margin-bottom: 20px; background: #fff; border-radius: 8px; border: 1px solid #eaeeef; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <div class="card-body" style="padding: 20px;">
      <div style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 250px;">
          <label class="form-label" style="font-weight: 700; color: #2c3e50;"><i class="fas fa-search-dollar" style="color:#4a7dc7; margin-right: 5px;"></i> Cari Riwayat Tagihan Pasien (Per Kunjungan)</label>
          <div style="display: flex; gap: 8px; margin-top: 6px;">
            <select id="cari-pendaftaran-pembayaran" class="form-control" style="padding: 10px; border-radius: 6px; font-size:13px;" onchange="loadBillingByPendaftaran()">
              <option value="">-- Pilih Nomor Pendaftaran / Nama Pasien --</option>
              <?php if (!empty($pendaftaran)): foreach($pendaftaran as $pd): ?>
                <option value="<?= $pd['NO_PENDAFTARAN'] ?>"><?= $pd['NO_PENDAFTARAN'] ?> — <?= $pd['NAMA_PASIEN'] ?> (<?= date('d/m/Y', strtotime($pd['TANGGAL_DAFTAR'])) ?>)</option>
              <?php endforeach; endif; ?>
            </select>
            <button class="btn btn-primary" onclick="loadBillingByPendaftaran()" style="width: auto; padding: 10px 20px; font-weight:700;"><i class="fas fa-search"></i> Cari</button>
            <button class="btn btn-outline" onclick="resetBillingView()" style="width: auto; padding: 10px 15px;"><i class="fas fa-times-circle"></i> Reset</button>
          </div>
        </div>
        <div style="width: auto;">
          <button class="btn btn-primary" data-modal="modal-tambah-tagihan" style="padding: 11px 20px; font-weight:700;"><i class="fas fa-plus-circle"></i> Tambah Tagihan Manual</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Dynamic Summary Cards (Only visible when a specific registration is filtered) -->
  <div id="billing-summary-section" class="stats-grid" style="display: none; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;">
    <div class="stat-card" style="border-left: 5px solid #4a7dc7; background: #fff;">
      <div class="stat-info">
        <h3 id="bill-sum-total" style="font-size:24px; font-weight: 700; color:#2c3e50;">Rp 0</h3>
        <p style="font-size: 11px; color:#888;">Total Tagihan Kunjungan</p>
      </div>
    </div>
    <div class="stat-card green" style="border-left: 5px solid #28a745; background: #fff;">
      <div class="stat-info">
        <h3 id="bill-sum-lunas" style="font-size:24px; font-weight: 700; color:#28a745;">Rp 0</h3>
        <p style="font-size: 11px; color:#888;">Sudah Dibayar</p>
      </div>
    </div>
    <div class="stat-card amber" style="border-left: 5px solid #ffc107; background: #fff;">
      <div class="stat-info">
        <h3 id="bill-sum-belum" style="font-size:24px; font-weight: 700; color:#e0a800;">Rp 0</h3>
        <p style="font-size: 11px; color:#888;">Belum Dibayar</p>
      </div>
    </div>
  </div>

  <!-- Billing Table -->
  <div class="card" style="background:#fff; border-radius:8px; border:1px solid #eaeeef;">
    <div class="card-header" style="background:#fafbfc; border-bottom:1px solid #eee; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <h3 style="font-size: 15px; font-weight: 700; color: #2c3e50; margin: 0;" id="billing-title"><i class="fas fa-file-invoice-dollar" style="color:#4a7dc7; margin-right:8px;"></i> Semua Riwayat Transaksi Billing Pasien</h3>
      <span style="font-size: 11px; color:#aaa; font-weight: 600;" id="billing-subtitle">Daftar Transaksi Per Bagian / Tindakan</span>
    </div>
    <div class="card-body no-pad">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID Tagihan</th>
            <th>No. Daftar</th>
            <th>Tanggal</th>
            <th>Nama Pasien</th>
            <th>Layanan / Tindakan</th>
            <th>Keterangan</th>
            <th style="text-align: right; width: 150px;">Biaya</th>
            <th style="width: 140px; text-align: center;">Status</th>
            <th style="width: 250px; text-align: center;">Aksi & Kuitansi</th>
          </tr>
        </thead>
        <tbody id="billing-table-body">
          <?php if(!empty($pembayaran)): foreach($pembayaran as $p): ?>
          <?php
            $isLunas = $p['STATUS'] === 'lunas';
            $statusClass = $isLunas ? 'active' : 'pending';
          ?>
          <tr id="bill-row-<?= $p['ID_PEMBAYARAN'] ?>">
            <td><span class="badge-status info" style="font-family: monospace; font-size: 11px; font-weight: 600;"><?= $p['ID_PEMBAYARAN'] ?></span></td>
            <td><span style="font-family: monospace; font-size:11px;"><?= $p['NO_PENDAFTARAN'] ?></span></td>
            <td><span style="font-size:12px;"><?= date('Y-m-d H:i', strtotime($p['CREATED_AT'])) ?></span></td>
            <td>
              <a href="javascript:void(0)" onclick="selectAndLoadBilling('<?= $p['NO_PENDAFTARAN'] ?>')" style="color: #4a7dc7; font-weight: bold; text-decoration: underline; cursor: pointer;" title="Klik untuk filter rincian tagihan pasien ini">
                <?= $p['NAMA_PASIEN'] ?: 'Umum' ?>
              </a>
            </td>
            <td><span style="text-transform: capitalize; font-weight: 600; color: #4a7dc7;"><i class="fas fa-tag"></i> <?= str_replace('_', ' ', $p['JENIS_LAYANAN']) ?></span></td>
            <td><span style="font-size: 12px; color: #666;"><?= $p['KETERANGAN_LAYANAN'] ?: '-' ?></span></td>
            <td style="text-align: right; font-weight: 700; color: #2c3e50;">Rp <?= number_format($p['BIAYA'], 0, ',', '.') ?></td>
            <td style="text-align: center;">
              <span class="badge-status <?= $statusClass ?>" style="text-transform: uppercase; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                <?= $isLunas ? 'Lunas' : 'Belum Bayar' ?>
              </span>
            </td>
            <td style="text-align: center;">
              <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                <?php if(!$isLunas): ?>
                  <button class="btn btn-success btn-sm" onclick="openBayarTagihanModal('<?= $p['ID_PEMBAYARAN'] ?>', '<?= $p['NO_PENDAFTARAN'] ?>', '<?= addslashes($p['NAMA_PASIEN']) ?>', '<?= $p['JENIS_LAYANAN'] ?>', <?= $p['BIAYA'] ?>)" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; font-weight: 700;"><i class="fas fa-cash-register"></i> Bayar</button>
                <?php else: ?>
                  <button class="btn btn-outline btn-sm" onclick="cetakKuitansiBilling('<?= $p['ID_PEMBAYARAN'] ?>')" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; color: #4a7dc7; border-color: #4a7dc7; font-weight: 700;"><i class="fas fa-print"></i> Cetak Kuitansi</button>
                <?php endif; ?>
                <a href="<?= site_url('pembayaran/delete/'.$p['ID_PEMBAYARAN']) ?>" class="btn-icon delete" onclick="return confirm('Hapus tagihan ini?')"><i class="fas fa-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="9" style="text-align:center;padding:30px;color:#999;">Belum ada data billing</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Tagihan Manual -->
<div class="modal-overlay" id="modal-tambah-tagihan">
  <div class="modal">
    <form action="<?= site_url('pembayaran/save') ?>" method="post">
      <div class="modal-header">
        <h3>Buat Tagihan Billing Baru</h3>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div><label class="form-label">ID Tagihan</label><input name="id_pembayaran" class="form-control" value="<?= $nextPembayaranId ?>" readonly required></div>
          <div><label class="form-label">Nomor Pendaftaran Pasien</label>
            <select name="no_pendaftaran" class="form-control" required>
              <option value="">-- Pilih Pendaftaran Pasien --</option>
              <?php 
                // Display pendaftaran
                if (!empty($pendaftaran)) {
                  foreach($pendaftaran as $pd):
              ?>
                <option value="<?= $pd['NO_PENDAFTARAN'] ?>"><?= $pd['NO_PENDAFTARAN'] ?> - <?= $pd['NAMA_PASIEN'] ?></option>
              <?php endforeach; } ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div>
            <label class="form-label">Jenis Layanan / Tindakan</label>
            <select name="jenis_layanan" class="form-control" required>
              <option value="pemeriksaan">Pemeriksaan Dokter</option>
              <option value="rontgen">Rontgen / Radiologi</option>
              <option value="farmasi">Obat / Farmasi</option>
              <option value="laboratorium">Laboratorium</option>
              <option value="kamar">Kamar / Rawat Inap</option>
              <option value="administrasi">Lain-lain</option>
            </select>
          </div>
          <div><label class="form-label">Jumlah Biaya (Rp)</label><input type="number" name="biaya" class="form-control" placeholder="Biaya layanan" required min="0"></div>
        </div>
        <div class="form-row single">
          <div><label class="form-label">Keterangan Tagihan</label><input name="keterangan_layanan" class="form-control" placeholder="Contoh: Pembelian obat resep dr. Rina" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Buat Tagihan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Proses Pembayaran (Bayar) -->
<div class="modal-overlay" id="modal-proses-bayar">
  <div class="modal" style="width: 480px;">
    <form action="" method="post" id="form-proses-bayar">
      <div class="modal-header">
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h3 style="font-weight: 700; color:#2c3e50;">Proses Pembayaran Kasir</h3>
          <span style="font-size: 11px; color:#888;" id="bayar-subtitle">ID Tagihan: PAY001</span>
        </div>
        <button type="button" class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; border: 1px solid #eaeeef; margin-bottom: 16px;">
          <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size:12px; color:#666;">
            <span>Pasien:</span><strong id="bayar-nama-pasien" style="color:#333;">-</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size:12px; color:#666;">
            <span>No. Daftar:</span><strong id="bayar-no-pendaftaran" style="color:#333;">-</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size:12px; color:#666;">
            <span>Layanan:</span><strong id="bayar-jenis-layanan" style="text-transform: capitalize; color:#4a7dc7;">-</strong>
          </div>
          <div style="display:flex; justify-content:space-between; border-top: 1px dashed #ddd; margin-top: 8px; padding-top: 8px; font-size:15px; font-weight:700; color:#2c3e50;">
            <span>Total Tagihan:</span><span style="color:#e056fd; font-size:18px;" id="bayar-total-biaya">Rp 0</span>
          </div>
        </div>

        <div class="form-row single">
          <div>
            <label class="form-label" style="font-weight:700; color:#2c3e50;">Metode Pembayaran</label>
            <select name="jenis_pembayaran" class="form-control" style="padding:10px; font-size:13px;" required>
              <option value="Tunai">Tunai</option>
              <option value="Debit Mandiri">Debit Mandiri</option>
              <option value="Debit BCA">Debit BCA</option>
              <option value="QRIS">QRIS (Gopay/OVO/ShopeePay)</option>
              <option value="Transfer Bank">Transfer Bank</option>
            </select>
          </div>
        </div>

        <div class="form-row single" style="margin-top: 14px;">
          <div>
            <label class="form-label" style="font-weight:700; color:#2c3e50;">Uang Diterima / Nominal Pembayaran (Rp)</label>
            <input type="number" id="bayar-cash-received" class="form-control" style="font-size:16px; font-weight:700; padding: 10px;" placeholder="0" required oninput="calculateKembalian()">
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top: 14px; background: #fff9e6; border: 1px solid #ffeeba; border-radius: 6px; padding: 10px 14px; font-size: 14px; font-weight: 700; color: #856404;">
          <span>Uang Kembalian:</span>
          <span id="bayar-change-amount">Rp 0</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm modal-close">Batal</button>
        <button type="submit" class="btn btn-success btn-sm" style="background:#28a745; font-weight:700;"><i class="fas fa-check-circle"></i> Lunasi & Cetak Kuitansi</button>
      </div>
    </form>
  </div>
</div>

<script>
let totalBiayaLayanan = 0;

function openBayarTagihanModal(idPembayaran, noPendaftaran, namaPasien, jenisLayanan, biaya) {
  totalBiayaLayanan = biaya;
  
  document.getElementById('bayar-subtitle').innerText = 'ID Tagihan: ' + idPembayaran;
  document.getElementById('bayar-nama-pasien').innerText = namaPasien;
  document.getElementById('bayar-no-pendaftaran').innerText = noPendaftaran;
  document.getElementById('bayar-jenis-layanan').innerText = jenisLayanan.replace('_', ' ');
  document.getElementById('bayar-total-biaya').innerText = 'Rp ' + Number(biaya).toLocaleString('id-ID');
  
  document.getElementById('bayar-cash-received').value = biaya; // Default to exact amount
  document.getElementById('bayar-change-amount').innerText = 'Rp 0';
  
  // Set action URL dynamically
  const form = document.getElementById('form-proses-bayar');
  form.action = '<?= site_url("pembayaran/bayar") ?>/' + idPembayaran;
  
  const modal = document.getElementById('modal-proses-bayar');
  if (modal) {
    modal.classList.add('show');
  }
}

function calculateKembalian() {
  const received = Number(document.getElementById('bayar-cash-received').value) || 0;
  const change = Math.max(0, received - totalBiayaLayanan);
  document.getElementById('bayar-change-amount').innerText = 'Rp ' + change.toLocaleString('id-ID');
}

function loadBillingByPendaftaran() {
  const noReg = document.getElementById('cari-pendaftaran-pembayaran').value;
  if (!noReg) {
    alert('Silakan pilih Pasien / Nomor Pendaftaran terlebih dahulu.');
    return;
  }
  
  fetch('<?= site_url("pembayaran/riwayat") ?>/' + noReg)
    .then(res => res.json())
    .then(result => {
      if (result.status === 'success') {
        // Show summary cards
        document.getElementById('billing-summary-section').style.display = 'grid';
        document.getElementById('bill-sum-total').innerText = 'Rp ' + result.summary.total.toLocaleString('id-ID');
        document.getElementById('bill-sum-lunas').innerText = 'Rp ' + result.summary.lunas.toLocaleString('id-ID');
        document.getElementById('bill-sum-belum').innerText = 'Rp ' + result.summary.belum.toLocaleString('id-ID');
        
        // Update title
        document.getElementById('billing-title').innerHTML = `<i class="fas fa-file-invoice-dollar" style="color:#4a7dc7; margin-right:8px;"></i> Rincian Billing Pendaftaran: ${noReg}`;
        
        // Populate table body
        const tbody = document.getElementById('billing-table-body');
        tbody.innerHTML = '';
        
        if (result.data.length > 0) {
          result.data.forEach(p => {
            const isLunas = p.STATUS === 'lunas';
            const statusClass = isLunas ? 'active' : 'pending';
            
            let actionHtml = '';
            if (!isLunas) {
              actionHtml = `<button class="btn btn-success btn-sm" onclick="openBayarTagihanModal('${p.ID_PEMBAYARAN}', '${p.NO_PENDAFTARAN}', '${result.data[0].NAMA_PASIEN || 'Pasien'}', '${p.JENIS_LAYANAN}', ${p.BIAYA})" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; font-weight: 700;"><i class="fas fa-cash-register"></i> Bayar</button>`;
            } else {
              actionHtml = `<button class="btn btn-outline btn-sm" onclick="cetakKuitansiBilling('${p.ID_PEMBAYARAN}')" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; color: #4a7dc7; border-color: #4a7dc7; font-weight: 700;"><i class="fas fa-print"></i> Cetak Kuitansi</button>`;
            }
            
            const trHtml = `
              <tr id="bill-row-${p.ID_PEMBAYARAN}">
                <td><span class="badge-status info" style="font-family: monospace; font-size: 11px; font-weight: 600;">${p.ID_PEMBAYARAN}</span></td>
                <td><span style="font-family: monospace; font-size:11px;">${p.NO_PENDAFTARAN}</span></td>
                <td><strong>${p.NAMA_PASIEN || 'Umum'}</strong></td>
                <td><span style="text-transform: capitalize; font-weight: 600; color: #4a7dc7;"><i class="fas fa-tag"></i> ${p.JENIS_LAYANAN.replace('_', ' ')}</span></td>
                <td><span style="font-size: 12px; color: #666;">${p.KETERANGAN_LAYANAN || '-'}</span></td>
                <td style="text-align: right; font-weight: 700; color: #2c3e50;">Rp ${Number(p.BIAYA).toLocaleString('id-ID')}</td>
                <td style="text-align: center;">
                  <span class="badge-status ${statusClass}" style="text-transform: uppercase; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                    ${isLunas ? 'Lunas' : 'Belum Bayar'}
                  </span>
                </td>
                <td style="text-align: center;">
                  <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                    ${actionHtml}
                    <a href="<?= site_url('pembayaran/delete/') ?>${p.ID_PEMBAYARAN}" class="btn-icon delete" onclick="return confirm('Hapus tagihan ini?')"><i class="fas fa-trash"></i></a>
                  </div>
                </td>
              </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', trHtml);
          });
        } else {
          tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:30px;color:#999;">Tidak ada data tagihan untuk pendaftaran ini.</td></tr>`;
        }
      }
    })
    .catch(err => {
      alert('Gagal memuat billing pasien.');
      console.error(err);
    });
}

function selectAndLoadBilling(noReg) {
  const selectEl = document.getElementById('cari-pendaftaran-pembayaran');
  if (selectEl) {
    selectEl.value = noReg;
    loadBillingByPendaftaran();
  }
}

function resetBillingView() {
  document.getElementById('cari-pendaftaran-pembayaran').value = '';
  document.getElementById('billing-summary-section').style.display = 'none';
  document.getElementById('billing-title').innerHTML = `<i class="fas fa-file-invoice-dollar" style="color:#4a7dc7; margin-right:8px;"></i> Semua Riwayat Transaksi Billing Pasien`;
  
  // Reload all pembayaran by refreshing the whole dashboard/pembayaran view content
  // In our simplified setup, we can just reload the page or trigger standard list behavior
  location.reload();
}

function cetakKuitansiBilling(idPembayaran) {
  // We can open a print-friendly window or a dedicated route
  window.open('<?= site_url("pembayaran/kuitansi") ?>/' + idPembayaran, '_blank', 'width=800,height=600');
}
</script>
