@extends('dashboard.student.trainingsindex')
@section('title', 'Study Materials')
@section('content')
<style>
.study-library .library-header {background:#f0f5ff;border:1px solid #dfe7f5;border-radius:20px;padding:2rem;margin-bottom:1.75rem}
.study-library .library-grid {display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,320px),1fr));gap:1.5rem}
.study-library .resource-card {display:flex;flex-direction:column;overflow:hidden;border:1px solid #e2e8f0;border-radius:16px;background:#fff;box-shadow:0 4px 18px rgba(15,23,42,.04)}
.study-library .resource-preview {aspect-ratio:16 / 9;background:#101827}
.study-library .resource-preview iframe,.study-library .resource-preview video {display:block;width:100%;height:100%;border:0}
.study-library .document-preview {display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.75rem;background:#fff3ef;color:#b74428}
.study-library .document-preview i {font-size:3rem}
.study-library .resource-body {padding:1.5rem;display:flex;flex:1;flex-direction:column;align-items:flex-start}
.study-library .resource-title {font-size:1.1rem;line-height:1.5;overflow-wrap:anywhere}
.study-library .resource-kind {font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#52617a;margin-bottom:.5rem}
.study-library .resource-date {font-size:.8rem;color:#64748b;margin-bottom:1.25rem}
.study-library .resource-action {margin-top:auto}
.study-library .video-unavailable {display:flex;align-items:center;padding:1.5rem;color:#e2e8f0;font-size:.9rem}
.study-library .library-empty {padding:3rem 1.5rem;border:1px dashed #cbd5e1;border-radius:16px;text-align:center;background:#fff}
</style>
<div class="container-fluid study-library">
    <header class="library-header">
        <div class="small text-primary fw-bold mb-2">YOUR LEARNING LIBRARY</div>
        <h1 class="h3 mb-2">{{ $program->p_name }}</h1>
        <p class="text-muted mb-3">Watch lessons here and download documents to study at your own pace.</p>
        <span class="badge bg-white text-dark border rounded-pill px-3 py-2">{{ $materials->count() }} materials</span>
    </header>
    <div class="library-grid">
        @foreach($materials as $material)
            @php
                $isVideo = $material->type === 'video';
                $player = $isVideo ? $material->video_player : null;
            @endphp
            <article class="resource-card">
                @if($player && $player['kind'] === 'iframe')
                    <div class="resource-preview">
                        <iframe src="{{ $player['url'] }}" title="{{ $material->title }}" loading="lazy" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen></iframe>
                    </div>
                @elseif($player && $player['kind'] === 'video')
                    <div class="resource-preview">
                        <video controls preload="none" playsinline aria-label="{{ $material->title }}" src="{{ $player['url'] }}">Your browser does not support this video.</video>
                    </div>
                @elseif($isVideo)
                    <div class="resource-preview video-unavailable">This video link cannot be played here. Please contact your instructor for a YouTube, Vimeo, or direct video link.</div>
                @else
                    <div class="resource-preview document-preview" aria-hidden="true">
                        <i class="far fa-file-pdf"></i><span class="fw-semibold">Study document</span>
                    </div>
                @endif
                <div class="resource-body">
                    <span class="resource-kind">{{ $isVideo ? 'Video lesson' : 'Document' }}</span>
                    <h2 class="resource-title">{{ $material->title }}</h2>
                    <p class="resource-date">Added {{ ($material->uploaded_at ?? $material->created_at)->format('d M Y') }}</p>
                    @if(!$isVideo)
                        <a class="btn btn-outline-primary resource-action" href="{{ route('participants.getmaterial', ['p_id' => $program->id, 'filename' => $material->file]) }}"><i class="fa fa-download me-2" aria-hidden="true"></i>Download document</a>
                    @elseif($player)
                        <span class="small text-muted resource-action">Press play above to watch this lesson.</span>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
    @if($materials->isEmpty())
        <div class="library-empty"><h2 class="h5">Your materials will appear here</h2><p class="text-muted mb-0">Your instructor has not added any study materials yet.</p></div>
    @endif
    @if($useAi && $materials->count() > 0)
        @php $useCase = 'materials'; @endphp
        @include('layouts.partials.portal-ai-chat')
    @endif
</div>
@endsection
