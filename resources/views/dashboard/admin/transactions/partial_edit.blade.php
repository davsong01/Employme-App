@php
    $checks = ['payments.update'];
    $permissions = canUserAccessPermission($checks);
@endphp
<div class="row">
    <div class="container-fluid">
    <!-- Alerts Section -->
            <div class="card-title mb-4">
                {{-- @include('layouts.partials.alerts') --}}

                <span id ="formErrorSpan" style="display:none">
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <strong><span id="formErrors"></span></strong> 
                    </div>
                </span>
                <h4 class="text-uppercase font-weight-bold">Invoice ID: {{$transaction->invoice_id}}</h4>
                <h6 class="text-muted">Transaction ID: {{ $transaction->transid }}</h6>
            </div>

            <!-- Edit Transaction Form -->
            <form id="editTransactionForm" action="{{route('payments.update', ['payment' => $transaction->id, 'program_amount' => $transaction->p_amount])}}" method="POST" enctype="multipart/form-data" class="pb-4">
                @csrf
                @method('PATCH')
                <div class="row mb-4">
                    <!-- Transaction Details -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <p>
                                <strong>Program Name:</strong> {{ $transaction->p_name }} <br>
                                <strong>Program Amount:</strong> {{ \App\Models\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->p_amount) }} <br>
                                <label for="name"><strong>Name of Participant:</strong> {{ $transaction->name }}</label> <br>
                                <strong>Account Balance:</strong> {{ \App\Models\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->user->account_balance) }} <br>
                                <strong>Bank:</strong> {{ $transaction->t_type }} <br>
                                <strong>Paid:</strong> {{ \App\Models\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->t_amount) }}
                                @if($transaction->paymentthreads->count() > 0)
                                    <a class="btn btn-info btn-sm" href="javascript:void(0)" data-toggle="modal" data-bs-target="#payment-trail{{$transaction->transid }}"><i class="fa fa-eye"></i> View Payment Trail</a>
                                    
                                @endif
                                <br>
                                <strong>Balance:</strong> <span class="font-weight-bold" style="color:{{ $transaction->balance > 0 ? 'red' : 'green'}}">{{ \App\Models\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->balance) }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- New Amount -->
                <div class="form-group mb-4">
                    <label for="amount"><strong>New Amount</strong></label>
                    <input type="number" name="amount" value="{{ old('amount') ?? 0 }}" class="form-control @error('amount') is-invalid @enderror">
                    @error('amount')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <input type="hidden" name="transaction_id" id="transactionId" value="{{$transaction->id}}">
                <!-- Location Field (if available) -->
                @if(isset($locations) && !empty($locations))
                    <div class="form-group mb-4">
                        <label for="location"><strong>Location</strong></label>
                        <select id="location" name="location" class="form-control @error('location') is-invalid @enderror">
                            <option value="">Select Location</option>
                            @foreach ($locations as $location => $value)
                                <option value="{{ $location }}" {{ $location == $transaction->t_location ? 'selected' :''}}>{{ $location }}</option>
                            @endforeach
                        </select>
                        @error('location')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                @endif

                <!-- Coupon Field (if available) -->
                @if(isset($coupons) && $coupons->count() > 0)
                    <div class="form-group mb-4">
                        <label for="coupon_id"><strong>Coupon Used</strong></label>
                        <select id="coupon_id" name="coupon_id" class="form-control @error('coupon_id') is-invalid @enderror">
                            <option value="">Select Coupon</option>
                            @foreach ($coupons as $coupon)
                                <option value="{{ $coupon->id }}" {{ $coupon->id == $transaction->coupon_id ? 'selected' :''}}>{{ $coupon->code }} ({{ number_format($coupon->amount) }})</option>
                            @endforeach
                        </select>
                        @error('coupon_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                @endif

                <div class="form-group mb-4">
                    <label for="funds-source"><strong>Funds Source</strong></label>
                    <select id="funds-source" name="funds_source" class="form-control" required>
                        <option value="offline" selected>Offline Payment</option>
                        <option value="wallet">Wallet</option>
                    </select>
                </div>
                @if($permissions['payments.update'])
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </div>
                @endif
            </form>
        </div>
</div>
<script>
    $(document).on('click', '.open-modal', function () {
        const transactionId = $(this).data('id');
        $('#transactionId').val(transactionId);
        $('#editSidebarModal').modal('show'); 
    });

    $('#editTransactionForm').on('submit', function (e) {
    e.preventDefault();
    const form = $(this); 
    const url = form.attr('action');
    const formData = new FormData(form[0]); 
        // console.log(formData );
        //     return alert('loog');
    // Clear previous errors
    $('#formErrors').html('');
    $('#formErrorSpan').hide();

    $.ajax({
        url: url, 
        method: 'POST', 
        data: formData, 
        processData: false, 
        contentType: false, 
        success: function (response) {
            if (response.success) {
                // Close the modal
                $(`#transaction-amount-` + response.transaction_id).text(response.new_amount);
                var newBalance = response.new_balance;

                if (newBalance > 0) {
                    $(`#transaction-balance-red-` + response.transaction_id).text(newBalance);
                    $(`#transaction-balance-greenspan-` + response.transaction_id).hide();
                    $(`#transaction-balance-redspan-` + response.transaction_id).show();
                } else {
                    $(`#transaction-balance-green-` + response.transaction_id).text(newBalance);
                    $(`#transaction-balance-greenspan-` + response.transaction_id).show();
                    $(`#transaction-balance-greenspan-` + response.transaction_id).hide();
                }
                
                $('#editSidebarModal').modal('hide');

                window.location.href = window.location.href.split('#')[0] + '#transaction-row-' + response.transaction_id;
                
                $('#formSuccessSpan-' + response.transaction_id).show();
                $('.formSuccess').html(response.message || 'Transaction Updated Successfully');

            } else {
                // Handle any errors returned from the server
                $('#formErrorSpan').show();
                $('#formErrors').html(response.message || 'An error occurred.');
            }
        },
        error: function (xhr) {
            // Display validation errors
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                const errorMessages = Object.values(errors).map(err => `<div>${err}</div>`).join('');
                $('#formErrors').html(errorMessages);
            } else {
                $('#formErrors').html('An unexpected error occurred.');
            }
        }
    });
});



</script>