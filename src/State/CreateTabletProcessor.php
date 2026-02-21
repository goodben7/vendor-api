<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\TabletManager;
use App\Model\NewTabletModel;

class CreateTabletProcessor implements ProcessorInterface
{
    public function __construct(
        private TabletManager $manager,
    )
    {
    }

    /**
     *@param \App\Dto\CreateTabletDto $data  
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $model = new NewTabletModel(
            $data->platformTable,
            $data->label,
            $data->deviceId,
            $data->deviceModel,
            $data->mode,
            $data->lastHeartbeat,
            $data->active
        );

        return $this->manager->createFrom($model);
    }
}
