<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Created by PhpStorm.
 * User: stephane
 * Date: 14/11/2017
 * Time: 14:31
 */
class AddUserCommand extends Command
{
    /** @var  \Doctrine\ORM\EntityManager */
    private $manager;
    /**
     * @var PasswordEncoderInterface
     */
    private $encoder;

    /**
     * AddUserCommand constructor.
     * @param \Doctrine\ORM\EntityManager $manager
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->encoder = $encoder;
    }


    protected function configure()
    {
        $this
            ->setName('archerySportsManager:security:addAdmin')
            ->setDescription('Ajout d\'un administrateur')
            ->addArgument('addressMail', InputArgument::REQUIRED, 'address mail')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $user->setEmail($input->getArgument('addressMail'));
        $user->setDenomination($input->getArgument('addressMail'));
        $user->setIsActive(true);
        $user->setIsAdmin(true);

        $newPwd = $this->getRandomPwd(8);
        $encodePassword = $this->encoder->encodePassword($user, $newPwd);
        $user->setPlainPassword($newPwd);
        $user->setPassword($encodePassword);
        $this->manager->persist($user);
        $this->manager->flush();
        $output->writeln("Mot de passe : " . $newPwd);
    }

    private function getRandomPwd($length){
        $a = str_split("abcdefghijklmnpqrstuvwxyABCDEFGHJKLMNPQRSTUVWXY23456789");
        shuffle($a);
        return substr( implode($a), 0, $length );
    }

}