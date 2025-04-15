<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modele')
            ->add('immatriculation')
            ->add('couleur')
            ->add('nb_place')
            ->add('date_premiere_immatriculation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de première immatriculation'
            ])
            ->add('ecologique', CheckboxType::class, [
                'required' => false,
                'label' => 'Voiture écologique ?'
            ]);
        // PAS de champ 'user' ici !
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
