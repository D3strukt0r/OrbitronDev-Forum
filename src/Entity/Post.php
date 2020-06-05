<?php

namespace App\Entity;

use DateTime;
use Decoda\Decoda;
use Decoda\Hook\EmoticonHook;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="forum_posts")
 */
class Post
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var Thread
     * @ORM\ManyToOne(targetEntity="Thread", inversedBy="posts")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id", nullable=false)
     */
    protected $thread;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $post_number;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="\App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $subject;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created_on;

    /**
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Thread The thread
     */
    public function getThread(): Thread
    {
        return $this->thread;
    }

    /**
     * @param Thread $thread The thread
     *
     * @return $this
     */
    public function setThread(Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * @return int The number of posts
     */
    public function getPostNumber(): int
    {
        return $this->post_number;
    }

    /**
     * @param int $post_number The number of posts
     *
     * @return $this
     */
    public function setPostNumber(int $post_number): self
    {
        $this->post_number = $post_number;

        return $this;
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
     * @return string The subject
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject The subject
     *
     * @return $this
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string The message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string The formatted message
     */
    public function getFormattedMessage(): string
    {
        $bbParser = new Decoda($this->message);
        $bbParser->defaults();
        $bbParser->addHook(new EmoticonHook());

        return $bbParser->convertLineBreaks($bbParser->parse());
    }

    /**
     * @param string $message The message
     *
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return DateTime The date when the post was created
     */
    public function getCreatedOn(): DateTime
    {
        return $this->created_on;
    }

    /**
     * @param DateTime $created_on The date when the post was created
     *
     * @return $this
     */
    public function setCreatedOn(DateTime $created_on): self
    {
        $this->created_on = $created_on;

        return $this;
    }

    /**
     * @return array An array of all the properties of an object
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'thread' => $this->thread,
            'post_number' => $this->post_number,
            'user' => $this->user,
            'subject' => $this->subject,
            'message' => $this->message,
            'created_on' => $this->created_on,
        ];
    }
}
