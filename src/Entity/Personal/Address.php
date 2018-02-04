<?php

namespace App\Entity\Personal;

use App\Entity\Security\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Personal\AddressRepository")
 * @ORM\Table(name="addresses")
 */
class Address
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
     * @ORM\Column(type="string", length=40)
     * @Assert\NotNull(message="The first line is a required field and cannot be empty.")
     * @Assert\Length(
     *      min="1",
     *      max="40",
     *      minMessage="The first line must be at least {{ limit }} characters long.",
     *      maxMessage="The first line cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $line1;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     * @Assert\Length(
     *      max="40",
     *      maxMessage="The second line cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $line2;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     * @Assert\Length(
     *      max="40",
     *      maxMessage="The third line cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $line3;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotNull(message="The city is a required field and cannot be empty.")
     * @Assert\Length(
     *      min="1",
     *      max="40",
     *      minMessage="The city must be at least {{ limit }} characters long.",
     *      maxMessage="The city cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     * @Assert\Length(
     *      max="40",
     *      maxMessage="The third line cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $county;

    /**
     * @ORM\Column(type="string", length=3)
     * @Assert\NotNull(message="The country is a required field and cannot be empty.")
     * @Assert\Length(
     *      min="1",
     *      max="40",
     *      minMessage="The country must be at least {{ limit }} characters long.",
     *      maxMessage="The country cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotNull(message="The postcode is a required field and cannot be empty.")
     * @Assert\Length(
     *      min="1",
     *      max="40",
     *      minMessage="The postcode must be at least {{ limit }} characters long.",
     *      maxMessage="The postcode cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $postcode;

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
    public function getLine1(): string
    {
        return $this->line1;
    }

    /**
     * @param string $line1
     * @return Address
     */
    public function setLine1(string $line1): Address
    {
        $this->line1 = $line1;

        return $this;
    }

    /**
     * @return string
     */
    public function getLine2(): string
    {
        return $this->line2;
    }

    /**
     * @param string $line2
     * @return Address
     */
    public function setLine2(string $line2): Address
    {
        $this->line2 = $line2;

        return $this;
    }

    /**
     * @return string
     */
    public function getLine3(): string
    {
        return $this->line3;
    }

    /**
     * @param string $line3
     * @return Address
     */
    public function setLine3(string $line3): Address
    {
        $this->line3 = $line3;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Address
     */
    public function setCity(string $city): Address
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCounty(): string
    {
        return $this->county;
    }

    /**
     * @param string $county
     * @return Address
     */
    public function setCounty(string $county): Address
    {
        $this->county = $county;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Address
     */
    public function setCountry(string $country): Address
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     * @return Address
     */
    public function setPostcode(string $postcode): Address
    {
        $this->postcode = $postcode;

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
     * @return Address
     */
    public function setCreatedBy(User $createdBy): Address
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
     * @return Address
     */
    public function setUpdatedOn(): Address
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
     * @return Address
     */
    public function setUpdatedBy(User $updatedBy): Address
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

}
