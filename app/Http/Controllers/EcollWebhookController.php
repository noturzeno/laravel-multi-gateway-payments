<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\EcollHashService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EcollWebhookController extends Controller
{
    private EcollHashService $hashService;

    public function __construct(EcollHashService $hashService)
    {
        $this->hashService = $hashService;
    }

    public function handle(Request $request): JsonResponse
    {
        Log::info('eColl2.0 Webhook Received:', $request->all());

        $data = $request->all();

        if (! $this->hashService->validateOutwardHash($data)) {
            Log::error('eColl2.0 Webhook: HASH VALIDATION FAILED.', $data);

            return response()->json(['status' => 'error', 'message' => 'Invalid hash'], 400);
        }

        $transaction = Transaction::where('tran_ref_no', $data['TranRefNo'])->first();

        if (! $transaction) {
            Log::error('eColl2.0 Webhook: Transaction not found.', $data);

            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        if ($transaction->status === Transaction::STATUS_COMPLETED) {
            Log::info('eColl2.0 Webhook: Transaction already completed, skipping update.', ['id' => $transaction->id]);

            return response()->json(['status' => 'success']);
        }

        $transaction->pay_ref_no = $data['PayRefNo'] ?? null;
        $transaction->receipt_no = $data['ReceiptNo'] ?? null;
        $transaction->gateway_code = $data['GWCode'] ?? null;
        $transaction->gateway_message = $data['GWMsg'] ?? null;

        switch ($data['Status']) {
            case 'A':
                if (isset($data['TotAmt']) && $data['TotAmt'] > 0) {
                    $transaction->status = Transaction::STATUS_COMPLETED;
                }
                break;
            default:
                $transaction->status = Transaction::STATUS_FAILED;
                break;
        }

        $transaction->save();
        Log::info('eColl2.0 Webhook: Transaction updated successfully.', [
            'id' => $transaction->id,
            'status' => $transaction->status,
        ]);

        return response()->json(['status' => 'success']);
    }
}
