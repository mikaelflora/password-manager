<?php

namespace App\Form;

use App\Entity\Credential;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CredentialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('image', FileType::class, [
                'mapped' => false,
                'label_attr' => [
                    'class' => 'inner-file-label',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/svg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (jpeg, png, svg)',
                    ])
                ],
            ])
            ->add('url', TextType::class, [
                'required' => false,
            ])
            ->add('login', TextType::class, [
                'required' => false,
            ])
            ->add('password', TextType::class, [
                'required' => false,
                'mapped' => true,
                'attr' => [
                    'type' => 'password',
                ]
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Credential::class,
        ]);
    }
}
