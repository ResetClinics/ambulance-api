<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

#[AsCommand(
    name: 'user:change-password-by-phone',
    description: 'Change user password by phone number',
)]
class UserChangePasswordByPhoneCommand extends Command
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('phone', InputArgument::REQUIRED, 'User phone number')
            ->addArgument('password', InputArgument::REQUIRED, 'New user password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $phone = (string) $input->getArgument('phone');
        $plainPassword = (string) $input->getArgument('password');

        try {
            $user = $this->users->getByPhone($phone);
        } catch (UserNotFoundException) {
            $io->error(\sprintf('User with phone "%s" not found.', $phone));

            return Command::FAILURE;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $this->users->save($user, true);

        $io->success(\sprintf('Password updated for user with phone "%s".', $phone));

        return Command::SUCCESS;
    }
}
