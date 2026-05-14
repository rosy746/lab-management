{{-- resources/views/schedule/partials/skeleton.blade.php --}}
<div class="skeleton-wrap" id="skeleton">
    <div class="skel-head"></div>
    <div class="skel-body">
        <div class="skel-row">
            <div class="skel-cell skel-cell-sm"></div>
            @for($d = 0; $d < 7; $d++)
                <div class="skel-cell skel-cell-sm"></div>
            @endfor
        </div>
        @for($r = 0; $r < 7; $r++)
        <div class="skel-row">
            <div class="skel-cell" style="animation-delay:{{ $r * 30 }}ms"></div>
            @for($d = 0; $d < 7; $d++)
                <div class="skel-cell" style="animation-delay:{{ ($r * 7 + $d) * 20 }}ms"></div>
            @endfor
        </div>
        @endfor
    </div>
</div>