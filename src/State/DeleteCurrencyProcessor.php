<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Manager\CurrencyManager;

class DeleteCurrencyProcessor implements ProcessorInterface
{
    public function __construct(private CurrencyManager $manager)
    {   
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id']);
    }
}