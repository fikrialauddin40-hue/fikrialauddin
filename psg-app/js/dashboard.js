document.addEventListener('DOMContentLoaded', () => {
  if (!PSG.requireAuth()) return;
  PSG.init();

  const user = PSG.currentUser;
  renderGreeting(user);
  renderStats(user);
  renderProgress(user);
  renderActivityChart();
  renderMonthlyChart();
  renderNotifications();
  renderQuickActions(user);
  renderLaporanTerbaru();
});

function renderGreeting(user) {
  const el = document.querySelector('.greeting-content');
  if (!el) return;

  const initial = user.nama ? user.nama.charAt(0).toUpperCase() : '?';
  const fotoHtml = user.foto
    ? `<img src="${user.foto}" alt="${user.nama}">`
    : `<span class="user-avatar-initials">${initial}</span>`;

  el.innerHTML = `
    <div class="greeting-avatar">${fotoHtml}</div>
    <div>
      <div class="greeting-text">
        <h1>${PSG.getGreeting()}, <span class="highlight">${user.nama}</span></h1>
        <p>${user.sekolah || ''} &middot; ${user.kelas || ''} ${user.jurusan || ''}</p>
        <p style="margin-top:0.25rem;font-size:0.85rem">
          Tempat PSG: <strong>${user.tempat_psg || '-'}</strong> &middot;
          Pembimbing: <strong>${user.pembimbing || '-'}</strong>
        </p>
      </div>
      <div class="greeting-quote">${PSG.getQuote()}</div>
    </div>
  `;
}

function renderStats(user) {
  const laporan = PSG.db.getLaporan();
  const userLaporan = laporan.filter(l => l.nis === user.nis);
  const totalLaporan = userLaporan.length;
  const hariPkl = user.hari_pkl || PSG.getDayCount(user.tanggal_mulai || '2026-06-02') + 1;
  const verified = userLaporan.filter(l => l.status === 'verified').length;
  const pending = userLaporan.filter(l => l.status === 'pending').length;

  const stats = [
    { icon: '\uD83D\uDCC4', label: 'Total Laporan', value: totalLaporan, color: 'icon-primary' },
    { icon: '\u2705', label: 'Terverifikasi', value: verified, color: 'icon-success' },
    { icon: '\u23F3', label: 'Menunggu', value: pending, color: 'icon-warning' },
    { icon: '\uD83D\uDCC5', label: 'Hari PKL', value: `Hari ke-${hariPkl}`, color: 'icon-info' }
  ];

  const container = document.querySelector('.dashboard-stats');
  if (!container) return;

  container.innerHTML = stats.map(s => `
    <div class="glass-card stat-card animate-fadeInUp">
      <div class="stat-icon ${s.color}">${s.icon}</div>
      <div class="stat-info">
        <div class="stat-label">${s.label}</div>
        <div class="stat-value stat-counter-${s.label.toLowerCase().replace(/\s/g,'')}">0</div>
      </div>
    </div>
  `).join('');

  setTimeout(() => {
    const vals = [stats[0].value, stats[1].value, stats[2].value];
    document.querySelectorAll('.stat-counter-totallaporan, .stat-counter-terverifikasi, .stat-counter-menunggu').forEach((el, i) => el.textContent = vals[i]);
    const daysEl = document.querySelector('.stat-counter-haripkl');
    if (daysEl) daysEl.textContent = `Hari ke-${hariPkl}`;
  }, 400);
}

