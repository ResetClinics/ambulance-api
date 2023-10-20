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

    #[Route('/spelling', name: 'spelling')]
    public function spelling(
        Request $request,
        SpellingPageRepository $pagesRepository,
        SpellingWordRepository $wordsRepository,
        Flusher $flusher
    ): Response
    {
        $pages = $pagesRepository->findByIsChecked(false, 10);

        /** @var SpellingPage $page */
        foreach ($pages as $page){
            dump($page->getUrl());
            $resp = $this->client->request('GET', $page->getUrl());

            $content = $resp->getContent();

            $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
            $content = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $content);
            $content = preg_replace('#<!--(.*?)>(.*?)-->#is', '', $content);


            $resp = $this->client->request(
                'POST',
                'https://speller.yandex.net/services/spellservice/checkText',
                [
                    'body' => [
                        'text' => $content,
                        'lang' => 'ru',
                        'options' => 0,
                        'format' => 'html',
                    ],
                ]
            );

            $spellingResponse = $this->serializer->deserialize($resp->getContent(), Test::class, 'xml');

            $hasErrors = false;

            if ($spellingResponse->error){
                $errors = array_reverse($spellingResponse->error);
                foreach ($errors as $error){
                    $words = $wordsRepository->findByWord($error['word']);
                    if (count($words) === 0){
                        $hasErrors = true;
                        dump($error);
                    }
                }
            }

            if (!$hasErrors){
                $page->setIsChecked(true);
                $flusher->flush();
            }else{
                dd('Есть ошибки');
            }
        }

        dd(count($pages));









        return new Response($content);
    }
}
