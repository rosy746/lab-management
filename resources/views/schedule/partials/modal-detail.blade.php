{{-- resources/views/schedule/partials/modal-detail.blade.php --}}
<div class="detail-overlay" id="detail-overlay"
     onclick="if(event.target===this)closeDetail()"
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