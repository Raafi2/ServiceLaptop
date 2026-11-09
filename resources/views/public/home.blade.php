<x-layouts.base title="Service Laptop · Layanan Perbaikan Laptop">
    @push('styles')
    <style>
      .lp{-webkit-font-smoothing:antialiased}
      .lp img{display:block}
      .page{width:100%;max-width:1440px;margin:0 auto;background:var(--bg)}

      /* Navbar */
      .lp-navbar{background:#fff;border-bottom:1px solid var(--border);height:56px;display:flex;align-items:center;justify-content:space-between;padding:0 var(--px);position:sticky;top:0;z-index:10}
      .lp-brand{display:flex;gap:10px;align-items:center}
      .lp-brand .lp-logo{width:32px;height:32px;border-radius:6px;overflow:hidden}
      .lp-brand .lp-logo img{width:100%;height:100%;object-fit:contain}
      .lp-brand span{font:600 15px/20px var(--font);letter-spacing:-.1px}
      .lp-menu{display:flex;gap:24px;font:500 14px/20px var(--font);color:var(--muted)}
      .lp-menu a{color:var(--muted)}.lp-menu a:hover{color:var(--text)}
      .lp-nav-actions{display:flex;gap:8px;align-items:center}

      /* Hero */
      .lp-hero{display:flex;justify-content:space-between;gap:48px;align-items:center;padding:72px var(--px) 80px}
      .lp-hero-text{flex:1;min-width:0;max-width:460px;display:flex;flex-direction:column;gap:20px}
      .lp-hero-text h1{font:600 36px/42px var(--font);letter-spacing:-.8px}
      .lp-hero-text .lp-lead{font:400 15px/24px var(--font);color:var(--muted)}
      .lp-hero-benefits{display:flex;flex-direction:column;gap:8px;padding:2px 0}
      .lp-b-item{display:flex;align-items:center;gap:8px;font:500 13px/18px var(--font);color:var(--muted)}
      .lp-b-item svg{color:var(--brand);flex-shrink:0}
      .lp-hero-text .btn.p{width:100%;height:44px;font-size:15px}
      .lp-hero-img{width:420px;height:290px;border-radius:12px;overflow:hidden;flex-shrink:0;position:relative}
      .lp-hero-img img{width:100%;height:100%;object-fit:cover}
      .lp-chip{position:absolute;left:16px;bottom:16px;display:flex;gap:8px;align-items:center;padding:8px 12px;background:var(--surface);border:1px solid var(--border);border-radius:6px;box-shadow:0 4px 12px rgba(28,26,23,.08);font:400 12px/16px var(--font)}
      .lp-chip .lp-dot{width:6px;height:6px;border-radius:50%;background:var(--ink);flex-shrink:0}
      .lp-chip b{font:600 12px/16px var(--mono)}
      .lp-chip span{color:var(--muted)}



      /* Status preview */
      .lp-status{background:var(--subtle);padding:80px var(--px)}
      .lp-status-inner{display:flex;gap:56px;align-items:flex-start}
      .lp-status-text{flex:1;min-width:0;padding-top:2px}
      .lp-status-text h2{font:600 24px/30px var(--font);letter-spacing:-.4px;margin:0 0 24px 0}
      .lp-list{display:flex;flex-direction:column;gap:16px}
      .lp-point{display:flex;gap:10px;align-items:center}
      .lp-point .lp-pt-dot{width:6px;height:6px;border-radius:50%;background:var(--ink);flex-shrink:0}
      .lp-point p{font:500 15px/20px var(--font)}

      .lp-ticket{width:380px;flex-shrink:0;background:var(--surface);border:1px solid var(--border);border-radius:10px;box-shadow:0 4px 12px rgba(10,36,71,.06);padding:20px;display:flex;flex-direction:column;gap:12px}
      .lp-t-head{display:flex;align-items:center;justify-content:space-between}
      .lp-t-id{font:500 18px/24px var(--mono)}
      .lp-t-desc{font:400 13px/18px var(--font);color:var(--muted)}
      .lp-hr{height:1px;background:var(--border)}
      .lp-dl{display:flex;flex-direction:column}
      .lp-dl-row{display:grid;grid-template-columns:120px 1fr;gap:12px;padding:8px 0;border-bottom:1px solid var(--border)}
      .lp-dl-row:last-child{border-bottom:none}
      .lp-dl-row dt{font:400 13px/18px var(--font);color:var(--muted)}
      .lp-dl-row dd{font:500 13px/18px var(--font);margin:0}
      .lp-t-label{font:500 11px/16px var(--font);letter-spacing:.1px;color:var(--muted)}
      .lp-tl{display:flex;flex-direction:column}
      .lp-tl-item{display:grid;grid-template-columns:8px 1fr;gap:10px;min-height:44px}
      .lp-tl-item.h56{min-height:56px}
      .lp-rail{display:flex;flex-direction:column;align-items:center}
      .lp-rail .lp-dotw{padding-top:4px}
      .lp-rail .lp-dotw i{display:block;width:8px;height:8px;border-radius:50%;background:var(--strong);font-style:normal}
      .lp-rail .lp-dotw i.on{background:var(--ink)}
      .lp-rail .lp-linew{flex:1;min-height:1px;padding-top:2px}
      .lp-rail .lp-linew i{display:block;width:1px;height:100%;min-height:24px;background:var(--strong);font-style:normal}
      .lp-tl-body{display:flex;flex-direction:column}
      .lp-tl-item.h56 .lp-tl-body{padding-bottom:12px}
      .lp-tl-body .lp-ttl{font:500 13px/18px var(--font)}
      .lp-tl-body .lp-date{font:400 11px/16px var(--font);color:var(--muted)}
      .lp-tl-body .lp-note{font:400 11px/16px var(--font);color:var(--muted)}
      .lp-t-foot{display:flex;justify-content:flex-end}

      /* Keunggulan */
      .lp-adv{background:var(--surface);border-top:1px solid var(--border);padding:80px var(--px)}
      .lp-adv>h2{font:600 24px/30px var(--font);letter-spacing:-.4px}
      .lp-adv-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:32px;margin-top:40px}
      .lp-adv-item{border-top:1px solid var(--border);padding-top:24px;display:flex;flex-direction:column;gap:12px}
      .lp-adv-item .lp-num{font:600 14px/20px var(--mono);color:var(--ink)}
      .lp-adv-item h3{font:600 16px/22px var(--font);margin:0}
      .lp-adv-item p{font:400 14px/22px var(--font);color:var(--muted);margin:0}

      /* Footer */
      .lp-info{background:var(--ink);padding:72px var(--px)}
      .lp-info-inner{display:flex;gap:56px}
      .lp-info-left{width:280px;flex-shrink:0}
      .lp-info-left h3{font:600 20px/26px var(--font);letter-spacing:-.3px;color:#fff}
      .lp-info-left p{font:400 14px/22px var(--font);color:rgba(255,255,255,.5);margin-top:10px}
      .lp-info-right{flex:1;display:flex;flex-direction:column}
      .lp-info-row{display:flex;gap:16px;padding:12px 0;border-bottom:1px solid rgba(255,255,255,.1)}
      .lp-info-row:last-child{border-bottom:none}
      .lp-info-row dt{width:120px;flex-shrink:0;font:400 13px/20px var(--font);color:rgba(255,255,255,.5)}
      .lp-info-row dd{font:500 13px/20px var(--font);color:#fff;margin:0}

      /* Mobile burger */
      .lp-burger{display:none;width:36px;height:36px;border:0;background:0;cursor:pointer;align-items:center;justify-content:center;border-radius:6px}
      .lp-burger:hover{background:var(--subtle)}
      .lp-burger svg{width:20px;height:20px;stroke:var(--text);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
      .lp-mobile-menu{display:none;position:fixed;inset:0;z-index:100;background:rgba(11,23,48,.45)}
      .lp-mobile-menu.open{display:block}
      .lp-mobile-panel{position:fixed;left:0;top:0;width:min(280px,75vw);height:100vh;background:#fff;display:flex;flex-direction:column;z-index:101;border-right:1px solid var(--border)}
      .lp-mobile-panel-top{height:56px;display:flex;align-items:center;justify-content:space-between;padding:0 16px;border-bottom:1px solid var(--border)}
      .lp-mobile-nav{padding:12px}
      .lp-mobile-nav a{display:flex;align-items:center;height:44px;padding:0 12px;border-radius:6px;font:500 14px/20px var(--font);color:var(--muted)}
      .lp-mobile-nav a:hover{background:var(--subtle);color:var(--text)}

      /* Responsive */
      @media(max-width:1100px){
        .lp-hero{gap:32px}
        .lp-hero-text h1{font-size:30px;line-height:36px}
        .lp-hero-img{width:340px;height:240px}

        .lp-status-inner{gap:32px}
        .lp-ticket{width:340px}
        .lp-adv-grid{grid-template-columns:repeat(2,1fr)}
      }
      @media(max-width:900px){
        .lp-menu,.lp-nav-actions{display:none}
        .lp-burger{display:flex}
        .lp-hero{flex-direction:column;padding:32px var(--px) 40px;gap:24px}
        .lp-hero-text{max-width:100%}
        .lp-hero-text h1{font-size:26px;line-height:32px}
        .lp-hero-img{width:100%;height:auto;aspect-ratio:16/10}

        .lp-status{padding:40px var(--px)}
        .lp-status-inner{flex-direction:column;gap:24px}
        .lp-status-text h2{font-size:20px;line-height:26px}
        .lp-ticket{width:100%}
        .lp-adv{padding:40px var(--px)}
        .lp-adv-grid{grid-template-columns:1fr}
        .lp-info{padding:40px var(--px)}
        .lp-info-inner{flex-direction:column;gap:24px}
        .lp-info-left{width:100%}
        .lp-point p{font-size:14px}
      }
    </style>
    @endpush

    <div class="lp">
    <div class="page">

      <header class="lp-navbar">
        <a href="{{ route('home') }}" class="lp-brand">
          <div class="lp-logo"><img alt="" src="/LogoServiceLaptop.png"></div>
          <span>Service Laptop</span>
        </a>
        <nav class="lp-menu">
          <a href="{{ route('home') }}">Beranda</a>
          <a href="{{ route('check') }}">Cek Status</a>
          <a href="#unggul">Keunggulan</a>
          <a href="#toko">Informasi Toko</a>
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
            <a href="#unggul" onclick="document.getElementById('lp-mob').classList.remove('open')">Keunggulan</a>
            <a href="#toko" onclick="document.getElementById('lp-mob').classList.remove('open')">Informasi Toko</a>
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

      <section class="lp-hero">
        <div class="lp-hero-text">
          <h1>Layanan Perbaikan Laptop</h1>
          <p class="lp-lead">Masukkan nomor service dari tiket service Anda untuk melihat status perbaikan dan riwayat pengerjaan laptop.</p>
          <div class="lp-hero-benefits">
            <div class="lp-b-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Pengerjaan langsung oleh teknisi</span>
            </div>
            <div class="lp-b-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Biaya transparan disepakati di awal</span>
            </div>
            <div class="lp-b-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Bebas biaya jika perbaikan dibatalkan</span>
            </div>
          </div>
          <a class="btn p" href="{{ route('check') }}">Cek Status</a>
        </div>
        <div class="lp-hero-img">
          <img alt="" src="/gambar.png" fetchpriority="high">
          <div class="lp-chip">
            <div class="lp-dot"></div>
            <b>SL-K9WRK2</b>
            <span>Sedang dikerjakan</span>
          </div>
        </div>
      </section>

      <section class="lp-status">
        <div class="lp-status-inner">
          <div class="lp-status-text">
            <h2>Halaman status dapat diakses oleh siapa pun<br>yang memiliki nomor service.</h2>
            <div class="lp-list">
              <div class="lp-point"><div class="lp-pt-dot"></div><p>Nomor service, nama, dan tipe laptop</p></div>
              <div class="lp-point"><div class="lp-pt-dot"></div><p>Nama pelanggan</p></div>
              <div class="lp-point"><div class="lp-pt-dot"></div><p>Tanggal masuk</p></div>
              <div class="lp-point"><div class="lp-pt-dot"></div><p>Riwayat status lengkap</p></div>
            </div>
          </div>

          <div class="lp-ticket">
            <div class="lp-t-head">
              <div class="lp-t-id">SL-K9WRK2</div>
              <x-status-badge status="serahkan" />
            </div>
            <div class="lp-t-desc">Laptop telah diambil oleh pelanggan.</div>
            <div class="lp-hr"></div>
            <dl class="lp-dl">
              <div class="lp-dl-row"><dt>Laptop</dt><dd>Toshiba Dynabook G83</dd></div>
              <div class="lp-dl-row"><dt>Pelanggan</dt><dd>H***** A***** M*****</dd></div>
              <div class="lp-dl-row"><dt>Tanggal masuk</dt><dd>8 Nov 2026</dd></div>
            </dl>
            <div class="lp-t-label">Riwayat status</div>
            <div class="lp-tl">
              <div class="lp-tl-item h56">
                <div class="lp-rail"><div class="lp-dotw"><i></i></div><div class="lp-linew"><i></i></div></div>
                <div class="lp-tl-body"><div class="lp-ttl">Dikerjakan</div><div class="lp-date">8 Nov 2026</div></div>
              </div>
              <div class="lp-tl-item h56">
                <div class="lp-rail"><div class="lp-dotw"><i></i></div><div class="lp-linew"><i></i></div></div>
                <div class="lp-tl-body"><div class="lp-ttl">Selesai</div><div class="lp-date">8 Nov 2026</div></div>
              </div>
              <div class="lp-tl-item">
                <div class="lp-rail"><div class="lp-dotw"><i class="on"></i></div></div>
                <div class="lp-tl-body"><div class="lp-ttl">Diserahkan</div><div class="lp-date">8 Nov 2026</div></div>
              </div>
            </div>
            <div class="lp-hr"></div>
            <div class="lp-t-foot"><a class="btn s" href="{{ route('check') }}">Cari nomor lain</a></div>
          </div>
        </div>
      </section>

      <section class="lp-adv" id="unggul">
        <h2>Keunggulan</h2>
        <div class="lp-adv-grid">
          <div class="lp-adv-item"><div class="lp-num">01</div><h3>Proses cepat</h3><p>Perbaikan ditangani langsung oleh teknisi dengan estimasi waktu pengerjaan yang jelas.</p></div>
          <div class="lp-adv-item"><div class="lp-num">02</div><h3>Pantau status secara mandiri</h3><p>Cek status perbaikan kapan saja dengan nomor service pada tiket Anda, tanpa perlu login.</p></div>
          <div class="lp-adv-item"><div class="lp-num">03</div><h3>Harga terjangkau</h3><p>Biaya perbaikan disepakati di awal.</p></div>
          <div class="lp-adv-item"><div class="lp-num">04</div><h3>Tanpa biaya jika dibatalkan</h3><p>Apabila perbaikan dibatalkan, Anda tidak dikenakan biaya apa pun.</p></div>
          <div class="lp-adv-item"><div class="lp-num">05</div><h3>Riwayat pengerjaan transparan</h3><p>Setiap tahap pengerjaan tercatat lengkap beserta tanggalnya.</p></div>
          <div class="lp-adv-item"><div class="lp-num">06</div><h3>Data dan privasi terjaga</h3><p>Kondisi awal laptop didokumentasikan, identitas pelanggan disamarkan pada halaman status.</p></div>
        </div>
      </section>

      <footer class="lp-info" id="toko">
        <div class="lp-info-inner">
          <div class="lp-info-left">
            <h3>Informasi toko</h3>
            <p>Kami siap membantu mengatasi masalah laptop Anda.</p>
          </div>
          <dl class="lp-info-right">
            <div class="lp-info-row"><dt>Alamat</dt><dd>{{ config('toko.alamat') }}</dd></div>
            <div class="lp-info-row"><dt>Jam buka</dt><dd>{{ config('toko.jam_buka') }}</dd></div>
            <div class="lp-info-row"><dt>Kontak</dt><dd>{{ config('toko.whatsapp') }}</dd></div>
          </dl>
        </div>
      </footer>

    </div>
    </div>
</x-layouts.base>
