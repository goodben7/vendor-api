<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CurrencyRepository;
use ApiPlatform\Metadata\GetCollection;
use App\Contract\PlatformCentricInterface;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\CreateCurrencyDto;
use App\Dto\UpdateCurrencyDto;
use App\State\CreateCurrencyProcessor;
use App\State\UpdateCurrencyProcessor;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)] 
#[ORM\Table(name: '`currency`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => 'currency:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_CURRENCY_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_CURRENCY_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_CURRENCY_CREATE")',
            input: CreateCurrencyDto::class,
            processor: CreateCurrencyProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_CURRENCY_UPDATE")',
            input: UpdateCurrencyDto::class,
            processor: UpdateCurrencyProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'code' => 'exact',
    'symbol' => 'exact',
    'active' => 'exact',
    'platformId' => 'exact',
    'isDefault' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
class Currency implements RessourceInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "CY";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'CY_ID', length: 16)]
    #[Groups(['currency:get', 'platform:get'])]
    private ?string $id = null;

    #[ORM\Column(name: 'CY_CODE', length: 3)]
    #[Assert\Currency()]
    #[Assert\NotNull]
    #[Groups(['currency:get', 'platform:get'])]
    private ?string $code = null;

    #[ORM\Column(name: 'CY_LABEL', length: 255, nullable: true)]
    #[Groups(['currency:get', 'platform:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'CY_SYMBOL', length: 6)]
    #[Groups(['currency:get', 'platform:get'])]
    private ?string $symbol = null;

    #[ORM\Column(name: 'CY_ACTIVE')]
    #[Groups(['currency:get', 'platform:get'])]
    private ?bool $active = null;

    #[ORM\Column(name: 'CY_IS_DEFAULT', options: ['default' => false])]
    #[Groups(['currency:get', 'platform:get'])]
    private ?bool $isDefault = false;

    #[ORM\Column(name: 'CY_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['currency:get'])]
    private ?string $platformId = null;

    #[ORM\Column(name: 'CY_CREATED_AT')]
    #[Groups(['currency:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'CY_UPDATED_AT', nullable: true)]
    #[Groups(['currency:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;
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

    /**
     * Get the value of label
     */ 
    public function getLabel(): string|null
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @return  self
     */ 
    public function setLabel(string|null $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of isDefault
     */ 
    public function getIsDefault(): bool|null
    {
        return $this->isDefault;
    }

    /**
     * Set the value of isDefault
     *
     * @return  self
     */ 
    public function setIsDefault(bool|null $isDefault): static
    {
        $this->isDefault = $isDefault;

        return $this;
    }
}
