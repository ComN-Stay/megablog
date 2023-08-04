<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('short_description', TextareaType::class, [
                'label' => 'Description rapide',
                'required' => true,
                'attr' => [
                    'class' => 'tinymce',
                    ],
                ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'class' => 'tinymce',
                    ],
                ])
            ->add('fk_category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Categories::class,
                'choice_label' => 'name',
            ])
            ->add('logo', FileType::class, [
                'data_class' => null,
                'label' => 'Logo',
                'required' => false,
                'help' => 'Fichier jpg, jpeg, png ou webp ne dépassant pas 1 Mo',
                'constraints' => [
                    new File([ 
                        //'maxSize' => '10M',
                        //'maxSizeMessage' => 'Votre fichier ne doit pas dépasser les 1 M0',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => "Merci de télécharger une image valide.",
                    ]),
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Publication',
                'expanded' => true,
                'choices' => [
                    'En attente de publication' => false,
                    'En ligne' => true,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
