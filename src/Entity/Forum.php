<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="forums")
 */
class Forum
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=191)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=191)
     */
    protected $url;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="\App\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     */
    protected $owner;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    protected $closed = false;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $closed_message;

    /**
     * @var array|null
     * @ORM\Column(type="array", nullable=true)
     */
    protected $keywords;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $google_analytics_id;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $google_web_developer;

    /**
     * @var array|null
     * @ORM\Column(type="array", nullable=true)
     */
    protected $links;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true, options={"default": "en-US"})
     */
    protected $language;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $copyright;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Board", mappedBy="forum", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $boards;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
    }

    /**
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string The name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name The name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string The url
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url The url
     *
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return User The owner
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner The owner
     *
     * @return $this
     */
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return bool Whether the forum is closed
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @param bool $closed Whether the forum is closed
     *
     * @return $this
     */
    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * @return string|null The message why the forum was closed
     */
    public function getClosedMessage(): ?string
    {
        return $this->closed_message;
    }

    /**
     * @param string|null $closed_message The message why the forum was closed
     *
     * @return $this
     */
    public function setClosedMessage(string $closed_message = null): self
    {
        $this->closed_message = $closed_message;

        return $this;
    }

    /**
     * @return array|null The keywords
     */
    public function getKeywords(): ?array
    {
        return $this->keywords;
    }

    /**
     * @param string $keyword The keyword
     *
     * @return $this
     */
    public function addKeyword(string $keyword): self
    {
        if (!is_array($this->keywords)) {
            $this->keywords = [];
        }

        $array = new ArrayCollection($this->keywords);
        $array->add($keyword);
        $this->keywords = $array->toArray();

        return $this;
    }

    /**
     * @param string $keyword The keyword
     *
     * @return $this
     */
    public function removeKeyword(string $keyword): self
    {
        if (!is_array($this->keywords)) {
            $this->keywords = [];
        }

        $array = new ArrayCollection($this->keywords);
        if ($array->contains($keyword)) {
            $array->removeElement($keyword);
            $this->keywords = $array->toArray();
        }

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
     * @return string|null The Google Analytics ID
     */
    public function getGoogleAnalyticsId(): ?string
    {
        return $this->google_analytics_id;
    }

    /**
     * @param string|null $id The Google Analytics ID
     *
     * @return $this
     */
    public function setGoogleAnalyticsId(string $id = null): self
    {
        $this->google_analytics_id = $id;

        return $this;
    }

    /**
     * @return string|null The Google Web Developer ID
     */
    public function getGoogleWebDeveloper(): ?string
    {
        return $this->google_web_developer;
    }

    /**
     * @param string|null $google_web_dev The Google Web Developer ID
     *
     * @return $this
     */
    public function setGoogleWebDeveloper(string $google_web_dev = null): self
    {
        $this->google_web_developer = $google_web_dev;

        return $this;
    }

    /**
     * @return array|null The links
     */
    public function getLinks(): ?array
    {
        return $this->links;
    }

    /**
     * @param array|null $links The links
     *
     * @return $this
     */
    public function setLinks(array $links = null): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @return string|null The language
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string|null $language The language
     *
     * @return $this
     */
    public function setLanguage(string $language = null): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string|null The copyright notice
     */
    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    /**
     * @param string|null $copyright The copyright notice
     *
     * @return $this
     */
    public function setCopyright(string $copyright = null): self
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * @return DateTime The creation date
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created The creation date
     *
     * @return $this
     */
    public function setCreated(DateTime $created): self
    {
        $this->created = $created;

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
    public function addBoard(Board $board): self
    {
        $this->boards->add($board);
        $board->setForum($this);

        return $this;
    }

    /**
     * @param Board $board The boards
     *
     * @return $this
     */
    public function removeBoard(Board $board): self
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
            'name' => $this->name,
            'url' => $this->url,
            'owner' => $this->owner,
            'closed' => $this->closed,
            'closed_message' => $this->closed_message,
            'keywords' => $this->keywords,
            'description' => $this->description,
            'google_analytics_id' => $this->google_analytics_id,
            'google_web_developer' => $this->google_web_developer,
            'links' => $this->links,
            'language' => $this->language,
            'copyright' => $this->copyright,
            'created' => $this->created,
        ];
    }
}
