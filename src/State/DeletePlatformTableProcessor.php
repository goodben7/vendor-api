<?php

namespace App\State;

use App\Manager\PlatformTableManager;
use ApiPlatform\State\ProcessorInterface;

class DeletePlatformTableProcessor implements ProcessorInterface
{
    public function __construct(private PlatformTableManager $manager)
    {   
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id']);
    }
}