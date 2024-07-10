<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebSocket\Client;

#[AsCommand(
    name: 'websocket:test',
    description: 'Command test websocket connection',
)]
class WebsocketTestCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = new Client("ws://31.129.99.100:3001");

        dd($client->isConnected());

        $client->text("Hello WebSocket.org!");
        echo $client->receive();

        $client->close();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
