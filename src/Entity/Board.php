<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="forum_boards")
 */
class Board
{
    const TYPE_BOARD = 1;
    const TYPE_CATEGORY = 2;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var Forum
     * @ORM\ManyToOne(targetEntity="Forum", inversedBy="boards")
     * @ORM\JoinColumn(name="forum_id", referencedColumnName="id", nullable=false)
     */
    protected $forum;

    /**
     * @var Board|null
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="boards")
     * @ORM\JoinColumn(name="parent_board_id", referencedColumnName="id")
     */
    protected $parent_board;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    protected $type;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="\App\Entity\User")
     * @ORM\JoinColumn(name="last_post_user_id", referencedColumnName="id")
     */
    protected $last_post_user;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $last_post_time;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default": 0})
     */
    protected $thread_count = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default": 0})
     */
    protected $post_count = 0;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Board", mappedBy="parent_board", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $boards;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Thread", mappedBy="board", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $threads;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
        $this->threads = new ArrayCollection();
    }

    /**
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Forum The forum
     */
    public function getForum(): Forum
    {
        return $this->forum;
    }

    /**
     * @param Forum $forum The forum
     *
     * @return $this
     */
    public function setForum(Forum $forum): self
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * @return Board|null The parent board or null if already the top-most board
     */
    public function getParentBoard(): ?self
    {
        return $this->parent_board;
    }

    /**
     * @param Board|null $parent_board The parent board or null if already the top-most board
     *
     * @return $this
     */
    public function setParentBoard(self $parent_board = null): self
    {
        $this->parent_board = $parent_board;

        return $this;
    }

    /**
     * @return string The title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title The title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null The description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description The description
     *
     * @return $this
     */
    public function setDescription(string $description = null): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int The type
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type The type
     *
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return User|null The use who last posted
     */
    public function getLastPostUser(): ?User
    {
        return $this->last_post_user;
    }

    /**
     * @param User|null $last_post_user The user who last posted
     *
     * @return $this
     */
    public function setLastPostUser(User $last_post_user = null): self
    {
        $this->last_post_user = $last_post_user;

        return $this;
    }

    /**
     * @return DateTime|null The last time somebody posted
     */
    public function getLastPostTime(): ?DateTime
    {
        return $this->last_post_time;
    }

    /**
     * @param DateTime|null $last_post_time The last time somebody posted
     *
     * @return $this
     */
    public function setLastPostTime(DateTime $last_post_time = null): self
    {
        $this->last_post_time = $last_post_time;

        return $this;
    }

    /**
     * @return int The number of threads
     */
    public function getThreadCount(): int
    {
        return $this->thread_count;
    }

    /**
     * @param int $threads The number of threads
     *
     * @return $this
     */
    public function setThreadCount(int $threads): self
    {
        $this->thread_count = $threads;

        return $this;
    }

    /**
     * @return int The number of posts
     */
    public function getPostCount(): int
    {
        return $this->post_count;
    }

    /**
     * @param int $posts The number of posts
     *
     * @return $this
     */
    public function setPostCount(int $posts): self
    {
        $this->post_count = $posts;

        return $this;
    }

    /**
     * @return Thread[] The threads
     */
    public function getThreads(): array
    {
        return $this->threads->toArray();
    }

    /**
     * @param Thread $thread The thread
     *
     * @return $this
     */
    public function addThread(Thread $thread): self
    {
        $this->threads->add($thread);
        $thread->setBoard($this);

        return $this;
    }

    /**
     * @param Thread $thread The thread
     *
     * @return $this
     */
    public function removeThread(Thread $thread): self
    {
        if ($this->threads->contains($thread)) {
            $this->threads->removeElement($thread);
        }

        return $this;
    }

    /**
     * @return Board[] The boards
     */
    public function getBoards(): array
    {
        return $this->boards->toArray();
    }

    /**
     * @param Board $board The board
     *
     * @return $this
     */
    public function addBoard(self $board): self
    {
        $this->boards->add($board);
        $board->setParentBoard($this);

        return $this;
    }

    /**
     * @param Board $board The board
     *
     * @return $this
     */
    public function removeBoard(self $board): self
    {
        if ($this->boards->contains($board)) {
            $this->boards->removeElement($board);
        }

        return $this;
    }

    /**
     * @return array An array of all the properties of an object
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'forum' => $this->forum,
            'parent_board' => $this->parent_board,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'last_post_user' => $this->last_post_user,
            'last_post_time' => $this->last_post_time,
            'threads' => $this->thread_count,
            'posts' => $this->post_count,
        ];
    }
}
