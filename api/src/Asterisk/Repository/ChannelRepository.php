<?php

declare(strict_types=1);

namespace App\Asterisk\Repository;

use Doctrine\Persistence\ManagerRegistry;

readonly class ChannelRepository
{
    public function __construct(
        private ManagerRegistry $doctrine
    ) {}

    public function hasChannelByClientPhoneNumber(string $clientPhoneNumber): bool
    {
        $connection = $this->doctrine->getConnection('asterisk');
        return $connection->fetchOne(
            'SELECT 1 FROM channels WHERE client = :clientPhoneNumber',
            ['clientPhoneNumber' => $clientPhoneNumber]
        );
    }

    public function create(string $clientPhoneNumber, string $teamNumber): void
    {
        $connection = $this->doctrine->getConnection('asterisk');
        $connection->insert('channels', ['client' => $clientPhoneNumber, 'team' => $teamNumber]);
    }

    public function update(string $clientPhoneNumber, string $teamNumber): void
    {
        $connection = $this->doctrine->getConnection('asterisk');
        $connection->update('channels', ['team' => $teamNumber], ['client' => $clientPhoneNumber]);
    }

    public function deleteChannelByClientPhoneNumber(string $clientPhoneNumber): void
    {
        $connection = $this->doctrine->getConnection('asterisk');
        $connection->delete('channels', ['client' => $clientPhoneNumber]);
    }
}
