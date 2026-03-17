<form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure?');"
    action="{{ route('results.destroy', ['id' => $user->id, 'result' => $user->id, 'p_id' => $user->program_id]) }}"
    method="POST">

    @csrf
    @method('DELETE')

    <input type="hidden" name="uid" value="{{ $user->user_id }}">
    <input type="hidden" name="rid" value="{{ $user->result_id }}">
    <input type="hidden" name="pid" value="{{ $user->program_id }}">
    <input type="hidden" name="override_resit" value="yes">

    <button type="submit" class="btn {{ $btnClass }} btn-sm w-50">
        <i class="fa fa-redo"></i> {{ $label }}
    </button>
</form>