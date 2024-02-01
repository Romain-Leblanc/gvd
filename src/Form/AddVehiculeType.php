<?php

namespace App\Form;

use App\Entity\Carburant;
use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\TypeEtat;
use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use App\Validator\Immatriculation;
use App\Validator\Kilometrage;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddVehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fk_client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'select-client select2-value-100',
                ],
                'placeholder' => '-- CLIENTS --',
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
            ])
            ->add('fk_marque', EntityType::class, [
                'class' => Marque::class,
                "placeholder" => "-- Marque --",
                'choice_label' => function(Marque $marque){
                    return mb_strtoupper($marque->getMarque());
                },
                // Retourne la liste des marques qui ont au moins 1 modèle d'enregistré dans la table "Modèle"
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("ma")
                        ->innerJoin(Modele::class, 'mo', Join::WITH, 'mo.fk_marque = ma.id')
                        ->distinct()
                        ->addOrderBy('ma.marque')
                        ->addOrderBy('mo.modele')
                    ;
                },
                'attr' => [
                    'class' => 'text-center select2-value-100',
                    'onchange' => 'getModeleFromMarque(this.value);'
                ],
                'mapped' => false,
                'label' => "Marque :",
                'label_attr' => [
                    'class' => 'label-select-line col-md-6 col-form-label'
                ],
                'required' => true,
            ])
            // Obligatoire pour afficher et valider la valeur du champ Marque
            // en entité puisque ce champ n'est pas mappé (appartient à l'entité Modèle)
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $data = $event->getData();
                $form = $event->getForm();

                if ($data->getId() != null) {
                    $form->get('fk_marque')->setData($data->getFkModele()->getFkMarque());
                }
            });
        $builder->add('fk_modele', EntityType::class, [
                'class' => Modele::class,
                "placeholder" => "-- Modèle --",
                'choice_label' => function(Modele $modele){
                    return mb_strtoupper($modele->getModele());
                },
                'attr' => [
                    'class' => 'text-center select2-value-100',
                    'onchange' => 'enableBtnSubmitOnModele(this.value)',
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "Modèle :",
                'label_attr' => [
                    'class' => 'label-select-line col-md-6 col-form-label'
                ],
                'required' => true,
            ])
            ->add('fk_etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => function(Etat $etat){
                    return ucfirst($etat->getEtat());
                },
                // Sélection de l'état par défaut "fonctionnel" puisqu'on ajoute simplement un véhicule
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("e")
                        ->select("e")
                        ->innerJoin(TypeEtat::class, 'te', Join::WITH, 'e.fk_type_etat = te.id')
                        ->where('e.etat = :etat')
                        ->andWhere('te.type = :type')
                        ->setParameter(':etat', 'Fonctionnel')
                        ->setParameter(':type', 'vehicule')
                    ;
                },
                'attr' => [
                    'class' => 'form-select text-center input-50',
                ],
                'label' => "État :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ]
            ])
            ->add('fk_carburant', EntityType::class, [
                'class' => Carburant::class,
                "placeholder" => "-- Carburant --",
                'choice_label' => function(Carburant $carburant){
                    return mb_strtoupper($carburant->getCarburant());
                },
                'attr' => [
                    'class' => 'text-center input-50 select2-value-50',
                ],
                'label' => "Carburant :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ]
            ])
            ->add('annee', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'min' => (int) date('Y') - 75,
                    'max' => (int) date('Y'),
                    'placeholder' => ((int) date('Y') - 75)." à ".((int) date('Y')),
                ],
                'label' => "Année :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('kilometrage', IntegerType::class, [
                'attr' => [
                    'type' => 'number',
                    'precision' => false,
                    'scale' => false,
                    'class' => 'form-control text-center input-50',
                    'placeholder' => 'km',
                    'min' => 1
                ],
                'label' => "Kilométrage :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
                'constraints' => [
                    new Kilometrage()
                ]
            ])
            ->add('immatriculation', TextType::class, [
                'attr' => [
                    'class' => 'form-control text-center input-50',
                    'placeholder' => 'AA-123-AA'
                ],
                'label' => "Immatriculation :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
                'constraints' => [
                    new Immatriculation()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
