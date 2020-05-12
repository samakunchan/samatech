<?php

namespace App\Form;

use App\Entity\Service;
use App\Services\FileService;
use DateTime;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
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
            ->add('icone', DocumentType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('image', DocumentType::class, [
                'label' => false,
                'required' => false
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Service */
                if ($event->getData()->getIcone() !== null) {
                    $icone = $event->getData()->getIcone();
                    if ($icone->getFile()){
                        $dataIcone = $this->fileService->transformToWebP($icone->getFile());
                        $icone->setCompleteUrl($dataIcone['filename']);
                        $icone->setFolder($dataIcone['folder']);
                        $icone->setExt($dataIcone['ext']);
                        $icone->setTitle('Icone '.$event->getData()->getTitle());
                        $icone->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
                        $event->getData()->setIcone($icone);
                    }
                }
                /** @var Service */
                if ($event->getData()->getImage() !== null) {
                    $image = $event->getData()->getImage();
                    if ($image->getFile()){
                        $dataImage = $this->fileService->transformToWebP($image->getFile());
                        $image->setCompleteUrl($dataImage['filename']);
                        $image->setFolder($dataImage['folder']);
                        $image->setExt($dataImage['ext']);
                        $image->setTitle('Image '.$event->getData()->getTitle());
                        $image->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
                        $event->getData()->setImage($image);
                    }
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
