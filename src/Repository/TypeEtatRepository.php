<?php

namespace App\Repository;

use App\Entity\TypeEtat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeEtat>
 *
 * @method TypeEtat|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeEtat|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeEtat[]    findAll()
 * @method TypeEtat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeEtatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEtat::class);
    }
}
