<?php

namespace App\Entity;

use App\Dto\CreateTabletDto;
use App\Dto\UpdateTabletDto;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\TabletRepository;
use App\State\CreateTabletProcessor;
use App\State\DeleteTabletProcessor;
use App\State\UpdateTabletProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Contract\PlatformCentricInterface;
use App\Contract\PlatformRestrictiveInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TabletRepository::class)]
#[ORM\Table(name: '`tablet`')]
#[ApiResource(
    normalizationContext: ['groups' => 'tablet:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_TABLET_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_TABLET_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_TABLET_CREATE")',
            input: CreateTabletDto::class,
            processor: CreateTabletProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_TABLET_UPDATE")',
            input: UpdateTabletDto::class,
            processor: UpdateTabletProcessor::class,
        ),
        new Delete(
            security:"is_granted('ROLE_TABLET_DELETE')",
            processor: DeleteTabletProcessor::class
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'deviceId' => 'ipartial',
    'platformTable' => 'exact',
    'active' => 'exact',
    'platformId' => 'exact',
    'status' => 'exact',
    'deviceModel' => 'ipartial',
    'mode' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'lastHeartbeat'])]
class Tablet implements RessourceInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "TB";

    public const string MODE_WAITER = 'waiter';
    public const string MODE_SELF_ORDER = 'self_order';
    public const string MODE_KITCHEN = 'kitchen';
    public const string MODE_CASHIER = 'cashier';

    public const string STATUS_ONLINE = 'online';
    public const string STATUS_OFFLINE = 'offline';
    public const string STATUS_MAINTENANCE = 'maintenance';
    public const string STATUS_BLOCKED = 'blocked';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'TB_ID', length: 16)]
    #[Groups(['tablet:get', 'order:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(name: 'TB_TABLE', referencedColumnName: 'PT_ID', nullable: true)]
    #[Groups(['tablet:get'])]
    private ?PlatformTable $platformTable = null;

    #[ORM\Column(name: 'TB_LABEL', length: 255, nullable: true)]
    #[Groups(['tablet:get', 'order:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'TB_DEVICE_ID', length: 255, nullable: true)]
    #[Groups(['tablet:get', 'order:get'])]
    private ?string $deviceId = null;

    #[ORM\Column(name: 'TB_LAST_HEARTBEAT', nullable: true)]
    #[Groups(['tablet:get'])]
    private ?\DateTimeImmutable $lastHeartbeat = null;

    #[ORM\Column(name: 'TB_STATUS', length: 60, nullable: true, options: ['default' => self::STATUS_ONLINE])]
    #[Groups(['tablet:get'])]
    private ?string $status = self::STATUS_ONLINE;

    #[ORM\Column(name: 'TB_DEVICE_MODEL', length: 255, nullable: true)]
    #[Groups(['tablet:get'])]
    private ?string $deviceModel = null;

    #[ORM\Column(name: 'TB_MODE', length: 60, nullable: true)]
    #[Groups(['tablet:get'])]
    private ?string $mode = null;

    #[ORM\Column(name: 'TB_ACTIVE')]
    #[Groups(['tablet:get', 'order:get'])]
    private ?bool $active = null;

    #[ORM\Column(name: 'TP_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['tablet:get'])]
    private ?string $platformId = null;

    #[ORM\Column(name: 'TP_DELETED', options: ['default' => false])]
    #[Groups(['tablet:get'])]
    private ?bool $deleted = false;

    #[ORM\Column(name: 'TB_CREATED_AT')]
    #[Groups(['tablet:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'TB_UPDATED_AT', nullable: true)]
    #[Groups(['tablet:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    public function setDeviceId(?string $deviceId): static
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    public function getLastHeartbeat(): ?\DateTimeImmutable
    {
        return $this->lastHeartbeat;
    }

    public function setLastHeartbeat(?\DateTimeImmutable $lastHeartbeat): static
    {
        $this->lastHeartbeat = $lastHeartbeat;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getDeviceModel(): ?string
    {
        return $this->deviceModel;
    }

    public function setDeviceModel(?string $deviceModel): static
    {
        $this->deviceModel = $deviceModel;
        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode): static
    {
        $this->mode = $mode;
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

    public static function getModeAsChoices(): array
    {
        return [
            "Serveur" => self::MODE_WAITER,
            "Commande" => self::MODE_SELF_ORDER,
            "Cuisinier" => self::MODE_KITCHEN,
            "Caissier" => self::MODE_CASHIER,
        ];
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
