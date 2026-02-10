<?php

namespace App\Enum;

class EntityType
{
    // === ENTITÉS PRINCIPALES ===
    public const string USER = 'USER'; // Utilisateur du système
    public const string PROFILE = 'PROFILE'; // Profil utilisateur
    public const string PLATFORM = 'PLATFORM'; // Plateforme de vente
    public const string CATEGORY = 'CATEGORY'; // Catégorie de produit
    public const string MENU = 'MENU'; // Menu de navigation
    public const string PRODUCT = 'PRODUCT'; // Produit vendu
    public const string OPTION_GROUP = 'OPTION_GROUP'; // Groupe d'options de produit
    public const string OPTION_ITEM = 'OPTION_ITEM'; // Option individuelle de produit
    public const string PLATFORM_TABLE = 'PLATFORM_TABLE';
    public const string TABLET = 'TABLET';
    public const string ORDER = 'ORDER';
    public const string ORDER_ITEM = 'ORDER_ITEM';
    public const string ORDER_ITEM_OPTION = 'ORDER_ITEM_OPTION';
    public const string ACTIVITY = 'ACTIVITY';
    public const string DOCUMENT = 'DOCUMENT';

    public static function getAll(): array
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getConstants();
    }

    public static function getGrouped(): array
    {
        return [
            'entities' => [
                self::USER,
                self::PROFILE,
                self::PLATFORM,
                self::CATEGORY,
                self::MENU,
                self::PRODUCT,
                self::OPTION_GROUP,
                self::OPTION_ITEM,
                self::PLATFORM_TABLE,
                self::TABLET,
                self::ORDER,
                self::ORDER_ITEM,
                self::ORDER_ITEM_OPTION,
                self::ACTIVITY,
                self::DOCUMENT
            ]
        ];
    }
}
