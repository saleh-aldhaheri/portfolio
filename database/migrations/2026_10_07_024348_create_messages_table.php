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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->foreignId('visitor_id')->constrained('visitors')->cascadeOnDelete();
            $table->enum('role', array_column(\App\Enums\MessageRole::cases(), 'value'))
                ->default(\App\Enums\MessageRole::VISITOR->value);
            $table->timestamps();

            $table->index(['visitor_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
