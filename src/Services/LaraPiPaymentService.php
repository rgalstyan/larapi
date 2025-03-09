<?php

namespace Rgalstyan\Larapi\Services;

use Rgalstyan\Larapi\Clients\LaraPiAppClient;
use Rgalstyan\Larapi\Dto\PaymentDTO;
use Illuminate\Support\Facades\Log;

final readonly class LaraPiPaymentService
{
    public function __construct(
        private LaraPiAppClient $laraPiAppClient
    ){
    }

    public function createPayment(
        float $amount,
        string $uid,
        string $memo = 'A2U Payment',
        array $metadata = [],
    ): PaymentDTO|null
    {
        $params = [
            'payment' => [
                'amount' => $amount,
                'memo' => $memo,
                'metadata' => $metadata,
                'uid' => $uid,
            ]
        ];
        $response = $this->laraPiAppClient->post('payments', $params);

        if (!$response) {
            Log::error('Payment creation failed', ['uid' => $uid, 'amount' => $amount]);
            return null;
        }

        return PaymentDTO::fromObject($response) ?? null;
    }

    public function getPayment(string $paymentId): PaymentDTO|null
    {
        $response = $this->laraPiAppClient->get('payments/'.$paymentId);

        if (!$response) {
            Log::error('Failed to get payment', ['paymentId' => $paymentId]);
            return null;
        }

        return PaymentDTO::fromObject($response) ?? null;
    }

    public function approvePayment(string $paymentId): PaymentDTO|null
    {
        $response = $this->laraPiAppClient->post('payments/'.$paymentId.'/approve');

        if (!$response) {
            Log::error('Failed to approve payment', ['paymentId' => $paymentId]);
            return null;
        }

        return PaymentDTO::fromObject($response) ?? null;
    }

    public function completePayment(string $paymentId, string $txid): PaymentDTO|null
    {
        $response = $this->laraPiAppClient->post(
            'payments/'.$paymentId.'/complete',
            [
                'txid' => $txid,
            ]
        );

        if (!$response) {
            Log::error('Failed to complete payment', ['paymentId' => $paymentId, 'txid' => $txid]);
            return null;
        }

        return PaymentDTO::fromObject($response) ?? null;
    }

    public function cancelPayment(string $paymentId): PaymentDTO|null
    {
        $response = $this->laraPiAppClient->post('payments/'.$paymentId.'/cancel');

        if (!$response) {
            Log::error('Failed to cancel payment', ['paymentId' => $paymentId]);
            return null;
        }

        return PaymentDTO::fromObject($response) ?? null;
    }

    public function incompleteServerPayments(): object|null
    {
        $response = $this->laraPiAppClient->get('payments/incomplete_server_payments');

        if (!$response) {
            Log::error('Failed to retrieve incomplete server payments');
            return null;
        }

        return $response;
    }
}
