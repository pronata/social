<?php

namespace App\Repository;

use App\Entity\User;
use App\ValueObject\BirthDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function loadUserByIdentifier(string $identifier): ?User
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT u.id, u.first_name, u.second_name, u.city, u.biography, u.birth_date, u.password
                FROM social_user u
                WHERE u.id = :userId';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery(['userId' => $identifier]);

        $userArray = $resultSet->fetchAssociative();

        $user = new User();
        $user
            ->setId(new Uuid($identifier))
            ->setFirstName($userArray['first_name'])
            ->setSecondName($userArray['second_name'])
            ->setCity($userArray['city'])
            ->setBiography($userArray['biography'])
            ->setBirthDate(new BirthDate(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s',
                $userArray['birth_date'])->format('Y-m-d')))
            ->setPassword($userArray['password']);

        return $user;
    }

    /**
     * Поиск по частичному совпадению имени и фамилии.
     *
     * @return array|User
     * @throws \Doctrine\DBAL\Exception
     */
    public function findByFirstNameAndSecondName(string $firstName, string $secondName): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT u.id, u.first_name, u.second_name, u.birth_date, u.biography, u.city
                FROM social_user u
                WHERE LOWER(u.first_name) LIKE LOWER(:firstName)
                AND LOWER(u.second_name) LIKE LOWER(:secondName) ORDER BY u.id';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery([
            'firstName' => $firstName,
            'secondName' => $secondName,
        ]);

        return $resultSet->fetchAllAssociative();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?User
    {
        return $this->loadUserByIdentifier($id);
    }
}
