<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\DocumentRepository;
use App\Repository\ServiceRepository;
use App\Services\FileService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param DocumentRepository $documentRepository
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileService $fileService, DocumentRepository $documentRepository): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'view'    => $this->renderView('service/_ajax_document.html.twig', ['documents' => $documentRepository->findAll()])
            ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $bothImages = [$form->getData()->getIcone()[0], $form->getData()->getImage()[0]];
            foreach ($bothImages as $key => $result){
                $data = $fileService->transformToUrl($result->getFile());
                $result->setCompleteUrl($data['filename']);
                $result->setUpdatedAt(new DateTime('now'));
                $result->setFolder($data['folder']);
                if ($key === 0) {$result->setServiceIcone($service);} else {$result->setServiceImage($service);}
                if ($key === 0) {$service->addIcone($result);} else {$service->addImage($result);}
                // dd($service);
                // $result->setServiceIcone($service)? ($key === 0) : $result->setServiceImage($service);
                $fileService->moveToFolder($this->getParameter($data['folder']), $data['filename']);
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
            $bothImages = [$form->getData()->getIcone()[0], $form->getData()->getImage()[0]];
            foreach ($bothImages as $key => $resultEdit){
                if ($resultEdit->getFile()) {
                    $dataEdit = $fileService->transformToUrl($resultEdit->getFile());
                    $resultEdit->setCompleteUrl($dataEdit['filename']);
                    $resultEdit->setUpdatedAt(new DateTime('now'));
                    $resultEdit->setFolder($dataEdit['folder']);
                    if ($key === 0) {$resultEdit->setServiceIcone($service);} else {$resultEdit->setServiceImage($service);}
                    if ($key === 0) {$service->addIcone($resultEdit);} else {$service->addImage($resultEdit);}
                    $fileService->uploadFolder($this->getParameter($dataEdit['folder']), $dataEdit['filename'], $resultEdit->getTempFileName());
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
            $icone = $this->getParameter($service->getIcone()[0]->getFolder()).'/'.$service->getIcone()[0]->getCompleteUrl();
            $image = $this->getParameter($service->getImage()[0]->getFolder()).'/'.$service->getImage()[0]->getCompleteUrl();
            $datas = ['icone' => $icone, 'image' => $image];
            foreach ($datas as $data) {
                if (file_exists($data)) {
                    unlink($data);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }
        return $this->redirectToRoute('service_admin');
    }
}
