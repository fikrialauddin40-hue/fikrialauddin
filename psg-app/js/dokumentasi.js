let dokFiles = [];
let editingDokId = null;

document.addEventListener('DOMContentLoaded', () => {
  if (!PSG.requireAuth()) return;
  PSG.init();

  if (window.location.pathname.includes('dokumentasi')) {
    initDokumentasi();
  }
});

function initDokumentasi() {
  renderDokStats();
  renderGallery();
  initDokUpload();
  initDokFilters();
  renderTimeline();
}

function renderDokStats() {
  const container = document.querySelector('.dok-stats');
  if (!container) return;

  const dok = PSG.db.getDokumentasi();
  const user = PSG.currentUser;
  const userDok = user.role === 'siswa' ? dok.filter(d => d.nis === user.nis) : dok;

  const photos = userDok.filter(d => d.tipe === 'foto').reduce((sum, d) => sum + (d.files?.length || 0), 0);
  const videos = userDok.filter(d => d.tipe === 'video').length;
  const totalKeg = userDok.length;

  container.innerHTML = `
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon icon-primary">\uD83D\uDCF7</div>
      <div class="stat-info">
        <div class="stat-label">Total Foto</div>
        <div class="stat-value">${photos}</div>
      </div>
    </div>
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon icon-info">\uD83C\uDFA5</div>
      <div class="stat-info">
        <div class="stat-label">Total Video</div>
        <div class="stat-value">${videos}</div>
      </div>
    </div>
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon icon-success">\uD83D\uDCCB</div>
      <div class="stat-info">
        <div class="stat-label">Total Kegiatan</div>
        <div class="stat-value">${totalKeg}</div>
      </div>
    </div>
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon icon-warning">\uD83D\uDCC5</div>
      <div class="stat-info">
        <div class="stat-label">Bulan Ini</div>
        <div class="stat-value">${userDok.filter(d => new Date(d.tanggal).getMonth() === new Date().getMonth()).length}</div>
      </div>
    </div>
  `;
}

function renderGallery() {
  const container = document.getElementById('galleryGrid');
  if (!container) return;

  const dok = PSG.db.getDokumentasi();
  const user = PSG.currentUser;
  let data = user.role === 'siswa' ? dok.filter(d => d.nis === user.nis) : [...dok];

  const search = document.getElementById('searchDok')?.value?.toLowerCase() || '';
  if (search) {
    data = data.filter(d =>
      d.nama_kegiatan?.toLowerCase().includes(search) ||
      d.lokasi?.toLowerCase().includes(search) ||
      d.caption?.toLowerCase().includes(search)
    );
  }

  data.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

  if (!data.length) {
    container.innerHTML = `
      <div class="empty-state" style="grid-column:1/-1">
        <div class="empty-icon">\uD83D\uDCF8</div>
        <h3>Belum Ada Dokumentasi</h3>
        <p>Upload foto atau video kegiatan PKL Anda</p>
      </div>
    `;
    return;
  }

  container.innerHTML = data.map(d => {
    const thumbSrc = d.tipe === 'foto' && d.files?.length
      ? d.files[0].data
      : d.tipe === 'video' && d.files?.length
        ? d.files[0].data
        : 'https://via.placeholder.com/300?text=No+Media';

    const tipeIcon = d.tipe === 'foto' ? '\uD83D\uDCF7' : '\uD83C\uDFA5';
    const fileCount = d.files?.length || 0;

    return `
      <div class="gallery-item" data-id="${d.id}">
        <span class="gallery-type-badge">${tipeIcon} ${d.tipe === 'foto' ? fileCount + ' Foto' : 'Video'}</span>
        <div class="gallery-actions">
          <button class="edit-btn" onclick="editDokumentasi('${d.id}')" title="Edit">\u270F\uFE0F</button>
          <button class="delete-btn" onclick="hapusDokumentasi('${d.id}')" title="Hapus">\uD83D\uDDD1\uFE0F</button>
        </div>
        ${d.tipe === 'foto'
          ? `<img src="${thumbSrc}" alt="${d.nama_kegiatan}" onclick="PSG.openLightbox('${d.files[0].data}','${d.caption || d.nama_kegiatan}')">`
          : `<video src="${thumbSrc}" onclick="openVideo('${thumbSrc}')"></video>`
        }
        <div class="gallery-overlay">
          <div class="gallery-title">${d.nama_kegiatan || 'Kegiatan'}</div>
          <div class="gallery-meta">${PSG.formatDate(d.tanggal)} &middot; ${d.lokasi || '-'}</div>
          ${d.caption ? `<div class="gallery-meta" style="font-size:0.7rem;margin-top:0.15rem">${d.caption}</div>` : ''}
        </div>
      </div>
    `;
  }).join('');
}

