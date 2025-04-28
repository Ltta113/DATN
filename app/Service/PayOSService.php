<?php

namespace App\Service;

use App\Service\PayOSCustom;

class PayOSService
{
    private $payos;

    public function __construct()
    {
        $this->payos = new PayOSCustom(
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

    public function confirmWebhookUrl($paymentData)
    {
        return $this->payos->confirmWebhook($paymentData);
    }
}
