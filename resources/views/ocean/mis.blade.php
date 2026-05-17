@extends('layouts.app')
@section('title', 'MIS — Management Information System')

@push('styles')
<style>
:root {
    --c-recycled:#16A34A; --c-incinerated:#2563EB;
    --c-mismanaged:#D97706; --c-landfilled:#6B7280;
}
.cc { background:rgba(10,30,60,0.88); border:1px solid rgba(56,189,248,0.13); border-radius:14px; backdrop-filter:blur(16px); overflow:hidden; }
.cc-head { padding:13px 18px; border-bottom:1px solid rgba(56,189,248,0.08); display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
.cc-title { font-size:13px; font-weight:600; color:var(--text-primary); }
.cc-sub   { font-size:11px; color:var(--text-muted); margin-top:2px; }
.cc-tag   { font-size:9px; font-weight:700; letter-spacing:0.8px; padding:2px 8px; border-radius:20px; background:rgba(56,189,248,0.12); color:#38bdf8; white-space:nowrap; flex-shrink:0; }
.cc-body  { padding:16px 18px; }
.toggle-row { display:flex; gap:4px; flex-shrink:0; }
.tog { padding:4px 10px; border-radius:6px; font-size:10px; font-weight:600; border:1px solid rgba(56,189,248,0.2); background:transparent; color:var(--text-muted); cursor:pointer; transition:all 0.15s; font-family:'Space Grotesk',sans-serif; }
.tog.active,.tog:hover { background:rgba(56,189,248,0.15); color:#38bdf8; border-color:rgba(56,189,248,0.4); }
.ind-tab { padding:3px 9px; border-radius:5px; font-size:10px; font-weight:600; border:1px solid rgba(56,189,248,0.15); background:transparent; color:var(--text-muted); cursor:pointer; transition:all 0.15s; font-family:'Space Grotesk',sans-serif; }
.ind-tab.active { background:rgba(56,189,248,0.15); color:#38bdf8; border-color:rgba(56,189,248,0.35); }

/* KPI */
.kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:16px; }
.kpi-card { background:rgba(10,30,60,0.88); border:1px solid rgba(56,189,248,0.13); border-radius:14px; padding:16px 18px; position:relative; overflow:hidden; backdrop-filter:blur(16px); transition:transform 0.18s; }
.kpi-card:hover { transform:translateY(-2px); }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
.kpi-card.kc-b::before { background:linear-gradient(90deg,#0891b2,#06b6d4); }
.kpi-card.kc-g::before { background:linear-gradient(90deg,#10b981,#34d399); }
.kpi-card.kc-o::before { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.kpi-card.kc-r::before { background:linear-gradient(90deg,#ef4444,#f87171); }
.kpi-icon { position:absolute; top:14px; right:14px; font-size:20px; opacity:0.2; }
.kpi-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--text-muted); margin-bottom:6px; }
.kpi-val   { font-size:26px; font-weight:700; font-family:'JetBrains Mono',monospace; letter-spacing:-1px; color:var(--text-primary); line-height:1; }
.kpi-sub   { font-size:11px; color:var(--text-muted); margin-top:4px; }
.skel { background:linear-gradient(90deg,rgba(56,189,248,0.06) 25%,rgba(56,189,248,0.10) 50%,rgba(56,189,248,0.06) 75%); background-size:200% 100%; animation:shimmer 1.6s infinite; border-radius:6px; display:inline-block; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Layout */
.g2  { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
.g21 { display:grid; grid-template-columns:2fr 1fr; gap:12px; margin-bottom:12px; }

/* Globe */
#globe-mount { width:100%; height:360px; position:relative; background:#000814; border-radius:0 0 14px 14px; overflow:hidden; }
#globe-canvas-3d { display:block; width:100%!important; height:100%!important; }
.globe-hint-bar {
    position:absolute; bottom:10px; left:50%; transform:translateX(-50%);
    background:rgba(10,30,60,0.85); border:1px solid rgba(56,189,248,0.15);
    border-radius:6px; padding:4px 14px; font-size:11px; color:var(--text-muted);
    pointer-events:none; white-space:nowrap; z-index:10;
}
.globe-legend-box {
    position:absolute; bottom:10px; left:10px;
    background:rgba(10,30,60,0.85); border:1px solid rgba(56,189,248,0.15);
    border-radius:9px; padding:9px 12px; z-index:10;
}
.gle-title { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--text-muted); margin-bottom:6px; }
.gle-row   { display:flex; align-items:center; gap:6px; margin-bottom:3px; }
.gle-dot   { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.gle-lbl   { font-size:10px; color:var(--text-secondary); }

/* Atlas */
#atlas-mount { width:100%; height:360px; position:relative; overflow:hidden; border-radius:0 0 14px 14px; }
#atlas-mount svg { display:block; width:100%; height:100%; }

/* Bar rows */
.bar-row   { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
.bar-lbl   { font-size:11px; color:var(--text-secondary); width:92px; text-align:right; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex-shrink:0; }
.bar-track { flex:1; height:14px; background:rgba(56,189,248,0.07); border-radius:4px; overflow:hidden; }
.bar-fill  { height:100%; border-radius:4px; transition:width 1.2s cubic-bezier(.22,1,.36,1); }
.bar-val   { font-size:11px; color:var(--text-muted); width:55px; text-align:right; font-family:'JetBrains Mono',monospace; flex-shrink:0; }
.legend    { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
.leg-item  { display:flex; align-items:center; gap:5px; font-size:11px; color:var(--text-secondary); }
.leg-dot   { width:10px; height:10px; border-radius:3px; flex-shrink:0; }
canvas.cc  { display:block; width:100%; }
</style>
@endpush

@section('content')

{{-- KPI MIS-04 --}}
<div class="kpi-grid" id="kpi-grid">
    @foreach(['kc-b','kc-g','kc-o','kc-r'] as $c)
    <div class="kpi-card {{$c}}">
        <div class="kpi-label"><span class="skel" style="height:12px;width:120px;">&nbsp;</span></div>
        <div class="kpi-val"><span class="skel" style="height:28px;width:90px;">&nbsp;</span></div>
    </div>
    @endforeach
</div>

{{-- ROW 1: GEO-01 + MIS-03 --}}
<div class="g21">

    {{-- GEO-01 --}}
    <div class="cc">
        <div class="cc-head">
            <div>
                <div class="cc-title">🌍 Peta Polusi Plastik Global</div>
                <div class="cc-sub" id="geo-sub">Ocean Pollution Share per negara (2019)</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <div style="display:flex;gap:4px;" id="ind-tabs">
                    <button class="ind-tab active" data-ind="ocean_pollution_share">Polusi Laut</button>
                    <button class="ind-tab" data-ind="mismanaged_per_capita">Mismanaged/Kap</button>
                    <button class="ind-tab" data-ind="recycled_share">Recycling</button>
                </div>
                <div class="toggle-row">
                    <button class="tog active" id="btn-globe">🌍 Globe</button>
                    <button class="tog" id="btn-atlas">🗺 Atlas</button>
                </div>
            </div>
        </div>

        {{-- Globe (Three.js) --}}
        <div id="globe-mount">
            <canvas id="globe-canvas-3d"></canvas>
            <div class="globe-hint-bar" id="globe-hint">🖱 Drag putar · Scroll zoom · Hover negara</div>
            <div class="globe-legend-box">
                <div class="gle-title" id="gle-title">Ocean Pollution Share</div>
                <div class="gle-row"><div class="gle-dot" style="background:#DC2626;"></div><div class="gle-lbl">Tinggi</div></div>
                <div class="gle-row"><div class="gle-dot" style="background:#D97706;"></div><div class="gle-lbl">Sedang</div></div>
                <div class="gle-row"><div class="gle-dot" style="background:#16A34A;"></div><div class="gle-lbl">Rendah</div></div>
            </div>
        </div>

        {{-- Atlas --}}
        <div id="atlas-mount" style="display:none;"></div>
    </div>

    {{-- MIS-03 Top 10 Mismanaged Per Kapita --}}
    <div class="cc">
        <div class="cc-head">
            <div>
                <div class="cc-title">Top 10 Mismanaged/Kapita</div>
                <div class="cc-sub">kg per orang per tahun (2019)</div>
            </div>
            <div class="toggle-row" id="mis03-toggle">
                <button class="tog active" data-chart="hbar">H-Bar</button>
                <button class="tog" data-chart="lollipop">Lollipop</button>
                <button class="tog" data-chart="vbar">Column</button>
            </div>
        </div>
        <div class="cc-body" style="padding:14px 16px;">
            <canvas id="mis03-canvas" style="display:block;width:100%;" height="290"></canvas>
        </div>
    </div>
</div>

{{-- ROW 2: MIS-01 + MIS-02 --}}
<div class="g2">
    <div class="cc">
        <div class="cc-head">
            <div>
                <div class="cc-title">📈 Tren Produksi Plastik (1950–2019)</div>
                <div class="cc-sub">Annual plastic production (ton)</div>
            </div>
            <div class="toggle-row" id="mis01-toggle">
                <button class="tog active" data-chart="line">Line</button>
                <button class="tog" data-chart="area">Area</button>
                <button class="tog" data-chart="bar">Bar/Dekade</button>
            </div>
        </div>
        <div class="cc-body">
            <canvas id="mis01-canvas" style="display:block;width:100%;" height="220"></canvas>
        </div>
    </div>
    <div class="cc">
        <div class="cc-head">
            <div>
                <div class="cc-title">♻️ Komposisi Pengelolaan Sampah</div>
                <div class="cc-sub">Share per metode · regional data</div>
            </div>
            <div class="toggle-row" id="mis02-toggle">
                <button class="tog active" data-chart="stacked">Stacked</button>
                <button class="tog" data-chart="grouped">Grouped</button>
                <button class="tog" data-chart="pct">100% Stack</button>
            </div>
        </div>
        <div class="cc-body">
            <div class="legend">
                <div class="leg-item"><div class="leg-dot" style="background:#16A34A;"></div>Recycled</div>
                <div class="leg-item"><div class="leg-dot" style="background:#2563EB;"></div>Incinerated</div>
                <div class="leg-item"><div class="leg-dot" style="background:#D97706;"></div>Mismanaged</div>
                <div class="leg-item"><div class="leg-dot" style="background:#6B7280;"></div>Landfilled</div>
            </div>
            <canvas id="mis02-canvas" style="display:block;width:100%;" height="190"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Three.js dari CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
{{-- D3 + TopoJSON untuk Atlas view --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/topojson/3.0.2/topojson.min.js"></script>
<script>
/* ═══════════════════════════════════════════
   BASE URL untuk semua API fetch
═══════════════════════════════════════════ */
const BASE = window.location.origin;
const API  = {
    kpi:          BASE + '/api/ocean/kpi',
    production:   BASE + '/api/ocean/production',
    wasteFate:    BASE + '/api/ocean/waste-fate',
    topMismanaged:BASE + '/api/ocean/top-mismanaged',
    geo:          BASE + '/api/ocean/geo',
};

/* ═══════════════════════════════════════════
   CANVAS HELPER
═══════════════════════════════════════════ */
function initCanvas(id, heightAttr) {
    const c = document.getElementById(id);
    if (!c) return null;
    const dpr = Math.min(devicePixelRatio, 2);
    const W   = c.parentElement.clientWidth || 400;
    const H   = parseInt(c.getAttribute('height') || heightAttr || 200);
    c.width   = W * dpr;
    c.height  = H * dpr;
    const ctx = c.getContext('2d');
    ctx.scale(dpr, dpr);
    return { c, ctx, W, H };
}

/* ═══════════════════════════════════════════
   KPI (MIS-04)
═══════════════════════════════════════════ */
async function loadKpi() {
    try {
        const res = await fetch(API.kpi);
        if (!res.ok) throw new Error(res.status);
        const d = await res.json();
        const prod = (d.total_production_2019 / 1e6).toFixed(0);
        document.getElementById('kpi-grid').innerHTML = `
        <div class="kpi-card kc-b">
            <div class="kpi-icon">🏭</div>
            <div class="kpi-label">Produksi Plastik 2019</div>
            <div class="kpi-val">${Number(prod).toLocaleString()}</div>
            <div class="kpi-sub">Juta ton · produksi global</div>
        </div>
        <div class="kpi-card kc-g">
            <div class="kpi-icon">♻️</div>
            <div class="kpi-label">Recycling Rate Global</div>
            <div class="kpi-val">${d.avg_recycled ?? '—'}%</div>
            <div class="kpi-sub">World average (2019)</div>
        </div>
        <div class="kpi-card kc-o">
            <div class="kpi-icon">⚠️</div>
            <div class="kpi-label">Mismanaged Global</div>
            <div class="kpi-val">${d.world_mismanaged ?? '—'}%</div>
            <div class="kpi-sub">Share sampah tak terkelola</div>
        </div>
        <div class="kpi-card kc-r">
            <div class="kpi-icon">🐠</div>
            <div class="kpi-label">Top Poluter Laut</div>
            <div class="kpi-val" style="font-size:18px;letter-spacing:0;">${d.top_polluter?.entity ?? '—'}</div>
            <div class="kpi-sub">${d.top_polluter ? d.top_polluter.ocean_pollution_share.toFixed(2)+'% kontribusi' : ''}</div>
        </div>`;
    } catch(e) {
        console.error('KPI error:', e);
    }
}

/* ═══════════════════════════════════════════
   MIS-01 TREN PRODUKSI
═══════════════════════════════════════════ */
let mis01Data = [];
async function loadMis01() {
    try {
        const res = await fetch(API.production);
        mis01Data = await res.json();
        renderMis01('line');
    } catch(e) { console.error('MIS01:', e); }
}
function renderMis01(type) {
    const cv = initCanvas('mis01-canvas', 220);
    if (!cv || !mis01Data.length) return;
    const {ctx, W, H} = cv;
    const pad = {l:52, r:16, t:12, b:36};
    const iW = W-pad.l-pad.r, iH = H-pad.t-pad.b;
    ctx.clearRect(0,0,W,H);

    let labels = mis01Data.map(r=>r.year);
    let values = mis01Data.map(r=>+r.plastic_production);

    if (type === 'bar') {
        const dek = {};
        mis01Data.forEach(r=>{ const d=Math.floor(r.year/10)*10; dek[d]=dek[d]||[]; dek[d].push(+r.plastic_production); });
        labels = Object.keys(dek).sort();
        values = labels.map(d=>dek[d].reduce((a,b)=>a+b,0)/dek[d].length);
    }

    const mn = 0, mx = Math.max(...values)*1.08;
    const px = i=>pad.l+(i/(labels.length-1||1))*iW;
    const py = v=>pad.t+(1-v/mx)*iH;

    // Grid
    ctx.strokeStyle='rgba(56,189,248,0.08)'; ctx.lineWidth=0.5;
    [0,.25,.5,.75,1].forEach(f=>{
        const y=pad.t+(1-f)*iH;
        ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(W-pad.r,y); ctx.stroke();
        ctx.fillStyle='#5ba8be'; ctx.font='9px JetBrains Mono'; ctx.textAlign='right';
        ctx.fillText(((mn+f*(mx-mn))/1e6).toFixed(0)+'M', pad.l-4, y+3);
    });

    if (type==='bar') {
        const bw=Math.min(iW/labels.length*0.7,28);
        labels.forEach((l,i)=>{
            const x=pad.l+(i+0.5)*iW/labels.length;
            const h=(values[i]/mx)*iH;
            const g=ctx.createLinearGradient(0,py(values[i]),0,py(0));
            g.addColorStop(0,'#06b6d4cc'); g.addColorStop(1,'#0891b233');
            ctx.fillStyle=g; ctx.fillRect(x-bw/2,py(values[i]),bw,py(0)-py(values[i]));
            ctx.fillStyle='#5ba8be'; ctx.font='9px JetBrains Mono'; ctx.textAlign='center';
            ctx.fillText(l+'s',x,H-pad.b+13);
        });
    } else {
        const pts=labels.map((l,i)=>({x:px(i),y:py(values[i])}));
        if (type==='area') {
            ctx.beginPath();
            ctx.moveTo(pts[0].x,pad.t+iH);
            pts.forEach(p=>ctx.lineTo(p.x,p.y));
            ctx.lineTo(pts[pts.length-1].x,pad.t+iH);
            ctx.closePath();
            const g=ctx.createLinearGradient(0,pad.t,0,pad.t+iH);
            g.addColorStop(0,'rgba(6,182,212,0.3)'); g.addColorStop(1,'rgba(6,182,212,0.02)');
            ctx.fillStyle=g; ctx.fill();
        }
        ctx.beginPath();
        pts.forEach((p,i)=>i===0?ctx.moveTo(p.x,p.y):ctx.lineTo(p.x,p.y));
        ctx.strokeStyle='#06b6d4'; ctx.lineWidth=2; ctx.stroke();
        const step=Math.max(1,Math.floor(labels.length/8));
        ctx.fillStyle='#5ba8be'; ctx.font='9px JetBrains Mono'; ctx.textAlign='center';
        labels.forEach((l,i)=>{ if(i%step===0) ctx.fillText(l,px(i),H-pad.b+13); });
    }
}

/* ═══════════════════════════════════════════
   MIS-02 KOMPOSISI WASTE
═══════════════════════════════════════════ */
let mis02Data = [];
async function loadMis02() {
    try {
        const res = await fetch(API.wasteFate);
        const all = await res.json();
        mis02Data = all.filter(r=>r.year==2019);
        if (mis02Data.length<3) {
            const last={};
            all.forEach(r=>{ if(!last[r.entity]||r.year>last[r.entity].year) last[r.entity]=r; });
            mis02Data=Object.values(last);
        }
        renderMis02('stacked');
    } catch(e) { console.error('MIS02:', e); }
}
function renderMis02(type) {
    const cv = initCanvas('mis02-canvas', 190);
    if (!cv || !mis02Data.length) return;
    const {ctx, W, H} = cv;
    const keys=['recycled_share','incinerated_share','mismanaged_share','landfilled_share'];
    const cols=['#16A34A','#2563EB','#D97706','#6B7280'];
    const data = mis02Data.filter(r=>r.entity!=='World').slice(0,10);
    if (!data.length) return;
    ctx.clearRect(0,0,W,H);

    if (type==='stacked'||type==='pct') {
        const pad={l:135,r:16,t:8,b:8};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const rowH=iH/data.length;
        data.forEach((row,ri)=>{
            const total=type==='pct'?keys.reduce((s,k)=>s+(+row[k]||0),0):100;
            const bh=Math.min(rowH*.65,16);
            const by=pad.t+ri*rowH+(rowH-bh)/2;
            ctx.fillStyle='#94d5e8'; ctx.font='10px Space Grotesk'; ctx.textAlign='right';
            ctx.fillText(row.entity.length>20?row.entity.slice(0,19)+'…':row.entity, pad.l-5, by+bh/2+3);
            let cx2=pad.l;
            keys.forEach((k,ki)=>{
                const w=(((+row[k])||0)/total)*iW;
                ctx.fillStyle=cols[ki]; ctx.fillRect(cx2,by,w,bh); cx2+=w;
            });
        });
    } else {
        const pad={l:16,r:16,t:12,b:28};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const mx=Math.max(...data.flatMap(r=>keys.map(k=>+r[k]||0)));
        const gW=iW/data.length;
        const bW=Math.min(gW/5,11);
        ctx.strokeStyle='rgba(56,189,248,0.07)'; ctx.lineWidth=0.5;
        [0,.5,1].forEach(f=>{
            const y=pad.t+(1-f)*iH;
            ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(W-pad.r,y); ctx.stroke();
        });
        data.forEach((row,ri)=>{
            const cx2=pad.l+(ri+0.5)*gW;
            keys.forEach((k,ki)=>{
                const val=+row[k]||0;
                const bx=cx2+(ki-1.5)*(bW+2);
                const bH=(val/mx)*iH;
                ctx.fillStyle=cols[ki]+'cc';
                ctx.fillRect(bx-bW/2, pad.t+iH-bH, bW, bH);
            });
            ctx.fillStyle='#5ba8be'; ctx.font='8px Space Grotesk'; ctx.textAlign='center';
            const s=row.entity.length>8?row.entity.slice(0,7)+'.':row.entity;
            ctx.fillText(s, cx2, H-pad.b+11);
        });
    }
}

/* ═══════════════════════════════════════════
   MIS-03 TOP 10 MISMANAGED
═══════════════════════════════════════════ */
let mis03Data = [];
async function loadMis03() {
    try {
        const res = await fetch(API.topMismanaged);
        mis03Data = await res.json();
        renderMis03('hbar');
    } catch(e) { console.error('MIS03:', e); }
}
function renderMis03(type) {
    const cv = initCanvas('mis03-canvas', 290);
    if (!cv || !mis03Data.length) return;
    const {ctx, W, H} = cv;
    ctx.clearRect(0,0,W,H);

    if (type==='hbar') {
        const pad={l:96,r:62,t:8,b:8};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const mx=Math.max(...mis03Data.map(r=>+r.mismanaged_per_capita));
        const rowH=iH/mis03Data.length;
        mis03Data.forEach((r,i)=>{
            const bh=Math.min(rowH*.62,16);
            const by=pad.t+i*rowH+(rowH-bh)/2;
            const bw=(+r.mismanaged_per_capita/mx)*iW;
            ctx.fillStyle='#94d5e8'; ctx.font='10px Space Grotesk'; ctx.textAlign='right';
            ctx.fillText(r.entity.length>13?r.entity.slice(0,12)+'…':r.entity, pad.l-5, by+bh/2+3);
            const g=ctx.createLinearGradient(pad.l,0,pad.l+bw,0);
            g.addColorStop(0,'#0891b2aa'); g.addColorStop(1,'#06b6d4');
            ctx.fillStyle=g; ctx.fillRect(pad.l,by,bw,bh);
            ctx.fillStyle='#5ba8be'; ctx.font='10px JetBrains Mono'; ctx.textAlign='left';
            ctx.fillText((+r.mismanaged_per_capita).toFixed(1)+' kg', pad.l+bw+4, by+bh/2+3);
        });
    } else if (type==='lollipop') {
        const pad={l:96,r:62,t:8,b:8};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const mx=Math.max(...mis03Data.map(r=>+r.mismanaged_per_capita));
        const rowH=iH/mis03Data.length;
        mis03Data.forEach((r,i)=>{
            const y=pad.t+i*rowH+rowH/2;
            const x=pad.l+(+r.mismanaged_per_capita/mx)*iW;
            ctx.fillStyle='#94d5e8'; ctx.font='10px Space Grotesk'; ctx.textAlign='right';
            ctx.fillText(r.entity.length>13?r.entity.slice(0,12)+'…':r.entity, pad.l-5, y+3);
            ctx.strokeStyle='#06b6d455'; ctx.lineWidth=1.5;
            ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(x,y); ctx.stroke();
            ctx.beginPath(); ctx.arc(x,y,5,0,Math.PI*2);
            ctx.fillStyle='#06b6d4'; ctx.fill();
            ctx.fillStyle='#5ba8be'; ctx.font='10px JetBrains Mono'; ctx.textAlign='left';
            ctx.fillText((+r.mismanaged_per_capita).toFixed(1), x+8, y+3);
        });
    } else {
        const pad={l:36,r:8,t:12,b:55};
        const iW=W-pad.l-pad.r, iH=H-pad.t-pad.b;
        const bW=iW/mis03Data.length*.7;
        const mx=Math.max(...mis03Data.map(r=>+r.mismanaged_per_capita));
        ctx.strokeStyle='rgba(56,189,248,0.07)'; ctx.lineWidth=0.5;
        [0,.5,1].forEach(f=>{
            const y=pad.t+(1-f)*iH;
            ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(W-pad.r,y); ctx.stroke();
            ctx.fillStyle='#5ba8be'; ctx.font='9px JetBrains Mono'; ctx.textAlign='right';
            ctx.fillText((mx*f).toFixed(0), pad.l-3, y+3);
        });
        mis03Data.forEach((r,i)=>{
            const cx2=pad.l+(i+0.5)*iW/mis03Data.length;
            const bH=(+r.mismanaged_per_capita/mx)*iH;
            const g=ctx.createLinearGradient(0,pad.t+iH-bH,0,pad.t+iH);
            g.addColorStop(0,'#06b6d4'); g.addColorStop(1,'#0891b244');
            ctx.fillStyle=g; ctx.fillRect(cx2-bW/2,pad.t+iH-bH,bW,bH);
            ctx.fillStyle='#5ba8be'; ctx.font='8px Space Grotesk'; ctx.textAlign='center';
            ctx.save(); ctx.translate(cx2,H-pad.b+5); ctx.rotate(-Math.PI/4);
            ctx.fillText(r.entity.length>8?r.entity.slice(0,7)+'.':r.entity,0,0);
            ctx.restore();
        });
    }
}

/* ═══════════════════════════════════════════
   GEO-01: THREE.JS GLOBE dengan Earth Texture
═══════════════════════════════════════════ */
let threeScene, threeCamera, threeRenderer, earthMesh, markerGroup;
let threeAnimId, isDragging=false, prevMouse={x:0,y:0};
let geoPoints=[], currentIndicator='ocean_pollution_share', geoMode='globe';

const COORDS={
    'Afghanistan':[33,65],'Albania':[41,20],'Algeria':[28,2],'Angola':[-12,18],
    'Argentina':[-34,-64],'Armenia':[40,45],'Australia':[-27,133],'Austria':[47,14],
    'Azerbaijan':[40,48],'Bangladesh':[24,90],'Belarus':[53,28],'Belgium':[50,4],
    'Benin':[9,2],'Bolivia':[-17,-65],'Brazil':[-15,-47],'Bulgaria':[43,25],
    'Burkina Faso':[13,-2],'Burundi':[-3,30],'Cambodia':[12,105],'Cameroon':[4,12],
    'Canada':[56,-106],'Chad':[15,19],'Chile':[-30,-71],'China':[35,105],
    'Colombia':[4,-73],'Comoros':[-12,44],'Costa Rica':[10,-84],'Croatia':[45,16],
    "Cote d'Ivoire":[7,-5],'Cuba':[22,-80],'Czechia':[50,15],
    'Democratic Republic of Congo':[-4,24],'Denmark':[56,10],'Dominican Republic':[19,-70],
    'Ecuador':[-2,-78],'Egypt':[27,30],'El Salvador':[14,-89],'Ethiopia':[9,40],
    'Finland':[64,26],'France':[46,2],'Gabon':[-1,12],'Germany':[51,10],
    'Ghana':[8,-1],'Greece':[39,22],'Guatemala':[15,-90],'Guinea':[11,-11],
    'Guyana':[5,-59],'Haiti':[19,-73],'Honduras':[15,-87],'Hungary':[47,19],
    'India':[20,78],'Indonesia':[-5,120],'Iran':[32,53],'Iraq':[33,44],
    'Ireland':[53,-8],'Italy':[42,12],'Jamaica':[18,-77],'Japan':[36,138],
    'Jordan':[31,36],'Kazakhstan':[48,68],'Kenya':[1,38],'Libya':[27,17],
    'Madagascar':[-20,47],'Malawi':[-13,34],'Malaysia':[4,108],'Mali':[17,-4],
    'Mexico':[24,-102],'Moldova':[47,29],'Morocco':[32,-5],'Mozambique':[-18,35],
    'Myanmar':[17,96],'Nepal':[28,84],'Netherlands':[52,5],'New Zealand':[-41,174],
    'Nicaragua':[13,-85],'Niger':[17,8],'Nigeria':[9,8],'Norway':[60,8],
    'Pakistan':[30,69],'Panama':[9,-80],'Papua New Guinea':[-6,147],
    'Paraguay':[-23,-58],'Peru':[-10,-76],'Philippines':[12,122],'Poland':[52,20],
    'Portugal':[39,-8],'Romania':[46,25],'Russia':[60,90],'Rwanda':[-2,30],
    'Saudi Arabia':[24,45],'Senegal':[14,-14],'Sierra Leone':[8,-12],
    'Somalia':[6,46],'South Africa':[-30,25],'South Korea':[36,128],
    'Spain':[40,-4],'Sri Lanka':[7,81],'Sudan':[15,30],'Suriname':[4,-56],
    'Sweden':[60,15],'Switzerland':[47,8],'Syria':[35,38],'Taiwan':[24,121],
    'Tanzania':[-6,35],'Thailand':[15,101],'Togo':[8,1],
    'Trinidad and Tobago':[11,-61],'Tunisia':[34,9],'Turkey':[39,35],
    'Uganda':[1,32],'Ukraine':[49,31],'United Kingdom':[54,-2],
    'United States':[38,-97],'Uruguay':[-33,-56],'Uzbekistan':[41,64],
    'Venezuela':[8,-66],'Vietnam':[16,108],'Yemen':[15,48],
    'Zambia':[-13,27],'Zimbabwe':[-20,30],
};

function latLonToVec3(lat, lon, r) {
    const phi   = (90 - lat) * Math.PI / 180;
    const theta = (lon + 180) * Math.PI / 180;
    return new THREE.Vector3(
        -r * Math.sin(phi) * Math.cos(theta),
         r * Math.cos(phi),
         r * Math.sin(phi) * Math.sin(theta)
    );
}

function valueToColor(norm, indicator) {
    if (indicator === 'recycled_share') {
        // low=red high=green
        return new THREE.Color(
            0.86 - norm*0.80,
            0.16 + norm*0.48,
            0.16
        );
    }
    // low=green high=red
    return new THREE.Color(
        0.086 + norm*0.855,
        0.639 - norm*0.575,
        0.290 - norm*0.270
    );
}

function initThreeGlobe() {
    const mount = document.getElementById('globe-mount');
    const canvas = document.getElementById('globe-canvas-3d');
    const W = mount.clientWidth, H = mount.clientHeight;

    threeScene    = new THREE.Scene();
    threeCamera   = new THREE.PerspectiveCamera(45, W/H, 0.1, 100);
    threeCamera.position.z = 2.4;

    threeRenderer = new THREE.WebGLRenderer({ canvas, antialias:true, alpha:true });
    threeRenderer.setSize(W, H);
    threeRenderer.setPixelRatio(Math.min(devicePixelRatio, 2));
    threeRenderer.setClearColor(0x000814, 1);

    // Stars background
    const starGeo = new THREE.BufferGeometry();
    const starPos = [];
    for (let i=0; i<6000; i++) {
        starPos.push((Math.random()-0.5)*80, (Math.random()-0.5)*80, (Math.random()-0.5)*80);
    }
    starGeo.setAttribute('position', new THREE.Float32BufferAttribute(starPos, 3));
    threeScene.add(new THREE.Points(starGeo, new THREE.PointsMaterial({color:0xffffff,size:0.05,sizeAttenuation:true})));

    // Earth sphere — menggunakan texture procedural (blue marble look)
    const earthGeo = new THREE.SphereGeometry(1, 64, 64);

    // Load texture dari NASA/Stamen CDN
    const loader = new THREE.TextureLoader();
    const earthTexUrl = 'https://raw.githubusercontent.com/mrdoob/three.js/r128/examples/textures/planets/earth_atmos_2048.jpg';

    // Buat material dengan canvas texture procedural sebagai fallback
    const canvas2d = document.createElement('canvas');
    canvas2d.width=1024; canvas2d.height=512;
    const ctx2 = canvas2d.getContext('2d');
    // Ocean gradient
    const oceanGrad = ctx2.createLinearGradient(0,0,0,512);
    oceanGrad.addColorStop(0,'#0a2a5e');
    oceanGrad.addColorStop(0.5,'#0c3d7a');
    oceanGrad.addColorStop(1,'#071e45');
    ctx2.fillStyle=oceanGrad; ctx2.fillRect(0,0,1024,512);
    // Simple continent shapes (very rough procedural)
    ctx2.fillStyle='#2d5a1b';
    // North America
    ctx2.beginPath(); ctx2.ellipse(190,165,80,90,0,0,Math.PI*2); ctx2.fill();
    // South America
    ctx2.beginPath(); ctx2.ellipse(240,320,55,95,-0.3,0,Math.PI*2); ctx2.fill();
    // Europe
    ctx2.beginPath(); ctx2.ellipse(490,145,60,60,0,0,Math.PI*2); ctx2.fill();
    // Africa
    ctx2.beginPath(); ctx2.ellipse(500,285,70,110,0,0,Math.PI*2); ctx2.fill();
    // Asia
    ctx2.beginPath(); ctx2.ellipse(680,165,165,90,0,0,Math.PI*2); ctx2.fill();
    // Australia
    ctx2.beginPath(); ctx2.ellipse(775,340,75,55,0,0,Math.PI*2); ctx2.fill();
    // Antarctica
    ctx2.fillStyle='#d4e8f0';
    ctx2.beginPath(); ctx2.ellipse(512,480,320,40,0,0,Math.PI*2); ctx2.fill();
    // Greenland
    ctx2.fillStyle='#b8d4e8';
    ctx2.beginPath(); ctx2.ellipse(280,100,40,55,0,0,Math.PI*2); ctx2.fill();
    // Ice
    ctx2.fillStyle='rgba(200,230,255,0.4)';
    ctx2.beginPath(); ctx2.ellipse(512,10,512,30,0,0,Math.PI*2); ctx2.fill();

    const proceduralTex = new THREE.CanvasTexture(canvas2d);
    const earthMat = new THREE.MeshPhongMaterial({
        map: proceduralTex,
        specular: new THREE.Color(0x1a3a6e),
        shininess: 18,
        emissive: new THREE.Color(0x061428),
        emissiveIntensity: 0.15,
    });
    earthMesh = new THREE.Mesh(earthGeo, earthMat);
    threeScene.add(earthMesh);

    // Try to load NASA texture
    loader.load(earthTexUrl,
        (tex) => { earthMat.map = tex; earthMat.needsUpdate = true; },
        undefined,
        () => { /* fallback ke procedural */ }
    );

    // Atmosphere glow
    const atmGeo = new THREE.SphereGeometry(1.02, 32, 32);
    const atmMat = new THREE.MeshPhongMaterial({
        color: 0x4488ff,
        transparent: true,
        opacity: 0.07,
        side: THREE.FrontSide,
    });
    threeScene.add(new THREE.Mesh(atmGeo, atmMat));

    // Outer glow ring
    const glowGeo = new THREE.SphereGeometry(1.08, 32, 32);
    const glowMat = new THREE.MeshPhongMaterial({
        color: 0x0066cc,
        transparent: true,
        opacity: 0.04,
        side: THREE.BackSide,
    });
    threeScene.add(new THREE.Mesh(glowGeo, glowMat));

    // Marker group
    markerGroup = new THREE.Group();
    threeScene.add(markerGroup);

    // Lighting
    threeScene.add(new THREE.AmbientLight(0x334466, 0.8));
    const sun = new THREE.DirectionalLight(0xffffff, 1.2);
    sun.position.set(5, 3, 5);
    threeScene.add(sun);
    const rim = new THREE.DirectionalLight(0x4488cc, 0.4);
    rim.position.set(-5, -2, -3);
    threeScene.add(rim);

    // Auto-rotate
    let autoRot = true;
    const clock = new THREE.Clock();

    // Mouse events
    const onDown = e => {
        isDragging = true;
        autoRot = false;
        const cl = e.touches ? e.touches[0] : e;
        prevMouse = {x: cl.clientX, y: cl.clientY};
    };
    const onUp = () => {
        isDragging = false;
        setTimeout(() => autoRot = true, 2500);
    };
    const onMove = e => {
        if (!isDragging) return;
        const cl = e.touches ? e.touches[0] : e;
        const dx = cl.clientX - prevMouse.x;
        const dy = cl.clientY - prevMouse.y;
        earthMesh.rotation.y += dx * 0.005;
        markerGroup.rotation.y += dx * 0.005;
        earthMesh.rotation.x += dy * 0.003;
        markerGroup.rotation.x += dy * 0.003;
        earthMesh.rotation.x = Math.max(-Math.PI/2.5, Math.min(Math.PI/2.5, earthMesh.rotation.x));
        markerGroup.rotation.x = earthMesh.rotation.x;
        prevMouse = {x: cl.clientX, y: cl.clientY};
    };
    const onWheel = e => {
        threeCamera.position.z = Math.max(1.5, Math.min(4.5, threeCamera.position.z + e.deltaY*0.002));
    };
    mount.addEventListener('mousedown', onDown);
    mount.addEventListener('touchstart', onDown, {passive:true});
    window.addEventListener('mouseup', onUp);
    window.addEventListener('touchend', onUp);
    window.addEventListener('mousemove', onMove);
    window.addEventListener('touchmove', onMove, {passive:true});
    mount.addEventListener('wheel', onWheel, {passive:true});

    // Resize
    const ro = new ResizeObserver(() => {
        const W2=mount.clientWidth, H2=mount.clientHeight;
        threeCamera.aspect = W2/H2;
        threeCamera.updateProjectionMatrix();
        threeRenderer.setSize(W2, H2);
    });
    ro.observe(mount);

    // Render loop
    const animate = () => {
        threeAnimId = requestAnimationFrame(animate);
        if (autoRot) {
            earthMesh.rotation.y += 0.002;
            markerGroup.rotation.y += 0.002;
        }
        threeRenderer.render(threeScene, threeCamera);
    };
    animate();
}

function updateGlobeMarkers(data, indicator) {
    if (!markerGroup) return;
    // Clear old markers
    while (markerGroup.children.length) markerGroup.remove(markerGroup.children[0]);

    const vals = data.filter(r=>COORDS[r.entity]).map(r=>parseFloat(r[indicator])||0);
    const mn = Math.min(...vals), mx = Math.max(...vals)||1;

    data.forEach(r => {
        if (!COORDS[r.entity]) return;
        const val = parseFloat(r[indicator]) || 0;
        const norm = (val - mn) / (mx - mn);
        const [lat, lon] = COORDS[r.entity];

        const size = 0.012 + norm * 0.032;
        const col  = valueToColor(norm, indicator);

        // Spike marker (cylinder pointing outward)
        const geo = new THREE.SphereGeometry(size, 8, 8);
        const mat = new THREE.MeshPhongMaterial({
            color: col,
            emissive: col,
            emissiveIntensity: 0.5,
            transparent: true,
            opacity: 0.85,
        });
        const mesh = new THREE.Mesh(geo, mat);
        const pos  = latLonToVec3(lat, lon, 1.01);
        mesh.position.copy(pos);
        mesh.userData = { entity: r.entity, value: val, indicator };
        markerGroup.add(mesh);

        // Glow ring for high values
        if (norm > 0.5) {
            const ringGeo = new THREE.SphereGeometry(size*1.8, 8, 8);
            const ringMat = new THREE.MeshPhongMaterial({
                color: col, transparent:true, opacity:0.2
            });
            const ring = new THREE.Mesh(ringGeo, ringMat);
            ring.position.copy(pos);
            markerGroup.add(ring);
        }
    });
}

/* ═══════════════════════════════════════════
   GEO DATA LOAD
═══════════════════════════════════════════ */
let geoData = [];
async function loadGeo(indicator) {
    try {
        const res = await fetch(API.geo + '?indicator=' + indicator);
        if (!res.ok) throw new Error(res.status);
        const d = await res.json();
        geoData = d.data;
        currentIndicator = indicator;

        if (geoMode === 'globe') updateGlobeMarkers(geoData, indicator);
        else drawAtlas();

        const labels = {
            ocean_pollution_share: 'Ocean Pollution Share per negara (2019)',
            mismanaged_per_capita: 'Mismanaged Plastic Waste per Kapita (2019)',
            recycled_share:        'Recycling Rate per entitas (2019)',
        };
        document.getElementById('geo-sub').textContent = labels[indicator] || indicator;
        document.getElementById('gle-title').textContent = {
            ocean_pollution_share:'Ocean Pollution',
            mismanaged_per_capita:'Mismanaged/Kapita',
            recycled_share:'Recycling Rate'
        }[indicator] || indicator;
    } catch(e) { console.error('GEO:', e); }
}

/* ═══════════════════════════════════════════
   ATLAS (2D flat map)
═══════════════════════════════════════════ */
/* ── Atlas state ── */
let atlasWorld = null;   // cached TopoJSON world data
let atlasInited = false;

async function drawAtlas() {
    const mount = document.getElementById('atlas-mount');
    if (!mount || !geoData.length) return;

    // Remove old SVG if exists
    d3.select('#atlas-mount svg').remove();

    const W = mount.clientWidth;
    const H = mount.clientHeight;

    // Build value lookup from geoData
    const valueMap = {};
    geoData.forEach(r => {
        const v = parseFloat(r[currentIndicator]);
        if (!isNaN(v)) valueMap[r.entity] = v;
    });

    const vals = Object.values(valueMap);
    const mn = d3.min(vals), mx = d3.max(vals) || 1;

    // Color scale
    const colorScale = currentIndicator === 'recycled_share'
        ? d3.scaleSequential(d3.interpolateRdYlGn).domain([mn, mx])
        : d3.scaleSequential(d3.interpolateYlOrRd).domain([mn, mx]);

    // Projection — Natural Earth
    const projection = d3.geoNaturalEarth1()
        .scale(W / 6.3)
        .translate([W / 2, H / 2]);

    const path = d3.geoPath().projection(projection);

    // SVG setup
    const svg = d3.select('#atlas-mount')
        .append('svg')
        .attr('width', W)
        .attr('height', H)
        .style('display', 'block')
        .style('background', 'linear-gradient(180deg,#010b18 0%,#04223c 100%)');

    // Graticule (grid lines)
    const graticule = d3.geoGraticule()();
    svg.append('path')
        .datum(graticule)
        .attr('d', path)
        .attr('fill', 'none')
        .attr('stroke', 'rgba(56,189,248,0.08)')
        .attr('stroke-width', 0.5);

    // Load world TopoJSON once, then cache
    if (!atlasWorld) {
        try {
            atlasWorld = await d3.json('https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json');
        } catch(e) {
            svg.append('text').attr('x', W/2).attr('y', H/2)
               .attr('fill','#5ba8be').attr('text-anchor','middle')
               .text('Gagal load peta — cek koneksi internet');
            return;
        }
    }

    const countries = topojson.feature(atlasWorld, atlasWorld.objects.countries);

    // Country name mapping (ISO numeric → name in our data)
    // TopoJSON pakai ISO numeric ID, kita perlu lookup
    const isoToName = {
        4:'Afghanistan',8:'Albania',12:'Algeria',24:'Angola',32:'Argentina',
        51:'Armenia',36:'Australia',40:'Austria',31:'Azerbaijan',50:'Bangladesh',
        112:'Belarus',56:'Belgium',204:'Benin',68:'Bolivia',76:'Brazil',100:'Bulgaria',
        854:'Burkina Faso',108:'Burundi',116:'Cambodia',120:'Cameroon',124:'Canada',
        148:'Chad',152:'Chile',156:'China',170:'Colombia',174:'Comoros',188:'Costa Rica',
        191:'Croatia',384:"Cote d'Ivoire",192:'Cuba',203:'Czechia',
        180:'Democratic Republic of Congo',208:'Denmark',214:'Dominican Republic',
        218:'Ecuador',818:'Egypt',222:'El Salvador',231:'Ethiopia',246:'Finland',
        250:'France',266:'Gabon',276:'Germany',288:'Ghana',300:'Greece',320:'Guatemala',
        324:'Guinea',328:'Guyana',332:'Haiti',340:'Honduras',348:'Hungary',
        356:'India',360:'Indonesia',364:'Iran',368:'Iraq',372:'Ireland',380:'Italy',
        388:'Jamaica',392:'Japan',400:'Jordan',398:'Kazakhstan',404:'Kenya',
        434:'Libya',450:'Madagascar',454:'Malawi',458:'Malaysia',466:'Mali',
        484:'Mexico',498:'Moldova',504:'Morocco',508:'Mozambique',104:'Myanmar',
        524:'Nepal',528:'Netherlands',554:'New Zealand',558:'Nicaragua',562:'Niger',
        566:'Nigeria',578:'Norway',586:'Pakistan',591:'Panama',598:'Papua New Guinea',
        600:'Paraguay',604:'Peru',608:'Philippines',616:'Poland',620:'Portugal',
        642:'Romania',643:'Russia',646:'Rwanda',682:'Saudi Arabia',686:'Senegal',
        694:'Sierra Leone',706:'Somalia',710:'South Africa',410:'South Korea',
        724:'Spain',144:'Sri Lanka',729:'Sudan',740:'Suriname',752:'Sweden',
        756:'Switzerland',760:'Syria',158:'Taiwan',834:'Tanzania',764:'Thailand',
        768:'Togo',780:'Trinidad and Tobago',788:'Tunisia',792:'Turkey',800:'Uganda',
        804:'Ukraine',826:'United Kingdom',840:'United States',858:'Uruguay',
        860:'Uzbekistan',862:'Venezuela',704:'Vietnam',887:'Yemen',
        894:'Zambia',716:'Zimbabwe'
    };

    // Tooltip div
    let tooltip = d3.select('#atlas-tooltip');
    if (tooltip.empty()) {
        tooltip = d3.select('body').append('div')
            .attr('id', 'atlas-tooltip')
            .style('position','fixed')
            .style('background','rgba(3,15,30,0.92)')
            .style('border','1px solid rgba(56,189,248,0.3)')
            .style('border-radius','8px')
            .style('padding','8px 12px')
            .style('color','#e2f7ff')
            .style('font-size','12px')
            .style('font-family','Space Grotesk, sans-serif')
            .style('pointer-events','none')
            .style('display','none')
            .style('z-index','9999');
    }

    const indicatorLabel = {
        'ocean_pollution_share': 'Ocean Pollution Share',
        'risk_score': 'Risk Score',
        'mismanaged_per_capita': 'Mismanaged/Kapita (kg)',
        'recycled_share': 'Recycling Rate'
    };

    // Draw countries
    svg.selectAll('.country')
        .data(countries.features)
        .join('path')
        .attr('class', 'country')
        .attr('d', path)
        .attr('fill', d => {
            const name = isoToName[+d.id];
            const val = valueMap[name];
            return val != null ? colorScale(val) : 'rgba(30,60,90,0.5)';
        })
        .attr('stroke', 'rgba(56,189,248,0.15)')
        .attr('stroke-width', 0.4)
        .on('mousemove', function(event, d) {
            const name = isoToName[+d.id];
            const val = valueMap[name];
            d3.select(this).attr('stroke','rgba(56,189,248,0.8)').attr('stroke-width', 1.2);
            if (name) {
                const fmt = val != null
                    ? `<b>${name}</b><br>${indicatorLabel[currentIndicator]||currentIndicator}: <b>${
                        currentIndicator==='ocean_pollution_share'||currentIndicator==='recycled_share'||currentIndicator==='mismanaged_share'
                        ? (val*100).toFixed(2)+'%'
                        : val.toFixed(2)
                      }</b>`
                    : `<b>${name}</b><br><span style="color:#5ba8be">Tidak ada data</span>`;
                tooltip.style('display','block').html(fmt)
                    .style('left', (event.clientX+12)+'px')
                    .style('top',  (event.clientY-36)+'px');
            }
        })
        .on('mouseleave', function() {
            d3.select(this).attr('stroke','rgba(56,189,248,0.15)').attr('stroke-width', 0.4);
            tooltip.style('display','none');
        });

    // Country borders (inner)
    svg.append('path')
        .datum(topojson.mesh(atlasWorld, atlasWorld.objects.countries, (a,b) => a !== b))
        .attr('d', path)
        .attr('fill', 'none')
        .attr('stroke', 'rgba(56,189,248,0.12)')
        .attr('stroke-width', 0.3);

    // Legend
    const legendW = 160, legendH = 10;
    const lx = W - legendW - 16, ly = H - 36;

    const defs = svg.append('defs');
    const grad = defs.append('linearGradient').attr('id','atlas-legend-grad');
    const stops = currentIndicator === 'recycled_share'
        ? [['0%','#d73027'],['50%','#ffffbf'],['100%','#1a9850']]
        : [['0%','#ffffb2'],['50%','#fd8d3c'],['100%','#800026']];
    stops.forEach(([offset,color]) => grad.append('stop').attr('offset',offset).attr('stop-color',color));

    svg.append('rect').attr('x',lx).attr('y',ly)
        .attr('width',legendW).attr('height',legendH)
        .attr('rx',3).attr('fill','url(#atlas-legend-grad)');

    const fmtLegend = v => currentIndicator==='ocean_pollution_share'||currentIndicator==='recycled_share'
        ? (v*100).toFixed(1)+'%' : v.toFixed(1);

    svg.append('text').attr('x',lx).attr('y',ly-4)
        .attr('fill','#5ba8be').attr('font-size','9px').attr('font-family','Space Grotesk, sans-serif')
        .text(fmtLegend(mn));
    svg.append('text').attr('x',lx+legendW).attr('y',ly-4)
        .attr('fill','#5ba8be').attr('font-size','9px').attr('font-family','Space Grotesk, sans-serif')
        .attr('text-anchor','end').text(fmtLegend(mx));
    svg.append('text').attr('x',lx+legendW/2).attr('y',ly-4)
        .attr('fill','#94b8cc').attr('font-size','9px').attr('font-family','Space Grotesk, sans-serif')
        .attr('text-anchor','middle').text(indicatorLabel[currentIndicator]||currentIndicator);

    // Caption
    svg.append('text').attr('x',8).attr('y',H-8)
        .attr('fill','rgba(91,168,190,0.6)').attr('font-size','9px').attr('font-family','Space Grotesk, sans-serif')
        .text('Hover negara untuk detail · Data 2019');
}

/* ═══════════════════════════════════════════
   EVENT WIRING
═══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', async () => {

    // Init globe
    initThreeGlobe();

    // Load semua data paralel
    await Promise.all([loadKpi(), loadMis01(), loadMis02(), loadMis03(), loadGeo('ocean_pollution_share')]);

    // Toggle MIS-01
    document.getElementById('mis01-toggle').addEventListener('click', e=>{
        const b=e.target.closest('.tog'); if(!b) return;
        document.querySelectorAll('#mis01-toggle .tog').forEach(x=>x.classList.remove('active'));
        b.classList.add('active'); renderMis01(b.dataset.chart);
    });
    // Toggle MIS-02
    document.getElementById('mis02-toggle').addEventListener('click', e=>{
        const b=e.target.closest('.tog'); if(!b) return;
        document.querySelectorAll('#mis02-toggle .tog').forEach(x=>x.classList.remove('active'));
        b.classList.add('active'); renderMis02(b.dataset.chart);
    });
    // Toggle MIS-03
    document.getElementById('mis03-toggle').addEventListener('click', e=>{
        const b=e.target.closest('.tog'); if(!b) return;
        document.querySelectorAll('#mis03-toggle .tog').forEach(x=>x.classList.remove('active'));
        b.classList.add('active'); renderMis03(b.dataset.chart);
    });
    // Globe ↔ Atlas
    document.getElementById('btn-globe').addEventListener('click', ()=>{
        geoMode='globe';
        document.getElementById('btn-globe').classList.add('active');
        document.getElementById('btn-atlas').classList.remove('active');
        document.getElementById('globe-mount').style.display='';
        document.getElementById('atlas-mount').style.display='none';
    });
    document.getElementById('btn-atlas').addEventListener('click', ()=>{
        geoMode='atlas';
        document.getElementById('btn-atlas').classList.add('active');
        document.getElementById('btn-globe').classList.remove('active');
        document.getElementById('globe-mount').style.display='none';
        document.getElementById('atlas-mount').style.display='';
        setTimeout(drawAtlas, 60);
    });
    // Indicator tabs
    document.getElementById('ind-tabs').addEventListener('click', e=>{
        const b=e.target.closest('.ind-tab'); if(!b) return;
        document.querySelectorAll('.ind-tab').forEach(x=>x.classList.remove('active'));
        b.classList.add('active');
        loadGeo(b.dataset.ind);
    });
    // Resize rerender
    let rt;
    window.addEventListener('resize', ()=>{
        clearTimeout(rt); rt=setTimeout(()=>{
            renderMis01(document.querySelector('#mis01-toggle .tog.active')?.dataset.chart||'line');
            renderMis02(document.querySelector('#mis02-toggle .tog.active')?.dataset.chart||'stacked');
            renderMis03(document.querySelector('#mis03-toggle .tog.active')?.dataset.chart||'hbar');
            if(geoMode==='atlas') drawAtlas();
        },200);
    });
});
</script>
@endpush