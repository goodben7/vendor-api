<?php

namespace App\State;

use App\Manager\OrderManager;
use App\Dto\MarkOrderAsServedDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class MarkOrderAsServedProcessor implements ProcessorInterface
{
    public function __construct(private OrderManager $manager)
    {
    }
 
    /** @param MarkOrderAsServedDto $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        return $this->manager->markOrderAsServed($data->order);
    }
}
