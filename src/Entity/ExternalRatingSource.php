<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'external_rating_source')]
#[ORM\Entity]
class ExternalRatingSource
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private $name;

    /**
     * Get the id of the External Rating Source.
     *
     * @return int|null the id of the External Rating Source
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name of the External Rating Source.
     *
     * @return string|null the name of the External Rating Source
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the External Rating Source.
     *
     * @param string the new name of the External Rating Source
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
