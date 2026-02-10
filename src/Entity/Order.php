<?php

namespace App\Entity;

use App\Dto\CreateOrderDto;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use App\Dto\MarkOrderAsReadyDto;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use App\Dto\SentToKitchenOrderDto;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\OrderRepository;
use App\State\CreateOrderProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\MarkOrderAsReadyProcessor;
use App\State\SentToKitchenOrderProcessor;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Dto\MarkOrderAsServedDto;
use App\State\MarkOrderAsServedProcessor;
use App\Dto\CancelOrderDto;
use App\State\CancelOrderProcessor;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource(
    normalizationContext: ['groups' => ['order:get']],
    operations: [
        new Get(
            security: 'is_granted("ROLE_ORDER_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_ORDER_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_ORDER_CREATE")',
            input: CreateOrderDto::class,
            processor: CreateOrderProcessor::class,
        ),
        new Post(
            uriTemplate: '/orders/status/sent-to-kitchen',
            input: SentToKitchenOrderDto::class,
            processor: SentToKitchenOrderProcessor::class,
            security: "is_granted('ROLE_ORDER_SENT_TO_KITCHEN')",
            status: 200,
        ),
        new Post(
            uriTemplate: '/orders/status/ready',
            input: MarkOrderAsReadyDto::class,
            processor: MarkOrderAsReadyProcessor::class,
            security: "is_granted('ROLE_ORDER_AS_READY')",
            status: 200,
        ),
        new Post(
            uriTemplate: '/orders/status/served',
            input: MarkOrderAsServedDto::class,
            processor: MarkOrderAsServedProcessor::class,
            security: "is_granted('ROLE_ORDER_AS_SERVED')",
            status: 200,
        ),
        new Post(
            uriTemplate: '/orders/status/cancelled',
            input: CancelOrderDto::class,
            processor: CancelOrderProcessor::class,
            security: "is_granted('ROLE_ORDER_AS_CANCELLED')",
            status: 200,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'platformTable' => 'exact',
    'tablet' => 'exact',
    'referenceUnique' => 'exact',
    'status' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'sentToKitchenAt', 'cancelledAt', 'servedAt', 'readyAt'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt', 'sentToKitchenAt', 'cancelledAt', 'servedAt', 'readyAt'])]
class Order implements RessourceInterface
{
    public const string ID_PREFIX = "OR";

    public const string STATUS_DRAFT = 'D';
    public const string STATUS_SENT_TO_KITCHEN = 'K';
    public const string STATUS_READY = 'R';
    public const string STATUS_SERVED = 'S';
    public const string STATUS_PAID = 'P';
    public const string STATUS_CANCELLED = 'C';


    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'OR_ID', length: 16)]
    #[Groups(['order:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: PlatformTable::class)]
    #[ORM\JoinColumn(name: 'OR_PLATFORM_TABLE', referencedColumnName: 'PT_ID', nullable: false)]
    #[Groups(['order:get'])]
    private ?PlatformTable $platformTable = null;

    #[ORM\ManyToOne(targetEntity: Tablet::class)]
    #[ORM\JoinColumn(name: 'OR_TABLET', referencedColumnName: 'TB_ID', nullable: false)]
    #[Groups(['order:get'])]
    private ?Tablet $tablet = null;

    #[ORM\Column(name: 'OR_REFERENCE_UNIQUE', length: 32, unique: true)]
    #[Groups(['order:get'])]
    private ?string $referenceUnique = null;

    #[ORM\Column(name: 'OR_STATUS', length: 255, options: ['default' => self::STATUS_DRAFT])]
    #[Groups(['order:get'])]
    private ?string $status = self::STATUS_DRAFT;

    #[ORM\Column(name: 'OR_SENT_TO_KITCHEN_AT', nullable: true)]
    #[Groups(['order:get'])]
    private ?\DateTimeImmutable $sentToKitchenAt = null;

    #[ORM\Column(name: 'OR_READY_AT', nullable: true)]
    #[Groups(['order:get'])]
    private ?\DateTimeImmutable $readyAt = null;

    #[ORM\Column(name: 'OR_SERVED_AT', nullable: true)]
    #[Groups(['order:get'])]
    private ?\DateTimeImmutable $servedAt = null;

    #[ORM\Column(name: 'OR_CANCELLED_AT', nullable: true)]
    #[Groups(['order:get'])]
    private ?\DateTimeImmutable $cancelledAt = null;

    #[ORM\Column(name: 'OR_CANCELLATION_REASON', type: 'text', nullable: true)]
    #[Groups(['order:get'])]
    private ?string $cancellationReason = null;

    #[ORM\Column(name: 'OR_TOTAL_AMOUNT', type: Types::DECIMAL, precision: 17, scale: 2)]
    #[Groups(['order:get'])]
    private ?string $totalAmount = null;

    #[ORM\Column(name: 'OR_CREATED_AT')]
    #[Groups(['order:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'OR_UPDATED_AT', nullable: true)]
    #[Groups(['order:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    #[Groups(['order:get'])]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPlatformTable(): ?PlatformTable
    {
        return $this->platformTable;
    }

    public function setPlatformTable(?PlatformTable $platformTable): static
    {
        $this->platformTable = $platformTable;
        return $this;
    }

    public function getTablet(): ?Tablet
    {
        return $this->tablet;
    }

    public function setTablet(?Tablet $tablet): static
    {
        $this->tablet = $tablet;
        return $this;
    }

    public function getReferenceUnique(): ?string
    {
        return $this->referenceUnique;
    }

    public function setReferenceUnique(string $referenceUnique): static
    {
        $this->referenceUnique = $referenceUnique;
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

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
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
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of sentToKitchenAt
     */ 
    public function getSentToKitchenAt(): \DateTimeImmutable|null
    {
        return $this->sentToKitchenAt;
    }

    /**
     * Set the value of sentToKitchenAt
     *
     * @return  self
     */ 
    public function setSentToKitchenAt(?\DateTimeImmutable $sentToKitchenAt): static
    {
        $this->sentToKitchenAt = $sentToKitchenAt;

        return $this;
    }

    /**
     * Get the value of readyAt
     */
    public function getReadyAt(): ?\DateTimeImmutable
    {
        return $this->readyAt;
    }

    /**
     * Set the value of readyAt
     *
     * @return  self
     */
    public function setReadyAt(?\DateTimeImmutable $readyAt): static
    {
        $this->readyAt = $readyAt;

        return $this;
    }

    /**
     * Get the value of servedAt
     */
    public function getServedAt(): ?\DateTimeImmutable
    {
        return $this->servedAt;
    }

    /**
     * Set the value of servedAt
     *
     * @return  self
     */
    public function setServedAt(?\DateTimeImmutable $servedAt): static
    {
        $this->servedAt = $servedAt;

        return $this;
    }

    /**
     * Get the value of cancelledAt
     */
    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelledAt;
    }

    /**
     * Set the value of cancelledAt
     *
     * @return  self
     */
    public function setCancelledAt(?\DateTimeImmutable $cancelledAt): static
    {
        $this->cancelledAt = $cancelledAt;

        return $this;
    }

    /**
     * Get the value of cancellationReason
     */
    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    /**
     * Set the value of cancellationReason
     *
     * @return  self
     */
    public function setCancellationReason(?string $cancellationReason): static
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }
}
