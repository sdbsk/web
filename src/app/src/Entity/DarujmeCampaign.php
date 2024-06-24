<?php

namespace App\Entity;

use App\Repository\DarujmeCampaignRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DarujmeCampaignRepository::class)]
class DarujmeCampaign
{
    const array AMOUNT_TYPES = ['onetime' => 'onetime', 'periodical' => 'recurring'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?bool $is_legacy = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\Column]
    private ?int $donated_total = null;

    #[ORM\Column(length: 40)]
    private ?string $darujmeId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $onetimeAmount1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $onetimeAmount2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $onetimeAmount3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $recurringAmount1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $recurringAmount2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $recurringAmount3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $onetimeAmount4 = null;

    #[ORM\Column(nullable: true)]
    private ?int $recurringAmount4 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isLegacy(): ?bool
    {
        return $this->is_legacy;
    }

    public function setLegacy(bool $is_legacy): static
    {
        $this->is_legacy = $is_legacy;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDonatedTotal(): ?int
    {
        return $this->donated_total;
    }

    public function setDonatedTotal(int $donated_total): static
    {
        $this->donated_total = $donated_total;

        return $this;
    }

    public function getDarujmeId(): ?string
    {
        return $this->darujmeId;
    }

    public function setDarujmeId(string $darujmeId): static
    {
        $this->darujmeId = $darujmeId;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOnetimeAmount1(): ?int
    {
        return $this->onetimeAmount1;
    }

    public function setOnetimeAmount1(?int $onetimeAmount1): static
    {
        $this->onetimeAmount1 = $onetimeAmount1;

        return $this;
    }

    public function getOnetimeAmount2(): ?int
    {
        return $this->onetimeAmount2;
    }

    public function setOnetimeAmount2(?int $onetimeAmount2): static
    {
        $this->onetimeAmount2 = $onetimeAmount2;

        return $this;
    }

    public function getOnetimeAmount3(): ?int
    {
        return $this->onetimeAmount3;
    }

    public function setOnetimeAmount3(?int $onetimeAmount3): static
    {
        $this->onetimeAmount3 = $onetimeAmount3;

        return $this;
    }

    public function getRecurringAmount1(): ?int
    {
        return $this->recurringAmount1;
    }

    public function setRecurringAmount1(?int $recurringAmount1): static
    {
        $this->recurringAmount1 = $recurringAmount1;

        return $this;
    }

    public function getRecurringAmount2(): ?int
    {
        return $this->recurringAmount2;
    }

    public function setRecurringAmount2(?int $recurringAmount2): static
    {
        $this->recurringAmount2 = $recurringAmount2;

        return $this;
    }

    public function getRecurringAmount3(): ?int
    {
        return $this->recurringAmount3;
    }

    public function setRecurringAmount3(?int $recurringAmount3): static
    {
        $this->recurringAmount3 = $recurringAmount3;

        return $this;
    }

    public function getOnetimeAmount4(): ?int
    {
        return $this->onetimeAmount4;
    }

    public function setOnetimeAmount4(?int $onetimeAmount4): static
    {
        $this->onetimeAmount4 = $onetimeAmount4;

        return $this;
    }

    public function getRecurringAmount4(): ?int
    {
        return $this->recurringAmount4;
    }

    public function setRecurringAmount4(?int $recurringAmount4): static
    {
        $this->recurringAmount4 = $recurringAmount4;

        return $this;
    }
}
