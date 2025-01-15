<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => [Task::GROUP_READ]],
    denormalizationContext: ['groups' => [Task::GROUP_WRITE]]
)]
#[ApiResource(
    uriTemplate: '/categories/{id}/tasks',
    operations: [new GetCollection()],
    uriVariables: [
        'id' => new Link(
            fromProperty: 'tasks',
            fromClass: Category::class
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    // partial => "contient", start => "commence par", exact => "valeur exacte"
    'title' => 'partial',
    'description' => 'partial',
    // booléen => souvent "exact"
    'done' => 'exact'
])]
#[ApiFilter(DateFilter::class, properties: ['dueDate'])]
#[ApiFilter(RangeFilter::class, properties: ['priority'])]
#[ApiFilter(OrderFilter::class, properties: [
    'dueDate' => 'ASC',
    'priority' => 'ASC',
    'createdAt' => 'ASC',
    'updatedAt' => 'ASC'
], arguments: ['orderParameterName' => 'order'])]
class Task
{
    public const string GROUP_READ = 'task:read';
    public const string GROUP_WRITE = 'task:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?bool $done = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?int $priority = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?Category $category = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function setDone(bool $isDone): static
    {
        $this->done = $isDone;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
