<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Facture;
use App\Entity\MoyenPaiement;
use App\Entity\TVA;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $uneFacture = $options['data'];
        $dateMinPaiement = $uneFacture->getDateFacture();
        $dateMaxPaiement = clone $dateMinPaiement;
        $dateMaxPaiement->modify('+ 3 weeks');
        $dateFormat = 'Y-m-d';

        // Définit la modification du formulaire en fonction des valeurs du moyen/date de paiement
        if($uneFacture->getFkMoyenPaiement() == null) { $etatMoyenPaiement = false; }
        else { $etatMoyenPaiement = true; }
        if($uneFacture->getDatePaiement() == null) { $etatDatePaiement = false; }
        else { $etatDatePaiement = true; }

        // La date est cachée puisque elle sera simplement affichée
        $builder
            ->add('date_facture', HiddenType::class);
        $builder->get('date_facture')->addModelTransformer( new DateTimeToStringTransformer());
        $builder->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'select2-value-100',
                    'disabled' => true
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
                    return $taux->getTaux()." %";
                },
                'attr' => [
                    'class' => 'form-select input-50',
                    'disabled' => true
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
                    'disabled' => $etatMoyenPaiement
                ],
                'label' => "Moyen paiement :",
                'label_attr' => [
                    'class' => 'text-center col-md-6 col-form-label'
                ],
                'required' => true
            ]);
        if ($etatMoyenPaiement == true && $etatDatePaiement == true) {
            $builder
                ->add('date_paiement', DateType::class, [
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'form-control',
                        'disabled' => $etatDatePaiement
                    ],
                    'label' => "Date paiement :",
                    'label_attr' => [
                        'class' => 'text-center col-md-6 col-form-label'
                    ],
                    'required' => true
                ]);
        }
        else {
            $builder
                ->add('date_paiement', DateType::class, [
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'form-control',
                        'min' => $dateMinPaiement->format($dateFormat),
                        'max' => $dateMaxPaiement->format($dateFormat),
                        'disabled' => $etatDatePaiement
                    ],
                    'label' => "Date paiement :",
                    'label_attr' => [
                        'class' => 'text-center col-md-6 col-form-label'
                    ],
                    'required' => true
                ]);
        }
        $builder
            ->add('montant_ht', HiddenType::class, ['required' => true])
            ->add('montant_tva', HiddenType::class, ['required' => true])
            ->add('montant_ttc', HiddenType::class, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}