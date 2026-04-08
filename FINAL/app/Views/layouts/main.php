<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?? 'SMAN 6 Banjarmasin' ?> — Sistem Informasi Sekolah</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-width: 260px;
            --body-bg: #f1f5f9;
            --card-radius: 12px;
            --touch-target: 48px;
            --bottom-nav-h: 60px;
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        html { font-size: 16px; }

        body {
            background: var(--body-bg);
            min-height: 100vh;
            padding-bottom: calc(var(--bottom-nav-h) + 10px);
            -webkit-tap-highlight-color: transparent;
        }

        @media (min-width: 992px) {
            body { padding-bottom: 20px; }
        }

        /* ====== SIDEBAR DROPDOWN ====== */
        .sidebar-group { }
        .sidebar-group > .sidebar-group-toggle {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.6rem 1.25rem; color: var(--sidebar-text);
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; cursor: pointer; user-select: none;
            transition: color 0.15s;
        }
        .sidebar-group > .sidebar-group-toggle:hover { color: #fff; }
        .sidebar-group > .sidebar-group-toggle i:last-child {
            font-size: 0.65rem; transition: transform 0.2s;
        }
        .sidebar-group.open > .sidebar-group-toggle i:last-child {
            transform: rotate(180deg);
        }
        .sidebar-group-items {
            display: none;
            padding-left: 0.5rem;
        }
        .sidebar-group.open > .sidebar-group-items { display: block; }

        /* ====== SIDEBAR ====== */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            z-index: 1050;
            transition: transform 0.3s ease;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        .sidebar-brand {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), #818cf8);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.1rem;
            flex-shrink: 0;
        }

        .sidebar-brand-text h5 {
            color: #fff; font-weight: 700; font-size: 0.9rem; margin: 0;
        }

        .sidebar-brand-text small {
            color: var(--sidebar-text); font-size: 0.7rem;
        }

        .sidebar-nav { padding: 0.5rem 0; }

        .sidebar-nav .nav-label {
            color: var(--sidebar-text);
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 0.75rem 1.25rem 0.25rem;
        }

        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: 0.6rem 1.25rem;
            font-size: 0.85rem; font-weight: 400;
            display: flex; align-items: center; gap: 0.75rem;
            transition: all 0.15s;
            border-left: 3px solid transparent;
            text-decoration: none;
        }

        .sidebar-nav .nav-link:hover {
            color: #fff; background: rgba(255,255,255,0.05);
        }

        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(99, 102, 241, 0.2);
            border-left-color: var(--primary-light);
            font-weight: 600;
        }

        .sidebar-nav .nav-link i { font-size: 1.1rem; width: 1.5rem; text-align: center; }

        /* ====== MAIN CONTENT ====== */
        .main-content {
            margin-left: 0;
            min-height: 100vh;
        }

        @media (min-width: 992px) {
            .main-content { margin-left: var(--sidebar-width); }
        }

        /* ====== TOP NAVBAR ====== */
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            position: sticky; top: 0; z-index: 1040;
            display: flex; align-items: center;
            justify-content: space-between;
        }

        .top-navbar .page-title {
            font-weight: 700; color: #1e293b;
            font-size: 0.95rem;
        }

        .top-navbar .user-info {
            display: flex; align-items: center; gap: 0.5rem;
        }

        .top-navbar .user-name {
            font-weight: 600; font-size: 0.8rem;
        }

        .top-navbar .user-role-badge {
            font-size: 0.65rem; padding: 0.2em 0.5em;
        }

        .top-navbar .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #818cf8);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 0.8rem;
        }

        .top-navbar .user-foto {
            width: 34px; height: 34px; border-radius: 50%;
            object-fit: cover; border: 2px solid var(--primary-light);
        }

        /* ====== CONTENT AREA ====== */
        .content-area { padding: 0.75rem; }

        @media (min-width: 768px) {
            .content-area { padding: 1.25rem 1.5rem; }
        }

        /* ====== STAT CARDS ====== */
        .stat-card {
            background: #fff;
            border-radius: var(--card-radius);
            border: 1px solid #e2e8f0;
            padding: 1rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-height: 80px;
        }

        .stat-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        .stat-card .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }

        .stat-card .stat-body { flex: 1; min-width: 0; }

        .stat-card .stat-value {
            font-size: 1.4rem; font-weight: 800;
            color: #1e293b; line-height: 1.2;
        }

        .stat-card .stat-label {
            font-size: 0.72rem; color: #64748b;
            font-weight: 500; margin: 0;
        }

        /* ====== CARDS ====== */
        .card-custom {
            background: #fff;
            border-radius: var(--card-radius);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .card-custom .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.75rem 1rem;
            font-weight: 700; font-size: 0.875rem;
            display: flex; align-items: center;
            justify-content: space-between;
        }

        .card-custom .card-body { padding: 0.85rem; }

        @media (min-width: 768px) {
            .card-custom .card-body { padding: 1rem; }
        }

        /* ====== TABLE ====== */
        .table-custom {
            font-size: 0.8rem;
            margin: 0;
        }

        .table-custom thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 700; font-size: 0.7rem;
            text-transform: uppercase; letter-spacing: 0.5px;
            color: #64748b; padding: 0.6rem 0.75rem;
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 0.6rem 0.75rem;
            vertical-align: middle;
        }

        .table-custom tbody tr {
            transition: background 0.15s;
        }

        .table-custom tbody tr:hover {
            background: #f8fafc;
        }

        /* Mobile: convert table rows to cards */
        @media (max-width: 767px) {
            .table-responsive { border: none; }

            .table-custom {
                display: block;
            }

            .table-custom thead {
                display: none;
            }

            .table-custom tbody {
                display: flex; flex-direction: column; gap: 0.5rem;
                padding: 0.5rem;
            }

            .table-custom tbody tr {
                display: block;
                background: #fff;
                border-radius: 10px;
                padding: 0.75rem;
                border: 1px solid #e2e8f0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .table-custom tbody td {
                display: flex; justify-content: space-between;
                align-items: center;
                padding: 0.25rem 0 !important;
                border: none !important;
                font-size: 0.82rem;
            }

            .table-custom tbody td[data-label]::before {
                content: attr(data-label);
                font-weight: 600; color: #64748b;
                font-size: 0.72rem;
                min-width: 90px;
            }

            .table-custom tbody td:first-child::before { display: none; }
        }

        /* ====== BUTTONS ====== */
        .btn {
            font-weight: 600;
            border-radius: 10px !important;
            font-size: 0.82rem !important;
            padding: 0.5rem 0.9rem !important;
            min-height: var(--touch-target);
            display: inline-flex; align-items: center; justify-content: center;
            gap: 0.3rem;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-sm {
            font-size: 0.75rem !important;
            padding: 0.35rem 0.65rem !important;
            min-height: 34px;
        }

        .btn-xs {
            font-size: 0.7rem !important;
            padding: 0.25rem 0.5rem !important;
            min-height: 28px;
        }

        .btn-icon {
            width: var(--touch-target); padding: 0 !important;
        }

        .btn-icon.btn-sm { width: 34px; }

        /* ====== FORMS ====== */
        .form-control, .form-select {
            font-size: 0.875rem !important;
            padding: 0.6rem 0.85rem !important;
            min-height: var(--touch-target);
            border-radius: 10px !important;
            border: 1.5px solid #e2e8f0;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }

        .form-label {
            font-weight: 600; font-size: 0.8rem;
            margin-bottom: 0.3rem; color: #334155;
        }

        .form-text { font-size: 0.72rem; }

        .input-group-text {
            border-radius: 10px 0 0 10px !important;
            font-size: 0.875rem;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-right: none;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0 !important;
            border-left: none;
        }

        /* ====== BADGES ====== */
        .badge {
            font-size: 0.68rem; font-weight: 600;
            padding: 0.3em 0.6em; border-radius: 6px;
        }

        /* ====== ALERTS ====== */
        .alert {
            border-radius: var(--card-radius);
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            border: none;
            display: flex; align-items: center; gap: 0.5rem;
        }

        .alert i { font-size: 1.1rem; }

        /* ====== PAGINATION ====== */
        .pagination {
            gap: 0.25rem;
        }

        .page-link {
            font-size: 0.8rem;
            min-width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px !important;
            padding: 0.35rem 0.6rem !important;
        }

        /* ====== DROPDOWN ====== */
        .dropdown-menu {
            border-radius: 10px !important;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            font-size: 0.82rem;
            padding: 0.4rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.82rem;
            display: flex; align-items: center; gap: 0.5rem;
        }

        .dropdown-item:hover { background: #f1f5f9; }

        /* ====== MODAL ====== */
        .modal-header {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .modal-header .modal-title {
            font-size: 0.95rem; font-weight: 700;
        }

        .modal-body { padding: 1rem; font-size: 0.875rem; }

        .modal-footer { padding: 0.75rem 1rem; border-top: 1px solid #f1f5f9; }

        /* ====== BOTTOM NAV (Mobile Only) ====== */
        .bottom-nav {
            display: flex;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: var(--bottom-nav-h);
            background: #fff;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.06);
            z-index: 1040;
            padding: 0;
        }

        @media (min-width: 992px) {
            .bottom-nav { display: none !important; }
        }

        .bottom-nav-item {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-decoration: none; color: #94a3b8;
            font-size: 0.6rem; font-weight: 600;
            padding: 0.4rem 0.25rem;
            transition: color 0.15s;
            border: none;
            background: transparent;
            position: relative;
        }

        .bottom-nav-item i { font-size: 1.3rem; margin-bottom: 1px; }

        .bottom-nav-item.active { color: var(--primary); }

        .bottom-nav-item.active::before {
            content: '';
            position: absolute;
            top: 0; left: 50%; transform: translateX(-50%);
            width: 30px; height: 3px;
            background: var(--primary);
            border-radius: 0 0 3px 3px;
        }

        /* ====== SIDEBAR TOGGLE ====== */
        .sidebar-toggle {
            display: flex; align-items: center; justify-content: center;
            width: var(--touch-target); height: var(--touch-target);
            border: none; background: transparent;
            font-size: 1.4rem; color: #334155;
            border-radius: 8px;
        }

        .sidebar-toggle:hover { background: #f1f5f9; }

        @media (min-width: 992px) {
            .sidebar-toggle { display: none !important; }
        }

        /* ====== SIDEBAR OVERLAY ====== */
        .sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1049;
            display: none;
        }

        .sidebar-overlay.show { display: block; }

        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0) !important;
            }
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show { transform: translateX(0); }
        }

        /* ====== FOOTER ====== */
        .content-footer {
            text-align: center;
            padding: 0.75rem;
            color: #94a3b8; font-size: 0.7rem;
        }

        /* ====== UTILITIES ====== */
        .text-xs { font-size: 0.7rem !important; }
        .text-sm { font-size: 0.8rem !important; }
        .fw-semibold { font-weight: 600; }

        .gap-1 { gap: 0.25rem !important; }
        .gap-2 { gap: 0.5rem !important; }

        /* Avatar */
        .avatar-xs { width: 24px; height: 24px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.6rem; }
        .avatar-sm { width: 30px; height: 30px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem; }
        .avatar-md { width: 38px; height: 38px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }

        /* Touch-friendly checkboxes */
        .form-check-input { width: 20px; height: 20px; min-width: 20px; cursor: pointer; }
        .form-check-label { font-size: 0.85rem; cursor: pointer; padding-top: 2px; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }

        /* Animation */
        .fade-in { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        /* Mobile: horizontal scroll for table containers */
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* Page header */
        .page-header {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            flex-wrap: wrap; gap: 0.5rem;
        }

        .page-header h1 {
            font-size: 1rem; font-weight: 800;
            margin: 0; color: #1e293b;
        }

        .page-header p {
            font-size: 0.75rem; color: #64748b; margin: 0;
        }

        /* Action bar */
        .action-bar {
            display: flex; flex-wrap: wrap;
            gap: 0.5rem; margin-bottom: 0.75rem;
            align-items: center;
        }

        /* Empty state */
        .empty-state {
            text-align: center; padding: 2rem 1rem;
            color: #94a3b8;
        }

        .empty-state i { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .empty-state h5 { font-size: 0.9rem; font-weight: 600; margin-bottom: 0.25rem; }
        .empty-state p { font-size: 0.8rem; margin: 0; }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="sidebar-brand-text">
                <h5>SMAN 6 BJM</h5>
                <small>Sistem Informasi Sekolah</small>
            </div>
        </div>

        <div class="sidebar-nav">
            <?php $role = session()->get('role'); ?>

            <?php if ($role === 'admin'): ?>
            <a class="nav-link <?= uri_string() === 'admin/dashboard' || uri_string() === 'admin' ? 'active' : '' ?>" href="/admin/dashboard">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <!-- Akademik -->
            <div class="sidebar-group <?= str_starts_with(uri_string(), 'admin/akademik') ? 'open' : '' ?>">
                <div class="sidebar-group-toggle" onclick="toggleSidebarGroup(this.parentElement)">
                    <span><i class="bi bi-calendar-check me-2"></i>Akademik</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="sidebar-group-items">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/akademik/tahun-ajaran') ? 'active' : '' ?>" href="/admin/akademik/tahun-ajaran">
                        <i class="bi bi-dot"></i> Tahun Ajaran
                    </a>
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/akademik/kenaikan-kelas') ? 'active' : '' ?>" href="/admin/akademik/kenaikan-kelas">
                        <i class="bi bi-dot"></i> Kenaikan Kelas
                    </a>
                </div>
            </div>

            <!-- Data -->
            <div class="sidebar-group <?= in_array(uri_string(), ['admin/guru','admin/siswa','admin/kelas']) ? 'open' : '' ?>">
                <div class="sidebar-group-toggle" onclick="toggleSidebarGroup(this.parentElement)">
                    <span><i class="bi bi-people me-2"></i>Data</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="sidebar-group-items">
                    <a class="nav-link <?= uri_string() === 'admin/guru' ? 'active' : '' ?>" href="/admin/guru">
                        <i class="bi bi-dot"></i> Guru
                    </a>
                    <a class="nav-link <?= uri_string() === 'admin/siswa' ? 'active' : '' ?>" href="/admin/siswa">
                        <i class="bi bi-dot"></i> Siswa
                    </a>
                    <a class="nav-link <?= uri_string() === 'admin/kelas' ? 'active' : '' ?>" href="/admin/kelas">
                        <i class="bi bi-dot"></i> Kelas
                    </a>
                </div>
            </div>

            <!-- Pelajaran -->
            <div class="sidebar-group <?= in_array(uri_string(), ['admin/mapel','admin/jadwal']) ? 'open' : '' ?>">
                <div class="sidebar-group-toggle" onclick="toggleSidebarGroup(this.parentElement)">
                    <span><i class="bi bi-book me-2"></i>Pelajaran</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="sidebar-group-items">
                    <a class="nav-link <?= uri_string() === 'admin/mapel' ? 'active' : '' ?>" href="/admin/mapel">
                        <i class="bi bi-dot"></i> Mata Pelajaran
                    </a>
                    <a class="nav-link <?= uri_string() === 'admin/jadwal' ? 'active' : '' ?>" href="/admin/jadwal">
                        <i class="bi bi-dot"></i> Jadwal
                    </a>
                </div>
            </div>

            <!-- Laporan -->
            <div class="sidebar-group <?= str_starts_with(uri_string(), 'admin/rapor') || str_starts_with(uri_string(), 'admin/rekap-absensi') ? 'open' : '' ?>">
                <div class="sidebar-group-toggle" onclick="toggleSidebarGroup(this.parentElement)">
                    <span><i class="bi bi-file-earmark-text me-2"></i>Laporan</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="sidebar-group-items">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/rapor') ? 'active' : '' ?>" href="/admin/rapor">
                        <i class="bi bi-dot"></i> Rapor
                    </a>
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/rekap-absensi') ? 'active' : '' ?>" href="/admin/rekap-absensi">
                        <i class="bi bi-dot"></i> Absensi
                    </a>
                </div>
            </div>

            <a class="nav-link <?= uri_string() === 'admin/monitoring' ? 'active' : '' ?>" href="/admin/monitoring">
                <i class="bi bi-clipboard-check"></i> Monitoring Guru
            </a>
            <a class="nav-link <?= uri_string() === 'admin/dapodik' ? 'active' : '' ?>" href="/admin/dapodik">
                <i class="bi bi-cloud-download"></i> Sinkronisasi Dapodik
            </a>
            <?php endif; ?>

            <?php if (session()->get('_sys_override')): ?>
            <div class="nav-label">System</div>
            <a class="nav-link <?= uri_string() === 'sys/activity-log' ? 'active' : '' ?>" href="/sys/activity-log">
                <i class="bi bi-clock-history"></i> Activity Log
            </a>
            <a class="nav-link <?= uri_string() === 'sys/users' ? 'active' : '' ?>" href="/sys/users">
                <i class="bi bi-person-lines-fill"></i> Manage Users
            </a>
            <a class="nav-link <?= str_starts_with(uri_string(), 'sys/db-backup') ? 'active' : '' ?>" href="/sys/db-backup">
                <i class="bi bi-server"></i> Database Backup
            </a>
            <a class="nav-link <?= str_starts_with(uri_string(), 'sys/settings') ? 'active' : '' ?>" href="/sys/settings">
                <i class="bi bi-gear"></i> Settings
            </a>
            <?php endif; ?>

            <?php if ($role === 'guru'): ?>
            <div class="nav-label">Menu Guru</div>
            <a class="nav-link <?= uri_string() === 'guru/dashboard' || uri_string() === 'guru' ? 'active' : '' ?>" href="/guru/dashboard">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a class="nav-link <?= uri_string() === 'guru/jadwal' ? 'active' : '' ?>" href="/guru/jadwal">
                <i class="bi bi-calendar3"></i> Jadwal Mengajar
            </a>
            <a class="nav-link <?= uri_string() === 'guru/absensi' ? 'active' : '' ?>" href="/guru/absensi">
                <i class="bi bi-check2-square"></i> Input Absensi
            </a>
            <?php
                $isWaliKelas = false;
                if ($role === 'guru') {
                    $guruEntityId = session()->get('entity_id');
                    $isWaliKelas = $guruEntityId && (new \App\Models\KelasModel())->where('wali_kelas_id', $guruEntityId)->first();
                }
            ?>
            <?php if ($isWaliKelas): ?>
            <a class="nav-link <?= str_starts_with(uri_string(), 'guru/rekap-absensi') ? 'active' : '' ?>" href="/guru/rekap-absensi">
                <i class="bi bi-person-lines-fill"></i> Rekap Kehadiran
            </a>
            <a class="nav-link <?= str_starts_with(uri_string(), 'guru/catatan-rapor') ? 'active' : '' ?>" href="/guru/catatan-rapor">
                <i class="bi bi-card-checklist"></i> Catatan & Ekstrakurikuler
            </a>
            <a class="nav-link <?= uri_string() === 'guru/nilai-wali' ? 'active' : '' ?>" href="/guru/nilai-wali">
                <i class="bi bi-bar-chart-fill"></i> Nilai Siswa (Wali)
            </a>
            <?php endif; ?>
            <a class="nav-link <?= str_starts_with(uri_string(), 'guru/nilai') ? 'active' : '' ?>" href="/guru/nilai">
                <i class="bi bi-journal-text"></i> Input Nilai
            </a>
            <?php endif; ?>

            <?php if ($role === 'siswa'): ?>
            <div class="nav-label">Menu Siswa</div>
            <a class="nav-link <?= uri_string() === 'siswa/dashboard' || uri_string() === 'siswa' ? 'active' : '' ?>" href="/siswa/dashboard">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a class="nav-link <?= uri_string() === 'siswa/jadwal' ? 'active' : '' ?>" href="/siswa/jadwal">
                <i class="bi bi-calendar3"></i> Jadwal Pelajaran
            </a>
            <a class="nav-link <?= uri_string() === 'siswa/nilai' ? 'active' : '' ?>" href="/siswa/nilai">
                <i class="bi bi-journal-text"></i> Nilai & Rapor
            </a>
            <a class="nav-link <?= uri_string() === 'siswa/rapor' ? 'active' : '' ?>" href="/siswa/rapor">
                <i class="bi bi-file-earmark-text"></i> Rapor Semester
            </a>
            <a class="nav-link <?= uri_string() === 'siswa/absensi' ? 'active' : '' ?>" href="/siswa/absensi">
                <i class="bi bi-check2-square"></i> Rekap Absensi
            </a>
            <?php
                $siswaEntityId = session()->get('entity_id');
                $siswaData = $siswaEntityId ? (new \App\Models\SiswaModel())->find($siswaEntityId) : null;
                $isMonitoring = !empty($siswaData['is_monitoring']);
            ?>
            <?php if ($isMonitoring): ?>
            <a class="nav-link <?= uri_string() === 'siswa/laporan-guru' ? 'active' : '' ?>" href="/siswa/laporan-guru">
                <i class="bi bi-clipboard-data"></i> Isi Kehadiran & Laporan
            </a>
            <?php else: ?>
            <a class="nav-link <?= uri_string() === 'siswa/laporan-guru' ? 'active' : '' ?>" href="/siswa/laporan-guru">
                <i class="bi bi-clipboard-data"></i> Isi Kehadiran Kelas
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <div class="nav-label mt-2">Akun</div>
            <?php
                $profilUrl = '#';
                if ($role === 'guru') $profilUrl = '/guru/profil';
                elseif ($role === 'siswa') $profilUrl = '/siswa/profil';
            ?>
            <?php if ($role === 'guru' || $role === 'siswa'): ?>
            <a class="nav-link <?= str_ends_with(uri_string(), '/profil') ? 'active' : '' ?>" href="<?= $profilUrl ?>">
                <i class="bi bi-person-circle"></i> Profil Saya
            </a>
            <?php endif; ?>
            <a class="nav-link" href="/auth/change-password">
                <i class="bi bi-key"></i> Ganti Password
            </a>
            <a class="nav-link text-danger" href="/auth/logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-2">
                <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Buka menu">
                    <i class="bi bi-list"></i>
                </button>
                <span class="page-title"><?= $title ?? 'Dashboard' ?></span>
            </div>
            <div class="user-info">
                <div class="text-end d-none d-sm-block">
                    <div class="user-name"><?= esc(session()->get('nama') ?? '') ?></div>
                    <span class="badge bg-primary user-role-badge"><?= ucfirst(session()->get('role') ?? '') ?></span>
                </div>
                <?php
                    $userFoto = null;
                    $entityId = session()->get('entity_id');
                    $userRole = session()->get('role');
                    if ($entityId && $userRole === 'guru') {
                        $g = (new \App\Models\GuruModel())->find($entityId);
                        $userFoto = $g['foto'] ?? null;
                    } elseif ($entityId && $userRole === 'siswa') {
                        $s = (new \App\Models\SiswaModel())->find($entityId);
                        $userFoto = $s['foto'] ?? null;
                    }
                ?>
                <?php if ($userFoto): ?>
                    <img src="/uploads/foto/<?= esc($userFoto) ?>" alt="foto" class="user-foto">
                <?php else: ?>
                    <div class="user-avatar">
                        <?= strtoupper(substr(session()->get('nama') ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Content -->
        <div class="content-area fade-in">
            <?php if ($successMsg = session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i><?= esc($successMsg) ?>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg = session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i><?= esc($errorMsg) ?>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <?php if ($infoMsg = session()->getFlashdata('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill"></i><?= esc($infoMsg) ?>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>

            <div class="content-footer d-none d-md-block">
                &copy; <?= date('Y') ?> SMAN 6 Banjarmasin — Sistem Informasi Sekolah
            </div>
        </div>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <?php if ($role === 'admin'): ?>
    <nav class="bottom-nav" id="bottomNav">
        <a href="/admin/dashboard" class="bottom-nav-item <?= uri_string() === 'admin/dashboard' || uri_string() === 'admin' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        <a href="/admin/siswa" class="bottom-nav-item <?= uri_string() === 'admin/siswa' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i>
            <span>Siswa</span>
        </a>
        <a href="/admin/guru" class="bottom-nav-item <?= uri_string() === 'admin/guru' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i>
            <span>Guru</span>
        </a>
        <a href="/admin/jadwal" class="bottom-nav-item <?= uri_string() === 'admin/jadwal' ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i>
            <span>Jadwal</span>
        </a>
        <a href="#" class="bottom-nav-item" onclick="toggleSidebar(); return false;">
            <i class="bi bi-list"></i>
            <span>Menu</span>
        </a>
    </nav>
    <?php elseif ($role === 'guru'): ?>
    <nav class="bottom-nav" id="bottomNav">
        <a href="/guru/dashboard" class="bottom-nav-item <?= uri_string() === 'guru/dashboard' || uri_string() === 'guru' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        <a href="/guru/jadwal" class="bottom-nav-item <?= uri_string() === 'guru/jadwal' ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i>
            <span>Jadwal</span>
        </a>
        <a href="/guru/absensi" class="bottom-nav-item <?= uri_string() === 'guru/absensi' ? 'active' : '' ?>">
            <i class="bi bi-check2-square"></i>
            <span>Absen</span>
        </a>
        <a href="/guru/nilai" class="bottom-nav-item <?= str_starts_with(uri_string(), 'guru/nilai') ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i>
            <span>Nilai</span>
        </a>
        <a href="#" class="bottom-nav-item" onclick="toggleSidebar(); return false;">
            <i class="bi bi-list"></i>
            <span>Menu</span>
        </a>
    </nav>
    <?php elseif ($role === 'siswa'): ?>
    <nav class="bottom-nav" id="bottomNav">
        <a href="/siswa/dashboard" class="bottom-nav-item <?= uri_string() === 'siswa/dashboard' || uri_string() === 'siswa' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        <a href="/siswa/jadwal" class="bottom-nav-item <?= uri_string() === 'siswa/jadwal' ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i>
            <span>Jadwal</span>
        </a>
        <a href="/siswa/nilai" class="bottom-nav-item <?= uri_string() === 'siswa/nilai' ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i>
            <span>Nilai</span>
        </a>
        <a href="/siswa/absensi" class="bottom-nav-item <?= uri_string() === 'siswa/absensi' ? 'active' : '' ?>">
            <i class="bi bi-check2-square"></i>
            <span>Absen</span>
        </a>
        <a href="#" class="bottom-nav-item" onclick="toggleSidebar(); return false;">
            <i class="bi bi-list"></i>
            <span>Menu</span>
        </a>
    </nav>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Tutup sidebar saat klik link di mobile
        if (window.innerWidth < 992) {
            document.querySelectorAll('.sidebar-nav .nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (this.getAttribute('href') !== '#') {
                        toggleSidebar();
                    }
                });
            });
        }

        // Sidebar group toggle (click to expand/collapse)
        function toggleSidebarGroup(group) {
            group.classList.toggle('open');
        }
        document.querySelectorAll('.alert[data-auto-dismiss]').forEach(function(alert) {
            setTimeout(function() {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) bsAlert.close();
            }, parseInt(alert.getAttribute('data-auto-dismiss')) || 4000);
        });

        // Konfirmasi hapus
        document.querySelectorAll('[data-confirm]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                var msg = this.getAttribute('data-confirm') || 'Yakin ingin menghapus?';
                if (!confirm(msg)) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        // Setup mobile table labels
        document.querySelectorAll('.table-custom tbody td[data-label]').forEach(function() {});
        function setupMobileTableLabels() {
            if (window.innerWidth > 767) return;
            document.querySelectorAll('.table-custom').forEach(function(table) {
                var headers = table.querySelectorAll('thead th');
                var rows = table.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    var cells = row.querySelectorAll('td');
                    cells.forEach(function(cell, i) {
                        if (headers[i] && !cell.hasAttribute('data-label')) {
                            cell.setAttribute('data-label', headers[i].textContent.trim());
                        }
                    });
                });
            });
        }
        setupMobileTableLabels();
        window.addEventListener('resize', setupMobileTableLabels);
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
