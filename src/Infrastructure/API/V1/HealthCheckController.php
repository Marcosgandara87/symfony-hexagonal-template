<?php
namespace App\Infrastructure\API\V1;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HealthCheckController
{
    #[Route('/health', name: 'api_health_check', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'OK',
            'timestamp' => time(),
            'service' => '!bookingkit-integration-api'
        ], Response::HTTP_OK);
    }
}
