<?php

declare(strict_types=1);

namespace App\Command;

use App\Flusher;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'user:set-default-city',
    description: 'Add a short description for your command',
)]
class UserSetDefaultCityCommand extends Command
{
    public function __construct(
        private readonly CityRepository $cityRepository,
        private readonly UserRepository $userRepository,
        private readonly Flusher $flusher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $city = $this->cityRepository->find(1);
        if (!$city) {
            $io->error('Город не найден');
            return Command::FAILURE;
        }

        foreach ($this->userRepository->findAll() as $user) {
            $user->setCities([$city]);
        }

        $this->flusher->flush();

        $io->success('Всем пользователям проставлена Москва');

        return Command::SUCCESS;
    }
}
