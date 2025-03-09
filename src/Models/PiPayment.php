<?php

declare(strict_types=1);

namespace Rgalstyan\Larapi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PiPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'user_id',
        'identifier',
        'user_uid',
        'amount',
        'memo',
        'metadata',
        'from_address',
        'to_address',
        'direction',
        'network',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(config('larapi.user.model'), 'id', 'user_id');
    }

    public function paymentStatus(): HasOne
    {
        return $this->hasOne(PiPaymentStatus::class, 'pi_payment_id', 'id');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(PiTransaction::class, 'pi_payment_id', 'id');
    }
}
