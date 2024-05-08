<?php

namespace App\Command;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\LinkModel;
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

        $lead = $this->client->leads()->getOne('22852671');

        if (!$lead) {
            throw new NotFoundHttpException('Не получен лид');
        }

        $linksService = $this->client->links(EntityTypesInterface::LEADS);

        $filter = new EntitiesLinksFilter(['22852671']);
        $allLinks = $linksService->get($filter);


        $contactId = null;
        $companyId = null;
        /** @var LinkModel $link */
        foreach ($allLinks as $link) {
           if (
               $link->getMetadata()
               && isset($link->getMetadata()['main_contact'])
               && $link->getMetadata()['main_contact']
           ) {
               $contactId = $link->getToEntityId();
           }

           if ($link->getToEntityType() === 'companies'){
               $companyId = $link->getToEntityId();
           }
        }



        return Command::SUCCESS;
    }
}
