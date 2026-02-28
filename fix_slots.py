f = '/home/maikel/lab-management/resources/views/schedule/index.blade.php'
c = open(f).read()

old = """    const slotIdx   = ALL_SLOTS.findIndex(s => s.id == sid);
    const nextSlot  = slotIdx >= 0 && slotIdx + 1 < ALL_SLOTS.length ? ALL_SLOTS[slotIdx + 1] : null;
    const nextAvail = nextSlot && !bookedSlots.includes(nextSlot.id);
    const wrap      = document.getElementById('slot-options');
    wrap.innerHTML  = '';

    const o1 = document.createElement('button');
    o1.type = 'button'; o1.className = 'slot-opt selected';
    o1.innerHTML = '<strong>1 Slot</strong><br><span style="font-size:10px">' + sname + ' · ' + stime + '</span>';
    o1.onclick = () => selectOpt(o1, '');
    wrap.appendChild(o1);

    const o2 = document.createElement('button');
    o2.type = 'button'; o2.className = 'slot-opt';
    if (nextAvail) {
        const end   = nextSlot.end_time ? nextSlot.end_time.slice(0,5) : '';
        const start = stime.split('–')[0];
        o2.innerHTML = '<strong>2 Slot</strong><br><span style="font-size:10px">' + sname + ' + ' + nextSlot.name + ' · ' + start + '–' + end + '</span>';
        o2.dataset.slotId = nextSlot.id;
        o2.onclick = () => selectOpt(o2, nextSlot.id);
    } else {
        o2.disabled = true;
        o2.innerHTML = '<strong>2 Slot</strong><br><span style="font-size:10px">Tidak tersedia</span>';
    }
    wrap.appendChild(o2);"""

new = """    // Multi-slot selector
    const slotIdx  = ALL_SLOTS.findIndex(s => s.id == sid);
    const wrap     = document.getElementById('slot-options');
    const startTime = stime.split('\\u2013')[0].split('-')[0].trim();
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
        document.getElementById('b-slot').textContent = '\\uD83D\\uDD50 ' + sname
            + (selectedCount > 1 ? ' \\u2013 ' + lastSlot.name : '')
            + ' \\u00B7 ' + startTime + (endTime ? '\\u2013' + endTime : '');
    }

    // Buat tombol per slot
    availableSlots.forEach((slot, i) => {
        const btn  = document.createElement('button');
        btn.type   = 'button';
        btn.className = 'slot-opt' + (i === 0 ? ' selected' : '');
        const endT = slot.end_time ? slot.end_time.slice(0,5) : '';
        if (i === 0) {
            btn.innerHTML = '<strong>' + slot.name + '</strong><br><span style="font-size:10px">' + startTime + (endT ? '\\u2013' + endT : '') + '</span>';
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
        btnAll.innerHTML = '<strong>Full (' + availableSlots.length + ')</strong><br><span style="font-size:10px">' + startTime + (lastEnd ? '\\u2013' + lastEnd : '') + '</span>';
        btnAll.onclick = () => {
            selectedCount = availableSlots.length;
            updateExtraSlots();
            wrap.querySelectorAll('.slot-opt').forEach(b => b.classList.remove('selected'));
            btnAll.classList.add('selected');
        };
        wrap.appendChild(btnAll);
    }

    updateExtraSlots();"""

if old in c:
    c = c.replace(old, new)
    open(f, 'w').write(c)
    print("OK")
else:
    print("NOT FOUND - checking...")
    idx = c.find("const slotIdx   = ALL_SLOTS.findIndex")
    print("slotIdx line found at char:", idx)
    if idx > 0:
        print(repr(c[idx:idx+100]))
