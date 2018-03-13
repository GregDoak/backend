<?php

namespace App\Constant\Admin;

use App\Constant\AppConstant;

/**
 * Class CronConstant
 * @package App\Constant\Admin
 */
class CronConstant
{

    //Constants for Annotations in App\Controller\Api\Admin\CronController
    public const GET_CRON_JOBS_SECURITY_ERROR = 'You do not have access to view the cron jobs.';
    public const GET_CRON_JOB_TASKS_SECURITY_ERROR = 'You do not have access to view the cron job tasks.';
    public const GET_CRON_JOB_TASK_SECURITY_ERROR = 'You do not have access to view this cron job task.';
    public const CREATE_CRON_JOB_TASK_SECURITY_ERROR = 'You do not have access to create a cron job task.';

    //Constants for Messages in App\Controller\Api\Admin\CronController
    public const GET_MULTIPLE_SUCCESS_MESSAGE = 'User received a list a list of cron jobs.';
    public const GET_MULTIPLE_TASKS_SUCCESS_MESSAGE = 'User received a list of cron tasks.';
    public const GET_SINGLE_TASK_SUCCESS_MESSAGE = 'User received a the details of %s.';
    public const CREATE_VALIDATION_ERROR = 'Sorry, unable to create the cron job task, please note the errors below:';
    public const CREATE_SUCCESS_MESSAGE = 'Successfully created %s.';
    public const CREATE_SUCCESS_LOG = 'User created a cron job task with the command of %s.';
    public const CREATE_ERROR_LOG = 'User failed to create a new cron job task.';

    //Constants for validation fields in App\Controller\Api\Admin\CronController
    public const START_DATE_VALIDATION = 'The start date is in the incorrect format (YYYY-MM-DD HH:MM:SS).';
    public const INTERVAL_PERIOD_VALIDATION = 'The interval period must be greater than 1.';
    public const INTERVAL_CONTEXT_VALIDATION = 'The interval context must be one of the following %s.';
    public const PRIORITY_VALIDATION = 'The priority must be between 1 and 10.';

    public const INTERVAL_CONTEXT_OPTIONS = ['year', 'month', 'day', 'hour', 'minute', 'second'];

    public static function loadConstants(): void
    {
        //Constants for Annotations in App\Controller\Api\Admin\GroupController
        AppConstant::defineConstant('CRON_GET_CRON_JOBS_SECURITY_ERROR', self::GET_CRON_JOBS_SECURITY_ERROR);
        AppConstant::defineConstant('CRON_GET_CRON_JOB_TASKS_SECURITY_ERROR', self::GET_CRON_JOB_TASKS_SECURITY_ERROR);
        AppConstant::defineConstant('CRON_GET_CRON_JOB_TASK_SECURITY_ERROR', self::GET_CRON_JOB_TASK_SECURITY_ERROR);
        AppConstant::defineConstant(
            'CRON_CREATE_CRON_JOB_TASK_SECURITY_ERROR',
            self::CREATE_CRON_JOB_TASK_SECURITY_ERROR
        );
    }
}