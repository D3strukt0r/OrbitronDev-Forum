<?php

namespace App\Controller\Panel;

use App\Entity\Forum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomiseController extends AbstractController
{
    public static function __setupNavigation()
    {
        return [
            [
                'type' => 'group',
                'parent' => 'root',
                'id' => 'customise',
                'title' => 'Customise',
                'icon' => 'hs-admin-brush-alt',
            ],
            [
                'type' => 'link',
                'parent' => 'customise',
                'id' => 'theme',
                'title' => 'Themes',
                'href' => 'customise-theme',
                'view' => 'CustomiseController::customiseTheme',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 40;
    }

    public function customiseTheme($navigation, Forum $forum)
    {
        return $this->forward(
            'App\\Controller\\Panel\\DefaultController::notFound',
            [
                'navigation' => $navigation,
                'forum' => $forum,
            ]
        );
    }
}
