<?php

namespace Database\Seeders;

use App\Models\PaymentThread;
use App\Models\Program;
use App\Models\TempTransaction;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillLegacyProgramUserTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $backfilled = 0;
        $skippedExisting = 0;
        $skippedInvalid = 0;

        DB::table('program_user')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$backfilled, &$skippedExisting, &$skippedInvalid) {
                foreach ($rows as $row) {
                    $user = User::find($row->user_id);
                    $program = Program::find($row->program_id);

                    if (! $user || ! $program) {
                        $skippedInvalid++;
                        continue;
                    }

                    $hasTransaction = TempTransaction::query()
                        ->where('user_id', $user->id)
                        ->where('status', 'complete')
                        ->where(function ($query) use ($program) {
                            $query->where('program_id', $program->id)
                                ->orWhereJsonContains('program_ids', $program->id);
                        })
                        ->exists();

                    if (! $hasTransaction && ! empty($row->transid)) {
                        $hasTransaction = TempTransaction::where('transid', $row->transid)->exists();
                    }

                    if (! $hasTransaction && ! empty($row->invoice_id)) {
                        $hasTransaction = TempTransaction::where('invoice_id', $row->invoice_id)->exists();
                    }

                    if ($hasTransaction) {
                        $skippedExisting++;
                        continue;
                    }

                    $amountPaid = (float) ($row->amount ?? 0);
                    $balance = max(0, (float) ($row->balance ?? 0));
                    $expectedAmount = $amountPaid + $balance;
                    $paymentType = $this->resolvePaymentType($row->paymenttype ?? null, $balance);
                    $transid = $row->transid ?: PaymentService::getReference('BFILL');
                    $invoiceId = $row->invoice_id ?: PaymentService::getInvoiceId($row->id);
                    $createdAt = $row->created_at ?? now();
                    $updatedAt = $row->updated_at ?? $createdAt;
                    $location = $row->t_location ?? null;

                    $transaction = TempTransaction::create([
                        'expected_amount' => $expectedAmount,
                        'email' => $user->email,
                        'type' => $paymentType,
                        'payment_type' => $paymentType,
                        'program_id' => $program->id,
                        'coupon_id' => null,
                        'facilitator_id' => $row->facilitator_id ?? null,
                        'amount' => $amountPaid,
                        'discount' => null,
                        'transid' => $transid,
                        'invoice_id' => $invoiceId,
                        'payment_mode' => 0,
                        'preferred_timing' => null,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'location' => $location,
                        'training_mode' => null,
                        'meta' => null,
                        'is_package' => 0,
                        'program_ids' => [$program->id],
                        'status' => 'complete',
                        't_type' => $row->t_type ?? 'Transfer',
                        'balance' => $balance,
                        'currency' => 'NGN',
                        'currency_symbol' => '₦',
                        'exchange_rate' => $row->exchange_rate ?? 1,
                        'remarks' => $row->remarks ?? null,
                        'coupon_code' => null,
                        'coupon_amount' => null,
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ]);

                    PaymentThread::firstOrCreate(
                        [
                            'program_id' => $program->id,
                            'user_id' => $user->id,
                            'payment_id' => $transaction->id,
                            'transaction_id' => $transaction->transid,
                            'parent_transaction_id' => $transaction->transid,
                        ],
                        [
                            'admin_id' => null,
                            't_type' => strtolower((string) ($transaction->t_type ?? 'transfer')),
                            'amount' => $transaction->amount,
                            'created_at' => $createdAt,
                            'updated_at' => $updatedAt,
                        ]
                    );

                    $backfilled++;
                }
            });

        $message = sprintf(
            'BackfillLegacyProgramUserTransactionsSeeder completed: %d records backfilled, %d skipped because a transaction already existed, %d skipped because user/program was missing.',
            $backfilled,
            $skippedExisting,
            $skippedInvalid
        );

        Log::error($message);
        if (isset($this->command) && $this->command) {
            $this->command->info($message);
        } else {
            echo $message . PHP_EOL;
        }
    }

    private function resolvePaymentType($pivotPaymentType, float $balance): string
    {
        $paymentType = strtolower(trim((string) $pivotPaymentType));

        return match ($paymentType) {
            'eb', 'earlybird' => 'earlybird',
            'part', 'partial' => 'part',
            default => $balance > 0 ? 'part' : 'full',
        };
    }
}
