<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use App\Dto\CreateCategoryDto;
use App\Dto\UpdateCategoryDto;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use App\State\CreateCategoryProcessor;
use App\State\DeleteCategoryProcessor;
use App\State\UpdateCategoryProcessor;
use ApiPlatform\Metadata\GetCollection;
use App\Contract\PlatformCentricInterface;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)] 
#[ORM\Table(name: '`category`')]
#[ApiResource(
    normalizationContext: ['groups' => 'category:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_CATEGORY_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_CATEGORY_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_CATEGORY_CREATE")',
            input: CreateCategoryDto::class,
            processor: CreateCategoryProcessor::class,
        ),
        new Patch( 
            security: 'is_granted("ROLE_CATEGORY_UPDATE")',
            input: UpdateCategoryDto::class,
            processor: UpdateCategoryProcessor::class,
        ),
        new Delete(
            security:"is_granted('ROLE_CATEGORY_DELETE')",
            processor: DeleteCategoryProcessor::class
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'position' => 'exact',
    'active' => 'exact',
    'menu' => 'exact',
    'platformId' => 'exact',
    'deleted' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'position'])]
class Category implements RessourceInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "CT";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'CT_ID', length: 16)]
    #[Groups(['category:get', 'product:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(name: 'CT_MENU', referencedColumnName: 'MN_ID', nullable: false)]
    #[Groups(['category:get'])]
    private ?Menu $menu = null;

    #[ORM\Column(name: 'CT_LABEL', length: 120)]
    #[Groups(['category:get', 'product:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'CT_DESCRIPTION', type: Types::TEXT, nullable: true)]
    #[Groups(['category:get'])]
    private ?string $description = null;

    #[ORM\Column(name: 'CT_DELETED', options: ['default' => false])]
    #[Groups(['category:get'])]
    private ?bool $deleted = false;

    #[ORM\Column(name: 'CT_POSITION')]
    #[Groups(['category:get'])]
    private ?int $position = null;

    #[ORM\Column(name: 'CT_ACTIVE')]
    #[Groups(['category:get'])]
    private ?bool $active = null;

    #[ORM\Column(name: 'CT_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['category:get'])]
    private ?string $platformId = null;

    #[ORM\Column(name: 'CT_CREATED_AT')]
    #[Groups(['category:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'CT_UPDATED_AT', nullable: true)]
    #[Groups(['category:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(Menu $menu): static
    {
        $this->menu = $menu;
        return $this;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;
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

    /**
     * Get the value of platformId
     */ 
    public function getPlatformId(): string|null
    {
        return $this->platformId;
    }

    /**
     * Set the value of platformId
     *
     * @return  self
     */ 
    public function setPlatformId(?string $platformId): static
    {
        $this->platformId = $platformId;

        return $this;
    }
}
