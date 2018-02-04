<?php

namespace App\Entity\Personal;

use App\Entity\Lookup\Gender;
use App\Entity\Lookup\Title;
use App\Entity\Security\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Personal\PersonRepository")
 * @ORM\Table(name="persons",indexes={@ORM\Index(columns={"first_name", "last_name", "date_of_birth"})})
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     * @Assert\Uuid()
     * @var string
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lookup\Title", cascade={"persist"})
     * @ORM\JoinColumn(name="title", referencedColumnName="id")
     * @Assert\NotNull(message="The title is a required field and cannot be empty.")
     * @var Title
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="The first name is a required field and cannot be empty.")
     * @Assert\Length(
     *      min="1",
     *      max="255",
     *      minMessage="The first name must be at least {{ limit }} characters long.",
     *      maxMessage="The first name cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255",
     *      maxMessage="The title cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $middleName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="The last name is a required field and cannot be empty.")
     * @Assert\Length(
     *      min="1",
     *      max="255",
     *      minMessage="The last name must be at least {{ limit }} characters long.",
     *      maxMessage="The last name cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $lastName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lookup\Gender", cascade={"persist"})
     * @ORM\JoinColumn(name="gender", referencedColumnName="id", nullable=true)
     * @var Gender
     */
    protected $gender;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $dateOfBirth;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personal\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address", referencedColumnName="id", nullable=true)
     * @var Address
     */
    protected $address;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Assert\NotNull(message="The Created By is a required field and cannot be empty.")
     * @var User
     */
    protected $createdBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $updatedOn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
     * @var User
     */
    protected $updatedBy;

    public function __construct()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Person
     */
    public function setTitle(string $title): Person
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     * @return Person
     */
    public function setMiddleName(string $middleName): Person
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return Gender
     */
    public function getGender(): Gender
    {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     * @return Person
     */
    public function setGender(Gender $gender): Person
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return Person
     */
    public function setAddress(Address $address): Person
    {
        $this->address = $address;

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
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     * @return Person
     */
    public function setCreatedBy(User $createdBy): Person
    {
        $this->createdBy = $createdBy;

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
     * @return Person
     */
    public function setUpdatedOn(): Person
    {
        $this->updatedOn = new \DateTime();

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
     * @return Person
     */
    public function setUpdatedBy(User $updatedBy): Person
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Person
     */
    public function setFirstName(string $firstName): Person
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Person
     */
    public function setLastName(string $lastName): Person
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAge(): int
    {
        $now = new \DateTime();

        return $now->diff($this->getDateOfBirth())->y;

    }

    /**
     * @return \DateTime
     */
    public function getDateOfBirth(): \DateTime
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \DateTime $dateOfBirth
     * @return Person
     */
    public function setDateOfBirth(\DateTime $dateOfBirth): Person
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

}
