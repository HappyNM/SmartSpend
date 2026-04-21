<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('savings_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('target_amount', 12, 2)->nullable(); // for amount-based lock
            $table->decimal('current_amount', 12, 2)->default(0);

            $table->enum('lock_type', ['time', 'amount', 'time_and_amount']);
            $table->timestamp('lock_until')->nullable(); // for time-based lock

            $table->boolean('allow_partial_withdrawal')->default(true);
            $table->enum('status', ['active', 'completed', 'cancelled', 'broken'])->default('active');

            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['wallet_id', 'status']);
            $table->index(['user_id', 'lock_type', 'status']);
            $table->index(['lock_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_goals');
    }
};