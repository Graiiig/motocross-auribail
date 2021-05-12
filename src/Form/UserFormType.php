<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, [
                'label'   => false,
                'attr'=> [
                    'placeholder'=>'Addresse email'
                ]
            ])
            ->add('lastName', null, [
                'label'   => false,
                'attr'=> [
                    'placeholder'=>'Nom'
                ]
            ])
            ->add('firstName', null, [
                'label'   => false,
                'attr'=> [
                    'placeholder'=>'Prénom'
                ]
            ])
            ->add('address', null, [
                'label'   => false,
                'attr'=> [
                    'placeholder'=>'Adresse'
                ]
            ])
            ->add('phoneNumber', null, [
                'label'   => false,
                'attr'=> [
                    'placeholder'=>'N° de téléphone'
                ]
            ])
            ->add('license', null, [
                'label'   => false,
                'attr'=> [
                    'placeholder'=>'N° de licence'
                ]
            ])
            ->add('birthdate', null, [
                'label'   => 'Date d\'anniversaire',
                'years' => range(1950, date('Y')-6),
                'attr'=> [
                    'placeholder'=>'Date d\'anniversaire'
                ]
            ])
            // TODO : Chemin vers un avatar de base
            ->add('profilePicture', null, [
                'label'   => false, 
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
