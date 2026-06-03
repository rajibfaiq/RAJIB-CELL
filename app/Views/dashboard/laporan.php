<!-- Laporan -->
<div class="page-section" id="page-laporan" style="display:none;">
  <div class="toolbar">
    <div style="display:flex; gap: 10px; align-items: center; flex-wrap: wrap;">
      <select id="report-type" class="form-control" style="width: 200px;">
        <option value="">-- Pilih Jenis Laporan --</option>
        <option value="pendaftaran|Laporan Pendaftaran">Laporan Pendaftaran</option>
        <option value="pemeriksaan|Laporan Pemeriksaan Medis">Laporan Pemeriksaan Medis</option>
        <option value="pengobatan|Laporan Pengobatan">Laporan Pengobatan</option>
        <option value="rontgen|Laporan Rontgen">Laporan Rontgen</option>
        <option value="perawatan|Laporan Perawatan">Laporan Perawatan</option>
        <option value="administrasi|Laporan Administrasi">Laporan Administrasi</option>
        <option value="kamar|Laporan Penggunaan Kamar">Laporan Penggunaan Kamar</option>
        <option value="pembayaran|Laporan Pembayaran">Laporan Pembayaran</option>
      </select>
      
      <select id="report-period" class="form-control" style="width: 150px;" onchange="togglePeriodInput()">
        <option value="semua">Semua Waktu</option>
        <option value="harian">Harian</option>
        <option value="mingguan">Mingguan</option>
        <option value="bulanan">Bulanan</option>
        <option value="tahunan">Tahunan</option>
      </select>
      <input type="date" id="filter-date" class="form-control" style="width: 150px; display: none;">
      <input type="week" id="filter-week" class="form-control" style="width: 150px; display: none;">
      <input type="month" id="filter-month" class="form-control" style="width: 150px; display: none;">
      <input type="number" id="filter-year" class="form-control" style="width: 100px; display: none;" placeholder="Tahun" min="2000" max="2100">

      <button class="btn btn-primary btn-sm" onclick="generateReport()"><i class="fas fa-sync"></i> Tampilkan</button>
      <button class="btn btn-success btn-sm" onclick="printReport()" id="btn-print" style="display:none;"><i class="fas fa-print"></i> Cetak</button>
    </div>
  </div>

  <div class="card" id="report-container" style="display:none; margin-top: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; background: #fff; padding: 30px;">
    <!-- Kop Surat (Hospital Letterhead) -->
    <div class="report-header-kop" style="display: flex; align-items: center; border-bottom: 3px double #2c3e50; padding-bottom: 15px; margin-bottom: 20px;">
      <div style="width: 70px; height: 70px; background: #4a7dc7; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0; box-shadow: 0 4px 8px rgba(74, 125, 199, 0.2);">
        <i class="fas fa-hospital-alt" style="font-size: 36px; color: #fff;"></i>
      </div>
      <div style="flex: 1;">
        <h1 style="font-size: 24px; font-weight: 800; color: #2c3e50; margin: 0; letter-spacing: 1px;">RUMAH SAKIT SEJAHTERA</h1>
        <p style="margin: 3px 0 0; color: #4a5568; font-size: 12px; line-height: 1.4;"><i class="fas fa-map-marker-alt" style="color: #4a7dc7; margin-right: 5px;"></i> Jl. Kesehatan Raya No. 123, Blok A, Jakarta Selatan 12340</p>
        <p style="margin: 2px 0 0; color: #718096; font-size: 11px;"><i class="fas fa-phone-alt" style="color: #4a7dc7; margin-right: 5px;"></i> Telp: (021) 123-4567 | Fax: (021) 123-4568 | Email: info@rssejahtera.com | Web: www.rssejahtera.com</p>
      </div>
      <div style="text-align: right; font-family: monospace; font-size: 11px; color: #718096; line-height: 1.5;">
        <div>DOKUMEN UTAMA</div>
        <div style="font-weight: 700; color: #2c3e50; margin-top: 3px;" id="doc-serial-number">DOC-LAP-2026-0001</div>
      </div>
    </div>

    <!-- Title & Metadata -->
    <div style="text-align: center; margin-bottom: 25px;">
      <h2 id="report-title-display" style="font-size: 18px; font-weight: 800; color: #1a202c; text-transform: uppercase; margin: 0 0 8px; letter-spacing: 0.5px; border-bottom: 2px solid #2c3e50; display: inline-block; padding-bottom: 4px;">Judul Laporan</h2>
      <div style="display: flex; justify-content: center; gap: 20px; font-size: 12px; color: #4a5568; margin-top: 6px; flex-wrap: wrap;">
        <div><i class="far fa-calendar-alt" style="color: #4a7dc7; margin-right: 5px;"></i> <span id="report-period-display" style="font-weight: 700;">Periode: Semua Waktu</span></div>
        <div>•</div>
        <div><i class="far fa-clock" style="color: #4a7dc7; margin-right: 5px;"></i> Tanggal Cetak: <span id="print-date" style="font-weight: 700;">-</span></div>
        <div>•</div>
        <div><i class="far fa-user" style="color: #4a7dc7; margin-right: 5px;"></i> Operator: <span style="font-weight: 700;"><?= session()->get('fullname') ?></span></div>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="report-table-container" style="margin-bottom: 30px;">
      <div id="report-table-wrapper" style="overflow-x: auto;">
        <!-- Table will be injected here -->
      </div>
    </div>

    <!-- Dynamic Summary / Statistics Section -->
    <div id="report-summary-block" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 15px; margin-bottom: 30px; display: none;">
      <h4 style="font-size: 13px; font-weight: 700; color: #334155; margin: 0 0 10px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fas fa-calculator" style="color: #4a7dc7; margin-right: 6px;"></i> Ringkasan Laporan</h4>
      <div style="display: flex; gap: 30px; flex-wrap: wrap;" id="report-summary-details">
        <!-- Will be dynamically updated by JS -->
      </div>
    </div>

    <!-- Signature block -->
    <div class="report-signatures" style="display: flex; justify-content: space-between; margin-top: 40px; font-size: 13px;">
      <div style="width: 250px; text-align: center; display: flex; flex-direction: column; justify-content: space-between; height: 130px;">
        <div style="color: #718096;">Disiapkan Oleh,<br><span style="font-weight: 600; color: #2c3e50;">Staf Administrasi Rumah Sakit</span></div>
        <div>
          <div style="font-weight: 700; color: #2c3e50; text-decoration: underline;"><?= session()->get('fullname') ?></div>
          <div style="font-size: 11px; color: #718096; margin-top: 2px;">NIP: <?= session()->get('username') ?: 'ADM-'.session()->get('role') ?></div>
        </div>
      </div>
      
      <div style="width: 250px; text-align: center; display: flex; flex-direction: column; justify-content: space-between; height: 130px;">
        <div style="color: #718096;">Menyetujui,<br><span style="font-weight: 600; color: #2c3e50;">Direktur RS Sejahtera</span></div>
        <div>
          <div style="font-weight: 700; color: #2c3e50; text-decoration: underline;">Dr. dr. H. Ahmad Fauzi, M.M.</div>
          <div style="font-size: 11px; color: #718096; margin-top: 2px;">NIP. 19780512 200501 1 002</div>
        </div>
      </div>
    </div>
  </div>
  
  <div id="report-empty-state" style="text-align:center; padding: 40px; color:#888;">
    <i class="fas fa-chart-bar" style="font-size: 40px; color:#ddd; margin-bottom: 10px;"></i>
    <p>Silakan pilih jenis laporan lalu klik Tampilkan.</p>
  </div>
