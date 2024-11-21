<?php

namespace App\Services\ATS\BlacklistService;

readonly class PhoneMember
{
    public function __construct(
        public string $id,
        public string $blacklistId,
        public string $number,
    )
    {
    }

    public static function fromArray(array $data): PhoneMember
    {
        return new self(
            $data['id'],
            $data['blacklistId'],
            $data['number'],
        );
    }
}