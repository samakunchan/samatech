<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordType;
use App\Repository\UserRepository;
use LogicException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/reset-password", name="reset_password")
     * @param UserRepository $userRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param MailerInterface $mailer
     * @return Response
     * Il n'y a que moi comme utilisateur, donc osef de créer un formulaire
     * @throws TransportExceptionInterface
     */
    public function sendForgotPassword(UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer): Response
    {
        $moiMeme = $userRepository->findOneBy(['email'=> 'sam@test.fr']);
        $token = $tokenGenerator->generateToken();
        $moiMeme->setResetToken($token);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($moiMeme);
        $entityManager->flush();

        $url = $this->generateUrl('update_reset_password', array('resetToken' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from('notifications@samakunchan-technology.com')
            ->to('sam@test.fr')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Réinitialisation de mot depasse')
            ->htmlTemplate( 'security/request_password.html.twig')
            ->context([ 'url' => $url ])
            ;

        $mailer->send($email);
        $this->addFlash('message', 'E-mail de réinitialisation du mot de passe envoyé !');

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/forgot-password/{resetToken}", name="update_reset_password", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function updateForgotPassword(Request $request, User $user, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setResetToken(null);
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Mot de passe mis à jour');

            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/new_password.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
