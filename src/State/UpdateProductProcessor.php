<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\ProductManager;
use App\Model\UpdateProductModel;

class UpdateProductProcessor implements ProcessorInterface
{
    public function __construct(private ProductManager $manager)
    {
    }

    /**
     * @param \App\Dto\UpdateProductDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new UpdateProductModel(
            $data->category,
            $data->label,
            $data->description,
            $data->basePrice,
            $data->isAvailable,
            $data->optionGroups,
        );

        return $this->manager->updateFrom($uriVariables['id'], $model);
    }
}
