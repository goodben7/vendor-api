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

    yield Permission::new('ROLE_PROFILE_CREATE', "Créer un profil utilisateur");
    yield Permission::new('ROLE_PROFILE_LIST', "Consulter la liste des profils utilisateur");
    yield Permission::new('ROLE_PROFILE_UPDATE', "Modifier un profil utilisateur");
    yield Permission::new('ROLE_PROFILE_DETAILS', "Consulter les détails d'un profil utilisateur");

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

    yield Permission::new('ROLE_CATEGORY_CREATE', "Créer une catégorie");
    yield Permission::new('ROLE_CATEGORY_LIST', "Consulter la liste des catégories");
    yield Permission::new('ROLE_CATEGORY_UPDATE', "Modifier une catégorie");
    yield Permission::new('ROLE_CATEGORY_DETAILS', "Consulter les détails d'une catégorie");

};
