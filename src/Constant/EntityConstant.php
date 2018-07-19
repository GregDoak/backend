<?php

namespace App\Constant;


class EntityConstant
{
    public const ENTITY_MANAGER = 'doctrine.orm.entity_manager';

    // Admin
    public const AUDIT_LOG = 'DataDogAuditBundle:AuditLog';
    public const CRON_JOB = 'GregDoakCronBundle:CronJob';
    public const CRON_JOB_TASK = 'GregDoakCronBundle:CronJobTask';

    // My
    public const EVENT = 'App:My\Event';

    // Security
    public const GROUP = 'App:Security\Group';
    public const JWT_REFRESH_TOKEN = 'App:Security\JwtRefreshToken';
    public const ROLE = 'App:Security\Role';
    public const USER = 'App:Security\User';
}