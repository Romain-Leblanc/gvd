<?php

namespace App\Form;

use App\Entity\Carburant;
use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\TypeEtat;
use App\Entity\Intervention;
use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use App\Form\EventSubscriber\ModeleSubscriber;
use App\Repository\ModeleRepository;
use App\Validator\Immatriculation;
use App\Validator\Kilometrage;
use App\Validator\Modele as modele_validator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditVehiculeType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fk_client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'select-client select2-value-100'
                ],
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ]
            ])
            ->add('fk_marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => function(Marque $marque){
                    return mb_strtoupper($marque->getMarque());
                },
                // Retourne la liste des marques qui ont au moins 1 modèle d'enregistré dans la table "Modèle"
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("ma")
                        ->innerJoin(Modele::class, 'mo', Join::WITH, 'mo.fk_marque = ma.id')
                        ->distinct()
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
                'query_builder' => function(ModeleRepository $modeleRepository) use($builder) {
                    return $modeleRepository->createQueryBuilder('mo')
                        ->select('mo')
                        ->where('mo.fk_marque = :id_marque')
                        ->setParameter(':id_marque', $builder->getData()->getFkModele()->getFkMarque()->getId())
                        ->orderBy('mo.id');
                },
                'choice_label' => function(Modele $modele){
                    return mb_strtoupper($modele->getModele());
                },
                'attr' => [
                    'class' => 'text-center select2-value-100',
                    'onchange' => 'enableBtnSubmitOnModele(this.value)'
                ],
                'label' => "Modèle :",
                'label_attr' => [
                    'class' => 'label-select-line col-md-6 col-form-label'
                ],
                'required' => true,
                'constraints' => [
                    new modele_validator()
                ],
            ])
            // Obligatoire pour traduire les valeurs transmises par Ajax
            // en entité afin d'être mis à jour ensuite
            ->addEventSubscriber(new ModeleSubscriber($this->em));
        $builder->add('fk_etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => function(Etat $etat){
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
                'attr' => [
                    'class' => 'form-select text-center input-50',
                ],
                'label' => "État :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
            ])
            ->add('fk_carburant', EntityType::class, [
                'class' => Carburant::class,
                'choice_label' => function(Carburant $carburant){
                    return mb_strtoupper($carburant->getCarburant());
                },
                'attr' => [
                    'class' => 'input-50 select2-value-50',
                ],
                'label' => "Carburant :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
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
                'data' => (int) date('Y')-10,
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

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // Si le véhicule est déjà dans une intervention, on désactive la modification du client, marque et modèle
        if(!empty($options['intervention'])) {
            $disabled = true;
        }
        else {
            $disabled = false;
        }
        $view->children['fk_client']->vars['attr']['disabled'] = $disabled;
        $view->children['fk_modele']->vars['attr']['disabled'] = $disabled;
        $view->children['fk_marque']->vars['attr']['disabled'] = $disabled;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
            'intervention' => Intervention::class, // Ajout de la classe "Intervention"
            'modele_repository' => ModeleRepository::class, // Ajout du repository "Modele"
        ]);
    }

}