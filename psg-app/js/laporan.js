let fotoFiles = [];
let videoFile = null;
let editingId = null;

document.addEventListener('DOMContentLoaded', () => {
  if (!PSG.requireAuth()) return;
  PSG.init();

  const page = window.location.pathname.split('/').pop().replace('.html', '');

  if (page === 'laporan') {
    initFormLaporan();
  } else if (page === 'riwayat') {
    initRiwayat();
  } else if (page === 'detail-laporan') {
    initDetailLaporan();
  }
});

function initFormLaporan() {
  const user = PSG.currentUser;
  if (!user) return;

  document.getElementById('nama').value = user.nama || '';
  document.getElementById('nisn').value = user.nis || '';
  document.getElementById('kelas').value = user.kelas || '';
  document.getElementById('jurusan').value = user.jurusan || '';
  document.getElementById('sekolah').value = user.sekolah || '';
  document.getElementById('tempatPSG').value = user.tempat_psg || '';
  document.getElementById('pembimbing').value = user.pembimbing || '';
  document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];

  document.getElementById('jamMasuk').value = '07:30';
  document.getElementById('jamPulang').value = '15:30';

  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('edit')) {
    loadEditData(urlParams.get('edit'));
  }

  initUploadFoto();
  initUploadVideo();
  initFormSubmit();
  initAutoJam();
}

function initAutoJam() {
  const now = new Date();
  const jam = now.getHours().toString().padStart(2, '0');
  const menit = now.getMinutes().toString().padStart(2, '0');
  if (!document.getElementById('jamMasuk').value) {
    document.getElementById('jamMasuk').value = `${jam}:${menit}`;
  }
}

function initUploadFoto() {
  const uploadArea = document.getElementById('fotoUploadArea');
  const input = document.getElementById('fotoInput');
  const preview = document.getElementById('fotoPreview');

  if (!uploadArea || !input) return;

  uploadArea.addEventListener('click', () => input.click());
  uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
  });
  uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
  });
  uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
      handleFotoFiles(e.dataTransfer.files);
    }
  });

  input.addEventListener('change', (e) => {
    if (e.target.files.length) {
      handleFotoFiles(e.target.files);
    }
  });
}

function handleFotoFiles(files) {
  for (const file of files) {
    if (!file.type.startsWith('image/')) continue;
    if (fotoFiles.length >= 10) {
      PSG.showToast('Maksimal 10 foto', 'warning');
      break;
    }
    fotoFiles.push(file);
    previewFoto(file);
  }
}

function previewFoto(file) {
  const container = document.getElementById('fotoPreview');
  if (!container) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    const item = document.createElement('div');
    item.className = 'preview-item';
    item.innerHTML = `
      <img src="${e.target.result}" alt="Preview">
      <button class="preview-remove" data-file="${file.name}">&times;</button>
    `;
    container.appendChild(item);

    item.querySelector('.preview-remove').addEventListener('click', () => {
      fotoFiles = fotoFiles.filter(f => f.name !== file.name);
      item.remove();
    });
  };
  reader.readAsDataURL(file);
}

function initUploadVideo() {
  const uploadArea = document.getElementById('videoUploadArea');
  const input = document.getElementById('videoInput');
  const preview = document.getElementById('videoPreview');

  if (!uploadArea || !input) return;

  uploadArea.addEventListener('click', () => input.click());
  input.addEventListener('change', (e) => {
    if (e.target.files.length) {
      const file = e.target.files[0];
      if (!file.type.startsWith('video/')) {
        PSG.showToast('File harus video', 'error');
        return;
      }
      if (file.size > 50 * 1024 * 1024) {
        PSG.showToast('Video maksimal 50MB', 'error');
        return;
      }
      videoFile = file;
      previewVideo(file);
    }
  });
}

function previewVideo(file) {
  const container = document.getElementById('videoPreview');
  if (!container) return;

  const url = URL.createObjectURL(file);
  container.innerHTML = `
    <div class="preview-item" style="aspect-ratio:16/9">
      <video src="${url}" controls></video>
      <button class="preview-remove" id="removeVideo">&times;</button>
    </div>
  `;

  document.getElementById('removeVideo')?.addEventListener('click', () => {
    videoFile = null;
    container.innerHTML = '';
    document.getElementById('videoInput').value = '';
  });
}

