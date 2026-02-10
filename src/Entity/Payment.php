<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use App\Dto\CreatePaymentDto;
use App\Model\RessourceInterface;
use App\Repository\PaymentRepository;
use App\State\CreatePaymentProcessor;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PaymentRepository::class)] 
#[ORM\Table(name: '`payment`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['payment:get']],
    operations: [
        new Get(
            security: 'is_granted("ROLE_PAYMENT_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PAYMENT_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_PAYMENT_CREATE")',
            input: CreatePaymentDto::class,
            processor: CreatePaymentProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'order' => 'exact',
    'method' => 'exact',
    'provider' => 'exact',
    'status' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['paidAt', 'createdAt', 'updatedAt'])]
#[ApiFilter(DateFilter::class, properties: ['paidAt', 'createdAt', 'updatedAt'])]
class Payment implements RessourceInterface
{
    public const string ID_PREFIX = "PA";

    public const string STATUS_PENDING = 'P';
    public const string STATUS_SUCCESS = 'S';
    public const string STATUS_FAILED = 'F';

    public const string EVENT_PAYMENT_CREATED = 'payment.created';

    public const string METHOD_CARD = 'CARD';
    public const string METHOD_CASH = 'CASH';
    public const string METHOD_MOBILE_MONEY = 'MOBILE_MONEY';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'PA_ID', length: 16)]
    #[Groups(['payment:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(name: 'PA_ORDER', referencedColumnName: 'OR_ID', nullable: false)]
    #[Groups(['payment:get'])]
    private ?Order $order = null;

    #[ORM\Column(name: 'PA_AMOUNT', type: Types::DECIMAL, precision: 17, scale: 2)]
    #[Groups(['payment:get'])]
    private ?string $amount = null;

    #[ORM\Column(name: 'PA_METHOD', length: 30)]
    #[Groups(['payment:get'])]
    private ?string $method = null;

    #[ORM\Column(name: 'PA_PROVIDER', length: 255, nullable: true)]
    #[Groups(['payment:get'])]
    private ?string $provider = null;

    #[ORM\Column(name: 'PA_TRANSACTION_REF', length: 255, nullable: true)]
    #[Groups(['payment:get'])]
    private ?string $transactionRef = null;

    #[ORM\Column(name: 'PA_STATUS', length: 1)]
    #[Groups(['payment:get'])]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(name: 'PA_RAW_RESPONSE_JSON', type: Types::JSON, nullable: true)]
    #[Groups(['payment:get'])]
    private ?array $rawResponseJson = null;

    #[ORM\Column(name: 'PA_PAID_AT')]
    #[Groups(['payment:get'])]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column(name: 'PA_CREATED_AT')]
    #[Groups(['payment:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'PA_UPDATED_AT', nullable: true)]
    #[Groups(['payment:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;
        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): static
    {
        $this->provider = $provider;
        return $this;
    }

    public function getTransactionRef(): ?string
    {
        return $this->transactionRef;
    }

    public function setTransactionRef(?string $transactionRef): static
    {
        $this->transactionRef = $transactionRef;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getRawResponseJson(): ?array
    {
        return $this->rawResponseJson;
    }

    public function setRawResponseJson(?array $rawResponseJson): static
    {
        $this->rawResponseJson = $rawResponseJson;
        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeImmutable $paidAt): static
    {
        $this->paidAt = $paidAt;
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

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function getMethodAsChoices(): array
    {
        return [
            "Carte" => self::METHOD_CARD,
            "Espèces" => self::METHOD_CASH,
            "Mobile Money" => self::METHOD_MOBILE_MONEY,
        ];
    }
}
