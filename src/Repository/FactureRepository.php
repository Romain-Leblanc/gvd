<?php

namespace App\Repository;

use App\Entity\Facture;
use App\Entity\Vehicule;
use App\Entity\Intervention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 *
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function updateFacture(Facture $facture) {
        return $this->createQueryBuilder('u')
            ->update(Facture::class, 'f')
            ->set('f.date_paiement', ":date_paiement")
            ->set('f.fk_moyen_paiement', ":moyen_paiement")
            ->where('f.id = :id_facture')
            ->andWhere('f.date_facture = :date_facture')
            ->setParameter("date_paiement", $facture->getDatePaiement()->format('Y-m-d'))
            ->setParameter("moyen_paiement", $facture->getFKMoyenPaiement()->getId())
            ->setParameter("id_facture", $facture->getId())
            ->setParameter("date_facture", $facture->getDateFacture()->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /* Récupère les résultats de(s) filtre(s) saisi(s) */
    public function filtreTableFacture(array $filtre) {
        $query = $this->createQueryBuilder('f')
            ->innerJoin(Intervention::class, 'i', Join::WITH, 'i.fk_facture = f.id')
            ->innerJoin(Vehicule::class, 'v', Join::WITH, 'i.fk_vehicule = v.id')
            ;
        // Ajoute les valeurs de filtres en fonction de ceux qui ont été saisis
        if ($filtre['id_facture'] !== "") {
            $value = $filtre['id_facture'];
            $query
                ->andWhere('f.id LIKE :id')
                ->setParameter('id', $value)
            ;
        }
        if ($filtre['date_facture'] !== "") {
            $value = $filtre['date_facture'];
            $query
                ->andWhere('f.date_facture = :date_facture')
                ->setParameter('date_facture', $value)
            ;
        }
        if ($filtre['client'] !== "") {
            $value = $filtre['client'];
            $query
                ->andWhere('v.fk_client = :client')
                ->setParameter('client', $value)
            ;
        }
        if ($filtre['montant_ht'] !== "") {
            $value = $filtre['montant_ht'];
            $query
                ->andWhere('f.montant_ht = :montant_ht')
                ->setParameter('montant_ht', $value)
            ;
        }
        $query->orderBy('f.id', 'DESC');
        return $query->getQuery()->getResult();
    }
}
