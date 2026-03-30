<?php

declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsRuService
{
    private string $apiId;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(
        string $smsRuApiId,
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
    ) {
        $this->apiId = $smsRuApiId;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * @return array{check_id: string, call_phone: string, call_phone_pretty: string}|null
     */
    public function callcheckAdd(string $phone): ?array
    {
        $phone = preg_replace('/\D/', '', $phone);

        try {
            $response = $this->httpClient->request('GET', 'https://sms.ru/callcheck/add', [
                'query' => [
                    'api_id' => $this->apiId,
                    'phone'  => $phone,
                    'json'   => 1,
                ],
            ]);

            $data = $response->toArray();

            if (($data['status'] ?? null) === 'OK') {
                return [
                    'check_id'          => (string) $data['check_id'],
                    'call_phone'        => (string) $data['call_phone'],
                    'call_phone_pretty' => (string) ($data['call_phone_pretty'] ?? $data['call_phone']),
                ];
            }

            $this->logger->error('SmsRu callcheck/add error', ['response' => $data]);
            return null;
        } catch (\Throwable $e) {
            $this->logger->error('SmsRu callcheck/add failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * @return string "confirmed"|"pending"|"error"
     */
    public function callcheckStatus(string $checkId): string
    {
        try {
            $response = $this->httpClient->request('GET', 'https://sms.ru/callcheck/status', [
                'query' => [
                    'api_id'   => $this->apiId,
                    'check_id' => $checkId,
                    'json'     => 1,
                ],
            ]);

            $data = $response->toArray();

            if (($data['status'] ?? null) === 'OK') {
                // 401 = номер подтверждён (звонок получен)
                if (((int) ($data['check_status'] ?? 0)) === 401) {
                    return 'confirmed';
                }
                return 'pending';
            }

            return 'error';
        } catch (\Throwable $e) {
            $this->logger->error('SmsRu callcheck/status failed', [
                'check_id' => $checkId,
                'error'    => $e->getMessage(),
            ]);
            return 'error';
        }
    }
}
