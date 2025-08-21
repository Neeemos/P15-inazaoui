<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrez le nom de votre invité']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Entrez l\'email de votre invité']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['placeholder' => 'Entrez le mot de passe de votre invité']
            ])
              ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Entrez la description de cette utilisateur']
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Grade',
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Invité' => 'ROLE_GUEST',
                    'Bloqué' => 'IS_LOCKED',
                ],
                'multiple' => true,
                'expanded' => false,
            ]);


        if ($options['is_edit']) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false,
                'empty_data' => '',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false, // Option to determine if the form is for editing
        ]);
    }
}