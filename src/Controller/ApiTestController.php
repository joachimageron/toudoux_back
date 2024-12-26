<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;


class ApiTestController
{


    #[Route('/api/test', name: 'api_test', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a test message if the user is authenticated',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'vous êtes identifié avec succès')
            ],
            type: 'object')
    )]
    #[OA\Tag(name: 'Test')]
    #[Security(name: 'Bearer')]
    public function test(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Vous êtes authentifié avec succès !'
        ]);
    }
}
