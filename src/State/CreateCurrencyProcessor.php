<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\CurrencyManager;
use App\Model\NewCurrencyModel;

class CreateCurrencyProcessor implements ProcessorInterface
{
    public function __construct(private CurrencyManager $manager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewCurrencyModel(
            $data->code,
            $data->label,
            $data->symbol,
            $data->active,
            $data->isDefault
        );

        return $this->manager->createFrom($model);
    }
}
