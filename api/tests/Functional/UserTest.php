<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\AbstractTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @internal
 */
final class UserTest extends AbstractTest
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUsersListAdminAccess(): void
    {
        $this->createClientWithCredentials()->request('GET', '/api/users');

        self::assertResponseIsSuccessful();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testUsersListUserAccessDenied(): void
    {
        $token = $this->getToken([
            'phone' => '79000000001',
            'password' => 'secret',
        ]);

        $this->createClientWithCredentials($token)->request('GET', '/api/users');
        self::assertJsonContains(['hydra:description' => 'Access Denied.']);
        self::assertResponseStatusCodeSame(403);
    }
}
