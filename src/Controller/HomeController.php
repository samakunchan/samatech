<?php

namespace App\Controller;

use App\Repository\PortfolioRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ServiceRepository $serviceRepository
     * @param PortfolioRepository $portfolioRepository
     * @return Response
     */
    public function index(ServiceRepository $serviceRepository, PortfolioRepository $portfolioRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'services' => $serviceRepository->findAll(),
            'portfolios' => $portfolioRepository->findAll(),
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('home/contact.html.twig', []);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog()
    {
        return $this->render('home/blog.html.twig', []);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function blogShow()
    {
        return $this->render('home/blog_show.html.twig', []);
    }

    /**
     * Cette méthode est injecté dans la vue blog
     */
    public function blogSideBar()
    {
        return $this->render('home/blog_side_bar.html.twig', []);
    }

    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing()
    {
        return $this->render('home/pricing.html.twig', []);
    }
}
