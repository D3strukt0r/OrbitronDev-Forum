<?php

namespace App\Entity;

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
     * @var \App\Entity\Thread
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
     * @var \App\Entity\User
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
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created_on;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \App\Entity\Thread
     */
    public function getThread(): Thread
    {
        return $this->thread;
    }

    /**
     * @param \App\Entity\Thread $thread
     *
     * @return $this
     */
    public function setThread(Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * @return int
     */
    public function getPostNumber(): int
    {
        return $this->post_number;
    }

    /**
     * @param int $post_number
     *
     * @return $this
     */
    public function setPostNumber(int $post_number): self
    {
        $this->post_number = $post_number;

        return $this;
    }

    /**
     * @return \App\Entity\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param \App\Entity\User $user
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getFormattedMessage(): string
    {
        $bbParser = new Decoda($this->message);
        $bbParser->defaults();
        $bbParser->addHook(new EmoticonHook());

        return $bbParser->convertLineBreaks($bbParser->parse());
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn(): \DateTime
    {
        return $this->created_on;
    }

    /**
     * @param \DateTime $created_on
     *
     * @return $this
     */
    public function setCreatedOn(\DateTime $created_on): self
    {
        $this->created_on = $created_on;

        return $this;
    }

    /**
     * @return array
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
