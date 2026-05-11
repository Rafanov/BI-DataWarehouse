@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
    .kpi-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px 18px;
        position: relative;
        overflow: hidden;
    }
    .kpi-card::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; }
    .kpi-card.blue::before { background:var(--accent); }
    .kpi-card.green::before { background:#10b981; }
    .kpi-card.orange::before { background:#f59e0b; }
    .kpi-card.purple::before { background:#8b5cf6; }
    .kpi-label { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:var(--text-muted); margin-bottom:6px; }
    .kpi-value { font-size:26px; font-weight:700; font-family:'DM Mono',monospace; letter-spacing:-1px; color:var(--text-primary); line-height:1; }
    .kpi-sub { font-size:11px; color:var(--text-muted); margin-top:4px; }
    .kpi-icon { position:absolute; top:14px; right:14px; width:30px; height:30px; border-radius:7px; display:flex; align-items:center; justify-content:center; }

    .charts-row { display:grid; gap:12px; margin-bottom:12px; }
    .charts-row.cols-2 { grid-template-columns:1fr 1fr; }
    .charts-row.cols-3 { grid-template-columns:1fr 1fr 1fr; }
    .charts-row.cols-31 { grid-template-columns:2fr 1fr; }

    .chart-box { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:16px; box-shadow:var(--shadow-sm); }
    .chart-box-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; }
    .chart-box-title { font-size:12px; font-weight:600; color:var(--text-primary); }
    .chart-box-sub { font-size:10px; color:var(--text-muted); margin-top:2px; }
    .chart-tag { font-size:9px; font-weight:600; padding:2px 7px; border-radius:100px; background:var(--accent-light); color:var(--accent); white-space:nowrap; }

    .dataset-pill { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:100px; font-size:11px; font-weight:500; background:var(--bg); border:1px solid var(--border); color:var(--text-secondary); margin-right:5px; margin-bottom:5px; }
    .dataset-pill .dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }

    .skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#e8e8e8 50%,#f0f0f0 75%); background-size:200% 100%; animation:shimmer 1.5s infinite; border-radius:8px; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    .empty-state { text-align:center; padding:60px 20px; }
    .empty-state-icon { font-size:40px; margin-bottom:12px; }
    .empty-state-title { font-size:15px; font-weight:600; color:var(--text-primary); margin-bottom:6px; }
    .empty-state-sub { font-size:12px; color:var(--text-muted); }

    .ticker-bar { background:var(--text-primary); border-radius:var(--radius); padding:8px 16px; display:flex; align-items:center; gap:16px; margin-bottom:16px; overflow:hidden; }
    .ticker-label { font-size:9px; font-weight:700; letter-spacing:1px; color:var(--accent); text-transform:uppercase; white-space:nowrap; }
    .ticker-wrap { overflow:hidden; flex:1; }
    .ticker-items { display:flex; gap:32px; animation:ticker 25s linear infinite; white-space:nowrap; width:max-content; }
    .ticker-item { font-size:11px; font-family:'DM Mono',monospace; color:#e2e8f0; }
    .ticker-item span { color:#10b981; margin-left:5px; }
    @keyframes ticker { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

    /* Insight box */
    .insight-bar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 12px;
    }
    .insight-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .insight-icon { font-size: 22px; flex-shrink: 0; margin-top: 1px; }
    .insight-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 3px; }
    .insight-value { font-size: 13px; font-weight: 600; color: var(--text-primary); line-height: 1.3; }
    .insight-sub { font-size: 10px; color: var(--text-muted); margin-top: 2px; }
</style>
@endpush

@section('content')

{{-- Ticker bar --}}
<div class="ticker-bar" id="ticker-bar" style="display:none;">
    <div class="ticker-label">LIVE</div>
    <div class="ticker-wrap">
        <div class="ticker-items" id="ticker-items"></div>
    </div>
</div>
{{-- KPI Cards --}}
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-icon" style="background:#eff4ff;">
            <svg width="18" height="18" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"/><ellipse cx="12" cy="7" rx="8" ry="4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
        </div>
        <div class="kpi-label">Total Datasets</div>
        <div class="kpi-value">{{ $totalDatasets }}</div>
        <div class="kpi-sub">Across all uploads</div>
    </div>
    <div class="kpi-card green">
        <div class="kpi-icon" style="background:#ecfdf3;">
            <svg width="18" height="18" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="kpi-label">Total Rows</div>
        <div class="kpi-value">{{ number_format($totalRows) }}</div>
        <div class="kpi-sub">Records indexed</div>
    </div>
    <div class="kpi-card orange">
        <div class="kpi-icon" style="background:#fff7ed;">
            <svg width="18" height="18" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="kpi-label">Total Columns</div>
        <div class="kpi-value">{{ number_format($totalCols) }}</div>
        <div class="kpi-sub">Fields tracked</div>
    </div>
    <div class="kpi-card purple">
        <div class="kpi-icon" style="background:#f5f3ff;">
            <svg width="18" height="18" fill="none" stroke="#8b5cf6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div class="kpi-label">Active User</div>
        <div class="kpi-value">1</div>
        <div class="kpi-sub">{{ auth()->user()->name }}</div>
    </div>
</div>

{{-- Loading state --}}
<div id="loading-state">
    <div class="charts-row cols-2">
        <div class="skeleton" style="height:320px;"></div>
        <div class="skeleton" style="height:320px;"></div>
    </div>
    <div class="charts-row cols-3">
        <div class="skeleton" style="height:260px;"></div>
        <div class="skeleton" style="height:260px;"></div>
        <div class="skeleton" style="height:260px;"></div>
    </div>
</div>

{{-- Empty state --}}
<div id="empty-state" style="display:none;">
    <div class="chart-box empty-state">
        <div class="empty-state-icon">📊</div>
        <div class="empty-state-title">No Data Yet</div>
        <div class="empty-state-sub">Upload your first dataset to see the dashboard come alive.</div>
        <a href="{{ route('datasets.create') }}" class="btn btn-primary" style="margin-top:20px;">+ Upload Dataset</a>
    </div>
</div>

{{-- Dashboard content --}}
<div id="dashboard-content" style="display:none;">

    {{-- Dataset pills --}}
    <div style="margin-bottom:20px;" id="dataset-pills"></div>

    {{-- AI Insight bar (diisi JS) --}}
    <div class="insight-bar" id="insight-bar"></div>

    {{-- Row 1: Main chart + Doughnut --}}
    <div class="charts-row cols-31" id="row1"></div>

    {{-- Row 2: 3 charts --}}
    <div class="charts-row cols-3" id="row2"></div>

    {{-- Row 3: 3 charts (FIXED: cols-3 bukan cols-2) --}}
    <div class="charts-row cols-3" id="row3"></div>

    {{-- Dataset table --}}
    <div class="chart-box" style="margin-top:16px;">
        <div class="chart-box-header">
            <div>
                <div class="chart-box-title">Dataset Registry</div>
                <div class="chart-box-sub">All uploaded datasets</div>
            </div>
            <a href="{{ route('datasets.create') }}" class="btn btn-primary btn-sm">+ Upload</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Name</th><th>File</th><th>Rows</th><th>Columns</th><th>Uploaded</th><th></th></tr>
                </thead>
                <tbody id="datasets-table-body"></tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const PALETTE = ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#84cc16','#ec4899','#14b8a6'];
let chartInstances = {};

function destroyAll() { Object.values(chartInstances).forEach(c=>c.destroy()); chartInstances={}; }

function getChartOpts(type, color, extra={}) {
    const isPie = ['pie','doughnut'].includes(type);
    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 700, easing: 'easeInOutQuart' },
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                display: isPie,
                position: 'bottom',
                labels: { font:{size:9,family:'DM Sans'}, padding:8, boxWidth:8, usePointStyle:true }
            },
            tooltip: {
                backgroundColor: '#0f172a',
                titleColor: '#94a3b8',
                bodyColor: '#f1f5f9',
                titleFont: { size:10, family:'DM Sans', weight:'600' },
                bodyFont: { size:11, family:'DM Mono' },
                padding: 10, cornerRadius: 8,
                borderColor: '#1e293b', borderWidth: 1,
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: ${Number(ctx.parsed.y ?? ctx.parsed).toLocaleString()}`
                }
            }
        },
        scales: isPie ? {} : {
            x: {
                grid: { display:false },
                border: { display:false },
                ticks: { font:{size:9,family:'DM Sans'}, maxRotation:40, color:'#94a3b8', maxTicksLimit:10 }
            },
            y: {
                beginAtZero: true,
                grid: { color:'#f1f5f9' },
                border: { display:false },
                ticks: { font:{size:9,family:'DM Mono'}, color:'#94a3b8', maxTicksLimit:5 }
            }
        },
        onClick: (e, els, chart) => {
            if (!els.length) return;
            const idx = els[0].index;
            const meta = chart.getDatasetMeta(0);
            meta.data.forEach((el, i) => {
                el.options.backgroundColor = i === idx
                    ? (isPie ? PALETTE[i] : color)
                    : (isPie ? PALETTE[i]+'66' : color+'33');
            });
            chart.update('none');
            setTimeout(() => {
                meta.data.forEach((el, i) => {
                    el.options.backgroundColor = isPie ? PALETTE[i]+'cc' : color+'25';
                });
                chart.update('none');
            }, 1200);
        },
        ...extra
    };
}

function makeChart(id, type, labels, values, label, color, extra={}) {
    const ctx = document.getElementById(id);
    if (!ctx) return null;
    if (chartInstances[id]) { chartInstances[id].destroy(); }

    const isPie = ['pie','doughnut'].includes(type);
    const c = new Chart(ctx, {
        type,
        data: {
            labels,
            datasets: [{
                label,
                data: values,
                backgroundColor: isPie ? PALETTE.map(c=>c+'cc') : color+'25',
                borderColor: isPie ? PALETTE : color,
                borderWidth: isPie ? 1.5 : 1.5,
                borderRadius: type==='bar' ? 5 : 0,
                tension: 0.4,
                fill: extra.fill || false,
                pointRadius: type==='line' ? 3 : 0,
                pointHoverRadius: 5,
                pointBackgroundColor: color,
                hoverBorderWidth: 2,
            }]
        },
        options: getChartOpts(type, color, extra.opts||{})
    });
    chartInstances[id] = c;
    return c;
}

function box(id, title, sub, tag, h=200, types=['bar','line','pie']) {
    const toggleBtns = types.map(t =>
        `<button onclick="toggleChart('${id}','${t}',this)" class="toggle-btn ${t==='bar'||t==='line'?'active':''}" data-type="${t}" style="padding:2px 7px;font-size:9px;font-weight:600;border:1px solid var(--border);border-radius:4px;background:${types[0]===t?'var(--accent)':'white'};color:${types[0]===t?'white':'var(--text-muted)'};cursor:pointer;transition:all 0.15s;">${t.toUpperCase()}</button>`
    ).join('');
    return `
    <div class="chart-box" style="position:relative;">
        <div class="chart-box-header">
            <div>
                <div class="chart-box-title">${title}</div>
                <div class="chart-box-sub">${sub}</div>
            </div>
            <div style="display:flex;align-items:center;gap:4px;">
                ${tag ? `<span class="chart-tag">${tag}</span>` : ''}
                <div style="display:flex;gap:3px;margin-left:6px;" data-chart-id="${id}">${toggleBtns}</div>
            </div>
        </div>
        <div style="position:relative;height:${h}px;"><canvas id="${id}"></canvas></div>
    </div>`;
}

const chartDataStore = {};

function toggleChart(id, newType, btn) {
    const store = chartDataStore[id];
    if (!store) return;
    const container = btn.closest('[data-chart-id]');
    container.querySelectorAll('.toggle-btn').forEach(b => {
        const isActive = b.dataset.type === newType;
        b.style.background = isActive ? 'var(--accent)' : 'white';
        b.style.color = isActive ? 'white' : 'var(--text-muted)';
        b.style.borderColor = isActive ? 'var(--accent)' : 'var(--border)';
    });
    makeChart(id, newType, store.labels, store.values, store.label, store.color, store.extra);
}

function storeAndMake(id, type, labels, values, label, color, extra={}) {
    chartDataStore[id] = { labels, values, label, color, extra };
    makeChart(id, type, labels, values, label, color, extra);
}

function detectCols(records, headers) {
    const num = [], cat = [];
    headers.forEach(h => {
        const vals = records.slice(0,20).map(r=>r[h]).filter(v=>v!==''&&v!=null);
        const n = vals.filter(v=>!isNaN(parseFloat(v))&&isFinite(v)&&v!=='').length;
        if (n > vals.length*0.6) num.push(h); else cat.push(h);
    });
    return { num, cat };
}

function topN(records, xCol, yCol, n=10) {
    const agg = {};
    records.forEach(r => {
        const k = String(r[xCol]??'?').substring(0,22);
        const v = parseFloat(r[yCol])||0;
        if (v > 0) agg[k] = (agg[k]||0) + v;
    });
    const sorted = Object.entries(agg).sort((a,b)=>b[1]-a[1]).slice(0,n);
    return { labels: sorted.map(e=>e[0]), values: sorted.map(e=>Math.round(e[1]*10000)/10000) };
}

// topN tanpa skip zero — untuk kolom yang semua nilainya kecil
function topNAll(records, xCol, yCol, n=10) {
    const agg = {};
    records.forEach(r => {
        const k = String(r[xCol]??'?').substring(0,22);
        const v = parseFloat(r[yCol])||0;
        agg[k] = (agg[k]||0) + v;
    });
    const sorted = Object.entries(agg).sort((a,b)=>b[1]-a[1]).slice(0,n);
    return { labels: sorted.map(e=>e[0]), values: sorted.map(e=>Math.round(e[1]*10000)/10000) };
}

function observeCharts() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.chart-box').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(16px)';
        el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        observer.observe(el);
    });
}

// Generate insight cards dari data
function buildInsights(ds, num, cat) {
    const insightEl = document.getElementById('insight-bar');
    if (!insightEl || !num.length || !cat.length) return;

    // Insight 1: kolom numerik pertama — total & top entity
    const col0 = num[0];
    const t0 = topNAll(ds.records, cat[0], col0, 1);
    const total0 = ds.records.reduce((s,r)=>s+(parseFloat(r[col0])||0),0);
    const top0Label = t0.labels[0] || '-';
    const top0Val = t0.values[0] || 0;

    // Insight 2: kolom numerik ke-2 — rata-rata
    const col1 = num[1] || num[0];
    const vals1 = ds.records.map(r=>parseFloat(r[col1])||0).filter(v=>v>0);
    const avg1 = vals1.length ? (vals1.reduce((a,b)=>a+b,0)/vals1.length) : 0;
    const max1 = vals1.length ? Math.max(...vals1) : 0;

    // Insight 3: total records & kolom count
    const rowCount = ds.row_count || ds.records.length;
    const colCount = ds.headers.length;
    const numColCount = num.length;

    insightEl.innerHTML = `
        <div class="insight-card">
            <div class="insight-icon">🏆</div>
            <div>
                <div class="insight-label">Top ${cat[0]}</div>
                <div class="insight-value">${top0Label}</div>
                <div class="insight-sub">${col0}: ${Number(top0Val.toFixed(4)).toLocaleString()} (highest)</div>
            </div>
        </div>
        <div class="insight-card">
            <div class="insight-icon">📈</div>
            <div>
                <div class="insight-label">Avg ${col1}</div>
                <div class="insight-value">${avg1.toFixed(4)}</div>
                <div class="insight-sub">Max: ${max1.toFixed(4)} across ${vals1.length} records</div>
            </div>
        </div>
        <div class="insight-card">
            <div class="insight-icon">🗂️</div>
            <div>
                <div class="insight-label">Dataset Shape</div>
                <div class="insight-value">${Number(rowCount).toLocaleString()} rows</div>
                <div class="insight-sub">${colCount} columns · ${numColCount} numeric · ${cat.length} categorical</div>
            </div>
        </div>
    `;
}

async function buildDashboard() {
    const res = await fetch('{{ route("dashboard.chart-data") }}');
    const datasets = await res.json();

    document.getElementById('loading-state').style.display = 'none';

    if (!datasets.length) {
        document.getElementById('empty-state').style.display = 'block';
        return;
    }

    document.getElementById('dashboard-content').style.display = 'block';
    destroyAll();

    // Pills
    document.getElementById('dataset-pills').innerHTML = datasets.map((ds,i) =>
        `<span class="dataset-pill"><span class="dot" style="background:${PALETTE[i%PALETTE.length]}"></span>${ds.dataset_name}</span>`
    ).join('');

    // Ticker
    const tickerContent = datasets.map(ds =>
        `<span class="ticker-item">${ds.dataset_name} <span>▲ ${ds.row_count.toLocaleString()} rows</span></span>`
    ).join('');
    document.getElementById('ticker-items').innerHTML = tickerContent + tickerContent;
    document.getElementById('ticker-bar').style.display = 'flex';

    // Table
    document.getElementById('datasets-table-body').innerHTML = datasets.map((ds,i) => `
        <tr>
            <td style="color:var(--text-muted);font-size:11px;">${i+1}</td>
            <td style="font-family:'DM Sans';font-weight:500;color:var(--text-primary);">
                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:${PALETTE[i%PALETTE.length]};margin-right:7px;"></span>${ds.dataset_name}
            </td>
            <td>${ds.file_name}</td>
            <td>${ds.row_count.toLocaleString()}</td>
            <td>${ds.col_count}</td>
            <td>${ds.uploaded_at}</td>
            <td><a href="/data-warehouse/public/datasets/${ds.dataset_id}" class="btn btn-secondary btn-sm">View</a></td>
        </tr>
    `).join('');

    const row1 = document.getElementById('row1');
    const row2 = document.getElementById('row2');
    const row3 = document.getElementById('row3');
    row1.innerHTML=''; row2.innerHTML=''; row3.innerHTML='';

    const ds = datasets[0];
    const color0 = PALETTE[0];
    const { num, cat } = detectCols(ds.records, ds.headers);
    if (!num.length || !cat.length) return;

    // Insight cards
    buildInsights(ds, num, cat);

    // ROW 1: Main bar (wide) + doughnut
    const t1 = topNAll(ds.records, cat[0], num[0], 12);
    const tp1 = topNAll(ds.records, cat[0], num[0], 7);
    row1.innerHTML += box(`c-main`, ds.dataset_name, `${num[0]} by ${cat[0]} — Top 12`, 'OVERVIEW', 220, ['bar','line','doughnut']);
    row1.innerHTML += box(`c-pie`, `Distribution`, `${num[0]} top 7`, 'SHARE', 220, ['doughnut','pie','bar']);
    setTimeout(() => {
        storeAndMake(`c-main`, 'bar', t1.labels, t1.values, num[0], color0);
        storeAndMake(`c-pie`, 'doughnut', tp1.labels, tp1.values, num[0], PALETTE[1]);
    }, 50);

    // ROW 2: 3 chart dari kolom numerik berbeda
    // FIXED: pakai topNAll (tidak skip zero) dan tidak ada filter ketat
    const row2Cols = num.slice(0, 3); // langsung ambil 3 pertama, apapun isinya
    row2Cols.forEach((yCol, i) => {
        const colors2 = [PALETTE[2], PALETTE[3], PALETTE[4]];
        const types2 = [['line','bar','doughnut'], ['bar','line','pie'], ['line','bar','pie']];
        const t = topNAll(ds.records, cat[0], yCol, 12);
        row2.innerHTML += box(`c-r2-${i}`, ds.dataset_name, `${yCol} by ${cat[0]}`, yCol.substring(0,14).toUpperCase(), 180, types2[i]);
        setTimeout(() => storeAndMake(`c-r2-${i}`, types2[i][0], t.labels, t.values, yCol, colors2[i], { fill: i===0 }), 120+i*40);
    });

    // Fallback: kalau num < 3, isi sisa slot row2 dari kolom num berbeda dengan cat berbeda
    if (row2Cols.length < 3 && cat.length > 1) {
        for (let i = row2Cols.length; i < 3; i++) {
            const yCol = num[i % num.length];
            const xCol = cat[Math.min(i, cat.length-1)];
            const colors2 = [PALETTE[2], PALETTE[3], PALETTE[4]];
            const t = topNAll(ds.records, xCol, yCol, 12);
            row2.innerHTML += box(`c-r2-fb-${i}`, ds.dataset_name, `${yCol} by ${xCol}`, yCol.substring(0,14).toUpperCase(), 180, ['bar','line','pie']);
            setTimeout(() => storeAndMake(`c-r2-fb-${i}`, 'bar', t.labels, t.values, yCol, colors2[i%3]), 120+i*40);
        }
    }

    // ROW 3: 3 chart kombinasi
    const colors3 = [PALETTE[5], PALETTE[6], PALETTE[7]];

    // Chart 3-1: Pie dari kategori ke-2 (atau ke-1)
    const catCol2 = cat[1] || cat[0];
    const numCol2 = num[1] || num[0];
    const tp3 = topNAll(ds.records, catCol2, numCol2, 6);
    row3.innerHTML += box(`c-r3-0`, ds.dataset_name, `${numCol2} by ${catCol2}`, 'PIE', 180, ['pie','doughnut','bar']);
    setTimeout(() => storeAndMake(`c-r3-0`, 'pie', tp3.labels, tp3.values, numCol2, colors3[0]), 180);

    // Chart 3-2: Bar dari num ke-3
    const numCol3 = num[3] || num[2] || num[1] || num[0];
    const tb3 = topNAll(ds.records, cat[0], numCol3, 10);
    row3.innerHTML += box(`c-r3-1`, ds.dataset_name, `${numCol3} by ${cat[0]}`, 'BAR', 180, ['bar','line','doughnut']);
    setTimeout(() => storeAndMake(`c-r3-1`, 'bar', tb3.labels, tb3.values, numCol3, colors3[1]), 210);

    // Chart 3-3: Line trend dari num ke-4
    const numCol4 = num[4] || num[2] || num[0];
    const lineRecs = ds.records.slice(0, 20);
    const ll = lineRecs.map(r=>String(r[cat[0]]??'').substring(0,15));
    const lv = lineRecs.map(r=>parseFloat(r[numCol4])||0);
    row3.innerHTML += box(`c-r3-2`, ds.dataset_name, `${numCol4} trend`, 'LINE', 180, ['line','bar','pie']);
    setTimeout(() => storeAndMake(`c-r3-2`, 'line', ll, lv, numCol4, colors3[2], { fill:true }), 240);

    // Animasi scroll
    setTimeout(observeCharts, 300);
}

buildDashboard();
</script>
@endpush