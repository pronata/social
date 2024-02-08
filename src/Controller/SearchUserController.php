<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SearchUserController extends AbstractController
{
    private const string KEY_FIRST_NAME = 'first_name';
    private const string KEY_SECOND_NAME = 'last_name';

    #[Route(path: '/user/search', name: 'user_search', methods: ['GET'], priority: 1)]
    public function index(
        Request $request,
        UserRepository $userRepository
    ): JsonResponse {
        $requiredParams = [
            self::KEY_FIRST_NAME,
            self::KEY_SECOND_NAME,
        ];

        if (count(array_diff(array_keys($request->query->all()), $requiredParams)) > 0) {
            throw new BadRequestHttpException('Невалидные данные');
        }

        $firstName = $request->query->get(self::KEY_FIRST_NAME, '');
        $lastName = $request->query->get(self::KEY_SECOND_NAME, '');

        $users = $userRepository->findByFirstNameAndSecondName($firstName, $lastName);

        return $this->json($users);
    }
}