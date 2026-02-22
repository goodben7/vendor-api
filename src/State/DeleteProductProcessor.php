<?php

namespace App\State;

use App\Manager\ProductManager;
use ApiPlatform\State\ProcessorInterface;

class DeleteProductProcessor implements ProcessorInterface
{
    public function __construct(private ProductManager $manager)
    {   
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id']);
    }
}