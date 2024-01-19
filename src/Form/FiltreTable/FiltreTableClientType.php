<?php

namespace App\Form\FiltreTable;

use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreTableClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupère la liste des clients
        $lesClients = $options['data'];

        $builder
            ->add('id_client', ChoiceType::class, [
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'N° client',
                'choices' => $lesClients,
                'choice_label' => 'id',
                'choice_value' => 'id',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client) {
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom());
                },
                'choice_value' => 'id',
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'Client'
            ])
            ->add('coordonnees', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'onchange' => 'submit();'
                ],
                'label' => 'Coordonnées',
            ])
            ->add('adresse_complete', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'onchange' => 'submit();'
                ],
                'label' => 'Adresse complète',
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