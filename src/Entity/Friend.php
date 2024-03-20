<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Friend
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $userId;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $friendId;

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getFriendId(): Uuid
    {
        return $this->friendId;
    }

    public function setUserId(Uuid $userId): Friend
    {
        $this->userId = $userId;
        return $this;
    }

    public function setFriendId(Uuid $friendId): Friend
    {
        $this->friendId = $friendId;
        return $this;
    }
}