function initFormSubmit() {
  const form = document.getElementById('formLaporan');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!validateForm()) return;

    const user = PSG.currentUser;
    const data = {
      id: editingId || PSG.generateId(),
      nis: user.nis,
      nama: document.getElementById('nama').value,
      nisn: document.getElementById('nisn').value,
      kelas: document.getElementById('kelas').value,
      jurusan: document.getElementById('jurusan').value,
      sekolah: document.getElementById('sekolah').value,
      tempat_psg: document.getElementById('tempatPSG').value,
      pembimbing: document.getElementById('pembimbing').value,
      tanggal: document.getElementById('tanggal').value,
      jam_masuk: document.getElementById('jamMasuk').value,
      jam_pulang: document.getElementById('jamPulang').value,
      aktivitas: document.getElementById('aktivitas').value,
      hasil: document.getElementById('hasil').value,
      kendala: document.getElementById('kendala').value,
      solusi: document.getElementById('solusi').value,
      catatan: document.getElementById('catatan').value,
      foto: [],
      video: null,
      status: 'pending',
      createdAt: editingId ? undefined : new Date().toISOString(),
      updatedAt: new Date().toISOString()
    };

    for (const file of fotoFiles) {
      data.foto.push(await fileToBase64(file));
    }

    if (videoFile) {
      data.video = await fileToBase64(videoFile);
    }

    const laporan = PSG.db.getLaporan();
    if (editingId) {
      const idx = laporan.findIndex(l => l.id === editingId);
      if (idx !== -1) {
        data.createdAt = laporan[idx].createdAt;
        laporan[idx] = data;
      }
    } else {
      laporan.push(data);
    }

    PSG.db.setLaporan(laporan);
    addNotification('Laporan baru ditambahkan', user.nama, 'success');

    await Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: editingId ? 'Laporan berhasil diperbarui' : 'Laporan berhasil disimpan',
      confirmButtonColor: '#4F46E5',
      background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1E293B' : '#fff',
      color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#F1F5F9' : '#1E293B',
    });

    window.location.href = 'riwayat.html';
  });
}

function validateForm() {
  let valid = true;
  const required = [
    'nama', 'nisn', 'kelas', 'jurusan', 'sekolah',
    'tempatPSG', 'pembimbing', 'tanggal', 'jamMasuk',
    'jamPulang', 'aktivitas', 'hasil'
  ];

  required.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    if (!el.value.trim()) {
      el.classList.add('error');
      valid = false;
    } else {
      el.classList.remove('error');
    }
  });

  if (!valid) {
    PSG.showToast('Harap lengkapi semua field yang wajib diisi', 'error');
  }

  return valid;
}

function loadEditData(id) {
  const laporan = PSG.db.getLaporan();
  const data = laporan.find(l => l.id === id);
  if (!data) return;

  editingId = id;

  document.getElementById('nama').value = data.nama;
  document.getElementById('nisn').value = data.nisn;
  document.getElementById('kelas').value = data.kelas;
  document.getElementById('jurusan').value = data.jurusan;
  document.getElementById('sekolah').value = data.sekolah;
  document.getElementById('tempatPSG').value = data.tempat_psg;
  document.getElementById('pembimbing').value = data.pembimbing;
  document.getElementById('tanggal').value = data.tanggal;
  document.getElementById('jamMasuk').value = data.jam_masuk;
  document.getElementById('jamPulang').value = data.jam_pulang;
  document.getElementById('aktivitas').value = data.aktivitas;
  document.getElementById('hasil').value = data.hasil;
  document.getElementById('kendala').value = data.kendala || '';
  document.getElementById('solusi').value = data.solusi || '';
  document.getElementById('catatan').value = data.catatan || '';

  const submitBtn = document.querySelector('#formLaporan button[type="submit"]');
  if (submitBtn) submitBtn.textContent = 'Perbarui Laporan';
}

function fileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

function addNotification(message, sender, type = 'info') {
  const notifs = PSG.db.getNotifications();
  notifs.unshift({
    id: PSG.generateId(),
    message,
    sender,
    type,
    read: false,
    createdAt: new Date().toISOString()
  });
  PSG.db.setNotifications(notifs);
}

function initRiwayat() {
  renderRiwayatStats();
  renderRiwayatTable();
  initRiwayatFilters();
  initExportButtons();
}

