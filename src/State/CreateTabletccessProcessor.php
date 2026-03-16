<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\UserManager;
use App\Model\NewTabletAccessModel;

class CreateTabletccessProcessor implements ProcessorInterface
{
    public function __construct(private UserManager $manager)
    {
        
    }

    /**
     * @param \App\Dto\CreateTabletAccessDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewTabletAccessModel(
            $data->email,
            $data->plainPassword,
            $data->profile,
            $data->phone,
            $data->displayName,
            $data->platformId,
            $data->holderId,
            $data->holderType,
        );

        return $this->manager->createTabletAccess($model);
    }
}
