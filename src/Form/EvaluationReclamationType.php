<?php
// src/Form/EvaluationReclamationType.php
namespace App\Form;

use App\Entity\EvaluationReclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', IntegerType::class, [
                'label' => 'Note (1-5)',
                'attr' => ['min' => 1, 'max' => 5]
            ])
            ->add('commentaire', TextType::class, [
                'label' => 'Commentaire'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EvaluationReclamation::class,
        ]);
    }
}
