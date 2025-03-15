<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user-create',
    description: 'Command creating new user',
)]
class UserCreateCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /**
         * @var QuestionHelper $helper
         */
        $helper = $this->getHelper('question');

        $question = new Question('Please enter user email: ');

        $email = $helper->ask($input, $output, $question);

        $question = new Question('Please enter user password: ');

        $password = $helper->ask($input, $output, $question);

        $user = new User();

        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $password);

        $user
            ->setEmail($email)
            ->setPassword($hashedPassword);

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $io->success('User successfully created.');

        return Command::SUCCESS;
    }
}
