<?php

namespace App\Form;

use App\Entity\About;
use App\Services\FileService;
use DateTime;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AboutType extends AbstractType
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
            ->add('title')
            ->add('description')
            ->add('image', DocumentType::class, [
                'label' => false,
                'required' => false
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var About */
                if ($event->getData()->getImage()->getFile() !== null) {
                    $image = $event->getData()->getImage();
                    $data = $this->fileService->transformToWebP($image->getFile());
                    $image->setCompleteUrl($data['filename']);
                    $image->setFolder($data['folder']);
                    $image->setExt($data['ext']);
                    $image->setTitle('Image de moi-même');
                    $image->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
                    $event->getData()->setImage($image);
                }
                $event->getData()->setSlug(About::DEFAULT_SLUG);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => About::class,
        ]);
    }
}
