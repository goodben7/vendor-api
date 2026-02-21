<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use App\Dto\CreatePlatformTableDto;
use App\Dto\UpdatePlatformTableDto;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Contract\PlatformCentricInterface;
use App\Repository\PlatformTableRepository;
use App\State\CreatePlatformTableProcessor;
use App\State\DeletePlatformTableProcessor;
use App\State\UpdatePlatformTableProcessor;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PlatformTableRepository::class)]
#[ORM\Table(name: '`platform_table`')]
#[ApiResource(
    normalizationContext: ['groups' => 'platform_table:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_PLATFORM_TABLE_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PLATFORM_TABLE_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_PLATFORM_TABLE_CREATE")',
            input: CreatePlatformTableDto::class,
            processor: CreatePlatformTableProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_PLATFORM_TABLE_UPDATE")',
            input: UpdatePlatformTableDto::class,
            processor: UpdatePlatformTableProcessor::class,
        ),
        new Delete(
            security:"is_granted('ROLE_PLATFORM_TABLE_DELETE')",
            processor: DeletePlatformTableProcessor::class
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'active' => 'exact',
    'platformId' => 'exact',
    'capacity' => 'exact',
    'status' => 'exact',
    'deleted' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
class PlatformTable implements RessourceInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "PT";

    public const string STATUS_AVAILABLE = 'available';
    public const string STATUS_OCCUPIED = 'occupied';
    public const string STATUS_RESERVED = 'reserved';
    public const string STATUS_CLEANING = 'cleaning';
    public const string STATUS_OUT_OF_SERVICE = 'out_of_service';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'PT_ID', length: 16)]
    #[Groups(['platform_table:get', 'tablet:get', 'order:get'])]
    private ?string $id = null;

    #[ORM\Column(name: 'PT_LABEL', length: 120)]
    #[Groups(['platform_table:get', 'tablet:get', 'order:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'PT_ACTIVE')]
    #[Groups(['platform_table:get', 'order:get'])]
    private ?bool $active = null;

    #[ORM\Column(name: 'PT_CREATED_AT')]
    #[Groups(['platform_table:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'PT_UPDATED_AT', nullable: true)]
    #[Groups(['platform_table:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(name: 'PT_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['platform_table:get'])]
    private ?string $platformId = null;

    #[ORM\Column(name: 'PT_CAPACITY', nullable: true)]
    #[Groups(['platform_table:get'])]
    private ?int $capacity = null;

    #[ORM\Column(name: 'PT_STATUS', length: 60, nullable: true, options: ['default' => self::STATUS_AVAILABLE])]
    #[Groups(['platform_table:get'])]
    private ?string $status = self::STATUS_AVAILABLE;

    #[ORM\Column(name: 'PT_DELETED', options: ['default' => false])]
    #[Groups(['platform_table:get'])]
    private ?bool $deleted = false;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getPlatformId(): ?string
    {
        return $this->platformId;
    }

    public function setPlatformId(?string $platformId): static
    {
        $this->platformId = $platformId;
        return $this;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): static
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get the value of deleted
     */ 
    public function getDeleted(): bool|null
    {
        return $this->deleted;
    }

    /**
     * Set the value of deleted
     *
     * @return  self
     */ 
    public function setDeleted(?bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }
}