function initDokUpload() {
  const uploadArea = document.getElementById('dokUploadArea');
  const input = document.getElementById('dokInput');
  const preview = document.getElementById('dokPreview');
  const form = document.getElementById('formDokumentasi');

  if (!uploadArea || !input) return;

  uploadArea.addEventListener('click', () => input.click());

  uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
  });
  uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
  uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    if (e.dataTransfer.files.length) handleDokFiles(e.dataTransfer.files);
  });

  input.addEventListener('change', (e) => {
    if (e.target.files.length) handleDokFiles(e.target.files);
  });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    await saveDokumentasi();
  });
}

function handleDokFiles(files) {
  const preview = document.getElementById('dokPreview');
  if (!preview) return;

  for (const file of files) {
    if (dokFiles.length >= 10) {
      PSG.showToast('Maksimal 10 file', 'warning');
      break;
    }
    dokFiles.push(file);

    const reader = new FileReader();
    reader.onload = (e) => {
      const item = document.createElement('div');
      item.className = 'preview-item';
      const isVideo = file.type.startsWith('video/');
      item.innerHTML = `
        ${isVideo
          ? `<video src="${e.target.result}" muted></video><span class="preview-type">Video</span>`
          : `<img src="${e.target.result}" alt="Preview"><span class="preview-type">Foto</span>`
        }
        <button class="preview-remove" data-file="${file.name}">&times;</button>
      `;
      preview.appendChild(item);
      item.querySelector('.preview-remove').addEventListener('click', () => {
        dokFiles = dokFiles.filter(f => f.name !== file.name);
        item.remove();
      });
    };
    reader.readAsDataURL(file);
  }
}

async function saveDokumentasi() {
  const nama = document.getElementById('dokNamaKegiatan')?.value;
  const lokasi = document.getElementById('dokLokasi')?.value;
  const tanggal = document.getElementById('dokTanggal')?.value;
  const caption = document.getElementById('dokCaption')?.value;

  if (!nama || !tanggal) {
    PSG.showToast('Nama kegiatan dan tanggal wajib diisi', 'error');
    return;
  }
  if (!dokFiles.length && !editingDokId) {
    PSG.showToast('Pilih file yang akan diupload', 'error');
    return;
  }

  const user = PSG.currentUser;
  const files = [];
  let tipe = 'foto';

  for (const file of dokFiles) {
    const data = await fileToBase64(file);
    files.push({ name: file.name, data, type: file.type });
    if (file.type.startsWith('video/')) tipe = 'video';
  }

  const docData = {
    id: editingDokId || PSG.generateId(),
    nis: user.nis,
    nama: user.nama,
    nama_kegiatan: nama,
    lokasi: lokasi || '',
    tanggal: tanggal,
    caption: caption || '',
    tipe: tipe,
    files: files,
    createdAt: editingDokId ? undefined : new Date().toISOString(),
    updatedAt: new Date().toISOString()
  };

  let dok = PSG.db.getDokumentasi();
  if (editingDokId) {
    const idx = dok.findIndex(d => d.id === editingDokId);
    if (idx !== -1) {
      docData.createdAt = dok[idx].createdAt;
      dok[idx] = docData;
    }
  } else {
    dok.push(docData);
  }

  PSG.db.setDokumentasi(dok);
  resetDokForm();

  await Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: editingDokId ? 'Dokumentasi diperbarui' : 'Dokumentasi berhasil diupload',
    confirmButtonColor: '#4F46E5',
    background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1E293B' : '#fff',
    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#F1F5F9' : '#1E293B',
  });

  renderDokStats();
  renderGallery();
  renderTimeline();
}

