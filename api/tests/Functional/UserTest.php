<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserTest extends ApiTestCase
{

    /**
     * @throws TransportExceptionInterface
     */
    public function testSuccess(): void
    {
        $response = static::createClient()->request('GET', '/api/users');

        self::assertResponseIsSuccessful();

        $this->assertEquals(42, 42);
    }
}
