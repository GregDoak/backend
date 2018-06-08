<?php

namespace App\Helper;

use App\Constant\LabelConstant;
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
            LabelConstant::STATUS => false,
            LabelConstant::CODE => $code,
            LabelConstant::DATA => self::buildMessageResponse('danger', $message, $messages),
            LabelConstant::COUNT => 3,
            LabelConstant::TIME => time(),
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
            LabelConstant::TYPE => $type,
            LabelConstant::MESSAGE => $message,
            LabelConstant::MESSAGES => $messages,
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
            LabelConstant::STATUS => true,
            LabelConstant::CODE => $code,
            LabelConstant::DATA => $data,
            LabelConstant::COUNT => \is_array($data) ? \count($data) : 1,
            LabelConstant::TIME => time(),
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
                LabelConstant::CODE => $data[LabelConstant::CODE],
                LabelConstant::ACTION => $action,
                LabelConstant::MESSAGE => $message,
                LabelConstant::MESSAGES => $messages,
                LabelConstant::USERNAME => $username,
            ]
        );
    }
}