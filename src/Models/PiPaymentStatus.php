<?php

declare(strict_types=1);

namespace Rgalstyan\Larapi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PiPaymentStatus extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'pi_payment_id',
        'developer_approved',
        'transaction_verified',
        'developer_completed',
        'canceled',
        'user_cancelled',
    ];

    public function piPayment(): BelongsTo
    {
        return $this->belongsTo(PiPayment::class);
    }
}
