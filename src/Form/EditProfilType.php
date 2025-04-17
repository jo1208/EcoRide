<?php

namespace App\Form;

use App\Entity\User;
use PHPUnit\Framework\Constraint\LessThan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class)
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'required' => false,
                'mapped' => false, // ce champ n'est pas lié directement à l'entité User
            ])

            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', TextType::class)
            ->add('telephone', TextType::class)
            ->add('adresse', TextType::class)
            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre date de naissance.',
                    ]),
                    new LessThan([
                        'value' => 'today',
                        'message' => 'La date de naissance n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('isChauffeur', CheckboxType::class, [
                'label' => 'Je souhaite être chauffeur',
                'required' => false,
            ])
            ->add('isPassager', CheckboxType::class, [
                'label' => 'Je souhaite être passager',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
