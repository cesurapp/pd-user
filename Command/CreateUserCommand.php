<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Create New User.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class CreateUserCommand extends Command
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
     * @var string
     */
    private $profileClass;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, string $userClass, string $profileClass)
    {
        $this->em = $entityManager;
        $this->container = $container;
        $this->userClass = $userClass;
        $this->profileClass = $profileClass;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Application create user.')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email address')
            ->addArgument('password', InputArgument::OPTIONAL, 'User password')
            ->addArgument('role', InputOption::VALUE_OPTIONAL, 'User Role');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $question = new Question('Email Adress: ', 'demo@demo.com');
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $answer);
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Password: ', '123123');
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('password', $answer);
        }

        if (!$input->getArgument('role')) {
            $question = new ChoiceQuestion('Roles: ', ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], 2);
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('role', $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create User
        $user = (new $this->userClass())
            ->setEnabled(true)
            ->setEmail($input->getArgument('email'))
            ->setRoles([$input->getArgument('role')])
            ->setProfile((new $this->profileClass())
                ->setFirstname('Demo')
                ->setLastname('Account'));

        // Set Password
        $password = $this->container->get('security.password_encoder')->encodePassword($user, $input->getArgument('password') ?? '123123');
        $user->setPassword($password);

        // Save
        $this->em->persist($user);
        $this->em->flush();

        // Output
        $output->writeln('Created User:');
        $output->writeln(sprintf('Email: <comment>%s</comment>', $user->getUsername()));
        $output->writeln(sprintf('Password: <comment>%s</comment>', $input->getArgument('password')));
        $output->writeln(sprintf('Role: <comment>%s</comment>', $input->getArgument('role')));

        return 0;
    }
}
