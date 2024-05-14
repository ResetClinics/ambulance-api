<?php

namespace App\Command;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use App\Dto\Amo\Employee;
use App\Services\AmoCRM;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


#[AsCommand(
    name: 'app:test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    private AmoCRMApiClient $client;
    public function __construct(
        AmoCRM        $amoCRM,
    )
    {
        parent::__construct();
        $this->client = $amoCRM->getClient();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $lead = $this->client->leads()->getOne('22891185');

        if (!$lead) {
            throw new NotFoundHttpException('Не получен лид');
        }

        dump($lead);

        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 873881) {
                $userName = $field->getValues()?->first()->getValue();

                dump($userName);
                dump($this->getNumberInsideBrackets($userName));
            }
        }

        return Command::SUCCESS;
    }

    function getNumberInsideBrackets($str): ?int
    {
        preg_match('/\((\d+)\)/', $str, $matches);
        dump($matches);
        return isset($matches[1]) ? (int)$matches[1] : null;
    }
}
