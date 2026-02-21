<?php

namespace App\State;

use App\Manager\UserManager;
use App\Model\NewAdminAccessModel;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class CreateAdminAccessProcessor implements ProcessorInterface
{
    public function __construct(private UserManager $manager)
    {
        
    }

    /**
     * @param \App\Dto\CreateAdminAccessDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewAdminAccessModel(
            $data->email,
            $data->plainPassword,
            $data->profile,
            $data->phone,
            $data->displayName,
            $data->platformId,
            $data->holderId,
            $data->holderType,
        );

        return $this->manager->createAdminAccess($model);
    }
}
