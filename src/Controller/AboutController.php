<?php

namespace App\Controller;

use App\Entity\About;
use App\Form\AboutType;
use App\Repository\AboutRepository;
use App\Services\FileService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    /**
     * @Route("/admin/edit/{slug}", name="admin_edit_about", methods={"GET","POST"})
     * @param Request $request
     * @param About $about
     * @param FileService $fileService
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, About $about, FileService $fileService) :Response
    {
        $form = $this->createForm(AboutType::class, $about);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $documentsCollection = [$form->getData()->getDocuments()[0]];
            foreach ($documentsCollection as $document) {
                if ($document->getFile()) {
                    $dataEdit = $fileService->transformToWebP($document->getFile());
                    $document->setCompleteUrl($dataEdit['filename']);
                    $document->setAbout($about);
                    $document->setUpdatedAt(new DateTime('now'));
                    $document->setFolder('images');
                    $document->setExt('.webp');
                    $about->addDocument($document);
                    if ($document->getTempFileName()) {
                        $fileService->moveToFolderAndModifyToWebP($this->getParameter($dataEdit['folder']), $dataEdit['filename'], $document->getTempFileName());
                    }
                }
            }
            $about->setUpdatedAt(new DateTime('now'));
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice','Les données ont été mis à jours.');
        }
        return $this->render('about/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/a-propos-de-moi", name="about")
     * @param AboutRepository $aboutRepository
     * @return Response
     */
    public function show(AboutRepository $aboutRepository)
    {
        return $this->render('about/show.html.twig', ['data' => $aboutRepository->findOneBy(['slug'=> 'a-propos-de-moi'])]);
    }
}
