<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create  User email password',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $manager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'User Creator',
            '=================='
        ]);

        $output->writeln('Email: ' . $input->getArgument('email'));

        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setEmail($email);
        $hashPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashPassword);

        $violations = $this->validator->validate($user);

        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $io->error($violation->getPropertyPath() . ':' . $violation->getMessage());
            }
            return Command::FAILURE;
        }

        $this->manager->persist($user);
        $this->manager->flush();

        $io->success(sprintf('Create user successfully with email: %s .', $email));

        return Command::SUCCESS;
    }
}
