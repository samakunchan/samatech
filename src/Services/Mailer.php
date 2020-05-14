<?php

namespace App\Services;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class Mailer
{
    private $twig;
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Mailer constructor.
     * @param Environment $twig
     * @param Swift_Mailer $mailer
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     */
    public function __construct(Environment $twig, Swift_Mailer $mailer, ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->container = $container;
        $this->em = $em;
    }
    /**
     * @param Contact $contact
     * @return bool|int
     */
    public function sendContact(Contact $contact)
    {
        try {
            $message = (new Swift_Message())
                ->setSubject('Samakunchan Technology - '.ucfirst($contact->getCategory()->getType()))
                ->setFrom($contact->getEmail())
                ->setTo('badjah.cedric@gmail.com')
                ->setBody(
                    $this->twig->render(
                        'contact/email.html.twig',
                        array(
                            'name' => $contact->getName(),
                            'category' => $contact->getCategory()->getType(),
                            'email' => $contact->getEmail(),
                            'message' => $contact->getMessage(),
                            'phone' => $contact->getPhone()
                        )
                    ),
                    'text/html'
                );
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
        return $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @param $url
     * @return int
     */
    public function sendMailForANewPassword(User $user, $url)
    {
        try {
            $message = (new Swift_Message())
                ->setFrom('notifications@samakunchan-technology.com')
                ->setSubject('Requete pour le changement de mot de passe.')
                ->setTo('badjah.cedric@gmail.com')
                ->setBody(
                    $this->twig->render(
                        'security/request_password.html.twig',
                        [
                            'name' => $user->getFirstname().' '.$user->getLastname(),
                            'email' => $user->getEmail(),
                            'url' => $url
                        ]
                    ),
                    'text/html'
                );
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
        return $this->mailer->send($message);
    }
}
