<?php

namespace App\Controller\Api\My;

use App\Constant\AppConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Constant\My\EventConstant;
use App\Controller\Api\ApiController;
use App\Entity\My\Event;
use App\Exception\ValidationException;
use App\Form\My\EventType;
use App\Helper\EventHelper;
use App\Helper\ResponseHelper;
use App\Repository\My\EventRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class EventController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * @Rest\Get("/my/events.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER')", message=EVENT_GET_EVENTS_SECURITY_ERROR)
     * @return View
     */
    public function getEvents(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);
        /** @var EventRepository $eventRepository */
        $eventRepository = $this->entityManager->getRepository(EntityConstant::EVENT);

        $events = $eventRepository->getEvents($this->authenticatedUser);

        $data = ResponseHelper::buildSuccessResponse(200, $events);

        ResponseHelper::logResponse(EventConstant::GET_MULTIPLE_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Get("/my/events/upcoming.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER')", message=EVENT_GET_EVENTS_SECURITY_ERROR)
     * @return View
     */
    public function getUpcomingEvents(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);
        /** @var EventRepository $eventRepository */
        $eventRepository = $this->entityManager->getRepository(EntityConstant::EVENT);

        $events = $eventRepository->getUpcomingEvents($this->authenticatedUser);

        $data = ResponseHelper::buildSuccessResponse(200, $events);

        ResponseHelper::logResponse(EventConstant::GET_MULTIPLE_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Post("/my/event.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER')", message=EVENT_CREATE_EVENT_SECURITY_ERROR)
     * @param Request $request
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function createEvent(Request $request): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);

        try {
            $event = new Event();
            $event
                ->setCreatedBy($this->authenticatedUser);

            $this
                ->createForm(EventType::class, $event)
                ->submit($request->request->all());

            EventHelper::setUsers($event, (array)$request->get(LabelConstant::USERS), $this->entityManager);

            $this->validateEntity($event, EventConstant::CREATE_VALIDATION_ERROR);

            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(AppConstant::SUCCESS_TYPE,
                EventConstant::CREATE_SUCCESS_MESSAGE);

            $data = ResponseHelper::buildSuccessResponse(201, $data);

            ResponseHelper::logResponse(EventConstant::CREATE_SUCCESS_LOG, $data, $this);

        } catch (ValidationException $exception) {
            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );
            ResponseHelper::logResponse(EventConstant::CREATE_ERROR_LOG, $data, $this);
        }

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Put("/my/event/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER') && event.isCreator(user)", message=EVENT_UPDATE_EVENT_SECURITY_ERROR)
     * @ParamConverter("user", class="App\Entity\My\Event", options={"id" = "id"})
     * @param Request $request
     * @param Event $event
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function updateEvent(Request $request, Event $event): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);

        try {
            $event
                ->setUpdatedBy($this->authenticatedUser)
                ->setUpdatedOn()
                ->clearUsers();

            $this
                ->createForm(EventType::class, $event)
                ->submit($request->request->all());

            EventHelper::setUsers($event, (array)$request->get(LabelConstant::USERS), $this->entityManager);

            $this->validateEntity($event, EventConstant::UPDATE_VALIDATION_ERROR);

            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(AppConstant::SUCCESS_TYPE,
                EventConstant::UPDATE_SUCCESS_MESSAGE);

            $data = ResponseHelper::buildSuccessResponse(200, $data);

            ResponseHelper::logResponse(EventConstant::UPDATE_SUCCESS_LOG, $data, $this);

        } catch (ValidationException $exception) {
            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );
            ResponseHelper::logResponse(EventConstant::UPDATE_ERROR_LOG, $data, $this);
        }

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Delete("/my/event/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER') && event.isCreator(user)", message=EVENT_DELETE_EVENT_SECURITY_ERROR)
     * @ParamConverter("user", class="App\Entity\My\Event", options={"id" = "id"})
     * @param Event $event
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function deleteEvent(Event $event): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);

        $event
            ->setActive(false)
            ->setUpdatedBy($this->authenticatedUser)
            ->setUpdatedOn()
            ->clearUsers();

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $data = ResponseHelper::buildMessageResponse(AppConstant::SUCCESS_TYPE, EventConstant::DELETE_SUCCESS_MESSAGE);

        $data = ResponseHelper::buildSuccessResponse(200, $data);

        ResponseHelper::logResponse(EventConstant::DELETE_SUCCESS_LOG, $data, $this);

        return $this->view($data, $data['code']);
    }
}