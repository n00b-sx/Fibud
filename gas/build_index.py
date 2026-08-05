import base64

with open('c:/Users/syahr/Documents/Fibud/gas/wallet_b64_clean.txt', 'r') as f:
    img_b64 = f.read().strip()

template = '''<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pencatatan Keuangan & Budgeting - Finud App</title>
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Font: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <style>
    body { font-family: 'Inter', sans-serif; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #0f172a; }
    ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #475569; }
  </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex antialiased">

  <!-- Mobile Sidebar Overlay -->
  <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 lg:hidden hidden"></div>

  <!-- SIDEBAR NAVIGATION (KIRI) -->
  <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div>
      <!-- Logo Header -->
      <div class="h-20 flex items-center px-6 border-b border-slate-800/80 justify-between">
        <div class="flex items-center space-x-3">
          <div class="p-2 bg-gradient-to-tr from-indigo-600 to-indigo-400 rounded-xl shadow-lg shadow-indigo-500/20 text-white">
            <img src="https://openmoji.org/data/color/svg/1F4B0.svg" class="w-6 h-6" alt="OpenMoji Money">
          </div>
          <div>
            <h1 class="text-lg font-bold text-white tracking-wide">Finud App</h1>
            <p class="text-[10px] text-indigo-400 font-medium tracking-wider uppercase">Financial Tracker</p>
          </div>
        </div>
        <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation Links (OpenMoji Icons) -->
      <nav class="p-4 space-y-1.5">
        <!-- a. Beranda -->
        <button onclick="navTo('beranda')" id="nav-beranda"
          class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-md">
          <img src="https://openmoji.org/data/color/svg/1F3E0.svg" class="w-5 h-5" alt="Home">
          <span>Beranda</span>
        </button>

        <!-- b. Dompet -->
        <button onclick="navTo('dompet')" id="nav-dompet"
          class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
          <div class="flex items-center space-x-3">
            <img src="https://openmoji.org/data/color/svg/1F45B.svg" class="w-5 h-5" alt="Wallet">
            <span>Dompet</span>
          </div>
        </button>

        <!-- c. Transaksi -->
        <button onclick="navTo('transaksi')" id="nav-transaksi"
          class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
          <div class="flex items-center space-x-3">
            <img src="https://openmoji.org/data/color/svg/1F4DD.svg" class="w-5 h-5" alt="Transactions">
            <span>Transaksi</span>
          </div>
          <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full border border-slate-700">Soon</span>
        </button>

        <!-- d. Anggaran -->
        <button onclick="navTo('anggaran')" id="nav-anggaran"
          class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
          <div class="flex items-center space-x-3">
            <img src="https://openmoji.org/data/color/svg/1F4CA.svg" class="w-5 h-5" alt="Budget">
            <span>Anggaran</span>
          </div>
          <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full border border-slate-700">Soon</span>
        </button>

        <!-- e. Laporan -->
        <button onclick="navTo('laporan')" id="nav-laporan"
          class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
          <div class="flex items-center space-x-3">
            <img src="https://openmoji.org/data/color/svg/1F4C8.svg" class="w-5 h-5" alt="Report">
            <span>Laporan</span>
          </div>
          <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full border border-slate-700">Soon</span>
        </button>
      </nav>
    </div>

    <div class="p-4 border-t border-slate-800/80">
      <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700/40 text-center">
        <span class="inline-flex items-center text-xs text-emerald-400 font-medium">
          <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
          Google Sheets Database
        </span>
      </div>
    </div>
  </aside>

  <!-- MAIN CONTENT AREA -->
  <div class="flex-1 flex flex-col min-w-0">
    
    <!-- Top Header -->
    <header class="h-20 bg-slate-900/60 backdrop-blur-md border-b border-slate-800/80 px-6 flex items-center justify-between sticky top-0 z-30">
      <div class="flex items-center space-x-4">
        <button onclick="toggleSidebar()" class="lg:hidden text-slate-300 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <h2 id="page-title" class="text-xl font-bold text-white">Beranda</h2>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex items-center space-x-3">
        <button onclick="openModalCategory()" class="hidden sm:flex items-center space-x-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold py-2.5 px-4 rounded-xl border border-slate-700 transition-all">
          <img src="https://openmoji.org/data/color/svg/2795.svg" class="w-3.5 h-3.5" alt="Add">
          <span>+ Kategori</span>
        </button>
        
        <button onclick="openModalTransaction()" class="flex items-center space-x-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
          <img src="https://openmoji.org/data/color/svg/2795.svg" class="w-3.5 h-3.5 filter brightness-200" alt="Add">
          <span>+ Transaksi</span>
        </button>
      </div>
    </header>

    <!-- Notification Toast -->
    <div id="toast" class="fixed top-24 right-6 z-50 transition-all duration-300 transform translate-x-full opacity-0 max-w-md">
      <div id="toast-content" class="flex items-center p-4 rounded-xl shadow-2xl text-sm font-medium border backdrop-blur-md"></div>
    </div>

    <!-- MAIN VIEW CONTAINER -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
      
      <!-- VIEW 1: BERANDA (DASHBOARD) -->
      <div id="view-beranda" class="space-y-6">

        <!-- 1. KARTU UTAMA HERO CARD "SEKILAS HARI INI" -->
        <div class="bg-gradient-to-r from-black via-slate-900 to-emerald-950/90 border border-emerald-900/40 rounded-[32px] sm:rounded-[36px] p-6 sm:p-8 shadow-2xl relative overflow-hidden">
          <div class="absolute -right-12 -top-12 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

          <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
            
            <!-- Kolom Kiri (Teks Informasi) -->
            <div class="flex-1 space-y-4 w-full">
              <!-- a. Label Atas (Ikon Petir Kuning OpenMoji + Text) -->
              <div class="flex items-center space-x-2">
                <img src="https://openmoji.org/data/color/svg/26A1.svg" class="w-5 h-5 animate-pulse" alt="Petir Kuning">
                <span id="hero-label-date" class="text-xs font-bold tracking-wider text-slate-400 uppercase">
                  SEKILAS HARI INI | <span id="hero-today-date">Selasa, 4 Agustus 2026</span>
                </span>
              </div>

              <!-- b. Nominal Utama & Subtitle -->
              <div>
                <div class="flex items-center space-x-3">
                  <h3 id="hero-main-nominal" class="text-4xl sm:text-5xl font-black text-white tracking-tight">
                    Rp 1.952.000
                  </h3>
                  <!-- Ikon Mata kecil OpenMoji untuk Toggle Saldo -->
                  <button onclick="toggleBalanceVisibility()" title="Sembunyikan/Tampilkan Nominal" class="p-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 transition-all">
                    <img src="https://openmoji.org/data/color/svg/1F441.svg" class="w-5 h-5" alt="Eye Toggle">
                  </button>
                </div>
                <!-- c. Subtitle Nominal & Progress Bar Hijau Terang -->
                <p class="text-xs sm:text-sm font-semibold text-emerald-400 mt-1 flex items-center space-x-1">
                  <span>budget harian yang tersisa</span>
                </p>
              </div>

              <!-- Garis Indikator Progress Bar Hijau Terang -->
              <div class="w-full bg-slate-950/80 rounded-full h-2.5 overflow-hidden border border-emerald-900/50">
                <div id="hero-progress-bar" class="bg-emerald-400 h-2.5 rounded-full shadow-[0_0_12px_rgba(52,211,153,0.6)] transition-all duration-500" style="width: 88%"></div>
              </div>

              <!-- d. Ringkasan Bawah (Pemasukan & Pengeluaran Berdampingan) -->
              <div class="pt-2 flex items-center space-x-6 sm:space-x-8">
                <!-- Pemasukan (Hijau) -->
                <div>
                  <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">PEMASUKAN</p>
                  <h4 id="hero-pemasukan" class="text-lg sm:text-xl font-black text-emerald-400 tracking-tight">
                    Rp 2.000.000
                  </h4>
                </div>

                <!-- Pembatas Vertikal Tipis -->
                <div class="h-9 border-r border-slate-800"></div>

                <!-- Pengeluaran (Merah) -->
                <div>
                  <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">PENGELUARAN</p>
                  <h4 id="hero-pengeluaran" class="text-lg sm:text-xl font-black text-rose-400 tracking-tight">
                    Rp 48.000
                  </h4>
                </div>
              </div>
            </div>

            <!-- Kolom Kanan (Ilustrasi 3D Dompet Kulit + Uang & Bintang) -->
            <div class="flex-shrink-0 flex items-center justify-center relative group">
              <div class="absolute inset-0 bg-emerald-500/20 rounded-full blur-2xl group-hover:bg-emerald-500/30 transition-all"></div>
              <img src="data:image/jpeg;base64,''' + img_b64 + '''" 
                alt="3D Wallet Illustration" 
                class="w-44 h-44 sm:w-52 sm:h-52 object-contain relative z-10 drop-shadow-[0_20px_25px_rgba(16,185,129,0.3)] transition-transform duration-500 hover:scale-105">
            </div>

          </div>
        </div>

        <!-- 2. KARTU BARU "PROGRESS PENGELUARAN" (PUTIH + SHADOW) -->
        <div class="bg-white text-slate-900 rounded-[28px] sm:rounded-[32px] p-6 sm:p-8 shadow-xl border border-slate-100">
          <div class="flex flex-col md:flex-row items-stretch gap-6 md:gap-8">
            
            <!-- Bagian Kiri (Seperempat Lebar - Hari Ke) -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 md:w-1/4 flex flex-col justify-center items-center text-center shadow-inner">
              <span class="text-[11px] font-black text-slate-400 tracking-wider uppercase mb-1">HARI KE-</span>
              <div class="flex items-baseline justify-center">
                <span id="progress-card-day-num" class="text-6xl sm:text-7xl font-black text-slate-900 leading-none">4</span>
                <span id="progress-card-days-total" class="text-lg sm:text-xl font-bold text-slate-400 ml-1">/ 31</span>
              </div>
            </div>

            <!-- Bagian Kanan (Tiga Perempat Lebar - Header & 3 Progress Bar) -->
            <div class="md:w-3/4 flex flex-col justify-between space-y-4">
              
              <!-- Header -->
              <div>
                <div class="flex items-center space-x-2">
                  <img src="https://openmoji.org/data/color/svg/1F4CA.svg" class="w-5 h-5" alt="Chart OpenMoji">
                  <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Progres Pengeluaran</h3>
                </div>
                <div class="flex flex-wrap items-center text-xs font-semibold text-slate-500 mt-1 gap-2">
                  <span class="bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-full border border-slate-200 uppercase tracking-wide font-bold text-[10px]">
                    SESUAI DENGAN ANGGARAN • HARI <span id="progress-card-subtitle-day">4 / 31</span>
                  </span>
                  <span id="progress-card-date-range" class="text-slate-400">1 Agu 2026 - 31 Agu 2026</span>
                </div>
              </div>

              <!-- Daftar Progress Bar (3 Item Bersusun Vertikal) -->
              <div class="space-y-3.5 pt-1">
                
                <!-- 1. Kebutuhan (Masih Aman - Ungu) -->
                <div class="space-y-1">
                  <div class="flex justify-between items-center text-xs font-bold">
                    <div class="flex items-center space-x-2">
                      <span class="text-slate-800">Kebutuhan</span>
                      <span class="text-purple-700 bg-purple-100 font-bold text-[11px] px-2.5 py-0.5 rounded-full border border-purple-200">
                        masih aman
                      </span>
                    </div>
                    <span class="text-slate-800 font-extrabold">5%</span>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-purple-600 h-3 rounded-full transition-all duration-500" style="width: 5%"></div>
                  </div>
                </div>

                <!-- 2. Keinginan (Terkendali - Oranye) -->
                <div class="space-y-1">
                  <div class="flex justify-between items-center text-xs font-bold">
                    <div class="flex items-center space-x-2">
                      <span class="text-slate-800">Keinginan</span>
                      <span class="text-amber-700 bg-amber-100 font-bold text-[11px] px-2.5 py-0.5 rounded-full border border-amber-200">
                        terkendali
                      </span>
                    </div>
                    <span class="text-slate-400 font-extrabold">0%</span>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-amber-500 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                  </div>
                </div>

                <!-- 3. Tabungan (Belum Nabung Nih - Biru) -->
                <div class="space-y-1">
                  <div class="flex justify-between items-center text-xs font-bold">
                    <div class="flex items-center space-x-2">
                      <span class="text-slate-800">Tabungan</span>
                      <span class="text-blue-700 bg-blue-100 font-bold text-[11px] px-2.5 py-0.5 rounded-full border border-blue-200">
                        belum nabung nih?
                      </span>
                    </div>
                    <span class="text-slate-400 font-extrabold">0%</span>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                  </div>
                </div>

              </div>

            </div>

          </div>
        </div>

        <!-- BARIS 2: GRID 2 KOLOM (REALISASI ANGGARAN & PENGELUARAN TERBESAR) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- CARD REALISASI ANGGARAN -->
          <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
                <div>
                  <h3 class="text-base font-bold text-white flex items-center">
                    <img src="https://openmoji.org/data/color/svg/1F4CA.svg" class="w-5 h-5 mr-2" alt="Budget Chart">
                    Realisasi Anggaran
                  </h3>
                  <p class="text-xs text-slate-400">Jumlah total anggaran yang dibuat & realisasi pemakaian</p>
                </div>
                <span id="overall-budget-badge" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">0%</span>
              </div>

              <div class="bg-slate-800/40 border border-slate-800 rounded-2xl p-4 space-y-2 mb-5">
                <div class="flex justify-between text-xs text-slate-300 font-medium">
                  <span>Total Realisasi: <strong id="budget-total-spent" class="text-white">Rp 0</strong></span>
                  <span>Target Limit: <strong id="budget-total-limit" class="text-indigo-400">Rp 0</strong></span>
                </div>
                <div class="w-full bg-slate-950 rounded-full h-3 overflow-hidden border border-slate-800">
                  <div id="overall-budget-bar" class="bg-indigo-500 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
              </div>

              <div id="budget-categories-list" class="space-y-4 max-h-60 overflow-y-auto pr-1">
                <p class="text-xs text-slate-500 text-center py-4">Memuat data anggaran...</p>
              </div>
            </div>

            <button onclick="openModalCategory()" class="w-full mt-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition-all flex items-center justify-center space-x-1.5">
              <span>+ Kelola Anggaran & Kategori</span>
            </button>
          </div>

          <!-- CARD PENGELUARAN TERBESAR -->
          <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5 flex flex-col justify-between">
            <div>
              <div class="border-b border-slate-800 pb-4 mb-4">
                <h3 class="text-base font-bold text-white flex items-center">
                  <img src="https://openmoji.org/data/color/svg/1F4C8.svg" class="w-5 h-5 mr-2" alt="Top Expense">
                  Pengeluaran Terbesar Bulan Ini
                </h3>
                <p class="text-xs text-slate-400">Top 5 kategori pengeluaran dengan nominal tertinggi</p>
              </div>

              <div id="top-expenses-list" class="space-y-3.5">
                <p class="text-xs text-slate-500 text-center py-8">Memuat data pengeluaran...</p>
              </div>
            </div>

            <div class="text-xs text-slate-500 text-center pt-2">
              Berdasarkan akumulasi pengeluaran bulan berjalan
            </div>
          </div>
        </div>

        <!-- BARIS 3: GRID 2 KOLOM (AKTIVITAS BULAN INI & TRANSAKSI TERBARU) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- CARD AKTIVITAS BULAN INI -->
          <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-3">
                <div>
                  <h3 class="text-base font-bold text-white flex items-center">
                    <img src="https://openmoji.org/data/color/svg/1F4C5.svg" class="w-5 h-5 mr-2" alt="Calendar">
                    Aktivitas Bulan Ini
                  </h3>
                  <p class="text-xs text-slate-400">Rekapitulasi transaksi per hari</p>
                </div>
                <span id="calendar-month-name" class="text-xs font-semibold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-lg border border-indigo-500/20">-</span>
              </div>

              <div class="w-full">
                <div class="grid grid-cols-7 gap-1 text-center text-[10px] sm:text-xs font-semibold text-slate-400 mb-1.5">
                  <div class="py-0.5 text-rose-400">Ming</div>
                  <div class="py-0.5">Sen</div>
                  <div class="py-0.5">Sel</div>
                  <div class="py-0.5">Rab</div>
                  <div class="py-0.5">Kam</div>
                  <div class="py-0.5">Jum</div>
                  <div class="py-0.5 text-emerald-400">Sab</div>
                </div>

                <div id="calendar-cells" class="grid grid-cols-7 gap-1 sm:gap-1.5">
                  <p class="col-span-7 text-center text-xs text-slate-500 py-8">Memuat kalender...</p>
                </div>
              </div>
            </div>

            <div class="text-[10px] text-slate-500 text-center pt-2">
              <span class="text-emerald-400">+Pemasukan</span> • <span class="text-rose-400">-Pengeluaran</span>
            </div>
          </div>

          <!-- CARD TRANSAKSI TERBARU -->
          <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-3">
                <div>
                  <h3 class="text-base font-bold text-white flex items-center">
                    <img src="https://openmoji.org/data/color/svg/1F551.svg" class="w-5 h-5 mr-2" alt="Clock">
                    Transaksi Terbaru
                  </h3>
                  <p class="text-xs text-slate-400">5 riwayat transaksi terkini</p>
                </div>

                <button onclick="openModalTransaction()" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">+ Catat</button>
              </div>

              <div id="recent-transactions-list" class="space-y-3">
                <p class="text-xs text-slate-500 text-center py-6">Memuat riwayat transaksi...</p>
              </div>
            </div>

            <div class="pt-2">
              <button onclick="navTo('transaksi')" class="w-full py-2.5 bg-slate-800/80 hover:bg-slate-800 text-indigo-400 hover:text-indigo-300 text-xs font-bold rounded-xl border border-slate-700/60 transition-all flex items-center justify-center space-x-2">
                <span>Lihat Semua Transaksi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- VIEW 2: HALAMAN DOMPET (REKENING) -->
      <div id="view-dompet" class="hidden space-y-6">
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950/30 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-500/20">
              MANAJEMEN REKENING
            </span>
            <h3 class="text-2xl font-bold text-white mt-2">Daftar Rekening & Sumber Dana</h3>
            <p class="text-xs text-slate-400 mt-1">Kelola akun bank, e-wallet, dan kas tunai Anda dalam satu tempat</p>
          </div>

          <button onclick="openModalTambahRekening()" class="flex items-center justify-center space-x-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white text-xs font-bold py-3 px-5 rounded-2xl shadow-xl shadow-emerald-500/20 transition-all shrink-0">
            <img src="https://openmoji.org/data/color/svg/2795.svg" class="w-4 h-4 filter brightness-200" alt="Add">
            <span>+ Tambah Rekening Baru</span>
          </button>
        </div>

        <!-- GRID 3 KOLOM CARD REKENING -->
        <div id="rekening-cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <p class="col-span-3 text-center text-xs text-slate-500 py-12">Memuat daftar rekening...</p>
        </div>
      </div>

      <!-- VIEW LAIN: PLACEHOLDER COMING SOON -->
      <div id="view-coming-soon" class="hidden min-h-[60vh] flex items-center justify-center">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-12 text-center max-w-md shadow-2xl space-y-4">
          <div class="w-16 h-16 bg-indigo-500/10 text-indigo-400 rounded-2xl flex items-center justify-center mx-auto border border-indigo-500/20">
            <img src="https://openmoji.org/data/color/svg/1F4F7.svg" class="w-8 h-8" alt="Coming Soon">
          </div>
          <h3 id="coming-soon-title" class="text-xl font-bold text-white">Menu Dalam Pengembangan</h3>
          <p class="text-xs text-slate-400 leading-relaxed">Fitur ini sedang disiapkan dan akan segera hadir untuk melengkapi pengelolaan keuangan Anda.</p>
          <button onclick="navTo('beranda')" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition-all">Kembali ke Beranda</button>
        </div>
      </div>

    </main>
  </div>

  <!-- MODAL 1: DETAIL REKENING (KLIK CARD REKENING) -->
  <div id="modal-detail-rekening" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl relative space-y-5">
      
      <div class="flex justify-between items-center border-b border-slate-800 pb-4">
        <div class="flex items-center space-x-3">
          <div id="detail-rek-icon" class="p-2.5 rounded-2xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
            <img src="https://openmoji.org/data/color/svg/1F45B.svg" class="w-6 h-6" alt="Wallet Icon">
          </div>
          <div>
            <h3 id="detail-rek-nama" class="text-lg font-bold text-white">-</h3>
            <span id="detail-rek-jenis-badge" class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">-</span>
          </div>
        </div>

        <button onclick="closeModalDetailRekening()" class="text-slate-400 hover:text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="bg-slate-800/40 border border-slate-800 rounded-2xl p-5 text-center space-y-1">
        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Total Saldo Terkini</p>
        <h4 id="detail-rek-saldo" class="text-3xl font-extrabold text-white tracking-tight">Rp 0</h4>
      </div>

      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3 flex items-center">
          <img src="https://openmoji.org/data/color/svg/1F551.svg" class="w-4 h-4 mr-1.5" alt="History">
          3 Transaksi Terakhir Rekening Ini
        </h4>

        <div id="detail-rek-trx-list" class="space-y-2.5 max-h-48 overflow-y-auto">
          <p class="text-xs text-slate-500 text-center py-4">Memuat riwayat transaksi...</p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-800">
        <button id="btn-edit-rek" onclick="openModalEditRekeningFromDetail()" class="py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition-all flex items-center justify-center space-x-1.5">
          <span>Ubah Rekening</span>
        </button>

        <button id="btn-delete-rek" onclick="confirmHapusRekeningFromDetail()" class="py-2.5 bg-rose-950/40 hover:bg-rose-900/60 text-rose-300 text-xs font-bold rounded-xl border border-rose-800/60 transition-all flex items-center justify-center space-x-1.5">
          <span>Hapus Rekening</span>
        </button>
      </div>

    </div>
  </div>

  <!-- MODAL 2: FORM TAMBAH / EDIT REKENING -->
  <div id="modal-form-rekening" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl relative space-y-4">
      
      <div class="flex justify-between items-center border-b border-slate-800 pb-3">
        <h3 id="form-rek-title" class="text-base font-bold text-white">Tambah Rekening Baru</h3>
        <button onclick="closeModalFormRekening()" class="text-slate-400 hover:text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form id="form-rekening" onsubmit="submitFormRekening(event)" class="space-y-4">
        <input type="hidden" id="form-rek-id" value="">

        <div>
          <label for="form-rek-nama" class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Rekening</label>
          <input type="text" id="form-rek-nama" placeholder="Contoh: BCA Utama, GoPay, Dompet Kas" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
          <label for="form-rek-jenis" class="block text-xs font-semibold text-slate-300 mb-1.5">Jenis Rekening</label>
          <select id="form-rek-jenis" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
            <option value="Bank">🏦 Bank</option>
            <option value="E-Wallet">📱 E-Wallet</option>
            <option value="Tunai">💵 Tunai</option>
          </select>
        </div>

        <div>
          <label for="form-rek-saldo" class="block text-xs font-semibold text-slate-300 mb-1.5">Saldo Awal (Rp)</label>
          <input type="number" id="form-rek-saldo" min="0" placeholder="0" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
        </div>

        <button type="submit" id="btn-submit-form-rek" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:from-emerald-500 flex items-center justify-center space-x-2 disabled:opacity-50">
          <span id="btn-text-form-rek">Simpan Rekening</span>
          <svg id="btn-spinner-form-rek" class="hidden animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
      </form>
    </div>
  </div>

  <!-- MODAL 3: TAMBAH TRANSAKSI -->
  <div id="modal-transaction" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl relative space-y-4">
      <div class="flex justify-between items-center border-b border-slate-800 pb-3">
        <h3 class="text-base font-bold text-white">Tambah Transaksi Baru</h3>
        <button onclick="closeModalTransaction()" class="text-slate-400 hover:text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form id="form-transaksi" onsubmit="submitTransaksi(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1.5">Jenis Transaksi</label>
          <div class="grid grid-cols-2 gap-3">
            <label class="cursor-pointer">
              <input type="radio" name="jenis" value="Pengeluaran" checked class="peer sr-only">
              <div class="py-2.5 text-center text-xs font-semibold rounded-xl border border-slate-700 bg-slate-950 text-slate-400 peer-checked:bg-rose-500/10 peer-checked:border-rose-500 peer-checked:text-rose-400 transition-all">💸 Pengeluaran</div>
            </label>
            <label class="cursor-pointer">
              <input type="radio" name="jenis" value="Pemasukan" class="peer sr-only">
              <div class="py-2.5 text-center text-xs font-semibold rounded-xl border border-slate-700 bg-slate-950 text-slate-400 peer-checked:bg-emerald-500/10 peer-checked:border-emerald-500 peer-checked:text-emerald-400 transition-all">💰 Pemasukan</div>
            </label>
          </div>
        </div>

        <div>
          <label for="trx-tanggal" class="block text-xs font-semibold text-slate-300 mb-1.5">Tanggal</label>
          <input type="date" id="trx-tanggal" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
          <label for="trx-rekening" class="block text-xs font-semibold text-slate-300 mb-1.5">Rekening / Sumber Dana</label>
          <select id="trx-rekening" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
            <option value="" disabled selected>Memuat rekening...</option>
          </select>
        </div>

        <div>
          <label for="trx-kategori" class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori</label>
          <select id="trx-kategori" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
            <option value="" disabled selected>Memuat kategori...</option>
          </select>
        </div>

        <div>
          <label for="trx-nominal" class="block text-xs font-semibold text-slate-300 mb-1.5">Nominal (Rp)</label>
          <input type="number" id="trx-nominal" min="1" placeholder="Contoh: 50000" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
          <label for="trx-keterangan" class="block text-xs font-semibold text-slate-300 mb-1.5">Keterangan (Opsional)</label>
          <textarea id="trx-keterangan" rows="2" placeholder="Catatan singkat..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500"></textarea>
        </div>

        <button type="submit" id="btn-submit-trx" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:from-indigo-500 flex items-center justify-center space-x-2 disabled:opacity-50">
          <span id="btn-text-trx">Simpan Transaksi</span>
          <svg id="btn-spinner-trx" class="hidden animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
      </form>
    </div>
  </div>

  <!-- MODAL 4: TAMBAH KATEGORI & LIMIT ANGGARAN -->
  <div id="modal-category" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl relative space-y-4">
      <div class="flex justify-between items-center border-b border-slate-800 pb-3">
        <h3 class="text-base font-bold text-white">Tambah Kategori & Limit Anggaran</h3>
        <button onclick="closeModalCategory()" class="text-slate-400 hover:text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form id="form-kategori" onsubmit="submitKategori(event)" class="space-y-4">
        <div>
          <label for="cat-nama" class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Kategori Baru</label>
          <input type="text" id="cat-nama" placeholder="Contoh: Makanan, Transportasi, Tabungan" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
          <label for="cat-limit" class="block text-xs font-semibold text-slate-300 mb-1.5">Limit Batas Anggaran (Rp)</label>
          <input type="number" id="cat-limit" min="1" placeholder="Contoh: 1500000" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500">
        </div>

        <button type="submit" id="btn-submit-cat" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:from-emerald-500 flex items-center justify-center space-x-2 disabled:opacity-50">
          <span id="btn-text-cat">Tambah Kategori</span>
          <svg id="btn-spinner-cat" class="hidden animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
      </form>
    </div>
  </div>

  <!-- JAVASCRIPT LOGIC -->
  <script>
    let currentDetailRekening = null;
    let isBalanceHidden = false;
    let actualHeroBalance = 0;

    function formatRupiah(num) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
    }

    function formatShort(num) {
      if (!num || num <= 0) return '';
      if (num >= 1000000) {
        const val = num / 1000000;
        return (val % 1 === 0 ? val : val.toFixed(1)).toString().replace('.', ',') + 'jt';
      }
      if (num >= 1000) {
        const val = num / 1000;
        return (val % 1 === 0 ? val : val.toFixed(1)).toString().replace('.', ',') + 'rb';
      }
      return num.toString();
    }

    function getTodayString() {
      const today = new Date();
      return `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    }

    function toggleBalanceVisibility() {
      isBalanceHidden = !isBalanceHidden;
      const elem = document.getElementById('hero-main-nominal');
      if (isBalanceHidden) {
        elem.textContent = 'Rp ••••••••';
      } else {
        elem.textContent = formatRupiah(actualHeroBalance);
      }
    }

    function callServerBridge(funcName, params) {
      return new Promise((resolve, reject) => {
        if (typeof google === 'undefined' || !google.script || !google.script.run) {
          reject(new Error("Lingkungan google.script.run tidak tersedia. Jalankan di Apps Script Web App."));
          return;
        }
        google.script.run
          .withSuccessHandler(resolve)
          .withFailureHandler(reject)
          .callServer(funcName, params);
      });
    }

    function showToast(msg, isSuccess = true) {
      const toast = document.getElementById('toast');
      const content = document.getElementById('toast-content');
      content.className = isSuccess 
        ? "p-4 rounded-xl shadow-2xl text-sm font-medium bg-emerald-950/90 border border-emerald-600 text-emerald-200" 
        : "p-4 rounded-xl shadow-2xl text-sm font-medium bg-rose-950/90 border border-rose-600 text-rose-200";
      content.textContent = msg;
      toast.classList.remove('translate-x-full', 'opacity-0');
      setTimeout(() => toast.classList.add('translate-x-full', 'opacity-0'), 4000);
    }

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebar-overlay');
      sidebar.classList.toggle('-translate-x-full');
      overlay.classList.toggle('hidden');
    }

    function navTo(menu) {
      const pageTitle = document.getElementById('page-title');
      const viewBeranda = document.getElementById('view-beranda');
      const viewDompet = document.getElementById('view-dompet');
      const viewComingSoon = document.getElementById('view-coming-soon');
      const comingSoonTitle = document.getElementById('coming-soon-title');

      const menus = ['beranda', 'dompet', 'transaksi', 'anggaran', 'laporan'];
      menus.forEach(m => {
        const btn = document.getElementById('nav-' + m);
        if (btn) {
          btn.className = (m === menu)
            ? "w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition-all bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-md"
            : "w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all";
        }
      });

      viewBeranda.classList.add('hidden');
      viewDompet.classList.add('hidden');
      viewComingSoon.classList.add('hidden');

      if (menu === 'beranda') {
        pageTitle.textContent = "Beranda";
        viewBeranda.classList.remove('hidden');
        loadDashboardData();
      } else if (menu === 'dompet') {
        pageTitle.textContent = "Dompet & Rekening";
        viewDompet.classList.remove('hidden');
        loadDaftarRekening();
      } else {
        const titleMap = { transaksi: 'Transaksi', anggaran: 'Anggaran', laporan: 'Laporan' };
        pageTitle.textContent = titleMap[menu] || 'Menu';
        comingSoonTitle.textContent = `Fitur ${titleMap[menu] || 'Menu'} (Coming Soon)`;
        viewComingSoon.classList.remove('hidden');
      }

      const sidebar = document.getElementById('sidebar');
      if (!sidebar.classList.contains('-translate-x-full')) {
        toggleSidebar();
      }
    }

    // Modal Handlers
    function openModalTransaction() {
      document.getElementById('trx-tanggal').value = getTodayString();
      document.getElementById('modal-transaction').classList.remove('hidden');
    }
    function closeModalTransaction() {
      document.getElementById('modal-transaction').classList.add('hidden');
    }

    function openModalCategory() {
      document.getElementById('modal-category').classList.remove('hidden');
    }
    function closeModalCategory() {
      document.getElementById('modal-category').classList.add('hidden');
    }

    function openModalDetailRekening() {
      document.getElementById('modal-detail-rekening').classList.remove('hidden');
    }
    function closeModalDetailRekening() {
      document.getElementById('modal-detail-rekening').classList.add('hidden');
    }

    function openModalTambahRekening() {
      document.getElementById('form-rek-title').textContent = "Tambah Rekening Baru";
      document.getElementById('form-rek-id').value = "";
      document.getElementById('form-rek-nama').value = "";
      document.getElementById('form-rek-jenis').value = "Bank";
      document.getElementById('form-rek-saldo').value = "0";
      document.getElementById('modal-form-rekening').classList.remove('hidden');
    }

    function openModalEditRekening(rek) {
      document.getElementById('form-rek-title').textContent = "Ubah Rekening";
      document.getElementById('form-rek-id').value = rek.id;
      document.getElementById('form-rek-nama').value = rek.nama;
      document.getElementById('form-rek-jenis').value = rek.jenis;
      document.getElementById('form-rek-saldo').value = rek.saldoAwal;
      document.getElementById('modal-form-rekening').classList.remove('hidden');
    }

    function openModalEditRekeningFromDetail() {
      if (currentDetailRekening) {
        closeModalDetailRekening();
        openModalEditRekening(currentDetailRekening);
      }
    }

    function closeModalFormRekening() {
      document.getElementById('modal-form-rekening').classList.add('hidden');
    }

    // Load Dashboard Data
    async function loadDashboardData() {
      try {
        const data = await callServerBridge('getDashboardData');

        // Formatted Day & Date
        const now = new Date();
        const fullDayDateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        
        // Update Card Utama Hero
        document.getElementById('hero-today-date').textContent = fullDayDateStr;
        
        actualHeroBalance = data.todayStats.totalSaldo || 0;
        if (!isBalanceHidden) {
          document.getElementById('hero-main-nominal').textContent = formatRupiah(actualHeroBalance);
        }
        
        document.getElementById('hero-pemasukan').textContent = formatRupiah(data.todayStats.pemasukanHariIni);
        document.getElementById('hero-pengeluaran').textContent = formatRupiah(data.todayStats.pengeluaranHariIni);

        // Update Card Progress Pengeluaran Baru (Hari ke-X/31)
        const currentDayNum = now.getDate();
        const totalDaysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
        
        document.getElementById('progress-card-day-num').textContent = currentDayNum;
        document.getElementById('progress-card-days-total').textContent = `/ ${totalDaysInMonth}`;
        document.getElementById('progress-card-subtitle-day').textContent = `${currentDayNum} / ${totalDaysInMonth}`;

        // Date Range Subtitle (1 Agu 2026 - 31 Agu 2026)
        const firstDayStr = new Date(now.getFullYear(), now.getMonth(), 1).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        const lastDayStr = new Date(now.getFullYear(), now.getMonth() + 1, 0).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        document.getElementById('progress-card-date-range').textContent = `${firstDayStr} - ${lastDayStr}`;

        // Other Cards
        renderBudgetRealization(data.budgetRealization);
        renderTopExpenses(data.topExpenses);
        renderCalendarActivity(data.monthActivity);
        renderRecentTransactions(data.recentTransactions);

        renderCategoryDropdown(data.categories || []);
        renderRekeningDropdown(data.rekeningList || []);

      } catch (err) {
        console.error("Gagal memuat data dashboard:", err);
        showToast("Gagal memuat data: " + (err.message || err), false);
      }
    }

    // Load Rekening for Dompet Page
    async function loadDaftarRekening() {
      const grid = document.getElementById('rekening-cards-grid');
      grid.innerHTML = '<p class="col-span-3 text-center text-xs text-slate-500 py-12">Memuat daftar rekening...</p>';

      try {
        const list = await callServerBridge('getDaftarRekening');
        grid.innerHTML = '';

        if (!list || list.length === 0) {
          grid.innerHTML = `
            <div class="col-span-3 bg-slate-900 border border-slate-800 rounded-3xl p-12 text-center space-y-3">
              <p class="text-sm font-semibold text-slate-400">Belum ada rekening terdaftar.</p>
              <button onclick="openModalTambahRekening()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition-all">
                + Tambah Rekening Pertama
              </button>
            </div>
          `;
          return;
        }

        list.forEach(rek => {
          let badgeStyle = "bg-indigo-500/10 text-indigo-400 border-indigo-500/20";
          let iconUrl = "https://openmoji.org/data/color/svg/1F3E6.svg";

          if (rek.jenis === 'E-Wallet') {
            badgeStyle = "bg-teal-500/10 text-teal-400 border-teal-500/20";
            iconUrl = "https://openmoji.org/data/color/svg/1F4F1.svg";
          } else if (rek.jenis === 'Tunai') {
            badgeStyle = "bg-amber-500/10 text-amber-400 border-amber-500/20";
            iconUrl = "https://openmoji.org/data/color/svg/1F4B5.svg";
          }

          const card = document.createElement('div');
          card.className = "bg-slate-900 border border-slate-800 hover:border-indigo-500/50 rounded-3xl p-6 shadow-xl cursor-pointer transition-all hover:-translate-y-1 group flex flex-col justify-between space-y-6";
          card.onclick = () => loadDetailRekeningModal(rek.id);

          card.innerHTML = `
            <div class="flex items-start justify-between">
              <div class="flex items-center space-x-3">
                <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 group-hover:border-slate-700 transition-all">
                  <img src="${iconUrl}" class="w-6 h-6" alt="${rek.jenis}">
                </div>
                <div>
                  <h4 class="font-bold text-base text-white group-hover:text-indigo-300 transition-colors">${rek.nama}</h4>
                  <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${badgeStyle}">
                    ${rek.jenis}
                  </span>
                </div>
              </div>

              <div class="text-slate-500 group-hover:text-slate-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </div>
            </div>

            <div class="border-t border-slate-800/80 pt-4">
              <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Saldo Terkini</p>
              <h3 class="text-2xl font-extrabold text-white tracking-tight mt-1">${formatRupiah(rek.totalSaldo)}</h3>
            </div>
          `;

          grid.appendChild(card);
        });

      } catch (err) {
        showToast("Gagal memuat rekening: " + (err.message || err), false);
      }
    }

    async function loadDetailRekeningModal(idRekening) {
      openModalDetailRekening();
      document.getElementById('detail-rek-nama').textContent = "Memuat...";
      document.getElementById('detail-rek-saldo').textContent = "Rp ...";
      document.getElementById('detail-rek-trx-list').innerHTML = '<p class="text-xs text-slate-500 text-center py-4">Memuat transaksi...</p>';

      try {
        const detail = await callServerBridge('getDetailRekening', { id: idRekening });
        const rek = detail.rekening;
        currentDetailRekening = rek;

        document.getElementById('detail-rek-nama').textContent = rek.nama;
        document.getElementById('detail-rek-jenis-badge').textContent = rek.jenis;
        document.getElementById('detail-rek-saldo').textContent = formatRupiah(rek.totalSaldo);

        const listContainer = document.getElementById('detail-rek-trx-list');
        listContainer.innerHTML = '';

        if (!detail.recentTransactions || detail.recentTransactions.length === 0) {
          listContainer.innerHTML = '<p class="text-xs text-slate-500 text-center py-4">Belum ada transaksi di rekening ini.</p>';
          return;
        }

        detail.recentTransactions.forEach(t => {
          const isIncome = (t.jenis === 'Pemasukan');
          const div = document.createElement('div');
          div.className = "bg-slate-950/60 border border-slate-800 rounded-xl p-3 flex items-center justify-between";
          div.innerHTML = `
            <div>
              <h5 class="text-xs font-bold text-white">${t.keterangan || t.kategori}</h5>
              <p class="text-[10px] text-slate-400">${t.kategori} • ${t.tanggal}</p>
            </div>
            <span class="text-xs font-bold ${isIncome ? 'text-emerald-400' : 'text-rose-400'}">
              ${isIncome ? '+' : '-'}${formatRupiah(t.nominal)}
            </span>
          `;
          listContainer.appendChild(div);
        });

      } catch (err) {
        showToast("Gagal memuat detail rekening: " + (err.message || err), false);
        closeModalDetailRekening();
      }
    }

    async function submitFormRekening(e) {
      e.preventDefault();
      const btn = document.getElementById('btn-submit-form-rek');
      const btnText = document.getElementById('btn-text-form-rek');
      const btnSpinner = document.getElementById('btn-spinner-form-rek');

      const id = document.getElementById('form-rek-id').value;
      const nama = document.getElementById('form-rek-nama').value;
      const jenis = document.getElementById('form-rek-jenis').value;
      const saldoAwal = document.getElementById('form-rek-saldo').value;

      btn.disabled = true;
      btnText.textContent = "Menyimpan...";
      btnSpinner.classList.remove('hidden');

      try {
        let res;
        if (id) {
          res = await callServerBridge('editRekening', { id, nama, jenis, saldoAwal: Number(saldoAwal) });
        } else {
          res = await callServerBridge('tambahRekening', { nama, jenis, saldoAwal: Number(saldoAwal) });
        }

        showToast(res.message || "Rekening berhasil disimpan!");
        closeModalFormRekening();
        loadDaftarRekening();
      } catch (err) {
        showToast("Gagal simpan rekening: " + (err.message || err), false);
      } finally {
        btn.disabled = false;
        btnText.textContent = "Simpan Rekening";
        btnSpinner.classList.add('hidden');
      }
    }

    async function confirmHapusRekeningFromDetail() {
      if (!currentDetailRekening) return;
      if (confirm(`Apakah Anda yakin ingin menghapus rekening "${currentDetailRekening.nama}"?`)) {
        try {
          const res = await callServerBridge('hapusRekening', { id: currentDetailRekening.id });
          showToast(res.message || "Rekening berhasil dihapus!");
          closeModalDetailRekening();
          loadDaftarRekening();
        } catch (err) {
          showToast("Gagal menghapus rekening: " + (err.message || err), false);
        }
      }
    }

    function renderCategoryDropdown(categories) {
      const select = document.getElementById('trx-kategori');
      select.innerHTML = categories.length 
        ? '<option value="" disabled selected>-- Pilih Kategori --</option>' 
        : '<option value="Umum" selected>Umum</option>';
      
      categories.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat;
        opt.textContent = cat;
        opt.className = "bg-slate-900 text-white";
        select.appendChild(opt);
      });
    }

    function renderRekeningDropdown(rekeningList) {
      const select = document.getElementById('trx-rekening');
      select.innerHTML = rekeningList.length 
        ? '<option value="" disabled selected>-- Pilih Rekening --</option>' 
        : '<option value="Dompet Tunai" selected>Dompet Tunai</option>';
      
      rekeningList.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.nama;
        opt.textContent = `${r.nama} (${r.jenis})`;
        opt.className = "bg-slate-900 text-white";
        select.appendChild(opt);
      });
    }

    function renderBudgetRealization(budget) {
      document.getElementById('budget-total-spent').textContent = formatRupiah(budget.totalSpent);
      document.getElementById('budget-total-limit').textContent = formatRupiah(budget.totalLimit);
      
      const overallBar = document.getElementById('overall-budget-bar');
      const overallBadge = document.getElementById('overall-budget-badge');
      const pct = budget.percentage || 0;
      
      overallBar.style.width = `${Math.min(pct, 100)}%`;
      overallBadge.textContent = `${pct}%`;

      if (pct >= 90) overallBar.className = "bg-rose-500 h-3 rounded-full transition-all duration-500";
      else if (pct >= 70) overallBar.className = "bg-amber-500 h-3 rounded-full transition-all duration-500";
      else overallBar.className = "bg-indigo-500 h-3 rounded-full transition-all duration-500";

      const container = document.getElementById('budget-categories-list');
      container.innerHTML = '';

      if (!budget.categoriesList || budget.categoriesList.length === 0) {
        container.innerHTML = '<p class="text-xs text-slate-500 text-center py-4">Belum ada kategori anggaran.</p>';
        return;
      }

      budget.categoriesList.forEach(item => {
        const itemPct = item.percentage;
        let barColor = "bg-emerald-500";
        if (itemPct >= 90) barColor = "bg-rose-500";
        else if (itemPct >= 70) barColor = "bg-amber-500";

        const div = document.createElement('div');
        div.className = "space-y-1.5";
        div.innerHTML = `
          <div class="flex justify-between text-xs font-medium">
            <span class="text-slate-200">${item.kategori}</span>
            <span class="text-slate-400">${formatRupiah(item.spent)} / <span class="text-slate-500">${formatRupiah(item.limit)}</span></span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-2 overflow-hidden border border-slate-800">
            <div class="${barColor} h-2 rounded-full transition-all duration-500" style="width: ${Math.min(itemPct, 100)}%"></div>
          </div>
        `;
        container.appendChild(div);
      });
    }

    function renderTopExpenses(topList) {
      const container = document.getElementById('top-expenses-list');
      container.innerHTML = '';

      if (!topList || topList.length === 0) {
        container.innerHTML = '<p class="text-xs text-slate-500 text-center py-6">Belum ada pengeluaran bulan ini.</p>';
        return;
      }

      const highestNominal = topList[0].total || 1;

      topList.forEach((item, index) => {
        const widthPct = Math.round((item.total / highestNominal) * 100);
        const div = document.createElement('div');
        div.className = "bg-slate-800/40 border border-slate-800 rounded-2xl p-3.5 space-y-2";
        div.innerHTML = `
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2.5">
              <span class="w-6 h-6 rounded-lg bg-rose-500/10 text-rose-400 font-bold text-xs flex items-center justify-center border border-rose-500/20">#${index + 1}</span>
              <span class="text-xs font-semibold text-white">${item.kategori}</span>
            </div>
            <span class="text-xs font-bold text-rose-400">${formatRupiah(item.total)}</span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-1.5 overflow-hidden">
            <div class="bg-gradient-to-r from-rose-500 to-amber-500 h-1.5 rounded-full" style="width: ${widthPct}%"></div>
          </div>
        `;
        container.appendChild(div);
      });
    }

    function renderCalendarActivity(activityMap) {
      const container = document.getElementById('calendar-cells');
      container.innerHTML = '';

      const now = new Date();
      const year = now.getFullYear();
      const month = now.getMonth();

      const monthName = now.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
      document.getElementById('calendar-month-name').textContent = monthName;

      const firstDayIndex = new Date(year, month, 1).getDay();
      const totalDays = new Date(year, month + 1, 0).getDate();

      for (let i = 0; i < firstDayIndex; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = "h-11 sm:h-14 bg-slate-950/40 border border-slate-900/80 rounded-lg";
        container.appendChild(emptyCell);
      }

      const todayDate = now.getDate();

      for (let day = 1; day <= totalDays; day++) {
        const cell = document.createElement('div');
        const isToday = (day === todayDate);
        cell.className = `h-11 sm:h-14 p-1 border rounded-lg flex flex-col justify-between transition-all ${
          isToday ? 'bg-indigo-950/40 border-indigo-500/80 shadow-md shadow-indigo-500/10' : 'bg-slate-800/30 border-slate-800/80 hover:border-slate-700'
        }`;

        const dayData = activityMap[day] || { income: 0, expense: 0 };
        const incShort = formatShort(dayData.income);
        const expShort = formatShort(dayData.expense);

        cell.innerHTML = `
          <div class="flex justify-between items-center leading-none">
            <span class="text-[10px] font-bold ${isToday ? 'text-indigo-400 bg-indigo-500/20 px-1 rounded' : 'text-slate-400'}">${day}</span>
          </div>
          <div class="space-y-0.5 text-[8px] sm:text-[9px] font-semibold tracking-tighter overflow-hidden leading-tight">
            ${incShort ? `<div class="text-emerald-400 bg-emerald-950/60 px-0.5 py-0.2 rounded truncate">+${incShort}</div>` : ''}
            ${expShort ? `<div class="text-rose-400 bg-rose-950/60 px-0.5 py-0.2 rounded truncate">-${expShort}</div>` : ''}
          </div>
        `;

        container.appendChild(cell);
      }
    }

    function renderRecentTransactions(trxList) {
      const container = document.getElementById('recent-transactions-list');
      container.innerHTML = '';

      if (!trxList || trxList.length === 0) {
        container.innerHTML = '<p class="text-xs text-slate-500 text-center py-6">Belum ada transaksi recorded.</p>';
        return;
      }

      trxList.forEach(item => {
        const isIncome = (item.jenis === 'Pemasukan');
        const div = document.createElement('div');
        div.className = "bg-slate-800/40 border border-slate-800/80 rounded-2xl p-3.5 flex items-center justify-between hover:border-slate-700 transition-all";
        div.innerHTML = `
          <div class="flex items-center space-x-3 min-w-0">
            <div class="p-2 rounded-xl shrink-0 ${isIncome ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'}">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${isIncome ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>'}
              </svg>
            </div>
            <div class="min-w-0">
              <h4 class="text-xs font-bold text-white truncate">${item.keterangan || item.kategori}</h4>
              <p class="text-[10px] text-slate-400 truncate">${item.kategori} ${item.rekening ? '• ' + item.rekening : ''} • ${item.tanggal}</p>
            </div>
          </div>
          <div class="text-right shrink-0 ml-2">
            <span class="text-xs font-extrabold ${isIncome ? 'text-emerald-400' : 'text-rose-400'}">
              ${isIncome ? '+' : '-'}${formatRupiah(item.nominal)}
            </span>
          </div>
        `;
        container.appendChild(div);
      });
    }

    async function submitTransaksi(e) {
      e.preventDefault();
      const btn = document.getElementById('btn-submit-trx');
      const btnText = document.getElementById('btn-text-trx');
      const btnSpinner = document.getElementById('btn-spinner-trx');

      const jenis = document.querySelector('input[name="jenis"]:checked').value;
      const tanggal = document.getElementById('trx-tanggal').value;
      const rekening = document.getElementById('trx-rekening').value || 'Dompet Tunai';
      const kategori = document.getElementById('trx-kategori').value || 'Umum';
      const nominal = document.getElementById('trx-nominal').value;
      const keterangan = document.getElementById('trx-keterangan').value;

      btn.disabled = true;
      btnText.textContent = "Menyimpan...";
      btnSpinner.classList.remove('hidden');

      try {
        const res = await callServerBridge('simpanTransaksi', { tanggal, jenis, kategori, nominal, keterangan, rekening });
        showToast(res.message || "Transaksi berhasil disimpan!");
        closeModalTransaction();
        document.getElementById('trx-nominal').value = '';
        document.getElementById('trx-keterangan').value = '';
        await loadDashboardData();
      } catch (err) {
        showToast("Gagal: " + (err.message || err), false);
      } finally {
        btn.disabled = false;
        btnText.textContent = "Simpan Transaksi";
        btnSpinner.classList.add('hidden');
      }
    }

    async function submitKategori(e) {
      e.preventDefault();
      const btn = document.getElementById('btn-submit-cat');
      const btnText = document.getElementById('btn-text-cat');
      const btnSpinner = document.getElementById('btn-spinner-cat');

      const nama = document.getElementById('cat-nama').value;
      const limit = document.getElementById('cat-limit').value;

      btn.disabled = true;
      btnText.textContent = "Menyimpan...";
      btnSpinner.classList.remove('hidden');

      try {
        const res = await callServerBridge('tambahKategori', { kategori: nama, limit: limit });
        showToast(res.message || "Kategori berhasil ditambahkan!");
        closeModalCategory();
        document.getElementById('cat-nama').value = '';
        document.getElementById('cat-limit').value = '';
        await loadDashboardData();
      } catch (err) {
        showToast("Gagal: " + (err.message || err), false);
      } finally {
        btn.disabled = false;
        btnText.textContent = "Tambah Kategori";
        btnSpinner.classList.add('hidden');
      }
    }

    window.onload = function() {
      loadDashboardData();
    };
  </script>
</body>
</html>'''

with open('c:/Users/syahr/Documents/Fibud/gas/Index.html', 'w', encoding='utf-8') as f:
    f.write(template)

print('Generated Index.html with Python successfully!')
