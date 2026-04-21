<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'deposit',
                'withdrawal',
                'lock',
                'unlock',
                'goal_contribution',
                'goal_withdrawal',
                'reversal',
                'adjustment',
            ]);

            $table->enum('status', ['pending', 'completed', 'failed', 'reversed'])->default('pending');

            $table->decimal('amount', 12, 2);
            $table->decimal('available_balance_after', 12, 2)->nullable();
            $table->decimal('locked_balance_after', 12, 2)->nullable();

            $table->string('reference', 100)->nullable();
            $table->string('external_reference', 100)->nullable();
            $table->string('source', 40)->nullable(); // mpesa, system, admin, user

            $table->nullableMorphs('related'); // related_type, related_id (goal, deposit, etc)
            $table->json('meta')->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['wallet_id', 'status', 'created_at']);
            $table->index(['user_id', 'type', 'created_at']);
            $table->index(['external_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
