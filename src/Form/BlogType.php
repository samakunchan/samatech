<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Category;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\TagsInputType;
use App\Services\FileService;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
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
            ->add('status', CheckboxType::class, [
                'label'    => 'Publier l\'article?',
                'required' => false
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Blog */
                if ($event->getData()->getMainImage() !== null) {
                    $image = $event->getData()->getMainImage();
                    if ($image->getFile()) {
                        $data = $this->fileService->transformToWebP($image->getFile());
                        $image->setCompleteUrl($data['filename']);
                        $image->setFolder($data['folder']);
                        $image->setExt($data['ext']);
                        $image->setTitle('Image blog '. $event->getData()->getTitle());
                        $image->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
                        $event->getData()->setMainImage($image);
                    }
                }
                $event->getData()->setSlug($event->getData()->getTitle());
                $event->getData()->setView(0);
            })
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
