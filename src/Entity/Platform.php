<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use App\Dto\CreatePlatformDto;
use App\Dto\UpdatePlatformDto;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\PlatformRepository;
use App\State\CreatePlatformProcessor;
use App\State\UpdatePlatformProcessor;
use ApiPlatform\Metadata\GetCollection;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PlatformRepository::class)]
#[ORM\Table(name: '`platform`')]
#[ApiResource(
    normalizationContext: ['groups' => 'platform:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_PLATFORM_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PLATFORM_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_PLATFORM_CREATE")',
            input: CreatePlatformDto::class,
            processor: CreatePlatformProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_PLATFORM_UPDATE")',
            input: UpdatePlatformDto::class,
            processor: UpdatePlatformProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'name' => 'ipartial',
    'phone' => 'ipartial',
    'email' => 'ipartial',
    'currency' => 'exact',
    'active' => 'exact',
    'allowTableManagement' => 'exact',
    'allowOnlineOrder' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
class Platform implements RessourceInterface, PlatformRestrictiveInterface
{
    public const string ID_PREFIX = "PL";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'PL_ID', length: 16)]
    #[Groups(['platform:get'])]
    private ?string $id = null;

    #[ORM\Column(name: 'PL_NAME', length: 180, nullable: true)]
    #[Groups(['platform:get'])]
    private ?string $name = null;

    #[ORM\Column(name: 'PL_ADDRESS', type: Types::TEXT, nullable: true)]
    #[Groups(['platform:get'])]
    private ?string $address = null;

    #[ORM\Column(name: 'PL_DESCRIPTION', type: Types::TEXT, nullable: true)]
    #[Groups(['platform:get'])]
    private ?string $description = null;

    #[ORM\Column(name: 'PL_CURRENCY', length: 3, nullable: true, options: ['default' => 'CDF'])]
    #[Groups(['platform:get'])]
    private ?string $currency = null;

    #[ORM\Column(name: 'PL_PHONE', length: 30, nullable: true)]
    #[Groups(['platform:get'])]
    private ?string $phone = null;

    #[ORM\Column(name: 'PL_EMAIL', length: 180, nullable: true)]
    #[Groups(['platform:get'])]
    private ?string $email = null;

    #[ORM\Column(name: 'PL_ALLOW_TABLE_MANAGEMENT', options: ['default' => true])]
    #[Groups(['platform:get'])]
    private ?bool $allowTableManagement = true;

    #[ORM\Column(name: 'PL_ALLOW_ONLINE_ORDER', options: ['default' => false])]
    #[Groups(['platform:get'])]
    private ?bool $allowOnlineOrder = false;

    #[ORM\Column(name: 'PL_PAYMENT_CONFIG', type: Types::JSON, nullable: true)]
    #[Groups(['platform:get'])] 
    private ?array $paymentConfigJson = null;

    #[ORM\Column(name: 'PL_ACTIVE')]
    #[Groups(['platform:get'])]
    private ?bool $active = null;

    #[ORM\Column(name: 'PL_CREATED_AT')]
    #[Groups(['platform:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'PL_UPDATED_AT', nullable: true)]
    #[Groups(['platform:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPlatformId(): string|null
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPaymentConfigJson(): ?array
    {
        return $this->paymentConfigJson;
    }

    public function setPaymentConfigJson(?array $config): static
    {
        $this->paymentConfigJson = $config;
        return $this;
    }

    public function isAllowTableManagement(): ?bool
    {
        return $this->allowTableManagement;
    }

    public function setAllowTableManagement(?bool $allowTableManagement): static
    {
        $this->allowTableManagement = $allowTableManagement;
        return $this;
    }

    public function isAllowOnlineOrder(): ?bool
    {
        return $this->allowOnlineOrder;
    }

    public function setAllowOnlineOrder(?bool $allowOnlineOrder): static
    {
        $this->allowOnlineOrder = $allowOnlineOrder;
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
