<?php

namespace App\Constant\Admin;

use App\Constant\AppConstant;

/**
 * Class GroupConstant
 * @package App\Constant\Admin
 */
class GroupConstant
{
    //Constants for Annotations in App\Entity\Security\Group
    public const UNIQUE_ENTITY_ERROR = 'This group {{ value }} already exists in the database.';
    public const TITLE_EMPTY_ERROR = 'The title is a required field and cannot be empty.';
    public const TITLE_MIN_LENGTH_ERROR = 'The title must be at least {{ limit }} characters long.';
    public const TITLE_MAX_LENGTH_ERROR = 'The title cannot be longer than {{ limit }} characters.';
    public const DESCRIPTION_EMPTY_ERROR = 'The description is a required field and cannot be empty.';
    public const DESCRIPTION_MIN_LENGTH_ERROR = 'The description must be at least {{ limit }} characters long.';
    public const DESCRIPTION_MAX_LENGTH_ERROR = 'The description cannot be longer than {{ limit }} characters.';
    public const CREATED_BY_EMPTY_ERROR = 'The Created By is a required field and cannot be empty.';

    //Constants for Annotations in App\Controller\Api\Admin\GroupController
    public const GET_GROUPS_SECURITY_ERROR = 'You do not access to view the list of groups.';
    public const GET_GROUP_SECURITY_ERROR = 'You do not access to view this group.';
    public const CREATE_GROUP_SECURITY_ERROR = 'You do not access to create a group.';
    public const UPDATE_GROUP_SECURITY_ERROR = 'You do not access to update this group.';
    public const DELETE_GROUP_SECURITY_ERROR = 'You do not access to delete this groups.';

    //Constants for Messages in App\Controller\Api\Admin\GroupController
    public const GET_MULTIPLE_SUCCESS_MESSAGE = 'User received a list of all groups.';
    public const GET_SINGLE_SUCCESS_MESSAGE = 'User received the details of %s';
    public const CREATE_VALIDATION_ERROR = 'Sorry, unable to create the group, please note the errors below:';
    public const CREATE_SUCCESS_MESSAGE = 'Successfully created %s.';
    public const CREATE_SUCCESS_LOG = 'User created a new group with the title of %s';
    public const CREATE_ERROR_LOG = 'User failed to create a new group';
    public const UPDATE_VALIDATION_ERROR = 'Sorry, unable to update the group, please note the errors below:';
    public const UPDATE_SUCCESS_MESSAGE = 'Successfully updated %s.';
    public const UPDATE_SUCCESS_LOG = 'User successfully updated %s';
    public const UPDATE_ERROR_LOG = 'User failed to update %s';
    public const DELETE_SUCCESS_MESSAGE = 'Successfully deleted %s.';
    public const DELETE_SUCCESS_LOG = 'User successfully deleted %s';

    public static function loadConstants(): void
    {
        //Constants for Annotations in App\Entity\Security\Group
        AppConstant::defineConstant('GROUP_UNIQUE_ENTITY_ERROR', self::UNIQUE_ENTITY_ERROR);
        AppConstant::defineConstant('GROUP_TITLE_EMPTY_ERROR', self::TITLE_EMPTY_ERROR);
        AppConstant::defineConstant('GROUP_TITLE_MIN_LENGTH_ERROR', self::TITLE_MIN_LENGTH_ERROR);
        AppConstant::defineConstant('GROUP_TITLE_MAX_LENGTH_ERROR', self::TITLE_MAX_LENGTH_ERROR);
        AppConstant::defineConstant('GROUP_DESCRIPTION_EMPTY_ERROR', self::DESCRIPTION_EMPTY_ERROR);
        AppConstant::defineConstant('GROUP_DESCRIPTION_MIN_LENGTH_ERROR', self::DESCRIPTION_MIN_LENGTH_ERROR);
        AppConstant::defineConstant('GROUP_DESCRIPTION_MAX_LENGTH_ERROR', self::DESCRIPTION_MAX_LENGTH_ERROR);
        AppConstant::defineConstant('GROUP_CREATED_BY_EMPTY_ERROR', self::CREATED_BY_EMPTY_ERROR);

        //Constants for Annotations in App\Controller\Api\Admin\GroupController
        AppConstant::defineConstant('GROUP_GET_GROUPS_SECURITY_ERROR', self::GET_GROUPS_SECURITY_ERROR);
        AppConstant::defineConstant('GROUP_GET_GROUP_SECURITY_ERROR', self::GET_GROUP_SECURITY_ERROR);
        AppConstant::defineConstant('GROUP_CREATE_GROUP_SECURITY_ERROR', self::CREATE_GROUP_SECURITY_ERROR);
        AppConstant::defineConstant('GROUP_UPDATE_GROUP_SECURITY_ERROR', self::UPDATE_GROUP_SECURITY_ERROR);
        AppConstant::defineConstant('GROUP_DELETE_GROUP_SECURITY_ERROR', self::DELETE_GROUP_SECURITY_ERROR);
    }
}