<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('assignment_submissions', 'grade')) {
                $table->integer('grade')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('assignment_submissions', 'feedback')) {
                $table->text('feedback')->nullable()->after('grade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_submissions', 'feedback')) {
                $table->dropColumn('feedback');
            }
            if (Schema::hasColumn('assignment_submissions', 'grade')) {
                $table->dropColumn('grade');
            }
        });
    }
};