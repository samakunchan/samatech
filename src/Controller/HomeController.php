<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PortfolioRepository;
use App\Repository\ServiceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ServiceRepository $serviceRepository
     * @param PortfolioRepository $portfolioRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     * @Cache(smaxage=60)
     */
    public function index(ServiceRepository $serviceRepository, PortfolioRepository $portfolioRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'services' => $serviceRepository->findAll(),
            'portfolios' => $portfolioRepository->findAll(),
            'categories' => $categoryRepository->findAllCategoriesPortfolio(),
        ]);
    }
}
