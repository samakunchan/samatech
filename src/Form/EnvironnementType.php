<?php

namespace App\Form;

use App\Entity\Environnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnvironnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('categories', CollectionType::class, [
                'entry_type'   => CategoryType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'required' => false,
                'label' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Environnement */
                $categories = $event->getData()->getCategories();
                foreach ($categories as $category) {
                    $category->setSlug($category->getType());
                    $category->setEnvironnement($event->getData());
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Environnement::class,
        ]);
    }
}
