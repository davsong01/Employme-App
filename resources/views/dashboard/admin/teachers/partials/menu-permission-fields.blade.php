<section class="teacher-form-section">
    <h2 class="teacher-section-title">Menu permissions</h2>
    <p class="teacher-section-hint">Choose which admin pages this teacher can access.</p>

    <div class="row g-3">
        @foreach($menuPermissions as $menu)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 bg-light">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="form-check">
                            <input class="form-check-input parent-checkbox" type="checkbox" name="menu_permissions[]" value="{{ $menu['route'] }}" id="parent-{{ $menu['id'] }}" {{ in_array($menu['route'], $selectedMenuPermissions, true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="parent-{{ $menu['id'] }}">{{ $menu['name'] }}</label>
                        </div>
                    </div>
                    @if(!empty($menu['children']))
                        <div class="card-body pt-2">
                            @foreach($menu['children'] as $child)
                                <div class="form-check">
                                    <input class="form-check-input child-checkbox" type="checkbox" name="menu_permissions[]" value="{{ $child['route'] }}" id="child-{{ $child['id'] }}" data-parent-id="parent-{{ $menu['id'] }}" {{ in_array($child['route'], $selectedMenuPermissions, true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="child-{{ $child['id'] }}">{{ $child['name'] }}</label>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
