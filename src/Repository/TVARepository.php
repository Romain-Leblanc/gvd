<?php

namespace App\Repository;

use App\Entity\TVA;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TVA>
 *
 * @method TVA|null find($id, $lockMode = null, $lockVersion = null)
 * @method TVA|null findOneBy(array $criteria, array $orderBy = null)
 * @method TVA[]    findAll()
 * @method TVA[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TVARepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TVA::class);
    }
}
