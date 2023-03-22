<?php

namespace App\Entity;

use App\Repository\AmoCrmTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AmoCrmToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $accessToken = null;

    #[ORM\Column(length: 255)]
    private ?string $refreshToken = null;

    #[ORM\Column(nullable: true)]
    private ?int $expires = null;

    #[ORM\Column(length: 255)]
    private ?string $baseDomain = null;

    /**
     * @param string|null $accessToken
     * @param string|null $refreshToken
     * @param int|null $expires
     * @param string|null $baseDomain
     */
    public function __construct(?string $accessToken, ?string $refreshToken, ?int $expires, ?string $baseDomain)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expires = $expires;
        $this->baseDomain = $baseDomain;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getExpires(): ?int
    {
        return $this->expires;
    }

    public function setExpires(?int $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getBaseDomain(): ?string
    {
        return $this->baseDomain;
    }

    public function setBaseDomain(string $baseDomain): self
    {
        $this->baseDomain = $baseDomain;

        return $this;
    }
}
