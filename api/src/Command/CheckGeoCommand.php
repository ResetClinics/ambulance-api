<?php

namespace App\Command;

use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\YaGeolocation\Api;
use DomainException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'check:geo',
    description: 'Add a short description for your command',
)]
class CheckGeoCommand extends Command
{

    private CallingRepository $callings;
    private Api $geocodingApi;
    private Flusher $flusher;

    public function __construct(CallingRepository $callings, Api $geocodingApi, Flusher $flusher)
    {
        parent::__construct();

        $this->callings = $callings;
        $this->geocodingApi = $geocodingApi;
        $this->flusher = $flusher;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->callings->findAll() as $calling){
            if((!$calling->getLat() || !$calling->getLon()) && !empty($calling->getAddress())){
                try {
                    dump($calling->getAddress());
                    $geolocation = $this->geocodingApi->getPositionByAddress($calling->getAddress());
                    dd($geolocation);
                    if ($geolocation){
                        dd($geolocation);
                        $calling->setLat($geolocation->getLat());
                        $calling->setLon($geolocation->getLon());
                    }
                }catch (DomainException){}
            }

            $this->flusher->flush();
        }

        $io->success('Geo updated.');

        return Command::SUCCESS;
    }
}
