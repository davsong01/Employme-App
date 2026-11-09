@php
    $teacherFormRoles = old('role', $teacher?->roles ?? []);
    $teacherFormRoles = is_array($teacherFormRoles)
        ? $teacherFormRoles
        : array_filter(explode(',', (string) $teacherFormRoles));
    $teacherFormStatus = old('status', $teacher?->status ?? 'active');
    $teacherFormPaymentMode = old('payment_mode', $teacher?->payment_mode);
@endphp

<section class="teacher-form-section">
    <h2 class="teacher-section-title">Account details</h2>
    <p class="teacher-section-hint">Manage the teacher’s contact details, role, status, and sign-in.</p>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name">Full name</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $teacher?->name) }}" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email">Email address</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $teacher?->email) }}" required>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone">Phone number</label>
                    <input id="phone" type="tel" class="form-control" name="phone" value="{{ old('phone', $teacher?->phone) }}">
                    @error('phone')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password">Password</label>
                    <input id="password" type="password" class="form-control" name="password" autocomplete="new-password">
                    <div class="form-text">{{ $teacher ? 'Leave blank to keep the current password.' : 'Leave blank to use the default password: 12345.' }}</div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @if ($allowRoleFields)
                    <div class="col-md-6">
                        <label>Role</label>
                        <div class="row g-2">
                            @foreach(['Facilitator', 'Grader', 'Admin'] as $roleOption)
                                <div class="col-sm-4">
                                    <div class="form-check h-100">
                                        <input class="form-check-input" type="checkbox" name="role[]" value="{{ $roleOption }}" id="role-{{ strtolower($roleOption) }}" {{ in_array($roleOption, $teacherFormRoles, true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role-{{ strtolower($roleOption) }}">{{ $roleOption }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('role')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active" {{ $teacherFormStatus === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $teacherFormStatus === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <input type="hidden" name="status" value="{{ $teacherFormStatus }}">
                @endif
                <div class="col-md-6">
                    <label for="payment_mode">Payment mode <span class="text-muted fw-normal">(optional)</span></label>
                    <select name="payment_mode" id="payment_mode" class="form-control">
                        <option value="">No payment mode</option>
                        @foreach($payment_modes as $mode)
                            <option value="{{ $mode->id }}" {{ (string) $teacherFormPaymentMode === (string) $mode->id ? 'selected' : '' }}>
                                {{ ucfirst($mode->name) }}{{ $mode->status !== 'active' ? ' (Inactive)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_mode')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <label for="file">Profile picture</label>
            <div class="teacher-profile-preview mb-3" id="profile_picture" @if(!$teacher) style="display:none" @endif>
                @if ($teacher?->image)
                    <img src="{{ $teacher->image }}" alt="{{ $teacher->name }} profile picture">
                @else
                    <img src="" alt="Verified teacher profile picture">
                @endif
            </div>
            <input id="file" type="file" name="file" class="form-control" accept="image/*">
            <input type="hidden" id="picture" name="picture" value="{{ old('picture') }}">
            @error('file')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</section>
