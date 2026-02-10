<?php

declare(strict_types=1);

use App\Entity\Menu;
use App\Entity\User;
use App\Entity\Tablet;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemOption;

use App\Entity\Product;
use App\Entity\Profile;
use App\Entity\Category;
use App\Entity\Platform;
use App\Model\Ressource;
use App\Entity\OptionItem;
use App\Entity\OptionGroup;
use App\Entity\PlatformTable;

return static function (): iterable {

    yield Ressource::new("user", User::class, "US", true);
    yield Ressource::new("profile", Profile::class, "PR", true);
    yield Ressource::new("platform", Platform::class, "PL", true);
    yield Ressource::new("category", Category::class, "CT", true);
    yield Ressource::new("menu", Menu::class, "MN", true);
    yield Ressource::new("product", Product::class, "PD", true);
    yield Ressource::new("option_item", OptionItem::class, "OI", true);
    yield Ressource::new("option_group", OptionGroup::class, "OG", true);
    yield Ressource::new("platform_table", PlatformTable::class, "PT", true);
    yield Ressource::new("tablet", Tablet::class, "TB", true);
    yield Ressource::new("order", Order::class, "OR", true);
    yield Ressource::new("order_item", OrderItem::class, "OE", true);
    yield Ressource::new("order_item_option", OrderItemOption::class, "OO", true);
};
