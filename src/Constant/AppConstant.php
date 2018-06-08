<?php

namespace App\Constant;

use App\Constant\Admin\AuditConstant;
use App\Constant\Admin\CronConstant;
use App\Constant\Admin\GroupConstant;
use App\Constant\Admin\RoleConstant;
use App\Constant\Admin\UserConstant;
use App\Constant\My\PasswordConstant;
use App\Constant\My\ProfileConstant;
use App\Constant\My\TokenConstant;
use App\Constant\Security\AuthenticationConstant;

/**
 * Class AppConstant
 * @package App\Constant
 */
class AppConstant
{
    public const FORMAT_DATETIME = 'Y-m-d H:i:s';
    public const DANGER_TYPE = 'danger';
    public const DEFAULT_EXCEPTION = 'An unknown error has occurred. Please try again.';
    public const HTTP_NOT_FOUND = 'Page not found';
    public const HTTP_METHOD_NOT_ALLOWED = 'This method is not allowed on this page.';
    public const NOT_LOGGED_IN = 'You are not currently logged in.  Please login in and try again.';
    public const ORM_EXCEPTION = 'There was a problem connecting to the database.  Please try again later.';
    public const SUCCESS_TYPE = 'success';
    public const SYSTEM_USERNAME = 'system';

    /**
     * @param string $message
     * @return string
     */
    public static function convertStringToSprintF(string $message): string
    {
        $convertedMessage = '';
        $limit = max(substr_count($message, '{{'), substr_count($message, '}}'));

        for ($index = 0; $index < $limit; $index++) {
            $openPosition = strpos($message, '{{');
            if ($openPosition !== false) {
                $convertedMessage .= substr($message, 0, $openPosition).'%s';
            }
            $closedPosition = strpos($message, '}}');
            if ($closedPosition !== false) {
                $convertedMessage .= substr($message, $closedPosition + 2);
            }
        }

        return \strlen($convertedMessage) > 0 ? $convertedMessage : $message;
    }

    /**
     * @param string $constant
     * @param $value
     */
    public static function defineConstant(string $constant, $value): void
    {
        if ( ! \defined($constant)) {
            \define($constant, $value);
        }
    }

    public static function loadConstants(): void
    {
        AuditConstant::loadConstants();
        AuthenticationConstant::loadConstants();
        CronConstant::loadConstants();
        GroupConstant::loadConstants();
        PasswordConstant::loadConstants();
        ProfileConstant::loadConstants();
        RoleConstant::loadConstants();
        TokenConstant::loadConstants();
        UserConstant::loadConstants();
    }

}