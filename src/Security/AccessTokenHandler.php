<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly ApiTokenRepository $repository,
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $apiToken
    ): UserBadge {
        $apiToken = $this->repository->findOneByToken($apiToken);
        if (null === $apiToken || !$apiToken->isValid(new \DateTimeImmutable('now'))) {
            throw new AccessDeniedException();
        }

        // and return a UserBadge object containing the user identifier from the found token
        return new UserBadge($apiToken->getUser()->getUserIdentifier());
    }
}

