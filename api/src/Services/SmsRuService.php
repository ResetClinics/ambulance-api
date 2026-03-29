<?php

declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Отправка SMS через SMS.ru API.
 *
 * Требуется переменная окружения SMSRU_API_ID.
 * @see https://sms.ru/api
 */
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

    public function send(string $phone, string $message): bool
    {
        // Формат телефона: 79991234567 (11 цифр)
        $phone = preg_replace('/\D/', '', $phone);

        try {
            $response = $this->httpClient->request('GET', 'https://sms.ru/sms/send', [
                'query' => [
                    'api_id' => $this->apiId,
                    'to'     => $phone,
                    'msg'    => $message,
                    'json'   => 1,
                ],
            ]);

            $data = $response->toArray();

            if (($data['status'] ?? null) === 'OK') {
                $this->logger->info('SMS sent', ['phone' => $phone]);
                return true;
            }

            $this->logger->error('SMS.ru error', ['response' => $data]);
            return false;
        } catch (\Throwable $e) {
            $this->logger->error('SMS sending failed', [
                'phone'   => $phone,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Отправить 4-значный код звонком через sms.ru /code/call.
     * Возвращает код (4 цифры) или null при ошибке.
     *
     * @see https://sms.ru/api/code_call
     */
    public function callCode(string $phone, string $ip = ''): ?string
    {
        $phone = preg_replace('/\D/', '', $phone);

        $query = [
            'api_id' => $this->apiId,
            'phone'  => $phone,
            'json'   => 1,
        ];

        if ($ip !== '') {
            $query['ip'] = $ip;
        }

        try {
            $response = $this->httpClient->request('GET', 'https://sms.ru/code/call', [
                'query' => $query,
            ]);

            $data = $response->toArray();

            $this->logger->warning('SmsRu callCode response', ['phone' => $phone, 'response' => $data]);

            if (($data['status'] ?? null) === 'OK') {
                return (string) $data['code'];
            }

            $this->logger->error('SmsRu callCode error', ['response' => $data]);
            return null;
        } catch (\Throwable $e) {
            $this->logger->error('SmsRu callCode failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
