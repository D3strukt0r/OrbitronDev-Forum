<?php

namespace App\Controller\Panel;

use App\Entity\Forum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostingController extends AbstractController
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

    public function bbCode($navigation, Forum $forum)
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
