<?php

namespace App\Manager;

use App\Entity\Category;
use App\Event\ActivityEvent;
use App\Storage\DataStorage;
use App\Model\NewCategoryModel;
use App\Model\UpdateCategoryModel;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;

class CategoryManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
        private DataStorage $dataStorage,
    ) {
    }

    public function createFrom(NewCategoryModel $model): Category
    {
        $platformId = $this->dataStorage->getPlatformId();

        if (null === $platformId) {
            throw new UnavailableDataException('Platform not found');
        }

        $category = new Category();
        if ($model->menu !== null) {
            $category->setMenu($model->menu);
        }
        $category->setLabel($model->label);
        $category->setDescription($model->description);
        $category->setPosition($model->position ?? 0);
        $category->setActive($model->active ?? true);
        $category->setCreatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($category);
        $this->em->flush();

        $this->eventDispatcher->dispatch($category, ActivityEvent::ACTION_CREATE);

        return $category;
    }

    public function updateFrom(string $categoryId, UpdateCategoryModel $model): Category
    {
        $category = $this->findCategory($categoryId);

        if ($model->menu !== null) {
            $category->setMenu($model->menu);
        }
        $category->setLabel($model->label);
        if ($model->description !== null) {
            $category->setDescription($model->description);
        }
        if ($model->position !== null) {
            $category->setPosition($model->position);
        }
        if ($model->active !== null) {
            $category->setActive($model->active);
        }
        $category->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($category, ActivityEvent::ACTION_EDIT);

        return $category;
    }

    private function findCategory(string $categoryId): Category
    {
        $category = $this->em->find(Category::class, $categoryId);
        if (null === $category) {
            throw new UnavailableDataException(sprintf('cannot find category with id: %s', $categoryId));
        }
        return $category;
    }

    public function delete(string $categoryId): void
    {
        $category = $this->findCategory($categoryId);

        if ($category->getDeleted()) {
            throw new \InvalidArgumentException('this action is not allowed');
        }

        $category->setDeleted(true);
        $category->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($category, ActivityEvent::ACTION_DELETE);
    }
}
