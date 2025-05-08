<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères.',
                    ])
                ],
            ])
            ->add('nom')
            ->add('prenom')
            ->add('telephone', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre numéro de téléphone.',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 10,
                        'exactMessage' => 'Le numéro de téléphone doit contenir exactement {{ limit }} chiffres.',
                    ]),
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^[0-9]{10}$/',
                        'message' => 'Le numéro de téléphone doit être composé uniquement de chiffres (sans espace ni tiret).',
                    ]),
                ],
            ])

            ->add('adresse')
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
            ->add('pseudo');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'registration_item',
        ]);
    }
}
