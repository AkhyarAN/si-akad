<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - SIAKAD SMP</title>
    <meta name="description" content="Sistem Informasi Akademik SMP - Mengelola data akademik siswa secara digital">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --primary: #1E40AF;
            --primary-light: #3B82F6;
            --primary-dark: #1E3A8A;
            --secondary: #7C3AED;
            --secondary-light: #A78BFA;
            --accent: #06B6D4;
            --accent-light: #22D3EE;
            --success: #059669;
            --success-light: #34D399;
            --warning: #D97706;
            --warning-light: #FBBF24;
            --danger: #DC2626;
            --danger-light: #F87171;

            --bg-dark: #0F172A;
            --bg-card: #1E293B;
            --bg-card-hover: #334155;
            --bg-sidebar: #0B1120;
            --text-primary: #F1F5F9;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
            --border-color: #334155;

            --sidebar-width: 270px;
            --navbar-height: 70px;

            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(148, 163, 184, 0.1);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        body.theme-light {
            --bg-dark: #F8FAFC;
            --bg-card: #FFFFFF;
            --bg-card-hover: #F1F5F9;
            --bg-sidebar: #FFFFFF;
            --text-primary: #0F172A;
            --text-secondary: #334155;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(0, 0, 0, 0.08);
            --glass-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body.theme-color-purple {
            --primary: #6D28D9;
            --primary-light: #8B5CF6;
            --primary-dark: #4C1D95;
            --secondary: #DB2777;
            --secondary-light: #F472B6;
            --accent: #0EA5E9;
            --accent-light: #38BDF8;
        }

        body.theme-color-emerald {
            --primary: #047857;
            --primary-light: #10B981;
            --primary-dark: #064E3B;
            --secondary: #0369A1;
            --secondary-light: #0EA5E9;
            --accent: #F59E0B;
            --accent-light: #FBBF24;
        }

        body.theme-color-rose {
            --primary: #BE123C;
            --primary-light: #F43F5E;
            --primary-dark: #881337;
            --secondary: #4338CA;
            --secondary-light: #6366F1;
            --accent: #D97706;
            --accent-light: #F59E0B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Background decoration */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(30, 64, 175, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 50% 80%, rgba(6, 182, 212, 0.08) 0%, transparent 50%);
            z-index: 0;
            pointer-events: none;
        }

        /* === SIDEBAR === */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--glass-border);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .sidebar-brand .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.4);
        }

        .sidebar-brand .brand-text h1 {
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .sidebar-brand .brand-text p {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        .menu-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            padding: 16px 12px 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 10px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 2px;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary-light);
        }

        .menu-item.active {
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.2), rgba(124, 58, 237, 0.15));
            color: var(--primary-light);
            font-weight: 600;
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: linear-gradient(to bottom, var(--primary-light), var(--secondary));
            border-radius: 0 3px 3px 0;
        }

        .menu-item i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--glass-border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            background: var(--glass-bg);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
        }

        .user-info .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-info .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* === MAIN CONTENT === */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* === NAVBAR === */
        .navbar {
            min-height: var(--navbar-height);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--glass-border);
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .navbar-left h2 {
            font-size: 20px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .breadcrumb a {
            color: var(--primary-light);
            text-decoration: none;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-navbar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            background: var(--glass-bg);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 18px;
        }

        .btn-navbar:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 24px;
            cursor: pointer;
        }

        /* === PAGE CONTENT === */
        .page-content {
            padding: 28px 32px;
        }

        /* === CARDS === */
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 24px;
            min-width: 0;
            box-shadow: var(--glass-shadow);
            transition: all 0.3s ease;
        }

        .card:hover {
            border-color: rgba(59, 130, 246, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* === STAT CARDS === */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }

        .stat-card.blue::before { background: linear-gradient(90deg, var(--primary), var(--primary-light)); }
        .stat-card.purple::before { background: linear-gradient(90deg, var(--secondary), var(--secondary-light)); }
        .stat-card.cyan::before { background: linear-gradient(90deg, var(--accent), var(--accent-light)); }
        .stat-card.green::before { background: linear-gradient(90deg, var(--success), var(--success-light)); }
        .stat-card.yellow::before { background: linear-gradient(90deg, var(--warning), var(--warning-light)); }
        .stat-card.red::before { background: linear-gradient(90deg, var(--danger), var(--danger-light)); }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-card.blue .stat-icon { background: rgba(59, 130, 246, 0.15); color: var(--primary-light); }
        .stat-card.purple .stat-icon { background: rgba(167, 139, 250, 0.15); color: var(--secondary-light); }
        .stat-card.cyan .stat-icon { background: rgba(34, 211, 238, 0.15); color: var(--accent-light); }
        .stat-card.green .stat-icon { background: rgba(52, 211, 153, 0.15); color: var(--success-light); }
        .stat-card.yellow .stat-icon { background: rgba(251, 191, 36, 0.15); color: var(--warning-light); }
        .stat-card.red .stat-icon { background: rgba(248, 113, 113, 0.15); color: var(--danger-light); }

        .stat-info .stat-value {
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        .stat-info .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }

        /* === GRID LAYOUTS === */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }

        /* === TABLE === */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        thead th {
            background: rgba(30, 41, 59, 0.8);
            padding: 14px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }

        tbody td {
            padding: 14px 16px;
            font-size: 13.5px;
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
            color: var(--text-secondary);
        }

        tbody tr {
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        /* === BADGES === */
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .badge.bg-success { background: rgba(5, 150, 105, 0.2); color: var(--success-light); }
        .badge.bg-warning { background: rgba(217, 119, 6, 0.2); color: var(--warning-light); }
        .badge.bg-danger { background: rgba(220, 38, 38, 0.2); color: var(--danger-light); }
        .badge.bg-info { background: rgba(6, 182, 212, 0.2); color: var(--accent-light); }
        .badge.bg-secondary { background: rgba(100, 116, 139, 0.2); color: var(--text-muted); }
        .badge.bg-primary { background: rgba(59, 130, 246, 0.2); color: var(--primary-light); }

        /* === BUTTONS === */
        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.5);
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), var(--success-light));
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), var(--danger-light));
            color: white;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 12px;
            border-radius: 8px;
        }

        /* === FORMS === */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394A3B8' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 40px;
        }

        select.form-control option {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        /* === ALERTS === */
        .alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: rgba(5, 150, 105, 0.15);
            border: 1px solid rgba(52, 211, 153, 0.3);
            color: var(--success-light);
        }

        .alert-error, .alert-danger {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: var(--danger-light);
        }

        .alert-warning {
            background: rgba(217, 119, 6, 0.15);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: var(--warning-light);
        }

        .alert-info {
            background: rgba(6, 182, 212, 0.15);
            border: 1px solid rgba(34, 211, 238, 0.3);
            color: var(--accent-light);
        }

        /* === PAGINATION === */
        .pagination {
            display: flex;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 20px 0 0;
            justify-content: center;
        }

        .pagination li a, .pagination li span {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            border: 1px solid var(--border-color);
            background: var(--glass-bg);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination li.active span {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .pagination li a:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        /* === MODAL === */
        .modal-backdrop {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 200;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-backdrop.show {
            display: flex;
        }

        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 32px;
            width: 100%;
            max-width: 540px;
            max-height: 80vh;
            overflow-y: auto;
            animation: modalIn 0.3s ease;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 24px;
        }

        /* === ANIMATIONS === */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .animate-fade { animation: fadeIn 0.5s ease; }

        /* === RESPONSIVE === */
        @media (max-width: 1024px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block;
            }

            .page-content {
                padding: 20px 16px;
            }

            .navbar {
                padding: 12px 16px;
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .sidebar-menu {
                padding-bottom: 32px;
            }

            .navbar-left {
                width: 100%;
            }

            .navbar-right {
                width: 100%;
                justify-content: flex-end;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .hide-on-mobile {
                display: none !important;
            }
        }

        /* === EMPTY STATE === */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.3;
        }

        .empty-state p {
            font-size: 14px;
        }

        /* Chart container */
        .chart-container {
            position: relative;
            width: 100%;
            height: 300px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        /* Filter bar */
        .filter-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            align-items: end;
        }

        .filter-bar .form-group {
            margin-bottom: 0;
            min-width: 180px;
            flex: 1;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="brand-text">
                <h1>{{ \App\Models\Setting::get('app_name', 'SIAKAD') }}</h1>
                <p>{{ \App\Models\Setting::get('app_subtitle', 'SMP Digital System') }}</p>
            </div>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('kepala_sekolah'))
            <div class="menu-label">Master Data</div>
            <a href="{{ route('master.academic-years') }}" class="menu-item {{ request()->routeIs('master.academic-years') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Tahun Ajaran
            </a>
            <a href="{{ route('master.teachers') }}" class="menu-item {{ request()->routeIs('master.teachers') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Data Guru
            </a>
            <a href="{{ route('master.classes') }}" class="menu-item {{ request()->routeIs('master.classes') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Data Kelas
            </a>
            <a href="{{ route('master.subjects') }}" class="menu-item {{ request()->routeIs('master.subjects') ? 'active' : '' }}">
                <i class="bi bi-book-fill"></i> Mata Pelajaran
            </a>
            <a href="{{ route('master.schedules') }}" class="menu-item {{ request()->routeIs('master.schedules') ? 'active' : '' }}">
                <i class="bi bi-clock-fill"></i> Jadwal Pelajaran
            </a>
            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('master.dapodik') }}" class="menu-item {{ request()->routeIs('master.dapodik') ? 'active' : '' }}">
                <i class="bi bi-cloud-arrow-down-fill"></i> Sinkron Dapodik
            </a>
            <a href="{{ route('master.whatsapp') }}" class="menu-item {{ request()->routeIs('master.whatsapp') ? 'active' : '' }}">
                <i class="bi bi-whatsapp"></i> WhatsApp Gateway
            </a>
            <a href="{{ route('master.settings') }}" class="menu-item {{ request()->routeIs('master.settings') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Pengaturan Aplikasi
            </a>
            <a href="{{ route('master.backups') }}" class="menu-item {{ request()->routeIs('master.backups') ? 'active' : '' }}">
                <i class="bi bi-database-fill-down"></i> Backup Database
            </a>
            @endif
            @endif

            @if(!auth()->user()->hasRole('orang_tua'))
            <div class="menu-label">Akademik</div>
            <a href="{{ route('students.index') }}" class="menu-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Data Siswa
            </a>
            @if(auth()->user()->hasRole('wali_kelas'))
            <a href="{{ route('homeroom.index') }}" class="menu-item {{ request()->routeIs('homeroom.*') ? 'active' : '' }}">
                <i class="bi bi-house-heart-fill"></i> Kelas Saya
            </a>
            @endif
            <a href="{{ route('teaching-documents.index') }}" class="menu-item {{ request()->routeIs('teaching-documents.*') ? 'active' : '' }}">
                <i class="bi bi-folder-fill"></i> Perangkat Mengajar
            </a>
            @if(auth()->user()->hasRole('guru') || auth()->user()->hasRole('wali_kelas'))
            <a href="{{ route('teacher-attendances.index') }}" class="menu-item {{ request()->routeIs('teacher-attendances.*') ? 'active' : '' }}">
                <i class="bi bi-person-video3"></i> Riwayat Mengajar
            </a>
            <a href="{{ route('exam-plans.index') }}" class="menu-item {{ request()->routeIs('exam-plans.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check-fill"></i> Rencana Penilaian
            </a>
            @endif
            <a href="{{ route('attendance.index') }}" class="menu-item {{ request()->routeIs('attendance.index') || request()->routeIs('attendance.create') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill"></i> Absensi
            </a>
            <a href="{{ route('grades.index') }}" class="menu-item {{ request()->routeIs('grades.index') || request()->routeIs('grades.create') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark-fill"></i> Penilaian
            </a>
            @endif

            <div class="menu-label">Laporan</div>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('kepala_sekolah'))
            <a href="{{ route('teacher-attendances.report') }}" class="menu-item {{ request()->routeIs('teacher-attendances.report') ? 'active' : '' }}">
                <i class="bi bi-person-bounding-box"></i> Rekap Absen Guru
            </a>
            @endif
            @if(!auth()->user()->hasRole('orang_tua'))
            <a href="{{ route('attendance.report') }}" class="menu-item {{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> Rekap Absensi Siswa
            </a>
            @endif
            <a href="{{ route('grades.report') }}" class="menu-item {{ request()->routeIs('grades.report') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Rekap Nilai
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="user-info" style="flex: 1; min-width: 0;">
                    <div class="user-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->roles->first()?->name ?? 'User')) }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="flex-shrink: 0;">
                    @csrf
                    <button type="submit" class="btn-navbar" title="Logout" style="width: 34px; height: 34px; font-size: 14px;">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <header class="navbar">
            <div class="navbar-left" style="flex: 1; min-width: 0;">
                <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="bi bi-list"></i>
                </button>
                <div style="min-width: 0;">
                    <h2>{{ $title ?? 'Dashboard' }}</h2>
                    @if(isset($breadcrumb))
                    <div class="breadcrumb">
                        <a href="{{ route('dashboard') }}" style="flex-shrink: 0;">Home</a>
                        <i class="bi bi-chevron-right" style="font-size: 10px; flex-shrink: 0;"></i>
                        <span style="overflow: hidden; text-overflow: ellipsis;">{{ $breadcrumb }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="navbar-right">
                <div style="display: flex; gap: 6px; align-items: center; margin-right: 12px;">
                    <button class="btn-navbar" onclick="changeFontSize(10)" style="font-weight: bold; font-size: 14px;" title="Perbesar Teks">A+</button>
                    <button class="btn-navbar" onclick="changeFontSize(-10)" style="font-weight: bold; font-size: 12px;" title="Perkecil Teks">A-</button>
                </div>
                <button class="btn-navbar" onclick="toggleTheme()" title="Ganti Tema">
                    <i class="bi bi-moon-fill" id="theme-icon"></i>
                </button>
                <div class="btn-navbar hide-on-mobile" title="Tahun Ajaran Aktif" style="width: auto; padding: 0 14px; font-size: 12px; gap: 6px;">
                    <i class="bi bi-calendar-event" style="font-size: 14px;"></i>
                    {{ \App\Models\AcademicYear::getActive()?->full_name ?? 'Belum diset' }}
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content animate-fade">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Auto-dismiss alerts
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Chart.js global defaults for dark theme
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = '#94A3B8';
            Chart.defaults.borderColor = 'rgba(51, 65, 85, 0.5)';
            Chart.defaults.font.family = "'Inter', sans-serif";
        }
    </script>

    <script>
        // Set initial theme before body renders to prevent flash
        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('theme-light');
        }
        
        const savedColorTheme = localStorage.getItem('colorTheme');
        if (savedColorTheme) {
            document.body.classList.add(savedColorTheme);
        }

        const savedFontSize = localStorage.getItem('fontSize');
        if (savedFontSize) {
            document.body.style.zoom = (savedFontSize / 100);
            // Fallback for Firefox
            document.body.style.MozTransform = `scale(${savedFontSize / 100})`;
            document.body.style.MozTransformOrigin = 'top left';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme');
            const icon = document.getElementById('theme-icon');
            if (savedTheme === 'light' && icon) {
                icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            }
        });

        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('theme-icon');
            
            if (body.classList.contains('theme-light')) {
                body.classList.remove('theme-light');
                localStorage.setItem('theme', 'dark');
                if(icon) icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
            } else {
                body.classList.add('theme-light');
                localStorage.setItem('theme', 'light');
                if(icon) icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            }
        }

        function setColorTheme(themeClass) {
            const body = document.body;
            // Remove existing color themes
            body.classList.remove('theme-color-purple', 'theme-color-emerald', 'theme-color-rose');
            
            if (themeClass) {
                body.classList.add(themeClass);
                localStorage.setItem('colorTheme', themeClass);
            } else {
                localStorage.removeItem('colorTheme');
            }
        }

        function changeFontSize(step) {
            let currentSize = parseInt(localStorage.getItem('fontSize') || '100');
            currentSize += step;
            
            // Limit font size between 80% and 150%
            if (currentSize < 80) currentSize = 80;
            if (currentSize > 150) currentSize = 150;
            
            document.body.style.zoom = (currentSize / 100);
            // Fallback for Firefox
            document.body.style.MozTransform = `scale(${currentSize / 100})`;
            document.body.style.MozTransformOrigin = 'top left';
            
            localStorage.setItem('fontSize', currentSize);
        }
    </script>
    @yield('scripts')
</body>
</html>
