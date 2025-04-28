<?php

namespace App\Service;

use Exception;
use GuzzleHttp\Client;
use PayOS\Exceptions\ErrorCode;
use PayOS\Exceptions\ErrorMessage;
use PayOS\Utils\PayOSSignatureUtils;

const PAYOS_BASE_URL = 'https://api-merchant.payos.vn';

/**
 * PayOS
 *
 * @package PayOS
 */
class PayOSCustom
{
    private string $clientId;
    private string $apiKey;
    private string $checksumKey;
    private ?string $partnerCode;

    private Client $httpClient;

    /**
     * Create a payOS object to use payment channel methods. Credentials are fields provided after creating a payOS payment channel.
     *
     * @param string $clientId Client ID of the payOS payment channel
     * @param string $apiKey Api Key of the payOS payment channel
     * @param string $checksumKey Checksum Key of the payOS payment channel
     * @param null|string $partnerCode Your Partner Code
     */
    public function __construct(string $clientId, string $apiKey, string $checksumKey, ?string $partnerCode = null)
    {
        $this->clientId = $clientId;
        $this->apiKey = $apiKey;
        $this->checksumKey = $checksumKey;
        $this->partnerCode = $partnerCode;
        $this->httpClient = new Client();
    }

    /**
     * Create a payment link for the order data passed in the parameter.
     *
     * @param  array $paymentData Payment data
     * @return array
     * @throws Exception
     */
    public function createPaymentLink(array $paymentData): array
    {
        $orderCode = $paymentData['orderCode'] ?? null;
        $amount = $paymentData['amount'] ?? null;
        $returnUrl = $paymentData['returnUrl'] ?? null;
        $cancelUrl = $paymentData['cancelUrl'] ?? null;
        $description = $paymentData['description'] ?? null;

        if (!($paymentData && $orderCode && $amount && $returnUrl && $cancelUrl && $description)) {
            $requiredPaymentData = [
                'orderCode' => $orderCode,
                'amount' => $amount,
                'returnUrl' => $returnUrl,
                'cancelUrl' => $cancelUrl,
                'description' => $description
            ];
            $requiredKeys = array_keys($requiredPaymentData);
            $keysError = array_filter($requiredKeys, function ($key) use ($requiredPaymentData) {
                return $requiredPaymentData[$key] === null;
            });

            $msgError = ErrorMessage::INVALID_PARAMETER . ' ' . implode(', ', $keysError) . ' must not be null.';
            throw new Exception($msgError, ErrorCode::INVALID_PARAMETER);
        }

        $url = PAYOS_BASE_URL . '/v2/payment-requests';
        $signaturePaymentRequest = PayOSSignatureUtils::createSignatureOfPaymentRequest(
            $this->checksumKey,
            $paymentData
        );

        try {
            $headers = [
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ];
            if ($this->partnerCode != null) {
                $headers['x-partner-code'] = $this->partnerCode;
            }

            $data = array_merge($paymentData, ['signature' => $signaturePaymentRequest]);

            $response = $this->httpClient->post($url, [
                'headers' => $headers,
                'json' => $data
            ]);

            $paymentLinkRes = json_decode($response->getBody()->getContents(), true);

            if ($paymentLinkRes['code'] == '00') {
                $paymentLinkResSignature = PayOSSignatureUtils::createSignatureFromObj(
                    $this->checksumKey,
                    $paymentLinkRes['data']
                );
                if ($paymentLinkResSignature !== $paymentLinkRes['signature']) {
                    throw new Exception(ErrorMessage::DATA_NOT_INTEGRITY, ErrorCode::DATA_NOT_INTEGRITY);
                }
                if ($paymentLinkRes['data']) {
                    return $paymentLinkRes['data'];
                }
            }

            throw new Exception($paymentLinkRes['desc'], $paymentLinkRes['code']);
        } catch (Exception $error) {
            throw new Exception($error->getMessage(), $error->getCode());
        }
    }

    /**
     * Get payment information of an order that has created a payment link.
     *
     * @param string|int $orderCode Order code
     * @return array
     * @throws Exception
     */
    public function getPaymentLinkInformation(string|int $orderCode): array
    {
        if (!$orderCode || (is_string($orderCode) && strlen($orderCode) == 0) || (is_int($orderCode) && $orderCode < 0)) {
            throw new Exception(ErrorMessage::INVALID_PARAMETER, ErrorCode::INVALID_PARAMETER);
        }

        $url = PAYOS_BASE_URL . '/v2/payment-requests/' . $orderCode;
        try {
            $headers = [
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ];

            $response = $this->httpClient->get($url, [
                'headers' => $headers
            ]);

            $paymentLinkRes = json_decode($response->getBody()->getContents(), true);

            if ($paymentLinkRes['code'] == '00') {
                $paymentLinkResSignature = PayOSSignatureUtils::createSignatureFromObj(
                    $this->checksumKey,
                    $paymentLinkRes['data']
                );
                if ($paymentLinkResSignature !== $paymentLinkRes['signature']) {
                    throw new Exception(ErrorMessage::DATA_NOT_INTEGRITY, ErrorCode::DATA_NOT_INTEGRITY);
                }
                if ($paymentLinkRes['data']) {
                    return $paymentLinkRes['data'];
                }
            }

            throw new Exception($paymentLinkRes['desc'], $paymentLinkRes['code']);
        } catch (Exception $error) {
            throw new Exception($error->getMessage(), $error->getCode());
        }
    }

