<?php

namespace App\State;

use App\Manager\CategoryManager;
use ApiPlatform\State\ProcessorInterface;

class DeleteCategoryProcessor implements ProcessorInterface
{
    public function __construct(private CategoryManager $manager)
    {   
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id']);
    }
}