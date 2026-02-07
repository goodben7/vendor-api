<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\OptionGroupManager;
use App\Model\NewOptionGroupModel;

class CreateOptionGroupProcessor implements ProcessorInterface
{
    public function __construct(private OptionGroupManager $manager)
    {
    }

    /**
     * @param \App\Dto\CreateOptionGroupDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewOptionGroupModel(
            $data->product,
            $data->label,
            $data->isRequired,
            $data->maxChoices,
            $data->isAvailable,
            $data->optionItems,
        );

        return $this->manager->createFrom($model);
    }
}
