<?php

namespace App\Controller\Panel;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PostingController extends Controller
{
    public static function __setupNavigation()
    {
        return [
            [
                'type' => 'group',
                'parent' => 'root',
                'id' => 'posting',
                'title' => 'Posting',
                'icon' => 'hs-admin-comment',
            ],
            [
                'type' => 'link',
                'parent' => 'posting',
                'id' => 'bbcode',
                'title' => 'BBCode',
                'href' => 'posting-bbcode',
                'view' => 'PostingController::bbCode',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 20;
    }

    public function bbCode($navigation, $forum)
    {
        return $response = $this->forward('App\\Controller\\Panel\\DefaultController::notFound', [
            'navigation' => $navigation,
            'forum' => $forum,
        ]);
    }
}
