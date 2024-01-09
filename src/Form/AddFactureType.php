<?php

namespace App\Form;

use App\Entity\Vehicule;
use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\TypeEtat;
use App\Entity\Facture;
use App\Entity\Intervention;
use App\Entity\MoyenPaiement;
use App\Entity\TVA;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateFacture = new \DateTime();

        // La date de facture sera ajoutée dans le contrôleur
        $builder
            ->add('date_facture', HiddenType::class);
        $builder->get('date_facture')->addModelTransformer( new DateTimeToStringTransformer());
            $builder->add('client', EntityType::class, [
                'class' => Client::class,
                "placeholder" => "-- CLIENTS --",
                // Retourne la liste des clients qui ont au moins 1 intervention terminée non facturé
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("c")
                        ->select("c")
                        ->innerJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_client = c.id')
                        ->innerJoin(Intervention::class, 'i', Join::WITH, 'i.fk_vehicule = v.id')
                        ->innerJoin(Etat::class, 'e', Join::WITH, 'i.fk_etat = e.id')
                        ->innerJoin(TypeEtat::class, 'te', Join::WITH, 'e.fk_type_etat = te.id')
                        ->where("e.etat = :etat")
                        ->andWhere("te.type = :type_etat")
                        ->andWhere("i.fk_facture IS NULL")
                        ->setParameter('etat', 'Terminé')
                        ->setParameter('type_etat', 'intervention')
                    ;
                },
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'getInfosFromClientFacture();'
                ],
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'mapped' => false,
                'required' => true
            ])
            ->add('fk_taux', EntityType::class, [
                'class' => TVA::class,
                'choice_label' => function(TVA $taux){
                    return str_replace(".", ",", $taux->getTaux())." %";
                },
                'attr' => [
                    'class' => 'form-select input-50',
                    'onchange' => 'changeTotalFromTaux();',
                    'disabled' => true,
                ],
                'label' => "Taux :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_moyen_paiement', EntityType::class, [
                'class' => MoyenPaiement::class,
                'choice_label' => function(MoyenPaiement $moyenPaiement){
                    return $moyenPaiement->getMoyenPaiement();
                },
                'placeholder' => '-- Moyen paiement --',
                'attr' => [
                    'class' => 'form-select',
                    'disabled' => true,
                ],
                'label' => "Moyen paiement :",
                'label_attr' => [
                    'class' => 'text-center col-md-6 col-form-label'
                ],
                'required' => false
            ])
            ->add('date_paiement', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => $dateFacture->format('Y-m-d'),
                    'disabled' => true,
                ],
                'label' => "Date paiement :",
                'label_attr' => [
                    'class' => 'text-center col-md-6 col-form-label'
                ],
                'required' => false
            ])
            ->add('montant_ht', HiddenType::class, [
                'data' => '0,00 €'
            ])
            ->add('montant_tva', HiddenType::class, [
                'data' => '0,00 €'
            ])
            ->add('montant_ttc', HiddenType::class, [
                'data' => '0,00 €'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
