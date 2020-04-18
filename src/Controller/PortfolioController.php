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
            $documentsCollection = [$form->getData()->getImage()[0]];
            foreach ($documentsCollection as $key => $result){
                if ($result) {
                    $data = $fileService->transformToUrl($result->getFile());
                    $result->setCompleteUrl($data['filename']);
                    $result->setFolder($data['folder']);
                    $result->setPortfolio($portfolio);
                    $portfolio->addImage($result);
                    $fileService->createThumbnail($result->getFile(), 'uploads/thumbs/'.$data['filename'], 500);
                    $fileService->moveToFolder($this->getParameter($data['folder']), $data['filename']);
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $portfolio->setSlug($form->getData()->getTitle());
            $entityManager->persist($portfolio);
            $entityManager->flush();

            return $this->redirectToRoute('portfolio_index');
        }

        return $this->render('portfolio/new.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form->createView(),
        ]);
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
            $documentsCollection = [$form->getData()->getImage()];
            foreach ($documentsCollection as $document) {
                foreach ($document as $image) {
                    if ($image && $image->getFile()) {
                        $dataEdit = $fileService->transformToUrl($image->getFile());
                        $image->setCompleteUrl($dataEdit['filename']);
                        $image->setPortfolio($portfolio);
                        $image->setFolder('images');
                        $portfolio->addImage($image);
                        if ($image->getTempFileName()) {
                            $fileService->clearThumbnail($this->getParameter('thumbs'), $dataEdit['filename'], $image->getTempFileName());
                            $fileService->createThumbnail($image->getFile(), 'uploads/thumbs/'.$dataEdit['filename'], 500);
                            $fileService->uploadFolder($this->getParameter($dataEdit['folder']), $dataEdit['filename'], $image->getTempFileName());
                        } else {
                            $fileService->createThumbnail($image->getFile(), 'uploads/thumbs/'.$dataEdit['filename'], 500);
                            $fileService->moveToFolder($this->getParameter($dataEdit['folder']), $dataEdit['filename']);
                        }
                    }
                }

            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('portfolio_index');
        }

        return $this->render('portfolio/edit.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form->createView(),
        ]);
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
            $image = $this->getParameter($portfolio->getImage()[0]->getFolder()).'/'.$portfolio->getImage()[0]->getCompleteUrl();
            $thumb = $this->getParameter('thumbs').'/'.$portfolio->getImage()[0]->getCompleteUrl();
            if (file_exists($image)) {
                unlink($image);
            }
            if (file_exists($thumb)){
                unlink($thumb);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($portfolio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('portfolio_index');
    }
}