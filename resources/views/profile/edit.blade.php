<x-layouts.app active="profile" crumb="Profile" title="Service Laptop · Profile">
    <div>
        <h1>Profile</h1>
        <div class="small muted">Data akun dan kata sandi</div>
    </div>

    @if(session('success'))
        <div class="banner" role="status" style="border-color:var(--brand);background:var(--brand-bg, #eff6ff)">
            <x-icon name="check" size="16" class="brandc" />
            <span style="color:var(--brand)">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="card stack" style="gap:24px;padding:24px" novalidate>
        @csrf
        @method('PUT')

        <div class="grid2">
            <div class="stack" style="gap:16px">
                <h2>Data akun</h2>

                <x-field id="name" label="Nama" :error="$errors->first('name')">
                    <input class="in" id="name" name="name" maxlength="60" autocomplete="name" value="{{ old('name', $user->name) }}" required>
                </x-field>

                @if($user->isTechnician())
                    <x-field id="phone" label="No HP" :error="$errors->first('phone')">
                        <input class="in" id="phone" name="phone" type="tel" inputmode="tel" maxlength="20" value="{{ old('phone', $technician?->phone) }}" required>
                    </x-field>

                    <div class="f">
                        <label>Email</label>
                        <input class="in" value="{{ $user->email }}" disabled style="background:var(--bg);color:var(--muted);cursor:not-allowed">
                    </div>
                @else
                    <x-field id="email" label="Email" :error="$errors->first('email')">
                        <input class="in" id="email" name="email" type="email" autocomplete="email" value="{{ old('email', $user->email) }}" required>
                    </x-field>
                @endif
            </div>

            <div class="stack" style="gap:16px">
                <h2>Ubah kata sandi</h2>

                <x-field id="current_password" label="Kata sandi saat ini" :error="$errors->first('current_password')">
                    <input class="in" id="current_password" name="current_password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi saat ini">
                </x-field>

                <x-field id="password" label="Kata sandi baru" :error="$errors->first('password')">
                    <input class="in" id="password" name="password" type="password" autocomplete="new-password" placeholder="Minimal 8 karakter">
                </x-field>

                <x-field id="password_confirmation" label="Konfirmasi kata sandi" :error="$errors->first('password_confirmation')">
                    <input class="in" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Ulangi kata sandi baru">
                </x-field>
            </div>
        </div>

        <div style="height:1px;background:var(--border)"></div>

        <div class="row" style="justify-content:flex-end">
            <button class="btn p" type="submit" style="padding:0 24px">Update</button>
        </div>
    </form>
</x-layouts.app>
