<script>
    $(function () {
        const programs = @json($programs->values());
        const permissionMenus = @json($trainingPermissionMenus);
        const accordion = $('#trainingAccordion');
        let rowNumber = accordion.find('.accordion-item').length;

        function initializeSearchableTrainingSelects() {
            if (!$.fn.select2) {
                return;
            }

            accordion.find('.training-dropdown').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        width: '100%',
                        placeholder: 'Search trainings',
                        allowClear: true,
                    });
                }
            });
        }

        function updateProgramOptions() {
            const selectedIds = accordion.find('.training-dropdown').map(function () {
                return $(this).val();
            }).get().filter(Boolean);

            accordion.find('.training-dropdown').each(function () {
                const currentId = $(this).val();
                const options = programs
                    .filter((program) => String(program.id) === currentId || !selectedIds.includes(String(program.id)))
                    .map((program) => `<option value="${program.id}" ${String(program.id) === currentId ? 'selected' : ''}>${program.p_name}</option>`)
                    .join('');

                $(this)
                    .html(`<option value="">Select a program</option>${options}`)
                    .val(currentId)
                    .trigger('change.select2');
            });

            initializeSearchableTrainingSelects();
        }

        function updateSectionToggles(scope) {
            scope.find('.permission-group').each(function () {
                const permissions = $(this).find('.permission-checkbox');
                const allChecked = permissions.length > 0 && permissions.filter(':checked').length === permissions.length;

                $(this).find('.permission-section-toggle').prop('checked', allChecked);
            });
        }

        function buildPermissionFields(programId, rowId) {
            return permissionMenus.map((menu, menuIndex) => {
                if (!menu.children || menu.children.length === 0) {
                    return '';
                }

                const fields = menu.children.map((permission, permissionIndex) => {
                    const checkboxId = `permission-${rowId}-${menuIndex}-${permissionIndex}`;

                    return `
                        <div class="col-md-4 col-lg-3">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox" type="checkbox" name="training_permissions[${programId}][]" value="${permission.route}" id="${checkboxId}">
                                <label class="form-check-label" for="${checkboxId}">${permission.name}</label>
                            </div>
                        </div>`;
                }).join('');

                return `
                    <div class="col-12 mb-3 permission-group">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h4 class="h6 text-primary mb-0">${menu.name}</h4>
                            <label class="d-inline-flex align-items-center gap-2 mb-0">
                                <input type="checkbox" class="permission-section-toggle">
                                <span class="small">Select all</span>
                            </label>
                        </div>
                        <div class="row">${fields}</div>
                    </div>`;
            }).join('');
        }

        $('#addRowButton').on('click', function () {
            const selectedIds = accordion.find('.training-dropdown').map(function () {
                return $(this).val();
            }).get().filter(Boolean);
            const availablePrograms = programs.filter((program) => !selectedIds.includes(String(program.id)));

            if (availablePrograms.length === 0) {
                window.alert('No more trainings are available to select.');
                return;
            }

            rowNumber += 1;
            const rowId = `training-${rowNumber}`;
            const collapseId = `collapse-${rowNumber}`;
            const programOptions = availablePrograms
                .map((program) => `<option value="${program.id}">${program.p_name}</option>`)
                .join('');
            const permissionFields = buildPermissionFields(availablePrograms[0].id, rowId);

            accordion.append(`
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-${rowNumber}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">New training</button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="heading-${rowNumber}" data-bs-parent="#trainingAccordion">
                        <div class="accordion-body">
                            <div class="training-row row mt-2">
                                <div class="col-md-6">
                                    <label for="training-select-${rowNumber}">Select training</label>
                                    <select id="training-select-${rowNumber}" class="form-control select2 training-dropdown" name="training[]" required>
                                        <option value="">Select a program</option>${programOptions}
                                    </select>
                                </div>
                                <div class="col-12 mt-4">
                                    <h3 class="h6 mb-3">Permissions</h3>
                                    <div class="row permissions-container p-3">${permissionFields}</div>
                                </div>
                                <div class="col-12 mt-3 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm removeRowButton">Remove training</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`);

            updateProgramOptions();
        });

        accordion.on('click', '.removeRowButton', function () {
            $(this).closest('.accordion-item').remove();
            updateProgramOptions();
        });

        accordion.on('change', '.permission-section-toggle', function () {
            $(this).closest('.permission-group').find('.permission-checkbox').prop('checked', $(this).prop('checked'));
        });

        accordion.on('change', '.permission-checkbox', function () {
            updateSectionToggles($(this).closest('.permissions-container'));
        });

        accordion.on('change', '.training-dropdown', function () {
            const programId = $(this).val();
            const row = $(this).closest('.training-row');

            row.find('.permission-checkbox').prop('checked', false).attr('name', `training_permissions[${programId}][]`);
            updateSectionToggles(row);
            $(this).closest('.accordion-item').find('.accordion-button').text($(this).find(':selected').text() || 'Select a training');
            updateProgramOptions();
        });

        updateProgramOptions();
        updateSectionToggles(accordion);
    });
</script>
