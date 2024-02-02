<?php

namespace App\Controller\Admin;

use App\Entity\Modele;
use App\Entity\Vehicule;
use App\Form\Admin\NewModeleType;
use App\Form\Admin\EditModeleType;
use App\Form\FiltreTable\Admin\FiltreTableModeleType;
use App\Repository\ModeleRepository;
use App\Repository\MarqueRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/modele')]
class ModeleController extends AbstractController
{
    #[Route('/', name: 'modele_admin_index', methods: ['GET', 'POST'])]
    public function index(ModeleRepository $modeleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupère la requête puisque KnpPaginator s'occupe des données lui-même
        $donnees = $modeleRepository->createQueryBuilder('mo')
            ->select('mo')
            ->addSelect('COUNT(v.fk_modele) as nombreVehicule')
            ->leftJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_modele = mo.id')
            ->join('mo.fk_marque', 'ma')
            ->groupBy('mo.id')
            ->addOrderBy('ma.marque')
            ->addOrderBy('mo.modele')
        ;

        // Récupère le paramètre de limite de résultat s'il a été définit dans l'URL
        if($request->query->getInt('max') > 0
            && is_integer($request->query->getInt('max'))
            && in_array($request->query->getInt('max'), $this->configPaginationList())
        ) {
            $donnees = $donnees->setMaxResults($request->query->getInt('max'))->getQuery();
        }

        // Traitement des données par KnpPaginator
        $lesModelesPagination = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            ($request->query->getInt('max') > 0) ? $request->query->getInt('max') : $this->configPaginationLimit()
        );
        // Valeurs par défaut des résultats des filtres
        $lesModelesForm = $lesModelesPagination->getItems();

        $form = $this->createForm(FiltreTableModeleType::class, $modeleRepository->findAll());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_modele');
            $query = $modeleRepository->createQueryBuilder('mo')
                ->select('mo')
                ->addSelect('COUNT(v.fk_modele) as nombreVehicule')
                ->leftJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_modele = mo.id')
                ->join('mo.fk_marque', 'ma')
            ;
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_modele'] !== "" || $data['modele'] !== "" || $data['marque'] !== "") {
                if ($data['id_modele'] !== "" || $data['modele'] !== "") {
                    if ($data['id_modele']) { $value = $data['id_modele']; }
                    else { $value = $data['modele']; }
                    $query = $query
                        ->andWhere('mo.id = :id')
                        ->setParameter('id', $value)
                    ;
                }
                if ($data['marque'] !== "") {
                    $query = $query
                        ->andWhere('mo.fk_marque = :id_marque')
                        ->setParameter('id_marque', $data['marque'])
                    ;
                }
                $lesModelesForm = $query->groupBy('mo.id')->addOrderBy('ma.marque')->addOrderBy('mo.modele')->getQuery()->getResult();
            }
        }

        return $this->render('admin/modele/index.html.twig', [
            // Données pour Knppaginator
            'lesModelesPagination' => $lesModelesPagination,
            // Données pour le formulaire et tableau
            'lesModelesForm' => $lesModelesForm,
            'choixListe' => $this->configPaginationList(),
            'formFiltreTable' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'modele_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MarqueRepository $marqueRepository): Response
    {
        $modele = new Modele();
        // Récupère l'identifiant de la marque insérée qui nécessite un modèle
        $idMarque = $request->get('idmarque');
        // Si l'identifiant de la marque est présent,
        // on redirige vers le formulaire d'ajout d'un modèle
        if($idMarque > 0 && $idMarque != null) {
            $modele->setFkMarque($marqueRepository->find($idMarque));
        }
        $form = $this->createForm(NewModeleType::class, $modele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le modèle en majuscule
            $modele->setModele(strtoupper($modele->getModele()));
            $entityManager->persist($modele);
            $entityManager->flush();

            return $this->redirectToRoute('modele_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/modele/new.html.twig', [
            'errors' => $form->getErrors(true),
            'formNewModele' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'modele_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Modele $modele, EntityManagerInterface $entityManager, VehiculeRepository $vehiculeRepository): Response
    {
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($modele == null) {
            $this->addFlash('modele', 'Ce modèle n\'existe pas.');
            return $this->redirectToRoute('modele_admin_index');
        }
        elseif(!empty($vehiculeRepository->findBy(['fk_modele' => $modele->getId()]))) {
            // Si l'identifiant existe dans la table correspondante, on génère un message d'erreur
            $this->addFlash('modele', 'Ce modèle n\'est pas modifiable.');
            return $this->redirectToRoute('modele_admin_index');
        }

        $form = $this->createForm(EditModeleType::class, $modele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le modèle en majuscule
            $modele->setModele(strtoupper($modele->getModele()));
            $entityManager->persist($modele);
            $entityManager->flush();
            return $this->redirectToRoute('modele_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/modele/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formEditModele' => $form
        ]);
    }

    #[Route('/{id}', name: 'modele_admin_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Modele $modele, EntityManagerInterface $entityManager, VehiculeRepository $vehiculeRepository): Response
    {
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($modele == null) {
            $this->addFlash('modele', 'Ce modèle n\'existe pas.');
        }
        elseif(!empty($vehiculeRepository->findBy(['fk_modele' => $modele->getId()]))) {
            // Si l'identifiant existe dans la table correspondante, on génère un message d'erreur
            $this->addFlash('modele', 'Ce modèle n\'est pas supprimable.');
        }
        elseif (
            $this->isCsrfTokenValid('delete'.$modele->getId(), $request->request->get('_token'))
            && empty($vehiculeRepository->findBy(['fk_modele' => $modele->getId()]))
        ) {
            // Vérifie le token puis supprime cet élément
            $entityManager->remove($modele);
            $entityManager->flush();
        }

        return $this->redirectToRoute('modele_admin_index');
    }

    private function configPaginationList()
    {
        return [25, 50, 100];
    }

    private function configPaginationLimit()
    {
        return 25;
    }
}
