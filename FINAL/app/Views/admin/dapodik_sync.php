<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .sync-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem; }
    .sync-card h4 { margin-bottom: 0.5rem; }
    .sync-card p { opacity: 0.85; margin-bottom: 0; font-size: 0.9rem; }
    .diff-added { background:rgba(16,185,129,0.1); border-left:3px solid #10b981; }
    .diff-changed { background:rgba(245,158,11,0.1); border-left:3px solid #f59e0b; }
    .diff-same { opacity:0.5; }
    .progress-sync { height:6px; border-radius:3px; }
    .preview-table { font-size:0.8rem; }
    .preview-table th { font-size:0.7rem; text-transform:uppercase; color:#64748b; }
    .nav-pills .nav-link { font-weight:600; }
    .nav-pills .nav-link.active { background:#4f46e5; }
</style>

<!-- Header Card -->
<div class="sync-card">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-cloud-download" style="font-size:2.5rem;opacity:0.9"></i>
        <div>
            <h4 class="fw-bold mb-1">Sinkronisasi Dapodik</h4>
            <p>Tarik data profil siswa & guru dari aplikasi Dapodik lokal</p>
        </div>
    </div>
</div>

<!-- Step 1: Connection Settings -->
<div class="card-custom mb-3">
    <div class="card-header">
        <span class="badge bg-primary rounded-pill me-2">1</span>
        Konfigurasi Koneksi Dapodik
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label fw-semibold">URL Dapodik <small class="text-muted">(dari PC sekolah)</small></label>
                <input type="text" id="dapodikUrl" class="form-control" value="http://localhost:5774" placeholder="http://localhost:5774">
                <small class="text-muted">Biasanya http://localhost:5774 atau IP lokal operator</small>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Token API <small class="text-muted">(Access Key)</small></label>
                <div class="input-group">
                    <input type="password" id="dapodikToken" class="form-control" placeholder="Paste token dari Dapodik...">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleToken()">
                        <i class="bi bi-eye" id="tokenEyeIcon"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="testConnection()">
                    <i class="bi bi-plug me-1"></i>Tes Koneksi
                </button>
            </div>
        </div>
        <div id="connectionStatus" class="mt-3" style="display:none"></div>
    </div>
</div>

<!-- Step 2: Sync Tabs (Siswa & Guru) -->
<div id="step2Card" style="display:none">
    <div class="card-custom mb-3">
        <div class="card-header">
            <span class="badge bg-primary rounded-pill me-2">2</span>
            Preview & Sinkronisasi Data
        </div>
        <div class="card-body">
            <!-- Tabs -->
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabSiswa" onclick="fetchSiswaData()">
                        <i class="bi bi-people me-1"></i>Data Siswa
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabGuru" onclick="fetchGuruData()">
                        <i class="bi bi-person-badge me-1"></i>Data Guru
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Siswa Tab -->
                <div class="tab-pane fade show active" id="tabSiswa">
                    <div id="siswaProgress" style="display:none">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span id="siswaProgressText" style="font-size:0.85rem">Mengambil data siswa...</span>
                        </div>
                        <div class="progress progress-sync"><div class="progress-bar bg-primary" id="siswaProgressBar" style="width:0%"></div></div>
                    </div>
                    <div id="siswaResult" style="display:none">
                        <div class="row g-2 mb-3">
                            <div class="col-4"><div class="stat-card text-center" style="padding:0.75rem"><div class="fw-bold text-success" style="font-size:1.5rem" id="siswaCountNew">0</div><div style="font-size:0.7rem;color:#64748b">Siswa Baru</div></div></div>
                            <div class="col-4"><div class="stat-card text-center" style="padding:0.75rem"><div class="fw-bold text-warning" style="font-size:1.5rem" id="siswaCountChanged">0</div><div style="font-size:0.7rem;color:#64748b">Data Berubah</div></div></div>
                            <div class="col-4"><div class="stat-card text-center" style="padding:0.75rem"><div class="fw-bold text-muted" style="font-size:1.5rem" id="siswaCountSame">0</div><div style="font-size:0.7rem;color:#64748b">Tidak Berubah</div></div></div>
                        </div>
                        <div class="table-responsive" style="max-height:350px;overflow-y:auto">
                            <table class="table table-sm preview-table mb-0"><thead class="sticky-top bg-white"><tr>
                                <th><input type="checkbox" id="siswaSelectAll" checked onchange="toggleAll('siswa')"></th>
                                <th>Status</th><th>NISN</th><th>Nama</th><th>JK</th><th>Perubahan</th>
                            </tr></thead><tbody id="siswaPreviewBody"></tbody></table>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Centang yang ingin disinkronkan</small>
                            <button class="btn btn-success" onclick="applyData('siswa')" id="btnApplySiswa">
                                <i class="bi bi-check-lg me-1"></i>Terapkan <span id="siswaSelectedCount">0</span> Perubahan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Guru Tab -->
                <div class="tab-pane fade" id="tabGuru">
                    <div id="guruProgress" style="display:none">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span id="guruProgressText" style="font-size:0.85rem">Mengambil data guru...</span>
                        </div>
                        <div class="progress progress-sync"><div class="progress-bar bg-primary" id="guruProgressBar" style="width:0%"></div></div>
                    </div>
                    <div id="guruResult" style="display:none">
                        <div class="row g-2 mb-3">
                            <div class="col-4"><div class="stat-card text-center" style="padding:0.75rem"><div class="fw-bold text-success" style="font-size:1.5rem" id="guruCountNew">0</div><div style="font-size:0.7rem;color:#64748b">Guru Baru</div></div></div>
                            <div class="col-4"><div class="stat-card text-center" style="padding:0.75rem"><div class="fw-bold text-warning" style="font-size:1.5rem" id="guruCountChanged">0</div><div style="font-size:0.7rem;color:#64748b">Data Berubah</div></div></div>
                            <div class="col-4"><div class="stat-card text-center" style="padding:0.75rem"><div class="fw-bold text-muted" style="font-size:1.5rem" id="guruCountSame">0</div><div style="font-size:0.7rem;color:#64748b">Tidak Berubah</div></div></div>
                        </div>
                        <div class="table-responsive" style="max-height:350px;overflow-y:auto">
                            <table class="table table-sm preview-table mb-0"><thead class="sticky-top bg-white"><tr>
                                <th><input type="checkbox" id="guruSelectAll" checked onchange="toggleAll('guru')"></th>
                                <th>Status</th><th>NUPTK</th><th>Nama</th><th>JK</th><th>Perubahan</th>
                            </tr></thead><tbody id="guruPreviewBody"></tbody></table>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Centang yang ingin disinkronkan</small>
                            <button class="btn btn-success" onclick="applyData('guru')" id="btnApplyGuru">
                                <i class="bi bi-check-lg me-1"></i>Terapkan <span id="guruSelectedCount">0</span> Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Step 3: Result -->
<div class="card-custom mb-3" id="step3Card" style="display:none">
    <div class="card-header"><span class="badge bg-success rounded-pill me-2">3</span> Hasil Sinkronisasi</div>
    <div class="card-body text-center py-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i>
        <h5 class="fw-bold mt-2">Sinkronisasi Berhasil!</h5>
        <p class="text-muted" id="resultDesc"></p>
        <div class="d-flex gap-2 justify-content-center mt-2">
            <a href="/admin/siswa" class="btn btn-outline-primary"><i class="bi bi-people me-1"></i>Data Siswa</a>
            <a href="/admin/guru" class="btn btn-outline-primary"><i class="bi bi-person-badge me-1"></i>Data Guru</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let syncStore = { siswa: [], guru: [] };
let siswaFetched = false, guruFetched = false;

function toggleToken() {
    const inp = document.getElementById('dapodikToken');
    const ico = document.getElementById('tokenEyeIcon');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function getConfig() {
    return {
        url: document.getElementById('dapodikUrl').value.replace(/\/+$/, ''),
        token: document.getElementById('dapodikToken').value
    };
}

async function testConnection() {
    const { url, token } = getConfig();
    const s = document.getElementById('connectionStatus');
    if (!url || !token) { s.style.display='block'; s.innerHTML='<div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Isi URL dan Token.</div>'; return; }
    s.style.display='block'; s.innerHTML='<div class="alert alert-info mb-0"><div class="spinner-border spinner-border-sm me-2"></div>Menghubungkan...</div>';
    try {
        const res = await fetch(url + '/WebService/getSekolah', { headers: { 'Authorization': 'Bearer ' + token } });
        const data = await res.json();
        if (data && (data.rows || data.nama)) {
            const sk = data.rows ? data.rows[0] : data;
            s.innerHTML = `<div class="alert alert-success mb-0"><i class="bi bi-check-circle me-2"></i><strong>Terhubung!</strong> Sekolah: <strong>${sk.nama||'N/A'}</strong> (NPSN: ${sk.npsn||'N/A'})</div>`;
            document.getElementById('step2Card').style.display = 'block';
            fetchSiswaData();
        } else {
            s.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-2"></i>Data sekolah tidak ditemukan. Periksa token.</div>';
        }
    } catch (err) {
        s.innerHTML = `<div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-2"></i><strong>Gagal terhubung.</strong><br>• Pastikan membuka dari PC sekolah<br>• Dapodik sedang berjalan<br><small>${err.message}</small></div>`;
    }
}

async function fetchSiswaData() {
    if (siswaFetched) return;
    const { url, token } = getConfig();
    show('siswaProgress'); hide('siswaResult');
    try {
        updateProg('siswa', 30, 'Mengambil data peserta didik...');
        const res = await fetch(url + '/WebService/getPesertaDidik', { headers: { 'Authorization': 'Bearer ' + token } });
        const data = await res.json();
        updateProg('siswa', 60, 'Membandingkan dengan database...');
        const cmp = await fetch('/admin/dapodik/compare', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ siswa: data.rows || data || [] })
        });
        syncStore.siswa = (await cmp.json()).results || [];
        updateProg('siswa', 100, 'Selesai!');
        setTimeout(() => { hide('siswaProgress'); show('siswaResult'); renderPreview('siswa'); siswaFetched = true; }, 400);
    } catch (err) { hide('siswaProgress'); alert('Gagal: ' + err.message); }
}

async function fetchGuruData() {
    if (guruFetched) return;
    const { url, token } = getConfig();
    show('guruProgress'); hide('guruResult');
    try {
        updateProg('guru', 30, 'Mengambil data GTK...');
        const res = await fetch(url + '/WebService/getGtk', { headers: { 'Authorization': 'Bearer ' + token } });
        const data = await res.json();
        updateProg('guru', 60, 'Membandingkan dengan database...');
        const cmp = await fetch('/admin/dapodik/compare-guru', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ guru: data.rows || data || [] })
        });
        syncStore.guru = (await cmp.json()).results || [];
        updateProg('guru', 100, 'Selesai!');
        setTimeout(() => { hide('guruProgress'); show('guruResult'); renderPreview('guru'); guruFetched = true; }, 400);
    } catch (err) { hide('guruProgress'); alert('Gagal: ' + err.message); }
}

