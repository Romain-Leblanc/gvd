<?php

namespace App\Repository;

use App\Entity\Carburant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Carburant>
 *
 * @method Carburant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Carburant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Carburant[]    findAll()
 * @method Carburant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarburantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carburant::class);
    }
}
