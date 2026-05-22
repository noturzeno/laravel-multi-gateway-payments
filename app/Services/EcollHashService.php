<?php

namespace App\Services;

class EcollHashService
{
    /**
     * Build SHA-512 inward hash for redirect to eColl gateway.
     */
    public function buildInwardHash(array $params): string
    {
        $stringToHash = http_build_query($params, '', '&') . config('ecoll.hash_secret');

        return hash('sha512', $stringToHash);
    }

    /**
     * Validate outward hash from eColl webhook response.
     */
    public function validateOutwardHash(array $data): bool
    {
        if (! isset($data['HASH'])) {
            return false;
        }

        $receivedHash = $data['HASH'];
        unset($data['HASH']);

        $orderedKeys = [
            'tran-type',
            'Tender',
            'TotAmt',
            'ReceiptNo',
            'TranRefNo',
            'PayRefNo',
            'Status',
            'Source',
            'GWCode',
            'GWMsg',
        ];

        $orderedData = [];
        foreach ($orderedKeys as $key) {
            if (isset($data[$key])) {
                $orderedData[$key] = $data[$key];
            }
        }

        $stringToHash = http_build_query($orderedData, '', '&') . config('ecoll.hash_secret');
        $calculatedHash = hash('sha512', $stringToHash);

        return hash_equals($calculatedHash, $receivedHash);
    }
}
