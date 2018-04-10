<?php

namespace App\Controller\Panel;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CustomiseController extends Controller
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

    public function customiseTheme($navigation, $forum)
    {
        return $response = $this->forward('App\\Controller\\Panel\\DefaultController::notFound', [
            'navigation' => $navigation,
            'forum' => $forum,
        ]);
    }
}
