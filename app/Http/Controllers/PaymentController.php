<?php

namespace App\Http\Controllers;

use App\Services\ReconPaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    private ReconPaymentService $recon;

    public function __construct(ReconPaymentService $recon)
    {
        $this->recon = $recon;
    }

    public function toRecon(): View
    {
        $url = $this->recon->buildPaymentUrl();

        return view('payment', compact('url'));
    }

    public function payment(): View
    {
        $url = $this->recon->buildPaymentUrl();

        return view('payment.payment', compact('url'));
    }

    public function notify(): View
    {
        return view('payment.notify');
    }

    public function return(Request $request): View
    {
        return view('payment.return-page', ['payload' => $request->all()]);
    }
}
