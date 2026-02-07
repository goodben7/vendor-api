<?php

namespace App\Manager;

use App\Entity\OptionGroup;
use App\Model\NewOptionGroupModel;
use App\Model\UpdateOptionGroupModel;
use App\Event\ActivityEvent;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;

class OptionGroupManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
    ) {
    }

    public function createFrom(NewOptionGroupModel $model): OptionGroup
    {
        $group = new OptionGroup();
        if ($model->product !== null) {
            $group->setProduct($model->product);
        }
        $group->setLabel($model->label);
        $group->setIsRequired($model->isRequired ?? false);
        $group->setMaxChoices($model->maxChoices ?? 1);
        $group->setIsAvailable($model->isAvailable ?? true);
        $group->setCreatedAt(new \DateTimeImmutable('now'));

        foreach ($model->optionItems as $optionItem) {
            $group->addOptionItem($optionItem);
            $optionItem->setOptionGroup($group);
        }

        $this->em->persist($group);
        $this->em->flush();

        $this->eventDispatcher->dispatch($group, ActivityEvent::ACTION_CREATE);

        return $group;
    }

    public function updateFrom(string $groupId, UpdateOptionGroupModel $model): OptionGroup
    {
        $group = $this->findGroup($groupId);

        if ($model->product !== null) {
            $group->setProduct($model->product);
        }
        $group->setLabel($model->label);
        if ($model->isRequired !== null) {
            $group->setIsRequired($model->isRequired);
        }
        if ($model->maxChoices !== null) {
            $group->setMaxChoices($model->maxChoices);
        }
        if ($model->isAvailable !== null) {
            $group->setIsAvailable($model->isAvailable);
        }
        $group->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($group, ActivityEvent::ACTION_EDIT);

        return $group;
    }

    private function findGroup(string $groupId): OptionGroup
    {
        $group = $this->em->find(OptionGroup::class, $groupId);
        if (null === $group) {
            throw new UnavailableDataException(sprintf('cannot find option group with id: %s', $groupId));
        }
        return $group;
    }
}
