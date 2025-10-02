<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword); 
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    public function findGuests(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT u.id
        FROM "user" u
        WHERE u.roles::jsonb @> :role
        ORDER BY u.id ASC
    ';

        $stmt = $conn->executeQuery(
            $sql,
            ['role' => json_encode(['ROLE_GUEST'])],
            ['role' => \PDO::PARAM_STR]
        );

        $userIds = array_column($stmt->fetchAllAssociative(), 'id');

        if (empty($userIds)) {
            return [];
        }

        return $this->createQueryBuilder('u')
            ->leftJoin('u.medias', 'm')
            ->addSelect('m')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', $userIds)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
