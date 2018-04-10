<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="forum_boards")
 */
class Board
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var \App\Entity\Forum
     * @ORM\ManyToOne(targetEntity="Forum", inversedBy="boards")
     * @ORM\JoinColumn(name="forum_id", referencedColumnName="id", nullable=false)
     */
    protected $forum;

    /**
     * @var null|\App\Entity\Board
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

    const TYPE_BOARD = 1;
    const TYPE_CATEGORY = 2;

    /**
     * @var null|\App\Entity\User
     * @ORM\ManyToOne(targetEntity="\App\Entity\User")
     * @ORM\JoinColumn(name="last_post_user_id", referencedColumnName="id")
     */
    protected $last_post_user;

    /**
     * @var \DateTime|null
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
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Board", mappedBy="parent_board", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $boards;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Thread", mappedBy="board", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $threads;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
        $this->threads = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \App\Entity\Forum
     */
    public function getForum(): Forum
    {
        return $this->forum;
    }

    /**
     * @param \App\Entity\Forum $forum
     *
     * @return $this
     */
    public function setForum(Forum $forum): self
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * @return \App\Entity\Board|null
     */
    public function getParentBoard(): ?self
    {
        return $this->parent_board;
    }

    /**
     * @param \App\Entity\Board|null $parent_board
     *
     * @return $this
     */
    public function setParentBoard(self $parent_board = null): self
    {
        $this->parent_board = $parent_board;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     *
     * @return $this
     */
    public function setDescription(string $description = null): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return \App\Entity\User|null
     */
    public function getLastPostUser(): ?User
    {
        return $this->last_post_user;
    }

    /**
     * @param \App\Entity\User|null $last_post_user
     *
     * @return $this
     */
    public function setLastPostUser(User $last_post_user = null): self
    {
        $this->last_post_user = $last_post_user;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastPostTime(): ?\DateTime
    {
        return $this->last_post_time;
    }

    /**
     * @param \DateTime|null $last_post_time
     *
     * @return $this
     */
    public function setLastPostTime(\DateTime $last_post_time = null): self
    {
        $this->last_post_time = $last_post_time;

        return $this;
    }

    /**
     * @return int
     */
    public function getThreadCount(): int
    {
        return $this->thread_count;
    }

    /**
     * @param int $threads
     *
     * @return $this
     */
    public function setThreadCount(int $threads): self
    {
        $this->thread_count = $threads;

        return $this;
    }

    /**
     * @return int
     */
    public function getPostCount(): int
    {
        return $this->post_count;
    }

    /**
     * @param int $posts
     *
     * @return $this
     */
    public function setPostCount(int $posts): self
    {
        $this->post_count = $posts;

        return $this;
    }

    /**
     * @return \App\Entity\Thread[]
     */
    public function getThreads(): array
    {
        return $this->threads->toArray();
    }

    /**
     * @param \App\Entity\Thread $thread
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
     * @param \App\Entity\Thread $thread
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
     * @return \App\Entity\Board[]
     */
    public function getBoards(): array
    {
        return $this->boards->toArray();
    }

    /**
     * @param \App\Entity\Board $board
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
     * @param \App\Entity\Board $board
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
     * @return array
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
