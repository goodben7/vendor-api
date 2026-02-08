<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\TabletManager;
use App\Model\UpdateTabletModel;

class UpdateTabletProcessor implements ProcessorInterface
{
    public function __construct(
        private TabletManager $manager,
    ) {
    }

    /**
     *@param \App\Dto\UpdateTabletDto $data  
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $model = new UpdateTabletModel(
            $data->platformTable,
            $data->label,
            $data->deviceId,
            $data->lastHeartbeat,
            $data->active
        );

        return $this->manager->updateFrom($uriVariables['id'], $model);
    }
}
