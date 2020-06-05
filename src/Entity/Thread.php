<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="forum_threads")
 */
class Thread
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="\App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var Board
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="threads")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id", nullable=false)
     */
    protected $board;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $topic;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default": 0})
     */
    protected $views = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default": 0})
     */
    protected $replies = 0;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    protected $sticky = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    protected $closed = false;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created_on;

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
     * @var Collection
     * @ORM\OneToMany(targetEntity="Post", mappedBy="thread", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User The user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user The user
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Board The board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @param Board $board The board
     *
     * @return $this
     */
    public function setBoard(Board $board): self
    {
        $this->board = $board;

        return $this;
    }

    /**
     * @return string The topic
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @param string $topic The topic
     *
     * @return $this
     */
    public function setTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * @return int The views
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views The views
     *
     * @return $this
     */
    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return int The replies
     */
    public function getReplies(): int
    {
        return $this->replies;
    }

    /**
     * @param int $replies The replies
     *
     * @return $this
     */
    public function setReplies(int $replies): self
    {
        $this->replies = $replies;

        return $this;
    }

    /**
     * @return bool Whether the thread is sticky
     */
    public function isSticky(): bool
    {
        return $this->sticky;
    }

    /**
     * @param bool $sticky Whether the thread is sticky
     *
     * @return $this
     */
    public function setSticky(bool $sticky): self
    {
        $this->sticky = $sticky;

        return $this;
    }

    /**
     * @return bool Whether the thread is closed
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @param bool $closed Whether the thread is closed
     *
     * @return $this
     */
    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * @return DateTime The date when the thread was created
     */
    public function getCreatedOn(): DateTime
    {
        return $this->created_on;
    }

    /**
     * @param DateTime $created_on The date when the thread was created
     *
     * @return $this
     */
    public function setCreatedOn(DateTime $created_on): self
    {
        $this->created_on = $created_on;

        return $this;
    }

    /**
     * @return User|null The user of the last post
     */
    public function getLastPostUser(): ?User
    {
        return $this->last_post_user;
    }

    /**
     * @param User|null $last_post_user The user of the last post
     *
     * @return $this
     */
    public function setLastPostUser(User $last_post_user = null): self
    {
        $this->last_post_user = $last_post_user;

        return $this;
    }

    /**
     * @return DateTime|null The last time something was posted
     */
    public function getLastPostTime(): ?DateTime
    {
        return $this->last_post_time;
    }

    /**
     * @param DateTime|null $last_post_time The last time something was posted
     *
     * @return $this
     */
    public function setLastPostTime(DateTime $last_post_time = null): self
    {
        $this->last_post_time = $last_post_time;

        return $this;
    }

    /**
     * @return Post[] The posts
     */
    public function getPosts(): array
    {
        return $this->posts->toArray();
    }

    /**
     * @param Post $post The post
     *
     * @return $this
     */
    public function addPost(Post $post): self
    {
        $this->posts->add($post);
        $post->setThread($this);

        return $this;
    }

    /**
     * @param Post $post The post
     *
     * @return $this
     */
    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
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
            'user' => $this->user,
            'board' => $this->board,
            'topic' => $this->topic,
            'views' => $this->views,
            'replies' => $this->replies,
            'sticky' => $this->sticky,
            'closed' => $this->closed,
            'created_on' => $this->created_on,
            'last_post_user' => $this->last_post_user,
            'last_post_time' => $this->last_post_time,
        ];
    }
}
