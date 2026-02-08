<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\TabletRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Model\RessourceInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Dto\CreateTabletDto;
use App\Dto\UpdateTabletDto;
use App\State\CreateTabletProcessor;
use App\State\UpdateTabletProcessor;

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
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'deviceId' => 'ipartial',
    'platformTable' => 'exact',
    'active' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'lastHeartbeat'])]
class Tablet implements RessourceInterface
{
    public const string ID_PREFIX = "TB";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'TB_ID', length: 16)]
    #[Groups(['tablet:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(name: 'TB_TABLE', referencedColumnName: 'PT_ID', nullable: false)]
    #[Groups(['tablet:get'])]
    private ?PlatformTable $platformTable = null;

    #[ORM\Column(name: 'TB_LABEL', length: 255, nullable: true)]
    #[Groups(['tablet:get'])]
    private ?string $label = null;

    #[ORM\Column(name: 'TB_DEVICE_ID', length: 255, nullable: true)]
    #[Groups(['tablet:get'])]
    private ?string $deviceId = null;

    #[ORM\Column(name: 'TB_LAST_HEARTBEAT', nullable: true)]
    #[Groups(['tablet:get'])]
    private ?\DateTimeImmutable $lastHeartbeat = null;

    #[ORM\Column(name: 'TB_ACTIVE')]
    #[Groups(['tablet:get'])]
    private ?bool $active = null;

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
