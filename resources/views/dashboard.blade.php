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
    .kpi-icon  { position:absolute; top:14px; right:14px; font-size:20px; opacity:0.22; }

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

    /* ── GLOBE ── */
    .globe-container {
        position:relative; background:rgba(2,10,20,0.5);
        border-radius:0 0 14px 14px; overflow:hidden;
    }
    canvas#globe3d { display:block; width:100%; cursor:grab; }
    canvas#globe3d:active { cursor:grabbing; }
    .globe-hint {
        position:absolute; top:10px; left:50%; transform:translateX(-50%);
        background:rgba(10,30,60,0.82); border:1px solid rgba(56,189,248,0.15);
        border-radius:6px; padding:4px 12px;
        font-size:11px; color:var(--text-muted);
        backdrop-filter:blur(10px); white-space:nowrap; z-index:5;
        transition:opacity 0.3s;
    }
    .globe-overlay {
        position:absolute; bottom:10px; left:10px; right:10px;
        display:flex; justify-content:space-between; align-items:flex-end;
        pointer-events:none; z-index:5;
    }
    .globe-legend {
        background:rgba(10,30,60,0.82); border:1px solid rgba(56,189,248,0.15);
        border-radius:9px; padding:9px 11px; backdrop-filter:blur(10px);
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
    .ins-icon { font-size:20px; flex-shrink:0; margin-top:2px; }
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
    .empty-icon  { font-size:48px; margin-bottom:14px; }
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
        <div class="kpi-icon">🗄️</div>
        <div class="kpi-label">Total Datasets</div>
        <div class="kpi-value">{{ $totalDatasets }}</div>
        <div class="kpi-sub">Semua upload</div>
    </div>
    <div class="kpi-card g">
        <div class="kpi-icon">📋</div>
        <div class="kpi-label">Total Rows</div>
        <div class="kpi-value">{{ number_format($totalRows) }}</div>
        <div class="kpi-sub">Records indexed</div>
    </div>
    <div class="kpi-card o">
        <div class="kpi-icon">📐</div>
        <div class="kpi-label">Total Columns</div>
        <div class="kpi-value">{{ number_format($totalCols) }}</div>
        <div class="kpi-sub">Fields tracked</div>
    </div>
    <div class="kpi-card r">
        <div class="kpi-icon">📂</div>
        <div class="kpi-label">Recent Datasets</div>
        <div class="kpi-value">{{ $recentDatasets->count() }}</div>
        <div class="kpi-sub">Dalam 5 upload terakhir</div>
    </div>
    <div class="kpi-card p">
        <div class="kpi-icon">👤</div>
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
            <div class="empty-icon">🌊</div>
            <div class="empty-title">Laut Data Masih Kosong</div>
            <div class="empty-sub">Upload dataset CSV pertama kamu untuk melihat dashboard.</div>
            <a href="{{ route('datasets.create') }}" class="btn btn-primary" style="margin-top:20px;">⬆ Upload Dataset</a>
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
                    <div class="cbox-title">🌍 Globe 3D — Data dari Dataset CSV</div>
                    <div class="cbox-sub">Titik = nilai kolom numerik · Drag untuk putar · Hover untuk detail</div>
                </div>
                <div class="cbox-tag">3D INTERACTIVE</div>
            </div>
            <div class="globe-container">
                <canvas id="globe3d" style="height:300px;"></canvas>
                <div class="globe-hint" id="globe-hint">🖱️ Drag untuk memutar globe</div>
                <div class="globe-overlay">
                    <div class="globe-legend">
                        <div class="g-legend-title">Nilai Data</div>
                        <div class="g-legend-row"><div class="g-legend-dot" style="background:#ef4444;"></div><div class="g-legend-lbl">Tinggi</div></div>
                        <div class="g-legend-row"><div class="g-legend-dot" style="background:#f59e0b;"></div><div class="g-legend-lbl">Sedang</div></div>
                        <div class="g-legend-row"><div class="g-legend-dot" style="background:#10b981;"></div><div class="g-legend-lbl">Rendah</div></div>
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
            <a href="{{ route('datasets.create') }}" class="btn btn-primary btn-sm">⬆ Upload</a>
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
    const canvas = document.getElementById('globe3d');
    const ctx    = canvas.getContext('2d');
    const DPR    = Math.min(devicePixelRatio, 2);
    let W, H;

    function resize() {
        W = canvas.offsetWidth;
        H = canvas.offsetHeight;
        canvas.width  = W * DPR;
        canvas.height = H * DPR;
        ctx.scale(DPR, DPR);
    }
    resize();
    new ResizeObserver(resize).observe(canvas);

    let rotX = 0.25, rotY = 0.3;
    let drag = false, lastMX = 0, lastMY = 0;
    let auto = true;
    let hovered = null;

    /* Country lat/lon database */
    const COORDS = {
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

    /* Map first categorical column values to coords */
    let pointData = [];
    if (globeData && globeData.length) {
        const maxV = Math.max(...globeData.map(d => d.value));
        globeData.forEach(d => {
            const coords = COORDS[d.label];
            if (coords) {
                pointData.push({
                    label: d.label,
                    value: d.value,
                    lat: coords[0],
                    lon: coords[1],
                    norm: maxV > 0 ? d.value / maxV : 0
                });
            }
        });
    }
    /* fallback: use some sample world points */
    if (pointData.length === 0) {
        Object.entries(COORDS).slice(0, 20).forEach(([label, [lat, lon]], i) => {
            pointData.push({ label, value: Math.random() * 100, lat, lon, norm: Math.random() });
        });
    }

    function latLon3D(lat, lon, r) {
        const phi   = (90 - lat)  * Math.PI / 180;
        const theta = (lon + 180) * Math.PI / 180;
        return {
            x: -r * Math.sin(phi) * Math.cos(theta),
            y:  r * Math.cos(phi),
            z:  r * Math.sin(phi) * Math.sin(theta)
        };
    }
    function rotate(p, rx, ry) {
        let { x, y, z } = p;
        // Y-axis
        let x1 = x * Math.cos(ry) + z * Math.sin(ry);
        let z1 = -x * Math.sin(ry) + z * Math.cos(ry);
        // X-axis
        let y2 = y * Math.cos(rx) - z1 * Math.sin(rx);
        let z2 = y * Math.sin(rx) + z1 * Math.cos(rx);
        return { x: x1, y: y2, z: z2 };
    }
    function project(p, cx, cy, R) {
        const sc = (R * 1.6) / (R * 1.6 + p.z * 0.25);
        return { x: cx + p.x * sc, y: cy - p.y * sc, visible: p.z > -R * 0.08, z: p.z };
    }
    function normColor(n) {
        if (n > 0.7) return { fill: '#ef4444', glow: 'rgba(239,68,68,0.4)' };
        if (n > 0.4) return { fill: '#f59e0b', glow: 'rgba(245,158,11,0.3)' };
        return { fill: '#10b981', glow: 'rgba(16,185,129,0.3)' };
    }

    function draw() {
        ctx.clearRect(0, 0, W, H);
        const cx = W / 2, cy = H / 2;
        const R  = Math.min(W, H) * 0.42;

        /* Ocean gradient */
        const g = ctx.createRadialGradient(cx - R * 0.3, cy - R * 0.3, 0, cx, cy, R);
        g.addColorStop(0, '#0c4a6e');
        g.addColorStop(0.55, '#064e6e');
        g.addColorStop(1, '#021c2e');
        ctx.beginPath(); ctx.arc(cx, cy, R, 0, Math.PI * 2);
        ctx.fillStyle = g; ctx.fill();

        /* Grid */
        ctx.strokeStyle = 'rgba(56,189,248,0.09)'; ctx.lineWidth = 0.5;
        for (let lat = -75; lat <= 75; lat += 15) {
            ctx.beginPath(); let first = true;
            for (let lon = -180; lon <= 180; lon += 4) {
                const rp = rotate(latLon3D(lat, lon, R), rotX, rotY);
                const pp = project(rp, cx, cy, R);
                if (pp.visible) { first ? ctx.moveTo(pp.x, pp.y) : ctx.lineTo(pp.x, pp.y); first = false; }
                else first = true;
            }
            ctx.stroke();
        }
        for (let lon = -180; lon <= 180; lon += 30) {
            ctx.beginPath(); let first = true;
            for (let lat = -90; lat <= 90; lat += 4) {
                const rp = rotate(latLon3D(lat, lon, R), rotX, rotY);
                const pp = project(rp, cx, cy, R);
                if (pp.visible) { first ? ctx.moveTo(pp.x, pp.y) : ctx.lineTo(pp.x, pp.y); first = false; }
                else first = true;
            }
            ctx.stroke();
        }

        /* Data points — sorted back→front */
        const pts = pointData.map(d => {
            const rp = rotate(latLon3D(d.lat, d.lon, R), rotX, rotY);
            const pp = project(rp, cx, cy, R);
            return { ...d, pp };
        }).filter(d => d.pp.visible).sort((a, b) => a.pp.z - b.pp.z);

        pts.forEach(d => {
            const { fill, glow } = normColor(d.norm);
            const isH = d.label === hovered;
            const baseR = 4 + d.norm * 7;
            const r2 = isH ? baseR + 4 : baseR;
            const { x, y } = d.pp;

            if (isH) {
                ctx.beginPath(); ctx.arc(x, y, r2 + 9, 0, Math.PI * 2);
                ctx.strokeStyle = glow; ctx.lineWidth = 1; ctx.stroke();
            }
            ctx.beginPath(); ctx.arc(x, y, r2, 0, Math.PI * 2);
            ctx.fillStyle = isH ? fill : fill + 'cc'; ctx.fill();

            if (isH) {
                const lw = 120, lh = 32;
                const lx = x + 10 + lw > W ? x - lw - 10 : x + 10;
                const ly = y - 16;
                ctx.fillStyle = 'rgba(10,25,50,0.92)';
                ctx.beginPath();
                ctx.roundRect ? ctx.roundRect(lx, ly, lw, lh, 6) : ctx.rect(lx, ly, lw, lh);
                ctx.fill();
                ctx.strokeStyle = 'rgba(56,189,248,0.3)'; ctx.lineWidth = 0.5; ctx.stroke();
                ctx.fillStyle = '#e2f7ff'; ctx.font = 'bold 11px JetBrains Mono, monospace';
                ctx.textAlign = 'left';
                ctx.fillText(d.label, lx + 8, ly + 13);
                ctx.fillStyle = fill; ctx.font = '10px JetBrains Mono, monospace';
                ctx.fillText('val: ' + (+d.value.toFixed(3)).toLocaleString(), lx + 8, ly + 26);
            }
        });

        /* Highlight */
        const shine = ctx.createRadialGradient(cx - R * 0.32, cy - R * 0.32, 0, cx, cy, R);
        shine.addColorStop(0, 'rgba(255,255,255,0.07)');
        shine.addColorStop(0.5, 'rgba(255,255,255,0)');
        ctx.beginPath(); ctx.arc(cx, cy, R, 0, Math.PI * 2);
        ctx.fillStyle = shine; ctx.fill();

        /* Border */
        ctx.beginPath(); ctx.arc(cx, cy, R, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(6,182,212,0.3)'; ctx.lineWidth = 1.5; ctx.stroke();
    }

    canvas.addEventListener('mousedown', e => { drag = true; auto = false; lastMX = e.offsetX; lastMY = e.offsetY; });
    canvas.addEventListener('mousemove', e => {
        if (drag) {
            rotY += (e.offsetX - lastMX) * 0.008;
            rotX += (e.offsetY - lastMY) * 0.008;
            rotX = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, rotX));
            lastMX = e.offsetX; lastMY = e.offsetY;
        } else {
            const cx = W / 2, cy = H / 2, R = Math.min(W, H) * 0.42;
            hovered = null;
            pointData.forEach(d => {
                const rp = rotate(latLon3D(d.lat, d.lon, R), rotX, rotY);
                const pp = project(rp, cx, cy, R);
                if (!pp.visible) return;
                const dist = Math.hypot(e.offsetX - pp.x, e.offsetY - pp.y);
                if (dist < 14) hovered = d.label;
            });
            const hint = document.getElementById('globe-hint');
            hint.textContent = hovered
                ? `${hovered} · val: ${(pointData.find(d=>d.label===hovered)?.value||0).toFixed(2)}`
                : '🖱️ Drag untuk putar · Hover titik untuk detail';
        }
    });
    canvas.addEventListener('mouseup',    () => { drag = false; setTimeout(() => { auto = true; }, 2800); });
    canvas.addEventListener('mouseleave', () => { drag = false; hovered = null; });

    canvas.addEventListener('touchstart', e => { drag = true; auto = false; lastMX = e.touches[0].clientX; lastMY = e.touches[0].clientY; e.preventDefault(); }, { passive: false });
    canvas.addEventListener('touchmove', e => {
        if (!drag) return;
        rotY += (e.touches[0].clientX - lastMX) * 0.008;
        rotX += (e.touches[0].clientY - lastMY) * 0.008;
        rotX = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, rotX));
        lastMX = e.touches[0].clientX; lastMY = e.touches[0].clientY;
        e.preventDefault();
    }, { passive: false });
    canvas.addEventListener('touchend', () => { drag = false; setTimeout(() => { auto = true; }, 2800); });

    function loop() {
        if (auto) rotY += 0.003;
        draw();
        requestAnimationFrame(loop);
    }
    loop();
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
            <div class="ins-icon">🏆</div>
            <div>
                <div class="ins-label">Top ${cat[0]}</div>
                <div class="ins-val">${topEntity}</div>
                <div class="ins-sub">${num[0]}: ${t10.values[0]?.toLocaleString() || '-'} (tertinggi)</div>
            </div>
        </div>
        <div class="insight-card">
            <div class="ins-icon">📈</div>
            <div>
                <div class="ins-label">Rata-rata ${num[0]}</div>
                <div class="ins-val">${avg0.toFixed(3)}</div>
                <div class="ins-sub">Max: ${max0.toFixed(3)} · ${allVals0.length} records</div>
            </div>
        </div>
        <div class="insight-card">
            <div class="ins-icon">🗂️</div>
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