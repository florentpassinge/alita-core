<?php

declare(strict_types = 1);

namespace App\Entity\Traits;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks
 */
trait TimestampableTrait
{
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private \DateTime $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="change")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private \DateTime $updatedAt;

    /**
     * @var string
     * @ORM\Column(name="created_by", type="string")
     */
    private string $createdBy;

    /**
     * @var string
     * @ORM\Column(name="updated_by", type="string")
     */
    private string $updatedBy;

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updatedTimestampable(): void
    {
        $now = Carbon::now();
        $this->setUpdatedAt($now);
    }

    /**
     * @ORM\PrePersist()
     */
    public function insertTimestampable(): void
    {
        $now = Carbon::now();
        $this->setUpdatedAt($now);
        $this->setCreatedAt($now);
    }
}
