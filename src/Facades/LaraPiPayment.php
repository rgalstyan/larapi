<?php

namespace Rgalstyan\Larapi\Facades;

use Illuminate\Support\Facades\Facade;
use Rgalstyan\Larapi\Dto\PaymentDTO;

/**
 * @method static PaymentDTO|null createPayment(float $amount, string $uid, string $memo = 'A2U Payment', array $metadata = [])
 * @method static PaymentDTO|null getPayment(string $paymentId)
 * @method static PaymentDTO|null approvePayment(string $paymentId)
 * @method static PaymentDTO|null completePayment(string $paymentId, string $txid)
 * @method static PaymentDTO|null cancelPayment(string $paymentId)
 * @method static object|null incompleteServerPayments()
 */
class LaraPiPayment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lara_pi_payment';
    }
}
