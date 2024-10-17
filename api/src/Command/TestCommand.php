<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\YaDiskApi\YaDiskApiInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'api:test',
    description: 'Init user for exchange',
)]
class TestCommand extends Command
{

    public function __construct(
        private readonly YaDiskApiInterface $api
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filePath = dirname(__DIR__) . "/../var/гушина .aac";

        $this->api->upload($filePath, 'disk:/Аудиозаписи вызовов/11/гушина .aac');

        $filePath = dirname(__DIR__) . "/../var/часовая.m4a";

        $this->api->upload($filePath, 'disk:/Аудиозаписи вызовов/11/часовая.m4a');

        $io->success('.');

        return Command::SUCCESS;
    }
}
