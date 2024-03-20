<?php

namespace App\Tests\Controller;

use App\Entity\Friend;
use App\Entity\User;
use App\Repository\UserRepository;
use App\ValueObject\BirthDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FriendControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testAddFriend(): void
    {
        $user = new User();
        $user->setBiography('fdmdfk');
        $user->setFirstName('Иван');
        $user->setSecondName('Иванов');
        $birthDate = new BirthDate('1998-01-02');
        $user->setBirthDate($birthDate);
        $user->setCity('London');

        $hashedPassword = $this->getPasswordHasher()->hashPassword(
            $user,
            'abc'
        );

        $user->setPassword($hashedPassword);

        $this->getEntityManager()->persist($user);

        $friend = new User();
        $friend->setBiography('ldfll;ldf;;');
        $friend->setFirstName('Петр');
        $friend->setSecondName('Петров');
        $birthDate = new BirthDate('1997-08-03');
        $friend->setBirthDate($birthDate);
        $friend->setCity('Moscow');

        $hashedPassword = $this->getPasswordHasher()->hashPassword(
            $user,
            'lflldf'
        );

        $friend->setPassword($hashedPassword);

        $this->getEntityManager()->persist($friend);

        $friend2 = new User();
        $friend2->setBiography('kjdkjhkdf');
        $friend2->setFirstName('Андрей');
        $friend2->setSecondName('Андреев');
        $birthDate = new BirthDate('1995-04-02');
        $friend2->setBirthDate($birthDate);
        $friend2->setCity('Saint-Petersburg');

        $hashedPassword = $this->getPasswordHasher()->hashPassword(
            $user,
            'dllsdlsd'
        );

        $friend2->setPassword($hashedPassword);

        $this->getEntityManager()->persist($friend2);

        $friendConn = new Friend();
        $friendConn->setUserId($user->getId());
        $friendConn->setFriendId($friend2->getId());

        $this->getEntityManager()->persist($friendConn);

        $this->getEntityManager()->flush();

        $this->getEntityManager()->clear();

        $this->client->loginUser($user);

        $this->client->request(
            Request::METHOD_PUT,
            '/friend/set/'.$friend->getId(),
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertSame('Пользователь успешно указал своего друга', json_decode($response->getContent()));

        $friends = $this->getUserRepository()->findFriends($user->getId());

        $ids = array_map(fn(User $friend) => $friend->getId(), $friends);

        $friend->getId()->equals($ids[0]);
        $friend->getId()->equals($ids[1]);
    }

    public function testDeleteFriend(): void
    {
        $user = new User();
        $user->setBiography('fdmdfk');
        $user->setFirstName('Иван');
        $user->setSecondName('Иванов');
        $birthDate = new BirthDate('1998-01-02');
        $user->setBirthDate($birthDate);
        $user->setCity('London');

        $hashedPassword = $this->getPasswordHasher()->hashPassword(
            $user,
            'abc'
        );

        $user->setPassword($hashedPassword);

        $this->getEntityManager()->persist($user);

        $friend = new User();
        $friend->setBiography('ldfll;ldf;;');
        $friend->setFirstName('Петр');
        $friend->setSecondName('Петров');
        $birthDate = new BirthDate('1997-08-03');
        $friend->setBirthDate($birthDate);
        $friend->setCity('Moscow');

        $hashedPassword = $this->getPasswordHasher()->hashPassword(
            $user,
            'lflldf'
        );

        $friend->setPassword($hashedPassword);

        $this->getEntityManager()->persist($friend);

        $friendConn = new Friend();
        $friendConn->setUserId($user->getId());
        $friendConn->setFriendId($friend->getId());

        $this->getEntityManager()->persist($friendConn);

        $this->getEntityManager()->flush();

        $this->getEntityManager()->clear();

        $this->client->loginUser($user);

        $this->client->request(
            Request::METHOD_PUT,
            '/friend/delete/'.$friend->getId(),
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertSame('Пользователь успешно удалил из друзей пользователя', json_decode($response->getContent()));

        $friends = $this->getUserRepository()->findFriends($user->getId());

        $this->assertCount(0, $friends);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function getUserRepository(): UserRepository
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get(UserRepository::class);
    }

    protected function getPasswordHasher(): UserPasswordHasherInterface
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get(UserPasswordHasherInterface::class);
    }
}