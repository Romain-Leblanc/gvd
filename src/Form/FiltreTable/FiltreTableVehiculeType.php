<?php

namespace App\Form\FiltreTable;

use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\TypeEtat;
use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreTableVehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupère la liste des véhicules
        $lesVehicules = $options['data'];

        $builder
            ->add('id_vehicule', ChoiceType::class, [
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'N° véhicule',
                'choices' => $lesVehicules,
                'choice_label' => 'id',
                'choice_value' => 'id',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client) {
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom());
                },
                // Sélection des clients de véhicules possibles
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('c')
                        ->innerJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_client = c.id')
                        ->addOrderBy('c.nom')
                        ->addOrderBy('c.prenom')
                    ;
                },
                'choice_value' => 'id',
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'Client'
            ])
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                // Sélection des marques-modèles de véhicule possibles
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("v")
                        ->innerJoin(Modele::class, 'mo', Join::WITH, 'v.fk_modele = mo.id')
                        ->innerJoin(Marque::class, 'ma', Join::WITH, 'mo.fk_marque = ma.id')
                        ->addOrderBy('ma.marque')
                        ->addOrderBy('mo.modele')
                    ;
                },
                'choice_label' => function(Vehicule $vehicule){
                    return mb_strtoupper($vehicule->getFKModele()->getFKMarque()->getMarque())." ".ucfirst($vehicule->getFKModele()->getModele());
                },
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'choice_value' => 'fk_modele.id',
                'placeholder' => '',
                'label' => 'Voiture'
            ])
            ->add('immatriculation', ChoiceType::class, [
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'Immatriculation',
                'choices' => $lesVehicules,
                'choice_label' => 'immatriculation',
                'choice_value' => 'immatriculation',
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => function(Etat $etat) {
                    return ucfirst($etat->getEtat());
                },
                // Sélection des états de véhicule possibles
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("e")
                        ->select("e")
                        ->innerJoin(TypeEtat::class, 'te', Join::WITH, 'e.fk_type_etat = te.id')
                        ->andWhere('te.type = :type')
                        ->setParameter(':type', 'vehicule')
                    ;
                },
                'choice_value' => 'id',
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'État'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
