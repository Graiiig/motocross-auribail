<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, [
                'label'   => 'Adresse Email',
                'attr'=> [
                    'placeholder'=>'Adresse Email'
                ]
            ])
            
            ->add('roles', ChoiceType::class, array(
                'label'=>false,
                'choices' => 
                array
                (
                    'Administrateur' => 'ROLE_ADMIN',
                    'Membre' => 'ROLE_MEMBER', 
                    'Non membre' => 'ROLE_NON_MEMBER'
                    )
                ,
                'multiple' => true,
                'required' => true,
                'expanded'=>true
                ))
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
            ->add('birthdate', null, [
                
                'label'   => 'Date de naissance',
                'attr'=> [
                    'placeholder'=>'Date de naissance'],
                'widget'=>'single_text',
                'years' => range(1950, date('Y')-6) ])
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
            ->add('address', null, [
                'label'   => 'Adresse',
                'attr'=> [
                    'placeholder'=>'Adresse'
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
