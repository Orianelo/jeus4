<?php

namespace App\Form;

use App\Entity\Player;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label'=>false,
                'attr'    => [
                    'placeholder'=>'Login'
                ]
            ])
            ->add('email', EmailType::class, [
                'label'=>false,
                'attr'    => [
                    'placeholder'=>'Email'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type'  => PasswordType::class,
                'first_options'   => [
                    'label'=>false,
                    'attr'    => [
                        'placeholder' => 'Mot de passe'
                    ]
                ],
                'second_options'  => [
                    'label'=>false,
                    'attr'    => [
                        'placeholder' => 'Confirmation Mot de passe'
                    ]
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Player::class,
        ]);
    }
}
