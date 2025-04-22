<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;
use PayOS\PayOS;

class PayOSService
{
    private $payos;

    public function __construct()
    {
        $this->payos = new PayOS(
            config('payos.client_id'),
            config('payos.api_key'),
            config('payos.checksum_key')
        );
    }

    public function createOrder($orderData)
    {
        return $this->payos->createPaymentLink($orderData);
    }

    public function verifyPayment($paymentData)
    {
        return $this->payos->verifyPaymentWebhookData($paymentData);
    }
}
