<?php

namespace App\Entity;

use App\Entity\OptionGroup;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use App\Dto\CreateProductDto;
use App\Dto\UpdateProductDto;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Model\RessourceInterface;
use App\Model\AttachmentInterface;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use App\State\CreateProductProcessor;
use App\State\DeleteProductProcessor;
use App\State\UpdateProductProcessor;
use ApiPlatform\Metadata\GetCollection;
use App\Contract\PlatformCentricInterface;
use Doctrine\Common\Collections\Collection;
use App\Contract\PlatformRestrictiveInterface;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: '`product`')]
#[ApiResource(
    normalizationContext: ['groups' => 'product:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_PRODUCT_DETAILS")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PRODUCT_LIST")'
        ),
        new Post(
            security: 'is_granted("ROLE_PRODUCT_CREATE")',
            input: CreateProductDto::class,
            processor: CreateProductProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_PRODUCT_UPDATE")',
            input: UpdateProductDto::class,
            processor: UpdateProductProcessor::class,
        ),
        new Post(
            uriTemplate: "products/{id}/logo",
            denormalizationContext: ['groups' => 'product:logo'],
            security: 'is_granted("ROLE_PRODUCT_UPDATE")',
            inputFormats: ['multipart' => ['multipart/form-data']],
            processor: PersistProcessor::class,
            status: 200
        ),
        new Delete(
            security:"is_granted('ROLE_PRODUCT_DELETE')",
            processor: DeleteProductProcessor::class
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'category' => 'exact',
    'isAvailable' => 'exact',
    'platformId' => 'exact',
    'deleted' => 'exact',
])]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'basePrice'])]
class Product implements RessourceInterface, AttachmentInterface, PlatformRestrictiveInterface, PlatformCentricInterface
{
    public const string ID_PREFIX = "PD";

    public const string EVENT_DELETED = 'product.deleted';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'PD_ID', length: 16)]
    #[Groups(['product:get', 'option_group:get', 'order:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'PD_CATEGORY', referencedColumnName: 'CT_ID', nullable: true)]
    #[Groups(['product:get'])]
    private ?Category $category = null;

    #[ORM\Column(name: 'PD_LABEL', length: 120)]
    #[Groups(['product:get', 'option_group:get', 'order:get'])]  
    private ?string $label = null;

    #[ORM\Column(name: 'PD_DESCRIPTION', length: 255, nullable: true)]
    #[Groups(['product:get'])]
    private ?string $description = null;

    #[ORM\Column(name: 'PD_BASE_PRICE', type: Types::DECIMAL, precision: 17, scale: 2)]
    #[Groups(['product:get', 'option_group:get', 'order:get'])]
    private ?string $basePrice = null;

    #[ORM\Column(name: 'PD_IS_AVAILABLE')]
    #[Groups(['product:get'])]
    private ?bool $isAvailable = null;

    #[Groups(groups: ['product:logo'])]
    #[Vich\UploadableField(mapping: 'media_object', fileNameProperty: 'filePath', size: 'fileSize')]
    private ?File $file = null;

    #[ORM\Column(length: 255, nullable: true,name:'PD_FILE_PATH')]
    #[Groups(groups: ['product:get'])]
    private ?string $filePath = null;

    #[ORM\Column(nullable: true, name:'PD_FILE_SIZE')]
    #[Groups(groups: ['product:get'])]
    private ?int $fileSize = null;

    #[Groups(groups: ['product:get'])]
    private ?string $contentUrl;

    #[ORM\Column(name: 'PD_PLATFORM_ID', length: 16, nullable: true)]
    #[Groups(['product:get'])]
    private ?string $platformId = null;

    #[ORM\Column(name: 'PD_DELETED', options: ['default' => false])]
    #[Groups(['product:get'])]
    private ?bool $deleted = false;

    #[ORM\Column(name: 'PD_CREATED_AT')]
    #[Groups(['product:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'PD_UPDATED_AT', nullable: true)]
    #[Groups(['product:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, OptionGroup>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OptionGroup::class, cascade: ['all'])]
    #[Groups(['product:get', 'order:get'])]
    private Collection $optionGroups;

    public function __construct()
    {
        $this->optionGroups = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): static
    {
        $this->category = $category;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getBasePrice(): ?string
    {
        return $this->basePrice;
    }

    public function setBasePrice(?string $basePrice): static
    {
        $this->basePrice = $basePrice;
        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;
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

    public function getOptionGroups(): Collection
    {
        return $this->optionGroups;
    }

    public function addOptionGroup(OptionGroup $group): static
    {
        if (!$this->optionGroups->contains($group)) {
            $this->optionGroups->add($group);
            $group->setProduct($this);
        }
        return $this;
    }

    public function removeOptionGroup(OptionGroup $group): static
    {
        if ($this->optionGroups->removeElement($group)) {
            if ($group->getProduct() === $this) {
                $group->setProduct(null);
            }
        }
        return $this;
    }

    /**
     * Get the value of isAvailable
     */ 
    public function getIsAvailable(): bool|null
    {
        return $this->isAvailable;
    }

    /**
     * Get the value of filePath
     */ 
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * Set the value of filePath
     *
     * @return  self
     */ 
    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get the value of fileSize
     */ 
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * Set the value of fileSize
     *
     * @return  self
     */ 
    public function setFileSize(?int $fileSize): static
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get the value of contentUrl
     */ 
    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    /**
     * Set the value of contentUrl
     *
     * @return  self
     */ 
    public function setContentUrl(?string $contentUrl): static
    {
        $this->contentUrl = $contentUrl;

        return $this;
    }

    /**
     * Get the value of file
     */ 
    public function getFile(): File|null
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @return  self
     */ 
    public function setFile($file): static
    {
        $this->file = $file;

        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable('now');
        }

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
    public function setPlatformId(string|null $platformId): static
    {
        $this->platformId = $platformId;

        return $this;
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
    public function setDeleted(bool|null $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }
}
