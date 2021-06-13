<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Security\Core\Security;

class UserFormType extends AbstractType
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
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
            ->add('profilePicture', FileType::class, [
                'label' => 'Importez un avatar',
                'data_class' => null,
                'attr' => ['placeholder' => $this->security->getUser()->getProfilePicture()],
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image au format JPG/PNG valide',
                    ])
                ],
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
