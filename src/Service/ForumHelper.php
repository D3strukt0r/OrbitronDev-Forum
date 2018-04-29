<?php

namespace App\Service;

use App\Entity\Board;
use App\Entity\Forum;
use Doctrine\Common\Persistence\ObjectManager;

class ForumHelper
{
    public static $settings = [
        'forum' => [
            'name' => [
                'min_length' => 4,
            ],
            'url' => [
                'min_length' => 3,
            ],
        ],
    ];

    const DEFAULT_SHOW_THREAD_COUNT = 10;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    public function __construct(ObjectManager $manager)
    {
        $this->em = $manager;
    }

    /**
     * Checks whether the given url exists, in other words, if the forum exists.
     *
     * @param string $url
     *
     * @return bool
     */
    public function urlExists($url)
    {
        /** @var \App\Entity\Forum[] $find */
        $find = $this->em->getRepository(Forum::class)->findBy(['url' => $url]);

        if (null !== $find) {
            if (count($find)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a breadcrumb for the current tree.
     *
     * @param \App\Entity\Board $board
     *
     * @return \App\Entity\Board[]
     */
    public function getBreadcrumb($board)
    {
        $boardsList = [];
        $parentBoard = $board->getParentBoard();

        while (null !== $parentBoard) {
            $next = $parentBoard;
            array_unshift($boardsList, $next);
            $board = $next;
            $parentBoard = $board->getParentBoard();
        }

        return $boardsList;
    }

    /**
     * @param \App\Entity\Forum      $forum
     * @param \App\Entity\Board|null $board
     * @param int                    $level
     * @param array                  $list
     *
     * @return array
     */
    public function listBoardsFormSelect($forum, $board, $level = 1, &$list = [])
    {
        /** @var \App\Entity\Board[] $boardList */
        $boardList = $this->em->getRepository(Board::class)->findBy(['forum' => $forum, 'parent_board' => $board]);

        if (empty($list)) {
            $list['- Root (ID: 0)'] = 0;
        }

        foreach ($boardList as $currentBoard) {
            $line = '-';
            for ($i = mb_strlen($line) - 1; $i < $level; ++$i) {
                $line .= '-';
            }

            $title = $line.' '.$currentBoard->getTitle().' (ID: '.$currentBoard->getId().')';
            $list[$title] = $currentBoard->getId();

            // Has sub-boards
            if (count($currentBoard->getBoards())) {
                $nextLevel = $level + 1;
                self::listBoardsFormSelect($forum, $currentBoard, $nextLevel, $list);
            }
        }

        return $list;
    }
}
