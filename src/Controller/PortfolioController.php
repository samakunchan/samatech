<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Form\PortfolioType;
use App\Repository\CategoryRepository;
use App\Repository\PortfolioRepository;
use App\Services\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PortfolioController extends AbstractController
{
    /**
     * @Route("/portfolio", name="portfolio_index_redirected", methods={"GET"})
     * @return Response
     */
    public function indexRedirected(): Response
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/portfolio/{slug}", name="portfolio_show", methods={"GET"})
     * @param Portfolio $portfolio
     * @return Response
     */
    public function show(Portfolio $portfolio): Response
    {
        return $this->render('portfolio/show.html.twig', [
            'portfolio' => $portfolio,
        ]);
    }

    /**
     * @Route("/admin/portfolio", name="portfolio_index", methods={"GET"})
     * @param PortfolioRepository $portfolioRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(PortfolioRepository $portfolioRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('portfolio/index.html.twig', [
            'portfolios' => $portfolioRepository->findAll(),
            'categories' => $categoryRepository->findAllCategoriesPortfolio(),
        ]);
    }

    /**
     * @Route("/admin/portfolio/new", name="portfolio_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileService $fileService
     * @return Response
     */
    public function new(Request $request, FileService $fileService): Response
    {
        $portfolio = new Portfolio();
        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($portfolio->getImage()->getFile()) {
                $fileService->moveToFolderAndModifyToWebP($this->getParameter($portfolio->getImage()->getFolder()), $portfolio->getImage()->getExt(), $portfolio->getImage()->getCompleteUrl()
                );
                $portfolio->getImage()->setExt('.webp');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($portfolio);
            $entityManager->flush();
            return $this->redirectToRoute('portfolio_index');
        }
        return $this->render('portfolio/new.html.twig', ['portfolio' => $portfolio, 'form' => $form->createView()]);
    }

    /**
     * @Route("/admin/portfolio/edit/{slug}", name="portfolio_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Portfolio $portfolio
     * @param FileService $fileService
     * @return Response
     */
    public function edit(Request $request, Portfolio $portfolio, FileService $fileService): Response
    {
        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($portfolio->getImage() !== null) {
                if ($portfolio->getImage()->getTempFileName() && $portfolio->getImage()->getFile()) {
                    $fileService->uploadFolder($this->getParameter($portfolio->getImage()->getFolder()), $portfolio->getImage()->getExt(), $portfolio->getImage()->getCompleteUrl(),$portfolio->getImage()->getTempFileName().'.webp');
                } elseif ($portfolio->getImage()->getFile()) {
                    $fileService->moveToFolderAndModifyToWebP($this->getParameter($portfolio->getImage()->getFolder()), $portfolio->getImage()->getExt(), $portfolio->getImage()->getCompleteUrl()
                    );
                }
                $portfolio->getImage()->setExt('.webp');
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('portfolio_index');
        }
        return $this->render('portfolio/edit.html.twig', ['portfolio' => $portfolio, 'form' => $form->createView()]);
    }

    /**
     * @Route("/admin/portfolio/{id}", name="portfolio_delete", methods={"DELETE"})
     * @param Request $request
     * @param Portfolio $portfolio
     * @return Response
     */
    public function delete(Request $request, Portfolio $portfolio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$portfolio->getId(), $request->request->get('_token'))) {
            if ($portfolio->getImage() !== null) {
                $image = $this->getParameter($portfolio->getImage()->getFolder()).'/'.$portfolio->getImage()->getCompleteUrl();
                if (file_exists($image.'.webp')) {
                    unlink($image.'.webp');
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($portfolio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('portfolio_index');
    }
}
