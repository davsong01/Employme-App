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
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong><span id="formErrors"></span></strong> 
                </div>
            </span>
            <h4 class="text-uppercase fw-bold">Invoice ID: {{$transaction->invoice_id}}</h4>
            <h6 class="text-muted">Transaction ID: {{ $transaction->transid }}</h6>
        </div>

        <!-- Edit Transaction Form -->
        <form id="editTransactionForm" action="{{route('payments.update', ['payment' => $transaction->id, 'program_amount' => $transaction->p_amount])}}" method="POST" enctype="multipart/form-data" class="pb-4">
            @csrf
            @method('PATCH')
            <div class="row mb-4">
                <!-- Transaction Details -->
                <div class="col-md-12">
            <div class="mb-3">
                        <p>
                            <div class="mb-2">
                                @if($transaction->is_package)
                                    <div class="mb-2">
                                        <span class="fw-bold">Package Name:</span>
                                        <span class="fw-medium"><a href="{{ route('groupedprogram.edit', $transaction->program_id)}}" target="_blank">{{ ucfirst($transaction->group->p_name) }}</a></span>
                                    </div>
                                @endif
                                <span class="fw-bold">
                                    Training{{ $transaction->is_package ? 's' : '' }}:
                                </span>
                                <div class="bg-light rounded p-2 mt-1">
                                    <ol class="mb-0 ps-3">
                                        @foreach ($transaction->allPrograms() as $child)
                                            <li><a href="{{ route('programs.edit', $child->id)}}" target="_blank" class="fw-bold text-primary">{{ $child->p_name}}</a></li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                            <strong>Expected Amount:</strong> {{ $transaction->currency_symbol. number_format($transaction->expected_amount) }} <br>
                            <strong>Name of Participant:</strong> {{ $transaction->user->name }}<br>
                            <strong>Account Balance:</strong> {{ $transaction->currency_symbol. number_format($transaction->user->account_balance) }} <br>
                            <strong>Channel:</strong> {{ $transaction->t_type }} <br>
                            <strong>Paid:</strong> {{ $transaction->currency_symbol. number_format($transaction->amount) }}
                            
                            <br>
                            <strong>Balance:</strong>
                            <span
                                id="transaction-modal-balance-wrap-{{ $transaction->id }}"
                                class="fw-bold {{ $transaction->balance > 0 ? 'text-danger' : 'text-success' }}"
                                data-currency-symbol="{{ $transaction->currency_symbol }}"
                            >
                                {{ $transaction->currency_symbol }} <span id="transaction-modal-balance-value-{{ $transaction->id }}">{{ number_format($transaction->balance) }}</span>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
{{-- programs --}}
            <div class="mb-4">
                <label for="location"><strong>Payment Type</strong></label>
                <select id="type" name="type" class="form-control @error('type') is-invalid @enderror">
                    <option value="">Select payment type</option>
                    <option value="part" {{ 'part' == $transaction->type ? 'selected' :''}}>Part</option>
                    <option value="full" {{ 'full' == $transaction->type ? 'selected' :''}}>Full</option>
                    <option value="earlybird" {{ 'earlybird' == $transaction->type ? 'selected' :''}}>Early Bird</option>
                   
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-4">
                <label for="expected_amount"><strong>Expected Amount</strong></label>
                <input type="number" name="expected_amount" value="{{ $transaction->expected_amount }}" class="form-control @error('expected_amount') is-invalid @enderror">
                @error('expected_amount')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-4">
                <label for="amount_paid"><strong>Amount Paid</strong></label>
                <input type="number" name="amount_paid" value="{{ $transaction->amount }}" class="form-control @error('amount_paid') is-invalid @enderror">
                @error('amount_paid')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-4">
                <label for="balance"><strong>Balance</strong></label>
                <input type="number" name="balance" value="{{ $transaction->balance }}" class="form-control @error('balance') is-invalid @enderror">
                @error('balance')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <!-- New Amount -->
            <div class="mb-4">
                <label for="amount"><strong>New Amount</strong></label>
                <input type="number" name="amount" value="{{ old('amount') ?? 0 }}" class="form-control @error('amount') is-invalid @enderror">
                @error('amount')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <input type="hidden" name="transaction_id" id="transactionId" value="{{$transaction->id}}">
            <!-- Location Field (if available) -->
            @if(isset($locations) && !empty($locations))
                <div class="mb-4">
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
                <div class="mb-4">
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

            <div class="mb-4">
                <label for="funds-source"><strong>Funds Source</strong></label>
                <select id="funds-source" name="funds_source" class="form-control" required>
                    <option value="Transfer" {{$transaction->t_type == 'Transfer' ? 'selected' : ''}}>Transfer</option>
                    <option value="Online" {{$transaction->t_type == 'Online' ? 'selected' : ''}}>Online</option>
                    <option value="Wallet" {{$transaction->t_type == 'Wallet' ? 'selected' : ''}}>Wallet</option>
                </select>
            </div>
            @if($permissions['payments.update'])
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-save"></i> Update
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
        const modalEl = document.getElementById('editSidebarModal');
        if (window.bootstrap && window.bootstrap.Modal && modalEl) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
            $('#editSidebarModal').modal('show');
        }
    });

    $('#editTransactionForm').on('submit', function (e) {
    e.preventDefault();
    const form = $(this); 
    const url = form.attr('action');
    const formData = new FormData(form[0]); 
 
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
                const newBalance = Number(response.new_balance);
                const formattedBalance = response.new_balance_formatted ?? response.new_balance;
                const balanceWrap = $(`#transaction-balance-wrap-` + response.transaction_id);
                const balanceValue = $(`#transaction-balance-value-` + response.transaction_id);
                const currencySymbol = balanceWrap.data('currency-symbol') || '₦';

                if (balanceValue.length) {
                    balanceValue.text(formattedBalance);
                } else if (balanceWrap.length) {
                    balanceWrap.html(`${currencySymbol} <span id="transaction-balance-value-${response.transaction_id}">${formattedBalance}</span>`);
                }

                if (newBalance > 0) {
                    balanceWrap.removeClass('text-success').addClass('text-danger');
                } else {
                    balanceWrap.removeClass('text-danger').addClass('text-success');
                }
                
                const modalEl = document.getElementById('editSidebarModal');
                if (window.bootstrap && window.bootstrap.Modal && modalEl) {
                    window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                } else {
                    $('#editSidebarModal').modal('hide');
                }

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
