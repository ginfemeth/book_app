<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Dto\BookCoverUpload;
use App\State\BookCoverUploadProcessor;
use App\Repository\BookRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\State\BookDeleteProcessor;
use Symfony\Component\HttpFoundation\Request;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'app_book')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['book:read']]),
        new Post(denormalizationContext: ['groups' => ['book:write']], normalizationContext: ['groups' => ['book:read']]),
        new Get(normalizationContext: ['groups' => ['book:read']]),
        new Patch(denormalizationContext: ['groups' => ['book:write']], normalizationContext: ['groups' => ['book:read']]),
        new Delete(
            processor: BookDeleteProcessor::class
        ),

        new Post(
            uriTemplate: '/books/{id}/cover',
            input: BookCoverUpload::class,
            output: self::class,
            processor: BookCoverUploadProcessor::class,
            name: 'book_upload_cover',
            normalizationContext: ['groups' => ['book:read']],
            deserialize: false
        ),
    ]
)]

class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['book:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['book:read', 'book:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['book:read', 'book:write'])]
    private ?int $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['book:read', 'book:write'])]
    private ?string $resume = null;

    #[ORM\Column]
    #[Groups(['book:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['book:read'])]
    private ?string $coverImagePath = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    // #[Groups(['book:read'])]
    // public function getCoverImageUrl(): ?string
    // {
    //     if (!$this->coverImagePath) {
    //         return null;
    //     }

    //     // Récupère le domaine depuis la requête HTTP
    //     $request = Request::createFromGlobals();
    //     $baseUrl = $request->getSchemeAndHttpHost();

    //     return $baseUrl . $this->coverImagePath;
    // }


    // getters/setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCoverImagePath(): ?string
    {
        return $this->coverImagePath;
    }

    public function setCoverImagePath(?string $coverImagePath): self
    {
        $this->coverImagePath = $coverImagePath;
        return $this;
    }
}
