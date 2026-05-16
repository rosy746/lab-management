{{-- resources/views/schedule/partials/panels.blade.php --}}
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
                                         role="button" tabindex="0"
                                         data-detail='{{ $detailSunApproved }}'
                                         onclick="showDetail(JSON.parse(this.dataset.detail))"
                                         onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                        <div class="sc-name">{{ $sunBook->teacher_name }}</div>
                                        <div class="sc-class">{{ $sunBook->class_name }}</div>
                                        @if($sunBook->subject_name)<div class="sc-subject">{{ $sunBook->subject_name }}</div>@endif
                                        <div class="sc-status">✓ Disetujui · Seharian</div>
                                    </div>

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
                                         role="button" tabindex="0"
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
                                    <button class="bk-btn bk-btn-sun"
                                        style="height:100%;min-height:400px;width:100%;box-sizing:border-box;"
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
                                     role="button" tabindex="0"
                                     data-detail='{{ $detailTetap }}'
                                     onclick="showDetail(JSON.parse(this.dataset.detail))"
                                     onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                    <div class="sc-name">{{ $sched->teacher_name }}</div>
                                    <div class="sc-class">{{ $sched->labClass?->name ?? '-' }}</div>
                                    @if($sched->subject_name)<div class="sc-subject">{{ $sched->subject_name }}</div>@endif
                                </div>

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
                                     role="button" tabindex="0"
                                     data-detail='{{ $detailApproved }}'
                                     onclick="showDetail(JSON.parse(this.dataset.detail))"
                                     onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                    <div class="sc-name">{{ $book->teacher_name }}</div>
                                    <div class="sc-class">{{ $book->class_name }}</div>
                                    <div class="sc-status">✓ Disetujui</div>
                                </div>

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
                                     role="button" tabindex="0"
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