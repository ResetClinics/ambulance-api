<?php

declare(strict_types=1);

namespace App\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'spelling:check',
    description: 'Add a short description for your command',
)]
class SpellingCheckCommand extends Command
{
    private HttpClientInterface $client;
    private SerializerInterface $serializer;

    public function __construct(
        HttpClientInterface $client,
        SerializerInterface $serializer
    ) {
        parent::__construct();
        $this->client = $client;
        $this->serializer = $serializer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->success('Spelling check finished.');

        $resp = $this->client->request('GET', 'https://rc-respect.ru/psihiatriya/');

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

        $content = $resp->getContent();

        // dd($content);

        dd($this->serializer->deserialize($content, Test::class, 'xml'));
        return Command::SUCCESS;
    }
}
