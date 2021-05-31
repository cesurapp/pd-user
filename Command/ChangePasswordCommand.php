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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User Change Password.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class ChangePasswordCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $hasher,
        private string $userClass)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:changepassword')
            ->setDescription('User change password')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email address')
            ->addArgument('password', InputArgument::OPTIONAL, 'User password');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $question = new Question('Email Adress: ', 'demo@demo.com');
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $answer);
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Password: ');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \RuntimeException('Password can not be empty');
                }

                return $password;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('password', $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Find User
        $user = $this->entityManager
            ->getRepository($this->userClass)
            ->findOneBy(['email' => $input->getArgument('email')]);

        if (null !== $user) {
            // Set Password
            $password = $this->hasher->hashPassword($user, $input->getArgument('password'));
            $user->setPassword($password);

            // Save
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Output
            $output->writeln('User Password Changed:');
            $output->writeln(sprintf('Email: <comment>%s</comment>', $user->getEmail()));
            $output->writeln(sprintf('Password: <comment>%s</comment>', $input->getArgument('password')));
        } else {
            $output->writeln('User not found!');
        }

        return Command::SUCCESS;
    }
}
