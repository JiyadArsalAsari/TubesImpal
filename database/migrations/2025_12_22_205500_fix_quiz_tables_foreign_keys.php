<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the foreign key constraint exists and drop it
        try {
            DB::statement('ALTER TABLE quiz_questions DROP FOREIGN KEY quiz_questions_exercise_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist, continue anyway
        }

        try {
            DB::statement('ALTER TABLE quiz_attempts DROP FOREIGN KEY quiz_attempts_exercise_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist, continue anyway
        }

        // Check what table the foreign key is currently referencing
        try {
            $result = DB::select("SELECT REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = 'studyflow' AND TABLE_NAME = 'quiz_questions' AND COLUMN_NAME = 'exercise_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
            
            if (!empty($result) && $result[0]->REFERENCED_TABLE_NAME !== 'exercises') {
                // The foreign key is referencing the wrong table, we need to drop and recreate it
                // First drop the column constraint
                DB::statement('ALTER TABLE quiz_questions DROP INDEX quiz_questions_exercise_id_foreign');
            }
        } catch (\Exception $e) {
            // Continue anyway
        }

        try {
            $result = DB::select("SELECT REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = 'studyflow' AND TABLE_NAME = 'quiz_attempts' AND COLUMN_NAME = 'exercise_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
            
            if (!empty($result) && $result[0]->REFERENCED_TABLE_NAME !== 'exercises') {
                // The foreign key is referencing the wrong table, we need to drop and recreate it
                // First drop the column constraint
                DB::statement('ALTER TABLE quiz_attempts DROP INDEX quiz_attempts_exercise_id_foreign');
            }
        } catch (\Exception $e) {
            // Continue anyway
        }

        // Recreate the foreign key constraints to reference the exercises table
        try {
            DB::statement('ALTER TABLE quiz_questions ADD CONSTRAINT quiz_questions_exercise_id_foreign FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Constraint might already exist correctly
        }

        try {
            DB::statement('ALTER TABLE quiz_attempts ADD CONSTRAINT quiz_attempts_exercise_id_foreign FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Constraint might already exist correctly
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the corrected foreign key constraints
        try {
            DB::statement('ALTER TABLE quiz_questions DROP FOREIGN KEY quiz_questions_exercise_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        try {
            DB::statement('ALTER TABLE quiz_attempts DROP FOREIGN KEY quiz_attempts_exercise_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist
        }
    }
};