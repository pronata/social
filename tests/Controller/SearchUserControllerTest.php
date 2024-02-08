<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\ValueObject\BirthDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SearchUserControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testSearchUserSuccess()
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
        $this->getEntityManager()->flush();

        $this->getEntityManager()->clear();

        $found = $this->getEntityManager()->find(User::class, $user->getId());

        $this->client->loginUser($user);

        $response = $this->client->request(
            Request::METHOD_GET,
            '/user/search',
            [
                'last_name' => 'Иванов',
                'first_name' => 'Иван'
            ]
        );

        $this->assertResponseIsSuccessful();

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
}