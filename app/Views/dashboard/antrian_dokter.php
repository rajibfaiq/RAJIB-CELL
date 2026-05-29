<!-- Antrian Dokter -->
<div class="page-section" id="page-antrian" style="display:none;">
  
  <?php if(session()->get('role') !== 'dokter' && !session()->get('ID_DOKTER')): ?>
    <div style="background:#fff3cd;color:#856404;padding:20px;border-radius:6px;border:1px solid #ffeeba;text-align:center;">
      <i class="fas fa-exclamation-triangle" style="font-size:32px;margin-bottom:10px;"></i>
      <h3>Halaman Khusus Dokter</h3>
      <p>Halaman ini hanya dapat diakses oleh pengguna yang masuk dengan akun Dokter dan sudah terhubung dengan ID Dokter.</p>
    </div>
  <?php else: ?>
    
    <!-- Queue Summary Widgets -->
    <?php
      $totalToday = count($antrianDokter);
      $menunggu = count(array_filter($antrianDokter, fn($a) => $a['STATUS_ANTRIAN'] === 'menunggu'));
      $diperiksa = count(array_filter($antrianDokter, fn($a) => $a['STATUS_ANTRIAN'] === 'sedang_diperiksa'));
      $selesai = count(array_filter($antrianDokter, fn($a) => $a['STATUS_ANTRIAN'] === 'selesai'));
    ?>
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
      <div class="stat-card" style="border-left: 5px solid #4a7dc7; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div class="stat-info">
            <h3 style="font-size: 28px; font-weight: 700; color: #2c3e50; margin: 0;" id="stat-total"><?= $totalToday ?></h3>
            <p style="font-size: 12px; color: #7f8c8d; margin: 4px 0 0;">Total Antrian Hari Ini</p>
          </div>
          <div class="stat-icon blue" style="width: 46px; height: 46px; background: #e8f0fe; color: #4a7dc7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fas fa-users"></i>
          </div>
        </div>
      </div>

      <div class="stat-card" style="border-left: 5px solid #ffc107; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div class="stat-info">
            <h3 style="font-size: 28px; font-weight: 700; color: #d39e00; margin: 0;" id="stat-menunggu"><?= $menunggu ?></h3>
            <p style="font-size: 12px; color: #7f8c8d; margin: 4px 0 0;">Menunggu Diperiksa</p>
          </div>
          <div class="stat-icon amber" style="width: 46px; height: 46px; background: #fff8e1; color: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fas fa-hourglass-half"></i>
          </div>
        </div>
      </div>

      <div class="stat-card" style="border-left: 5px solid #17a2b8; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div class="stat-info">
            <h3 style="font-size: 28px; font-weight: 700; color: #17a2b8; margin: 0;" id="stat-diperiksa"><?= $diperiksa ?></h3>
            <p style="font-size: 12px; color: #7f8c8d; margin: 4px 0 0;">Sedang Diperiksa</p>
          </div>
          <div class="stat-icon teal" style="width: 46px; height: 46px; background: #e0f7fa; color: #17a2b8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fas fa-stethoscope"></i>
          </div>
        </div>
      </div>

      <div class="stat-card" style="border-left: 5px solid #28a745; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div class="stat-info">
            <h3 style="font-size: 28px; font-weight: 700; color: #28a745; margin: 0;" id="stat-selesai"><?= $selesai ?></h3>
            <p style="font-size: 12px; color: #7f8c8d; margin: 4px 0 0;">Selesai Diperiksa</p>
          </div>
          <div class="stat-icon green" style="width: 46px; height: 46px; background: #e6f7ed; color: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fas fa-check-double"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Doctor Queue Table -->
    <div class="card" style="background:#fff; border-radius:8px; border: 1px solid #eaeeef; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
      <div class="card-header" style="background: #fafbfc; border-bottom:1px solid #eee; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <h3 style="font-size: 15px; font-weight: 700; color: #2c3e50; margin: 0;"><i class="fas fa-clipboard-list" style="color: #4a7dc7; margin-right: 8px;"></i> Daftar Antrian Pasien Anda Hari Ini</h3>
          <span style="font-size: 11px; color:#888;">Tanggal: <?= date('d F Y') ?></span>
        </div>
        <button class="btn btn-outline btn-sm" onclick="refreshDoctorQueue()" style="border-radius:20px; padding: 4px 15px;"><i class="fas fa-sync-alt"></i> Refresh</button>
      </div>
      <div class="card-body no-pad">
        <table class="data-table" id="doctor-queue-table">
          <thead>
            <tr>
              <th style="width: 100px; text-align: center;">No. Antrian</th>
              <th>Biodata Pasien</th>
              <th>Jenis Kelamin</th>
              <th>Umur</th>
              <th>Jam Terdaftar</th>
              <th style="width: 150px;">Status Antrian</th>
              <th style="width: 250px; text-align: center;">Tindakan & Alur</th>
            </tr>
          </thead>
          <tbody id="doctor-queue-body">
            <?php if(!empty($antrianDokter)): foreach($antrianDokter as $ad): ?>
              <?php
                // Calculate age
                $birthDate = new DateTime($ad['TGL_LAHIR']);
                $today = new DateTime();
                $age = $today->diff($birthDate)->y;
                
                $statusClass = 'pending';
                if ($ad['STATUS_ANTRIAN'] === 'sedang_diperiksa') $statusClass = 'info';
                if ($ad['STATUS_ANTRIAN'] === 'selesai') $statusClass = 'active';
                if ($ad['STATUS_ANTRIAN'] === 'batal') $statusClass = 'inactive';
              ?>
              <tr id="queue-row-<?= $ad['NO_PENDAFTARAN'] ?>">
                <td style="text-align: center;">
                  <div style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; background:#e8f0fe; color:#4a7dc7; font-weight:800; font-size:16px; box-shadow: 0 2px 4px rgba(74, 125, 199, 0.1);">
                    <?= $ad['NO_ANTRIAN'] ?>
                  </div>
                </td>
                <td>
                  <strong><?= $ad['NAMA_PASIEN'] ?></strong>
                  <br><span style="font-family: monospace; font-size: 11px; color: #7f8c8d;"><?= $ad['ID_PASIEN'] ?></span>
                  <br><span style="font-size: 11px; color: #aaa;">No. Daftar: <?= $ad['NO_PENDAFTARAN'] ?></span>
                </td>
                <td><?= $ad['JENIS_KELAMIN'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                <td><?= $age ?> Tahun</td>
                <td><?= date('H:i', strtotime($ad['JAM_PENDAFTARAN'])) ?></td>
                <td>
                  <span class="badge-status <?= $statusClass ?>" id="status-badge-<?= $ad['NO_PENDAFTARAN'] ?>" style="text-transform: capitalize; font-weight: 600;">
                    <?= str_replace('_', ' ', $ad['STATUS_ANTRIAN']) ?>
                  </span>
                </td>
                <td style="text-align: center;">
                  <div style="display: flex; gap: 8px; justify-content: center;" id="actions-box-<?= $ad['NO_PENDAFTARAN'] ?>">
                    <?php if ($ad['STATUS_ANTRIAN'] === 'menunggu'): ?>
                      <button class="btn btn-primary btn-sm" onclick="changeQueueStatus('<?= $ad['NO_PENDAFTARAN'] ?>', 'sedang_diperiksa')" style="border-radius: 4px; background: #4a7dc7; padding: 6px 12px; font-weight: 700;"><i class="fas fa-stethoscope"></i> Panggil & Periksa</button>
                      <button class="btn btn-outline btn-sm" onclick="changeQueueStatus('<?= $ad['NO_PENDAFTARAN'] ?>', 'batal')" style="border-radius: 4px; padding: 6px 12px; color: #dc3545; border-color: #dc3545;"><i class="fas fa-times"></i> Batal</button>
                    <?php elseif ($ad['STATUS_ANTRIAN'] === 'sedang_diperiksa'): ?>
                      <button class="btn btn-success btn-sm" onclick="changeQueueStatus('<?= $ad['NO_PENDAFTARAN'] ?>', 'selesai')" style="border-radius: 4px; padding: 6px 12px; font-weight: 700;"><i class="fas fa-check-circle"></i> Selesai Periksa</button>
                      <button class="btn btn-outline btn-sm" onclick="changeQueueStatus('<?= $ad['NO_PENDAFTARAN'] ?>', 'menunggu')" style="border-radius: 4px; padding: 6px 12px;"><i class="fas fa-undo"></i> Tunda</button>
                    <?php else: ?>
                      <span style="color: #bbb; font-size: 12px;"><i class="fas fa-check-double"></i> Alur Selesai</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="7" style="text-align:center;padding:40px;color:#999;"><i class="fas fa-calendar-times" style="font-size: 32px; color: #ccc; margin-bottom: 10px; display: block;"></i>Belum ada antrian pasien terdaftar untuk Anda hari ini</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <script>
    function changeQueueStatus(noPendaftaran, status) {
      const formData = new FormData();
      formData.append('no_pendaftaran', noPendaftaran);
      formData.append('status', status);
      
      fetch('<?= site_url("pendaftaran/updateStatus") ?>', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(result => {
        if (result.status === 'success') {
          // Success, let's refresh the whole doctor queue to ensure UI integrity
          refreshDoctorQueue();
        } else {
          alert('Gagal memperbarui status: ' + result.message);
        }
      })
      .catch(err => {
        alert('Gagal mengirim pembaruan status.');
        console.error(err);
      });
    }
    
    function refreshDoctorQueue() {
      fetch('<?= site_url("pendaftaran/antrianDokter") ?>')
        .then(res => res.json())
        .then(result => {
          if (result.status === 'success') {
            // Update stats widgets
            document.getElementById('stat-total').innerText = result.summary.total;
            document.getElementById('stat-menunggu').innerText = result.summary.menunggu;
            document.getElementById('stat-diperiksa').innerText = result.data.filter(d => d.STATUS_ANTRIAN === 'sedang_diperiksa').length;
            document.getElementById('stat-selesai').innerText = result.summary.selesai;
            
            // Re-render table body
            const tbody = document.getElementById('doctor-queue-body');
            tbody.innerHTML = '';
            
            if (result.data.length > 0) {
              result.data.forEach(ad => {
                // Calculate age
                const birthDate = new Date(ad.TGL_LAHIR);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                  age--;
                }
                
                let statusClass = 'pending';
                if (ad.STATUS_ANTRIAN === 'sedang_diperiksa') statusClass = 'info';
                if (ad.STATUS_ANTRIAN === 'selesai') statusClass = 'active';
                if (ad.STATUS_ANTRIAN === 'batal') statusClass = 'inactive';
                
                let actionHtml = '';
                if (ad.STATUS_ANTRIAN === 'menunggu') {
                  actionHtml = `
                    <button class="btn btn-primary btn-sm" onclick="changeQueueStatus('${ad.NO_PENDAFTARAN}', 'sedang_diperiksa')" style="border-radius: 4px; background: #4a7dc7; padding: 6px 12px; font-weight: 700;"><i class="fas fa-stethoscope"></i> Panggil & Periksa</button>
                    <button class="btn btn-outline btn-sm" onclick="changeQueueStatus('${ad.NO_PENDAFTARAN}', 'batal')" style="border-radius: 4px; padding: 6px 12px; color: #dc3545; border-color: #dc3545;"><i class="fas fa-times"></i> Batal</button>
                  `;
                } else if (ad.STATUS_ANTRIAN === 'sedang_diperiksa') {
                  actionHtml = `
                    <button class="btn btn-success btn-sm" onclick="changeQueueStatus('${ad.NO_PENDAFTARAN}', 'selesai')" style="border-radius: 4px; padding: 6px 12px; font-weight: 700;"><i class="fas fa-check-circle"></i> Selesai Periksa</button>
                    <button class="btn btn-outline btn-sm" onclick="changeQueueStatus('${ad.NO_PENDAFTARAN}', 'menunggu')" style="border-radius: 4px; padding: 6px 12px;"><i class="fas fa-undo"></i> Tunda</button>
                  `;
                } else {
                  actionHtml = `<span style="color: #bbb; font-size: 12px;"><i class="fas fa-check-double"></i> Alur Selesai</span>`;
                }
                
                const trHtml = `
                  <tr id="queue-row-${ad.NO_PENDAFTARAN}">
                    <td style="text-align: center;">
                      <div style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; background:#e8f0fe; color:#4a7dc7; font-weight:800; font-size:16px; box-shadow: 0 2px 4px rgba(74, 125, 199, 0.1);">
                        ${ad.NO_ANTRIAN}
                      </div>
                    </td>
                    <td>
                      <strong>${ad.NAMA_PASIEN}</strong>
                      <br><span style="font-family: monospace; font-size: 11px; color: #7f8c8d;">${ad.ID_PASIEN}</span>
                      <br><span style="font-size: 11px; color: #aaa;">No. Daftar: ${ad.NO_PENDAFTARAN}</span>
                    </td>
                    <td>${ad.JENIS_KELAMIN === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                    <td>${age} Tahun</td>
                    <td>${ad.JAM_PENDAFTARAN.substring(11, 16)}</td>
                    <td>
                      <span class="badge-status ${statusClass}" style="text-transform: capitalize; font-weight: 600;">
                        ${ad.STATUS_ANTRIAN.replace('_', ' ')}
                      </span>
                    </td>
                    <td style="text-align: center;">
                      <div style="display: flex; gap: 8px; justify-content: center;">
                        ${actionHtml}
                      </div>
                    </td>
                  </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', trHtml);
              });
            } else {
              tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:#999;"><i class="fas fa-calendar-times" style="font-size: 32px; color: #ccc; margin-bottom: 10px; display: block;"></i>Belum ada antrian pasien terdaftar untuk Anda hari ini</td></tr>`;
            }
          }
        })
        .catch(err => {
          console.error('Gagal mengambil data antrian:', err);
        });
    }
    
    // Auto-refresh every 30 seconds if page is active
    setInterval(() => {
      const activeItem = document.querySelector('.nav-item.active');
      if (activeItem && activeItem.dataset.page === 'antrian') {
        refreshDoctorQueue();
      }
    }, 30000);
    </script>
    
  <?php endif; ?>
</div>
