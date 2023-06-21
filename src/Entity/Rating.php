<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(
    name: 'rating',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'unique_rating', columns: ['series_id', 'users_id']),
    ],
    indexes: [
        new ORM\Index(name: 'IDX_D88926225278319C', columns: ['series_id']),
        new ORM\Index(name: 'IDX_D8892622A76ED395', columns: ['user_id']),
    ]
)]
#[ORM\Entity]
class Rating
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'value', type: 'integer', nullable: false)]
    private $value;

    #[ORM\Column(name: 'comment', type: 'text', length: 0, nullable: false)]
    private $comment;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: false)]
    private $date;

    #[ORM\Column(name: 'valide', type: 'boolean', nullable: false)]
    private $valide = '0';

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'rates')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private $user;

    #[ORM\ManyToOne(targetEntity: 'Series', inversedBy: 'rates')]
    #[ORM\JoinColumn(name: 'series_id', referencedColumnName: 'id')]
    private $series;

    /**
     * Get the id of the rating.
     *
     * @return int|null the id of the rating
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of the rating.
     *
     * @return float|null the value of the rating
     */
    public function getVale(): ?float
    {
        return $this->value / 2;
    }

    /**
     * Set the value of the rating.
     *
     * @param int $value the new value of the rating
     */
    public function setValue(int $value): self
    {
        $this->value = $value * 2;

        return $this;
    }

    /**
     * Get the comment of the rating.
     *
     * @return string|null the comment of the rating
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set the comment of the rating.
     *
     * @param string $comment the new comment of the rating
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the date of the rating.
     *
     * @return DateTimeInterface|null the date of the rating
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Set the date of the rating.
     *
     * @param DateTimeInterface $date the new date of the rating
     */
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the user of the rating.
     *
     * @return User|null the user of the rating
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the user associated to the rating.
     *
     * @param User|null $user the new user associated of the rating
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the state 'valide' of the rating.
     *
     * @return bool|null the state of the rating
     */
    public function getValide(): ?bool
    {
        return $this->valide;
    }

    /**
     * Set the state of the rating.
     *
     * @param bool $valide the new state of the rating
     */
    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get the Series of the rating.
     *
     * @return Series|null the series of the rating
     */
    public function getSeries(): ?Series
    {
        return $this->series;
    }

    /**
     * Set the Series of the rating.
     *
     * @param Series $series the new series of the rating
     */
    public function setSeries(?Series $series): self
    {
        $this->series = $series;

        return $this;
    }
}
