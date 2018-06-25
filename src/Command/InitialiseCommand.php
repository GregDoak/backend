<?php

namespace App\Command;

use App\Constant\AppConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Entity\Lookup\Gender;
use App\Entity\Lookup\Title;
use App\Entity\Security\Role;
use App\Entity\Security\User;
use App\Helper\ConsoleHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class InitialiseCommand
 * @package App\Command
 */
class InitialiseCommand extends ContainerAwareCommand
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /**
     * InitialiseCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @throws LogicException
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('app:initialise')
            ->setDescription('Propagates the database with initial values');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        ConsoleHelper::$output = $output;

        $doctrineDatabaseCreate = $this->getApplication()->find('doctrine:database:create');
        $doctrineMigrationsMigrate = $this->getApplication()->find('doctrine:migrations:migrate');

        try {

            ConsoleHelper::header();

            $doctrineDatabaseCreate->run($input, $output);
            $doctrineMigrationsMigrate->run($input, $output);

            $this->initialiseUsers();
            ConsoleHelper::outputEmptyLine();
            $this->initialiseGender();
            ConsoleHelper::outputEmptyLine();
            $this->initialiseTitles();

            ConsoleHelper::footer();
        } catch (\Exception $exception) {
            ConsoleHelper::outputMessage($exception->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    private function initialiseUsers(): void
    {
        ConsoleHelper::outputMessage('Initialise Users Started...');

        $created = 0;
        $records = [
            [
                LabelConstant::USERNAME => AppConstant::SYSTEM_USERNAME,
                LabelConstant::PASSWORD => \random_bytes(20),
                LabelConstant::ROLES => ['ROLE_ADMIN'],
            ],
            [
                LabelConstant::USERNAME => $this->getContainer()->getParameter('app.defaults.username'),
                LabelConstant::PASSWORD => $this->getContainer()->getParameter('app.defaults.password'),
                LabelConstant::ROLES => ['ROLE_SUPER_ADMIN'],
            ],
        ];

        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);
        $roleRepository = $this->entityManager->getRepository(EntityConstant::ROLE);

        foreach ($records as $record) {
            $user = $userRepository->findOneBy([LabelConstant::USERNAME => $record[LabelConstant::USERNAME]]);
            if ($user === null) {
                $user = new User();
                $plainPassword = $record[LabelConstant::PASSWORD];
                $user
                    ->setUsername($record[LabelConstant::USERNAME])
                    ->setPassword($this->encoder->encodePassword($user, $plainPassword))
                    ->setLoginCount()
                    ->setEnabled(true);
                $user
                    ->setCreatedBy($user);
                $created++;
            }

            $roles = $roleRepository->findAll();
            if (\count($roles) === 0) {
                $this->initialiseRoles($user);
            }

            foreach ((array)$record[LabelConstant::ROLES] as $roleTitle) {
                $role = $roleRepository->findOneBy([LabelConstant::TITLE => $roleTitle]);
                if ($role instanceof Role) {
                    $user->setRole($role);
                }
            }

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        ConsoleHelper::outputMessage($created.' user records created.');
        ConsoleHelper::outputMessage('Initialise Users Ended...');
    }

    /**
     * @param User $user
     */
    private function initialiseRoles(User $user): void
    {
        $created = 0;
        $records = [
            [
                LabelConstant::TITLE => 'ROLE_SUPER_ADMIN',
                LabelConstant::DESCRIPTION => 'This role has full access to every area of the app',
            ],
            [
                LabelConstant::TITLE => 'ROLE_ADMIN',
                LabelConstant::DESCRIPTION => 'This role has access to administrative features only',
            ],
            [
                LabelConstant::TITLE => 'ROLE_USER',
                LabelConstant::DESCRIPTION => 'This role only allows the user to login and access the most basic features',
            ],
        ];

        $roleRepository = $this->entityManager->getRepository(EntityConstant::ROLE);

        foreach ($records as $record) {
            $role = $roleRepository->findOneBy([LabelConstant::TITLE => $record[LabelConstant::TITLE]]);

            if ($role === null) {
                $role = new Role();
                $role
                    ->setTitle($record[LabelConstant::TITLE])
                    ->setDescription($record[LabelConstant::DESCRIPTION])
                    ->setCreatedBy($user);

                $this->entityManager->persist($role);
                $created++;
            }
        }

        $this->entityManager->flush();

        ConsoleHelper::outputMessage($created.' security role records created.');
    }

    private function initialiseGender(): void
    {
        ConsoleHelper::outputMessage('Initialise Gender Started...');

        $created = 0;
        $records = [
            'Female',
            'Male',
        ];

        $genderRepository = $this->entityManager->getRepository(EntityConstant::GENDER);
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        $user = $userRepository->findOneBy([LabelConstant::USERNAME => AppConstant::SYSTEM_USERNAME]);

        foreach ($records as $record) {
            $gender = $genderRepository->findOneBy([LabelConstant::TITLE => $record]);
            if ($gender === null && $user instanceof User) {
                $gender = new Gender();
                $gender
                    ->setTitle($record)
                    ->setCreatedBy($user);
                $created++;

                $this->entityManager->persist($gender);
            }
        }

        $this->entityManager->flush();

        ConsoleHelper::outputMessage($created.' gender records created.');
        ConsoleHelper::outputMessage('Initialise Gender Ended...');
    }

    private function initialiseTitles(): void
    {
        ConsoleHelper::outputMessage('Initialise Title Started...');

        $created = 0;
        $records = [
            'Mr',
            'Mrs',
            'Ms',
            'Miss',
        ];

        $titleRepository = $this->entityManager->getRepository(EntityConstant::TITLE);
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        $user = $userRepository->findOneBy([LabelConstant::USERNAME => AppConstant::SYSTEM_USERNAME]);

        foreach ($records as $record) {
            $title = $titleRepository->findOneBy([LabelConstant::TITLE => $record]);
            if ($title === null && $user instanceof User) {
                $title = new Title();
                $title
                    ->setTitle($record)
                    ->setCreatedBy($user);
                $created++;

                $this->entityManager->persist($title);
            }
        }

        $this->entityManager->flush();

        ConsoleHelper::outputMessage($created.' title records created.');
        ConsoleHelper::outputMessage('Initialise Title Ended...');
    }
}

