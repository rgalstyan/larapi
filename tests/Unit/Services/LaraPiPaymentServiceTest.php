<?php
namespace Tests\Unit\Services;

use Rgalstyan\Larapi\Clients\LaraPiAppClient;
use Rgalstyan\Larapi\Dto\PaymentDTO;
use Rgalstyan\Larapi\Services\LaraPiPaymentService;
use Mockery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

final class LaraPiPaymentServiceTest extends BaseTestCase
{
    private LaraPiAppClient $client;
    private LaraPiPaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(LaraPiAppClient::class);

        $this->client->shouldReceive('post')
            ->with('payments', Mockery::type('array'))
            ->andReturn((object) [
                'identifier' => 'PAYMENT_IDENTIFIER',
                'user_uid' => 'PI_USER_UUID',
                'amount' => 1.0,
                'memo' => 'A2U Payment',
                'metadata' => (object)['test' => 1234],
                'from_address' => 'FROM_ADDRESS',
                'to_address' => 'TO_ADDRESS',
                'direction' => 'app_to_user',
                'status' => (object) [
                    'developer_approved' => true,
                    'transaction_verified' => false,
                    'developer_completed' => false,
                    'cancelled' => true,
                    'user_cancelled' => false,
                ],
                'transaction' => null,
                'created_at' => '2025-03-03T13:31:35.468Z',
                'network' => 'Pi Testnet'
            ]);

