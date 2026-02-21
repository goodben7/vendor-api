<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\PlatformManager;
use App\Model\NewPlatformModel;

class CreatePlatformProcessor implements ProcessorInterface
{
    public function __construct(private PlatformManager $manager)
    {
    }

    /**
     * @param \App\Dto\CreatePlatformDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewPlatformModel(
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

        return $this->manager->createFrom($model);
    }
}
