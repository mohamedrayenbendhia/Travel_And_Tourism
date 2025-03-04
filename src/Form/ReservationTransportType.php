<?php

namespace App\Form;

use App\Entity\ReservationTransport;
use App\Entity\Transport;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationTransportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Start Date'
            ])
            ->add('end_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'End Date'
            ])
            ->add('statut')
            ->add('user_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('transport_id', EntityType::class, [
                'class' => Transport::class,
                'choice_label' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationTransport::class,
        ]);
    }
}