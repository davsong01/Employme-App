@php
    $resitStatus = $user->training_result->class_test_resit_status ?? null;
    $resitExpiry = $user->training_result->class_test_resit_expiry ?? null;
    $parsedDate = $resitExpiry ? \Carbon\Carbon::parse($resitExpiry) : null;
    $canDestroy = $permissions['results.destroy'] ?? false;
@endphp

@if($canDestroy)
    @if(in_array($resitStatus, [0,2]) || (!$resitStatus && !$parsedDate))
        {{-- Resit never taken, allow enable --}}
        <form onsubmit="return confirm('This will enable a resit for this user. Are you sure?');" 
              action="{{ route('classtests.results.destroy', ['id' => $user->id, 'result' => $user->id, 'p_id' => $user->program_id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="uid" value="{{ $user->user_id }}">
            <input type="hidden" name="rid" value="{{ $user->result_id }}">
            <input type="hidden" name="pid" value="{{ $user->program_id }}">
            <input type="hidden" name="override_resit" value="yes">
            <button type="submit" class="btn btn-danger btn-sm w-50 mb-1">
                <i class="fa fa-redo"></i> Enable Resit
            </button>
        </form>

    @elseif($resitStatus == 1 && $parsedDate)
        @if($parsedDate >= now())
            {{-- Resit in progress --}}
            <small class="resit-status d-block mb-1 text-danger"><strong>Resit In Progress!</strong></small>
            <small class="resit-status d-block mb-2">
                <strong style="color:red">Expires on:</strong> {{ $parsedDate->format('Y-m-d H:i') }}
            </small>
        @else
            {{-- Resit expired --}}
            <form onsubmit="return confirm('This will re-enable a resit for this user. Are you sure?');" 
                  action="{{ route('classtests.results.destroy', ['id' => $user->id, 'result' => $user->id, 'p_id' => $user->program_id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="uid" value="{{ $user->user_id }}">
                <input type="hidden" name="rid" value="{{ $user->result_id }}">
                <input type="hidden" name="pid" value="{{ $user->program_id }}">
                <input type="hidden" name="override_resit" value="yes">
                <button type="submit" class="btn btn-dark btn-sm w-50 mb-1">
                    <i class="fa fa-redo"></i> Re-Enable Resit
                </button>
            </form>
        @endif
    @endif
@endif