<?php

namespace App\Controller\Panel;

use App\Entity\Board;
use App\Entity\Forum;
use App\Form\CreateBoardType;
use App\Service\ForumHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ForumController extends AbstractController
{
    public static function __setupNavigation()
    {
        return [
            [
                'type' => 'group',
                'parent' => 'root',
                'id' => 'boards',
                'title' => 'Boards',
                'icon' => 'hs-admin-layers',
            ],
            [
                'type' => 'link',
                'parent' => 'boards',
                'id' => 'list',
                'title' => 'Manage Boards',
                'href' => 'board-list',
                'view' => 'ForumController::boardList',
            ],
            [
                'type' => 'link',
                'parent' => 'null',
                'id' => 'new_board',
                'title' => 'New Board',
                'href' => 'new-board',
                'view' => 'ForumController::newBoard',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 10;
    }

    public function boardList($navigation, Forum $forum)
    {
        $em = $this->getDoctrine()->getManager();

        $boardList = $em->getRepository(Board::class)->findBy(['forum' => $forum, 'parent_board' => null]);

        return $this->render(
            'theme_admin1/board-list.html.twig',
            [
                'current_forum' => $forum,
                'board_list' => $boardList,
                'navigation_links' => $navigation,
            ]
        );
    }

    public function newBoard(Request $request, ForumHelper $helper, $navigation, Forum $forum)
    {
        $em = $this->getDoctrine()->getManager();

        $specialBoardList = $helper->listBoardsFormSelect($forum, null);

        $createBoardForm = $this->createForm(CreateBoardType::class, null, ['board_list' => $specialBoardList]);

        $createBoardForm->handleRequest($request);
        if ($createBoardForm->isSubmitted() && $createBoardForm->isValid()) {
            $formData = $createBoardForm->getData();

            /** @var Board|null $parentBoard */
            $parentBoard = $em->getRepository(Board::class)->findOneBy(['id' => $formData['parent']]);

            $newBoard = new Board();
            $newBoard
                ->setForum($forum)
                ->setParentBoard($parentBoard)
                ->setTitle($formData['name'])
                ->setDescription($formData['description'])
                ->setType($formData['type'])
            ;

            $em->persist($newBoard);
            $em->flush();

            $this->addFlash('board_added', '');
        }

        return $this->render(
            'theme_admin1/new-board.html.twig',
            [
                'create_board_form' => $createBoardForm->createView(),
                'current_forum' => $forum,
                'navigation_links' => $navigation,
            ]
        );
    }
}
