<?php

namespace Rgalstyan\Larapi\Dto;

final readonly class PaymentDTO
{
    public function __construct(
        public string $identifier,
        public string $user_uid,
        public float $amount,
        public string $memo,
        public object $metadata,
        public string $from_address,
        public string $to_address,
        public string $direction,
        public string $network,
        public object $status,
        public ?object $transaction,
        public string $created_at
    ) {}

    public static function fromObject(object $payment): self
    {
        return new self(
            $payment->identifier,
            $payment->user_uid,
            $payment->amount,
            $payment->memo,
            $payment->metadata,
            $payment->from_address ?? '',
            $payment->to_address ?? '',
            $payment->direction,
            $payment->network,
            $payment->status,
            $payment->transaction ?? null,
            $payment->created_at
        );
    }
}
