<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\LogMood;
use App\Repository\LogRepository;
use App\State\LogProcessor;
use App\State\LogStateProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    processor: LogProcessor::class,
    order: ['id' => 'desc'],
    provider: LogStateProvider::class
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'content' => 'partial',
        'mood' => 'exact'
    ]
)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read', 'write'])]
    private ?string $content = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(enumType: LogMood::class)]
    private ?LogMood $mood = LogMood::OK;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'M-j D H:i'])]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMood(): ?LogMood
    {
        return $this->mood;
    }

    public function setMood(LogMood $mood): self
    {
        $this->mood = $mood;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTimeImmutable('now'));
    }
}
