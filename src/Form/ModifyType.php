<?php

namespace App\Form;

use App\Entity\Player;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifyType extends AbstractType implements FormTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label'=>false,
                'attr'    => [
                    'placeholder'=>'Login',
                    'required' => null,
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type'  => PasswordType::class,
                'first_options'   => [
                    'label'=>false,
                    'attr'    => [
                        'placeholder' => 'Mot de passe',
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
