<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use App\Entity\OptionGroup;
use App\Dto\CreateProductDto;
use App\Dto\UpdateProductDto;
use App\State\CreateProductProcessor;
use App\State\UpdateProductProcessor;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Model\RessourceInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'category' => 'exact',
    'isAvailable' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'basePrice'])]
class Product implements RessourceInterface
{
    public const string ID_PREFIX = "PD";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'PD_ID', length: 16)]
    #[Groups(['product:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'PD_CATEGORY', referencedColumnName: 'CT_ID', nullable: true)]
    #[Groups(['product:get'])]
    private ?Category $category = null;

    #[ORM\Column(name: 'PD_LABEL', length: 120)]
    #[Groups(['product:get'])]  
    private ?string $label = null;

    #[ORM\Column(name: 'PD_DESCRIPTION', length: 255, nullable: true)]
    #[Groups(['product:get'])]
    private ?string $description = null;

    #[ORM\Column(name: 'PD_BASE_PRICE', type: Types::DECIMAL, precision: 17, scale: 2)]
    #[Groups(['product:get'])]
    private ?string $basePrice = null;

    #[ORM\Column(name: 'PD_IS_AVAILABLE')]
    #[Groups(['product:get'])]
    private ?bool $isAvailable = null;

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
    #[Groups(['product:get'])]
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
}
