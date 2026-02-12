<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\PlatformTableRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Model\RessourceInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Dto\CreatePlatformTableDto;
use App\Dto\UpdatePlatformTableDto;
use App\State\CreatePlatformTableProcessor;
use App\State\UpdatePlatformTableProcessor;

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
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'active' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
class PlatformTable implements RessourceInterface
{
    public const string ID_PREFIX = "PT";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'PT_ID', length: 16)]
    #[Groups(['platform_table:get', 'tablet:get'])]
    private ?string $id = null;

    #[ORM\Column(name: 'PT_LABEL', length: 120)]
    #[Groups(['platform_table:get', 'tablet:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'PT_ACTIVE')]
    #[Groups(['platform_table:get'])]
    private ?bool $active = null;

    #[ORM\Column(name: 'PT_CREATED_AT')]
    #[Groups(['platform_table:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'PT_UPDATED_AT', nullable: true)]
    #[Groups(['platform_table:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

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
}
