<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * /**
     * @Assert\NotNull
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotNull
     */
    private $firstname;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\LessThan("Today")
     */
    private $birthDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deathDate;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 500,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     */
    private $biography;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="author")
     */
    private $books;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getDeathDate(): ?DateTimeInterface
    {
        return $this->deathDate;
    }

    public function setDeathDate(?DateTimeInterface $deathDate): self
    {
        $this->deathDate = $deathDate;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->name . " " . $this->firstname;
    }

    /**
     * @param mixed $books
     */
    public function setBooks($books): void
    {
        $this->books = $books;
    }

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getBooks()
    {
        return $this->books;
    }

}
