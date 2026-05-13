<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal Lab - Lab Management</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
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

        .navbar {
            position: sticky; top: 0; z-index: 40;
            background: linear-gradient(135deg, var(--g9), var(--g8));
            box-shadow: 0 2px 16px rgba(0,0,0,.3);
            animation: slideDown .4s cubic-bezier(.16,1,.3,1) both;
            width: 100%; overflow: hidden;
        }
        .navbar-inner {
            max-width: 1280px; margin: auto;
            padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            height: 60px; gap: 10px;
        }
        .brand { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .brand-icon {
            width: 32px; height: 32px; border-radius: 9px;
            background: rgba(172,200,162,.15);
            display: flex; align-items: center; justify-content: center;
            transition: background .2s; flex-shrink: 0;
        }
        .brand-icon:hover { background: rgba(172,200,162,.25); }
        .brand-name { font-family: 'Outfit', sans-serif; font-weight: 700; color: #fff; font-size: 14px; line-height: 1.2; }
        .brand-sub  { font-size: 10px; color: rgba(172,200,162,.5); }

        .nav-actions {
            display: flex; align-items: center; gap: 4px;
            overflow-x: auto; -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .nav-actions::-webkit-scrollbar { display: none; }

        .nav-link {
            font-size: 12px; font-weight: 600;
            color: rgba(172,200,162,.6); text-decoration: none;
            padding: 6px 10px; border-radius: 8px;
            transition: color .15s, background .15s;
            white-space: nowrap;
        }
        .nav-link:hover { color: var(--acc); background: rgba(172,200,162,.08); }
        .nav-btn {
            font-size: 11px; font-weight: 700; text-decoration: none;
            padding: 6px 14px; border-radius: 9px;
            border: 1px solid rgba(172,200,162,.3); color: var(--acc);
            transition: background .15s, transform .15s;
            white-space: nowrap; flex-shrink: 0;
        }
        .nav-btn:hover { background: rgba(172,200,162,.1); transform: translateY(-1px); }

        @media (max-width: 640px) {
            .navbar-inner { padding: 0 1rem; }
            .brand-sub { display: none; }
            .brand-name { font-size: 13px; }
            .nav-link { padding: 6px 8px; font-size: 11px; }
            .nav-btn { padding: 5px 12px; }
        }

        .hero {
            background: linear-gradient(135deg, var(--g9) 0%, var(--g8) 55%, var(--g7) 100%);
            padding: 2rem 1.5rem 3.5rem;
            animation: fadeUp .5s .08s cubic-bezier(.16,1,.3,1) both;
            overflow: hidden;
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
        .legend-dot { width: 13px; height: 13px; border-radius: 4px; flex-shrink: 0; }
        .legend-text { font-size: 11px; color: rgba(172,200,162,.85); }

        .main {
            max-width: 1280px; margin: -20px auto 0;
            padding: 0 1.5rem 3rem;
            animation: fadeUp .5s .2s cubic-bezier(.16,1,.3,1) both;
        }

        .flash {
            margin-bottom: 14px; padding: 11px 15px;
            border-radius: 10px; font-size: 13px; font-weight: 600;
            animation: fadeUp .3s both;
        }
        .flash-ok  { color: #166534; background: #f0fdf4; border: 1px solid #bbf7d0; }
        .flash-err { color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; }

        .week-nav { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
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

        .tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .tbl-wrap::-webkit-scrollbar { height: 4px; }
        .tbl-wrap::-webkit-scrollbar-thumb { background: var(--acc); border-radius: 4px; }

        table { width: 100%; border-collapse: collapse; min-width: 780px; font-size: 12px; height: 1px; }
        thead tr { background: #f8faf7; border-bottom: 2px solid var(--border); }
        thead th {
            padding: 9px 7px; text-align: center;
            font-size: 10px; font-weight: 700; color: var(--muted);
            letter-spacing: .1em; text-transform: uppercase;
            width: calc((100% - 78px) / 7);
        }
        thead th.col-time { text-align: left; padding-left: 13px; width: 78px; }
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

        td.slot-td { padding: 4px 3px; border-right: 1px solid #f5f5f5; }
        td.slot-td.td-today { background: rgba(172,200,162,.04); }
        td.slot-td.td-sun   { background: rgba(254,226,226,.15); }

        .sc {
            border-radius: 9px; padding: 6px 9px;
            overflow: hidden; position: relative;
            transition: transform .18s, box-shadow .18s, filter .18s;
        }
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

        .slot-past { text-align: center; padding: 13px 0; font-size: 11px; color: var(--muted); opacity: .3; }

        .slot-locked {
            text-align: center; padding: 9px 3px; border-radius: 9px;
            background: rgba(254,226,226,.4); border: 1.5px dashed #fca5a5;
        }

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

        .break-row td {
            text-align: center; padding: 8px;
            font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            background: linear-gradient(135deg,#fef9ec,#fef3c7);
            color: #92400e; border-top: 1px solid #fde68a; border-bottom: 1px solid #fde68a;
        }

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
        .detail-head { padding: 16px 20px 14px; }
        .detail-head-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .detail-type { font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; margin-bottom: 3px; }
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

        footer { text-align: center; padding: 18px; font-size: 12px; color: var(--muted); }

        .hide-xs { display: inline; }
        .show-xs { display: none; }
        @media (max-width: 600px) {
            .hide-xs { display: none; }
            .show-xs { display: inline; }
            .field-row { grid-template-columns: 1fr; }
            .navbar-inner { padding: 0 1rem; }
            .hero, .main { padding-left: 1rem; padding-right: 1rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }

        td.td-sun[rowspan] {
            padding: 6px !important;
            vertical-align: top;
            width: calc((100% - 78px) / 7);
            max-width: calc((100% - 78px) / 7);
            overflow: hidden;
            height: 100%;
        }
        td.td-sun[rowspan] .sc {
            height: 100%;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 8px;
        }
        td.td-sun[rowspan] .bk-btn {
            height: 100%;
            min-height: 400px;
            width: 100%;
            justify-content: center;
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

.pub-nav-row2 {
    display: none; /* hidden di desktop */
}

@media (max-width: 600px) {
    /* Sembunyikan link di baris 1 saat mobile */
    .pub-links .pub-link {
        display: none;
    }

    /* Tampilkan baris 2 */
    .pub-nav-row2 {
        display: flex;
        align-items: center;
        gap: 2px;
        padding: 0 10px 8px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        border-top: 1px solid rgba(172,200,162,.1);
    }
    .pub-nav-row2::-webkit-scrollbar { display: none; }

    .pub-nav2-link {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: rgba(172,200,162,.55);
        text-decoration: none;
        white-space: nowrap;
        transition: color .15s, background .15s;
    }
    .pub-nav2-link:hover,
    .pub-nav2-link.on {
        color: #ACC8A2;
        background: rgba(172,200,162,.1);
    }
}

.page-trans{position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#1A2517,#2d3d29);opacity:0;pointer-events:none;transition:opacity .22s ease}
.page-trans.go{opacity:1;pointer-events:all}

.toast {
    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(20px);
    background: #1A2517; color: #fff;
    padding: 12px 22px; border-radius: 12px;
    font-size: 13px; font-weight: 600;
    box-shadow: 0 6px 24px rgba(0,0,0,.25);
    z-index: 9999; opacity: 0;
    transition: opacity .25s, transform .25s;
    white-space: nowrap;
    border-left: 4px solid #ef4444;
}
.toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    </style>
</head>
<body>

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
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="pub-btn">Login →</a>
            @endauth
        </div>
    </div>

    {{-- Baris kedua: navigasi (mobile only) --}}
    <div class="pub-nav-row2">
        <a href="{{ route('home') }}" class="pub-nav2-link {{ request()->routeIs('home') ? 'on' : '' }}">Jadwal</a>
        <a href="{{ route('inventory.public') }}" class="pub-nav2-link {{ request()->routeIs('inventory.public') ? 'on' : '' }}">Inventaris</a>
        <a href="{{ route('rekap.public') }}" class="pub-nav2-link {{ request()->routeIs('rekap.public') ? 'on' : '' }}">Rekap</a>
        <a href="{{ route('assignment.public') }}" class="pub-nav2-link {{ request()->routeIs('assignment.public') ? 'on' : '' }}">Tugas</a>
    </div>
</nav>

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

<div class="main">

    @if(session('success'))
        <div class="flash flash-ok">
            ✓ {{ session('success') }}
            <div style="margin-top:6px;font-size:12px;font-weight:400;color:#166534;">
                Pantau status booking di halaman jadwal. Slot yang sudah diajukan akan tampil dengan warna kuning (Pending).
            </div>
        </div>
    @endif
    @if($errors->has('error'))
        <div class="flash flash-err">⚠ {{ $errors->first('error') }}</div>
    @endif

    @php
        $nextWeekStart = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->addWeek();
        $isMaxWeek = $weekStart->gte($nextWeekStart);
    @endphp

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
        <button onclick="changeWeek('{{ $nextWeek }}')"
            class="week-btn week-btn-next"
            {{ $isMaxWeek ? 'disabled' : '' }}
            style="{{ $isMaxWeek ? 'opacity:.4;cursor:not-allowed;pointer-events:none' : '' }}">
            <span class="hide-xs">Minggu Depan</span>
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

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

    <div id="panels-wrap">
    @foreach($resources as $i => $resource)
    <div id="panel-{{ $resource->id }}" class="lab-panel" style="{{ $i !== 0 ? 'display:none' : '' }}">
        <div class="panel-card">

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

            <div class="swipe-hint">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Geser kiri/kanan untuk semua hari
            </div>

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
                            <td colspan="{{ count($days) }}">
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

                                $nonBreakSlots   = $timeSlots->where('is_break', false)->values();
                                $isFirstNonBreak = $nonBreakSlots->first()?->id === $slot->id;
                                $sunRowspan      = $timeSlots->count();
                                $sunKey          = $resource->id . '_' . $date;
                                $sunBook         = $sundayBookings->get($sunKey)?->first();
                            @endphp

                            @if($isSun)
                                @if($isFirstNonBreak)
                                <td class="slot-td td-sun" rowspan="{{ $sunRowspan }}" style="vertical-align:top;padding:6px;height:100%;">

                                    {{-- ═══════════════════════════════════════════════════════ --}}
                                    {{-- PERUBAHAN #1a: slot Minggu approved                    --}}
                                    {{-- SEBELUM: onclick="showDetail({teacher:'{{ addslashes(...) }}', ...})" --}}
                                    {{-- SESUDAH: data-detail='{{ json_encode(..., JSON_HEX_*) }}'           --}}
                                    {{-- ═══════════════════════════════════════════════════════ --}}
                                    @if($sunBook && $sunBook->status === 'approved')
                                        @php
                                        $detailSunApproved = json_encode([
                                            'type'         => 'approved',
                                            'teacher'      => $sunBook->teacher_name,
                                            'class_name'   => $sunBook->class_name ?? '',
                                            'subject'      => $sunBook->subject_name ?? '',
                                            'slot'         => 'Seharian',
                                            'time'         => '07:00–12:45',
                                            'day'          => 'Minggu',
                                            'date'         => \Carbon\Carbon::parse($date)->translatedFormat('d M Y'),
                                            'lab'          => $resource->name,
                                            'phone'        => $sunBook->teacher_phone ?? '',
                                            'title'        => $sunBook->title ?? '',
                                            'desc'         => $sunBook->description ?? '',
                                            'participants' => (string)($sunBook->participant_count ?? ''),
                                        ], JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
                                        @endphp
                                        <div class="sc sc-approved"
                                             style="cursor:pointer;min-height:80px;display:flex;flex-direction:column;justify-content:flex-start;align-items:flex-start;"
                                             role="button"
                                             tabindex="0"
                                             data-detail='{{ $detailSunApproved }}'
                                             onclick="showDetail(JSON.parse(this.dataset.detail))"
                                             onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                            <div class="sc-name">{{ $sunBook->teacher_name }}</div>
                                            <div class="sc-class">{{ $sunBook->class_name }}</div>
                                            @if($sunBook->subject_name)<div class="sc-subject">{{ $sunBook->subject_name }}</div>@endif
                                            <div class="sc-status">✓ Disetujui · Seharian</div>
                                        </div>

                                    {{-- ═══════════════════════════════════════════════════════ --}}
                                    {{-- PERUBAHAN #1b: slot Minggu pending                     --}}
                                    {{-- ═══════════════════════════════════════════════════════ --}}
                                    @elseif($sunBook && $sunBook->status === 'pending')
                                        @php
                                        $detailSunPending = json_encode([
                                            'type'         => 'pending',
                                            'teacher'      => $sunBook->teacher_name,
                                            'class_name'   => $sunBook->class_name ?? '',
                                            'subject'      => $sunBook->subject_name ?? '',
                                            'slot'         => 'Seharian',
                                            'time'         => '07:00–12:45',
                                            'day'          => 'Minggu',
                                            'date'         => \Carbon\Carbon::parse($date)->translatedFormat('d M Y'),
                                            'lab'          => $resource->name,
                                            'phone'        => $sunBook->teacher_phone ?? '',
                                            'title'        => $sunBook->title ?? '',
                                            'desc'         => $sunBook->description ?? '',
                                            'participants' => (string)($sunBook->participant_count ?? ''),
                                        ], JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
                                        @endphp
                                        <div class="sc sc-pending"
                                             style="cursor:pointer;min-height:80px;display:flex;flex-direction:column;justify-content:flex-start;align-items:flex-start;"
                                             role="button"
                                             tabindex="0"
                                             data-detail='{{ $detailSunPending }}'
                                             onclick="showDetail(JSON.parse(this.dataset.detail))"
                                             onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                            <div class="sc-name">{{ $sunBook->teacher_name }}</div>
                                            <div class="sc-class">{{ $sunBook->class_name }}</div>
                                            <div class="sc-status">⏳ Pending · Seharian</div>
                                        </div>

                                    @elseif($isPast)
                                        <div class="slot-past" style="padding:30px 0;">Lewat</div>

                                    @else
                                        <button class="bk-btn bk-btn-sun" style="height:100%;min-height:400px;width:100%;box-sizing:border-box;"
                                            onclick="openSundayBooking({{ $resource->id }},'{{ e($resource->name) }}','{{ $date }}')">
                                            <svg class="bk-icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span class="bk-text">Booking</span>
                                            <span style="font-size:9px;color:#fca5a5;margin-top:2px">Seharian</span>
                                        </button>
                                    @endif
                                </td>
                                @endif

                            @else
                            {{-- ─── HARI BIASA ─── --}}
                            <td class="slot-td {{ $isToday ? 'td-today' : '' }}">

                                {{-- ═══════════════════════════════════════════════════════ --}}
                                {{-- PERUBAHAN #2a: slot tetap                              --}}
                                {{-- SEBELUM: onclick="showDetail({teacher:'{{ addslashes($sched->teacher_name) }}', ...})" --}}
                                {{-- SESUDAH: data-detail='{{ json_encode([...], JSON_HEX_*) }}'                           --}}
                                {{-- ═══════════════════════════════════════════════════════ --}}
                                @if($sched)
                                    @php
                                    $detailTetap = json_encode([
                                        'type'         => 'tetap',
                                        'teacher'      => $sched->teacher_name,
                                        'class_name'   => $sched->labClass?->name ?? '-',
                                        'subject'      => $sched->subject_name ?? '',
                                        'slot'         => $slot->name,
                                        'time'         => \Carbon\Carbon::parse($slot->start_time)->format('H:i')
                                                          . ($slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : ''),
                                        'day'          => $day,
                                        'date'         => \Carbon\Carbon::parse($date)->translatedFormat('d M Y'),
                                        'lab'          => $resource->name,
                                        'phone'        => '',
                                        'title'        => '',
                                        'desc'         => '',
                                        'participants' => '',
                                    ], JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
                                    @endphp
                                    <div class="sc sc-tetap"
                                         style="cursor:pointer"
                                         role="button"
                                         tabindex="0"
                                         data-detail='{{ $detailTetap }}'
                                         onclick="showDetail(JSON.parse(this.dataset.detail))"
                                         onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                        <div class="sc-name">{{ $sched->teacher_name }}</div>
                                        <div class="sc-class">{{ $sched->labClass?->name ?? '-' }}</div>
                                        @if($sched->subject_name)<div class="sc-subject">{{ $sched->subject_name }}</div>@endif
                                    </div>

                                {{-- ═══════════════════════════════════════════════════════ --}}
                                {{-- PERUBAHAN #2b: booking approved                        --}}
                                {{-- ═══════════════════════════════════════════════════════ --}}
                                @elseif($book && $book->status === 'approved')
                                    @php
                                    $detailApproved = json_encode([
                                        'type'         => 'approved',
                                        'teacher'      => $book->teacher_name,
                                        'class_name'   => $book->class_name ?? '',
                                        'subject'      => $book->subject_name ?? '',
                                        'slot'         => $slot->name,
                                        'time'         => \Carbon\Carbon::parse($slot->start_time)->format('H:i')
                                                          . ($slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : ''),
                                        'day'          => $day,
                                        'date'         => \Carbon\Carbon::parse($date)->translatedFormat('d M Y'),
                                        'lab'          => $resource->name,
                                        'phone'        => $book->teacher_phone ?? '',
                                        'title'        => $book->title ?? '',
                                        'desc'         => $book->description ?? '',
                                        'participants' => (string)($book->participant_count ?? ''),
                                    ], JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
                                    @endphp
                                    <div class="sc sc-approved"
                                         style="cursor:pointer"
                                         role="button"
                                         tabindex="0"
                                         data-detail='{{ $detailApproved }}'
                                         onclick="showDetail(JSON.parse(this.dataset.detail))"
                                         onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                        <div class="sc-name">{{ $book->teacher_name }}</div>
                                        <div class="sc-class">{{ $book->class_name }}</div>
                                        <div class="sc-status">✓ Disetujui</div>
                                    </div>

                                {{-- ═══════════════════════════════════════════════════════ --}}
                                {{-- PERUBAHAN #2c: booking pending                         --}}
                                {{-- ═══════════════════════════════════════════════════════ --}}
                                @elseif($book && $book->status === 'pending')
                                    @php
                                    $detailPending = json_encode([
                                        'type'         => 'pending',
                                        'teacher'      => $book->teacher_name,
                                        'class_name'   => $book->class_name ?? '',
                                        'subject'      => $book->subject_name ?? '',
                                        'slot'         => $slot->name,
                                        'time'         => \Carbon\Carbon::parse($slot->start_time)->format('H:i')
                                                          . ($slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : ''),
                                        'day'          => $day,
                                        'date'         => \Carbon\Carbon::parse($date)->translatedFormat('d M Y'),
                                        'lab'          => $resource->name,
                                        'phone'        => $book->teacher_phone ?? '',
                                        'title'        => $book->title ?? '',
                                        'desc'         => $book->description ?? '',
                                        'participants' => (string)($book->participant_count ?? ''),
                                    ], JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
                                    @endphp
                                    <div class="sc sc-pending"
                                         style="cursor:pointer"
                                         role="button"
                                         tabindex="0"
                                         data-detail='{{ $detailPending }}'
                                         onclick="showDetail(JSON.parse(this.dataset.detail))"
                                         onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                        <div class="sc-name">{{ $book->teacher_name }}</div>
                                        <div class="sc-class">{{ $book->class_name }}</div>
                                        <div class="sc-status">⏳ Pending</div>
                                    </div>

                                @elseif($isPast || $isSlotPast)
                                    <div class="slot-past">Lewat</div>

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
                                    <button class="bk-btn"
                                        onclick="openBooking({{ $resource->id }},'{{ e($resource->name) }}',{{ $slot->id }},'{{ e($slot->name) }}','{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}{{ $slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : '' }}','{{ $dayEn }}','{{ $day }}','{{ $date }}',{{ json_encode($takenSlotIds) }})">
                                        <svg class="bk-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span class="bk-text">Booking</span>
                                    </button>
                                @endif
                            </td>
                            @endif

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
    </div>
</div>

{{-- ═══ DETAIL MODAL ═══ --}}
<div class="detail-overlay" id="detail-overlay" onclick="if(event.target===this)closeDetail()"
     role="dialog" aria-modal="true" aria-labelledby="d-teacher">
    <div class="detail-box" id="detail-box">
        <div class="detail-head" id="detail-head">
            <div class="detail-head-top">
                <div>
                    <div class="detail-type" id="d-type"></div>
                    <div class="detail-teacher" id="d-teacher"></div>
                </div>
                <button class="detail-close" onclick="closeDetail()" aria-label="Tutup detail">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="detail-body" id="detail-body"></div>
    </div>
</div>

{{-- ═══ MODAL SUNDAY BOOKING ═══ --}}
<div class="modal-overlay" id="sunday-modal-overlay" onclick="if(event.target===this)closeSundayModal()"
     role="dialog" aria-modal="true" aria-labelledby="sunday-modal-title">
    <div class="modal-box" id="sunday-modal-box">
        <div class="modal-head">
            <div class="modal-head-top">
                <div>
                    <p class="modal-eyebrow">📅 Booking Hari Minggu</p>
                    <h2 class="modal-title" id="sunday-modal-title">Booking Seharian</h2>
                </div>
                <button class="modal-close" onclick="closeSundayModal()" aria-label="Tutup form booking Minggu">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-badges">
                <span class="badge" id="sb-lab">🖥 -</span>
                <span class="badge" id="sb-date">📅 -</span>
                <span class="badge" style="background:rgba(248,113,113,.15);color:#fca5a5;border-color:rgba(248,113,113,.3)">🕐 Seharian · 07:00–12:45</span>
            </div>
        </div>

        <form method="POST" action="{{ route('sunday.booking.store') }}" class="modal-body">
            @csrf
            <input type="hidden" name="resource_id"  id="sb_rid">
            <input type="hidden" name="booking_date" id="sb_date_val">
            <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">

            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 13px;font-size:12px;color:#991b1b;font-weight:600;">
                🔴 Booking ini akan menggunakan lab <strong>seharian penuh</strong> di hari Minggu.
            </div>

            <div class="field-row">
                <div style="position:relative">
                    <label class="field-label">Nama Pengajar *</label>
                    <input name="teacher_name" id="sb_teacher_name" type="text"
                        placeholder="Nama lengkap" class="inp" required autocomplete="off"
                        oninput="filterTeacherSunday(this.value)">
                    <div id="sb_teacher_sug" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid #ACC8A2;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.1);z-index:999;max-height:180px;overflow-y:auto"></div>
                </div>
                <div>
                    <label class="field-label">Nomor HP *</label>
                    <input name="teacher_phone" id="sb_teacher_phone" type="text"
                        placeholder="08xxxxxxxxxx" class="inp" required>
                </div>
            </div>

            <div>
                <label class="field-label">Unit Sekolah *</label>
                <select name="organization_id" id="sb_org" class="inp" required
                    onchange="loadKelasSunday(this.value)" style="appearance:auto">
                    <option value="">— Pilih unit sekolah —</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label">Kelas *</label>
                <select name="class_id" id="sb_class" class="inp" required disabled style="appearance:auto">
                    <option value="">— Pilih unit sekolah dulu —</option>
                </select>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label">Mata Pelajaran *</label>
                    <input name="subject_name" type="text" placeholder="Contoh: TIK" class="inp" required>
                </div>
                <div>
                    <label class="field-label">Jumlah Peserta *</label>
                    <input name="participant_count" type="number" min="1" placeholder="0" class="inp" required>
                </div>
            </div>

            <div>
                <label class="field-label">Judul Kegiatan *</label>
                <input name="title" type="text" placeholder="Contoh: Latihan KIR" class="inp" required>
            </div>

            <div>
                <label class="field-label">Keterangan</label>
                <input name="description" type="text" placeholder="Opsional" class="inp">
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeSundayModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">✓ Ajukan Booking Minggu</button>
            </div>
        </form>
    </div>
</div>

<footer>© {{ date('Y') }} Lab Management System · Nuris Jember</footer>

{{-- ═══ MODAL BOOKING ═══ --}}
<div class="modal-overlay" id="modal-overlay" onclick="if(event.target===this)closeModal()"
     role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-box" id="modal-box">
        <div class="modal-head">
            <div class="modal-head-top">
                <div>
                    <p class="modal-eyebrow">Form Booking Lab</p>
                    <h2 class="modal-title" id="modal-title">Ajukan Penggunaan Lab</h2>
                </div>
                <button class="modal-close" onclick="closeModal()" aria-label="Tutup form booking">
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

            {{-- ═══════════════════════════════════════════════════════════════════ --}}
            {{-- PERUBAHAN #3: autocomplete teacher — innerHTML diganti createElement --}}
            {{-- Fungsi filterTeacher() & selectTeacher() ada di blok <script> bawah --}}
            {{-- ═══════════════════════════════════════════════════════════════════ --}}

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
const TEACHERS  = @json($teachers);

// ═══════════════════════════════════════════════════════════════════════════
// PERUBAHAN #3: filterTeacher — innerHTML diganti createElement + textContent
// SEBELUM: box.innerHTML = filtered.map(t => `<div>...${t.name}...${t.phone}...</div>`).join('')
// SESUDAH: setiap item dibuat lewat createElement, nilai diisi via textContent
// ═══════════════════════════════════════════════════════════════════════════
function buildSuggestionItem(name, phone, onClickFn) {
    const item = document.createElement('div');
    item.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center';
    item.addEventListener('mouseover', () => item.style.background = '#f0f7ee');
    item.addEventListener('mouseout',  () => item.style.background = '');
    item.addEventListener('click', onClickFn);

    const nameSpan = document.createElement('span');
    nameSpan.style.cssText = 'font-weight:600;color:#1A2517';
    nameSpan.textContent = name; // ← textContent, bukan innerHTML

    const phoneSpan = document.createElement('span');
    phoneSpan.style.cssText = 'font-size:11px;color:#9ca3af';
    phoneSpan.textContent = phone || ''; // ← textContent

    item.appendChild(nameSpan);
    item.appendChild(phoneSpan);
    return item;
}

function filterTeacher(val) {
    const box = document.getElementById('teacher_suggestions');
    if (!val || val.length < 2) { box.style.display = 'none'; return; }
    const filtered = TEACHERS.filter(t => t.name.toLowerCase().includes(val.toLowerCase()));
    if (!filtered.length) { box.style.display = 'none'; return; }
    box.innerHTML = '';
    filtered.forEach(t => {
        box.appendChild(buildSuggestionItem(t.name, t.phone, () => selectTeacher(t.name, t.phone ?? '')));
    });
    box.style.display = 'block';
}

function selectTeacher(name, phone) {
    document.getElementById('inp_teacher_name').value  = name;
    document.getElementById('inp_teacher_phone').value = phone;
    document.getElementById('teacher_suggestions').style.display = 'none';
}

// ═══════════════════════════════════════════════════════════════════════════
// PERUBAHAN #4: filterTeacherSunday — sama, pakai createElement + textContent
// ═══════════════════════════════════════════════════════════════════════════
function filterTeacherSunday(val) {
    const box = document.getElementById('sb_teacher_sug');
    if (!val || val.length < 2) { box.style.display = 'none'; return; }
    const filtered = TEACHERS.filter(t => t.name.toLowerCase().includes(val.toLowerCase()));
    if (!filtered.length) { box.style.display = 'none'; return; }
    box.innerHTML = '';
    filtered.forEach(t => {
        box.appendChild(buildSuggestionItem(t.name, t.phone, () => selectTeacherSunday(t.name, t.phone ?? '')));
    });
    box.style.display = 'block';
}

function selectTeacherSunday(name, phone) {
    document.getElementById('sb_teacher_name').value  = name;
    document.getElementById('sb_teacher_phone').value = phone;
    document.getElementById('sb_teacher_sug').style.display = 'none';
}

// Tutup dropdown saat klik di luar — digabung jadi satu listener
document.addEventListener('click', function(e) {
    if (!e.target.closest('#inp_teacher_name') && !e.target.closest('#teacher_suggestions'))
        document.getElementById('teacher_suggestions').style.display = 'none';
    if (!e.target.closest('#sb_teacher_name') && !e.target.closest('#sb_teacher_sug'))
        document.getElementById('sb_teacher_sug').style.display = 'none';
});

// ─── TABS ─────────────────────────────────────────────────────────────────
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

// ─── BOOKING MODAL ────────────────────────────────────────────────────────
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

    const slotIdx   = ALL_SLOTS.findIndex(s => s.id == sid);
    const wrap      = document.getElementById('slot-options');
    const startTime = stime.split('\u2013')[0].split('-')[0].trim();
    wrap.innerHTML  = '';

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

    if (availableSlots.length > 2) {
        const btnAll = document.createElement('button');
        btnAll.type  = 'button';
        btnAll.className = 'slot-opt slot-opt-full';
        btnAll.style.cssText = 'background:linear-gradient(135deg,#1A2517,#2a3826);color:#ACC8A2;border-color:#3d5438;min-width:90px';
        const lastT   = availableSlots[availableSlots.length - 1];
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

// ─── DETAIL MODAL ─────────────────────────────────────────────────────────
const TYPE_CONFIG = {
    tetap:    { label: '📅 Jadwal Tetap',         headBg: 'linear-gradient(135deg,#2a4a28,#3a6b38)', typeColor: 'rgba(172,200,162,.7)', teacherColor: '#d6ead2' },
    approved: { label: '✓ Booking Disetujui',     headBg: 'linear-gradient(135deg,#14532d,#166534)', typeColor: '#86efac',             teacherColor: '#d1fae5' },
    pending:  { label: '⏳ Menunggu Persetujuan', headBg: 'linear-gradient(135deg,#78350f,#92400e)', typeColor: '#fcd34d',             teacherColor: '#fef3c7' },
};

// ═══════════════════════════════════════════════════════════════════════════
// PERUBAHAN #5 (utama): showDetail — innerHTML diganti createElement + textContent
// SEBELUM: detail-body.innerHTML = rows.map(r => `<div>...${r.val}...</div>`).join('')
//          → r.val tidak di-escape, XSS bisa masuk lewat data server
// SESUDAH: setiap elemen dibuat via createElement, nilai diisi via textContent
//          → tidak ada string yang diparse sebagai HTML oleh browser
// ═══════════════════════════════════════════════════════════════════════════
function showDetail(d) {
    const cfg = TYPE_CONFIG[d.type] || TYPE_CONFIG.tetap;

    const head = document.getElementById('detail-head');
    head.style.background = cfg.headBg;

    // textContent — tidak pernah diparse sebagai HTML
    document.getElementById('d-type').style.color    = cfg.typeColor;
    document.getElementById('d-type').textContent    = cfg.label;
    document.getElementById('d-teacher').style.color = cfg.teacherColor;
    document.getElementById('d-teacher').textContent = d.teacher;

    const rows = [
        { icon: '🖥', key: 'Lab',      val: d.lab },
        { icon: '📚', key: 'Kelas',    val: d.class_name || '-' },
        { icon: '📖', key: 'Mapel',    val: d.subject || '-' },
        { icon: '📅', key: 'Hari',     val: d.day + ', ' + d.date },
        { icon: '🕐', key: 'Slot',     val: d.slot + ' · ' + d.time },
    ];
    if (d.title)        rows.push({ icon: '📝', key: 'Kegiatan',   val: d.title });
    if (d.participants) rows.push({ icon: '👥', key: 'Peserta',    val: d.participants + ' orang' });
    if (d.phone)        rows.push({ icon: '📞', key: 'No. HP',     val: d.phone });
    if (d.desc)         rows.push({ icon: '💬', key: 'Keterangan', val: d.desc });

    const body = document.getElementById('detail-body');
    body.innerHTML = ''; // kosongkan container — aman karena tidak ada user data di sini

    rows.forEach(function(r) {
        const wrap = document.createElement('div');
        wrap.className = 'detail-row';

        const iconEl = document.createElement('span');
        iconEl.className = 'detail-icon';
        iconEl.textContent = r.icon; // emoji via textContent

        const info = document.createElement('div');

        const keyEl = document.createElement('div');
        keyEl.className = 'detail-key';
        keyEl.textContent = r.key; // ← textContent

        const valEl = document.createElement('div');
        valEl.className = 'detail-val';
        valEl.textContent = r.val; // ← textContent, bukan innerHTML

        info.appendChild(keyEl);
        info.appendChild(valEl);
        wrap.appendChild(iconEl);
        wrap.appendChild(info);
        body.appendChild(wrap);
    });

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

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeModal(); closeDetail(); closeSundayModal(); }
});

// ─── AJAX WEEK NAVIGATION ─────────────────────────────────────────────────
let fetchController    = null;
let currentActiveTabId = null;

function getNextMonday() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const day = today.getDay();
    const daysToMonday = day === 0 ? 1 : 8 - day;
    const next = new Date(today);
    next.setDate(today.getDate() + daysToMonday);
    return next;
}

function changeWeek(week) {
    const targetDate = new Date(week + 'T00:00:00');
    if (targetDate > getNextMonday()) {
        showToast('⛔ Jadwal hanya bisa dilihat sampai 1 minggu ke depan.');
        return;
    }

    if (fetchController) fetchController.abort();
    fetchController = new AbortController();

    const activeTab = document.querySelector('.tab-btn.tab-active');
    if (activeTab) currentActiveTabId = activeTab.id.replace('tab-', '');

    document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');
    document.getElementById('skeleton').style.display = 'block';
    document.querySelectorAll('.week-btn').forEach(b => { b.disabled = true; b.style.opacity = '.6'; });

    const url = new URL(window.location.href);
    url.searchParams.set('week', week);
    window.history.pushState({ week }, '', url.toString());

    fetch(url.toString(), {
        signal: fetchController.signal,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => { fetchController = null; applyWeekResponse(html, week); })
    .catch(err => {
        if (err.name === 'AbortError') return;
        fetchController = null;
        window.location.href = url.toString();
    });
}

function applyWeekResponse(html, week) {
    const parser = new DOMParser();
    const newDoc = parser.parseFromString(html, 'text/html');

    const newLabel = newDoc.getElementById('week-label');
    if (newLabel) document.getElementById('week-label').innerHTML = newLabel.innerHTML;

    const newWrap = newDoc.getElementById('panels-wrap');
    const oldWrap = document.getElementById('panels-wrap');
    if (newWrap && oldWrap) oldWrap.innerHTML = newWrap.innerHTML;

    const newBtns = newDoc.querySelectorAll('.week-btn');
    const oldBtns = document.querySelectorAll('.week-btn');
    newBtns.forEach((nb, i) => {
        if (!oldBtns[i]) return;
        const match = nb.getAttribute('onclick')?.match(/'([^']+)'/);
        if (match) oldBtns[i].setAttribute('onclick', `changeWeek('${match[1]}')`);
    });

    newDoc.querySelectorAll('script').forEach(s => {
        const m = s.textContent.match(/const ALL_SLOTS\s*=\s*(\[[\s\S]*?\]);/);
        if (m) { try { window.ALL_SLOTS = JSON.parse(m[1]); } catch(e) {} }
    });

    document.getElementById('skeleton').style.display = 'none';
    document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');

    const targetId = currentActiveTabId || document.querySelector('.lab-panel')?.id?.replace('panel-', '');
    const target   = targetId ? document.getElementById('panel-' + targetId) : document.querySelector('.lab-panel');
    if (target) {
        target.style.display = '';
        target.style.animation = 'none';
        void target.offsetWidth;
        target.style.animation = '';
    }

    document.querySelectorAll('.week-btn').forEach(b => { b.disabled = false; b.style.opacity = ''; });

    const nextBtn = document.querySelector('.week-btn-next');
    if (nextBtn) {
        if (new Date(week + 'T00:00:00') >= getNextMonday()) {
            nextBtn.disabled = true;
            nextBtn.style.opacity = '.4';
            nextBtn.style.cursor = 'not-allowed';
            nextBtn.style.pointerEvents = 'none';
        } else {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '';
            nextBtn.style.cursor = '';
            nextBtn.style.pointerEvents = '';
        }
    }
}

window.addEventListener('popstate', (e) => {
    const week = e.state?.week;
    if (!week) return;
    if (fetchController) { fetchController.abort(); fetchController = null; }
    const activeTab = document.querySelector('.tab-btn.tab-active');
    if (activeTab) currentActiveTabId = activeTab.id.replace('tab-', '');
    document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');
    document.getElementById('skeleton').style.display = 'block';
    document.querySelectorAll('.week-btn').forEach(b => { b.disabled = true; b.style.opacity = '.6'; });
    const url = new URL(window.location.href);
    fetchController = new AbortController();
    fetch(url.toString(), {
        signal: fetchController.signal,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => { fetchController = null; applyWeekResponse(html, week); })
    .catch(err => { if (err.name === 'AbortError') return; window.location.href = url.toString(); });
});

function showToast(msg) {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => toast.classList.remove('show'), 3000);
}

function loadKelas(orgId) {
    const sel = document.getElementById('f_class');
    if (!orgId) { sel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>'; sel.disabled = true; return; }
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled  = true;
    fetch('/kelas?organization_id=' + encodeURIComponent(orgId))
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— Pilih kelas —</option>';
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value       = c.id;
                opt.textContent = c.name; // ← textContent
                sel.appendChild(opt);
            });
            sel.disabled = false;
        })
        .catch(() => { sel.innerHTML = '<option value="">Gagal memuat</option>'; });
}

function loadKelasSunday(orgId) {
    const sel = document.getElementById('sb_class');
    if (!orgId) { sel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>'; sel.disabled = true; return; }
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled  = true;
    fetch('/kelas?organization_id=' + encodeURIComponent(orgId))
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— Pilih kelas —</option>';
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value       = c.id;
                opt.textContent = c.name; // ← textContent
                sel.appendChild(opt);
            });
            sel.disabled = false;
        })
        .catch(() => { sel.innerHTML = '<option value="">Gagal memuat</option>'; });
}

// ─── SUNDAY BOOKING ───────────────────────────────────────────────────────
function openSundayBooking(rid, rname, date) {
    document.getElementById('sb_rid').value      = rid;
    document.getElementById('sb_date_val').value = date;
    const d  = new Date(date + 'T00:00:00');
    const mn = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('sb-lab').textContent  = '🖥 ' + rname;
    document.getElementById('sb-date').textContent = '📅 Minggu, ' + d.getDate() + ' ' + mn[d.getMonth()] + ' ' + d.getFullYear();
    document.getElementById('sb_teacher_name').value  = '';
    document.getElementById('sb_teacher_phone').value = '';
    document.getElementById('sb_org').value = '';
    document.getElementById('sb_class').innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
    document.getElementById('sb_class').disabled  = true;
    document.getElementById('sunday-modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeSundayModal() {
    const overlay = document.getElementById('sunday-modal-overlay');
    const box     = document.getElementById('sunday-modal-box');
    box.style.transition = 'opacity .16s, transform .16s';
    box.style.opacity    = '0';
    box.style.transform  = 'translateY(10px) scale(.97)';
    setTimeout(() => {
        overlay.classList.remove('show');
        box.style.transition = box.style.opacity = box.style.transform = '';
        document.body.style.overflow = '';
    }, 160);
}

// ─── DOUBLE SUBMIT PREVENTION ─────────────────────────────────────────────
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const btn = this.querySelector('.btn-submit');
        if (!btn) return;
        if (btn.dataset.loading === 'true') { e.preventDefault(); return; }
        btn.dataset.loading = 'true';
        btn.disabled = true;
        btn.style.opacity = '.7';
        btn.style.cursor = 'not-allowed';
        btn.textContent = '⏳ Memproses...';
        setTimeout(() => {
            btn.dataset.loading = 'false';
            btn.disabled = false;
            btn.style.opacity = '';
            btn.style.cursor = '';
            btn.textContent = '✓ Ajukan Booking';
        }, 10000);
    });
});

// ─── SPA PAGE TRANSITION ──────────────────────────────────────────────────
document.querySelectorAll('a.pub-link, a.pub-btn, a.pub-brand').forEach(a => {
    const href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript') || a.getAttribute('target') === '_blank') return;
    a.addEventListener('click', function(e) {
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

window.addEventListener('pageshow', () => {
    document.getElementById('pt').classList.remove('go');
});
</script>

<div class="page-trans" id="pt"></div>
</body>
</html>