<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends FOSRestController
{
    /**
     * @Rest\Get("/.{_format}", defaults={"_format"="json"})
     * @return View
     */
    public function index(): View
    {
        $data = [
            'title' => 'App Backend',
            'info' => 'Please refer to the documentation on how to use this service',
        ];

        return $this->view($data, 200);
    }
}