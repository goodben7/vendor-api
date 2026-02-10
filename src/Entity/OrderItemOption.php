<?php

namespace App\Entity;

use App\Entity\OrderItem;
use App\Entity\OptionItem;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Model\NewOrderItemOptionModel;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OrderItemOptionRepository;
use App\State\CreateOrderItemOptionProcessor;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderItemOptionRepository::class)]
#[ORM\Table(name: '`order_item_option`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['order_item_option:get']],
    operations: [
        new Get(
            security: 'is_granted("ROLE_ORDER_ITEM_OPTION_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_ORDER_ITEM_OPTION_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_ORDER_ITEM_OPTION_CREATE")',
            input: NewOrderItemOptionModel::class,
            processor: CreateOrderItemOptionProcessor::class,
        ),
    ]
)]
class OrderItemOption implements RessourceInterface
{
    public const string ID_PREFIX = "OO";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'OO_ID', length: 16)]
    #[Groups(['order_item_option:get', 'order:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: OrderItem::class, inversedBy: 'orderItemOptions')]
    #[ORM\JoinColumn(name: 'OO_ORDER_ITEM', referencedColumnName: 'OI_ID', nullable: false)]
    #[Groups(['order_item_option:get'])]
    private ?OrderItem $orderItem = null;

    #[ORM\ManyToOne(targetEntity: OptionItem::class)]
    #[ORM\JoinColumn(name: 'OO_OPTION_ITEM', referencedColumnName: 'OI_ID', nullable: false)]
    #[Groups(['order_item_option:get'])]                
    private ?OptionItem $optionItem = null;

    #[ORM\Column(name: 'OO_PRICE_SNAPSHOT', type: Types::DECIMAL, precision: 17, scale: 2)]
    #[Groups(['order_item_option:get', 'order:get'])]
    private ?string $priceSnapshot = null;

    #[ORM\Column(name: 'OO_CREATED_AT')]
    #[Groups(['order_item_option:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'OO_UPDATED_AT', nullable: true)]
    #[Groups(['order_item_option:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOrderItem(): ?OrderItem
    {
        return $this->orderItem;
    }

    public function setOrderItem(?OrderItem $orderItem): static
    {
        $this->orderItem = $orderItem;
        return $this;
    }

    public function getOptionItem(): ?OptionItem
    {
        return $this->optionItem;
    }

    public function setOptionItem(?OptionItem $optionItem): static
    {
        $this->optionItem = $optionItem;
        return $this;
    }

    public function getPriceSnapshot(): ?string
    {
        return $this->priceSnapshot;
    }

    public function setPriceSnapshot(string $priceSnapshot): static
    {
        $this->priceSnapshot = $priceSnapshot;
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
