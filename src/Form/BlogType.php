<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Category;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\TagsInputType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class, ['required' => false])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('c')
                        ->andWhere('c.environnement = :val')
                        ->setParameter('val', '2')
                        ;
                },
                'choice_label' => 'type'
            ])
            ->add('mainImage', DocumentType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('tags', TagsInputType::class, [
                'label' => 'Ajouter des tags',
                'required' => false,
            ])
            ->add('createdAt', DateTimePickerType::class, [
                'label' => 'Date de publication',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
