<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'actor')]
#[ORM\Entity]
class Actor
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'name', type: 'string', length: 128, nullable: false)]
    private $name;

    #[ORM\ManyToMany(targetEntity: 'Series', inversedBy: 'actor')]
    #[ORM\JoinTable(
        name: 'actor_series',
        joinColumns: [new ORM\JoinColumn(name: 'actor_id', referencedColumnName: 'id')],
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
     * get the id of the actor.
     *
     * @return int $id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * get the name of the Actor.
     *
     * @return string $name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * set the name of the actor.
     *
     * @return string $name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Series>
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    /**
     * Add series to the array.
     *
     * @param Series|null $series
     */
    public function addSeries(Series $series): self
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
        }

        return $this;
    }

    /**
     * remove series to the array.
     *
     * @param Series|null $series
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
