<?php

namespace App\Manager;

use App\Entity\Product;
use App\Entity\OptionGroup;
use App\Event\ActivityEvent;
use App\Storage\DataStorage;
use App\Model\NewProductModel;
use App\Model\UpdateProductModel;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;

class ProductManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
        private DataStorage $dataStorage,
    ) {
    }

    public function createFrom(NewProductModel $model): Product
    {
        $platformId = $this->dataStorage->getPlatformId();

        if (null === $platformId) {
            throw new UnavailableDataException('Platform not found');
        }

        $product = new Product();
        if ($model->category !== null) {
            $product->setCategory($model->category);
        }
        $product->setLabel($model->label);
        $product->setDescription($model->description);
        $product->setBasePrice($model->basePrice ?? '0.00');
        $product->setIsAvailable($model->isAvailable ?? true);
        $product->setCreatedAt(new \DateTimeImmutable('now'));

        foreach ($model->optionGroups as $optionGroup) {
            $product->addOptionGroup($optionGroup);
            $optionGroup->setProduct($product);
        }

        $this->em->persist($product);
        $this->em->flush();

        $this->eventDispatcher->dispatch($product, ActivityEvent::ACTION_CREATE);

        return $product;
    }

    public function updateFrom(string $productId, UpdateProductModel $model): Product
    {
        $product = $this->findProduct($productId);

        if ($model->category !== null) {
            $product->setCategory($model->category);
        }
        $product->setLabel($model->label);
        $product->setDescription($model->description);
        if ($model->basePrice !== null) {
            $product->setBasePrice($model->basePrice);
        }
        if ($model->isAvailable !== null) {
            $product->setIsAvailable($model->isAvailable);
        }
        $product->setUpdatedAt(new \DateTimeImmutable('now'));

        // Synchronize option groups with model (add missing, keep existing, remove absent)
        // Build current set indexed by id
        $currentGroups = $product->getOptionGroups();
        $currentById = [];
        foreach ($currentGroups as $grp) {
            if ($grp instanceof OptionGroup && null !== $grp->getId()) {
                $currentById[$grp->getId()] = $grp;
            }
        }
        // Build desired id set
        $desiredById = [];
        foreach ($model->optionGroups as $desired) {
            if ($desired instanceof OptionGroup && null !== $desired->getId()) {
                $desiredById[$desired->getId()] = true;
            }
        }
        // Remove groups not present anymore
        /** @var OptionGroup $grp */
        foreach ($currentGroups as $grp) {
            $gid = $grp->getId();
            if ($gid !== null && !isset($desiredById[$gid])) {
                $currentGroups->removeElement($grp);
                $this->em->remove($grp);
            }
        }
        // Add groups that are not already linked
        foreach ($model->optionGroups as $desired) {
            if ($desired instanceof OptionGroup) {
                $gid = $desired->getId();
                if ($gid === null || !isset($currentById[$gid])) {
                    $product->addOptionGroup($desired);
                    $desired->setProduct($product);
                }
            }
        }

        $this->em->flush();

        $this->eventDispatcher->dispatch($product, ActivityEvent::ACTION_EDIT);

        return $product;
    }

    private function findProduct(string $productId): Product
    {
        $product = $this->em->find(Product::class, $productId);
        if (null === $product) {
            throw new UnavailableDataException(sprintf('cannot find product with id: %s', $productId));
        }
        return $product;
    }

    public function delete(string $productId): void
    {
        $product = $this->findProduct($productId);

        if ($product->getDeleted()) {
            throw new \InvalidArgumentException('this action is not allowed');
        }

        $product->setDeleted(true);
        $product->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($product, ActivityEvent::ACTION_DELETE);
    }
}
