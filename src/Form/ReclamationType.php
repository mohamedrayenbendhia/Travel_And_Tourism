<?php
// src/Form/ReclamationType.php
namespace App\Form;
use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            ->add('description', TextareaType::class)
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('document', FileType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Problème avec le logement' => 'logement',
                    'Problème de service' => 'service',
                    'Comportement de l\'hôte' => 'comportement',
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Soumettre la réclamation']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
