<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exercise_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');
            $table->unsignedBigInteger('exercise_id');
            $table->decimal('score', 5, 2);
            $table->dateTime('attempted_at');
            $table->enum('status', ['lulus', 'tidak_lulus']);
            $table->integer('attempt_number')->default(1);
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            
            $table->index(['mahasiswa_id', 'exercise_id']);
            $table->index('attempted_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_results');
    }
};