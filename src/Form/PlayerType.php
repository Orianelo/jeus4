<?php

namespace App\Form;

use App\Entity\Player;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label' =>  'Login',
                'attr'    => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email',
                'attr'    => [
                    'class' => 'form-control'
                ]
            ])
            ->add('date_inscription', DateType::class, [
                'label' => 'Date Inscription',
            ])
            ->add('date_co', DateType::class, [
                'label' => 'Date Connexion'
            ])
            ->add('connexion', null, [
                'label' => 'Connexion',
                'attr'    => [
                    'class' => 'checkbox'
                ]
            ])
            ->add('blocage', null, [
                'label' => 'Bloquage',
                'attr'    => [
                    'class' => 'checkbox'
                ]
            ])
            ->add('points', null , [
                'label' => 'Nombre de points',
                'attr'    => [
                    'class' => 'form-control'
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
