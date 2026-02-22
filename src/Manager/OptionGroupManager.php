<?php

namespace App\Manager;

use App\Entity\OptionGroup;
use App\Entity\OptionItem;
use App\Model\NewOptionGroupModel;
use App\Model\UpdateOptionGroupModel;
use App\Event\ActivityEvent;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Storage\DataStorage;

class OptionGroupManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
        private DataStorage $dataStorage
    ) {
    }

    public function createFrom(NewOptionGroupModel $model): OptionGroup
    {
        $platformId = $this->dataStorage->getPlatformId();

        if (null === $platformId) {
            throw new UnavailableDataException('Platform not found');
        }

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

        // Synchronize option items with model (add missing, keep existing, remove absent)
        $currentItems = $group->getOptionItems();
        $currentById = [];
        foreach ($currentItems as $item) {
            if ($item instanceof OptionItem && null !== $item->getId()) {
                $currentById[$item->getId()] = $item;
            }
        }
        $desiredById = [];
        foreach ($model->optionItems as $desired) {
            if ($desired instanceof OptionItem && null !== $desired->getId()) {
                $desiredById[$desired->getId()] = true;
            }
        }
        // Remove items not present anymore
        /** @var OptionItem $item */
        foreach ($currentItems as $item) {
            $iid = $item->getId();
            if ($iid !== null && !isset($desiredById[$iid])) {
                $currentItems->removeElement($item);
                $this->em->remove($item);
            }
        }
        // Add items that are not already linked (including new ones with null id)
        foreach ($model->optionItems as $desired) {
            if ($desired instanceof OptionItem) {
                $iid = $desired->getId();
                if ($iid === null || !isset($currentById[$iid])) {
                    $group->addOptionItem($desired);
                    $desired->setOptionGroup($group);
                }
            }
        }

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
