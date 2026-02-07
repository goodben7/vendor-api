<?php

declare(strict_types=1);

use App\Entity\Menu;
use App\Entity\User;
use App\Entity\Profile;

use App\Entity\Category;
use App\Entity\Platform;
use App\Model\Ressource;

return static function (): iterable {

    yield Ressource::new("user", User::class, "US", true);
    yield Ressource::new("profile", Profile::class, "PR", true);
    yield Ressource::new("platform", Platform::class, "PL", true);
    yield Ressource::new("category", Category::class, "CT", true);
    yield Ressource::new("menu", Menu::class, "MN", true);

};