function renderPreview(type) {
    const results = syncStore[type];
    const tbody = document.getElementById(type + 'PreviewBody');
    let nc=0,cc=0,sc=0; tbody.innerHTML='';
    const idField = type === 'siswa' ? 'nisn' : 'nuptk';
    results.forEach((r,i) => {
        let badge, cls='';
        if (r.status==='new') { badge='<span class="badge bg-success">Baru</span>'; cls='diff-added'; nc++; }
        else if (r.status==='changed') { badge='<span class="badge bg-warning text-dark">Berubah</span>'; cls='diff-changed'; cc++; }
        else { badge='<span class="badge bg-secondary">Sama</span>'; cls='diff-same'; sc++; }
        const changes = r.changes ? r.changes.map(c=>`<span class="badge bg-light text-dark border me-1 mb-1" style="font-size:0.65rem" title="${c.old} → ${c.new}">${c.field}</span>`).join(''):'−';
        const checked = r.status!=='same' ? 'checked' : '';
        tbody.innerHTML+=`<tr class="${cls}"><td><input type="checkbox" class="${type}-cb" data-idx="${i}" ${checked} onchange="updateCount('${type}')"></td>
            <td>${badge}</td><td><code style="font-size:0.75rem">${r[idField]||'-'}</code></td><td style="font-size:0.8rem">${r.nama||'-'}</td><td>${r.jenis_kelamin||'-'}</td><td>${changes}</td></tr>`;
    });
    document.getElementById(type+'CountNew').textContent=nc;
    document.getElementById(type+'CountChanged').textContent=cc;
    document.getElementById(type+'CountSame').textContent=sc;
    updateCount(type);
}

