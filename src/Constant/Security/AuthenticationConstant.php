<?php

namespace App\Constant\Security;

/**
 * Class AuthenticationConstant
 * @package App\Constant\Security
 */
class AuthenticationConstant
{
    //Constants for Messages in App\Security\User\UserProvider
    public const INVALID_CREDENTIALS = 'Invalid credentials, please try your username and password again.';
    public const DISABLED_ACCOUNT = 'Your login request has failed as your account is disabled.';
    public const UNAUTHORISED_ACCOUNT = 'Your login request has failed as the account is not authorised.';
    public const UNKNOWN_ERROR = 'An unknown error has prevented your login attempt.  Please try again.';

    //Constants for Messages in App\EventListener\JWTInvalidListener
    public const JWT_NOT_FOUND = 'Your authentication token is missing.  Please try logging in again.';
    public const JWT_INVALID = 'Your authentication token is invalid.  Please try logging in again.';
    public const JWT_EXPIRED = 'Your authentication token has expired.  Please try logging in again.';
    public const JWT_REFRESH_FAILED = 'The refresh token is invalid or does not exist.';

    public static function loadConstants(): void
    {
    }
}