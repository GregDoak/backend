<?php

namespace App\Entity\Security;

use App\Entity\Personal\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Security\UserRepository")
 * @ORM\Table(name="users",indexes={@ORM\Index(columns={"username"})})
 * @UniqueEntity(fields="username", message=USER_UNIQUE_ENTITY_ERROR)
 */
class User implements UserInterface
{
    /**
     * @var \Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken[]
     */
    public $tokens;
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
     * @Assert\NotNull(message=USER_USERNAME_EMPTY_ERROR)
     * @Assert\NotBlank(message=USER_USERNAME_EMPTY_ERROR)
     * @Assert\Length(
     *      min="1",
     *      max="255",
     *      minMessage=USER_USERNAME_MIN_LENGTH_ERROR,
     *      maxMessage=USER_USERNAME_MAX_LENGTH_ERROR
     * )
     * @var string
     */
    protected $username;
    /**
     * @Assert\NotNull(message=USER_PASSWORD_EMPTY_ERROR)
     * @Assert\NotBlank(message=USER_PASSWORD_EMPTY_ERROR)
     * @Assert\Length(
     *      min="8",
     *      max="255",
     *      minMessage=USER_PASSWORD_MIN_LENGTH_ERROR,
     *      maxMessage=USER_PASSWORD_MAX_LENGTH_ERROR
     * )
     * @var string
     */
    protected $plainPassword;
    /**
     * @JMS\Exclude()
     * @ORM\Column(type="string", length=1024)
     * @var string
     */
    protected $password;


    protected $passwordLastChanged;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personal\Person", cascade={"persist"})
     * @ORM\JoinColumn(name="person", referencedColumnName="id", nullable=true)
     * @var Person
     */
    protected $person;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $lastLogin;
    /**
     * @ORM\Column(type="integer", length=12)
     * @var integer
     */
    protected $loginCount;
    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    protected $enabled;
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdOn;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Assert\NotNull(message=USER_CREATED_BY_EMPTY_ERROR)
     * @Assert\NotBlank(message=USER_CREATED_BY_EMPTY_ERROR)
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
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Security\Role", cascade={"persist"})
     * @ORM\JoinTable(name="users_roles",
     *      joinColumns={@ORM\JoinColumn(name="user", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="id")}
     * )
     * @var ArrayCollection
     */
    protected $roles;

    public function __construct()
    {
        $this->passwordLastChanged = new \DateTime();
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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return User
     */
    public function setUsername($username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $password
     * @return User
     */
    public function setPlainPassword($password): User
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordLastChanged(): \DateTime
    {
        return $this->passwordLastChanged;
    }

    /**
     * @param \DateTime $passwordLastChanged
     * @return User
     */
    public function setPasswordLastChanged(\DateTime $passwordLastChanged): User
    {
        $this->passwordLastChanged = $passwordLastChanged;

        return $this;
    }

    /**
     * @return null|Person
     */
    public function getPerson(): ?Person
    {
        return $this->person;
    }

    /**
     * @param Person $person
     * @return User
     */
    public function setPerson(Person $person): User
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin(): \DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @return User
     */
    public function setLastLogin(): User
    {
        $this->lastLogin = new \DateTime();

        return $this;
    }

    /**
     * @return int
     */
    public function getLoginCount(): int
    {
        return $this->loginCount;
    }

    /**
     * @return User
     */
    public function setLoginCount(): User
    {
        $this->loginCount = $this->loginCount === null ? 0 : $this->loginCount + 1;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return User
     */
    public function setEnabled($enabled): User
    {
        $this->enabled = $enabled;

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
     * @return User
     */
    public function setCreatedBy(User $createdBy): User
    {
        $this->createdBy = $createdBy;

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
     * @return User
     */
    public function setUpdatedBy(User $updatedBy): User
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return User
     */
    public function setUpdatedOn(): User
    {
        $this->updatedOn = new \DateTime();

        return $this;
    }

    /**
     * @param Role $role
     * @return User
     */
    public function setRole(Role $role): User
    {
        if ( ! $this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @return User
     */
    public function clearRoles(): User
    {
        $this->roles->clear();

        return $this;
    }

    /**
     * @param Role $role
     * @return User
     */
    public function removeRole(Role $role): User
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    /**
     * @return \Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @param \Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken[] $tokens
     * @return User
     */
    public function setTokens($tokens): User
    {
        $this->tokens = $tokens;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAuthorised(): bool
    {
        $roles = $this->getRoles();

        return
            \in_array('ROLE_USER', $roles, true) ||
            \in_array('ROLE_ADMIN', $roles, true) ||
            \in_array('ROLE_SUPER_ADMIN', $roles, true);
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

    public function getSalt(): void
    {
    }

    public function eraseCredentials(): void
    {
    }

}
