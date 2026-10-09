<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RawMat Control')</title>

    <!-- Skrip Cepat untuk Terapkan Tema Sebelum Halaman Selesai Render -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('rawmat_theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Font Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
    /* Default / Light Mode Variables */
    :root, [data-bs-theme="light"] {
        --body-bg: #f8fafc;
        --card-bg: #ffffff;
        --card-border: #e2e8f0;
        --text-main: #0f172a;
        --text-sub: #64748b;

        /* Card Stat Gradients (Light Mode) */
        --stat-red-bg: linear-gradient(135deg, #ffffff 0%, #fff1f2 100%);
        --stat-red-border: #fecdd3;
        --stat-red-text: #e11d48;

        --stat-green-bg: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
        --stat-green-border: #bbf7d0;
        --stat-green-text: #16a34a;

        --stat-yellow-bg: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
        --stat-yellow-border: #fde68a;
        --stat-yellow-text: #d97706;

        --stat-blue-bg: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
        --stat-blue-border: #bae6fd;
        --stat-blue-text: #0284c7;
    }

    /* Full Black Dark Mode Variables */
    [data-bs-theme="dark"] {
        --body-bg: #000000 !important;
        --card-bg: #121212 !important;
        --card-border: #27272a !important;
        --text-main: #ffffff !important;
        --text-sub: #a1a1aa !important;

        /* Card Stat Gradients (Dark Mode) */
        --stat-red-bg: linear-gradient(135deg, #18181b 0%, #2c1215 100%);
        --stat-red-border: #881337;
        --stat-red-text: #fb7185;

        --stat-green-bg: linear-gradient(135deg, #18181b 0%, #0e2a18 100%);
        --stat-green-border: #14532d;
        --stat-green-text: #4ade80;

        --stat-yellow-bg: linear-gradient(135deg, #18181b 0%, #2a1f0d 100%);
        --stat-yellow-border: #713f12;
        --stat-yellow-text: #facc15;

        --stat-blue-bg: linear-gradient(135deg, #18181b 0%, #0c2340 100%);
        --stat-blue-border: #075985;
        --stat-blue-text: #38bdf8;
    }

    html, body, main {
        background-color: var(--body-bg) !important;
        background-image: none !important;
        color: var(--text-main) !important;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    /* Judul & Teks di Dark Mode */
    [data-bs-theme="dark"] h1,
    [data-bs-theme="dark"] h2,
    [data-bs-theme="dark"] h3,
    [data-bs-theme="dark"] h4,
    [data-bs-theme="dark"] h5,
    [data-bs-theme="dark"] h6,
    [data-bs-theme="dark"] .h1,
    [data-bs-theme="dark"] .h2,
    [data-bs-theme="dark"] .h3,
    [data-bs-theme="dark"] .h4,
    [data-bs-theme="dark"] .text-title {
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .text-sub,
    [data-bs-theme="dark"] .text-muted {
        color: #a1a1aa !important;
    }

    /* Kolom Search, Select, & Input Group di Dark Mode */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .input-group-text {
        background-color: #18181b !important;
        border-color: #27272a !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .form-control::placeholder {
        color: #71717a !important;
    }

    [data-bs-theme="dark"] .form-select option {
        background-color: #18181b !important;
        color: #ffffff !important;
    }

    /* Card Stat Styling */
    .card-stat-red {
        background: var(--stat-red-bg) !important;
        border: 1px solid var(--stat-red-border) !important;
    }
    .card-stat-red .stat-number { color: var(--stat-red-text) !important; }

    .card-stat-green {
        background: var(--stat-green-bg) !important;
        border: 1px solid var(--stat-green-border) !important;
    }
    .card-stat-green .stat-number { color: var(--stat-green-text) !important; }

    .card-stat-yellow {
        background: var(--stat-yellow-bg) !important;
        border: 1px solid var(--stat-yellow-border) !important;
    }
    .card-stat-yellow .stat-number { color: var(--stat-yellow-text) !important; }

    .card-stat-blue {
        background: var(--stat-blue-bg) !important;
        border: 1px solid var(--stat-blue-border) !important;
    }
    .card-stat-blue .stat-number { color: var(--stat-blue-text) !important; }

    .card-main {
        background-color: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        color: var(--text-main) !important;
    }

    .text-title { color: var(--text-main) !important; }
    .text-sub { color: var(--text-sub) !important; }
    /* Card Header & Footer di Dark Mode */
[data-bs-theme="dark"] .card-header,
[data-bs-theme="dark"] .card-footer {
    background-color: #121212 !important;
    border-color: #27272a !important;
    color: #ffffff !important;
}

/* Memaksa elemen bergaya bg-white ikut gelap di Dark Mode */
[data-bs-theme="dark"] .bg-white {
    background-color: #121212 !important;
    border-color: #27272a !important;
    color: #ffffff !important;
}

/* Memastikan teks judul di dalam header kartu berwarna putih terang */
[data-bs-theme="dark"] .card-header h1,
[data-bs-theme="dark"] .card-header h2,
[data-bs-theme="dark"] .card-header h3,
[data-bs-theme="dark"] .card-header h4,
[data-bs-theme="dark"] .card-header h5,
[data-bs-theme="dark"] .card-header h6,
[data-bs-theme="dark"] .card-header span,
[data-bs-theme="dark"] .card-header div {
    color: #ffffff !important;
}
</style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-dark navbar-dark px-3 py-2 shadow-sm">
        <div class="container-fluid">
            
            {{-- Brand / Logo --}}
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-box-seam text-primary fs-4"></i>
                <span>RawMat Control</span>
            </a>

            {{-- Group Tombol Kanan (Dark Mode Toggle & Garis Tiga Berdampingan) --}}
            <div class="d-flex align-items-center gap-2 order-lg-last ms-auto me-2 me-lg-0">
                
                {{-- Tombol Dark Mode --}}
                <button id="themeToggle" class="btn btn-outline-secondary border-0 p-2 d-flex align-items-center justify-content-center rounded-circle" type="button" title="Ganti Tema">
                    <i id="themeIcon" class="bi bi-moon-stars text-warning fs-5"></i>
                </button>

                {{-- Tombol Garis Tiga (Navbar Toggler) --}}
                <button class="navbar-toggler border-0 p-2 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

            </div>

            {{-- Container Menu Navigasi yang BISA di-Collapse/Toggle --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold text-white' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('batches.*') ? 'active fw-bold text-white' : '' }}" href="{{ route('batches.index') }}">
                            <i class="bi bi-layers me-1"></i> Batch Produksi
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>

    <main class="py-4">
        <div class="container mb-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Single Bootstrap JS Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            
            const updateUI = (theme) => {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill text-warning fs-5';
                } else {
                    themeIcon.className = 'bi bi-moon-stars text-secondary fs-5';
                }
            };

            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateUI(currentTheme);

            themeToggleBtn.addEventListener('click', () => {
                const activeTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                
                document.documentElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('rawmat_theme', newTheme);
                updateUI(newTheme);
            });
        });
    </script>
</body>
</html>