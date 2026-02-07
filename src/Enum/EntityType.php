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
            ]
        ];
    }
}
