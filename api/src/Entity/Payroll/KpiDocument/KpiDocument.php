<?php

declare(strict_types=1);

namespace App\Entity\Payroll\KpiDocument;

use App\Repository\Payroll\KpiDocument\KpiDocumentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: KpiDocumentRepository::class)]
#[ORM\Table(name: 'payroll_kpi_documents')]
class KpiDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    private ?DateTimeImmutable $periodStart = null;

    #[ORM\Column(nullable: true)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    private ?DateTimeImmutable $periodEnd = null;

    #[ORM\OneToMany(mappedBy: 'document', targetEntity: KpiRecord::class, orphanRemoval: true)]
    private Collection $records;

    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, KpiRecord>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function addRecord(KpiRecord $record): static
    {
        if (!$this->records->contains($record)) {
            $this->records->add($record);
        }

        return $this;
    }

    public function removeRecord(KpiRecord $record): static
    {
        $this->records->removeElement($record);

        return $this;
    }

    public function clearRecords(): void
    {
        $this->records->clear();
    }
}
