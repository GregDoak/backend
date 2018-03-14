<?php

namespace App\Entity\Security;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Security\GroupRepository")
 * @ORM\Table(name="groups",indexes={@ORM\Index(columns={"title"})})
 * @UniqueEntity(fields="title", message=GROUP_UNIQUE_ENTITY_ERROR)
 */
class Group
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
     * @Assert\NotNull(message=GROUP_TITLE_EMPTY_ERROR)
     * @Assert\NotBlank(message=GROUP_TITLE_EMPTY_ERROR)
     * @Assert\Length(
     *      min="1",
     *      max="255",
     *      minMessage=GROUP_TITLE_MIN_LENGTH_ERROR,
     *      maxMessage=GROUP_TITLE_MAX_LENGTH_ERROR
     * )
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=1024)
     * @Assert\NotNull(message=GROUP_DESCRIPTION_EMPTY_ERROR)
     * @Assert\NotBlank(message=GROUP_DESCRIPTION_EMPTY_ERROR)
     * @Assert\Length(
     *      min="1",
     *      max="1024",
     *      minMessage=GROUP_DESCRIPTION_MIN_LENGTH_ERROR,
     *      maxMessage=GROUP_DESCRIPTION_MAX_LENGTH_ERROR
     * )
     * @var string
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Security\Role", cascade={"persist"})
     * @ORM\JoinTable(name="groups_roles",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     * @var ArrayCollection
     */
    protected $roles;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Assert\NotNull(message=GROUP_CREATED_BY_EMPTY_ERROR)
     * @Assert\NotBlank(message=GROUP_CREATED_BY_EMPTY_ERROR)
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
        $this->roles = new ArrayCollection();
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
     * @return Group
     */
    public function setTitle(string $title): Group
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
     * @param string $description
     * @return Group
     */
    public function setDescription(string $description): Group
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->getRolesCollection() as $role) {
            $roles[] = $role->getTitle();
        }

        return $roles;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getRolesCollection()
    {
        return $this->roles;
    }

    /**
     * @param Role $role
     * @return Group
     */
    public function setRole(Role $role): Group
    {
        if ( ! $this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @return Group
     */
    public function clearRoles(): Group
    {
        $this->roles->clear();

        return $this;
    }

    /**
     * @param Role $role
     * @return Group
     */
    public function removeRole(Role $role): Group
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

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
     * @return Group
     */
    public function setCreatedBy(User $createdBy): Group
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
     * @return Group
     */
    public function setUpdatedOn(): Group
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
     * @return Group
     */
    public function setUpdatedBy(User $updatedBy): Group
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

}
