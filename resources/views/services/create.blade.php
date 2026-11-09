<x-layouts.app active="baru" crumb="Daftar service &nbsp;/&nbsp; Penerimaan service" title="Service Laptop · Penerimaan Service">
    <a href="{{ route('services.index') }}" class="small muted" style="text-decoration:none;font-weight:500">Daftar service</a>

    <div>
        <h1>Penerimaan service</h1>
    </div>

    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="grid2" id="newForm" novalidate>
        @csrf
        <div class="card stack" style="gap:16px;padding:24px">
            <h2>Data pelanggan</h2>
            <div class="two">
                <x-field id="nama" label="Nama pelanggan" :error="$errors->first('nama')">
                    <input class="in" id="nama" name="nama" maxlength="100" autocomplete="off" value="{{ old('nama') }}" required>
                </x-field>

                <x-field id="hp" label="No HP" :error="$errors->first('hp')">
                    <input class="in" id="hp" name="hp" type="tel" inputmode="tel" maxlength="20" value="{{ old('hp') }}" required>
                </x-field>
            </div>

            <x-field id="laptop" label="Laptop (merek dan tipe)" :error="$errors->first('laptop')">
                <input class="in" id="laptop" name="laptop" maxlength="100" placeholder="ASUS VivoBook 14 X415" value="{{ old('laptop') }}" required>
            </x-field>

            <x-field id="keluhan" label="Keluhan" hint="Maksimal 500 karakter" :error="$errors->first('keluhan')">
                <textarea class="in" id="keluhan" name="keluhan" maxlength="500" required>{{ old('keluhan') }}</textarea>
            </x-field>

            <div class="two">
                <x-field id="biaya" label="Biaya yang disepakati (Rp)" :error="$errors->first('biaya')">
                    <div class="pre">
                        <span class="muted">Rp</span>
                        <input class="in" id="biaya" name="biaya" inputmode="numeric" placeholder="350.000" value="{{ old('biaya') ? number_format((int) preg_replace('/\D/', '', old('biaya')), 0, ',', '.') : '' }}" required>
                    </div>
                </x-field>

                <x-field id="teknisi_id" label="Teknisi penanggung jawab" :error="$errors->first('teknisi_id')">
                    <select class="in" id="teknisi_id" name="teknisi_id" required>
                        <option value="">Pilih Teknisi</option>
                        @foreach($technicians as $t)
                            <option value="{{ $t->id }}" {{ old('teknisi_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>
        </div>

        <div class="stack">
            <div class="card stack" style="gap:16px">
                <h2>Foto kondisi awal</h2>

                <div class="f">
                    <label for="foto">Foto <span style="color:var(--err)">*</span></label>
                    <input class="in" type="file" id="foto" name="foto" accept="image/jpeg,image/png" required>
                    <div class="hint">JPG atau PNG, maks 10 MB</div>
                    @error('foto')
                        <div class="hint err" style="color:var(--danger)">{{ $message }}</div>
                    @enderror
                </div>

                <div class="f">
                    <label for="foto2">Foto tambahan <span class="muted">(opsional)</span></label>
                    <input class="in" type="file" id="foto2" name="foto2" accept="image/jpeg,image/png">
                    <div class="hint">JPG atau PNG, maks 10 MB</div>
                    @error('foto2')
                        <div class="hint err" style="color:var(--danger)">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="sticky" style="align-items:center">
                <a class="btn g" href="{{ route('services.index') }}">Batal</a>
                <button class="btn p" type="submit">Simpan service</button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        const hpInput = document.getElementById('hp');
        const biayaInput = document.getElementById('biaya');
        const fotoInput = document.getElementById('foto');
        const fotoInput2 = document.getElementById('foto2');
        const form = document.getElementById('newForm');
    </script>
    @endpush
</x-layouts.app>
