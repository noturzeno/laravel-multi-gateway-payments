<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Services\EcollHashService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EcollWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('ecoll.hash_secret', 'webhook-test-secret');
    }

    public function test_webhook_rejects_invalid_hash(): void
    {
        $response = $this->postJson('/ecoll/webhook', [
            'TranRefNo' => '00580AAAA000001',
            'Status' => 'A',
            'HASH' => 'invalid',
        ]);

        $response->assertStatus(400)
            ->assertJson(['status' => 'error', 'message' => 'Invalid hash']);
    }

    public function test_webhook_updates_transaction_on_valid_hash(): void
    {
        $transaction = Transaction::create([
            'payer_name' => 'Jane Doe',
            'payer_email' => 'jane@example.com',
            'amount' => 10.50,
            'status' => Transaction::STATUS_PENDING,
            'tran_ref_no' => '00580AAAA000001',
        ]);

        $payload = [
            'tran-type' => 'TEST',
            'Tender' => 'CC',
            'TotAmt' => '10.50',
            'ReceiptNo' => 'R001',
            'TranRefNo' => $transaction->tran_ref_no,
            'PayRefNo' => 'P001',
            'Status' => 'A',
            'Source' => 'WEB',
            'GWCode' => '00',
            'GWMsg' => 'Approved',
        ];

        $hashService = new EcollHashService();
        $payload['HASH'] = $hashService->buildInwardHash([
            'tran-type' => $payload['tran-type'],
            'Tender' => $payload['Tender'],
            'TotAmt' => $payload['TotAmt'],
            'ReceiptNo' => $payload['ReceiptNo'],
            'TranRefNo' => $payload['TranRefNo'],
            'PayRefNo' => $payload['PayRefNo'],
            'Status' => $payload['Status'],
            'Source' => $payload['Source'],
            'GWCode' => $payload['GWCode'],
            'GWMsg' => $payload['GWMsg'],
        ]);

        $response = $this->postJson('/ecoll/webhook', $payload);

        $response->assertOk()
            ->assertJson(['status' => 'success']);

        $transaction->refresh();
        $this->assertSame(Transaction::STATUS_COMPLETED, $transaction->status);
        $this->assertSame('P001', $transaction->pay_ref_no);
    }

    public function test_webhook_is_idempotent_for_completed_transaction(): void
    {
        $transaction = Transaction::create([
            'payer_name' => 'Jane Doe',
            'payer_email' => 'jane@example.com',
            'amount' => 10.50,
            'status' => Transaction::STATUS_COMPLETED,
            'tran_ref_no' => '00580AAAA000002',
            'pay_ref_no' => 'P001',
        ]);

        $payload = [
            'tran-type' => 'TEST',
            'Tender' => 'CC',
            'TotAmt' => '10.50',
            'ReceiptNo' => 'R002',
            'TranRefNo' => $transaction->tran_ref_no,
            'PayRefNo' => 'P999',
            'Status' => 'A',
            'Source' => 'WEB',
            'GWCode' => '00',
            'GWMsg' => 'Approved',
        ];

        $hashService = new EcollHashService();
        $payload['HASH'] = $hashService->buildInwardHash([
            'tran-type' => $payload['tran-type'],
            'Tender' => $payload['Tender'],
            'TotAmt' => $payload['TotAmt'],
            'ReceiptNo' => $payload['ReceiptNo'],
            'TranRefNo' => $payload['TranRefNo'],
            'PayRefNo' => $payload['PayRefNo'],
            'Status' => $payload['Status'],
            'Source' => $payload['Source'],
            'GWCode' => $payload['GWCode'],
            'GWMsg' => $payload['GWMsg'],
        ]);

        $response = $this->postJson('/ecoll/webhook', $payload);

        $response->assertOk();

        $transaction->refresh();
        $this->assertSame('P001', $transaction->pay_ref_no);
    }
}
