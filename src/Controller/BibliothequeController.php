<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentsType;
use App\Repository\DocumentRepository;
use App\Services\FileService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bibliotheque", name="bibliotheque_")
 */
class BibliothequeController extends AbstractController
{
    /**
     * @Route("/", name="list")
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
     * @Route("/add", name="add_documents")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileService $fileService
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, EntityManagerInterface $entityManager, FileService $fileService): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $files = $request->files->get('documents')['files'];
            foreach ($files as $file)
            {
                $data = $fileService->transformToUrl($file);
                $document = new Document();
                $document->setCompleteUrl($data['filename']);
                $document->setUpdatedAt(new DateTime('now'));
                $document->setFolder($data['folder']);
                $entityManager->persist($document);
                $entityManager->flush();
                $fileService->moveToFolder($file, $this->getParameter('images'), $data['filename']);
            }
            $this->addFlash('notice','Les données ont été mis à jours');
        }
        return $this->render('bibliotheque/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}", name="delete_document", methods={"DELETE"})
     * @param Request $request
     * @param Document $document
     * @return Response
     */
    public function delete(Request $request, Document $document): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($document);
            $entityManager->flush();
        }
        return $this->redirectToRoute('bibliotheque_list');
    }
}
