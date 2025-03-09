<?php

namespace Rgalstyan\Larapi\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rgalstyan\Larapi\Dto\PaymentDTO;
use Rgalstyan\Larapi\Exceptions\PaymentAlreadyExistsException;
use Rgalstyan\Larapi\Models\PiPayment;
use Rgalstyan\Larapi\Models\PiPaymentStatus;
use Rgalstyan\Larapi\Models\PiTransaction;
use Throwable;

final readonly class LaraPiDbService
{
    /**
     * @throws Exception
     */
    public function createPayment(PaymentDto $dto): PiPayment
    {

        try {
            if (PiPayment::query()->where('identifier', $dto->identifier)->exists()) {
                throw new PaymentAlreadyExistsException($dto->identifier);
            }

            DB::beginTransaction();

            $payment = PiPayment::query()->create([
                'user_id' => auth()->id() ?? null,
                'identifier' => $dto->identifier,
                'user_uid' => $dto->user_uid,
                'amount' => $dto->amount,
                'memo' => $dto->memo,
                'metadata' => json_encode($dto->metadata),
                'from_address' => $dto->from_address,
                'to_address' => $dto->to_address,
                'direction' => $dto->direction,
                'network' => $dto->network,
            ]);

            PiPaymentStatus::query()->create([
                'pi_payment_id' => $payment->id,
                'developer_approved' => $dto->status->developer_approved ?? false,
                'transaction_verified' => $dto->status->transaction_verified ?? false,
                'developer_completed' => $dto->status->developer_completed ?? false,
                'canceled' => $dto->status->canceled ?? false,
                'user_cancelled' => $dto->status->user_cancelled ?? false,
            ]);

            if ($dto->transaction) {
                PiTransaction::query()->create([
                    'pi_payment_id' => $payment->id,
                    'txid' => $dto->transaction->txid,
                    'verified' => $dto->transaction->verified,
                    '_link' => $dto->transaction->_link,
                ]);
            }
            DB::commit();

            return $payment->load(['paymentStatus', 'transaction']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function updatePayment(int $paymentId, PaymentDto $dto): PiPayment
    {
        try {

            DB::beginTransaction();

            PiPayment::query()->where(['identifier' => $dto->identifier])->update([
                'user_id' => auth()->id() ?? null,
                'user_uid' => $dto->user_uid,
                'amount' => $dto->amount,
                'memo' => $dto->memo,
                'metadata' => json_encode($dto->metadata),
                'from_address' => $dto->from_address,
                'to_address' => $dto->to_address,
                'direction' => $dto->direction,
                'network' => $dto->network,
            ]);

            PiPaymentStatus::query()->where(['pi_payment_id' => $paymentId])->update([
                'developer_approved' => $dto->status->developer_approved ?? false,
                'transaction_verified' => $dto->status->transaction_verified ?? false,
                'developer_completed' => $dto->status->developer_completed ?? false,
                'canceled' => $dto->status->canceled ?? false,
                'user_cancelled' => $dto->status->user_cancelled ?? false,
            ]);

            if ($dto->transaction) {
                PiTransaction::query()->updateOrCreate(
                    ['pi_payment_id' => $paymentId],
                    [
                        'pi_payment_id' => $paymentId,
                        'txid' => $dto->transaction->txid,
                        'verified' => $dto->transaction->verified,
                        '_link' => $dto->transaction->_link,
                    ]
                );
            }

            DB::commit();
            return PiPayment::query()->with([
                'paymentStatus', 'transaction'
            ])->findOrFail($paymentId);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function deletePayment(int $paymentId): bool
    {
        try {
            return PiPayment::query()->where(['identifier' => $paymentId])->delete();
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function getPaymentById(int $paymentId): ?PiPayment
    {
        return PiPayment::query()->findOrFail($paymentId);
    }

    public function getPaymentsByUserId(int $userId): ?object
    {
        return PiPayment::query()->where('user_id', $userId)->get();
    }

    public function getPaymentsByUserUid(string $piUserUid): ?object
    {
        return PiPayment::query()->where('user_uid', $piUserUid)->get();
    }

    public function getLimitedPaymentsByUserUid(string $piUserUid, $limit = 10): ?object
    {
        return PiPayment::query()
                ->where('user_uid', $piUserUid)
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get();
    }

    public function getPaymentByIdentifier(string $identifier): ?object
    {
        return PiPayment::query()->where('identifier', $identifier)->first();
    }
}