        $this->service = new LaraPiPaymentService($this->client);
    }


    public function testCreatePayment()
    {
        $paymentDTO = new PaymentDTO(
            'PAYMENT_IDENTIFIER',
            'PI_USER_UUID',
            '1.0',
            'A2U Payment',
            (object)['test' => 1234],
            'FROM_ADDRESS',
            'TO_ADDRESS',
            'app_to_user',
            'Pi Testnet',
            (object)[
                'developer_approved' => true,
                'transaction_verified' => false,
                'developer_completed' => false,
                'cancelled' => true,
                'user_cancelled' => false
            ],
            null,
            '2025-03-03T13:31:35.468Z'
        );

        $this->client->shouldReceive('post')
            ->with('payments', Mockery::type('array'))
            ->andReturn($paymentDTO);

        $result = $this->service->createPayment(
            1.0,
            'PI_USER_UUID',
            'A2U Payment',
            ['test' => 1234]
        );

        $this->assertInstanceOf(PaymentDTO::class, $result);
        $this->assertEquals('PAYMENT_IDENTIFIER', $result->identifier);
        $this->assertEquals(1.0, $result->amount);
        $this->assertEquals('A2U Payment', $result->memo);
        $this->assertTrue($result->status->developer_approved);
    }

    public function testGetPayment()
    {
        $paymentDTOWithNullTransaction = new PaymentDTO(
            'PAYMENT_IDENTIFIER',
            'PI_USER_UUID',
            '1.0',
            'A2U Payment',
            (object)['test' => 1234],
            'FROM_ADDRESS',
            'TO_ADDRESS',
            'app_to_user',
            'Pi Testnet',
            (object)[
                'developer_approved' => true,
                'transaction_verified' => false,
                'developer_completed' => false,
                'cancelled' => true,
                'user_cancelled' => false
            ],
            null,
            '2025-03-03T13:31:35.468Z'
        );

        $this->client->shouldReceive('get')
            ->with('payments/PAYMENT_IDENTIFIER')
            ->andReturn($paymentDTOWithNullTransaction);

        $result = $this->service->getPayment('PAYMENT_IDENTIFIER');
        $this->assertInstanceOf(PaymentDTO::class, $result);
        $this->assertNull($result->transaction);

        $paymentDTOWithTransaction = new PaymentDTO(
            'PAYMENT_IDENTIFIER',
            'PI_USER_UUID',
            '1.0',
            'A2U Payment',
            (object)['test' => 1234],
            'FROM_ADDRESS',
            'TO_ADDRESS',
            'app_to_user',
            'Pi Testnet',
            (object)[
                'developer_approved' => true,
                'transaction_verified' => true,
                'developer_completed' => true,
                'cancelled' => false,
                'user_cancelled' => false
            ],
            (object)[
                'txid' => 'abcd1234',
                'verified' => true,
                '_link' => 'https://piblockchain.com/tx/abcd1234'
            ],
            '2025-03-03T13:31:35.468Z'
        );

        $this->client->shouldReceive('get')
            ->with('payments/PAYMENT_IDENTIFIER_2')
            ->andReturn($paymentDTOWithTransaction);

        $result = $this->service->getPayment('PAYMENT_IDENTIFIER_2');
        $this->assertInstanceOf(PaymentDTO::class, $result);
        $this->assertNotNull($result->transaction);
        $this->assertEquals('abcd1234', $result->transaction->txid);
        $this->assertTrue($result->transaction->verified);
        $this->assertEquals('https://piblockchain.com/tx/abcd1234', $result->transaction->_link);
    }

    public function testApprovePayment()
    {
        $paymentDTO = new PaymentDTO(
            'PAYMENT_IDENTIFIER',
            'PI_USER_UUID',
            '1.0',
            'A2U Payment',
            (object)['test' => 1234],
            'FROM_ADDRESS',
            'TO_ADDRESS',
            'app_to_user',
            'Pi Testnet',
            (object)[
                'developer_approved' => true,
                'transaction_verified' => false,
                'developer_completed' => false,
                'cancelled' => false,
                'user_cancelled' => false
            ],
            null,
            '2025-03-03T13:31:35.468Z'
        );

        $this->client->shouldReceive('post')
            ->with('payments/PAYMENT_IDENTIFIER/approve')
            ->andReturn($paymentDTO);

        $result = $this->service->approvePayment('PAYMENT_IDENTIFIER');
        $this->assertInstanceOf(PaymentDTO::class, $result);
        $this->assertTrue($result->status->developer_approved);

        $this->client->shouldReceive('post')
            ->with('payments/PAYMENT_IDENTIFIER_2/approve')
            ->andReturnNull();

        $result = $this->service->approvePayment('PAYMENT_IDENTIFIER_2');
        $this->assertNull($result);
    }

    public function testCompletePayment()
    {
        $paymentDTO = new PaymentDTO(
            'PAYMENT_IDENTIFIER',
            'PI_USER_UUID',
            '1.0',
            'A2U Payment',
            (object)['test' => 1234],
            'FROM_ADDRESS',
            'TO_ADDRESS',
            'app_to_user',
            'Pi Testnet',
            (object)[
                'developer_approved' => true,
                'transaction_verified' => true,
                'developer_completed' => true,
                'cancelled' => false,
                'user_cancelled' => false
            ],
            (object)[
                'txid' => 'abcdef1234567890',
                'verified' => true,
                '_link' => 'http://blockchain-link.com'
            ],
            '2025-03-03T13:31:35.468Z'
        );

        $this->client->shouldReceive('post')
            ->with('payments/PAYMENT_IDENTIFIER/complete', ['txid' => 'abcdef1234567890'])
            ->andReturn($paymentDTO);

        $result = $this->service->completePayment('PAYMENT_IDENTIFIER', 'abcdef1234567890');
        $this->assertInstanceOf(PaymentDTO::class, $result);
        $this->assertEquals('abcdef1234567890', $result->transaction->txid);
        $this->assertTrue($result->transaction->verified);


        $this->client->shouldReceive('post')
            ->with('payments/PAYMENT_IDENTIFIER_2/complete', ['txid' => 'abcdef1234567890'])
            ->andReturnNull();

        $result = $this->service->completePayment('PAYMENT_IDENTIFIER_2', 'abcdef1234567890');
        $this->assertNull($result);
    }

    public function testCancelPayment()
    {
        $paymentDTO = new PaymentDTO(
            'PAYMENT_IDENTIFIER',
            'PI_USER_UUID',
            '1.0',
            'A2U Payment',
            (object)['test' => 1234],
            'FROM_ADDRESS',
            'TO_ADDRESS',
            'app_to_user',
            'Pi Testnet',
            (object)[
                'developer_approved' => true,
                'transaction_verified' => true,
                'developer_completed' => true,
                'cancelled' => true,
                'user_cancelled' => false
            ],
            (object)[
                'txid' => 'abcdef1234567890',
                'verified' => true,
                '_link' => 'http://blockchain-link.com'
            ],
            '2025-03-03T13:31:35.468Z'
        );

        $this->client->shouldReceive('post')
            ->with('payments/PAYMENT_IDENTIFIER/cancel')
            ->andReturn($paymentDTO);

        $result = $this->service->cancelPayment('PAYMENT_IDENTIFIER');
        $this->assertInstanceOf(PaymentDTO::class, $result);
        $this->assertTrue($result->status->cancelled);

        $this->client->shouldReceive('post')
            ->with('payments/PAYMENT_IDENTIFIER_2/cancel')
            ->andReturnNull();

        $result = $this->service->cancelPayment('PAYMENT_IDENTIFIER_2');
        $this->assertNull($result);
    }

    public function testIncompleteServerPaymentsNotNull()
    {
        $response = (object)[
            'incomplete_server_payments' => [
                (object)[
                    'identifier' => 'PAYMENT_IDENTIFIER',
                ],
                (object)[
                    'identifier' => 'PAYMENT_IDENTIFIER_2',
                ],
            ],
        ];

        $this->client->shouldReceive('get')
            ->with('payments/incomplete_server_payments')
            ->andReturn($response);

        $result = $this->service->incompleteServerPayments();
        $this->assertNotNull($result);
        $this->assertArrayHasKey('incomplete_server_payments', (array) $result);
    }
    public function testIncompleteServerPaymentsNull()
    {
        $this->client->shouldReceive('get')
            ->with('payments/incomplete_server_payments')
            ->andReturnNull();

        $nullResult = $this->service->incompleteServerPayments();
        $this->assertNull($nullResult);
    }
}
