<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @internal
 */
final class UserRepositoryTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public function testSomething(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $userRepository = $container->get(UserRepository::class);
        $badUser = new BadUserClass();

        $this->expectExceptionMessage('Instances of "App\Tests\Service\BadUserClass" are not supported.');

        $userRepository->upgradePassword($badUser, 'newPasswordHash');
    }
}

class BadUserClass implements PasswordAuthenticatedUserInterface
{
    public function getPassword(): ?string
    {
        return 'password';
    }
}
