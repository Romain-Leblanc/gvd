<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\TypeEtat;
use App\Entity\Intervention;
use App\Entity\Vehicule;
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

class AddInterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Initialisation des dates
        $dateCreation = new \DateTime();
        $dateIntervention = new \DateTime();

        // Impossible d'ajouter une intervention pour le dimanche, donc on la reporte au lendemain
        // Sinon on ajoute un jour à la date de l'intervention
        $dateLendemain = $dateIntervention->modify('+ 1 day');
        if(date("l", $dateLendemain->getTimestamp()) == "Sunday") {
            $date = date("Y-m-d", date_modify($dateLendemain, ' + 1 day')->getTimestamp());
        }
        else {
            $date = date("Y-m-d", $dateLendemain->getTimestamp());
        }
        // On transforme la chaine de date en objet DateTime
        $dateIntervention = new \DateTime($date);

        // Les champs "date_creation" et "fk_facture" ne sont pas présent
        // puisqu'ils utilisent respectivement la date du jour et une valeur toujours NULL à l'ajout

        $builder->add('date_intervention', DateType::class, [
                'widget' => 'single_text',
                'data' => $dateIntervention,
                'attr' => [
                    'class' => 'form-control input-50',
                    'min' => $dateCreation->format('Y-m-d')
                ],
                'label' => "Date intervention :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                "placeholder" => "-- CLIENTS --",
                // Sélection des clients de véhicules possibles
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('c')
                        ->select("c")
                        ->innerJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_client = c.id')
                        ->innerJoin(Etat::class, 'e', Join::WITH, 'v.fk_etat = e.id')
                        ->innerJoin(TypeEtat::class, 'te', Join::WITH, 'e.fk_type_etat = te.id')
                        ->where('e.etat = :etat')
                        ->andWhere('te.type = :type')
                        ->setParameter(':etat', 'Fonctionnel')
                        ->setParameter(':type', 'vehicule')
                        ->groupBy('c.id')
                        ->distinct()
                        ;
                },
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'getInfosFromClientIntervention();',
                ],
                'label' => 'Client :',
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'mapped' => false,
                'required' => true
            ])
            ->add('fk_vehicule', EntityType::class, [
                'class' => Vehicule::class,
                "placeholder" => "-- VEHICULE --",
                'choice_label' => function(Vehicule $vehicule){
                    return mb_strtoupper($vehicule->getFKModele()->getFKMarque()->getMarque())." ".ucfirst($vehicule->getFKModele()->getModele())." (".mb_strtoupper($vehicule->getImmatriculation()).")";
                },
                'attr' => [
                    'class' => 'text-center select2-value-100',
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "Véhicule :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_etat', EntityType::class, [
                'class' => Etat::class,
                // Sélection de l'état par défaut "en attente" puisqu'on ajoute simplement une intervention
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("e")
                        ->select('e')
                        ->innerJoin(TypeEtat::class, 'te', Join::WITH, 'e.fk_type_etat = te.id')
                        ->where('e.etat LIKE :etat')
                        ->andWhere('te.type = :type')
                        ->setParameter(':etat', '%attente%')
                        ->setParameter(':type', 'intervention')
                        ;
                },
                'choice_label' => function(Etat $etat){
                    return ucfirst($etat->getEtat());
                },
                'attr' => [
                    'class' => 'form-select input-50',
                    // Actualisé par Ajax
                    'disabled' => true
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
                    // Actualisé par Ajax
                    'disabled' => true
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
                    'value' => 1,
                    // Actualisé par Ajax
                    'disabled' => true
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
                    'value' => 0,
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "Montant HT (en €) :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
        ]);
    }
}