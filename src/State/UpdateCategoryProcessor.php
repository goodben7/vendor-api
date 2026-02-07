<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\CategoryManager;
use App\Model\UpdateCategoryModel;

class UpdateCategoryProcessor implements ProcessorInterface
{
    public function __construct(private CategoryManager $manager)
    {
    }

    /**
     * @param \App\Dto\UpdateCategoryDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new UpdateCategoryModel(
            $data->menu,
            $data->label,
            $data->position,
            $data->active
        );

        return $this->manager->updateFrom($uriVariables['id'], $model);
    }
}