function resetDokForm() {
  document.getElementById('dokNamaKegiatan').value = '';
  document.getElementById('dokLokasi').value = '';
  document.getElementById('dokTanggal').value = new Date().toISOString().split('T')[0];
  document.getElementById('dokCaption').value = '';
  document.getElementById('dokPreview').innerHTML = '';
  document.getElementById('dokInput').value = '';
  dokFiles = [];
  editingDokId = null;
  const btn = document.querySelector('#formDokumentasi button[type="submit"]');
  if (btn) btn.textContent = 'Upload Dokumentasi';
}

function editDokumentasi(id) {
  const dok = PSG.db.getDokumentasi();
  const data = dok.find(d => d.id === id);
  if (!data) return;

  editingDokId = id;
  document.getElementById('dokNamaKegiatan').value = data.nama_kegiatan;
  document.getElementById('dokLokasi').value = data.lokasi || '';
  document.getElementById('dokTanggal').value = data.tanggal;
  document.getElementById('dokCaption').value = data.caption || '';
  document.getElementById('dokPreview').innerHTML = '';

  data.files?.forEach(f => {
    const item = document.createElement('div');
    item.className = 'preview-item';
    item.innerHTML = `
      ${f.type?.startsWith('video/')
        ? `<video src="${f.data}" muted></video><span class="preview-type">Video</span>`
        : `<img src="${f.data}" alt="Preview"><span class="preview-type">Foto</span>`
      }
    `;
    document.getElementById('dokPreview')?.appendChild(item);
  });

  const btn = document.querySelector('#formDokumentasi button[type="submit"]');
  if (btn) btn.textContent = 'Perbarui Dokumentasi';

  document.getElementById('dokUploadArea')?.scrollIntoView({ behavior: 'smooth' });
}

async function hapusDokumentasi(id) {
  const result = await showConfirm('Yakin ingin menghapus dokumentasi ini?');
  if (!result.isConfirmed) return;

  let dok = PSG.db.getDokumentasi();
  dok = dok.filter(d => d.id !== id);
  PSG.db.setDokumentasi(dok);
  PSG.showToast('Dokumentasi berhasil dihapus', 'success');
  renderGallery();
  renderDokStats();
  renderTimeline();
}

function initDokFilters() {
  const search = document.getElementById('searchDok');
  search?.addEventListener('keyup', () => renderGallery());
  search?.addEventListener('change', () => renderGallery());
}

function renderTimeline() {
  const container = document.querySelector('.timeline-kegiatan');
  if (!container) return;

  const dok = PSG.db.getDokumentasi();
  const user = PSG.currentUser;
  const data = (user.role === 'siswa' ? dok.filter(d => d.nis === user.nis) : dok)
    .sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal))
    .slice(0, 10);

  if (!data.length) {
    container.innerHTML = '<div class="empty-state" style="padding:1rem"><p>Belum ada kegiatan</p></div>';
    return;
  }

  container.innerHTML = data.map(d => {
    const thumbSrc = d.files?.length ? d.files[0].data : '';
    const thumbHtml = thumbSrc
      ? (d.tipe === 'video'
        ? `<video src="${thumbSrc}"></video>`
        : `<img src="${thumbSrc}" alt="">`)
      : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.5rem">\uD83D\uDCF7</div>';

    return `
      <div class="timeline-keg-item">
        <div class="keg-thumb">${thumbHtml}</div>
        <div class="keg-info">
          <div class="keg-name">${d.nama_kegiatan}</div>
          <div class="keg-meta">
            <span>\uD83D\uDCC5 ${PSG.formatDate(d.tanggal)}</span>
            <span>\uD83D\uDCCD ${d.lokasi || '-'}</span>
            <span>${d.files?.length || 0} file</span>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

function openVideo(src) {
  PSG.openLightbox(src, 'Video Dokumentasi');
}
