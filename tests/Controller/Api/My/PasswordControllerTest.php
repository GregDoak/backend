<?php

namespace App\Tests\Controller\Api\My;

use App\Tests\WebTestCase;

class PasswordControllerTest extends WebTestCase
{
    //password cannot be in password histories
    //password histories have a limit
    //the password is expired when the start date + period is less than today or the enabled is set to true
}