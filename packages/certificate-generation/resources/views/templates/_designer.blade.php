@php
    $template = $template ?? null;
    $certificateRoute = $certificateRoute ?? config('certificates.routes.name', 'certificates.').'manage.templates';
    $moduleLabels = $moduleLabels ?? $modules ?? config('certificates.modules', []);
    $fontOptions = $fontOptions ?? [];
    $backgroundPreview = $backgroundPreview ?? null;
    $canvasDefaults = config('certificates.designer.canvas', ['width' => 1123, 'height' => 794, 'orientation' => 'landscape']);
    $elementDefaults = config('certificates.designer.element_defaults', []);
    $uploadAccept = collect(config('certificates.uploads.mimes', ['jpg', 'jpeg', 'png', 'webp']))->map(fn ($mime) => '.'.ltrim($mime, '.'))->implode(',');
    $ui = config('certificates.ui', []);
    $defaultSettings = [
        'canvas' => $canvasDefaults,
        'elements' => [],
    ];
    $settingsJson = old('settings');
    if (blank($settingsJson)) {
        $settingsJson = json_encode($template?->settings ?: $defaultSettings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    $selectedModules = old('supported_modules', $template?->supported_modules ?? []);
    if (! is_array($selectedModules)) {
        $selectedModules = [];
    }
    $initialCanvas = data_get($template?->settings, 'canvas', $defaultSettings['canvas']);
    $previewKey = old('preview_key', $previewKey ?? ($template?->id ? 'template-' . $template->id : (string) \Illuminate\Support\Str::uuid()));
    $previewEnabled = config('certificates.routes.preview_enabled', true);
    $previewUrl = $previewEnabled
        ? ($previewUrl ?? ($template
            ? route($certificateRoute.'.preview-template', ['template' => $template->id])
            : route($certificateRoute.'.preview')))
        : '#';
    $previewTemplateUrl = $previewEnabled
        ? ($previewTemplateUrl ?? ($template
            ? route($certificateRoute.'.preview-template', ['template' => $template->id])
            : ''))
        : '';
    $indexUrl = $indexUrl ?? route($certificateRoute.'.index');
    $elementLibrary = config('certificates.element_library', []);
    $elementLibraryEnabled = (bool) ($elementLibrary['enabled'] ?? true);
    $paletteGroups = $elementLibraryEnabled ? ($elementLibrary['groups'] ?? []) : [];
    $textTypeOptions = collect($paletteGroups)
        ->mapWithKeys(function (array $group) {
            return [
                $group['key'] => collect($group['items'] ?? [])
                    ->pluck('label', 'text_type')
                    ->all(),
            ];
        })
        ->all();
    $flatTextTypeOptions = collect($textTypeOptions)->flatMap(fn ($items) => $items)->all();
    $bootstrapVersion = (int) config('certificates.ui.bootstrap_version', 4);
    $useBootstrap4 = $bootstrapVersion < 5;
@endphp

@include('certificates::templates._designer_styles')

<div class="certificate-designer" id="certificateDesigner" data-preview-url="{{ $previewUrl }}" data-preview-template-url="{{ $previewTemplateUrl }}">
    <div class="card designer-top-card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge {{ $useBootstrap4 ? 'badge-primary' : 'bg-blue-lt text-blue' }}">{{ $ui['designer_badge'] ?? 'Certificate Designer' }}</span>
                        @if(!empty($selectedModules))
                            @foreach($selectedModules as $module)
                                <span class="module-pill">{{ $moduleLabels[$module] ?? $module }}</span>
                            @endforeach
                        @endif
                    </div>
                    <h3 class="page-title mb-1">{{ $template ? ($ui['edit_title'] ?? 'Edit Certificate Template') : ($ui['create_title'] ?? 'Create Certificate Template') }}</h3>
                    <div class="text-muted">{{ $ui['designer_description'] ?? 'Build reusable certificates with drag-and-drop positioning, server preview, and one JSON settings payload.' }}</div>
                </div>
                <div class="btn-list">
                    <button type="button" class="btn btn-light" data-designer-action="fit">
                        <i class="ti ti-maximize me-2"></i>Fit
                    </button>
                    <button type="button" class="btn btn-light" data-designer-action="zoom-out">
                        <i class="ti ti-zoom-out me-2"></i>-
                    </button>
                    <button type="button" class="btn btn-light" data-designer-action="zoom-in">
                        <i class="ti ti-zoom-in me-2"></i>+
                    </button>
                    <button type="button" class="btn btn-light" data-designer-action="duplicate">
                        <i class="ti ti-copy me-2"></i>Duplicate
                    </button>
                    <button type="button" class="btn btn-light" data-designer-action="bring-forward">
                        <i class="ti ti-arrow-up me-2"></i>Forward
                    </button>
                    <button type="button" class="btn btn-light" data-designer-action="send-backward">
                        <i class="ti ti-arrow-down me-2"></i>Backward
                    </button>
                    @if($previewEnabled)<button type="button" class="btn btn-outline-secondary" data-designer-action="preview">
                        <i class="ti ti-eye me-2"></i>Preview
                    </button>@endif
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-2"></i>{{ $ui['save_label'] ?? 'Save Template' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('certificates::partials.alerts')

    <div class="card designer-toolbar mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-5">
                    <label class="form-label">Template Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $template->name ?? '') }}" required>
                </div>
                <div class="col-lg-5">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description', $template->description ?? '') }}" placeholder="Optional note about the template">
                </div>
                <div class="col-lg-2">
                    <label class="form-label d-block">Status</label>
                    <input type="hidden" name="status" value="0">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="templateStatus" @checked(old('status', $template->status ?? true))>
                        <label class="form-check-label" for="templateStatus">Active</label>
                    </div>
                </div>
                @if($moduleLabels !== [])
                <div class="col-lg-6">
                    <label class="form-label">Supported Modules</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($moduleLabels as $key => $label)
                            <label class="form-check mb-0">
                                <input type="checkbox" class="form-check-input js-module-toggle" name="supported_modules[]" value="{{ $key }}" @checked(in_array($key, $selectedModules, true))>
                                <span class="form-check-label">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="muted-help mt-1">Only enabled modules will appear in the element palette.</div>
                </div>
                @endif
                <div class="col-lg-4">
                    <label class="form-label">Background Image @unless($template)<span class="text-danger">*</span>@endunless</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="file" name="certificate_template_upload" id="certificateBackgroundUpload" class="form-control" accept="{{ $uploadAccept }}" hidden>
                        <button type="button" class="btn btn-outline-secondary" id="certificateBackgroundButton">
                            <i class="ti ti-upload me-2"></i>Choose Background
                        </button>
                        <span style="display:none" class="text-muted small" id="certificateBackgroundLabel">{{ $template?->certificate_template ? basename((string) $template->certificate_template) : 'No file selected' }}</span>
                    </div>
                    @if($template?->certificate_template)
                        <div class="muted-help mt-1">Stored securely; changing it will not move your objects.</div>
                    @endif
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Canvas</label>
                    <div class="form-control-plaintext small text-muted" id="canvasSummary">Loading…</div>
                </div>
            </div>
        </div>
    </div>

    <textarea name="settings" id="certificateSettingsJson" class="d-none" aria-hidden="true">{{ $settingsJson }}</textarea>
    <input type="hidden" name="preview_key" value="{{ $previewKey }}">

    <div class="designer-shell row g-4 align-items-start">
        <aside class="col-md-3">
            <div class="designer-panel h-100">
                @if($elementLibraryEnabled)
                <div class="panel-card">
                    <div class="panel-header">
                        <div class="eyebrow"><i class="ti ti-layout-grid me-1"></i>{{ $elementLibrary['eyebrow'] ?? 'Library' }}</div>
                        <h6>{{ $elementLibrary['title'] ?? 'Elements Library' }}</h6>
                        <p>{{ $elementLibrary['description'] ?? 'Drag fields and static items onto the certificate canvas.' }}</p>
                        <div class="panel-header-actions">
                            <span class="panel-chip"><i class="ti ti-stack-2 me-1"></i><span id="libraryCountChip">0 items</span></span>
                            <span id="libraryGroupChips" class="d-inline-flex flex-wrap gap-1"></span>
                        </div>
                    </div>
                    <div class="panel-body">
                    <div id="paletteContainer" class="d-grid gap-2"></div>
                    <div class="muted-help mt-3">
                        {{ $elementLibrary['help'] ?? 'Drag an item onto the certificate or click it to place it at the current viewport center.' }}
                    </div>
                    </div>
                </div>
                @endif

                <div class="panel-card">
                    <div class="panel-header">
                        <div class="eyebrow"><i class="ti ti-adjustments-horizontal me-1"></i>Tools</div>
                        <h6>Canvas Tools</h6>
                        <p>Fine-tune the editing experience while you position elements.</p>
                        <div class="panel-header-actions">
                            <span class="panel-chip"><i class="ti ti-toggle-right me-1"></i>3 controls</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-light justify-content-start" data-designer-action="toggle-grid">
                                <i class="ti ti-grid-dots me-2"></i>Grid: <span id="gridStateLabel">On</span>
                            </button>
                            <button type="button" class="btn btn-light justify-content-start" data-designer-action="toggle-snap">
                                <i class="ti ti-magnet me-2"></i>Snap: <span id="snapStateLabel">On</span>
                            </button>
                            <button type="button" class="btn btn-light justify-content-start" data-designer-action="toggle-guides">
                                <i class="ti ti-ruler-measure-2 me-2"></i>Guides: <span id="guidesStateLabel">On</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <section class="designer-workspace col-md-9">
            <div class="canvas-shell">
                <div class="canvas-toolbar">
                    <div>
                        <div class="fw-semibold">Design Canvas</div>
                        <div class="muted-help">Objects stay within the canvas and render with the same JSON used by the server preview.</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark" id="zoomLabel">100%</span>
                        <span class="badge bg-light text-dark" id="canvasDimensionLabel">{{ $initialCanvas['width'] ?? $canvasDefaults['width'] }} × {{ $initialCanvas['height'] ?? $canvasDefaults['height'] }}</span>
                    </div>
                </div>

                <div class="context-toolbar-floating d-none" id="contextToolbar">
                    <div class="card">
                        <div class="card-body py-2">
                            <div class="inspector-row inspector-meta">
                                <div>
                                    <div class="fw-semibold" id="contextToolbarTitle">Selected Element</div>
                                    <div class="muted-help" id="contextToolbarSubtitle">Double-click text to edit it directly on the canvas.</div>
                                </div>
                                <div class="btn-list">
                                    <button type="button" class="btn btn-light btn-sm" data-designer-action="hide-toolbar" title="Hide toolbar">
                                        <i class="ti ti-x me-1"></i>Hide
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm" data-designer-action="edit-text">
                                        <i class="ti ti-pencil me-1"></i>Edit Text
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm" data-designer-action="duplicate">
                                        <i class="ti ti-copy me-1"></i>Duplicate
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm text-danger" data-designer-action="delete">
                                        <i class="ti ti-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>

                            <div class="inspector-row inspector-row-fields">
                                <div>
                                    <label class="form-label">Font</label>
                                    <select class="form-control form-control-sm" id="contextFont">
                                        @foreach($fontOptions as $fontFile => $fontLabel)
                                            <option value="{{ $fontFile }}">{{ $fontLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Size</label>
                                    <input type="number" min="1" class="form-control form-control-sm" id="contextFontSize">
                                </div>
                                <div>
                                    <label class="form-label">Weight</label>
                                    <input type="number" min="100" max="900" step="100" class="form-control form-control-sm" id="contextFontWeight">
                                </div>
                                <div>
                                    <label class="form-label">Color</label>
                                    <div class="color-control">
                                        <input type="text" class="form-control form-control-sm hex-input" id="contextColorHex" placeholder="#ffffff" inputmode="text" autocomplete="off" spellcheck="false">
                                        <input type="color" class="form-control form-control-sm" id="contextColor" title="Pick a color" aria-label="Pick a color">
                                    </div>
                                    <div class="muted-help mt-1">Hex or picker.</div>
                                </div>
                                <div>
                                    <label class="form-label">Align</label>
                                    <select class="form-control form-control-sm" id="contextAlign">
                                        <option value="left">Left</option>
                                        <option value="center">Center</option>
                                        <option value="right">Right</option>
                                    </select>
                                </div>
                            </div>

                            <div class="inspector-row inspector-row-controls">
                                <div>
                                    <label class="form-label">Opacity</label>
                                    <input type="range" min="0" max="1" step="0.01" class="form-range custom-range" id="contextOpacity">
                                </div>
                                <div class="toggle-list">
                                    <label class="form-check compact-toggle">
                                        <input class="form-check-input" type="checkbox" id="contextBold">
                                        <span class="form-check-label">Bold</span>
                                    </label>
                                    <label class="form-check compact-toggle">
                                        <input class="form-check-input" type="checkbox" id="contextItalic">
                                        <span class="form-check-label">Italic</span>
                                    </label>
                                    <label class="form-check compact-toggle">
                                        <input class="form-check-input" type="checkbox" id="contextUppercase">
                                        <span class="form-check-label">Uppercase</span>
                                    </label>
                                    <label class="form-check compact-toggle">
                                        <input class="form-check-input" type="checkbox" id="contextVisible">
                                        <span class="form-check-label">Visible</span>
                                    </label>
                                    <label class="form-check compact-toggle">
                                        <input class="form-check-input" type="checkbox" id="contextLocked">
                                        <span class="form-check-label">Locked</span>
                                    </label>
                                </div>
                            </div>

                            <div class="row g-2 mt-1 d-none" id="contextQrGroup">
                                <div class="col-lg-2">
                                    <label class="form-label">QR Size</label>
                                    <input type="number" min="60" class="form-control form-control-sm" id="contextQrSize">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="canvas-viewer" id="canvasViewport">
                    <div class="ruler-top"></div>
                    <div class="d-flex align-items-start gap-0">
                        <div class="ruler-left"></div>
                        <div class="position-relative flex-grow-1" id="canvasStageHost">
                            <div class="canvas-stage" id="canvasStage">
                                <img id="canvasBackgroundImage" class="canvas-background" alt="Certificate background" src="{{ $backgroundPreview ?? '' }}" style="{{ $backgroundPreview ? '' : 'display:none;' }}">
                                <div id="canvasOverlay" class="canvas-overlay"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <div class="row mt-3">
        <div class="col-12 d-flex justify-content-between flex-wrap gap-2">
            <a href="{{ $indexUrl }}" class="btn btn-light">
                <i class="ti ti-arrow-left me-2"></i>Back to List
            </a>
            <div class="btn-list">
                @if($previewEnabled)<button type="button" class="btn btn-outline-secondary" data-designer-action="preview">
                    <i class="ti ti-eye me-2"></i>Preview
                </button>@endif
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-2"></i>{{ $ui['save_label'] ?? 'Save Template' }}
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="certificatePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl preview-modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Server Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="preview-modal-image-wrap text-center">
                        <img src="" alt="Certificate preview" id="certificatePreviewImage" class="img-fluid rounded border">
                    </div>
                    <div class="alert alert-danger d-none mt-3" id="certificatePreviewError"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('certificates::templates._designer_scripts')

