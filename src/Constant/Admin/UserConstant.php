<?php

namespace App\Constant\Admin;

use App\Constant\AppConstant;

/**
 * Class UserConstant
 * @package App\Constant\Admin
 */
class UserConstant
{
    //Constants for Annotations in App\Entity\Security\User
    public const UNIQUE_ENTITY_ERROR = 'This username {{ value }} already exists in the database.';
    public const USERNAME_EMPTY_ERROR = 'The username is a required field and cannot be empty.';
    public const USERNAME_MIN_LENGTH_ERROR = 'The username must be at least {{ limit }} characters long.';
    public const USERNAME_MAX_LENGTH_ERROR = 'The username cannot be longer than {{ limit }} characters.';
    public const PASSWORD_EMPTY_ERROR = 'The password is a required field and cannot be empty.'; //NOSONAR
    public const PASSWORD_MIN_LENGTH_ERROR = 'The password must be at least {{ limit }} characters long.'; //NOSONAR
    public const PASSWORD_MAX_LENGTH_ERROR = 'The password cannot be longer than {{ limit }} characters.'; //NOSONAR
    public const CREATED_BY_EMPTY_ERROR = 'The Created By is a required field and cannot be empty.';

    //Constants for Annotations in App\Controller\Api\Admin\UserController
    public const GET_USERS_SECURITY_ERROR = 'You do not access to view the list of users.';
    public const GET_USER_SECURITY_ERROR = 'You do not access to view this user.';
    public const CREATE_USER_SECURITY_ERROR = 'You do not access to create a user.';
    public const UPDATE_USER_SECURITY_ERROR = 'You do not access to update this user.';
    public const DELETE_USER_SECURITY_ERROR = 'You do not access to delete this user.';

    //Constants for Messages in App\Controller\Api\Admin\UserController
    public const GET_MULTIPLE_SUCCESS_MESSAGE = 'User received a list of all users.';
    public const GET_SINGLE_SUCCESS_MESSAGE = 'User received the details of %s';
    public const CREATE_VALIDATION_ERROR = 'Sorry, unable to create the user, please note the errors below:';
    public const CREATE_SUCCESS_MESSAGE = 'Successfully created %s.';
    public const CREATE_SUCCESS_LOG = 'User created a new user with the username of %s';
    public const CREATE_ERROR_LOG = 'User failed to create a new user';
    public const UPDATE_VALIDATION_ERROR = 'Sorry, unable to update the user, please note the errors below:';
    public const UPDATE_SUCCESS_MESSAGE = 'Successfully updated %s.';
    public const UPDATE_SUCCESS_LOG = 'User successfully updated %s';
    public const UPDATE_ERROR_LOG = 'User failed to update %s';
    public const DELETE_SUCCESS_MESSAGE = 'Successfully deleted %s.';
    public const DELETE_SUCCESS_LOG = 'User successfully deleted %s';

    public static function loadConstants(): void
    {
        //Constants for Annotations in App\Entity\Security\Role
        AppConstant::defineConstant('USER_UNIQUE_ENTITY_ERROR', self::UNIQUE_ENTITY_ERROR);
        AppConstant::defineConstant('USER_USERNAME_EMPTY_ERROR', self::USERNAME_EMPTY_ERROR);
        AppConstant::defineConstant('USER_USERNAME_MIN_LENGTH_ERROR', self::USERNAME_MIN_LENGTH_ERROR);
        AppConstant::defineConstant('USER_USERNAME_MAX_LENGTH_ERROR', self::USERNAME_MAX_LENGTH_ERROR);
        AppConstant::defineConstant('USER_PASSWORD_EMPTY_ERROR', self::PASSWORD_EMPTY_ERROR);
        AppConstant::defineConstant('USER_PASSWORD_MIN_LENGTH_ERROR', self::PASSWORD_MIN_LENGTH_ERROR);
        AppConstant::defineConstant('USER_PASSWORD_MAX_LENGTH_ERROR', self::PASSWORD_MAX_LENGTH_ERROR);
        AppConstant::defineConstant('USER_CREATED_BY_EMPTY_ERROR', self::CREATED_BY_EMPTY_ERROR);

        //Constants for Annotations in App\Controller\Api\Admin\RoleController
        AppConstant::defineConstant('USER_GET_USERS_SECURITY_ERROR', self::GET_USERS_SECURITY_ERROR);
        AppConstant::defineConstant('USER_GET_USER_SECURITY_ERROR', self::GET_USER_SECURITY_ERROR);
        AppConstant::defineConstant('USER_CREATE_USER_SECURITY_ERROR', self::CREATE_USER_SECURITY_ERROR);
        AppConstant::defineConstant('USER_UPDATE_USER_SECURITY_ERROR', self::UPDATE_USER_SECURITY_ERROR);
        AppConstant::defineConstant('USER_DELETE_USER_SECURITY_ERROR', self::DELETE_USER_SECURITY_ERROR);
    }
}