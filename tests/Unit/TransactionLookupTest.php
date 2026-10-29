<?php

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransactionLookupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::create('program_user', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('program_id');
            $table->decimal('amount', 11, 2)->default(0);
            $table->decimal('balance', 11, 2)->default(0);
            $table->string('currency_symbol')->nullable();
            $table->timestamps();
        });

        Schema::create('temp_transactions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('program_ids')->nullable();
            $table->decimal('amount', 11, 2)->default(0);
            $table->decimal('balance', 11, 2)->default(0);
            $table->string('currency_symbol')->nullable();
            $table->timestamps();
        });
    }

    public function test_current_program_user_payment_overrides_stale_temp_transaction(): void
    {
        DB::table('temp_transactions')->insert([
            'user_id' => 7,
            'program_ids' => json_encode([126]),
            'amount' => 35000,
            'balance' => 35000,
            'currency_symbol' => '₦',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        DB::table('program_user')->insert([
            'user_id' => 7,
            'program_id' => 126,
            'amount' => 70000,
            'balance' => 0,
            'currency_symbol' => '₦',
            'created_at' => now()->subDay(),
            'updated_at' => now(),
        ]);

        $transaction = getTransactionFromProgramIds(126, 7);

        $this->assertSame(70000.0, (float) $transaction->amount);
        $this->assertSame(0.0, (float) $transaction->balance);
    }
}
