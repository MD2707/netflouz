<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'user', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_8D93D649E7927C74', columns: ['email']),
    new ORM\UniqueConstraint(name: 'IDX_8D93D649F92F3E70', columns: ['country_id']),
])]
#[ORM\Entity]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'name', type: 'string', length: 128, nullable: false)]
    private $name;

    /**
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|fr)$/",
     *     message="Email doit être valide (exemple: nom@domaine.com)"
     * )
     */
    #[ORM\Column(name: 'email', type: 'string', length: 128, nullable: false)]
    private $email;

    #[ORM\Column(name: 'password', type: 'string', length: 128, nullable: false)]
    private $password;

    #[ORM\Column(name: 'register_date', type: 'datetime', nullable: true)]
    private $registerDate;

    #[ORM\Column(name: 'admin', type: 'boolean', nullable: false)]
    private $admin = '0';

    #[ORM\Column(name: 'ban', type: 'boolean', nullable: false)]
    private $ban = '0';

    #[ORM\Column(name: 'user_id', type: 'string', length: 128, nullable: true)]
    private $userId;

    #[ORM\OneToMany(targetEntity: 'Rating', mappedBy: 'user')]
    private $rates;

    #[ORM\ManyToOne(targetEntity: 'Country')]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    private $country;

    #[ORM\ManyToMany(targetEntity: 'Series', inversedBy: 'genre')]
    #[ORM\JoinTable(
        name: 'user_series',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'series_id', referencedColumnName: 'id')]
    )]
    private $series = [];

    #[ORM\ManyToMany(targetEntity: 'Episode', inversedBy: 'genre')]
    #[ORM\JoinTable(
        name: 'user_episode',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'episode_id', referencedColumnName: 'id')]
    )]
    private $episode = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->series = new \Doctrine\Common\Collections\ArrayCollection();
        $this->episode = new \Doctrine\Common\Collections\ArrayCollection();
        $this->registerDate = new \DateTime();
        $this->rates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    public function setRegisterDate($registerDate): self
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getBan(): ?bool
    {
        return $this->ban;
    }

    public function setBan(bool $ban): self
    {
        $this->ban = $ban;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, Series>
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Series $series): self
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
        }

        return $this;
    }

    public function removeSeries(Series $series): self
    {
        $this->series->removeElement($series);

        return $this;
    }

    /**
     * @return Collection<int, Episode>
     */
    public function getEpisode(): Collection
    {
        return $this->episode;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episode->contains($episode)) {
            $this->episode->add($episode);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        $this->episode->removeElement($episode);

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getRoles(): array
    {
        if ($this->isAdmin()) {
            return ['ROLE_ADMIN'];
        }

        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    public function addRate(Rating $rate): self
    {
        if (!$this->rates->contains($rate)) {
            $this->rates->add($rate);
            $rate->setUser($this);
        }

        return $this;
    }

    public function removeRate(Rating $rate): self
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getUser() === $this) {
                $rate->setUser(null);
            }
        }

        return $this;
    }
}
