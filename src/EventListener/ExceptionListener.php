<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        $exception->getMessage();
    }
}