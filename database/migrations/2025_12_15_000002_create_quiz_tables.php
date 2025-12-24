<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quiz_questions')) {
            Schema::create('quiz_questions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exercise_id');
                $table->text('question');
                $table->timestamps();

                $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('quiz_options')) {
            Schema::create('quiz_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('question_id');
                $table->string('option_text');
                $table->boolean('is_correct')->default(false);
                $table->timestamps();

                $table->foreign('question_id')->references('id')->on('quiz_questions')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exercise_id');
                $table->unsignedBigInteger('mahasiswa_id');
                $table->integer('score')->default(0);
                $table->dateTime('started_at')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->timestamps();

                $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
                $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('quiz_answers')) {
            Schema::create('quiz_answers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attempt_id');
                $table->unsignedBigInteger('question_id');
                $table->unsignedBigInteger('option_id')->nullable();
                $table->timestamps();

                $table->foreign('attempt_id')->references('id')->on('quiz_attempts')->onDelete('cascade');
                $table->foreign('question_id')->references('id')->on('quiz_questions')->onDelete('cascade');
                $table->foreign('option_id')->references('id')->on('quiz_options')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
    }
};

