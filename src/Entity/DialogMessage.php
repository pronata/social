<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class DialogMessage
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: UuidType::NAME, name: 'from_user')]
    private Uuid $from;

    #[ORM\Column(type: UuidType::NAME, name: 'to_user')]
    private Uuid $to;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getFrom(): Uuid
    {
        return $this->from;
    }

    public function getTo(): Uuid
    {
        return $this->to;
    }

    public function setId(Uuid $id): DialogMessage
    {
        $this->id = $id;
        return $this;
    }

    public function setFrom(Uuid $from): DialogMessage
    {
        $this->from = $from;
        return $this;
    }

    public function setText(string $text): DialogMessage
    {
        $this->text = $text;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setTo(Uuid $to): DialogMessage
    {
        $this->to = $to;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): DialogMessage
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}