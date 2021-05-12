<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, [
                'label'   => 'Addresse email',
                'attr'=> [
                    'placeholder'=>'Addresse email'
                ]
            ])
            ->add('lastName', null, [
                'label'   => 'Nom',
                'attr'=> [
                    'placeholder'=>'Nom'
                ]
            ])
            ->add('firstName', null, [
                'label'   => 'Prénom',
                'attr'=> [
                    'placeholder'=>'Prénom'
                ]
            ])
            ->add('address', null, [
                'label'   => 'Adresse',
                'attr'=> [
                    'placeholder'=>'Adresse'
                ]
            ])
            ->add('phoneNumber', null, [
                'label'   => 'N° de téléphone',
                'attr'=> [
                    'placeholder'=>'N° de téléphone'
                ]
            ])
            ->add('license', null, [
                'label'   => 'N° de licence',
                'attr'=> [
                    'placeholder'=>'N° de licence'
                ]
            ])
            ->add('birthdate', DateType::class, [
                'label'   => 'Date de naissance',
                'years' => range(1950, date('Y')-6),
                'widget'=>'single_text',
                'attr'=> [
                    'placeholder'=>'Date de naissance'
                ]
            ])
            // TODO : Chemin vers un avatar de base
            ->add('profilePicture', null, [
                'label'   => 'Téléchargez une image de profil', 
                'attr'=> [
                    'placeholder'=>'Téléchargez une image de profil'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
