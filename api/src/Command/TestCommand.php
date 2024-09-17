<?php

declare(strict_types=1);

namespace App\Command;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Services\AmoCRM;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'lead:test',
    description: 'Init user for exchange',
)]
class TestCommand extends Command
{

    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
    )
    {
        $this->client = $amoCRM->getClient();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $filter = new LeadsFilter();
        $filter->setStatuses([
            [
                'status_id' => 38307805,
                'pipeline_id' => 4018768
            ]
        ]);


        $leads = $this->client->leads()->get($filter);
        dd($leads->count());


        $io->success('Новый пользователь добавлен.');

        return Command::SUCCESS;
    }
}
