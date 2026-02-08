<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\PlatformTableManager;
use App\Model\NewPlatformTableModel;

class CreatePlatformTableProcessor implements ProcessorInterface
{
    public function __construct(private PlatformTableManager $manager)
    {
    }

    /**
     * @param \App\Dto\CreatePlatformTableDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewPlatformTableModel(
            $data->label,
            $data->active
        );

        return $this->manager->createFrom($model);
    }
}
