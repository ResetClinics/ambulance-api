<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'password_reset_codes')]
#[ORM\Index(columns: ['phone', 'check_id'], name: 'idx_phone_check')]
class PasswordResetCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 11)]
    private string $phone;

    /** sms.ru callcheck ID */
    #[ORM\Column(type: 'string', length: 255)]
    private string $checkId;

    #[ORM\Column(type: 'boolean')]
    private bool $confirmed = false;

    #[ORM\Column(type: 'boolean')]
    private bool $used = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $phone, string $checkId, int $ttlMinutes = 5)
    {
        $this->phone = $phone;
        $this->checkId = $checkId;
        $this->createdAt = new \DateTimeImmutable();
        $this->expiresAt = $this->createdAt->modify("+{$ttlMinutes} minutes");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCheckId(): string
    {
        return $this->checkId;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function markConfirmed(): void
    {
        $this->confirmed = true;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function markUsed(): void
    {
        $this->used = true;
    }

    public function isExpired(): bool
    {
        return new \DateTimeImmutable() > $this->expiresAt;
    }

    public function isValid(): bool
    {
        return !$this->used && !$this->isExpired();
    }
}
