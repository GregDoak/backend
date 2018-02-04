<?php

namespace App\Logger;

use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ResponseLogger
 * @package App\Logger
 */
class ResponseLogger
{
    /** @var RequestStack $requestStack */
    private $requestStack;

    /**
     * ResponseLogger constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $record
     * @throws SuspiciousOperationException
     * @return array
     */
    public function processRecord(array $record): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request instanceof Request) {
            $record['extra'] = [
                'user_agent' => $request->headers->get('User-Agent'),
                'url' => $request->getRequestUri(),
                'method' => $request->getRealMethod(),
                'parameters' => $request->request->all(),
            ];
        }

        return $record;
    }
}