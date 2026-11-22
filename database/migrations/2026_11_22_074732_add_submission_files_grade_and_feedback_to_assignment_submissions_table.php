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
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('submitted_at');
            $table->string('original_name')->nullable()->after('file_path');
            $table->string('mime_type')->nullable()->after('original_name');
            $table->decimal('grade', 5, 2)->nullable()->after('mime_type');
            $table->text('feedback')->nullable()->after('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'original_name', 'mime_type', 'grade', 'feedback']);
        });
    }
};
