<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EcollPaymentService
{
    private EcollHashService $hashService;

    public function __construct(EcollHashService $hashService)
    {
        $this->hashService = $hashService;
    }

    public function createPendingTransaction(array $input): Transaction
    {
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'payer_name' => $input['name'],
            'payer_email' => $input['email'],
            'amount' => $input['amount'],
            'status' => Transaction::STATUS_PENDING,
        ]);

        $tranRefNo = $this->buildTranRefNo($transaction->id);
        $transaction->update(['tran_ref_no' => $tranRefNo]);

        return $transaction->fresh();
    }

    public function buildTranRefNo(int $transactionId): string
    {
        $deptCode = config('ecoll.dept_code');
        $activityCode = config('ecoll.activity_code');
        $appCode = config('ecoll.app_code');
        $serialNumber = str_pad((string) $transactionId, 6, '0', STR_PAD_LEFT);

        return "{$deptCode}{$activityCode}{$appCode}{$serialNumber}";
    }

    public function buildGatewayRedirectUrl(Transaction $transaction): string
    {
        $params = [
            'tran-type' => config('ecoll.tran_type'),
            'UnitAmountIncTax' => number_format($transaction->amount, 2, '.', ''),
            'Name' => $transaction->payer_name,
            'TranRefNo' => $transaction->tran_ref_no,
            'Email' => $transaction->payer_email,
            'Description' => 'Payment for Order #' . $transaction->id,
            'Timestamp' => Carbon::now('UTC')->format('YmdHi'),
        ];

        $params['HASH'] = $this->hashService->buildInwardHash($params);

        return config('ecoll.base_uri') . '?' . http_build_query($params);
    }
}
