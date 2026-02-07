<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class AddUserSideRolesDto
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Count(min: 1)]
        public array $sideRoles = [],
    ) {
    }
}

