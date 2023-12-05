<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    public function __construct(
        readonly private HttpClientInterface $client
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dddd = $this->client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'key='
            ],
            'json' => [
                "data" =>  [
                    "callingId" =>  222,
                    "callingStatus" =>  'assigned',
                    "url" =>  'сalls',
                ],
                'to' => '',
                'notification' => [
                    'title' => 'Титле',
                    'body' =>  'Боди',
                ]

            ],
        ]);

        dd($dddd->toArray());

        $io->success('Test successful.');

        return Command::SUCCESS;
    }
}
