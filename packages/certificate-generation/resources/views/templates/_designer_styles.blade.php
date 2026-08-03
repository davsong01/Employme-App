<style>
    <?php
        $certificateFonts = config('certificates.fonts', []);
        foreach ($certificateFonts as $filename => $font) {
            $fontFamily = 'Certificate '.str_replace(['-', '_'], ' ', pathinfo($filename, PATHINFO_FILENAME));
            $fontUrl = asset('certificate_fonts/' . $filename);
            echo "@font-face {\n";
            echo "    font-family: '{$fontFamily}';\n";
            echo "    src: url('{$fontUrl}') format('truetype');\n";
            echo "    font-style: normal;\n";
            echo "    font-weight: normal;\n";
            echo "    font-display: swap;\n";
            echo "}\n";
        }
    ?>

    .certificate-designer {
        --designer-bg: #0f172a;
        --designer-panel: #ffffff;
        --designer-muted: #6b7280;
        --designer-border: #e5e7eb;
        --designer-accent: #2563eb;
        --designer-accent-soft: rgba(37, 99, 235, .08);
        min-height: calc(100vh - 140px);
    }

    .certificate-designer .designer-toolbar,
    .certificate-designer .designer-top-card,
    .certificate-designer .designer-panel,
    .certificate-designer .designer-stage-card {
        border: 1px solid var(--designer-border);
        border-radius: 18px;
        background: var(--designer-panel);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .certificate-designer .designer-top-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    }

    .certificate-designer .designer-toolbar .btn {
        border-radius: 12px;
    }

    .certificate-designer .badge {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: auto !important;
        height: auto !important;
        min-width: 0;
        padding: .35rem .6rem;
        border-radius: .35rem !important;
        box-shadow: none;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }

    .certificate-designer .designer-shell {
        margin-bottom: 1rem;
    }

    .certificate-designer .designer-panel {
        position: sticky;
        top: 1rem;
        max-height: calc(100vh - 180px);
        overflow: auto;
        background: transparent;
        border: 0;
        box-shadow: none;
    }

    .certificate-designer .designer-panel .panel-card {
        border: 1px solid var(--designer-border);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        overflow: hidden;
    }

    .certificate-designer .designer-panel .panel-card + .panel-card {
        margin-top: .85rem;
    }

    .certificate-designer .panel-header {
        padding: .8rem .9rem .65rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-bottom: 1px solid var(--designer-border);
    }

    .certificate-designer .panel-header .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #2563eb;
        font-weight: 700;
    }

    .certificate-designer .panel-header h6 {
        margin: .25rem 0 0;
        font-size: .95rem;
        font-weight: 700;
        color: #0f172a;
    }

    .certificate-designer .panel-header p {
        margin: .25rem 0 0;
        font-size: .8rem;
        color: var(--designer-muted);
        line-height: 1.4;
    }

    .certificate-designer .panel-body {
        padding: .75rem .85rem .9rem;
    }

    .certificate-designer .panel-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .3rem;
        margin-top: .45rem;
    }

    .certificate-designer .panel-chip {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .25rem .5rem;
        border-radius: 999px;
        background: #eef4ff;
        border: 1px solid #dbeafe;
        color: #2252b7;
        font-size: .72rem;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    .certificate-designer .designer-panel .btn {
        min-height: 32px;
        padding: .32rem .65rem;
        font-size: .86rem;
        border-radius: 12px;
    }

    .certificate-designer .palette-item {
        border: 1px dashed #d7dde8;
        border-radius: 14px;
        background: #fafcff;
        padding: 0;
        cursor: grab;
        transition: all .16s ease;
        font-size: .88rem;
    }

    .certificate-designer .palette-item:hover {
        border-color: var(--designer-accent);
        background: var(--designer-accent-soft);
        transform: translateY(-1px);
    }

    .certificate-designer .palette-item .palette-chip {
        display: inline-flex;
        align-items: center;
        margin-top: 0;
        padding: .16rem .4rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, .08);
        color: #2563eb;
        font-size: .68rem;
        font-weight: 700;
        line-height: 1;
    }

    .certificate-designer .palette-item small {
        color: var(--designer-muted);
        font-size: .76rem;
    }

    .certificate-designer .designer-workspace {
        min-width: 0;
    }

    .certificate-designer .canvas-shell {
        position: relative;
        background: linear-gradient(180deg, #f9fbff 0%, #eef3fb 100%);
        border-radius: 20px;
        padding: 1rem;
        border: 1px solid var(--designer-border);
    }

    .certificate-designer .canvas-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: .75rem;
    }

    .certificate-designer .canvas-viewer {
        overflow: auto;
        min-height: 680px;
        padding: .5rem;
        border-radius: 18px;
        background:
            linear-gradient(90deg, rgba(255,255,255,.55), rgba(255,255,255,.35)),
            repeating-linear-gradient(0deg, rgba(2, 6, 23, .03) 0, rgba(2, 6, 23, .03) 1px, transparent 1px, transparent 22px),
            repeating-linear-gradient(90deg, rgba(2, 6, 23, .03) 0, rgba(2, 6, 23, .03) 1px, transparent 1px, transparent 22px);
    }

    .certificate-designer .canvas-viewer > .d-flex.align-items-start.gap-0 {
        min-height: 100%;
        width: 100%;
    }

    .certificate-designer #canvasStageHost {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100%;
        padding: 1rem 0;
    }

    .certificate-designer .canvas-stage {
        position: relative;
        background: #fff;
        border: 1px solid rgba(148, 163, 184, .5);
        box-shadow: 0 18px 50px rgba(15, 23, 42, .12);
        overflow: hidden;
        transform-origin: top left;
    }

    .certificate-designer .canvas-background {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center center;
        user-select: none;
        pointer-events: none;
        background: #fff;
    }

    .certificate-designer .canvas-overlay {
        position: absolute;
        inset: 0;
    }

    .certificate-designer .guide-line {
        position: absolute;
        pointer-events: none;
        z-index: 20;
        background: rgba(37, 99, 235, .7);
        box-shadow: 0 0 0 1px rgba(37, 99, 235, .15);
    }

    .certificate-designer .guide-line.vertical {
        top: 0;
        bottom: 0;
        width: 1px;
    }

    .certificate-designer .guide-line.horizontal {
        left: 0;
        right: 0;
        height: 1px;
    }

    .certificate-designer .preview-modal-dialog {
        width: min(95vw, 1600px);
        max-width: min(95vw, 1600px);
    }

    .certificate-designer .preview-modal-image-wrap {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .certificate-designer #certificatePreviewImage {
        max-width: 100%;
        max-height: 78vh;
        object-fit: contain;
    }

    .certificate-designer .certificate-element {
        position: absolute;
        box-sizing: border-box;
        border: 1px solid transparent;
        cursor: move;
        user-select: none;
        touch-action: none;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .certificate-designer .certificate-element.is-selected {
        border-color: var(--designer-accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
    }

    .certificate-designer .certificate-element.is-locked {
        cursor: not-allowed;
    }

    .certificate-designer .element-body {
        width: 100%;
        height: 100%;
        overflow: hidden;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        background: transparent;
    }

    .certificate-designer .element-body.is-qr {
        background: rgba(15, 23, 42, .04);
        border: 1px dashed rgba(15, 23, 42, .2);
        border-radius: 10px;
        align-items: center;
        justify-content: center;
        color: #0f172a;
    }

    .certificate-designer .element-body .element-text {
        display: block;
        width: 100%;
        padding: 0;
        line-height: 1;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .certificate-designer .element-handle {
        position: absolute;
        right: -5px;
        bottom: -5px;
        width: 11px;
        height: 11px;
        background: #fff;
        border: 1px solid var(--designer-accent);
        border-radius: 50%;
        cursor: nwse-resize;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .15);
    }

    .certificate-designer .element-tag {
        position: absolute;
        left: 0;
        top: -1.6rem;
        background: rgba(15, 23, 42, .9);
        color: #fff;
        font-size: .70rem;
        padding: .15rem .45rem;
        border-radius: 999px;
        white-space: nowrap;
        pointer-events: none;
    }

    .certificate-designer .ruler-top,
    .certificate-designer .ruler-left {
        position: sticky;
        background:
            linear-gradient(to right, rgba(148, 163, 184, .45) 1px, transparent 1px) 0 100%/50px 100%,
            linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        z-index: 3;
    }

    .certificate-designer .ruler-top {
        height: 24px;
        border-bottom: 1px solid rgba(148, 163, 184, .4);
        margin-bottom: .5rem;
    }

    .certificate-designer .ruler-left {
        width: 24px;
        border-right: 1px solid rgba(148, 163, 184, .4);
        background:
            linear-gradient(to bottom, rgba(148, 163, 184, .45) 1px, transparent 1px) 100% 0/100% 50px,
            linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    }

    .certificate-designer .muted-help {
        color: var(--designer-muted);
        font-size: .85rem;
    }

    .certificate-designer .module-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .55rem;
        border-radius: 999px;
        background: #eef4ff;
        color: #2252b7;
        font-size: .78rem;
    }

    .certificate-designer .context-toolbar-floating {
        position: absolute;
        z-index: 40;
        width: min(680px, calc(100% - 1.25rem));
        pointer-events: none;
        transform: translate3d(0, 0, 0);
        transition: top .15s ease, left .15s ease, opacity .15s ease;
    }

    .certificate-designer .context-toolbar-floating .card {
        pointer-events: auto;
        border: 1px solid rgba(37, 99, 235, .18);
        border-radius: 16px;
        background: rgba(255, 255, 255, .98);
        box-shadow: 0 16px 42px rgba(15, 23, 42, .14);
        backdrop-filter: blur(8px);
    }

    .certificate-designer .context-toolbar-floating .form-label {
        margin-bottom: .2rem;
        font-size: .74rem;
        font-weight: 600;
        color: #475569;
    }

    .certificate-designer .context-toolbar-floating .card-body {
        padding: .75rem .8rem !important;
    }

    .certificate-designer .context-toolbar-floating .btn-sm {
        padding: .27rem .5rem;
        line-height: 1.1;
    }

    .certificate-designer .context-toolbar-floating .form-control-sm,
    .certificate-designer .context-toolbar-floating .form-select-sm {
        min-height: calc(1.45rem + 2px);
        padding-top: .18rem;
        padding-bottom: .18rem;
        font-size: .8rem;
    }

    .certificate-designer .context-toolbar-floating .compact-toggle {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        margin: 0;
        font-size: .76rem;
    }

    .certificate-designer .context-toolbar-floating .compact-toggle .form-check-input {
        margin-top: 0;
    }

    .certificate-designer .context-toolbar-floating .color-control {
        display: flex;
        gap: .4rem;
        align-items: center;
    }

    .certificate-designer .context-toolbar-floating .color-control input[type="color"] {
        width: 2.15rem;
        min-width: 2.15rem;
        height: 1.95rem;
        padding: 0;
        border-radius: .5rem;
        overflow: hidden;
        flex: 0 0 auto;
    }

    .certificate-designer .context-toolbar-floating .color-control .hex-input {
        flex: 1 1 auto;
        min-width: 96px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        text-transform: uppercase;
    }

    .certificate-designer .context-toolbar-floating .inspector-row {
        display: grid;
        gap: .55rem;
    }

    .certificate-designer .context-toolbar-floating .inspector-row + .inspector-row {
        margin-top: .6rem;
        padding-top: .6rem;
        border-top: 1px solid rgba(148, 163, 184, .22);
    }

    .certificate-designer .context-toolbar-floating .inspector-meta {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem;
    }

    .certificate-designer .context-toolbar-floating .inspector-meta .btn-list {
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: .35rem;
    }

    .certificate-designer .context-toolbar-floating .inspector-row-fields {
        display: grid;
        gap: .6rem;
        grid-template-columns: minmax(0, 1.4fr) minmax(88px, .55fr) minmax(88px, .55fr) minmax(0, 1fr) minmax(88px, .55fr);
    }

    .certificate-designer .context-toolbar-floating .inspector-row-controls {
        display: grid;
        gap: .55rem;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
    }

    .certificate-designer .context-toolbar-floating .toggle-list {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem .75rem;
        align-items: center;
    }

    @media (max-width: 1199.98px) {
        .certificate-designer .context-toolbar-floating .inspector-row-fields,
        .certificate-designer .context-toolbar-floating .inspector-row-controls {
            grid-template-columns: 1fr 1fr;
        }
    }

    .certificate-designer .inline-text-editor {
        position: absolute;
        z-index: 25;
        border: 2px solid var(--designer-accent);
        border-radius: 10px;
        background: rgba(255, 255, 255, .96);
        padding: 0;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .18);
        resize: none;
        outline: none;
        font-family: inherit;
    }

    @if($useBootstrap4)
        .certificate-designer .designer-top-card .card-body,
        .certificate-designer .designer-toolbar .card-body,
        .certificate-designer .designer-panel .panel-body,
        .certificate-designer .canvas-shell,
        .certificate-designer .canvas-toolbar,
        .certificate-designer .card-header,
        .certificate-designer .card-footer {
            padding: .8rem !important;
        }
        .certificate-designer .designer-top-card,
        .certificate-designer .designer-toolbar,
        .certificate-designer .canvas-shell {
            border-radius: 12px;
        }
        .certificate-designer .designer-top-card .btn,
        .certificate-designer .designer-toolbar .btn,
        .certificate-designer .designer-panel .btn,
        .certificate-designer .canvas-toolbar .btn,
        .certificate-designer .btn-list .btn {
            padding: .28rem .55rem;
            line-height: 1.1;
            min-height: 30px;
        }
        .certificate-designer .btn-list {
            gap: .35rem !important;
        }
        .certificate-designer .designer-top-card .btn-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .certificate-designer .panel-card + .panel-card {
            margin-top: .65rem;
        }
        .certificate-designer .panel-header {
            padding: .65rem .75rem .5rem;
        }
        .certificate-designer .panel-body {
            padding: .65rem .75rem .75rem;
        }
        .certificate-designer .panel-header h6,
        .certificate-designer .canvas-toolbar .fw-semibold,
        .certificate-designer .page-title {
            margin-bottom: .25rem;
        }
        .certificate-designer .canvas-shell {
            padding: .75rem;
        }
        .certificate-designer .canvas-toolbar {
            margin-bottom: .65rem;
        }
        .certificate-designer .designer-toolbar .row,
        .certificate-designer .designer-top-card .d-flex,
        .certificate-designer .canvas-toolbar {
            align-items: flex-start;
        }
        .certificate-designer .me-1 { margin-right: .25rem !important; }
        .certificate-designer .me-2 { margin-right: .5rem !important; }
        .certificate-designer .ms-1 { margin-left: .25rem !important; }
        .certificate-designer .ms-2 { margin-left: .5rem !important; }
        .certificate-designer .gap-1 > * + * { margin-left: .25rem !important; margin-top: .25rem !important; }
        .certificate-designer .gap-2 > * + * { margin-left: .5rem !important; margin-top: .5rem !important; }
        .certificate-designer .gap-3 > * + * { margin-left: 1rem !important; margin-top: 1rem !important; }
        .certificate-designer .gap-4 > * + * { margin-left: 1.5rem !important; margin-top: 1.5rem !important; }
        .certificate-designer .badge.bg-blue-lt,
        .certificate-designer .badge.text-blue {
            background: #e7f1ff !important;
            color: #0d6efd !important;
        }
        .certificate-designer .form-switch {
            padding-left: 0;
        }
        .certificate-designer .form-switch .form-check-input {
            margin-left: 0;
            position: static;
        }
    @endif

    @media (max-width: 1400px) {
        .certificate-designer .designer-shell {
            grid-template-columns: 1fr;
        }

        .certificate-designer .designer-panel {
            position: static;
            max-height: none;
        }
    }
</style>
