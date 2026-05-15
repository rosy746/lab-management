{{-- resources/views/rekap/public.blade.php --}}
@extends('layouts.public-schedule')

@section('title', 'Rekap Penggunaan Lab')

@section('vite')
@vite(['resources/css/rekap.css'])
@endsection

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="hero">
    <h1>📊 Rekap Penggunaan Laboratorium</h1>
    <p>{{ $months[$month] }} {{ $year }} · Jadwal tetap & booking digabung</p>
</div>

{{-- ═══ FILTER ═══ --}}
<div class="filter-bar">
    <form method="GET" action="/rekap" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <select name="month" class="inp">
            @foreach($months as $m => $mName)
            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $mName }}</option>
            @endforeach
        </select>
        <select name="year" class="inp">
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Tampilkan</button>
    </form>
    <div class="period-lbl">{{ $startDate->translatedFormat('d M') }} – {{ $endDate->translatedFormat('d M Y') }}</div>
</div>

{{-- ═══ MAIN WRAP ═══ --}}
<div class="wrap">

    {{-- SUMMARY --}}
    <div class="sum-grid">
        <div class="sum-card">
            <div class="sum-lbl">Total Kapasitas</div>
            <div class="sum-val" style="color:#6b7280">{{ $summary['total_capacity'] }}</div>
            <div class="sum-sub">Slot tersedia bulan ini</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Jadwal Tetap</div>
            <div class="sum-val" style="color:#1A2517">{{ $summary['total_scheduled'] }}</div>
            <div class="sum-sub">Slot terisi rutin</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Booking</div>
            <div class="sum-val" style="color:#2563eb">{{ $summary['total_booking'] }}</div>
            <div class="sum-sub">Booking disetujui</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Tingkat Penggunaan</div>
            @php
                $pc       = $summary['total_pct'];
                $pcColor  = $pc >= 70 ? '#16a34a' : ($pc >= 40 ? '#d97706' : '#dc2626');
                $barColor = $pc >= 70 ? '#22c55e' : ($pc >= 40 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="sum-val" style="color:{{ $pcColor }}">{{ $pc }}%</div>
            <div class="sum-sub">{{ $summary['total_used'] }} dari {{ $summary['total_capacity'] }} slot</div>
            <div class="pbar-wrap">
                <div class="pbar" style="width:{{ $pc }}%;background:{{ $barColor }}"></div>
            </div>
        </div>
    </div>

    {{-- EXPORT BAR --}}
    <div class="export-bar">
        <span class="export-bar-label">Export:</span>
        <button class="btn-exp btn-exp-xl" onclick="exportExcel()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 17l1.5-2.5L8.5 12H10l.75 1.5L11.5 12H13l-1.5 2.5L13 17h-1.5l-.75-1.5-.75 1.5H8.5z"/></svg>
            Excel
        </button>
        <button class="btn-exp btn-exp-csv" onclick="exportCSV()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/></svg>
            CSV
        </button>
        <button class="btn-exp btn-exp-pdf" onclick="exportPDF()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/></svg>
            PDF
        </button>
        <button class="btn-exp btn-exp-print" onclick="window.print()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6v-8z"/></svg>
            Print
        </button>
    </div>

    {{-- TABS --}}
    <div class="tabs">
        @foreach($labData as $i => $lab)
        <button class="tab {{ $i === 0 ? 'on' : '' }}" onclick="switchTab({{ $i }})">
            <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ $lab['resource']->name }}
            <span class="tab-pct">{{ $lab['percentage'] }}%</span>
        </button>
        @endforeach
    </div>

    {{-- PANELS --}}
    @foreach($labData as $i => $lab)
    @php
        $pct      = $lab['percentage'];
        $pctColor = $pct >= 70 ? '#86efac' : ($pct >= 40 ? '#fcd34d' : '#f87171');
        $ps       = $lab['totalCapacity'] > 0 ? ($lab['scheduledSlots'] / $lab['totalCapacity'] * 100) : 0;
        $pb       = $lab['totalCapacity'] > 0 ? ($lab['bookingSlots']   / $lab['totalCapacity'] * 100) : 0;
    @endphp
    <div class="panel {{ $i === 0 ? 'on' : '' }}" id="panel-{{ $i }}">
        <div class="lab-card">

            {{-- Header --}}
            <div class="lab-hdr">
                <div>
                    <h2 style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:18px;color:#fff;margin:0">
                        🏫 {{ $lab['resource']->name }}
                    </h2>
                    <p style="font-size:11px;color:rgba(172,200,162,.4);margin-top:4px">
                        {{ $lab['totalCapacity'] }} slot kapasitas · {{ $totalSlotPerDay }} slot/hari
                    </p>
                </div>
                <div style="display:flex;align-items:center;gap:14px">
                    <div style="text-align:right">
                        <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:32px;font-weight:800;color:{{ $pctColor }};line-height:1">{{ $pct }}%</div>
                        <div style="font-size:10px;color:rgba(172,200,162,.35);margin-top:2px">Tingkat Penggunaan</div>
                    </div>
                    <svg viewBox="0 0 36 36" style="width:56px;height:56px;transform:rotate(-90deg)">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(172,200,162,.1)" stroke-width="3.5"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $pctColor }}" stroke-width="3.5"
                            stroke-dasharray="{{ $pct }} {{ 100-$pct }}" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            {{-- Stats --}}
            <div class="stat-row">
                <div class="stat-cell" style="background:rgba(172,200,162,.05)">
                    <div class="stat-val" style="color:#1A2517">{{ $lab['scheduledSlots'] }}</div>
                    <div class="stat-key">Jadwal Tetap</div>
                </div>
                <div class="stat-cell" style="background:#eff6ff">
                    <div class="stat-val" style="color:#2563eb">{{ $lab['bookingSlots'] }}</div>
                    <div class="stat-key">Booking</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-val" style="color:#1A2517">{{ $lab['totalUsed'] }}</div>
                    <div class="stat-key">Total Terpakai</div>
                </div>
                <div class="stat-cell" style="background:#f9fafb">
                    <div class="stat-val" style="color:#9ca3af">{{ $lab['totalFree'] }}</div>
                    <div class="stat-key">Slot Kosong</div>
                </div>
            </div>

            {{-- Progress --}}
            <div class="prog">
                <div class="prog-row">
                    <span class="prog-lbl">Jadwal Tetap</span>
                    <div class="prog-track">
                        <div class="prog-fill" style="width:{{ $ps }}%;background:linear-gradient(90deg,#ACC8A2,#3d5438)"></div>
                    </div>
                    <span class="prog-pct" style="color:#1A2517">{{ round($ps,1) }}%</span>
                </div>
                <div class="prog-row">
                    <span class="prog-lbl">Booking</span>
                    <div class="prog-track">
                        <div class="prog-fill" style="width:{{ $pb }}%;background:linear-gradient(90deg,#93c5fd,#2563eb)"></div>
                    </div>
                    <span class="prog-pct" style="color:#2563eb">{{ round($pb,1) }}%</span>
                </div>
            </div>

            {{-- Calendar --}}
            <div class="cal">
                <div class="sec-lbl">Kalender Penggunaan</div>
                <div class="cal-grid">
                    @foreach($lab['dailyData'] as $day)
                    @php
                        $dc = 'dc-mt';
                        if ($day['isSunday'])                                  $dc = 'dc-sun';
                        elseif ($day['schedule'] > 0 && $day['booking'] > 0)  $dc = 'dc-both';
                        elseif ($day['schedule'] > 0)                          $dc = 'dc-sch';
                        elseif ($day['booking']  > 0)                          $dc = 'dc-book';
                    @endphp
                    <div class="dc {{ $dc }}"
                         style="{{ $day['isToday'] ? 'outline:2px solid #ACC8A2;outline-offset:1px;' : '' }}"
                         title="{{ $day['date']->translatedFormat('d M Y') }} — Jadwal: {{ $day['schedule'] }} | Booking: {{ $day['booking'] }} | Total: {{ $day['total'] }}/{{ $day['capacity'] }} slot">
                        <div>{{ $day['date']->format('d') }}</div>
                        @if(!$day['isSunday'] && $day['total'] > 0)
                        <div style="font-size:8px;margin-top:1px">{{ $day['total'] }}/{{ $day['capacity'] }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="legend">
                    @foreach([
                        ['dc-sch leg-dot', 'Jadwal Tetap'],
                        ['dc-book leg-dot', 'Booking'],
                        ['dc-both leg-dot', 'Keduanya'],
                        ['dc-mt leg-dot',  'Kosong'],
                        ['dc-sun leg-dot',  'Minggu'],
                    ] as $l)
                    <div class="leg">
                        <div class="leg-dot dc {{ $l[0] }}" style="padding:0;min-width:14px"></div>
                        {{ $l[1] }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Detail --}}
            <div class="detail">

                @if($lab['scheduleDetails']->isNotEmpty())
                <div class="detail-heading" style="color:#3d5438">
                    <span>📅</span> Jadwal Tetap
                    <span style="font-size:11px;font-weight:400;color:#9ca3af">
                        ({{ $lab['scheduleDetails']->count() }} entri · {{ $lab['scheduledSlots'] }} slot/bulan)
                    </span>
                </div>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Slot Waktu</th>
                                <th>Guru / Pengajar</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th style="text-align:center">Frekuensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $dayColorIdx = ['Monday'=>0,'Tuesday'=>1,'Wednesday'=>2,'Thursday'=>3,'Friday'=>4,'Saturday'=>5,'Sunday'=>6]; @endphp
                            @foreach($lab['scheduleDetails'] as $sd)
                            <tr>
                                <td>
                                    <span class="badge badge-day-{{ $dayColorIdx[$sd->day_of_week] ?? 0 }}">
                                        {{ $sd->day_name_id }}
                                    </span>
                                </td>
                                <td style="color:#6b7280;font-weight:600;white-space:nowrap">
                                    {{ $sd->timeSlot?->name ?? '-' }}
                                    @if($sd->timeSlot)
                                    <div style="font-size:10px;color:#9ca3af">
                                        {{ \Carbon\Carbon::parse($sd->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($sd->timeSlot->end_time)->format('H:i') }}
                                    </div>
                                    @endif
                                </td>
                                <td style="font-weight:700;color:#1A2517">{{ $sd->teacher_name }}</td>
                                <td style="color:#374151">{{ $sd->labClass?->name ?? '-' }}</td>
                                <td style="color:#6b7280">{{ $sd->subject_name ?? '-' }}</td>
                                <td style="text-align:center">
                                    <span class="badge badge-freq">{{ $sd->occurrences }}×/bln</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($lab['bookingDetails']->isNotEmpty())
                <div class="detail-heading" style="color:#1d4ed8">
                    <span>📝</span> Booking Disetujui
                    <span style="font-size:11px;font-weight:400;color:#9ca3af">
                        ({{ $lab['bookingDetails']->count() }} booking)
                    </span>
                </div>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Slot Waktu</th>
                                <th>Pengajar</th>
                                <th>Kelas</th>
                                <th>Kegiatan / Mapel</th>
                                <th style="text-align:center">Peserta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lab['bookingDetails'] as $bd)
                            <tr>
                                <td style="white-space:nowrap">
                                    <div style="font-weight:700;color:#1A2517">
                                        {{ \Carbon\Carbon::parse($bd->booking_date)->translatedFormat('d M Y') }}
                                    </div>
                                    <div style="font-size:10px;color:#9ca3af">
                                        {{ \Carbon\Carbon::parse($bd->booking_date)->translatedFormat('l') }}
                                    </div>
                                </td>
                                <td style="color:#6b7280;font-weight:600;white-space:nowrap">
                                    {{ $bd->timeSlot?->name ?? '-' }}
                                    @if($bd->timeSlot)
                                    <div style="font-size:10px;color:#9ca3af">
                                        {{ \Carbon\Carbon::parse($bd->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($bd->timeSlot->end_time)->format('H:i') }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight:700;color:#1A2517">{{ $bd->teacher_name }}</div>
                                    @if($bd->teacher_phone)
                                    <div style="font-size:10px;color:#9ca3af">{{ $bd->teacher_phone }}</div>
                                    @endif
                                </td>
                                <td style="color:#374151">{{ $bd->class_name ?? '-' }}</td>
                                <td>
                                    <div style="font-weight:600;color:#1A2517">{{ $bd->title }}</div>
                                    @if($bd->subject_name)
                                    <div style="font-size:10px;color:#9ca3af">{{ $bd->subject_name }}</div>
                                    @endif
                                </td>
                                <td style="text-align:center">
                                    <span class="badge badge-book">{{ $bd->participant_count ?? '-' }} org</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($lab['scheduleDetails']->isEmpty() && $lab['bookingDetails']->isEmpty())
                <div class="empty">
                    <div style="font-size:36px;margin-bottom:10px">📭</div>
                    Tidak ada penggunaan pada bulan ini
                </div>
                @endif

            </div>{{-- /detail --}}
        </div>{{-- /lab-card --}}
    </div>{{-- /panel --}}
    @endforeach

    <p style="text-align:center;font-size:12px;color:#9ca3af;margin-top:8px">
        Data diperbarui otomatis · Lab Management Nuris Jember
    </p>

</div>{{-- /wrap --}}

{{-- JS rekap --}}
<script>
function switchTab(idx) {
    document.querySelectorAll('.panel').forEach(function(p) { p.classList.remove('on'); });
    document.querySelectorAll('.tab').forEach(function(t)   { t.classList.remove('on'); });
    document.getElementById('panel-' + idx).classList.add('on');
    document.querySelectorAll('.tab')[idx].classList.add('on');
    window.scrollTo({ top: 120, behavior: 'smooth' });
}

function getActiveLabName() {
    var t = document.querySelector('.tab.on');
    return t ? t.textContent.trim().split('\n')[0].trim() : 'Rekap';
}
function getPeriod() {
    var l = document.querySelector('.period-lbl');
    return l ? l.textContent.trim() : '';
}
function getActiveTableData() {
    var panel = document.querySelector('.panel.on');
    if (!panel) return { jadwal: [], booking: [] };
    var jadwal = [], booking = [];
    panel.querySelectorAll('table.tbl').forEach(function(tbl) {
        var h = tbl.previousElementSibling;
        var isBook = h && h.textContent.includes('Booking');
        var target = isBook ? booking : jadwal;
        var headers = Array.from(tbl.querySelectorAll('thead th')).map(function(th) { return th.textContent.trim(); });
        if (target.length === 0) target.push(headers);
        tbl.querySelectorAll('tbody tr').forEach(function(row) {
            target.push(Array.from(row.querySelectorAll('td')).map(function(td) {
                return td.textContent.trim().replace(/\s+/g, ' ');
            }));
        });
    });
    return { jadwal: jadwal, booking: booking };
}
function getSummaryRows() {
    return Array.from(document.querySelectorAll('.sum-card')).map(function(c) {
        return [
            c.querySelector('.sum-lbl')?.textContent.trim() || '',
            c.querySelector('.sum-val')?.textContent.trim() || '',
            c.querySelector('.sum-sub')?.textContent.trim() || '',
        ];
    });
}

function exportExcel() {
    if (typeof XLSX === 'undefined') {
        var s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
        s.onload = function() { doExportExcel(); };
        document.head.appendChild(s);
    } else {
        doExportExcel();
    }
}
function doExportExcel() {
    var lab = getActiveLabName(), period = getPeriod(), wb = XLSX.utils.book_new();
    var ws1 = XLSX.utils.aoa_to_sheet([
        ['REKAP PENGGUNAAN LABORATORIUM'],
        ['Lab: ' + lab, 'Periode: ' + period],
        [],
        ['Indikator', 'Nilai', 'Keterangan'],
        ...getSummaryRows()
    ]);
    ws1['!cols'] = [{ wch: 22 }, { wch: 12 }, { wch: 28 }];
    XLSX.utils.book_append_sheet(wb, ws1, 'Summary');
    var data = getActiveTableData();
    if (data.jadwal.length > 1) {
        var ws2 = XLSX.utils.aoa_to_sheet(data.jadwal);
        ws2['!cols'] = [{ wch: 12 }, { wch: 14 }, { wch: 22 }, { wch: 14 }, { wch: 18 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, ws2, 'Jadwal Tetap');
    }
    if (data.booking.length > 1) {
        var ws3 = XLSX.utils.aoa_to_sheet(data.booking);
        ws3['!cols'] = [{ wch: 14 }, { wch: 14 }, { wch: 22 }, { wch: 14 }, { wch: 20 }, { wch: 10 }];
        XLSX.utils.book_append_sheet(wb, ws3, 'Booking');
    }
    XLSX.writeFile(wb, 'Rekap_' + lab.replace(/\s+/g, '_') + '_' + period.replace(/[^a-zA-Z0-9]/g, '_') + '.xlsx');
}

function exportCSV() {
    var lab = getActiveLabName(), period = getPeriod();
    var data = getActiveTableData();
    var toCSV = function(rows) {
        return rows.map(function(r) {
            return r.map(function(c) { return '"' + c.replace(/"/g, '""') + '"'; }).join(',');
        }).join('\n');
    };
    var csv = 'REKAP PENGGUNAAN LABORATORIUM\nLab: ' + lab + '\nPeriode: ' + period + '\n\n';
    if (data.jadwal.length  > 1) csv += 'JADWAL TETAP\n'      + toCSV(data.jadwal)   + '\n\n';
    if (data.booking.length > 1) csv += 'BOOKING DISETUJUI\n' + toCSV(data.booking)  + '\n';
    var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = 'Rekap_' + lab.replace(/\s+/g, '_') + '_' + period.replace(/[^a-zA-Z0-9]/g, '_') + '.csv';
    a.click();
    URL.revokeObjectURL(url);
}

function exportPDF() {
    var lab   = getActiveLabName(), period = getPeriod();
    var panel = document.querySelector('.panel.on');
    if (!panel) return;
    var stats = Array.from(panel.querySelectorAll('.stat-cell')).map(function(c) {
        return { val: c.querySelector('.stat-val')?.textContent.trim() || '', key: c.querySelector('.stat-key')?.textContent.trim() || '' };
    });
    var data = getActiveTableData();
    var tblHTML = function(rows, title, color) {
        if (rows.length <= 1) return '';
        var headers = rows[0], body = rows.slice(1);
        return '<h3 style="color:' + color + ';font-size:13px;margin:18px 0 8px">' + title + '</h3>'
            + '<table><thead><tr>' + headers.map(function(h) { return '<th>' + h + '</th>'; }).join('') + '</tr></thead>'
            + '<tbody>' + body.map(function(r) { return '<tr>' + r.map(function(c) { return '<td>' + c + '</td>'; }).join('') + '</tr>'; }).join('') + '</tbody></table>';
    };
    var html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Rekap ' + lab + '</title>'
        + '<style>body{font-family:Arial,sans-serif;font-size:11px;color:#1A2517;padding:20px}h1{font-size:17px;margin-bottom:3px}h2{font-size:13px;color:#6b7280;font-weight:400;margin-bottom:14px}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin:12px 0 18px}.stat{background:#f8faf7;border:1px solid #e8f0e6;border-radius:8px;padding:10px;text-align:center}.stat-v{font-size:20px;font-weight:800}.stat-k{font-size:10px;color:#9ca3af;margin-top:3px}table{width:100%;border-collapse:collapse;margin-bottom:16px}th{background:#1A2517;color:#ACC8A2;padding:7px 9px;text-align:left;font-size:10px}td{padding:6px 9px;border-bottom:1px solid #e8f0e6;font-size:11px}tr:nth-child(even) td{background:#f8faf7}.footer{margin-top:16px;font-size:10px;color:#9ca3af;text-align:center}</style>'
        + '</head><body>'
        + '<h1>📊 Rekap Penggunaan Laboratorium</h1>'
        + '<h2>' + lab + ' &nbsp;·&nbsp; ' + period + '</h2>'
        + '<div class="stats">' + stats.map(function(s) { return '<div class="stat"><div class="stat-v">' + s.val + '</div><div class="stat-k">' + s.key + '</div></div>'; }).join('') + '</div>'
        + tblHTML(data.jadwal,  '📅 Jadwal Tetap',      '#3d5438')
        + tblHTML(data.booking, '📝 Booking Disetujui', '#1d4ed8')
        + '<div class="footer">Lab Management – Nuris Jember &nbsp;|&nbsp; Dicetak: ' + new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) + '</div>'
        + '</body></html>';
    var win = window.open('', '_blank');
    win.document.write(html);
    win.document.close();
    win.focus();
    setTimeout(function() { win.print(); }, 500);
}
</script>

@endsection