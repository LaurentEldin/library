<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Je rajoute le type de l'input + un array avec les param supplémentaire que je désire (trouver dans la doc symfony)
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'label_attr'=>[
                    'class'=>'label-form'
                ]
            ])
            ->add('nbPages', IntegerType::class, [
                'label' => 'Nb de pages',
                'required' => false,
                'label_attr'=>[
                    'class'=>'label-form'
                ]
            ])
            ->add('style', ChoiceType::class, [
                'choices' => [
                    'disney' => 'disney',
                    'Horreur' => 'Horreur',
                    'épouvante' => 'épouvante',
                    'education' => 'education',
                    'policier' => 'policier',
                    'drama' => 'drama',
                    'porno' => 'porno',
                    'Religion' => 'Religion',
                    'Politique' => 'Politique',
                    'Manga' => 'Manga',
                    'Bande dessiné' => 'Bande dessiné',
                    'humour' => 'humour',
                ],
                'label' => 'Categorie',
                'label_attr'=>[
                    'class'=>'label-form'
                ]
            ])
            ->add('inStock', CheckboxType::class, [
                'label' => 'Disponible',
                'label_attr'=>[
                    'class'=>'label-form'
                ],
                'required' => null])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'fullName',
                'required' => false,
                'placeholder' => 'Choisissez un auteur'
            ])
            ->add('enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
