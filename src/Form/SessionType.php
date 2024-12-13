<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formateur;
use App\Entity\Formation;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sessionName', TextType::class, [                
                'attr'  => [
                'class' => 'form-control'
            ]
            ])
            ->add('dateDebutSession', DateType::class, [
                'widget' => 'single_text',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dateFinSession', DateType::class, [
                'widget' => 'single_text',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('nombrePlace', IntegerType::class, [
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('formateur', EntityType::class, [
                'class' => Formateur::class,
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('stagiaires', EntityType::class, [
                'class' => Stagiaire::class,
                'multiple' => true,
                'expanded' => true,
                'attr'  => [
                    'class' => 'form-control'
                ]
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
            'data_class' => Session::class,
        ]);
    }
}
