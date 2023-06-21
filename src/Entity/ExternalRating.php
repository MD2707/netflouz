<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'external_rating', indexes: [
    new ORM\Index(name: 'IDX_AC0AB9CB953C1C61', columns: ['source_id']),
    new ORM\Index(name: 'IDX_AC0AB9CB5278319C', columns: ['series_id']),
])]
#[ORM\Entity]
class ExternalRating
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'value', type: 'string', length: 255, nullable: false)]
    private $value;

    #[ORM\Column(name: 'votes', type: 'integer', nullable: true)]
    private $votes;

    #[ORM\ManyToOne(targetEntity: 'ExternalRatingSource')]
    #[ORM\JoinColumn(name: 'source_id', referencedColumnName: 'id')]
    private $source;

    #[ORM\ManyToOne(targetEntity: 'Series')]
    #[ORM\JoinColumn(name: 'series_id', referencedColumnName: 'id')]
    private $series;

    /**
     * Get the id of the Externalrating.
     *
     * @return int|null the id of the Externalrating
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of the Externalrating.
     *
     * @return string|null the value of the Externalrating
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Set the value of the Externalrating.
     *
     * @param string $value the new value of the Externalrating
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the votes of the Externalrating.
     *
     * @return int|null the votes of the Externalrating
     */
    public function getVotes(): ?int
    {
        return $this->votes;
    }

    /**
     * Set the votes of the Externalrating.
     *
     * @param int|null $votes the new votes of the Externalrating
     */
    public function setVotes(?int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get the source of the Externalrating.
     *
     * @return ExternalRatingSource|null the source of the Externalrating
     */
    public function getSource(): ?ExternalRatingSource
    {
        return $this->source;
    }

    /**
     * Set the source of the Externalrating.
     *
     * @param ExternalRatingSource|null $source the new source of the Externalrating
     */
    public function setSource(?ExternalRatingSource $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the series of the Externalrating.
     *
     * @return Series|null the series of the Externalrating
     */
    public function getSeries(): ?Series
    {
        return $this->series;
    }

    /**
     * Set the series of the Externalrating.
     *
     * @param Series|null $series the new series of the Externalrating
     */
    public function setSeries(?Series $series): self
    {
        $this->series = $series;

        return $this;
    }
}
