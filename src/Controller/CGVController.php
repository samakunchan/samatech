<?php

namespace App\Controller;

use App\Entity\CGV;
use App\Form\CGVType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CGVController extends AbstractController
{
    /**
     * @Route("admin/cgv/new", name="cgv_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $cGV = new CGV();
        $form = $this->createForm(CGVType::class, $cGV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cGV->setSlug('Conditions generales de ventes');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cGV);
            $entityManager->flush();

            return $this->redirectToRoute('cgv_edit', ['slug' => $cGV->getSlug()]);
        }

        return $this->render('cgv/new.html.twig', [
            'cgv' => $cGV,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cgv/{slug}", name="cgv_show", methods={"GET"})
     * @param CGV $cGV
     * @return Response
     */
    public function show(CGV $cGV): Response
    {
        return $this->render('cgv/show.html.twig', [
            'cgv' => $cGV,
        ]);
    }

    /**
     * @Route("admin/cgv/edit/{slug}", name="cgv_edit", methods={"GET","POST"})
     * @param Request $request
     * @param CGV $cGV
     * @return Response
     */
    public function edit(Request $request, CGV $cGV): Response
    {
        if (!$cGV) {
            return $this->redirectToRoute('cgv_new');
        }
        $form = $this->createForm(CGVType::class, $cGV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cgv_edit');
        }

        return $this->render('cgv/edit.html.twig', [
            'cgv' => $cGV,
            'form' => $form->createView(),
        ]);
    }

}
