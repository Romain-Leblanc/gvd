<?php

namespace App\Form\Admin;

use App\Entity\Marque;
use App\Entity\Modele;
use App\Repository\MarqueRepository;
use App\Validator\Admin\Modele as admin_modele;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewModeleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if($options['data']->getFkMarque() != null) {
            $data = $options['data']->getFkMarque();
            $builder
                ->add('fk_marque', EntityType::class, [
                    'class' => Marque::class,
                    'choice_label' => function(Marque $marque){
                        return mb_strtoupper($marque->getMarque());
                    },
                    'query_builder' => function(MarqueRepository $marqueRepository) use ($data) {
                        return $marqueRepository->createQueryBuilder('ma')
                            ->select('ma')
                            ->where('ma.id = :id_marque')
                            ->setParameter(':id_marque', $data);
                    },
                    'attr' => [
                        'class' => 'select2-value-100'
                    ],
                    'label' => "Marque :",
                    'label_attr' => [
                        'class' => 'text-center col-md-5 col-form-label'
                    ],
                    'required' => true
                ]);
        }
        else {
            $builder
                ->add('fk_marque', EntityType::class, [
                    'class' => Marque::class,
                    "placeholder" => "-- Marque --",
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
                ]);
        }
        $builder
            ->add('modele', TextType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'placeholder' => 'Saisir un modèle'
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
