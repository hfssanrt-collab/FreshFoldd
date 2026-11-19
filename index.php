<?php
require_once 'auth.php';
require_login();

$adminName = (string)($_SESSION['admin_name'] ?? 'Admin');
$letters = preg_split('//u', $adminName, -1, PREG_SPLIT_NO_EMPTY) ?: ['A'];
$adminInitial = mb_strtoupper(implode('', array_slice($letters, 0, 2)), 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>FreshFold — Sistem Laundri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --green: #176b55;
            --dark: #10513f;
            --soft: #e8f5ef;
            --ink: #20332b;
            --muted: #7c8982;
            --line: #e7ede9;
            --bg: #f7f9f7;
            --white: #fff;
            --orange: #b97b24;
            --blue: #4777bd;
            --red: #c05e4e
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif
        }

        button,
        input,
        select {
            font: inherit
        }

        button {
            cursor: pointer
        }

        .app {
            display: flex;
            min-height: 100vh
        }

        .sidebar {
            width: 245px;
            flex-shrink: 0;
            background: #fff;
            border-right: 1px solid var(--line);
            padding: 25px 17px;
            display: flex;
            flex-direction: column
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 9px 32px
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: var(--green);
            color: white;
            display: grid;
            place-items: center;
            font-size: 22px
        }

        .brand-name {
            font: 800 21px Manrope, sans-serif;
            letter-spacing: -.8px
        }

        .brand-name span {
            color: var(--green)
        }

        .brand-sub {
            font-size: 10px;
            color: var(--muted);
            letter-spacing: 1px;
            margin-top: 2px
        }

        .nav-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.4px;
            color: #a0aaa4;
            margin: 12px 12px
        }

        .nav {
            display: grid;
            gap: 5px
        }

        .nav-btn {
            border: 0;
            background: transparent;
            color: #718078;
            text-align: left;
            padding: 13px 14px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 600
        }

        .nav-btn:hover,
        .nav-btn.active {
            background: var(--soft);
            color: var(--green)
        }

        .nav-btn span {
            display: inline-block;
            width: 28px;
            font-size: 16px
        }

        .side-bottom {
            margin-top: auto
        }

        .help {
            margin: 25px 4px 20px;
            padding: 15px;
            background: #f5f8f5;
            border: 1px solid var(--line);
            border-radius: 14px
        }

        .help b {
            font-size: 12px
        }

        .help p {
            font-size: 11px;
            color: var(--muted);
            line-height: 1.6
        }

        .help button {
            width: 100%;
            background: white;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 9px;
            color: var(--green);
            font-size: 11px;
            font-weight: 700
        }

        .profile {
            border-top: 1px solid var(--line);
            padding: 17px 6px 0;
            display: flex;
            align-items: center;
            gap: 10px
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e5efe8;
            color: var(--green);
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800
        }

        .profile b {
            font-size: 12px
        }

        .profile small {
            display: block;
            color: var(--muted);
            font-size: 10px;
            margin-top: 3px
        }

        .main {
            flex: 1;
            min-width: 0
        }

        .topbar {
            height: 76px;
            background: white;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 34px
        }

        .crumb {
            font-size: 12px;
            color: var(--muted)
        }

        .crumb b {
            color: var(--ink)
        }

        .date {
            background: var(--bg);
            padding: 10px 13px;
            border-radius: 9px;
            color: var(--muted);
            font-size: 12px
        }

        .content {
            max-width: 1450px;
            margin: auto;
            padding: 32px 34px 48px
        }

        .page {
            display: none
        }

        .page.active {
            display: block;
            animation: fade .2s ease
        }

        @keyframes fade {
            from {
                opacity: .5;
                transform: translateY(3px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 25px
        }

        h1 {
            font: 800 27px Manrope, sans-serif;
            letter-spacing: -.8px;
            margin: 0 0 7px
        }

        .sub {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
            line-height: 1.6
        }

        .primary,
        .secondary {
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 12px;
            font-weight: 700
        }

        .primary {
            border: 0;
            background: var(--green);
            color: white;
            box-shadow: 0 5px 14px #176b5520
        }

        .primary:hover {
            background: var(--dark)
        }

        .secondary {
            border: 1px solid var(--line);
            background: white;
            color: var(--ink)
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 22px
        }

        .stat,
        .panel {
            background: white;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 8px 28px #203e3008
        }

        .stat {
            padding: 19px
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600
        }

        .stat-icon {
            width: 35px;
            height: 35px;
            border-radius: 11px;
            background: var(--soft);
            color: var(--green);
            display: grid;
            place-items: center;
            font-size: 17px
        }

        .stat-value {
            font: 800 24px Manrope, sans-serif;
            letter-spacing: -.7px;
            margin: 15px 0 6px
        }

        .stat-note {
            font-size: 10px;
            color: var(--muted)
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(260px, .8fr);
            gap: 18px
        }

        .panel {
            padding: 21px;
            min-width: 0
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 18px
        }

        .panel-title {
            font: 800 15px Manrope, sans-serif;
            margin: 0
        }

        .panel-sub {
            font-size: 11px;
            color: var(--muted);
            margin-top: 5px
        }

        .link {
            border: 0;
            background: transparent;
            color: var(--green);
            font-weight: 700;
            font-size: 11px
        }

        .table-wrap {
            overflow-x: auto
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 590px
        }

        th {
            text-align: left;
            font-size: 10px;
            color: #98a29c;
            padding: 0 10px 12px;
            border-bottom: 1px solid var(--line);
            white-space: nowrap
        }

        td {
            padding: 14px 10px;
            border-bottom: 1px solid #f0f3f1;
            font-size: 11px;
            white-space: nowrap
        }

        tr:last-child td {
            border-bottom: 0
        }

        .order-id {
            font-weight: 800;
            color: var(--green)
        }

        .customer {
            font-weight: 700;
            font-size: 12px
        }

        .muted {
            font-size: 10px;
            color: var(--muted);
            margin-top: 4px
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 9px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 700
        }

        .status:before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor
        }

        .wait {
            background: #fff3df;
            color: var(--orange)
        }

        .process {
            background: #eaf2ff;
            color: var(--blue)
        }

        .done {
            background: #e8f5ed;
            color: #28764e
        }

        .taken {
            background: #eee;
            color: #68716c
        }

        .unpaid {
            background: #fff0ed;
            color: var(--red)
        }

        .quick {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px
        }

        .quick button {
            border: 1px solid var(--line);
            background: white;
            text-align: left;
            border-radius: 12px;
            padding: 14px
        }

        .quick button:hover {
            border-color: #b6d6c4
        }

        .quick .emoji {
            font-size: 19px;
            margin-bottom: 10px
        }

        .quick b {
            display: block;
            font-size: 11px
        }

        .quick small {
            display: block;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.5;
            margin-top: 5px
        }

        .progress-list {
            display: grid;
            gap: 16px
        }

        .progress-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            font-size: 11px
        }

        .progress-bar {
            grid-column: 1/-1;
            height: 7px;
            background: #eef2ef;
            border-radius: 9px;
            overflow: hidden
        }

        .fill {
            height: 100%;
            background: var(--green);
            border-radius: 9px
        }

        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px
        }

        .field,
        .select {
            width: 100%;
            border: 1px solid #e1e9e3;
            background: white;
            border-radius: 10px;
            padding: 12px;
            color: var(--ink);
            outline: none;
            font-size: 12px
        }

        .field:focus,
        .select:focus {
            border-color: #83b59b;
            box-shadow: 0 0 0 3px #176b5510
        }

        .search {
            flex: 1;
            min-width: 200px
        }

        .filter-select {
            width: auto;
            min-width: 155px
        }

        .form-panel {
            max-width: 900px
        }

        .form-title {
            font: 800 13px Manrope, sans-serif;
            margin: 3px 0 16px
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        .form-group {
            display: grid;
            gap: 8px
        }

        .form-group label {
            font-size: 11px;
            font-weight: 700;
            color: #53625a
        }

        .form-group.full {
            grid-column: 1/-1
        }

        .spacer {
            height: 27px
        }

        .price-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f2f8f4;
            border: 1px solid #dfede3;
            border-radius: 12px;
            padding: 17px;
            margin-top: 22px
        }

        .price-box span {
            font-size: 12px;
            color: #66776d;
            line-height: 1.7
        }

        .price-box strong {
            font: 800 22px Manrope, sans-serif;
            color: var(--green)
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 9px;
            margin-top: 21px
        }

        .tracking-hero {
            background: linear-gradient(125deg, #125640, #208064);
            color: white;
            border-radius: 18px;
            padding: 30px;
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 15px;
            overflow: hidden
        }

        .tracking-hero h2 {
            font: 800 25px Manrope, sans-serif;
            margin: 0 0 10px
        }

        .tracking-hero p {
            color: #d3e9df;
            font-size: 12px;
            line-height: 1.7;
            max-width: 400px
        }

        .tracking-form {
            display: flex;
            gap: 8px;
            max-width: 430px
        }

        .tracking-form input {
            border: 0;
            border-radius: 9px;
            padding: 13px;
            min-width: 0;
            flex: 1;
            font-size: 12px
        }

        .tracking-form button {
            border: 0;
            border-radius: 9px;
            background: #e4f4e9;
            color: var(--dark);
            padding: 0 15px;
            font-size: 12px;
            font-weight: 800
        }

        .laundry-emoji {
            display: grid;
            place-items: center;
            font-size: 85px
        }

        .timeline {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            margin: 25px 0
        }

        .step {
            position: relative;
            text-align: center;
            padding-top: 29px;
            font-size: 10px;
            color: #9aa59e
        }

        .step:before {
            content: '';
            position: absolute;
            top: 0;
            left: calc(50% - 9px);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #e9eeeb;
            border: 4px solid #e9eeeb;
            z-index: 2
        }

        .step:after {
            content: '';
            position: absolute;
            top: 8px;
            left: 50%;
            width: 100%;
            height: 3px;
            background: #e9eeeb
        }

        .step:last-child:after {
            display: none
        }

        .step.complete {
            color: var(--green);
            font-weight: 700
        }

        .step.complete:before {
            background: var(--green);
            border-color: #d8eee1
        }

        .step.complete:after {
            background: var(--green)
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px
        }

        .info-tile {
            background: #f8faf8;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 14px
        }

        .info-tile span {
            display: block;
            color: var(--muted);
            font-size: 10px;
            margin-bottom: 7px
        }

        .info-tile b {
            font-size: 12px
        }

        .empty {
            text-align: center;
            color: var(--muted);
            padding: 28px 12px;
            font-size: 12px
        }

        .toast {
            position: fixed;
            right: 22px;
            bottom: 22px;
            background: #203b30;
            color: white;
            padding: 13px 17px;
            border-radius: 10px;
            box-shadow: 0 10px 30px #0002;
            font-size: 12px;
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            transition: .2s;
            z-index: 50
        }

        .toast.show {
            opacity: 1;
            transform: none
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: #14251e70;
            display: none;
            place-items: center;
            padding: 20px;
            z-index: 40
        }

        .modal-backdrop.show {
            display: grid
        }

        .modal {
            background: white;
            border-radius: 17px;
            padding: 24px;
            width: min(100%, 430px)
        }

        .modal h3 {
            font: 800 19px Manrope, sans-serif;
            margin: 0 0 7px
        }

        .modal p {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 20px
        }

        .mobile-menu {
            display: none
        }

        @media(max-width:1050px) {
            .sidebar {
                width: 215px
            }

            .content {
                padding: 26px 22px
            }

            .topbar {
                padding: 0 22px
            }

            .stats {
                grid-template-columns: repeat(2, 1fr)
            }

            .layout {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:700px) {
            .sidebar {
                position: fixed;
                z-index: 35;
                inset: 0 auto 0 0;
                width: 250px;
                transform: translateX(-105%);
                transition: .25s;
                box-shadow: 10px 0 35px #0001
            }

            .sidebar.open {
                transform: none
            }

            .mobile-menu {
                display: inline-block;
                border: 1px solid var(--line);
                background: white;
                border-radius: 8px;
                padding: 8px;
                margin-right: 10px
            }

            .topbar {
                height: 65px;
                padding: 0 14px
            }

            .content {
                padding: 23px 13px
            }

            .heading {
                align-items: flex-start
            }

            h1 {
                font-size: 22px
            }

            .heading .primary {
                padding: 10px;
                font-size: 11px
            }

            .stat {
                padding: 14px
            }

            .stat-value {
                font-size: 20px
            }

            .stat-top {
                font-size: 10px
            }

            .stat-icon {
                width: 30px;
                height: 30px
            }

            .panel {
                padding: 15px
            }

            .form-grid {
                grid-template-columns: 1fr
            }

            .form-group.full {
                grid-column: auto
            }

            .tracking-hero {
                grid-template-columns: 1fr;
                padding: 22px
            }

            .laundry-emoji {
                display: none
            }

            .tracking-form {
                flex-direction: column
            }

            .tracking-form button {
                padding: 13px
            }

            .info-grid {
                grid-template-columns: 1fr
            }

            .step {
                font-size: 9px
            }

            .date {
                display: none
            }
        }

        .cat {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 700
        }

        .cat-Aset { background: #e8f5ed; color: #28764e }
        .cat-Kewajiban { background: #fff0ed; color: var(--red) }
        .cat-Ekuitas { background: #f2eaff; color: #8a65bb }
        .cat-Pendapatan { background: #eaf2ff; color: var(--blue) }
        .cat-Beban { background: #fff3df; color: var(--orange) }

        .btn-sm {
            border: 1px solid var(--line);
            background: white;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
            color: var(--ink)
        }

        .btn-sm:hover { border-color: #b6d6c4 }
        .btn-sm.danger { color: var(--red) }
        .btn-sm.danger:hover { border-color: #e8c3bc; background: #fff7f5 }

    </style>
</head>

<body>
    <div class="app">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <div class="brand-mark">✦</div>
                <div>
                    <div class="brand-name">fresh<span>fold</span></div>
                    <div class="brand-sub">LAUNDRY MANAGEMENT</div>
                </div>
            </div>
            <div class="nav-label">MENU UTAMA</div>
            <nav class="nav"><button class="nav-btn active"
                    data-page="dashboard"><span>▦</span>Dashboard</button><button class="nav-btn"
                    data-page="orders"><span>▤</span>Data Pesanan</button><button class="nav-btn"
                    data-page="new-order"><span>＋</span>Buat Pesanan</button><button class="nav-btn"
                    data-page="customers"><span>♙</span>Pelanggan</button><button class="nav-btn"
                    data-page="employees"><span>♧</span>Karyawan</button><button class="nav-btn"
                    data-page="services"><span>✧</span>Layanan</button>
                <button class="nav-btn" data-page="coa">
                    <span>▤</span>COA
                </button>
            </nav>
            <div class="nav-label" style="margin-top:25px">LAINNYA</div>
            <nav class="nav"><button class="nav-btn" data-page="admins"><span>♙</span>Data Admin</button><button
                    class="nav-btn" data-page="tracking"><span>⌕</span>Cek Status Laundry</button><button
                    class="nav-btn" data-page="reports"><span>▥</span>Laporan Ringkas</button><button
                    class="nav-btn" onclick="logout()" style="color:var(--red)"><span>⎋</span>Keluar</button></nav>
            <div class="side-bottom">
                <div class="help"><b>Butuh bantuan?</b>
                    <p>Kelola pesanan laundry jadi lebih mudah dan teratur.</p><button
                        onclick="toast('Hubungi admin FreshFold untuk bantuan.')">Pusat Bantuan ↗</button>
                </div>
                <div class="profile">
                    <div class="avatar"><?= htmlspecialchars($adminInitial, ENT_QUOTES, 'UTF-8') ?></div>
                    <div><b><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></b><small>Administrator</small></div>
                </div>
            </div>
        </aside>
        <main class="main">
            <header class="topbar">
                <div><button class="mobile-menu" id="mobileMenu">☰</button><span class="crumb">Workspace / <b
                            id="breadcrumb">Dashboard</b></span></div>
                <div class="date" id="todayDate"></div>
            </header>
            <div class="content">
                <section class="page active" id="page-dashboard">
                    <div class="heading">
                        <div>
                            <h1>Selamat pagi, Admin! 👋</h1>
                            <p class="sub">Ini ringkasan aktivitas laundry kamu hari ini.</p>
                        </div><button class="primary" onclick="navigate('new-order')">＋ Buat Pesanan Baru</button>
                    </div>
                    <div class="stats">
                        <div class="stat">
                            <div class="stat-top">Total Pesanan <span class="stat-icon">▤</span></div>
                            <div class="stat-value" id="statTotal">0</div>
                            <div class="stat-note">Seluruh transaksi tercatat</div>
                        </div>
                        <div class="stat">
                            <div class="stat-top">Sedang Diproses <span class="stat-icon"
                                    style="background:#eaf2ff;color:#5588ce">◷</span></div>
                            <div class="stat-value" id="statProcess">0</div>
                            <div class="stat-note">Pesanan belum selesai</div>
                        </div>
                        <div class="stat">
                            <div class="stat-top">Siap Diambil <span class="stat-icon"
                                    style="background:#fff3df;color:#bd812e">✓</span></div>
                            <div class="stat-value" id="statReady">0</div>
                            <div class="stat-note">Menunggu pelanggan</div>
                        </div>
                        <div class="stat">
                            <div class="stat-top">Pendapatan Dibayar <span class="stat-icon"
                                    style="background:#f2eaff;color:#8a65bb">↗</span></div>
                            <div class="stat-value" id="statRevenue">Rp0</div>
                            <div class="stat-note">Pembayaran terkonfirmasi</div>
                        </div>
                    </div>
                    <div class="layout">
                        <div class="panel">
                            <div class="panel-head">
                                <div>
                                    <h3 class="panel-title">Pesanan Terbaru</h3>
                                    <div class="panel-sub">Pantau transaksi laundry terbaru</div>
                                </div><button class="link" onclick="navigate('orders')">Lihat semua →</button>
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>NO. PESANAN</th>
                                            <th>PELANGGAN</th>
                                            <th>LAYANAN</th>
                                            <th>TOTAL</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentOrders"></tbody>
                                </table>
                            </div>
                        </div>
                        <div style="display:grid;gap:18px;align-content:start">
                            <div class="panel">
                                <div class="panel-head">
                                    <div>
                                        <h3 class="panel-title">Akses Cepat</h3>
                                        <div class="panel-sub">Menu yang sering digunakan</div>
                                    </div>
                                </div>
                                <div class="quick"><button onclick="navigate('new-order')">
                                        <div class="emoji">🧺</div><b>Pesanan Baru</b><small>Catat laundry masuk</small>
                                    </button><button onclick="navigate('orders')">
                                        <div class="emoji">📋</div><b>Kelola Pesanan</b><small>Perbarui status</small>
                                    </button><button onclick="navigate('tracking')">
                                        <div class="emoji">🔎</div><b>Cek Status</b><small>Lacak pesanan</small>
                                    </button><button onclick="navigate('reports')">
                                        <div class="emoji">📈</div><b>Laporan</b><small>Ringkasan transaksi</small>
                                    </button></div>
                            </div>
                            <div class="panel">
                                <div class="panel-head">
                                    <div>
                                        <h3 class="panel-title">Ringkasan Status</h3>
                                        <div class="panel-sub">Komposisi pesanan saat ini</div>
                                    </div>
                                </div>
                                <div class="progress-list" id="statusSummary"></div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="page" id="page-orders">
                    <div class="heading">
                        <div>
                            <h1>Data Pesanan</h1>
                            <p class="sub">Kelola semua transaksi laundry dalam satu tempat.</p>
                        </div><button class="primary" onclick="navigate('new-order')">＋ Pesanan Baru</button>
                    </div>
                    <div class="panel">
                        <div class="filters"><input class="field search" id="orderSearch"
                                placeholder="⌕ Cari nomor pesanan atau nama pelanggan..."><select
                                class="select filter-select" id="statusFilter">
                                <option value="all">Semua Status</option>
                                <option>Menunggu Proses</option>
                                <option>Sedang Diproses</option>
                                <option>Selesai</option>
                                <option>Sudah Diambil</option>
                            </select></div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>NO. PESANAN</th>
                                        <th>PELANGGAN</th>
                                        <th>LAYANAN</th>
                                        <th>BERAT</th>
                                        <th>TOTAL</th>
                                        <th>STATUS LAUNDRY</th>
                                        <th>PEMBAYARAN</th>
                                        <th>AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="allOrders"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <section class="page" id="page-new-order">
                    <div class="heading">
                        <div>
                            <h1>Buat Pesanan Baru</h1>
                            <p class="sub">Masukkan detail pelanggan dan layanan laundry.</p>
                        </div>
                    </div>
                    <div class="panel form-panel">
                        <form id="orderForm">
                            <h3 class="form-title">Informasi Pelanggan</h3>
                            <div class="form-grid">
                                <div class="form-group full">
                                    <label for="customerSelect">Pelanggan *</label>
                                    <select class="select" id="customerSelect" required>
                                        <option value="">-- Pilih pelanggan --</option>
                                    </select>
                                    <small style="color:var(--muted)">Belum terdaftar? Tambahkan dulu di menu Data Pelanggan.</small>
                                </div>
                            </div>
                            <div class="spacer"></div>
                            <h3 class="form-title">Detail Layanan</h3>
                            <div class="form-grid">
                                <select class="select" id="serviceType">
                                    <option value="">Memuat layanan...</option>
                                </select>
                                <div class="form-group"><label for="weight">Berat pakaian (kg) *</label><input
                                        class="field" id="weight" type="number" min="0.1" step="0.1" required
                                        placeholder="Contoh: 3"></div>
                                <div class="form-group"><label for="orderDate">Tanggal masuk *</label><input
                                        class="field" id="orderDate" type="date" required></div>
                                <div class="form-group"><label for="dueDate">Estimasi selesai *</label><input
                                        class="field" id="dueDate" type="date" required></div>
                                <div class="form-group full"><label for="orderNotes">Catatan (opsional)</label><input
                                        class="field" id="orderNotes" placeholder="Contoh: pisahkan pakaian putih">
                                </div>
                            </div>
                            <div class="price-box"><span>Estimasi total pembayaran<br><small id="priceDetail">Pilih
                                        layanan dan masukkan berat pakaian.</small></span><strong
                                    id="priceTotal">Rp0</strong></div>
                            <div class="form-actions"><button type="button" class="secondary"
                                    onclick="navigate('orders')">Batal</button><button type="submit"
                                    class="primary">Simpan Pesanan ↗</button></div>
                        </form>
                    </div>
                </section>
                <section class="page" id="page-tracking">
                    <div class="heading">
                        <div>
                            <h1>Cek Status Laundry</h1>
                            <p class="sub">Lacak proses laundry dengan nomor pesanan.</p>
                        </div>
                    </div>
                    <div class="tracking-hero">
                        <div>
                            <h2>Pakaian bersih, hati tenang.</h2>
                            <p>Masukkan nomor pesanan yang diberikan oleh admin untuk melihat status pengerjaan laundry kamu.</p>
                            <form class="tracking-form" id="trackingForm"><input id="trackingId"
                                    placeholder="Contoh: LD-001" required><button type="submit">Lacak Pesanan</button>
                            </form>
                        </div>
                        <div class="laundry-emoji">🧺</div>
                    </div>
                    <div id="trackingResult" style="margin-top:20px"></div>
                </section>

                <section class="page" id="page-customers">
                    <div class="heading">
                        <div>
                            <h1>Data Pelanggan</h1>
                            <p class="sub">Kelola data dari tabel <b>customers</b>.</p>
                        </div>
                    </div>

                    <div class="panel form-panel" style="margin-bottom:18px">
                        <form id="customerForm">
                            <h3 class="form-title">Tambah Pelanggan</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="custName">Nama *</label>
                                    <input class="field" id="custName" required maxlength="100" placeholder="Contoh: Rina Putri">
                                </div>
                                <div class="form-group">
                                    <label for="custPhone">Nomor HP *</label>
                                    <input class="field" id="custPhone" required maxlength="12" inputmode="tel" placeholder="Contoh: 081234567890">
                                </div>
                            </div>
                            <div style="margin-top:14px">
                                <button class="primary" type="submit">Simpan Pelanggan</button>
                            </div>
                        </form>
                    </div>

                    <div class="panel">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>NAMA</th>
                                        <th>NOMOR HP</th>
                                        <th>TERDAFTAR</th>
                                    </tr>
                                </thead>
                                <tbody id="customerTable">
                                    <tr>
                                        <td colspan="5" class="empty">Memuat data pelanggan...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <section class="page" id="page-employees">
                    <div class="heading">
                        <div>
                            <h1>Data Karyawan</h1>
                            <p class="sub">Kelola data seluruh staff dan pekerja laundry dari database.</p>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>NAMA KARYAWAN</th>
                                        <th>NOMOR HP</th>
                                        <th>JABATAN</th>
                                        <th>TANGGAL MASUK</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="employeeTable">
                                    <tr>
                                        <td colspan="6" class="empty">Memuat data karyawan...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <section class="page" id="page-services">
                    <div class="heading">
                        <div>
                            <h1>Data Layanan</h1>
                            <p class="sub">Daftar layanan laundry dan harga dari tabel <b>services</b>.</p>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>NAMA LAYANAN</th>
                                        <th>HARGA</th>
                                        <th>SATUAN</th>
                                        <th>KETERANGAN</th>
                                    </tr>
                                </thead>
                                <tbody id="serviceTable">
                                    <tr>
                                        <td colspan="5" class="empty">Memuat data layanan...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </section>
                    <section class="page" id="page-coa">
                        <div class="heading">
                            <div>
                                <h1>Chart of Accounts</h1>
                                <p class="sub">Kelola daftar akun keuangan FreshFold dari tabel <b>coa</b>.</p>
                            </div>
                            <div style="display:flex;gap:9px;flex-wrap:wrap">
                                <button class="secondary" onclick="cetakCoa()">🖨 Cetak COA</button>
                                <button class="primary" onclick="showCoaForm()">＋ Tambah Akun</button>
                            </div>
                        </div>

                        <div class="stats" id="coaStats">
                            <div class="stat"><div class="stat-top">Total Akun</div><div class="stat-value" id="coaTotal">0</div><div class="stat-note">Seluruh akun tercatat</div></div>
                            <div class="stat"><div class="stat-top">Aset</div><div class="stat-value" id="coaAset">0</div><div class="stat-note">Saldo normal Debit</div></div>
                            <div class="stat"><div class="stat-top">Kewajiban &amp; Ekuitas</div><div class="stat-value" id="coaKewEk">0</div><div class="stat-note">Saldo normal Kredit</div></div>
                            <div class="stat"><div class="stat-top">Pendapatan &amp; Beban</div><div class="stat-value" id="coaPenBeb">0</div><div class="stat-note">Akun laba rugi</div></div>
                        </div>

                        <div class="panel form-panel" id="coaForm" style="display:none;margin-bottom:18px">
                            <h3 class="form-title" id="coaFormTitle">Tambah Akun</h3>
                            <input type="hidden" id="oldCoaCode">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="coaCode">Kode Akun *</label>
                                    <input class="field" type="text" id="coaCode" maxlength="20" placeholder="Contoh: 1-1100">
                                </div>
                                <div class="form-group">
                                    <label for="coaName">Nama Akun *</label>
                                    <input class="field" type="text" id="coaName" maxlength="100" placeholder="Contoh: Kas">
                                </div>
                                <div class="form-group">
                                    <label for="coaCategory">Kategori *</label>
                                    <select class="select" id="coaCategory">
                                        <option value="Aset">Aset</option>
                                        <option value="Kewajiban">Kewajiban</option>
                                        <option value="Ekuitas">Ekuitas</option>
                                        <option value="Pendapatan">Pendapatan</option>
                                        <option value="Beban">Beban</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="coaBalance">Saldo Normal *</label>
                                    <select class="select" id="coaBalance">
                                        <option value="Debit">Debit</option>
                                        <option value="Kredit">Kredit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="secondary" onclick="hideCoaForm()">Batal</button>
                                <button type="button" class="primary" onclick="saveCoa()">Simpan Akun</button>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="filters">
                                <input class="field search" id="coaSearch" placeholder="⌕ Cari kode atau nama akun...">
                                <select class="select filter-select" id="coaFilter">
                                    <option value="all">Semua Kategori</option>
                                    <option>Aset</option>
                                    <option>Kewajiban</option>
                                    <option>Ekuitas</option>
                                    <option>Pendapatan</option>
                                    <option>Beban</option>
                                </select>
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>KODE AKUN</th>
                                            <th>NAMA AKUN</th>
                                            <th>KATEGORI</th>
                                            <th>SALDO NORMAL</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="coaTable">
                                        <tr><td colspan="5" class="empty">Memuat data COA...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                    <section class="page" id="page-admins">
                        <div class="heading">
                            <div>
                                <h1>Data Admin</h1>
                                <p class="sub">Menu untuk mengelola akun dari tabel <b>admins</b>.</p>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="empty">♙<br><b style="color:var(--ink)">Manajemen Admin</b>
                                <p>Bagian ini disiapkan untuk pengelolaan akun admin FreshFold.</p>
                            </div>
                        </div>
                    </section>
                    <section class="page" id="page-reports">
                        <div class="heading">
                            <div>
                                <h1>Laporan Ringkas</h1>
                                <p class="sub">Ringkasan transaksi dan pembayaran laundry.</p>
                            </div><button class="secondary" onclick="exportCSV()">↓ Unduh CSV</button>
                        </div>
                        <div class="stats">
                            <div class="stat">
                                <div class="stat-top">Total Transaksi</div>
                                <div class="stat-value" id="reportOrders">0</div>
                                <div class="stat-note">Semua pesanan</div>
                            </div>
                            <div class="stat">
                                <div class="stat-top">Pendapatan Dibayar</div>
                                <div class="stat-value" id="reportPaid">Rp0</div>
                                <div class="stat-note">Pembayaran terkonfirmasi</div>
                            </div>
                            <div class="stat">
                                <div class="stat-top">Belum Dibayar</div>
                                <div class="stat-value" id="reportUnpaid">Rp0</div>
                                <div class="stat-note">Menunggu pembayaran</div>
                            </div>
                            <div class="stat">
                                <div class="stat-top">Berat Laundry</div>
                                <div class="stat-value" id="reportWeight">0 kg</div>
                                <div class="stat-note">Total pakaian tercatat</div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-head">
                                <div>
                                    <h3 class="panel-title">Rincian Seluruh Pesanan</h3>
                                    <div class="panel-sub">Data transaksi terkini dari database</div>
                                </div>
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>NO. PESANAN</th>
                                            <th>PELANGGAN</th>
                                            <th>TANGGAL MASUK</th>
                                            <th>LAYANAN</th>
                                            <th>BERAT</th>
                                            <th>TOTAL</th>
                                            <th>PEMBAYARAN</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reportTable"></tbody>
                                </table>
                            </div>
                        </div>
                    </section>
            </div>
        </main>
    </div>
    <div class="modal-backdrop" id="manageModal">
        <div class="modal">
            <h3>Kelola Pesanan</h3>
            <p id="manageInfo"></p>
            <input type="hidden" id="manageId">
            <div class="form-group" style="margin-top:14px">
                <label for="manageStatus">Status laundry</label>
                <select class="select" id="manageStatus">
                    <option>Menunggu Proses</option>
                    <option>Sedang Diproses</option>
                    <option>Selesai</option>
                    <option>Sudah Diambil</option>
                </select>
            </div>
            <div class="form-group" style="margin-top:14px">
                <label for="managePayment">Status pembayaran</label>
                <select class="select" id="managePayment">
                    <option>Belum Lunas</option>
                    <option>Lunas</option>
                </select>
            </div>
            <div class="modal-actions" style="flex-wrap:wrap">
                <button type="button" class="secondary" onclick="sendReceiptFromManage()">💬 Kirim Struk</button>
                <button type="button" class="secondary" onclick="sendReadyFromManage()">🔔 Info Siap</button>
                <button type="button" class="secondary" onclick="closeManage()">Batal</button>
                <button type="button" class="primary" id="manageSave" onclick="saveManage()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
    <div class="modal-backdrop" id="receiptModal">
        <div class="modal">
            <h3>Pesanan Berhasil Dibuat ✅</h3>
            <p>Kirim struk ke WhatsApp pelanggan?</p>
            <div id="receiptPreview" style="background:#f2f8f4;border:1px solid #dfede3;border-radius:12px;padding:14px;font-size:12px;line-height:1.7;white-space:pre-wrap;margin-top:12px"></div>
            <div class="modal-actions" style="flex-wrap:wrap">
                <button type="button" class="secondary" onclick="closeReceipt()">Lewati</button>
                <button type="button" class="secondary" onclick="receiptOrder && copyMessage(receiptText(receiptOrder))">📋 Salin Pesan</button>
                <button type="button" class="primary" onclick="sendReceiptFromModal()">💬 Kirim ke WhatsApp</button>
            </div>
        </div>
    </div>
    <div class="modal-backdrop" id="readyModal">
        <div class="modal" style="max-height:92vh;overflow:auto">
            <h3>Laundry Sudah Siap 🧺</h3>
            <p>Kirim pemberitahuan ke pelanggan, lengkap dengan pilihan metode pembayaran, nomor rekening, dan QRIS.</p>
            <div id="readyPreview" style="background:#f2f8f4;border:1px solid #dfede3;border-radius:12px;padding:14px;font-size:12px;line-height:1.7;white-space:pre-wrap;margin-top:12px"></div>
            <div id="qrisBox" style="margin-top:14px;text-align:center">
                <img id="qrisImg" src="" alt="QRIS FreshFold" style="max-width:190px;width:100%;border:1px solid var(--line);border-radius:12px;padding:8px;background:#fff" onerror="this.parentNode.style.display='none'">
                <div class="muted" style="margin-top:8px">WhatsApp tidak bisa melampirkan gambar otomatis. Setelah chat terbuka, lampirkan gambar QRIS ini secara manual
                    (<a href="#" id="qrisDownload" download="qris-freshfold.png" style="color:var(--green);font-weight:700">unduh QRIS</a>).</div>
            </div>
            <div class="modal-actions" style="flex-wrap:wrap">
                <button type="button" class="secondary" onclick="closeReady()">Lewati</button>
                <button type="button" class="secondary" onclick="readyOrder && copyMessage(readyText(readyOrder))">📋 Salin Pesan</button>
                <button type="button" class="primary" onclick="sendReadyFromModal()">💬 Kirim ke WhatsApp</button>
            </div>
        </div>
    </div>
    <div class="toast" id="toast"></div>

    <script>
        // Jika sesi habis (API membalas 401), kembali ke halaman login
        const _origFetch = window.fetch;
        window.fetch = async (...args) => {
            const res = await _origFetch(...args);
            if (res.status === 401) location.href = 'login.php';
            return res;
        };

        function logout() {
            if (confirm('Yakin ingin keluar dari FreshFold?')) location.href = 'logout.php';
        }

        const rupiah = n => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(n);
        const iso = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        const today = new Date(),
            future = new Date();
        future.setDate(future.getDate() + 2);

        document.getElementById('todayDate').textContent = new Intl.DateTimeFormat('id-ID', {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        }).format(today);
        document.getElementById('orderDate').value = iso(today);
        document.getElementById('dueDate').value = iso(future);

        let orders = [];

        // AMBIL DATA DATABASE
        async function loadDatabaseData() {
            try {
                let response = await fetch('api.php?action=get_data');
                let data = await response.json();

                if (data.success) {
                    // 1. Set orders
                    orders = data.orders.map(o => ({
                        dbId: Number(o.id),
                        id: o.id_str,
                        name: o.name,
                        phone: o.phone,
                        service: o.service,
                        weight: Number(o.weight),
                        total: Number(o.total),
                        date: o.date ? o.date.split(' ')[0] : '',
                        due: o.due,
                        status: o.status === 'Menunggu' ? 'Menunggu Proses' : o.status,
                        payment: o.payment || 'Belum Lunas'
                    }));

                    // 2. Render Karyawan
                    let empTable = document.getElementById('employeeTable');
                    if (empTable && data.employees) {
                        empTable.innerHTML = data.employees.length ? data.employees.map(e => `
                            <tr>
                                <td><b>#EMP-${String(e.id).padStart(3, '0')}</b></td>
                                <td><div class="customer">${esc(e.name)}</div></td>
                                <td>${esc(e.phone)}</td>
                                <td>${esc(e.position)}</td>
                                <td>${esc(e.hire_date)}</td>
                                <td><span class="status done">${esc(e.status)}</span></td>
                            </tr>
                        `).join('') : '<tr><td colspan="6" class="empty">Belum ada data karyawan.</td></tr>';
                    }
                    // Render Layanan (tabel + dropdown)
                    let svcTable = document.getElementById('serviceTable');
                    let svcSelect = document.getElementById('serviceType');
                    if (data.services) {
                        if (svcTable) {
                            svcTable.innerHTML = data.services.length ? data.services.map(s => `
                            <tr>
                                <td><b>#SRV-${String(s.id).padStart(3, '0')}</b></td>
                                <td><div class="customer">${esc(s.service_name.trim())}</div></td>
                                <td>${rupiah(Number(s.price_per_unit))}</td>
                                <td>${esc(s.unit)}</td>
                                <td>${esc(s.description || '-')}</td>
                            </tr>
                        `).join('') : '<tr><td colspan="5" class="empty">Belum ada data layanan.</td></tr>';
                        }

                        if (svcSelect) {
                            let selected = svcSelect.value; // simpan pilihan supaya tidak reset saat reload
                            svcSelect.innerHTML = data.services.length ? data.services.map(s =>
                                `<option value="${s.id}" data-price="${Number(s.price_per_unit)}">${esc(s.service_name.trim())} — ${rupiah(Number(s.price_per_unit))}/${esc(s.unit)}</option>`
                            ).join('') : '<option value="">Belum ada layanan</option>';
                            if (selected) svcSelect.value = selected;
                        }
                    }
                    // 3. Render elemen lainnya
                    renderDashboard();
                    renderOrders();
                    renderReports();
                    updatePrice();
                    // Render Pelanggan (tabel + dropdown)
                    let custTable = document.getElementById('customerTable');
                    let custSelect = document.getElementById('customerSelect');

                    if (data.customers) {
                        if (custTable) {
                            custTable.innerHTML = data.customers.length ? data.customers.map(c => `
                                <tr>
                                    <td><b>#CUS-${String(c.id).padStart(3, '0')}</b></td>
                                    <td><div class="customer">${esc(c.name)}</div></td>
                                    <td>${esc(c.phone)}</td>
                                    <td>${esc(c.address || '-')}</td>
                                    <td>${esc((c.created_at || '').split(' ')[0])}</td>
                                </tr>
                            `).join('') : '<tr><td colspan="5" class="empty">Belum ada data pelanggan.</td></tr>';
                        }

                        if (custSelect) {
                            let selected = custSelect.value;
                            custSelect.innerHTML = '<option value="">-- Pilih pelanggan --</option>' +
                                data.customers.map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
                            if (selected) custSelect.value = selected;
                        }
                    }
                }
            } catch (err) {
                console.error("Gagal memuat data dari database:", err);
            }
        }

        const statuses = ['Menunggu Proses', 'Sedang Diproses', 'Selesai', 'Sudah Diambil'];
        const cls = s => ({
            'Menunggu Proses': 'wait',
            'Sedang Diproses': 'process',
            'Selesai': 'done',
            'Sudah Diambil': 'taken'
        } [s] || 'wait');
        const dateFmt = d => d ? new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }).format(new Date(d + 'T00:00:00')) : '-';
        const esc = s => String(s).replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));
        const badge = s => `<span class="status ${cls(s)}">${esc(s)}</span>`;
        const payBadge = s => `<span class="status ${s === 'Lunas' ? 'done' : 'unpaid'}">${esc(s)}</span>`;

        function toast(msg) {
            let el = document.getElementById('toast');
            el.textContent = msg;
            el.classList.add('show');
            clearTimeout(window.toastTimer);
            window.toastTimer = setTimeout(() => el.classList.remove('show'), 2600);
        }

        function navigate(page) {
            document.querySelectorAll('.page').forEach(p => p.classList.toggle('active', p.id === 'page-' + page));
            document.querySelectorAll('.nav-btn[data-page]').forEach(b => b.classList.toggle('active', b.dataset.page === page));
            let labels = {
                dashboard: 'Dashboard',
                orders: 'Data Pesanan',
                'new-order': 'Buat Pesanan',
                customers: 'Data Pelanggan',
                employees: 'Data Karyawan',
                services: 'Data Layanan',
                coa: 'Chart of Accounts',
                admins: 'Data Admin',
                tracking: 'Cek Status Laundry',
                reports: 'Laporan Ringkas'
            };
            document.getElementById('breadcrumb').textContent = labels[page] || 'Dashboard';
            document.getElementById('sidebar').classList.remove('open');
            if (page === 'dashboard') renderDashboard();
            if (page === 'orders') renderOrders();
            if (page === 'reports') renderReports();
            if (page === 'coa') {
                loadCoa();
            }
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        document.querySelectorAll('.nav-btn[data-page]').forEach(b => b.addEventListener('click', () => navigate(b.dataset.page)));
        document.getElementById('mobileMenu').onclick = () => document.getElementById('sidebar').classList.toggle('open');

        function renderDashboard() {
            document.getElementById('statTotal').textContent = orders.length;
            document.getElementById('statProcess').textContent = orders.filter(o => ['Menunggu Proses', 'Sedang Diproses'].includes(o.status)).length;
            document.getElementById('statReady').textContent = orders.filter(o => o.status === 'Selesai').length;
            document.getElementById('statRevenue').textContent = rupiah(orders.filter(o => o.payment === 'Lunas').reduce((s, o) => s + o.total, 0));
            let recent = [...orders].reverse().slice(0, 5);
            document.getElementById('recentOrders').innerHTML = recent.length ? recent.map(o => `<tr><td><span class="order-id">${esc(o.id)}</span></td><td><div class="customer">${esc(o.name)}</div><div class="muted">${esc(o.phone)}</div></td><td>${esc(o.service)}</td><td>${rupiah(o.total)}</td><td>${badge(o.status)}</td></tr>`).join('') : '<tr><td colspan="5" class="empty">Belum ada pesanan.</td></tr>';
            document.getElementById('statusSummary').innerHTML = statuses.map(s => {
                let count = orders.filter(o => o.status === s).length;
                return `<div class="progress-row"><span>${s}</span><span style="color:var(--muted)">${count} pesanan</span><div class="progress-bar"><div class="fill" style="width:${count / Math.max(1, orders.length) * 100}%"></div></div></div>`
            }).join('');
        }

        function renderOrders() {
            let q = (document.getElementById('orderSearch').value || '').toLowerCase(),
                f = document.getElementById('statusFilter').value;
            let list = [...orders].reverse().filter(o => (o.id.toLowerCase().includes(q) || o.name.toLowerCase().includes(q) || o.phone.includes(q)) && (f === 'all' || o.status === f));
            document.getElementById('allOrders').innerHTML = list.length ? list.map(o => `<tr><td><span class="order-id">${esc(o.id)}</span><div class="muted">${dateFmt(o.date)}</div></td><td><div class="customer">${esc(o.name)}</div><div class="muted">${esc(o.phone)}</div></td><td>${esc(o.service)}</td><td>${o.weight} kg</td><td><b>${rupiah(o.total)}</b></td><td>${badge(o.status)}</td><td>${payBadge(o.payment)}</td><td><button class="secondary" style="padding:7px 9px" onclick="openManage(${o.dbId})">Kelola</button></td></tr>`).join('') : '<tr><td colspan="8" class="empty">Pesanan tidak ditemukan.</td></tr>';
        }

        document.getElementById('orderSearch').addEventListener('input', renderOrders);
        document.getElementById('statusFilter').addEventListener('change', renderOrders);

        function updatePrice() {
            let s = document.getElementById('serviceType'),
                price = Number(s.options[s.selectedIndex].dataset.price || 0),
                w = Number(document.getElementById('weight').value || 0);
            document.getElementById('priceTotal').textContent = rupiah(Math.round(price * w));
            document.getElementById('priceDetail').textContent = `${rupiah(price)} × ${w || 0} kg`;
        }

        document.getElementById('serviceType').addEventListener('change', updatePrice);
        document.getElementById('weight').addEventListener('input', updatePrice);

        document.getElementById('orderForm').addEventListener('submit', async e => {
            e.preventDefault();
            let formData = new URLSearchParams();
            formData.append('customer_id', document.getElementById('customerSelect').value);

            let sel = document.getElementById('serviceType');
            formData.append('service_id', sel.value);
            formData.append('weight', document.getElementById('weight').value);
            formData.append('date', document.getElementById('orderDate').value);
            formData.append('due', document.getElementById('dueDate').value);

            let price = Number(sel.options[sel.selectedIndex].dataset.price || 0);
            let weight = Number(document.getElementById('weight').value);
            formData.append('total', Math.round(price * weight));

            try {
                let response = await fetch('api.php?action=add_order', {
                    method: 'POST',
                    body: formData
                });
                let result = await response.json();
                if (result.success) {
                    toast(result.message);
                    e.target.reset();
                    document.getElementById('orderDate').value = iso(today);
                    document.getElementById('dueDate').value = iso(future);
                    await loadDatabaseData();
                    navigate('orders');
                    const created = orders.find(o => o.id === result.order_code);
                    if (created) openReceipt(created);
                } else {
                    toast('Gagal menyimpan: ' + result.message);
                }
            } catch (err) {
                toast('Gagal terhubung ke server');
            }
        });
        document.getElementById('customerForm').addEventListener('submit', async e => {
            e.preventDefault();
            let formData = new URLSearchParams();
            formData.append('name', document.getElementById('custName').value.trim());
            formData.append('phone', document.getElementById('custPhone').value.trim());

            try {
                let response = await fetch('api.php?action=add_customer', {
                    method: 'POST',
                    body: formData
                });
                let result = await response.json();
                if (result.success) {
                    toast(result.message);
                    e.target.reset();
                    await loadDatabaseData(); // tabel dan dropdown ikut ter-update
                } else {
                    toast('Gagal menyimpan: ' + result.message);
                }
            } catch (err) {
                toast('Gagal terhubung ke server');
            }
        });

        function renderReports() {
            document.getElementById('reportOrders').textContent = orders.length;
            document.getElementById('reportPaid').textContent = rupiah(orders.filter(o => o.payment === 'Lunas').reduce((s, o) => s + o.total, 0));
            document.getElementById('reportUnpaid').textContent = rupiah(orders.filter(o => o.payment !== 'Lunas').reduce((s, o) => s + o.total, 0));
            document.getElementById('reportWeight').textContent = orders.reduce((s, o) => s + o.weight, 0).toLocaleString('id-ID') + ' kg';
            document.getElementById('reportTable').innerHTML = [...orders].reverse().map(o => `<tr><td><span class="order-id">${esc(o.id)}</span></td><td>${esc(o.name)}</td><td>${dateFmt(o.date)}</td><td>${esc(o.service)}</td><td>${o.weight} kg</td><td>${rupiah(o.total)}</td><td>${payBadge(o.payment)}</td></tr>`).join('') || '<tr><td colspan="7" class="empty">Belum ada data transaksi.</td></tr>';
        }

        // ====== STRUK WHATSAPP ======
        function waNumber(phone) {
            let d = String(phone || '').replace(/\D/g, '');
            if (d.startsWith('0')) d = '62' + d.slice(1);
            else if (d.startsWith('8')) d = '62' + d;
            return d;
        }

        function waLink(num, text) {
            return 'https://wa.me/' + num + '?text=' + encodeURIComponent(text);
        }

        async function copyMessage(text) {
            try {
                await navigator.clipboard.writeText(text);
            } catch (err) {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.cssText = 'position:fixed;opacity:0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
            }
            toast('Pesan disalin. Tinggal tempel (Ctrl+V) di chat WhatsApp.');
        }

        function receiptText(o) {
            const line = '------------------------';
            const out = [
                '*FRESHFOLD LAUNDRY*',
                '_Pakaian bersih, hati tenang_',
                '',
                `Halo Kak *${o.name}*,`,
                'Terima kasih sudah mempercayakan cucian Kakak kepada FreshFold. Pesanan Kakak sudah kami terima dan akan kami kerjakan dengan sepenuh hati.',
                '',
                line,
                '*DETAIL PESANAN*',
                line,
                `No. Pesanan : *${o.id}*`,
                `Tanggal Masuk : ${dateFmt(o.date)}`,
                `Estimasi Selesai : ${dateFmt(o.due)}`,
                `Layanan : ${o.service}`,
                `Berat : ${o.weight} kg`,
                `Total : *${rupiah(o.total)}*`,
                `Pembayaran : *${o.payment}*`,
                line,
                ''
            ];
            if (o.payment !== 'Lunas') {
                out.push(
                    '*Pilihan pembayaran*',
                    'Kakak bisa membayar secara Tunai, Transfer Bank, atau QRIS. Detailnya akan kami kirimkan saat cucian sudah siap diambil.',
                    ''
                );
            }
            out.push(
                'Kami akan langsung mengabari Kakak begitu cucian sudah bersih, wangi, dan siap diambil. Jika ada pertanyaan, silakan balas pesan ini ya.',
                '',
                'Salam hangat,',
                '*Tim FreshFold*'
            );
            return out.join('\n');
        }

        function sendReceipt(o) {
            const num = waNumber(o.phone);
            if (num.length < 10) {
                toast('Nomor HP pelanggan tidak valid');
                return;
            }
            window.open(waLink(num, receiptText(o)), '_blank');
        }

        // ====== PENGATURAN PEMBAYARAN (ISI DENGAN DATA ASLI) ======
        const PAYMENT_INFO = {
            bankName: 'BCA',                 // contoh: BCA
            accountNumber: '575754632',       // contoh: 1234567890
            accountName: 'FreshFold Laundry',              // contoh: FreshFold Laundry
            qrisImage: 'qris.jpeg',                     // file gambar QRIS, taruh di folder yang sama dengan index.php
            qrisUrl: ''                                // opsional: link QRIS yang bisa dibuka publik (jika kosong, QRIS dilampirkan manual)
        };

        function paymentConfigured() {
            return !Object.values(PAYMENT_INFO).some(v => String(v).startsWith('ISI_'));
        }

        function readyText(o) {
            const line = '------------------------';
            const head = [
                '*LAUNDRY KAMU SUDAH SIAP*',
                '',
                `Halo Kak *${o.name}*,`,
                'Kabar baik! Cucian kamu sudah bersih, wangi, dan rapi, siap diambil kapan saja.',
                '',
                line,
                '*DETAIL PESANAN*',
                line,
                `No. Pesanan : *${o.id}*`,
                `Layanan : ${o.service}`,
                `Berat : ${o.weight} kg`,
                `Total : *${rupiah(o.total)}*`
            ];
            if (o.payment === 'Lunas') {
                return head.concat([
                    'Pembayaran : *LUNAS*',
                    line,
                    '',
                    'Pembayaran kamu sudah kami terima, jadi tinggal datang dan bawa pulang cucian kamu. Kami tunggu kedatangannya ya.',
                    '',
                    'Terima kasih sudah memilih FreshFold.',
                    '',
                    'Salam hangat,',
                    '*Tim FreshFold*'
                ]).join('\n');
            }
            return head.concat([
                'Pembayaran : *Belum Lunas*',
                line,
                '',
                '*PILIHAN PEMBAYARAN*',
                'Kamu bisa memilih salah satu cara berikut:',
                '',
                '*1. Tunai*',
                'Bayar langsung saat pengambilan laundry.',
                '',
                '*2. Transfer Bank*',
                `Bank : ${PAYMENT_INFO.bankName}`,
                `No. Rekening : *${PAYMENT_INFO.accountNumber}*`,
                `Atas Nama : ${PAYMENT_INFO.accountName}`,
                '',
                '*3. QRIS*',
                PAYMENT_INFO.qrisUrl ? `Scan kode QRIS di sini: ${PAYMENT_INFO.qrisUrl}` : 'Scan kode QRIS yang kami kirimkan di chat ini.',
                '',
                line,
                '',
                'Mohon balas pesan ini dengan nomor pilihan Kakak (1, 2, atau 3). Jika sudah transfer atau scan QRIS, jangan lupa kirimkan bukti pembayarannya ya.',
                '',
                'Terima kasih sudah memilih FreshFold. Kami tunggu kedatangannya!',
                '',
                'Salam hangat,',
                '*Tim FreshFold*'
            ]).join('\n');
        }

        let readyOrder = null;

        function openReady(o) {
            if (!o) return;
            readyOrder = o;
            document.getElementById('readyPreview').textContent = readyText(o).replace(/[*_]/g, '');
            const showQris = o.payment !== 'Lunas';
            const box = document.getElementById('qrisBox');
            box.style.display = showQris ? 'block' : 'none';
            if (showQris) {
                document.getElementById('qrisImg').src = PAYMENT_INFO.qrisImage + '?v=' + Date.now();
                document.getElementById('qrisDownload').href = PAYMENT_INFO.qrisImage;
            }
            document.getElementById('readyModal').classList.add('show');
        }

        function closeReady() {
            document.getElementById('readyModal').classList.remove('show');
        }

        function sendReady(o) {
            if (!o) return;
            if (o.payment !== 'Lunas' && !paymentConfigured()) {
                toast('Isi dulu data rekening di PAYMENT_INFO (index.php)');
                return;
            }
            const num = waNumber(o.phone);
            if (num.length < 10) {
                toast('Nomor HP pelanggan tidak valid');
                return;
            }
            window.open(waLink(num, readyText(o)), '_blank');
        }

        function sendReadyFromModal() {
            sendReady(readyOrder);
            closeReady();
        }

        function sendReadyFromManage() {
            const o = orders.find(x => x.dbId === Number(document.getElementById('manageId').value));
            if (o) openReady(o);
        }

        let receiptOrder = null;

        function openReceipt(o) {
            receiptOrder = o;
            document.getElementById('receiptPreview').textContent = receiptText(o).replace(/[*_]/g, '');
            document.getElementById('receiptModal').classList.add('show');
        }

        function closeReceipt() {
            document.getElementById('receiptModal').classList.remove('show');
        }

        function sendReceiptFromModal() {
            if (receiptOrder) sendReceipt(receiptOrder);
            closeReceipt();
        }

        function sendReceiptFromManage() {
            const o = orders.find(x => x.dbId === Number(document.getElementById('manageId').value));
            if (o) sendReceipt(o);
        }

        function openManage(id) {
            const o = orders.find(x => x.dbId === Number(id));
            if (!o) {
                toast('Data pesanan tidak ditemukan');
                return;
            }
            document.getElementById('manageId').value = o.dbId;
            document.getElementById('manageInfo').textContent = `${o.id} • ${o.name} • ${o.service} • ${rupiah(o.total)}`;
            document.getElementById('manageStatus').value = o.status;
            document.getElementById('managePayment').value = o.payment === 'Lunas' ? 'Lunas' : 'Belum Lunas';
            document.getElementById('manageModal').classList.add('show');
        }

        function closeManage() {
            document.getElementById('manageModal').classList.remove('show');
        }

        async function saveManage() {
            const btn = document.getElementById('manageSave');
            const body = new URLSearchParams();
            body.append('order_id', document.getElementById('manageId').value);
            body.append('status', document.getElementById('manageStatus').value);
            body.append('payment_status', document.getElementById('managePayment').value);

            const idNow = Number(document.getElementById('manageId').value);
            const before = orders.find(x => x.dbId === idNow);
            const wasDone = before && before.status === 'Selesai';
            const newStatus = document.getElementById('manageStatus').value;

            btn.disabled = true;
            try {
                const response = await fetch('api.php?action=update_order', {
                    method: 'POST',
                    body
                });
                const result = await response.json();
                if (result.success) {
                    toast(result.message);
                    closeManage();
                    await loadDatabaseData();
                    if (newStatus === 'Selesai' && !wasDone) {
                        openReady(orders.find(x => x.dbId === idNow));
                    }
                } else {
                    toast('Gagal menyimpan: ' + result.message);
                }
            } catch (err) {
                toast('Gagal terhubung ke server');
            } finally {
                btn.disabled = false;
            }
        }

        document.getElementById('manageModal').addEventListener('click', e => {
            if (e.target.id === 'manageModal') closeManage();
        });
        document.getElementById('readyModal').addEventListener('click', e => {
            if (e.target.id === 'readyModal') closeReady();
        });
        document.getElementById('receiptModal').addEventListener('click', e => {
            if (e.target.id === 'receiptModal') closeReceipt();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeManage();
                closeReceipt();
                closeReady();
            }
        });

        // LOAD DATA SAAT PERTAMA KALI HALAMAN DIBUKA
        loadDatabaseData();


        let coaData = [];

        async function loadCoa() {
            try {
                const response = await fetch('api.php?action=get_coa');
                const data = await response.json();
                if (data.success) {
                    coaData = data.coa || [];
                    renderCoa();
                } else {
                    toast(data.message || 'Gagal memuat data COA');
                }
            } catch (error) {
                console.error(error);
                document.getElementById('coaTable').innerHTML = '<tr><td colspan="5" class="empty">Gagal mengambil data COA dari server.</td></tr>';
            }
        }

        function renderCoa() {
            const table = document.getElementById('coaTable');
            const q = (document.getElementById('coaSearch').value || '').toLowerCase();
            const f = document.getElementById('coaFilter').value;

            document.getElementById('coaTotal').textContent = coaData.length;
            document.getElementById('coaAset').textContent = coaData.filter(a => a.kategori === 'Aset').length;
            document.getElementById('coaKewEk').textContent = coaData.filter(a => ['Kewajiban', 'Ekuitas'].includes(a.kategori)).length;
            document.getElementById('coaPenBeb').textContent = coaData.filter(a => ['Pendapatan', 'Beban'].includes(a.kategori)).length;

            const list = coaData.filter(a =>
                (String(a.kode_akun).toLowerCase().includes(q) || String(a.nama_akun).toLowerCase().includes(q)) &&
                (f === 'all' || a.kategori === f)
            );

            table.innerHTML = list.length ? list.map(a => `
                <tr>
                    <td><span class="order-id">${esc(a.kode_akun)}</span></td>
                    <td><div class="customer">${esc(a.nama_akun)}</div></td>
                    <td><span class="cat cat-${esc(a.kategori)}">${esc(a.kategori)}</span></td>
                    <td>${esc(a.saldo_normal)}</td>
                    <td>
                        <button class="btn-sm" onclick="editCoa(decodeURIComponent('${encodeURIComponent(a.kode_akun)}'))">Edit</button>
                        <button class="btn-sm danger" onclick="deleteCoa(decodeURIComponent('${encodeURIComponent(a.kode_akun)}'))">Hapus</button>
                    </td>
                </tr>`).join('') : '<tr><td colspan="5" class="empty">Akun tidak ditemukan.</td></tr>';
        }


        function cetakCoa() {
            const q = (document.getElementById('coaSearch').value || '').toLowerCase();
            const f = document.getElementById('coaFilter').value;
            const list = coaData.filter(a =>
                (String(a.kode_akun).toLowerCase().includes(q) || String(a.nama_akun).toLowerCase().includes(q)) &&
                (f === 'all' || a.kategori === f)
            );

            if (!list.length) {
                toast('Tidak ada data COA untuk dicetak');
                return;
            }

            const tgl = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date());
            const info = f === 'all' ? 'Semua kategori' : 'Kategori: ' + f;
            const rows = list.map((a, i) => `
                <tr>
                    <td class="c">${i + 1}</td>
                    <td>${esc(a.kode_akun)}</td>
                    <td>${esc(a.nama_akun)}</td>
                    <td>${esc(a.kategori)}</td>
                    <td>${esc(a.saldo_normal)}</td>
                </tr>`).join('');

            const html = `<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Chart of Accounts - FreshFold</title>
<style>
    @page { size: A4; margin: 18mm 15mm }
    * { box-sizing: border-box }
    body { font-family: Arial, Helvetica, sans-serif; color: #20332b; font-size: 12px; margin: 0 }
    .head { border-bottom: 2px solid #176b55; padding-bottom: 10px; margin-bottom: 16px }
    .head h1 { margin: 0 0 4px; font-size: 20px; color: #176b55 }
    .head h2 { margin: 0; font-size: 14px }
    .meta { margin-top: 6px; color: #5d6b64; font-size: 11px }
    table { width: 100%; border-collapse: collapse }
    th { background: #e8f5ef; text-align: left; padding: 8px; border: 1px solid #c9d8cf; font-size: 11px }
    td { padding: 7px 8px; border: 1px solid #dde6e0 }
    td.c { text-align: center; width: 36px }
    tr { page-break-inside: avoid }
    .foot { margin-top: 14px; font-size: 11px; color: #5d6b64 }
</style></head><body>
    <div class="head">
        <h1>FreshFold</h1>
        <h2>Chart of Accounts (COA)</h2>
        <div class="meta">${esc(info)} &bull; Dicetak pada ${esc(tgl)}</div>
    </div>
    <table>
        <thead><tr><th>No</th><th>Kode Akun</th><th>Nama Akun</th><th>Kategori</th><th>Saldo Normal</th></tr></thead>
        <tbody>${rows}</tbody>
    </table>
    <div class="foot">Total: ${list.length} akun</div>
</body></html>`;

            const frame = document.createElement('iframe');
            frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
            document.body.appendChild(frame);
            const doc = frame.contentWindow.document;
            doc.open();
            doc.write(html);
            doc.close();

            setTimeout(() => {
                frame.contentWindow.focus();
                frame.contentWindow.print();
                setTimeout(() => frame.remove(), 1500);
            }, 250);
        }

        document.getElementById('coaSearch').addEventListener('input', renderCoa);
        document.getElementById('coaFilter').addEventListener('change', renderCoa);

        // Saldo normal otomatis mengikuti kategori
        document.getElementById('coaCategory').addEventListener('change', e => {
            document.getElementById('coaBalance').value = ['Aset', 'Beban'].includes(e.target.value) ? 'Debit' : 'Kredit';
        });

        function showCoaForm(account = null) {
            document.getElementById('coaForm').style.display = 'block';
            document.getElementById('coaFormTitle').textContent = account ? 'Edit Akun' : 'Tambah Akun';
            document.getElementById('oldCoaCode').value = account ? account.kode_akun : '';
            document.getElementById('coaCode').value = account ? account.kode_akun : '';
            document.getElementById('coaName').value = account ? account.nama_akun : '';
            document.getElementById('coaCategory').value = account ? account.kategori : 'Aset';
            document.getElementById('coaBalance').value = account ? account.saldo_normal : 'Debit';
            document.getElementById('coaCode').focus();
            document.getElementById('coaForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function hideCoaForm() {
            document.getElementById('coaForm').style.display = 'none';
            document.getElementById('oldCoaCode').value = '';
        }

        function editCoa(code) {
            const account = coaData.find(a => String(a.kode_akun) === String(code));
            if (account) showCoaForm(account);
        }

        async function saveCoa() {
            const oldCode = document.getElementById('oldCoaCode').value;
            const code = document.getElementById('coaCode').value.trim();
            const name = document.getElementById('coaName').value.trim();

            if (!code || !name) {
                toast('Kode akun dan nama akun wajib diisi');
                return;
            }

            const body = new URLSearchParams();
            body.append('kode_akun', code);
            body.append('nama_akun', name);
            body.append('kategori', document.getElementById('coaCategory').value);
            body.append('saldo_normal', document.getElementById('coaBalance').value);
            if (oldCode) body.append('old_kode_akun', oldCode);

            try {
                const response = await fetch('api.php?action=' + (oldCode ? 'update_coa' : 'add_coa'), {
                    method: 'POST',
                    body
                });
                const result = await response.json();
                toast(result.message || (result.success ? 'Berhasil disimpan' : 'Gagal menyimpan'));
                if (result.success) {
                    hideCoaForm();
                    await loadCoa();
                }
            } catch (err) {
                toast('Gagal terhubung ke server');
            }
        }

        async function deleteCoa(code) {
            if (!confirm('Hapus akun ' + code + '?')) return;

            const body = new URLSearchParams();
            body.append('kode_akun', code);

            try {
                const response = await fetch('api.php?action=delete_coa', {
                    method: 'POST',
                    body
                });
                const result = await response.json();
                toast(result.message || (result.success ? 'Akun dihapus' : 'Gagal menghapus'));
                if (result.success) await loadCoa();
            } catch (err) {
                toast('Gagal terhubung ke server');
            }
        }
    </script>
</body>

</html>