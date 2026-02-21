<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\PlatformTableManager;
use App\Model\UpdatePlatformTableModel;

class UpdatePlatformTableProcessor implements ProcessorInterface
{
    public function __construct(private PlatformTableManager $manager)
    {
    }


    /**
     *@param \App\Dto\UpdatePlatformTableDto $data  
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new UpdatePlatformTableModel(
            $data->label,
            $data->capacity,
            $data->active
        );

        return $this->manager->updateFrom($uriVariables['id'], $model);
    }
}
