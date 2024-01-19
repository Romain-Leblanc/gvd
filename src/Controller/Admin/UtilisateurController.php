<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\Admin\NewUtilisateurType;
use App\Form\Admin\EditUtilisateurType;
use App\Form\FiltreTable\Admin\FiltreTableUtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateur')]
class UtilisateurController extends AbstractController
{
    #[Route('/', name: 'utilisateur_admin_index', methods: ['GET', 'POST'])]
    public function index(UtilisateurRepository $utilisateurRepository, PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupère la requête puisque KnpPaginator s'occupe des données lui-même
        $donnees = $utilisateurRepository->createQueryBuilder('u');

        // Récupère le paramètre de limite de résultat s'il a été définit dans l'URL
        if($request->query->getInt('max') > 0
            && is_integer($request->query->getInt('max'))
            && in_array($request->query->getInt('max'), $this->configPaginationList())
        ) {
            $donnees = $donnees->setMaxResults($request->query->getInt('max'))->getQuery();
        }
        elseif ($request->query->get('sort') == "u.roles" && in_array($request->query->get('direction'), ['asc', 'desc'])) {
            // Le tri par role utilisateur avec createQueryBuilder ne fonctionne pas
            // donc on utilise une requête manuelle
            $sql = "SELECT u FROM App\Entity\Utilisateur u ORDER BY CONCAT('%', u.roles, '%') ".$request->query->get('direction');
            $donnees = $entityManager->createQuery($sql);
        }

        // Traitement des données par KnpPaginator
        $lesUtilisateursPagination = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            ($request->query->getInt('max') > 0) ? $request->query->getInt('max') : $this->configPaginationLimit()
        );

        // Valeurs par défaut des résultats des filtres
        $lesUtilisateursForm = $lesUtilisateursPagination->getItems();

        $form = $this->createForm(FiltreTableUtilisateurType::class, $lesUtilisateursPagination->getItems());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_utilisateur');
            $query = $utilisateurRepository->createQueryBuilder('u');
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_utilisateur'] !== "" || $data['utilisateur'] !== "" || $data['roles'] !== "") {
                if ($data['id_utilisateur'] !== "" || $data['utilisateur'] !== "") {
                    if ($data['id_utilisateur']) { (int) $value = $data['id_utilisateur']; }
                    else { $value = (int) $data['utilisateur']; }
                    $query = $query
                        ->andWhere('u.id = :id')
                        ->setParameter('id', $value)
                    ;
                }
                if ($data['roles'] !== "") {
                    $query = $query
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', "%".$data['roles']."%")
                    ;
                }
                $lesUtilisateursForm = $query->getQuery()->getResult();
            }
        }

        return $this->render('admin/utilisateur/index.html.twig', [
            // Données pour Knppaginator
            'lesUtilisateursPagination' => $lesUtilisateursPagination,
            // Données pour le formulaire et tableau
            'lesUtilisateursForm' => $lesUtilisateursForm,
            'choixListe' => $this->configPaginationList(),
            'formFiltreTable' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'utilisateur_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(NewUtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setPassword($passwordEncoder->hashPassword($utilisateur, $form->get('password')->getData()));
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('utilisateur_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/utilisateur/new.html.twig', [
            'errors' => $form->getErrors(true),
            'formNewUtilisateur' => $form,
        ]);
    }

    #[Route('/{id}', name: 'utilisateur_admin_show', defaults: ['id' => 0], methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($utilisateur == null) {
            $this->addFlash('utilisateur', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('utilisateur_admin_index');
        }

        return $this->render('admin/utilisateur/show.html.twig', [
            'unUtilisateur' => $utilisateur
        ]);
    }

    #[Route('/{id}/edit', name: 'utilisateur_admin_edit', defaults: ['id' => 0], methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($utilisateur == null) {
            $this->addFlash('utilisateur', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('utilisateur_admin_index');
        }

        $form = $this->createForm(EditUtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour des infos de l'utilisateur dont l'encodage du mot de passe
            if($form->get('password')->getData() != null && $form->get('password')->getData() != "") {
                $utilisateur->setPassword($passwordEncoder->hashPassword($utilisateur, $form->get('password')->getData()));
            }
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            // Si l'utilisateur modifié est celui actuellement connecté, on le force à se reconnecter
            if ($utilisateur->getId() === $this->getUser()->getId()) {
                return $this->redirectToRoute('app_logout');
            }
            return $this->redirectToRoute('utilisateur_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/utilisateur/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationUtilisateur' => $form
        ]);
    }

    #[Route('/{id}', name: 'utilisateur_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on génère une erreur
        if($utilisateur == null) {
            $this->addFlash('utilisateur', 'Cet utilisateur n\'existe pas.');
        }
        elseif ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            // Si l'utilisateur supprimé est celui actuellement connecté, on le force à se reconnecter
            if ($utilisateur->getId() === $this->getUser()->getId()) {
                // Réinitialise la session utilisateur
                $this->container->get('security.token_storage')->setToken(null);
                // Supprime l'utilisateur et redirige vers le formulaire de connexion
                $entityManager->remove($utilisateur);
                return $this->redirectToRoute('app_logout');
            }
            else {
                // Vérifie le token puis supprime cet élément
                $entityManager->remove($utilisateur);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('utilisateur_admin_index', [], Response::HTTP_SEE_OTHER);
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