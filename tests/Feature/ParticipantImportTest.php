<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ParticipantImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.url', 'http://localhost');
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::create('programs', function (Blueprint $table): void {
            $table->id();
            $table->string('p_name');
            $table->decimal('p_amount', 11, 2);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('roles')->nullable();
            $table->timestamps();
        });

        Schema::create('program_user', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('user_id');
            $table->string('transid')->nullable();
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
        });

        Schema::create('temp_transactions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();

            foreach (['email', 'type', 'payment_type', 'program_id', 'coupon_id', 'facilitator_id', 'amount', 'discount', 'transid', 'invoice_id', 'payment_mode', 'preferred_timing', 'name', 'phone', 'location', 'training_mode', 'meta', 'is_package', 'program_ids', 'status', 't_type', 'balance', 'currency', 'currency_symbol', 'exchange_rate', 'remarks', 'coupon_code', 'coupon_amount', 'expected_amount'] as $column) {
                $table->text($column)->nullable();
            }

            $table->timestamps();
        });

        Schema::create('payment_threads', function (Blueprint $table): void {
            $table->id();

            foreach (['program_id', 'admin_id', 'user_id', 'payment_id', 'transaction_id', 't_type', 'parent_transaction_id', 'amount'] as $column) {
                $table->text($column)->nullable();
            }

            $table->timestamps();
        });

        $admin = new Admin(['roles' => ['Admin']]);
        $admin->id = 1;
        $this->actingAs($admin, 'admin');
        $this->withoutMiddleware();
    }

    public function test_existing_enrollment_does_not_roll_back_later_participants(): void
    {
        $program = Program::create(['p_name' => 'Example program', 'p_amount' => 1000]);
        $existingUser = User::create(['name' => 'Existing participant', 'email' => 'existing@example.com']);
        $existingUser->programs()->attach($program->id);

        $response = $this->from('http://localhost/admin/import')->post('http://localhost/admin/import-training-participant', [
            'p_id' => $program->id,
            'send_email' => 'no',
            'manual_participants' => [
                ['name' => 'Existing participant', 'email' => 'existing@example.com'],
                ['name' => 'New participant', 'email' => 'new@example.com'],
            ],
        ]);

        $response->assertRedirect('/admin/import');
        $response->assertSessionHas('message', fn (string $message): bool => str_contains($message, '2 participants processed (1 newly enrolled, 1 already enrolled); 0 failed.'));
        $response->assertSessionMissing('error');

        $this->assertDatabaseHas('program_user', ['program_id' => $program->id, 'user_id' => User::where('email', 'new@example.com')->value('id')]);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_duplicate_only_import_counts_existing_enrollment_as_success(): void
    {
        $program = Program::create(['p_name' => 'Example program', 'p_amount' => 1000]);
        $existingUser = User::create(['name' => 'Existing participant', 'email' => 'existing@example.com']);
        $existingUser->programs()->attach($program->id);

        $response = $this->from('http://localhost/admin/import')->post('http://localhost/admin/import-training-participant', [
            'p_id' => $program->id,
            'send_email' => 'no',
            'manual_participants' => [
                ['name' => 'Existing participant', 'email' => 'existing@example.com'],
            ],
        ]);

        $response->assertRedirect('/admin/import');
        $response->assertSessionHas('message', fn (string $message): bool => str_contains($message, '1 participant processed (0 newly enrolled, 1 already enrolled); 0 failed.'));
        $response->assertSessionMissing('error');

        $this->assertDatabaseCount('temp_transactions', 0);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_existing_user_without_program_is_enrolled(): void
    {
        $program = Program::create(['p_name' => 'Example program', 'p_amount' => 1000]);
        $existingUser = User::create(['name' => 'Existing participant', 'email' => 'existing@example.com']);

        $response = $this->from('http://localhost/admin/import')->post('http://localhost/admin/import-training-participant', [
            'p_id' => $program->id,
            'send_email' => 'no',
            'manual_participants' => [
                ['name' => 'Existing participant', 'email' => 'existing@example.com'],
            ],
        ]);

        $response->assertRedirect('/admin/import');
        $response->assertSessionHas('message', fn (string $message): bool => str_contains($message, '1 participant processed (1 newly enrolled, 0 already enrolled); 0 failed.'));

        $this->assertDatabaseHas('program_user', ['program_id' => $program->id, 'user_id' => $existingUser->id]);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_user_creation_failure_is_reported_and_next_row_is_enrolled(): void
    {
        $program = Program::create(['p_name' => 'Example program', 'p_amount' => 1000]);
        DB::statement("CREATE TRIGGER fail_user_insert BEFORE INSERT ON users WHEN NEW.email = 'fail@example.com' BEGIN SELECT RAISE(ABORT, 'blocked'); END");

        $response = $this->from('http://localhost/admin/import')->post('http://localhost/admin/import-training-participant', [
            'p_id' => $program->id,
            'send_email' => 'no',
            'manual_participants' => [
                ['name' => 'Failed participant', 'email' => 'fail@example.com'],
                ['name' => 'New participant', 'email' => 'new@example.com'],
            ],
        ]);

        $response->assertRedirect('/admin/import');
        $response->assertSessionHas('message', fn (string $message): bool => str_contains($message, '1 participant processed (1 newly enrolled, 0 already enrolled); 1 failed.'));
        $response->assertSessionHas('error', fn (string $message): bool => str_contains($message, 'fail@example.com'));

        $this->assertDatabaseMissing('users', ['email' => 'fail@example.com']);
        $this->assertDatabaseHas('program_user', ['program_id' => $program->id, 'user_id' => User::where('email', 'new@example.com')->value('id')]);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_program_attachment_failure_is_reported_and_next_row_is_enrolled(): void
    {
        $program = Program::create(['p_name' => 'Example program', 'p_amount' => 1000]);
        $existingUser = User::create(['name' => 'Existing participant', 'email' => 'existing@example.com']);
        DB::statement('CREATE TRIGGER fail_program_attach BEFORE INSERT ON program_user WHEN NEW.user_id = '.$existingUser->id." BEGIN SELECT RAISE(ABORT, 'blocked'); END");

        $response = $this->from('http://localhost/admin/import')->post('http://localhost/admin/import-training-participant', [
            'p_id' => $program->id,
            'send_email' => 'no',
            'manual_participants' => [
                ['name' => 'Existing participant', 'email' => 'existing@example.com'],
                ['name' => 'New participant', 'email' => 'new@example.com'],
            ],
        ]);

        $response->assertRedirect('/admin/import');
        $response->assertSessionHas('message', fn (string $message): bool => str_contains($message, '1 participant processed (1 newly enrolled, 0 already enrolled); 1 failed.'));
        $response->assertSessionHas('error', fn (string $message): bool => str_contains($message, 'existing@example.com'));

        $this->assertDatabaseMissing('program_user', ['program_id' => $program->id, 'user_id' => $existingUser->id]);
        $this->assertDatabaseHas('program_user', ['program_id' => $program->id, 'user_id' => User::where('email', 'new@example.com')->value('id')]);
        $this->assertSame(0, DB::transactionLevel());
    }
}