function renderRiwayatStats() {
  const container = document.querySelector('.riwayat-stats');
  if (!container) return;

  const laporan = PSG.db.getLaporan();
  const user = PSG.currentUser;
  const userLaporan = user.role === 'siswa' ? laporan.filter(l => l.nis === user.nis) : laporan;

  const verified = userLaporan.filter(l => l.status === 'verified').length;
  const pending = userLaporan.filter(l => l.status === 'pending').length;
  const rejected = userLaporan.filter(l => l.status === 'rejected').length;
  const draft = userLaporan.filter(l => l.status === 'draft' || !l.status).length;

  container.innerHTML = `
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon icon-success">\u2705</div>
      <div class="stat-info">
        <div class="stat-label">Terverifikasi</div>
        <div class="stat-value">${verified}</div>
      </div>
    </div>
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon icon-warning">\u23F3</div>
      <div class="stat-info">
        <div class="stat-label">Menunggu</div>
        <div class="stat-value">${pending}</div>
      </div>
    </div>
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon icon-danger">\u274C</div>
      <div class="stat-info">
        <div class="stat-label">Ditolak</div>
        <div class="stat-value">${rejected}</div>
      </div>
    </div>
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon icon-info">\uD83D\uDCC4</div>
      <div class="stat-info">
        <div class="stat-label">Total</div>
        <div class="stat-value">${userLaporan.length}</div>
      </div>
    </div>
  `;
}

function renderRiwayatTable(page = 1) {
  const container = document.getElementById('riwayatTableBody');
  if (!container) return;

  const laporan = PSG.db.getLaporan();
  const user = PSG.currentUser;
  let data = user.role === 'siswa' ? laporan.filter(l => l.nis === user.nis) : [...laporan];

  const search = document.getElementById('searchLaporan')?.value?.toLowerCase() || '';
  const filterBulan = document.getElementById('filterBulan')?.value || '';
  const filterTahun = document.getElementById('filterTahun')?.value || '';

  if (search) {
    data = data.filter(l =>
      l.aktivitas?.toLowerCase().includes(search) ||
      l.tempat_psg?.toLowerCase().includes(search) ||
      l.nama?.toLowerCase().includes(search)
    );
  }
  if (filterBulan) {
    data = data.filter(l => new Date(l.tanggal).getMonth() === parseInt(filterBulan));
  }
  if (filterTahun) {
    data = data.filter(l => new Date(l.tanggal).getFullYear() === parseInt(filterTahun));
  }

  data.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

  const perPage = 10;
  const totalPages = Math.ceil(data.length / perPage);
  const start = (page - 1) * perPage;
  const pageData = data.slice(start, start + perPage);

  if (!pageData.length) {
    container.innerHTML = `
      <tr><td colspan="6" class="empty-state" style="padding:2rem">
        <div class="empty-icon">\uD83D\uDCE6</div>
        <h3>Belum Ada Laporan</h3>
        <p>Belum ada data laporan yang tersedia</p>
      </td></tr>
    `;
    document.querySelector('.pagination').innerHTML = '';
    return;
  }

  container.innerHTML = pageData.map(l => `
    <tr>
      <td>${PSG.formatDate(l.tanggal)}</td>
      <td>${l.tempat_psg}</td>
      <td>${l.aktivitas?.substring(0, 40) || '-'}${l.aktivitas?.length > 40 ? '...' : ''}</td>
      <td>${PSG.getStatusBadge(l.status)}</td>
      <td>${l.foto?.length ? '\uD83D\uDCF7 ' + l.foto.length : '-'}</td>
      <td>
        <div class="table-actions">
          <button class="btn btn-sm btn-ghost" onclick="window.location.href='detail-laporan.html?id=${l.id}'" title="Detail">\uD83D\uDD0D</button>
          ${user.role === 'siswa' ? `<button class="btn btn-sm btn-ghost" onclick="editLaporan('${l.id}')" title="Edit">\u270F\uFE0F</button>` : ''}
          ${user.role === 'pembimbing' ? `<button class="btn btn-sm btn-success" onclick="verifikasiLaporan('${l.id}','verified')">\u2705</button><button class="btn btn-sm btn-danger" onclick="verifikasiLaporan('${l.id}','rejected')">\u274C</button>` : ''}
          <button class="btn btn-sm btn-ghost" onclick="hapusLaporan('${l.id}')" title="Hapus" style="color:var(--danger)">\uD83D\uDDD1\uFE0F</button>
        </div>
      </td>
    </tr>
  `).join('');

  const pagination = document.querySelector('.pagination');
  if (pagination) {
    pagination.innerHTML = `
      <button class="page-btn" onclick="renderRiwayatTable(${page - 1})" ${page <= 1 ? 'disabled' : ''}>&laquo;</button>
      ${Array.from({length: totalPages}, (_, i) => i + 1).map(p =>
        `<button class="page-btn ${p === page ? 'active' : ''}" onclick="renderRiwayatTable(${p})">${p}</button>`
      ).join('')}
      <button class="page-btn" onclick="renderRiwayatTable(${page + 1})" ${page >= totalPages ? 'disabled' : ''}>&raquo;</button>
    `;
  }
}

