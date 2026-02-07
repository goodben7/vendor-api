<?php

namespace App\Entity;

use App\Enum\EntityType;
use App\Dto\CreateUserDto;
use App\Dto\UpdateUserDto;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use App\Dto\ChangePasswordDto;
use App\Dto\SetUserProfileDto;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Model\UserProxyIntertace;
use Doctrine\DBAL\Types\Types;
use App\Manager\PermissionManager;
use App\Repository\UserRepository;
use App\State\CreateUserProcessor;
use App\State\DeleteUserProcessor;
use App\State\SetProfileProcessor;
use App\State\UpdateUserProcessor;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\State\ToggleLockUserProcessor;
use ApiPlatform\Metadata\GetCollection;
use App\State\ChangeUserPasswordProcessor;
use App\Dto\AddUserSideRolesDto;
use App\State\AddUserSideRolesProcessor;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use App\Model\RessourceInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_PHONE', fields: ['phone'])]
#[ORM\UniqueConstraint(name: 'UNIQ_HOLDER', fields: ['holderId'])]
#[ApiResource(
    normalizationContext: ['groups' => 'user:get'], 
    operations:[
        new Get(
            security: 'is_granted("ROLE_USER_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_USER_CREATE")',
            input: CreateUserDto::class,
            processor: CreateUserProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_EDIT")',
            input: UpdateUserDto::class,
            processor: UpdateUserProcessor::class,
        ),
        new Patch(
            uriTemplate: "users/{id}/credentials",
            security: 'is_granted("ROLE_USER_CHANGE_PWD", object)', 
            input: ChangePasswordDto::class,
            processor: ChangeUserPasswordProcessor::class, 
        ),
        new Delete(
            security: 'is_granted("ROLE_USER_DELETE")',
            processor: DeleteUserProcessor::class
        ),
        new Post(
            uriTemplate: "users/{id}/lock_toggle",
            status: 200,
            denormalizationContext: ['groups' => 'user:lock'],
            security: 'is_granted("ROLE_USER_LOCK")',
            processor: ToggleLockUserProcessor::class
        ),
        new Post(
            uriTemplate: "users/{id}/profiles",
            security: 'is_granted("ROLE_USER_SET_PROFILE")',
            normalizationContext: ['groups' => 'user:get'],
            input: SetUserProfileDto::class,
            processor: SetProfileProcessor::class,  
            status: 200,
        ),
        new Post(
            uriTemplate: "users/{id}/side_roles",
            security: 'is_granted("ROLE_USER_ADD_SIDE_ROLES")',
            normalizationContext: ['groups' => 'user:get'],
            input: AddUserSideRolesDto::class,
            processor: AddUserSideRolesProcessor::class,  
            status: 200,
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'email' => 'exact',
    'roles' => 'exact',
    'phone' => 'exact',
    'displayName' => 'ipartial',
    'deleted' => 'exact',
    'profile' => 'exact',
    'locked' => 'exact',
    'isConfirmed' => 'exact',
    'mustChangePassword' => 'exact',
    'holderId' => 'exact',
    "holderType" => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, RessourceInterface
{
    public const string ID_PREFIX = "US";
    
    public const string EVENT_USER_CREATED = "registrated";
    public const string EVENT_USER_UPDATED = "updated";
    public const string EVENT_USER_DELETED = "deleted";
    public const string EVENT_USER_CHANGED_PASSWORD = "changed_password";
    public const string EVENT_USER_LOCKED = "locked";
    public const string EVENT_USER_UNLOCKED = "unlocked";

    public const string EVENT_USER_SET_PROFILE = "set_profile";

    #[ORM\Id]
    #[ORM\GeneratedValue( strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(name: 'US_ID', length: 16)]
    #[Groups(['user:get'])]
    private ?string $id = null;

    #[ORM\Column(name: 'US_EMAIL', length: 180, nullable: true)]
    #[Groups(['user:get'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(name: 'US_ROLES')]
    #[Groups(['user:get'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name: 'US_PASSWORD')]
    private ?string $password = null;

    public ?string $plainPassword;

    #[ORM\Column(name: 'US_PHONE', length: 15, nullable: true)]
    #[Groups(['user:get'])]
    private ?string $phone = null;

    #[ORM\Column(name: 'US_DISPLAY_NAME', length: 120, nullable: true)]
    #[Groups(['user:get'])]
    private ?string $displayName = null;

    #[ORM\Column(name: 'US_DELETED', options: ['default' => false])]
    #[Groups(['user:get'])]
    private ?bool $deleted = false;

    #[ORM\Column(name: 'US_LOCKED', options: ['default' => false])]
    #[Groups(['user:get'])]
    private ?bool $locked = false;

    #[ORM\Column(name: 'US_IS_CONFIRMED', options: ['default' => false])]
    #[Groups(['user:get'])]
    private ?bool $isConfirmed = false;

    #[ORM\Column(name: 'US_MUST_CHANGE_PASSWORD', options: ['default' => false])]
    #[Groups(['user:get'])]
    private ?bool $mustChangePassword = false;

    #[ORM\Column(name: 'US_PERSON_TYPE', length: 15)]
    #[Groups(['user:get'])]
    private ?string $personType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'US_PROFILE', nullable: true, referencedColumnName: 'PR_ID')]
    #[Groups(['user:get'])]
    private ?Profile $profile = null;

    #[ORM\Column(name: 'US_HOLDER_ID', length: 16, nullable: true)]
    #[Groups(['user:get'])]
    private ?string $holderId = null;

    #[ORM\Column(name: 'US_HOLDER_TYPE', length: 255, nullable: true)]
    #[Assert\Choice(callback: [EntityType::class, 'getAll'], message: 'Invalid holder type.')]
    #[Groups(['user:get'])]
    private ?string $holderType = null;

    #[ORM\Column(name: 'US_SIDE_ROLES', type: Types::SIMPLE_ARRAY, nullable: true)]
    #[Groups(['user:get'])]
    private array $sideRoles = [];

    #[ORM\Column(name: 'US_CREATED_AT')]
    #[Groups(['user:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'US_UPDATED_AT', nullable: true)]
    #[Groups(['user:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) ($this->email ?: $this->phone);
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        $roles[] = $this->getPersonRole();

        if (UserProxyIntertace::PERSON_SUPER_ADMIN === $this->personType) {
            $roles = array_merge($roles, array_values((array)PermissionManager::getInstance()->getPermissionsAsListChoices()));
        } elseif (null !== $this->profile) {
            $roles = array_merge($roles, $this->profile->getPermission());  
        }

        if ($this->sideRoles !== null && !empty($this->sideRoles))
            $roles = [...$roles, ...$this->sideRoles];
        
        return array_values(array_unique($roles));
    
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
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

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): static
    {
        $this->locked = $locked;

        return $this;
    }

    #[Groups(['user:get'])]
    public function isConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): static
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    public function isMustChangePassword(): ?bool
    {
        return $this->mustChangePassword;
    }

    public function setMustChangePassword(bool $mustChangePassword): static
    {
        $this->mustChangePassword = $mustChangePassword;

        return $this;
    }

    public function getPersonType(): ?string
    {
        return $this->personType;
    }

    public function setPersonType(string $personType): static
    {
        $this->personType = $personType;

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
     * Get the value of plainPassword
     */ 
    public function getPlainPassword(): string|null
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @return  self
     */ 
    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get the value of profile
     */ 
    public function getProfile(): Profile|null
    {
        return $this->profile;
    }

    /**
     * Set the value of profile
     *
     * @return  self
     */ 
    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    private function getPersonRole(): string
    {
        return array_search($this->personType, [
            "ROLE_SUPER_ADMIN" => UserProxyIntertace::PERSON_SUPER_ADMIN,
            "ROLE_ADMIN" => UserProxyIntertace::PERSON_ADMIN,
            "ROLE_MANAGER" => UserProxyIntertace::PERSON_MANAGER,
            "ROLE_STAFF" => UserProxyIntertace::PERSON_STAFF,
            "ROLE_KITCHEN" => UserProxyIntertace::PERSON_KITCHEN,
        ]);
    }

    public static function getAcceptedPersonList(): array
    {
        return [
            UserProxyIntertace::PERSON_SUPER_ADMIN,
            UserProxyIntertace::PERSON_ADMIN,
            UserProxyIntertace::PERSON_MANAGER,
            UserProxyIntertace::PERSON_STAFF,
            UserProxyIntertace::PERSON_KITCHEN,
        ];
    }

    public static function getPersonTypesAsChoices(): array
    {
        return [
            "Super Administrateur" => UserProxyIntertace::PERSON_SUPER_ADMIN,
            "Administrateur" => UserProxyIntertace::PERSON_ADMIN,
            "Manager" => UserProxyIntertace::PERSON_MANAGER,
            "Personnel" => UserProxyIntertace::PERSON_STAFF,
            "Cuisine" => UserProxyIntertace::PERSON_KITCHEN,
        ];
    }

    public static function getPersonTypesAsList(): array
    {
        return array_values(self::getPersonTypesAsChoices());
    }

    public function __toString(): string
    {
        return $this->getDisplayName() ?? sprintf("User %s", $this->id);
    }

    /**
     * Get the value of holderId
     */ 
    public function getHolderId(): string|null
    {
        return $this->holderId;
    }

    /**
     * Set the value of holderId
     *
     * @return  self
     */ 
    public function setHolderId(?string $holderId): static
    {
        $this->holderId = $holderId;

        return $this;
    }

    /**
     * Get the value of holderType
     */ 
    public function getHolderType(): string|null
    {
        return $this->holderType;
    }

    /**
     * Set the value of holderType
     *
     * @return  self
     */ 
    public function setHolderType(?string $holderType): static
    {
        $this->holderType = $holderType;

        return $this;
    }

    public function getSideRoles(): array
    {
        return $this->sideRoles;
    }

    public function addSideRole(?string $sideRole): self
    {
        $sideRoles = $this->sideRoles;
        if (!\in_array($sideRole, $sideRoles)) {
            $sideRoles[] = $sideRole;
            $this->setSideRoles($sideRoles);
        }

        return $this;
    }

    public function removeSideRole(?string $sideRole): self
    {
        $sideRoles = $this->sideRoles;
        if (\in_array($sideRole, $sideRoles)) {
            unset($sideRoles[$sideRole]);
            $this->setSideRoles($sideRoles);
        }

        return $this;
    }

    public function setSideRoles(?array $sideRoles): self
    {
        $this->sideRoles = $sideRoles;

        return $this;
    }
}
