<?php

namespace App\Constant;

/**
 * Class LabelConstant
 * @package App\Constant
 */
class LabelConstant
{
    // Security Labels
    public const USERNAME = 'username';
    public const PASSWORD = 'password'; //NOSONAR
    public const CURRENT_PASSWORD = 'currentPassword'; //NOSONAR
    public const CONFIRM_PASSWORD = 'confirmPassword'; //NOSONAR
    public const GROUPS = 'groups';
    public const REFRESH_TOKEN = 'refresh_token';
    public const ROLES = 'roles';

    // General Labels
    public const ACTION = 'action';
    public const ACTIVE = 'active';
    public const CHANGES = 'changes';
    public const CODE = 'code';
    public const COMMAND = 'command';
    public const COUNT = 'count';
    public const DATA = 'data';
    public const DESCRIPTION = 'description';
    public const ID = 'id';
    public const INTERVAL_CONTEXT = 'intervalContext';
    public const INTERVAL_PERIOD = 'intervalPeriod';
    public const MESSAGE = 'message';
    public const MESSAGES = 'messages';
    public const PRIORITY = 'priority';
    public const SOURCE = 'source';
    public const START_DATE = 'startDate';
    public const STATUS = 'status';
    public const TABLE = 'table';
    public const TARGET = 'target';
    public const TIME = 'time';
    public const TITLE = 'title';
    public const TYPE = 'type';
    public const UPDATED_BY = 'updatedBy';
    public const UPDATED_ON = 'updatedOn';

    //HTTP headers
    public const CONTENT_TYPE = 'CONTENT_TYPE';
    public const HTTP_AUTHORIZATION = 'HTTP_AUTHORIZATION';
    public const JSON_TYPE = 'application/json';


}