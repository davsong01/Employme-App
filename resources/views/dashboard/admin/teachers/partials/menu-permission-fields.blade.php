<section class="teacher-form-section">
    <h2 class="teacher-section-title">Menu permissions</h2>
    <p class="teacher-section-hint">Choose which admin pages this teacher can access.</p>

    <div class="teacher-menu-permissions-grid">
        @foreach($menuPermissions as $menuIndex => $menu)
            <div class="card h-100 border-0 bg-light">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="form-check">
                        <input class="form-check-input parent-checkbox" type="checkbox" name="menu_permissions[]" value="{{ $menu['route'] }}" id="parent-{{ $menuIndex }}" {{ in_array($menu['route'], $selectedMenuPermissions, true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="parent-{{ $menuIndex }}">{{ $menu['name'] }}</label>
                    </div>
                </div>
                @if(!empty($menu['children']))
                    <div class="card-body pt-2">
                        @foreach($menu['children'] as $childIndex => $child)
                            <div class="form-check">
                                <input class="form-check-input child-checkbox" type="checkbox" name="menu_permissions[]" value="{{ $child['route'] }}" id="child-{{ $menuIndex }}-{{ $childIndex }}" {{ in_array($child['route'], $selectedMenuPermissions, true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="child-{{ $menuIndex }}-{{ $childIndex }}">{{ $child['name'] }}</label>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>

<script>
    const syncMenuParentCheckbox = (menuCard) => {
        const children = menuCard.find('.child-checkbox');

        if (children.length > 0) {
            menuCard.find('.parent-checkbox').prop('checked', children.filter(':checked').length > 0);
        }
    };

    $('.teacher-menu-permissions-grid .card').each(function () {
        syncMenuParentCheckbox($(this));
    });

    $(document).on('change', '.parent-checkbox', function () {
        $(this).closest('.card').find('.child-checkbox').prop('checked', $(this).prop('checked'));
    });

    $(document).on('change', '.child-checkbox', function () {
        syncMenuParentCheckbox($(this).closest('.card'));
    });
</script>
