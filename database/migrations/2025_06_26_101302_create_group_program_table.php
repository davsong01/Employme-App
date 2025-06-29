<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_program', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id');
            $table->integer('program_id'); // linked programs
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('group_program');
    }
};