function initRiwayatFilters() {
  const search = document.getElementById('searchLaporan');
  const bulan = document.getElementById('filterBulan');
  const tahun = document.getElementById('filterTahun');

  [search, bulan, tahun].forEach(el => {
    el?.addEventListener('change', () => renderRiwayatTable());
    el?.addEventListener('keyup', () => renderRiwayatTable());
  });

  if (tahun) {
    const year = new Date().getFullYear();
    tahun.innerHTML = '<option value="">Semua Tahun</option>' +
      Array.from({length: 5}, (_, i) => {
        const y = year - i;
        return `<option value="${y}">${y}</option>`;
      }).join('');
  }
}

function editLaporan(id) {
  window.location.href = `laporan.html?edit=${id}`;
}

async function hapusLaporan(id) {
  const result = await showConfirm('Yakin ingin menghapus laporan ini?');
  if (!result.isConfirmed) return;

  let laporan = PSG.db.getLaporan();
  laporan = laporan.filter(l => l.id !== id);
  PSG.db.setLaporan(laporan);
  PSG.showToast('Laporan berhasil dihapus', 'success');
  renderRiwayatTable();
}

async function verifikasiLaporan(id, status) {
  let laporan = PSG.db.getLaporan();
  const idx = laporan.findIndex(l => l.id === id);
  if (idx === -1) return;

  if (status === 'rejected') {
    const { value: catatan } = await Swal.fire({
      title: 'Verifikasi Ditolak',
      input: 'textarea',
      inputLabel: 'Catatan revisi',
      inputPlaceholder: 'Berikan catatan untuk perbaikan...',
      showCancelButton: true,
      confirmButtonColor: '#EF4444',
      cancelButtonColor: '#64748B',
      confirmButtonText: 'Tolak',
      cancelButtonText: 'Batal',
      background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1E293B' : '#fff',
      color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#F1F5F9' : '#1E293B',
    });
    if (!catatan) return;
    laporan[idx].catatan_revisi = catatan;
  }

  laporan[idx].status = status;
  laporan[idx].verifiedBy = PSG.currentUser.nama;
  laporan[idx].verifiedAt = new Date().toISOString();
  PSG.db.setLaporan(laporan);

  const msg = status === 'verified' ? 'Laporan diverifikasi' : 'Laporan ditolak';
  PSG.showToast(msg, status === 'verified' ? 'success' : 'warning');
  renderRiwayatTable();
}

function initDetailLaporan() {
  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get('id');
  if (!id) {
    document.querySelector('.content').innerHTML = '<div class="empty-state"><h3>Laporan tidak ditemukan</h3></div>';
    return;
  }

  const laporan = PSG.db.getLaporan();
  const data = laporan.find(l => l.id === id);
  if (!data) {
    document.querySelector('.content').innerHTML = '<div class="empty-state"><h3>Laporan tidak ditemukan</h3></div>';
    return;
  }

  renderDetail(data);
}

