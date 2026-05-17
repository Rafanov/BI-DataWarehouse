@extends('layouts.app')
@section('title', 'DSS — Decision Support System')

@push('styles')
<style>
:root {
    --c-critical:#DC2626; --c-high:#D97706; --c-medium:#CA8A04; --c-low:#16A34A;
    --c-recycled:#16A34A; --c-incinerated:#2563EB; --c-mismanaged:#D97706; --c-landfilled:#6B7280;
}
.cc { background:rgba(10,30,60,0.88); border:1px solid rgba(56,189,248,0.13); border-radius:14px; backdrop-filter:blur(16px); overflow:hidden; }
.cc-head { padding:13px 18px; border-bottom:1px solid rgba(56,189,248,0.08); display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
.cc-title { font-size:13px; font-weight:600; color:var(--text-primary); }
.cc-sub   { font-size:11px; color:var(--text-muted); margin-top:2px; }
.cc-tag   { font-size:9px; font-weight:700; letter-spacing:0.8px; padding:2px 8px; border-radius:20px; background:rgba(56,189,248,0.12); color:#38bdf8; white-space:nowrap; }
.cc-body  { padding:16px 18px; }
.toggle-row { display:flex; gap:4px; flex-shrink:0; }
.tog { padding:4px 10px; border-radius:6px; font-size:10px; font-weight:600; border:1px solid rgba(56,189,248,0.2); background:transparent; color:var(--text-muted); cursor:pointer; transition:all 0.15s; font-family:'Space Grotesk',sans-serif; }
.tog.active,.tog:hover { background:rgba(56,189,248,0.15); color:#38bdf8; border-color:rgba(56,189,248,0.4); }

/* Risk Summary */
.risk-summary { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:16px; }
.risk-card { border-radius:12px; padding:14px 16px; border:1px solid; text-align:center; backdrop-filter:blur(12px); }
.risk-card.critical { background:rgba(220,38,38,0.1);  border-color:rgba(220,38,38,0.25); }
.risk-card.high     { background:rgba(217,119,6,0.1);  border-color:rgba(217,119,6,0.25); }
.risk-card.medium   { background:rgba(202,138,4,0.1);  border-color:rgba(202,138,4,0.25); }
.risk-card.low      { background:rgba(22,163,74,0.1);  border-color:rgba(22,163,74,0.25); }
.rck-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; margin-bottom:6px; }
.risk-card.critical .rck-label { color:#DC2626; }
.risk-card.high     .rck-label { color:#D97706; }
.risk-card.medium   .rck-label { color:#CA8A04; }
.risk-card.low      .rck-label { color:#16A34A; }
.rck-count { font-size:32px; font-weight:700; font-family:'JetBrains Mono',monospace; letter-spacing:-2px; }
.risk-card.critical .rck-count { color:#DC2626; }
.risk-card.high     .rck-count { color:#D97706; }
.risk-card.medium   .rck-count { color:#CA8A04; }
.risk-card.low      .rck-count { color:#16A34A; }
.rck-desc { font-size:10px; color:var(--text-muted); margin-top:4px; line-height:1.4; }

/* Skel */
.skel { background:linear-gradient(90deg,rgba(56,189,248,0.06) 25%,rgba(56,189,248,0.10) 50%,rgba(56,189,248,0.06) 75%); background-size:200% 100%; animation:shimmer 1.6s infinite; border-radius:6px; display:inline-block; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Layout */
.g2  { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
.g21 { display:grid; grid-template-columns:2fr 1fr; gap:12px; margin-bottom:12px; }
.g3  { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:12px; }

/* Bar rows */
.bar-row { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
.bar-lbl { font-size:11px; color:var(--text-secondary); width:96px; text-align:right; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex-shrink:0; }
.bar-track { flex:1; height:14px; background:rgba(56,189,248,0.07); border-radius:4px; overflow:hidden; }
.bar-fill  { height:100%; border-radius:4px; }
.bar-val   { font-size:11px; color:var(--text-muted); width:55px; text-align:right; font-family:'JetBrains Mono',monospace; flex-shrink:0; }

/* Legend */
.legend   { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
.leg-item { display:flex; align-items:center; gap:5px; font-size:11px; color:var(--text-secondary); }
.leg-dot  { width:10px; height:10px; border-radius:3px; flex-shrink:0; }

/* Radar selector */
.radar-selector { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px; }
.entity-btn { padding:3px 9px; border-radius:5px; font-size:10px; font-weight:600; border:1px solid rgba(56,189,248,0.18); background:transparent; color:var(--text-muted); cursor:pointer; font-family:'Space Grotesk',sans-serif; transition:all 0.15s; }
.entity-btn.sel { color:var(--sel-col); border-color:var(--sel-col); background:rgba(from var(--sel-col) r g b / 0.12); }

/* Priority table */
.prio-tbl { width:100%; border-collapse:collapse; font-size:12px; }
.prio-tbl thead th { padding:8px 12px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); background:rgba(56,189,248,0.04); border-bottom:1px solid rgba(56,189,248,0.09); white-space:nowrap; }
.prio-tbl tbody td { padding:10px 12px; border-bottom:1px solid rgba(56,189,248,0.06); color:var(--text-secondary); font-family:'JetBrains Mono',monospace; }
.prio-tbl tbody tr:last-child td { border-bottom:none; }
.prio-tbl tbody tr:hover td { background:rgba(56,189,248,0.04); }
.rb { display:inline-block; padding:2px 7px; border-radius:4px; font-size:10px; font-weight:700; }
.rb-cr { background:rgba(220,38,38,0.18); color:#f87171; }
.rb-hi { background:rgba(217,119,6,0.18);  color:#fbbf24; }
.rb-me { background:rgba(202,138,4,0.18);  color:#fcd34d; }
.rb-lo { background:rgba(22,163,74,0.18);   color:#34d399; }

/* Score bar inline */
.score-track { display:inline-block; width:80px; height:6px; background:rgba(56,189,248,0.12); border-radius:3px; vertical-align:middle; margin-right:6px; overflow:hidden; }
.score-fill  { height:100%; border-radius:3px; }
</style>
@endpush

@section('content')

{{-- Risk Summary Cards --}}
<div class="risk-summary" id="risk-summary">
    @foreach(['critical','high','medium','low'] as $c)
    <div class="risk-card {{$c}}">
        <div class="rck-label">{{ strtoupper($c) }}</div>
        <div class="rck-count"><span class="skel" style="height:32px;width:40px;">&nbsp;</span></div>
        <div class="rck-desc">Loading...</div>
    </div>
    @endforeach
</div>

{{-- ROW 1: DSS-03 Top 10 + DSS-01 Risk Dist --}}
<div class="g21">
    <div class="cc">
        <div class="cc-head">
            <div>
                <div class="cc-title" style="display:flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#38bdf8" stroke-width="1.8"><path d="M2 12c1.5-3 3.5-4 6-4s4 2 6.5 2 4-1.5 7.5-1.5"/><path d="M2 17c1.5-3 3.5-4 6-4s4 2 6.5 2 4-1.5 7.5-1.5"/><path d="M2 7c1.5-3 3.5-4 6-4s4 2 6.5 2 4-1.5 7.5-1.5"/></svg> Top 10 Kontributor Polusi Laut</div>
                <div class="cc-sub">Ocean pollution share per negara (2019)</div>
            </div>
            <div class="toggle-row" id="dss03-toggle">
                <button class="tog active" data-chart="hbar">H-Bar</button>
                <button class="tog" data-chart="treemap">Treemap</button>
                <button class="tog" data-chart="waterfall">Waterfall</button>
            </div>
        </div>
        <div class="cc-body">
            <canvas id="dss03-canvas" style="display:block;width:100%;" height="270"></canvas>
        </div>
    </div>

    <div class="cc">
        <div class="cc-head">
            <div>
                <div class="cc-title" style="display:flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="1.8"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Distribusi Kategori Risiko</div>
                <div class="cc-sub">Jumlah negara per level risiko</div>
            </div>
            <div class="toggle-row" id="dss01-toggle">
                <button class="tog active" data-chart="donut">Donut</button>
                <button class="tog" data-chart="pie">Pie</button>
                <button class="tog" data-chart="bar">Bar</button>
            </div>
        </div>
        <div class="cc-body">
            <canvas id="dss01-canvas" style="display:block;width:100%;" height="270"></canvas>
        </div>
    </div>
</div>

{{-- ROW 2: DSS-02 Multivariate + DSS-04 Profil --}}
<div class="g2">
    <div class="cc">
        <div class="cc-head">
            <div>
                <div class="cc-title" style="display:flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#38bdf8" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg> Analisis Multivariat Risk Score</div>
                <div class="cc-sub">Komposisi & perbandingan indikator risiko</div>
            </div>
            <div class="toggle-row" id="dss02-toggle">
                <button class="tog active" data-chart="grouped">Grouped Bar</button>
                <button class="tog" data-chart="decomp">Dekomposisi</button>
                <button class="tog" data-chart="line">Dual Line</button>
            </div>
        </div>
        <div class="cc-body">
            <div class="legend">
                <div class="leg-item"><div class="leg-dot" style="background:#38bdf8;"></div>Ocean Pollution</div>
                <div class="leg-item"><div class="leg-dot" style="background:#D97706;"></div>Mismanaged</div>
                <div class="leg-item"><div class="leg-dot" style="background:#16A34A;"></div>Recycled</div>
                <div class="leg-item"><div class="leg-dot" style="background:#8b5cf6;"></div>Risk Score</div>
            </div>
            <canvas id="dss02-canvas" style="display:block;width:100%;" height="230"></canvas>
        </div>
    </div>

    <div class="cc">
        <div class="cc-head">
            <div>
                <div class="cc-title" style="display:flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#38bdf8" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg> Profil Negara Prioritas</div>
                <div class="cc-sub">Perbandingan multi-indikator risiko</div>
            </div>
            <div class="toggle-row" id="dss04-toggle">
                <button class="tog active" data-chart="radar">Radar</button>
                <button class="tog" data-chart="hstack">Priority Matrix</button>
                <button class="tog" data-chart="grouped">Grouped Bar</button>
            </div>
        </div>
        <div class="cc-body">
            <div class="radar-selector" id="radar-selector"></div>
            <canvas id="dss04-canvas" style="display:block;width:100%;" height="210"></canvas>
        </div>
    </div>
</div>

{{-- Priority Table --}}
<div class="cc" style="margin-bottom:12px;">
    <div class="cc-head">
        <div>
            <div class="cc-title" style="display:flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#38bdf8" stroke-width="1.8"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg> Daftar Negara Prioritas Mitigasi</div>
            <div class="cc-sub">Berdasarkan DSS Risk Score — rekomendasi intervensi</div>
        </div>
        <div class="cc-tag">DSS OUTPUT</div>
    </div>
    <div style="overflow-x:auto;" id="prio-wrap">
        <div style="padding:28px;text-align:center;color:var(--text-muted);font-size:13px;">Memuat data...</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ───────────────────────────────
   BASE URL — gunakan window.location.origin supaya tidak hardcode
─────────────────────────────── */
const BASE = window.location.origin;
const API = {
    riskDist:    BASE + '/api/ocean/risk-dist',
    multivariate:BASE + '/api/ocean/multivariate',
    topOcean:    BASE + '/api/ocean/top-ocean',
    priority:    BASE + '/api/ocean/priority',
};

/* ───────────────────────────────
   CANVAS HELPER
─────────────────────────────── */
function initCanvas(id, h) {
    const c = document.getElementById(id);
    if (!c) return null;
    const dpr = Math.min(devicePixelRatio, 2);
    const W   = c.parentElement.clientWidth || 400;
    const H   = parseInt(c.getAttribute('height') || h || 200);
    c.width   = W * dpr;
    c.height  = H * dpr;
    const ctx = c.getContext('2d');
    ctx.scale(dpr, dpr);
    return {c, ctx, W, H};
}

const RCOL = {Critical:'#DC2626', High:'#D97706', Medium:'#CA8A04', Low:'#16A34A'};
const RCLS = {Critical:'rb-cr', High:'rb-hi', Medium:'rb-me', Low:'rb-lo'};
const RDESC= {Critical:'Intervensi darurat dibutuhkan', High:'Program mitigasi aktif', Medium:'Edukasi & pencegahan', Low:'Pemeliharaan sistem'};
function rc(cat){ return RCOL[cat]||'#38bdf8'; }
function rb(cat){ return `<span class="rb ${RCLS[cat]||''}">${cat||'—'}</span>`; }

const RADAR_COLORS = ['#06b6d4','#f59e0b','#10b981','#8b5cf6','#ef4444'];

/* ───────────────────────────────
   DSS-01 RISK DISTRIBUTION
─────────────────────────────── */
let dss01Data = [];
async function loadDss01() {
    try {
        const res = await fetch(API.riskDist);
        if (!res.ok) throw new Error(res.status);
        dss01Data = await res.json();

        // Update summary cards
        document.getElementById('risk-summary').innerHTML = dss01Data.map(d=>{
            const cls = d.risk_category.toLowerCase();
            return `<div class="risk-card ${cls}">
                <div class="rck-label">${d.risk_category}</div>
                <div class="rck-count">${d.country_count}</div>
                <div class="rck-desc">${RDESC[d.risk_category]||''}</div>
            </div>`;
        }).join('');

        renderDss01('donut');
    } catch(e) { console.error('DSS01:', e); }
}

function renderDss01(type) {
    const cv = initCanvas('dss01-canvas', 270);
    if (!cv || !dss01Data.length) return;
    const {ctx, W, H} = cv;
    ctx.clearRect(0,0,W,H);

    const data = dss01Data.map(d=>({ label:d.risk_category, v:parseInt(d.country_count), color:rc(d.risk_category) }));
    const total = data.reduce((s,d)=>s+d.v, 0) || 1;

    if (type==='donut'||type==='pie') {
        const cx=W/2, cy=H/2, R=Math.min(W,H)*0.36, RI=type==='pie'?0:R*0.56;
        let a=-Math.PI/2;
        data.forEach(d=>{
            if (d.v===0) return;
            const slice=(d.v/total)*Math.PI*2;
            ctx.beginPath(); ctx.moveTo(cx,cy);
            ctx.arc(cx,cy,R,a,a+slice); ctx.closePath();
            ctx.fillStyle=d.color; ctx.fill();
            ctx.strokeStyle='rgba(10,30,60,0.5)'; ctx.lineWidth=1.5; ctx.stroke();
            // Label
            const mid=a+slice/2;
            const lx=cx+Math.cos(mid)*(R+(slice>0.4?22:16));
            const ly=cy+Math.sin(mid)*(R+(slice>0.4?22:16));
            ctx.fillStyle='#e2f7ff'; ctx.font='bold 11px JetBrains Mono'; ctx.textAlign='center';
            ctx.fillText(d.label, lx, ly-5);
            ctx.fillStyle='#5ba8be'; ctx.font='10px JetBrains Mono';
            ctx.fillText(`${d.v} (${((d.v/total)*100).toFixed(1)}%)`, lx, ly+8);
            a+=slice;
        });
        if (type==='donut') {
            ctx.beginPath(); ctx.arc(cx,cy,RI,0,Math.PI*2);
            ctx.fillStyle='rgba(10,30,60,0.92)'; ctx.fill();
            ctx.fillStyle='#e2f7ff'; ctx.font='bold 16px JetBrains Mono'; ctx.textAlign='center';
            ctx.fillText(total, cx, cy+6);
            ctx.fillStyle='#5ba8be'; ctx.font='9px Space Grotesk';
            ctx.fillText('negara', cx, cy+20);
        }
    } else {
        const pad={l:80,r:20,t:20,b:36};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const mx=Math.max(...data.map(d=>d.v))||1;
        const bW=iW/data.length*0.6;
        [0,.25,.5,.75,1].forEach(f=>{
            const y=pad.t+(1-f)*iH;
            ctx.strokeStyle='rgba(56,189,248,0.07)'; ctx.lineWidth=0.5;
            ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(W-pad.r,y); ctx.stroke();
            ctx.fillStyle='#5ba8be'; ctx.font='9px JetBrains Mono'; ctx.textAlign='right';
            ctx.fillText(Math.round(mx*f), pad.l-4, y+3);
        });
        data.forEach((d,i)=>{
            const cx2=pad.l+(i+0.5)*iW/data.length;
            const bH=(d.v/mx)*iH;
            const g=ctx.createLinearGradient(0,pad.t+iH-bH,0,pad.t+iH);
            g.addColorStop(0,d.color+'dd'); g.addColorStop(1,d.color+'44');
            ctx.fillStyle=g; ctx.fillRect(cx2-bW/2,pad.t+iH-bH,bW,bH);
            ctx.fillStyle='#e2f7ff'; ctx.font='10px Space Grotesk'; ctx.textAlign='center';
            ctx.fillText(d.label, cx2, H-pad.b+14);
            ctx.fillStyle=d.color; ctx.font='bold 12px JetBrains Mono';
            ctx.fillText(d.v, cx2, pad.t+iH-bH-5);
        });
    }
}

/* ───────────────────────────────
   DSS-02 MULTIVARIATE
─────────────────────────────── */
let dss02Data = {};
async function loadDss02() {
    try {
        const res = await fetch(API.multivariate);
        if (!res.ok) throw new Error(res.status);
        dss02Data = await res.json();
        renderDss02('grouped');
    } catch(e) { console.error('DSS02:', e); }
}

function renderDss02(type) {
    const cv = initCanvas('dss02-canvas', 230);
    if (!cv) return;
    const {ctx, W, H} = cv;
    ctx.clearRect(0,0,W,H);

    const top10 = (dss02Data.top20||[]).slice(0,10);
    if (!top10.length) return;

    const pad = {l:96, r:16, t:12, b:52};
    const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;

    function xLabel(i) {
        const x=pad.l+(i+0.5)*iW/top10.length;
        ctx.fillStyle='#5ba8be'; ctx.font='8px Space Grotesk'; ctx.textAlign='center';
        ctx.save(); ctx.translate(x, H-pad.b+5); ctx.rotate(-Math.PI/4);
        ctx.fillText(top10[i].entity.length>10?top10[i].entity.slice(0,9)+'.':top10[i].entity, 0, 0);
        ctx.restore();
    }
    function gridY(mx) {
        ctx.strokeStyle='rgba(56,189,248,0.07)'; ctx.lineWidth=0.5;
        [0,.25,.5,.75,1].forEach(f=>{
            const y=pad.t+(1-f)*iH;
            ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(W-pad.r,y); ctx.stroke();
            ctx.fillStyle='#5ba8be'; ctx.font='9px JetBrains Mono'; ctx.textAlign='right';
            ctx.fillText((mx*f).toFixed(f===0?0:1), pad.l-3, y+3);
        });
    }

    if (type==='grouped') {
        const mx=Math.max(...top10.flatMap(r=>[
            parseFloat(r.ocean_pollution_share)||0,
            parseFloat(r.mismanaged_share)||0,
            parseFloat(r.recycled_share)||0,
        ]))||1;
        gridY(mx);
        const bW=Math.min(iW/top10.length/4, 11);
        top10.forEach((r,i)=>{
            const cx2=pad.l+(i+0.5)*iW/top10.length;
            [
                [parseFloat(r.ocean_pollution_share)||0,'#38bdf8'],
                [parseFloat(r.mismanaged_share)||0,'#D97706'],
                [parseFloat(r.recycled_share)||0,'#16A34A'],
            ].forEach(([val,col],ki)=>{
                const bx=cx2+(ki-1)*(bW+2);
                const bH=(val/mx)*iH;
                ctx.fillStyle=col+'cc';
                ctx.fillRect(bx-bW/2, pad.t+iH-bH, bW, bH);
            });
            xLabel(i);
        });
    } else if (type==='decomp') {
        // Stacked horizontal — proporsi komponen risk score
        const rowH=iH/top10.length;
        top10.forEach((r,i)=>{
            const score=parseFloat(r.risk_score)||0;
            const by=pad.t+i*rowH;
            const bh=Math.min(rowH*.65,16);
            const bY=by+(rowH-bh)/2;
            ctx.fillStyle='#94d5e8'; ctx.font='10px Space Grotesk'; ctx.textAlign='right';
            ctx.fillText(r.entity.length>13?r.entity.slice(0,12)+'…':r.entity, pad.l-4, bY+bh/2+3);
            const maxScore=Math.max(...top10.map(x=>parseFloat(x.risk_score)||0))||1;
            // Komposisi: 40% ocean, 30% mismanaged, 20% per kapita
            const parts=[
                {w:(score*0.4/maxScore)*iW, col:'#38bdf8', label:'Ocean 40%'},
                {w:(score*0.3/maxScore)*iW, col:'#D97706', label:'Managed 30%'},
                {w:(score*0.2/maxScore)*iW, col:'#ef4444', label:'PerKap 20%'},
            ];
            let cx2=pad.l;
            parts.forEach(p=>{ ctx.fillStyle=p.col+'cc'; ctx.fillRect(cx2,bY,p.w,bh); cx2+=p.w; });
            ctx.fillStyle='#5ba8be'; ctx.font='10px JetBrains Mono'; ctx.textAlign='left';
            ctx.fillText(score.toFixed(1), cx2+4, bY+bh/2+3);
        });
    } else {
        // Dual line: risk_score vs ocean_pollution_share
        const mx1=Math.max(...top10.map(r=>parseFloat(r.risk_score)||0))||1;
        const mx2=Math.max(...top10.map(r=>parseFloat(r.ocean_pollution_share)||0))||1;
        const pxFn=i=>pad.l+(i/(top10.length-1||1))*iW;
        const py1=v=>pad.t+(1-v/mx1)*iH;
        const py2=v=>pad.t+(1-v/mx2)*iH;
        gridY(mx1);

        // Area risk score
        ctx.beginPath();
        ctx.moveTo(pxFn(0), pad.t+iH);
        top10.forEach((r,i)=>ctx.lineTo(pxFn(i),py1(parseFloat(r.risk_score)||0)));
        ctx.lineTo(pxFn(top10.length-1),pad.t+iH);
        ctx.closePath();
        ctx.fillStyle='rgba(139,92,246,0.12)'; ctx.fill();

        // Risk score line
        ctx.beginPath();
        top10.forEach((r,i)=>i===0?ctx.moveTo(pxFn(i),py1(parseFloat(r.risk_score)||0)):ctx.lineTo(pxFn(i),py1(parseFloat(r.risk_score)||0)));
        ctx.strokeStyle='#8b5cf6'; ctx.lineWidth=2.2; ctx.stroke();

        // Ocean pollution dashed line
        ctx.beginPath();
        top10.forEach((r,i)=>i===0?ctx.moveTo(pxFn(i),py2(parseFloat(r.ocean_pollution_share)||0)):ctx.lineTo(pxFn(i),py2(parseFloat(r.ocean_pollution_share)||0)));
        ctx.strokeStyle='#38bdf8'; ctx.lineWidth=2; ctx.setLineDash([5,3]); ctx.stroke();
        ctx.setLineDash([]);

        top10.forEach((_,i)=>xLabel(i));
    }
}

/* ───────────────────────────────
   DSS-03 TOP 10 OCEAN POLLUTERS
─────────────────────────────── */
let dss03Data = [];
async function loadDss03() {
    try {
        const res = await fetch(API.topOcean);
        if (!res.ok) throw new Error(res.status);
        dss03Data = await res.json();
        renderDss03('hbar');
    } catch(e) { console.error('DSS03:', e); }
}

function renderDss03(type) {
    const cv = initCanvas('dss03-canvas', 270);
    if (!cv || !dss03Data.length) return;
    const {ctx, W, H} = cv;
    ctx.clearRect(0,0,W,H);

    if (type==='hbar') {
        const pad={l:108,r:72,t:8,b:8};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const mx=Math.max(...dss03Data.map(r=>parseFloat(r.ocean_pollution_share)));
        const rowH=iH/dss03Data.length;
        dss03Data.forEach((r,i)=>{
            const bh=Math.min(rowH*.62,16);
            const by=pad.t+i*rowH+(rowH-bh)/2;
            const bw=(parseFloat(r.ocean_pollution_share)/mx)*iW;
            const col=rc(r.risk_category);
            ctx.fillStyle='#94d5e8'; ctx.font='10px Space Grotesk'; ctx.textAlign='right';
            ctx.fillText(r.entity.length>14?r.entity.slice(0,13)+'…':r.entity, pad.l-5, by+bh/2+3);
            // Rank badge
            ctx.fillStyle='rgba(56,189,248,0.12)'; ctx.fillRect(pad.l-22,by,16,bh);
            ctx.fillStyle='#38bdf8'; ctx.font='8px JetBrains Mono'; ctx.textAlign='center';
            ctx.fillText(`#${i+1}`, pad.l-14, by+bh/2+3);
            // Bar
            const g=ctx.createLinearGradient(pad.l,0,pad.l+bw,0);
            g.addColorStop(0,col+'88'); g.addColorStop(1,col);
            ctx.fillStyle=g; ctx.fillRect(pad.l,by,bw,bh);
            // Value
            ctx.fillStyle='#5ba8be'; ctx.font='10px JetBrains Mono'; ctx.textAlign='left';
            ctx.fillText(`${parseFloat(r.ocean_pollution_share).toFixed(2)}%`, pad.l+bw+4, by+bh/2+3);
        });
    } else if (type==='treemap') {
        const data=[...dss03Data];
        const total=data.reduce((s,r)=>s+parseFloat(r.ocean_pollution_share),0)||1;
        // Row-based treemap — 3 baris
        const nRows=3;
        const perRow=Math.ceil(data.length/nRows);
        let y=0;
        for(let row=0;row<nRows;row++){
            const items=data.slice(row*perRow,(row+1)*perRow);
            if(!items.length) break;
            const rowTotal=items.reduce((s,r)=>s+parseFloat(r.ocean_pollution_share),0)||1;
            const rH=(rowTotal/total)*H;
            let x=0;
            items.forEach(r=>{
                const val=parseFloat(r.ocean_pollution_share);
                const rW=(val/rowTotal)*W;
                const col=rc(r.risk_category);
                ctx.fillStyle=col+'88';
                ctx.fillRect(x+1,y+1,rW-2,rH-2);
                ctx.strokeStyle='rgba(10,30,60,0.6)'; ctx.lineWidth=1;
                ctx.strokeRect(x+1,y+1,rW-2,rH-2);
                if(rW>50&&rH>22){
                    ctx.fillStyle='#e2f7ff'; ctx.font='bold 10px Space Grotesk'; ctx.textAlign='center';
                    ctx.fillText(r.entity.length>10?r.entity.slice(0,9):r.entity, x+rW/2,y+rH/2-4);
                    ctx.fillStyle='rgba(226,247,255,0.7)'; ctx.font='9px JetBrains Mono';
                    ctx.fillText(val.toFixed(1)+'%', x+rW/2, y+rH/2+8);
                }
                x+=rW;
            });
            y+=rH;
        }
    } else {
        // Waterfall
        const pad={l:108,r:50,t:22,b:32};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const totalW=dss03Data.reduce((s,r)=>s+parseFloat(r.ocean_pollution_share),0)||1;
        const bW=iW/dss03Data.length*0.68;
        ctx.strokeStyle='rgba(56,189,248,0.08)'; ctx.lineWidth=0.5;
        ctx.beginPath(); ctx.moveTo(pad.l,pad.t); ctx.lineTo(W-pad.r,pad.t); ctx.stroke();
        ctx.fillStyle='#5ba8be'; ctx.font='9px JetBrains Mono'; ctx.textAlign='right';
        ctx.fillText('100%',pad.l-3,pad.t+3);

        let cumul=0;
        dss03Data.forEach((r,i)=>{
            const val=parseFloat(r.ocean_pollution_share);
            const col=rc(r.risk_category);
            const cx2=pad.l+(i+0.5)*iW/dss03Data.length;
            const y1=pad.t+(1-(cumul+val)/totalW)*iH;
            const y0=pad.t+(1-cumul/totalW)*iH;
            ctx.fillStyle=col+'aa';
            ctx.fillRect(cx2-bW/2,y1,bW,y0-y1);
            // Connector
            if(i<dss03Data.length-1){
                const nx=pad.l+(i+1.5)*iW/dss03Data.length;
                ctx.strokeStyle='rgba(56,189,248,0.18)'; ctx.lineWidth=0.6; ctx.setLineDash([3,3]);
                ctx.beginPath(); ctx.moveTo(cx2+bW/2,y1); ctx.lineTo(nx-bW/2,y1); ctx.stroke();
                ctx.setLineDash([]);
            }
            ctx.fillStyle='#5ba8be'; ctx.font='8px Space Grotesk'; ctx.textAlign='center';
            ctx.fillText(r.entity.length>8?r.entity.slice(0,7)+'.':r.entity, cx2, H-pad.b+11);
            cumul+=val;
        });
    }
}

/* ───────────────────────────────
   DSS-04 PROFIL & PRIORITY
─────────────────────────────── */
let dss04Data = [];
let radarSel  = [];

async function loadDss04() {
    try {
        const res = await fetch(API.priority);
        if (!res.ok) throw new Error(res.status);
        dss04Data = await res.json();
        radarSel  = dss04Data.slice(0,2).map(r=>r.entity);
        buildRadarSelector();
        renderDss04('radar');
        buildPrioTable();
    } catch(e) { console.error('DSS04:', e); }
}

function buildRadarSelector() {
    const el = document.getElementById('radar-selector');
    el.innerHTML = dss04Data.slice(0,6).map((r,i)=>{
        const col = RADAR_COLORS[i%5];
        const sel = radarSel.includes(r.entity);
        return `<button class="entity-btn${sel?' sel':''}"
            style="--sel-col:${col}"
            onclick="toggleRadar(this,'${r.entity}',${i})">${r.entity}</button>`;
    }).join('');
}

window.toggleRadar = (btn, entity, idx) => {
    if (radarSel.includes(entity)) {
        if (radarSel.length<=1) return;
        radarSel = radarSel.filter(e=>e!==entity);
        btn.classList.remove('sel');
    } else {
        if (radarSel.length>=3) {
            const first=radarSel.shift();
            document.querySelector(`.entity-btn[onclick*="'${first}'"]`)?.classList.remove('sel');
        }
        radarSel.push(entity);
        btn.classList.add('sel');
    }
    renderDss04(document.querySelector('#dss04-toggle .tog.active')?.dataset.chart||'radar');
};

function renderDss04(type) {
    const cv = initCanvas('dss04-canvas', 210);
    if (!cv || !dss04Data.length) return;
    const {ctx, W, H} = cv;
    ctx.clearRect(0,0,W,H);

    if (type==='radar') {
        drawRadar(ctx, W, H);
    } else if (type==='hstack') {
        // Priority matrix — horizontal stacked by risk component
        const top8=dss04Data.slice(0,8);
        const pad={l:114,r:64,t:8,b:8};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const rowH=iH/top8.length;
        const maxScore=Math.max(...top8.map(r=>parseFloat(r.risk_score)||0))||1;
        top8.forEach((r,i)=>{
            const score=parseFloat(r.risk_score)||0;
            const bh=Math.min(rowH*.65,16);
            const by=pad.t+i*rowH+(rowH-bh)/2;
            ctx.fillStyle='#94d5e8'; ctx.font='10px Space Grotesk'; ctx.textAlign='right';
            ctx.fillText(r.entity.length>15?r.entity.slice(0,14)+'…':r.entity, pad.l-5, by+bh/2+3);
            const parts=[
                {w:(score*0.4/maxScore)*iW,col:'#38bdf8'},
                {w:(score*0.3/maxScore)*iW,col:'#D97706'},
                {w:(score*0.2/maxScore)*iW,col:'#ef4444'},
            ];
            let cx2=pad.l;
            parts.forEach(p=>{ ctx.fillStyle=p.col+'cc'; ctx.fillRect(cx2,by,p.w,bh); cx2+=p.w; });
            ctx.fillStyle='#5ba8be'; ctx.font='10px JetBrains Mono'; ctx.textAlign='left';
            ctx.fillText(score.toFixed(1), cx2+4, by+bh/2+3);
        });
    } else {
        // Grouped bar: recycled, mismanaged, landfilled per negara
        const top8=dss04Data.slice(0,8);
        const pad={l:16,r:16,t:10,b:52};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const keys=['recycled_share','mismanaged_share','landfilled_share'];
        const cols=['#16A34A','#D97706','#6B7280'];
        const mx=100;
        const gW=iW/top8.length;
        const bW=Math.min(gW/4,10);
        ctx.strokeStyle='rgba(56,189,248,0.07)'; ctx.lineWidth=0.5;
        [0,25,50,75,100].forEach(f=>{
            const y=pad.t+(1-f/100)*iH;
            ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(W-pad.r,y); ctx.stroke();
        });
        top8.forEach((r,i)=>{
            const cx2=pad.l+(i+0.5)*gW;
            keys.forEach((k,ki)=>{
                const val=parseFloat(r[k])||0;
                const bx=cx2+(ki-1)*(bW+2);
                const bH=(val/mx)*iH;
                ctx.fillStyle=cols[ki]+'cc';
                ctx.fillRect(bx-bW/2,pad.t+iH-bH,bW,bH);
            });
            ctx.fillStyle='#5ba8be'; ctx.font='8px Space Grotesk'; ctx.textAlign='center';
            ctx.save(); ctx.translate(cx2,H-pad.b+5); ctx.rotate(-Math.PI/4);
            ctx.fillText(r.entity.length>8?r.entity.slice(0,7)+'.':r.entity,0,0);
            ctx.restore();
        });
    }
}

function drawRadar(ctx, W, H) {
    const cx=W/2, cy=H/2+4, R=Math.min(W,H)*0.34;
    const axes=['Ocean Pollution','Mismanaged %','Mis/Kapita','Landfilled','Recycled (inv)'];
    const N=axes.length;

    // Grid circles
    [0.2,0.4,0.6,0.8,1].forEach(f=>{
        ctx.beginPath();
        for(let i=0;i<N;i++){
            const a=i*2*Math.PI/N-Math.PI/2;
            const x=cx+Math.cos(a)*R*f, y=cy+Math.sin(a)*R*f;
            i===0?ctx.moveTo(x,y):ctx.lineTo(x,y);
        }
        ctx.closePath();
        ctx.strokeStyle=`rgba(56,189,248,${0.06+f*0.06})`; ctx.lineWidth=0.8; ctx.stroke();
    });
    // Axes
    for(let i=0;i<N;i++){
        const a=i*2*Math.PI/N-Math.PI/2;
        ctx.beginPath(); ctx.moveTo(cx,cy);
        ctx.lineTo(cx+Math.cos(a)*R, cy+Math.sin(a)*R);
        ctx.strokeStyle='rgba(56,189,248,0.18)'; ctx.lineWidth=0.8; ctx.stroke();
    }
    // Axis labels
    axes.forEach((label,i)=>{
        const a=i*2*Math.PI/N-Math.PI/2;
        const x=cx+Math.cos(a)*(R+15), y=cy+Math.sin(a)*(R+15);
        ctx.fillStyle='#5ba8be'; ctx.font='8px Space Grotesk'; ctx.textAlign='center';
        ctx.fillText(label, x, y+3);
    });

    // Max values per axis
    const maxVals=[
        Math.max(...dss04Data.map(r=>parseFloat(r.ocean_pollution_share)||0))||1,
        100, // mismanaged_share
        Math.max(...dss04Data.map(r=>parseFloat(r.mismanaged_per_capita)||0))||1,
        100, // landfilled_share
        100, // recycled (inverted)
    ];

    const getVal=(r,ai)=>{
        const raw=[
            parseFloat(r.ocean_pollution_share)||0,
            parseFloat(r.mismanaged_share)||0,
            parseFloat(r.mismanaged_per_capita)||0,
            parseFloat(r.landfilled_share)||0,
            100-(parseFloat(r.recycled_share)||0),
        ];
        return raw[ai]/maxVals[ai];
    };

    const selected=dss04Data.filter(r=>radarSel.includes(r.entity));
    selected.forEach((r,si)=>{
        const col=RADAR_COLORS[dss04Data.indexOf(r)%5];
        ctx.beginPath();
        for(let i=0;i<N;i++){
            const a=i*2*Math.PI/N-Math.PI/2;
            const norm=getVal(r,i);
            const x=cx+Math.cos(a)*R*norm, y=cy+Math.sin(a)*R*norm;
            i===0?ctx.moveTo(x,y):ctx.lineTo(x,y);
        }
        ctx.closePath();
        ctx.fillStyle=col+'22'; ctx.fill();
        ctx.strokeStyle=col; ctx.lineWidth=1.8; ctx.stroke();
        // Points
        for(let i=0;i<N;i++){
            const a=i*2*Math.PI/N-Math.PI/2;
            const norm=getVal(r,i);
            ctx.beginPath(); ctx.arc(cx+Math.cos(a)*R*norm, cy+Math.sin(a)*R*norm, 3.5,0,Math.PI*2);
            ctx.fillStyle=col; ctx.fill();
        }
    });
}

/* ───────────────────────────────
   PRIORITY TABLE
─────────────────────────────── */
function buildPrioTable() {
    const RECS={
        Critical:'Intervensi darurat — program nasional emergency',
        High:'Mitigasi aktif — pendanaan World Bank direkomendasikan',
        Medium:'Edukasi & infrastruktur daur ulang',
        Low:'Pemantauan rutin — pertahankan sistem',
    };
    const maxScore=Math.max(...dss04Data.map(r=>parseFloat(r.risk_score)||0))||1;
    const html=`<table class="prio-tbl">
        <thead><tr>
            <th>#</th><th>Negara</th><th>Risk Score</th><th>Kategori</th>
            <th>Ocean Pollution</th><th>Mismanaged/Kap</th><th>Recycled</th><th>Rekomendasi DSS</th>
        </tr></thead>
        <tbody>
        ${dss04Data.slice(0,15).map((r,i)=>{
            const score=parseFloat(r.risk_score)||0;
            const col=rc(r.risk_category);
            return `<tr>
                <td style="color:var(--text-muted);">${i+1}</td>
                <td style="font-family:'Space Grotesk';font-weight:500;color:var(--text-primary);">${r.entity}</td>
                <td>
                    <span class="score-track"><span class="score-fill" style="width:${(score/maxScore*100).toFixed(0)}%;background:${col};"></span></span>
                    <span style="color:${col};font-weight:600;">${score.toFixed(1)}</span>
                </td>
                <td>${rb(r.risk_category)}</td>
                <td>${r.ocean_pollution_share!=null?parseFloat(r.ocean_pollution_share).toFixed(3)+'%':'—'}</td>
                <td>${r.mismanaged_per_capita!=null?parseFloat(r.mismanaged_per_capita).toFixed(2)+' kg':'—'}</td>
                <td>${r.recycled_share!=null?parseFloat(r.recycled_share).toFixed(1)+'%':'—'}</td>
                <td style="font-family:'Space Grotesk';font-size:11px;color:var(--text-secondary);">${RECS[r.risk_category]||'—'}</td>
            </tr>`;
        }).join('')}
        </tbody>
    </table>`;
    document.getElementById('prio-wrap').innerHTML=html;
}

/* ───────────────────────────────
   INIT
─────────────────────────────── */
document.addEventListener('DOMContentLoaded', async ()=>{

    await Promise.all([loadDss01(), loadDss02(), loadDss03(), loadDss04()]);

    // Toggle wiring
    [
        ['dss01-toggle', renderDss01],
        ['dss02-toggle', renderDss02],
        ['dss03-toggle', renderDss03],
        ['dss04-toggle', renderDss04],
    ].forEach(([id, fn])=>{
        document.getElementById(id).addEventListener('click', e=>{
            const b=e.target.closest('.tog'); if(!b) return;
            document.querySelectorAll(`#${id} .tog`).forEach(x=>x.classList.remove('active'));
            b.classList.add('active'); fn(b.dataset.chart);
        });
    });

    // Resize
    let rt;
    window.addEventListener('resize',()=>{
        clearTimeout(rt); rt=setTimeout(()=>{
            renderDss01(document.querySelector('#dss01-toggle .tog.active')?.dataset.chart||'donut');
            renderDss02(document.querySelector('#dss02-toggle .tog.active')?.dataset.chart||'grouped');
            renderDss03(document.querySelector('#dss03-toggle .tog.active')?.dataset.chart||'hbar');
            renderDss04(document.querySelector('#dss04-toggle .tog.active')?.dataset.chart||'radar');
        },200);
    });
});
</script>
@endpush