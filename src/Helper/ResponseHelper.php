<?php

namespace App\Helper;

use App\Controller\Api\ApiController;
use App\Entity\Security\User;

/**
 * Class ResponseHelper
 * @package App\Helper
 */
class ResponseHelper
{
    /**
     * @param int $code
     * @param string $message
     * @param array $messages
     * @return array
     */
    public static function buildErrorResponse(int $code, string $message = null, array $messages = []): array
    {
        return [
            'status' => false,
            'code' => $code,
            'data' => self::buildMessageResponse('danger', $message, $messages),
            'count' => 3,
            'time' => time(),
        ];
    }

    /**
     * @param string $type
     * @param string $message
     * @param array $messages
     * @return array
     */
    public static function buildMessageResponse(string $type, string $message, array $messages = []): array
    {
        return [
            'type' => $type,
            'message' => $message,
            'messages' => $messages,
        ];
    }

    /**
     * @param int $code
     * @param mixed $data
     * @return array
     */
    public static function buildSuccessResponse(int $code, $data): array
    {
        return [
            'status' => true,
            'code' => $code,
            'data' => $data,
            'count' => \is_array($data) ? \count($data) : 1,
            'time' => time(),
        ];
    }

    /**
     * @param string $action
     * @param array $data
     * @param ApiController $class
     */
    public static function logResponse(string $action, array $data, ApiController $class): void
    {
        $message = array_key_exists('message', $data['data']) ? $data['data']['message'] : '';
        $messages = array_key_exists('messages', $data['data']) ? $data['data']['messages'] : [];
        $username = $class->authenticatedUser instanceof User ? $class->authenticatedUser->getUsername() : 'Unknown';

        $class->logger->info(
            $class->getClassName(),
            [
                'code' => $data['code'],
                'action' => $action,
                'message' => $message,
                'messages' => $messages,
                'username' => $username,
            ]
        );
    }
}