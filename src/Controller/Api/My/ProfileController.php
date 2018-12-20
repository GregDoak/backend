<?php

namespace App\Controller\Api\My;

use App\Constant\My\ProfileConstant;
use App\Controller\Api\ApiController;
use App\Helper\ResponseHelper;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class ProfileController
 * @package App\Controller\Api\My
 */
class ProfileController extends ApiController
{
    /**
     * @Rest\Get("/my/account.{_format}", defaults={"_format"="json"})
     * @Security("is_granted('ROLE_USER')", message=PROFILE_GET_ACCOUNT_SECURITY_ERROR)
     * @return View
     */
    public function getAccount(): View
    {
        $this->authenticatedUser = $this->getUser();

        $data = ResponseHelper::buildSuccessResponse(200, $this->authenticatedUser);

        ResponseHelper::logResponse(ProfileConstant::GET_ACCOUNT_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }
}
