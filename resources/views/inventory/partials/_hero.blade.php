<div class="hero anim-hero">
    <h1 class="hero-title">Inventaris Laboratorium</h1>
    <p class="hero-desc">Data perangkat dan perlengkapan seluruh laboratorium komputer Nuris Jember</p>
    <div class="hero-stats">
        <div class="stat-card">
            <div class="stat-val">{{ $resources->count() }}</div>
            <div class="stat-lbl">Laboratorium</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $totalItems }}</div>
            <div class="stat-lbl">Jenis Barang</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $totalUnits }}</div>
            <div class="stat-lbl">Total Unit</div>
        </div>
        <div class="stat-card">
            <div class="stat-val danger">{{ $totalBroken }}</div>
            <div class="stat-lbl">Unit Rusak</div>
        </div>
    </div>
</div>