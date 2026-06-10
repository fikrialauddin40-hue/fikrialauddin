const PSG = {
  version: '3.2-hari',
  currentUser: null,
  theme: 'light',

  init() {
    this.loadTheme();
    this.loadCurrentUser();
    this.initLoadingScreen();
    this.initSidebar();
    this.initNavigation();
    this.initThemeToggle();
    this.updateUI();
  },

  db: {
    get(key) {
      try {
        const data = localStorage.getItem(`psg_${key}`);
        return data ? JSON.parse(data) : null;
      } catch { return null; }
    },
    set(key, value) {
      try {
        localStorage.setItem(`psg_${key}`, JSON.stringify(value));
        return true;
      } catch { return false; }
    },
    remove(key) {
      localStorage.removeItem(`psg_${key}`);
    },
    getUsers() { return this.get('users') || []; },
    setUsers(users) { return this.set('users', users); },
    getLaporan() { return this.get('laporan') || []; },
    setLaporan(laporan) { return this.set('laporan', laporan); },
    getDokumentasi() { return this.get('dokumentasi') || []; },
    setDokumentasi(dok) { return this.set('dokumentasi', dok); },
    getSekolah() { return this.get('sekolah') || []; },
    setSekolah(s) { return this.set('sekolah', s); },
    getTempatPSG() { return this.get('tempat_psg') || []; },
    setTempatPSG(t) { return this.set('tempat_psg', t); },
    getNotifications() { return this.get('notifications') || []; },
    setNotifications(n) { return this.set('notifications', n); },
    backup() {
      const data = {
        users: this.getUsers(),
        laporan: this.getLaporan(),
        dokumentasi: this.getDokumentasi(),
        sekolah: this.getSekolah(),
        tempat_psg: this.getTempatPSG(),
        notifications: this.getNotifications(),
        exportedAt: new Date().toISOString()
      };
      const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `psg_backup_${new Date().toISOString().slice(0,10)}.json`;
      a.click();
      URL.revokeObjectURL(url);
      return true;
    },
    restore(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
          try {
            const data = JSON.parse(e.target.result);
            if (data.users) this.setUsers(data.users);
            if (data.laporan) this.setLaporan(data.laporan);
            if (data.dokumentasi) this.setDokumentasi(data.dokumentasi);
            if (data.sekolah) this.setSekolah(data.sekolah);
            if (data.tempat_psg) this.setTempatPSG(data.tempat_psg);
            if (data.notifications) this.setNotifications(data.notifications);
            resolve(true);
          } catch (err) { reject(err); }
        };
        reader.onerror = () => reject(new Error('Gagal membaca file'));
        reader.readAsText(file);
      });
    },
    reset() {
      this.remove('users');
      this.remove('laporan');
      this.remove('dokumentasi');
      this.remove('sekolah');
      this.remove('tempat_psg');
      this.remove('notifications');
      this.remove('current_user');
      this.remove('theme');
      return true;
    }
  },

  loadCurrentUser() {
    this.currentUser = this.db.get('current_user');
  },

  isLoggedIn() {
    return this.currentUser !== null;
  },

  requireAuth() {
    if (!this.isLoggedIn()) {
      window.location.href = 'login.html';
      return false;
    }
    return true;
  },

  login(nis, password, role) {
    const users = this.db.getUsers();
    const user = users.find(u => u.nis === nis && u.password === password && u.role === role);
    if (user) {
      this.currentUser = user;
      this.db.set('current_user', user);
      if (document.getElementById('rememberMe')?.checked) {
        this.db.set('remembered_user', { nis, role });
      } else {
        this.db.remove('remembered_user');
      }
      return true;
    }
    return false;
  },

  logout() {
    this.currentUser = null;
    this.db.remove('current_user');
    window.location.href = 'login.html';
  },

  getGreeting() {
    const h = new Date().getHours();
    if (h < 12) return 'Selamat Pagi';
    if (h < 15) return 'Selamat Siang';
    if (h < 18) return 'Selamat Sore';
    return 'Selamat Malam';
  },

  getQuote() {
    const quotes = [
      'Semangat belajar dan terus berkembang!',
      'Pengalaman hari ini adalah investasi masa depan.',
      'Rajin mencatat, sukses di kemudian hari.',
      'Setiap langkah kecil membawa perubahan besar.',
      'Jadilah pribadi yang disiplin dan bertanggung jawab.',
      'Ilmu tanpa amal bagaikan pohon tanpa buah.',
      'Bekerja keras, bermimpi besar.',
      'Kunci kesuksesan adalah konsistensi.',
      'Hari ini bekerja keras, esok menuai hasil.',
      'Teruslah belajar dan jangan pernah menyerah.'
    ];
    return quotes[Math.floor(Math.random() * quotes.length)];
  },

  getDayCount(startDate) {
    const start = new Date(startDate);
    const today = new Date();
    const diff = Math.floor((today - start) / (1000 * 60 * 60 * 24));
    return Math.max(0, diff);
  },

  loadTheme() {
    this.theme = this.db.get('theme') || 'light';
    document.documentElement.setAttribute('data-theme', this.theme);
  },

  toggleTheme() {
    this.theme = this.theme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', this.theme);
    this.db.set('theme', this.theme);
    const icon = document.querySelector('.theme-toggle .theme-icon');
    if (icon) icon.textContent = this.theme === 'dark' ? '\u2600\uFE0F' : '\uD83C\uDF19';
  },

  initLoadingScreen() {
    const loader = document.getElementById('loadingScreen');
    if (loader) {
      setTimeout(() => loader.classList.add('hidden'), 600);
    }
  },

  initSidebar() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
      document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1024 && sidebar.classList.contains('open')) {
          if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
            sidebar.classList.remove('open');
          }
        }
      });
    }
  },

  initNavigation() {
    document.querySelectorAll('.nav-item').forEach(item => {
      item.addEventListener('click', function(e) {
        if (this.dataset.page) {
          window.location.href = this.dataset.page;
        }
        if (this.dataset.action === 'logout') {
          PSG.logout();
        }
      });
    });

    document.querySelectorAll('.nav-item').forEach(item => {
      const currentPage = window.location.pathname.split('/').pop();
      if (item.dataset.page === currentPage) {
        item.classList.add('active');
      }
    });
  },

  initThemeToggle() {
    const toggle = document.querySelector('.theme-toggle');
    if (toggle) {
      const icon = toggle.querySelector('.theme-icon');
      if (icon) icon.textContent = this.theme === 'dark' ? '\u2600\uFE0F' : '\uD83C\uDF19';
      toggle.addEventListener('click', () => this.toggleTheme());
    }
  },

  updateUI() {
    if (!this.currentUser) return;

    const userNameEls = document.querySelectorAll('.sidebar-user-name, .user-name-display');
    userNameEls.forEach(el => { el.textContent = this.currentUser.nama; });

    const userRoleEls = document.querySelectorAll('.sidebar-user-role');
    userRoleEls.forEach(el => {
      const roleMap = { siswa: 'Siswa PKL', pembimbing: 'Pembimbing', admin: 'Administrator' };
      el.textContent = roleMap[this.currentUser.role] || this.currentUser.role;
    });

    const avatarEls = document.querySelectorAll('.user-avatar-initials');
    avatarEls.forEach(el => {
      el.textContent = this.currentUser.nama ? this.currentUser.nama.charAt(0).toUpperCase() : '?';
    });

    const avatarImgs = document.querySelectorAll('.user-avatar-img');
    avatarImgs.forEach(el => {
      if (this.currentUser.foto) {
        el.src = this.currentUser.foto;
        el.style.display = 'block';
        el.parentElement.querySelector('.user-avatar-initials')?.remove();
      }
    });
  },

  showToast(message, type = 'info') {
    const container = document.querySelector('.toast-container');
    if (!container) {
      const c = document.createElement('div');
      c.className = 'toast-container';
      document.body.appendChild(c);
    }
    const icons = {
      success: '\u2705', error: '\u274C', warning: '\u26A0\uFE0F', info: '\u2139\uFE0F'
    };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      <span class="toast-icon">${icons[type]}</span>
      <span class="toast-message">${message}</span>
      <button class="toast-close" onclick="this.parentElement.remove()">\u2716</button>
    `;
    document.querySelector('.toast-container')?.appendChild(toast);
    setTimeout(() => {
      toast.classList.add('toast-out');
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  },

  formatDate(date) {
    const d = new Date(date);
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
  },

  formatDateTime(date) {
    const d = new Date(date);
    return `${this.formatDate(d)} ${d.getHours().toString().padStart(2,'0')}:${d.getMinutes().toString().padStart(2,'0')}`;
  },

  getMonthName(m) {
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return months[m];
  },

  generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2, 9);
  },

  getStatusBadge(status) {
    const map = {
      draft: '<span class="badge badge-info">Draft</span>',
      pending: '<span class="badge badge-warning">Menunggu Verifikasi</span>',
      verified: '<span class="badge badge-success">Terverifikasi</span>',
      rejected: '<span class="badge badge-danger">Ditolak</span>'
    };
    return map[status] || map.draft;
  },

  getStatusText(status) {
    const map = {
      draft: 'Draft',
      pending: 'Menunggu Verifikasi',
      verified: 'Terverifikasi',
      rejected: 'Ditolak'
    };
    return map[status] || 'Draft';
  },

  openLightbox(src, caption = '') {
    const overlay = document.getElementById('lightboxOverlay');
    if (!overlay) {
      const el = document.createElement('div');
      el.id = 'lightboxOverlay';
      el.className = 'lightbox-overlay';
      el.innerHTML = `
        <button class="lightbox-close">&times;</button>
        <img src="" alt="Preview">
        <div class="lightbox-caption"></div>
      `;
      document.body.appendChild(el);
      el.addEventListener('click', (e) => {
        if (e.target === el || e.target.classList.contains('lightbox-close')) {
          el.classList.remove('active');
        }
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') el.classList.remove('active');
      });
    }
    const img = document.querySelector('#lightboxOverlay img');
    const captionEl = document.querySelector('#lightboxOverlay .lightbox-caption');
    if (img) img.src = src;
    if (captionEl) captionEl.textContent = caption;
    document.querySelector('#lightboxOverlay')?.classList.add('active');
  },

  downloadFile(url, filename) {
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
  }
};

document.addEventListener('DOMContentLoaded', () => PSG.init());
window.PSG = PSG;

function showAlert(message, icon = 'info') {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      icon: icon,
      title: message,
      confirmButtonColor: '#4F46E5',
      confirmButtonText: 'OK',
      background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1E293B' : '#fff',
      color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#F1F5F9' : '#1E293B',
    });
  } else {
    PSG.showToast(message, icon === 'error' ? 'error' : icon === 'success' ? 'success' : 'info');
  }
}

function showConfirm(message) {
  return Swal.fire({
    title: 'Konfirmasi',
    text: message,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#4F46E5',
    cancelButtonColor: '#EF4444',
    confirmButtonText: 'Ya',
    cancelButtonText: 'Batal',
    background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1E293B' : '#fff',
    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#F1F5F9' : '#1E293B',
  });
}
