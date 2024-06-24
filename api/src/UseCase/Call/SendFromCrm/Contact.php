<?php

namespace App\UseCase\Call\SendFromCrm;

class Contact
{
    private ?string $name;
    private string $phone;

    public function __construct(?string $name, string $phone)
    {
        $this->name = $name;
        $this->phone = $phone;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}