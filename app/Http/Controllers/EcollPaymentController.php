<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\EcollPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EcollPaymentController extends Controller
{
    private EcollPaymentService $paymentService;

    public function __construct(EcollPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function create(): View
    {
        return view('payment.ecoll.create');
    }

    public function redirectToGateway(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $transaction = $this->paymentService->createPendingTransaction($validated);
        $gatewayUrl = $this->paymentService->buildGatewayRedirectUrl($transaction);

        return Redirect::away($gatewayUrl);
    }

    public function success(Request $request): View
    {
        $tranRefNo = $request->query('TranRefNo');
        $transaction = Transaction::where('tran_ref_no', $tranRefNo)->firstOrFail();

        return view('payment.ecoll.success', compact('transaction'));
    }

    public function failed(Request $request): View
    {
        $tranRefNo = $request->query('TranRefNo');
        $transaction = Transaction::where('tran_ref_no', $tranRefNo)->firstOrFail();
        $message = $request->query('GWMsg', 'The payment was declined or failed.');

        return view('payment.ecoll.failed', compact('transaction', 'message'));
    }

    public function cancelled(Request $request): View
    {
        return view('payment.ecoll.cancelled');
    }
}
