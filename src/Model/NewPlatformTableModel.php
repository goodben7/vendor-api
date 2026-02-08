<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class NewPlatformTableModel
{
    public function __construct(
        #[Assert\Length(max: 120)]
        public ?string $label = null,
        
        public ?bool $active = null,
    ) {
    }
}
