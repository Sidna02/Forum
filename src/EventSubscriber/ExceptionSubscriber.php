<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\DiscordService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private DiscordService $discordService;

    public function __construct(DiscordService $discordService)
    {

        $this->discordService = $discordService;
    }
    public function onKernelException(ExceptionEvent $event): void
    {
        $this->discordService->sendError(
            $event->getThrowable()->getMessage(),
            $event->getRequest()->getClientIp(),
            $event->getThrowable()->getTraceAsString()
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
