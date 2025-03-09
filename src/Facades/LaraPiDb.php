<?php

namespace Rgalstyan\Larapi\Facades;

use Illuminate\Support\Facades\Facade;
use Rgalstyan\Larapi\Dto\PaymentDTO;
use Rgalstyan\Larapi\Models\PiPayment;

/**
 * @method static PiPayment createPayment(PaymentDto $dto)
 * @method static PiPayment updatePayment(int $paymentId, PaymentDto $dto)
 * @method static bool deletePayment(int $paymentId)
 * @method static PiPayment|null getPaymentById(int $paymentId)
 * @method static object|null getPaymentsByUserId(int $userId)
 * @method static object|null getPaymentsByUserUid(string $piUserUid)
 * @method static object|null getLimitedPaymentsByUserUid(string $piUserUid, $limit = 10)
 * @method static object|null getPaymentByIdentifier(string $identifier)
 */
class LaraPiDb extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lara_pi_db';
    }
}
