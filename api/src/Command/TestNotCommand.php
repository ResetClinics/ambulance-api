<?php

namespace App\Command;

use App\Services\YaGeolocation\Api;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'test:geocode',
    description: 'Add a short description for your command',
)]
class TestNotCommand extends Command
{
    public function __construct(
        private Api $api,
    )
    {
        parent::__construct();


    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $position = $this->api->getPositionByAddress('Москва, улица Новый Арбат, дом 24');

        dd($position);


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
