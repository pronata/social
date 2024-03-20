<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post/feed', name: 'user_get', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Security $security,
        Request $request
    ): JsonResponse {
        $offset = $request->query->get('offset', 0);
        $limit = $request->query->get('limit', 10);
        /** @var User $user */
        $user = $security->getUser();

        $posts = $postRepository->findFriendsPosts($user->getId(), $offset, $limit);

        $response = new JsonResponse($posts);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $response;
    }
}