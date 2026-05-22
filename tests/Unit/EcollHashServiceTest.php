<?php

namespace Tests\Unit;

use App\Services\EcollHashService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EcollHashServiceTest extends TestCase
{
    private EcollHashService $hashService;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('ecoll.hash_secret', 'test-secret');

        $this->hashService = new EcollHashService();
    }

    public function test_build_inward_hash_is_deterministic(): void
    {
        $params = [
            'tran-type' => 'TEST',
            'UnitAmountIncTax' => '10.50',
            'Name' => 'Jane Doe',
            'TranRefNo' => '00580AAAA000001',
            'Email' => 'jane@example.com',
            'Description' => 'Payment for Order #1',
            'Timestamp' => '202601011200',
        ];

        $hash1 = $this->hashService->buildInwardHash($params);
        $hash2 = $this->hashService->buildInwardHash($params);

        $this->assertSame(128, strlen($hash1));
        $this->assertSame($hash1, $hash2);
    }

    public function test_validate_outward_hash_accepts_valid_payload(): void
    {
        $data = [
            'tran-type' => 'TEST',
            'Tender' => 'CC',
            'TotAmt' => '10.50',
            'ReceiptNo' => 'R001',
            'TranRefNo' => '00580AAAA000001',
            'PayRefNo' => 'P001',
            'Status' => 'A',
            'Source' => 'WEB',
            'GWCode' => '00',
            'GWMsg' => 'Approved',
        ];

        $data['HASH'] = $this->hashService->buildInwardHash([
            'tran-type' => $data['tran-type'],
            'Tender' => $data['Tender'],
            'TotAmt' => $data['TotAmt'],
            'ReceiptNo' => $data['ReceiptNo'],
            'TranRefNo' => $data['TranRefNo'],
            'PayRefNo' => $data['PayRefNo'],
            'Status' => $data['Status'],
            'Source' => $data['Source'],
            'GWCode' => $data['GWCode'],
            'GWMsg' => $data['GWMsg'],
        ]);

        $this->assertTrue($this->hashService->validateOutwardHash($data));
    }

    public function test_validate_outward_hash_rejects_invalid_hash(): void
    {
        $data = [
            'tran-type' => 'TEST',
            'TranRefNo' => '00580AAAA000001',
            'Status' => 'A',
            'HASH' => 'invalid',
        ];

        $this->assertFalse($this->hashService->validateOutwardHash($data));
    }
}
