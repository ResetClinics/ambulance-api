<?php

namespace App\Command;

use App\Services\WSClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebSocket\Client;

#[AsCommand(
    name: 'websocket:test',
    description: 'Command test websocket connection',
)]
class WebsocketTestCommand extends Command
{
    public function __construct(
        private readonly WSClient $client
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->client->send(json_encode( ['type' => 'test'] ));

        $io->success('Success.');

        return Command::SUCCESS;
    }
}
