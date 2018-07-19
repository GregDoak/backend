<?php

namespace App\Tests\Controller\Api\My;

use App\Constant\AppConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Constant\My\EventConstant;
use App\Entity\My\Event;
use App\Entity\Security\User;
use App\Tests\WebTestCase;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Response;

class EventControllerTest extends WebTestCase
{
    private const API_URL_SINGLE = '/api/my/event';
    private const TYPE = 'json';

    public function testGetEvents(): void
    {
        $url = '/api/my/events.'.self::TYPE;
        $this->client->request('GET', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);

        $content = $this->getResponseContent();
        $this->assertTrue(
            $content->status,
            'Failed to get a list of Events'
        );
    }

    public function testCreateInvalidEventDescription(): void
    {
        $url = self::API_URL_SINGLE.'.'.self::TYPE;

        $now = new \DateTime();

        $parameters = [
            LabelConstant::DESCRIPTION => '',
            LabelConstant::LOCATION => 'Valid Location',
            LabelConstant::START_DATE_TIME => $now->format(AppConstant::FORMAT_DATETIME),
            LabelConstant::END_DATE_TIME => $now->format(AppConstant::FORMAT_DATETIME),
            LabelConstant::USERS => [],
        ];

        $this->client->request('POST', $url, $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            EventConstant::CREATE_VALIDATION_ERROR,
            [
                EventConstant::DESCRIPTION_EMPTY_ERROR,
            ]
        );
    }

