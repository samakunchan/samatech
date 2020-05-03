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
}
