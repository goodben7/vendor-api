<?php

namespace App\Dto;

use App\Entity\Tablet;
use App\Entity\PlatformTable;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateTabletDto
{
    public function __construct(
        public ?PlatformTable $platformTable = null,

        #[Assert\Length(max: 255)]
        public ?string $label = null,

        #[Assert\Length(max: 255)]
        public ?string $deviceId = null,

        #[Assert\Length(max: 255)]
        public ?string $deviceModel = null,

        #[Assert\Length(max: 60)]
        #[Assert\Choice(callback: [Tablet::class, 'getModeAsChoices'])]
        public ?string $mode = null,

        public ?\DateTimeImmutable $lastHeartbeat = null,

        public ?bool $active = null,
    ) {
    }
}
