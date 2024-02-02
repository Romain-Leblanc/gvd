<?php

namespace App\Repository;

use App\Entity\MoyenPaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MoyenPaiement>
 *
 * @method MoyenPaiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method MoyenPaiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method MoyenPaiement[]    findAll()
 * @method MoyenPaiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoyenPaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoyenPaiement::class);
    }
}
