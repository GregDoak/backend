<?php

namespace App\Constant\Admin;

use App\Constant\AppConstant;

/**
 * Class AuditConstant
 * @package App\Constant\Admin
 */
class AuditConstant
{

    //Constants for Annotations in App\Controller\Api\Admin\AuditController
    public const GET_AUDIT_LOG_SECURITY_ERROR = 'You do not access to view this audit log.';
    public const GET_AUDIT_LOGS_SECURITY_ERROR = 'You do not access to view the audit logs.';

    //Constants for Messages in App\Controller\Api\Admin\AuditController
    public const GET_SINGLE_SUCCESS_MESSAGE = 'User received the details of %s.';
    public const GET_MULTIPLE_SUCCESS_MESSAGE = 'User received a list of audit logs.';

    public static function loadConstants(): void
    {
        //Constants for Annotations in App\Controller\Api\Admin\GroupController
        AppConstant::defineConstant('AUDIT_GET_AUDIT_LOG_SECURITY_ERROR', self::GET_AUDIT_LOG_SECURITY_ERROR);
        AppConstant::defineConstant('AUDIT_GET_AUDIT_LOGS_SECURITY_ERROR', self::GET_AUDIT_LOGS_SECURITY_ERROR);
    }
}