@extends('dashboard.admin.index')
@section('title', 'Add Facilitator')
@section('css')
    @parent
    @include('dashboard.admin.teachers.partials.form-styles')
@endsection
@section('content')
<div class="container-fluid teacher-page">
    <header class="teacher-page-header">
        <div>
            <span class="teacher-page-eyebrow">Teacher management / New</span>
            <h1 class="teacher-page-title">Add a teacher</h1>
            <p class="teacher-page-description">Create a staff account and set up professional details and access.</p>
        </div>
        <a href="{{ route('teachers.index') }}" class="teacher-back-link"><i class="fa fa-arrow-left" aria-hidden="true"></i> All teachers</a>
    </header>
    <div class="row">
        <div class="col-md-12">
            <div class="card teacher-form-card">
                <div class="card-body">
                    @include('layouts.partials.alerts')
                    <form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        @include('dashboard.admin.teachers.partials.account-fields', ['teacher' => null, 'payment_modes' => $payment_modes, 'allowRoleFields' => true])
                        @include('dashboard.admin.teachers.partials.profile-fields', ['teacher' => null, 'showLicenseVerification' => true])
                        @php
                            $trainingRows = collect(old('training', []))->map(function ($programId) use ($programs) {
                                $program = $programs->firstWhere('id', $programId);

                                return (object) [
                                    'program_id' => $programId,
                                    'p_name' => $program?->p_name,
                                    'training_permissions' => old('training_permissions.'.$programId, []),
                                ];
                            });
                        @endphp
                        
                        @include('dashboard.admin.teachers.partials.menu-permission-fields', ['menuPermissions' => app('app\\Http\\Controllers\\Controller')->adminMenus(), 'selectedMenuPermissions' => (array) old('menu_permissions', [])])
                        @include('dashboard.admin.teachers.partials.training-fields', ['programs' => $programs, 'trainingRows' => $trainingRows])
                        {{ csrf_field() }}
                        <div class="teacher-submit-bar">
                            <button type="submit" class="btn teacher-submit-button">Create teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('extra-scripts')
    @include('dashboard.admin.teachers.partials.profile-scripts')
@endsection
