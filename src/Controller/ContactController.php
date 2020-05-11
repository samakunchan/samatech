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
// TODO Faire absolument les images du portfolio en OTO sinon, ensuite dans le template du coté de l'aside gauche, faire en sorte de vérifier quand une image n'existe pas.

// TODO meme chose pour /about.

// TODO l'url /portfolio tout court doit exécuter une redirection vers la /home parce que je n'ai pas l'intention de faire exister /portfolio.

// TODO Faire des fixtures pour les tests avec les https://placem.at/places comme images et lnr lnr-cog pour les icones.

// TODO Mettre l'image en OTO et faire le setPhoto pour About.

// TODO Faire les metas.

// TODO Faire un logout

// TODO Faire une extension des 2 bases sinon je ne pourrai pas faire de meta ou récolter les notifications

// TODO Faire le cache comme la demo

// TODO Trouver un moyen de provide jquery pour le taginput
class ContactController extends AbstractController
{
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
        $recaptcha = new ReCaptcha($this->getParameter('google_recaptcha_site_key'));
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
                        $result->setExt($data['ext']);
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
     * @Route("admin/contact/delete/{id}", name="contact_delete", methods={"DELETE"})
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
