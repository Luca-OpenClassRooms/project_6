<?php

namespace App\Entity;

use App\Repository\TrickMediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TrickMediaRepository::class)]
class TrickMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('media:read')]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups('media:read')]
    private ?int $trick_id = null;

    #[ORM\Column(length: 255)]
    #[Groups('media:read')]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('media:read')]
    private ?string $content = null;

    #[Groups('media:read')]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(targetEntity: Trick::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Trick $trick;

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrickId(): ?int
    {
        return $this->trick_id;
    }

    public function setTrickId(int $trick_id): self
    {
        $this->trick_id = $trick_id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
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
}
