<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal Lab - Lab Management</title>

    {{-- Hanya font dari Google (opsional, bisa dihapus kalau mau lebih ringan lagi) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ─── RESET ─────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --g9: #1A2517;
            --g8: #2d3d29;
            --g7: #3d5438;
            --acc: #ACC8A2;
            --acc2: #8ab87e;
            --white: #fff;
            --bg: #f0f4ef;
            --border: #e8f0e6;
            --text: #374151;
            --muted: #9ca3af;
            --r: 12px;
            --shadow: 0 2px 12px rgba(0,0,0,.08);
        }

        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ─── ANIMATIONS ────────────────────── */
        @keyframes slideDown {
            from { transform: translateY(-64px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes fadeUp {
            from { transform: translateY(16px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes shimmer {
            0%   { background-position: -600px 0; }
            100% { background-position:  600px 0; }
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(16px) scale(.97); }
            to   { opacity: 1; transform: none; }
        }
        @keyframes panelIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: none; }
        }

        /* ─── NAVBAR ────────────────────────── */
        .navbar {
            position: sticky; top: 0; z-index: 40;
            background: linear-gradient(135deg, var(--g9), var(--g8));
            box-shadow: 0 2px 16px rgba(0,0,0,.3);
            animation: slideDown .4s cubic-bezier(.16,1,.3,1) both;
        }
        .navbar-inner {
            max-width: 1280px; margin: auto;
            padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            height: 60px;
        }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: rgba(172,200,162,.15);
            display: flex; align-items: center; justify-content: center;
            transition: background .2s;
        }
        .brand-icon:hover { background: rgba(172,200,162,.25); }
        .brand-name { font-family: 'Outfit', sans-serif; font-weight: 700; color: #fff; font-size: 15px; line-height: 1.2; }
        .brand-sub  { font-size: 11px; color: rgba(172,200,162,.5); }

        .nav-actions { display: flex; align-items: center; gap: 6px; }
        .nav-link {
            font-size: 13px; font-weight: 600;
            color: rgba(172,200,162,.6); text-decoration: none;
            padding: 6px 12px; border-radius: 8px;
            transition: color .15s, background .15s;
        }
        .nav-link:hover { color: var(--acc); background: rgba(172,200,162,.08); }
        .nav-btn {
            font-size: 12px; font-weight: 600; text-decoration: none;
            padding: 6px 14px; border-radius: 9px;
            border: 1px solid rgba(172,200,162,.3); color: var(--acc);
            transition: background .15s, transform .15s;
        }
        .nav-btn:hover { background: rgba(172,200,162,.1); transform: translateY(-1px); }

        /* ─── HERO ──────────────────────────── */
        .hero {
            background: linear-gradient(135deg, var(--g9) 0%, var(--g8) 55%, var(--g7) 100%);
            padding: 2rem 1.5rem 3.5rem;
            animation: fadeUp .5s .08s cubic-bezier(.16,1,.3,1) both;
        }
        .hero-inner { max-width: 1280px; margin: auto; }
        .hero-eyebrow {
            font-size: 11px; font-weight: 600; letter-spacing: .12em;
            text-transform: uppercase; color: rgba(172,200,162,.55);
            margin-bottom: 5px; text-align: center;
        }
        .hero-title {
            font-family: 'Outfit', sans-serif; font-weight: 800;
            font-size: clamp(1.5rem, 4vw, 2.1rem);
            color: #fff; letter-spacing: -.02em; margin-bottom: 4px;
            text-align: center;
        }
        .hero-desc { font-size: 13px; color: rgba(172,200,162,.55); margin-bottom: 14px; text-align: center; }
        .hero-inner { text-align: center; }
        .period-badge {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 5px 13px; border-radius: 999px;
            background: rgba(172,200,162,.1); border: 1px solid rgba(172,200,162,.22);
            font-size: 13px; font-weight: 600; color: var(--acc);
            transition: background .2s;
        }
        .period-badge:hover { background: rgba(172,200,162,.18); }

        /* ─── LEGEND ────────────────────────── */
        .legend {
            display: flex; flex-wrap: wrap; gap: 7px; margin-top: 13px;
            animation: fadeUp .5s .14s cubic-bezier(.16,1,.3,1) both;
            justify-content: center;
        }
        .legend-item {
            display: flex; align-items: center; gap: 7px;
            padding: 5px 11px; border-radius: 8px;
            background: rgba(172,200,162,.07); border: 1px solid rgba(172,200,162,.14);
            transition: background .15s, transform .15s;
            cursor: default;
        }
        .legend-item:hover { background: rgba(172,200,162,.14); transform: translateY(-1px); }
        .legend-dot {
            width: 13px; height: 13px; border-radius: 4px; flex-shrink: 0;
        }
        .legend-text { font-size: 11px; color: rgba(172,200,162,.85); }

        /* ─── MAIN ──────────────────────────── */
        .main {
            max-width: 1280px; margin: -20px auto 0;
            padding: 0 1.5rem 3rem;
            animation: fadeUp .5s .2s cubic-bezier(.16,1,.3,1) both;
        }

        /* ─── FLASH ─────────────────────────── */
        .flash {
            margin-bottom: 14px; padding: 11px 15px;
            border-radius: 10px; font-size: 13px; font-weight: 600;
            animation: fadeUp .3s both;
        }
        .flash-ok  { color: #166534; background: #f0fdf4; border: 1px solid #bbf7d0; }
        .flash-err { color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; }

        /* ─── WEEK NAV ──────────────────────── */
        .week-nav {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 16px;
        }
        .week-btn {
            display: flex; align-items: center; gap: 5px;
            font-size: 13px; font-weight: 700; text-decoration: none;
            padding: 9px 16px; border-radius: 11px; flex-shrink: 0;
            transition: transform .15s, box-shadow .15s;
        }
        .week-btn:hover { transform: translateY(-2px); }
        .week-btn-prev {
            background: linear-gradient(135deg, var(--g9), var(--g8));
            color: var(--acc); box-shadow: 0 3px 10px rgba(26,37,23,.22);
        }
        .week-btn-prev:hover { box-shadow: 0 6px 16px rgba(26,37,23,.32); }
        .week-btn-next {
            background: linear-gradient(135deg, var(--acc), var(--acc2));
            color: var(--g9); box-shadow: 0 3px 10px rgba(172,200,162,.35);
        }
        .week-btn-next:hover { box-shadow: 0 6px 16px rgba(172,200,162,.45); }
        .week-label {
            flex: 1; min-width: 0; text-align: center;
            font-size: 12px; font-weight: 700; color: var(--g9);
            background: #fff; padding: 9px 12px;
            border-radius: 10px; border: 1.5px solid #e5e7eb;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
        }

        /* ─── TABS ──────────────────────────── */
        .tabs { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 16px; }
        .tab-btn {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 15px; border-radius: 11px;
            font-size: 13px; font-weight: 600; font-family: inherit;
            cursor: pointer; border: 1.5px solid #e5e7eb;
            background: #fff; color: #6b7280;
            transition: border-color .18s, color .18s, transform .18s, box-shadow .18s, background .18s;
            white-space: nowrap;
        }
        .tab-btn:hover:not(.tab-active) {
            border-color: var(--acc); color: var(--g7);
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(172,200,162,.18);
        }
        .tab-btn.tab-active {
            background: linear-gradient(135deg, var(--g9), var(--g8));
            color: var(--acc); border-color: transparent;
            box-shadow: 0 4px 14px rgba(26,37,23,.28);
            transform: translateY(-1px);
        }

        /* ─── SKELETON ──────────────────────── */
        .skeleton-wrap {
            background: #fff; border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow); overflow: hidden;
            display: none;
        }
        .skel-head { height: 70px; background: linear-gradient(135deg, var(--g9), var(--g8)); }
        .skel-body { padding: 12px 16px; display: flex; flex-direction: column; gap: 6px; }
        .skel-row  { display: grid; grid-template-columns: 75px repeat(7,1fr); gap: 5px; }
        .skel-cell {
            height: 56px; border-radius: 9px;
            background: linear-gradient(90deg, #e8f0e6 25%, #f4f8f3 50%, #e8f0e6 75%);
            background-size: 600px 100%;
            animation: shimmer 1.4s infinite;
        }
        .skel-cell-sm { height: 24px; }

        /* ─── PANEL ─────────────────────────── */
        .lab-panel { animation: panelIn .28s cubic-bezier(.16,1,.3,1) both; }
        .panel-card {
            background: #fff; border-radius: 14px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border); overflow: hidden;
        }
        .panel-header {
            display: flex; align-items: center; gap: 13px;
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--g9), var(--g8));
        }
        .panel-icon {
            width: 38px; height: 38px; border-radius: 11px;
            background: rgba(172,200,162,.12);
            border: 1.5px solid rgba(172,200,162,.25);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .panel-name { font-family: 'Outfit', sans-serif; font-weight: 700; color: #fff; font-size: 17px; }
        .panel-cap  { font-size: 11px; color: rgba(172,200,162,.5); margin-top: 2px; }

        /* ─── SWIPE HINT ────────────────────── */
        .swipe-hint { display: none; }
        @media (max-width: 768px) {
            .swipe-hint {
                display: flex; align-items: center; gap: 6px;
                font-size: 11px; font-weight: 600; color: #6b7280;
                background: #fff; border: 1px solid var(--border);
                border-radius: 8px; padding: 6px 12px;
                margin: 10px 16px 0; width: fit-content;
            }
        }

        /* ─── TABLE ─────────────────────────── */
        .tbl-wrap {
            overflow-x: auto; -webkit-overflow-scrolling: touch;
        }
        .tbl-wrap::-webkit-scrollbar { height: 4px; }
        .tbl-wrap::-webkit-scrollbar-thumb { background: var(--acc); border-radius: 4px; }

        table { width: 100%; border-collapse: collapse; min-width: 780px; font-size: 12px; }
        thead tr { background: #f8faf7; border-bottom: 2px solid var(--border); }
        thead th {
            padding: 9px 7px; text-align: center;
            font-size: 10px; font-weight: 700; color: var(--muted);
            letter-spacing: .1em; text-transform: uppercase;
        }
        thead th.col-time {
            text-align: left; padding-left: 13px; width: 78px;
        }
        thead th.th-today { color: var(--g9); border-bottom: 3px solid var(--acc); }
        thead th.th-sun   { color: #dc2626; }

        tbody tr { border-top: 1px solid #f3f4f6; transition: background .12s; }
        tbody tr:hover { background: #fafcf9; }
        tbody tr:hover .col-time { background: #fafcf9; }

        .col-time {
            padding: 9px 9px 9px 13px;
            position: sticky; left: 0; z-index: 2;
            background: #fff; border-right: 1px solid #f0f0f0;
        }
        .slot-label { font-family: 'Outfit', sans-serif; font-weight: 700; color: var(--text); font-size: 11px; }
        .slot-time  { font-size: 10px; color: var(--muted); margin-top: 1px; }

        td.slot-td {
            padding: 4px 3px;
            border-right: 1px solid #f5f5f5;
        }
        td.slot-td.td-today { background: rgba(172,200,162,.04); }
        td.slot-td.td-sun   { background: rgba(254,226,226,.15); }

        /* ─── SLOT CARDS ────────────────────── */
        .sc {
            border-radius: 9px; padding: 6px 9px;
            overflow: hidden; position: relative;
            transition: transform .18s, box-shadow .18s, filter .18s;
        }
        /* Shine sweep */
        .sc::before {
            content: '';
            position: absolute; top: 0; left: -70%; width: 45%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(255,255,255,.2), transparent);
            transform: skewX(-18deg);
            transition: left .45s ease;
            pointer-events: none;
        }
        .sc:hover::before { left: 130%; }
        .sc:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 16px rgba(0,0,0,.1);
            filter: brightness(1.04);
            z-index: 4;
        }
        .sc-name    { font-weight: 700; font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sc-class   { font-size: 10px; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sc-subject { font-size: 10px; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sc-status  { font-size: 10px; margin-top: 1px; }

        .sc-tetap    { background: linear-gradient(135deg,#e0edd9,#cde0c7); border: 1.5px solid var(--acc); }
        .sc-tetap .sc-name    { color: var(--g9); }
        .sc-tetap .sc-class   { color: var(--g7); }
        .sc-tetap .sc-subject { color: #7a9475; }

        .sc-approved { background: linear-gradient(135deg,#f0fdf4,#dcfce7); border: 1.5px solid #86efac; }
        .sc-approved .sc-name   { color: #166534; }
        .sc-approved .sc-class  { color: #15803d; }
        .sc-approved .sc-status { color: #4ade80; }

        .sc-pending  { background: linear-gradient(135deg,#fffbeb,#fef3c7); border: 1.5px solid #fcd34d; }
        .sc-pending .sc-name   { color: #92400e; }
        .sc-pending .sc-class  { color: #b45309; }
        .sc-pending .sc-status { color: #f59e0b; }

        /* Past slot */
        .slot-past { text-align: center; padding: 13px 0; font-size: 11px; color: var(--muted); opacity: .3; }

        /* Sunday locked */
        .slot-locked {
            text-align: center; padding: 9px 3px; border-radius: 9px;
            background: rgba(254,226,226,.4); border: 1.5px dashed #fca5a5;
        }

        /* Booking button */
        .bk-btn {
            width: 100%; border-radius: 9px; padding: 13px 3px;
            background: transparent; cursor: pointer;
            border: 1.5px dashed #c8d9c5;
            transition: background .18s, border-color .18s, transform .18s, box-shadow .18s;
            display: flex; flex-direction: column; align-items: center; gap: 3px;
        }
        .bk-btn:hover {
            background: rgba(172,200,162,.08);
            border-color: var(--acc);
            transform: scale(1.04);
            box-shadow: 0 3px 12px rgba(172,200,162,.18);
        }
        .bk-btn:hover .bk-icon { color: var(--acc); transform: rotate(90deg); }
        .bk-icon { transition: transform .2s, color .2s; color: #d1d5db; }
        .bk-text { font-size: 10px; font-weight: 500; color: var(--muted); }

        .bk-btn-sun { border-color: #fca5a5; }
        .bk-btn-sun:hover { background: rgba(248,113,113,.05); border-color: #f87171; box-shadow: 0 3px 12px rgba(248,113,113,.12); }
        .bk-btn-sun:hover .bk-icon { color: #f87171; }
        .bk-btn-sun .bk-icon { color: #fca5a5; }
        .bk-btn-sun .bk-text { color: #fca5a5; }

        /* Break row */
        .break-row td {
            text-align: center; padding: 8px;
            font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            background: linear-gradient(135deg,#fef9ec,#fef3c7);
            color: #92400e; border-top: 1px solid #fde68a; border-bottom: 1px solid #fde68a;
        }

        /* ─── DETAIL MODAL ──────────────────── */
        .detail-overlay {
            display: none; position: fixed; inset: 0; z-index: 110;
            background: rgba(26,37,23,.82); backdrop-filter: blur(5px);
            align-items: center; justify-content: center; padding: 1rem;
        }
        .detail-overlay.show { display: flex; }
        .detail-box {
            background: #fff; border-radius: 16px;
            width: 100%; max-width: 420px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
            animation: modalIn .22s cubic-bezier(.16,1,.3,1);
        }
        .detail-head {
            padding: 16px 20px 14px;
        }
        .detail-head-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .detail-type {
            font-size: 10px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; margin-bottom: 3px;
        }
        .detail-teacher { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 18px; line-height: 1.2; }
        .detail-close {
            background: none; border: none; cursor: pointer;
            padding: 4px; border-radius: 7px; flex-shrink: 0;
            transition: background .15s;
            color: rgba(255,255,255,.6);
        }
        .detail-close:hover { background: rgba(255,255,255,.15); color: #fff; }

        .detail-body { padding: 14px 20px 18px; display: flex; flex-direction: column; gap: 10px; }
        .detail-row {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 9px 12px; border-radius: 9px;
            background: #f8faf7; border: 1px solid var(--border);
        }
        .detail-icon { font-size: 14px; flex-shrink: 0; margin-top: 1px; }
        .detail-key  { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
        .detail-val  { font-size: 13px; font-weight: 600; color: var(--text); margin-top: 1px; }

        /* ─── MODAL ─────────────────────────── */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 100;
            background: rgba(26,37,23,.82); backdrop-filter: blur(5px);
            align-items: center; justify-content: center; padding: 1rem;
        }
        .modal-overlay.show { display: flex; }
        .modal-box {
            background: #fff; border-radius: 16px;
            width: 100%; max-width: 510px; max-height: 93vh;
            overflow: hidden; display: flex; flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
            animation: modalIn .22s cubic-bezier(.16,1,.3,1);
        }
        .modal-head {
            padding: 18px 22px 14px; flex-shrink: 0;
            background: linear-gradient(135deg, var(--g9), var(--g8));
        }
        .modal-head-top { display: flex; align-items: flex-start; justify-content: space-between; }
        .modal-eyebrow { font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: rgba(172,200,162,.55); margin-bottom: 3px; }
        .modal-title { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 19px; color: #fff; }
        .modal-close {
            background: none; border: none; cursor: pointer;
            color: rgba(172,200,162,.5); padding: 4px; border-radius: 7px;
            transition: color .15s, background .15s;
        }
        .modal-close:hover { color: var(--acc); background: rgba(172,200,162,.1); }

        .modal-badges { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 11px; }
        .badge {
            font-size: 11px; padding: 3px 10px; border-radius: 999px; font-weight: 600;
            background: rgba(172,200,162,.1); color: var(--acc);
            border: 1px solid rgba(172,200,162,.2); white-space: nowrap;
        }

        .modal-body { flex: 1; overflow-y: auto; padding: 18px 22px; display: flex; flex-direction: column; gap: 13px; }

        .field-label {
            display: block; font-size: 11px; font-weight: 700; color: #6b7280;
            text-transform: uppercase; letter-spacing: .06em; margin-bottom: 5px;
        }
        .inp {
            width: 100%; border-radius: 9px; border: 1.5px solid #e5e7eb;
            padding: 9px 12px; font-size: 13px; font-family: inherit;
            background: #fafcf9; outline: none;
            transition: border-color .15s, box-shadow .15s, transform .12s;
        }
        .inp:focus { border-color: var(--acc); box-shadow: 0 0 0 3px rgba(172,200,162,.12); transform: translateY(-1px); }
        .inp:disabled { opacity: .5; cursor: not-allowed; background: #f3f4f6; }

        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 11px; }

        /* Slot options */
        .slot-opts { display: flex; gap: 7px; flex-wrap: wrap; }
        .slot-opt {
            flex: 1; min-width: 110px; padding: 9px 11px; border-radius: 9px;
            cursor: pointer; text-align: center; font-size: 12px; font-family: inherit;
            border: 2px solid #e5e7eb; background: #fff; color: var(--text);
            transition: border-color .15s, background .15s, transform .15s;
        }
        .slot-opt:not(:disabled):hover { border-color: var(--acc); transform: translateY(-1px); }
        .slot-opt.selected {
            border-color: var(--acc);
            background: linear-gradient(135deg, var(--g9), var(--g8));
            color: var(--acc);
        }
        .slot-opt:disabled { opacity: .5; cursor: not-allowed; border-style: dashed; }

        .slot-duration-wrap {
            background: #f8faf7; border: 1.5px solid var(--border);
            border-radius: 11px; padding: 11px 13px;
        }

        /* Modal buttons */
        .modal-actions { display: flex; gap: 9px; padding-top: 3px; padding-bottom: 3px; }
        .btn-cancel {
            flex: 1; background: #f3f4f6; border: none; color: var(--text);
            font-weight: 700; padding: 12px; border-radius: 11px;
            font-size: 13px; font-family: inherit; cursor: pointer;
            transition: background .15s, transform .15s;
        }
        .btn-cancel:hover { background: #e5e7eb; transform: translateY(-1px); }
        .btn-submit {
            flex: 1; background: linear-gradient(135deg, var(--g9), var(--g8));
            border: none; color: var(--acc); font-weight: 700; padding: 12px;
            border-radius: 11px; font-size: 13px; font-family: inherit;
            cursor: pointer; box-shadow: 0 3px 12px rgba(26,37,23,.25);
            transition: transform .15s, box-shadow .15s, filter .15s;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(26,37,23,.35); filter: brightness(1.08); }

        /* ─── FOOTER ────────────────────────── */
        footer { text-align: center; padding: 18px; font-size: 12px; color: var(--muted); }

        /* ─── RESPONSIVE ────────────────────── */
        .hide-xs { display: inline; }
        .show-xs { display: none; }
        @media (max-width: 600px) {
            .hide-xs { display: none; }
            .show-xs { display: inline; }
            .field-row { grid-template-columns: 1fr; }
            .navbar-inner { padding: 0 1rem; }
            .hero, .main { padding-left: 1rem; padding-right: 1rem; }
        }

        /* ─── REDUCED MOTION ─────────────────── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }
    
.pub-navbar{position:sticky;top:0;z-index:100;background:linear-gradient(135deg,#1A2517,#2a3826);box-shadow:0 2px 16px rgba(0,0,0,.28);animation:navSlideDown .4s cubic-bezier(.16,1,.3,1) both}
.pub-inner{max-width:1280px;margin:0 auto;padding:0 1.5rem;display:flex;align-items:center;justify-content:space-between;height:60px}
.pub-brand{display:flex;align-items:center;gap:10px;text-decoration:none;flex-shrink:0}
.pub-brand-icon{width:34px;height:34px;border-radius:9px;background:rgba(172,200,162,.12);border:1.5px solid rgba(172,200,162,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .18s}
.pub-brand-icon:hover{background:rgba(172,200,162,.22)}
.pub-brand-name{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff;line-height:1.2}
.pub-brand-sub{font-size:10px;color:rgba(172,200,162,.4)}
.pub-links{display:flex;align-items:center;gap:4px}
.pub-link{font-size:13px;font-weight:600;color:rgba(172,200,162,.55);text-decoration:none;padding:7px 13px;border-radius:8px;transition:color .15s,background .15s;white-space:nowrap}
.pub-link:hover,.pub-link.on{color:#ACC8A2;background:rgba(172,200,162,.1)}
.pub-btn{font-size:12px;font-weight:700;padding:7px 15px;border-radius:8px;color:#ACC8A2;border:1.5px solid rgba(172,200,162,.3);text-decoration:none;margin-left:6px;transition:background .15s,transform .15s;white-space:nowrap}
.pub-btn:hover{background:rgba(172,200,162,.08);transform:translateY(-1px)}
@keyframes navSlideDown{from{transform:translateY(-64px);opacity:0}to{transform:none;opacity:1}}
@media(max-width:600px){.pub-inner{padding:0 1rem}.pub-brand-sub{display:none}.pub-link{padding:6px 9px;font-size:12px}.pub-btn{padding:6px 11px;font-size:12px;margin-left:3px}}


.page-trans{position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#1A2517,#2d3d29);opacity:0;pointer-events:none;transition:opacity .22s ease}
.page-trans.go{opacity:1;pointer-events:all}
</style>
</head>
<body>

{{-- ═══ NAVBAR ═══ --}}
<nav class="pub-navbar">
    <div class="pub-inner">
        <a href="{{ route('home') }}" class="pub-brand">
            <div class="pub-brand-icon">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="pub-brand-name">Lab Management</div>
                <div class="pub-brand-sub">Nuris Jember</div>
            </div>
        </a>
        <div class="pub-links">
            <a href="{{ route('home') }}" class="pub-link on">Jadwal</a>
            <a href="{{ route('inventory.public') }}" class="pub-link">Inventaris</a>
            <a href="/rekap" class="pub-link">Rekap</a>
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="pub-btn">Login →</a>
            @endauth
        </div>
    </div>
</nav>

{{-- ═══ HERO ═══ --}}
<div class="hero">
    <div class="hero-inner">
        <p class="hero-eyebrow">Sistem Informasi Laboratorium</p>
        <h1 class="hero-title">Jadwal Penggunaan Lab</h1>
        <p class="hero-desc">Pilih lab · Klik slot kosong untuk booking · Tanpa perlu login</p>

        <div class="period-badge">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:linear-gradient(135deg,#d6ead2,#ACC8A2)"></div>
                <span class="legend-text">Jadwal Tetap</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#dcfce7;border:1.5px solid #86efac"></div>
                <span class="legend-text">Disetujui</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#fef3c7;border:1.5px solid #fcd34d"></div>
                <span class="legend-text">Pending</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:transparent;border:2px dashed rgba(172,200,162,.5)"></div>
                <span class="legend-text">Tersedia — klik booking</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MAIN ═══ --}}
<div class="main">

    @if(session('success'))
        <div class="flash flash-ok">✓ {{ session('success') }}</div>
    @endif
    @if($errors->has('error'))
        <div class="flash flash-err">⚠ {{ $errors->first('error') }}</div>
    @endif

    {{-- Week Nav --}}
    <div class="week-nav">
        <button onclick="changeWeek('{{ $prevWeek }}')" class="week-btn week-btn-prev">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="hide-xs">Minggu Lalu</span>
        </button>
        <span class="week-label" id="week-label">
            📅
            <span class="hide-xs">{{ $weekStart->translatedFormat('d M Y') }} – {{ $weekEnd->translatedFormat('d M Y') }}</span>
            <span class="show-xs">{{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}</span>
        </span>
        <button onclick="changeWeek('{{ $nextWeek }}')" class="week-btn week-btn-next">
            <span class="hide-xs">Minggu Depan</span>
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    {{-- Tabs --}}
    <div class="tabs">
        @foreach($resources as $i => $resource)
        <button onclick="switchTab({{ $resource->id }})" id="tab-{{ $resource->id }}"
            class="tab-btn {{ $i === 0 ? 'tab-active' : '' }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ $resource->name }}
        </button>
        @endforeach
    </div>

    {{-- Skeleton --}}
    <div class="skeleton-wrap" id="skeleton">
        <div class="skel-head"></div>
        <div class="skel-body">
            <div class="skel-row">
                <div class="skel-cell skel-cell-sm"></div>
                @for($d=0;$d<7;$d++)<div class="skel-cell skel-cell-sm"></div>@endfor
            </div>
            @for($r=0;$r<7;$r++)
            <div class="skel-row">
                <div class="skel-cell" style="animation-delay:{{ $r*30 }}ms"></div>
                @for($d=0;$d<7;$d++)
                <div class="skel-cell" style="animation-delay:{{ ($r*7+$d)*20 }}ms"></div>
                @endfor
            </div>
            @endfor
        </div>
    </div>

    {{-- Lab Panels --}}
    <div id="panels-wrap">
    @foreach($resources as $i => $resource)
    <div id="panel-{{ $resource->id }}" class="lab-panel" style="{{ $i !== 0 ? 'display:none' : '' }}">
        <div class="panel-card">

            {{-- Header --}}
            <div class="panel-header">
                <div class="panel-icon">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="panel-name">{{ $resource->name }}</div>
                    @if($resource->capacity)
                    <div class="panel-cap">Kapasitas {{ $resource->capacity }} komputer</div>
                    @endif
                </div>
            </div>

            {{-- Swipe hint --}}
            <div class="swipe-hint">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Geser kiri/kanan untuk semua hari
            </div>

            {{-- Table --}}
            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr>
                            <th class="col-time">JAM</th>
                            @foreach($days as $day)
                            @php
                                $isToday = \Carbon\Carbon::parse($weekDates[$day])->isToday();
                                $isSun   = $day === 'Minggu';
                            @endphp
                            <th class="{{ $isToday ? 'th-today' : '' }} {{ $isSun ? 'th-sun' : '' }}">
                                <div>{{ $day }}</div>
                                <div style="margin-top:2px;font-weight:400">
                                    @if($isToday)
                                        <span style="background:var(--g9);color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:999px">
                                            {{ \Carbon\Carbon::parse($weekDates[$day])->format('d/m') }}
                                        </span>
                                    @else
                                        <span style="font-size:10px;color:{{ $isSun ? '#fca5a5' : 'var(--muted)' }}">
                                            {{ \Carbon\Carbon::parse($weekDates[$day])->format('d/m') }}
                                        </span>
                                    @endif
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $slot)
                        @php $isBreak = $slot->is_break ?? false; @endphp

                        @if($isBreak)
                        <tr class="break-row">
                            <td colspan="{{ count($days) + 1 }}">
                                ☕ ISTIRAHAT · {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                @if($slot->end_time) – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}@endif
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td class="col-time">
                                <div class="slot-label">{{ $slot->name }}</div>
                                <div class="slot-time">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                    @if($slot->end_time)–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}@endif
                                </div>
                            </td>
                            @foreach($days as $day)
                            @php
                                $dayEn      = $dayMapReverse[$day];
                                $date       = $weekDates[$day];
                                $sk         = $resource->id.'_'.$dayEn.'_'.$slot->id;
                                $bk         = $resource->id.'_'.$date.'_'.$slot->id;
                                $isToday    = \Carbon\Carbon::parse($date)->isToday();
                                $isPast     = \Carbon\Carbon::parse($date)->isPast() && !$isToday;
                                $slotTime   = \Carbon\Carbon::parse($slot->start_time)->setDateFrom(\Carbon\Carbon::today());
                                $isSlotPast = $isToday && $slotTime->isPast();
                                $isSun      = $day === 'Minggu';
                                $sched      = $schedules->get($sk)?->first();
                                $book       = $bookings->get($bk)?->first();
                                $sundayLocked = $isSun && !$book && !$sched &&
                                    $bookings->flatten()->filter(fn($b) =>
                                        $b->resource_id == $resource->id &&
                                        $b->booking_date == $date &&
                                        in_array($b->status, ['pending','approved'])
                                    )->isNotEmpty();
                            @endphp
                            <td class="slot-td {{ $isToday ? 'td-today' : '' }} {{ $isSun ? 'td-sun' : '' }}">

                                @if($sched)
                                    <div class="sc sc-tetap" style="cursor:pointer" onclick="showDetail({
                                        type:'tetap',
                                        teacher:'{{ addslashes($sched->teacher_name) }}',
                                        class_name:'{{ addslashes($sched->labClass?->name ?? '-') }}',
                                        subject:'{{ addslashes($sched->subject_name ?? '') }}',
                                        slot:'{{ addslashes($slot->name) }}',
                                        time:'{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}{{ $slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : '' }}',
                                        day:'{{ $day }}',
                                        date:'{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}',
                                        lab:'{{ addslashes($resource->name) }}',
                                        phone:'',
                                        title:'',
                                        desc:'',
                                        participants:''
                                    })">
                                        <div class="sc-name">{{ $sched->teacher_name }}</div>
                                        <div class="sc-class">{{ $sched->labClass?->name ?? '-' }}</div>
                                        @if($sched->subject_name)<div class="sc-subject">{{ $sched->subject_name }}</div>@endif
                                    </div>

                                @elseif($book && $book->status === 'approved')
                                    <div class="sc sc-approved" style="cursor:pointer" onclick="showDetail({
                                        type:'approved',
                                        teacher:'{{ addslashes($book->teacher_name) }}',
                                        class_name:'{{ addslashes($book->class_name ?? '') }}',
                                        subject:'{{ addslashes($book->subject_name ?? '') }}',
                                        slot:'{{ addslashes($slot->name) }}',
                                        time:'{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}{{ $slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : '' }}',
                                        day:'{{ $day }}',
                                        date:'{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}',
                                        lab:'{{ addslashes($resource->name) }}',
                                        phone:'{{ addslashes($book->teacher_phone ?? '') }}',
                                        title:'{{ addslashes($book->title ?? '') }}',
                                        desc:'{{ addslashes($book->description ?? '') }}',
                                        participants:'{{ $book->participant_count ?? '' }}'
                                    })">
                                        <div class="sc-name">{{ $book->teacher_name }}</div>
                                        <div class="sc-class">{{ $book->class_name }}</div>
                                        <div class="sc-status">✓ Disetujui</div>
                                    </div>

                                @elseif($book && $book->status === 'pending')
                                    <div class="sc sc-pending" style="cursor:pointer" onclick="showDetail({
                                        type:'pending',
                                        teacher:'{{ addslashes($book->teacher_name) }}',
                                        class_name:'{{ addslashes($book->class_name ?? '') }}',
                                        subject:'{{ addslashes($book->subject_name ?? '') }}',
                                        slot:'{{ addslashes($slot->name) }}',
                                        time:'{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}{{ $slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : '' }}',
                                        day:'{{ $day }}',
                                        date:'{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}',
                                        lab:'{{ addslashes($resource->name) }}',
                                        phone:'{{ addslashes($book->teacher_phone ?? '') }}',
                                        title:'{{ addslashes($book->title ?? '') }}',
                                        desc:'{{ addslashes($book->description ?? '') }}',
                                        participants:'{{ $book->participant_count ?? '' }}'
                                    })">
                                        <div class="sc-name">{{ $book->teacher_name }}</div>
                                        <div class="sc-class">{{ $book->class_name }}</div>
                                        <div class="sc-status">⏳ Pending</div>
                                    </div>

                                @elseif($isPast || $isSlotPast)
                                    <div class="slot-past">Lewat</div>

                                @elseif($sundayLocked)
                                    <div class="slot-locked">
                                        <div style="font-size:13px">🔒</div>
                                        <div style="font-size:10px;color:#dc2626;font-weight:600;margin-top:2px">Minggu 1x</div>
                                    </div>

                                @else
                                @php
                                    $bookedSlotIds = $bookings->flatten()->filter(fn($b) =>
                                        $b->resource_id == $resource->id &&
                                        $b->booking_date == $date &&
                                        in_array($b->status, ['pending','approved'])
                                    )->pluck('time_slot_id')->toArray();
                                    $scheduledSlotIds = $timeSlots->filter(fn($ts) =>
                                        !($ts->is_break ?? false) &&
                                        $schedules->has($resource->id.'_'.$dayEn.'_'.$ts->id)
                                    )->pluck('id')->toArray();
                                    $takenSlotIds = array_unique(array_merge($bookedSlotIds, $scheduledSlotIds));
                                @endphp
                                    <button class="bk-btn {{ $isSun ? 'bk-btn-sun' : '' }}"
                                        onclick="openBooking({{ $resource->id }},'{{ addslashes($resource->name) }}',{{ $slot->id }},'{{ $slot->name }}','{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}{{ $slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : '' }}','{{ $dayEn }}','{{ $day }}','{{ $date }}',{{ json_encode($takenSlotIds) }})">
                                        <svg class="bk-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span class="bk-text">Booking</span>
                                    </button>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
    </div>{{-- /panels-wrap --}}
</div>

{{-- ═══ DETAIL MODAL ═══ --}}
<div class="detail-overlay" id="detail-overlay" onclick="if(event.target===this)closeDetail()">
    <div class="detail-box" id="detail-box">
        <div class="detail-head" id="detail-head">
            <div class="detail-head-top">
                <div>
                    <div class="detail-type" id="d-type"></div>
                    <div class="detail-teacher" id="d-teacher"></div>
                </div>
                <button class="detail-close" onclick="closeDetail()">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="detail-body" id="detail-body"></div>
    </div>
</div>

<footer>© {{ date('Y') }} Lab Management System · Nuris Jember</footer>

{{-- ═══ MODAL ═══ --}}
<div class="modal-overlay" id="modal-overlay" onclick="if(event.target===this)closeModal()">
    <div class="modal-box" id="modal-box">
        <div class="modal-head">
            <div class="modal-head-top">
                <div>
                    <p class="modal-eyebrow">Form Booking Lab</p>
                    <h2 class="modal-title">Ajukan Penggunaan Lab</h2>
                </div>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-badges">
                <span class="badge" id="b-lab">🖥 -</span>
                <span class="badge" id="b-date">📅 -</span>
                <span class="badge" id="b-slot">🕐 -</span>
            </div>
        </div>

        <form method="POST" action="/booking" class="modal-body">
            @csrf
            <input type="hidden" name="resource_id"  id="f_rid">
            <input type="hidden" name="time_slot_id" id="f_sid">
            <input type="hidden" name="booking_date" id="f_date">
            <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">

            <div class="slot-duration-wrap">
                <label class="field-label">🕐 Durasi Booking</label>
                <div class="slot-opts" id="slot-options"></div>
                <input type="hidden" name="extra_slot_ids" id="f_extra_slots" value="">
            </div>

            <div class="field-row">
                <div style="position:relative">
                    <label class="field-label">Nama Pengajar *</label>
                    <input name="teacher_name" id="inp_teacher_name" type="text" placeholder="Nama lengkap" class="inp" required value="{{ old('teacher_name') }}" autocomplete="off" oninput="filterTeacher(this.value)">
                    <div id="teacher_suggestions" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid #ACC8A2;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.1);z-index:999;max-height:180px;overflow-y:auto"></div>
                </div>
                <div>
                    <label class="field-label">Nomor HP *</label>
                    <input name="teacher_phone" id="inp_teacher_phone" type="text" placeholder="08xxxxxxxxxx" class="inp" required value="{{ old('teacher_phone') }}">
                </div>
            </div>
            <script>
            const TEACHERS = @json($teachers);
            function filterTeacher(val) {
                const box = document.getElementById('teacher_suggestions');
                if (!val || val.length < 2) { box.style.display = 'none'; return; }
                const filtered = TEACHERS.filter(t => t.name.toLowerCase().includes(val.toLowerCase()));
                if (!filtered.length) { box.style.display = 'none'; return; }
                box.innerHTML = filtered.map(t => `
                    <div onclick="selectTeacher('${t.name}','${t.phone ?? ''}')"
                        style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center"
                        onmouseover="this.style.background='#f0f7ee'" onmouseout="this.style.background=''">
                        <span style="font-weight:600;color:#1A2517">${t.name}</span>
                        <span style="font-size:11px;color:#9ca3af">${t.phone ?? ''}</span>
                    </div>`).join('');
                box.style.display = 'block';
            }
            function selectTeacher(name, phone) {
                document.getElementById('inp_teacher_name').value = name;
                document.getElementById('inp_teacher_phone').value = phone;
                document.getElementById('teacher_suggestions').style.display = 'none';
            }
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#inp_teacher_name') && !e.target.closest('#teacher_suggestions')) {
                    document.getElementById('teacher_suggestions').style.display = 'none';
                }
            });
            </script>

            <div>
                <label class="field-label">Unit Sekolah *</label>
                <select name="organization_id" id="f_org" class="inp" required onchange="loadKelas(this.value)" style="appearance:auto">
                    <option value="">— Pilih unit sekolah —</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label">Kelas *</label>
                <select name="class_id" id="f_class" class="inp" required disabled style="appearance:auto">
                    <option value="">— Pilih unit sekolah dulu —</option>
                </select>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label">Mata Pelajaran *</label>
                    <input name="subject_name" type="text" placeholder="Contoh: TIK" class="inp" required value="{{ old('subject_name') }}">
                </div>
                <div>
                    <label class="field-label">Jumlah Peserta *</label>
                    <input name="participant_count" type="number" min="1" placeholder="0" class="inp" required value="{{ old('participant_count') }}">
                </div>
            </div>

            <div>
                <label class="field-label">Judul Kegiatan *</label>
                <input name="title" type="text" placeholder="Contoh: Praktikum Microsoft Excel" class="inp" required value="{{ old('title') }}">
            </div>

            <div>
                <label class="field-label">Keterangan</label>
                <input name="description" type="text" placeholder="Opsional" class="inp" value="{{ old('description') }}">
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">✓ Ajukan Booking</button>
            </div>
        </form>
    </div>
</div>

<script>
const ALL_SLOTS = @json($timeSlots->where('is_break', false)->values());

function switchTab(id) {
    document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('tab-active'));
    document.getElementById('skeleton').style.display = 'block';
    document.getElementById('tab-' + id).classList.add('tab-active');

    setTimeout(() => {
        document.getElementById('skeleton').style.display = 'none';
        const panel = document.getElementById('panel-' + id);
        panel.style.display = '';
        panel.style.animation = 'none';
        void panel.offsetWidth;
        panel.style.animation = '';
    }, 380);
}

function openBooking(rid, rname, sid, sname, stime, dayEn, dayId, date, bookedSlots) {
    bookedSlots = bookedSlots || [];
    document.getElementById('f_rid').value  = rid;
    document.getElementById('f_sid').value  = sid;
    document.getElementById('f_extra_slots').value = '';
    document.getElementById('f_date').value = date;

    const d  = new Date(date + 'T00:00:00');
    const mn = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('b-lab').textContent  = '🖥 ' + rname;
    document.getElementById('b-date').textContent = '📅 ' + dayId + ', ' + d.getDate() + ' ' + mn[d.getMonth()] + ' ' + d.getFullYear();
    document.getElementById('b-slot').textContent = '🕐 ' + sname + ' · ' + stime;

    // Multi-slot selector
    const slotIdx  = ALL_SLOTS.findIndex(s => s.id == sid);
    const wrap     = document.getElementById('slot-options');
    const startTime = stime.split('\u2013')[0].split('-')[0].trim();
    wrap.innerHTML = '';

    // Cari semua slot berurutan yang tersedia
    let availableSlots = [];
    for (let i = slotIdx; i < ALL_SLOTS.length; i++) {
        if (i > slotIdx && bookedSlots.includes(ALL_SLOTS[i].id)) break;
        availableSlots.push(ALL_SLOTS[i]);
    }

    let selectedCount = 1;

    function updateExtraSlots() {
        const extras = availableSlots.slice(1, selectedCount).map(s => s.id);
        document.getElementById('f_extra_slots').value = extras.join(',');
        wrap.querySelectorAll('.slot-opt:not(.slot-opt-full)').forEach((btn, i) => {
            btn.classList.toggle('selected', i < selectedCount);
        });
        const lastSlot = availableSlots[selectedCount - 1];
        const endTime  = lastSlot.end_time ? lastSlot.end_time.slice(0,5) : '';
        document.getElementById('b-slot').textContent = '\uD83D\uDD50 ' + sname
            + (selectedCount > 1 ? ' \u2013 ' + lastSlot.name : '')
            + ' \u00B7 ' + startTime + (endTime ? '\u2013' + endTime : '');
    }

    // Buat tombol per slot
    availableSlots.forEach((slot, i) => {
        const btn  = document.createElement('button');
        btn.type   = 'button';
        btn.className = 'slot-opt' + (i === 0 ? ' selected' : '');
        const endT = slot.end_time ? slot.end_time.slice(0,5) : '';
        if (i === 0) {
            btn.innerHTML = '<strong>' + slot.name + '</strong><br><span style="font-size:10px">' + startTime + (endT ? '\u2013' + endT : '') + '</span>';
        } else {
            btn.innerHTML = '<strong>+ ' + slot.name + '</strong><br><span style="font-size:10px">s/d ' + endT + '</span>';
        }
        btn.onclick = () => { selectedCount = i + 1; updateExtraSlots(); };
        wrap.appendChild(btn);
    });

    // Tombol Full Day jika ada lebih dari 2 slot tersedia
    if (availableSlots.length > 2) {
        const btnAll = document.createElement('button');
        btnAll.type  = 'button';
        btnAll.className = 'slot-opt slot-opt-full';
        btnAll.style.cssText = 'background:linear-gradient(135deg,#1A2517,#2a3826);color:#ACC8A2;border-color:#3d5438;min-width:90px';
        const lastT  = availableSlots[availableSlots.length - 1];
        const lastEnd = lastT.end_time ? lastT.end_time.slice(0,5) : '';
        btnAll.innerHTML = '<strong>Full (' + availableSlots.length + ')</strong><br><span style="font-size:10px">' + startTime + (lastEnd ? '\u2013' + lastEnd : '') + '</span>';
        btnAll.onclick = () => {
            selectedCount = availableSlots.length;
            updateExtraSlots();
            wrap.querySelectorAll('.slot-opt').forEach(b => b.classList.remove('selected'));
            btnAll.classList.add('selected');
        };
        wrap.appendChild(btnAll);
    }

    updateExtraSlots();

    document.getElementById('f_class').innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
    document.getElementById('f_class').disabled  = true;
    document.getElementById('f_org').value = '';

    document.getElementById('modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

// selectOpt diganti dengan multi-slot logic di openBooking

function closeModal() {
    const overlay = document.getElementById('modal-overlay');
    const box     = document.getElementById('modal-box');
    box.style.transition = 'opacity .16s, transform .16s';
    box.style.opacity    = '0';
    box.style.transform  = 'translateY(10px) scale(.97)';
    setTimeout(() => {
        overlay.classList.remove('show');
        box.style.transition = '';
        box.style.opacity    = '';
        box.style.transform  = '';
        document.body.style.overflow = '';
    }, 160);
}

// ─── DETAIL MODAL ────────────────────────────────────
const TYPE_CONFIG = {
    tetap:    { label: '📅 Jadwal Tetap',  headBg: 'linear-gradient(135deg,#2a4a28,#3a6b38)', typeColor: 'rgba(172,200,162,.7)', teacherColor: '#d6ead2' },
    approved: { label: '✓ Booking Disetujui', headBg: 'linear-gradient(135deg,#14532d,#166534)', typeColor: '#86efac', teacherColor: '#d1fae5' },
    pending:  { label: '⏳ Menunggu Persetujuan', headBg: 'linear-gradient(135deg,#78350f,#92400e)', typeColor: '#fcd34d', teacherColor: '#fef3c7' },
};

function showDetail(d) {
    const cfg = TYPE_CONFIG[d.type] || TYPE_CONFIG.tetap;

    // Header
    const head = document.getElementById('detail-head');
    head.style.background = cfg.headBg;
    document.getElementById('d-type').style.color    = cfg.typeColor;
    document.getElementById('d-type').textContent    = cfg.label;
    document.getElementById('d-teacher').style.color = cfg.teacherColor;
    document.getElementById('d-teacher').textContent = d.teacher;

    // Build rows
    const rows = [
        { icon: '🖥', key: 'Lab',      val: d.lab },
        { icon: '📚', key: 'Kelas',    val: d.class_name || '-' },
        { icon: '📖', key: 'Mapel',    val: d.subject || '-' },
        { icon: '📅', key: 'Hari',     val: d.day + ', ' + d.date },
        { icon: '🕐', key: 'Slot',     val: d.slot + ' · ' + d.time },
    ];
    if (d.title)        rows.push({ icon: '📝', key: 'Kegiatan',  val: d.title });
    if (d.participants) rows.push({ icon: '👥', key: 'Peserta',   val: d.participants + ' orang' });
    if (d.phone)        rows.push({ icon: '📞', key: 'No. HP',    val: d.phone });
    if (d.desc)         rows.push({ icon: '💬', key: 'Keterangan', val: d.desc });

    document.getElementById('detail-body').innerHTML = rows.map(r => `
        <div class="detail-row">
            <span class="detail-icon">${r.icon}</span>
            <div>
                <div class="detail-key">${r.key}</div>
                <div class="detail-val">${r.val}</div>
            </div>
        </div>
    `).join('');

    document.getElementById('detail-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDetail() {
    const box = document.getElementById('detail-box');
    box.style.transition = 'opacity .16s, transform .16s';
    box.style.opacity    = '0';
    box.style.transform  = 'translateY(10px) scale(.97)';
    setTimeout(() => {
        document.getElementById('detail-overlay').classList.remove('show');
        box.style.transition = box.style.opacity = box.style.transform = '';
        document.body.style.overflow = '';
    }, 160);
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeModal(); closeDetail(); } });

// ─── AJAX WEEK NAVIGATION ─────────────────────────────
let currentActiveTabId = null;

function changeWeek(week) {
    // Simpan tab aktif
    const activeTab = document.querySelector('.tab-btn.tab-active');
    if (activeTab) currentActiveTabId = activeTab.id.replace('tab-', '');

    // Tampilkan skeleton
    document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');
    document.getElementById('skeleton').style.display = 'block';

    // Disable tombol minggu
    document.querySelectorAll('.week-btn').forEach(b => { b.disabled = true; b.style.opacity = '.6'; });

    // Update URL tanpa reload
    const url = new URL(window.location.href);
    url.searchParams.set('week', week);
    window.history.pushState({week}, '', url.toString());

    // Fetch HTML halaman baru
    fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.text())
    .then(html => {
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');

        // Update week label
        const newLabel = newDoc.getElementById('week-label');
        if (newLabel) document.getElementById('week-label').innerHTML = newLabel.innerHTML;

        // Update data panel
        const newWrap = newDoc.getElementById('panels-wrap');
        const oldWrap = document.getElementById('panels-wrap');
        if (newWrap && oldWrap) oldWrap.innerHTML = newWrap.innerHTML;

        // Update tombol prev/next
        const newBtns = newDoc.querySelectorAll('.week-btn');
        const oldBtns = document.querySelectorAll('.week-btn');
        newBtns.forEach((nb, i) => {
            if (oldBtns[i]) {
                const match = nb.getAttribute('onclick')?.match(/'([^']+)'/);
                if (match) oldBtns[i].setAttribute('onclick', `changeWeek('${match[1]}')`);
            }
        });

        // Update ALL_SLOTS
        newDoc.querySelectorAll('script');
        const scripts = newDoc.querySelectorAll('script');
        scripts.forEach(s => {
            const m = s.textContent.match(/const ALL_SLOTS\s*=\s*(\[[\s\S]*?\]);/);
            if (m) { try { window.ALL_SLOTS = JSON.parse(m[1]); } catch(e) {} }
        });

        // Sembunyikan skeleton
        document.getElementById('skeleton').style.display = 'none';

        // Sembunyikan SEMUA panel dulu (penting!)
        document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');

        // Tampilkan hanya panel yang aktif
        const targetId = currentActiveTabId || document.querySelector('.lab-panel')?.id?.replace('panel-', '');
        const target   = targetId ? document.getElementById('panel-' + targetId) : document.querySelector('.lab-panel');
        if (target) {
            target.style.display = '';
            target.style.animation = 'none';
            void target.offsetWidth;
            target.style.animation = '';
        }
        // Re-enable tombol
        document.querySelectorAll('.week-btn').forEach(b => { b.disabled = false; b.style.opacity = ''; });
    })
    .catch(() => { window.location.href = url.toString(); });
}


function loadKelas(orgId) {
    const sel = document.getElementById('f_class');
    if (!orgId) { sel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>'; sel.disabled = true; return; }
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled  = true;
    fetch('/kelas?organization_id=' + orgId)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— Pilih kelas —</option>';
            data.forEach(c => { sel.innerHTML += `<option value="${c.id}">${c.name}</option>`; });
            sel.disabled = false;
        })
        .catch(() => { sel.innerHTML = '<option value="">Gagal memuat</option>'; });
}
</script>

<div class="page-trans" id="pt"></div>
<script>
// SPA-like page transition
document.querySelectorAll('a.pub-link, a.pub-btn, a.pub-brand').forEach(a => {
    const href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript') || a.getAttribute('target') === '_blank') return;
    a.addEventListener('click', function(e) {
        // Skip jika sudah di halaman yang sama
        const current = window.location.pathname;
        try {
            const target = new URL(href, window.location.href).pathname;
            if (target === current) return;
        } catch(err) {}
        e.preventDefault();
        const pt = document.getElementById('pt');
        pt.classList.add('go');
        setTimeout(() => { window.location.href = href; }, 220);
    });
});
// Fade in on back navigation
window.addEventListener('pageshow', () => {
    document.getElementById('pt').classList.remove('go');
});
</script>

</body>
</html>