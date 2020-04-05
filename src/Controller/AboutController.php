<?php

namespace App\Controller;

use App\Entity\About;
use App\Entity\Document;
use App\Form\AboutType;
use App\Repository\AboutRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class AboutController extends AbstractController
{
    /**
     * @Route("edit/{slug}", name="admin_about", methods={"GET","POST"})
     * @param Request $request
     * @param About $about
     * @param EntityManagerInterface $entityManager
     * @param CacheInterface $cache
     * @return Response
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function edit(Request $request, About $about, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AboutType::class, $about);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $documentsCollection = $form->getData()->getDocuments();
            foreach ($documentsCollection as $document)
            {
                /**
                 * @var Document $document
                 */
                $document->setAbout($about);
                $document->setUpdatedAt(new DateTime('now'));
                $document->setFolder('images');
                $about->addDocument($document);
            }
            $about->setUpdatedAt(new DateTime('now'));
            $entityManager->persist($about);
            $entityManager->flush();
            $this->addFlash('notice','Les données ont été mis à jours');
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
        dump($aboutRepository->findOneBy(['slug'=> 'a-propos-de-moi']));
        return $this->render('about/show.html.twig', ['data' => $aboutRepository->findOneBy(['slug'=> 'a-propos-de-moi'])]);
    }
}
