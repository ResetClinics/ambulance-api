<?php

declare(strict_types=1);

namespace App\Controller;

use App\Console\Test;
use App\Entity\SpellingPage;
use App\Flusher;
use App\Repository\SpellingPageRepository;
use App\Repository\SpellingWordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    private HttpClientInterface $client;
    private SerializerInterface $serializer;

    public function __construct(
        HttpClientInterface $client,
        SerializerInterface $serializer
    )
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    #[Route('/api/version', name: 'version')]
    public function version(): Response
    {
        return $this->json([
            'min' => '1.0.0',
            'target' => '1.0.0',
        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->json(['app' => 'ambulance']);
    }

}
