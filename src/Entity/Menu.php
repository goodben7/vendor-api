<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Model\RessourceInterface;
use App\Repository\MenuRepository;
use App\State\DeleteMenuProcessor;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Contract\PlatformCentricInterface;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: '`menu`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => 'menu:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_MENU_DETAILS")',
        ),
        new GetCollection(
            security: 'is_granted("ROLE_MENU_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_MENU_CREATE")',
            denormalizationContext: ['groups' => 'menu:post',],
            processor: PersistProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_MENU_UPDATE")',
            denormalizationContext: ['groups' => 'menu:patch',],
            processor: PersistProcessor::class,
        ),
        new Delete(
            security:"is_granted('ROLE_MENU_DELETE')",
            processor: DeleteMenuProcessor::class
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'active' => 'exact',
    'platformId' => 'exact',
    'deleted' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
class Menu implements RessourceInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "MN";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'MN_ID', length: 16)]
    #[Groups(['menu:get', 'category:get'])]
    private ?string $id = null;

    #[ORM\Column(name: 'MN_LABEL', length: 120)]
    #[Groups(['menu:get', 'menu:post', 'menu:patch', 'category:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'MN_ACTIVE')]
    #[Groups(['menu:get', 'menu:post', 'menu:patch'])]
    private ?bool $active = null;

    #[ORM\Column(name: 'MN_DESCRIPTION', type: Types::TEXT, nullable: true)]
    #[Groups(['menu:get', 'menu:post', 'menu:patch'])]
    private ?string $description = null;

    #[ORM\Column(name: 'MN_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['menu:get'])]
    private ?string $platformId = null;

    #[ORM\Column(name: 'MN_DELETED', options: ['default' => false])]
    #[Groups(['menu:get'])]
    private ?bool $deleted = false;

    #[ORM\Column(name: 'MN_CREATED_AT')]
    #[Groups(['menu:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'MN_UPDATED_AT', nullable: true)]
    #[Groups(['menu:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(mappedBy: 'menu', targetEntity: Category::class)]
    #[Groups(['menu:get'])]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
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

    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function buildCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setMenu($this);
        }
        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);
        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription(): string|null
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription(?string $description): static
    {
        $this->description = $description;

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
