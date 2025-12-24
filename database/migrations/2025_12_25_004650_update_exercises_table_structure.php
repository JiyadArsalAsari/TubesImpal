<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns that are expected by subsequent migrations
        if (!Schema::hasColumn('exercises', 'dosen_id')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->unsignedBigInteger('dosen_id');
            });
        }

        if (!Schema::hasColumn('exercises', 'mahasiswa_id')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->unsignedBigInteger('mahasiswa_id')->nullable();
            });
        }

        if (!Schema::hasColumn('exercises', 'type')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->enum('type', ['quiz', 'assignment']);
            });
        }

        if (!Schema::hasColumn('exercises', 'deadline')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->dateTime('deadline')->nullable();
            });
        }

        if (!Schema::hasColumn('exercises', 'duration_minutes')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->integer('duration_minutes')->nullable();
            });
        }

        if (!Schema::hasColumn('exercises', 'link')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->string('link')->nullable();
            });
        }

        if (!Schema::hasColumn('exercises', 'status')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published'])->default('published');
            });
        }

        // Add max_score if it doesn't exist
        if (!Schema::hasColumn('exercises', 'max_score')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->integer('max_score')->default(100);
            });
        }


        // Update foreign keys
        Schema::table('exercises', function (Blueprint $table) {
            // We do not need to add dosen_id foreign key as it is already created in the create_exercises_table migration
            // and the definition is the same (cascade).

            // However, we want to update mahasiswa_id FK to 'set null' instead of 'cascade'.
            // First we need to drop the existing foreign key.
            try {
                $table->dropForeign(['mahasiswa_id']);
            } catch (\Exception $e) {
                // connection/driver might throw if not exists, but usually we prefer to check. 
                // In standard Laravel mysql driver, dropForeign by array generates name 'exercises_mahasiswa_id_foreign'.
            }

            if (Schema::hasColumn('exercises', 'mahasiswa_id')) {
                $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('set null');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropForeign(['dosen_id']);
            if (Schema::hasColumn('exercises', 'mahasiswa_id')) {
                $table->dropForeign(['mahasiswa_id']);
            }

            $table->dropColumn([
                'dosen_id',
                'mahasiswa_id',
                'type',
                'deadline',
                'duration_minutes',
                'link',
                'status',
                'max_score'
            ]);
        });
    }
};
