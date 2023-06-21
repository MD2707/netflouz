<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'genre')]
class Genre
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'name', type: 'string', length: 128, nullable: false)]
    private $name;

    #[ORM\ManyToMany(targetEntity: 'Series', inversedBy: 'genre')]
    #[ORM\JoinTable(
        name: 'genre_series',
        joinColumns: [new ORM\JoinColumn(name: 'genre_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'series_id', referencedColumnName: 'id')]
    )]
    private $series = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->series = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get the id of the Genre.
     *
     * @return int|null the id of the Genre
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name of the Genre.
     *
     * @return string|null the name of the Genre
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the Genre.
     *
     * @param string $name the new name of the Genre
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the series of the Genre.
     *
     * @return Collection the series of the Genre
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    /**
     * Add a series to the Genre.
     *
     * @param Series $series the series to add to the Genre
     */
    public function addSeries(Series $series): self
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
        }

        return $this;
    }

    /**
     * Remove a series from the Genre.
     *
     * @param Series $series the series to remove from the Genre
     */
    public function removeSeries(Series $series): self
    {
        $this->series->removeElement($series);

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
