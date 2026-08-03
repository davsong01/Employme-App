<script>
(() => {
    const designer = document.getElementById('certificateDesigner');
    if (!designer) {
        return;
    }

    const form = designer.closest('form');
    const settingsField = document.getElementById('certificateSettingsJson');
    const backgroundInput = document.getElementById('certificateBackgroundUpload');
    const backgroundButton = document.getElementById('certificateBackgroundButton');
    const backgroundLabel = document.getElementById('certificateBackgroundLabel');
    const backgroundImage = document.getElementById('canvasBackgroundImage');
    const canvasShell = document.querySelector('.certificate-designer .canvas-shell');
    const canvasStage = document.getElementById('canvasStage');
    const canvasViewport = document.getElementById('canvasViewport');
    const canvasOverlay = document.getElementById('canvasOverlay');
    const paletteContainer = document.getElementById('paletteContainer');
    const libraryGroupChips = document.getElementById('libraryGroupChips');
    const canvasSummary = document.getElementById('canvasSummary');
    const canvasDimensionLabel = document.getElementById('canvasDimensionLabel');
    const zoomLabel = document.getElementById('zoomLabel');
    const gridStateLabel = document.getElementById('gridStateLabel');
    const snapStateLabel = document.getElementById('snapStateLabel');
    const guidesStateLabel = document.getElementById('guidesStateLabel');
    const libraryCountChip = document.getElementById('libraryCountChip');
    const contextToolbar = document.getElementById('contextToolbar');
    const contextToolbarTitle = document.getElementById('contextToolbarTitle');
    const contextToolbarSubtitle = document.getElementById('contextToolbarSubtitle');
    const contextFont = document.getElementById('contextFont');
    const contextFontSize = document.getElementById('contextFontSize');
    const contextFontWeight = document.getElementById('contextFontWeight');
    const contextColorHex = document.getElementById('contextColorHex');
    const contextColor = document.getElementById('contextColor');
    const contextAlign = document.getElementById('contextAlign');
    const contextOpacity = document.getElementById('contextOpacity');
    const contextBold = document.getElementById('contextBold');
    const contextItalic = document.getElementById('contextItalic');
    const contextUppercase = document.getElementById('contextUppercase');
    const contextVisible = document.getElementById('contextVisible');
    const contextLocked = document.getElementById('contextLocked');
    const contextQrGroup = document.getElementById('contextQrGroup');
    const contextQrSize = document.getElementById('contextQrSize');
    const previewModalElement = document.getElementById('certificatePreviewModal');
    const previewImage = document.getElementById('certificatePreviewImage');
    const previewError = document.getElementById('certificatePreviewError');
    let inlineEditor = null;
    let inlineEditorTarget = null;

    const textTypeLabels = @json($flatTextTypeOptions);
    const fontOptions = @json($fontOptions);
    const previewRoute = designer.dataset.previewUrl;
    const defaultCanvas = @json($canvasDefaults);
    const designerElementDefaults = @json($elementDefaults);
    const designerGridSize = Math.max(1, Number(@json(config('certificates.designer.grid_size', 10))) || 10);

    const sampleValues = @json(config('certificates.sample_data', []));
    const issuedDateSample = @json(\Carbon\Carbon::parse('2026-08-22')->format(config('certificates.rendering.issued_date_format', 'jS \\d\\a\\y \\o\\f F, Y')));
    const elementLibrary = @json($elementLibrary);
    const paletteGroups = @json($paletteGroups);
    const state = {
        zoom: 1,
        canvas: { ...defaultCanvas },
        elements: [],
        selectedId: null,
        gridEnabled: true,
        snapEnabled: true,
        guidesEnabled: true,
        clipboard: null,
        dragging: null,
        resizing: null,
        backgroundUrl: backgroundImage?.getAttribute('src') || '',
        canvasExplicit: false,
        activeColor: '#000000',
    };

    const initialSettings = (() => {
        try {
            return JSON.parse(settingsField.value || '{}');
        } catch (error) {
            return {};
        }
    })();

    if (initialSettings.canvas && typeof initialSettings.canvas === 'object') {
        state.canvas = {
            width: Math.max(1, parseInt(initialSettings.canvas.width ?? defaultCanvas.width, 10)),
            height: Math.max(1, parseInt(initialSettings.canvas.height ?? defaultCanvas.height, 10)),
            orientation: initialSettings.canvas.orientation ?? defaultCanvas.orientation,
        };
        state.canvasExplicit = true;
    }

    state.elements = Array.isArray(initialSettings.elements)
        ? initialSettings.elements.map((item) => normalizeElement(item))
        : [];

    if (! state.backgroundUrl) {
        backgroundImage.style.display = 'none';
    }

    canvasSummary.textContent = `${state.canvas.width} × ${state.canvas.height}`;
    canvasDimensionLabel.textContent = `${state.canvas.width} × ${state.canvas.height}`;

    function uuid() {
        return (window.crypto && typeof window.crypto.randomUUID === 'function')
            ? window.crypto.randomUUID()
            : `element_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
    }

    function clone(value) {
        return JSON.parse(JSON.stringify(value));
    }

    function tightTextHeight(fontSize, lineHeight = 1.2) {
        const size = Math.max(8, Number(fontSize) || 8);
        const height = Math.round(size * Math.max(1, Number(lineHeight) || 1));

        return Math.max(18, height);
    }

    function normalizeElement(element) {
        const font = element.font || element.text_type_face || Object.keys(fontOptions)[0] || 'Times New Roman.ttf';
        const opacity = element.opacity ?? element.auto_certificate_opacity ?? designerElementDefaults.opacity ?? 1;
        const height = parseInt(element.height ?? designerElementDefaults.height ?? 80, 10);
        const fontSize = parseInt(element.font_size ?? element.auto_certificate_name_font_size ?? designerElementDefaults.font_size ?? 36, 10);
        const lineHeight = parseFloat(element.line_height ?? designerElementDefaults.line_height ?? 1.2);
        const isTextElement = (element.text_type || 'custom_text') !== 'qr_code';
        return {
            id: element.id || uuid(),
            text_type: element.text_type || 'custom_text',
            label: element.label || textTypeLabels[element.text_type] || 'Custom Text',
            font,
            text_type_face: font,
            color: element.color || element.auto_certificate_color || designerElementDefaults.color || '#000000',
            top: parseInt(element.top ?? element.auto_certificate_top_offset ?? 0, 10),
            left: parseInt(element.left ?? element.auto_certificate_left_offset ?? 0, 10),
            width: parseInt(element.width ?? designerElementDefaults.width ?? 320, 10),
            height: isTextElement && (! element.height || height === (designerElementDefaults.height ?? 80))
                ? tightTextHeight(fontSize, lineHeight)
                : height,
            size: parseInt(element.size ?? Math.max(parseInt(element.width ?? 0, 10), parseInt(element.height ?? 0, 10), designerElementDefaults.size ?? 120), 10),
            font_size: fontSize,
            font_weight: parseInt(element.font_weight ?? element.auto_certificate_name_font_weight ?? designerElementDefaults.font_weight ?? 400, 10),
            align: element.align || element.text_align || designerElementDefaults.align || 'left',
            rotation: parseFloat(element.rotation ?? 0),
            opacity: parseFloat(opacity) > 1 ? parseFloat(opacity) / 100 : parseFloat(opacity),
            visible: !!(element.visible ?? true),
            uppercase: !!(element.uppercase ?? false),
            bold: !!(element.bold ?? false),
            italic: !!(element.italic ?? false),
            line_height: parseFloat(element.line_height ?? designerElementDefaults.line_height ?? 1.2),
            letter_spacing: parseFloat(element.letter_spacing ?? designerElementDefaults.letter_spacing ?? 0),
            z_index: parseInt(element.z_index ?? 1, 10),
            locked: !!(element.locked ?? false),
            sample_text: element.sample_text ?? null,
            custom_text: element.custom_text ?? null,
        };
    }

    function setStateFromSettings(settings) {
        state.canvas = {
            width: Math.max(1, parseInt(settings.canvas?.width ?? defaultCanvas.width, 10)),
            height: Math.max(1, parseInt(settings.canvas?.height ?? defaultCanvas.height, 10)),
            orientation: settings.canvas?.orientation ?? defaultCanvas.orientation,
        };
        state.canvasExplicit = true;
        state.elements = Array.isArray(settings.elements) ? settings.elements.map(normalizeElement) : [];
        state.selectedId = state.elements[0]?.id ?? null;
        syncUI();
    }

    function syncUI() {
        renderPalette();
        syncContextToolbar();
        render();
    }

    function syncSettingsField() {
        settingsField.value = JSON.stringify({
            canvas: {
                width: Math.max(1, Math.round(state.canvas.width)),
                height: Math.max(1, Math.round(state.canvas.height)),
                orientation: state.canvas.orientation || 'landscape',
            },
            elements: state.elements.map((item) => ({
                id: item.id,
                text_type: item.text_type,
                label: item.label,
                font: item.font,
                text_type_face: item.text_type_face,
                color: item.color,
                top: Math.round(item.top),
                left: Math.round(item.left),
                width: Math.round(item.width),
                height: Math.round(item.height),
                size: Math.round(item.size),
                font_size: Math.round(item.font_size),
                font_weight: Math.round(item.font_weight),
                align: item.align,
                rotation: Number(item.rotation) || 0,
                opacity: Number(item.opacity) ?? 1,
                visible: !!item.visible,
                uppercase: !!item.uppercase,
                bold: !!item.bold,
                italic: !!item.italic,
                line_height: Number(item.line_height) || 1.2,
                letter_spacing: Number(item.letter_spacing) || 0,
                z_index: Number(item.z_index) || 1,
                locked: !!item.locked,
                sample_text: item.sample_text,
                custom_text: item.custom_text,
            })),
        });
    }

    function selectedElement() {
        return state.elements.find((item) => item.id === state.selectedId) || null;
    }

    function clampElement(element) {
        element.width = Math.max(20, Number(element.width) || 20);
        element.height = Math.max(20, Number(element.height) || 20);
        element.size = Math.max(60, Number(element.size) || 60);
        element.font_size = Math.max(8, Number(element.font_size) || 8);

        const maxLeft = Math.max(0, state.canvas.width - element.width);
        const maxTop = Math.max(0, state.canvas.height - element.height);
        element.left = Math.min(Math.max(0, Number(element.left) || 0), maxLeft);
        element.top = Math.min(Math.max(0, Number(element.top) || 0), maxTop);

        if (element.text_type === 'qr_code') {
            const qrMaxLeft = Math.max(0, state.canvas.width - element.size);
            const qrMaxTop = Math.max(0, state.canvas.height - element.size);
            element.left = Math.min(Math.max(0, Number(element.left) || 0), qrMaxLeft);
            element.top = Math.min(Math.max(0, Number(element.top) || 0), qrMaxTop);
        }

        return element;
    }

    function snapValue(value) {
        if (! state.snapEnabled) {
            return value;
        }

        const grid = designerGridSize;
        return Math.round(value / grid) * grid;
    }

    function activeModules() {
        return Array.from(document.querySelectorAll('.js-module-toggle:checked')).map((input) => input.value);
    }

    function allowedForGroup(group) {
        return ! group.module || activeModules().includes(group.module);
    }

    function renderPalette() {
        if (! paletteContainer) {
            return;
        }

        paletteContainer.innerHTML = '';
        let visibleItemCount = 0;
        const visibleGroups = paletteGroups.filter(allowedForGroup);

        visibleGroups.forEach((group) => {
            const items = Array.isArray(group.items) ? group.items : [];
            const groupCount = items.length;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <div class="fw-semibold">${group.label}</div>
                    <span class="panel-chip">${groupCount} item${groupCount === 1 ? '' : 's'}</span>
                </div>
                <div class="d-grid gap-2 mb-3"></div>
            `;
            const list = wrapper.querySelector('.d-grid');

            items.forEach((item) => {
                visibleItemCount++;
                const el = document.createElement('div');
                el.className = 'palette-item';
                el.draggable = true;
                el.dataset.textType = item.text_type;
                el.dataset.label = item.label;
                el.dataset.sampleText = item.sample_text || '';
                el.innerHTML = `
                    <div class="fw-semibold">${item.icon ? `<i class="${item.icon} me-1"></i>` : ''}${item.label}</div>
                    <small>${elementLibrary.item_action || 'Drag to canvas'}</small>
                    <div class="palette-chip">${item.text_type.replace(/_/g, ' ')}</div>
                `;
                el.addEventListener('dragstart', (event) => {
                    event.dataTransfer.setData('text/plain', JSON.stringify(item));
                });
                el.addEventListener('click', () => {
                    placeElement(item, state.canvas.width / 2 - 140, state.canvas.height / 2 - 40);
                });
                list.appendChild(el);
            });

            paletteContainer.appendChild(wrapper);
        });

        if (libraryCountChip) {
            libraryCountChip.textContent = `${visibleItemCount} item${visibleItemCount === 1 ? '' : 's'}`;
        }

        if (libraryGroupChips) {
            libraryGroupChips.innerHTML = visibleGroups
                .map((group) => `<span class="panel-chip">${group.label}</span>`)
                .join('');
        }

        if (visibleItemCount === 0) {
            paletteContainer.innerHTML = `<div class="muted-help">${elementLibrary.empty_message || 'No certificate elements are configured.'}</div>`;
        }
    }

    function objectPreviewText(item) {
        if (item.text_type === 'qr_code') {
            return 'QR';
        }

        if (item.text_type === 'date_issued') {
            return issuedDateSample || sampleValues.date_issued || item.sample_text || item.label || textTypeLabels[item.text_type] || 'Date Issued';
        }

        if (item.text_type === 'custom_text') {
            return item.custom_text || item.sample_text || item.label;
        }

        return item.sample_text || sampleValues[item.text_type] || item.label || textTypeLabels[item.text_type] || 'Text';
    }

    function render() {
        syncSettingsField();

        const width = Math.round(state.canvas.width * state.zoom);
        const height = Math.round(state.canvas.height * state.zoom);

        canvasStage.style.width = `${width}px`;
        canvasStage.style.height = `${height}px`;
        canvasSummary.textContent = `${state.canvas.width} × ${state.canvas.height}`;
        canvasDimensionLabel.textContent = `${state.canvas.width} × ${state.canvas.height}`;
        zoomLabel.textContent = `${Math.round(state.zoom * 100)}%`;
        gridStateLabel.textContent = state.gridEnabled ? 'On' : 'Off';
        snapStateLabel.textContent = state.snapEnabled ? 'On' : 'Off';
        guidesStateLabel.textContent = state.guidesEnabled ? 'On' : 'Off';

        if (backgroundImage.getAttribute('src')) {
            backgroundImage.style.display = 'block';
        }

        const gridStep = Math.max(18, Math.round(50 * state.zoom));
        canvasStage.style.backgroundImage = state.gridEnabled
            ? `repeating-linear-gradient(0deg, rgba(2, 6, 23, .04) 0, rgba(2, 6, 23, .04) 1px, transparent 1px, transparent ${gridStep}px), repeating-linear-gradient(90deg, rgba(2, 6, 23, .04) 0, rgba(2, 6, 23, .04) 1px, transparent 1px, transparent ${gridStep}px)`
            : 'none';

        const sorted = [...state.elements].sort((a, b) => (a.z_index || 0) - (b.z_index || 0));
        canvasOverlay.innerHTML = '';

        sorted.forEach((item) => {
            const element = document.createElement('div');
            element.className = `certificate-element${state.selectedId === item.id ? ' is-selected' : ''}${item.locked ? ' is-locked' : ''}`;
            element.dataset.id = item.id;
            element.style.left = `${item.left * state.zoom}px`;
            element.style.top = `${item.top * state.zoom}px`;
            element.style.width = `${(item.text_type === 'qr_code' ? item.size : item.width) * state.zoom}px`;
            element.style.height = `${(item.text_type === 'qr_code' ? item.size : item.height) * state.zoom}px`;
            element.style.zIndex = item.z_index || 1;
            element.style.opacity = item.visible === false ? '0.25' : String(item.opacity ?? 1);
            element.style.transform = `rotate(${item.rotation || 0}deg)`;
            element.title = item.label || textTypeLabels[item.text_type] || 'Element';

            const body = document.createElement('div');
            body.className = `element-body${item.text_type === 'qr_code' ? ' is-qr' : ''}`;

            if (item.text_type === 'qr_code') {
                body.innerHTML = `<div class="text-center fw-semibold"><i class="ti ti-qrcode fs-1 d-block mb-1"></i>${item.label || 'QR Code'}</div>`;
            } else {
                const text = document.createElement('div');
                text.className = 'element-text';
                text.textContent = objectPreviewText(item);
                text.style.fontFamily = resolveBrowserFont(item.font);
                text.style.fontSize = `${Math.max(8, item.font_size * state.zoom)}px`;
                text.style.fontWeight = item.bold ? 700 : item.font_weight || 400;
                text.style.fontStyle = item.italic ? 'italic' : 'normal';
                text.style.color = item.color || '#000000';
                text.style.textAlign = item.align || 'left';
                text.style.letterSpacing = `${item.letter_spacing || 0}px`;
                text.style.lineHeight = '1';
                text.style.textTransform = item.uppercase ? 'uppercase' : 'none';
                body.appendChild(text);
            }

            element.appendChild(body);

            const tag = document.createElement('div');
            tag.className = 'element-tag';
            tag.textContent = item.label || textTypeLabels[item.text_type] || item.text_type;
            element.appendChild(tag);

            if (! item.locked) {
                const handle = document.createElement('div');
                handle.className = 'element-handle';
                handle.addEventListener('pointerdown', (event) => startResize(event, item.id));
                element.appendChild(handle);
            }

            element.addEventListener('pointerdown', (event) => startDrag(event, item.id));
            element.addEventListener('click', (event) => {
                event.stopPropagation();
                selectElement(item.id);
            });
            element.addEventListener('dblclick', (event) => {
                event.preventDefault();
                event.stopPropagation();
                selectElement(item.id);
                inlineEditorTarget = item.id;
                render();
            });

            canvasOverlay.appendChild(element);
        });

        renderGuides(sorted);

        renderInlineEditor();
        syncContextToolbar();
        positionContextToolbar();
    }

    function resolveBrowserFont(fontFile) {
        const key = (fontFile || '').toLowerCase();
        const fontMap = {
            'times-new-roman.ttf': '"Certificate Times New Roman", "Times New Roman", serif',
            'times-new-roman-bold.ttf': '"Certificate Times New Roman Bold", "Times New Roman", serif',
            'pesaro-bold.ttf': '"Certificate Pesaro Bold", "Pesaro", serif',
            'edwardian-script-itc.ttf': '"Certificate Edwardian Script ITC", "Edwardian Script ITC", cursive',
        };

        if (fontMap[key]) {
            return fontMap[key];
        }
        if (key.includes('times')) {
            return '"Times New Roman", serif';
        }
        if (key.includes('arial')) {
            return 'Arial, sans-serif';
        }
        if (key.includes('verdana')) {
            return 'Verdana, sans-serif';
        }
        if (key.includes('georgia')) {
            return 'Georgia, serif';
        }
        return '"Certificate Times New Roman", "Times New Roman", serif';
    }

    function renderGuides(sortedElements) {
        if (! state.guidesEnabled) {
            return;
        }

        const active = selectedElement();
        if (! active) {
            return;
        }

        const width = state.canvas.width;
        const height = state.canvas.height;
        const threshold = 8;
        const activeWidth = active.text_type === 'qr_code' ? active.size : active.width;
        const activeHeight = active.text_type === 'qr_code' ? active.size : active.height;
        const points = [];

        const guideTargetsX = [
            { value: 0, type: 'start' },
            { value: width / 2, type: 'center' },
            { value: width, type: 'end' },
        ];

        const guideTargetsY = [
            { value: 0, type: 'start' },
            { value: height / 2, type: 'center' },
            { value: height, type: 'end' },
        ];

        const activeLinesX = [
            { value: active.left, type: 'start' },
            { value: active.left + activeWidth / 2, type: 'center' },
            { value: active.left + activeWidth, type: 'end' },
        ];

        const activeLinesY = [
            { value: active.top, type: 'start' },
            { value: active.top + activeHeight / 2, type: 'center' },
            { value: active.top + activeHeight, type: 'end' },
        ];

        guideTargetsX.forEach((target) => {
            activeLinesX.forEach((line) => {
                if (Math.abs(target.value - line.value) <= threshold) {
                    points.push({ axis: 'x', value: target.value });
                }
            });
        });

        guideTargetsY.forEach((target) => {
            activeLinesY.forEach((line) => {
                if (Math.abs(target.value - line.value) <= threshold) {
                    points.push({ axis: 'y', value: target.value });
                }
            });
        });

        const used = new Set();
        points.forEach((point) => {
            const key = `${point.axis}:${Math.round(point.value)}`;
            if (used.has(key)) {
                return;
            }

            used.add(key);
            const guide = document.createElement('div');
            guide.className = `guide-line ${point.axis === 'x' ? 'vertical' : 'horizontal'}`;
            if (point.axis === 'x') {
                guide.style.left = `${point.value * state.zoom}px`;
            } else {
                guide.style.top = `${point.value * state.zoom}px`;
            }
            canvasOverlay.appendChild(guide);
        });
    }

    function selectElement(id) {
        if (state.selectedId !== id) {
            stopInlineEditing(false);
        }
        state.selectedId = id;
        render();
    }

    function createElementFromPalette(item, x, y) {
        const configuredDefaults = item.defaults && typeof item.defaults === 'object' ? item.defaults : {};
        const defaultFontSize = parseInt(configuredDefaults.font_size ?? designerElementDefaults.font_size ?? 36, 10);
        const defaultLineHeight = parseFloat(configuredDefaults.line_height ?? designerElementDefaults.line_height ?? 1.2);
        const defaultHeight = item.text_type === 'qr_code'
            ? (configuredDefaults.height ?? designerElementDefaults.size ?? 120)
            : tightTextHeight(defaultFontSize, defaultLineHeight);
        const base = {
            id: uuid(),
            text_type: item.text_type,
            label: item.label,
            font: Object.keys(fontOptions)[0] || 'Times New Roman.ttf',
            text_type_face: Object.keys(fontOptions)[0] || 'Times New Roman.ttf',
            color: '#000000',
            top: Math.max(0, Math.round(y ?? 40)),
            left: Math.max(0, Math.round(x ?? 40)),
            width: 320,
            height: defaultHeight,
            size: 120,
            font_size: defaultFontSize,
            font_weight: 400,
            align: 'left',
            rotation: 0,
            opacity: 1,
            visible: true,
            uppercase: false,
            bold: false,
            italic: false,
            line_height: defaultLineHeight,
            letter_spacing: 0,
            z_index: state.elements.length + 1,
            locked: false,
            sample_text: item.text_type === 'date_issued'
                ? issuedDateSample
                : (item.sample_text || textTypeLabels[item.text_type] || item.label),
            custom_text: item.text_type === 'custom_text' ? (item.sample_text || 'Congratulations!') : null,
            ...designerElementDefaults,
            id: uuid(),
            text_type: item.text_type,
            label: item.label,
            top: Math.max(0, Math.round(y ?? 40)),
            left: Math.max(0, Math.round(x ?? 40)),
            z_index: state.elements.length + 1,
            sample_text: item.text_type === 'date_issued'
                ? issuedDateSample
                : (item.sample_text || textTypeLabels[item.text_type] || item.label),
            custom_text: item.text_type === 'custom_text' ? (item.sample_text || item.label) : null,
            ...configuredDefaults,
        };

        if (item.text_type === 'qr_code') {
            base.width = configuredDefaults.width ?? 120;
            base.height = configuredDefaults.height ?? 120;
            base.size = configuredDefaults.size ?? 120;
            base.label = configuredDefaults.label ?? item.label;
            base.sample_text = configuredDefaults.sample_text ?? item.sample_text ?? item.label;
        }

        if (item.text_type === 'custom_text') {
            base.sample_text = configuredDefaults.sample_text ?? item.sample_text ?? item.label;
            base.custom_text = configuredDefaults.custom_text ?? base.sample_text;
        }

        if (item.text_type !== 'qr_code') {
            base.label = item.label;
            base.height = configuredDefaults.height ?? tightTextHeight(base.font_size, base.line_height);
        } else {
            base.height = configuredDefaults.height ?? configuredDefaults.size ?? designerElementDefaults.size ?? 120;
        }

        return clampElement(base);
    }

    function placeElement(item, x, y) {
        state.elements.push(createElementFromPalette(item, x, y));
        state.selectedId = state.elements[state.elements.length - 1].id;
        render();
    }

    function canvasPoint(event) {
        const rect = canvasStage.getBoundingClientRect();
        return {
            x: (event.clientX - rect.left) / state.zoom,
            y: (event.clientY - rect.top) / state.zoom,
        };
    }

    function startDrag(event, id) {
        const target = state.elements.find((item) => item.id === id);
        if (! target || target.locked || event.button !== 0) {
            return;
        }

        selectElement(id);
        event.preventDefault();
        event.stopPropagation();

        const start = canvasPoint(event);
        state.dragging = {
            id,
            startX: start.x,
            startY: start.y,
            originalLeft: target.left,
            originalTop: target.top,
        };

        window.addEventListener('pointermove', onPointerMove);
        window.addEventListener('pointerup', stopInteraction);
    }

    function startResize(event, id) {
        const target = state.elements.find((item) => item.id === id);
        if (! target || target.locked || event.button !== 0) {
            return;
        }

        selectElement(id);
        event.preventDefault();
        event.stopPropagation();

        const start = canvasPoint(event);
        state.resizing = {
            id,
            startX: start.x,
            startY: start.y,
            originalWidth: target.text_type === 'qr_code' ? target.size : target.width,
            originalHeight: target.text_type === 'qr_code' ? target.size : target.height,
            originalLeft: target.left,
            originalTop: target.top,
        };

        window.addEventListener('pointermove', onPointerMove);
        window.addEventListener('pointerup', stopInteraction);
    }

    function onPointerMove(event) {
        if (state.dragging) {
            const element = state.elements.find((item) => item.id === state.dragging.id);
            if (! element) {
                return;
            }

            const point = canvasPoint(event);
            const deltaX = point.x - state.dragging.startX;
            const deltaY = point.y - state.dragging.startY;
            const nextLeft = state.dragging.originalLeft + deltaX;
            const nextTop = state.dragging.originalTop + deltaY;
            element.left = state.snapEnabled ? snapValue(nextLeft) : nextLeft;
            element.top = state.snapEnabled ? snapValue(nextTop) : nextTop;
            clampElement(element);
            render();
        }

        if (state.resizing) {
            const element = state.elements.find((item) => item.id === state.resizing.id);
            if (! element) {
                return;
            }

            const point = canvasPoint(event);
            const deltaX = point.x - state.resizing.startX;
            const deltaY = point.y - state.resizing.startY;
            const nextWidth = Math.max(20, state.resizing.originalWidth + deltaX);
            const nextHeight = Math.max(20, state.resizing.originalHeight + deltaY);

            if (element.text_type === 'qr_code') {
                element.size = state.snapEnabled ? snapValue(Math.max(60, nextWidth)) : Math.max(60, nextWidth);
                element.width = element.size;
                element.height = element.size;
            } else {
                element.width = state.snapEnabled ? snapValue(nextWidth) : nextWidth;
                element.height = state.snapEnabled ? snapValue(nextHeight) : nextHeight;
            }

            clampElement(element);
            render();
        }
    }

    function stopInteraction() {
        state.dragging = null;
        state.resizing = null;
        window.removeEventListener('pointermove', onPointerMove);
        window.removeEventListener('pointerup', stopInteraction);
    }

    function duplicateSelected() {
        const current = selectedElement();
        if (! current) {
            return;
        }

        const copy = normalizeElement({
            ...clone(current),
            id: uuid(),
            top: current.top + 20,
            left: current.left + 20,
            z_index: (current.z_index || 1) + 1,
        });

        state.elements.push(copy);
        state.selectedId = copy.id;
        render();
    }

    function deleteSelected() {
        if (! state.selectedId) {
            return;
        }

        state.elements = state.elements.filter((item) => item.id !== state.selectedId);
        state.selectedId = state.elements.at(-1)?.id ?? null;
        render();
    }

    function shiftZ(delta) {
        const current = selectedElement();
        if (! current) {
            return;
        }

        current.z_index = Math.max(1, (current.z_index || 1) + delta);
        render();
    }

    function setZoom(level) {
        state.zoom = Math.max(0.25, Math.min(2.5, Number(level) || 1));
        render();
    }

    function fitToScreen() {
        const bounds = canvasViewport.getBoundingClientRect();
        const availableWidth = Math.max(320, bounds.width - 60);
        const availableHeight = Math.max(320, bounds.height - 100);
        const zoom = Math.min(availableWidth / state.canvas.width, availableHeight / state.canvas.height, 1.05);
        setZoom(zoom);
    }

    function stopInlineEditing(commit = true) {
        if (! inlineEditor) {
            inlineEditorTarget = null;
            return;
        }

        const editor = inlineEditor;
        const targetId = inlineEditorTarget;
        const nextValue = editor.value;

        editor.remove();
        inlineEditor = null;
        inlineEditorTarget = null;

        if (! commit || ! targetId) {
            return;
        }

        const target = state.elements.find((item) => item.id === targetId);
        if (! target) {
            return;
        }

        if (target.text_type === 'custom_text') {
            target.custom_text = nextValue;
        } else if (nextValue.trim()) {
            target.sample_text = nextValue;
        }

        syncSettingsField();
        render();
    }

    function updateElementPreviewText(id, text) {
        const preview = canvasOverlay.querySelector(`.certificate-element[data-id="${CSS.escape(id)}"] .element-text`);
        if (preview) {
            preview.textContent = text;
        }
    }

    function renderInlineEditor() {
        if (! inlineEditorTarget) {
            return;
        }

        const target = state.elements.find((item) => item.id === inlineEditorTarget);
        if (! target) {
            return;
        }

        const existing = inlineEditor;
        if (existing && existing.isConnected) {
            return;
        }

        const node = canvasOverlay.querySelector(`.certificate-element[data-id="${CSS.escape(target.id)}"]`);
        if (! node) {
            return;
        }

        const textNode = node.querySelector('.element-text');
        const rect = node.getBoundingClientRect();
        const canvasRect = canvasStage.getBoundingClientRect();
        const editor = document.createElement('textarea');
        editor.className = 'inline-text-editor';
        editor.value = target.text_type === 'custom_text'
            ? (target.custom_text || target.sample_text || '')
            : (target.sample_text || textNode?.textContent || '');
        editor.style.left = `${(rect.left - canvasRect.left)}px`;
        editor.style.top = `${(rect.top - canvasRect.top)}px`;
        editor.style.width = `${Math.max(80, rect.width)}px`;
        editor.style.height = `${Math.max(36, rect.height)}px`;
        editor.style.fontFamily = resolveBrowserFont(target.font);
        editor.style.fontSize = `${Math.max(12, (target.font_size || 24) * state.zoom)}px`;
        editor.style.fontWeight = target.bold ? '700' : String(target.font_weight || 400);
        editor.style.fontStyle = target.italic ? 'italic' : 'normal';
        editor.style.color = target.color || '#000000';
        editor.style.textAlign = target.align || 'left';
        editor.style.letterSpacing = `${target.letter_spacing || 0}px`;
        editor.style.lineHeight = '1';
        editor.style.textTransform = target.uppercase ? 'uppercase' : 'none';

        editor.addEventListener('input', () => updateElementPreviewText(target.id, editor.value));
        editor.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                stopInlineEditing(false);
            }
            if (event.key === 'Enter' && ! event.shiftKey) {
                event.preventDefault();
                stopInlineEditing(true);
            }
        });
        editor.addEventListener('blur', () => stopInlineEditing(true));

        canvasOverlay.appendChild(editor);
        inlineEditor = editor;
        editor.focus();
        editor.select();
    }

    function normalizeHexColor(value) {
        let hex = String(value || '').trim();
        if (! hex) {
            return '#000000';
        }

        if (! hex.startsWith('#')) {
            hex = `#${hex}`;
        }

        if (/^#([0-9a-fA-F]{3})$/.test(hex)) {
            const [, short] = hex.match(/^#([0-9a-fA-F]{3})$/);
            return `#${short.split('').map((char) => char + char).join('')}`.toLowerCase();
        }

        if (/^#([0-9a-fA-F]{6})$/.test(hex)) {
            return hex.toLowerCase();
        }

        return '#000000';
    }

    function isValidHexColor(value) {
        return /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(String(value || '').trim());
    }

    function commitHexColorInput(input) {
        if (! input) {
            return;
        }

        const raw = String(input.value || '').trim();
        if (! isValidHexColor(raw)) {
            input.value = selectedElement()?.color || '#000000';
            return;
        }

        const normalized = normalizeHexColor(raw);
        input.value = normalized;
        if (contextColor && contextColor.value !== normalized) {
            contextColor.value = normalized;
        }
        updateSelectedProperty('color', normalized);
    }

    function updateSelectedProperty(property, value) {
        const current = selectedElement();
        if (! current) {
            return;
        }

        if (inlineEditor) {
            stopInlineEditing(true);
        }

        switch (property) {
            case 'font':
                current.font = value;
                current.text_type_face = value;
                break;
            case 'font_size':
                current.font_size = Math.max(1, parseInt(value || '1', 10));
                break;
            case 'font_weight':
                current.font_weight = Math.max(100, Math.min(900, parseInt(value || '400', 10)));
                break;
            case 'color':
                current.color = normalizeHexColor(value);
                break;
            case 'align':
                current.align = value || 'left';
                break;
            case 'opacity':
                current.opacity = Math.max(0, Math.min(1, parseFloat(value || '1')));
                break;
            case 'bold':
            case 'italic':
            case 'uppercase':
            case 'visible':
            case 'locked':
                current[property] = value === true || value === '1' || value === 1 || value === 'on';
                break;
            case 'size':
                current.size = Math.max(60, parseInt(value || '60', 10));
                current.width = current.size;
                current.height = current.size;
                break;
            default:
                current[property] = value;
        }

        clampElement(current);
        syncSettingsField();
        syncContextToolbar();
        render();
    }

    function syncContextToolbar() {
        const current = selectedElement();
        if (! contextToolbar) {
            return;
        }

        if (! current) {
            contextToolbar.classList.add('d-none');
            contextToolbar.style.left = '';
            contextToolbar.style.top = '';
            return;
        }

        contextToolbar.classList.remove('d-none');
        contextToolbarTitle.textContent = current.label || textTypeLabels[current.text_type] || 'Selected Element';
        contextToolbarSubtitle.textContent = current.text_type === 'custom_text'
            ? 'Double-click to edit the text directly on the canvas.'
            : 'Use the controls below to refine typography and layout.';

        const isQr = current.text_type === 'qr_code';
        contextFont.value = current.font || Object.keys(fontOptions)[0] || '';
        contextFontSize.value = current.font_size || 24;
        contextFontWeight.value = current.font_weight || 400;
        contextColor.value = normalizeHexColor(current.color || '#000000');
        if (contextColorHex) {
            contextColorHex.value = normalizeHexColor(current.color || '#000000');
        }
        contextAlign.value = current.align || 'left';
        contextOpacity.value = current.opacity ?? 1;
        contextBold.checked = !!current.bold;
        contextItalic.checked = !!current.italic;
        contextUppercase.checked = !!current.uppercase;
        contextVisible.checked = current.visible !== false;
        contextLocked.checked = !!current.locked;
        contextFont.disabled = isQr;
        contextFontSize.disabled = isQr;
        contextFontWeight.disabled = isQr;
        contextColor.disabled = isQr;
        if (contextColorHex) {
            contextColorHex.disabled = isQr;
        }
        contextAlign.disabled = isQr;
        contextBold.disabled = isQr;
        contextItalic.disabled = isQr;
        contextUppercase.disabled = isQr;
        contextQrGroup.classList.toggle('d-none', ! isQr);
        contextQrSize.value = current.size || 120;
    }

    function hideContextToolbar() {
        stopInlineEditing(false);
        state.selectedId = null;
        if (contextToolbar) {
            contextToolbar.classList.add('d-none');
            contextToolbar.style.left = '';
            contextToolbar.style.top = '';
        }
        render();
    }

    function positionContextToolbar() {
        if (! contextToolbar || contextToolbar.classList.contains('d-none') || ! canvasShell) {
            return;
        }

        const current = selectedElement();
        if (! current) {
            return;
        }

        const selectedNode = canvasOverlay.querySelector(`.certificate-element[data-id="${CSS.escape(current.id)}"]`);
        if (! selectedNode) {
            return;
        }

        const shellRect = canvasShell.getBoundingClientRect();
        const nodeRect = selectedNode.getBoundingClientRect();
        const toolbarRect = contextToolbar.getBoundingClientRect();
        const gap = 12;

        let left = (nodeRect.left - shellRect.left) + (nodeRect.width / 2) - (toolbarRect.width / 2);
        let top = (nodeRect.top - shellRect.top) - toolbarRect.height - gap;

        if (top < gap) {
            top = (nodeRect.bottom - shellRect.top) + gap;
        }

        const maxLeft = Math.max(gap, shellRect.width - toolbarRect.width - gap);
        const maxTop = Math.max(gap, shellRect.height - toolbarRect.height - gap);

        left = Math.min(Math.max(gap, left), maxLeft);
        top = Math.min(Math.max(gap, top), maxTop);

        contextToolbar.style.left = `${left}px`;
        contextToolbar.style.top = `${top}px`;
    }

    [contextFont, contextColor, contextAlign, contextOpacity].forEach((field) => {
        field?.addEventListener('input', () => {
            if (field === contextColor) {
                const normalized = normalizeHexColor(field.value);
                if (contextColorHex) {
                    contextColorHex.value = normalized;
                }
                field.value = normalized;
                updateSelectedProperty('color', normalized);
                return;
            }

            const property = field.id.replace('context', '');
            const map = {
                Font: 'font',
                Color: 'color',
                Align: 'align',
                Opacity: 'opacity',
            };
            updateSelectedProperty(map[property] || property.toLowerCase(), field.value);
        });
    });

    [
        [contextFontSize, 'font_size'],
        [contextFontWeight, 'font_weight'],
        [contextQrSize, 'size'],
    ].forEach(([field, property]) => {
        const commit = () => updateSelectedProperty(property, field?.value);
        field?.addEventListener('change', commit);
        field?.addEventListener('blur', commit);
    });

    contextColorHex?.addEventListener('change', () => commitHexColorInput(contextColorHex));
    contextColorHex?.addEventListener('blur', () => commitHexColorInput(contextColorHex));
    contextColorHex?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            commitHexColorInput(contextColorHex);
        }
    });

    [
        [contextBold, 'bold'],
        [contextItalic, 'italic'],
        [contextUppercase, 'uppercase'],
        [contextVisible, 'visible'],
        [contextLocked, 'locked'],
    ].forEach(([field, property]) => {
        field?.addEventListener('change', () => updateSelectedProperty(property, field.checked));
    });

    function handleCanvasDrop(event) {
        event.preventDefault();
        const data = event.dataTransfer.getData('text/plain');
        if (! data) {
            return;
        }

        try {
            const item = JSON.parse(data);
            const point = canvasPoint(event);
            placeElement(item, point.x - 80, point.y - 30);
        } catch (error) {
            // no-op
        }
    }

    function readCanvasFile(file) {
        if (! file) {
            return;
        }

        backgroundLabel.textContent = file.name;

        const url = URL.createObjectURL(file);
        backgroundImage.onload = () => {
            render();
            fitToScreen();
        };
        backgroundImage.src = url;
        backgroundImage.style.display = 'block';
        state.backgroundUrl = url;
    }

    function setBackgroundPreview(url, preserveCanvas = false) {
        if (! url) {
            return;
        }

        backgroundImage.onload = () => {
            render();
            fitToScreen();
        };
        backgroundImage.src = url;
        backgroundImage.style.display = 'block';
    }

    function buildAjaxFormData(extraFields = {}) {
        const payload = new FormData(form);

        // Keep AJAX actions isolated from the save form's spoofed HTTP method.
        payload.delete('_method');

        Object.entries(extraFields).forEach(([key, value]) => {
            if (value === null || value === undefined) {
                payload.delete(key);
                return;
            }

            payload.set(key, value);
        });

        return payload;
    }

    function previewTemplate() {
        syncSettingsField();
        const formData = buildAjaxFormData({ settings: settingsField.value });

        const modal = window.bootstrap && window.bootstrap.Modal && previewModalElement
            ? new window.bootstrap.Modal(previewModalElement)
            : null;
        previewError.classList.add('d-none');
        previewError.textContent = '';
        previewImage.removeAttribute('src');
        const showPreviewModal = () => {
            if (modal) {
                modal.show();
                return;
            }

            if (window.jQuery && previewModalElement) {
                window.jQuery(previewModalElement).modal('show');
            }
        };

        const button = designer.querySelector('[data-designer-action="preview"]');
        const originalHtml = button?.innerHTML;
        if (button) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Rendering…';
        }

        fetch(previewRoute, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'image/*,application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: formData,
        })
            .then(async (response) => {
                const contentType = response.headers.get('content-type') || '';
                if (! response.ok) {
                    if (contentType.includes('application/json')) {
                        const payload = await response.json();
                        throw new Error(Object.values(payload.errors || { error: [payload.message || 'Unable to generate preview.'] }).flat().join(' '));
                    }

                    const text = await response.text();
                    throw new Error(text || 'Unable to generate preview.');
                }

                if (contentType.includes('application/json')) {
                    const payload = await response.json();
                    const previewSource = payload.preview_data_url || payload.preview_url;
                    if (! previewSource) {
                        throw new Error(payload.message || 'Unable to generate preview.');
                    }

                    previewImage.src = previewSource;
                    showPreviewModal();
                    return null;
                }

                if (! contentType.startsWith('image/')) {
                    const text = await response.text();
                    throw new Error(text ? 'Preview returned an unexpected response. Please try again.' : 'Preview did not return an image.');
                }

                return response.blob();
            })
            .then((blob) => {
                if (! blob) {
                    return;
                }
                previewImage.src = URL.createObjectURL(blob);
                showPreviewModal();
            })
            .catch((error) => {
                previewError.textContent = error.message || 'Unable to generate preview right now.';
                previewError.classList.remove('d-none');
                showPreviewModal();
            })
            .finally(() => {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                }
            });
    }

    backgroundButton?.addEventListener('click', () => backgroundInput?.click());
    backgroundInput?.addEventListener('change', () => readCanvasFile(backgroundInput.files?.[0]));

    document.querySelectorAll('.js-module-toggle').forEach((input) => {
        input.addEventListener('change', () => {
            renderPalette();
        });
    });

    canvasViewport.addEventListener('dragover', (event) => event.preventDefault());
    canvasViewport.addEventListener('drop', handleCanvasDrop);
    canvasViewport.addEventListener('scroll', () => positionContextToolbar(), { passive: true });
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (inlineEditor) {
                event.preventDefault();
                stopInlineEditing(false);
                render();
                return;
            }

            if (state.selectedId || ! contextToolbar.classList.contains('d-none')) {
                event.preventDefault();
                hideContextToolbar();
                return;
            }
        }

        if (! form.contains(document.activeElement) && ! state.selectedId) {
            return;
        }

        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'd') {
            event.preventDefault();
            duplicateSelected();
            return;
        }

        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'c') {
            const current = selectedElement();
            if (current) {
                state.clipboard = clone(current);
            }
            return;
        }

        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'v') {
            if (state.clipboard) {
                const copy = normalizeElement({
                    ...clone(state.clipboard),
                    id: uuid(),
                    top: state.clipboard.top + 20,
                    left: state.clipboard.left + 20,
                    z_index: (state.clipboard.z_index || 1) + 1,
                });
                state.elements.push(copy);
                state.selectedId = copy.id;
                render();
            }
            return;
        }

        if (event.key === 'Delete' || event.key === 'Backspace') {
            if (document.activeElement && ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                return;
            }
            deleteSelected();
            return;
        }

        const selected = selectedElement();
        if (! selected) {
            return;
        }

        const step = event.shiftKey ? 10 : 1;
        let handled = false;

        switch (event.key) {
            case 'ArrowLeft':
                selected.left -= step;
                handled = true;
                break;
            case 'ArrowRight':
                selected.left += step;
                handled = true;
                break;
            case 'ArrowUp':
                selected.top -= step;
                handled = true;
                break;
            case 'ArrowDown':
                selected.top += step;
                handled = true;
                break;
            default:
                break;
        }

        if (handled) {
            event.preventDefault();
            clampElement(selected);
            render();
        }
    });

    designer.querySelectorAll('[data-designer-action]').forEach((button) => {
        button.addEventListener('click', () => {
            const action = button.dataset.designerAction;
        switch (action) {
                case 'hide-toolbar':
                    hideContextToolbar();
                    break;
                case 'fit':
                    fitToScreen();
                    break;
                case 'zoom-in':
                    setZoom(state.zoom + 0.1);
                    break;
                case 'zoom-out':
                    setZoom(state.zoom - 0.1);
                    break;
                case 'duplicate':
                    duplicateSelected();
                    break;
                case 'delete':
                    deleteSelected();
                    break;
                case 'edit-text':
                    {
                        const current = selectedElement();
                        if (current && current.text_type !== 'qr_code') {
                            inlineEditorTarget = current.id;
                            render();
                        }
                    }
                    break;
                case 'bring-forward':
                    shiftZ(1);
                    break;
                case 'send-backward':
                    shiftZ(-1);
                    break;
                case 'toggle-grid':
                    state.gridEnabled = ! state.gridEnabled;
                    render();
                    break;
                case 'toggle-snap':
                    state.snapEnabled = ! state.snapEnabled;
                    render();
                    break;
                case 'toggle-guides':
                    state.guidesEnabled = ! state.guidesEnabled;
                    render();
                    break;
                case 'preview':
                    previewTemplate();
                    break;
                default:
                    break;
            }
        });
    });

    window.addEventListener('resize', () => {
        fitToScreen();
        positionContextToolbar();
    });

    const submitButton = form.querySelector('button[type="submit"]');
    form.addEventListener('submit', () => {
        syncSettingsField();
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.dataset.originalLabel = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving…';
        }
    });

    canvasViewport.addEventListener('pointerdown', (event) => {
        if (event.target === canvasViewport || event.target === canvasStage || event.target === canvasOverlay || event.target === canvasStageHost || event.target === backgroundImage) {
            state.selectedId = null;
            stopInlineEditing();
            render();
        }
    });

    if (state.backgroundUrl) {
        setBackgroundPreview(state.backgroundUrl, true);
    }

    renderPalette();
    syncSettingsField();
    render();
    fitToScreen();
})();
</script>
