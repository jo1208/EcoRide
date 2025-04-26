<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'choices' => [
                    '⭐' => 1,
                    '⭐⭐' => 2,
                    '⭐⭐⭐' => 3,
                    '⭐⭐⭐⭐' => 4,
                    '⭐⭐⭐⭐⭐' => 5,
                ],
                'expanded' => true, // boutons radios
                'multiple' => false,
                'label' => 'Note du trajet',
                'attr' => [
                    'class' => 'd-flex gap-3', // ✅ Ajoute un espace entre les boutons
                ],
            ])
            ->add('trajetBienPasse', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,  // radio boutons
                'multiple' => false,
                'label' => 'Le trajet s\'est-il bien passé ?',
                'data' => true,  // ✅ valeur par défaut = Oui
                'attr' => [
                    'class' => 'd-flex gap-3',
                ],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Ton commentaire',
                'attr' => [
                    'placeholder' => 'Ton retour sur le trajet...',
                    'rows' => 4,
                    'class' => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
