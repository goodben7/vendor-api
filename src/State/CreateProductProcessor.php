<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\ProductManager;
use App\Model\NewProductModel;

class CreateProductProcessor implements ProcessorInterface
{
    public function __construct(private ProductManager $manager)
    {
    }

    /**
     * @param \App\Dto\CreateProductDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewProductModel(
            $data->category,
            $data->label,
            $data->description,
            $data->basePrice,
            $data->isAvailable,
            $data->optionGroups
        );

        return $this->manager->createFrom($model);
    }
}
