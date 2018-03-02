<?php

namespace App\Constant\My;

use App\Constant\AppConstant;

/**
 * Class PasswordConstant
 * @package App\Constant\My
 */
class PasswordConstant
{
    //Constants for Annotations in App\Controller\Api\My\PasswordController
    public const UPDATE_PASSWORD_SECURITY_ERROR = 'You must be logged in and authorised to update your password.';

    //Constants for Messages in App\Controller\Api\My\PasswordController
    public const UPDATE_PASSWORD_INCORRECT = 'Your current password is incorrect.';
    public const UPDATE_PASSWORD_NOT_MATCHING = 'Your new passwords do not match.';
    public const UPDATE_PASSWORD_MATCHING = 'Your new password must be different from your old password';
    public const UPDATE_PASSWORD_SUCCESS_MESSAGE = 'Your password has been updated.';
    public const UPDATE_PASSWORD_SUCCESS_LOG = 'The user updated their password.';
    public const UPDATE_PASSWORD_VALIDATION_MESSAGE = 'Your password could not be updated.';
    public const UPDATE_PASSWORD_ERROR_LOG = 'The user failed to update their password.';

    public static function loadConstants(): void
    {
        //Constants for Annotations in App\Controller\Api\My\PasswordController
        AppConstant::defineConstant('PASSWORD_UPDATE_PASSWORD_SECURITY_ERROR', self::UPDATE_PASSWORD_SECURITY_ERROR);
    }
}