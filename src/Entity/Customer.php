<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUsers", "getCustomers"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getCustomers"])]
    private ?User $relation = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers", "getCustomers"])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers", "getCustomers"])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers", "getCustomers"])]
    private ?string $lastName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelation(): ?User
    {
        return $this->relation;
    }

    public function setRelation(?User $relation): self
    {
        $this->relation = $relation;

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
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
}
