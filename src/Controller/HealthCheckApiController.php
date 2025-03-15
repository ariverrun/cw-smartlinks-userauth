<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthCheckApiController extends AbstractController
{
    #[Route('/api/v1/healthCheck', methods: ['GET'], name: 'api_health_check')]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'ok' => true,
        ]);
    }
}
