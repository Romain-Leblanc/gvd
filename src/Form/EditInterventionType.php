<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\TypeEtat;
use App\Entity\Intervention;
use App\Entity\Vehicule;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditInterventionType extends AbstractType
{
    private $etatRepository;

    public function __construct(EtatRepository $etatRepository)
    {
        $this->etatRepository = $etatRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Date minimale pour la date d'intervention
        $dateCreation = $options['data']->getDateCreation();
        // Récupère le libellé de l'état de l'intervention
        $etat = $this->etatRepository->find($builder->getData()->getFkEtat()->getId())->getEtat();

        // Une fois l'intervention créée, il n'est pas possible de modifier le client ni le véhicule
        $etatClient = true;
        $etatVehicule = true;
        // Si l'état de la facture est à "Facturé", on désactive la modification du formulaire
        if($etat == "Facturé") {
            $etatElements = true;
            $etatDateIntervention = true;
            $etatFkEtat = true;
        }
        else { // Sinon on ne désactive rien sauf le client et le véhicule
            $etatElements = false;
            $etatDateIntervention = false;
            $etatFkEtat = false;
        }

        // Les champs "date_creation" et "fk_facture" ne sont pas présent puisqu'ils ne seront pas affichés

        $builder
            ->add('date_intervention', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control input-50',
                    'min' => $dateCreation->format('Y-m-d'),
                    'disabled' => $etatDateIntervention,
                ],
                'label' => "Date intervention :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ]);
            $builder->add('client', EntityType::class, [
                'class' => Client::class,
                // Retourne la liste des clients qui ont au moins 1 véhicule d'enregistré dans la table "Véhicule"
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("c")
                        ->select("c")
                        ->innerJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_client = c.id')
                        ->groupBy("c.id")
                        ->distinct()
                    ;
                },
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'select2-value-100',
                    'disabled' => $etatClient,
                ],
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'mapped' => false,
                'required' => true
            ])
            ->add('fk_vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => function(Vehicule $vehicule){
                    return mb_strtoupper($vehicule->getFKModele()->getFKMarque()->getMarque())." ".ucfirst($vehicule->getFKModele()->getModele())." (".mb_strtoupper($vehicule->getImmatriculation()).")";
                },
                'attr' => [
                    'class' => 'text-center select2-value-100',
                    'disabled' => $etatVehicule,
                ],
                'label' => "Véhicule :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_etat', EntityType::class, [
                'class' => Etat::class,
                // Sélection des états d'intervention possibles hormis "Facturé"
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("e")
                        ->select("e")
                        ->innerJoin(TypeEtat::class, 'te', Join::WITH, 'e.fk_type_etat = te.id')
                        ->where("e.etat NOT LIKE :etat")
                        ->andWhere("te.type = :type")
                        ->setParameter(':etat', '%Facturé%')
                        ->setParameter(':type', 'intervention')
                    ;
                },
                'choice_label' => function(Etat $etat){
                    return ucfirst($etat->getEtat());
                },
                'attr' => [
                    'class' => 'form-select input-50',
                    'disabled' => $etatFkEtat,
                ],
                'label' => "État :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('detail', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                    'cols' => 50,
                    'disabled' => $etatElements,
                ],
                'label' => "Détail intervention :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            // Durée d'intervention en heure
            ->add('duree', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'min' => 1,
                    'max' => 50,
                    'disabled' => $etatElements,
                ],
                'label' => "Durée (en heures) :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('montant_ht', NumberType::class, [
                // Affiche ce type en <input type='number'>
                'html5' => true,
                'attr' => [
                    'type' => 'number',
                    'precision' => 3,
                    'scale' => 1,
                    'class' => 'form-control input-50',
                    'min' => 0,
                    'disabled' => $etatElements,
                ],
                'label' => "Montant HT (en €) :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class
        ]);
    }
}