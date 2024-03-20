<?php

namespace App\Tests\Controller;

use App\Entity\Friend;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\ValueObject\BirthDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testGetPostsFeed()
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

        // Первый друг
        $friend = new User();
        $friend->setBiography('ldfll;ldf;;');
        $friend->setFirstName('Петр');
        $friend->setSecondName('Петров');
        $birthDate = new BirthDate('1997-08-03');
        $friend->setBirthDate($birthDate);
        $friend->setCity('Moscow');

        $hashedPassword = $this->getPasswordHasher()->hashPassword(
            $friend,
            'lflldf'
        );

        $friend->setPassword($hashedPassword);

        $this->getEntityManager()->persist($friend);

        $friendConn = new Friend();
        $friendConn->setUserId($user->getId());
        $friendConn->setFriendId($friend->getId());

        $this->getEntityManager()->persist($friendConn);

        // Второй друг
        $friend2 = new User();
        $friend2->setBiography('щшывловыло');
        $friend2->setFirstName('Дмитрий');
        $friend2->setSecondName('Дмитриев');
        $birthDate = new BirthDate('1999-02-01');
        $friend2->setBirthDate($birthDate);
        $friend2->setCity('Krasnodar');

        $hashedPassword = $this->getPasswordHasher()->hashPassword(
            $friend2,
            'jjjl'
        );

        $friend2->setPassword($hashedPassword);

        $this->getEntityManager()->persist($friend2);

        $friend2Conn = new Friend();
        $friend2Conn->setUserId($user->getId());
        $friend2Conn->setFriendId($friend2->getId());

        $this->getEntityManager()->persist($friend2Conn);

        $now = new \DateTimeImmutable();

        // Пост 1 первого друга
        $post1 = new Post();
        $post1Text = '1 пост';
        $post1
            ->setText($post1Text)
            ->setAuthorUserId($friend->getId())
            ->setCreatedAt($now->modify('-3 weeks'));

        $this->getEntityManager()->persist($post1);

        // Пост 2 первого друга
        $post2 = new Post();
        $post2Text = '2 пост';
        $post2
            ->setText($post2Text)
            ->setAuthorUserId($friend->getId())
            ->setCreatedAt($now->modify('-1 week'));

        $this->getEntityManager()->persist($post2);

        // Пост 3 второго друга
        $post3 = new Post();
        $post3Text = '3 пост';
        $post3
            ->setText($post3Text)
            ->setAuthorUserId($friend2->getId())
            ->setCreatedAt($now->modify('-2 weeks'));

        $this->getEntityManager()->persist($post3);

        // Пост 4 второго друга
        $post4 = new Post();
        $post4Text = '4 пост';
        $post4
            ->setText($post4Text)
            ->setAuthorUserId($friend2->getId())
            ->setCreatedAt($now->modify('-1 day'));

        $this->getEntityManager()->persist($post4);

        $this->getEntityManager()->flush();

        $this->getEntityManager()->clear();

        $this->client->loginUser($user);

        $this->client->request(
            Request::METHOD_GET,
            '/post/feed',
            [
                'offset' => 1,
                'limit' => 2
            ]
        );

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();

        $this->assertSame(
            [
                [
                    'id' => (string) $post2->getId(),
                    'text' => $post2Text,
                    'author_user_id' => (string) $post2->getAuthorUserId()
                ],
                [
                    'id' => (string) $post3->getId(),
                    'text' => $post3Text,
                    'author_user_id' => (string) $post3->getAuthorUserId()
                ]
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function testCreatePost()
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

        $this->client->loginUser($user);

        $postMessage = 'Какой сегодня день?';

        $this->client->request(
            Request::METHOD_POST,
            '/post/create',
            content: json_encode(
                [
                    'text' => $postMessage
                ]
            )
        );

        $this->assertResponseIsSuccessful();

        $response = $this->client->getResponse();

        $this->assertSame('Успешно создан пост', json_decode($response->getContent()));

        $posts = $this->getPostRepository()->findAll();

        $this->assertCount(1, $posts);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function getPasswordHasher(): UserPasswordHasherInterface
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get(UserPasswordHasherInterface::class);
    }

    protected function getPostRepository(): PostRepository
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get(PostRepository::class);
    }
}