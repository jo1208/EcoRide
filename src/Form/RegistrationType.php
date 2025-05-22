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
use Symfony\Component\Form\Extension\Core\Type\FileType;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, [
                'label' => 'Photo de profil (facultative)',
                'mapped' => false,
                'required' => false,
            ])
            ->add('email')
            ->add('password', PasswordType::class, [])
            ->add('nom')
            ->add('prenom')
            ->add('telephone', null,)
            ->add('adresse')
            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',
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
