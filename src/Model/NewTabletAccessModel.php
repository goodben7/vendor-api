<?php

namespace App\Model;

use App\Entity\Profile;
use App\Enum\EntityType;
use Symfony\Component\Validator\Constraints as Assert;

class NewTabletAccessModel
{
    public function __construct(
        
        #[Assert\Email]
        public ?string $email = null,

        #[Assert\NotNull]
        #[Assert\NotBlank]
        public ?string $plainPassword = null,

        #[Assert\NotNull]
        #[Assert\NotBlank]
        public ?Profile $profile = null,

        public ?string $phone = null,

        #[Assert\Length(max: 120)]
        public ?string $displayName = null,

        #[Assert\NotNull]
        #[Assert\NotBlank]
        #[Assert\Length(max: 16)]
        public ?string $platformId = null,

        #[Assert\Length(max: 16)]
        public ?string $holderId = null,

        #[Assert\Choice(callback: [EntityType::class, 'getAll'], message: 'Invalid holder type.')]
        public ?string $holderType = null,
    )
    {
    }
}