<?php

namespace App\Constant\My;

use App\Constant\AppConstant;

/**
 * Class TokenConstant
 * @package App\Constant\My
 */
class TokenConstant
{
    //Constants for Annotations in App\Controller\Api\My\TokenController
    public const GET_TOKENS_SECURITY_ERROR = 'You must be logged in and authorised to view your tokens.';
    public const DELETE_TOKENS_SECURITY_ERROR = 'You must be logged in and authorised to delete your tokens.';
    public const DELETE_TOKEN_SECURITY_ERROR = 'You must be logged in and authorised to delete your token.';

    //Constants for Messages in App\Controller\Api\My\TokenController
    public const GET_MULTIPLE_SUCCESS_LOG = 'User retrieved a list of their login tokens.';
    public const DELETE_TOKENS_SUCCESS_MESSAGE = 'You have cleared your login tokens.';
    public const DELETE_TOKENS_SUCCESS_LOG = 'User %s deleted login tokens.';
    public const DELETE_TOKEN_SUCCESS_MESSAGE = 'You have deleted a login token.';
    public const DELETE_TOKEN_SUCCESS_LOG = 'User %s deleted a login token.';

    public static function loadConstants(): void
    {
        //Constants for Annotations in App\Controller\Api\My\TokenController
        AppConstant::defineConstant('TOKEN_GET_TOKENS_SECURITY_ERROR', self::GET_TOKENS_SECURITY_ERROR);
        AppConstant::defineConstant('TOKEN_DELETE_TOKENS_SECURITY_ERROR', self::DELETE_TOKENS_SECURITY_ERROR);
        AppConstant::defineConstant('TOKEN_DELETE_TOKEN_SECURITY_ERROR', self::DELETE_TOKEN_SECURITY_ERROR);
    }
}