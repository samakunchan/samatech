<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Repository\BlogRepository;
use App\Repository\ContactRepository;
use App\Repository\DocumentRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     * @param BlogRepository $blogRepository
     * @param DocumentRepository $documentRepository
     * @param ContactRepository $contactRepository
     * @return Response
     */
    public function index(BlogRepository $blogRepository, DocumentRepository $documentRepository, ContactRepository $contactRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        return $this->render('admin/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
            'views' => $blogRepository->findTotalViews(),
            'documents' => $documentRepository->findAll(),
            'images' => $documentRepository->findBy(['folder' => 'images']),
            'pdfs' => $documentRepository->findBy(['folder' => 'pdf']),
            'non_repertoriers' => $documentRepository->findBy(['folder' => 'non_repertorier']),
            'contacts' => $contactRepository->findAll(),
            'contacts_non_lus' => $contactRepository->findBy(['readed' => false]),
        ]);
    }

    /**
     * @Route("/account", name="admin_account", methods={"GET", "POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     * @throws Exception
     */
    public function account(Request $request, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['email' => 'sam@test.fr']);
        $form = $this->createForm(ProfilType::class, $user );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Les données ont été mis à jours.'
            );
            return $this->redirectToRoute('admin_account');
        }
        return $this->render('admin/account.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
