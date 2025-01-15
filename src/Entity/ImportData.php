<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Repository\ImportDataRepository;
use App\State\ImportDataStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImportDataRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => [ImportData::GROUP_READ]],
    denormalizationContext: ['groups' => [ImportData::GROUP_WRITE]]
)]
#[Get]
#[GetCollection]
#[Post(
    openapi: new Model\Operation(
        summary: 'Import json data',
        description: 'Import json data from Google Keep. Here is the JSON format:<br><br><pre><code>{<br>  "kind": "",<br>  "items": [<br>    {<br>      "kind": "",<br>      "id": "",<br>      "title": "",<br>      "updated": "",<br>      "selfLink": "",<br>      "items": [<br>        {<br>          "kind": "",<br>          "id": "",<br>          "title": "",<br>          "updated": "",<br>          "selfLink": "",<br>          "status": ""<br>        }<br>      ]<br>    }<br>  ]<br>}</code></pre>',
        requestBody: new Model\RequestBody(
            description: 'The JSON data to import',
            content: new \ArrayObject([
                'application/ld+json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'data' => [
                                'type' => 'string',
                                'description' => 'The JSON data to import'
                            ]
                        ]
                    ],
                    'example' => [
                        'name' => 'Import 1',
                        'data' => '{"kind": "keep#data", "items": [{"kind": "keep#note", "id": "1", "title": "Note 1", "updated": "2021-09-01T00:00:00Z", "selfLink": "https://keep.google.com/1", "items": [{"kind": "keep#list", "id": "1", "title": "List 1", "updated": "2021-09-01T00:00:00Z", "selfLink": "https://keep.google.com/1", "status": "completed"}]}]}'
                    ]
                ]
            ])
        )
    ),
    processor: ImportDataStateProcessor::class
)]
class ImportData
{
    public const string GROUP_READ = 'import:read';
    public const string GROUP_WRITE = 'import:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50)]
    #[Groups([self::GROUP_READ])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'imports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_READ])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?int $itemNumber = null;

    #[ORM\Column(length: 1000, nullable: true)]
    #[Groups([self::GROUP_READ])]
    private ?string $log = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups([self::GROUP_WRITE])]
    private ?string $data = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getItemNumber(): ?int
    {
        return $this->itemNumber;
    }

    public function setItemNumber(int $itemNumber): static
    {
        $this->itemNumber = $itemNumber;

        return $this;
    }

    public function getLog(): ?string
    {
        return $this->log;
    }

    public function setLog(?string $log): static
    {
        $this->log = $log;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): static
    {
        $this->data = $data;

        return $this;
    }
}
