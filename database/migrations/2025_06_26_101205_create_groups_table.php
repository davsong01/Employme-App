<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if(!Schema::hasTable('groups')){
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->string('p_name');
                $table->string('p_abbr');
                $table->decimal('p_amount', 10, 2)->nullable();
                $table->decimal('e_amount', 10, 2)->nullable();
                $table->date('p_start')->nullable();
                $table->date('p_end')->nullable();
                $table->string('image')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('early_bird_status')->default(0);
                $table->json('currencies')->nullable(); // e.g. {"USD":100,"NGN":50000}
                $table->json('meta')->nullable(); // flexible metadata
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('groups');
    }
};
