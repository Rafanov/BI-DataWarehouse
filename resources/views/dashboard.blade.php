@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    /* ── KPI GRID ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .kpi-card {
        background: rgba(10,30,60,0.88);
        border: 1px solid rgba(56,189,248,0.14);
        border-radius: 14px;
        padding: 16px 18px;
        position: relative; overflow: hidden;
        backdrop-filter: blur(16px);
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(6,182,212,0.15); }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
    .kpi-card.b::before { background: linear-gradient(90deg,#0891b2,#06b6d4); }
    .kpi-card.g::before { background: linear-gradient(90deg,#10b981,#34d399); }
    .kpi-card.o::before { background: linear-gradient(90deg,#f59e0b,#fbbf24); }
    .kpi-card.r::before { background: linear-gradient(90deg,#ef4444,#f87171); }
    .kpi-card.p::before { background: linear-gradient(90deg,#8b5cf6,#a78bfa); }
    .kpi-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--text-muted); margin-bottom:7px; }
    .kpi-value { font-size:26px; font-weight:700; font-family:'JetBrains Mono',monospace; letter-spacing:-1px; color:var(--text-primary); line-height:1; }
    .kpi-sub   { font-size:11px; color:var(--text-muted); margin-top:5px; }
    .kpi-icon  { position:absolute; top:14px; right:14px; width:22px; height:22px; opacity:0.22; }

    /* ── TICKER ── */
    .ticker {
        background: rgba(8,145,178,0.10);
        border: 1px solid rgba(56,189,248,0.14);
        border-radius: 9px; padding: 7px 14px;
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 16px; overflow: hidden;
    }
    .tick-live { font-size:9px; font-weight:800; letter-spacing:1.2px; color:#38bdf8; text-transform:uppercase; flex-shrink:0; }
    .tick-wrap { flex:1; overflow:hidden; }
    .tick-inner { display:flex; gap:30px; white-space:nowrap; animation:tickScroll 22s linear infinite; width:max-content; }
    .tick-item  { font-size:12px; font-family:'JetBrains Mono',monospace; color:var(--text-secondary); }
    .tick-item .up { color:#34d399; margin-left:4px; }
    @keyframes tickScroll { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

    /* ── CHART ROWS ── */
    .chart-row { display:grid; gap:12px; margin-bottom:12px; }
    .chart-row.r21 { grid-template-columns:2fr 1fr; }
    .chart-row.r3  { grid-template-columns:1fr 1fr 1fr; }
    .chart-row.r31 { grid-template-columns:3fr 1fr; }

    /* ── CHART BOX ── */
    .cbox {
        background: rgba(10,30,60,0.88);
        border: 1px solid rgba(56,189,248,0.13);
        border-radius: 14px; overflow: hidden;
        backdrop-filter: blur(16px);
    }
    .cbox-head {
        padding: 13px 18px;
        border-bottom: 1px solid rgba(56,189,248,0.07);
        display: flex; align-items: center; justify-content: space-between;
    }
    .cbox-title { font-size:13px; font-weight:600; color:var(--text-primary); }
    .cbox-sub   { font-size:11px; color:var(--text-muted); margin-top:2px; }
    .cbox-tag {
        font-size:9px; font-weight:700; padding:2px 8px; border-radius:20px;
        background: rgba(56,189,248,0.12); color:#38bdf8; white-space:nowrap;
    }
    .cbox-body { padding:16px; }

    /* ── GLOBE (Three.js) ── */
    .globe-container {
        position:relative; background:#000814;
        border-radius:0 0 14px 14px; overflow:hidden;
    }
    #dash-globe-canvas { display:block; width:100%!important; height:100%!important; cursor:grab; }
    #dash-globe-canvas:active { cursor:grabbing; }
    .globe-hint {
        position:absolute; bottom:10px; left:50%; transform:translateX(-50%);
        background:rgba(10,30,60,0.85); border:1px solid rgba(56,189,248,0.15);
        border-radius:6px; padding:4px 12px;
        font-size:11px; color:var(--text-muted);
        backdrop-filter:blur(10px); white-space:nowrap; z-index:10;
        pointer-events:none;
    }
    .globe-overlay {
        position:absolute; bottom:10px; left:10px; right:10px;
        display:flex; justify-content:space-between; align-items:flex-end;
        pointer-events:none; z-index:10;
    }
    .globe-legend {
        background:rgba(10,30,60,0.85); border:1px solid rgba(56,189,248,0.15);
        border-radius:9px; padding:9px 11px;
    }
    .g-legend-title { font-size:9px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px; }
    .g-legend-row   { display:flex; align-items:center; gap:6px; margin-bottom:3px; }
    .g-legend-dot   { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .g-legend-lbl   { font-size:10px; color:var(--text-secondary); }

    /* ── BAR CHART ── */
    .bar-row    { display:flex; align-items:center; gap:8px; margin-bottom:7px; }
    .bar-label  { font-size:11px; color:var(--text-secondary); width:84px; text-align:right; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex-shrink:0; }
    .bar-track  { flex:1; height:14px; background:rgba(56,189,248,0.07); border-radius:4px; overflow:hidden; }
    .bar-fill   { height:100%; border-radius:4px; transition:width 1.2s cubic-bezier(.22,1,.36,1); }
    .bar-val    { font-size:11px; color:var(--text-muted); width:40px; text-align:right; font-family:'JetBrains Mono',monospace; flex-shrink:0; }

    /* ── DONUT ── */
    .donut-wrap  { display:flex; align-items:center; gap:16px; }
    .donut-items { flex:1; }
    .donut-row   { display:flex; align-items:center; gap:7px; margin-bottom:9px; }
    .donut-dot   { width:9px; height:9px; border-radius:3px; flex-shrink:0; }
    .donut-lbl   { font-size:12px; color:var(--text-secondary); flex:1; }
    .donut-pct   { font-size:12px; color:var(--text-muted); font-family:'JetBrains Mono',monospace; }

    /* ── INSIGHT CARDS ── */
    .insight-row { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:14px; }
    .insight-card {
        background: rgba(10,30,60,0.88);
        border: 1px solid rgba(56,189,248,0.13);
        border-radius: 12px; padding: 14px 16px;
        display:flex; align-items:flex-start; gap:12px;
        backdrop-filter: blur(16px);
    }
    .ins-icon { width:22px; height:22px; flex-shrink:0; margin-top:2px; }
    .ins-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); margin-bottom:3px; }
    .ins-val   { font-size:14px; font-weight:600; color:var(--text-primary); line-height:1.3; }
    .ins-sub   { font-size:10px; color:var(--text-muted); margin-top:2px; }

    /* ── TABLE ── */
    .tbl-scroll { overflow-x:auto; }
    .tbl-scroll table { width:100%; border-collapse:collapse; font-size:13px; }
    .tbl-scroll thead th {
        padding:9px 14px; text-align:left;
        font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;
        color:var(--text-muted); background:rgba(56,189,248,0.04);
        border-bottom:1px solid rgba(56,189,248,0.09);
    }
    .tbl-scroll tbody td {
        padding:11px 14px; border-bottom:1px solid rgba(56,189,248,0.06);
        color:var(--text-secondary); font-family:'JetBrains Mono',monospace; font-size:12px;
    }
    .tbl-scroll tbody tr:last-child td { border-bottom:none; }
    .tbl-scroll tbody tr:hover td { background:rgba(56,189,248,0.04); }
    .tbadge { display:inline-block; padding:2px 7px; border-radius:4px; font-size:10px; font-weight:700; }
    .tbadge-r { background:rgba(239,68,68,0.18); color:#f87171; }
    .tbadge-o { background:rgba(245,158,11,0.18); color:#fbbf24; }
    .tbadge-g { background:rgba(16,185,129,0.18); color:#34d399; }

    /* ── DATASET PILLS ── */
    .ds-pills { display:flex; flex-wrap:wrap; gap:7px; margin-bottom:18px; }
    .ds-pill  {
        display:inline-flex; align-items:center; gap:5px;
        padding:4px 11px; border-radius:100px; font-size:12px; font-weight:500;
        background:rgba(10,30,60,0.88); border:1px solid rgba(56,189,248,0.14);
        color:var(--text-secondary);
    }
    .ds-pill .dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }

    /* ── EMPTY STATE ── */
    .empty-state { text-align:center; padding:70px 20px; }
    .empty-icon  { width:52px; height:52px; margin:0 auto 14px; opacity:0.4; }
    .empty-title { font-size:18px; font-weight:600; color:var(--text-primary); margin-bottom:8px; }
    .empty-sub   { font-size:14px; color:var(--text-muted); }

    /* Skeleton loader */
    .skel {
        background: linear-gradient(90deg,rgba(56,189,248,0.06) 25%,rgba(56,189,248,0.10) 50%,rgba(56,189,248,0.06) 75%);
        background-size:200% 100%;
        animation:shimmer 1.6s infinite;
        border-radius:10px;
    }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
</style>
@endpush

@section('content')

{{-- TICKER --}}
<div class="ticker" id="ticker-bar" style="display:none;">
    <div class="tick-live">● LIVE</div>
    <div class="tick-wrap">
        <div class="tick-inner" id="ticker-items"></div>
    </div>
</div>

{{-- KPI CARDS --}}
<div class="kpi-grid">
    <div class="kpi-card b">
        <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#38bdf8" stroke-width="1.5"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg></div>
        <div class="kpi-label">Total Datasets</div>
        <div class="kpi-value">{{ $totalDatasets }}</div>
        <div class="kpi-sub">Semua upload</div>
    </div>
    <div class="kpi-card g">
        <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg></div>
        <div class="kpi-label">Total Rows</div>
        <div class="kpi-value">{{ number_format($totalRows) }}</div>
        <div class="kpi-sub">Records indexed</div>
    </div>
    <div class="kpi-card o">
        <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="1.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></div>
        <div class="kpi-label">Total Columns</div>
        <div class="kpi-value">{{ number_format($totalCols) }}</div>
        <div class="kpi-sub">Fields tracked</div>
    </div>
    <div class="kpi-card r">
        <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="1.5"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></div>
        <div class="kpi-label">Recent Datasets</div>
        <div class="kpi-value">{{ $recentDatasets->count() }}</div>
        <div class="kpi-sub">Dalam 5 upload terakhir</div>
    </div>
    <div class="kpi-card p">
        <div class="kpi-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#a78bfa" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <div class="kpi-label">Active User</div>
        <div class="kpi-value" style="font-size:18px;letter-spacing:0;">{{ Str::words(auth()->user()->name, 1, '') }}</div>
        <div class="kpi-sub">{{ auth()->user()->email }}</div>
    </div>
</div>

{{-- LOADING SKELETON --}}
<div id="loading-state">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;margin-bottom:12px;">
        <div class="skel" style="height:340px;"></div>
        <div class="skel" style="height:340px;"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
        <div class="skel" style="height:220px;"></div>
        <div class="skel" style="height:220px;"></div>
        <div class="skel" style="height:220px;"></div>
    </div>
</div>

{{-- EMPTY STATE --}}
<div id="empty-state" style="display:none;">
    <div class="cbox">
        <div class="empty-state">
            <div class="empty-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#38bdf8" stroke-width="1.5"><path d="M2 12c1.5-3 3.5-4 6-4s4 2 6.5 2 4-1.5 7.5-1.5"/><path d="M2 17c1.5-3 3.5-4 6-4s4 2 6.5 2 4-1.5 7.5-1.5"/><path d="M2 7c1.5-3 3.5-4 6-4s4 2 6.5 2 4-1.5 7.5-1.5"/></svg></div>
            <div class="empty-title">Laut Data Masih Kosong</div>
            <div class="empty-sub">Upload dataset CSV pertama kamu untuk melihat dashboard.</div>
            <a href="{{ route('datasets.create') }}" class="btn btn-primary" style="margin-top:20px;"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px"><polyline points="16 16 12 12 8 16"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path></svg>Upload Dataset</a>
        </div>
    </div>
</div>

{{-- DASHBOARD CONTENT --}}
<div id="dash-content" style="display:none;">

    {{-- Dataset Pills --}}
    <div class="ds-pills" id="ds-pills"></div>

    {{-- Insight Row --}}
    <div class="insight-row" id="insight-row"></div>

    {{-- ROW 1: Globe + Top chart --}}
    <div class="chart-row r21" style="margin-bottom:12px;">
        {{-- Globe --}}
        <div class="cbox">
            <div class="cbox-head">
                <div>
                    <div class="cbox-title" style="display:flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#38bdf8" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> Globe 3D — Data dari Dataset CSV</div>
                    <div class="cbox-sub">Titik = nilai kolom numerik · Drag untuk putar · Hover untuk detail</div>
                </div>
                <div class="cbox-tag">3D INTERACTIVE</div>
            </div>
            <div class="globe-container" id="dash-globe-mount" style="height:300px;">
                <canvas id="dash-globe-canvas"></canvas>
                <div class="globe-hint" id="globe-hint"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;margin-right:4px"><path d="M5 9l-3 3 3 3M9 5l3-3 3 3M15 19l-3 3-3-3M19 9l3 3-3 3M2 12h20M12 2v20"/></svg>Drag putar · Scroll zoom · Hover negara</div>
                <div class="globe-overlay">
                    <div class="globe-legend">
                        <div class="g-legend-title">Nilai Data</div>
                        <div class="g-legend-row"><div class="g-legend-dot" style="background:#DC2626;"></div><div class="g-legend-lbl">Tinggi</div></div>
                        <div class="g-legend-row"><div class="g-legend-dot" style="background:#D97706;"></div><div class="g-legend-lbl">Sedang</div></div>
                        <div class="g-legend-row"><div class="g-legend-dot" style="background:#16A34A;"></div><div class="g-legend-lbl">Rendah</div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 10 bar --}}
        <div class="cbox">
            <div class="cbox-head">
                <div>
                    <div class="cbox-title" id="top10-title">Top Kategori</div>
                    <div class="cbox-sub" id="top10-sub">dari dataset</div>
                </div>
                <div class="cbox-tag">RANK</div>
            </div>
            <div class="cbox-body" id="top10-bars"></div>
        </div>
    </div>

    {{-- ROW 2: 3 charts --}}
    <div class="chart-row r3" style="margin-bottom:12px;">
        <div class="cbox">
            <div class="cbox-head">
                <div>
                    <div class="cbox-title" id="c2-title">Chart 2</div>
                    <div class="cbox-sub" id="c2-sub"></div>
                </div>
                <div class="cbox-tag">TREND</div>
            </div>
            <div class="cbox-body" style="padding:12px 16px;">
                <canvas id="chart2" height="150"></canvas>
            </div>
        </div>
        <div class="cbox">
            <div class="cbox-head">
                <div>
                    <div class="cbox-title" id="c3-title">Chart 3</div>
                    <div class="cbox-sub" id="c3-sub"></div>
                </div>
                <div class="cbox-tag">DIST</div>
            </div>
            <div class="cbox-body">
                <div class="donut-wrap">
                    <canvas id="chart3" width="100" height="100" style="flex-shrink:0;"></canvas>
                    <div class="donut-items" id="donut-legend"></div>
                </div>
            </div>
        </div>
        <div class="cbox">
            <div class="cbox-head">
                <div>
                    <div class="cbox-title" id="c4-title">Chart 4</div>
                    <div class="cbox-sub" id="c4-sub"></div>
                </div>
                <div class="cbox-tag">BAR</div>
            </div>
            <div class="cbox-body" id="c4-bars"></div>
        </div>
    </div>

    {{-- Dataset registry table --}}
    <div class="cbox" style="margin-bottom:12px;">
        <div class="cbox-head">
            <div>
                <div class="cbox-title">Dataset Registry</div>
                <div class="cbox-sub">Semua dataset yang diupload</div>
            </div>
            <a href="{{ route('datasets.create') }}" class="btn btn-primary btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px"><polyline points="16 16 12 12 8 16"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path></svg>Upload</a>
        </div>
        <div class="tbl-scroll">
            <table>
                <thead>
                    <tr><th>#</th><th>Nama</th><th>File</th><th>Rows</th><th>Columns</th><th>Upload</th><th></th></tr>
                </thead>
                <tbody id="ds-table"></tbody>
            </table>
        </div>
    </div>

</div>{{-- /dash-content --}}
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
/* ═══════════════════════════════════════════
   PALETTE & HELPERS
═══════════════════════════════════════════ */
const PAL = ['#06b6d4','#10b981','#f59e0b','#ef4444','#8b5cf6','#38bdf8','#f97316','#84cc16','#ec4899','#14b8a6'];

function topN(records, xCol, yCol, n = 12) {
    const agg = {};
    records.forEach(r => {
        const k = String(r[xCol] ?? '?').substring(0, 24);
        const v = parseFloat(r[yCol]) || 0;
        agg[k] = (agg[k] || 0) + v;
    });
    const sorted = Object.entries(agg).sort((a, b) => b[1] - a[1]).slice(0, n);
    return { labels: sorted.map(e => e[0]), values: sorted.map(e => +e[1].toFixed(4)) };
}

function detectCols(records, headers) {
    const num = [], cat = [];
    headers.forEach(h => {
        const vals = records.slice(0, 30).map(r => r[h]).filter(v => v !== '' && v != null);
        const n = vals.filter(v => !isNaN(parseFloat(v)) && isFinite(v) && v !== '').length;
        (n > vals.length * 0.55 ? num : cat).push(h);
    });
    return { num, cat };
}

/* ═══════════════════════════════════════════
   GLOBE 3D
═══════════════════════════════════════════ */
function initGlobe(globeData) {
    // ── Three.js Globe (sama seperti MIS page) ──
    if (typeof THREE === 'undefined') {
        console.warn('Three.js belum loaded');
        return;
    }

    const mount  = document.getElementById('dash-globe-mount');
    const canvas = document.getElementById('dash-globe-canvas');
    if (!mount || !canvas) return;

    const W = mount.clientWidth, H = mount.clientHeight || 300;

    const scene  = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(45, W/H, 0.1, 100);
    camera.position.z = 2.4;

    const renderer = new THREE.WebGLRenderer({ canvas, antialias:true, alpha:true });
    renderer.setSize(W, H);
    renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
    renderer.setClearColor(0x000814, 1);

    // Stars
    const starPos = [];
    for (let i=0; i<5000; i++) starPos.push((Math.random()-.5)*80,(Math.random()-.5)*80,(Math.random()-.5)*80);
    const starGeo = new THREE.BufferGeometry();
    starGeo.setAttribute('position', new THREE.Float32BufferAttribute(starPos, 3));
    scene.add(new THREE.Points(starGeo, new THREE.PointsMaterial({color:0xffffff,size:0.05,sizeAttenuation:true})));

    // Earth procedural texture
    const tc = document.createElement('canvas');
    tc.width=1024; tc.height=512;
    const tx = tc.getContext('2d');
    const og = tx.createLinearGradient(0,0,0,512);
    og.addColorStop(0,'#0a2a5e'); og.addColorStop(.5,'#0c3d7a'); og.addColorStop(1,'#071e45');
    tx.fillStyle=og; tx.fillRect(0,0,1024,512);
    tx.fillStyle='#2d5a1b';
    [[190,165,80,90],[240,320,55,95],[490,145,60,60],[500,285,70,110],[680,165,165,90],[775,340,75,55]]
        .forEach(([cx,cy,rx,ry])=>{ tx.beginPath(); tx.ellipse(cx,cy,rx,ry,0,0,Math.PI*2); tx.fill(); });
    tx.fillStyle='#d4e8f0'; tx.beginPath(); tx.ellipse(512,480,320,40,0,0,Math.PI*2); tx.fill();
    tx.fillStyle='#b8d4e8'; tx.beginPath(); tx.ellipse(280,100,40,55,0,0,Math.PI*2); tx.fill();

    const earthMat = new THREE.MeshPhongMaterial({
        map: new THREE.CanvasTexture(tc),
        specular: new THREE.Color(0x1a3a6e), shininess:18,
        emissive: new THREE.Color(0x061428), emissiveIntensity:0.15,
    });

    // Try NASA texture
    new THREE.TextureLoader().load(
        'https://raw.githubusercontent.com/mrdoob/three.js/r128/examples/textures/planets/earth_atmos_2048.jpg',
        tex => { earthMat.map = tex; earthMat.needsUpdate = true; },
        undefined, ()=>{}
    );

    const earthMesh = new THREE.Mesh(new THREE.SphereGeometry(1,64,64), earthMat);
    scene.add(earthMesh);

    // Atmosphere
    scene.add(new THREE.Mesh(
        new THREE.SphereGeometry(1.02,32,32),
        new THREE.MeshPhongMaterial({color:0x4488ff,transparent:true,opacity:0.06,side:THREE.FrontSide})
    ));

    // Lighting
    scene.add(new THREE.AmbientLight(0x334466, 0.8));
    const sun = new THREE.DirectionalLight(0xffffff, 1.2);
    sun.position.set(5,3,5); scene.add(sun);
    const rim = new THREE.DirectionalLight(0x4488cc, 0.4);
    rim.position.set(-5,-2,-3); scene.add(rim);

    // Markers
    const COORDS_3D = {
        'China':[35,105],'Indonesia':[-5,120],'Philippines':[12,122],'Vietnam':[16,108],
        'Sri Lanka':[7,81],'Thailand':[15,101],'Egypt':[27,30],'Malaysia':[4,108],
        'Nigeria':[9,8],'Bangladesh':[24,90],'USA':[38,-97],'Brazil':[-15,-47],
        'India':[20,78],'UK':[54,-2],'Germany':[51,10],'Japan':[36,138],
        'Australia':[-27,133],'Mexico':[24,-102],'Argentina':[-34,-64],
        'South Africa':[-30,25],'Canada':[56,-106],'Russia':[60,90],
        'France':[46,2],'Italy':[42,12],'Spain':[40,-4],'Pakistan':[30,69],
        'Turkey':[39,35],'South Korea':[36,128],'Iran':[32,53],'Saudi Arabia':[24,45],
        'Colombia':[4,-73],'Peru':[-10,-76],'Chile':[-30,-71],'Algeria':[28,2],
        'Morocco':[32,-5],'Kenya':[1,38],'Ghana':[8,-1],'Ethiopia':[9,40],
        'Tanzania':[-6,35],'Myanmar':[17,96],'Cambodia':[12,105],'Nepal':[28,84],
        'New Zealand':[-41,174],'Poland':[52,20],'Ukraine':[49,31],'Romania':[46,25],
        'Netherlands':[52,5],'Belgium':[50,4],'Sweden':[60,15],'Norway':[60,8],
        'Finland':[64,26],'Denmark':[56,10],'Portugal':[39,-8],'Greece':[39,22],
        'Czech Republic':[50,15],'Hungary':[47,19],'Austria':[47,14],'Switzerland':[47,8],
    };

    function latLonVec3(lat,lon,r) {
        const phi=(90-lat)*Math.PI/180, theta=(lon+180)*Math.PI/180;
        return new THREE.Vector3(-r*Math.sin(phi)*Math.cos(theta), r*Math.cos(phi), r*Math.sin(phi)*Math.sin(theta));
    }

    const markerGroup = new THREE.Group();
    scene.add(markerGroup);

    if (globeData && globeData.length) {
        const mx = Math.max(...globeData.map(d=>d.value), 1);
        globeData.forEach(d=>{
            const coord = COORDS_3D[d.label]; if (!coord) return;
            const norm = d.value / mx;
            const size = 0.012 + norm*0.032;
            const r = norm > 0.7 ? 0.863 : norm > 0.4 ? 0.851 : 0.086;
            const g = norm > 0.7 ? 0.165 : norm > 0.4 ? 0.467 : 0.639;
            const b = norm > 0.7 ? 0.165 : norm > 0.4 ? 0.071 : 0.290;
            const col = new THREE.Color(r, g, b);
            const mat = new THREE.MeshPhongMaterial({color:col,emissive:col,emissiveIntensity:0.5,transparent:true,opacity:0.85});
            const mesh = new THREE.Mesh(new THREE.SphereGeometry(size,8,8), mat);
            mesh.position.copy(latLonVec3(coord[0], coord[1], 1.01));
            markerGroup.add(mesh);
            if (norm > 0.5) {
                const rm = new THREE.Mesh(new THREE.SphereGeometry(size*1.8,8,8),
                    new THREE.MeshPhongMaterial({color:col,transparent:true,opacity:0.2}));
                rm.position.copy(latLonVec3(coord[0],coord[1],1.01));
                markerGroup.add(rm);
            }
        });
    }

    // Mouse/touch controls
    let isDrag=false, autoRot=true, prevM={x:0,y:0};
    const onDown = e=>{ isDrag=true; autoRot=false; const c=e.touches?e.touches[0]:e; prevM={x:c.clientX,y:c.clientY}; };
    const onUp   = ()=>{ isDrag=false; setTimeout(()=>autoRot=true,2500); };
    const onMove = e=>{ if(!isDrag) return; const c=e.touches?e.touches[0]:e;
        const dx=c.clientX-prevM.x, dy=c.clientY-prevM.y;
        earthMesh.rotation.y+=dx*0.005; markerGroup.rotation.y+=dx*0.005;
        earthMesh.rotation.x+=dy*0.003; markerGroup.rotation.x+=dy*0.003;
        earthMesh.rotation.x=Math.max(-Math.PI/2.5,Math.min(Math.PI/2.5,earthMesh.rotation.x));
        markerGroup.rotation.x=earthMesh.rotation.x;
        prevM={x:c.clientX,y:c.clientY};
    };
    const onWheel=e=>{ camera.position.z=Math.max(1.5,Math.min(4.5,camera.position.z+e.deltaY*0.002)); };
    mount.addEventListener('mousedown',onDown);
    mount.addEventListener('touchstart',onDown,{passive:true});
    window.addEventListener('mouseup',onUp);
    window.addEventListener('touchend',onUp);
    window.addEventListener('mousemove',onMove);
    window.addEventListener('touchmove',onMove,{passive:true});
    mount.addEventListener('wheel',onWheel,{passive:true});

    // Resize
    new ResizeObserver(()=>{
        const W2=mount.clientWidth, H2=mount.clientHeight||300;
        camera.aspect=W2/H2; camera.updateProjectionMatrix();
        renderer.setSize(W2,H2);
    }).observe(mount);

    // Animate
    (function loop(){
        requestAnimationFrame(loop);
        if(autoRot){ earthMesh.rotation.y+=0.002; markerGroup.rotation.y+=0.002; }
        renderer.render(scene,camera);
    })();
}

/* ═══════════════════════════════════════════
   LINE CHART (Chart.js-free, plain canvas)
═══════════════════════════════════════════ */
function drawLineCanvas(canvasId, labels, values, label, color) {
    const c   = document.getElementById(canvasId);
    if (!c) return;
    const ctx = c.getContext('2d');
    const W   = c.offsetWidth || 260;
    const H   = parseInt(c.getAttribute('height')) || 150;
    c.width = W; c.height = H;
    const pad = { l: 36, r: 12, t: 10, b: 22 };
    const maxV = Math.max(...values, 1);
    const minV = Math.min(...values, 0);
    const range = maxV - minV || 1;

    function px(i) { return pad.l + (i / (values.length - 1 || 1)) * (W - pad.l - pad.r); }
    function py(v) { return pad.t + (1 - (v - minV) / range) * (H - pad.t - pad.b); }

    ctx.strokeStyle = 'rgba(56,189,248,0.08)'; ctx.lineWidth = 0.5;
    [0, 0.25, 0.5, 0.75, 1].forEach(f => {
        const y = pad.t + (1 - f) * (H - pad.t - pad.b);
        ctx.beginPath(); ctx.moveTo(pad.l, y); ctx.lineTo(W - pad.r, y); ctx.stroke();
        const val = minV + f * range;
        ctx.fillStyle = '#5ba8be'; ctx.font = '8px JetBrains Mono'; ctx.textAlign = 'right';
        ctx.fillText(val.toFixed(val < 10 ? 1 : 0), pad.l - 3, y + 3);
    });

    const pts = values.map((v, i) => ({ x: px(i), y: py(v) }));
    ctx.beginPath(); ctx.moveTo(pts[0].x, H - pad.b);
    pts.forEach(p => ctx.lineTo(p.x, p.y));
    ctx.lineTo(pts[pts.length - 1].x, H - pad.b); ctx.closePath();
    ctx.fillStyle = color + '18'; ctx.fill();

    ctx.beginPath(); pts.forEach((p, i) => i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y));
    ctx.strokeStyle = color; ctx.lineWidth = 1.8; ctx.stroke();

    // x-labels (every ~4)
    ctx.fillStyle = '#5ba8be'; ctx.font = '8px JetBrains Mono'; ctx.textAlign = 'center';
    const step = Math.max(1, Math.floor(labels.length / 6));
    labels.forEach((l, i) => {
        if (i % step === 0) ctx.fillText(String(l).substring(0, 8), px(i), H - pad.b + 13);
    });
}

/* ═══════════════════════════════════════════
   DONUT CHART
═══════════════════════════════════════════ */
function drawDonut(canvasId, legendId, data) {
    const c = document.getElementById(canvasId);
    if (!c) return;
    const ctx = c.getContext('2d');
    const CX = 50, CY = 50, R = 40, RI = 24;
    const total = data.reduce((s, d) => s + d.v, 0);
    let a = -Math.PI / 2;
    data.forEach((d, i) => {
        const slice = (d.v / total) * Math.PI * 2;
        ctx.beginPath(); ctx.moveTo(CX, CY);
        ctx.arc(CX, CY, R, a, a + slice); ctx.closePath();
        ctx.fillStyle = PAL[i % PAL.length]; ctx.fill();
        a += slice;
    });
    ctx.beginPath(); ctx.arc(CX, CY, RI, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(10,30,60,0.92)'; ctx.fill();

    const el = document.getElementById(legendId);
    if (el) {
        el.innerHTML = data.map((d, i) =>
            `<div class="donut-row">
                <div class="donut-dot" style="background:${PAL[i%PAL.length]};"></div>
                <div class="donut-lbl">${d.label.substring(0,16)}</div>
                <div class="donut-pct">${((d.v/total)*100).toFixed(1)}%</div>
            </div>`
        ).join('');
    }
}

/* ═══════════════════════════════════════════
   BAR ROWS
═══════════════════════════════════════════ */
function renderBars(containerId, labels, values, color) {
    const el = document.getElementById(containerId);
    if (!el) return;
    const maxV = Math.max(...values, 1);
    el.innerHTML = labels.map((l, i) =>
        `<div class="bar-row">
            <div class="bar-label">${l}</div>
            <div class="bar-track">
                <div class="bar-fill" style="width:${(values[i]/maxV*100).toFixed(1)}%;background:${color||PAL[i%PAL.length]};"></div>
            </div>
            <div class="bar-val">${(+values[i].toFixed(3)).toLocaleString()}</div>
        </div>`
    ).join('');
}

/* ═══════════════════════════════════════════
   MAIN FETCH + RENDER
═══════════════════════════════════════════ */
async function buildDashboard() {
    const res  = await fetch('{{ route("dashboard.chart-data") }}');
    const data = await res.json();

    document.getElementById('loading-state').style.display = 'none';

    if (!data.length) {
        document.getElementById('empty-state').style.display = 'block';
        return;
    }

    document.getElementById('dash-content').style.display = 'block';
    document.getElementById('ticker-bar').style.display   = 'flex';

    const ds = data[0];
    const { num, cat } = detectCols(ds.records, ds.headers);

    /* Dataset pills */
    document.getElementById('ds-pills').innerHTML = data.map((d, i) =>
        `<div class="ds-pill"><span class="dot" style="background:${PAL[i%PAL.length]};"></span>${d.dataset_name}</div>`
    ).join('');

    /* Ticker */
    const tickHtml = data.map(d =>
        `<span class="tick-item">${d.dataset_name} <span class="up">▲ ${d.row_count.toLocaleString()} rows</span></span>`
    ).join('');
    document.getElementById('ticker-items').innerHTML = tickHtml + tickHtml;

    /* Table */
    document.getElementById('ds-table').innerHTML = data.map((d, i) =>
        `<tr>
            <td style="color:var(--text-muted);">${i+1}</td>
            <td style="font-family:'Space Grotesk',sans-serif;font-weight:500;color:var(--text-primary);">
                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:${PAL[i%PAL.length]};margin-right:8px;"></span>${d.dataset_name}
            </td>
            <td>${d.file_name}</td>
            <td>${d.row_count.toLocaleString()}</td>
            <td>${d.col_count}</td>
            <td>${d.uploaded_at}</td>
            <td><a href="/datasets/${d.dataset_id}" class="btn btn-secondary btn-sm">Lihat</a></td>
        </tr>`
    ).join('');

    if (!num.length || !cat.length) { initGlobe([]); return; }

    /* ── GLOBE data from CSV ── */
    const globeRaw = topN(ds.records, cat[0], num[0], 40);
    const globeData = globeRaw.labels.map((l, i) => ({ label: l, value: globeRaw.values[i] }));
    initGlobe(globeData);

    /* ── TOP 10 BARS ── */
    const t10 = topN(ds.records, cat[0], num[0], 10);
    document.getElementById('top10-title').textContent = `Top ${cat[0]}`;
    document.getElementById('top10-sub').textContent   = `nilai ${num[0]}`;
    renderBars('top10-bars', t10.labels, t10.values, null);

    /* ── INSIGHT ROW ── */
    const allVals0 = ds.records.map(r => parseFloat(r[num[0]]) || 0).filter(v => v > 0);
    const total0   = allVals0.reduce((a, b) => a + b, 0);
    const avg0     = allVals0.length ? (total0 / allVals0.length) : 0;
    const max0     = Math.max(...allVals0, 0);
    const topEntity = t10.labels[0] || '-';

    const numCol2 = num[1] || num[0];
    const vals2   = ds.records.map(r => parseFloat(r[numCol2]) || 0).filter(v => v > 0);
    const avg2    = vals2.length ? vals2.reduce((a, b) => a + b, 0) / vals2.length : 0;

    document.getElementById('insight-row').innerHTML = `
        <div class="insight-card">
            <div class="ins-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="1.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
            <div>
                <div class="ins-label">Top ${cat[0]}</div>
                <div class="ins-val">${topEntity}</div>
                <div class="ins-sub">${num[0]}: ${t10.values[0]?.toLocaleString() || '-'} (tertinggi)</div>
            </div>
        </div>
        <div class="insight-card">
            <div class="ins-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="1.5"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></div>
            <div>
                <div class="ins-label">Rata-rata ${num[0]}</div>
                <div class="ins-val">${avg0.toFixed(3)}</div>
                <div class="ins-sub">Max: ${max0.toFixed(3)} · ${allVals0.length} records</div>
            </div>
        </div>
        <div class="insight-card">
            <div class="ins-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#38bdf8" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg></div>
            <div>
                <div class="ins-label">Dataset Shape</div>
                <div class="ins-val">${ds.row_count.toLocaleString()} baris</div>
                <div class="ins-sub">${ds.headers.length} kolom · ${num.length} numerik · ${cat.length} kategorik</div>
            </div>
        </div>`;

    /* ── CHART 2: Line ── */
    const lineRecs = ds.records.slice(0, 24);
    const lineX    = lineRecs.map(r => String(r[cat[0]] || '').substring(0, 10));
    const lineY    = lineRecs.map(r => parseFloat(r[num[0]]) || 0);
    document.getElementById('c2-title').textContent = `${num[0]} trend`;
    document.getElementById('c2-sub').textContent   = `${ds.dataset_name}`;
    setTimeout(() => drawLineCanvas('chart2', lineX, lineY, num[0], '#06b6d4'), 80);

    /* ── CHART 3: Donut ── */
    const catCol2 = cat[1] || cat[0];
    const d3 = topN(ds.records, catCol2, num[0], 5);
    document.getElementById('c3-title').textContent = `Distribusi ${catCol2}`;
    document.getElementById('c3-sub').textContent   = `${num[0]}`;
    setTimeout(() => drawDonut('chart3', 'donut-legend',
        d3.labels.map((l, i) => ({ label: l, v: d3.values[i] }))
    ), 100);

    /* ── CHART 4: Bar num[2] ── */
    const numCol4 = num[2] || num[1] || num[0];
    const t4 = topN(ds.records, cat[0], numCol4, 8);
    document.getElementById('c4-title').textContent = `Top ${cat[0]}`;
    document.getElementById('c4-sub').textContent   = `nilai ${numCol4}`;
    setTimeout(() => renderBars('c4-bars', t4.labels, t4.values, '#10b981'), 60);
}

buildDashboard();
</script>
@endpush