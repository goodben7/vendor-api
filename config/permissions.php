<?php

declare(strict_types=1);

use App\Model\Permission;

return static function (): iterable {

    yield Permission::new('ROLE_USER_CREATE', "Créér un utilisateur");
    yield Permission::new('ROLE_USER_LOCK', "Vérouiller/Déverrouiller un utilisateur");
    yield Permission::new('ROLE_USER_CHANGE_PWD', "Modifier mot de passe");
    yield Permission::new('ROLE_USER_DETAILS', "Consulter les détails d'un utilisateur");
    yield Permission::new('ROLE_USER_LIST', "Consulter la liste des utilisateurs");
    yield Permission::new('ROLE_USER_EDIT', "Editer les informations d'un utilisateur");
    yield Permission::new('ROLE_USER_DELETE', "Supprimer un utilisateur");
    yield Permission::new('ROLE_USER_SET_PROFILE', "Modifier le profil utilisateur");
    yield Permission::new('ROLE_USER_ADD_SIDE_ROLES', "Ajouter des droits supplémentaires à un utilisateur");
    yield Permission::new('ROLE_ADMIN_ACCESS_CREATE', "Créer un accès administrateur");



    yield Permission::new('ROLE_PROFILE_CREATE', "Créer un profil utilisateur");
    yield Permission::new('ROLE_PROFILE_LIST', "Consulter la liste des profils utilisateur");
    yield Permission::new('ROLE_PROFILE_UPDATE', "Modifier un profil utilisateur");
    yield Permission::new('ROLE_PROFILE_DETAILS', "Consulter les détails d'un profil utilisateur");

    yield Permission::new('ROLE_CURRENCY_CREATE', "Créer une devise");
    yield Permission::new('ROLE_CURRENCY_LIST', "Consulter la liste des devises");
    yield Permission::new('ROLE_CURRENCY_UPDATE', "Modifier une devise");
    yield Permission::new('ROLE_CURRENCY_DETAILS', "Consulter les détails d'une devise");
    yield Permission::new('ROLE_CURRENCY_DELETE', "Supprimer une devise");



    yield Permission::new('ROLE_ACTIVITY_LIST', "Consulter la liste des activités"); 
    yield Permission::new('ROLE_ACTIVITY_VIEW', "Consulter les détails d'une activité"); 

    yield Permission::new('ROLE_PLATFORM_CREATE', "Créer une plateforme");
    yield Permission::new('ROLE_PLATFORM_LIST', "Consulter la liste des plateformes");
    yield Permission::new('ROLE_PLATFORM_UPDATE', "Modifier une plateforme");
    yield Permission::new('ROLE_PLATFORM_DETAILS', "Consulter les détails d'une plateforme");

    yield Permission::new('ROLE_MENU_CREATE', "Créer un menu");
    yield Permission::new('ROLE_MENU_LIST', "Consulter la liste des menus");
    yield Permission::new('ROLE_MENU_UPDATE', "Modifier un menu");
    yield Permission::new('ROLE_MENU_DETAILS', "Consulter les détails d'un menu");
    yield Permission::new('ROLE_MENU_DELETE', "Supprimer un menu");

    yield Permission::new('ROLE_CATEGORY_CREATE', "Créer une catégorie");
    yield Permission::new('ROLE_CATEGORY_LIST', "Consulter la liste des catégories");
    yield Permission::new('ROLE_CATEGORY_UPDATE', "Modifier une catégorie");
    yield Permission::new('ROLE_CATEGORY_DETAILS', "Consulter les détails d'une catégorie");

    yield Permission::new('ROLE_PRODUCT_CREATE', "Créer un produit");
    yield Permission::new('ROLE_PRODUCT_LIST', "Consulter la liste des produits");
    yield Permission::new('ROLE_PRODUCT_UPDATE', "Modifier un produit");
    yield Permission::new('ROLE_PRODUCT_DETAILS', "Consulter les détails d'un produit");
    yield Permission::new('ROLE_PRODUCT_DELETE', "Supprimer un produit");

    yield Permission::new('ROLE_OPTION_GROUP_CREATE', "Créer un groupe d'options");
    yield Permission::new('ROLE_OPTION_GROUP_LIST', "Consulter la liste des groupes d'options");
    yield Permission::new('ROLE_OPTION_GROUP_UPDATE', "Modifier un groupe d'options");
    yield Permission::new('ROLE_OPTION_GROUP_DETAILS', "Consulter les détails d'un groupe d'options");
    yield Permission::new('ROLE_OPTION_GROUP_DELETE', "Supprimer un groupe d'options");

    yield Permission::new('ROLE_OPTION_ITEM_CREATE', "Créer une option");
    yield Permission::new('ROLE_OPTION_ITEM_LIST', "Consulter la liste des options");
    yield Permission::new('ROLE_OPTION_ITEM_UPDATE', "Modifier une option");
    yield Permission::new('ROLE_OPTION_ITEM_DETAILS', "Consulter les détails d'une option");
    yield Permission::new('ROLE_OPTION_ITEM_DELETE', "Supprimer une option");

    yield Permission::new('ROLE_PLATFORM_TABLE_CREATE', "Créer une table de plateforme");
    yield Permission::new('ROLE_PLATFORM_TABLE_LIST', "Consulter la liste des tables de plateforme");
    yield Permission::new('ROLE_PLATFORM_TABLE_UPDATE', "Modifier une table de plateforme");
    yield Permission::new('ROLE_PLATFORM_TABLE_DETAILS', "Consulter les détails d'une table de plateforme");
    yield Permission::new('ROLE_PLATFORM_TABLE_DELETE', "Supprimer une table de plateforme"); 

    yield Permission::new('ROLE_TABLET_CREATE', "Créer une tablette");
    yield Permission::new('ROLE_TABLET_LIST', "Consulter la liste des tablettes");
    yield Permission::new('ROLE_TABLET_UPDATE', "Modifier une tablette");
    yield Permission::new('ROLE_TABLET_DETAILS', "Consulter les détails d'une tablette");
    yield Permission::new('ROLE_TABLET_DELETE', "Supprimer une tablette");

    yield Permission::new('ROLE_ORDER_DETAILS', "Consulter les détails d'une commande");
    yield Permission::new('ROLE_ORDER_LIST', "Consulter la liste des commandes");
    yield Permission::new('ROLE_ORDER_CREATE', "Créer une commande");
    yield Permission::new('ROLE_ORDER_SENT_TO_KITCHEN', "Marquer une commande comme envoyée en cuisine");
    yield Permission::new('ROLE_ORDER_AS_READY', "Marquer une commande comme prête");
    yield Permission::new('ROLE_ORDER_AS_SERVED', "Marquer une commande comme servie");
    yield Permission::new('ROLE_ORDER_AS_CANCELLED', "Annuler une commande");

    yield Permission::new('ROLE_ORDER_ITEM_DETAILS', "Consulter les détails d'un article de commande");
    yield Permission::new('ROLE_ORDER_ITEM_LIST', "Consulter la liste des articles de commande");
    yield Permission::new('ROLE_ORDER_ITEM_CREATE', "Créer un article de commande");

    yield Permission::new('ROLE_ORDER_ITEM_OPTION_DETAILS', "Consulter les détails d'une option d'article de commande");
    yield Permission::new('ROLE_ORDER_ITEM_OPTION_LIST', "Consulter la liste des options d'article de commande");
    yield Permission::new('ROLE_ORDER_ITEM_OPTION_CREATE', "Créer une option d'article de commande");

    yield Permission::new('ROLE_DOC_CREATE', "Créer un document");
    yield Permission::new('ROLE_DOC_LIST', "Consulter la liste des documents");
    yield Permission::new('ROLE_DOC_DETAILS', "Consulter les détails d'un document");
    yield Permission::new('ROLE_DOC_DELETE', "Supprimer un document");

    yield Permission::new('ROLE_PAYMENT_DETAILS', "Consulter les détails d'un paiement");
    yield Permission::new('ROLE_PAYMENT_LIST', "Consulter la liste des paiements");
    yield Permission::new('ROLE_PAYMENT_CREATE', "Créer un paiement");
    
    yield Permission::new('ROLE_EXCHANGE_RATE_READ', "Consulter les taux de change");
    yield Permission::new('ROLE_EXCHANGE_RATE_CREATE', "Créer un taux de change");
    yield Permission::new('ROLE_EXCHANGE_RATE_UPDATE', "Modifier un taux de change");
    yield Permission::new('ROLE_EXCHANGE_RATE_DELETE', "Supprimer un taux de change");
};
