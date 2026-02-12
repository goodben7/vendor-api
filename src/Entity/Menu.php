<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\MenuRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Model\RessourceInterface;
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
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'active' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
class Menu implements RessourceInterface 
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

    #[ORM\Column(name: 'MN_CREATED_AT')]
    #[Groups(['menu:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'MN_UPDATED_AT', nullable: true)]
    #[Groups(['menu:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

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
}
