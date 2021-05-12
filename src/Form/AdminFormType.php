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
            ->add('email')
            
            ->add('roles', ChoiceType::class, array(
                
                'choices' => 
                array
                (
                    'admin' => 'ROLE_ADMIN',
                    'membre' => 'ROLE_MEMBER', 
                    'non membre' => 'ROLE_NON_MEMBER'
                    )
                ,
                'multiple' => true,
                'required' => true,
                'expanded'=>true
                ))
            ->add('lastName')
            ->add('firstName')
            ->add('birthdate', null, [
                'years' => range(1900, date('Y')) ])
            ->add('phoneNumber')
            ->add('license')
            ->add('address')
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
