<?php

namespace App\State;

use App\Manager\OrderManager;
use App\Dto\MarkOrderAsReadyDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class MarkOrderAsReadyProcessor implements ProcessorInterface
{
    public function __construct(private OrderManager $manager)
    {
    }

    /** @param MarkOrderAsReadyDto $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        return $this->manager->markOrderAsReady($data->order);
    }
}
