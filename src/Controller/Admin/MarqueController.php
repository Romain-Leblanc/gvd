<?php

namespace App\Controller\Admin;

use App\Entity\Marque;
use App\Entity\Modele;
use App\Form\Admin\NewMarqueType;
use App\Form\Admin\EditMarqueType;
use App\Form\FiltreTable\Admin\FiltreTableMarqueType;
use App\Repository\MarqueRepository;
use App\Repository\ModeleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/marque')]
class MarqueController extends AbstractController
{
    #[Route('/', name: 'marque_admin_index', methods: ['GET', 'POST'])]
    public function index(MarqueRepository $marqueRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupère la requête puisque KnpPaginator s'occupe des données lui-même
        $donnees = $marqueRepository->createQueryBuilder('ma')
            ->select('ma.id')
            ->addSelect('ma.marque')
            ->addSelect('COUNT(mo.id) as nombre')
            ->leftJoin(Modele::class, 'mo', Join::WITH, 'ma.id = mo.fk_marque')
            ->groupBy('ma.id')
            ->orderBy('ma.marque')
        ;

        // Récupère le paramètre de limite de résultat s'il a été définit dans l'URL
        if($request->query->getInt('max') > 0
            && is_integer($request->query->getInt('max'))
            && in_array($request->query->getInt('max'), $this->configPaginationList())
        ) {
            $donnees = $donnees->setMaxResults($this->configPaginationLimit())->getQuery();
        }

        // Traitement des données par KnpPaginator
        $lesMarquesPagination = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            $this->configPaginationLimit()
        );
        // Valeurs par défaut des résultats des filtres
        $lesMarquesForm = $lesMarquesPagination->getItems();

        $form = $this->createForm(FiltreTableMarqueType::class, $marqueRepository->findAll());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_marque');
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_marque'] !== "" || $data['marque'] !== "") {
                if ($data['id_marque']) { $value = $data['id_marque']; }
                else { $value = $data['marque']; }
                $lesMarquesForm = $marqueRepository->createQueryBuilder('ma')
                    ->select('ma.id')
                    ->addSelect('ma.marque')
                    ->addSelect('COUNT(mo.id) as nombre')
                    ->leftJoin(Modele::class, 'mo', Join::WITH, 'ma.id = mo.fk_marque')
                    ->where('ma.id = :value')
                    ->setParameter('value', $value)
                    ->groupBy('ma.id')
                    ->getQuery()
                    ->getResult()
                ;
            }
        }

        return $this->render('admin/marque/index.html.twig', [
            // Données pour Knppaginator
            'lesMarquesPagination' => $lesMarquesPagination,
            // Données pour le formulaire et tableau
            'lesMarquesForm' => $lesMarquesForm,
            'choixListe' => $this->configPaginationList(),
            'formFiltreTable' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'marque_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $marque = new Marque();
        $form = $this->createForm(NewMarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marque->setMarque(strtoupper($marque->getMarque()));
            $entityManager->persist($marque);
            $entityManager->flush();

            // return $this->redirectToRoute('marque_admin_index', [], Response::HTTP_SEE_OTHER);
            return $this->redirectToRoute('modele_admin_new', ['idmarque' => $marque->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/marque/new.html.twig', [
            'errors' => $form->getErrors(true),
            'formNewMarque' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'marque_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Marque $marque, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($marque == null) {
            $this->addFlash('marque', 'Cette marque n\'existe pas.');
            return $this->redirectToRoute('marque_admin_index');
        }

        $form = $this->createForm(EditMarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marque->setMarque(strtoupper($marque->getMarque()));
            $entityManager->persist($marque);
            $entityManager->flush();

            return $this->redirectToRoute('marque_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/marque/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formEditMarque' => $form
        ]);
    }

    #[Route('/{id}', name: 'marque_admin_delete', methods: ['POST'])]
    public function delete(Marque $marque, Request $request, EntityManagerInterface $entityManager, MarqueRepository $marqueRepository, ModeleRepository $modeleRepository): Response
    {
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($marque == null) {
            $this->addFlash('marque', 'Cette marque n\'existe pas.');
        }
        elseif(!empty($modeleRepository->findBy(['fk_marque' => $marque->getId()]))) {
            // Si l'identifiant existe dans la table correspondante, on génère un message d'erreur
            $this->addFlash('marque', 'Cette marque n\'est pas supprimable (un ou plusieurs modèles associés.');
        }
        elseif (
            $this->isCsrfTokenValid('delete'.$marque->getId(), $request->request->get('_token'))
            && empty($modeleRepository->findBy(['fk_marque' => $marque->getId()]))
        ) {
            // Vérifie le token puis supprime cet élément
            $entityManager->remove($marque);
            $entityManager->flush();
        }

        return $this->redirectToRoute('marque_admin_index', [], Response::HTTP_SEE_OTHER);
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
