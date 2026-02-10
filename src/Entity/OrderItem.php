<?php

namespace App\Entity;

use App\Entity\Order;
use App\Entity\Product;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use App\Dto\CreateOrderItemDto;
use App\Entity\OrderItemOption;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OrderItemRepository;
use App\State\CreateOrderItemProcessor;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Table(name: '`order_item`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['order_item:get']],
    operations: [
        new Get(
            security: 'is_granted("ROLE_ORDER_ITEM_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_ORDER_ITEM_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_ORDER_ITEM_CREATE")',
            input: CreateOrderItemDto::class,
            processor: CreateOrderItemProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'order' => 'exact',
    'product' => 'exact',
    'itemStatus' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'cookingAt', 'readyAt'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt', 'cookingAt', 'readyAt'])]
class OrderItem implements RessourceInterface
{
    public const string ID_PREFIX = "OE";

    public const string STATUS_PENDING = "P";
    public const string STATUS_COOKING = "C";
    public const string STATUS_READY = "R";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'OI_ID', length: 16)]            
    #[Groups(['order_item:get', 'order:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(name: 'OI_ORDER', referencedColumnName: 'OR_ID', nullable: false)]
    #[Groups(['order_item:get'])]   
    private ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'OI_PRODUCT', referencedColumnName: 'PD_ID', nullable: false)]
    #[Groups(['order_item:get', 'order:get'])]
    private ?Product $product = null;

    #[ORM\Column(name: 'OI_QUANTITY', type: Types::INTEGER)]
    #[Groups(['order_item:get', 'order:get'])]
    private ?int $quantity = null;

    #[ORM\Column(name: 'OI_UNIT_PRICE_ORDER', type: Types::DECIMAL, precision: 17, scale: 2)]   
    #[Groups(['order_item:get', 'order:get'])]
    private ?string $unitPriceOrder = null;

    #[ORM\Column(name: 'OI_ITEM_STATUS', length: 1)]
    #[Groups(['order_item:get', 'order:get'])]
    private ?string $itemStatus = null;

    #[ORM\Column(name: 'OI_COOKING_AT', nullable: true)]
    #[Groups(['order_item:get'])]
    private ?\DateTimeImmutable $cookingAt = null;

    #[ORM\Column(name: 'OI_READY_AT', nullable: true)]
    #[Groups(['order_item:get'])]
    private ?\DateTimeImmutable $readyAt = null;

    #[ORM\Column(name: 'OI_CREATED_AT')]
    #[Groups(['order_item:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'OI_UPDATED_AT', nullable: true)]
    #[Groups(['order_item:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'orderItem', targetEntity: OrderItemOption::class, cascade: ['persist', 'remove'])]
    #[Groups(['order_item:get', 'order:get'])]
    private Collection $orderItemOptions;

    public function __construct()
    {
        $this->orderItemOptions = new ArrayCollection();
    }

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnitPriceOrder(): ?string
    {
        return $this->unitPriceOrder;
    }

    public function setUnitPriceOrder(string $unitPriceOrder): static
    {
        $this->unitPriceOrder = $unitPriceOrder;
        return $this;
    }

    public function getItemStatus(): ?string
    {
        return $this->itemStatus;
    }

    public function setItemStatus(string $itemStatus): static
    {
        $this->itemStatus = $itemStatus;

        return $this;
    }

    public function getCookingAt(): ?\DateTimeImmutable
    {
        return $this->cookingAt;
    }

    public function setCookingAt(?\DateTimeImmutable $cookingAt): self
    {
        $this->cookingAt = $cookingAt;

        return $this;
    }

    public function getReadyAt(): ?\DateTimeImmutable
    {
        return $this->readyAt;
    }

    public function setReadyAt(?\DateTimeImmutable $readyAt): self
    {
        $this->readyAt = $readyAt;

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
     * @return Collection<int, OrderItemOption>
     */
    public function getOrderItemOptions(): Collection
    {
        return $this->orderItemOptions;
    }

    public function addOrderItemOption(OrderItemOption $orderItemOption): static
    {
        if (!$this->orderItemOptions->contains($orderItemOption)) {
            $this->orderItemOptions->add($orderItemOption);
            $orderItemOption->setOrderItem($this);
        }

        return $this;
    }

    public function removeOrderItemOption(OrderItemOption $orderItemOption): static
    {
        if ($this->orderItemOptions->removeElement($orderItemOption)) {
            if ($orderItemOption->getOrderItem() === $this) {
                $orderItemOption->setOrderItem(null);
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
}
