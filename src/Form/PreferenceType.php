<?php

namespace App\Form;

use App\Entity\Preference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fumeur', CheckboxType::class, [
                'required' => false,
                'label' => 'Fumeur',
            ])
            ->add('animal', CheckboxType::class, [
                'required' => false,
                'label' => 'Animal',
            ])
            ->add('musique', CheckboxType::class, [
                'required' => false,
                'label' => 'Musique',
            ])
            ->add('autres', TextareaType::class, [
                'required' => false,
                'label' => 'Autres préférences (ex : pas de parfum, silence, etc.)',
                'attr' => ['placeholder' => 'Tapez vos préférences personnelles ici...']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Preference::class,
        ]);
    }
}
