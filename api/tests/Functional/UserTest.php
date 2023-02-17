<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @internal
 */
final class UserTest extends ApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testSuccess(): void
    {
        self::createClient()->request('GET', '/api/users');

        self::assertResponseIsSuccessful();

        self::assertEquals(42, 42);
    }
}
