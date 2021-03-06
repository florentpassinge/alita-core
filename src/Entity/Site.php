<?php

declare(strict_types = 1);

namespace Alita\Entity;

use Alita\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("sites")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Site
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $metadescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $facebook;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $twitter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $linkedin;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $footer_description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $footer_hours;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $phone;

    /**
     * @ORM\OneToMany(targetEntity="Alita\Entity\User", mappedBy="site")
     */
    private Collection $users;

    use TimestampableTrait;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Site
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Site
    {
        $this->title = $title;

        return $this;
    }

    public function getMetadescription(): ?string
    {
        return $this->metadescription;
    }

    public function setMetadescription(string $metadescription): Site
    {
        $this->metadescription = $metadescription;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): Site
    {
        $this->url = $url;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(string $facebook): Site
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(string $twitter): Site
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(string $linkedin): Site
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getFooterDescription(): ?string
    {
        return $this->footer_description;
    }

    /**
     * @return $this
     */
    public function setFooterDescription(?string $footer_description): Site
    {
        $this->footer_description = $footer_description;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return $this
     */
    public function setAddress(?string $address): Site
    {
        $this->address = $address;

        return $this;
    }

    public function getFooterHours(): ?string
    {
        return $this->footer_hours;
    }

    /**
     * @return $this
     */
    public function setFooterHours(?string $footer_hours): Site
    {
        $this->footer_hours = $footer_hours;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return $this
     */
    public function setEmail(?string $email): Site
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): Site
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }
}
