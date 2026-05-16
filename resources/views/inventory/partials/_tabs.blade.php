<div class="tab-bar">
    @foreach($resources as $i => $lab)
        @php $cnt = $inventories->where('resource_id', $lab->id)->count(); @endphp
        <button class="tab-btn {{ $loop->first ? 'active' : '' }}"
                onclick="switchLab({{ $lab->id }}, this)">
            {{ $lab->name }}
            @if($cnt > 0)
                <span class="tab-count">{{ $cnt }}</span>
            @endif
        </button>
    @endforeach
</div>