<?php

namespace App\Controller\Api\My;

use App\Constant\Admin\UserConstant;
use App\Constant\EntityConstant;
use App\Controller\Api\ApiController;
use App\Helper\ResponseHelper;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * @Rest\Get("/my/similar-access-users.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER')", message=USER_GET_USERS_SECURITY_ERROR)
     * @return View
     */
    public function getSimilarAccessUsers(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);
        /** @var UserRepository $eventRepository */
        $eventRepository = $this->entityManager->getRepository(EntityConstant::USER);

        if ($this->authenticatedUser->isAdmin()) {
            $users = $eventRepository->getOtherUsers($this->authenticatedUser);
        } else {
            $users = $eventRepository->getUsersBySameRole($this->authenticatedUser);
        }

        $data = ResponseHelper::buildSuccessResponse(200, $users);

        ResponseHelper::logResponse(UserConstant::GET_MULTIPLE_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }

}