<?php

namespace App\Entity\Calling;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Embeddable]
class TransportCharge
{
    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $mileage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $tollRoad = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $parkingFees = null;

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): void
    {
        $this->mileage = $mileage;
    }

    public function getTollRoad(): ?int
    {
        return $this->tollRoad;
    }

    public function setTollRoad(?int $tollRoad): void
    {
        $this->tollRoad = $tollRoad;
    }

    public function getParkingFees(): ?int
    {
        return $this->parkingFees;
    }

    public function setParkingFees(?int $parkingFees): void
    {
        $this->parkingFees = $parkingFees;
    }
}