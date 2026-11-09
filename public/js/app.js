const $=(s,r=document)=>r.querySelector(s);
const $$=(s,r=document)=>[...r.querySelectorAll(s)];

let tt;
function showToast(msg) {
    const t = $('#toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('on');
    clearTimeout(tt);
    tt = setTimeout(() => t.classList.remove('on'), 2500);
}

function openModal(html, wide = false, bare = false) {
    const ov = $('#ov');
    if (!ov) return;
    if (bare) {
        ov.innerHTML = `<div class="bk bk-bare" data-a="bk" style="background:rgba(0,0,0,.85);padding:16px"><div style="position:relative;display:inline-flex;flex-direction:column;align-items:center;max-width:95vw;max-height:95vh">${html}</div></div>`;
    } else {
        ov.innerHTML = `<div class="bk" data-a="bk"><div class="md${wide ? ' w' : ''}" role="dialog" aria-modal="true">${html}</div></div>`;
    }
    const f = $('#ov input, #ov textarea, #ov select, #ov button');
    if (f) f.focus();
    trapFocus();
}

function trapFocus() {
    const md = $('#ov .md, #ov [style*="display:inline-flex"]');
    if (!md) return;
    const focusable = $$('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])', md);
    if (focusable.length === 0) return;
    const first = focusable[0], last = focusable[focusable.length - 1];
    const handler = (e) => {
        if (e.key !== 'Tab') return;
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    };
    md.addEventListener('keydown', handler);
}

function closeModal() {
    const ov = $('#ov');
    if (ov) ov.innerHTML = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
});

document.addEventListener('click', e => {
    const el = e.target.closest('[data-a]');
    if (!el) return;
    const a = el.dataset.a;

    if (a === 'closeov') {
        closeModal();
        document.body.classList.remove('menu-open');
    } else if (a === 'bk') {
        if (e.target.classList.contains('bk')) {
            closeModal();
            document.body.classList.remove('menu-open');
        }
    } else if (a === 'menu') {
        const tmpl = document.getElementById('appMenuTemplate');
        if (tmpl) {
            const ov = $('#ov');
            if (!ov) return;
            ov.innerHTML = tmpl.innerHTML;
            document.body.classList.add('menu-open');
        }
    } else if (a === 'pubmenu') {
        const tmpl = document.getElementById('pubMenuTemplate');
        if (tmpl) {
            const ov = $('#ov');
            if (!ov) return;
            ov.innerHTML = tmpl.innerHTML;
            document.body.classList.add('menu-open');
        }
    }
});

// Helper formatting rupiah
function formatRupiah(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