function renderDetail(data) {
  const container = document.getElementById('detailContainer');
  if (!container) return;

  const fotoHtml = data.foto?.length
    ? data.foto.map(f => `
      <div class="detail-photo-item" onclick="PSG.openLightbox('${f}')">
        <img src="${f}" alt="Dokumentasi">
      </div>
    `).join('')
    : '<p style="color:var(--text-muted)">Tidak ada foto</p>';

  const videoHtml = data.video
    ? `<video controls style="width:100%;max-height:400px;border-radius:var(--radius-md)">
        <source src="${data.video}" type="video/mp4">
      </video>`
    : '<p style="color:var(--text-muted)">Tidak ada video</p>';

  const statusClass = data.status === 'verified' ? 'verified' : data.status === 'rejected' ? 'rejected' : 'pending';
  const statusText = PSG.getStatusText(data.status);

  container.innerHTML = `
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem">
      <div>
        <div class="glass-card card">
          <div class="card-header">
            <h3>Data Laporan</h3>
            <span class="verification-status ${statusClass}">${statusText}</span>
          </div>
          <div class="detail-grid">
            <div class="detail-section">
              <div class="detail-label">Tanggal</div>
              <div class="detail-value">${PSG.formatDate(data.tanggal)}</div>
            </div>
            <div class="detail-section">
              <div class="detail-label">Jam</div>
              <div class="detail-value">${data.jam_masuk} - ${data.jam_pulang}</div>
            </div>
            <div class="detail-section">
              <div class="detail-label">Tempat PSG</div>
              <div class="detail-value">${data.tempat_psg}</div>
            </div>
            <div class="detail-section">
              <div class="detail-label">Pembimbing</div>
              <div class="detail-value">${data.pembimbing}</div>
            </div>
          </div>
        </div>

        <div class="glass-card card" style="margin-top:1.25rem">
          <h3 style="margin-bottom:1rem">Aktivitas</h3>
          <div class="detail-section">
            <div class="detail-label">Aktivitas Hari Ini</div>
            <div class="detail-value" style="white-space:pre-wrap">${data.aktivitas}</div>
          </div>
          <div class="detail-section" style="margin-top:1rem">
            <div class="detail-label">Hasil Pekerjaan</div>
            <div class="detail-value" style="white-space:pre-wrap">${data.hasil}</div>
          </div>
          ${data.kendala ? `
          <div class="detail-section" style="margin-top:1rem">
            <div class="detail-label">Kendala</div>
            <div class="detail-value" style="white-space:pre-wrap">${data.kendala}</div>
          </div>` : ''}
          ${data.solusi ? `
          <div class="detail-section" style="margin-top:1rem">
            <div class="detail-label">Solusi</div>
            <div class="detail-value" style="white-space:pre-wrap">${data.solusi}</div>
          </div>` : ''}
          ${data.catatan ? `
          <div class="detail-section" style="margin-top:1rem">
            <div class="detail-label">Catatan Tambahan</div>
            <div class="detail-value" style="white-space:pre-wrap">${data.catatan}</div>
          </div>` : ''}
        </div>

        ${data.catatan_revisi ? `
        <div class="glass-card card" style="margin-top:1.25rem;border-left:4px solid var(--warning)">
          <h3 style="margin-bottom:0.5rem">Catatan Revisi</h3>
          <div class="detail-value" style="white-space:pre-wrap">${data.catatan_revisi}</div>
        </div>` : ''}
      </div>

      <div>
        <div class="glass-card card">
          <h3 style="margin-bottom:1rem">Data Siswa</h3>
          <div class="detail-section">
            <div class="detail-label">Nama</div>
            <div class="detail-value">${data.nama}</div>
          </div>
          <div class="detail-section">
            <div class="detail-label">NISN</div>
            <div class="detail-value">${data.nisn}</div>
          </div>
          <div class="detail-section">
            <div class="detail-label">Kelas</div>
            <div class="detail-value">${data.kelas}</div>
          </div>
          <div class="detail-section">
            <div class="detail-label">Jurusan</div>
            <div class="detail-value">${data.jurusan}</div>
          </div>
          <div class="detail-section">
            <div class="detail-label">Sekolah</div>
            <div class="detail-value">${data.sekolah}</div>
          </div>
        </div>

        <div class="glass-card card" style="margin-top:1.25rem">
          <h3 style="margin-bottom:1rem">Dokumentasi Foto</h3>
          <div class="detail-photos">${fotoHtml}</div>
        </div>

        <div class="glass-card card" style="margin-top:1.25rem">
          <h3 style="margin-bottom:1rem">Dokumentasi Video</h3>
          ${videoHtml}
        </div>

        <div class="glass-card card" style="margin-top:1.25rem">
          <div class="detail-section">
            <div class="detail-label">Dibuat Pada</div>
            <div class="detail-value">${PSG.formatDateTime(data.createdAt)}</div>
          </div>
          ${data.verifiedAt ? `
          <div class="detail-section" style="margin-top:0.75rem">
            <div class="detail-label">Diverifikasi Oleh</div>
            <div class="detail-value">${data.verifiedBy || '-'} pada ${PSG.formatDateTime(data.verifiedAt)}</div>
          </div>` : ''}
        </div>

        <div style="margin-top:1.25rem;display:flex;gap:0.75rem">
          <button class="btn btn-primary" onclick="window.print()" style="flex:1">\uD83D\uDDA8\uFE0F Cetak</button>
          <button class="btn btn-outline" onclick="window.history.back()" style="flex:1">Kembali</button>
        </div>
      </div>
    </div>
  `;

  if (data.verifiedBy && PSG.currentUser.role === 'pembimbing') {
    container.innerHTML += `
      <div style="margin-top:1.25rem;display:flex;gap:0.75rem">
        <button class="btn btn-success" onclick="verifikasiLaporan('${data.id}','verified')" ${data.status === 'verified' ? 'disabled' : ''}>Verifikasi</button>
        <button class="btn btn-danger" onclick="verifikasiLaporan('${data.id}','rejected')" ${data.status === 'rejected' ? 'disabled' : ''}>Tolak</button>
      </div>
    `;
  }
}

