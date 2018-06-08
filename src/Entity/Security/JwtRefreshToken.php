<?php

namespace App\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("jwt_refresh_tokens")
 * @ORM\Entity(repositoryClass="App\Repository\Security\JwtRefreshTokenRepository")
 * @UniqueEntity("refreshToken")
 */
class JwtRefreshToken implements RefreshTokenInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="refresh_token", type="string", length=128, unique=true)
     * @Assert\NotBlank()
     * @var string
     */
    protected $refreshToken;

    /**
     * @ORM\Column(name="username", type="string", length=255)
     * @Assert\NotBlank()
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(name="valid", type="datetime")
     * @Assert\NotBlank()
     * @var \DateTime
     */
    protected $valid;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @var \DateTime
     */
    protected $createdOn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $updatedOn;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @var string
     */
    protected $operatingSystem = 'Other';

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @var string
     */
    protected $browser = 'Other';

    /**
     * JwtRefreshToken constructor.
     */
    public function __construct()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getValid(): \DateTime
    {
        return $this->valid;
    }

    /**
     * @param \DateTime $valid
     * @return JwtRefreshToken
     */
    public function setValid($valid): JwtRefreshToken
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param $username
     * @return JwtRefreshToken
     */
    public function setUsername($username): JwtRefreshToken
    {
        $this->username = $username;

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
     * @return JwtRefreshToken
     */
    public function setCreatedOn(\DateTime $createdOn): JwtRefreshToken
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedOn(): ?\DateTime
    {
        return $this->updatedOn;
    }

    /**
     * @return JwtRefreshToken
     */
    public function setUpdatedOn(): JwtRefreshToken
    {
        $this->updatedOn = new \DateTime();

        return $this;
    }

    /**
     * @return string
     */
    public function getOperatingSystem(): string
    {
        return $this->operatingSystem;
    }

    /**
     * @param null|string $operatingSystem
     * @return JwtRefreshToken
     */
    public function setOperatingSystem(?string $operatingSystem): JwtRefreshToken
    {
        if ($this->operatingSystem !== null) {
            $this->operatingSystem = $operatingSystem;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getBrowser(): string
    {
        return $this->browser;
    }

    /**
     * @param null|string $browser
     * @return JwtRefreshToken
     */
    public function setBrowser(?string $browser): JwtRefreshToken
    {
        if ($this->operatingSystem !== null) {
            $this->browser = $browser;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $datetime = new \DateTime();

        return $this->valid >= $datetime;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getRefreshToken();
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param null $refreshToken
     * @return JwtRefreshToken
     * @throws \Exception
     */
    public function setRefreshToken($refreshToken = null): JwtRefreshToken
    {
        if ($refreshToken === null) {
            $this->refreshToken = bin2hex(random_bytes(64));
        } else {
            $this->refreshToken = $refreshToken;
        }

        return $this;
    }
}