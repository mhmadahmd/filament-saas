<?php

declare(strict_types=1);

namespace Mhmadahmd\FilamentSaas\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int|string $id
 * @property int $subscription_id
 * @property string $payment_method
 * @property float $amount
 * @property string $currency
 * @property string $status
 * @property string|null $transaction_id
 * @property string|null $reference_number
 * @property string|null $notes
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Subscription $subscription
 */
class SubscriptionPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'subscription_payments';

    public const METHOD_CASH = 'cash';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_ONLINE = 'online';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'subscription_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'reference_number',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'subscription_id' => 'integer',
        'amount' => 'float',
        'paid_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function markAsPaid(?Carbon $paidAt = null): self
    {
        $this->status = self::STATUS_PAID;
        $this->paid_at = $paidAt ?? Carbon::now();
        $this->save();

        return $this;
    }

    public function markAsFailed(): self
    {
        $this->status = self::STATUS_FAILED;
        $this->save();

        return $this;
    }

    public function markAsRefunded(): self
    {
        $this->status = self::STATUS_REFUNDED;
        $this->save();

        return $this;
    }

    public static function getPaymentMethods(): array
    {
        return [
            self::METHOD_CASH => 'Cash',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_ONLINE => 'Online Payment',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REFUNDED => 'Refunded',
        ];
    }
}

