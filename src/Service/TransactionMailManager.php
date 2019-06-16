<?php
/**
 * Created by PhpStorm.
 * User: floste
 * Date: 06/10/2018
 * Time: 13:47
 */

namespace App\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class TransactionMailManager
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var String
     */
    private $mailer_sender;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer, EngineInterface $templating, $mailer_sender=""){
        $this->em = $em;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->mailer_sender = $mailer_sender;
    }

    /**
     * @param User $user l'utilisateur
     */
    public function envoiMailResetPassword(User $user)
    {
        $token = bin2hex(random_bytes(10));

        $user->setResetToken($token);
        $this->em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject("Reset de mot de passe")
            ->setFrom($this->mailer_sender)
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    '@SfArcherySportsManager/security/mail_mdp_reset.html.twig',
                    [
                        'token' => $token,
                    ]
                ),
                'text/html'
            )
/*
            ->addPart(
                $this->templating->render(
                    'AzimutSortirBackofficeBundle:Emails:password_reset.txt.twig',
                    [
                        'token' => $token,
                    ]
                ),
                'text/plain'
            )
*/
        ;

        $this->mailer->send($message);
    }

}