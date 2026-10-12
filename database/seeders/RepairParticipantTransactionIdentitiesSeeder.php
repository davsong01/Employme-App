<?php

namespace Database\Seeders;

use App\Models\PaymentThread;
use App\Models\TempTransaction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RepairParticipantTransactionIdentitiesSeeder extends Seeder
{
    public function run(): void
    {
        $scanned = 0;
        $repaired = 0;
        $unmatched = 0;

        // Explicit corrections for legacy records whose stored participant
        // identity is known to be wrong. Keep these mappings auditable and
        // only add entries when the intended email has been verified.
        $knownCorrections = [
            'BT-77079196' => 'uubom2007@gmail.com',
        ];

        foreach ($knownCorrections as $transid => $email) {
            $user = User::where('email', $email)->first();
            $transactions = TempTransaction::where('transid', $transid)->get();

            if ($transactions->isEmpty() || ! $user) {
                $this->command?->warn("Known correction skipped for {$transid}: transaction or user not found");
                continue;
            }

            DB::transaction(function () use ($transactions, $user, &$repaired) {
                foreach ($transactions as $transaction) {
                    $transaction->forceFill([
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?: $transaction->phone,
                    ])->save();

                    $repaired++;
                }

                Transaction::where('transid', $transactions->first()->transid)
                    ->update(['user_id' => $user->id]);
            });
        }

        $duplicatesRemoved = $this->removeDuplicateProgramTransactions();

        TempTransaction::query()
            ->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhereNull('name')
                    ->orWhere('name', '')
                    ->orWhereNull('email')
                    ->orWhere('email', '')
                    ->orWhereNull('phone')
                    ->orWhere('phone', '');
            })
            ->orderBy('id')
            ->chunkById(200, function ($transactions) use (&$scanned, &$repaired, &$unmatched) {
                foreach ($transactions as $transaction) {
                    $scanned++;
                    $user = $this->resolveUser($transaction);

                    if (!$user) {
                        $unmatched++;
                        $this->command?->warn("No participant match for transaction {$transaction->transid}");
                        continue;
                    }

                    DB::transaction(function () use ($transaction, $user, &$repaired) {
                        $updates = [
                            'user_id' => $user->id,
                        ];

                        if (blank($transaction->name)) {
                            $updates['name'] = $user->name;
                        }

                        if (blank($transaction->email)) {
                            $updates['email'] = $user->email;
                        }

                        if (blank($transaction->phone) && filled($user->phone)) {
                            $updates['phone'] = $user->phone;
                        }

                        $transaction->forceFill($updates)->save();

                        // Repair pivot rows that carry the same transaction reference
                        // but are missing their participant link.
                        Transaction::where('transid', $transaction->transid)
                            ->whereNull('user_id')
                            ->update(['user_id' => $user->id]);

                        $repaired++;
                    });
                }
            });

        $this->command?->info("Scanned: {$scanned}");
        $this->command?->info("Repaired: {$repaired}");
        $this->command?->info("Unmatched: {$unmatched}");
        $this->command?->info("Duplicate program transactions removed: {$duplicatesRemoved}");
    }

    /**
     * Keep one pivot row when the same participant/program was written more
     * than once with the same transaction and invoice. Different transaction
     * IDs are retained because they may represent legitimate payment events.
     */
    private function removeDuplicateProgramTransactions(): int
    {
        $removed = 0;
        $seen = [];

        DB::table('program_user')
            ->select('id', 'user_id', 'program_id', 'transid', 'invoice_id')
            ->whereNotNull('user_id')
            ->whereNotNull('program_id')
            ->whereNotNull('transid')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$removed, &$seen) {
                foreach ($rows as $row) {
                    $key = implode('|', [
                        $row->user_id,
                        $row->program_id,
                        $row->transid,
                        $row->invoice_id ?? '',
                    ]);

                    if (isset($seen[$key])) {
                        DB::table('program_user')->where('id', $row->id)->delete();
                        $removed++;
                        continue;
                    }

                    $seen[$key] = $row->id;
                }
            });

        return $removed;
    }

    private function resolveUser(TempTransaction $transaction): ?User
    {
        if (filled($transaction->email)) {
            $user = User::where('email', trim($transaction->email))->first();

            if ($user) {
                return $user;
            }
        }

        if ($transaction->user_id) {
            $user = User::find($transaction->user_id);

            if ($user) {
                return $user;
            }
        }

        $pivotUserId = Transaction::where('transid', $transaction->transid)
            ->whereNotNull('user_id')
            ->value('user_id');

        if ($pivotUserId) {
            $user = User::find($pivotUserId);

            if ($user) {
                return $user;
            }
        }

        $threadUserId = PaymentThread::where('parent_transaction_id', $transaction->transid)
            ->whereNotNull('user_id')
            ->value('user_id');

        return $threadUserId ? User::find($threadUserId) : null;
    }
}
