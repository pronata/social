<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(
        Request $request,
        AuthenticationException $authException = null
    ): JsonResponse {
        return new JsonResponse([
            'error' => [
                'code' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Токен не передан, либо неверный, либо истёк его срок действия',
            ],
        ], Response::HTTP_UNAUTHORIZED);
    }
}
