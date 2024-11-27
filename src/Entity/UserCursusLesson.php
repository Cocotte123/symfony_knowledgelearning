<?php

namespace App\Entity;

use App\Repository\UserCursusLessonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserCursusLessonRepository::class)
 * Lessons bought by users
 */
class UserCursusLesson
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userCursusLessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Cursus::class, inversedBy="userCursusLessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cursus;

    /**
     * @ORM\ManyToOne(targetEntity=Lesson::class, inversedBy="userCursusLessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $learning;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValidated = false;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255, options={"default": "Cart"})
     */
    private $created_by;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updated_by;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->created_by = "Cart";
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): self
    {
        $this->cursus = $cursus;

        return $this;
    }

    public function getLearning(): ?Lesson
    {
        return $this->learning;
    }

    public function setLearning(?Lesson $learning): self
    {
        $this->learning = $learning;

        return $this;
    }

    public function isIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): self
    {
        $this->updated_by = $updated_by;

        return $this;
    }
}
