<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('icone', CollectionType::class, [
                'entry_type'   => DocumentType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'required' => false,
            ])
            ->add('image', CollectionType::class, [
                'entry_type'   => DocumentType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
