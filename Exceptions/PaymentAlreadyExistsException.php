<?php

declare(strict_types=1);

namespace Rgalstyan\Larapi\Exceptions;

use Exception;

final class PaymentAlreadyExistsException extends Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("Payment with identifier '{$identifier}' already exists.");
    }
}
