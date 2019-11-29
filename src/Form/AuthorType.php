<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'label_attr'=>[
                    'class'=>'label-form'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'label_attr'=>[
                    'class'=>'label-form'
                ]
            ])
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
                'required' => false,
                'label_attr'=>[
                    'class'=>'label-form'
                ]
                ])
            ->add('deathDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de décès',
                'required' => false,
                'label_attr'=>[
                    'class'=>'label-form'
                ]
            ])
            ->add('biography', TextareaType::class, [
                'label' => 'Biographie',
                'required' => false,
                'label_attr'=>[
                    'class'=>'label-form'
                ]
            ])
            ->add('enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
