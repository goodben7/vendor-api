<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use App\Dto\CreateCategoryDto;
use App\Dto\UpdateCategoryDto;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use App\State\CreateCategoryProcessor;
use App\State\UpdateCategoryProcessor;
use ApiPlatform\Metadata\GetCollection;
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
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'position' => 'exact',
    'active' => 'exact',
    'menu' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'position'])]
class Category implements RessourceInterface
{
    public const string ID_PREFIX = "CT";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'CT_ID', length: 16)]
    #[Groups(['category:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'CT_MENU', referencedColumnName: 'MN_ID', nullable: false)]
    #[Groups(['category:get'])]
    private ?Menu $menu = null;

    #[ORM\Column(name: 'CT_LABEL', length: 120)]
    #[Groups(['category:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'CT_POSITION')]
    #[Groups(['category:get'])]
    private ?int $position = null;

    #[ORM\Column(name: 'CT_ACTIVE')]
    #[Groups(['category:get'])]
    private ?bool $active = null;

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
}
