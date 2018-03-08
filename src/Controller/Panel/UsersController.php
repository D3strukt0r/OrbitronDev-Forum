<?php

namespace App\Controller\Panel;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsersController extends Controller
{
    public static function __setupNavigation()
    {
        return [
            [
                'type'   => 'group',
                'parent' => 'root',
                'id'     => 'users',
                'title'  => 'Users',
                'icon'   => 'hs-admin-user',
            ],
            [
                'type'   => 'link',
                'parent' => 'users',
                'id'     => 'bans',
                'title'  => 'Ban user',
                'href'   => 'users-ban',
                'view'   => 'UsersController::ban',
            ],
            [
                'type'   => 'link',
                'parent' => 'users',
                'id'     => 'ranks',
                'title'  => 'User ranks',
                'href'   => 'users-rank',
                'view'   => 'UsersController::rank',
            ],
            [
                'type'   => 'link',
                'parent' => 'users',
                'id'     => 'groups',
                'title'  => 'Manage groups',
                'href'   => 'groups',
                'view'   => 'UsersController::groups',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 30;
    }

    public function ban()
    {
        throw $this->createNotFoundException();
    }

    public function rank()
    {
        throw $this->createNotFoundException();
    }

    public function groups()
    {
        throw $this->createNotFoundException();
    }
}
