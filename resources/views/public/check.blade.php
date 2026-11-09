<x-layouts.base title="Service Laptop · Cek Status">
    <header class="lp-navbar">
        <a href="{{ route('home') }}" class="lp-brand">
            <div class="lp-logo"><img alt="" src="/LogoServiceLaptop.png"></div>
            <span>Service Laptop</span>
        </a>
        <nav class="lp-menu">
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('check') }}">Cek Status</a>
            <a href="{{ route('home') }}#unggul">Keunggulan</a>
            <a href="{{ route('home') }}#toko">Informasi Toko</a>
        </nav>
        <div class="lp-nav-actions">
            @auth
                <a class="btn s" href="{{ route('services.index') }}">Dashboard</a>
            @else
                <a class="btn s" href="{{ route('login') }}">Masuk</a>
            @endauth
        </div>
        <button class="lp-burger" onclick="document.getElementById('lp-mob').classList.add('open')" aria-label="Buka menu">
            <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
    </header>

    <div class="lp-mobile-menu" id="lp-mob" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="lp-mobile-panel">
            <div class="lp-mobile-panel-top">
                <a href="{{ route('home') }}" class="lp-brand">
                    <div class="lp-logo"><img alt="" src="/LogoServiceLaptop.png"></div>
                    <span>Service Laptop</span>
                </a>
                <button class="lp-burger" style="display:flex" onclick="document.getElementById('lp-mob').classList.remove('open')" aria-label="Tutup menu">
                    <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="lp-mobile-nav">
                <a href="{{ route('home') }}" onclick="document.getElementById('lp-mob').classList.remove('open')">Beranda</a>
                <a href="{{ route('check') }}" onclick="document.getElementById('lp-mob').classList.remove('open')">Cek Status</a>
                <a href="{{ route('home') }}#unggul" onclick="document.getElementById('lp-mob').classList.remove('open')">Keunggulan</a>
                <a href="{{ route('home') }}#toko" onclick="document.getElementById('lp-mob').classList.remove('open')">Informasi Toko</a>
            </div>
            <div style="flex:1"></div>
            <div class="lp-mobile-nav" style="border-top:1px solid var(--border)">
                @auth
                    <a href="{{ route('services.index') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Masuk</a>
                @endauth
            </div>
        </div>
    </div>

    <div class="pub">
        <div style="margin-bottom:24px">
            <div class="disp">Cek Status Perbaikan</div>
            <p class="muted" style="margin-top:8px">Ketik nomor service dari ticket service untuk melihat status laptop Anda.</p>
        </div>

        <form action="{{ route('check') }}" method="GET" class="check-form">
            <label for="c" class="lbl">Nomor Service</label>
                <div class="check-row">
                    <input class="in mono" id="c" name="c" placeholder="SL-XXXXXX" value="{{ $trackingNo }}" maxlength="9" autocomplete="off" required>
                    <button class="btn p" type="submit">Cek Status</button>
                </div>
            @if($rateLimitError)
                <div class="hint err">{{ $rateLimitError }}</div>
            @elseif($notFound)
                <div class="hint err">Nomor service tidak ditemukan.</div>
            @endif
        </form>

        @if($service)
            @if($service->status->value === 'dibatalkan')
                <div class="card stack" style="gap:14px">
                    <div class="row between wrap" style="gap:12px">
                        <span class="mono" style="font-size:24px;line-height:30px;font-weight:500">{{ $service->tracking_no }}</span>
                        <x-status-badge :status="$service->status" />
                    </div>
                    <div class="muted">
                        Servis ini dibatalkan. Silakan hubungi toko untuk informasi lebih lanjut.
                    </div>

                    <div style="height:1px;background:var(--border)"></div>

                    <div class="row" style="justify-content:flex-end">
                        <a href="{{ route('check') }}" class="btn s">Cari nomor lain</a>
                    </div>
                </div>
            @else
                @php
                    $message = match($service->status->value) {
                        'dikerjakan' => 'Laptop sedang dalam pengerjaan teknisi.',
                        'selesai' => 'Laptop sudah selesai dan siap diambil di toko.',
                        'serahkan' => 'Laptop sudah diambil pelanggan.',
                        default => '',
                    };
                @endphp
                <div class="card stack" style="gap:14px">
                    <div class="row between wrap" style="gap:12px">
                        <span class="mono" style="font-size:24px;line-height:30px;font-weight:500">{{ $service->tracking_no }}</span>
                        <x-status-badge :status="$service->status" />
                    </div>
                    <div class="muted">{{ $message }}</div>

                    <div style="height:1px;background:var(--border)"></div>

                    <dl style="margin:0">
                        <div class="kv">
                            <dt>Laptop</dt>
                            <dd>{{ $service->laptop }}</dd>
                        </div>
                        <div class="kv">
                            <dt>Pelanggan</dt>
                            <dd>{{ $maskedName }}</dd>
                        </div>
                        <div class="kv">
                            <dt>Tanggal masuk</dt>
                            <dd>{{ $service->received_at->locale('id')->isoFormat('D MMM Y') }}</dd>
                        </div>
                    </dl>

                    <div class="lbl muted">Riwayat status</div>
                    @php
                        $timelineCollect = collect($timeline)->map(fn($item) => (object)[
                            'event' => $item->event,
                            'created_at' => $item->created_at,
                            'note' => $item->note,
                        ]);
                    @endphp
                    <x-status-timeline :logs="$timelineCollect" :public="true" />

                    <div style="height:1px;background:var(--border)"></div>

                    <div class="row" style="justify-content:flex-end">
                        <a href="{{ route('check') }}" class="btn s">Cari nomor lain</a>
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layouts.base>
