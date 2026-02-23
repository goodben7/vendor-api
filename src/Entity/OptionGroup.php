<?php

namespace App\Entity;

use App\Entity\OptionItem;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Dto\CreateOptionGroupDto;
use App\Dto\UpdateOptionGroupDto;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OptionGroupRepository;
use App\State\CreateOptionGroupProcessor;
use App\State\UpdateOptionGroupProcessor;
use App\Contract\PlatformCentricInterface;
use Doctrine\Common\Collections\Collection;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OptionGroupRepository::class)] 
#[ORM\Table(name: '`option_group`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => 'option_group:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_OPTION_GROUP_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_OPTION_GROUP_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_OPTION_GROUP_CREATE")',
            input: CreateOptionGroupDto::class,
            processor: CreateOptionGroupProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_OPTION_GROUP_UPDATE")',
            input: UpdateOptionGroupDto::class,
            processor: UpdateOptionGroupProcessor::class,
        ),
        new Delete(
            security:"is_granted('ROLE_OPTION_GROUP_DELETE')",
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'product' => 'exact',
    'isRequired' => 'exact',
    'isAvailable' => 'exact',
    'platformId' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'maxChoices'])]
class OptionGroup implements RessourceInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "OG";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'OG_ID', length: 16)]
    #[Groups(['option_group:get', 'product:get', 'order:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'optionGroups')]
    #[ORM\JoinColumn(name: 'OG_PRODUCT', referencedColumnName: 'PD_ID', nullable: false)]
    #[Groups(['option_group:get', 'option_group:post', 'option_group:patch'])]
    private ?Product $product = null;

    #[ORM\Column(name: 'OG_LABEL', length: 120)]
    #[Groups(['option_group:get', 'option_group:post', 'option_group:patch', 'product:get', 'order:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'OG_IS_REQUIRED')]
    #[Groups(['option_group:get', 'option_group:post', 'option_group:patch', 'product:get'])]
    private ?bool $isRequired = null;

    #[ORM\Column(name: 'OG_MAX_CHOICES')]
    #[Groups(['option_group:get', 'option_group:post', 'option_group:patch', 'product:get'])]
    private ?int $maxChoices = null;

    #[ORM\Column(name: 'OG_IS_AVAILABLE')]
    #[Groups(['option_group:get', 'option_group:post', 'option_group:patch', 'product:get'])]
    private ?bool $isAvailable = null;

    #[ORM\Column(name: 'OG_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['option_group:get'])]
    private ?string $platformId = null;

    #[ORM\Column(name: 'OG_CREATED_AT')]
    #[Groups(['option_group:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'OG_UPDATED_AT', nullable: true)]
    #[Groups(['option_group:get'])]
    private ?\DateTimeImmutable $updatedAt = null;
    
    /**
     * @var Collection<int, OptionItem>
     */
    #[ORM\OneToMany(mappedBy: 'optionGroup', targetEntity: OptionItem::class, cascade: ['all'])]
    #[Groups(['option_group:get', 'product:get', 'order:get'])]
    private Collection $optionItems;
    
    public function __construct()
    {
        $this->optionItems = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
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

    public function isRequired(): ?bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): static
    {
        $this->isRequired = $isRequired;
        return $this;
    }

    public function getMaxChoices(): ?int
    {
        return $this->maxChoices;
    }

    public function setMaxChoices(?int $maxChoices): static
    {
        $this->maxChoices = $maxChoices;
        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;
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
    
    public function getOptionItems(): Collection
    {
        return $this->optionItems;
    }
    
    public function addOptionItem(OptionItem $item): static
    {
        if (!$this->optionItems->contains($item)) {
            $this->optionItems->add($item);
            $item->setOptionGroup($this);
        }
        return $this;
    }
    
    public function removeOptionItem(OptionItem $item): static
    {
        if ($this->optionItems->removeElement($item)) {
            if ($item->getOptionGroup() === $this) {
                $item->setOptionGroup(null);
            }
        }
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
     * Get the value of isAvailable
     */ 
    public function getIsAvailable(): bool|null
    {
        return $this->isAvailable;
    }

    /**
     * Get the value of isRequired
     */ 
    public function getIsRequired(): bool|null
    {
        return $this->isRequired;
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
