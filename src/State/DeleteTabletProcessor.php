<?php

namespace App\State;

use App\Manager\TabletManager;
use ApiPlatform\State\ProcessorInterface;

class DeleteTabletProcessor implements ProcessorInterface
{
    public function __construct(private TabletManager $manager)
    {   
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id']);
    }
}