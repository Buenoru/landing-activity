<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/rnd/{url}', name: 'app_rnd_url')]
    public function url(string $url, MessageBusInterface $bus): Response
    {
        $generateUrl = $this->generateUrl('app_rnd_url', ['url' => $url]);
        $activity = new Activity($generateUrl);
        $bus->dispatch($activity);

        return $this->render('main/url.html.twig', [
            'url' => $url,
        ]);
    }
}
