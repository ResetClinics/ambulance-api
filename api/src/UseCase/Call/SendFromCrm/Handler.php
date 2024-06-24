<?php

namespace App\UseCase\Call\SendFromCrm;

class Handler
{
    public function handle(Command $command): void
    {
        file_put_contents(
            dirname(__DIR__) . '/../../../var/hook-home-call-handler.txt',
            print_r($command, true).PHP_EOL,
            FILE_APPEND);
    }
}