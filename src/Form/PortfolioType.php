<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Portfolio;
use App\Form\Type\TagsInputType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PortfolioType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('c')
                        ->andWhere('c.environnement = :val')
                        ->setParameter('val', '4')
                        ;
                },
                'choice_label' => 'type'
            ])
            ->add('tags', TagsInputType::class, [
                'label' => 'Ajouter des tags',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Portfolio::class,
        ]);
    }
}
