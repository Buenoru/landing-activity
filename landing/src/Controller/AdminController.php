<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ActivityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    public function __construct(
        private ActivityService $activityService
    ) {
    }

    #[Route('/admin/activity', name: 'app_admin_activity')]
    public function index(): Response
    {
        $activities = $this->activityService->getList();

        return $this->render('admin/index.html.twig', [
            'activities' => $activities,
        ]);
    }
}
