<x-layouts.app active="service" crumb="Daftar service" title="Service Laptop · Service">
    <div class="row between wrap">
        <div>
            <h1>Daftar service</h1>
        </div>
        @if(!auth()->user()->isTechnician())
            <div class="row" style="gap:8px">
                <a class="btn s hide-m" href="{{ route('services.backup') }}" title="Unduh rekap data servis (Excel / CSV)">Rekap data</a>
                <a class="btn p hide-m" href="{{ route('services.create') }}">Penerimaan service</a>
            </div>
        @endif
    </div>

    <form method="GET" action="{{ route('services.index') }}" class="row between wrap" id="filterForm">
        <label class="search">
            <x-icon name="search" size="16" />
            <input id="q" name="q" value="{{ $q }}" placeholder="Cari nama, No HP, laptop, teknisi, atau track number" aria-label="Cari service" onchange="document.getElementById('filterForm').submit()">
        </label>
        <div class="tabs" id="tabs">
            @foreach(['Aktif', 'Semua', 'Dikerjakan', 'Selesai', 'Diserahkan', 'Dibatalkan'] as $f)
                <a href="{{ route('services.index', array_filter(['filter' => $f, 'q' => $q])) }}" class="tab {{ $filter === $f ? 'on' : '' }}">
                    {{ $f }} <span class="muted">{{ $counts[$f] }}</span>
                </a>
            @endforeach
        </div>
    </form>

    <div class="tbl">
        <div class="th">
            <span></span>
            <span>Nomor service</span>
            <span>Pelanggan, laptop, teknisi</span>
            <span>Status</span>
            <span>Biaya</span>
            <span>Masuk</span>
            <span></span>
        </div>

        <div id="rows">
            @forelse($services as $s)
                <div class="tr" onclick="window.location.href='{{ route('services.show', $s->tracking_no) }}'" tabindex="0" role="link" onkeydown="if(event.key==='Enter')this.click()">
                    <div class="thumb c-th">
                        @if($s->thumb_path)
                            <img src="{{ route('services.photo', str_replace('services/', '', $s->thumb_path)) }}" alt="Foto laptop" width="36" height="36" style="width:100%;height:100%;object-fit:cover" loading="lazy">
                        @else
                            {{ strtoupper(substr($s->laptop, 0, 2)) }}
                        @endif
                    </div>
                    <div class="mono brandc c-no" style="font-size:13px">{{ $s->tracking_no }}</div>
                    <div class="nm">
                        <b>{{ $s->customer->name }}</b>
                        <span>{{ $s->laptop }} · {{ $s->technician->name }}</span>
                    </div>
                    <div class="c-st">
                        <x-status-badge :status="$s->status" />
                    </div>
                    <div class="c-bi">Rp {{ number_format($s->agreed_cost, 0, ',', '.') }}</div>
                    <div class="muted small c-tg">{{ $s->received_at->locale('id')->isoFormat('D MMM Y') }}</div>
                    <div class="c-ch muted">
                        <x-icon name="chevR" size="16" />
                    </div>
                </div>
            @empty
                <div class="empty">
                    @if(filled($q))
                        Tidak ada service yang cocok.
                        <a href="{{ route('services.index') }}" class="brandc" style="font-weight:500">Tampilkan semua service</a>
                    @else
                        Tidak ada service.
                    @endif
                </div>
            @endforelse

            @if($services->lastPage() > 1)
                <div class="pagi-bar">
                    <div class="pagi-info">
                        Menampilkan <strong>{{ $services->firstItem() }}</strong> &ndash; <strong>{{ $services->lastItem() }}</strong>
                    </div>
                    <nav class="pagi-nav" aria-label="Navigasi Halaman">
                        @if($services->onFirstPage())
                            <span class="pagi-btn disabled" aria-disabled="true" title="Halaman sebelumnya">
                                <x-icon name="chevL" size="14" />
                            </span>
                        @else
                            <a href="{{ $services->previousPageUrl() }}" class="pagi-btn" rel="prev" title="Halaman sebelumnya" aria-label="Halaman sebelumnya">
                                <x-icon name="chevL" size="14" />
                            </a>
                        @endif

                        <div class="pagi-pages">
                        @php
                            $current = $services->currentPage();
                            $last = $services->lastPage();
                            $window = 1;
                            
                            $pages = [];
                            $pages[] = 1;
                            
                            $start = max(2, $current - $window);
                            $end = min($last - 1, $current + $window);
                            
                            if ($start > 2) {
                                $pages[] = '...';
                            }
                            
                            for ($i = $start; $i <= $end; $i++) {
                                $pages[] = $i;
                            }
                            
                            if ($end < $last - 1) {
                                $pages[] = '...';
                            }
                            
                            if ($last > 1) {
                                $pages[] = $last;
                            }
                        @endphp
                        
                        @foreach($pages as $page)
                            @if($page === '...')
                                <span class="pagi-dots">&hellip;</span>
                            @elseif($page == $current)
                                <span class="pagi-btn active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $services->url($page) }}" class="pagi-btn" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                        </div>

                        @if($services->hasMorePages())
                            <a href="{{ $services->nextPageUrl() }}" class="pagi-btn" rel="next" title="Halaman selanjutnya" aria-label="Halaman selanjutnya">
                                <x-icon name="chevR" size="14" />
                            </a>
                        @else
                            <span class="pagi-btn disabled" aria-disabled="true" title="Halaman selanjutnya">
                                <x-icon name="chevR" size="14" />
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>

    @if(!auth()->user()->isTechnician())
        <div class="sticky show-m" style="gap:8px">
            <a class="btn s" href="{{ route('services.backup') }}" style="flex:1">Rekap data</a>
            <a class="btn p" href="{{ route('services.create') }}" style="flex:1">Penerimaan service</a>
        </div>
    @endif

    @push('scripts')
    <script>
        document.querySelector('#tabs .tab.on')?.scrollIntoView({inline:'center',block:'nearest'});
    </script>
    @endpush
</x-layouts.app>
