@php
    $check = [
        'teachers.update.menu',
        'teachers.update.training.access',
        'teachers.view.referral.details',
        'teachers.update',
        'teachers.role.status'
    ];

    $allpermissions = canUserAccessPermission($check);
@endphp
@extends('dashboard.admin.index')
@section('title', $user->name )
@section('css')
    @parent
    @include('dashboard.admin.teachers.partials.form-styles')
@endsection
@section('content')
<div class="container-fluid teacher-page">
    <header class="teacher-page-header">
        <div>
            <span class="teacher-page-eyebrow">Teacher management / Edit</span>
            <h1 class="teacher-page-title">{{ $user->name }}</h1>
            <p class="teacher-page-description">Update account details, professional profile, and access.</p>
            @if ($allpermissions['teachers.view.referral.details'])
                <div class="mt-2 small">
                    <a href="{{ url('/?facilitator='.$user->license) }}" class="me-3">Referral link</a>
                    @if ($user->waaccsp_link)
                        <a href="{{ $user->waaccsp_link }}" target="_blank" rel="noopener">WAACSP profile</a>
                    @endif
                </div>
            @endif
        </div>
        <a href="{{ route('teachers.index') }}" class="teacher-back-link"><i class="fa fa-arrow-left" aria-hidden="true"></i> All teachers</a>
    </header>
    <div class="row">
        <div class="col-md-12">
            <div class="card teacher-form-card">
                <div class="card-body">
                    @include('layouts.partials.alerts')
                    
                    <form action="{{ route('teachers.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="pb-2">
                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                        
                        @include('dashboard.admin.teachers.partials.account-fields', ['teacher' => $user, 'payment_modes' => $payment_modes, 'allowRoleFields' => $allpermissions['teachers.role.status']])
                        @include('dashboard.admin.teachers.partials.profile-fields', ['teacher' => $user, 'showLicenseVerification' => true])

                        @if ($allpermissions['teachers.update.menu'])
                            @include('dashboard.admin.teachers.partials.menu-permission-fields', ['menuPermissions' => app('app\\Http\\Controllers\\Controller')->adminMenus(), 'selectedMenuPermissions' => $user->permissions()])
                        @endif

                        @if ($allpermissions['teachers.update.training.access'])
                            @include('dashboard.admin.teachers.partials.training-fields', ['programs' => $allprograms, 'trainingRows' => $user->trainings])
                        @endif

                        @if ($allpermissions['teachers.update'])
                        <div class="teacher-submit-bar">
                            <button type="submit" class="btn teacher-submit-button">Save changes</button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('dashboard.admin.teachers.partials.profile-scripts')
@endsection
