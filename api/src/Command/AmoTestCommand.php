<?php

declare(strict_types=1);

namespace App\Command;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Services\AmoCRM;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'amo:test',
    description: 'Тестовое получение лида из amocrm',
)]
class AmoTestCommand extends Command
{
    private AmoCRMApiClient $client;
    public function __construct(
        AmoCRM $amoCRM,
   ) {
        $this->client = $amoCRM->getClient();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Lead id')
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $id = $input->getArgument('id');

        $filter = new LeadsFilter();
        $filter->setIds([$id]);

        $leads = $this->client->leads()->get($filter);




        dd($leads);
        return Command::SUCCESS;
    }
}
