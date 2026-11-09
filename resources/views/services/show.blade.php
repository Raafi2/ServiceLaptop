<x-layouts.app active="service" :crumb="'Daftar service &nbsp;/&nbsp; ' . $service->tracking_no" :title="'Service Laptop · ' . $service->tracking_no">
    <a href="{{ route('services.index') }}" class="small muted" style="text-decoration:none;font-weight:500">Daftar service</a>

    <div class="card stack" style="gap:16px">
        <div class="row between wrap" style="align-items:flex-start">
            <div>
                <div class="lbl muted">Nomor service</div>
                <div class="row wrap" style="gap:12px;margin-top:4px">
                    <span class="mono" style="font-size:24px;line-height:30px;font-weight:500;letter-spacing:-.4px">{{ $service->tracking_no }}</span>
                    <x-status-badge :status="$service->status" />
                </div>
            </div>
            <div class="actions">
                <button class="btn s" onclick="navigator.clipboard.writeText('{{ $service->tracking_no }}').then(() => showToast('Nomor lacak disalin'))">Salin nomor</button>
                @unless(auth()->user()->isTechnician())
                    <button class="btn p" id="btnReceipt">Unduh ticket service</button>
                @endunless
            </div>
        </div>

        <div style="height:1px;background:var(--border)"></div>

        <div class="row between wrap">
            <div class="muted" style="flex:1;min-width:200px">
                @if($service->status->value === 'dikerjakan')
                    Dikerjakan oleh {{ $service->technician->name }} sejak {{ $service->received_at->locale('id')->isoFormat('D MMM Y') }}.
                @elseif($service->status->value === 'selesai')
                    Laptop siap diambil. Pembayaran dilakukan langsung di toko.
                @elseif($service->status->value === 'serahkan')
                    Diserahkan pada {{ $service->handed_over_at ? $service->handed_over_at->locale('id')->isoFormat('D MMM Y') : '-' }}.
                @elseif($service->status->value === 'dibatalkan')
                    Dibatalkan. {{ $service->cancel_reason ? 'Alasan: ' . $service->cancel_reason : '' }}
                @endif
            </div>

            <div class="actions">
                @if($service->status->value === 'dikerjakan')
                    @if(auth()->user()?->isAdmin())
                        <button class="btn s" id="btnEdit" style="border-radius:4px">Ubah data</button>
                    @elseif(auth()->user()?->isTechnician())
                        <button class="btn s" id="btnCancel" style="color:var(--danger)">Batalkan service</button>
                        <button class="btn p" id="btnFinish">Tandai selesai</button>
                    @endif
                @elseif($service->status->value === 'selesai')
                    @if(auth()->user()?->isAdmin())
                        <button class="btn s" id="btnEdit" style="border-radius:4px">Ubah data</button>
                        <button class="btn p" id="btnHandover">Serahkan laptop</button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="grid2">
        <div class="card">
            <h2>Data service</h2>
            <dl style="margin:0">
                <div class="kv">
                    <dt>Pelanggan</dt>
                    <dd>{{ $service->customer->name }}</dd>
                </div>
                <div class="kv">
                    <dt>No HP</dt>
                    <dd>{{ $service->customer->phone }}</dd>
                </div>
                <div class="kv">
                    <dt>Laptop</dt>
                    <dd>{{ $service->laptop }}</dd>
                </div>
                <div class="kv">
                    <dt>Keluhan</dt>
                    <dd>{{ $service->complaint }}</dd>
                </div>
                <div class="kv">
                    <dt>Biaya disepakati</dt>
                    <dd>Rp {{ number_format($service->agreed_cost, 0, ',', '.') }}</dd>
                </div>
                <div class="kv">
                    <dt>Teknisi</dt>
                    <dd>{{ $service->technician->name }}</dd>
                </div>
                @if($service->status->value === 'dibatalkan')
                    <div class="kv">
                        <dt>Alasan pembatalan</dt>
                        <dd>{{ $service->cancel_reason }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="stack">
            <div class="card">
                <h2 style="margin-bottom:14px">Riwayat status</h2>
                <x-status-timeline :logs="$service->logs" />
            </div>

            <div class="card stack" style="gap:12px">
                <h2>Foto kondisi awal</h2>
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));gap:12px">
                    @if($service->photo_path)
                        <div>
                            <div class="small muted" style="margin-bottom:4px">Foto</div>
                            <button type="button" onclick="openPhotoViewer('{{ route('services.photo', str_replace('services/', '', $service->photo_path)) }}')" title="Buka foto ukuran penuh" style="width:100%;aspect-ratio:16/9;padding:0;border:1px solid var(--border);border-radius:6px;background:#09090b;cursor:pointer;overflow:hidden;display:block">
                                <img src="{{ route('services.photo', str_replace('services/', '', $service->photo_path)) }}" alt="Foto kondisi awal laptop" width="400" height="300" style="width:100%;height:auto;object-fit:contain;display:block">
                            </button>
                        </div>
                    @else
                        <div class="small muted">Foto tidak tersedia</div>
                    @endif

                    @if($service->photo_path_2)
                        <div>
                            <div class="small muted" style="margin-bottom:4px">Foto tambahan</div>
                            <button type="button" onclick="openPhotoViewer('{{ route('services.photo', str_replace('services/', '', $service->photo_path_2)) }}')" title="Buka foto ukuran penuh" style="width:100%;aspect-ratio:16/9;padding:0;border:1px solid var(--border);border-radius:6px;background:#09090b;cursor:pointer;overflow:hidden;display:block">
                                <img src="{{ route('services.photo', str_replace('services/', '', $service->photo_path_2)) }}" alt="Foto tambahan kondisi awal laptop" width="400" height="300" style="width:100%;height:auto;object-fit:contain;display:block">
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.openPhotoViewer = function(src) {
            if (!src) return;
            const html = `<img src="${src}" alt="Foto kondisi laptop" style="display:block;max-width:90vw;max-height:85vh;object-fit:contain;border-radius:8px">`;
            openModal(html, false, true);
        };

        const btnHandover = document.getElementById('btnHandover');
        if (btnHandover) {
            btnHandover.addEventListener('click', () => {
                const html = `
                    <h1>Serahkan laptop?</h1>
                    <p class="muted">{{ $service->customer->name }}, {{ $service->laptop }}. Pelanggan membayar <b style="color:var(--text)">Rp {{ number_format($service->agreed_cost, 0, ',', '.') }}</b> langsung di toko.</p>
                    <form action="{{ route('services.handover', $service->tracking_no) }}" method="POST" style="margin:0">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                        <div class="ft">
                            <button type="button" class="btn s" data-a="closeov">Kembali</button>
                            <button type="submit" class="btn p">Serahkan laptop</button>
                        </div>
                    </form>
                `;
                openModal(html);
            });
        }

        const btnCancel = document.getElementById('btnCancel');
        if (btnCancel) {
            btnCancel.addEventListener('click', () => {
                const html = `
                    <h1>Batalkan service?</h1>
                    <p class="muted">{{ $service->customer->name }}, {{ $service->laptop }}. Alasan pembatalan wajib diisi dan tidak dapat diubah.</p>
                    <form action="{{ route('services.cancel', $service->tracking_no) }}" method="POST" class="stack" style="gap:16px">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                        <div class="f">
                            <label for="alasan">Alasan pembatalan</label>
                            <textarea class="in" id="alasan" name="alasan" maxlength="200" required></textarea>
                            <div class="hint">Maksimal 200 karakter</div>
                        </div>
                        <div class="ft">
                            <button type="button" class="btn s" data-a="closeov">Kembali</button>
                            <button type="submit" class="btn d">Batalkan service</button>
                        </div>
                    </form>
                `;
                openModal(html);
            });
        }

        const btnEdit = document.getElementById('btnEdit');
        if (btnEdit) {
            btnEdit.addEventListener('click', () => {
                const html = `
                    <h1>Ubah data service</h1>
                    <form action="{{ route('services.update', $service->tracking_no) }}" method="POST" class="stack" style="gap:16px">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="f">
                            <label for="laptop">Laptop (merek dan tipe)</label>
                            <input class="in" id="laptop" name="laptop" maxlength="100" value="{{ addslashes($service->laptop) }}" required>
                        </div>
                        <div class="f">
                            <label for="keluhan">Keluhan</label>
                            <textarea class="in" id="keluhan" name="keluhan" maxlength="500" required>{{ addslashes($service->complaint) }}</textarea>
                        </div>
                        <div class="f">
                            <label for="teknisi_id">Teknisi penanggung jawab</label>
                            <select class="in" id="teknisi_id" name="teknisi_id" required>
                                @foreach($technicians as $t)
                                    <option value="{{ $t->id }}" {{ $service->technician_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ft">
                            <button type="button" class="btn s" data-a="closeov">Batal</button>
                            <button type="submit" class="btn p">Simpan perubahan</button>
                        </div>
                    </form>
                `;
                openModal(html, true);
            });
        }

        const btnFinish = document.getElementById('btnFinish');
        if (btnFinish) {
            btnFinish.addEventListener('click', () => {
                const token = document.querySelector('meta[name=csrf-token]')?.content;
                const html = `
                    <h1>Tandai service selesai?</h1>
                    <p class="muted">Status laptop akan diubah menjadi <b style="color:var(--text)">Selesai</b> dan siap diambil oleh pelanggan di toko.</p>
                    <form action="{{ route('services.finish', $service->tracking_no) }}" method="POST" class="ft">
                        <input type="hidden" name="_token" value="${token}">
                        <button type="button" class="btn s" data-a="closeov">Batal</button>
                        <button type="submit" class="btn p">Ya, tandai selesai</button>
                    </form>
                `;
                openModal(html);
            });
        }

        @unless(auth()->user()->isTechnician())
        const btnReceipt = document.getElementById('btnReceipt');
        btnReceipt.addEventListener('click', () => {
            const checkUrl = "{{ route('check.nomor', $service->tracking_no) }}";
            const html = `
                <h1>Unduh ticket service</h1>
                <div class="paper">
                    <div class="brand">Ticket Service</div>
                    <div class="lbl muted">Nomor Service</div>
                    <div class="mono" style="font-size:24px;line-height:30px;font-weight:500">{{ $service->tracking_no }}</div>
                    <div style="height:1px;background:var(--border)"></div>
                    <dl style="margin:0">
                        <div class="kv"><dt class="muted">Pelanggan</dt><dd>{{ $service->customer->name }}</dd></div>
                        <div class="kv"><dt class="muted">Laptop</dt><dd>{{ $service->laptop }}</dd></div>
                        <div class="kv"><dt class="muted">Keluhan</dt><dd>{{ $service->complaint }}</dd></div>
                        <div class="kv"><dt class="muted">Biaya disepakati</dt><dd>Rp {{ number_format($service->agreed_cost, 0, ',', '.') }}</dd></div>
                        <div class="kv"><dt class="muted">Tanggal masuk</dt><dd>{{ $service->received_at->locale('id')->isoFormat('D MMM Y') }}</dd></div>
                    </dl>
                    <div style="height:1px;background:var(--border)"></div>
                    <div class="small muted">Simpan tanda terima ini. Cek status perbaikan laptop Anda pada website resmi kami.</div>
                </div>
                <div class="ft">
                    <button type="button" class="btn s" data-a="closeov">Tutup</button>
                    <button type="button" class="btn p" id="btnDlPdf">Unduh ticket (PDF)</button>
                </div>
            `;
            openModal(html, true);

            document.getElementById('btnDlPdf').addEventListener('click', async () => {
                const canvas = await generateReceiptCanvas();
                const pdf = generatePdfBlob(canvas);
                saveBlob(pdf, 'Ticket-service-{{ $service->tracking_no }}.pdf');
                showToast('PDF ticket service diunduh');
            });
        });

        function paintReceipt(ctx, checkUrl) {
            const W = 720, P = 40;
            let y = P;
            ctx.textBaseline = 'top';

            const T = (txt, font, color) => {
                ctx.font = font;
                ctx.fillStyle = color;
                ctx.fillText(txt, P, y);
            };

            const wrapText = (txt, maxW, font) => {
                ctx.font = font;
                const lines = [];
                let line = '';
                for (const word of String(txt).split(/\s+/)) {
                    const test = line ? line + ' ' + word : word;
                    if (ctx.measureText(test).width <= maxW) {
                        line = test;
                        continue;
                    }
                    if (line) lines.push(line);
                    line = word;
                }
                if (line) lines.push(line);
                return lines;
            };

            const hr = () => {
                ctx.fillStyle = '#E7E5E4';
                ctx.fillRect(P, y, W - 2 * P, 1);
                y += 16;
            };

            const SF = 'Inter,system-ui,-apple-system,sans-serif';
            const MF = '"JetBrains Mono",monospace';

            T('Ticket Service', '600 20px ' + SF, '#0B1730'); y += 30;
            T('Nomor Service', '14px ' + SF, '#78716C'); y += 30;
            T('{{ $service->tracking_no }}', '500 34px ' + MF, '#1C1917'); y += 52;
            hr();

            const rows = [
                ['Pelanggan', '{{ addslashes($service->customer->name) }}'],
                ['Laptop', '{{ addslashes($service->laptop) }}'],
                ['Keluhan', '{{ addslashes($service->complaint) }}'],
                ['Biaya disepakati', 'Rp {{ number_format($service->agreed_cost, 0, ',', '.') }}'],
                ['Tanggal masuk', '{{ $service->received_at->locale('id')->isoFormat('D MMM Y') }}']
            ];

            rows.forEach(r => {
                const lines = wrapText(r[1], W - 2 * P - 160, '500 15px ' + SF);
                ctx.font = '14px ' + SF;
                ctx.fillStyle = '#78716C';
                ctx.fillText(r[0], P, y);

                lines.forEach((l, idx) => {
                    ctx.font = '500 15px ' + SF;
                    ctx.fillStyle = '#1C1917';
                    ctx.fillText(l, P + 160, y + idx * 22);
                });
                y += Math.max(1, lines.length) * 22 + 12;
            });

            y += 4;
            hr();
            wrapText('Simpan tanda terima ini. Cek status perbaikan laptop Anda pada website resmi kami.', W - 2 * P, '13px ' + SF).forEach(l => {
                T(l, '13px ' + SF, '#78716C');
                y += 18;
            });

            return y + P;
        }

        async function generateReceiptCanvas() {
            try { await document.fonts.ready; } catch(e) {}
            const checkUrl = "{{ route('check.nomor', $service->tracking_no) }}";
            const tempCtx = document.createElement('canvas').getContext('2d');
            const h = paintReceipt(tempCtx, checkUrl);

            const c = document.createElement('canvas');
            c.width = 1440;
            c.height = Math.ceil(h * 2);
            const ctx = c.getContext('2d');
            ctx.scale(2, 2);
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, 720, h + 1);
            paintReceipt(ctx, checkUrl);
            return c;
        }

        function saveBlob(blob, name) {
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = name;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(() => URL.revokeObjectURL(a.href), 1500);
        }

        function generatePdfBlob(c) {
            const jpg = atob(c.toDataURL('image/jpeg', 0.92).split(',')[1]);
            const w = c.width, h = c.height, pw = (w / 4).toFixed(2), ph = (h / 4).toFixed(2);
            const parts = [], offs = [];
            let off = 0;
            const add = t => { parts.push(t); off += t.length; };

            add('%PDF-1.4\n');
            const obj = (n, b) => { offs[n] = off; add(n + ' 0 obj\n' + b + '\nendobj\n'); };
            obj(1, '<</Type/Catalog/Pages 2 0 R>>');
            obj(2, '<</Type/Pages/Kids[3 0 R]/Count 1>>');
            obj(3, `<</Type/Page/Parent 2 0 R/MediaBox[0 0 ${pw} ${ph}]/Resources<</XObject<</Im0 4 0 R>>>>/Contents 5 0 R>>`);
            offs[4] = off;
            add(`4 0 obj\n<</Type/XObject/Subtype/Image/Width ${w}/Height ${h}/ColorSpace/DeviceRGB/BitsPerComponent 8/Filter/DCTDecode/Length ${jpg.length}>>\nstream\n`);
            add(jpg);
            add('\nendstream\nendobj\n');

            const ct = `q ${pw} 0 0 ${ph} 0 0 cm /Im0 Do Q`;
            obj(5, `<</Length ${ct.length}>>\nstream\n${ct}\nendstream`);
            const xr = off;
            add('xref\n0 6\n0000000000 65535 f \n' + [1,2,3,4,5].map(n => String(offs[n]).padStart(10, '0') + ' 00000 n \n').join(''));
            add(`trailer\n<</Size 6/Root 1 0 R>>\nstartxref\n${xr}\n%%EOF`);

            const str = parts.join('');
            const u = new Uint8Array(str.length);
            for (let i = 0; i < str.length; i++) u[i] = str.charCodeAt(i);
            return new Blob([u], { type: 'application/pdf' });
        }
        @endunless
    </script>
    @endpush
</x-layouts.app>
