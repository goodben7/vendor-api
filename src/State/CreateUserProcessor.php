<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Message\Command\CommandBusInterface;
use App\Message\Command\CreateUserCommand;

class CreateUserProcessor implements ProcessorInterface
{
    public function __construct(private CommandBusInterface $commands)
    {
        
    }

    /**
     * @param \App\Dto\CreateUserDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $command = new CreateUserCommand(
            $data->email,
            $data->plainPassword,
            $data->profile,
            $data->phone,
            $data->displayName,
            $data->holderId,
            $data->holderType,
        );

        return $this->commands->dispatch($command); 
    }
}
