<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use App\Service\FileService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bibliotheque")
 */
class BibliothequeController extends AbstractController
{
    /**
     * @Route("/", name="bibliotheque")
     * @param DocumentRepository $documentRepository
     * @return Response
     */
    public function index(DocumentRepository $documentRepository)
    {
        dump($documentRepository->findAll());
        return $this->render('bibliotheque/index.html.twig', [
            'documents' => $documentRepository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="bibliotheque_add_documents")
     * @param Request $request
     * @param FileService $service
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, FileService $service, EntityManagerInterface $entityManager): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $type = 'image';
            if ($form->getData()->getFile()->getMimeType() === 'application/pdf') {
                $type = 'pdf';
            }
            $fileName = $service->upload($form->getData()->getFile(), 'images');
            $document->setType($type);
            $document->setPath($fileName);
            if (!$document->getCreatedAt()) {
                $document->setCreatedAt(new DateTime('now'));
            }
            $document->setUpdatedAt(new DateTime('now'));

            $entityManager->persist($document);
            $entityManager->flush();
        }
        return $this->render('bibliotheque/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
