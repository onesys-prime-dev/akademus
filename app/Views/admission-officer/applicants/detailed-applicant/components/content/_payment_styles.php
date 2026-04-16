<style>
/* ── Shared Payment Review Styles ─────────────────────────── */

/* Empty state */
.pr-empty{padding:52px 24px;text-align:center;}
.pr-empty__icon{width:52px;height:52px;border-radius:50%;background:#f8fafc;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;}
.pr-empty__title{font-size:14px;font-weight:700;color:#2d3a4f;margin-bottom:5px;}
.pr-empty__desc{font-size:13px;color:#94a3b8;line-height:1.6;}

/* Status banner */
.pr-status-banner{display:flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;font-size:12px;font-weight:600;margin-bottom:20px;flex-wrap:wrap;}
.pr-status-banner--pending {background:#fffbeb;color:#92400e;border:1px solid #fde68a;}
.pr-status-banner--approved{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;}
.pr-status-banner--rejected{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;}
.pr-status-banner svg{flex-shrink:0;}
.pr-status-banner__by{margin-left:auto;font-size:11px;font-weight:500;opacity:.75;}

/* Two-column layout */
.pr-layout{display:grid;grid-template-columns:1fr 220px;gap:24px;align-items:start;margin-bottom:0;}
@media(max-width:640px){.pr-layout{grid-template-columns:1fr;}}

/* Section label */
.pr-section-label{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:12px;}

/* Info grid */
.pr-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px;}
.pr-info-item{}
.pr-info-item--full{grid-column:1/-1;}
.pr-info-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-bottom:3px;}
.pr-info-value{font-size:13px;font-weight:600;color:#1e293b;}
.pr-info-value--amount{font-size:18px;font-weight:800;color:#0d1e38;}
.pr-info-value--mono{font-family:ui-monospace,monospace;font-size:12px;background:#f8fafc;padding:3px 7px;border-radius:4px;border:1px solid #e2e8f0;display:inline-block;}
.pr-info-sub{font-size:11px;color:#94a3b8;margin-top:1px;}

/* Proof of payment */
.pr-proof-col{}
.pr-proof-thumb{position:relative;border-radius:8px;overflow:hidden;border:1px solid #e2e8f0;cursor:pointer;background:#f8fafc;aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;}
.pr-proof-thumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .2s;}
.pr-proof-thumb:hover img{transform:scale(1.03);}
.pr-proof-thumb__overlay{position:absolute;inset:0;background:rgba(0,0,0,.35);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;opacity:0;transition:opacity .18s;font-size:12px;font-weight:700;color:#fff;}
.pr-proof-thumb:hover .pr-proof-thumb__overlay{opacity:1;}
.pr-proof-open-link{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#64748b;text-decoration:none;margin-top:8px;}
.pr-proof-open-link:hover{color:var(--primary,#0040a6);}
.pr-proof-file-btn{display:flex;align-items:center;gap:12px;padding:14px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;text-decoration:none;color:#1e293b;transition:border-color .15s,background .15s;}
.pr-proof-file-btn:hover{border-color:var(--primary,#0040a6);background:#e8f0ff;}
.pr-proof-none{font-size:12px;color:#94a3b8;padding:14px 0;}

/* Action area */
.pr-action-area{margin-top:20px;border-top:1px solid #e2e8f0;padding-top:20px;}
.pr-action-header{display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:#475569;margin-bottom:14px;}
.pr-action-body{}
.pr-remarks-label{font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px;}
.pr-remarks-label span{font-weight:400;color:#94a3b8;}
.pr-remarks-input{width:100%;border:1px solid #cbd5e0;border-radius:7px;padding:9px 12px;font-size:13px;color:#1e293b;resize:vertical;font-family:inherit;line-height:1.5;transition:border-color .15s,box-shadow .15s;}
.pr-remarks-input:focus{outline:none;border-color:var(--primary,#0040a6);box-shadow:0 0 0 3px rgba(0,64,166,.1);}
.pr-action-err{font-size:12px;color:#ef4444;min-height:16px;margin-top:6px;}
.pr-action-btns{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;}
.pr-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:7px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:opacity .15s,transform .1s;}
.pr-btn:active{transform:scale(.97);}
.pr-btn:disabled{opacity:.45;cursor:not-allowed;}
.pr-btn--approve{background:#22c55e;color:#fff;}
.pr-btn--reject{background:transparent;color:#ef4444;border:1.5px solid #fca5a5;}
.pr-btn:hover:not(:disabled){opacity:.88;}

/* Lightbox */
.pr-lightbox{position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:99999;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s;}
.pr-lightbox.active{opacity:1;pointer-events:all;}
.pr-lightbox img{max-width:90vw;max-height:88vh;border-radius:8px;box-shadow:0 24px 64px rgba(0,0,0,.5);}
.pr-lightbox__close{position:absolute;top:18px;right:22px;background:rgba(255,255,255,.12);border:none;color:#fff;font-size:24px;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;}
.pr-lightbox__close:hover{background:rgba(255,255,255,.25);}
</style>
