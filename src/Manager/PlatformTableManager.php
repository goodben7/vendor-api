<?php

namespace App\Manager;

use App\Entity\PlatformTable;
use App\Model\NewPlatformTableModel;
use App\Model\UpdatePlatformTableModel;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Event\ActivityEvent;
use App\Service\ActivityEventDispatcher;

class PlatformTableManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
    ) {
    }

    public function createFrom(NewPlatformTableModel $model): PlatformTable
    {
        $table = new PlatformTable();
        $table->setLabel($model->label);
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
}