function renderProgress(user) {
  const el = document.getElementById('progressOverview');
  if (!el) return;

  const targetDays = 180;
  const currentDays = user.hari_pkl || PSG.getDayCount(user.tanggal_mulai || '2026-06-02') + 1;
  const progress = Math.min(100, Math.round((currentDays / targetDays) * 100));

  const circumference = 2 * Math.PI * 52;
  const offset = circumference - (progress / 100) * circumference;

  el.innerHTML = `
    <div class="progress-circle-wrap">
      <svg width="0" height="0">
        <defs>
          <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#4F46E5" />
            <stop offset="100%" stop-color="#7C3AED" />
          </linearGradient>
        </defs>
      </svg>
      <div class="progress-circle">
        <svg viewBox="0 0 120 120">
          <circle class="progress-bg" cx="60" cy="60" r="52" />
          <circle class="progress-fill" cx="60" cy="60" r="52"
            stroke-dasharray="${circumference}"
            stroke-dashoffset="${offset}" />
        </svg>
        <div class="progress-text">
          <span class="value">${progress}%</span>
          <span class="label">Progress PKL</span>
        </div>
      </div>
    </div>
    <div class="progress-details">
      <div class="progress-detail-item">
        <div class="value">${currentDays}</div>
        <div class="label">Hari Ke-</div>
      </div>
      <div class="progress-detail-item">
        <div class="value">${targetDays}</div>
        <div class="label">Target Hari</div>
      </div>
      <div class="progress-detail-item">
        <div class="value">${user.tempat_psg || '-'}</div>
        <div class="label">Tempat PSG</div>
      </div>
      <div class="progress-detail-item">
        <div class="value">${user.pembimbing || '-'}</div>
        <div class="label">Pembimbing</div>
      </div>
    </div>
  `;
}

function renderActivityChart() {
  const canvas = document.getElementById('activityChart');
  if (!canvas || typeof Chart === 'undefined') return;

  const laporan = PSG.db.getLaporan();
  const user = PSG.currentUser;
  const userLaporan = laporan.filter(l => l.nis === user.nis);

  const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const counts = new Array(7).fill(0);

  userLaporan.forEach(l => {
    const day = new Date(l.tanggal).getDay();
    counts[day]++;
  });

  new Chart(canvas, {
    type: 'bar',
    data: {
      labels: days,
      datasets: [{
        label: 'Laporan',
        data: counts,
        backgroundColor: [
          'rgba(79, 70, 229, 0.8)', 'rgba(16, 185, 129, 0.8)',
          'rgba(245, 158, 11, 0.8)', 'rgba(59, 130, 246, 0.8)',
          'rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)',
          'rgba(14, 165, 233, 0.8)'
        ],
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 },
          grid: { color: 'rgba(148, 163, 184, 0.1)' }
        },
        x: {
          grid: { display: false }
        }
      }
    }
  });
}

function renderMonthlyChart() {
  const canvas = document.getElementById('monthlyChart');
  if (!canvas || typeof Chart === 'undefined') return;

  const laporan = PSG.db.getLaporan();
  const user = PSG.currentUser;
  const userLaporan = laporan.filter(l => l.nis === user.nis);

  const months = Array.from({length: 12}, (_, i) => PSG.getMonthName(i));
  const counts = new Array(12).fill(0);

  userLaporan.forEach(l => {
    const m = new Date(l.tanggal).getMonth();
    counts[m]++;
  });

  new Chart(canvas, {
    type: 'line',
    data: {
      labels: months,
      datasets: [{
        label: 'Laporan Bulanan',
        data: counts,
        borderColor: '#4F46E5',
        backgroundColor: 'rgba(79, 70, 229, 0.1)',
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#4F46E5',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 },
          grid: { color: 'rgba(148, 163, 184, 0.1)' }
        },
        x: {
          grid: { display: false }
        }
      }
    }
  });
}

function renderNotifications() {
  const container = document.querySelector('.notification-list');
  if (!container) return;

  const notifs = PSG.db.getNotifications().slice(0, 5);
  if (!notifs.length) {
    container.innerHTML = `
      <div class="empty-state" style="padding:1.5rem">
        <div class="empty-icon" style="font-size:2rem">\uD83D\uDCEC</div>
        <p>Tidak ada notifikasi</p>
      </div>
    `;
    return;
  }

  const icons = {
    info: '\uD83D\uDCE2', success: '\u2705', warning: '\u26A0\uFE0F', error: '\u274C'
  };

  container.innerHTML = notifs.map(n => `
    <div class="notif-item ${n.read ? '' : 'unread'}">
      <div class="notif-icon" style="background:${n.color || 'rgba(79,70,229,0.1)'}">
        ${icons[n.type] || '\uD83D\uDCE2'}
      </div>
      <div class="notif-content">
        <div class="notif-text">${n.message}</div>
        <div class="notif-time">${PSG.formatDateTime(n.createdAt)}</div>
      </div>
    </div>
  `).join('');
}

