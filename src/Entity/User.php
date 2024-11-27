<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;



/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Un compte avec cet e-mail existe déjà.")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message="L'adresse email {{value}} est incorrecte.")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ Nom ne peut pas être vide.")
     * @Assert\Length(min=5, max=50, minMessage="Le nom doit faire {{ limit }} caractères minimum.", maxMessage="Le nom doit faire {{ limit }} caractères maximum.")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ Prénom ne peut pas être vide.")
     * @Assert\Length(min=5, max=50, minMessage="Le prénom doit faire {{ limit }} caractères minimum.", maxMessage="Le prénom doit faire {{ limit }} caractères maximum.")
     */
    private $firstName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActivated = false;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255, options={"default": "Init"})
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

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Usercursus::class, mappedBy="user")
     */
    private $usercursuses;

    /**
     * @ORM\OneToMany(targetEntity=UserCursusLesson::class, mappedBy="relations")
     */
    private $userCursusLessons;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->created_by = "Init";
        $this->orders = new ArrayCollection();
        $this->usercursuses = new ArrayCollection();
        $this->userCursusLessons = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function isIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

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

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Usercursus>
     */
    public function getUsercursuses(): Collection
    {
        return $this->usercursuses;
    }

    public function addUsercursus(Usercursus $usercursus): self
    {
        if (!$this->usercursuses->contains($usercursus)) {
            $this->usercursuses[] = $usercursus;
            $usercursus->setUser($this);
        }

        return $this;
    }

    public function removeUsercursus(Usercursus $usercursus): self
    {
        if ($this->usercursuses->removeElement($usercursus)) {
            // set the owning side to null (unless already changed)
            if ($usercursus->getUser() === $this) {
                $usercursus->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserCursusLesson>
     */
    public function getUserCursusLessons(): Collection
    {
        return $this->userCursusLessons;
    }

    public function addUserCursusLesson(UserCursusLesson $userCursusLesson): self
    {
        if (!$this->userCursusLessons->contains($userCursusLesson)) {
            $this->userCursusLessons[] = $userCursusLesson;
            $userCursusLesson->setRelations($this);
        }

        return $this;
    }

    public function removeUserCursusLesson(UserCursusLesson $userCursusLesson): self
    {
        if ($this->userCursusLessons->removeElement($userCursusLesson)) {
            // set the owning side to null (unless already changed)
            if ($userCursusLesson->getRelations() === $this) {
                $userCursusLesson->setRelations(null);
            }
        }

        return $this;
    }
}
