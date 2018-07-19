<?php

namespace App\Controller\Api;

use App\Entity\Security\User;
use App\Exception\ValidationException;
use FOS\RestBundle\Controller\FOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ApiController
 * @package App\Controller\Api
 */
class ApiController extends FOSRestController
{
    /** @var User $authenticatedUser */
    public $authenticatedUser;
    /** @var LoggerInterface $logger */
    public $logger;
    /** @var ValidatorInterface $validator */
    public $validator;
    /** @var array $entityErrors */
    private $entityErrors = [];

    /**
     * ApiController constructor.
     * @param LoggerInterface $logger
     * @param ValidatorInterface $validator
     */
    public function __construct(LoggerInterface $logger, ValidatorInterface $validator)
    {
        $this->logger = $logger;
        $this->validator = $validator;
    }

    /**
     * @throws
     * @return string
     */
    public function getClassName(): string
    {
        $class = new \ReflectionClass($this);

        return $class->getName();
    }

    /**
     * @param $entity
     * @param string $message
     * @throws ValidationException
     */
    public function validateEntity($entity, string $message): void
    {
        $errors = $this->validator->validate($entity);
        foreach ($errors as $error) {
            if ( ! \in_array($error->getMessage(), $this->getEntityErrors(), true)) {
                $this->setEntityError($error->getMessage());
            }
        }

        if (\count($this->getEntityErrors()) > 0) {
            throw new ValidationException($message);
        }
    }

    /**
     * @return array
     */
    public function getEntityErrors(): array
    {
        return $this->entityErrors;
    }

    /**
     * @param string $entityError
     * @return ApiController
     */
    public function setEntityError(string $entityError): ApiController
    {
        $this->entityErrors[] = $entityError;

        return $this;
    }


}