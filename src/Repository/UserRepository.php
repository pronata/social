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
            'firstName' => $this->wrapAsPartSearch($firstName),
            'secondName' => $this->wrapAsPartSearch($secondName),
        ]);

        return $resultSet->fetchAllAssociative();
    }

    /**
     * Добавление друга.
     */
    public function addFriend(Uuid $userId, Uuid $friendId): void
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'INSERT INTO friend (user_id, friend_id) VALUES (:userId, :friendId)';

        $stmt = $conn->prepare($sql);

        $stmt->executeQuery([
            'userId' => (string) $userId,
            'friendId' => (string) $friendId
        ]);
    }

    /**
     * Удаление друга.
     */
    public function deleteFriend(Uuid $userId, Uuid $friendId): void
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'DELETE FROM friend f WHERE f.user_id=:userId AND f.friend_id=:friendId';

        $stmt = $conn->prepare($sql);

        $stmt->executeQuery([
            'userId' => $userId,
            'friendId' => $friendId
        ]);
    }

    /**
     * Поиск друзей.
     *
     * @return User[]
     */
    public function findFriends(Uuid $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT f.friend_id as friend_id, u.first_name as first_name, u.second_name as second_name, u.city as city, u.biography as biography, u.birth_date as birth_date, u.password as password
                FROM friend f
                LEFT JOIN social_user u ON f.friend_id=u.id
                WHERE f.user_id = :userId ORDER BY f.friend_id';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery(['userId' => $userId]);

        $userArray = $resultSet->fetchAllAssociative();

        return array_map(
            function(array $user) {
                $friend = new User();
                return $friend
                    ->setId(new Uuid($user['friend_id']))
                    ->setFirstName($user['first_name'])
                    ->setSecondName($user['second_name'])
                    ->setCity($user['city'])
                    ->setBiography($user['biography'])
                    ->setBirthDate(new BirthDate(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s',
                        $user['birth_date'])->format('Y-m-d')))
                    ->setPassword($user['password']);
            },
            $userArray
        );
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?User
    {
        return $this->loadUserByIdentifier($id);
    }

    /**
     * Делает UNESCAPE символов % и _ для значения LIKE параметра,
     * чтобы символы не воспринимались как подстановочные знаки (wildcard)
     * для LIKE выражения.
     */
    private function wrapAsPartSearch(string $value): string
    {
        return sprintf('%s%%', addcslashes($value, '%_'));
    }
}
