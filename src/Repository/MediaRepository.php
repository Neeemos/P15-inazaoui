<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Album;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 * 
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }


    /**
     * @return array<int, Media>
     */
    public function findByAlbumAndUserRole(?Album $album, string $role = 'ROLE_GUEST'): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT m.*
        FROM media m
        INNER JOIN "user" u ON u.id = m.user_id
        WHERE u.roles::jsonb @> :role
    ';

        $params = ['role' => json_encode([$role])];
        $types = ['role' => \PDO::PARAM_STR];

        if ($album !== null) {
            $sql .= ' AND m.album_id = :albumId';
            $params['albumId'] = $album->getId();
            $types['albumId'] = \PDO::PARAM_INT;
        }

        $stmt = $conn->executeQuery($sql, $params, $types);
        $rows = $stmt->fetchAllAssociative();

        if (empty($rows)) {
            return [];
        }

        return $this->createQueryBuilder('m')
            ->where('m.id IN (:ids)')
            ->setParameter('ids', array_column($rows, 'id'))
            ->getQuery()
            ->getResult();
    }
}
