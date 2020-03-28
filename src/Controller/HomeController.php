<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', []);
    }

    /**
     * @Route("/a-propos-de-moi", name="about")
     */
    public function about()
    {
        return $this->render('home/about.html.twig', []);
    }

    /**
     * @Route("/service", name="service")
     */
    public function service()
    {
        return $this->render('home/service.html.twig', []);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('home/contact.html.twig', []);
    }

    /**
     * @Route("/portfolio", name="portfolio")
     */
    public function portfolio()
    {
        return $this->render('home/portfolio.html.twig', []);
    }
    /**
     * @Route("/portfolio/{id}", name="portfolio_show")
     */
    public function portfolioShow()
    {
        return $this->render('home/portfolio_show.html.twig', []);
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

    // /**
    //  * @Route("/login", name="login")
    //  */
    // public function login()
    // {
    //     return $this->render('home/login.html.twig', []);
    // }
}
