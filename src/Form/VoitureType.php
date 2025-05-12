<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('immatriculation', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La plaque d\'immatriculation est obligatoire.']),
                ],
            ])
            ->add('modele', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le modèle est obligatoire.']),
                ],
            ])
            ->add('marque', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le marque est obligatoire.']),
                ],
            ])
            ->add('couleur', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La couleur est obligatoire.']),
                ],
            ])
            ->add('date_premiere_immatriculation', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'La date de première immatriculation est obligatoire.']),
                ],
            ])
            ->add('nb_place', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nombre de places est obligatoire.']),
                ],
            ])
            ->add('ecologique', CheckboxType::class, [
                'required' => false,
                'label' => 'Véhicule électrique ?',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
            'csrf_protection' => true,
        ]);
    }
}
