<?php

/**
 * This file is part of the pdAdmin pdUser package.
 *
 * @package     pdUser
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 Ramazan APAYDIN
 * @license     LICENSE
 *
 * @link        https://github.com/rmznpydn/pd-user
 */

namespace Pd\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * User Role Changer.
 *
 * @author  Ramazan Apaydın <iletisim@ramazanapaydin.com>
 */
class RoleUserCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $userClass;

    /**
     * CreateUserCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface     $container
     * @param string                 $userClass
     */
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, string $userClass)
    {
        $this->em = $entityManager;
        $this->container = $container;
        $this->userClass = $userClass;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:role')
            ->setDescription('Change user role')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email address')
            ->addArgument('role', InputOption::VALUE_OPTIONAL, 'User Role');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $question = new Question('Email Adress: ');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }

                return $email;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $answer);
        }

        if (!$input->getArgument('role')) {
            $question = new ChoiceQuestion('Role: ', ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], 2);
            $question->setMultiselect(true);
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('role', $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Find User
        $user = $this->em->getRepository($this->userClass)->findOneBy(['email' => $input->getArgument('email')]);

        if (null !== $user) {
            // Set Roles
            $user->setRoles($input->getArgument('role'));

            // Save
            $this->em->persist($user);
            $this->em->flush();

            // Output
            $output->writeln('User Roles Changed:');
            $output->writeln(sprintf('Email: <comment>%s</comment>', $user->getEmail()));
            $output->writeln(sprintf('Roles: <comment>%s</comment>', implode(',', $input->getArgument('role'))));
        } else {
            $output->writeln('User not found!');
        }
    }
}
