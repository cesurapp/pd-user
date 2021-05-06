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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * User Role Changer.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class RoleUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $userClass)
    {
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
                    throw new \RuntimeException('Email can not be empty');
                }

                return $email;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $answer);
        }

        if (!$input->getArgument('role')) {
            $question = new ChoiceQuestion('Role: ', ['ROLE_USER', 'ROLE_SUPER_ADMIN'], 1);
            $question->setMultiselect(true);
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('role', $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Find User
        $user = $this->entityManager
            ->getRepository($this->userClass)
            ->findOneBy(['email' => $input->getArgument('email')]);

        if (null !== $user) {
            // Set Roles
            $user->setRoles($input->getArgument('role'));

            // Save
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Output
            $output->writeln('User Roles Changed:');
            $output->writeln(sprintf('Email: <comment>%s</comment>', $user->getEmail()));
            $output->writeln(sprintf('Roles: <comment>%s</comment>', implode(',', $input->getArgument('role'))));
        } else {
            $output->writeln('User not found!');
        }

        return Command::SUCCESS;
    }
}
