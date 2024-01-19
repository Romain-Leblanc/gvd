<?php

namespace App\Form\Admin;

use App\Entity\Marque;
use App\Entity\Modele;
use App\Validator\Admin\Modele as admin_modele;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditModeleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fk_marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => function(Marque $marque){
                    return mb_strtoupper($marque->getMarque());
                },
                'attr' => [
                    'class' => 'select2-value-100'
                ],
                'label' => "Marque :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('modele', TextType::class, [
                'attr' => [
                    'class' => 'form-control input-50'
                ],
                'label' => "Modèle :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
                'constraints' => [
                    new admin_modele()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Modele::class,
        ]);
    }
}
