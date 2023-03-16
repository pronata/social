<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private const KEY_ID = 'id';
    private const KEY_PASSWORD = 'password';

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        ApiTokenRepository $tokenRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $params = json_decode($request->getContent(), true);

        $requiredParams = [
            self::KEY_ID,
            self::KEY_PASSWORD,
        ];

        if (count(array_diff(array_keys($params), $requiredParams)) > 0) {
            throw new BadRequestHttpException('Невалидные данные');
        }

        $user = $userRepository->find($params['id']);
        $em->persist($user);

        if ($user === null) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        if (!$passwordHasher->isPasswordValid($user,
            $params[self::KEY_PASSWORD])) {
            throw new BadRequestHttpException('Невалидные данные');
        }

        // TODO: Заменить на plain sql
        $token = new ApiToken($user);
        $tokenRepository->save($token, flush: true);

        return $this->json([
            'token' => $token->getToken(),
        ]);
    }
}
