<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $authorUserId;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getAuthorUserId(): Uuid
    {
        return $this->authorUserId;
    }

    public function setText(string $text): Post
    {
        $this->text = $text;
        return $this;
    }

    public function setAuthorUserId(Uuid $authorUserId): Post
    {
        $this->authorUserId = $authorUserId;
        return $this;
    }
}