function renderQuickActions(user) {
  const container = document.querySelector('.quick-actions');
  if (!container) return;

  if (user.role === 'siswa') {
    container.innerHTML = `
      <div class="quick-report-card glass-card" onclick="window.location.href='laporan.html'">
        <div class="quick-icon">\uD83D\uDCDD</div>
        <h4>Buat Laporan Baru</h4>
        <p>Catat aktivitas PKL hari ini</p>
      </div>
      <div class="quick-report-card glass-card" onclick="window.location.href='riwayat.html'">
        <div class="quick-icon">\uD83D\uDCCA</div>
        <h4>Lihat Riwayat</h4>
        <p>Cek laporan yang sudah dibuat</p>
      </div>
      <div class="quick-report-card glass-card" onclick="window.location.href='dokumentasi.html'">
        <div class="quick-icon">\uD83D\uDCF7</div>
        <h4>Dokumentasi</h4>
        <p>Upload foto kegiatan PKL</p>
      </div>
      <div class="quick-report-card glass-card" onclick="window.location.href='profil.html'">
        <div class="quick-icon">\uD83D\uDC64</div>
        <h4>Profil Saya</h4>
        <p>Lihat dan edit data diri</p>
      </div>
    `;
  } else if (user.role === 'pembimbing') {
    container.innerHTML = `
      <div class="quick-report-card glass-card" onclick="window.location.href='riwayat.html'">
        <div class="quick-icon">\uD83D\uDCCA</div>
        <h4>Semua Laporan Siswa</h4>
        <p>Verifikasi laporan yang masuk</p>
      </div>
      <div class="quick-report-card glass-card" onclick="window.location.href='dokumentasi.html'">
        <div class="quick-icon">\uD83D\uDCF7</div>
        <h4>Dokumentasi</h4>
        <p>Lihat dokumentasi kegiatan</p>
      </div>
    `;
  } else {
    container.innerHTML = `
      <div class="quick-report-card glass-card" onclick="window.location.href='riwayat.html'">
        <div class="quick-icon">\uD83D\uDCCA</div>
        <h4>Kelola Laporan</h4>
        <p>Semua laporan siswa</p>
      </div>
      <div class="quick-report-card glass-card" onclick="window.location.href='pengaturan.html'">
        <div class="quick-icon">\u2699\uFE0F</div>
        <h4>Pengaturan</h4>
        <p>Backup & reset data</p>
      </div>
    `;
  }
}

function renderLaporanTerbaru() {
  const container = document.querySelector('.laporan-terbaru tbody');
  if (!container) return;

  const laporan = PSG.db.getLaporan();
  const user = PSG.currentUser;
  const userLaporan = laporan.filter(l => l.nis === user.nis).slice(-5).reverse();

  if (!userLaporan.length) {
    container.innerHTML = `
      <tr><td colspan="5" class="empty-state" style="padding:2rem">
        <p>Belum ada laporan. Mulai buat laporan pertama Anda!</p>
      </td></tr>
    `;
    return;
  }

  container.innerHTML = userLaporan.map(l => `
    <tr>
      <td>${PSG.formatDate(l.tanggal)}</td>
      <td>${l.tempat_psg}</td>
      <td>${l.aktivitas?.substring(0, 50) || '-'}${l.aktivitas?.length > 50 ? '...' : ''}</td>
      <td>${PSG.getStatusBadge(l.status)}</td>
      <td>
        <button class="btn btn-sm btn-ghost" onclick="window.location.href='detail-laporan.html?id=${l.id}'">Detail</button>
      </td>
    </tr>
  `).join('');
}
