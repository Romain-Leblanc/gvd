<?php

namespace App\Repository;

use App\Entity\Intervention;
use App\Entity\Client;
use App\Entity\Vehicule;
use App\Entity\Modele;
use App\Entity\Etat;
use App\Entity\TypeEtat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Intervention>
 *
 * @method Intervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervention[]    findAll()
 * @method Intervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }

    public function updateIntervention(Intervention $intervention) {
        return $this->createQueryBuilder('u')
            ->update(Intervention::class, 'i')
            ->set('i.date_intervention', ":date_intervention")
            ->set('i.duree', ":duree")
            ->set('i.detail', ":detail")
            ->set('i.montant_ht', ":montant")
            ->set('i.fk_etat', ":etat")
            ->where('i.id = :id_intervention')
            ->setParameter("id_intervention", $intervention->getId())
            ->setParameter("date_intervention", $intervention->getDateIntervention())
            ->setParameter("duree", $intervention->getDuree())
            ->setParameter("detail", $intervention->getDetail())
            ->setParameter("montant", $intervention->getMontantHT())
            ->setParameter("etat", $intervention->getFKEtat()->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    public function updateInterventionByEtatAndNumFacture(array $idIntervention, int $idEtat, int $idFacture)
    {
        $query = $this->createQueryBuilder('f');
        return $query
            ->update(Intervention::class, 'i')
            ->set('i.fk_etat', ":id_etat")
            ->set('i.fk_facture', ":id_facture")
            ->where($query->expr()->in("i.id", ":id_intervention"))
            ->setParameter("id_etat", $idEtat)
            ->setParameter("id_facture", $idFacture)
            ->setParameter("id_intervention", $idIntervention)
            ->getQuery()
            ->getResult();
    }

    /* Renvoi la liste des interventions non facturés des véhicules du client pour Ajax au format JSON */
    public function findInterventionByClientAndEtat(int $idClient)
    {
        $query = $this->createQueryBuilder('i');
        return $query
            ->innerJoin(Vehicule::class, 'v', Join::WITH, 'i.fk_vehicule = v.id')
            ->innerJoin(Client::class, 'c', Join::WITH, 'v.fk_client = c.id')
            ->innerJoin(Etat::class, 'e', Join::WITH, 'i.fk_etat = e.id')
            ->innerJoin(TypeEtat::class, 'te', Join::WITH, 'e.fk_type_etat = te.id')
            ->where('c.id = :id_client')
            ->andWhere('i.fk_facture IS NULL')
            ->andWhere('e.etat = :id_etat')
            ->andWhere('te.type = :id_type_etat')
            ->setParameter('id_client', $idClient)
            ->setParameter('id_etat', 'Terminé')
            ->setParameter('id_type_etat', 'intervention')
            ->getQuery()
            ->getResult();
    }

    /* Récupère les résultats de(s) filtre(s) saisi(s) */
    public function filtreTableIntervention(array $filtre) {
        $query = $this->createQueryBuilder('i')
            ->innerJoin(Vehicule::class, 'v', Join::WITH, 'i.fk_vehicule = v.id')
            ->innerJoin(Modele::class, 'mo', Join::WITH, 'v.fk_modele = mo.id')
            ;
        // Ajoute les valeurs de filtres en fonction de ceux qui ont été saisis
        if ($filtre['id_intervention'] !== "") {
            $value = $filtre['id_intervention'];
            $query
                ->andWhere('i.id LIKE :id')
                ->setParameter('id', $value)
            ;
        }
        if ($filtre['date_intervention'] !== "") {
            $value = $filtre['date_intervention'];
            $query
                ->andWhere('i.date_intervention = :date')
                ->setParameter('date_intervention', $value)
            ;
        }
        if ($filtre['vehicule'] !== "") {
            $value = $filtre['vehicule'];
            // Recherche les interventions du modèle de véhicule correspondant
            $query
                ->andWhere('mo.id = :id_modele')
                ->setParameter('id_modele', $value)
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
                ->andWhere('i.montant_ht = :montant_ht')
                ->setParameter('montant_ht', $value)
            ;
        }
        return $query->getQuery()->getResult();
    }
}
