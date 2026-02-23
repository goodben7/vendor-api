<?php

namespace App\Model;

interface RemoveInterface {
    public function getDeleted(): ?bool;
    public function setDeleted(?bool $deleted): ?static;
}