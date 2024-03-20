<?php

namespace App\Tests\Controller;

use App\Entity\DialogMessage;
use App\Entity\Friend;
use App\Entity\User;
use App\Repository\DialogMessageRepository;
use App\ValueObject\BirthDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DialogMessageControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testAddDialogMessage(): void
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

        $messageToSend = 'Привет!';

        $this->client->request(
            Request::METHOD_POST,
            '/dialog/'.$friend->getId().'/send',
            content: json_encode(['text' => $messageToSend])
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertSame('Успешно отправлено сообщение', json_decode($response->getContent()));

        $dialogMessages = $this->getDialogMessageRepository()->findAll();

        $this->assertCount(1, $dialogMessages);
    }

    public function testListDialog(): void
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

        $message1Text = 'Привет!';

        $now = new \DateTimeImmutable();

        $message1 = new DialogMessage();
        $message1
            ->setCreatedAt($now->modify('-2 hours'))
            ->setFrom($user->getId())
            ->setTo($friend->getId())
            ->setText($message1Text);

        $this->getEntityManager()->persist($message1);

        $message2Text = 'Привет, Никита!';

        $message2 = new DialogMessage();
        $message2
            ->setCreatedAt($now->modify('-1 hour'))
            ->setFrom($friend->getId())
            ->setTo($user->getId())
            ->setText($message2Text);

        $this->getEntityManager()->persist($message2);

        $this->getEntityManager()->flush();

        $this->getEntityManager()->clear();

        $this->client->loginUser($user);

        $this->client->request(
            Request::METHOD_GET,
            '/dialog/'.$friend->getId().'/list'
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $this->assertSame(
            [
                [
                    'from' => (string) $user->getId(),
                    'to' => (string) $friend->getId(),
                    'text' => $message1Text,
                ],
                [
                    'from' => (string) $friend->getId(),
                    'to' => (string) $user->getId(),
                    'text' => $message2Text,
                ]
            ],
            json_decode($response->getContent(), true)
        );

        $dialogMessages = $this->getDialogMessageRepository()->findAll();

        $this->assertCount(2, $dialogMessages);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function getDialogMessageRepository(): DialogMessageRepository
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get(DialogMessageRepository::class);
    }

    protected function getPasswordHasher(): UserPasswordHasherInterface
    {
        /* @phpstan-ignore-next-line */
        return static::getContainer()->get(UserPasswordHasherInterface::class);
    }
}