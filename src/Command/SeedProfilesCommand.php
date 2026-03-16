<?php

namespace App\Command;

use App\Entity\Profile;
use App\Model\UserProxyIntertace;
use App\Manager\PermissionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'vd:seed:profiles',
    description: 'Create new user',
)]
class SeedProfilesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pm = PermissionManager::getInstance();
        $all = array_map(fn($p) => $p->getPermissionId(), $pm->getPermissions());

        $adminExcluded = ['ROLE_PLATFORM_CREATE', 'ROLE_PROFILE_CREATE', 'ROLE_PROFILE_UPDATE'];
        $admin = [
            'label' => 'Administrateur',
            'person' => UserProxyIntertace::PERSON_ADMIN,
            'permissions' => array_values(array_filter($all, fn($id) => !in_array($id, $adminExcluded, true))),
        ];

        $managerPrefixes = [
            'ROLE_PLATFORM_',
            'ROLE_MENU_',
            'ROLE_CATEGORY_',
            'ROLE_PRODUCT_',
            'ROLE_OPTION_GROUP_',
            'ROLE_OPTION_ITEM_',
            'ROLE_PLATFORM_TABLE_',
            'ROLE_TABLET_',
            'ROLE_DOC_',
            'ROLE_PAYMENT_',
            'ROLE_ORDER_',
            'ROLE_ORDER_ITEM_',
            'ROLE_ORDER_ITEM_OPTION_',
        ];
        $managerBase = array_values(array_filter($all, fn($id) => str_starts_with($id, 'ROLE_PROFILE_') || str_starts_with($id, 'ROLE_ACTIVITY_') || str_starts_with($id, 'ROLE_CURRENCY_') || str_starts_with($id, 'ROLE_EXCHANGE_RATE_')));
        $manager = [
            'label' => 'Manager',
            'person' => UserProxyIntertace::PERSON_MANAGER,
            'permissions' => array_values(array_unique(array_merge(
                array_filter($all, fn($id) => array_reduce($managerPrefixes, fn($carry, $prefix) => $carry || str_starts_with($id, $prefix), false)),
                array_filter($managerBase, fn($id) => in_array($id, [
                    'ROLE_PROFILE_LIST', 'ROLE_PROFILE_DETAILS',
                    'ROLE_ACTIVITY_LIST', 'ROLE_ACTIVITY_VIEW',
                    'ROLE_CURRENCY_LIST', 'ROLE_CURRENCY_DETAILS',
                    'ROLE_EXCHANGE_RATE_READ',
                ], true))
            ))),
        ];

        $staff = [
            'label' => 'Personnel',
            'person' => UserProxyIntertace::PERSON_STAFF,
            'permissions' => [
                'ROLE_ORDER_LIST', 'ROLE_ORDER_DETAILS', 'ROLE_ORDER_CREATE', 'ROLE_ORDER_SENT_TO_KITCHEN', 'ROLE_ORDER_AS_SERVED', 'ROLE_ORDER_AS_CANCELLED',
                'ROLE_ORDER_ITEM_LIST', 'ROLE_ORDER_ITEM_DETAILS', 'ROLE_ORDER_ITEM_CREATE',
                'ROLE_ORDER_ITEM_OPTION_LIST', 'ROLE_ORDER_ITEM_OPTION_DETAILS', 'ROLE_ORDER_ITEM_OPTION_CREATE',
                'ROLE_PAYMENT_CREATE', 'ROLE_PAYMENT_LIST', 'ROLE_PAYMENT_DETAILS',
                'ROLE_DOC_CREATE', 'ROLE_DOC_LIST', 'ROLE_DOC_DETAILS',
                'ROLE_MENU_LIST', 'ROLE_MENU_DETAILS',
                'ROLE_CATEGORY_LIST', 'ROLE_CATEGORY_DETAILS',
                'ROLE_PRODUCT_LIST', 'ROLE_PRODUCT_DETAILS',
                'ROLE_OPTION_GROUP_LIST', 'ROLE_OPTION_GROUP_DETAILS',
                'ROLE_OPTION_ITEM_LIST', 'ROLE_OPTION_ITEM_DETAILS',
                'ROLE_PLATFORM_TABLE_LIST', 'ROLE_PLATFORM_TABLE_DETAILS',
                'ROLE_TABLET_LIST', 'ROLE_TABLET_DETAILS',
            ],
        ];

        $kitchen = [
            'label' => 'Cuisine',
            'person' => UserProxyIntertace::PERSON_KITCHEN,
            'permissions' => [
                'ROLE_ORDER_LIST', 'ROLE_ORDER_DETAILS', 'ROLE_ORDER_AS_READY',
                'ROLE_ORDER_ITEM_LIST', 'ROLE_ORDER_ITEM_DETAILS',
            ],
        ];

        $waiter = [
            'label' => 'Serveur',
            'person' => UserProxyIntertace::PERSON_WAITER,
            'permissions' => [
                // Voir le catalogue pour composer la commande
                'ROLE_MENU_LIST', 'ROLE_MENU_DETAILS',
                'ROLE_CATEGORY_LIST', 'ROLE_CATEGORY_DETAILS',
                'ROLE_PRODUCT_LIST', 'ROLE_PRODUCT_DETAILS',
                'ROLE_OPTION_GROUP_LIST', 'ROLE_OPTION_GROUP_DETAILS',
                'ROLE_OPTION_ITEM_LIST', 'ROLE_OPTION_ITEM_DETAILS',
                // Voir les tables de la plateforme
                'ROLE_PLATFORM_TABLE_LIST', 'ROLE_PLATFORM_TABLE_DETAILS',
                // Commande
                'ROLE_ORDER_LIST', 'ROLE_ORDER_DETAILS',
                'ROLE_ORDER_CREATE', 'ROLE_ORDER_SENT_TO_KITCHEN',
            ],
        ];

        $cashier = [
            'label' => 'Caissier',
            'person' => UserProxyIntertace::PERSON_CASHIER,
            'permissions' => [
                'ROLE_PAYMENT_CREATE', 'ROLE_PAYMENT_LIST', 'ROLE_PAYMENT_DETAILS',
                'ROLE_ORDER_LIST', 'ROLE_ORDER_DETAILS',
                'ROLE_ORDER_CREATE',
                'ROLE_PRODUCT_LIST',
                'ROLE_PLATFORM_TABLE_LIST',
            ],
        ];

        $selfOrder = [
            'label' => 'Commande (Self-Order)',
            'person' => UserProxyIntertace::PERSON_SELF_ORDER,
            'permissions' => [
                // Voir le catalogue minimal pour composer la commande
                'ROLE_MENU_LIST', 'ROLE_MENU_DETAILS',
                'ROLE_CATEGORY_LIST', 'ROLE_CATEGORY_DETAILS',
                'ROLE_PRODUCT_LIST', 'ROLE_PRODUCT_DETAILS',
                'ROLE_OPTION_GROUP_LIST', 'ROLE_OPTION_GROUP_DETAILS',
                'ROLE_OPTION_ITEM_LIST', 'ROLE_OPTION_ITEM_DETAILS',
                // Création de commande
                'ROLE_ORDER_CREATE',
            ],
        ];

        foreach ([$admin, $manager, $staff, $kitchen, $waiter, $cashier, $selfOrder] as $spec) {
            $repo = $this->em->getRepository(Profile::class);
            $existing = $repo->findOneBy(['personType' => $spec['person']]);
            $perms = array_values(array_intersect($all, $spec['permissions']));
            if (\in_array('ROLE_PLATFORM_LIST', $all, true) && !\in_array('ROLE_PLATFORM_LIST', $perms, true)) {
                $perms[] = 'ROLE_PLATFORM_LIST';
            }
            if ($existing) {
                $existing->setLabel($spec['label']);
                $existing->setPermission($perms);
                $existing->setActive(true);
            } else {
                $p = new Profile();
                $p->setLabel($spec['label']);
                $p->setPersonType($spec['person']);
                $p->setPermission($perms);
                $p->setActive(true);
                $this->em->persist($p);
            }
        }

        $this->em->flush();
        $output->writeln('Profiles seeded.');
        return Command::SUCCESS;
    }
}
