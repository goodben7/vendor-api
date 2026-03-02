<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Contract\PlatformCentricInterface;
use App\Contract\PlatformRestrictiveInterface;
use App\Doctrine\IdGenerator;
use App\Dto\CreateExchangeRateDto;
use App\Model\RessourceInterface;
use App\Repository\ExchangeRateRepository;
use App\State\CreateExchangeRateProcessor;
use App\State\DeleteExchangeRateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExchangeRateRepository::class)]
#[ORM\Table(name: 'exchange_rate')]
#[ApiResource(
    normalizationContext: ['groups' => 'exchange_rate:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_EXCHANGE_RATE_READ")',
        ),
        new GetCollection(
            security: 'is_granted("ROLE_EXCHANGE_RATE_READ")',
        ),
        new Post(
            security: 'is_granted("ROLE_EXCHANGE_RATE_CREATE")',
            input: CreateExchangeRateDto::class,
            processor: CreateExchangeRateProcessor::class, 
        ),
        new Patch(
            security: 'is_granted("ROLE_EXCHANGE_RATE_UPDATE")',
            denormalizationContext: ['groups' => 'exchange_rate:patch',],
            processor: PersistProcessor::class,
        ),
        new Delete(
            security:"is_granted('ROLE_EXCHANGE_RATE_DELETE')",
            processor: DeleteExchangeRateProcessor::class 
        ),
    ]
)]
class ExchangeRate implements RessourceInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "EX";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16, name: 'EX_ID')]
    #[Groups(['exchange_rate:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(nullable: false, name: 'EX_BASE_CUR_ID', referencedColumnName: 'CY_ID')]
    #[Groups(['exchange_rate:get'])]
    private Currency $baseCurrency;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(nullable: false, name: 'EX_TARGET_CUR_ID', referencedColumnName: 'CY_ID')]
    #[Groups(['exchange_rate:get'])]
    private Currency $targetCurrency;

    #[ORM\Column(type: 'decimal', precision: 17, scale: 2, name: 'EX_RATE', nullable:true)]
    #[Groups(['exchange_rate:get'])]
    private ?string $rate = null;

    #[ORM\Column(type: 'decimal', precision: 17, scale: 2, name: 'EX_BASE_RATE', nullable: true)]
    #[Groups(['exchange_rate:get'])]
    private ?string $baseRate = null;

    #[ORM\Column(type: 'decimal', precision: 17, scale: 2, name: 'EX_TARGET_RATE', nullable: true)]
    #[Groups(['exchange_rate:get'])]
    private ?string $targetRate = null;

    #[ORM\Column(name: 'EX_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['exchange_rate:get'])]
    private ?string $platformId = null;

    #[ORM\Column(type: 'datetime_immutable', name: 'EX_CREATED_AT')]
    #[Groups(['exchange_rate:get'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'boolean', name: 'EX_ACTIVE')]
    #[Groups(['exchange_rate:get', 'exchange_rate:patch'])]
    private bool $active = true;

    #[ORM\Column(name: 'EX_DELETED', options: ['default' => false])]
    #[Groups(['exchange_rate:get'])]
    private ?bool $deleted = false;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(Currency $currency): static
    {
        $this->baseCurrency = $currency;
        return $this;
    }

    public function getTargetCurrency(): Currency
    {
        return $this->targetCurrency;
    }

    public function setTargetCurrency(Currency $currency): static
    {
        $this->targetCurrency = $currency;
        return $this;
    }

    public function getRate(): ?string
    {
        return $this->getComputedRate();
    }

    public function setRate(?string $rate): static
    {
        $this->rate = $rate;
        return $this;
    }

    public function getBaseRate(): ?string
    {
        return $this->baseRate;
    }

    public function setBaseRate(?string $rate): static
    {
        $this->baseRate = $rate;
        return $this;
    }

    public function getTargetRate(): ?string
    {
        return $this->targetRate;
    }

    public function setTargetRate(?string $rate): static
    {
        $this->targetRate = $rate;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;
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

    public function getComputedRate(): string
    {
        return $this->targetRate / $this->baseRate;
    }

    public function convert(string $baseAmount): string
    {
        $numerator = bcmul($baseAmount, $this->targetRate ?? '0', 6);
        $result = bcdiv($numerator, $this->baseRate ?? '1', 6);
        return bcadd($result, '0', 2);
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
