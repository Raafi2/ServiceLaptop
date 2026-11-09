@props([
    'active' => 'servis',
    'crumb' => 'Daftar service',
    'title' => 'Service Laptop',
])

<x-layouts.base :title="$title">
    <div class="shell">
        <aside class="side">
            <a class="brand" href="{{ route('home') }}">
                <img class="logo" src="/LogoServiceLaptop.png" width="28" height="28" alt="">Service Laptop
            </a>
            <div class="sl">Menu</div>
            <nav class="nav">
                <a href="{{ route('services.index') }}" class="{{ $active === 'servis' || $active === 'service' ? 'on' : '' }}">
                    <x-icon name="list" size="18" />Daftar service
                </a>
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('services.create') }}" class="{{ $active === 'baru' ? 'on' : '' }}">
                        <x-icon name="plus" size="18" />Penerimaan service
                    </a>
                    <a href="{{ route('customers.index') }}" class="{{ $active === 'pelanggan' ? 'on' : '' }}">
                        <x-icon name="users" size="18" />Pelanggan
                    </a>
                    <a href="{{ route('technicians.index') }}" class="{{ $active === 'teknisi' ? 'on' : '' }}">
                        <x-icon name="tool" size="18" />Teknisi
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}" class="{{ $active === 'profile' ? 'on' : '' }}">
                    <x-icon name="user" size="18" />Profile
                </a>
            </nav>
            <div class="sp"></div>
            <div class="nav">
                <a href="{{ route('check') }}">
                    <x-icon name="link" size="18" />Cek status publik
                </a>
            </div>
            <div class="user">
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;flex:1;min-width:0">
                    <div style="flex:1;min-width:0;overflow:hidden">
                        <div style="font-weight:500;white-space:nowrap;text-overflow:ellipsis;overflow:hidden">{{ auth()->user()->name ?? 'Pengguna' }}</div>
                        <div class="small muted">{{ auth()->user()?->isTechnician() ? 'Teknisi' : 'Admin toko' }}</div>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" style="margin:0">
                    @csrf
                    <button type="submit" class="btn g" style="height:32px;padding:0 10px">Keluar</button>
                </form>
            </div>
        </aside>

        <div class="main">
            <header class="mtop">
                <a class="brand" href="{{ route('home') }}">
                    <img class="logo" src="/LogoServiceLaptop.png" width="28" height="28" alt="">Service Laptop
                </a>
                <button class="ib" data-a="menu" aria-label="Buka menu">
                    <span class="icon-open"><x-icon name="menu" size="22" /></span>
                    <span class="icon-close"><x-icon name="x" size="22" /></span>
                </button>
            </header>
            <div class="dtop">
                <span class="small muted">{!! $crumb !!}</span>
                <span class="small muted">{{ now()->locale('id')->isoFormat('D MMM Y') }}</span>
            </div>
            <main class="content">
                {{ $slot }}
            </main>
        </div>
    </div>

    <template id="appMenuTemplate">
        <div class="bk" data-a="bk" style="background:rgba(11,23,48,.45)">
            <div class="menu" role="dialog" aria-modal="true">
                <div class="mt">
                    <a class="brand" href="{{ route('home') }}">
                        <img class="logo" src="/LogoServiceLaptop.png" width="28" height="28" alt="">Service Laptop
                    </a>
                    <button class="ib" data-a="closeov" aria-label="Tutup menu">
                        <x-icon name="x" size="22" />
                    </button>
                </div>
                <div class="nav">
                    <a href="{{ route('services.index') }}" class="{{ $active === 'servis' || $active === 'service' ? 'on' : '' }}">
                        <x-icon name="list" size="20" />Daftar service
                    </a>
                    @if(auth()->user()?->isAdmin())
                        <a href="{{ route('services.create') }}" class="{{ $active === 'baru' ? 'on' : '' }}">
                            <x-icon name="plus" size="20" />Penerimaan service
                        </a>
                        <a href="{{ route('customers.index') }}" class="{{ $active === 'pelanggan' ? 'on' : '' }}">
                            <x-icon name="users" size="20" />Pelanggan
                        </a>
                        <a href="{{ route('technicians.index') }}" class="{{ $active === 'teknisi' ? 'on' : '' }}">
                            <x-icon name="tool" size="20" />Teknisi
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="{{ $active === 'profile' ? 'on' : '' }}">
                        <x-icon name="user" size="20" />Profile
                    </a>
                </div>
                <div class="sp"></div>
                <div class="nav" style="border-top:1px solid var(--border)">
                    <a href="{{ route('check') }}">
                        <x-icon name="link" size="20" />Cek status publik
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="margin:0">
                        @csrf
                        <button type="submit">
                            <x-icon name="logout" size="20" />Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</x-layouts.base>
