<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'series', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_3A10012D85489131', columns: ['imdb']),
])]
#[ORM\Entity]
class Series
{
    /**
     * All attributes are here with some data about type, mapping, name.
     */
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'title', type: 'string', length: 128, nullable: false)]
    private $title;

    #[ORM\Column(name: 'plot', type: 'text', length: 0, nullable: true)]
    private $plot;

    #[ORM\Column(name: 'imdb', type: 'string', length: 128, nullable: false)]
    private $imdb;

    #[ORM\Column(name: 'poster', type: 'blob', length: 0, nullable: true)]
    private $poster;

    #[ORM\Column(name: 'director', type: 'string', length: 128, nullable: true)]
    private $director;

    #[ORM\Column(name: 'youtube_trailer', type: 'string', length: 128, nullable: true)]
    private $youtubeTrailer;

    #[ORM\Column(name: 'awards', type: 'text', length: 0, nullable: true)]
    private $awards;

    #[ORM\Column(name: 'year_start', type: 'integer', nullable: true)]
    private $yearStart;

    #[ORM\Column(name: 'year_end', type: 'integer', nullable: true)]
    private $yearEnd;

    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'series')]
    private $user = [];

    #[ORM\ManyToMany(targetEntity: 'Genre', mappedBy: 'series')]
    private $genre = [];

    #[ORM\ManyToMany(targetEntity: 'Actor', mappedBy: 'series')]
    private $actor = [];

    #[ORM\ManyToMany(targetEntity: 'Country', mappedBy: 'series')]
    private $country = [];

    #[ORM\OneToMany(targetEntity: 'Season', mappedBy: 'series')]
    #[ORM\OrderBy(['number' => 'ASC'])]
    private $seasons;

    #[ORM\OneToMany(targetEntity: 'Rating', mappedBy: 'series')]
    private $rates;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
        $this->genre = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actor = new \Doctrine\Common\Collections\ArrayCollection();
        $this->country = new \Doctrine\Common\Collections\ArrayCollection();
        $this->seasons = new ArrayCollection();
        /* $this->series = new ArrayCollection(); */
        $this->rates = new ArrayCollection();
    }

    /**
     * Getter which return the id of the serie.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter which return the title.
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter which modify the title of the serie.
     *
     * @param string : title
     *
     * @return Serie
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Getter which return the plot of the current serie.
     *
     * @return string
     */
    public function getPlot(): ?string
    {
        return $this->plot;
    }

    /**
     * Setter which modify the plot of the serie.
     *
     * @param string : plot
     *
     * @return Serie
     */
    public function setPlot(?string $plot): self
    {
        $this->plot = $plot;

        return $this;
    }

    /**
     * Getter which return the Imdb of the serie.
     *
     * @return string
     */
    public function getImdb(): ?string
    {
        return $this->imdb;
    }

    /**
     * Setter which modify the Imdb of the serie.
     *
     * @param string : imdb
     *
     * @return Serie
     */
    public function setImdb(string $imdb): self
    {
        $this->imdb = $imdb;

        return $this;
    }

    /**
     * Getter which return the director of the serie.
     *
     * @return string
     */
    public function getDirector(): ?string
    {
        return $this->director;
    }

    /**
     * Setter which modify the director of the serie.
     *
     * @param string : director
     *
     * @return Serie
     */
    public function setDirector(?string $director): self
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Getter which return the Youtube trailer of the serie.
     *
     * @return string
     */
    public function getYoutubeTrailer(): ?string
    {
        $lien = str_replace('watch?v=', 'embed/', $this->youtubeTrailer);

        return $lien;
    }

    /**
     * Setter which modify the Imdb of the serie.
     *
     * @param string : youtubeTrailer
     *
     * @return Serie
     */
    public function setYoutubeTrailer(?string $youtubeTrailer): self
    {
        $this->youtubeTrailer = $youtubeTrailer;

        return $this;
    }

    /**
     * Getter which return the awards of the serie.
     *
     * @return string
     */
    public function getAwards(): ?string
    {
        return $this->awards;
    }

    /**
     * Setter which modify the awards of the serie.
     *
     * @param string : awards
     *
     * @return Serie
     */
    public function setAwards(?string $awards): self
    {
        $this->awards = $awards;

        return $this;
    }

    /**
     * Getter which return the Year start of the serie.
     *
     * @return int
     */
    public function getYearStart(): ?int
    {
        return $this->yearStart;
    }

    /**
     * Setter which modify the yearStart of the serie.
     *
     * @param int : yearStart
     *
     * @return Serie
     */
    public function setYearStart(?int $yearStart): self
    {
        $this->yearStart = $yearStart;

        return $this;
    }

    /**
     * Getter which return the Year end of the serie.
     *
     * @return int
     */
    public function getYearEnd(): ?int
    {
        return $this->yearEnd;
    }

    /**
     * Setter which modify the Year end of the serie.
     *
     * @param int : yearsEnd
     *
     * @return Serie
     */
    public function setYearEnd(?int $yearEnd): self
    {
        $this->yearEnd = $yearEnd;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    /**
     * add a user who follow the serie.
     *
     * @param User : user
     *
     * @return Serie
     */
    public function addUser(User $user): self
    {
        // if the user is not already contained in the array user
        if (!$this->user->contains($user)) {
            // the user is added in the array from the Serie object
            $this->user->add($user);
            // the serie is added in the array from the User object
            $user->addSeries($this);
        }

        return $this;
    }

    /**
     * remove a user from the user array of current serie.
     *
     * @param User : user
     *
     * @return Serie
     */
    public function removeUser(User $user): self
    {
        // if the user is removable
        if ($this->user->removeElement($user)) {
            // we remove it from current serie
            $user->removeSeries($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    /**
     * permit to add genre in the specified array.
     *
     * @param Genre : genre
     *
     * @return Serie
     */
    public function addGenre(Genre $genre): self
    {
        // if the genre is not contained
        if (!$this->genre->contains($genre)) {
            // we add it
            $this->genre->add($genre);
            // adding the serie from the genre (mapping from both side)
            $genre->addSeries($this);
        }

        return $this;
    }

    /**
     * permit to remove genre in the specified array.
     *
     * @param Genre : genre
     *
     * @return Serie
     */
    public function removeGenre(Genre $genre): self
    {
        // if the element is removable
        if ($this->genre->removeElement($genre)) {
            // we remove it
            $genre->removeSeries($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Actor>
     */
    public function getActor(): Collection
    {
        return $this->actor;
    }

    /**
     * permit to add an actor in the specified array.
     *
     * @param Actor : actor
     *
     * @return Serie
     */
    public function addActor(Actor $actor): self
    {
        // if the actor is not contained in the array
        if (!$this->actor->contains($actor)) {
            // we add it
            $this->actor->add($actor);
            // from actor we add the current serie (mapping from both side)
            $actor->addSeries($this);
        }

        return $this;
    }

    /**
     * permit to remove an actor in the specified array.
     *
     * @param Actor : actor
     *
     * @return Serie
     */
    public function removeActor(Actor $actor): self
    {
        // if the element is removable
        if ($this->actor->removeElement($actor)) {
            // then remove it from actor
            $actor->removeSeries($this);
        }

        return $this;
    }

    /**
     * return the country associated to the serie.
     *
     * @return Collection<int, Country>
     */
    public function getCountry(): Collection
    {
        return $this->country;
    }

    /**
     * add a country to the serie.
     */
    public function addCountry(Country $country): self
    {
        // if the country array does not contains the country
        if (!$this->country->contains($country)) {
            // we add it to the current array
            $this->country->add($country);
            // and we add it to the serie array of the country (mapping from both side)
            $country->addSeries($this);
        }

        return $this;
    }

    /**
     * remove a country to the serie.
     */
    public function removeCountry(Country $country): self
    {
        if ($this->country->removeElement($country)) {
            $country->removeSeries($this);
        }

        return $this;
    }

    /**
     * return the poster of the current serie.
     *
     * @return Series
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * modify the poster of the current serie.
     *
     * @return Series $this
     */
    public function setPoster($poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    /**
     * add a season to the collection.
     *
     * @return Collection<int, Season>
     */
    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons->add($season);
            $season->setSeries($this);
        }

        return $this;
    }

    /**
     * add a season to the collection.
     *
     * @return Serie $this
     */
    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getSeries() === $this) {
                $season->setSeries(null);
            }
        }

        return $this;
    }

    /**
     * print in the html code the img element with the corresponding source.
     */
    public function showPoster()
    {
        $poster = stream_get_contents($this->getPoster());
        $poster = base64_encode($poster);
        echo '<img src="data:poster/jpeg;base64,'.$poster.'"/>';
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    /**
     * add a season to the collection.
     *
     * @return Serie
     */
    public function addRate(Rating $rate): self
    {
        if (!$this->rates->contains($rate)) {
            $this->rates->add($rate);
            $rate->setSeries($this);
        }

        return $this;
    }

    /**
     * return the average value of all rates for the current serie.
     *
     * @return int
     */
    public function getAverage()
    {
        // if there is more than 0 rate
        if (count($this->rates) > 0) {
            $avg = 0;
            // for all rate
            foreach ($this->rates as $rate) {
                // get the avg value by calculating it
                $avg = $avg + $rate->getVale();
            }
            // handling the last oepration
            $number = $avg / count($this->rates);
            // handling the printed value
            $formatted_number = number_format($number, 1);

            return $formatted_number;
        }
    }

    /**
     * permit to remove a rate from the associated serie.
     *
     * @return Serie $this
     */
    public function removeRate(Rating $rate): self
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getSeries() === $this) {
                $rate->setSeries(null);
            }
        }

        return $this;
    }

    /**
     * return the last season of the current serie or null.
     *
     * @return Season $this
     */
    public function getLastSeason()
    {
        $last = -1;
        $se = null;
        // we check all season
        foreach ($this->seasons as $season) {
            if ($season->getNumber() > $last) {
                // get the latest season
                $last = $season->getNumber();
                $se = $season;
            }
        }

        return $se;
    }

    /**
     * return the last episode of the season.
     *
     * @return Episode $ep
     */
    public function getLastEpisode(Season $season)
    {
        $last = -1;

        $ep = null;
        foreach ($season->getEpisodes() as $episode) {
            if ($episode->getNumber() > $last) {
                $last = $episode->getNumber();
                $ep = $episode;
            }
        }

        return $ep;
    }

    /**
     * return the first season of the current serie or null.
     *
     * @param Season $season
     *
     * @return Episode $ep
     */
    public function getFirstSeason()
    {
        $last = $this->seasons->first()->getNumber();
        $se = null;
        foreach ($this->seasons as $season) {
            if ($season->getNumber() <= $last) {
                $last = $season->getNumber();
                $se = $season;
            }
        }

        return $se;
    }

    /**
     * return the first episode of the choosen season.
     *
     * @return Episode $ep
     */
    public function getFirstEpisode(Season $season)
    {
        // we get the number of the first episode of the choosen season
        $last = $season->getEpisodes()->first()->getNumber();
        $ep = null;
        foreach ($season->getEpisodes() as $episode) {
            // get the last episode
            if ($episode->getNumber() <= $last) {
                $last = $episode->getNumber();
                $ep = $episode;
            }
        }

        return $ep;
    }

    /**
     * return the number of rates of this serie.
     *
     * @return int
     */
    public function getNumberRates()
    {
        return count($this->getRates());
    }

    /**
     * return the current state of the serie.
     *
     * @return string
     */
    public function followSeries(User $user)
    {
        $lastSeason = $this->getLastSeason();
        $lastEpisode = $this->getLastEpisode($lastSeason);
        $firstSeason = $this->getFirstSeason();
        $firstEpisode = $this->getFirstEpisode($firstSeason);
        // if the user have watched the last episode
        if ($lastEpisode->getUser()->contains($user)) {
            return 'Watched';
        }
        // if the first episode is watched, the user "start" the serie
        if ($firstEpisode->getUser()->contains($user)) {
            return 'En cours';
        }
        // other case : to see
        return 'to see';
    }
}
