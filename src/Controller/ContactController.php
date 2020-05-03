<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Services\FileService;
use App\Services\Mailer;
use DateTime;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/admin/contact", defaults={"page": "1"}, methods={"GET"}, name="contact_index")
     * @Route("/admin/contact/{page<[1-9]\d*>}", methods="GET", name="contact_index_paginated")
     * @param ContactRepository $contactRepository
     * @param int $page
     * @return Response
     */
    public function index(ContactRepository $contactRepository, int $page): Response
    {
        return $this->render('contact/index.html.twig', [
            'paginate_contacts' => $contactRepository->findLatest($page),
            'unread' => $contactRepository->findBy(['readed' => false])
        ]);
    }

    /**
     * @Route("/contact", name="contact_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileService $fileService
     * @param Mailer $mailer
     * @return Response
     */
    public function new(Request $request, FileService $fileService, Mailer $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        $recaptcha = new ReCaptcha($this->getParameter('recaptcha'));
        $resp = $recaptcha->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());
        if ($form->isSubmitted() && $form->isValid()) {
            if ($resp->isSuccess()) {
                $documentsCollection = [$form->getData()->getDocument()[0]];
                foreach ($documentsCollection as $key => $result){
                    if ($result) {
                        $data = $fileService->transformToUrl($result->getFile());
                        $result->setCompleteUrl($data['filename']);
                        $result->setFolder($data['folder']);
                        $result->setContact($contact);
                        $result->setUpdatedAt(new DateTime('now'));
                        $contact->addDocument($result);
                        $fileService->moveToFolder($this->getParameter($data['folder']), $data['filename']);
                    }
                }
                $contact->setContactedAt(new DateTime('now'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($contact);
                $entityManager->flush();

                $mailer->sendContact($form->getData());
            }
            return $this->redirectToRoute('contact_new');
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/contact/show/{id}", name="contact_show", methods={"GET"})
     * @param Contact $contact
     * @return Response
     */
    public function show(Contact $contact): Response
    {
        if ($contact) {
            $contact->setReaded(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/{id}", name="contact_delete", methods={"DELETE"})
     * @param Request $request
     * @param Contact $contact
     * @return Response
     */
    public function delete(Request $request, Contact $contact): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contact_index');
    }
}
