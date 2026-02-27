<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\CurrencyManager;
use App\Model\UpdateCurrencyModel;

class UpdateCurrencyProcessor implements ProcessorInterface
{
    public function __construct(private CurrencyManager $manager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new UpdateCurrencyModel(
            $data->code,
            $data->label,
            $data->symbol,
            $data->active,
            $data->isDefault
        );

        return $this->manager->updateFrom($uriVariables['id'], $model);
    }
}
