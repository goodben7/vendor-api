<?php

namespace App\Contract;

interface PlatformRestrictiveInterface {
    public function getPlatformId(): ?string;
}