function initExportButtons() {
  document.querySelectorAll('[data-export]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const type = e.currentTarget.dataset.export;
      if (type === 'pdf') exportPDF();
      else if (type === 'excel') exportExcel();
      else if (type === 'print') window.print();
    });
  });
}

function exportPDF() {
  const { jsPDF } = window.jspdf;
  if (!jsPDF) {
    PSG.showToast('Library PDF tidak tersedia', 'error');
    return;
  }
  const doc = new jsPDF();
  const laporan = PSG.db.getLaporan();
  const user = PSG.currentUser;
  const data = user.role === 'siswa' ? laporan.filter(l => l.nis === user.nis) : laporan;

  doc.setFontSize(16);
  doc.text('Laporan Harian PSG', 105, 20, { align: 'center' });
  doc.setFontSize(10);
  doc.text(`Diekspor pada: ${PSG.formatDateTime(new Date().toISOString())}`, 105, 28, { align: 'center' });
  doc.line(15, 32, 195, 32);

  let y = 40;
  data.forEach((l, i) => {
    if (y > 260) {
      doc.addPage();
      y = 20;
    }
    doc.setFontSize(10);
    doc.text(`${i + 1}. ${PSG.formatDate(l.tanggal)} - ${l.tempat_psg}`, 15, y);
    y += 6;
    doc.setFontSize(9);
    doc.text(`   Aktivitas: ${l.aktivitas?.substring(0, 80)}`, 15, y);
    y += 10;
  });

  doc.save(`laporan_psg_${new Date().toISOString().slice(0, 10)}.pdf`);
}

function exportExcel() {
  const laporan = PSG.db.getLaporan();
  const user = PSG.currentUser;
  const data = user.role === 'siswa' ? laporan.filter(l => l.nis === user.nis) : laporan;

  const headers = ['Tanggal', 'Nama', 'NISN', 'Kelas', 'Jurusan', 'Sekolah', 'Tempat PSG', 'Pembimbing', 'Jam Masuk', 'Jam Pulang', 'Aktivitas', 'Hasil', 'Kendala', 'Solusi', 'Catatan', 'Status'];
  const rows = data.map(l => [
    l.tanggal, l.nama, l.nisn, l.kelas, l.jurusan, l.sekolah,
    l.tempat_psg, l.pembimbing, l.jam_masuk, l.jam_pulang,
    l.aktivitas, l.hasil, l.kendala || '', l.solusi || '', l.catatan || '',
    PSG.getStatusText(l.status)
  ]);

  let csv = headers.join(',') + '\n';
  rows.forEach(row => {
    csv += row.map(cell => `"${(cell || '').replace(/"/g, '""')}"`).join(',') + '\n';
  });

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `laporan_psg_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
  URL.revokeObjectURL(url);
  PSG.showToast('Data berhasil diexport', 'success');
}

function verifikasiBulk(status) {
  Swal.fire({
    title: `Verifikasi Semua Laporan`,
    text: `Yakin ingin ${status === 'verified' ? 'memverifikasi' : 'menolak'} semua laporan?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: status === 'verified' ? '#10B981' : '#EF4444',
    cancelButtonColor: '#64748B',
    confirmButtonText: 'Ya',
    cancelButtonText: 'Batal',
    background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1E293B' : '#fff',
    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#F1F5F9' : '#1E293B',
  }).then(result => {
    if (!result.isConfirmed) return;
    let laporan = PSG.db.getLaporan();
    laporan = laporan.map(l => {
      if (l.status === 'pending') {
        l.status = status;
        l.verifiedBy = PSG.currentUser.nama;
        l.verifiedAt = new Date().toISOString();
      }
      return l;
    });
    PSG.db.setLaporan(laporan);
    PSG.showToast(`Semua laporan ${status === 'verified' ? ' diverifikasi' : ' ditolak'}`, 'success');
    renderRiwayatTable();
  });
}
