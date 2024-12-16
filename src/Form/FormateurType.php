<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class FormateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'attr'  => [
                'class' => 'form-control'
        ]])
        ->add('prenom', TextType::class, [
            'attr'  => [
                'class' => 'form-control'
        ]])
        ->add('email', TextType::class, [
            'attr'  => [
                'class' => 'form-control'
        ]])
        ->add('dateNaissance', DateType::class, [
            'widget' => 'single_text',
            'attr'  => [
                'class' => 'form-control'
            ]
        ])
        ->add('ville', TextType::class, [
            'attr'  => [
                'class' => 'form-control'
        ]])
        ->add('phone', TextType::class, [
            'attr'  => [
                'class' => 'form-control'
        ]])
        ->add('password', PasswordType::class, [
            'required' => true,
            'attr'  => [
                'class' => 'form-control'
            ]
        ])
        ->add('sessions', EntityType::class, [
            'class' => Session::class, // Entité Session
            'multiple' => true, // Permet la sélection multiple
            'expanded' => true, // Optionnel : afficher sous forme de cases à cocher
            'required' => false,
        ])
        ->add('valider', SubmitType::class, [
            'label' => 'Valider',
            'attr' => [
                'class' => 'btn btn-primary'
                ]
            ]);
    ;
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formateur::class,
        ]);
    }
}
