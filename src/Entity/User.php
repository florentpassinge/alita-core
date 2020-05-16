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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

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

    public function setPassword(?string $password): self
    {
        $this->password = $password ?? $this->getPassword();

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setForceRenewPassword(bool $forceRenewPassword): self
    {
        $this->forceRenewPassword = $forceRenewPassword;

        return $this;
    }

    public function isForceRenewPassword(): bool
    {
        return $this->forceRenewPassword;
    }

    public function getBlockedAt(): ?\DateTime
    {
        return $this->blockedAt;
    }

    public function setBlockedAt(\DateTime $blockedAt): self
    {
        $this->blockedAt = $blockedAt;

        return $this;
    }

    public function getBlockedBy(): string
    {
        return $this->blockedBy;
    }

    public function setBlockedBy(string $blockedBy): self
    {
        $this->blockedBy = $blockedBy;

        return $this;
    }

    public function getBlockedFor(): string
    {
        return $this->blockedFor;
    }

    public function setBlockedFor(string $blockedFor): self
    {
        $this->blockedFor = $blockedFor;

        return $this;
    }

    public function getTryToConnect(): int
    {
        return $this->tryToConnect;
    }

    public function setTryToConnect(int $tryToConnect): self
    {
        $this->tryToConnect = $tryToConnect;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt ?? null;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getRenewAt(): ?\DateTime
    {
        return $this->renewAt ?? null;
    }

    public function setRenewAt(?\DateTime $renewAt): self
    {
        $this->renewAt = $renewAt;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function generateSalt(): self
    {
        $this->salt = uniqid('', true);

        return $this;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getName(): string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function upTryToConnect(): self
    {
        ++$this->tryToConnect;

        return $this;
    }

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

    public function isRoot(): bool
    {
        if (is_array($this->getRoles())) {
            return in_array('ROLE_SUPER_ADMIN', $this->getRoles(), true);
        }
    }
}