    /**
     * Validate the Webhook URL of a payment channel and add or update the Webhook URL for that Payment Channel if successful.
     *
     * @param string $webhookUrl Webhook URL
     * @return string
     * @throws Exception
     */
    public function confirmWebhook(string $webhookUrl): string
    {
        if (!$webhookUrl || strlen($webhookUrl) == 0) {
            throw new Exception(ErrorMessage::INVALID_PARAMETER, ErrorCode::INVALID_PARAMETER);
        }

        $url = PAYOS_BASE_URL . '/confirm-webhook';

        try {
            $headers = [
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ];

            $data = ['webhookUrl' => $webhookUrl];

            $response = $this->httpClient->post($url, [
                'headers' => $headers,
                'json' => $data
            ]);

            $confirmWebhookRes = json_decode($response->getBody()->getContents(), true);

            if ($confirmWebhookRes['code'] != '00') {
                throw new Exception($confirmWebhookRes['desc'], $confirmWebhookRes['code']);
            }

            return $webhookUrl;
        } catch (Exception $error) {
            throw new Exception($error->getMessage(), $error->getCode());
        }
    }

    /**
     * Cancel the payment link of the order.
     *
     * @param string|int $orderCode Order code
     * @param ?string cancellationReason Reason for cancelling payment link (optional)
     * @return array
     * @throws Exception
     */
    public function cancelPaymentLink(string|int $orderCode, ?string $cancellationReason = null): array
    {
        if (!$orderCode || (is_string($orderCode) && strlen($orderCode) == 0) || (is_int($orderCode) && $orderCode < 0)) {
            throw new Exception(ErrorMessage::INVALID_PARAMETER, ErrorCode::INVALID_PARAMETER);
        }

        $url = PAYOS_BASE_URL . '/v2/payment-requests/' . $orderCode . '/cancel';

        try {
            $headers = [
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ];

            $data = ['cancellationReason' => $cancellationReason];

            $response = $this->httpClient->post($url, [
                'headers' => $headers,
                'json' => $data
            ]);

            $cancelPaymentLinkRes = json_decode($response->getBody()->getContents(), true);

            if ($cancelPaymentLinkRes['code'] == '00') {
                $cancelPaymentLinkResSignature = PayOSSignatureUtils::createSignatureFromObj(
                    $this->checksumKey,
                    $cancelPaymentLinkRes['data']
                );
                if ($cancelPaymentLinkResSignature !== $cancelPaymentLinkRes['signature']) {
                    throw new Exception(ErrorMessage::DATA_NOT_INTEGRITY, ErrorCode::DATA_NOT_INTEGRITY);
                }
                if ($cancelPaymentLinkRes['data']) {
                    return $cancelPaymentLinkRes['data'];
                }
            }

            throw new Exception($cancelPaymentLinkRes['desc'], $cancelPaymentLinkRes['code']);
        } catch (Exception $error) {
            throw new Exception($error->getMessage(), $error->getCode());
        }
    }

    /**
     * Verify data received via webhook after payment.
     *
     * @param array $webhookBody Request body received from webhook
     * @return array
     * @throws Exception
     */
    public function verifyPaymentWebhookData(array $webhookBody): array
    {
        if (!$webhookBody || count($webhookBody) == 0) {
            throw new Exception(ErrorMessage::NO_DATA, ErrorCode::NO_DATA);
        }
        $signature = $webhookBody['signature'] ?? null;
        $data = $webhookBody['data'] ?? null;

        if (!$signature) {
            throw new Exception(ErrorMessage::NO_SIGNATURE, ErrorCode::NO_SIGNATURE);
        }
        if (!$data) {
            throw new Exception(ErrorMessage::NO_DATA, ErrorCode::NO_DATA);
        }
        $signatureData = PayOSSignatureUtils::createSignatureFromObj($this->checksumKey, $data);
        if ($signatureData !== $signature) {
            throw new Exception(ErrorMessage::DATA_NOT_INTEGRITY, ErrorCode::DATA_NOT_INTEGRITY);
        }
        return $data;
    }
}
