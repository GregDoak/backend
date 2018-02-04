<?php

namespace App\Entity\Lookup;

use App\Entity\Security\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Lookup\TitleRepository")
 * @ORM\Table(name="titles",indexes={@ORM\Index(columns={"title"})})
 * @UniqueEntity(fields="title", message="Title {{ value }} already exists in the database.")
 */
class Title
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="The title is a required field and cannot be empty.")
     * @Assert\Length(
     *      min="1",
     *      max="255",
     *      minMessage="The title must be at least {{ limit }} characters long.",
     *      maxMessage="The title cannot be longer than {{ limit }} characters."
     * )
     * @var string
     */
    protected $title;

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
     * @return Title
     */
    public function setTitle($title): Title
    {
        $this->title = $title;

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
     * @return Title
     */
    public function setCreatedBy(User $createdBy): Title
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
     * @return Title
     */
    public function setUpdatedOn(): Title
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
     * @return Title
     */
    public function setUpdatedBy(User $updatedBy): Title
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

}
