<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\CategoryManager;
use App\Model\NewCategoryModel;

class CreateCategoryProcessor implements ProcessorInterface
{
    public function __construct(private CategoryManager $manager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewCategoryModel(
            $data->menu,
            $data->label,
            $data->position,
            $data->active
        );

        return $this->manager->createFrom($model);
    }
}
