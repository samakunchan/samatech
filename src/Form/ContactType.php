<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Contact;
use App\Services\FileService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    /**
     * @var FileService $fileService
     */
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('phone', TextType::class, ['required' => false])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository
                        ->createQueryBuilder('c')
                        ->andWhere('c.environnement = :val')
                        ->setParameter('val', '1')
                        ;
                },
                'choice_label' => function($category) {
                    /** @var Category $category */
                    return $category->getType() . ' - ' . $category->getDescription();
                },
            ])
            ->add('message', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