</div>

<style>
  /* Screen styles for report container preview */
  #report-container {
    transition: all 0.3s ease;
  }
  
  #report-container .data-table {
    width: 100%;
    margin-top: 15px;
    border: 1px solid #e2e8f0;
    border-collapse: collapse;
  }
  
  #report-container .data-table th {
    background-color: #f1f5f9;
    color: #1e293b;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.5px;
    border: 1px solid #cbd5e1;
    padding: 10px 12px;
    text-align: left;
  }
  
  #report-container .data-table td {
    padding: 10px 12px;
    font-size: 13px;
    border: 1px solid #e2e8f0;
    color: #334155;
  }

  #report-container .data-table tr:nth-child(even) {
    background-color: #f8fafc;
  }

  #report-container .data-table tr:hover {
    background-color: #f1f5f9;
  }

  @media print {
    /* Set page margins */
    @page {
      size: A4 portrait;
      margin: 1.5cm;
    }
    
    /* Reset body styles for pure paper layout */
    body {
      background: #fff !important;
      color: #000 !important;
      font-family: "Segoe UI", Arial, sans-serif !important;
      font-size: 11px !important;
    }

    /* Hide sidebar, top header, filters, alerts */
    .sidebar, .topbar, .toolbar, #report-empty-state, .modal-overlay, #page-title, .breadcrumb, .alert {
      display: none !important;
    }

    /* Expand main layout to take full printed sheet */
    .app-layout, .main-content, .page-content {
      margin: 0 !important;
      padding: 0 !important;
      border: none !important;
      background: transparent !important;
      box-shadow: none !important;
      display: block !important;
      width: 100% !important;
    }

    /* Style report container for printable paper representation */
    #report-container {
      display: block !important;
      border: none !important;
      box-shadow: none !important;
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      background: #fff !important;
    }

    /* Professional table borders and colors for monochrome print */
    #report-container .data-table {
      border: 1.5px solid #000 !important;
      width: 100% !important;
      border-collapse: collapse !important;
      margin-top: 15px !important;
    }

    #report-container .data-table th {
      background-color: #e2e8f0 !important;
      color: #000 !important;
      border: 1px solid #000 !important;
      padding: 8px 10px !important;
      font-size: 10px !important;
      font-weight: bold !important;
      text-transform: uppercase !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    #report-container .data-table td {
      border: 1px solid #888 !important;
      padding: 8px 10px !important;
      font-size: 10px !important;
      color: #000 !important;
    }

    #report-container .data-table tr:nth-child(even) {
      background-color: #f8fafc !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Page breaks management */
    .report-signatures {
      page-break-inside: avoid !important;
      margin-top: 50px !important;
    }

    #report-summary-block {
      page-break-inside: avoid !important;
      border: 1.5px dashed #000 !important;
      background-color: #f8fafc !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
  }
