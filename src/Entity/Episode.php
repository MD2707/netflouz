<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'episode', indexes: [
    new ORM\Index(name: 'IDX_DDAA1CDA4EC001D1', columns: ['season_id']),
])]
#[ORM\Entity]
class Episode
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'title', type: 'string', length: 128, nullable: false)]
    private $title;

    #[ORM\Column(name: 'date', type: 'date', nullable: true)]
    private $date;

    #[ORM\Column(name: 'imdb', type: 'string', length: 128, nullable: false)]
    private $imdb;

    #[ORM\Column(name: 'imdbrating', type: 'float', precision: 10, scale: 0, nullable: true)]
    private $imdbrating;

    #[ORM\Column(name: 'number', type: 'integer', nullable: false)]
    private $number;

    #[ORM\ManyToOne(targetEntity: 'Season', inversedBy: 'episodes')]
    #[ORM\JoinColumn(name: 'season_id', referencedColumnName: 'id')]
    private $season;

    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'episode')]
    private $user = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get the id of the episode.
     *
     * @return int|null the id of the episode
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the title of the episode.
     *
     * @return string|null the title of the episode
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the episode.
     *
     * @param string $title the new title of the episode
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the imdb id of the episode.
     *
     * @return string|null the imdb id of the episode
     */
    public function getImdb(): ?string
    {
        return $this->imdb;
    }

    /**
     * Set the imdb id of the episode.
     *
     * @param string $imdb the new imdb id of the episode
     */
    public function setImdb(string $imdb): self
    {
        $this->imdb = $imdb;

        return $this;
    }

    /**
     * Get the imdb rating of the episode.
     *
     * @return float|null the imdb rating of the episode
     */
    public function getImdbrating(): ?float
    {
        return $this->imdbrating;
    }

    /**
     * Set the imdb rating of the episode.
     *
     * @param float|null $imdbrating the new imdb rating of the episode
     */
    public function setImdbrating(?float $imdbrating): self
    {
        $this->imdbrating = $imdbrating;

        return $this;
    }

    /**
     * Get the number of the episode.
     *
     * @return int|null the number of the episode
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * Set the number of the episode.
     *
     * @param int $number the new number of the episode
     */
    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get the season of the episode.
     *
     * @return Season|null the season of the episode
     */
    public function getSeason(): ?Season
    {
        return $this->season;
    }

    /**
     * Set the season of the episode.
     *
     * @param Season $season the new season of the episode
     */
    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get a collection of users that have watched this episode.
     *
     * @return Collection<int, User> the collection of users that have watched this episode
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    /**
     * Add a user to the collection of users that have watched this episode.
     *
     * @param User $user the user to add to the collection
     */
    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->addEpisode($this);
        }

        return $this;
    }

    /**
     * Remove a user from the collection of users that have watched this episode.
     *
     * @param User $user the user to remove from the collection
     */
    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            $user->removeEpisode($this);
        }

        return $this;
    }

    /**
     * Get the date that the episode was watched.
     *
     * @return \DateTimeInterface|null the date that the episode was watched
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Set the date that the episode was watched.
     *
     * @param \DateTimeInterface|null $date the new date that the episode was watched
     */
    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
