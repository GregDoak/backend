<?php

namespace App\Command;

use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ChangePasswordCommand
 * @package App\Command
 */
class ChangePasswordCommand extends Command
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /**
     * InitialiseCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @throws \LogicException
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:user:change-password')
            ->setDescription('Change the password of a user.')
            ->setDefinition(
                [
                    new InputArgument(LabelConstant::USERNAME, InputArgument::REQUIRED, 'The username'),
                    new InputArgument(LabelConstant::PASSWORD, InputArgument::REQUIRED, 'The password'),
                ]
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $username = $input->getArgument(LabelConstant::USERNAME);
        $password = $input->getArgument(LabelConstant::PASSWORD);

        $user = $this->entityManager->getRepository(EntityConstant::USER)->getUserByUsername($username);
        if ($user instanceof User) {
            $user->setPassword($this->encoder->encodePassword($user, $password));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $output->writeln(sprintf('Changed password for user <comment>%s</comment>', $username));
        } else {
            $output->writeln(sprintf('User <comment>%s</comment> does not exist in the database.', $username));
        }


    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];
        if ( ! $input->getArgument(LabelConstant::USERNAME)) {
            $question = new Question('Please give the username:');
            $question->setValidator(
                function ($username) {
                    if (empty($username)) {
                        throw new \UnexpectedValueException('Username can not be empty');
                    }

                    return $username;
                }
            );
            $questions[LabelConstant::USERNAME] = $question;
        }

        if ( ! $input->getArgument(LabelConstant::PASSWORD)) {
            $question = new Question('Please enter the new password:');
            $question->setValidator(
                function ($password) {
                    if (empty($password)) {
                        throw new \UnexpectedValueException('Password can not be empty');
                    }

                    return $password;
                }
            );
            $question->setHidden(true);
            $questions[LabelConstant::PASSWORD] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}