<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\ValueObject\BirthDate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterUserController extends AbstractController
{
    private const KEY_FIRST_NAME = 'first_name';
    private const KEY_SECOND_NAME = 'second_name';
    private const KEY_BIRTHDATE = 'birthdate';
    private const KEY_BIOGRAPHY = 'biography';
    private const KEY_CITY = 'city';
    private const KEY_PASSWORD = 'password';

    #[Route('/user/register', name: 'user_register', methods: ['POST'])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): JsonResponse {
        $params = json_decode($request->getContent(), true);

        $requiredParams = [
            self::KEY_FIRST_NAME,
            self::KEY_SECOND_NAME,
            self::KEY_BIRTHDATE,
            self::KEY_BIOGRAPHY,
            self::KEY_CITY,
            self::KEY_PASSWORD
        ];

        if (count(array_diff(array_keys($params), $requiredParams)) > 0) {
            throw new BadRequestHttpException('Невалидные данные');
        }

        try {
            $birthDate = new BirthDate($params[self::KEY_BIRTHDATE]);
        } catch (\InvalidArgumentException) {
            throw new BadRequestHttpException('Невалидные данные');
        }

        // TODO: Заменить на plain sql
        $user = new User();
        $user
            ->setFirstName($params[self::KEY_FIRST_NAME])
            ->setSecondName($params[self::KEY_SECOND_NAME])
            ->setBirthDate($birthDate)
            ->setBiography($params[self::KEY_BIOGRAPHY])
            ->setCity($params[self::KEY_CITY]);

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $params[self::KEY_PASSWORD]
        );

        $user->setPassword($hashedPassword);

        $userRepository->save($user, flush: true);

        return $this->json([
            'user_id' => $user->getUserIdentifier()
        ]);
    }
}
