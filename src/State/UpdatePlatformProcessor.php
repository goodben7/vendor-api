<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\PlatformManager;
use App\Model\UpdatePlatformModel;

class UpdatePlatformProcessor implements ProcessorInterface
{
    public function __construct(private PlatformManager $manager)
    {
    }

    /**
     * @param \App\Dto\UpdatePlatformDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new UpdatePlatformModel(
            $data->name,
            $data->address,
            $data->description,
            $data->currency,
            $data->phone,
            $data->email,
            $data->allowTableManagement,
            $data->allowOnlineOrder,
            $data->paymentConfigJson,
            $data->active
        );

        return $this->manager->updateFrom($uriVariables['id'], $model);
    }
}
