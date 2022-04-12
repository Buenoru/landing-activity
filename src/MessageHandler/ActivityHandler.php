<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\Activity;
use App\Service\ActivityService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ActivityHandler implements MessageHandlerInterface
{
    public function __construct(
        private ActivityService $activityService
    ) {
    }

    public function __invoke(Activity $activity)
    {
        $this->activityService->addOrIncrement($activity);
    }
}
