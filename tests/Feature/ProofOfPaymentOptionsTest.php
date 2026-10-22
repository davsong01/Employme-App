<?php

namespace Tests\Feature;

use App\Http\Controllers\PopController;
use App\Models\Group;
use App\Models\Program;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProofOfPaymentOptionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::create('programs', function (Blueprint $table): void {
            $table->id();
            $table->string('p_name');
            $table->date('p_end')->nullable();
            $table->decimal('p_amount', 11, 2)->default(0);
            $table->integer('status')->default(1);
            $table->string('is_closed')->default('no');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table): void {
            $table->id();
            $table->string('p_name');
            $table->date('p_end')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('group_program', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('program_id');
            $table->timestamps();
        });
    }

    public function test_only_current_trainings_are_offered_for_proof_of_payment(): void
    {
        $currentTraining = Program::create(['id' => 2, 'p_name' => 'Current training', 'p_end' => now()->toDateString()]);
        Program::create(['id' => 3, 'p_name' => 'Past training', 'p_end' => now()->subDay()->toDateString()]);
        Program::create(['id' => 4, 'p_name' => 'Closed training', 'p_end' => now()->addDay()->toDateString(), 'is_closed' => 'yes']);

        $view = app(PopController::class)->create();

        $this->assertSame([$currentTraining->id], $view->getData()['trainings']->modelKeys());
    }

    public function test_packages_require_a_current_training_and_an_unexpired_package_date(): void
    {
        $currentTraining = Program::create(['id' => 2, 'p_name' => 'Current training', 'p_end' => now()->toDateString()]);
        $pastTraining = Program::create(['id' => 3, 'p_name' => 'Past training', 'p_end' => now()->subDay()->toDateString()]);
        $futureTraining = Program::create(['id' => 4, 'p_name' => 'Future training', 'p_end' => now()->addDay()->toDateString()]);

        $currentPackage = Group::create(['p_name' => 'Current package', 'p_end' => now()->toDateString()]);
        $currentPackage->programs()->attach($currentTraining);

        $undatedCurrentPackage = Group::create(['p_name' => 'Undated current package']);
        $undatedCurrentPackage->programs()->attach($futureTraining);

        $pastPackage = Group::create(['p_name' => 'Past package', 'p_end' => now()->subDay()->toDateString()]);
        $pastPackage->programs()->attach($futureTraining);

        $undatedPastPackage = Group::create(['p_name' => 'Undated past package']);
        $undatedPastPackage->programs()->attach($pastTraining);

        $futurePackageWithPastTraining = Group::create(['p_name' => 'Future package with past training', 'p_end' => now()->addDay()->toDateString()]);
        $futurePackageWithPastTraining->programs()->attach($pastTraining);

        $view = app(PopController::class)->create();

        $this->assertSame(
            [$currentPackage->id, $undatedCurrentPackage->id],
            $view->getData()['groups']->modelKeys()
        );
    }
}
