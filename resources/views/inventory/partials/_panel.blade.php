<div class="lab-panel {{ $isFirst ? 'active' : '' }}" id="lab-{{ $lab->id }}">
    <div class="panel-wrap">

        <div class="panel-header">
            <div>
                <div class="panel-title">{{ $lab->name }}</div>
                <div class="panel-info">
                    {{ $lab->building ?? '-' }}
                    @if($lab->capacity) · Kapasitas {{ $lab->capacity }} unit @endif
                    · {{ $labItems->count() }} jenis
                    · {{ $labItems->sum('quantity') }} total unit
                </div>
            </div>
            @if($brokenCount > 0)
                <div class="broken-badge">
                    <div class="broken-val">{{ $brokenCount }}</div>
                    <div class="broken-lbl">unit rusak</div>
                </div>
            @endif
        </div>

        <div class="tbl-wrap" id="table-{{ $lab->id }}">
            <table class="inv-table" id="tbl-{{ $lab->id }}">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Merk / Model</th>
                        <th>Spesifikasi</th>
                        <th class="center">Total</th>
                        <th class="center">Baik</th>
                        <th class="center">Rusak</th>
                        <th class="center">Cadangan</th>
                        <th>Kondisi</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody id="tbody-{{ $lab->id }}">
                    @forelse($labItems as $idx => $item)
                        @php $goodPerc = $item->quantity > 0 ? ($item->quantity_good / $item->quantity) * 100 : 0; @endphp
                        <tr data-name="{{ strtolower($item->item_name) }}"
                            data-brand="{{ strtolower($item->brand ?? '') }}"
                            data-specs="{{ strtolower($item->specifications ?? '') }}"
                            data-category="{{ $item->category }}"
                            data-condition="{{ $item->condition }}">
                            <td class="no-col">{{ $idx + 1 }}</td>
                            <td class="item-name">
                                <div style="display:flex;align-items:center">
                                    <span style="font-size:16px;margin-right:8px">{{ $catIcons[$item->category] ?? '' }}</span>
                                    <div>
                                        <div class="search-target">{{ ucwords($item->item_name) }}</div>
                                        @if($item->quantity_broken > 0)
                                            <div class="progress-wrap">
                                                <div class="progress-bar" style="width: {{ $goodPerc }}%"></div>
                                            </div>
                                            <div class="progress-text">{{ round($goodPerc) }}% Baik</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge cat-{{ $item->category }}">{{ $catLabels[$item->category] ?? $item->category }}</span></td>
                            <td class="item-brand">
                                <div class="search-target">{{ $item->brand ? ucwords($item->brand) : '' }}</div>
                                <div class="search-target" style="font-size:10px;color:var(--muted)">{{ $item->model ?? '' }}</div>
                                @if(!$item->brand && !$item->model)<span style="color:#e5e7eb">—</span>@endif
                            </td>
                            <td class="item-specs"><div class="search-target">{{ $item->specifications ?: '—' }}</div></td>
                            <td class="qty-center qty-total">{{ $item->quantity }}</td>
                            <td class="qty-good">{{ $item->quantity_good }}</td>
                            <td class="{{ $item->quantity_broken > 0 ? 'qty-broken' : 'qty-zero' }}">{{ $item->quantity_broken }}</td>
                            <td class="{{ $item->quantity_backup > 0 ? 'qty-backup' : 'qty-zero' }}">{{ $item->quantity_backup }}</td>
                            <td><span class="badge cond-{{ $item->condition }}">{{ $condLabels[$item->condition] ?? $item->condition }}</span></td>
                            <td class="item-specs">{{ $item->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="11">
                                @include('inventory.partials._empty_state', [
                                    'type'  => 'no-data',
                                    'title' => 'Belum ada data inventaris',
                                    'desc'  => 'Data perangkat untuk lab ini belum diinput. Hubungi admin untuk menambahkan inventaris.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="cards-grid" id="cards-{{ $lab->id }}">
            @foreach($labItems as $item)
                <div class="inv-card"
                     data-name="{{ strtolower($item->item_name) }}"
                     data-brand="{{ strtolower($item->brand ?? '') }}"
                     data-specs="{{ strtolower($item->specifications ?? '') }}"
                     data-category="{{ $item->category }}"
                     data-condition="{{ $item->condition }}">
                    <div class="card-top">
                        <span class="badge cat-{{ $item->category }}">{{ $catIcons[$item->category] ?? '' }} {{ $catLabels[$item->category] ?? $item->category }}</span>
                        <span class="badge cond-{{ $item->condition }}">{{ $condLabels[$item->condition] ?? $item->condition }}</span>
                    </div>
                    <div class="card-name">{{ ucwords($item->item_name) }}</div>
                    @if($item->brand || $item->model)
                        <div class="card-brand">{{ $item->brand }}{{ $item->model ? ' / '.$item->model : '' }}</div>
                    @endif
                    @if($item->specifications)
                        <div class="card-specs">{{ $item->specifications }}</div>
                    @endif
                    <div class="card-qtys">
                        <span class="badge badge-qty">Total: {{ $item->quantity }}</span>
                        <span class="badge badge-good">Baik: {{ $item->quantity_good }}</span>
                        @if($item->quantity_broken > 0)
                            <span class="badge badge-broken">Rusak: {{ $item->quantity_broken }}</span>
                        @endif
                        @if($item->quantity_backup > 0)
                            <span class="badge" style="background:#e0f2fe;color:#0369a1">Cad: {{ $item->quantity_backup }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>