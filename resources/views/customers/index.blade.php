<x-layouts.app active="pelanggan" crumb="Pelanggan" title="Service Laptop · Pelanggan">
    <div class="row between wrap">
        <div>
            <h1>Pelanggan</h1>
        </div>
    </div>

    <form method="GET" action="{{ route('customers.index') }}" id="searchCustForm">
        <label class="search">
            <x-icon name="search" size="16" />
            <input id="pq" name="q" value="{{ $q }}" placeholder="Cari nama atau No HP" aria-label="Cari pelanggan" onchange="document.getElementById('searchCustForm').submit()">
        </label>
    </form>

    <div class="tbl" style="margin-top:0">
        <div class="th pl">
            <span>Nama dan no. HP</span>
            <span>Total service</span>
            <span>Servis terakhir</span>
            <span class="r">Aksi</span>
            <span></span>
        </div>

        <div id="crows">
            @forelse($customers as $c)
                @php
                    $latestService = $c->services->first();
                @endphp
                <div class="tr pl" onclick="window.location.href='{{ route('services.index', ['customer_id' => $c->id, 'filter' => 'Semua']) }}'" tabindex="0" role="link" onkeydown="if(event.key==='Enter')this.click()">
                    <div class="nm">
                        <b>{{ $c->name }}</b>
                        <span>{{ $c->phone }}</span>
                    </div>
                    <div class="c-jm">
                        <span class="small" style="font-weight:500">{{ $c->services_count }} Servis</span>
                    </div>
                    <div class="muted small c-tg">{{ $latestService ? $latestService->received_at->locale('id')->isoFormat('D MMM Y') : '-' }}</div>
                    <div class="c-ak actions" style="justify-content:flex-end">
                        <button type="button" class="btn s btn-edit-cust" data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-phone="{{ $c->phone }}" onclick="openEditCust(this, event)">Ubah</button>
                    </div>
                    <div class="c-ch muted">
                        <x-icon name="chevR" size="16" />
                    </div>
                </div>
            @empty
                <div class="empty">
                    @if(filled($q))
                        Tidak ada pelanggan yang cocok.
                        <a href="{{ route('customers.index') }}" class="brandc" style="font-weight:500">Tampilkan semua pelanggan</a>
                    @else
                        Belum ada data pelanggan.
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        window.openEditCust = function(btn, e) {
            if (e) e.stopPropagation();
            const id = btn.dataset.id;
            const name = escapeHtml(btn.dataset.name);
            const phone = escapeHtml(btn.dataset.phone);

            const html = `
                <h1>Ubah data pelanggan</h1>
                <form action="/pelanggan/${id}" method="POST" class="stack" style="gap:16px">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="f">
                        <label for="nama">Nama pelanggan</label>
                        <input class="in" id="nama" name="nama" maxlength="100" value="${name}" required>
                    </div>
                    <div class="f">
                        <label for="hp">No HP</label>
                        <input class="in" id="hp" name="hp" type="tel" maxlength="20" value="${phone}" required>
                    </div>
                    <div class="ft">
                        <button type="button" class="btn s" data-a="closeov">Batal</button>
                        <button type="submit" class="btn p">Simpan perubahan</button>
                    </div>
                </form>
            `;
            openModal(html);
        };
    </script>
    @endpush
</x-layouts.app>
