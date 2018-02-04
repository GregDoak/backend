<?php

namespace App\Constant\My;

use App\Constant\AppConstant;

/**
 * Class ProfileConstant
 * @package App\Constant\My
 */
class ProfileConstant
{
    //Constants for Annotations in App\Controller\Api\My\ProfileController
    public const GET_ACCOUNT_SECURITY_ERROR = 'You must be logged in and authorised to view your account details.';

    //Constants for Messages in App\Controller\Api\My\ProfileController
    public const GET_ACCOUNT_SUCCESS_MESSAGE = 'User received their profile.';

    public static function loadConstants(): void
    {
        //Constants for Annotations in App\Controller\Api\My\ProfileController
        AppConstant::defineConstant('PROFILE_GET_ACCOUNT_SECURITY_ERROR', self::GET_ACCOUNT_SECURITY_ERROR);
    }
}