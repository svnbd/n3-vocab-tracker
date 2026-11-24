<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vocab_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('day_number'); // 1 থেকে 60
            $table->unsignedTinyInteger('score')->default(0); // 10 এর মধ্যে স্কোর
            $table->boolean('passed')->default(false); // 7 বা তার বেশি পেলে true
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocab_progress');
    }
};