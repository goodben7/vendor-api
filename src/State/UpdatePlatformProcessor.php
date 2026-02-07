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

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new UpdatePlatformModel(
            $data->name,
            $data->address,
            $data->description,
            $data->currency,
            $data->paymentConfigJson,
            $data->active
        );

        return $this->manager->updateFrom($uriVariables['id'], $model);
    }
}
