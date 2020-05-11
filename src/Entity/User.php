<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table("users")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", name="last_name")
     */
    private ?string $lastName = null;

    /**
     * @ORM\Column(type="string", name="first_name")
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active = true;

    /**
     * @ORM\Column(type="boolean", name="force_renew_password")
     */
    private bool $forceRenewPassword = false;

    /**
     * @ORM\Column(type="datetime", name="blocked_at", nullable=true)
     */
    private ?\DateTime $blockedAt;

    /**
     * @ORM\Column(type="string", name="blocked_by", nullable=true)
     */
    private ?string $blockedBy;

    /**
     * @ORM\Column(type="string", name="blocked_for", nullable=true)
     */
    private ?string $blockedFor;

    /**
     * @ORM\Column(type="integer", name="try_to_connect", options={"default" = 0})
     */
    private int $tryToConnect = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $salt;

    /**
     * @ORM\Column(type="datetime", name="renew_at", nullable=true)
     */
    private ?\DateTime $renewAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="users")
     */
    private Site $site;

    use TimestampableTrait;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return User
     */
    public function setPassword(?string $password): User
    {
        $this->password = $password ?? $this->getPassword();

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param bool $active
     *
     * @return User
     */
    public function setActive(bool $active): User
    {
        $this->active = $active;

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
     * @param bool $forceRenewPassword
     *
     * @return User
     */
    public function setForceRenewPassword(bool $forceRenewPassword): User
    {
        $this->forceRenewPassword = $forceRenewPassword;

        return $this;
    }

    /**
     * @return bool
     */
    public function isForceRenewPassword(): bool
    {
        return $this->forceRenewPassword;
    }

    /**
     * @return \DateTime|null
     */
    public function getBlockedAt(): ?\DateTime
    {
        return $this->blockedAt;
    }

    /**
     * @param \DateTime $blockedAt
     *
     * @return User
     */
    public function setBlockedAt(\DateTime $blockedAt): User
    {
        $this->blockedAt = $blockedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockedBy(): string
    {
        return $this->blockedBy;
    }

    /**
     * @param string $blockedBy
     *
     * @return User
     */
    public function setBlockedBy(string $blockedBy): User
    {
        $this->blockedBy = $blockedBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockedFor(): string
    {
        return $this->blockedFor;
    }

    /**
     * @param string $blockedFor
     *
     * @return User
     */
    public function setBlockedFor(string $blockedFor): User
    {
        $this->blockedFor = $blockedFor;

        return $this;
    }

    /**
     * @return int
     */
    public function getTryToConnect(): int
    {
        return $this->tryToConnect;
    }

    /**
     * @param int $tryToConnect
     *
     * @return User
     */
    public function setTryToConnect(int $tryToConnect): User
    {
        $this->tryToConnect = $tryToConnect;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt ?? null;
    }

    /**
     * @param string $salt
     *
     * @return User
     */
    public function setSalt(string $salt): User
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRenewAt(): ?\DateTime
    {
        return $this->renewAt ?? null;
    }

    /**
     * @param \DateTime $renewAt
     *
     * @return User
     */
    public function setRenewAt(?\DateTime $renewAt): User
    {
        $this->renewAt = $renewAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    /**
     * @return $this
     */
    public function generateSalt(): User
    {
        $this->salt = uniqid('', true);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /**
     * @return User
     */
    public function upTryToConnect(): User
    {
        ++$this->tryToConnect;

        return $this;
    }

    /**
     * @param array $aPRO_CODE
     *
     * @return bool
     */
    public function hasRule(array $aPRO_CODE): bool
    {
        if ($this->isRoot()) {
            return true;
        }

        foreach ($aPRO_CODE as $PRO_CODE) {
            if (in_array($PRO_CODE, $this->getRoles(), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        if (is_array($this->getRoles())) {
            return in_array('ROLE_SUPER_ADMIN', $this->getRoles(), true);
        }
    }
}
