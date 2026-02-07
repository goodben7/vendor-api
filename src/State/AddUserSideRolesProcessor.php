<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\UserManager;

class AddUserSideRolesProcessor implements ProcessorInterface
{
    public function __construct(private UserManager $manager)
    {
    }

    /**
     * @param \App\Dto\AddUserSideRolesDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->addSideRoles($uriVariables['id'], $data->sideRoles);
    }
}

