<?php

namespace App\Constant\My;

use App\Constant\AppConstant;

/**
 * Class PasswordConstant
 * @package App\Constant\My
 */
class EventConstant
{
    //Constants for Annotations in App\Controller\Api\My\EventController
    public const GET_EVENTS_SECURITY_ERROR = 'You must be logged in and authorised to view events.';
    public const GET_EVENT_SECURITY_ERROR = 'You must be logged in and authorised to view this event.';
    public const CREATE_EVENT_SECURITY_ERROR = 'You must be logged in and authorised to create events.';
    public const DESCRIPTION_EMPTY_ERROR = 'The description is a required field and cannot be empty.';
    public const DESCRIPTION_MIN_LENGTH_ERROR = 'The description must be at least {{ limit }} characters long.';
    public const DESCRIPTION_MAX_LENGTH_ERROR = 'The description cannot be longer than {{ limit }} characters.';
    public const LOCATION_EMPTY_ERROR = 'The location is a required field and cannot be empty.';
    public const LOCATION_MIN_LENGTH_ERROR = 'The location must be at least {{ limit }} characters long.';
    public const LOCATION_MAX_LENGTH_ERROR = 'The location cannot be longer than {{ limit }} characters.';
    public const START_EMPTY_ERROR = 'The start date is a required field and cannot be empty.';
    public const END_EMPTY_ERROR = 'The end date is a required field and cannot be empty.';
    public const END_GREATER_ERROR = 'The end date must be greater than the start date.';
    public const CREATED_BY_EMPTY_ERROR = 'The Created By is a required field and cannot be empty.';
    public const UPDATE_EVENT_SECURITY_ERROR = 'You must be logged in and authorised to update your events.';
    public const DELETE_EVENT_SECURITY_ERROR = 'You must be logged in and authorised to cancel your events.';

    //Constants for Messages in App\Controller\Api\My\EventController
    public const GET_MULTIPLE_SUCCESS_MESSAGE = 'User received a list of all events.';
    public const GET_SINGLE_SUCCESS_MESSAGE = 'User received an event.';
    public const CREATE_VALIDATION_ERROR = 'Sorry, unable to create the event, please note the errors below:';
    public const CREATE_SUCCESS_MESSAGE = 'Successfully created your event.';
    public const CREATE_SUCCESS_LOG = 'User created a new event.';
    public const CREATE_ERROR_LOG = 'User failed to create a new event.';
    public const UPDATE_VALIDATION_ERROR = 'Sorry, unable to update the event, please note the errors below:';
    public const UPDATE_SUCCESS_MESSAGE = 'Successfully updated your event.';
    public const UPDATE_SUCCESS_LOG = 'User updated an event.';
    public const UPDATE_ERROR_LOG = 'User failed to update an event.';
    public const DELETE_SUCCESS_MESSAGE = 'Successfully cancelled your event.';
    public const DELETE_SUCCESS_LOG = 'User cancelled an event.';
    public const DELETE_ERROR_LOG = 'User failed to cancel an event.';

    public static function loadConstants(): void
    {
        //Constants for Annotations in App\Controller\Api\My\EventController
        AppConstant::defineConstant('EVENT_GET_EVENTS_SECURITY_ERROR', self::GET_EVENTS_SECURITY_ERROR);
        AppConstant::defineConstant('EVENT_GET_EVENT_SECURITY_ERROR', self::GET_EVENT_SECURITY_ERROR);
        AppConstant::defineConstant('EVENT_DESCRIPTION_EMPTY_ERROR', self::DESCRIPTION_EMPTY_ERROR);
        AppConstant::defineConstant('EVENT_DESCRIPTION_MIN_LENGTH_ERROR', self::DESCRIPTION_MIN_LENGTH_ERROR);
        AppConstant::defineConstant('EVENT_DESCRIPTION_MAX_LENGTH_ERROR', self::DESCRIPTION_MAX_LENGTH_ERROR);
        AppConstant::defineConstant('EVENT_LOCATION_EMPTY_ERROR', self::LOCATION_EMPTY_ERROR);
        AppConstant::defineConstant('EVENT_LOCATION_MIN_LENGTH_ERROR', self::LOCATION_MIN_LENGTH_ERROR);
        AppConstant::defineConstant('EVENT_LOCATION_MAX_LENGTH_ERROR', self::LOCATION_MAX_LENGTH_ERROR);
        AppConstant::defineConstant('EVENT_START_EMPTY_ERROR', self::START_EMPTY_ERROR);
        AppConstant::defineConstant('EVENT_END_EMPTY_ERROR', self::END_EMPTY_ERROR);
        AppConstant::defineConstant('EVENT_END_GREATER_ERROR', self::END_GREATER_ERROR);
        AppConstant::defineConstant('EVENT_CREATED_BY_EMPTY_ERROR', self::CREATED_BY_EMPTY_ERROR);
        AppConstant::defineConstant('EVENT_CREATE_EVENT_SECURITY_ERROR', self::CREATE_EVENT_SECURITY_ERROR);
        AppConstant::defineConstant('EVENT_UPDATE_EVENT_SECURITY_ERROR', self::UPDATE_EVENT_SECURITY_ERROR);
        AppConstant::defineConstant('EVENT_DELETE_EVENT_SECURITY_ERROR', self::DELETE_EVENT_SECURITY_ERROR);
    }
}