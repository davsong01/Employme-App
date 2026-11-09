<script>
    $(function () {
        if (window.CKEDITOR && !CKEDITOR.instances.ckeditor) {
            CKEDITOR.replace('ckeditor');
        }

        const profileEditor = window.CKEDITOR?.instances?.ckeditor;
        const verifyUrl = @json(env('ENT') === 'demo' ? 'http://localhost:8888/waacsp/public/api/verifyinstructor' : 'https://thewaacsp.com/api/verifyinstructor');
        const verifyToken = @json(\App\Models\Settings::value('token'));

        window.myFunction = function () {
            const verifyButton = $('#verify');
            const result = $('#result');

            result.removeClass('text-danger text-success').text('Verifying license…').show();
            verifyButton.prop('disabled', true).text('Verifying…');

            $.ajax({
                url: verifyUrl,
                headers: {
                    license: $('#license').val(),
                    token: verifyToken,
                },
                success: function (response) {
                    const instructor = response.data ?? {};
                    const instructorName = [instructor.first_name, instructor.middle_name, instructor.last_name]
                        .filter(Boolean)
                        .join(' ');

                    $('#name').val(instructorName);
                    $('#license').val(instructor.license ?? $('#license').val());
                    $('#email').val(instructor.email ?? '');
                    $('#phone').val(instructor.phone ?? '');
                    $('#waacsp_url').val(instructor.url ?? '');
                    $('#picture').val(instructor.avatar ?? '');

                    if (instructor.avatar) {
                        $('#profile_picture img').attr('src', instructor.avatar);
                        $('#profile_picture').show();
                    }

                    if (profileEditor) {
                        profileEditor.setData(instructor.short_profile ?? '');
                    }

                    result.removeClass('text-danger').addClass('text-success').text('License verified.');
                    verifyButton.removeClass('btn-info').addClass('btn-success').prop('disabled', false).text('Verified');
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message ?? 'Unable to verify this license. Please try again.';

                    result.removeClass('text-success').addClass('text-danger').text(message).show();
                    verifyButton.prop('disabled', false).text('Try again');
                },
            });
        };

        $('#license').on('input', function () {
            $('#verify').removeClass('btn-success').addClass('btn-info').prop('disabled', false).text('Verify license');
            $('#result').text('').hide();
        });
    });
</script>
