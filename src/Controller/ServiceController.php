<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use App\Services\FileService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    /**
     * @Route("/services", name="service_index", methods={"GET"})
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function show(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/services", name="service_admin", methods={"GET"})
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function listAdmin(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/admin_list.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/services/new", name="service_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileService $fileService
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileService $fileService): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($service->getImage() !== null) {
                $fileService->moveToFolderAndModifyToWebP(
                    $this->getParameter($service->getImage()->getFolder()),
                    $service->getImage()->getExt(),
                    $service->getImage()->getCompleteUrl()
                );
                $service->getImage()->setExt('.webp');
            }
            if ($service->getIcone() !== null) {
                $fileService->moveToFolderAndModifyToWebP(
                    $this->getParameter($service->getIcone()->getFolder()),
                    $service->getIcone()->getExt(),
                    $service->getIcone()->getCompleteUrl()
                );
                $service->getIcone()->setExt('.webp');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('service_admin');
        }
        return $this->render('service/admin_new.html.twig', ['service' => $service, 'form' => $form->createView()]);
    }

    /**
     * @Route("/admin/services/edit/{id}", name="service_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Service $service
     * @param FileService $fileService
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Service $service, FileService $fileService): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $datas = ['icone' => $service->getIcone(), 'image' => $service->getImage()];
            foreach ($datas as $data) {
                if ($data !== null) {
                    if ($data->getTempFileName() && $data->getFile()) {
                        $fileService->uploadFolder($this->getParameter($data->getFolder()),$data->getExt(),$data->getCompleteUrl(),$data->getTempFileName() . '.webp');
                    } elseif($data->getFile()) {
                        $fileService->moveToFolderAndModifyToWebP($this->getParameter($data->getFolder()),$data->getExt(),$data->getCompleteUrl()
                        );
                    }
                    $data->setExt('.webp');
                }
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('service_admin');
        }
        return $this->render('service/admin_edit.html.twig', ['service' => $service,'form' => $form->createView()]);
    }

    /**
     * @Route("admin/services/delete/{id}", name="service_delete", methods={"DELETE"})
     * @param Request $request
     * @param Service $service
     * @return Response
     */
    public function delete(Request $request, Service $service): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $datas = ['icone' => $service->getIcone(), 'image' => $service->getImage()];
            foreach ($datas as $data) {
                if ($data !== null) {
                    if (file_exists($this->getParameter($data->getFolder()).'/'.$data->getCompleteUrl().'.webp')) {
                        unlink($this->getParameter($data->getFolder()).'/'.$data->getCompleteUrl().'.webp');
                    }
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }
        return $this->redirectToRoute('service_admin');
    }
}