function toggleAll(type) {
    const checked = document.getElementById(type+'SelectAll').checked;
    document.querySelectorAll('.'+type+'-cb').forEach(cb=>cb.checked=checked);
    updateCount(type);
}
function updateCount(type) {
    document.getElementById(type+'SelectedCount').textContent = document.querySelectorAll('.'+type+'-cb:checked').length;
}

async function applyData(type) {
    const selected = [];
    document.querySelectorAll('.'+type+'-cb:checked').forEach(cb=>selected.push(syncStore[type][cb.dataset.idx]));
    if (!selected.length) { alert('Pilih minimal 1 data.'); return; }
    const btn = document.getElementById('btnApply' + (type==='siswa'?'Siswa':'Guru'));
    btn.disabled=true; btn.innerHTML='<div class="spinner-border spinner-border-sm me-2"></div>Menyimpan...';
    const endpoint = type==='siswa' ? '/admin/dapodik/apply' : '/admin/dapodik/apply-guru';
    try {
        const res = await fetch(endpoint, {
            method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify({ data: selected })
        });
        const result = await res.json();
        if (result.success) {
            document.getElementById('step3Card').style.display='block';
            document.getElementById('resultDesc').textContent = `${result.inserted||0} data baru ditambahkan, ${result.updated||0} data diperbarui.`;
        } else alert('Error: '+(result.message||'Gagal'));
    } catch(e) { alert('Gagal: '+e.message); }
    finally { btn.disabled=false; btn.innerHTML=`<i class="bi bi-check-lg me-1"></i>Terapkan Perubahan`; }
}

function show(id){document.getElementById(id).style.display='block';}
function hide(id){document.getElementById(id).style.display='none';}
function updateProg(type,pct,text){
    document.getElementById(type+'ProgressBar').style.width=pct+'%';
    document.getElementById(type+'ProgressText').textContent=text;
}
</script>
<?= $this->endSection() ?>
