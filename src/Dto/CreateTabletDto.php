<?php

namespace App\Dto;

use App\Entity\PlatformTable;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTabletDto
{
    public function __construct(
        #[Assert\NotBlank]
        public ?PlatformTable $platformTable = null,

        #[Assert\Length(max: 255)]
        public ?string $label = null,

        #[Assert\Length(max: 255)]
        public ?string $deviceId = null,

        public ?\DateTimeImmutable $lastHeartbeat = null,

        public ?bool $active = null,
    ) {
    }
}
