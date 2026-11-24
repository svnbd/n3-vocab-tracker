<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vocab_progress', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false)->after('passed');
        });

        DB::table('vocab_progress')
            ->where('passed', true)
            ->update(['is_completed' => true]);
    }

    public function down(): void
    {
        Schema::table('vocab_progress', function (Blueprint $table) {
            $table->dropColumn('is_completed');
        });
    }
};
