<div class="row">
    <div class="col-md-6">
        <div class="mb-3{{ $errors->has('license') ? ' is-invalid' : '' }}">
            <label for="license">WAACSP license</label>
            <div class="d-flex gap-2">
                <input id="license" type="text" class="form-control" name="license" value="{{ old('license', $teacher?->license) }}">
                @if ($showLicenseVerification)
                    <span id="verify-button">
                        <button type="button" class="btn btn-info text-nowrap" id="verify" onclick="myFunction()">Verify license</button>
                    </span>
                @endif
            </div>
            @error('license')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            <div class="text-danger small mt-1" id="result"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3{{ $errors->has('waacsp_url') ? ' is-invalid' : '' }}">
            <label for="waacsp_url">WAACSP URL</label>
            <input id="waacsp_url" type="text" class="form-control" name="waacsp_url" value="{{ old('waacsp_url', $teacher?->waacsp_url) }}">
            @error('waacsp_url')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="off_season_availability">Available off season?</label>
            <select name="off_season_availability" id="off_season_availability" class="form-control">
                <option value="" {{ old('off_season_availability', $teacher?->off_season_availability) == '' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('off_season_availability', $teacher?->off_season_availability) == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="mb-3{{ $errors->has('profile') ? ' is-invalid' : '' }}">
            <label for="ckeditor">Profile overview</label>
            <textarea id="ckeditor" class="form-control" name="profile" rows="8">{{ old('profile', $teacher?->profile) }}</textarea>
            @error('profile')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
