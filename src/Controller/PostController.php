<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(private readonly PostRepository $repository)
    {
    }

    #[Route('/post/feed', name: 'post_feed', methods: ['GET'])]
    public function feed(
        Security $security,
        Request $request
    ): JsonResponse {
        $offset = $request->query->get('offset', 0);
        $limit = $request->query->get('limit', 10);
        /** @var User $user */
        $user = $security->getUser();

        $posts = $this->repository->findFriendsPosts($user->getId(), $offset, $limit);

        $response = new JsonResponse($posts);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $response;
    }

    #[Route('/post/create', name: 'post_create', methods: ['POST'])]
    public function create(
        Security $security,
        Request $request
    ): JsonResponse {
        $contentParams = json_decode($request->getContent(), true);

        $requiredParams = ['text'];

        if (!$contentParams || count(array_diff(array_keys($contentParams), $requiredParams)) > 0) {
            throw new BadRequestHttpException('Невалидные данные');
        }

        /** @var User $authUser*/
        $authUser = $security->getUser();

        $this->repository->addPost($authUser->getId(), $contentParams['text'], new \DateTimeImmutable());

        $response = new JsonResponse('Успешно создан пост');
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $response;
    }
}