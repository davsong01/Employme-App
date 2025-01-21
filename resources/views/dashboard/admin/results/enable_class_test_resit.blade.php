{{-- @if(isset($user->training_result->class_test_resit_status))
<br>  --}}

    @if($user->training_result->class_test_resit_status == 0)
        <small class="resit-status">
            @if($permissions['results.destroy'])
                <form onsubmit="return confirm('This will delete this user class test details and enable tests to be re-taken. Are you sure you want to do this?');" 
                    action="{{ URL::signedRoute('classtests.results.destroy', ['id' => $user->id, 'result' => $user->id,'p_id' => $user->program_id]) }}" method="POST">
                    {{ csrf_field() }}
                    {{method_field('DELETE')}}
                    <input type="hidden" name="uid" value="{{ $user->user_id }}">
                    <input type="hidden" name="rid" value="{{ $user->result_id }}">
                    <input type="hidden" name="pid" value="{{ $user->program_id }}">
                    <input type="hidden" name="override_resit" value="yes">
                    <button type="submit" class="btn btn-danger btn-sm w-100"> 
                        <i class="fa fa-redo"> Enable Resit</i>
                    </button>
                </form>
            @endif
        </small>
    @elseif($user->training_result->class_test_resit_status == 1)
        @if(isset($user->training_result->class_test_resit_expiry))
            @php
                $parsedDate = \Carbon\Carbon::parse($user->training_result->class_test_resit_expiry);
            @endphp

            @if($parsedDate >= now())
                <small class="resit-status">
                    <button class="btn btn-danger btn-sm w-100" style="display: block;" disabled>Resit In Progress!</button>
                </small>
                <small class="resit-status">
                    <strong>Resit Expires on:</strong> {{$parsedDate}} <br>
                </small>
            @else   
                <small class="resit-status">
                    @if($permissions['results.destroy'])
                        <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" 
                            action="{{ URL::signedRoute('classtests.results.destroy', ['id' => $user->id, 'result' => $user->id,'p_id' => $user->program_id]) }}" method="POST">
                            {{ csrf_field() }}
                            {{method_field('DELETE')}}
                            <input type="hidden" name="uid" value="{{ $user->user_id }}">
                            <input type="hidden" name="rid" value="{{ $user->result_id }}">
                            <input type="hidden" name="pid" value="{{ $user->program_id }}">
                            <input type="hidden" name="override_resit" value="yes">
                            <button type="submit" class="btn btn-danger btn-sm w-100"> 
                                <i class="fa fa-redo"> Enable Resit</i>
                            </button>
                        </form>
                    @endif
                </small>
                <small class="resit-status">
                    <strong>Resit Expired on:</strong> {{$parsedDate}}
                </small>
            @endif
        @endif
    @endif
{{-- @endif --}}


