<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('trick', 'trick:user')]
    private ?int $id = null;

    #[Groups('trick')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups('trick')]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;
    
    #[Groups('trick')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Groups('trick')]
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[Groups('trick')]
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[Groups('trick')]
    #[ORM\Column]
    private ?bool $featured = null;

    #[Groups('trick')]
    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Category $category;

    #[Groups('trick:user')]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: "EAGER")]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?User $user;

    #[Groups('trick:medias')]
    #[ORM\OneToMany(targetEntity: TrickMedia::class, mappedBy: 'trick', fetch: 'LAZY')]
    private ?\Doctrine\ORM\PersistentCollection $medias;


    public function getMedias(): ?\Doctrine\ORM\PersistentCollection
    {
        return $this->medias;
    }

    public function setMedias(?\Doctrine\ORM\PersistentCollection $medias): self
    {
        $this->medias = $medias;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->created_at = new \DateTime("now");
    }

    #[ORM\PreUpdate]
    public function onPreUpdate()
    {
        $this->updated_at = new \DateTime("now");
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }
}
