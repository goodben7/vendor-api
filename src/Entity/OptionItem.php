<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OptionItemRepository;
use App\Contract\PlatformCentricInterface;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: OptionItemRepository::class)] 
#[ORM\Table(name: '`option_item`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => 'option_item:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_OPTION_ITEM_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_OPTION_ITEM_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_OPTION_ITEM_CREATE")',
            denormalizationContext: ['groups' => 'option_item:post'],
            processor: PersistProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_OPTION_ITEM_UPDATE")',
            denormalizationContext: ['groups' => 'option_item:patch'],
            processor: PersistProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'optionGroup' => 'exact',
    'platformId' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'priceDelta'])]
class OptionItem implements RessourceInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "OI";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'OI_ID', length: 16)]
    #[Groups(['option_item:get', 'product:get', 'option_group:get', 'order:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'optionItems')]
    #[ORM\JoinColumn(name: 'OI_GROUP', referencedColumnName: 'OG_ID', nullable: false)]
    #[Groups(['option_item:get', 'option_item:post', 'option_item:patch'])]
    private ?OptionGroup $optionGroup = null;

    #[ORM\Column(name: 'OI_LABEL', length: 120)]
    #[Groups(['option_item:get', 'option_item:post', 'option_item:patch', 'product:get', 'option_group:get', 'order:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'OI_PRICE_DELTA', type: Types::DECIMAL, precision: 17, scale: 2)]
    #[Groups(['option_item:get', 'option_item:post', 'option_item:patch', 'product:get', 'option_group:get', 'order:get'])]
    private ?string $priceDelta = null;

    #[ORM\Column(name: 'OI_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['option_item:get'])]
    private ?string $platformId = null;

    #[ORM\Column(name: 'OI_CREATED_AT')]
    #[Groups(['option_item:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'OI_UPDATED_AT', nullable: true)]
    #[Groups(['option_item:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOptionGroup(): ?OptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(?OptionGroup $optionGroup): static
    {
        $this->optionGroup = $optionGroup;
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

    public function getPriceDelta(): ?string
    {
        return $this->priceDelta;
    }

    public function setPriceDelta(?string $priceDelta): static
    {
        $this->priceDelta = $priceDelta;
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
    public function setPlatformId(string|null $platformId): static
    {
        $this->platformId = $platformId;

        return $this;
    }
}
