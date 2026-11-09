<x-layouts.app active="teknisi" crumb="Teknisi" title="Service Laptop · Teknisi">
    <div class="row between wrap">
        <div>
            <h1>Teknisi</h1>
        </div>
        <button class="btn p" id="btnAddTech">Tambah teknisi</button>
    </div>

    <div class="tbl" style="margin-top:0">
        <div class="th tk">
            <span>Nama dan no. HP</span>
            <span>Total service</span>
            <span>Status</span>
            <span class="r">Aksi</span>
            <span></span>
        </div>

        @forelse($stats as $st)
            @php
                $t = $st['technician'];
            @endphp
            <div class="tr tk" onclick="window.location.href='{{ route('services.index', ['technician_id' => $t->id, 'filter' => 'Semua']) }}'" tabindex="0" role="link" onkeydown="if(event.key==='Enter')this.click()">
                <div class="nm">
                    <b>{{ $t->name }}</b>
                    <span>{{ $t->phone }}</span>
                    @if($t->user)
                        <span class="small muted">Email: {{ $t->user->email }}</span>
                    @endif
                </div>
                <div class="c-sv">
                    <span class="small" style="font-weight:500">
                        {{ $st['total'] }} service
                        @if($st['dikerjakan'] > 0)
                            <span class="brandc">({{ $st['dikerjakan'] }} aktif)</span>
                        @endif
                    </span>
                </div>
                <div class="c-st">
                    <span class="small {{ $t->is_active ? 'brandc' : 'muted' }}" style="font-weight:500">
                        {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="c-ak actions" style="justify-content:flex-end;flex-wrap:nowrap;gap:8px">
                    <button type="button" class="btn s btn-edit-tech" data-id="{{ $t->id }}" data-name="{{ $t->name }}" data-phone="{{ $t->phone }}" data-email="{{ $t->user?->email ?? '' }}" data-active="{{ $t->is_active ? '1' : '0' }}" onclick="openEditTech(this, event)">Ubah</button>
                    @if($st['total'] === 0)
                        <form action="{{ route('technicians.destroy', $t->id) }}" method="POST" style="margin:0" onsubmit="return confirm('Hapus teknisi ini?')" onclick="event.stopPropagation()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn d">Hapus</button>
                        </form>
                    @endif
                </div>
                <div class="c-ch muted">
                    <x-icon name="chevR" size="16" />
                </div>
            </div>
        @empty
            <div class="empty">Belum ada data teknisi.</div>
        @endforelse
    </div>

    @push('scripts')
    <script>
        document.getElementById('btnAddTech').addEventListener('click', () => {
            const html = `
                <h1>Tambah teknisi</h1>
                <form action="{{ route('technicians.store') }}" method="POST" class="stack" style="gap:16px">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                    <div class="f">
                        <label for="tn">Nama teknisi</label>
                        <input class="in" id="tn" name="name" maxlength="60" autocomplete="off" required>
                    </div>
                    <div class="f">
                        <label for="th">No. HP</label>
                        <input class="in" id="th" name="phone" type="tel" inputmode="tel" maxlength="20" required>
                    </div>
                    <div class="f">
                        <label for="temail">Email</label>
                        <input class="in" id="temail" name="email" type="email" maxlength="255" required>
                    </div>
                    <div class="f">
                        <label for="tp">Kata sandi</label>
                        <input class="in" id="tp" name="password" type="password" minlength="8" autocomplete="new-password" required>
                    </div>
                    <div class="ft">
                        <button type="button" class="btn s" data-a="closeov">Batal</button>
                        <button class="btn p" type="submit">Simpan</button>
                    </div>
                </form>
            `;
            openModal(html);
        });

        window.openEditTech = function(btn, e) {
            if (e) e.stopPropagation();
            const id = btn.dataset.id;
            const name = escapeHtml(btn.dataset.name);
            const phone = escapeHtml(btn.dataset.phone);
            const email = escapeHtml(btn.dataset.email || '');
            const isActive = btn.dataset.active === '1';

            const html = `
                <h1>Ubah data teknisi</h1>
                <form action="/teknisi/${id}" method="POST" class="stack" style="gap:16px">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="f">
                        <label for="tn">Nama teknisi</label>
                        <input class="in" id="tn" name="name" maxlength="60" autocomplete="off" value="${name}" required>
                    </div>
                    <div class="f">
                        <label for="th">No. HP</label>
                        <input class="in" id="th" name="phone" type="tel" inputmode="tel" maxlength="20" value="${phone}" required>
                    </div>
                    <div class="f">
                        <label for="temail_edit">Email</label>
                        <input class="in" id="temail_edit" name="email" type="email" maxlength="255" value="${email}" required>
                    </div>
                    <div class="f">
                        <label for="ts">Status</label>
                        <select class="in" id="ts" name="is_active">
                            <option value="1" ${isActive ? 'selected' : ''}>Aktif</option>
                            <option value="0" ${!isActive ? 'selected' : ''}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="f">
                        <label for="tp_edit">Ubah kata sandi</label>
                        <input class="in" id="tp_edit" name="password" type="password" minlength="8" autocomplete="new-password">
                    </div>
                    <div class="ft">
                        <button type="button" class="btn s" data-a="closeov">Batal</button>
                        <button class="btn p" type="submit">Update</button>
                    </div>
                </form>
            `;
            openModal(html);
        };
    </script>
    @endpush
</x-layouts.app>