</style>

<script>
  function togglePeriodInput() {
    const period = document.getElementById('report-period').value;
    document.getElementById('filter-date').style.display = 'none';
    document.getElementById('filter-week').style.display = 'none';
    document.getElementById('filter-month').style.display = 'none';
    document.getElementById('filter-year').style.display = 'none';
    
    if (period === 'harian') document.getElementById('filter-date').style.display = 'inline-block';
    if (period === 'mingguan') document.getElementById('filter-week').style.display = 'inline-block';
    if (period === 'bulanan') document.getElementById('filter-month').style.display = 'inline-block';
    if (period === 'tahunan') document.getElementById('filter-year').style.display = 'inline-block';
  }

  document.addEventListener("DOMContentLoaded", function() {
      const today = new Date();
      // Adjust timezone offset to get local YYYY-MM-DD properly
      const localDate = new Date(today.getTime() - (today.getTimezoneOffset() * 60000));
      const dateString = localDate.toISOString().split('T')[0];
      
      document.getElementById('filter-date').value = dateString;
      document.getElementById('filter-month').value = dateString.slice(0, 7);
      document.getElementById('filter-year').value = today.getFullYear();
      
      const d = new Date(Date.UTC(today.getFullYear(), today.getMonth(), today.getDate()));
      const dayNum = d.getUTCDay() || 7;
      d.setUTCDate(d.getUTCDate() + 4 - dayNum);
      const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
      const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1)/7);
      document.getElementById('filter-week').value = d.getUTCFullYear() + "-W" + (weekNo < 10 ? "0"+weekNo : weekNo);
  });

  function generateReport() {
    const val = document.getElementById('report-type').value;
    if(!val) {
      alert("Silakan pilih jenis laporan terlebih dahulu.");
      return;
    }
    
    const parts = val.split('|');
    let pageId = 'page-' + parts[0];
    let title = parts[1];
    
    if(parts[0] === 'kamar') {
        pageId = 'page-kamarpage'; // Data kamar page has id page-kamarpage
    } else if(parts[0] === 'pemeriksaan') {
        pageId = 'page-rekammedis';
    } else if(parts[0] === 'perawatan') {
        pageId = 'page-rawatjalan';
    } else if(parts[0] === 'administrasi') {
        pageId = 'page-billing';
    }
    
    let sourcePage = document.getElementById(pageId);
    if(!sourcePage) {
       // if not found, it might be just page-kamar
       const altPage = document.getElementById('page-' + parts[0] + 'page') || document.getElementById('page-' + parts[0]);
       if(!altPage) {
           alert("Data laporan tidak ditemukan di sistem.");
           return;
       }
       sourcePage = altPage;
    }
    
    let clonedTable;
    if (parts[0] === 'perawatan') {
        const tableJalan = sourcePage.querySelector('#table-rawatjalan');
        const tableInap = sourcePage.querySelector('#table-rawatinap');
        
        if (!tableJalan && !tableInap) {
            alert("Tabel data perawatan tidak ditemukan.");
            return;
        }
        
        // Clone tableJalan as the template (they have identical columns)
        clonedTable = (tableJalan || tableInap).cloneNode(true);
        const tbody = clonedTable.querySelector('tbody');
        tbody.innerHTML = '';
        
        let hasRows = false;
        if (tableJalan) {
            const rowsJalan = tableJalan.querySelectorAll('tbody tr');
            rowsJalan.forEach(tr => {
                const tds = tr.querySelectorAll('td');
                if (!(tds.length === 1 && tds[0].colSpan > 1)) {
                    tbody.appendChild(tr.cloneNode(true));
                    hasRows = true;
                }
            });
        }
        
        if (tableInap) {
            const rowsInap = tableInap.querySelectorAll('tbody tr');
            rowsInap.forEach(tr => {
                const tds = tr.querySelectorAll('td');
                if (!(tds.length === 1 && tds[0].colSpan > 1)) {
                    tbody.appendChild(tr.cloneNode(true));
                    hasRows = true;
                }
            });
        }
        
        if (!hasRows) {
            // Append one empty/no-data row
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 7; // We will remove Action column later, so this is temporary
            td.style.textAlign = 'center';
            td.style.padding = '30px';
            td.style.color = '#999';
            td.innerText = 'Belum ada data perawatan';
            tr.appendChild(td);
            tbody.appendChild(tr);
        }
    } else {
        const sourceTable = sourcePage.querySelector('.data-table');
        if(!sourceTable) {
          alert("Tabel data tidak ditemukan untuk laporan ini.");
          return;
        }
        clonedTable = sourceTable.cloneNode(true);
    }
    
    // Find column indexes
    const ths = clonedTable.querySelectorAll('thead th');
    let actionIndex = -1;
    let dateIndex = -1;
    
    ths.forEach((th, index) => {
      const text = th.innerText.toLowerCase();
      if(text.includes('aksi')) {
        actionIndex = index;
        th.remove();
      }
      if(text.includes('tgl') || text.includes('tanggal') || text.includes('waktu') || text.includes('date')) {
        dateIndex = index;
      }
    });
    
    const period = document.getElementById('report-period').value;
    const filterDate = document.getElementById('filter-date').value;
    const filterWeek = document.getElementById('filter-week').value;
    const filterMonth = document.getElementById('filter-month').value;
    const filterYear = document.getElementById('filter-year').value;
    
    let periodText = 'Semua Waktu';
    if(period === 'harian') periodText = 'Harian (' + filterDate + ')';
    if(period === 'mingguan') periodText = 'Mingguan (' + filterWeek + ')';
    if(period === 'bulanan') periodText = 'Bulanan (' + filterMonth + ')';
    if(period === 'tahunan') periodText = 'Tahunan (' + filterYear + ')';
    
    const periodDisplay = document.getElementById('report-period-display');
    periodDisplay.innerText = 'Periode: ' + periodText;
    periodDisplay.style.display = 'block';

    if (period !== 'semua' && dateIndex === -1) {
       alert('Tabel ini tidak memiliki kolom tanggal untuk disaring. Menampilkan semua data.');
       periodDisplay.innerText += ' (Tidak difilter)';
    }

    const rows = clonedTable.querySelectorAll('tbody tr');
    let visibleRowCount = 0;

    rows.forEach(tr => {
      const tds = tr.querySelectorAll('td');
      // skip empty state
      if(tds.length === 1 && tds[0].colSpan > 1) return;
      
      let dateText = "";
      if (dateIndex > -1 && dateIndex < tds.length) {
          dateText = tds[dateIndex].innerText.trim();
      }

      if(actionIndex > -1 && actionIndex < tds.length) {
        tds[actionIndex].remove();
      }
      
      let showRow = true;
      if (period !== 'semua' && dateIndex > -1 && dateText) {
          // Date format might be YYYY-MM-DD HH:mm:ss or YYYY-MM-DD
          // Assume the first part is always YYYY-MM-DD
          const rowDateObj = new Date(dateText.replace(' ', 'T')); // Handle some safari/older parsing
          
          if (period === 'harian') {
              const rowDate = dateText.split(' ')[0];
              if (rowDate !== filterDate) showRow = false;
          } else if (period === 'bulanan') {
              const rowMonth = dateText.substring(0, 7);
              if (rowMonth !== filterMonth) showRow = false;
          } else if (period === 'tahunan') {
              const rowYear = dateText.substring(0, 4);
              if (rowYear !== filterYear) showRow = false;
          } else if (period === 'mingguan') {
              if (rowDateObj instanceof Date && !isNaN(rowDateObj)) {
                  const d = new Date(Date.UTC(rowDateObj.getFullYear(), rowDateObj.getMonth(), rowDateObj.getDate()));
                  const dayNum = d.getUTCDay() || 7;
                  d.setUTCDate(d.getUTCDate() + 4 - dayNum);
                  const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
                  const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1)/7);
                  const rowWeek = d.getUTCFullYear() + "-W" + (weekNo < 10 ? "0"+weekNo : weekNo);
                  
                  if (rowWeek !== filterWeek) showRow = false;
              }
          }
      }
      
      if (!showRow) {
          tr.remove();
      } else {
          visibleRowCount++;
      }
    });

    if (visibleRowCount === 0 && rows.length > 0) {
        const tbody = clonedTable.querySelector('tbody');
        tbody.innerHTML = ''; // clear original empty states if any
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = ths.length - (actionIndex > -1 ? 1 : 0);
        td.style.textAlign = 'center';
        td.style.padding = '30px';
        td.style.color = '#999';
        td.innerText = 'Tidak ada data untuk periode ini';
        tr.appendChild(td);
        tbody.appendChild(tr);
    }
    
    document.getElementById('report-title-display').innerText = title;
    
    const today = new Date();
    document.getElementById('print-date').innerText = today.toLocaleDateString('id-ID') + ' ' + today.toLocaleTimeString('id-ID');
    
    // Generate doc serial number
    const formattedDate = today.getFullYear() + 
                          String(today.getMonth() + 1).padStart(2, '0') + 
                          String(today.getDate()).padStart(2, '0');
    const randomSuffix = Math.floor(1000 + Math.random() * 9000);
    const reportCode = parts[0].substring(0, 3).toUpperCase();
    document.getElementById('doc-serial-number').innerText = `DOC-${reportCode}-${formattedDate}-${randomSuffix}`;
    
    // Calculate statistics
    let totalAmount = 0;
    let isFinancial = false;
    let recordCount = 0;
    
    const finalRows = clonedTable.querySelectorAll('tbody tr');
    finalRows.forEach(tr => {
      const tds = tr.querySelectorAll('td');
      if(tds.length === 1 && tds[0].colSpan > 1) return; // skip empty/no-data row
      
      recordCount++;
      tds.forEach(td => {
        const cellText = td.innerText.trim();
        // Check if cell starts with "Rp" or looks like currency
        if (cellText.includes('Rp')) {
          isFinancial = true;
          // Extract digits from the text
          const cleanValue = cellText.replace(/[^\d]/g, '');
          const numValue = parseFloat(cleanValue) || 0;
          totalAmount += numValue;
        }
      });
    });

    const summaryBlock = document.getElementById('report-summary-block');
    const summaryDetails = document.getElementById('report-summary-details');

    if (recordCount > 0) {
      if (isFinancial) {
        summaryDetails.innerHTML = `
          <div style="flex: 1; min-width: 200px; border-right: 1px solid #cbd5e1; padding-right: 20px;">
            <span style="font-size: 11px; color: #64748b; display: block; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Jumlah Item Transaksi</span>
            <strong style="font-size: 18px; color: #1e293b; display: block; margin-top: 4px;">${recordCount} Transaksi</strong>
          </div>
          <div style="flex: 1; min-width: 200px;">
            <span style="font-size: 11px; color: #64748b; display: block; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Total Nilai Keuangan</span>
            <strong style="font-size: 18px; color: #2e7d32; display: block; margin-top: 4px;">Rp ${totalAmount.toLocaleString('id-ID')}</strong>
          </div>
        `;
      } else {
        summaryDetails.innerHTML = `
          <div style="flex: 1; min-width: 200px;">
            <span style="font-size: 11px; color: #64748b; display: block; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Total Volume Data</span>
            <strong style="font-size: 18px; color: #1e293b; display: block; margin-top: 4px;">${recordCount} Data Terdaftar</strong>
          </div>
        `;
      }
      summaryBlock.style.display = 'block';
    } else {
      summaryBlock.style.display = 'none';
    }
    
    document.getElementById('report-table-wrapper').innerHTML = '';
    document.getElementById('report-table-wrapper').appendChild(clonedTable);
    
    document.getElementById('report-empty-state').style.display = 'none';
    document.getElementById('report-container').style.display = 'block';
    document.getElementById('btn-print').style.display = 'inline-flex';
  }
  
  function printReport() {
    window.print();
  }
</script>
