<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Post[]    findByAuthorUserId(Uuid $authorUserId, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Поиск постов друзей.
     *
     * @return Post[]
     */
    public function findFriendsPosts(Uuid $userId, int $offset, int $limit): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT p.id as id, p.text as text, p.author_user_id as author_user_id 
                FROM post p
                LEFT JOIN friend f ON p.author_user_id=f.friend_id
                WHERE f.user_id = :userId ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery([
            'userId' => $userId,
            'offset' => $offset,
            'limit' => $limit
        ]);

        return $resultSet->fetchAllAssociative();
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}