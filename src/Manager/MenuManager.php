<?php

namespace App\Manager;

use App\Entity\Menu;
use App\Event\ActivityEvent;
use App\Service\ActivityEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;

class MenuManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ActivityEventDispatcher $eventDispatcher,
    ) {
    }

    private function findMenu(string $menuId): Menu
    {
        $menu = $this->em->find(Menu::class, $menuId);
        if (null === $menu) {
            throw new UnavailableDataException(sprintf('cannot find menu with id: %s', $menuId));
        }
        return $menu;
    }

    public function delete(string $menuId): void
    {
        $menu = $this->findMenu($menuId);

        if ($menu->getDeleted()) {
            throw new \InvalidArgumentException('this action is not allowed');
        }

        $menu->setDeleted(true);
        $menu->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        $this->eventDispatcher->dispatch($menu, ActivityEvent::ACTION_DELETE);
    }
}
