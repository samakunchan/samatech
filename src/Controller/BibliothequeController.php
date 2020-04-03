<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentsType;
use App\Repository\DocumentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        return $this->render('bibliotheque/index.html.twig', [
            'pdfs' => $documentRepository->findBy(['folder' => 'pdf']),
            'nones' => $documentRepository->findBy(['folder' => 'non-repertorier']),
            'images' => $documentRepository->findBy(['folder' => 'images']),
            'total' => $documentRepository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="bibliotheque_add_documents")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $files = $request->files->get('documents')['files'];
            /**
             * @var UploadedFile $file
             */
            foreach ($files as $file)
            {
                $document = new Document();
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                $document->setCompleteUrl($fileName);
                $document->setUpdatedAt(new DateTime('now'));
                if ($file->getMimeType() === 'application/pdf') {
                    $document->setFolder('pdf');
                } else if ($file->getMimeType() === 'image/png' || $file->getMimeType() === 'image/jpg') {
                    $document->setFolder('images');
                } else {
                    $document->setFolder('non-repertorier');
                }
                // dd($file->getMimeType());
                $entityManager->persist($document);
                $entityManager->flush();
                if ($file->getMimeType() === 'application/pdf') {
                    $file->move($this->getParameter('pdf'), $fileName);
                } else if ($file->getMimeType() === 'image/png' || $file->getMimeType() === 'image/jpg') {
                    $file->move($this->getParameter('images'), $fileName);
                } else {
                    $file->move($this->getParameter('non-repertorier'), $fileName);
                }
            }
            $this->addFlash('notice','Les données ont été mis à jours');
        }

        return $this->render('bibliotheque/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
