<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1440">
    <meta name="description" content="Servis laptop tercatat rapi, statusnya bisa dicek kapan saja.">
    <title>ServiceLaptop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cousine&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #fafaf9;
            --subtle: #f5f5f4;
            --surface: #fff;
            --text: #1c1917;
            --muted: #78716c;
            --faint: #bebab3;
            --border: #e7e5e4;
            --strong: #d6d3d1;
            --navy: #0a2447;
            --navy-muted: #a9b8cf;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { background: var(--bg); }
        body { font-family: 'Inter', sans-serif; color: var(--text); -webkit-font-smoothing: antialiased; }
        img { display: block; max-width: none; }
        .page { width: 1440px; margin: 0 auto; background: var(--bg); display: flex; flex-direction: column; align-items: flex-start; }
        .page > * { flex-shrink: 0; width: 100%; }

        /* Navbar */
        .navbar { background: #fff; border-bottom: 0.667px solid var(--border); display: flex; gap: 282px; align-items: center; padding: 16px 29px; overflow: hidden; }
        .brand { display: flex; gap: 10px; align-items: center; }
        .brand .logo { width: 44px; height: 44px; border-radius: 8px; overflow: hidden; }
        .brand .logo img { width: 100%; height: 100%; object-fit: contain; border-radius: 8px; }
        .brand p { font-weight: 600; font-size: 18px; letter-spacing: -0.18px; white-space: nowrap; }
        .menu { display: flex; gap: 32px; align-items: flex-start; font-weight: 500; font-size: 15px; white-space: nowrap; color: var(--muted); }
        .menu a { color: inherit; text-decoration: none; }
        .menu a.active { color: var(--navy); font-weight: 600; }
        .nav-actions { display: flex; gap: 28px; align-items: center; }
        .btn-sm { height: 36px; display: flex; align-items: center; justify-content: center; padding: 0 14px; border-radius: 6px; border: 0.667px solid transparent; background: #fff; font-weight: 500; font-size: 14px; line-height: 20px; color: var(--text); white-space: nowrap; text-decoration: none; }
        .btn-sm.primary { background: var(--navy); color: #fff; }
        .btn-sm.outline { border-color: var(--strong); }

        /* Hero */
        .hero { position: relative; display: flex; gap: 64px; align-items: center; padding: 80px 80px 96px; overflow: hidden; }
        .hero-text { width: 640px; display: flex; flex-direction: column; gap: 27px; align-items: flex-start; overflow: hidden; }
        .hero-text h1 { font-weight: 600; font-size: 56px; line-height: 62px; letter-spacing: -1.12px; min-width: 100%; }
        .hero-text .lead { font-weight: 400; font-size: 18px; line-height: 28px; color: var(--muted); width: 507px; }
        .cta { width: 511px; background: var(--navy); display: flex; align-items: center; justify-content: center; padding: 18px 32px; border-radius: 12px; font-weight: 600; font-size: 17px; color: #fff; text-decoration: none; overflow: hidden; }
        .hero-img { width: 522px; height: 391px; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .hero-img img { width: 722px; height: 1284px; object-fit: cover; flex-shrink: 0; }
        .chip { position: absolute; left: 793px; top: 469px; display: flex; gap: 10px; align-items: center; padding: 10px 14px; background: var(--surface); border: 0.667px solid var(--border); border-radius: 6px; box-shadow: 0 10px 28px rgba(28,26,23,0.06); overflow: hidden; white-space: nowrap; font-size: 13px; }
        .chip .dot { width: 8px; height: 8px; background: var(--navy); border-radius: 50%; }
        .chip strong { font-weight: 600; }
        .chip span { font-weight: 400; color: var(--muted); }

        /* Cek status */
        .cek { background: #fff; padding: 120px 80px; overflow: hidden; }
        .cek-inner { display: flex; gap: 96px; align-items: center; width: 100%; overflow: hidden; }
        .cek-inner > img { width: 480px; height: 360px; object-fit: cover; flex-shrink: 0; }
        .form { width: 704px; flex-shrink: 0; background: var(--surface); border: 0.667px solid var(--border); border-radius: 20px; padding: 48px; display: flex; flex-direction: column; gap: 24px; align-items: flex-start; }
        .form h2 { font-weight: 600; font-size: 34px; line-height: 40px; letter-spacing: -0.1px; }
        .form > p { font-weight: 400; font-size: 17px; line-height: 20px; color: var(--muted); width: 482px; }
        .field { display: flex; flex-direction: column; gap: 10px; height: 80px; width: 100%; }
        .field label { flex: 1 0 0; min-height: 1px; font-weight: 500; font-size: 15px; line-height: 16px; white-space: nowrap; }
        .field input { height: 60px; width: 100%; flex-shrink: 0; background: var(--surface); border: 0.667px solid var(--strong); border-radius: 12px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 17px; text-transform: uppercase; color: var(--text); outline: none; }
        .field input::placeholder { color: var(--faint); opacity: 1; }
        .btn-lg { height: 60px; width: 100%; flex-shrink: 0; background: var(--navy); border: 0.667px solid transparent; border-radius: 12px; padding: 0 14px; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; font-weight: 500; font-size: 17px; line-height: 20px; color: #fff; cursor: pointer; }

        /* Status preview */
        .status { background: var(--subtle); padding: 120px 80px; overflow: hidden; }
        .status-inner { width: 1280px; display: flex; gap: 96px; align-items: center; overflow: hidden; }
        .status-text { flex: 1 0 0; min-width: 1px; display: flex; flex-direction: column; gap: 24px; align-items: flex-start; overflow: hidden; }
        .status-text h2 { font-weight: 600; font-size: 40px; line-height: 46px; letter-spacing: -0.8px; min-width: 100%; }
        .list { display: flex; flex-direction: column; gap: 20px; }
        .point { display: flex; gap: 16px; align-items: center; overflow: hidden; }
        .point .icon { width: 10px; height: 10px; background: var(--navy); border-radius: 50%; flex-shrink: 0; }
        .point p { font-weight: 500; font-size: 24px; line-height: normal; }

        .ticket { width: 480px; background: var(--surface); border: 0.667px solid var(--border); border-radius: 16px; padding: 20px; filter: drop-shadow(0 8px 12px rgba(10,36,71,0.08)); display: flex; flex-direction: column; gap: 14px; position: relative; flex-shrink: 0; }
        .t-head { display: flex; align-items: center; justify-content: space-between; }
        .t-id { font-family: 'Cousine', monospace; font-size: 24px; line-height: 30px; white-space: nowrap; }
        .badge { height: 22px; background: var(--subtle); border: 0.667px solid var(--border); border-radius: 5px; padding: 0 8px; display: flex; gap: 5px; align-items: center; justify-content: center; font-weight: 500; font-size: 12px; line-height: 16px; white-space: nowrap; }
        .t-desc { font-weight: 400; font-size: 14px; line-height: 20px; color: var(--muted); }
        .hr { height: 1px; background: var(--border); width: 100%; }
        .dl { display: flex; flex-direction: column; }
        .dl-row { display: flex; gap: 16px; padding: 10px 0; border-bottom: 0.667px solid var(--border); width: 100%; }
        .dl-row:last-child { border-bottom: none; }
        .dl-row dt { font-weight: 400; font-size: 14px; line-height: 20px; color: var(--muted); width: 80px; flex-shrink: 0; }
        .dl-row dd { font-weight: 500; font-size: 14px; line-height: 20px; flex: 1; }
        .t-label { font-weight: 500; font-size: 12px; line-height: 16px; letter-spacing: 0.1px; color: var(--muted); }
        .tl { display: flex; flex-direction: column; gap: 0px; }
        .tl-item { display: flex; gap: 12px; align-items: flex-start; }
        .rail { display: flex; flex-direction: column; align-items: center; height: 100%; }
        .dotw { width: 10px; height: 10px; border-radius: 5px; background: var(--strong); flex-shrink: 0; }
        .dotw.active { background: var(--navy); }
        .linew { width: 1px; background: var(--strong); flex: 1; min-height: 40px; }
        .tl-body { display: flex; flex-direction: column; gap: 2px; padding-top: 2px; }
        .tl-body .ttl { font-weight: 500; font-size: 14px; line-height: 20px; }
        .tl-body .date { font-weight: 400; font-size: 12px; line-height: 16px; color: var(--muted); }
        .tl-body .note { font-weight: 400; font-size: 12px; line-height: 16px; color: var(--muted); }
        .t-foot { display: flex; align-items: center; justify-content: flex-end; }

        /* Keunggulan */
        .adv { background: var(--surface); border-top: 0.667px solid var(--border); padding: 104px 80px; }
        .adv h2 { font-weight: 600; font-size: 36px; line-height: 26px; letter-spacing: -0.1px; color: #1c1917; white-space: nowrap; margin-bottom: 56px; }
        .adv-rows { display: flex; flex-direction: column; gap: 56px; width: 100%; }
        .adv-row { display: flex; gap: 40px; align-items: flex-start; width: 100%; overflow: hidden; }
        .adv-col { flex: 1 0 0; min-width: 1px; border-top: 0.667px solid var(--border); padding-top: 24px; display: flex; flex-direction: column; gap: 14px; align-items: flex-start; }
        .adv-col .num { font-family: 'Cousine', monospace; font-size: 16px; line-height: 20px; color: var(--navy); width: 100%; }
        .adv-col .ttl { font-weight: 600; font-size: 24px; line-height: 1.3; width: 100%; }
        .adv-col .txt { font-weight: 400; font-size: 17px; line-height: 1.5; color: var(--muted); width: 100%; }

        /* Footer */
        .info { background: var(--navy); padding: 88px 80px; }
        .info-inner { display: flex; gap: 64px; align-items: flex-start; width: 100%; }
        .info-left { width: 320px; display: flex; flex-direction: column; gap: 12px; align-self: stretch; }
        .info-left h3 { font-weight: 600; font-size: 28px; line-height: 34px; letter-spacing: -0.6px; color: #fff; }
        .info-left p { padding-top: 6px; font-weight: 400; font-size: 14px; line-height: 20px; color: var(--navy-muted); white-space: pre-wrap; }
        .info-right { flex: 1 0 0; min-width: 1px; display: flex; flex-direction: column; align-self: stretch; }
        .info-row { display: flex; gap: 16px; align-items: flex-start; padding: 14px 0; border-bottom: 0.667px solid rgba(255,255,255,0.14); width: 100%; }
        .info-row:last-child { border-bottom: none; }
        .info-row dt { width: 150px; flex-shrink: 0; font-weight: 400; font-size: 14px; line-height: 20px; color: var(--navy-muted); white-space: nowrap; }
        .info-row dd { flex: 1 0 0; min-width: 1px; font-weight: 500; font-size: 14px; line-height: 20px; color: #fff; }
    </style>
</head>
<body>
<div class="page">
    <header class="navbar">
        <div class="brand">
            <div class="logo">
                <img src="{{ asset('LogoServiceLaptop.png') }}" alt="Service Laptop">
            </div>
            <p>Service Laptop</p>
        </div>
        <nav class="menu">
            <p>Beranda</p>
            <p>Cek Status Perbaikan</p>
            <p>Keunggulan</p>
        </nav>
        <div class="nav-actions">
            <a href="#cek" class="btn-sm" style="width:94.333px">Cek Status</a>
            @if(auth()->check())
                <a href="{{ route('services.index') }}" class="btn-sm outline" style="width:90px">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-sm outline" style="width:90px">Masuk Staf</a>
            @endif
        </div>
    </header>

    <section class="hero" id="beranda">
        <div class="hero-text">
            <h1>Layanan Perbaikan Laptop</h1>
            <p class="lead">Masukkan nomor service dari tiket service Anda untuk melihat status perbaikan dan riwayat pengerjaan laptop.</p>
            <a href="#cek" class="cta">Cek Status</a>
        </div>
        <div class="hero-img">
            <img src="{{ asset('gambar.png') }}" alt="Ilustrasi laptop dengan status service" fetchpriority="high">
        </div>
        <div class="chip">
            <div class="dot"></div>
            <strong>SL-K9WRK2</strong>
            <span>Sedang dikerjakan</span>
        </div>
    </section>

    <section class="cek" id="cek">
        <div class="cek-inner">
            <img src="{{ asset('crash.png') }}" alt="Tampilan halaman status service">
            <div class="form">
                <h2>Cek Status Perbaikan</h2>
                <p class="sub">Masukkan nomor service yang tertera pada tiket service Anda.</p>
                <div class="field">
                    <label for="no">Nomor Service</label>
                    <input id="no" type="text" placeholder="SL-XXXXXX">
                </div>
                <button class="btn-lg" type="button">Cek Status</button>
            </div>
        </div>
    </section>

    <section class="status">
        <div class="status-inner">
            <div class="status-text">
                <h2>Halaman ini dapat diakses oleh siapa pun yang memiliki nomor service.</h2>
                <div class="list">
                    <div class="point"><div class="icon"></div><p>Nomor service, nama, dan tipe laptop</p></div>
                    <div class="point"><div class="icon"></div><p>Nama pelanggan</p></div>
                    <div class="point"><div class="icon"></div><p>Tanggal masuk</p></div>
                    <div class="point"><div class="icon"></div><p>Riwayat status</p></div>
                </div>
            </div>

            <div class="ticket">
                <div class="t-head">
                    <div class="t-id">SL-K9WRK2</div>
                    <div class="badge">Diserahkan</div>
                </div>
                <div class="t-desc">Laptop telah diambil oleh pelanggan.</div>
                <div class="hr"></div>
                <dl class="dl">
                    <div class="dl-row"><dt>Laptop</dt><dd>Toshiba Dynabook G83</dd></div>
                    <div class="dl-row"><dt>Pelanggan</dt><dd>H***** A***** M*****</dd></div>
                    <div class="dl-row"><dt>Tanggal masuk</dt><dd>8 Nov 2026</dd></div>
                </dl>
                <div class="t-label">Riwayat status</div>
                <div class="tl">
                    <div class="tl-item">
                        <div class="rail"><div class="dotw"></div><div class="linew"></div></div>
                        <div class="tl-body"><div class="ttl">Dikerjakan</div><div class="date">8 Nov 2026</div><div class="note">Laptop diterima dan mulai dikerjakan</div></div>
                    </div>
                    <div class="tl-item">
                        <div class="rail"><div class="dotw"></div><div class="linew"></div></div>
                        <div class="tl-body"><div class="ttl">Selesai</div><div class="date">8 Nov 2026</div><div class="note">Pengerjaan laptop telah selesai</div></div>
                    </div>
                    <div class="tl-item">
                        <div class="rail"><div class="dotw active"></div></div>
                        <div class="tl-body"><div class="ttl">Diserahkan</div><div class="date">8 Nov 2026</div><div class="note">Laptop telah diserahkan kepada pelanggan</div></div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="t-foot"><a class="btn-sm outline" href="#cek">Cari nomor lain</a></div>
            </div>
        </div>
    </section>

    <section class="adv" id="unggul">
        <h2>Keunggulan</h2>
        <div class="adv-rows">
            <div class="adv-row">
                <div class="adv-col"><p class="num">01</p><p class="ttl">Proses cepat</p><p class="txt">Perbaikan ditangani langsung oleh teknisi dengan estimasi waktu pengerjaan yang jelas.</p></div>
                <div class="adv-col"><p class="num">02</p><p class="ttl">Pantau status secara mandiri</p><p class="txt">Cek status perbaikan kapan saja dengan nomor service pada tiket Anda, tanpa perlu login.</p></div>
                <div class="adv-col"><p class="num">03</p><p class="ttl">Harga terjangkau</p><p class="txt">Biaya perbaikan disepakati di awal.</p></div>
            </div>
            <div class="adv-row">
                <div class="adv-col"><p class="num">04</p><p class="ttl">Tanpa biaya jika dibatalkan</p><p class="txt">Apabila perbaikan dibatalkan, Anda tidak dikenakan biaya apa pun.</p></div>
                <div class="adv-col"><p class="num">05</p><p class="ttl">Riwayat pengerjaan transparan</p><p class="txt">Setiap tahap pengerjaan tercatat lengkap beserta tanggalnya, sehingga mudah dipantau.</p></div>
                <div class="adv-col"><p class="num">06</p><p class="ttl">Data dan privasi terjaga</p><p class="txt">Kondisi awal laptop didokumentasikan, dan identitas pelanggan disamarkan pada halaman status.</p></div>
            </div>
        </div>
    </section>

    <section class="info">
        <div class="info-inner">
            <div class="info-left">
                <h3>Informasi toko</h3>
                <p>Kami siap membantu mengatasi masalah laptop Anda.</p>
            </div>
            <dl class="info-right">
                <div class="info-row"><dt>Alamat</dt><dd>Jl. Bintara Jaya No. 15, Kel. Bintara Jaya, Kec. Bekasi Barat, Kota Bekasi, Jawa Barat 17134</dd></div>
                <div class="info-row"><dt>Jam buka</dt><dd>Senin–Kamis, 09.00–21.00 WIB</dd></div>
                <div class="info-row"><dt>Kontak</dt><dd>+62 857-1175-8049</dd></div>
            </dl>
        </div>
    </section>

</div>
</body>
</html>
