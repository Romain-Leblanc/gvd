<?php

namespace App\Form\Admin;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class EditUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control input-50'
                ],
                'label' => "Email :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                // Le mot de passe sera encodé dans le contrôleur
                // donc il est déterminé à "false"
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control input-50',
                    'placeholder' => 'Aucune modification du mot de passe si champ vide'
                ],
                'label' => 'Mot de passe :',
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-select input-50',
                ],
                'multiple' => true,
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'label' => 'Rôle :',
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control input-50'
                ],
                'label' => "Nom :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control input-50'
                ],
                'label' => "Prénom :",
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
            'data_class' => Utilisateur::class,
        ]);
    }
}
