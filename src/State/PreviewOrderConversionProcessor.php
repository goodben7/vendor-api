<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\PreviewOrderConversionDto;
use App\Dto\PreviewOrderConversionView;
use App\Manager\OrderManager;

class PreviewOrderConversionProcessor implements ProcessorInterface
{
    public function __construct(private OrderManager $manager)
    {
    }

    /** @param PreviewOrderConversionDto $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $result = $this->manager->previewConversion($data->order, $data->paidCurrency);
        return new PreviewOrderConversionView(
            $result['orderId'],
            $result['baseCurrency'],
            $result['targetCurrency'],
            $result['baseAmount'],
            $result['targetAmount'],
            $result['rate'],
        );
    }
}
