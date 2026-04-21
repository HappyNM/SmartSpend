<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SavingsGoal extends Model
{
    protected $fillable = [
        'wallet_id',
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'lock_type',
        'lock_until',
        'allow_partial_withdrawal',
        'status',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'allow_partial_withdrawal' => 'boolean',
        'lock_until' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(WalletTransaction::class, 'related');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isUnlocked(): bool
    {
        if ($this->lock_type === 'amount' && $this->target_amount !== null) {
            return bccomp((string) $this->current_amount, (string) $this->target_amount, 2) >= 0;
        }

        if ($this->lock_type === 'time' && $this->lock_until !== null) {
            return now()->greaterThanOrEqualTo($this->lock_until);
        }

        if ($this->lock_type === 'time_and_amount') {
            $amountReached = $this->target_amount !== null
                && bccomp((string) $this->current_amount, (string) $this->target_amount, 2) >= 0;

            $timeReached = $this->lock_until !== null
                && now()->greaterThanOrEqualTo($this->lock_until);

            return $amountReached && $timeReached;
        }

        return false;
    }
}