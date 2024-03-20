<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class FriendController extends AbstractController
{
    #[Route('/friend/set/{friendId}', name: 'friend_add', methods: ['PUT'])]
    public function add(
        Uuid $friendId,
        UserRepository $userRepository,
        Security $security
    ): JsonResponse {
        /** @var User $authUser*/
        $authUser = $security->getUser();

        $userRepository->addFriend($authUser->getId(), $friendId);

        $response = new JsonResponse('Пользователь успешно указал своего друга');
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $response;
    }

    #[Route('/friend/delete/{userId}', name: 'friend_delete', methods: ['PUT'])]
    public function delete(
        Uuid $userId,
        UserRepository $userRepository,
        Security $security
    ): JsonResponse {
        /** @var User $authUser*/
        $authUser = $security->getUser();

        $userRepository->deleteFriend($authUser->getId(), $userId);

        $response = new JsonResponse('Пользователь успешно удалил из друзей пользователя');
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $response;
    }
}