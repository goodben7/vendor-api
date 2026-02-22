<?php

namespace App\State;

use App\Manager\MenuManager;
use ApiPlatform\State\ProcessorInterface;

class DeleteMenuProcessor implements ProcessorInterface
{
    public function __construct(private MenuManager $manager)
    {   
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id']);
    }
}