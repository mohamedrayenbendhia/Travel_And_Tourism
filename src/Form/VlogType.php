<?php


namespace App\Form;

use App\Entity\Vlog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class VlogType extends AbstractType
{
   
// src/Form/VlogType.php

public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('content', TextareaType::class, [
            'label' => 'Contenu'
        ])
        ->add('image', FileType::class, [
            'label' => 'Image (JPG, PNG, GIF)',
            'mapped' => false, // Non mappé à l'entité
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
                ])
            ],
        ])
        ->add('video', FileType::class, [
            'label' => 'Vidéo (optionnelle)',
            'mapped' => false, // Non mappé à l'entité
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '10M',
                    'mimeTypes' => ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'],
                    'mimeTypesMessage' => 'Veuillez télécharger une vidéo valide (MP4, AVI, MOV, MKV)',
                ])
            ],
        ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vlog::class,
        ]);
    }
}
