<?php

namespace App\Entity\My;

use App\Entity\Security\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\My\EventRepository")
 * @ORM\Table(name="events",indexes={@ORM\Index(columns={"start_date_time", "end_date_time", "created_by"})})
 **/
class Event // NOSONAR
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Assert\Uuid()
     * @var string
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=1024)
     * @Assert\NotNull(message=EVENT_DESCRIPTION_EMPTY_ERROR)
     * @Assert\NotBlank(message=EVENT_DESCRIPTION_EMPTY_ERROR)
     * @Assert\Length(
     *      min="1",
     *      max="1024",
     *      minMessage=EVENT_DESCRIPTION_MIN_LENGTH_ERROR,
     *      maxMessage=EVENT_DESCRIPTION_MAX_LENGTH_ERROR
     * )
     * @var string
     */
    protected $description;
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(message=EVENT_START_EMPTY_ERROR)
     * @Assert\NotBlank(message=EVENT_START_EMPTY_ERROR)
     * @Assert\Type("\DateTime")
     * @var \DateTime
     */
    protected $startDateTime;
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(message=EVENT_END_EMPTY_ERROR)
     * @Assert\NotBlank(message=EVENT_END_EMPTY_ERROR)
     * @Assert\GreaterThanOrEqual(propertyPath="start_datetime", message=EVENT_END_GREATER_ERROR)
     * @Assert\Type("\DateTime")
     * @var \DateTime
     */
    protected $endDateTime;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Assert\NotNull(message=GROUP_CREATED_BY_EMPTY_ERROR)
     * @Assert\NotBlank(message=GROUP_CREATED_BY_EMPTY_ERROR)
     * @var User
     */
    protected $createdBy;
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdOn;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
     * @var User
     */
    protected $updatedBy;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $updatedOn;
    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    protected $active;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinTable(name="events_users",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     * @var ArrayCollection
     */
    protected $users;

    public function __construct()
    {
        $this->createdOn = new \DateTime();
        $this->users = new ArrayCollection();
        $this->active = true;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Event
     */
    public function setDescription(string $description = null): Event
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDateTime(): ?\DateTime
    {
        return $this->startDateTime;
    }

    /**
     * @param \DateTime|null $startDateTime
     * @return Event
     */
    public function setStartDateTime(\DateTime $startDateTime = null): Event
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDateTime(): ?\DateTime
    {
        return $this->endDateTime;
    }

    /**
     * @param \DateTime|null $endDateTime
     * @return Event
     */
    public function setEndDateTime(\DateTime $endDateTime = null): Event
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn(): \DateTime
    {
        return $this->createdOn;
    }

    /**
     * @param \DateTime $createdOn
     * @return Event
     */
    public function setCreatedOn(\DateTime $createdOn): Event
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @return User
     */
    public function getUpdatedBy(): User
    {
        return $this->updatedBy;
    }

    /**
     * @param User $updatedBy
     * @return Event
     */
    public function setUpdatedBy(User $updatedBy): Event
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedOn(): \DateTime
    {
        return $this->updatedOn;
    }

    /**
     * @return Event
     */
    public function setUpdatedOn(): Event
    {
        $this->updatedOn = new \DateTime();

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Event
     */
    public function setActive(bool $active): Event
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        $usernames = [];
        foreach ($this->getUsersCollection() as $user) {
            /** @var User $user */
            $usernames[] = $user->getUsername();
        }

        return $usernames;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsersCollection(): ArrayCollection
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return Event
     */
    public function setUser(User $user): Event
    {
        if ( ! $this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * @return Event
     */
    public function clearUsers(): Event
    {
        $this->users->clear();

        return $this;
    }

    /**
     * @param User $user
     * @return Event
     */
    public function removeUser(User $user): Event
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isCreator(User $user): bool
    {
        return $user && $user->getId() === $this->getCreatedBy()->getId();
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     * @return Event
     */
    public function setCreatedBy(User $createdBy): Event
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}