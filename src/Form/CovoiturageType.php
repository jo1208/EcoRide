<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user']; // ✅ On récupère l'utilisateur passé via les options

        $builder
            ->add('date_depart', null, [
                'widget' => 'single_text',
            ])
            ->add('heure_depart', null, [
                'widget' => 'single_text',
            ])
            ->add('lieu_depart')
            ->add('date_arrivee', null, [
                'widget' => 'single_text',
            ])
            ->add('heure_arrivee', null, [
                'widget' => 'single_text',
            ])
            ->add('lieu_arrivee')
            ->add('nb_place')
            ->add('prix_personne')
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => 'modele',
                'choices' => $user ? $user->getVoitures() : [],
                'placeholder' => $user && count($user->getVoitures()) > 0 ? 'Sélectionnez un véhicule' : '⚠️ Vous devez d\'abord enregistrer un véhicule',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
            'user' => null, // ✅ On autorise le passage d'un utilisateur
        ]);
    }
}
