<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetUserController extends AbstractController
{
    #[Route('/user/{id}', name: 'user_get', methods: ['GET'])]
    public function index(
        string $id,
        UserRepository $userRepository
    ): JsonResponse {
        $user = $userRepository->find($id);

        $now = new \DateTimeImmutable('now');

        return $this->json([
            "id" => $user->getUserIdentifier(),
            "first_name" => $user->getFirstName(),
            "second_name" => $user->getSecondName(),
            "age" => $user->getAge($now),
            "birthdate" => $user->getBirthDate()->format('Y-m-d'),
            "biography" => $user->getBiography(),
            "city" => $user->getCity()
        ]);
    }
}
