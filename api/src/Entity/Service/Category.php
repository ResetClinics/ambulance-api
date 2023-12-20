<?php

namespace App\Entity\Service;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Partner\Agreement\Row;
use App\Repository\Service\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'service_categories')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
    ],
    normalizationContext: ['groups' => ['service-category:read', 'service-category:item:read']],
    denormalizationContext: ['groups' => ['service-category:write']],
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service-category:item:read'])]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Row::class)]
    private Collection $rows;

    #[ORM\Column(length: 255)]
    #[Groups(['service-category:item:read', 'service-category:write'])]
    private ?string $name = null;

    public function __construct()
    {
        $this->rows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, \App\Entity\Calling\Row>
     */
    public function getRows(): Collection
    {
        return $this->rows;
    }

    public function addRow(Row $row): self
    {
        if (!$this->rows->contains($row)) {
            $this->rows->add($row);
            $row->setService($this);
        }

        return $this;
    }

    public function removeRow(Row $row): self
    {
        if ($this->rows->removeElement($row)) {
            // set the owning side to null (unless already changed)
            if ($row->getService() === $this) {
                $row->setService(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
