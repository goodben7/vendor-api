<?php

namespace App\State;

use App\Dto\CreatePaymentDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Message\Command\CommandBusInterface;
use App\Message\Command\CreatePaymentCommand;

class CreatePaymentProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus)
    {
    }

    /**
     * @param CreatePaymentDto $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new CreatePaymentCommand(
            $data->order,
            $data->amount,
            $data->method,
            $data->provider,
            $data->transactionRef
        );

        return $this->commandBus->dispatch($command); 
    }
}
