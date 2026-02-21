<?php

namespace App\Manager;

use App\Event\ActivityEvent;
use App\Storage\DataStorage;
use App\Entity\PlatformTable;
use App\Model\NewPlatformTableModel;
use App\Model\UpdatePlatformTableModel;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;

class PlatformTableManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
        private DataStorage $dataStorage,
    ) {
    }

    public function createFrom(NewPlatformTableModel $model): PlatformTable
    {
        $platformId = $this->dataStorage->getPlatformId();

        if (null === $platformId) {
            throw new UnavailableDataException('Platform not found');
        }
        
        $table = new PlatformTable();
        $table->setLabel($model->label);
        $table->setCapacity($model->capacity);
        $table->setActive($model->active ?? true);
        $table->setCreatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($table);
        $this->em->flush();

        $this->eventDispatcher->dispatch($table, ActivityEvent::ACTION_CREATE);

        return $table;
    }

    public function updateFrom(string $tableId, UpdatePlatformTableModel $model): PlatformTable
    {
        $table = $this->findTable($tableId);
        $table->setLabel($model->label);
        if ($model->capacity !== null) {
            $table->setCapacity($model->capacity);
        }
        if ($model->active !== null) {
            $table->setActive($model->active);
        }
        $table->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($table, ActivityEvent::ACTION_EDIT);

        return $table;
    }

    private function findTable(string $tableId): PlatformTable
    {
        $table = $this->em->find(PlatformTable::class, $tableId);
        if (null === $table) {
            throw new UnavailableDataException(sprintf('cannot find platform table with id: %s', $tableId));
        }
        return $table;
    }

    public function delete(string $tableId): void
    {
        $table = $this->findTable($tableId);

        if ($table->getDeleted()) {
            throw new \InvalidArgumentException('this action is not allowed');
        }

        $table->setDeleted(true);
        $table->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($table, ActivityEvent::ACTION_DELETE);
    }
}
