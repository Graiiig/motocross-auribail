<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SessionRepository::class)
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     */
    private $date;
    /**
     * @ORM\OneToMany(targetEntity=PendingList::class, mappedBy="session", cascade={"persist", "remove"})
     */
    private $pendingLists;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->pendingLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|PendingList[]
     */
    public function getPendingLists(): Collection
    {
        return $this->pendingLists;
    }

    public function addPendingList(PendingList $pendingList)
    {
        if (!$this->pendingLists->contains($pendingList)) {
            $this->pendingLists[] = $pendingList;
            $pendingList->setSession($this);
        }
    }
    public function removePendingList(PendingList $pendingList): self
    {
        if ($this->pendingLists->removeElement($pendingList)) {
            // set the owning side to null (unless already changed)
            if ($pendingList->getSession() === $this) {
                $pendingList->setSession(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

}
