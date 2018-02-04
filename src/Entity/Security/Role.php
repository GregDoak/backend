<?php

namespace App\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Security\RoleRepository")
 * @ORM\Table(name="roles",indexes={@ORM\Index(columns={"title"})})
 * @UniqueEntity(fields="title", message=ROLE_UNIQUE_ENTITY_ERROR)
 */
class Role
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message=ROLE_TITLE_EMPTY_ERROR)
     * @Assert\NotBlank(message=ROLE_TITLE_EMPTY_ERROR)
     * @Assert\Length(
     *      min="2",
     *      max="255",
     *      minMessage=ROLE_TITLE_MIN_LENGTH_ERROR,
     *      maxMessage=ROLE_TITLE_MAX_LENGTH_ERROR
     * )
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=1024)
     * @Assert\NotNull(message=ROLE_DESCRIPTION_EMPTY_ERROR)
     * @Assert\NotBlank(message=ROLE_DESCRIPTION_EMPTY_ERROR)
     * @Assert\Length(
     *      min="5",
     *      max="1024",
     *      minMessage=ROLE_DESCRIPTION_MIN_LENGTH_ERROR,
     *      maxMessage=ROLE_DESCRIPTION_MAX_LENGTH_ERROR
     * )
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Assert\NotNull(message=ROLE_CREATED_BY_EMPTY_ERROR)
     * @Assert\NotBlank(message=ROLE_CREATED_BY_EMPTY_ERROR)
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
     * @param string|null $title
     * @return Role
     */
    public function setTitle(string $title = null): Role
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Role
     */
    public function setDescription(string $description = null): Role
    {
        $this->description = $description;

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
     * @return Role
     */
    public function setCreatedBy(User $createdBy): Role
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
     * @return Role
     */
    public function setUpdatedOn(): Role
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
     * @return Role
     */
    public function setUpdatedBy(User $updatedBy): Role
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

}
