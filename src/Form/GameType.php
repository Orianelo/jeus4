<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Player;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tour', null, [
                'label' => 'Tour N°',
                'attr'    => [
                    'class' => 'form-control'
                ]
            ])
            ->add('tour_joueur', null, [
                'label' => 'Joueur',
                'attr'    => [
                    'class' => 'form-control'
                ]
            ])
            ->add('etat', null, [
                'label' => 'Partie Terminée',
                'attr'    => [
                    'class' => 'checkbox'
                ]
            ])
            ->add('j1', EntityType::class, [
                'class' => Player::class,
                'choice_label'  => 'username',
                'attr'    => [
                    'class' => 'form-control'
                ]
            ])
            ->add('j2', EntityType::class, [
                'class' => Player::class,
                'choice_label'  => 'username',
                'attr'    => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
