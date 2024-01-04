<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /* Met à jour les informations d'un client */
    public function updateClient(Client $client) {
        return $this->createQueryBuilder('u')
            ->update(Client::class, 'c')
            ->set('c.nom', ":nom")
            ->set('c.prenom', ":prenom")
            ->set('c.tel', ":tel")
            ->set('c.email', ":email")
            ->set('c.adresse', ":adresse")
            ->set('c.suite_adresse', ":suite_adresse")
            ->set('c.code_postal', ":code_postal")
            ->set('c.ville', ":ville")
            ->set('c.num_tva', ":num_tva")
            ->where('c.id = :id_client')
            ->setParameter("id_client", $client->getId())
            ->setParameter("nom", $client->getNom())
            ->setParameter("prenom", $client->getPrenom())
            ->setParameter("tel", $client->getTel())
            ->setParameter("email", $client->getEmail())
            ->setParameter("adresse", $client->getAdresse())
            ->setParameter("suite_adresse", $client->getSuiteAdresse())
            ->setParameter("code_postal", $client->getCodePostal())
            ->setParameter("ville", $client->getVille())
            ->setParameter("num_tva", $client->getNumTVA())
            ->getQuery()
            ->getResult()
            ;
    }

    /* Récupère les résultats de(s) filtre(s) saisi(s) */
    public function filtreTableClient(array $filtre) {
        $query = $this->createQueryBuilder('c');
        // Ajoute les valeurs de filtres en fonction de ceux qui ont été saisis
        if ($filtre['id_client'] !== "" || $filtre['client'] !== "") {
            if ($filtre['id_client']) { $value = $filtre['id_client']; }
            else { $value = $filtre['client']; }
            $query
                ->andWhere('c.id LIKE :id')
                ->setParameter('id', $value)
            ;
        }
        if ($filtre['coordonnees'] !== "") {
            $value = $filtre['coordonnees'];
            $value = str_replace(" ", "", $value);
            $query
                ->andWhere('c.tel LIKE :coordonnees OR c.email LIKE :coordonnees')
                ->setParameter('coordonnees', '%'.$value.'%');
            ;
        }
        if ($filtre['adresse_complete'] !== "") {
            $value = $filtre['adresse_complete'];
            $query
                ->andWhere('c.adresse LIKE :adresse_complete OR c.suite_adresse LIKE :adresse_complete OR c.code_postal LIKE :adresse_complete OR c.ville LIKE :adresse_complete')
                ->setParameter('adresse_complete', '%'.$value.'%');
        }
        return $query->getQuery()->getResult();
    }
}
