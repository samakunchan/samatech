<?php

namespace App\Controller;

use App\Form\ProfilType;
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
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/messageries", name="admin_messageries")
     */
    public function messages()
    {
        return $this->render('admin/messageries.html.twig', []);
    }

    /**
     * @Route("/messages/add", name="admin_messages_add")
     */
    public function messagesAdd()
    {
        return $this->render('admin/messageries_add.html.twig', []);
    }

    /**
     * @Route("/medias", name="admin_medias")
     */
    public function medias()
    {
        return $this->render('admin/medias.html.twig', []);
    }

    /**
     * @Route("/medias/add", name="admin_medias_add")
     */
    public function mediasAdd()
    {
        return $this->render('admin/medias_add.html.twig', []);
    }

    /**
     * @Route("/articles", name="admin_articles")
     */
    public function articles()
    {
        return $this->render('admin/articles.html.twig', []);
    }

    /**
     * @Route("/articles/add", name="admin_articles_add")
     */
    public function articlesAdd()
    {
        return $this->render('admin/articles_add.html.twig', []);
    }

    /**
     * @Route("/categories-articles", name="admin_categories_articles")
     */
    public function categoriesArticles()
    {
        return $this->render('admin/categories_articles.html.twig', []);
    }

    /**
     * @Route("/services", name="admin_services")
     */
    public function services()
    {
        return $this->render('admin/services.html.twig', []);
    }

    /**
     * @Route("/services/add", name="admin_services_add")
     */
    public function servicesAdd()
    {
        return $this->render('admin/services_add.html.twig', []);
    }

    /**
     * @Route("/portfolios", name="admin_portfolios")
     */
    public function portfolio()
    {
        return $this->render('admin/portfolios.html.twig', []);
    }

    /**
     * @Route("/portfolio/add", name="admin_portfolio_add")
     */
    public function portfolioAdd()
    {
        return $this->render('admin/portfolio_add.html.twig', []);
    }

    /**
     * @Route("/categories-portfolios", name="admin_categories_portfolios")
     */
    public function categoriesPortfolios()
    {
        return $this->render('admin/categories_portfolios.html.twig', []);
    }

    // /**
    //  * @Route("/a-propos", name="admin_about")
    //  */
    // public function about()
    // {
    //     return $this->render('admin/about.html.twig', []);
    // }

    /**
     * @Route("/settings", name="admin_settings")
     */
    public function settings()
    {
        return $this->render('admin/settings.html.twig', []);
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
