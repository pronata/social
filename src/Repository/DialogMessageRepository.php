<?php

namespace App\Repository;

use App\Entity\DialogMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<DialogMessage>
 *
 * @method DialogMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method DialogMessage[]    findAll()
 * @method DialogMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DialogMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DialogMessage::class);
    }


    /**
     * Отправка сообщения.
     */
    public function addDialogMessage(Uuid $from, Uuid $to, string $text): void
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'INSERT INTO dialog_message ("id", "from_user", "to_user", "text", created_at) VALUES (:id, :from, :to, :text, current_timestamp)';

        $stmt = $conn->prepare($sql);

        $stmt->executeQuery([
            'id' => (string) Uuid::v4(),
            'from' => (string) $from,
            'to' => (string) $to,
            'text' => $text
        ]);
    }

    /**
     * Диалог между двумя пользователям.
     *
     * @return DialogMessage[]
     */
    public function findDialog(Uuid $user1Id, Uuid $user2Id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT d.from_user as "from", d.to_user as "to", d.text as text FROM dialog_message d WHERE "from_user"=:user1Id AND "to_user"=:user2Id OR "from_user"=:user2Id AND "to_user"=:user1Id ORDER BY d.created_at';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery([
            'user1Id' => (string) $user1Id,
            'user2Id' => (string) $user2Id
        ]);

        return $resultSet->fetchAllAssociative();
    }

    public function save(DialogMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DialogMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}