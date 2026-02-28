<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\ExchangeRateManager;
use App\Model\NewExchangeRateModel;

class CreateExchangeRateProcessor implements ProcessorInterface
{
    public function __construct(private ExchangeRateManager $manager)
    {
    }

    /**
    * @param \App\Dto\CreateExchangeRateDto $data 
    */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewExchangeRateModel(
            $data->baseCurrency,
            $data->targetCurrency,
            $data->baseRate,
            $data->targetRate,
            $data->active,
        );

        return $this->manager->createFrom($model);
    }
}
