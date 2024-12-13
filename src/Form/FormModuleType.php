<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\FormModule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormModuleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('moduleName', TextType::class, [
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'CategoryModuleName',
                'expanded' => true,
                'multiple' => false,
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'form-check-input', 
                    ],
                'row_attr' => [
                    'class' => 'form-check',
                ]
            ])
            ->add('valider', SubmitType::class, [
                'attr'  => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormModule::class,
        ]);
    }
}
