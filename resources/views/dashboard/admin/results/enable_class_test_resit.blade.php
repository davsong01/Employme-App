@php
    $resitStatus = $user->training_result->class_test_resit_status ?? null;
    $resitExpiry = $user->training_result->class_test_resit_expiry ?? null;
@endphp

@if(in_array($resitStatus, [0,2]))
    @if($permissions['results.destroy'])
        <small class="resit-status">
            <form onsubmit="return confirm('This will delete this user class test details and enable tests to be re-taken. Are you sure?');" 
                action="{{ route('classtests.results.destroy', ['id' => $user->id, 'result' => $user->id,'p_id' => $user->program_id]) }}" 
                method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="uid" value="{{ $user->user_id }}">
                <input type="hidden" name="rid" value="{{ $user->result_id }}">
                <input type="hidden" name="pid" value="{{ $user->program_id }}">
                <input type="hidden" name="override_resit" value="yes">
                <button type="submit" class="btn btn-danger btn-sm w-50">
                    <i class="fa fa-redo"></i> Enable Resit
                </button>
            </form>
        </small>
    @endif

@elseif($resitStatus == 1 && $resitExpiry)
    @php
        $parsedDate = \Carbon\Carbon::parse($resitExpiry);
        $isActive = $parsedDate >= now();
    @endphp

    @if($isActive)
        <small class="resit-status d-block mb-1">
            <strong style="color:red">Resit In Progress!</strong>
        </small>
        <small class="resit-status d-block mb-2">
            <strong style="color:red">Resit Expires on:</strong> {{ $parsedDate->format('Y-m-d H:i') }}
        </small>
    @else
        @if($permissions['results.destroy'])
            <small class="resit-status">
                <form onsubmit="return confirm('This will delete this user class test details and enable test to be re-taken. Are you sure?');" 
                    action="{{ route('classtests.results.destroy', ['id' => $user->id, 'result' => $user->id,'p_id' => $user->program_id]) }}" 
                    method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="uid" value="{{ $user->user_id }}">
                    <input type="hidden" name="rid" value="{{ $user->result_id }}">
                    <input type="hidden" name="pid" value="{{ $user->program_id }}">
                    <input type="hidden" name="override_resit" value="yes">
                    <button type="submit" class="btn btn-dark btn-sm w-50">
                        <i class="fa fa-redo"></i> Re-Enable Resit
                    </button>
                </form>
            </small>
        @endif
    @endif
@endif