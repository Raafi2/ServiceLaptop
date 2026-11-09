<x-layouts.base title="Service Laptop · Masuk">
    <div class="lnav">
        <a class="brand" href="{{ route('home') }}">
            <img class="logo" src="/LogoServiceLaptop.png" width="32" height="32" alt="">Service Laptop
        </a>
        <a class="btn g" href="{{ route('home') }}">Beranda</a>
    </div>

    <div class="center">
        <form class="login" action="{{ route('login') }}" method="POST" novalidate>
            @csrf
            <div>
                <div class="disp">Masuk</div>
                <p class="muted" style="margin-top:6px">Masuk untuk mengelola data service laptop.</p>
            </div>

            @if($errors->has('email') && $errors->first('email') === 'Terlalu banyak percobaan, coba sebentar lagi.')
                <div class="banner" role="alert">
                    <x-icon name="alert" size="16" />
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @elseif($errors->any())
                <div class="banner" role="alert">
                    <x-icon name="alert" size="16" />
                    <span>Email atau kata sandi salah</span>
                </div>
            @endif

            <x-field id="em" label="Email" :error="$errors->first('email')">
                <input class="in" id="em" name="email" type="text" autocomplete="username" placeholder="Masukkan email" value="{{ old('email') }}" required>
            </x-field>

            <x-field id="pw" label="Kata sandi" :error="$errors->first('password')">
                <div style="position:relative">
                    <input class="in" id="pw" name="password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" style="padding-right:40px" required>
                    <button type="button" id="btnTogglePw" onclick="togglePassword()" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;padding:8px;cursor:pointer;color:var(--muted);display:flex;align-items:center;justify-content:center" aria-label="Lihat kata sandi">
                        <span id="eyeIconWrap">
                            <x-icon name="eye" size="18" />
                        </span>
                    </button>
                </div>
            </x-field>

            <script>
                function togglePassword() {
                    const pw = document.getElementById('pw');
                    const wrap = document.getElementById('eyeIconWrap');
                    const btn = document.getElementById('btnTogglePw');
                    if (pw.type === 'password') {
                        pw.type = 'text';
                        btn.setAttribute('aria-label', 'Sembunyikan kata sandi');
                        wrap.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>';
                    } else {
                        pw.type = 'password';
                        btn.setAttribute('aria-label', 'Lihat kata sandi');
                        wrap.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
                    }
                }
            </script>

            <button class="btn p w" type="submit">Masuk</button>

            <div class="row small muted" style="justify-content:center;gap:4px">
                Pelanggan? <a class="brandc" style="font-weight:500" href="{{ route('check') }}">Cek Status Perbaikan</a>
            </div>
        </form>
    </div>
</x-layouts.base>
