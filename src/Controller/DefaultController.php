<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Forum;
use App\Entity\Post;
use App\Entity\Thread;
use App\Entity\User;
use App\Form\NewForumType;
use App\Form\PostType;
use App\Form\ThreadType;
use App\Service\AdminControlPanel;
use App\Service\ForumHelper;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Forum[] $forumList */
        $forumList = $em->getRepository(Forum::class)->findAll();

        return $this->render(
            'list-forums.html.twig',
            [
                'forums_list' => $forumList,
            ]
        );
    }

    /**
     * @Route("/new-forum", name="new")
     *
     * @param Request             $request    The request
     * @param TranslatorInterface $translator The translator
     *
     * @return RedirectResponse|Response
     */
    public function newForum(Request $request, TranslatorInterface $translator)
    {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException();
        }
        //////////// END TEST IF USER IS LOGGED IN ////////////

        $createForumForm = $this->createForm(NewForumType::class);

        $createForumForm->handleRequest($request);
        if ($createForumForm->isSubmitted() && $createForumForm->isValid()) {
            $formData = $createForumForm->getData();

            try {
                $newForum = new Forum();
                $newForum
                    ->setName($formData['name'])
                    ->setUrl($formData['url'])
                    ->setOwner($user)
                    ->setCreated(new DateTime())
                ;
                $em = $this->getDoctrine()->getManager();
                $em->persist($newForum);
                $em->flush();

                return $this->redirectToRoute('forum_index', ['forum' => $newForum->getUrl()]);
            } catch (Exception $e) {
                $createForumForm->addError(
                    new FormError(
                        $translator->trans(
                            'new_forum.not_created',
                            ['%error_message%' => $e->getMessage()],
                            'validators'
                        )
                    )
                );
            }
        }

        return $this->render(
            'create-new-forum.html.twig',
            [
                'create_forum_form' => $createForumForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{forum}", name="forum_index")
     *
     * @param string $forum The forum
     *
     * @return Response
     */
    public function forumIndex(string $forum)
    {
        //////////// TEST IF FORUM EXISTS ////////////
        $em = $this->getDoctrine()->getManager();
        /** @var Forum|null $forum */
        $forum = $em->getRepository(Forum::class)->findOneBy(['url' => $forum]);
        if (null === $forum) {
            throw $this->createNotFoundException();
        }
        //////////// END TEST IF FORUM EXISTS ////////////

        // Get all boards
        /** @var Board[] $boardTree */
        $boardTree = $em->getRepository(Board::class)->findBy(['forum' => $forum, 'parent_board' => null]);

        return $this->render(
            'theme1/index.html.twig',
            [
                'current_forum' => $forum,
                'board_tree' => $boardTree,
            ]
        );
    }

    /**
     * @Route("/{forum}/board/{board}", name="forum_board")
     *
     * @param Request     $request The request
     * @param ForumHelper $helper  The forum helper
     * @param string      $forum   The forum
     * @param string      $board   The board
     *
     * @return Response
     */
    public function forumBoard(Request $request, ForumHelper $helper, string $forum, string $board)
    {
        //////////// TEST IF FORUM EXISTS ////////////
        $em = $this->getDoctrine()->getManager();
        /** @var Forum|null $forum */
        $forum = $em->getRepository(Forum::class)->findOneBy(['url' => $forum]);
        if (null === $forum) {
            throw $this->createNotFoundException();
        }
        //////////// END TEST IF FORUM EXISTS ////////////

        //////////// TEST IF BOARD EXISTS ////////////
        /** @var Board|null $board */
        $board = $em->getRepository(Board::class)->findOneBy(['id' => $board]);
        if (null === $board) {
            throw $this->createNotFoundException();
        }
        //////////// TEST IF BOARD EXISTS ////////////

        // Breadcrumb
        $breadcrumb = $helper->getBreadcrumb($board);

        // Get all boards
        $boardTree = $em->getRepository(Board::class)->findBy(['forum' => $forum, 'parent_board' => $board]);

        // Get all threads
        $pagination = [];
        $pagination['item_limit'] = $request->query->getInt('show', ForumHelper::DEFAULT_SHOW_THREAD_COUNT);
        $pagination['current_page'] = $request->query->getInt('page', 1);

        /** @var Thread[] $threads */
        $threads = $em->getRepository(Thread::class)->findBy(
            ['board' => $board],
            ['last_post_time' => 'DESC'],
            $pagination['item_limit'],
            ($pagination['current_page'] - 1) * $pagination['item_limit']
        )
        ;

        // Pagination
        // Reference: http://www.strangerstudios.com/sandbox/pagination/diggstyle.php
        /** @var Thread[] $threadCount */
        $threadCount = $em->getRepository(Thread::class)->findBy(['board' => $board]);
        $pagination['total_items'] = count($threadCount);
        $pagination['adjacents'] = 1;

        $pagination['next_page'] = $pagination['current_page'] + 1;
        $pagination['previous_page'] = $pagination['current_page'] - 1;
        $pagination['pages_count'] = ceil($pagination['total_items'] / $pagination['item_limit']);
        $pagination['last_page_m1'] = $pagination['pages_count'] - 1;

        return $this->render(
            'theme1/board.html.twig',
            [
                'current_forum' => $forum,
                'current_board' => $board,
                'breadcrumb' => $breadcrumb,
                'board_tree' => $boardTree,
                'threads' => $threads,
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @Route("/{forum}/thread/{thread}", name="forum_thread")
     *
     * @param Request     $request The request
     * @param ForumHelper $helper  The forum helper
     * @param string      $forum   The forum
     * @param string      $thread  The thread
     *
     * @return Response
     */
    public function forumThread(Request $request, ForumHelper $helper, string $forum, string $thread)
    {
        //////////// TEST IF FORUM EXISTS ////////////
        $em = $this->getDoctrine()->getManager();
        /** @var Forum|null $forum */
        $forum = $em->getRepository(Forum::class)->findOneBy(['url' => $forum]);
        if (null === $forum) {
            throw $this->createNotFoundException();
        }
        //////////// END TEST IF FORUM EXISTS ////////////

        //////////// TEST IF THREAD EXISTS ////////////
        /** @var Thread|null $thread */
        $thread = $em->getRepository(Thread::class)->findOneBy(['id' => $thread]);
        if (null === $thread) {
            throw $this->createNotFoundException();
        }
        $thread->setViews($thread->getViews() + 1);
        $em->flush();

        $board = $thread->getBoard();
        //////////// END TEST IF THREAD EXISTS ////////////

        // Breadcrumb
        $breadcrumb = $helper->getBreadcrumb($board);

        // Get all posts
        $pagination = [];
        $pagination['item_limit'] = $request->query->getInt('show', ForumHelper::DEFAULT_SHOW_THREAD_COUNT);
        $pagination['current_page'] = $request->query->getInt('page', 1);

        /** @var Post[] $posts */
        $posts = $em->getRepository(Post::class)->findBy(
            ['thread' => $thread],
            ['post_number' => 'ASC'],
            $pagination['item_limit'],
            ($pagination['current_page'] - 1) * $pagination['item_limit']
        )
        ;

        // Pagination
        /** @var Post[] $postCount */
        $postCount = $em->getRepository(Post::class)->findBy(['thread' => $thread]);
        $pagination['total_items'] = count($postCount);
        $pagination['adjacents'] = 1;

        $pagination['next_page'] = $pagination['current_page'] + 1;
        $pagination['previous_page'] = $pagination['current_page'] - 1;
        $pagination['pages_count'] = ceil($pagination['total_items'] / $pagination['item_limit']);
        $pagination['last_page_m1'] = $pagination['pages_count'] - 1;

        return $this->render(
            'theme1/thread.html.twig',
            [
                'current_forum' => $forum,
                'current_board' => $board,
                'current_thread' => $thread,
                'posts' => $posts,
                'breadcrumb' => $breadcrumb,
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @Route("/{forum}/thread/{thread}/create-post", name="forum_create_post")
     *
     * @param Request     $request The request
     * @param ForumHelper $helper  The forum helper
     * @param string      $forum   The forum
     * @param string      $thread  The thread
     *
     * @return Response
     */
    public function forumCreatePost(Request $request, ForumHelper $helper, string $forum, string $thread)
    {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException();
        }
        //////////// END TEST IF USER IS LOGGED IN ////////////

        //////////// TEST IF FORUM EXISTS ////////////
        $em = $this->getDoctrine()->getManager();
        /** @var Forum|null $forum */
        $forum = $em->getRepository(Forum::class)->findOneBy(['url' => $forum]);
        if (null === $forum) {
            throw $this->createNotFoundException();
        }
        //////////// END TEST IF FORUM EXISTS ////////////

        //////////// TEST IF THREAD EXISTS ////////////
        /** @var Thread|null $thread */
        $thread = $em->getRepository(Thread::class)->findOneBy(['id' => $thread]);
        if (null === $thread) {
            throw $this->createNotFoundException();
        }

        $board = $thread->getBoard();
        //////////// END TEST IF THREAD EXISTS ////////////

        // Breadcrumb
        $breadcrumb = $helper->getBreadcrumb($board);

        $createPostForm = $this->createForm(PostType::class, null, ['topic' => $thread->getTopic()]);

        $createPostForm->handleRequest($request);
        if ($createPostForm->isSubmitted() && $createPostForm->isValid()) {
            $formData = $createPostForm->getData();

            try {
                $time = new DateTime();

                // Add post entity
                $newPost = new Post();
                $newPost
                    ->setThread($thread)
                    ->setUser($user)
                    ->setPostNumber($thread->getReplies() + 2)
                    ->setSubject($formData['title'])
                    ->setMessage($formData['message'])
                    ->setCreatedOn($time)
                ;
                $em->persist($newPost);

                // Update thread count and last post user and time
                $thread->setReplies($thread->getReplies() + 1);
                $thread->setLastPostUser($user);
                $thread->setLastPostTime($time);

                $board->setPostCount($board->getPostCount() + 1);
                $board->setLastPostUser($user);
                $board->setLastPostTime($time);
                foreach ($breadcrumb as $item) {
                    $item->setPostCount($item->getPostCount() + 1);
                    $item->setLastPostUser($user);
                    $item->setLastPostTime($time);
                }

                $em->flush();

                $this->addFlash('post_created', '');
            } catch (Exception $e) {
                $this->addFlash('post_not_created', '');
            }
        }

        return $this->render(
            'theme1/create-post.html.twig',
            [
                'current_forum' => $forum,
                'current_board' => $board,
                'current_thread' => $thread,
                'breadcrumb' => $breadcrumb,
                'create_post_form' => $createPostForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{forum}/board/{board}/create-thread", name="forum_create_thread")
     *
     * @param Request     $request The request
     * @param ForumHelper $helper  The forum helper
     * @param string      $forum   The forum
     * @param string      $board   The board
     *
     * @return Response
     */
    public function forumCreateThread(Request $request, ForumHelper $helper, string $forum, string $board)
    {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException();
        }
        //////////// END TEST IF USER IS LOGGED IN ////////////

        //////////// TEST IF FORUM EXISTS ////////////
        $em = $this->getDoctrine()->getManager();
        /** @var Forum|null $forum */
        $forum = $em->getRepository(Forum::class)->findOneBy(['url' => $forum]);
        if (null === $forum) {
            throw $this->createNotFoundException();
        }
        //////////// END TEST IF FORUM EXISTS ////////////

        //////////// TEST IF BOARD EXISTS ////////////
        /** @var Board|null $board */
        $board = $em->getRepository(Board::class)->findOneBy(['id' => $board]);
        if (null === $board) {
            throw $this->createNotFoundException();
        }
        //////////// TEST IF BOARD EXISTS ////////////

        // Breadcrumb
        $breadcrumb = $helper->getBreadcrumb($board);

        $createThreadForm = $this->createForm(ThreadType::class);

        $createThreadForm->handleRequest($request);
        if ($createThreadForm->isSubmitted() && $createThreadForm->isValid()) {
            $formData = $createThreadForm->getData();

            try {
                // Add thread and post entity
                $time = new DateTime();
                $newThread = new Thread();
                $newThread
                    ->setUser($user)
                    ->setBoard($board)
                    ->setTopic($formData['title'])
                    ->setCreatedOn($time)
                    ->setLastPostUser($user)
                    ->setLastPostTime($time)
                ;
                $newPost = new Post();
                $newPost
                    ->setUser($user)
                    ->setPostNumber(1)
                    ->setSubject($formData['title'])
                    ->setMessage($formData['message'])
                    ->setCreatedOn($time)
                ;
                $newThread->addPost($newPost);
                $em->persist($newThread);
                $em->persist($newPost);

                // Update thread count and last post user and time
                $board->setThreadCount($board->getThreadCount() + 1);
                $board->setLastPostUser($user);
                $board->setLastPostTime($time);
                foreach ($breadcrumb as $item) {
                    $item->setThreadCount($item->getThreadCount() + 1);
                    $item->setLastPostUser($user);
                    $item->setLastPostTime($time);
                }

                $em->flush();

                $this->addFlash('thread_created', '');

                return $this->render(
                    'theme1/create-thread.html.twig',
                    [
                        'current_forum' => $forum,
                        'current_board' => $board,
                        'breadcrumb' => $breadcrumb,
                        'new_thread_id' => $newThread->getId(),
                        'create_thread_form' => $createThreadForm->createView(),
                    ]
                );
            } catch (Exception $e) {
                $this->addFlash('thread_not_created', '');
            }
        }

        return $this->render(
            'theme1/create-thread.html.twig',
            [
                'current_forum' => $forum,
                'current_board' => $board,
                'breadcrumb' => $breadcrumb,
                'create_thread_form' => $createThreadForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{forum}/admin/{page}", name="forum_admin")
     *
     * @param KernelInterface       $kernel       The kernel
     * @param TokenStorageInterface $tokenStorage The token storage
     * @param Request               $request      The request
     * @param string                $forum        The forum
     * @param string                $page         The page
     *
     * @return Response
     */
    public function forumAdmin(
        KernelInterface $kernel,
        TokenStorageInterface $tokenStorage,
        Request $request,
        string $forum,
        string $page
    ) {
        //////////// TEST IF USER IS LOGGED IN ////////////
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException();
        }
        //////////// END TEST IF USER IS LOGGED IN ////////////

        //////////// TEST IF FORUM EXISTS ////////////
        $em = $this->getDoctrine()->getManager();
        /** @var Forum|null $forum */
        $forum = $em->getRepository(Forum::class)->findOneBy(['url' => $forum]);
        if (null === $forum) {
            throw $this->createNotFoundException();
        }
        //////////// END TEST IF FORUM EXISTS ////////////

        if ($user->getId() !== $forum->getOwner()->getId()) {
            throw $this->createAccessDeniedException();
        }

        AdminControlPanel::loadLibs($kernel->getProjectDir(), $tokenStorage);

        $navigationLinks = AdminControlPanel::getTree();

        $view = 'DefaultController::notFound';

        $list = AdminControlPanel::getFlatTree();

        $key = null;
        while ($item = current($list)) {
            if (isset($item['href']) && $item['href'] === $page) {
                $key = key($list);
            }
            next($list);
        }

        if (null !== $key) {
            if (is_callable('\\App\\Controller\\Panel\\'.$list[$key]['view'])) {
                $view = $list[$key]['view'];
            }
        }

        return $this->forward(
            'App\\Controller\\Panel\\'.$view,
            [
                'navigation' => $navigationLinks,
                'request' => $request,
                'forum' => $forum,
            ]
        );
    }
}