    public function testCreateInvalidEventDateFormat(): void
    {
        $url = self::API_URL_SINGLE.'.'.self::TYPE;

        $now = new \DateTime();

        $parameters = [
            LabelConstant::DESCRIPTION => 'Valid Description',
            LabelConstant::LOCATION => 'Valid Location',
            LabelConstant::START_DATE_TIME => $now->format('Y-m-d'),
            LabelConstant::END_DATE_TIME => $now->format(AppConstant::FORMAT_DATETIME),
            LabelConstant::USERS => [],
        ];

        $this->client->request('POST', $url, $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            EventConstant::CREATE_VALIDATION_ERROR,
            [
                EventConstant::START_EMPTY_ERROR,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function testCreateInvalidEventDates(): void
    {
        $url = self::API_URL_SINGLE.'.'.self::TYPE;

        $now = new \DateTime();
        $past = new \DateTime();
        $past->sub(new \DateInterval('P1D'));

        $parameters = [
            LabelConstant::DESCRIPTION => 'Valid Description, Invalid Dates',
            LabelConstant::LOCATION => 'Valid Location',
            LabelConstant::START_DATE_TIME => $now->format(AppConstant::FORMAT_DATETIME),
            LabelConstant::END_DATE_TIME => $past->format(AppConstant::FORMAT_DATETIME),
            LabelConstant::USERS => [],
        ];

        $this->client->request('POST', $url, $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            EventConstant::CREATE_VALIDATION_ERROR,
            [
                EventConstant::END_GREATER_ERROR,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function testCreateValidEvent(): void
    {
        $url = self::API_URL_SINGLE.'.'.self::TYPE;

        $now = new \DateTime();
        $future = new \DateTime();
        $future->add(new \DateInterval('P1D'));

        $parameters = [
            LabelConstant::DESCRIPTION => 'Valid Description, Valid Dates',
            LabelConstant::LOCATION => 'Valid Location',
            LabelConstant::START_DATE_TIME => $now->format(AppConstant::FORMAT_DATETIME),
            LabelConstant::END_DATE_TIME => $future->format(AppConstant::FORMAT_DATETIME),
            LabelConstant::USERS => [],
        ];

        $this->client->request('POST', $url, $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_CREATED);
        $this->doMessageTests(
            AppConstant::SUCCESS_TYPE,
            EventConstant::CREATE_SUCCESS_MESSAGE,
            []
        );
    }

    public function testUpdateMissingEvent(): void
    {
        $url = self::API_URL_SINGLE.'/.'.self::TYPE;
        $this->client->request('PUT', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }


    /**
     * @throws NoResultException
     */
    public function testUpdateInvalidEventFields(): void
    {
        $eventRepository = $this->entityManager->getRepository(EntityConstant::EVENT);

        /** @var Event[] $events */
        $events = $eventRepository->findAll();
        if (\count($events) === 0) {
            throw new NoResultException();
        }

        $event = $events[0];
        $url = self::API_URL_SINGLE.'/'.$event->getId().'.'.self::TYPE;

        $parameters = [
            LabelConstant::DESCRIPTION => '',
        ];

        $this->client->request('PUT', $url, $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            EventConstant::UPDATE_VALIDATION_ERROR,
            [
                EventConstant::DESCRIPTION_EMPTY_ERROR,
                EventConstant::LOCATION_EMPTY_ERROR,
                EventConstant::START_EMPTY_ERROR,
                EventConstant::END_EMPTY_ERROR,
            ]
        );
    }

    /**
     * @throws NoResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testUpdateInvalidUser(): void
    {
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);
        $systemUser = $userRepository->findOneBy(['username' => AppConstant::SYSTEM_USERNAME]);

        if ( ! $systemUser instanceof User) {
            throw new NoResultException();
        }

        $event = new Event();
        $event
            ->setDescription('Testing System User')
            ->setLocation('Testing Location')
            ->setStartDateTime(new \DateTime())
            ->setEndDateTime(new \DateTime())
            ->setCreatedBy($systemUser);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $url = self::API_URL_SINGLE.'/'.$event->getId().'.'.self::TYPE;

        $this->client->request('PUT', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_FORBIDDEN);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            EventConstant::UPDATE_EVENT_SECURITY_ERROR,
            []
        );
    }

    /**
     * @throws NoResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function testUpdateValidEvent(): void
    {
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);
        $adminUser = $userRepository->findOneBy(['username' => getenv('APP_DEFAULT_USERNAME')]);

        if ( ! $adminUser instanceof User) {
            throw new NoResultException();
        }

        $event = new Event();
        $event
            ->setDescription('Testing Valid Event')
            ->setLocation('Testing Location')
            ->setStartDateTime(new \DateTime())
            ->setEndDateTime(new \DateTime())
            ->setCreatedBy($adminUser);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $now = new \DateTime();
        $future = new \DateTime();
        $future->add(new \DateInterval('P1D'));

        $parameters = [
            LabelConstant::DESCRIPTION => 'Testing Update',
            LabelConstant::LOCATION => 'Testing Location Update',
            LabelConstant::START_DATE_TIME => $now->format('Y-m-d H:i:s'),
            LabelConstant::END_DATE_TIME => $future->format('Y-m-d H:i:s'),
        ];

        $url = self::API_URL_SINGLE.'/'.$event->getId().'.'.self::TYPE;

        $this->client->request('PUT', $url, $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doMessageTests(
            AppConstant::SUCCESS_TYPE,
            EventConstant::UPDATE_SUCCESS_MESSAGE,
            []
        );
    }

    /**
     * @throws NoResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function testDeleteValidEvent(): void
    {
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);
        $adminUser = $userRepository->findOneBy(['username' => getenv('APP_DEFAULT_USERNAME')]);

        if ( ! $adminUser instanceof User) {
            throw new NoResultException();
        }

        $event = new Event();
        $event
            ->setDescription('Testing Delete Event')
            ->setLocation('Testing Location')
            ->setStartDateTime(new \DateTime())
            ->setEndDateTime(new \DateTime())
            ->setCreatedBy($adminUser);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $url = self::API_URL_SINGLE.'/'.$event->getId().'.'.self::TYPE;

        $this->client->request('DELETE', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doMessageTests(
            AppConstant::SUCCESS_TYPE,
            EventConstant::DELETE_SUCCESS_MESSAGE,
            []
        );
    }
}
