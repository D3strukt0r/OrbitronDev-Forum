<?php

namespace App\Service;

use App\Entity\Board;
use App\Entity\Forum;
use Doctrine\ORM\EntityManagerInterface;

class ForumHelper
{
    public const DEFAULT_SHOW_THREAD_COUNT = 10;

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

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $manager)
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
        /** @var Forum[] $find */
        $find = $this->em->getRepository(Forum::class)->findBy(['url' => $url]);

        if (count($find)) {
            return true;
        }

        return false;
    }

    /**
     * Get a breadcrumb for the current tree.
     *
     * @param Board $board
     *
     * @return Board[]
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
     * @param Forum      $forum
     * @param Board|null $board
     * @param int        $level
     * @param array      $list
     *
     * @return array
     */
    public function listBoardsFormSelect($forum, $board, $level = 1, &$list = [])
    {
        /** @var Board[] $boardList */
        $boardList = $this->em->getRepository(Board::class)->findBy(['forum' => $forum, 'parent_board' => $board]);

        if (empty($list)) {
            $list['- Root (ID: 0)'] = 0;
        }

        foreach ($boardList as $currentBoard) {
            $line = '-';
            for ($i = mb_strlen($line) - 1; $i < $level; ++$i) {
                $line .= '-';
            }

            $title = $line . ' ' . $currentBoard->getTitle() . ' (ID: ' . $currentBoard->getId() . ')';
            $list[$title] = $currentBoard->getId();

            // Has sub-boards
            if (count($currentBoard->getBoards())) {
                $nextLevel = $level + 1;
                $this->listBoardsFormSelect($forum, $currentBoard, $nextLevel, $list);
            }
        }

        return $list;
    }
}
